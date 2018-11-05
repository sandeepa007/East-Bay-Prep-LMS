<?php

namespace app\wisdmlabs\edwiserBridge\BulkPurchase;

if (!class_exists('\app\wisdmlabs\edwiserBridge\BulkPurchase\EbBpDbMigrate')) {

    class EbBpDbMigrate
    {

        public function __construct()
        {
            $dbBackup = new BPDbBackUp();
            add_action('wp_ajax_create_cohort', array($this, 'createCohorts'));
            add_action('wp_ajax_enroll_user_to_cohort', array($this, 'addUserToCohort'));
            add_action('wp_ajax_remove_migration_submenu', array($this, 'removeMigrationSubmenu'));
            add_action('wp_ajax_backup_moodle_enrollment', array($dbBackup, 'run'));
        }

        public function run()
        {
            include_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-eb-bp-cohort-manage-user.php';
            require_once EB_PLUGIN_DIR . 'includes/class-eb.php';

//            if (isset($_POST['ebbp_migrate'])) {
//                $dbBackup->run();
//            }
        }

        public function removeMigrationSubmenu()
        {
            update_option("ebbp_migration_completion", 1);
            remove_submenu_page("edwiserbridge_lms", "eb-migration");
            $edwiserUrl = get_site_url() . "/wp-admin/admin.php?page=eb-settings";
            wp_send_json_success($edwiserUrl);
        }

        /**
         * function to create cohorts  depending on the moodle enrollment table
         * @return [type] [description]
         */
        public function createCohorts()
        {
            global $wpdb;
            $manageCohort = new BPManageCohort();
            $moodleEnrollTable = $wpdb->prefix . "moodle_enrollment";
            $cohortTable = $wpdb->prefix . "bp_cohort_info";
            $query = "SELECT enrolled_by,product_id FROM $moodleEnrollTable GROUP BY enrolled_by, product_id";
            $results = $wpdb->get_results($query, ARRAY_A);
            foreach ($results as $row) {
                if ($row['enrolled_by'] != null && $row['product_id'] != null && !empty($row['product_id']) && !empty($row['enrolled_by'])) {
                    $userId = $row['enrolled_by'];
                    $userInfo = get_userdata($userId);
                    $userName = $userInfo->user_login;
                    $productName = get_the_title($row['product_id']);
                    $cohortName = $userName . "_" . $productName;
                    $cohortDetails = $manageCohort->createCohortOnMoodle($userId, $cohortName, 1);
                    $cohortDetails = $cohortDetails[0];
                    $products = get_user_meta($userId, 'group_products', true);
                    if (!empty($products)) {
                        $productsArray = serialize(array($row['product_id'] => $products[$row['product_id']]));
                        $courses = $this->getCoursesFromProduct($row['product_id']);
                        if ($cohortDetails) {
                            $wpdb->insert(
                                    $cohortTable, array(
                                'COHORT_NAME' => $cohortDetails->name,
                                'MDL_COHORT_ID' => $cohortDetails->id,
                                'PRODUCTS' => $productsArray,
                                'COURSES' => $courses,
                                'COHORT_MANAGER' => $userId,
                                'INCOMP_ORD' => array(),
                                'SYNC' => 1,
                                    ), array(
                                '%s',
                                '%d',
                                '%s',
                                '%s',
                                '%d',
                                '%s',
                                '%d'
                                    )
                            );
                            $wpdb->query($query);
                            $manageCohort->enrollCohortIntoCourses(unserialize($courses), $cohortDetails->name, $userId);
                        } else {
                            $msg = '<span class="ebbp_migrate_error_msg"><span class="dashicons dashicons-warning"></span>' . __("Unable to create cohort, for support ", 'ebbp-textdomain') . '<a href="https://edwiser.org/contact-us/" target="_blank">' . __(" Click here", 'ebbp-textdomain') . '</a></span><br><br>';
                            wp_send_json_success($msg);
                        }
                    }
                }
            }
            $msg = '<span class="ebbp_migrate_notice_msg">' . __("Cohort creation completed", 'ebbp-textdomain') . '<span class="migrate-dashicons dashicons dashicons-yes"></span></span><br><br>';
            wp_send_json_success($msg);
        }

        /**
         * function to add the user in the cohort
         */
        public function addUserToCohort()
        {
            global $wpdb;
            $moodleEnrollTable = $wpdb->prefix . "moodle_enrollment";
            $cohortTable = $wpdb->prefix . "bp_cohort_info";
            $userManager = new BPCohortManageUser();
            $query = "SELECT * FROM $moodleEnrollTable";
            $result = $wpdb->get_results($query, ARRAY_A);
            foreach ($result as $row) {
                if ($row['enrolled_by'] != null && $row['product_id'] != null) {
                    $role = "Student";
                    if ($row['user_id'] == $row['enrolled_by']) {
                        $role = "Non Editing Teacher";
                    }
                    $productId = $row['product_id'];
                    $query = $wpdb->prepare("SELECT * FROM $cohortTable WHERE COHORT_MANAGER = %d", $row['enrolled_by']);
                    $bpCohortInfo = $wpdb->get_results($query, ARRAY_A);

                    foreach ($bpCohortInfo as $cohort) {
                        $cohortProduct = unserialize($cohort['PRODUCTS']);
                        if (array_key_exists($productId, $cohortProduct)) {
                            $success = $userManager->addUserToCohort($row['user_id'], $cohort['MDL_COHORT_ID'], $role, $row['enrolled_by'], 0);
                            if ($success) {
                                $this->processUnenrollment($row['id']);
                                $wpdb->update(
                                        $moodleEnrollTable, array(
                                    'mdl_cohort_id' => $cohort['MDL_COHORT_ID'],
                                    'role' => $role
                                        ), array('ID' => $row['id']), array(
                                    '%d',
                                    '%s'
                                        ), array('%d')
                                );
                            } else {
                                $msg = '<span class="ebbp_migrate_error_msg"> <span class="dashicons dashicons-warning"></span>' . __("Unable to add user in the cohort, for support", 'ebbp-textdomain') . '<a href="https://wordpress.org/support/plugin/edwiser-bridge/">' . __(" Click here", 'ebbp-textdomain') . '</a></span></span><br><br>';
                                wp_send_json_error($msg);
                                exit();
                            }
                        }
                    }
                }
            }
            $msg = '<span class="ebbp_migrate_notice_msg">' . __("Users added into the cohort", 'ebbp-textdomain') . '<span class="migrate-dashicons dashicons dashicons-yes"></span></span><br><br>';
            wp_send_json_success($msg);
        }

        /**
         * function to unenroll user from course after he enrolled in the cohort
         * @param  [type] $rid [description]
         * @return [type]      [description]
         */
        public function processUnenrollment($rid)
        {
            global $wpdb;
            $courseUnenrolled = 0;
            /* $edwiser_bridge = new \app\wisdmlabs\edwiserBridge\EdwiserBridge(); */

            $record = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}moodle_enrollment WHERE id={$rid};");
            if (is_array($record) && count($record) === 1) {
                $moodleFunction = "enrol_manual_unenrol_users";
                $ebConnectionHelper = BPManageCohort::getEBConnectionHelper();
                $moodleCourseId = get_post_meta($record[0]->course_id, "moodle_course_id");
                $moodleUserId = get_user_meta($record[0]->user_id, "moodle_user_id");

                if ($moodleCourseId) {
                    $args = array(
                        'userid' => $moodleUserId[0],
                        'courseid' => $moodleCourseId[0]
                    );

                    $ebConnectionHelper->connectMoodleWithArgsHelper($moodleFunction, array("enrolments" => array($args)));
                }
            }
            return $courseUnenrolled;
        }

        /**
         * function to get courses from the product id
         * @param  [type] $productId [description]
         * @return [type]            [description]
         */
        private function getCoursesFromProduct($productId)
        {
            global $wpdb;
            $tableName = $wpdb->prefix . "woo_moodle_course";
            $query = "SELECT moodle_post_id FROM $tableName WHERE product_id = $productId";
            $result = $wpdb->get_col($query);
            $result = serialize($result);
            return $result;
        }
    }
}
