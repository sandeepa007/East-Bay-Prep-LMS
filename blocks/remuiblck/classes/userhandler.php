<?php

namespace block_remuiblck;

use context_course;
use user_picture;
use moodle_url;

class userhandler
{
    // enrolled_users_state & latest members
    function enrolled_users_state()
    {
        global $CFG, $DB;
        if (is_siteadmin()) {
            // enrolled_users_state

            require_once($CFG->libdir. '/coursecatlib.php');
   
            $categorylist = \coursecat::make_categories_list();
            
            $inquery = implode(", ", array_keys($categorylist));
            
            $sqlq = 'SELECT DISTINCT category from {course} where category IN (' . $inquery . ')';
            $catres = $DB->get_records_sql($sqlq);
            
            if ($catres) {
                $templatecontext['hascategory'] = true;
                $count = 0;
                foreach ($catres as $key => $value) {
                    $category[$count] = new \stdClass;
                    $category[$count]->key = $key;
                    $category[$count]->categoryname = $categorylist[$key];
                    $count++;
                }
                $templatecontext['category'] = $category;
            }
            // end_enrolled_users_state
        }
        return $templatecontext;
    }

    function get_quiz_stats()
    {
        global $DB;
        $templatecontext = array();
        // quiz_stats
        $sqlq = ("SELECT DISTINCT q.course courseid, c.shortname shortname, c.fullname fullname FROM {quiz} q JOIN {course} c ON q.course = c.id");
        $courses_for_quiz = $DB->get_records_sql($sqlq);
        foreach ($courses_for_quiz as $course) {
            $context = context_course::instance($course->courseid);
            if (!has_capability('mod/quiz:preview', $context)) {
                unset($courses_for_quiz[$course->courseid]);
            }
        }
        if ($courses_for_quiz) {
            $templatecontext['has_courses_for_quiz'] = true;
            $templatecontext['courses_for_quiz'] = array_values($courses_for_quiz);
        }

        return $templatecontext;
        // end_quiz_stats
    }

    function get_latest_member_data()
    {
        $templatecontext['latest_members'] = $this->get_recent_user();
        $templatecontext['profile_url'] = new moodle_url('/user/profile.php?id');
        $templatecontext['user_profiles'] = new moodle_url('/admin/user.php');
        return $templatecontext;
    }

    // Get the recently added users
    function get_recent_user()
    {
        global  $DB;
        $userdata = array();
        $limitfrom = 0;
        $limitto = 8;
        $users = $DB->get_records_sql('SELECT u.* FROM {user} u  WHERE u.deleted = 0 AND id != 1 ORDER BY timecreated desc', array(1), $limitfrom, $limitto);
        $count = 0;
        foreach ($users as $value) {
            $date = date('d/m/Y', $value->timecreated);
            if ($date == date('d/m/Y')) {
                $date = get_string('today', 'theme_remui');
            } elseif ($date == date('d/m/Y', time() - (24 * 60 * 60))) {
                $date = get_string('yesterday', 'theme_remui');
            } else {
                $date = date('jS F Y', $value->timecreated);
            }
            $userdata[$count]['img'] = $this->get_user_image_link($value->id, 100);
            $userdata[$count]['name'] = $value->firstname .' '.$value->lastname;
            $userdata[$count]['register_date'] = $date;
            $userdata[$count]['id'] = $value->id;
            $count++;
        }
        return $userdata;
    }

    // get user profile pic link
    function get_user_image_link($userid, $imgsize)
    {
        global $USER;
        if (!$userid) {
            $userid = $USER->id;
        }
        global $DB, $PAGE;
        $user = $DB->get_record('user', array('id' => $userid));
        $userimg = new user_picture($user);
        $userimg->size = $imgsize;
        return  $userimg->get_url($PAGE);
    }
}
