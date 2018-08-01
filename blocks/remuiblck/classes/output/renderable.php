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
 * Class containing data for my overview block.
 *
 * @package    block_remuiblck
 * @author  wisdmlabs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_remuiblck\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;
use \block_remuiblck\coursehandler;

require_once($CFG->dirroot . '/blocks/remuiblck/lib.php');

/**
 * Class containing data for my overview block.
 *
 * @copyright  2017 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class remuiblck_initialcontent implements renderable, templatable
{

    /**
     * @var string The tab to display.
     */
    private $block = null;
    /**
     * Constructor.
     *
     * @param string $tab The tab to display.
     */
    public function __construct($block)
    {
        $this->block = $block;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output)
    {
        $context = new \stdClass();
        $context->block = $this->block;
       
        return $context;
    }
}


/**
 * Class containing data for my overview block.
 *
 * @copyright  2017 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class remuiblck_blckexist implements renderable, templatable
{

    /**
     * @var string The tab to display.
     */
    private $block = null;
    /**
     * Constructor.
     *
     * @param string $tab The tab to display.
     */
    public function __construct($block)
    {
        $this->block = $block;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output)
    {
        $context = new \stdClass();
        $context->block = $this->block;
       
        return $context;
    }
}



class remuiblck_mycourses implements renderable, templatable
{

    /**
     * @var string The tab to display.
     */
    private $block = null;
    /**
     * Constructor.
     *
     * @param string $tab The tab to display.
     */
    public function __construct($block)
    {
        $this->block = $block;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output)
    {
        global $USER, $DB, $CFG;
        require_once($CFG->dirroot . '/blocks/remuiblck/classes/mycourseoverview.php');
        $mycourseoverview = new \block_remuiblck\classes\mycourseoverview();
        $courses = $mycourseoverview->get_course_overview();
        $context = new \stdClass();
        $context->block = $this->block;
        if ($courses) {
            $context->hascourses = true;
            $context->courses = $courses->coursesview;
        }
        $context->nocourses = $courses->nocourses;
        $context->noevents = $courses->noevents;

        $context->incoursecount = $courses->incoursecount;
        $context->futurecount = $courses->futurecount;
        $context->duecount = $courses->duecount;
       
        return $context;
    }
}

/**
 * Class containing data for my overview block.
 *
 * @copyright  2017 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class remuiblck_coursestats implements renderable, templatable
{

    /**
     * @var string The tab to display.
     */
    private $block = null;
    /**
     * Constructor.
     *
     * @param string $tab The tab to display.
     */
    public function __construct($block)
    {
        $this->block = $block;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output)
    {
        global $USER, $DB, $CFG;

        $context = new \stdClass();
        $context->block = $this->block;

        $obj = new \block_remuiblck\coursehandler();
        $data = $obj->get_course_overview();

        $context->totalcourses = $data['completionCourses'];
        $context->totalactivities    = $data['activityCount'];
        $context->activitiesprogress = $data['activitiesProgress'];
        $context->courseprogress     = $data['courseProgress'];
        return $context;
    }
}
/**
 * Class containing data for my overview block.
 *
 * @copyright  2017 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class remuiblck_tasks implements renderable, templatable
{

    /**
     * @var string The tab to display.
     */
    private $block = null;
    /**
     * Constructor.
     *
     * @param string $tab The tab to display.
     */
    public function __construct($block)
    {
        $this->block = $block;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output)
    {
        global $USER, $DB, $CFG;
        $context = new \stdClass();
        $context->block = $this->block;
        return $context;
    }
}
/**
 * Class containing data for my overview block.
 *
 * @copyright  2017 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class remuiblck_courseprogress implements renderable, templatable
{

    /**
     * @var string The tab to display.
     */
    private $block = null;
    /**
     * Constructor.
     *
     * @param string $tab The tab to display.
     */
    public function __construct($block)
    {
        $this->block = $block;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output)
    {
        global $USER, $DB, $CFG;

        $context = new \stdClass();
        $context->block = $this->block;

        $obj = new \block_remuiblck\coursehandler();
        $data = $obj->teacher_courses_data();
        if ($data['isTeacher'] == true) {
            $context->isTeacher = $data['isTeacher'];
        }
        $context->course_progress = $data['course_progress'];

        return $context;
    }
}
    
