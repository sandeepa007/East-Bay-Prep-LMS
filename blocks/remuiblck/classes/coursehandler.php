<?php

namespace block_remuiblck;

use core_completion\progress;
use context_course;
use moodle_url;

// use coursecat_helper;

require_once($CFG->dirroot.'/course/renderer.php');
require_once($CFG->libdir. '/gradelib.php'); // required by get_analytics_overview
require_once($CFG->dirroot. '/grade/querylib.php'); // required by get_analytics_overview

class coursehandler
{
    //*** Start
    // Function for Course Stats Block
    function get_course_overview()
    {
        global $USER;

        $activityCount = 0;
        $courseProgress = 0;
        $activitiesProgress = 0;
        $completionCourses = 0;
        $completionActivities = 0;

        $courses = enrol_get_all_users_courses($USER->id, true);

        foreach ($courses as $course) {
            $completion = new \completion_info($course);
            if ($completion->is_enabled()) {
                $percentage = progress::get_course_progress_percentage($course, $USER->id);
                if (!empty($percentage)) {
                    $courseProgress += floor($percentage);
                }
                $completionCourses++;
            }

            // Get all the actvities of the courses
            $allActivities = get_array_of_activities($course->id);
            $activityCount += count($allActivities);

            // Get completed activities percentage
            $modules = $completion->get_activities();
            foreach ($modules as $module) {
                $moduledata = $completion->get_data($module, false, $USER->id);
                $activitiesProgress += $moduledata->completionstate == COMPLETION_INCOMPLETE ? 0 : 1;
                $completionActivities++;
            }
        }
        $data = array();

        $data['completionCourses'] = $completionCourses;
        $data['activityCount'] = $activityCount;
        $data['activitiesProgress'] = $activitiesProgress;
        $data['courseProgress'] = $courseProgress;
        
        return $data;
    }
    
    // Function for Course Stats Block
    //*** End

    /*Add Notes Block*/
    function get_notes_data()
    {
        $courses = get_courses();
        unset($courses[1]);
        return $courses;
    }
    /*Add Notes Block*/

    //*** Start
    // Function for Teacher Specific Dashboard

