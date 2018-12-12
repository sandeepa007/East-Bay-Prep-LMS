<?php

namespace app\wisdmlabs\edwiserBridge\BulkPurchase;

/**
 * The file that defines the core plugin class.
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link  www.wisdmlabs.com
 * @since 1.0.0
 */
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 *
 * @author     WisdmLabs, India <support@wisdmlabs.com>
 */
if (!class_exists("Edwiser_Multiple_Users_Course_Purchase")) {

    class EdwiserMultipleUsersCoursePurchase
    {

        /**
         * The loader that's responsible for maintaining and registering all hooks that power
         * the plugin.
         *
         * @since  1.0.0
         *
         * @var Edwiser_Multiple_Users_Course_Purchase_Loader Maintains and registers all hooks for the plugin.
         */
        protected $loader;

        /**
         * The unique identifier of this plugin.
         *
         * @since  1.0.0
         *
         * @var string The string used to uniquely identify this plugin.
         */
        protected $plugin_name;

        /**
         * The current version of the plugin.
         *
         * @since  1.0.0
         *
         * @var string The current version of the plugin.
         */
        protected $version;

        /**
         * Define the core functionality of the plugin.
         *
         * Set the plugin name and the plugin version that can be used throughout the plugin.
         * Load the dependencies, define the locale, and set the hooks for the admin area and
         * the public-facing side of the site.
         *
         * @since 1.0.0
         */
        public function __construct()
        {
            $this->plugin_name = 'edwiser-multiple-users-course-purchase';
            $this->version = '1.0.0';
            $this->defineConstants();
            $this->loadDependencies();
            $this->setLocale();
            $this->defineAdminHooks();
            $this->definePublicHooks();
            $this->defineEmailHooks();
            add_filter('check_group_purchase', array($this, 'wdmCheckGroupPurchase'), 10, 2);
            add_filter('eb_reset_email_tmpl_content', array($this, 'wdmParseEmailTemplate'), 10, 2);
            add_action('wdm_display_fields', array($this, 'wdmDisplayGroupPurchaseFields'), 10, 1);
            add_action('save_post', array($this, 'wdmSaveGroupPurchaseField'), 20, 2);
            $this->checkMoodleToken();
        }

        /**
         * Verifys that the plugin contains the moodle sso version 1.2.1 or higher
         */
        private function checkMoodleToken()
        {
            $connOptions = get_option('eb_connection');
            $mdlUrl = $this->getMdlConnectionUrl($connOptions);
            $mdlToken = $this->getMdlAccessToken($connOptions);
            $response = $this->prepateTokenVerifyRequest($mdlUrl, $mdlToken, "wdm_manage_cohort_enrollment", "");
            if (is_wp_error($response)) {
                add_action('admin_notices', array($this, 'moodlePluginInCompWarning'));
            } elseif (wp_remote_retrieve_response_code($response) == 200) {
                $body = json_decode(wp_remote_retrieve_body($response));
                /**
                 * Check moodle plugin installed and webservice function is added into the external services.
                 */
                if (isset($body->exception) && $body->exception == "webservice_access_exception") {
                    add_action('admin_notices', array($this, 'moodlePluginInCompWarning'));
                } else {
                    update_option("wdm_moodle_bp_version_notice", true);
                }
            }
        }

        private function getMdlConnectionUrl($connOptions)
        {
            $mdlUrl = false;
            if (isset($connOptions['eb_url'])) {
                $mdlUrl = $connOptions['eb_url'];
            }
            return $mdlUrl;
        }

        private function getMdlAccessToken($connOptions)
        {
            $mdlToken = false;
            if (isset($connOptions['eb_access_token'])) {
                $mdlToken = $connOptions['eb_access_token'];
            }
            return $mdlToken;
        }

        private function prepateTokenVerifyRequest($mdlUrl, $mdlToken, $webFunction, $token)
        {
            $reqUrl = $mdlUrl . '/webservice/rest/server.php?wstoken=';
            $reqUrl .= $mdlToken . '&wsfunction=' . $webFunction . '&moodlewsrestformat=json';
            $request_args = array(
                "body" => array('token' => $token),
            );
            return wp_remote_post($reqUrl, $request_args);
        }

        public function moodlePluginInCompWarning()
        {
            $isDismissed = get_option("wdm_moodle_bp_version_notice");
            if (isset($_GET['wdm_bp_mdl_v_check'])) {
                update_option("wdm_moodle_bp_version_notice", true);
                $isDismissed = true;
            }

            if (!$isDismissed) {
                $protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') === false ? 'http' : 'https';
                $host = $_SERVER['HTTP_HOST'];
                $script = $_SERVER['SCRIPT_NAME'];
                $params = $_SERVER['QUERY_STRING'];
                $url = $protocol . '://' . $host . $script . '?' . $params;
                ?>
                <div class="notice notice-warning">
                    <p>
                        <?php
                        _e("Moodle ", "ebbp-textdomain");
                        $docLink="<a href='https://edwiser.org/bridge/extensions/bulk-purchase/documentation/' target='_blank'>here</a>";
                        ?>
                        <a href="<?php echo EBBP_MDL_PLUGIN_DOWNLOAD_LINK; ?>" target="_blank">Bulk Purchase and Group Enrollment</a>
                        
                        <?php _e(" plugin is not compatible, please update to version 2.0.0 or higher. You can check the documentation $docLink. ", "ebbp-textdomain"); ?>
                        <a href="<?php echo add_query_arg(array("wdm_bp_mdl_v_check" => true), $url); ?>"><?php _e("Dismiss this notice", "ebbp-textdomain") ?></a>
                    </p>
                </div>
                <?php
            }
        }

        /**
         * Setup plugin constants.
         *
         * @since  1.0.0
         */
        private function defineConstants()
        {

            // Plugin version
            if (!defined('EB_WOO_EU_VERSION')) {
                define('EB_WOO_EU_VERSION', $this->version);
            }

            // Plugin Folder URL
            if (!defined('EB_WOO_EU_PLUGIN_URL')) {
                define('EB_WOO_EU_PLUGIN_URL', plugin_dir_url(dirname(__FILE__)));
            }

            // Plugin Folder Path
            if (!defined('EB_WOO_EU_PLUGIN_DIR')) {
                define('EB_WOO_EU_PLUGIN_DIR', plugin_dir_path(dirname(__FILE__)));
            }
        }

        /**
         * Load the required dependencies for this plugin.
         *
         * Include the following files that make up the plugin:
         *
         * - Edwiser_Multiple_Users_Course_Purchase_Loader. Orchestrates the hooks of the plugin.
         * - Edwiser_Multiple_Users_Course_Purchase_i18n. Defines internationalization functionality.
         * - Edwiser_Multiple_Users_Course_Purchase_Admin. Defines all hooks for the admin area.
         * - Edwiser_Multiple_Users_Course_Purchase_Public. Defines all hooks for the public side of the site.
         *
         * Create an instance of the loader which will be used to register the hooks
         * with WordPress.
         *
         * @since  1.0.0
         */
        private function loadDependencies()
        {
            include_once EB_WOO_EU_PLUGIN_DIR . 'includes/mucp-functions.php';
            include_once EB_WOO_EU_PLUGIN_DIR . 'includes/class-eb-bp-admin-noticess.php';

            if (!is_admin()) {
                $this->frontendDependencies();
            }

            include_once EB_WOO_EU_PLUGIN_DIR .
            'includes/class-edwiser-multiple-users-course-purchase-user-manager.php';
            new \app\wisdmlabs\edwiserBridge\BulkPurchase\EdwiserMultipleUsersCoursePurchaseUserManager();

            /**
             * class resopnsible for the manage enrollment table modifications
             */
            include_once EB_WOO_EU_PLUGIN_DIR . 'includes/class-edwiser-multiple-users-course-enrollment-manager.php';

            /**
             * The class responsible for orchestrating the actions and filters of the
             * core plugin.
             */
            include_once EB_WOO_EU_PLUGIN_DIR . 'includes/class-edwiser-multiple-users-course-purchase-loader.php';

            /**
             * The class responsible for defining internationalization functionality
             * of the plugin.
             */
            include_once EB_WOO_EU_PLUGIN_DIR . 'includes/class-edwiser-multiple-users-course-purchase-i18n.php';

            /**
             * The class responsible for defining all actions that occur in the admin area.
             */
            include_once EB_WOO_EU_PLUGIN_DIR . 'admin/class-edwiser-multiple-users-course-purchase-admin.php';

            /**
             * The class responsible for defining all actions that occur in the public-facing
             * side of the site.
             */
            include_once EB_WOO_EU_PLUGIN_DIR . 'public/class-edwiser-multiple-users-course-purchase-public.php';

            include_once EB_WOO_EU_PLUGIN_DIR . 'includes/class-edwiser-multiple-users-course-purchase-enroll-self.php';

            include_once 'class-eb-bp-ajax-handler.php';

            include_once 'class-eb-bp-manage-cohort.php';
            include_once 'class-eb-bp-cohort-manage-user.php';

            include_once 'emails/class-eb-bp-emailer.php';
            include_once EB_WOO_EU_PLUGIN_DIR . 'public/class-edwiser-multiple-users-course-enroll-users.php';
            $this->loader = new \app\wisdmlabs\edwiserBridge\BulkPurchase\EdwiserMultipleUsersCoursePurchaseLoader();
        }

        /**
         * public facing code.
         *
         * Include the following files that make up the plugin:
         * - Bridge_Woocommerce_Shortcodes. Defines set of shortcode.
         * - Bridge_Woo_Shortcode_Associated_Courses. Defines output for associated courses.
         *
         * @since  1.0.0
         */
        private function frontendDependencies()
        {
            /**
             * Tha classes responsible for defining shortcodes & templates.
             */
            include_once EB_WOO_EU_PLUGIN_DIR . 'public/class-edwiser-enroll-multiple-user-shortcode.php';
            include_once EB_WOO_EU_PLUGIN_DIR . 'public/shortcodes/class-eb-shortcode-enroll-users.php';
        }

        private function defineEmailHooks()
        {
            $pluginEmailer=  new EbBpSendEmailer();
            $this->loader->addAction('eb_bp_bulk_purchase_email', $pluginEmailer, 'sendBulkPurchaseEmail', 10, 1);
            $this->loader->addAction('eb_bp_new_user_to_cohort', $pluginEmailer, 'sendCohortEnrollmentEmail', 10, 1);
            $this->loader->addAction('eb_bp_remove_user_from_cohort', $pluginEmailer, 'sendCohortUnEnrollmentEmail', 10, 1);
        }

        /**
         * Define the locale for this plugin for internationalization.
         *
         * Uses the Edwiser_Multiple_Users_Course_Purchase_i18n class in order to set the domain and to register the hook
         * with WordPress.
         *
         * @since  1.0.0
         */
        private function setLocale()
        {
            $plugin_i18n = new \app\wisdmlabs\edwiserBridge\BulkPurchase\EdwiserMultipleUsersCoursePurchasei18n();
            $plugin_i18n->setDomain($this->getPluginName());
            $this->loader->addAction('plugins_loaded', $plugin_i18n, 'loadPluginTextdomain');
        }

        /**
         * Register all of the hooks related to the admin area functionality
         * of the plugin.
         * @since  1.0.0
         */
        private function defineAdminHooks()
        {
            $plugin_admin = new \app\wisdmlabs\edwiserBridge\BulkPurchase\EdwiserMultipleUsersCoursePurchaseAdmin(
                $this->getPluginName(),
                $this->getVersion()
            );


            new EbBpDbMigrate();
//            $databaseBackup = new BPDbBackUp();
//            $this->loader->addAction('wp_ajax_backup_moodle_enrollment', $databaseBackup, 'run');

            /**
             * class responsible for the modification in the manage enrollment table
             */
            $manageEnroll = new \app\wisdmlabs\edwiserBridge\BulkPurchase\EnrollmentManager();

            /**
             * filter to add more columns to the wp list table of manage enrollment
             */
            $this->loader->addFilter('edwiser_add_colomn_to_manage_enrollment', $manageEnroll, 'addColumnsToManageEnrollTable', 10);

            $this->loader->addFilter('eb_manage_student_enrollment_table_data', $manageEnroll, 'manageEnrollmentTableData', 10);

            /**
             * Class object to handle the ajax callback
             * @since 1.2.0
             */
            $adminAjaxInit = new BPAdminAjaxInitiater();

            /**
             * Cohort class object to handle the cohort callbacks.
             * @since 1.2.0
             */
            $cohortManager = new BPManageCohort();

            /**
             * action to enroll user into cohort
             * @since  1.2.0
             */
/*            $this->loader->addAction('wp_ajax_enroll_user_to_cohort', $cohortManager, 'updateCohortOnUserEnrollment');*/


            /**
             * Action to enque style and JS
             * @since 1.0.0
             */
            $this->loader->addAction('admin_enqueue_scripts', $plugin_admin, 'enqueueStyles');
            $this->loader->addAction('admin_enqueue_scripts', $plugin_admin, 'enqueueScripts');

            /**
             * Action to add the MAnge user page.
             * @since 1.2.0
             */


/*            $this->loader->addAction('admin_menu', $plugin_admin, 'addEbSubmenuUserEnrollment', 11);
*/
            /**
             * Added the actiona for the custom user profile fildes
             * @since 1.2.0
             */
            $this->loader->addAction('user_new_form', $plugin_admin, 'ebMyCustomUserfields');
            $this->loader->addAction('show_user_profile', $plugin_admin, 'ebMyCustomUserfields');
            $this->loader->addAction('edit_user_profile', $plugin_admin, 'ebMyCustomUserfields');

            /**
             * Added the actions to save the user profile filed
             * @since 1.2.0
             */
            $this->loader->addAction('user_register', $plugin_admin, 'ebSaveCustomUserProfilefields');
            $this->loader->addAction('personal_options_update', $plugin_admin, 'ebSaveCustomUserProfilefields');
            $this->loader->addAction('edit_user_profile_update', $plugin_admin, 'ebSaveCustomUserProfilefields');

            /**
             * Action to handle the user unenrollment
             * @since 1.2.0
             */
            $this->loader->addAction('wp_ajax_mucp_unenrol_user', $adminAjaxInit, 'ebbpActionManageUnenrol');

            /**
             * Action to show cohort details
             * @since 1.2.0
             */
            $this->loader->addAction('wp_ajax_mucp_cohort_details', $adminAjaxInit, 'ebbpCohortDetails');

            /**
             * Action to make the company filed compalsoty and save the company details on the woocomerce checkout.
             * @since 1.2.0
             */
            $this->loader->addAction('user_register', $cohortManager, 'updateUserProfile');
            $this->loader->addAction('woocommerce_billing_fields', $cohortManager, 'mandatoryCompanyFiled');
            $this->loader->addAction('woocommerce_checkout_order_processed', $cohortManager, 'handleOrderPlaced', 10);
            // $this->loader->addAction('woocommerce_order_status_completed', $cohortManager, 'orderComplete', 12);

            /**
             * Actions to add the custom fildes on the edit user profile page and save the user profile data.
             * @since 1.2.0
             */
            $this->loader->addAction('eb_edit_user_profile', $plugin_admin, 'ebCustomEditProfileFields');
            $this->loader->addAction('eb_save_account_details', $plugin_admin, 'ebSaveCustomUserProfilefields');

            
            /**
             * Action to create the cohort on the moodle and wordpress.
             * @since 1.2.0
             */
            $this->loader->addAction('eb_save_account_details', $plugin_admin, 'ebSaveCustomUserProfilefields');
            /**
             * Action to add the general setting option in the EB settings page
             * @since 1.2.0
             */
            $this->loader->addFilter('eb_general_settings', $plugin_admin, 'ebGeneralSettings', 111);

            /**
             * Action to add the email template and tempalate constants.
             * @since 1.2.0
             */
            $emailTmplManag=new EbBpTemplateManager();
            $this->loader->addFilter('eb_email_templates_list', $emailTmplManag, 'ebTemplatesList', 111);
            $this->loader->addFilter('eb_email_template_constant', $emailTmplManag, 'ebTemplatesConstants', 111);
            $this->loader->addFilter('eb_emailtmpl_content_before', $emailTmplManag, 'emailTemplateParser', 111);
        }

        /**
         * Register all of the hooks related to the public-facing functionality
         * of the plugin.
         *
         * @since  1.0.0
         */
        private function definePublicHooks()
        {
            $plugin_public = new \app\wisdmlabs\edwiserBridge\BulkPurchase\EdwiserMultipleUsersCoursePurchasePublic(
                $this->getPluginName(),
                $this->getVersion()
            );

            new \app\wisdmlabs\edwiserBridge\BulkPurchase\EdwiserMultipleCourseEnrollUsers();

            $this->loader->addAction('wp_enqueue_scripts', $plugin_public, 'enqueueStyles');
            $this->loader->addAction('wp_enqueue_scripts', $plugin_public, 'enqueueScripts');
        }

        public function wdmParseEmailTemplate($args)
        {
            $emailTmplPars=new EbBpTemplateManager();
            return $emailTmplPars->handleTemplateRestore($args);
        }

        /**
         * Run the loader to execute all of the hooks with WordPress.
         *
         * @since 1.0.0
         */
        public function run()
        {
            $this->loader->run();
        }

        /**
         * The name of the plugin used to uniquely identify it within the context of
         * WordPress and to define internationalization functionality.
         *
         * @since  1.0.0
         *
         * @return string The name of the plugin.
         */
        public function getPluginName()
        {
            return $this->plugin_name;
        }

        /**
         * The reference to the class that orchestrates the hooks with the plugin.
         *
         * @since  1.0.0
         *
         * @return Edwiser_Multiple_Users_Course_Purchase_Loader Orchestrates the hooks of the plugin.
         */
        public function getLoader()
        {
            return $this->loader;
        }

        /**
         * Retrieve the version number of the plugin.
         *
         * @since  1.0.0
         *
         * @return string The version number of the plugin.
         */
        public function getVersion()
        {
            return $this->version;
        }

        public function wdmCheckGroupPurchase($group_purchase, $product_id)
        {
            unset($group_purchase);
            $product_options = get_post_meta($product_id, 'product_options', true);
            if (isset($product_options['moodle_course_group_purchase'])) {
                return $product_options['moodle_course_group_purchase'];
            }

            return 'off';
        }

        public function wdmSaveGroupPurchaseField()
        {
            if (isset($_POST['ID']) && isset($_POST['product-type']) && $_POST['product-type'] == 'simple') {
                $post_id = $_POST['ID'];
                $post_type = get_post_type($post_id);
                if (isset($_POST[$post_type . '_options']) && !empty($_POST[$post_type . '_options'])) {
                    $product_options = get_post_meta($post_id, $post_type . '_options', true);
                    if (!isset($product_options)) {
                        $product_options = array();
                    }
                    if (isset($_POST['moodle_course_group_purchase']) &&
                        !empty($_POST['moodle_course_group_purchase'])) {
                        $product_options['moodle_course_group_purchase'] = $_POST['moodle_course_group_purchase'];
                        update_post_meta($post_id, '_sold_individually', '');
                    } else {
                        $product_options['moodle_course_group_purchase'] = 'off';
                        update_post_meta($post_id, '_sold_individually', 'yes');
                    }
                    update_post_meta($post_id, $post_type . '_options', $product_options);
                }
            }
        }

        public function wdmDisplayGroupPurchaseFields($product_id)
        {
            global $post;

            $checked = 'on';
            $current = 'off';
            $product_id = $post->ID;
            $product_options = get_post_meta($product_id, 'product_options', true);

            if (isset($product_options['moodle_course_group_purchase'])
             && !empty($product_options['moodle_course_group_purchase'])
             ) {
                $current = $product_options['moodle_course_group_purchase'];
            }
            ?>
            <div class="ebbp-settings show_if_simple">
                <p class="form-field">
                    <label for="moodle_course_group_purchase"><?php _e('Group Purchase', 'ebbp-textdomain');
            ?></label>
                    <input type="checkbox" id="moodle_course_group_purchase" name ="moodle_course_group_purchase" <?php echo ($checked == $current) ? 'checked' : '' ?> >
                    <img class="help_tip" data-tip='<?php _e('Allow user to purchase course product in bulk.', 'ebbp-textdomain') ?>' src="<?php echo esc_url(WC()->plugin_url());
            ?>/assets/images/help.png" height="16" width="16" />
                </p>
            </div>
            <?php
        }
    }
}
