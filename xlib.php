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
 * @category blocks
 * @author Valery Fremaux
 * @version Moodle 2.x
 *
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/blocks/user_mnet_hosts/locallib.php');

/**
 *
 */
function user_mnet_hosts_get_accesskey($wwwroot, $full = false) {
    return user_mnet_hosts_make_accesskey($wwwroot, $full);
}

// Wrappers from older versions.
function user_mnet_host_add_access($user, $wwwroot) {
   return user_mnet_hosts_add_access($user, $wwwroot);
}
function user_mnet_host_remove_access($user, $wwwroot) {
    return user_mnet_hosts_remove_access($user, $wwwroot);
}
function user_mnet_host_read_access($user, $wwwroot) {
    return user_mnet_hosts_read_access($user, $wwwroot);
}

/**
 * grants a user access to a platform in his profile customized data.
 * Note that this operation should ONLY be perfomed on a host where the
 * user has his master account record (i.e. not MNET).
 * @param object $user
 * @param string $wwwroot
 */
function user_mnet_hosts_add_access($user, $wwwroot) {
    global $DB;

    if (empty($wwwroot)) {
        return 'Add access error : empty host';
    }

    if (empty($user)) {
        return 'Add access error : empty user';
    }

    $hostfieldname = user_mnet_hosts_make_accesskey($wwwroot, false);
    if ($userfield = $DB->get_record('user_info_field', array('shortname' => $hostfieldname))) {
        $params = array('fieldid' => $userfield->id, 'userid' => $user->id);
        if ($accessrec = $DB->get_record('user_info_data', $params)) {
            $accessrec->data = 1;
            $DB->update_record('user_info_data', $accessrec);
            return "Add access : updated for $user->username on $wwwroot with $hostfieldname";
        } else {
            $accessrec = new StdClass();
            $accessrec->fieldid = $userfield->id;
            $accessrec->userid = $user->id;
            $accessrec->data = 1;
            $DB->insert_record('user_info_data', $accessrec);
            return "Add access : granted for $user->username on $wwwroot with $hostfieldname";
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
function user_mnet_hosts_remove_access($user, $wwwroot) {
    global $OUTPUT, $DB;

    if (empty($wwwroot)) {
        if (debugging()) {
            echo $OUTPUT->notification('Add access : empty host');
        }
        return;
    }

    if (empty($user)) {
        if (debugging()) {
            echo $OUTPUT->notification('Add access : empty user');
        }
        return;
    }

    $hostfieldname = user_mnet_hosts_make_accesskey($wwwroot, false);
    if ($userfield = $DB->get_record('user_info_field', array('shortname' => $hostfieldname))) {
        $params = array('fieldid' => $userfield->id, 'userid' => $user->id);
        if ($accessrec = $DB->get_record('user_info_data', $params)) {
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
function user_mnet_hosts_read_access($user, $wwwroot) {
    global $OUTPUT, $DB;

    if (empty($wwwroot)) {
        if (debugging()) {
            echo $OUTPUT->notification('Read access : empty host');
        }
        return;
    }
    if (empty($user)) {
        if (debugging()) {
            echo $OUTPUT->notification('Read access : empty user');
        }
        return;
    }

    // Power users always have all accesses open.
    $context = context_system::instance();
    if (has_capability('block/user_mnet_hosts:accessall', $context, $user->id)) {
        return true;
    }

    $hostfieldname = user_mnet_hosts_make_accesskey($wwwroot, false);
    if ($userfield = $DB->get_record('user_info_field', array('shortname' => $hostfieldname))) {
        $params = array('fieldid' => $userfield->id, 'userid' => $user->id);
        if ($accessrec = $DB->get_record('user_info_data', $params)) {
            return $accessrec->data;
        }
    }
    return false;
}

function user_mnet_hosts_read_group_access($groupid, $wwwroot) {
}

/**
 * Implements special rules within a consistant multitenant network.
 * A user should be matched against username and idnumber, potentially registered
 * from another mnethostid. 
 * In case the currently calling mnethostid is a primary assignation, and the local register,
 * is NOT, then the local account should be fixed for reflecting this identity.
 * If not, leave the mnethostid intact, but use this acocunt for the roaming session.
 * @param object $remoteuser a remote user record landing on moodle
 * @param object $remotehost the remote host known peer.
 */
function user_mnet_hosts_get_local_user($remoteuser, $remotehost) {
    global $DB;

    $params = array('username' => $remoteuser->username, 'idnumber' => $remoteuser->idnumber);
    if ($localuser = $DB->get_record('user', $params)) {
        if ($remoteuser->profile_field_isprimaryassignation &&
                (($localuser->auth == 'mnet') ||
                        ($localuser->auth == 'multimnet'))) {
            $localuser->mnethostid = $remotehost->id;
        }

        return $localuser;
    }
}