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
* adds a user profile fields category and registers it for access control
*
*/
function xmldb_block_user_mnet_hosts_install() {
    global $DB, $CFG;

    // if typical user field category does exist, make some for us
    if (!isset($CFG->accesscategory)) {
        $accesscategory = new stdClass;
        $accesscategory->name = get_string('accesscategorydefault', 'block_user_mnet_hosts');
        $accesscategory->sortorder = 1;
        $id = $DB->insert_record('user_info_category', $accesscategory);
        set_config('accesscategory', $id);
    }

    //  Create a field for ourself.
    $thishostlabel = user_mnet_hosts_make_accesskey($CFG->wwwroot, true);
    $thishostfield = user_mnet_hosts_make_accesskey($CFG->wwwroot, false);
    if (!$DB->get_record('user_info_field', array('shortname' => $thishostfield))) {
        $newfield = new stdClass;
        $newfield->shortname = $thishostfield;
        $newfield->name = get_string('fieldname', 'block_user_mnet_hosts').' '.$thishostlabel;
        $newfield->datatype = 'checkbox';
        $newfield->locked = 1;
        $newfield->visible = 0;
        $newfield->categoryid = $CFG->accesscategory;
        $DB->insert_record('user_info_field', $newfield);
    }

    // We need idnumber being sent over network when using controlled mnet host doors.

    $mnetconfig = get_config('moodle', 'mnetprofileexportfields');
    $mnetconfigarr = explode(',', $mnetconfig);
    if (!in_array('idnumber', $mnetconfigarr)) {
        $mnetconfigarr[] = 'idnumber';
    }
    set_config('mnetprofileexportfields', implode(',', $mnetconfigarr));

    $mnetconfig = get_config('moodle', 'mnetprofileimportfields');
    $mnetconfigarr = explode(',', $mnetconfig);
    if (!in_array('idnumber', $mnetconfigarr)) {
        $mnetconfigarr[] = 'idnumber';
    }
    set_config('mnetprofileexportfields', implode(',', $mnetconfigarr));

    return true;
}