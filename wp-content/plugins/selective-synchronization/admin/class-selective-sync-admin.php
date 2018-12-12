<?php

namespace ebSelectSync\admin;

use app\wisdmlabs\edwiserBridge as ed_parent;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://wisdmlabs.com
 * @since      1.0.0
 *
 * @package    Selective_Sync
 * @subpackage Selective_Sync/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Selective_Sync
 * @subpackage Selective_Sync/admin
 * @author     WisdmLabs <support@wisdmlabs.com>
 */
class SelectiveSyncAdmin
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

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueueStyles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in SelectiveSyncLoader as all of the hooks are defined
         * in that particular class.
         *
         * The SelectiveSyncLoader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style('select-datatable-css', SELECTIVE_SYNC_PLUGIN_URL . 'admin/assets/css/datatable.css', array(), $this->version, 'all');
        wp_enqueue_style('select-admin-css', SELECTIVE_SYNC_PLUGIN_URL . 'admin/assets/css/eb-select-sync.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueueScripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in SelectiveSyncLoader as all of the hooks are defined
         * in that particular class.
         *
         * The SelectiveSyncLoader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_script('select-datatable-js', SELECTIVE_SYNC_PLUGIN_URL . 'admin/assets/js/jquery.dataTables.js', array( 'jquery' ));
        wp_enqueue_script('columnfilter-datatable-js', SELECTIVE_SYNC_PLUGIN_URL . 'admin/assets/js/jquery.dataTables.columnFilter.js', array( 'select-datatable-js' ));
        wp_register_script('select-admin-js', SELECTIVE_SYNC_PLUGIN_URL . 'admin/assets/js/eb-select-sync.js', array( 'jquery','select-datatable-js','edwiserbridge','columnfilter-datatable-js' ));
    }
    
    /*
	 * Add "Selective Sync" tab in course synchronization after "User" 
	 *
	 * @param $section 	array  List of section in synchronize tab
	 * @return $section array Modified array with "Product" tab
	 * @since 1.0.2
	 */
    public function multipleCourseSynchronizationSection($section)
    {

        
        $section = array_merge(
            array_slice($section, 0, 1),
            array('select_sync' => __('Selective Courses', 'selective_synchronization')),
            array_slice($section, 1, null)
        );

      
        return $section;
    }
    
    /*
	 * Add fields in "Selective Sync" tab 
	 *
	 * @param $settings array List of settings fields
	 * @param $current_section string Gives current displayed section
	 *
	 * @return $settings array Modified array with settings for Selective Sync section
	 * @since 1.0.2
	 */
    public function multipleCourseSynchronizationSetting($settings, $current_section)
    {
        // echo "settings";
        // die()
        
        if ('select_sync' == $current_section) {
            $settings = array();

            $connected = ed_parent\edwiserBridgeInstance()->connectionHelper()->connectionTestHelper(EB_ACCESS_URL, EB_ACCESS_TOKEN);
            
            //$response_array['connection_response'] = $connected['success']; // add connection response in response array

            if ($connected['success'] == 1) {
                $response = ed_parent\edwiserBridgeInstance()->courseManager()->getMoodleCourses();
                $category_response = ed_parent\edwiserBridgeInstance()->courseManager()->getMoodleCourseCategories();

                if ($response['success'] == 1) {
                    if ($category_response['success'] == 1) {
                        $moodle_category_data = $category_response['response_data'];
                    }

                    $moodle_courses_data = $response['response_data'];

                    $settings = apply_filters('eb_select_course_synchronization_settings', array(

                        array(
                            'type'   => 'title',
                            'id'     => 'select_sync_options'
                        ),


                        array(
                            'title'           => __('Synchronization Options', 'selective_synchronization'),
                            'desc'            => __('Update previously synchronized courses', 'selective_synchronization'),
                            'id'              => 'eb_update_selected_courses',
                            'default'         => 'no',
                            'type'            => 'checkbox',
                            'show_if_checked' => 'option',
                            'autoload'        => false

                        ),

                        array(
                        'title'    => __('', 'selective_synchronization'),
                        'desc'     => __('', 'selective_synchronization'),
                        'id'       => 'eb_sync_selected_course_button',
                        'default'  => 'Start Synchronization',
                        'type'     => 'button',
                        'desc_tip' =>  false,
                        'class'    => 'button secondary'
                        ),

                        array(
                            'type'  => 'sectionend',
                            'id'    => 'select_sync_options'
                        ),

                    ));
                }
            } else {
                $moodle_category_data = array();
                $moodle_courses_data = array();
            }

            $category_list = array();

            include_once SELECTIVE_SYNC_PLUGIN_DIR. 'admin/partials/select_moodle_course_list.php';

            $nonce = wp_create_nonce('check_select_course_sync_action');

            $array_data = array('admin_ajax_path' => admin_url('admin-ajax.php'),
                                'nonce'           => $nonce,
                                'category_list'   => $category_list,
                                'chk_error'       => __('Select atleast one course to Synchronize.', 'selective_synchronization'),
                                'select_success'  => __('Courses synchronized successfully.', 'selective_synchronization'),
                                'connect_error'   => __('There is a problem while connecting to moodle server.', 'selective_synchronization') );

            wp_enqueue_script('select-admin-js');
            wp_localize_script('select-admin-js', 'admin_js_select_data', $array_data);
        }

        return $settings;
    }
}
