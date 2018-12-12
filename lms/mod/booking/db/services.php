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
 * Web service for mod booking
 * @package    mod_booking
 * @subpackage db
 * @since      Moodle 3.4
 * @copyright  2018 David Bogner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = array(
        'mod_booking_update_bookingnotes' => array(
                'classname'     => 'mod_booking\external',
                'methodname'    => 'update_bookingnotes',
                'description'   => 'Update the booking notes via AJAX',
                'type'          => 'write',
                'ajax'          => true,
                'capabilities'  => 'mod/booking:readresponses',
                'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE, 'local_mobile')
        ),
);
