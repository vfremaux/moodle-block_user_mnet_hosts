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

define('CLI_SCRIPT', true);
define('CLI_VMOODLE_OVERRIDE', true);

require(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php'); // Global moodle config file.
require_once($CFG->dirroot.'/lib/clilib.php'); // CLI only functions.

// Ensure options are blank.
unset($options);

// Now get cli options.

list($options, $unrecognized) = cli_get_params(
    array(
        'help'             => false,
        'fullstop'         => false,
        'wwwroot'          => false,
        'users'            => false,
        'where'            => false,
        'action'           => false,
        'debug'            => false,
    ),
    array(
        'h' => 'help',
        's' => 'fullstop',
        'W' => 'where',
        'U' => 'users',
        'a' => 'action',
        'w' => 'wwwroot',
        'd' => 'debug',
    )
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error("$unrecognized is not a recognized option\n");
}

if ($options['help']) {
    $help = "
Set or unset an access mark for one handled host to users or a selection of users.

    Options:
    -h, --help              Print out this help
    -s, --fullstop          Stops on furst error.
    -U, --users             User id list.
    -W, --where             A where clause given as <userfieldname>=<value> or profile_field_<fieldname>=<value>.
    -w, --wwwroot           The target peer host wwwroot to give access to.
    -a, --action            'set' (default) or 'unset'.
    -S, --simulate          'set' (default) or 'unset'.
    -d, --debug             Stops on furst error.

"; // TODO: localize - to be translated later when everything is finished.

    echo $help;
    die;
}

$debug = '';
if (!empty($options['debug'])) {
    $debug = ' --debug ';
}

if (empty($options['wwwroot'])) {
    die("No target wwwroot given\n");
}
$wwwroot = '--wwwroot='.$options['wwwroot'];

$users = '';
if (!empty($options['users'])) {
    $users = ' --users='.$options['users'];
}

$where = '';
if (!empty($options['where'])) {
    $where = ' --where='.$options['where'];
}

$simulate = '';
if (!empty($options['simulate'])) {
    $simulate = ' --simulate='.$options['simulate'];
}

$action = '';
if (!empty($options['action'])) {
    $action = ' --action='.$options['action'];
} else {
    $action = ' --action=set';
}

$allhosts = $DB->get_records('local_vmoodle', array('enabled' => 1));

// Start updating.
// Linux only implementation.

echo "Starting setting acccess for users....\n";

$i = 1;
foreach ($allhosts as $h) {
    $workercmd = "php {$CFG->dirroot}/blocks/user_mnet_hosts/cli/setaccess.php {$debug} --host=\"{$h->vhostname}\"
        {$action} {$users} {$where} {$wwwroot} {$simulate}";

    mtrace("Executing $workercmd\n######################################################\n");
    $output = array();
    exec($workercmd, $output, $return);
    echo implode("\n", $output);
    if ($return) {
        if (!empty($options['fullstop'])) {
            echo implode("\n", $output)."\n";
            die("Worker ended with error\n");
        } else {
            echo "Worker ended with error:\n";
            echo implode("\n", $output)."\n";
        }
    } else {
        if (!empty($options['verbose'])) {
            echo implode("\n", $output)."\n";
        }
    }
}

echo "Done.\n";
