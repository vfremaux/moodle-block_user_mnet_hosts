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

require_once($CFG->dirroot.'/blocks/user_mnet_hosts/db/install.php');

function xmldb_block_user_mnet_hosts_upgrade($oldversion = 0) {
    global $CFG, $DB;

    if ($oldversion < 2016051001) {
        xmldb_block_user_mnet_hosts_install();

        // User Mnet Host savepoint reached.
        upgrade_block_savepoint(true, 2016051001, 'user_mnet_hosts');
    }

    if ($oldversion < 2016051500) {
        // Converts old values of config to new component scope config.
        $globalkeys = $DB->get_records_select('config', " name LIKE 'block_u_m_h%' ");
        if ($globalkeys) {
            foreach ($globalkeys as $keyid => $key) {
                $oldkey = $key->name;
                $configkey = str_replace('block_u_m_h_', '', $oldkey);

                $newconf = new StdClass;
                $newconf->plugin = 'block_user_mnet_hosts';
                $newconf->name = $configkey;
                $newconf->value = $CFG->$oldkey;
                $params = array('plugin' => 'block_user_mnet_hosts', 'name' => $configkey);
                if (!$DB->record_exists('config_plugins', $params)) {
                    $DB->insert_record('config_plugins', $newconf);
                    set_config($key->name, null);
                }
            }
        }
        upgrade_block_savepoint(true, 2016051500, 'user_mnet_hosts');
    }

    return true;
}
