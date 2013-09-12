<?php

/**
* XLIB contains interface function for peer moodle components 
*
* @package block-user-mnet-hosts
* @author Valery Fremaux
* @version Moodle 2.x
*
*/

/**
* grants a user access to a platform in his profile customized data.
* Note that this operation should ONLY be perfomed on a host where the
* user has his master account record (i.e. not MNET).
* @param object $user
* @param string $wwwroot
*/
function user_mnet_host_add_access($user, $wwwroot){

	global $DB;

	if (empty($wwwroot)) {
		return 'Add access error : empty host';
	}
	if (empty($user)) {
		return 'Add access error : empty user';
	}

  	preg_match('/https?:\/\/([^.]*)/', $wwwroot, $matches);
  	$hostprefix = $matches[1];
  	$hostfieldname = 'access'.str_replace('-', '', strtoupper($hostprefix)); // need cleaning name from hyphens
  	if($userfield = $DB->get_record('user_info_field', array('shortname' => $hostfieldname))){
  		if ($accessrec = $DB->get_record('user_info_data', array('fieldid' => $userfield->id, 'userid' => $user->id))){
  			$accessrec->data = 1;
  			if (!$DB->update_record('user_info_data', $accessrec)){
  				return "Access Update Failure for $user->username on $wwwroot with $hostfieldname";
  			} else {
				return "Add access : updated for $user->username on $wwwroot with $hostfieldname";
			}
  		} else {
  			$accessrec = new StdClass();
  			$accessrec->fieldid = $userfield->id;
  			$accessrec->userid = $user->id;
  			$accessrec->data = 1;
  			if (!$DB->insert_record('user_info_data', $accessrec)){
  				return "Access Update Failure  for $user->username on $wwwroot with $hostfieldname";
  			} else {
				return "Add access : granted for $user->username on $wwwroot with $hostfieldname";
			}
  		}
  	} else {
  		return "Add access error : unknown field $hostfieldname";
  	}
}

/**
* removes a user's access to a platform in his profile customized data.
* Note that this operation should ONLY be perfomed on a host where the
* user has his master account record (i.e. not MNET).
* @param object $user
* @param string $wwwroot
*/
function user_mnet_host_remove_access($user, $wwwroot){

	global $OUTPUT, $DB;

	if (empty($wwwroot)) {
		if (debugging()) echo $OUTPUT->notification('Add access : empty host');
		return;
	}
	if (empty($user)) {
		if (debugging()) echo $OUTPUT->notification('Add access : empty user');
		return;
	}

  	preg_match('/https?:\/\/([^.]*)/', $wwwroot, $matches);
  	$hostprefix = $matches[1];
  	$hostfieldname = 'access'.str_replace('-', '', strtoupper($hostprefix)); // need cleaning name from hyphens
  	if($userfield = $DB->get_record('user_info_field', array('shortname' => $hostfieldname))){
  		if ($accessrec = $DB->get_record('user_info_data', array('fieldid' => $userfield->id, 'userid' => $user->id))){
  			$accessrec->value = 0;
  			$DB->update_record('user_info_data', $accessrec);
  		} else {
  			$accessrec = new StdClass();
  			$accessrec->fieldid = $userfield->id;
  			$accessrec->userid = $user->id;
  			$accessrec->value = 0;
  			$DB->insert_record('user_info_data', $accessrec);
  		}
  	}
}