/**
 * Class containing data for my overview block.
 *
 * @copyright  2017 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class remuiblck_userstats implements renderable, templatable
{

    /**
     * @var string The tab to display.
     */
    private $block = null;
    /**
     * Constructor.
     *
     * @param string $tab The tab to display.
     */
    public function __construct($block)
    {
        $this->block = $block;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output)
    {
        global $USER, $DB, $CFG;
        // require_once($CFG->dirroot . '/blocks/remuiblck/classes/userstats.php');

        $context = new \stdClass();
        $context->block = $this->block;

        $userobj = new \block_remuiblck\userhandler();
        $userdata  = $userobj->enrolled_users_state();

        $quizstats = $userobj->get_quiz_stats();
        $context->is_siteadmin = true;
        $context->data     = $userdata;
        $context->quizdata = $quizstats;

            // echo "<pre>";
            // print_r($context);
            // echo "</pre>";
            // exit;
        return $context;
    }
}

/**
 * Class containing data for my overview block.
 *
 * @copyright  2017 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class remuiblck_enrolledusers implements renderable, templatable
{

    /**
     * @var string The tab to display.
     */
    private $block = null;
    /**
     * Constructor.
     *
     * @param string $tab The tab to display.
     */
    public function __construct($block)
    {
        $this->block = $block;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output)
    {
        global $USER, $DB, $CFG;

        $context = new \stdClass();
        $context->block = $this->block;

        $userobj = new \block_remuiblck\userhandler();
        $userdata = $userobj->enrolled_users_state();

        $context->is_siteadmin = is_siteadmin();
        $context->data = $userdata;

        return $context;
    }
}

/**
 * Class containing data for my overview block.
 *
 * @copyright  2017 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class remuiblck_quizattempts implements renderable, templatable
{

    /**
     * @var string The tab to display.
     */
    private $block = null;
    /**
     * Constructor.
     *
     * @param string $tab The tab to display.
     */
    public function __construct($block)
    {
        $this->block = $block;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output)
    {
        global $USER, $DB, $CFG;
        // require_once($CFG->dirroot . '/blocks/remuiblck/classes/userstats.php');

        $context = new \stdClass();
        $context->block = $this->block;

        $userobj = new \block_remuiblck\userhandler();
        // $userdata  = $userobj->enrolled_users_state();

        $quizstats = $userobj->get_quiz_stats();
        $context->is_siteadmin = is_siteadmin();
        // $context->data     = $userdata;
        $context->quizdata = $quizstats;
        return $context;
    }
}


/**
 * Class containing data for my overview block.
 *
 * @copyright  2017 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class remuiblck_courseanlytics implements renderable, templatable
{

    /**
     * @var string The tab to display.
     */
    private $block = null;
    /**
     * Constructor.
     *
     * @param string $tab The tab to display.
     */
    public function __construct($block)
    {
        $this->block = $block;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output)
    {
        global $USER, $DB, $CFG;
        // require_once($CFG->dirroot . '/blocks/remuiblck/classes/courseanalytics.php');

        $context = new \stdClass();
        $context->block = $this->block;

        $obj = new \block_remuiblck\coursehandler();
        $data  = $obj->get_analytics_overview();
        $context->quizcourse = $data['quizcourse'];
        $context->hasanalytics = $data['hasanalytics'];

        return $context;
    }
}
 
/**
 * Class containing data for my overview block.
 *
 * @copyright  2017 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class remuiblck_latestmembers implements renderable, templatable
{

    /**
     * @var string The tab to display.
     */
    private $block = null;
    /**
     * Constructor.
     *
     * @param string $tab The tab to display.
     */
    public function __construct($block)
    {
        $this->block = $block;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output)
    {
        global $USER, $DB, $CFG;
        // require_once($CFG->dirroot . '/blocks/remuiblck/classes/userstats.php');

        $context = new \stdClass();
        $context->block = $this->block;

        // $obj = new \userstats();
        $obj = new \block_remuiblck\userhandler();
        $data = $obj->get_latest_member_data();

        if (is_siteadmin()) {
            $context->is_siteadmin = true;
        }
        $context->latest_members = $data['latest_members'];
        $context->profile_url = $data['profile_url'];
        $context->user_profiles = $data['user_profiles'];
        return $context;
    }
}
     
