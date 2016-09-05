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
 * @package block_user_mnet_hosts
 * @category  blocks
 * @author Edouard Poncelet
 * @author  Valery Fremaux (valery.fremaux@gmail.com)
 * @copyright  2008 Valery Fremaux
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * This page will be used to manually synchronize hosts fields
 */

require('../../config.php');

$context = context_system::instance();

// Security.

require_login();
require_capability('moodle/site:config', $context);


$url = new moodle_url('/blocks/user_mnet_hosts/admin.php');

$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_title(get_string('pluginname', 'block_user_mnet_hosts'));
$PAGE->set_heading(get_string('admintitle', 'block_user_mnet_hosts'));
$PAGE->set_pagelayout('admin');
$PAGE->navbar->add(get_string('pluginname', 'block_user_mnet_hosts'));
$PAGE->navbar->add(get_string('admintitle', 'block_user_mnet_hosts'));
$PAGE->set_focuscontrol('');
$PAGE->set_cacheable(false);
$PAGE->set_button('');

echo $OUTPUT->header();

echo $OUTPUT->box_start();
echo get_string('syncplatforms', 'block_user_mnet_hosts');
echo $OUTPUT->help_icon('resync', 'block_user_mnet_hosts');
echo $OUTPUT->box_end();

$fieldkeystr = get_string('fieldkey', 'block_user_mnet_hosts');
$fieldnamestr = get_string('fieldname', 'block_user_mnet_hosts');

$accessfields = $DB->get_records_select('user_info_field', " shortname LIKE 'access%' ", array());

if ($accessfields) {
    $table = new html_table();
    $table->header = array("<b>$fieldkeystr</b>", "<b>$fieldnamestr</b>");
    $table->width = '70%';
    $table->size = array('40%', '60%');
    $table->align = array('left', 'left');
    foreach ($accessfields as $field) {
        $table->data[] = array($field->shortname, format_text($field->name));
    }

    echo $OUTPUT->box_start();
    echo '<center>';
    echo html_writer::table($table);
    echo '</center>';
    echo $OUTPUT->box_end();
}

echo '<div class="buttonarray" align="center">';
echo $OUTPUT->single_button(new moodle_url('/blocks/user_mnet_hosts/synch.php'), get_string('dosync', 'block_user_mnet_hosts'), 'get');
echo '</div>';

echo $OUTPUT->footer();
