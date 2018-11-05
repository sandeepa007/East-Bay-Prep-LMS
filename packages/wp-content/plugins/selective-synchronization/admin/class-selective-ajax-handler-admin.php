<?php

namespace ebSelectSync\admin;

use app\wisdmlabs\edwiserBridge as ed_parent;

/**
 * This class contains functionality to handle actions of custom buttons implemented in settings page
 *
 * @link       https://edwiser.org
 * @since      1.0.0
 *
 * @package    Selective_Sync
 * @subpackage Selective_Sync/admin
 * @author     WisdmLabs <support@wisdmlabs.com>
 */
class SelectiveAjaxHandlerAdmin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
    }

    /**
     * initiate course synchronization process
     *
     * @since    1.0.0
     * @access   public
     *
     * @return
     */
    public function selectedCourseSynchronizationInitiater()
    {

        if (!isset($_POST['_wpnonce_field'])) {
            die('Busted!');
        }

        // verifying generated nonce we created earlier
        if (!wp_verify_nonce($_POST['_wpnonce_field'], 'check_select_course_sync_action')) {
            die('Busted!');
        }

        $selected_course_ids = $_POST['selected_courses'];

        // update previously sync courses
        $sync_options = $_POST['update_course'];

        // start working on request
        $response       = $this->selectedCourseSynchronizationHandler($selected_course_ids, $sync_options);

        echo json_encode($response);
        die();
    }

    /**
     * Creates or updates the selected courses in the wordpress
     * @param  array $course_ids   selected course ids
     * @param  int   $sync_options update previously sync course
     * @return array               response for synchronization
     */
    public function selectedCourseSynchronizationHandler($course_ids, $sync_options)
    {

        ed_parent\edwiserBridgeInstance()->logger()->add('user', "Initiating course & category sync process...."); // add course log

        $moodle_course_response   = array(); // contains course response from moodle
        $moodle_category_response = array(); // contains category response from moodle
        $response_array     = array(); // contains response message to be displayed to user.
        $courses_updated    = array(); // store updated course ids ( wordpress course ids )
        $courses_created    = array(); // store newely created course ids ( wordpress course ids )
       // $category_created   = array(); // array of categories created / synced from moodle

        // checking if moodle connection is working properly
        $connected = ed_parent\edwiserBridgeInstance()->connectionHelper()->connectionTestHelper(EB_ACCESS_URL, EB_ACCESS_TOKEN);

        $response_array['connection_response'] = $connected['success']; // add connection response in response array

        if ($connected['success'] == 1) {
            $moodle_category_response = ed_parent\edwiserBridgeInstance()->courseManager()->getMoodleCourseCategories(); // get categories from moodle

            // creating categories based on recieved data
            if ($moodle_category_response['success'] == 1) {
                ed_parent\edwiserBridgeInstance()->logger()->add('course', 'Creating course categories....');
                ed_parent\edwiserBridgeInstance()->courseManager()->createCourseCategoriesOnWordpress($moodle_category_response['response_data']);
                ed_parent\edwiserBridgeInstance()->logger()->add('course', 'Categories created....');
            }

            // push category response in array
            $response_array['category_success']          = $moodle_category_response['success'];
            $response_array['category_response_message'] = $moodle_category_response['response_message'];

            $moodle_course_response = $this->getSelectedMoodleCourses($course_ids);

            if ($moodle_course_response['success'] == 1) {
                foreach ($moodle_course_response['response_data'] as $course_data) {
                    /**
                         * moodle always returns moodle frontpage as first course,
                         * below step is to avoid the frontpage to be added as a course.
                         *
                         * @var [type]
                         */
                    if ($course_data->id == 1) {
                        continue;
                    }

                    // check if course is previously synced
                    $existing_course_id = ed_parent\edwiserBridgeInstance()->courseManager()->isCoursePresynced($course_data->id);

                    // creates new course or updates previously synced course conditionally
                    if (!is_numeric($existing_course_id)) {
                        ed_parent\edwiserBridgeInstance()->logger()->add('course', 'Creating a new course....');  // add course log

                        $course_id         = ed_parent\edwiserBridgeInstance()->courseManager()->createCourseOnWordpress($course_data, $sync_options);
                        $courses_created[] = $course_id; // push course id in courses created array

                        ed_parent\edwiserBridgeInstance()->logger()->add('course', 'Course created, ID is: '.$course_id); // add course log
                    } elseif (is_numeric($existing_course_id) && isset($sync_options) && $sync_options == 1) {
                        ed_parent\edwiserBridgeInstance()->logger()->add('course', 'Updating existing course: ID is: '.$existing_course_id);  // add course log

                        $course_id         = ed_parent\edwiserBridgeInstance()->courseManager()->updateCourseOnWordpress($existing_course_id, $course_data, $sync_options);
                        ed_parent\edwiserBridgeInstance()->logger()->add('course', 'Updated course....');  // add course log

                        $courses_updated[] = $course_id; // push course id in courses updated array
                    }
                }
            }

            // push course response in array
            $response_array['course_success']          = $moodle_course_response['success'];
            $response_array['course_response_message'] = $moodle_course_response['response_message'];
        } else {
            ed_parent\edwiserBridgeInstance()->logger()->add('course', "Connection problem in synchronization, Response:". print_r($connected, true)); // add connection log
        }
        return $response_array;
    }

    /**
     * retrieves course data of selected course ids from moodle
     * @param  array $course_ids selected course ids
     * @return array             selected course data
     */
    public function getSelectedMoodleCourses($course_ids)
    {

        ed_parent\edwiserBridgeInstance()->logger()->add('course', "\n Fetching courses from moodle.... \n"); // add course log

        $request_data = array('options' => array ( 'ids' => $course_ids ));

        $webservice_function = 'core_course_get_courses'; // get courses from moodle
        $response            = ed_parent\edwiserBridgeInstance()->connectionHelper()->connectMoodleWithArgsHelper($webservice_function, $request_data);

        return $response;
    }
}
