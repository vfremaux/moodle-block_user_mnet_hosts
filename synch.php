<?php

/**
 * This page roughly synchronizes the access field definitin used to control visibility of MNET
 * doors with known declared mnet hosts.
 *
 * @TODO : refine the model choosing only hosts for which we have the SSO service enabled.
 *
 * @category blocks
 * @package blocks-user-mnet-hosts
 * @author E.Poncelet
 * @version 2.0
 *
 */

	require_once('../../config.php');

	$context = context_system::instance();

	// only for admins
	require_login();
	require_capability('moodle/site:config', context_system::instance());

   	$full = get_string('single_full','block_user_mnet_hosts');
   	$short = get_string('single_short','block_user_mnet_hosts');
   	
   	$url = $CFG->wwwroot.'/blocks/user_mnet_hosts/sync.php';

	$PAGE->set_url($url);
	$PAGE->set_context($context);
    $PAGE->navbar->add($full);
    $PAGE->set_title($full);
    $PAGE->set_heading($short);
    /* SCANMSG: may be additional work required for $navigation variable */
    $PAGE->set_focuscontrol('');
    $PAGE->set_cacheable(false);
    $PAGE->set_button('');
    echo $OUTPUT->header();

	// if typical user field category does exist, make some for us
  	if(!isset($CFG->accesscategory)){
  		$accesscategory = new stdClass;
  		$accesscategory->name = get_string('accesscategory', 'block_user_mnet_hosts');
  		$accesscategory->sortorder = 1;
  		$id = $DB->insert_record('user_info_category', $accesscategory);
  		set_config('accesscategory',$id);
  	}

    //We are going to get all non-deleted hosts from our database
    $knownhosts = $DB->get_records('mnet_host', array('deleted' => '0'), '', 'id,wwwroot');

    //Then we get all accessfields
    $accessfields = $DB->get_records_select('user_info_field','shortname like \'access%\'');
	//We create local variables to monitor the actions
	$created = 0;
	$ignored = 0;
	$failed = 0;      
	//Now we need to oppose our hosts to our fields
	foreach($knownhosts as $host){
	  	if($host->wwwroot == '' || $host->wwwroot == $CFG->wwwroot){
	  		$ignored++;
	  		continue;
	  	}
	  	preg_match('/https?:\/\/([^.]*)/', $host->wwwroot, $matches);
	  	$hostprefix = $matches[1];
	  	$expectedname = 'access'.str_replace('-', '', strtoupper($hostprefix)); // need cleaning name from hyphens
	  	$results = false;
	  	if ($accessfields){
		  	foreach($accessfields as $field){     
		    	//If we have a match, we do have the field, we can skip the host
		   		if($field->shortname == $expectedname){
					$results = true;
					$ignored++;
					break;
		   		}
		  	}
		}
		if(!$results){	  
			$newfield = new stdClass;
			$newfield->shortname = $expectedname;
			$hostkey = strtoupper($hostprefix);
			$newfield->name = get_string('fieldname', 'block_user_mnet_hosts').' '.$hostkey;
			$newfield->datatype = 'checkbox';
			$newfield->locked = 1;
			$newfield->categoryid = $CFG->accesscategory;
	    	if($DB->insert_record('user_info_field', $newfield)){	      
				$created++;	      
	    	} else {
				$failed++;
	    	} 
	  	}
	}
	echo $OUTPUT->box_start();
	echo(get_string('createdfields','block_user_mnet_hosts').$created.'<br/>');
	echo(get_string('ignoredfields','block_user_mnet_hosts').$ignored.'<br/>');
	echo(get_string('failedfields','block_user_mnet_hosts').$failed.'<br/>');
	echo $OUTPUT->box_end();

    echo('<div="Button" align="center">');
    echo $OUTPUT->single_button(new moodle_url('/blocks/user_mnet_hosts/admin.php'), get_string('backsettings','block_user_mnet_hosts'), 'get');
    echo('</div>');

    echo $OUTPUT->footer($COURSE);
?>