    function teacher_courses_data()
    {
        global $USER, $CFG;
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
                    require_once($CFG->dirroot . '/blocks/remuiblck/externallib.php');
                    $temp = \block_remuiblck_external::get_course_progress($course->id);
                    $temp->backColor = 'alternate-row';
                    $temp->index = ++$course_count;
                    $course_progress[] = $temp;
                    break;
                }
            }
        }

        $templatecontext['isTeacher'] = $isTeacher;
       
        $templatecontext['course_progress'] = $course_progress;
        return $templatecontext;
    }

    // Function for Teacher Specific Dashboard
    //*** End

    /* Recent Active Forums*/
    function get_recent_active_forums()
    {
        $templatecontext['recentforums'] = $this->recent_forum_activity(false, 5);
        if (!empty($templatecontext['recentforums'])) {
            $templatecontext['hasrecentforums'] = 1;
        } else {
            $templatecontext['hasrecentforums'] = 0;
        }
        return $templatecontext;
    }


    function recent_forum_activity($userorid = false, $limit = 10, $since = null)
    {
     
        global $CFG, $DB;

        if (file_exists($CFG->dirroot.'/mod/hsuforum')) {
            require_once($CFG->dirroot.'/mod/hsuforum/lib.php');
        }
        
        // call to theme function
        // didn't write this function again in plugin because this function // can't be removed from theme as used in many files
        // to avoid redundancy called function from theme as it is
        $user = \theme_remui\utility::get_user($userorid);
        if (!$user) {
            return [];
        }
   
        if ($since === null) {
            $since = time() - (12 * WEEKSECS);
        }

        // Get all relevant forum ids for SQL in statement.
        // We use the post limit for the number of forums we are interested in too -
        // as they are ordered by most recent post.
        if (file_exists($CFG->dirroot.'/blocks/remuiblck/classes/user_forums.php')) {
            require_once($CFG->dirroot.'/blocks/remuiblck/classes/user_forums.php');
        }
        $userforums = new \block_remuiblck\user_forums($user, $limit);
        $forumids   = $userforums->forumids();
        $forumidsallgroups = $userforums->forumidsallgroups();
        $hsuforumids = $userforums->hsuforumids();
        $hsuforumidsallgroups = $userforums->hsuforumidsallgroups();

        if (empty($forumids) && empty($hsuforumids)) {
            return [];
        }

        $sqls = [];
        $params = [];

        // if ($limit > 0) {
        //     $limitsql = self::limit_sql(0, $limit); // Note, this is here for performance optimisations only.
        // } else {
        //     $limitsql = '';
        // }
        $limitsql = '';

        if (!empty($forumids)) {
            list($finsql, $finparams) = $DB->get_in_or_equal($forumids, SQL_PARAMS_NAMED, 'fina');
            $params = $finparams;
            $params = array_merge(
                $params,
                [
                     'sepgps1a' => SEPARATEGROUPS,
                     'sepgps2a' => SEPARATEGROUPS,
                     'user1a'   => $user->id,
                     'user2a'   => $user->id

                 ]
            );

            $fgpsql = '';
            if (!empty($forumidsallgroups)) {
                // Where a forum has a group mode of SEPARATEGROUPS we need a list of those forums where the current
                // user has the ability to access all groups.
                // This will be used in SQL later on to ensure they can see things in any groups.
                list($fgpsql, $fgpparams) = $DB->get_in_or_equal($forumidsallgroups, SQL_PARAMS_NAMED, 'allgpsa');
                $fgpsql = ' OR f1.id '.$fgpsql;
                $params = array_merge($params, $fgpparams);
            }

            $params['user2a'] = $user->id;

            $sqls[] = "(SELECT ".$DB->sql_concat("'F'", 'fp1.id')." AS id, 'forum' AS type, fp1.id AS postid,
                               fd1.forum, fp1.discussion, fp1.parent, fp1.userid, fp1.modified, fp1.subject,
                               fp1.message, 0 AS reveal, cm1.id AS cmid,
                               0 AS forumanonymous, f1.course, f1.name AS forumname,
                               u1.firstnamephonetic, u1.lastnamephonetic, u1.middlename, u1.alternatename, u1.firstname,
                               u1.lastname, u1.picture, u1.imagealt, u1.email,
                               c.shortname AS courseshortname, c.fullname AS coursefullname
                          FROM {forum_posts} fp1
                          JOIN {user} u1 ON u1.id = fp1.userid
                          JOIN {forum_discussions} fd1 ON fd1.id = fp1.discussion
                          JOIN {forum} f1 ON f1.id = fd1.forum AND f1.id $finsql
                          JOIN {course_modules} cm1 ON cm1.instance = f1.id
                          JOIN {modules} m1 ON m1.name = 'forum' AND cm1.module = m1.id
                          JOIN {course} c ON c.id = f1.course
                          LEFT JOIN {groups_members} gm1
                            ON cm1.groupmode = :sepgps1a
                           AND gm1.groupid = fd1.groupid
                           AND gm1.userid = :user1a
                         WHERE (cm1.groupmode <> :sepgps2a OR (gm1.userid IS NOT NULL $fgpsql))
                           AND fp1.userid <> :user2a
                           AND fp1.modified > $since
                        )
                        ORDER BY fp1.modified DESC
                               $limitsql
                         ";
            // TODO - when moodle gets private reply (anonymous) forums, we need to handle this here.
        }

        if (!empty($hsuforumids)) {
            list($afinsql, $afinparams) = $DB->get_in_or_equal($hsuforumids, SQL_PARAMS_NAMED, 'finb');
            $params = array_merge($params, $afinparams);
            $params = array_merge(
                $params,
                [
                                      'sepgps1b' => SEPARATEGROUPS,
                                      'sepgps2b' => SEPARATEGROUPS,
                                      'user1b'   => $user->id,
                                      'user2b'   => $user->id,
                                      'user3b'   => $user->id,
                                      'user4b'   => $user->id
                                  ]
            );

            $afgpsql = '';
            if (!empty($hsuforumidsallgroups)) {
                // Where a forum has a group mode of SEPARATEGROUPS we need a list of those forums where the current
                // user has the ability to access all groups.
                // This will be used in SQL later on to ensure they can see things in any groups.
                list($afgpsql, $afgpparams) = $DB->get_in_or_equal($hsuforumidsallgroups, SQL_PARAMS_NAMED, 'allgpsb');
                $afgpsql = ' OR f2.id '.$afgpsql;
                $params = array_merge($params, $afgpparams);
            }

            $sqls[] = "(SELECT ".$DB->sql_concat("'A'", 'fp2.id')." AS id, 'hsuforum' AS type, fp2.id AS postid,
                               fd2.forum, fp2.discussion, fp2.parent, fp2.userid, fp2.modified, fp2.subject,
                               fp2.message, fp2.reveal, cm2.id AS cmid,
                               f2.anonymous AS forumanonymous, f2.course, f2.name AS forumname,
                               u2.firstnamephonetic, u2.lastnamephonetic, u2.middlename, u2.alternatename, u2.firstname,
                               u2.lastname, u2.picture, u2.imagealt, u2.email,
                               c.shortname AS courseshortname, c.fullname AS coursefullname
                          FROM {hsuforum_posts} fp2
                          JOIN {user} u2 ON u2.id = fp2.userid
                          JOIN {hsuforum_discussions} fd2 ON fd2.id = fp2.discussion
                          JOIN {hsuforum} f2 ON f2.id = fd2.forum AND f2.id $afinsql
                          JOIN {course_modules} cm2 ON cm2.instance = f2.id
                          JOIN {modules} m2 ON m2.name = 'hsuforum' AND cm2.module = m2.id
                          JOIN {course} c ON c.id = f2.course
                          LEFT JOIN {groups_members} gm2
                            ON cm2.groupmode = :sepgps1b
                           AND gm2.groupid = fd2.groupid
                           AND gm2.userid = :user1b
                         WHERE (cm2.groupmode <> :sepgps2b OR (gm2.userid IS NOT NULL $afgpsql))
                           AND (fp2.privatereply = 0 OR fp2.privatereply = :user2b OR fp2.userid = :user3b)
                           AND fp2.userid <> :user4b
                           AND fp2.modified > $since
                      ORDER BY fp2.modified DESC
                               $limitsql
                        )
                         ";
        }

        $sql = '-- remui sql'. "\n".implode("\n".' UNION ALL '."\n", $sqls);
        if (count($sqls)>1) {
            $sql .= "\n".' ORDER BY modified DESC';
        }
        $posts = $DB->get_records_sql($sql, $params, 0, $limit);

        $activities = [];

        $discussionTopics = [];
        $topics = [];
        $count=-1;
        if (!empty($posts)) {
            foreach ($posts as $post) {
                $postuser = (object)[
                    'id' => $post->userid,
                    'firstnamephonetic' => $post->firstnamephonetic,
                    'lastnamephonetic' => $post->lastnamephonetic,
                    'middlename' => $post->middlename,
                    'alternatename' => $post->alternatename,
                    'firstname' => $post->firstname,
                    'lastname' => $post->lastname,
                    'picture' => $post->picture,
                    'imagealt' => $post->imagealt,
                    'email' => $post->email
                ];

                // Update the user object with user profile photo
                $postuser->profilepicture = \theme_remui\utility::get_user_picture($postuser, 15);

                if ($post->type === 'hsuforum') {
                    $postuser = hsuforum_anonymize_user($postuser, (object)array(
                        'id' => $post->forum,
                        'course' => $post->course,
                        'anonymous' => $post->forumanonymous
                    ), $post);
                }

                if (!in_array($post->cmid, $topics)) {
                    $activities[] = (object)[
                        'type' => $post->type,
                        'cmid' => $post->cmid,
                        'name' => $post->subject,
                        'courseshortname' => $post->courseshortname,
                        'coursefullname' => $post->coursefullname,
                        'forumname' => $post->forumname,
                        'sectionnum' => null,
                        'timestamp' => $post->modified,
                        'content' => (object)[
                            'id' => $post->postid,
                            'discussion' => $post->discussion,
                            'subject' => $post->subject,
                            'parent' => $post->parent
                        ],
                        'user' => $postuser
                    ];
                    $topics[] = $post->cmid;
                    $count++;
                    $activities[$count]->replies = 1;
                    $activities[$count]->recentuser = [$postuser];
                } else {
                    $activities[$count]->replies = $activities[$count]->replies + 1;
                    if (!in_array($postuser, $activities[$count]->recentuser) && count($activities[$count]->recentuser) <= 2) {
                        array_push($activities[$count]->recentuser, $postuser);
                    }
                }
            }
        }
        return $activities;
    }


    /*Get recent Assignment*/
    function get_recent_assignment()
    {
        global $USER;

        $templatecontext = array();
        $chelper = new \coursecat_helper();
        $recentassignments = \theme_remui\utility::grading();
        if ($recentassignments) {
            $templatecontext['hasrecentassignments'] = true;
            $i = 0;
            foreach ($recentassignments as $ungraded) {
                $modinfo = get_fast_modinfo($ungraded->course);
                $course = $modinfo->get_course();
                $cm = $modinfo->get_cm($ungraded->coursemoduleid);

                $array[0] = new \stdClass;
                $array[0]->cm_url = $cm->url;
                $array[0]->cm_name = $cm->name;
                $array[0]->course_fullname = strip_tags($chelper->get_course_formatted_name($course));

                if (++$i == 5) {
                    break;
                }
            }
            $templatecontext['recentassignments'] = $array;
        } else {
            $grades = \theme_remui\utility::graded();
            if (!empty($grades)) {
                $templatecontext['hasrecentfeedback'] = true;
                $i = 0;
                foreach ($grades as $grade) {
                    $modinfo = get_fast_modinfo($grade->courseid);
                    $course = $modinfo->get_course();

                    $modtype = $grade->itemmodule;
                    $cm = $modinfo->instances[$modtype][$grade->iteminstance];

                    $coursecontext = \context_course::instance($grade->courseid);
                    $canviewhiddengrade = has_capability('moodle/grade:viewhidden', $coursecontext);
                    $url = new \moodle_url('/grade/report/user/index.php', ['id' => $grade->courseid]);
                    if (in_array($modtype, ['quiz', 'assign']) && (!empty($grade->rawgrade) || !empty($grade->feedback))) {
                        $url = $cm->url;
                    }

                    $gradetitle = "$course->fullname / $cm->name";
                    $releasedon = isset($grade->timemodified) ? $grade->timemodified : $grade->timecreated;
                    $grade = new \grade_grade(array('itemid' => $grade->itemid, 'userid' => $USER->id));
                    if (!$grade->is_hidden() || $canviewhiddengrade) {
                        $array[$i] = new \stdClass;
                        $array[$i]->courseurl = new moodle_url('/course/view.php?id=' . $grade->grade_item->courseid);
                        $array[$i]->course_shortname = $course->shortname;
                        $array[$i]->assignurl = $cm->url;
                        $array[$i]->grade_itemname = $grade->grade_item->itemname;
                        $array[$i]->grade_rawgrade = intval($grade->rawgrade);
                        $array[$i]->grade_rawgrademax = intval($grade->rawgrademax);
                        $array[$i]->timemodified = $grade->timemodified;
                    }
                }
                $templatecontext['recentfeedback'] = $array;
            }
        }
        return $templatecontext;
    }

    function get_analytics_overview()
    {
        global $USER, $CFG;
        
        //analytics_overview
        $chelper = new \coursecat_helper();
        $courses = enrol_get_all_users_courses($USER->id);
        $qcourse = [];
        foreach ($courses as $course) {
            $course->fullname = strip_tags($chelper->get_course_formatted_name($course));
            $gradeActivities = grade_get_gradable_activities($course->id);
            if (!empty($gradeActivities)) {
                $qcourse[] = ['id' => $course->id, 'name' => $course->fullname];
            }
        }
        $templatecontext['quizcourse'] = $qcourse;

        if (count($qcourse)) {
            $templatecontext['hasanalytics'] = 1;
        } else {
            $templatecontext['hasanalytics'] = 0;
        }
        return $templatecontext;
    }
}
