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
          'debug' => false,
    ),
    array('h' => 'help',
          'H' => 'host',
          'd' => 'debug',
    )
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error("Not recognized options ".$unrecognized);
}

if ($options['help']) {
    $help = "
Resyncs all access fields analysing the mnet accessible environement. Be carefull
that this will cleanup undefined hosts.

Options:
    -h, --help            Print out this help.
    -H, --host            the virtual host you are working for.
    -d, --debug           Turn on debug mode.

Example:
\$sudo -u www-data /usr/bin/php blocks/user_mnet_hosts/cli/resync.php
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

$results = block_user_mnet_hosts_resync(true);

if (is_array($results)) {
    list($created, $ignored, $failed) = $results;
    mtrace("\tCreated : $created");
    mtrace("\tIgnored : $ignored");
    mtrace("\tFailed : $failed");
}

echo "Done.\n";
exit(0);