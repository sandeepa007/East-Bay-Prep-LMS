<?php

/**
 * External Web Service Template
 *
 * @package    local
 * @copyright  2011 Moodle Pty Ltd (http://moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir . "/externallib.php");
require_once($CFG->dirroot . '/enrol/cohort/locallib.php');

/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);
*/
class local_wdmgroupregistration_external extends external_api
{

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function wdm_manage_cohort_enrollment_parameters()
    {
        return new external_function_parameters(
            array(
                'cohort' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'courseId' => new external_value(PARAM_INT, 'id of course', VALUE_REQUIRED),
                            'cohortId' => new external_value(PARAM_INT, 'id of cohort', VALUE_REQUIRED)
                        )
                    )
                )
            )
        );
    }

    /**
     * Function responsible for enrolling cohort in course
     * @return string welcome message
     */
    public static function wdm_manage_cohort_enrollment($cohort)
    {
        global $USER;
        global $DB;

        //Parameter validation
        //REQUIRED

        $params = self::validate_parameters(
            self::wdm_manage_cohort_enrollment_parameters(),
            array('cohort' => $cohort)
        );


        //Context validation
        //OPTIONAL but in most web service it should present
        $context = get_context_instance(CONTEXT_USER, $USER->id);
        self::validate_context($context);

        //Capability checking
        //OPTIONAL but in most web service it should present
        if (!has_capability('moodle/user:viewdetails', $context)) {
            throw new moodle_exception('cannotviewprofile');
        }


        foreach ($params['cohort'] as $cohortDetails) {
            $cohortDetails = (object)$cohortDetails;
            if (isset($cohortDetails->cohortId) && !empty($cohortDetails->cohortId) && isset($cohortDetails->courseId) && !empty($cohortDetails->courseId)) {
                $courseid = $cohortDetails->courseId;
                $cohortid = $cohortDetails->cohortId;
                if (!enrol_is_enabled('cohort')) {
                    // Not enabled.
                    return "disabled";
                }
                $enrol = enrol_get_plugin('cohort');

                $course = $DB->get_record('course', array('id' => $courseid));

                $instance = array();
                $instance['name'] = '';
                $instance['status'] = ENROL_INSTANCE_ENABLED; // Enable it.
                $instance['customint1'] = $cohortid; // Used to store the cohort id.
                $instance['roleid'] = 5; // Default role for cohort enrol which is usually student.
                $instance['customint2'] = 0; // Optional group id.
                $instanceId = $enrol->add_instance($course, $instance);

                // Sync the existing cohort members.
                $trace = new null_progress_trace();
                enrol_cohort_sync($trace, $course->id);
                $trace->finished();
            }
        }
        return $instanceId;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function wdm_manage_cohort_enrollment_returns()
    {
        return new external_value(PARAM_INT, 'Id of the instance');
    }
}
