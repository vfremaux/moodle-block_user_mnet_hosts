<?php

/**
 * This page will be used to manually synchronize hosts fields
 *
 * @author Edouard Poncelet
 * @package block-publishflow
 * @category blocks
 *
 **/

	require_once('../../config.php');

	global $CFG;
	// Security : only for master admins
	require_capability('moodle/site:doanything', context_system::instance());

   	$full = get_string('single_full', 'block_user_mnet_hosts');
   	$short = get_string('single_short', 'block_user_mnet_hosts');

    $navlinks[] = array('name' => $full, 'link' => "$CFG->wwwroot", 'type' => 'misc');
    $navigation = build_navigation($navlinks);

    $PAGE->set_title($full);
    $PAGE->set_heading($short);
    /* SCANMSG: may be additional work required for $navigation variable */
    $PAGE->set_focuscontrol('');
    $PAGE->set_cacheable(false);
    $PAGE->set_button('');
    echo $OUTPUT->header();

    echo $OUTPUT->box_start();
    echo(get_string('syncplatforms', 'block_user_mnet_hosts'));
    $OUTPUT->help_icon('resync', 'block_user_mnet_hosts');
    echo $OUTPUT->box_end();

    echo('<div="buttonarray" align="center">');
    echo $OUTPUT->single_button(new moodle_url('/blocks/user_mnet_hosts/synch.php', ''), get_string('dosync', 'block_user_mnet_hosts'), 'get');
    echo('</div>');

    echo $OUTPUT->footer($COURSE);
?>