/**
 * Class containing data for my overview block.
 *
 * @copyright  2017 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class remuiblck_addnotes implements renderable, templatable
{

    /**
     * @var string The tab to display.
     */
    private $block = null;
    /**
     * Constructor.
     *
     * @param string $tab The tab to display.
     */
    public function __construct($block)
    {
        $this->block = $block;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output)
    {
        global $USER, $DB, $CFG;
        // require_once($CFG->dirroot . '/blocks/remuiblck/classes/coursestats.php');

        $context = new \stdClass();
        $context->block = $this->block;

        $obj = new \block_remuiblck\coursehandler();
        $courses = $obj->get_notes_data();
        if ($courses) {
            $context->has_courses = true;
            $context->courses = array_values($courses);
        }
        
        return $context;
    }
}
 
/**
 * Class containing data for my overview block.
 *
 * @copyright  2017 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// class remuiblck_recentsection implements renderable, templatable
// {

//     /**
//      * @var string The tab to display.
//      */
//     private $block = null;
//     /**
//      * Constructor.
//      *
//      * @param string $tab The tab to display.
//      */
//     public function __construct($block)
//     {
//         $this->block = $block;
//     }

//     /**
//      * Export this data so it can be used as the context for a mustache template.
//      *
//      * @param \renderer_base $output
//      * @return stdClass
//      */
//     public function export_for_template(renderer_base $output)
//     {
//         global $USER, $DB, $CFG;
//         $context = new \stdClass();
//         $context->block = $this->block;

//         $obj = new \block_remuiblck\coursehandler();
//         // Assignment Data
//         $data = $obj->get_recent_assignment();

//         if (!empty($data)) {
//             $context->recentdata = $data;
//         }
//         // Forum Data
//         $data = $obj->get_recent_active_forums();

//         if (!empty($data)) {
//             $context->recentforums = $data['recentforums'];
//             $context->hasrecentforums = $data['hasrecentforums'];
//         }
//         return $context;
//     }
// }


/**
 * Class containing data for my overview block.
 *
 * @copyright  2017 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class remuiblck_recentfeedback implements renderable, templatable
{

    /**
     * @var string The tab to display.
     */
    private $block = null;
    /**
     * Constructor.
     *
     * @param string $tab The tab to display.
     */
    public function __construct($block)
    {
        $this->block = $block;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output)
    {
        global $USER, $DB, $CFG;
        $context = new \stdClass();
        $context->block = $this->block;

        $obj = new \block_remuiblck\coursehandler();
        // Assignment Data
        $data = $obj->get_recent_assignment();

        if (!empty($data)) {
            $context->recentdata = $data;
        }
        // Forum Data
        // $data = $obj->get_recent_active_forums();

        // if (!empty($data)) {
        //     $context->recentforums = $data['recentforums'];
        //     $context->hasrecentforums = $data['hasrecentforums'];
        // }
        return $context;
    }
}


/**
 * Class containing data for my overview block.
 *
 * @copyright  2017 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class remuiblck_recentforums implements renderable, templatable
{

    /**
     * @var string The tab to display.
     */
    private $block = null;
    /**
     * Constructor.
     *
     * @param string $tab The tab to display.
     */
    public function __construct($block)
    {
        $this->block = $block;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output)
    {
        global $USER, $DB, $CFG;
        $context = new \stdClass();
        $context->block = $this->block;

        $obj = new \block_remuiblck\coursehandler();
        // Assignment Data
        // $data = $obj->get_recent_assignment();

        // if (!empty($data)) {
        //     $context->recentdata = $data;
        // }
        // Forum Data
        $data = $obj->get_recent_active_forums();

        if (!empty($data)) {
            $context->recentforums = $data['recentforums'];
            $context->hasrecentforums = $data['hasrecentforums'];
        }
        return $context;
    }
}
