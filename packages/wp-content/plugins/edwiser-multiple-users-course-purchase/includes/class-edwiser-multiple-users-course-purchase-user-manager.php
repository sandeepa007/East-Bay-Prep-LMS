<?php

namespace app\wisdmlabs\edwiserBridge\BulkPurchase;

use app\wisdmlabs\edwiserBridge as edwiserBridge;

/**
 * Create new user and update the course enrollment of user.
 */
if (!class_exists('EdwiserMultipleUsersCoursePurchaseUserManager')) {

    class EdwiserMultipleUsersCoursePurchaseUserManager
    {

        /**
         * call to create user function for creating wordpress and moodle user.
         */
        public function __construct()
        {
            add_action('wp_ajax_create_wordpress_user', array($this, 'createWordpressUsers'));
        }

        /**
         * create wordpress and moodle user and enroll the user in cources.
         */
        public function createWordpressUsers()
        {
            /**
             * Validae the request data.
             */
            if ($this->isInvalidUserReqData($_POST)) {
                wp_send_json_error(__("Invalid user data.", 'ebbp-textdomain'));
            }
            /**
             * Declare the arrays for the messages.
             */
            $enrollmentErr = array();
            $enrollmentSuc = array();
            $alreadyEnrolledUser = array();
            $currUserId = get_current_user_id();
            $details = $this->getCohortDetails($_POST['mdl_cohort_id']);
            $cohortName = $details['name'];
            $ebCourseIds = $details['courses'];
            $availableSeats = $details['quantity'];
            $remainingPrdCnt = $availableSeats;
            $mdlCohortId = $details['mdl_cohort_id'];
            $prdCourseArr = $details['products'];
            $firstNameArr = $_POST['firstname'];
            $lastnNameArr = $_POST['lastname'];
            $emailArr = $_POST['email'];
            $userRole = "Student";

            /**
             * Check requested sets are available or not
             */
            if (count($emailArr) > $availableSeats) {
                wp_send_json_error(__("Available sets quantity is less than requested quantity.", 'ebbp-textdomain'));
            }
            for ($cnt = 0; $cnt < count($emailArr); $cnt++) {
                $status = false;
                /**
                 * Check is all the request data is correct.
                 */
                if ($this->checkIsEmpty($firstNameArr, $cnt) && $this->checkIsEmpty($lastnNameArr, $cnt) && $this->checkIsEmpty($emailArr, $cnt)) {
                    $firstname = $firstNameArr[$cnt];
                    $lastname = $lastnNameArr[$cnt];
                    $email = $emailArr[$cnt];
                    $cohortManager = new BPCohortManageUser();
                    $user = $this->processUserAccCreation($email, $firstname, $lastname);

                    /**
                     * Check is user present on wordpress.
                     */
                    if ($user == null) {
                        $enrollmentErr[] = $email;
                        continue;
                    } else {
                        /**
                         * Check is user already enrolled in all the courses.
                         */
                        if ($this->isUserAlreadyEnrolled($ebCourseIds, $user->ID)) {
                            $alreadyEnrolledUser[] = $user->user_email;
                            continue;
                        }
                        $cohortManager->addUserToCohort($user->ID, $mdlCohortId, "subscriber", $currUserId);
                        $this->updateWordpresUserRole($user->ID, $user->user_email);
                        $status = $this->enrollUser($mdlCohortId, $user->ID, $currUserId, $userRole, $prdCourseArr);
                        if ($status) {
                            $remainingPrdCnt--;
                            $this->updateBpCohortInfoTableOnEnrollment(
                                $mdlCohortId,
                                $remainingPrdCnt
                            );
                        } else {
                            $enrollmentErr[] = $email;
                        }
                    }

                    /**
                     * check already enrolled users list.
                     */
                    if ($this->checkIfEnrolledSuccessfully($email, $alreadyEnrolledUser, $enrollmentErr)) {
                        $enrollmentSuc[] = $email;
                    }
                } else {
                    $enrollmentSuc[] = $emailArr[$cnt];
                }
            }

            $currentUser = wp_get_current_user();
            $cohortName = str_replace($currentUser->user_login . "_", "", $cohortName);
            $cohortName .= " (" . $remainingPrdCnt . ") ";
            
            /**
             * Prepare the responce messages and send responce.
             */
            wp_send_json_success(array("cohort" => html_entity_decode($cohortName), "msg" => $this->prepareResMsg($enrollmentErr, $alreadyEnrolledUser, $enrollmentSuc)));
        }

        /**
         * Checkes whether the all the data submited is correct to create the user
         * @param type $data array of post data
         * @return returns true if the data is incrrect and false if the data is correct
         */
        private function isInvalidUserReqData($data)
        {
            $keyArr = array("mdl_cohort_id", "firstname", "lastname", "email");
            foreach ($keyArr as $key) {
                if (!$this->checkIsEmpty($data, $key)) {
                    return true;
                }
            }
            return false;
        }

        /**
         * Check is the array value is present for the key
         * @param type $data array of the data
         * @param type $key key to check
         * @return true if the user array contains the value for the key ,false otherwise
         */
        private function checkIsEmpty($data, $key)
        {
            if (isset($data[$key]) && !empty($data[$key])) {
                return $data[$key];
            } else {
                return false;
            }
        }

        /**
         * Process the user account creation request,
         * Checks is the user account exist or not and prepeares the user object
         * and creates the user on wordpress
         * @param type $email user email address
         * @param type $firstname user first name
         * @param type $lastname use last name
         * @return Object WP_User returns the WP_user object for the given email address.
         */
        private function processUserAccCreation($email, $firstname, $lastname)
        {
            $user = null;
            $user_id = edwiserBridge\edwiserBridgeInstance()->userManager()->createWordpressUser($email, $firstname, $lastname);
            if (!empty($user_id) && !is_wp_error($user_id)) {
                $user = get_userdata($user_id);
                $cohortManager = new BPCohortManageUser();
                $cohortManager->updateMoodleUserProfile(5, $user->ID);
            } elseif (is_wp_error($user_id) && strpos($user_id->get_error_data(), 'eb_email_exists') !== false) {
                $user = get_user_by("email", $email);
            }
            return $user;
        }

        /**
         * Check is the user is enrolled for the all the users
         * @param type $ebCourseIds  array of the cohort corse ids
         * @param type $userId the user id for to check is the user enrolled for the courses
         * @return boolean true if the user is already enrolled to the all the courses, otherwise false
         */
        private function isUserAlreadyEnrolled($ebCourseIds, $userId)
        {
            global $wpdb;
            $mdlEnroll = $wpdb->prefix . "moodle_enrollment";
            $stmtUserEnroll = "select course_id from {$mdlEnroll} where user_id='$userId'";
            $result = $wpdb->get_col($stmtUserEnroll);
            if (array_intersect($ebCourseIds, $result) === $ebCourseIds) {
                return true;
            } else {
                return false;
            }
        }

        /**
         * Function to check if the user is enrolled successfuly or not
         * @param String $email, array $alreadyEnrolledUser, array $enrollment_err
         * return  $email if success or null
         */
        public function checkIfEnrolledSuccessfully($email, $alreadyEnrolledUser, $enrollment_err)
        {
            if (!in_array($email, $alreadyEnrolledUser) && !in_array($email, $enrollment_err)) {
                return true;
            } else {
                return false;
            }
        }

        /**
         * function to get array products having commomn courses used while updating moodleenrollment table.
         * @param  [type] $arr product.
         * @param  [type] $courseId searched in tghe product array.
         * @return [type]      [description]
         */
        public function getProductArray($products, $courseId)
        {
            global $wpdb;
            $products = unserialize($products);
            //array for the products with associated courses
            $productArray = [];
            $tableName = $wpdb->prefix . "woo_moodle_course";
            foreach ($products as $key => $value) {
                unset($value);
                $query = $wpdb->prepare("SELECT moodle_post_id FROM $tableName WHERE product_id = %d", $key);
                $results = $wpdb->get_col($query);
                if (in_array($courseId, $results)) {
                    array_push($productArray, $key);
                }
            }
            return $productArray;
        }

        /**
         * updating cohort product quantity on enrollment
         * @param  [type] $cohortId  [description]
         * @param  [type] $productId [description]
         * @return [type]            [description]
         */
        public function updateBpCohortInfoTableOnEnrollment($cohortId, $remQty)
        {
            global $wpdb;
            $tableName = $wpdb->prefix . "bp_cohort_info";
            $query = $wpdb->prepare("SELECT PRODUCTS FROM $tableName WHERE MDL_COHORT_ID = %d", $cohortId);
            $results = $wpdb->get_var($query);
            $cohortProd = unserialize($results);
            foreach ($cohortProd as $prodId => $qty) {
                $qty=$qty;
                $cohortProd[$prodId]=$remQty;
            }
            $cohortProd = serialize($cohortProd);
            $query = $wpdb->prepare(
                "update `{$tableName}` set PRODUCTS = %s WHERE MDL_COHORT_ID = %d",
                $cohortProd,
                $cohortId
            );
            $wpdb->query($query);
        }

        public function getCohortDetails($mdlCohortId)
        {
            global $wpdb;
            $tablName = $wpdb->prefix . "bp_cohort_info";
            $query = $wpdb->prepare("SELECT MDL_COHORT_ID, PRODUCTS, COURSES, COHORT_NAME FROM $tablName WHERE mdl_cohort_id = %d", $mdlCohortId);
            $results = $wpdb->get_row($query, ARRAY_A);
            $productsArray = $results['PRODUCTS'];
            $products = unserialize($results['PRODUCTS']);
            $courses = unserialize($results['COURSES']);
            $products = array_values($products);
            $minQuantity = min($products);
            return array("quantity" => $minQuantity, "name" => $results['COHORT_NAME'], "courses" => $courses, "products" => $productsArray, "mdl_cohort_id" => $results['MDL_COHORT_ID']);
        }

        private function prepareResMsg($enrollmentErr, $alreadyEnrolledUser, $enrollmentSuc)
        {
            ob_start();
            if (!empty($enrollmentSuc) && is_array($enrollmentSuc) && count($enrollmentSuc) > 0) {
                ?>
                <div class="wdm_success_message wdm_user_list">
                    <i class="fa fa-times-circle wdm_success_msg_dismiss"></i>
                    <span class="wdm_enroll_warning_message_lable">
                        <?php _e("Users with following email ids have been enrolled successfully", 'ebbp-textdomain'); ?>
                    </span>
                    <?php echo $this->createEmailList($enrollmentSuc); ?>
                </div>
                <?php
            }
            if (isset($alreadyEnrolledUser) && count($alreadyEnrolledUser) > 0) {
                ?>
                <div class="wdm_enroll_warning_message wdm_user_list">
                    <i class="fa fa-times-circle wdm_enroll_warning_msg_dismiss"></i>
                    <span class="wdm_enroll_warning_message_lable">
                        <?php _e("User with the following email ids already for all the courses", 'ebbp-textdomain'); ?>
                    </span>
                    <?php echo $this->createEmailList($alreadyEnrolledUser); ?>
                </div>
                <?php
            }

            if (!empty($enrollmentErr) && is_array($enrollmentErr) && count($enrollmentErr) > 0) {
                ?>
                <div class="wdm_error_message wdm_user_list">
                    <i class="fa fa-times-circle wdm_error_msg_dismiss"></i>
                    <span class="wdm_enroll_warning_message_lable">
                        <?php _e("Some Error occured while enrolling users with following email ids:", 'ebbp-textdomain'); ?>
                    </span>
                    <?php echo $this->createEmailList($enrollmentErr); ?>
                </div>
                <?php
            }
            return ob_get_clean();
        }

        /**
         * Creates the orderd list of users.
         *
         * @param type $emailArray array of the email addreses.
         * @version 1.1.0
         * @return HTML orderd list of the email addresses.
         *
         */
        private function createEmailList($emailArray)
        {
            ob_start();
            ?>
            <ol>
                <?php
                foreach ($emailArray as $email) {
                    ?>
                    <li>
                        <?php echo $email; ?>
                    </li>
                    <?php
                }
                ?>
            </ol>
            <?php
            return ob_get_clean();
        }

        public function updateWordpresUserRole($userId, $email)
        {
            $cuserId = get_current_user_id();
            $userInfo = get_userdata($cuserId);
            if (!user_can($userId, 'manage_options')) {
                $user = new \WP_User($userId);
                if ($userInfo->user_email == $email) {
                    $user->add_role('non_editing_teacher');
                } else {
                    $user->add_role('subscriber');
                }
            } else {
                $user = new \WP_User($userId);
                if ($userInfo->user_email == $email) {
                    $user->add_role('non_editing_teacher');
                } else {
                    $user->add_role('subscriber');
                }
            }
        }

        /**
         * Provides the functionality to fetch the moodle course post ids with the product ids
         *
         * @param Array $product Array of the product ids
         * @since 2.0.0
         * @return Array returns the array of the product ids with associated courses.
         */
        private function getProductCourses($product)
        {
            global $wpdb;
            $tbl_name = $wpdb->prefix . "woo_moodle_course";
            $ebCourseIds=array();
            $stmt="SELECT DISTINCT `product_id`,`moodle_post_id` FROM `{$tbl_name}` WHERE `product_id` in ('".implode("','", $product)."');";
            $result=$wpdb->get_results($stmt, ARRAY_A);
            foreach ($result as $rec) {
                $ebCourseIds[$rec['moodle_post_id']]=$rec['product_id'];
            }
            return $ebCourseIds;
        }

        /**
         * Functionality to update the enrollment records on enroll in to the cohort
         *
         * @param int $mdlCohortId moodle cohort id
         * @param int $user_id User id to enroll into the course
         * @param int $currUserId current user id
         * @param string $userRole Enrolled user role
         * @param Array $prodIds Array of the product cohort ids.
         * @since 2.0.0
         * @return boolean returns true on sucessfull DB update
         */
        protected function enrollUser($mdlCohortId, $user_id, $currUserId, $userRole, $prodIds)
        {
            global $wpdb;
            $status=false;
            $prodIds=  unserialize($prodIds);
            $coursePostIds=$this->getProductCourses(array_keys($prodIds));
            foreach ($coursePostIds as $courseId => $prodId) {
                    $status=$wpdb->insert(
                        $wpdb->prefix.'moodle_enrollment',
                        array(
                        'user_id' => $user_id,
                        'course_id' => $courseId,
                        'role_id' => "5",
                        'time' => date('Y-m-d H:i:s'),
                        'enrolled_by' => $currUserId,
                        'product_id' => $prodId,
                        'mdl_cohort_id' => $mdlCohortId,
                        'role' => $userRole,
                            ),
                        array(
                        '%d',
                        '%d',
                        '%d',
                        '%s',
                        '%s',
                        '%d',
                        '%d',
                        '%d',
                        '%s',
                            )
                    );
            }
            return $status;
        }
    }
}
