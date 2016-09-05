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

defined('MOODLE_INTERNAL') || die();

if (!isset($CFG->accesscategory)) {
    xmldb_block_user_mnet_hosts_install();
}

$syncstr = get_string('synchonizingaccesses', 'block_user_mnet_hosts');

$settings->add(new admin_setting_heading('synchronization', get_string('synchonizingaccesses', 'block_user_mnet_hosts'), "<a href=\"{$CFG->wwwroot}/blocks/user_mnet_hosts/admin.php\">$syncstr</a>"));

$sourceoptions = array('mnet_host' => get_string('mnetsource', 'block_user_mnet_hosts'),
                       'block_vmoodle' => get_string('vmoodlesource', 'block_user_mnet_hosts'),
                       'vmoodle_and_mnet' => get_string('vmoodleandmnetsource', 'block_user_mnet_hosts'));

$settings->add(new admin_setting_configselect('block_user_mnet_hosts/source', get_string('configaccesssource', 'block_user_mnet_hosts'),
       get_string('configaccesssource_desc', 'block_user_mnet_hosts'), 'mnet_host', $sourceoptions));

$settings->add(new admin_setting_configtext('block_user_mnet_hosts/keydeepness', get_string('configkeydeepness', 'block_user_mnet_hosts'),
       get_string('configkeydeepness_desc', 'block_user_mnet_hosts'), 1, PARAM_INT, 2));

$settings->add(new admin_setting_heading('display', get_string('display', 'block_user_mnet_hosts'), ''));

$settings->add(new admin_setting_configtext('block_user_mnet_hosts/displaylimit', get_string('configdisplaylimit', 'block_user_mnet_hosts'),
       get_string('configdisplaylimit_desc', 'block_user_mnet_hosts'), 40));

$settings->add(new admin_setting_heading('mnetbehaviour', get_string('mnetbehaviour', 'block_user_mnet_hosts'), ''));

$settings->add(new admin_setting_configcheckbox('block_user_mnet_hosts/maharapassthru', get_string('configmaharapassthru', 'block_user_mnet_hosts'),
       get_string('configmaharapassthru_desc', 'block_user_mnet_hosts'), 1));

$settings->add(new admin_setting_configcheckbox('block_user_mnet_hosts/singleaccountcheck', get_string('configsingleaccountcheck', 'block_user_mnet_hosts'),
       get_string('configsingleaccountcheck_desc', 'block_user_mnet_hosts'), 1));

$settings->add(new admin_setting_configcheckbox('block_user_mnet_hosts/localadminoverride', get_string('configlocaladminoverride', 'block_user_mnet_hosts'),
       get_string('configlocaladminoverride_desc', 'block_user_mnet_hosts'), 0));

$settings->add(new admin_setting_configcheckbox('disablemnetimportfilter', get_string('configdisablemnetimportfilter', 'block_user_mnet_hosts'),
       get_string('configdisablemnetimportfilter_desc', 'block_user_mnet_hosts'), 1));