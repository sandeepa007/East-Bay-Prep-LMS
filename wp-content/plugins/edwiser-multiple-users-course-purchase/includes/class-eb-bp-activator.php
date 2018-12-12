<?php

namespace app\wisdmlabs\edwiserBridge\BulkPurchase;

if (!class_exists('\app\wisdmlabs\edwiserBridge\BulkPurchase\BPPluginActivator')) {

    class BPPluginActivator
    {

        protected $pluginName;
        protected $pluginVersion;

        public function __construct($pluginName, $pluginVersion)
        {
            $this->pluginName = $pluginName;
            $this->pluginVersion = $pluginVersion;
        }

        public static function activate($networkWide)
        {
            if (function_exists('is_multisite') && is_multisite()) {
                if ($networkWide) {
                    // Get all blog ids
                    $blogIds = self::getBlogIds();

                    foreach ($blogIds as $blogId) {
                        switch_to_blog($blogId);
                        self::singleActivate();
                    }
                    restore_current_blog();
                } else {
                    self::singleActivate();
                }
            } else {
                self::singleActivate();
            }
            set_transient('_ebbp_activation_redirect', 1, 30);
        }

        /**
         * Get all blog ids of blogs in the current network that are:
         * - not archived
         * - not spam
         * - not deleted.
         *
         * @since    1.0.0
         *
         * @return array|false The blog ids, false if no matches.
         */
        private static function getBlogIds()
        {
            global $wpdb;
            // get an array of blog ids
            $sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";
            return $wpdb->get_col($sql);
        }

        private static function singleActivate()
        {
            self::addDefaultEmailTmplOptions();
            self::createWpPages();
            self::ebbpFixTableSchema();
            self::modifyBpCohortTableColumnSize();
            add_role(
                'non_editing_teacher',
                'Non editing Teacher',
                array('read' => true, 'level_0' => true)
            );
        }

        private static function addDefaultEmailTmplOptions()
        {
            $ebTmplManag=new EbBpTemplateManager();
            $bpPurchaseContent=$ebTmplManag->getBulkPurchaseDefaultNotification("eb_emailtmpl_bulk_prod_purchase_notifn");
            $cohortEnrolCont=$ebTmplManag->getBulkPurchaseCohortEnrollNotification("eb_emailtmpl_student_enroll_in_cohort_notifn");
            $cohortUnEnrolCont=$ebTmplManag->getBulkPurchaseCohortUnEnrollNotification("eb_emailtmpl_student_unenroll_in_cohort_notifn");
            update_option('eb_emailtmpl_bulk_prod_purchase_notifn', $bpPurchaseContent);
            update_option('eb_emailtmpl_student_enroll_in_cohort_notifn', $cohortEnrolCont);
            update_option('eb_emailtmpl_student_unenroll_in_cohort_notifn', $cohortUnEnrolCont);
            update_option('eb_emailtmpl_bulk_prod_purchase_notifn_notify_allow', "ON");
            update_option('eb_emailtmpl_student_enroll_in_cohort_notifn_notify_allow', "ON");
            update_option('eb_emailtmpl_student_unenroll_in_cohort_notifn_notify_allow', "ON");
        }

        private static function createWpPages()
        {
            $enroll_page_id = post_exists('Enroll Students', '[bridge_woo_enroll_users]');
            if (!$enroll_page_id) {
                $user_ID = get_current_user_id();
                $blogtime = current_time('mysql');
                $my_page = array(
                    'post_title' => 'Enroll Students',
                    'post_content' => '[bridge_woo_enroll_users]',
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'post_author' => $user_ID,
                    'post_date' => $blogtime,
                );
                $enroll_page_id = wp_insert_post($my_page);
            }
            update_option('wdm_enroll_students', $enroll_page_id);
            $ebGeneral = get_option("eb_general");
            $ebGeneral["mucp_group_enrol_page_id"] = $enroll_page_id;
            update_option("eb_general", $ebGeneral);
        }

        public static function ebbpFixTableSchema()
        {
            global $wpdb;

            $usr_enrol_tbl = $wpdb->prefix . 'moodle_enrollment';


            if ($wpdb->get_var("SHOW TABLES LIKE '$usr_enrol_tbl'") == $usr_enrol_tbl) {
                $columns = array(
                    /**
                     * @since 1.0.0
                     */
                    "enrolled_by" => "varchar(10)",
                    /**
                     * @since 1.0.1
                     */
                    "product_id" => "int(11)",
                    /**
                     * @since 1.2.0
                     */
                    "mdl_cohort_id" => "int(20)",
                    /**
                     * @since 1.2.0
                     */
                    "role" => "varchar(20)",
                );

                foreach ($columns as $colName => $colType) {
                    $query = "SHOW COLUMNS FROM `$usr_enrol_tbl` LIKE '$colName';";
                    $exists = $wpdb->query($query);

                    /**
                     * Checkes the column exist or not if not exist then add the column into the databse.
                     */
                    if (!$exists) {
                        $query = "ALTER TABLE `$usr_enrol_tbl` ADD COLUMN (`$colName` $colType);";
                        $wpdb->query($query);
                    }
                }
                self::createCohortInfoTable();
            }
        }

        public static function createCohortInfoTable()
        {
            global $wpdb;
            $charsetCollate = $wpdb->get_charset_collate();
            $tblCohortInfo = $wpdb->prefix . 'bp_cohort_info';
            $stmtCohortInfo = "CREATE TABLE IF NOT EXISTS $tblCohortInfo("
                    . "ID INT( 11 ) NOT NULL AUTO_INCREMENT ,"
                    . "COHORT_NAME VARCHAR( 300 ) NOT NULL ,"
                    . "MDL_COHORT_ID INT( 20 ) ,"
                    . "PRODUCTS VARCHAR( 100 ) ,"
                    . "COURSES VARCHAR( 100 ) NOT NULL ,"
                    . "COHORT_MANAGER INT( 10 ) NOT NULL ,"
                    . "INCOMP_ORD VARCHAR( 50 ) DEFAULT NULL ,"
                    . "SYNC TINYINT( 4 ) NOT NULL DEFAULT  '0',"
                    . "PRIMARY KEY ( ID ) ,"
                    . "UNIQUE KEY cohort_name( COHORT_NAME )"
                    . ")$charsetCollate;";
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta($stmtCohortInfo);
        }

        public static function modifyBpCohortTableColumnSize()
        {
            global $wpdb;
            $usr_enrol_tbl = $wpdb->prefix . 'bp_cohort_info';
            if ($wpdb->query("SHOW TABLES LIKE '".$usr_enrol_tbl."'")==1) {
                $query = "ALTER TABLE `$usr_enrol_tbl` MODIFY COLUMN COHORT_NAME varchar(300);";
                $wpdb->query($query);
            }
        }
    }
}
