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
 * @package     block_user_mnet_hosts
 * @category    blocks
 * @author      Edouard Poncelet
 * @author      Valery Fremaux (valery.fremaux@gmail.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Given a host name, builds properly an access filed name and access field label (fullname)
 * @param string $wwwroot the moodle hostname
 * @param bool $full if false, filteres the hostname and builds a compact short access key.
 */
function user_mnet_hosts_make_accesskey($wwwroot, $full = false) {

    $deepness = get_config('block_user_mnet_hosts', 'keydeepness');

    $accesskey = preg_replace('/https?:\/\//', '', $wwwroot);

    if (!$full) {
        // Filter if token.
        $accesskey = str_replace('-', '', $accesskey);
    }
    $accesskeyparts = explode('.', $accesskey);

    $keytokens = array();
    for ($i = 0; $i < $deepness; $i++) {
        $keytokens[] = array_shift($accesskeyparts);
    }

    if (!$full) {
        // Return short key name.
        $accesstoken = strtoupper(implode('', $keytokens));
        return 'access'.$accesstoken;
    } else {
        // Return full key name.
        $accesstoken = strtoupper(implode(' ', $keytokens));
        return $accesstoken;
    }
}

/**
 * set or unset an access to some peer
 * @param int $userid
 * @param bool $access true or false
 * @param string $wwwroot optional host root to give acces to. the access filed name is computed from the wwwroot url
 */
function user_mnet_hosts_set_access($userid, $access, $wwwroot = null) {
    global $CFG, $DB;

    if (!$wwwroot) {
        $wwwroot = $CFG->wwwroot;
    }

    $accesskey = user_mnet_hosts_make_accesskey($wwwroot, true);

    if (!$field = $DB->get_record('user_info_field', array('shortname' => $accesskey))) {
        // Try to setup install if results not having done before.
        if ($wwwroot == $CFG->wwwroot) {
            require_once($CFG->dirroot.'/blocks/user_mnet_hosts/db/install.php');
            xmldb_block_user_mnet_hosts_install();
            mtrace("Second chance $accesskey ");
            $field = $DB->get_record('user_info_field', array('shortname' => $accesskey));
        } else {
            return false;
        }
    }

    if ($data = $DB->get_record('user_info_data', array('userid' => $userid, 'fieldid' => $field->id))) {
        $data->data = $access;
        $DB->update_record('user_info_data', $data);
    } else {
        $data = new StdClass();
        $data->userid = $userid;
        $data->fieldid = $field->id;
        $data->data = $access;
        $DB->insert_record('user_info_data', $data);
    }
}

function user_mnet_hosts_get_hosts() {
    global $DB, $CFG;

    // Get the hosts and whether we are doing SSO with them.
    $sql = "
        SELECT DISTINCT
            h.id,
            h.name,
            h.wwwroot,
            a.name as application,
            a.display_name
        FROM
            {mnet_host} h,
            {mnet_application} a,
            {mnet_host2service} h2s_IDP,
            {mnet_service} s_IDP,
            {mnet_host2service} h2s_SP,
            {mnet_service} s_SP
        WHERE
            h.id != ? AND
            h.id = h2s_IDP.hostid AND
            h.deleted = 0 AND
            h.applicationid = a.id AND
            h2s_IDP.serviceid = s_IDP.id AND
            s_IDP.name = 'sso_idp' AND
            h2s_IDP.publish = '1' AND
            h.id = h2s_SP.hostid AND
            h2s_SP.serviceid = s_SP.id AND
            s_SP.name = 'sso_idp' AND
            h2s_SP.publish = '1'
        ORDER BY
             a.display_name,
             h.name";

    $hosts = $DB->get_records_sql($sql, array($CFG->mnet_localhost_id));
    return $hosts;
}

function user_mnet_hosts_get_access_fields() {
    global $DB, $USER;

    // If mnet access profile does not exist, setup profile.
    if (!$DB->get_records_select('user_info_field', " name LIKE 'access%' ")) {
       // TODO : Initialize mnetaccess profile data.
    }

    // Get user profile fields for access to hosts.
    $sql = "
        SELECT
            uif.shortname,
            data
        FROM
            {user_info_data} uid,
            {user_info_field} uif
        WHERE
            uid.userid = ? AND
            uid.fieldid = uif.id AND
            uif.shortname LIKE 'access%'
    ";

    $mnet_accesses = array();

    if ($usermnetaccessfields = $DB->get_records_sql_menu($sql, array($USER->id))) {
        foreach($usermnetaccessfields as $key => $datum) {
            $key = str_replace('access', '', $key);
            $mnet_accesses[str_replace('-', '', strtolower($key))] = str_replace('-', '', $datum);
        }
    }

    return $mnet_accesses;
}

/**
 * Creates and synchronize expected access control fields upon mnet environment analysis.
 *
 * Using vmoodle source may not require that the vmoodle records be effective, this is the case f.e, on
 * vmoodle slave instances. Vmoodle table values may be fed for environment definition, without having
 * no real use. VMoodle switching will only be effective on mainhost.
 *
 * @param bool $withcleanup If true, will delete all non relevant fields that do not match the environment
 * @param string $source the source table from where to consider the available environment. It can be mnet_host or block_vmoodle
 * records
 * @return void
 */
function block_user_mnet_hosts_resync($withcleanup = false, $source = 'mnet_host') {
    global $CFG, $DB;

    $expectedself = user_mnet_hosts_make_accesskey($CFG->wwwroot, false); // Need cleaning name from hyphens.

    // If typical user field category does exist, make some for us.
    if (!isset($CFG->accesscategory)) {
        $accesscategory = new stdClass;
        $accesscategory->name = get_string('accesscategorydefault', 'block_user_mnet_hosts');
        $accesscategory->sortorder = 1;
        $id = $DB->insert_record('user_info_category', $accesscategory);
        set_config('accesscategory', $id);
    }

    // We are going to get all non-deleted hosts from our database.
    if ($source == 'mnet_host') {
        $knownhosts = $DB->get_records('mnet_host', array('deleted' => '0'), '', 'id,wwwroot');
    } else if ($source == 'block_vmoodle') {
        $knownhosts = $DB->get_records('block_vmoodle', array('enabled' => 1), '', 'id,vhostname AS wwwroot');
    } else {
        $knownhosts = $DB->get_records('block_vmoodle', array('enabled' => 1), '', 'id,vhostname AS wwwroot');
        if ($mnetknownhosts = $DB->get_records('mnet_host', array('deleted' => '0'), '', 'id,wwwroot')) {
            foreach ($mnetknownhosts as $mhid => $mh) {
                if (empty($mh->wwwroot)) {
                    continue;
                }
                // Only add those who are not in vmoodle register.
                if (!$DB->record_exists('block_vmoodle', array('vhostname' => $mh->wwwroot))) {
                    // Securise that id do not overlap.
                    $knownhosts[1000 + $mhid] = $mh;
                }
            }
        }
    }

    // Then we get all accessfields.
    $accessfields = $DB->get_records_select('user_info_field'," shortname LIKE 'access%' ", array());

    // We create local variables to monitor the actions.
    $created = 0;
    $ignored = 0;
    $failed = 0;

    // Now we need to oppose our hosts to our fields.
    foreach ($knownhosts as $host) {

        if ($host->wwwroot == '') {
            $ignored++;
            continue;
        }

        $expectedfieldname = user_mnet_hosts_make_accesskey($host->wwwroot, false); // need cleaning name from hyphens
        $hostkey = user_mnet_hosts_make_accesskey($host->wwwroot, true);
        $results = false;

        if ($accessfields) {
            foreach ($accessfields as $field) {
                //If we have a match, we do have the field, we can skip the host
                if ($field->shortname == $expectedfieldname) {
                    $results = true;
                    $ignored++;
                    break;
                }
            }
        }

        if (!$results) {
            $newfield = new stdClass;
            $newfield->shortname = $expectedfieldname;
            $newfield->name = get_string('fieldname', 'block_user_mnet_hosts').' '.$hostkey;
            $newfield->datatype = 'checkbox';
            $newfield->locked = 1;
            $newfield->categoryid = $CFG->accesscategory;
            $validnames[] = $expectedfieldname;

            if (defined('CLI_SCRIPT')) {
                mtrace('Creating access field '.$newfield->shortname);
            }
            if ($DB->insert_record('user_info_field', $newfield)) {
                $created++;
            } else {
                $failed++;
            }
        }
    }

    if ($withcleanup) {
        // Finally cleanup all fields and data not matching hosts
        if (!empty($validnames)) {
            list ($insql, $inparams) = $DB->get_in_or_equal($validnames, SQL_PARAMS_QM, 'param', false);
            $alltodeletefields = $DB->get_records_select('user_info_field', " shortname LIKE 'access%' AND shortname $insql ", $inparams);
            if (!empty($alltodeletefields)) {
                foreach($alltodeletefields as $f) {

                    // Protect ourself.
                    if ($f->shortname == $expectedself) continue;

                    if (defined('CLI_SCRIPT')) {
                        mtrace('Deleting access field '.$f->shortname);
                    }
                    // Delete full related field information.
                    $DB->delete_records('user_info_data', array('fieldid' => $f->id));
                    $DB->delete_records('user_info_field', array('id' => $f->id));
                }
            }
        }
    }

    // Tag all local users for localhost field
    $thishostfieldname = user_mnet_hosts_make_accesskey($CFG->wwwroot, false);
    $thishostfield = $DB->get_record('user_info_field', array('shortname' => $thishostfieldname));
    if ($thishostfield) {
        $rs = $DB->get_recordset_select('user', " auth != 'mnet' OR auth != 'multimnet' ");
        if ($rs) {
            foreach ($rs as $u) {
                if (!$DB->record_exists('user_info_data', array('userid' => $u->id, 'fieldid' => $thishostfield->id))) {
                    $field = new StdClass;
                    $field->userid = $u->id;
                    $field->fieldid = $thishostfield->id;
                    $field->data = 1;
                    $DB->insert_record('user_info_data', $field);
                }
            }
        }
        $rs->close();
    }

    return array($created, $ignored, $failed);
}