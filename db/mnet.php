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
 * This file contains the mnet services for the user_mnet_host plugin
 *
 * @since 2.0
 * @package blocks
 * @subpackage user_mnet_hosts
 * @copyright 2012 Valery Fremaux
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$publishes = array(
    'mnetaccess' => array(
		'servicename' => 'mnetaccess',
		'description' => get_string('mnetaccess_service_name', 'block_user_mnet_hosts'),
        'apiversion' => 1,
        'classname'  => 'block_user_mnet_hosts',
        'filename'   => 'blocks/user_mnet_hosts/block_user_mnet_hosts.php',
        'methods'    => array(
            'remote_user_mnet_check'
        ),
    ),
);
$subscribes = array(
    'mnetaccess' => array(
        'remote_user_mnet_check' => 'blocks/user_mnet_hosts/block_user_mnet_hosts.php/remote_user_mnet_check',
    ),
);
