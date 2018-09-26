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
 * Meta course enrolment plugin event handler definition.
 *
 * @package mod_booking
 * @category event
 * @copyright 2014 David Bogner
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$observers = array(
    array('eventname' => '\core\event\course_module_updated',
        'callback' => 'mod_booking_observer::course_module_updated'),
    array('eventname' => '\core\event\user_deleted',
        'callback' => 'mod_booking_observer::user_deleted'),
    array('eventname' => '\core\event\user_enrolment_deleted',
        'callback' => 'mod_booking_observer::user_enrolment_deleted'));

