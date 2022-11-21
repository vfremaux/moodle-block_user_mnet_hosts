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
 * @package    block_user_mnet_hosts
 * @subpackage cli
 * @copyright  2008 Valery Fremaux
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CLI_VMOODLE_PRECHECK;

define('CLI_SCRIPT', true);
define('CACHE_DISABLE_ALL', true);
$CLI_VMOODLE_PRECHECK = true; // Force first config to be minimal.

require(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');

if (!isset($CFG->dirroot)) {
    die ('$CFG->dirroot must be explicitely defined in moodle config.php for this script to be used');
}

require_once($CFG->dirroot.'/lib/clilib.php'); // Cli only functions.

list($options, $unrecognized) = cli_get_params(
    array('help' => false,
          'host' => false,
          'users' => false,
          'where' => false,
          'wwwroot' => false,
          'action' => false,
          'simulate' => false,
          'debug' => false,
    ),
    array('h' => 'help',
          'U' => 'users',
          'W' => 'where',
          'w' => 'wwwroot',
          'a' => 'action',
          'H' => 'host',
          'S' => 'simulate',
          'D' => 'dryrun',
    )
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error("Not recognized options ".$unrecognized);
}

if ($options['help']) {
    $help = "
Set or unset an access mark for one handled host to users or a selection of users.

Options:
    -h, --help            Print out this help.
    -H, --host            the virtual host you are working for.
    -U, --users           User id list.
    -W, --where           A where clause given as <userfieldname>:<value> or profile_field_<fieldname>:<value>.
    -w, --wwwroot         The target peer host wwwroot to give access to.
    -a, --action          'set' (default) or 'unset'.
    -S, --simulate        If set to 1, dryruns the command and do not write anything to db.
    -d, --debug           Turn on debug mode.

Examples:
\$sudo -u www-data /usr/bin/php blocks/user_mnet_hosts/cli/setaccess.php [--host=<moodlewwwroot>] --action=set --accessfield=accessCOMMUN --users=213,214,215
\$sudo -u www-data /usr/bin/php blocks/user_mnet_hosts/cli/setaccess.php [--host=<moodlewwwroot>] --action=set --accessfield=accessCOMMUN --where=profile_field_usertype=teacher
";

    echo $help;
    exit(0);
}

if (!empty($options['host'])) {
    // Arms the vmoodle switching.
    echo('Arming for '.$options['host']."\n"); // Mtrace not yet available.
    define('CLI_VMOODLE_OVERRIDE', $options['host']);
}

// Replay full config whenever. If vmoodle switch is armed, will switch now config.

require(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php'); // Global moodle config file.
require_once($CFG->dirroot.'/blocks/user_mnet_hosts/locallib.php');
echo('Config check : playing for '.$CFG->wwwroot."\n");

if (!empty($options['debug'])) {
    $CFG->debug = E_ALL;
}

// Input checks

if (empty($options['users']) && empty($options['where'])) {
    die("Either \"users\" or \"where\" should be used. Use the keyword where=all for all users\n");
}

if (empty($options['wwwroot'])) {
    die("No target wwwroot given\n");
}

$accesskey = user_mnet_hosts_make_accesskey($options['wwwroot'], false);
if (!$DB->get_record('user_info_field', ['shortname' => $accesskey])) {
    die("No access field for this wwwroot. User mnet host register may not be synchronized, or wwwroot is not registered.\n");
}

if (empty($options['action'])) {
    $options['action'] = 'set';
}

if (!in_array($options['action'], ['set', 'unset'])) {
    die("Bad action word : use 'set' or 'unset'.\n");
}

if ($options['action'] == 'set') {
    $access = 1;
} else {
    $access = 0;
}

if (!empty($options['users'])) {
    $userids = explode(',', $options['users']);
} else {
    if (!empty($options['where'])) {

        if ($options['where'] == 'all') {
            $users = $DB->get_records('user', ['deleted' => 0], 'id,username,firstname,lastname,idnumber');
        } else {

            list($fieldname, $value) = explode(':', $options['where']);
            if (strpos($fieldname, 'profile_field') === 0) {
                // Is a profile field.
                $fieldname = str_replace('profile_field_', '', $fieldname);
                $field = $DB->get_record('user_info_field', ['shortname' => $fieldname]);
                if (!$field) {
                    die("Invalid profile field shortname in where clause.\n");
                }

                $comparetext = $DB->sql_compare_text('data');
                $sql = "
                    SELECT
                        *
                    FROM
                        {user_info_data}
                    WHERE
                        fieldid = :fieldid AND
                        $comparetext
                ";
                $userdata = $DB->get_records_sql($sql, ['fieldid' => $field->id, 'data' => $value]);
                $users = [];
                if (!empty($userdata)) {
                    foreach ($userdata as $ud) {
                        $u = $DB->get_record('user', ['id' => $ud->userid], 'id,firstname,lastname,idnumber,username');
                        if (!empty($u)) {
                            $users[$u->id] = $u;
                        }
                    }
                }
            } else {
                // The field is a standard user record field.
                $users = $DB->get_records('user', [$fieldname => $value], 'id', 'id,username,firstname,lastname,idnumber');
            }
        }
    } else {
        // Should not happen. 
        assert(1);
    }
}

if (empty($users)) {
    die("No users to process\nExciting.\n");
}

foreach ($users as $u) {
    echo "Setting access $access to [$u->username] $u->firstname $u->lastname ($u->idnumber)\n";
    user_mnet_hosts_set_access($u->id, $access, $options['wwwroot']);
}

echo "Done.\n";
exit(0);