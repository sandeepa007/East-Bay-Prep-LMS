<?php
namespace app\wisdmlabs\edwiserBridge\BulkPurchase;

if (!class_exists("EBManageCohort")) {

    class BPCohortManageUser
    {
        private $conHelper;

        public function __construct()
        {
            $this->conHelper = BPManageCohort::getEBConnectionHelper();
        }

        /**
         * Provides functionality to add the user into the cohort.
         * @param number $userId
         * @param String $cohortName
         * @param String $userRole
         */
        public function addUserToCohort($userId, $mdlCohortId, $userRole, $enrolledBy = 0, $sendMail = 1)
        {
            // Get Moodle Cohort Id from cohort Id
            if ($enrolledBy != 0) {
                $cohortManagerId = $enrolledBy;
            } else {
                $cohortManagerId = $userId;
                $userRole="manager";
            }

            $moodleFunction = "core_cohort_add_cohort_members";
            $usersMoodleId = $this->getUsersMoodleId($userId);
            $args = array(
                "cohorttype" => array("type" => "id", "value" => $mdlCohortId),
                'usertype' => array('type' => 'id', 'value' => $usersMoodleId),
            );

            $responce = $this->conHelper->connectMoodleWithArgsHelper($moodleFunction, array("members" => array($args)));
            if (isset($responce["success"]) && $responce["success"] == 1) {
                if ($userRole == "manager") {
                    $this->updateUserRole($userId, 4);
                } else {
                    $this->updateUserRole($userId, 5);
                }
                $args = $this->prepareEmailArgs($cohortManagerId, $userId, $mdlCohortId);

                if ($sendMail) {
                    do_action('eb_bp_new_user_to_cohort', $args);
                }

                return true;
            }
            return false;
        }

        /**
         * Provides the functionality to retrive the moodle user id of the user
         * @param number $userId WP user id
         * @return returns the moodle user id
         */
        private function getUsersMoodleId($userId)
        {
            return get_user_meta($userId, 'moodle_user_id', true);
        }

        public function updateUserRole($userId, $role)
        {
            $moodleFunction = "moodle_role_assign";
            $usersMoodleId = $this->getUsersMoodleId($userId);
            $userData = array(
                'userid' => $usersMoodleId,
                'roleid' => $role,
            );
            $this->conHelper->connectMoodleWithArgsHelper($moodleFunction, array("assignments" => array($userData)));
            /* exit; */
        }

        /**
         * function to delete the user from cohort
         *
         */
        public function deleteUserFromCohort($userId, $mdlCohortId, $enrolledBy)
        {
            if (isset($userId) && !empty($userId) && isset($userId) && !empty($mdlCohortId)) {
                $moodleFunction = "core_cohort_delete_cohort_members";
                if ($enrolledBy != 0) {
                    $cohortManagerId = $enrolledBy;
                } else {
                    $cohortManagerId = $userId;
                }
                $moodleUserId = get_user_meta($userId, "moodle_user_id", true);
                $args = array(
                    "cohortid" => $mdlCohortId,
                    'userid' => $moodleUserId
                );
                $responce = $this->conHelper->connectMoodleWithArgsHelper($moodleFunction, array("members" => array($args)));
                if ($responce['success'] == 1) {
                    $this->deleteCohortUserFromWordpress($mdlCohortId, $userId);
                    $response = array(
                        'status' => true,
                        'message' => __('Unenrolled successfully!', 'ebbp-textdomain')
                    );
                } else {
                    $response = array(
                        'status' => false,
                        'message' => __('Unable to remove user from the cohort', 'ebbp-textdomain')
                    );
                }

                if ($response['status']) {
                    $args = $this->prepareEmailArgs($cohortManagerId, $userId, $mdlCohortId);
                    do_action("eb_bp_remove_user_from_cohort", $args);
                }
                return $response;
            }
        }

        private function prepareEmailArgs($cohortManagerId, $userId, $mdlCohortId)
        {
            $user = get_userdata($userId);
            return array(
                'user_email' => $user->user_email,
                'username' => $user->nickname,
                'last_name' => $user->last_name,
                'first_name' => $user->first_name,
                'mdl_cohort_id' => $mdlCohortId,
                'cohort_manager_id' => $cohortManagerId,
            );
        }

        public function deleteCohortUserFromWordpress($cohortName, $userId)
        {
            global $wpdb;
            $wpdb->delete("{$wpdb->prefix}moodle_enrollment", array('mdl_cohort_id' => $cohortName, 'user_id' => $userId));
        }

        /**
         * function to update the user role on moodle
         * @return [type] [description]
         */
        public function updateMoodleUserProfile($roleId, $userId)
        {
            $userId = get_user_meta($userId, "moodle_user_id");
            $moodleFunction = "core_role_assign_roles";
            $user_data = array(
                'roleid' => $roleId,
                'userid' => $userId[0],
                'contextid' => 1
            );
            $connHelper = BPManageCohort::getEBConnectionHelper();
            $response = $connHelper->connectMoodleWithArgsHelper($moodleFunction, array("assignments" => array($user_data)));
            if ($response['success'] == 1) {
                return true;
            } else {
                return false;
            }
        }
    }
}
