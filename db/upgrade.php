<?php
// This file keeps track of upgrades to 
// the vmoodle block
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installtion to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the functions defined in lib/ddllib.php

require_once($CFG->dirroot.'/blocks/user_mnet_hosts/db/install.php');

function xmldb_block_user_mnet_hosts_upgrade($oldversion = 0) {
    global $CFG, $DB;

    $result = true;

    if ($oldversion < 2016051001) {
        xmldb_block_user_mnet_hosts_install();

        /// customlabel savepoint reached
        upgrade_block_savepoint($result, 2016051001, 'user_mnet_hosts');
    }

    if ($oldversion < 2016051500) {
        // Converts old values of config to new component scope config
        $globalkeys = $DB->get_records_select('config', " name LIKE 'block_u_m_h%' ");
        if ($globalkeys) {
            foreach ($globalkeys as $keyid => $key) {
                $oldkey = $key->name;
                $configkey = str_replace('block_u_m_h_', '', $oldkey);

                $newconf = new StdClass;
                $newconf->plugin = 'block_user_mnet_hosts';
                $newconf->name = $configkey;
                $newconf->value = $CFG->$oldkey;
                if (!$DB->record_exists('config_plugins', array('plugin' => 'block_user_mnet_hosts', 'name' => $configkey))) {
                    $DB->insert_record('config_plugins', $newconf);
                    set_config($key->name, null);
                }
            }
        }
        upgrade_block_savepoint($result, 2016051500, 'user_mnet_hosts');
    }

    return $result;
}
