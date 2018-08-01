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
 * External lessonplan API
 *
 * @package    mod_assign
 * @since      Moodle 2.4
 * @copyright  2012 Paul Charsley
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir . "/externallib.php");

use core_completion\progress;

class block_remuiblck_external extends external_api
{
    /**
    * Returns description of method parameters
    * @return external_function_parameters
    */
    public static function get_course_progress_parameters()
    {
        //get_course_progress_parameters() always return an external_function_parameters().
        // The external_function_parameters constructor expects an array of external_description.
        return new external_function_parameters(
            // a external_description can be: external_value, external_single_structure or external_multiple structure
                array('courseid' => new external_value(PARAM_INT, 'Course Id'))
        );
    }
 
    /**
     * The function itself
     * @return string welcome message
     */
    public static function get_course_progress($courseid)
    {
 
        global $DB, $USER;
        $percentage = 0;
        $course_progress = new stdClass();
        $course = get_course($courseid);

        $coursecontext = context_course::instance($courseid);
        $students = get_role_users(5, $coursecontext);

        foreach ($students as $studentid => $student) {
            $percentage += progress::get_course_progress_percentage($course, $student->id);
        }

        $course_progress->id = $course->id;
        $course_progress->fullname  = $course->fullname;
        $course_progress->shortname = $course->shortname;
        $course_progress->category  = $course->category;
        $course_progress->format    = $course->format;
        $course_progress->startdate = date("Y M, d", substr($course->startdate, 0, 10));
        $course_progress->enddate   = date("Y M, d", substr($course->enddate, 0, 10));
        $course_progress->timecreated = $course->timecreated;

        $course_progress->percentage = 0;

        if (0 != count($students)) {
            $course_progress->percentage  =  ceil(round($percentage / count($students), 2));
        } else {
            $course_progress->NoEnrollment = 'NoEnrollment';
        }

        $course_progress->enrolledStudents = count($students);

        return  $course_progress;
    }
 
    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_course_progress_returns()
    {
        return new external_single_structure(
            array(
                    'id' => new external_value(PARAM_INT, 'id of course'),
                    'fullname' => new external_value(PARAM_RAW, 'Full Name of the Course'),
                    'shortname' => new external_value(PARAM_RAW, 'Short Name of the Course'),
                    'category' => new external_value(PARAM_RAW, 'Category of the Course'),
                    'format' => new external_value(PARAM_RAW, 'Course format'),
                    'startdate' => new external_value(PARAM_RAW, 'Starting date of the course'),
                    'enddate' => new external_value(PARAM_RAW, 'End date of the course'),
                    'timecreated' => new external_value(PARAM_RAW, 'Time created'),
                    'percentage' => new external_value(PARAM_INT, 'Progress percentage'),
                    'enrolledStudents' => new external_value(PARAM_INT, 'Number of enrolled students in the course'),
                )
        );
    }
}
