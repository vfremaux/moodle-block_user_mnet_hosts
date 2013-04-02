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
	require_capability('moodle/site:doanything', get_context_instance(CONTEXT_SYSTEM));

   	$full = get_string('single_full', 'block_user_mnet_hosts');
   	$short = get_string('single_short', 'block_user_mnet_hosts');

    $navlinks[] = array('name' => $full, 'link' => "$CFG->wwwroot", 'type' => 'misc');
    $navigation = build_navigation($navlinks);

    print_header($full, $short, $navigation, '', '', false, '');

    print_box_start();
    echo(get_string('syncplatforms', 'block_user_mnet_hosts'));
    helpbutton('resync', get_string('helpsync','block_user_mnet_hosts'), 'block_user_mnet_hosts');
    print_box_end();

    echo('<div="buttonarray" align="center">');
    print_single_button('/blocks/user_mnet_hosts/synch.php','', get_string('dosync', 'block_user_mnet_hosts'));
    echo('</div>');

    print_footer($COURSE);
?>