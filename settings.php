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

$label = get_string('synchonizingaccesses', 'block_user_mnet_hosts');
$html = "<a href=\"{$CFG->wwwroot}/blocks/user_mnet_hosts/admin.php\">$syncstr</a>";
$settings->add(new admin_setting_heading('synchronization', $label, $html));

$sourceoptions = array('mnet_host' => get_string('mnetsource', 'block_user_mnet_hosts'),
                       'vmoodle' => get_string('vmoodlesource', 'block_user_mnet_hosts'),
                       'vmoodle_and_mnet' => get_string('vmoodleandmnetsource', 'block_user_mnet_hosts'));

$key = 'block_user_mnet_hosts/source';
$label = get_string('configaccesssource', 'block_user_mnet_hosts');
$desc = get_string('configaccesssource_desc', 'block_user_mnet_hosts');
$settings->add(new admin_setting_configselect($key, $label, $desc, 'mnet_host', $sourceoptions));

$key = 'block_user_mnet_hosts/keydeepness';
$label = get_string('configkeydeepness', 'block_user_mnet_hosts');
$desc = get_string('configkeydeepness_desc', 'block_user_mnet_hosts');
$settings->add(new admin_setting_configtext($key, $label, $desc, 1, PARAM_INT, 2));

$label = get_string('display', 'block_user_mnet_hosts');
$settings->add(new admin_setting_heading('display', $label, ''));

$key = 'block_user_mnet_hosts/displaylimit';
$label = get_string('configdisplaylimit', 'block_user_mnet_hosts');
$desc = get_string('configdisplaylimit_desc', 'block_user_mnet_hosts');
$settings->add(new admin_setting_configtext($key, $label, $desc, 40));

$label = get_string('mnetbehaviour', 'block_user_mnet_hosts');
$settings->add(new admin_setting_heading('mnetbehaviour', $label, ''));

$key = 'block_user_mnet_hosts/maharapassthru';
$label = get_string('configmaharapassthru', 'block_user_mnet_hosts');
$desc = get_string('configmaharapassthru_desc', 'block_user_mnet_hosts');
$settings->add(new admin_setting_configcheckbox($key, $label, $desc, 1));

$key = 'block_user_mnet_hosts/singleaccountcheck';
$label = get_string('configsingleaccountcheck', 'block_user_mnet_hosts');
$desc = get_string('configsingleaccountcheck_desc', 'block_user_mnet_hosts');
$settings->add(new admin_setting_configcheckbox($key, $label, $desc, 1));

$authplugins = get_enabled_auth_plugins(true);
$authoptions = array('' => get_string('noauthcheck', 'block_user_mnet_hosts'));
foreach ($authplugins as $authname) {
    $authoptions[$authname] = get_string('pluginname', 'auth_'.$authname);
}
$key = 'block_user_mnet_hosts/forceauth';
$label = get_string('configforceauth', 'block_user_mnet_hosts');
$desc = get_string('configforceauth_desc', 'block_user_mnet_hosts');
$settings->add(new admin_setting_configselect($key, $label, $desc, '', $authoptions));

$key = 'block_user_mnet_hosts/localadminoverride';
$label = get_string('configlocaladminoverride', 'block_user_mnet_hosts');
$desc = get_string('configlocaladminoverride_desc', 'block_user_mnet_hosts');
$settings->add(new admin_setting_configcheckbox($key, $label, $desc, 0));

$key = 'block_user_mnet_hosts/allowremoteadmins';
$label = get_string('configallowremoteadmins', 'block_user_mnet_hosts');
$desc = get_string('configallowremoteadmins_desc', 'block_user_mnet_hosts');
$settings->add(new admin_setting_configcheckbox($key, $label, $desc, 1));

$key = 'block_user_mnet_hosts/newwindow';
$label = get_string('confignewwindow', 'block_user_mnet_hosts');
$desc = get_string('confignewwindow_desc', 'block_user_mnet_hosts');
$settings->add(new admin_setting_configcheckbox($key, $label, $desc, 0));

$key = 'disablemnetimportfilter';
$label = get_string('configdisablemnetimportfilter', 'block_user_mnet_hosts');
$desc = get_string('configdisablemnetimportfilter_desc', 'block_user_mnet_hosts');
$settings->add(new admin_setting_configcheckbox($key, $label, $desc, 1));

$label = get_string('ldapmapping', 'block_user_mnet_hosts');
$html = '';
$settings->add(new admin_setting_heading('ldaphdr', $label, $html));

$key = 'block_user_mnet_hosts/ldap_access_attributes';
$label = get_string('configldapaccessattributes', 'block_user_mnet_hosts');
$desc = get_string('configldapaccessattributes_desc', 'block_user_mnet_hosts');
$settings->add(new admin_setting_configtext($key, $label, $desc, ''));

$key = 'block_user_mnet_hosts/ldap_host_patterns';
$label = get_string('configldaphostpatterns', 'block_user_mnet_hosts');
$desc = get_string('configldaphostpatterns_desc', 'block_user_mnet_hosts');
$settings->add(new admin_setting_configtext($key, $label, $desc, '^(.*)$'));

$key = 'block_user_mnet_hosts/host_wwwroot_mask';
$label = get_string('confighostwwwrootmask', 'block_user_mnet_hosts');
$desc = get_string('confighostwwwrootmask_desc', 'block_user_mnet_hosts');
$settings->add(new admin_setting_configtext($key, $label, $desc, 'http://%HOSTINFO%.mymoodledomain.edu'));
