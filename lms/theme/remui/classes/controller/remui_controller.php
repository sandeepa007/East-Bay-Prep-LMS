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

namespace theme_remui\controller;

use theme_remui\renderables\remui_sidebar;

// use theme_remui\utility;
defined('MOODLE_INTERNAL') || die();

/**
 * Handles requests regarding all ajax operations.
 *
 * @package   theme_remui
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class remui_controller extends controller_abstract
{
    /**
     * Do any security checks needed for the passed action
     *
     * @param string $action
     */
    public function require_capability($action)
    {
        $action = $action;
    }

     /**
      * get the remui sidebar mustache content
      *
      * @return json encode array
      */
    public function get_remui_sidebar_action()
    {
        global $PAGE;

        $remui_sidebar = new remui_sidebar();
        echo $PAGE->get_renderer('core')->render_remui_sidebar($remui_sidebar);
    }

    // public function get_add_activity_course_list_action() {
    //     $courseid = required_param('courseid', PARAM_INT);
    //     return json_encode(theme_controller::get_courses_add_activity($courseid));
    //     // return json_encode(array('html' => theme_controller::get_courses_for_teacher()));
    // }

    public function get_userlist_action()
    {
        global $DB;
        $courseid = optional_param('courseid', 0, PARAM_INT);
        $sqlq = ("

        SELECT u.id, u.firstname, u.lastname

        FROM {course} c
        JOIN {context} ct ON c.id = ct.instanceid
        JOIN {role_assignments} ra ON ra.contextid = ct.id
        JOIN {user} u ON u.id = ra.userid
        JOIN {role} r ON r.id = ra.roleid
        WHERE c.id = ? AND r.id=5

    ");
        $userlist = $DB->get_records_sql($sqlq, array($courseid));

        return json_encode($userlist);
    }

    public function save_user_profile_settings_ajax_action()
    {
        global $USER, $DB;
        $fname = required_param('fname', PARAM_TEXT);
        $lname = required_param('lname', PARAM_TEXT);
        //$emailid = required_param('emailid', PARAM_EMAIL);
        $description = required_param('description', PARAM_TEXT);
        $city = required_param('city', PARAM_TEXT);
        $country = required_param('country', PARAM_ALPHAEXT);
        // return "$fname $lname $emailid $description $city $country" ;
        return \theme_remui\utility::save_user_profile_info($fname, $lname, /*$emailid,*/ $description, $city, $country);
    }

    public function get_courses_by_category_action()
    {
        $categoryid = required_param('categoryid', PARAM_INT);
        return json_encode(\theme_remui\utility::get_courses_by_category($categoryid));
    }

    public function get_courses_for_quiz_action()
    {
        $courseid = required_param('courseid', PARAM_INT);
        return(json_encode(\theme_remui\utility::get_quiz_participation_data($courseid)));
    }


    public function set_setting_ajax_action()
    {
        $configname = required_param('configname', PARAM_RAW);
        $configvalue = required_param('configvalue', PARAM_RAW);

        set_config($configname, $configvalue, 'theme_remui');
    }

    public function get_data_for_messagearea_messages_ajax_action()
    {
        global $USER;
        $otheruserid = required_param('otheruserid', PARAM_INT);
        return json_encode(\theme_remui\utility::data_for_messagearea_messages($USER->id, $otheruserid, 0, 10, true));
    }

    public function send_quickmessage_ajax_action()
    {
        $contactid = optional_param('contactid', 0, PARAM_INT);
        $message = optional_param('message', '', PARAM_TEXT);
        return json_encode(\theme_remui\utility::quickmessage($contactid, $message));
    }

    // handle feedback form submit via ajax
    public function send_remuifeedback_ajax_action()
    {
        // $email = optional_param('email', '', PARAM_EMAIL);
        $rating = optional_param('rating', '', PARAM_TEXT);
        // $fullname = optional_param('fullname', '', PARAM_TEXT);
        $feedback = optional_param('feedback', '', PARAM_TEXT);

        return json_encode(\theme_remui\utility::sendfeedback('', '', $rating, $feedback));
    }

    //Handle Course Analytics
    public function ger_course_anlytics_ajax_action()
    {
        $courseid = required_param('courseid', PARAM_INT);
        return(json_encode(\theme_remui\utility::get_analytics_for_courses($courseid)));
    }

    //handle view toggle
    public function toggle_view_action()
    {
        $view = get_user_preferences('viewCourseCategory');
        if ($view == 'list') {
            set_user_preference('viewCourseCategory', 'grid');
        } else {
            set_user_preference('viewCourseCategory', 'list');
        }
    }


    // handle the course progress table on dashboard
    public function get_course_progress_ajax_action()
    {
        $courseid = optional_param('courseid', 0, PARAM_INT);
        return(json_encode(\theme_remui\utility::get_student_progress_view($courseid)));
    }

    // handle the course progress table on dashboard
    public function send_message_user_ajax_action()
    {
        $studentid = optional_param('studentid', 0, PARAM_INT);
        $message = optional_param('message', 0, PARAM_TEXT);
        return(json_encode(\theme_remui\utility::send_message_to_user($studentid, $message)));
    }

    // return courses based on parameters
    public function get_courses_ajax_action()
    {
        global $CFG;
        // get all parameters passed in the ajax url and pass in the function.
        $totalcourses = optional_param('totalcourses', false, PARAM_BOOL);
        $mycourses = optional_param('mycourses', 0, PARAM_INT);
        $search    = optional_param('search', '', PARAM_ALPHANUMEXT);
        $category  = optional_param('categoryid', 0, PARAM_INT);
        $startfrom = optional_param('startfrom', 0, PARAM_INT);
        $limitto = optional_param('limitto', 4, PARAM_INT);
        $categorysort = optional_param('categorysort', 0, PARAM_ALPHANUMEXT) == 'default' ? '' : optional_param('categorysort', 0, PARAM_ALPHANUMEXT) ;
        $courses = \theme_remui\utility::get_courses($totalcourses, $search, $category, $startfrom, $limitto, $mycourses);
        if ($categorysort == 'SORT_ASC' || $categorysort == 'SORT_DESC') {
            $courses = \theme_remui\utility::array_msort($courses, array('coursename'=>$categorysort));
        }
        // $html = '';
        // foreach ($courses as $course) {
            
        //     // $course['courseimage'] = ''.$course['courseimage'];
        //     $course = ''.$course['courseimage'];
        //     // $course['courseimage'] = $temp;
        //     var_dump(($courses));
        //     exit;
        //     // $course['courseimage'] = 'http://eluciddemo.dev/theme/image.php/remui/theme/1525931973/placeholder';
        //     // $course->courseimage = "hello";
        //     // return implode(', ', $course);
        //     // return 'Courseimage: '. ($course->courseimage);
        //     // $course = json_encode($course);
        //     // $url = $CFG->wwwroot . '/theme/remui/templates/course_card.html';
        //     // $html .= '<div data-fragment="' . $url . '">' . $course . '</div>';
        // }
        $view = get_user_preferences('course_view_state');
        if (empty($view)) {
            $view = set_user_preference('course_view_state', 'grid');
            $view = 'grid';
        }

        if ($view == 'grid') {
            $viewClasses = 'col-md-6 col-lg-3 gridview';
            $imgStyle = 'gridStyle';
            $listbuttons = '';
            $listprogress = '';
        } else {
            $viewClasses = 'col-md-12 col-lg-12 listview';
            $imgStyle = 'listStyle';
            $listbuttons = 'list-activity-buttons';
            $listprogress = "list-progress";
        }
        for ($course=0; $course < sizeof($courses) ; $course++) { 
            $courses[$course]['courseimage'] = ''.$courses[$course]['courseimage'];
            $courses[$course]['usercanmanage'] = false;
            if($mycourses == 0) {
                $courses[$course]['usercanmanage'] = true;
            }
            $courses[$course]['viewClasses'] = $viewClasses;
            $courses[$course]['imgStyle'] = $imgStyle;
            $courses[$course]['listbuttons'] = $listbuttons;
            $courses[$course]['listprogress'] = $listprogress;
            $courses[$course]['mycourses'] = $mycourses;
        }
        // var_dump($html);
        // exit;
        // return(json_encode($html));
        return(json_encode($courses));
        // return(json_encode("hello"));
    }
}
