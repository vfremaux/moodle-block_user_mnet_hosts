<?php

if (!defined('MOODLE_INTERNAL')) die ("You cannot use this script this way");

/// records in config the user field category holding access control fields

	if(!isset($CFG->accesscategory)){
		$accesscategory = new stdClass;   
		$accesscategory->name = addslashes(get_string('accesscategory', 'block_user_mnet_hosts'));
		$accesscategory->sortorder = 1;
		
		$id = insert_record('user_info_category', $accesscategory, true);
		set_config('accesscategory', $id);   
	}

/// adding self access field

	preg_match('/https?:\/\/([^.]*)/', $CFG->wwwroot, $matches);
	$hostprefix = $matches[1];
	$expectedname = 'access'.str_replace('-', '', strtoupper($hostprefix)); // need cleaning name from hyphens

	if (!$selfaccess = get_record('user_info_field', 'shortname', $expectedname)){
		$newfield = new stdClass;
		$newfield->shortname = $expectedname;
		$hostkey = strtoupper($hostprefix);
		$newfield->name = get_string('fieldname', 'block_user_mnet_hosts').' '.$hostkey;
		$newfield->datatype = 'checkbox';
		$newfield->locked = 1;
		$newfield->categoryid = $CFG->accesscategory;
	
		if($fieldid = insert_record('user_info_field',$newfield)){
			// we need setup a field value for all non deleted users
			if ($users = get_records('user', 'deleted', 0)){
				foreach($users as $u){
					$newvalue = new StdClass;
					$newvalue->userid = $u->id;
					$newvalue->fieldid = $fieldid;
					$newvalue->data = 1;
					insert_record('user_info_data', $newvalue);
				}
			}
		}
	}

	$syncstr = get_string('synchonizingaccesses', 'block_user_mnet_hosts');
	$settings->add(new admin_setting_heading('synchronization', get_string('synchonizingaccesses', 'block_user_mnet_hosts'), "<a href=\"{$CFG->wwwroot}/blocks/user_mnet_hosts/admin.php\">$syncstr</a>"));

?>