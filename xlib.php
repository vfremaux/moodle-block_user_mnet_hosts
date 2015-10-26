<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * XLIB contains interface function for peer moodle components
 *
 * @package block_user_mnet_hosts
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
function user_mnet_host_add_access($user, $wwwroot) {

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
    if ($userfield = $DB->get_record('user_info_field', array('shortname' => $hostfieldname))){
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
function user_mnet_host_remove_access($user, $wwwroot) {
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
    if ($userfield = $DB->get_record('user_info_field', array('shortname' => $hostfieldname))) {
        if ($accessrec = $DB->get_record('user_info_data', array('fieldid' => $userfield->id, 'userid' => $user->id))) {
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

/**
 * Gets the access state for a user in a specified host knwon by wwwroot
 * @param object $user
 * @param string $wwwroot
 */
function user_mnet_host_read_access($user, $wwwroot) {
    global $OUTPUT, $DB;

    if (empty($wwwroot)) {
        if (debugging()) echo $OUTPUT->notification('Read access : empty host');
        return;
    }
    if (empty($user)) {
        if (debugging()) echo $OUTPUT->notification('Read access : empty user');
        return;
    }

    // power users always have all accesses open
    $context = context_system::instance();
    if (has_capability('block/user_mnet_hosts:accessall', $context, $user->id)) {
        return true;
    }

    preg_match('/https?:\/\/([^.]*)/', $wwwroot, $matches);
    $hostprefix = $matches[1];
    $hostfieldname = 'access'.str_replace('-', '', strtoupper($hostprefix)); // need cleaning name from hyphens
    if ($userfield = $DB->get_record('user_info_field', array('shortname' => $hostfieldname))){
        if ($accessrec = $DB->get_record('user_info_data', array('fieldid' => $userfield->id, 'userid' => $user->id))){
            return $accessrec->data;
        }
    }
    return false;
}