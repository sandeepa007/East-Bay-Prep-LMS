<?php
defined('MOODLE_INTERNAL') || die();


class courseprogress
{
    function get_teacher_courses_data()
    {
        global $USER;
        // Teacher View Dashboard
        $mycourses = enrol_get_users_courses($USER->id);
        $course_progress = array();
        $course_count = 0;
        $isTeacher = false;
        foreach ($mycourses as $courseid => $course) {
            $coursecontext = context_course::instance($course->id);
            $roles = get_user_roles($coursecontext, $USER->id, true);
            foreach ($roles as $roleid => $role) {
                if ($role->roleid == 1 || $role->roleid == 2 || $role->roleid == 3 || $role->roleid == 4) {
                    $isTeacher = true;
                    $temp = \theme_remui\utility::get_course_progress($course->id);
                    $temp->backColor = 'alternate-row';
                    $temp->index = ++$course_count;
                    $course_progress[] = $temp;
                    break;
                }
            }
        }
        if ($isTeacher) {
            $templatecontext['isTeacher'] = $isTeacher;
        }
        $templatecontext['course_progress'] = $course_progress;
        return $templatecontext;
    }
}
