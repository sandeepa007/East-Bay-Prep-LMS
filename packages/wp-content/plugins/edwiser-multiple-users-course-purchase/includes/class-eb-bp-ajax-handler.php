<?php

namespace app\wisdmlabs\edwiserBridge\BulkPurchase;

if (!class_exists('\app\wisdmlabs\edwiserBridge\BulkPurchase\BPAdminAjaxInitiater')) {

    class BPAdminAjaxInitiater
    {

        protected $edwiser_bridge;

        public function __construct()
        {
            $this->edwiser_bridge = new \app\wisdmlabs\edwiserBridge\EdwiserBridge();
            add_action('wp_ajax_handle_cohort_synchronization', array( $this, 'handleCohortSynchronizationCallback' ));
            add_action('wp_ajax_mucp_get_course_details', array($this, 'checkIfUserIsEnrolledInCourse'));
            add_action('eb_before_manage_user_enrollment_table', array($this, 'showNoticeOnManageEnrollmentPage'));
        }

        public function handleCohortSynchronizationCallback()
        {
            $moodleFunction = "core_cohort_get_cohorts";
            $connHelper = BPManageCohort::getEBConnectionHelper();
            $response = $connHelper->connectMoodleWithArgsHelper($moodleFunction, array("cohortids" => array()));
            $moodleFunction = "core_cohort_get_cohort_members";
            $getMemberFunction = "";


            if ($response['success'] == 1) {
                foreach ($response['response_data'] as $cohort) {
                    $cohortId = $cohort['id'];
                    $response = $connHelper->connectMoodleWithArgsHelper($moodleFunction, array("cohortids" => array($cohortId)));
                    if ($response['success'] == 1) {
                        foreach ($response['response_data'] as $member) {
                            $userId = $member['userids'];
                            $response = $connHelper->connectMoodleWithArgsHelper($getMemberFunction, array("criteria" => array("key" => "id", "value" => $userId)));
                            $userArray = $response['response_data']['users'];
                            $userEmail = $userArray[0]['email'];
                            $user = get_user_by("email", $userEmail);
                            if (false != $user) {
                                // checkIfUserCohortDetailsExist($user);
                            }
                        }
                    }
                }
            }
        }


        /**
         * Provides the functionality to unenroll the student from the course.
         *
         */
        public function ebbpActionManageUnenrol()
        {
            $response = "Unsufficiant data to unenroll user";
            if (isset($_POST['mdl_cohort_id']) && !empty($_POST['mdl_cohort_id']) && isset($_POST['user_id']) && !empty($_POST['user_id']) && isset($_POST['enrolled_by']) && !empty($_POST['enrolled_by'])) {
                $cohortManager = new BPCohortManageUser();
                $response = $cohortManager->deleteUserFromCohort($_POST['user_id'], $_POST['mdl_cohort_id'], $_POST['enrolled_by']);
                if ($response['status']) {
                    wp_send_json_success('OK');
                } else {
                    wp_send_json_error($response['message']);
                }
            } else {
                wp_send_json_error($response);
            }
        }

        public function processUnenrollment($rid)
        {
            global $wpdb;
            $courseUnenrolled = 0;
            $record = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}moodle_enrollment WHERE id={$rid};");
            if (is_array($record) && count($record) === 1) {
                $courseUnenrolled = $this->edwiser_bridge->enrollmentManager()->updateUserCourseEnrollment(
                    array(
                            'user_id' => $record[0]->user_id,
                            'courses' => array($record[0]->course_id),
                            'unenroll' => 1,
                            'suspend' => 1
                        )
                );

                if ($courseUnenrolled == 1) {
                    $wpdb->delete("{$wpdb->prefix}moodle_enrollment", array('id' => $rid));
                }
            }
            return $courseUnenrolled;
        }

        /**
         * function to show the cohort details
         * @return [type] [description]
         */
        public function ebbpCohortDetails()
        {
            $responce = "Invalid argument passed to ajax request";
            if (isset($_POST["enrolled_by"]) && !empty($_POST["enrolled_by"]) && isset($_POST["mdl_cohort_id"]) && !empty($_POST["mdl_cohort_id"])) {
                global $wpdb;
                $groupMngId = $_POST["enrolled_by"];
                $mdlCohortId = $_POST["mdl_cohort_id"];
                $tblCohortInfo = $wpdb->prefix . 'bp_cohort_info';
                $stmt = "select COURSES,COHORT_NAME from $tblCohortInfo where COHORT_MANAGER='$groupMngId' AND MDL_COHORT_ID='$mdlCohortId'";
                $results = $wpdb->get_row($stmt, ARRAY_A);
                $courseIds = unserialize($results['COURSES']);
                $cohortName=$results['COHORT_NAME'];
                $courses = "";
                foreach ($courseIds as $course) {
                    $course = get_post($course);
                    $courses .= "<li>" . get_the_title($course) . "</li>";
                }
                $tableName = $wpdb->prefix . 'moodle_enrollment';
                $stmt = "select count(distinct user_id ) from $tableName where mdl_cohort_id='$mdlCohortId'";

                $members = $wpdb->get_var($stmt);

                $companyName = get_user_meta($groupMngId, "wdm_company", true);
                if (!$mdlCohortId || empty(trim($companyName))) {
                    $companyName = get_user_meta($groupMngId, "billing_company", true);
                }
                $managerName = getUserProfileURL($groupMngId);
                $currentUseranme = getUserProfileURL($_POST['user_id']);
                wp_send_json_success(array("cohort_name" => $cohortName, "companyName" => $companyName, "manager" => $managerName, "members" => $members, "courses" => $courses, 'currentUser' => $currentUseranme));
            } else {
                wp_send_json_error($responce);
            }
        }

        /**
         * function which will return courses in which cohort is enrolled also the courses in which user is directly enrolled
         * @return [type] [description]
         */
        public function getEnrolledCourses($userId, $cohortName = "")
        {
            if (!empty($cohortName)) {
                $tblCohortInfo = $wpdb->prefix . 'bp_cohort_info';
                $stmt = "select COURSES from $tblCohortInfo where COHORT_MANAGER='$userId' AND COHORT_NAME='$cohortName'";

                $results = $wpdb->get_var($stmt);
                $results = unserialize($results);

                return $results;
            } else {
            }
        }

        /**
         * function to check if the user is enrolled in the course or cohort
         * @param int $userId , int $courseId
         * @return [type] [description]
         */
        public function checkIfUserIsEnrolledInCourse($userId, $courseId)
        {

            if (isset($_POST['user_id']) && !empty($_POST['user_id']) && isset($_POST['cohort_name']) && !empty($_POST['cohort_name'])) {
                global $wpdb;
                $tableName = $wpdb->prefix;
                $query = $wpdb->prepare("select cohort_name from $tableName where user_id = %d and course_id = %d", $userId, $courseId);
                $result = $wpdb->get_col($query);
                if (in_array(null, $result)) {
                    return json_encode(array('success' => 1));
                }
                return json_encode(array('success' => 0));
            }
        }

        /**
        * function to show notice on unenrollment of user
        *
        */
        public function showNoticeOnManageEnrollmentPage()
        {
            if (isset($_GET["unenroll"])) {
                ?>
                <div class="mucp-notices">
                    <div class="notice notice-success is-dismissible">
                        <p>
                            <?php _e('Unenrolled Successfully', 'ebbp-textdomain'); ?>
                        </p>
                    </div>
                </div>
                <?php
                unset($_GET["unenroll"]);
            }
        }
    }
}
