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
 * This page roughly synchronizes the access field definitin used to control visibility of MNET
 * doors with known declared mnet hosts.
 *
 * @TODO : refine the model choosing only hosts for which we have the SSO service enabled.
 *
 * @category blocks
 * @package block_user_mnet_hosts
 * @author E.Poncelet
 * @version 2.0
 */

require('../../config.php');
require_once($CFG->dirroot.'/blocks/user_mnet_hosts/locallib.php');

$context = context_system::instance();

// Only for admins.
require_login();
require_capability('moodle/site:config', context_system::instance());

$full = get_string('syncfull', 'block_user_mnet_hosts');
$short = get_string('syncshort', 'block_user_mnet_hosts');

$url = new moodle_url('/blocks/user_mnet_hosts/sync.php');

$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->navbar->add($full);
$PAGE->set_title($full);
$PAGE->set_heading($short);
$PAGE->set_pagelayout('admin');
$PAGE->set_cacheable(false);

echo $OUTPUT->header();

$config = get_config('block_user_mnet_hosts');

list($created, $ignored, $failed) = block_user_mnet_hosts_resync(false, $config->source);

echo $OUTPUT->box_start();
echo(get_string('createdfields', 'block_user_mnet_hosts').$created.'<br/>');
echo(get_string('ignoredfields', 'block_user_mnet_hosts').$ignored.'<br/>');
echo(get_string('failedfields', 'block_user_mnet_hosts').$failed.'<br/>');
echo $OUTPUT->box_end();

echo('<div class="Button" align="center">');
$buttonurl = new moodle_url('/blocks/user_mnet_hosts/admin.php');
echo $OUTPUT->single_button($buttonurl, get_string('backsettings', 'block_user_mnet_hosts'), 'get');
echo('</div>');

echo $OUTPUT->footer($COURSE);
