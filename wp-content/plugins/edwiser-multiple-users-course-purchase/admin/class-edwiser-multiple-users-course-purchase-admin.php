<?php

namespace app\wisdmlabs\edwiserBridge\BulkPurchase;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link  www.wisdmlabs.com
 * @since 1.0.0
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @author     WisdmLabs, India <support@wisdmlabs.com>
 */
class EdwiserMultipleUsersCoursePurchaseAdmin
{

    /**
     * The ID of this plugin.
     *
     * @since  1.0.0
     *
     * @var string The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since  1.0.0
     *
     * @var string The current version of this plugin.
     */
    private $version;
    protected $edwiser_bridge;

    /**
     * Initialize the class and set its properties.
     *
     * @since 1.0.0
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version     The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        require_once EB_PLUGIN_DIR . 'includes/class-eb.php';
        $this->edwiser_bridge = new \app\wisdmlabs\edwiserBridge\EdwiserBridge();
        //var_dump('calling function');die();
        //add_action('admin_menu', array($this,'addPluginAdminMenu'), 1000);
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since 1.0.0
     */
    public function enqueueStyles()
    {
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'css/edwiser-multiple-users-course-purchase-admin.css',
            array(),
            $this->version,
            'all'
        );
        wp_enqueue_style(
            $this->plugin_name . "-jquery-ui",
            plugin_dir_url(__FILE__) . 'css/jquery-ui.min.css',
            array(),
            $this->version,
            'all'
        );
        wp_enqueue_style(
            $this->plugin_name . '_admin_font_awesome',
            plugin_dir_url(__FILE__) . '../public/css/font-awesome-4.4.0/css/font-awesome.min.css',
            array(),
            '1.0.2',
            'all'
        );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since 1.0.0
     */
    public function enqueueScripts()
    {
/*        wp_enqueue_script(
            $this->plugin_name . "-jquery-ui",
            plugin_dir_url(__FILE__) . 'js/jquery-ui.min.js',
            array('jquery'),
            $this->version,
            false
        );*/
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'js/edwiser-multiple-users-course-purchase-admin.js',
            array('jquery'),
            $this->version,
            false
        );
        wp_enqueue_script(
            "ebbp_migration",
            plugin_dir_url(__FILE__) . 'js/edwiser-multiple-users-course-purchase-migrate.js',
            array('jquery'),
            $this->version,
            false
        );
    }




    /**
     * Bulk purchase settings.
     *
     * @since 1.1.0
     */
    public function ebGeneralSettings($settings)
    {
        if (is_array($settings)) {
            $last = $settings[count($settings) - 1];
            unset($settings[count($settings) - 1]);

            $mucp_settings[] = array(
                'title' => __('Enroll Students Page', 'ebbp-textdomain'),
                'desc' => '<br />' . __('Select the page having shortcode [bridge_woo_enroll_users].', 'ebbp-textdomain'),
                'id' => 'mucp_group_enrol_page_id',
                'type' => 'single_select_page',
                'default' => '',
                'css' => 'min-width:300px;',
                'args' => array(
                    'show_option_none' => __('- Select a page -', 'ebbp-textdomain'),
                    'option_none_value' => '',
                )
            );

            $mucp_settings = (array) apply_filters('mucp_settings', $mucp_settings);

            foreach ($mucp_settings as $the_setting) {
                $settings[] = $the_setting;
            }
            $settings[] = $last;
        }
        return $settings;
    }

    public function actionManageUnenrol()
    {
        $response = array(
            'status' => false,
            'message' => __('Failed!', 'ebbp-textdomain')
        );

        if (isset($_POST['rec_id'])) {
            $rec_id = sanitize_text_field($_POST['rec_id']);
            if (is_numeric($rec_id)) {
                global $wpdb;
                $record = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}moodle_enrollment WHERE id={$rec_id};");
                if (is_array($record) && count($record) === 1) {
                    $course_unenrolled = $this->edwiser_bridge->enrollmentManager()->updateUserCourseEnrollment(
                        array(
                            'user_id' => $record[0]->user_id,
                            'courses' => array($record[0]->course_id),
                            'unenroll' => 1,
                            'suspend' => 1
                        )
                    );

                    $course_unenrolled = 1;
                    if ($course_unenrolled == 1) {
                        $response = array(
                            'status' => true,
                            'message' => __('Unenrolled successfully!', 'ebbp-textdomain')
                        );

                        $wpdb->delete("{$wpdb->prefix}moodle_enrollment", array('id' => $rec_id));
                    }
                }
            }
        }
        wp_send_json_success($response);
    }

    /**
     * Adds the company name filed on the user profile in backend.
     * callback for the user_new_form,show_user_profile,edit_user_profile
     * @param WP_User $users object
     */
    public function ebMyCustomUserfields($users)
    {
        if (property_exists($users, "ID")) {
            $wdmCompany=  get_user_meta($users->ID, "wdm_company", true);
        }
        $wdmCompany = "";
        ?>
        <h3><?php _e('Extra profile information', 'ebbp-textdomain'); ?></h3>
        <table class="form-table">
            <tr>
                <th><label for="wdm_company"><?php _e('Company Name', 'ebbp-textdomain'); ?></label></th>
                <td>
                    <input type="text" class="regular-text" name="wdm_company" id="wdm_company"
                    value="<?php echo $wdmCompany; ?>"/>
                    <span class="description"><?php _e('Where are you?', 'ebbp-textdomain');  ?></span>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Sets the user meta on the user profile update from the wp backend.
     * callback for the user_register,personal_options_update,edit_user_profile_update hooks.
     * @param type $user_id user id to update the frofile
     */
    public function ebSaveCustomUserProfilefields($user_id)
    {
        # save my custom field
        if (isset($_POST['wdm_company'])) {
            update_user_meta($user_id, 'wdm_company', $_POST['wdm_company']);
        }
    }

    /**
     * Adds the compny name fileds on the edit user profil in front end of the Edwiser bridge user account page.
     * @param Object $current_user curent user object.
     */
    public function ebCustomEditProfileFields($current_user)
    {
        $company = (
            isset($_POST['wdm_company']) &&
            !empty($_POST['wdm_company'])) ?
             $_POST['wdm_company'] :
             $current_user->wdm_company;
        ?>
        <p class="form-company">
            <label for="wdm_company"><?php _e('Company Name', 'eb-textdomain'); ?></label>
            <input class="text-input" name="wdm_company" type="text" id="wdm_company" value="<?php echo $company; ?>" />
        </p>
        <?php
    }
}
