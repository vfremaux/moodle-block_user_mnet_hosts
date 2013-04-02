<?php

/**
* XLIB contains interface function for peer moodle components 
*
* @package block-user-mnet-hosts
* @author Valery Fremaux
*
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

	if (empty($wwwroot)) {
		return 'Add access error : empty host';
	}
	if (empty($user)) {
		return 'Add access error : empty user';
	}

  	preg_match('/https?:\/\/([^.]*)/', $wwwroot, $matches);
  	$hostprefix = $matches[1];
  	$hostfieldname = 'access'.str_replace('-', '', strtoupper($hostprefix)); // need cleaning name from hyphens
  	
  	if($userfield = get_record('user_info_field', 'shortname', $hostfieldname)){
  		if ($accessrec = get_record('user_info_data', 'fieldid', $userfield->id, 'userid', $user->id)){
  			$accessrec->data = 1;
  			if (!update_record('user_info_data', $accessrec)){
  				return "Access Update Failure for $user->username on $wwwroot with $hostfieldname";
  			} else {
				return "Add access : updated for $user->username on $wwwroot with $hostfieldname";
			}
  		} else {
  			$accessrec->fieldid = $userfield->id;
  			$accessrec->userid = $user->id;
  			$accessrec->data = 1;
  			if (!insert_record('user_info_data', $accessrec)){
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

	if (empty($wwwroot)) {
		if (debugging()) notice('Add access : empty host');
		return;
	}
	if (empty($user)) {
		if (debugging()) notice('Add access : empty user');
		return;
	}

  	preg_match('/https?:\/\/([^.]*)/', $wwwroot, $matches);
  	$hostprefix = $matches[1];
  	$hostfieldname = 'access'.str_replace('-', '', strtoupper($hostprefix)); // need cleaning name from hyphens
  	
  	if($userfield = get_record('user_info_field', 'shortname', $hostfieldname)){
  		if ($accessrec = get_record('user_info_data', 'fieldid', $userfield->id, 'userid', $user->id)){
  			$accessrec->value = 0;
  			update_record('user_info_data', $accessrec);
  		} else {
  			$accessrec->fieldid = $userfield->id;
  			$accessrec->userid = $user->id;
  			$accessrec->value = 0;
  			insert_record('user_info_data', $accessrec);
  		}
  	}
}