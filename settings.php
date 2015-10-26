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

if (!defined('MOODLE_INTERNAL')) {
    die ("You cannot use this script this way");
}

/// records in config the user field category holding access control fields

    if (!isset($CFG->accesscategory)) {
        $accesscategory = new stdClass;
        $accesscategory->name = get_string('accesscategory', 'block_user_mnet_hosts');
        $accesscategory->sortorder = 1;
        $id = $DB->insert_record('user_info_category', $accesscategory);
        set_config('accesscategory', $id);
    }

// Adding self access field.

    preg_match('/https?:\/\/([^.]*)/', $CFG->wwwroot, $matches);
    $hostprefix = $matches[1];
    $expectedname = 'access'.str_replace('-', '', strtoupper($hostprefix)); // need cleaning name from hyphens

    if (!$selfaccess = $DB->get_record('user_info_field', array('shortname' => $expectedname))) {
        $newfield = new stdClass;
        $newfield->shortname = $expectedname;
        $hostkey = strtoupper($hostprefix);
        $newfield->name = get_string('fieldname', 'block_user_mnet_hosts').' '.$hostkey;
        $newfield->datatype = 'checkbox';
        $newfield->locked = 1;
        $newfield->categoryid = $CFG->accesscategory;
        if ($fieldid = $DB->insert_record('user_info_field', $newfield)) {
            // we need setup a field value for all non deleted users
            if ($users = $DB->get_records('user', array('deleted' => 0))) {
                foreach ($users as $u) {
                    $newvalue = new StdClass;
                    $newvalue->userid = $u->id;
                    $newvalue->fieldid = $fieldid;
                    $newvalue->data = 1;
                    if (!$DB->record_exists('user_info_data', array('userid' => $u->id, 'fieldid' => $fieldid))) {
                        $DB->insert_record('user_info_data', $newvalue);
                    }
                }
            }
        }
    }

    $syncstr = get_string('synchonizingaccesses', 'block_user_mnet_hosts');
    $settings->add(new admin_setting_heading('synchronization', get_string('synchonizingaccesses', 'block_user_mnet_hosts'), "<a href=\"{$CFG->wwwroot}/blocks/user_mnet_hosts/admin.php\">$syncstr</a>"));

    $settings->add(new admin_setting_configcheckbox('block_u_m_h_maharapassthru', get_string('maharapassthru', 'block_user_mnet_hosts'),
           get_string('configmaharapassthru', 'block_user_mnet_hosts'), 1));

