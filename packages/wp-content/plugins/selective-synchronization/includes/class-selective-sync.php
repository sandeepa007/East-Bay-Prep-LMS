<?php

namespace ebSelectSync\includes;

use ebSelectSync\admin as eb_admin;

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://wisdmlabs.com
 * @since      1.0.0
 *
 * @package    SelectiveSync
 * @subpackage SelectiveSync/includes
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
 * @package    SelectiveSync
 * @subpackage SelectiveSync/includes
 * @author     WisdmLabs <support@wisdmlabs.com>
 */
class SelectiveSync
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      SelectiveSyncLoader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;
    
    /**
    *
    *
    * @var SelectiveSync The single instance of the class
    * @since 1.0.0
    */
    protected static $_instance = null;
    
    /**
    * Main SelectiveSync Instance
    *
    * Ensures only one instance of SelectiveSync is loaded or can be loaded.
    *
    * @since 1.0.0
    * @static
    * @see SelectiveSync()
    * @return SelectiveSync - Main instance
    */
    public static function instance()
    {
    
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {

        $this->plugin_name = 'selective_synchronization';
        $this->version = '1.0.0';
        $this->defineConstants();
        $this->loadDependencies();
        // $this->set_locale();
        $this->defineAdminHooks();
        $this->definePublicHooks();
        
    }
    
    /**
    * Setup plugin constants
    *
    * @access private
    * @since 1.0.0
    * @return void
    */
    private function defineConstants()
    {
      
           // Plugin version
        if (!defined('SELECTIVE_SYNC_VERSION')) {
            define('SELECTIVE_SYNC_VERSION', $this->version);
        }
       
           // Plugin Folder URL
        if (!defined('SELECTIVE_SYNC_PLUGIN_URL')) {
            define('SELECTIVE_SYNC_PLUGIN_URL', plugin_dir_url(dirname(__FILE__)));
        }
       
           // Plugin Folder Path
        if (!defined('SELECTIVE_SYNC_PLUGIN_DIR')) {
            define('SELECTIVE_SYNC_PLUGIN_DIR', plugin_dir_path(dirname(__FILE__)));
        }
      
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - SelectiveSyncLoader. Orchestrates the hooks of the plugin.
     * - Selective_Sync_i18n. Defines internationalization functionality.
     * - SelectiveSyncAdmin. Defines all hooks for the admin area.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function loadDependencies()
    {

        if (!is_admin()) {
            $this->frontendDependencies();
        }

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        include_once SELECTIVE_SYNC_PLUGIN_DIR . 'includes/class-selective-sync-loader.php';

        $this->loader = new SelectiveSyncLoader();

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        // include_once SELECTIVE_SYNC_PLUGIN_DIR . 'includes/class-bridge-woocommerce-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        include_once SELECTIVE_SYNC_PLUGIN_DIR . 'admin/class-selective-sync-admin.php';
        
        /*
		 *The class responsible for defining all actions that occur for AJAX
		 */
        
        include_once SELECTIVE_SYNC_PLUGIN_DIR . 'admin/class-selective-ajax-handler-admin.php';
    }
    
    /**
    * public facing code
    *
    * Include the following files that make up the plugin:
    * - Selective_Sync_Shortcodes. Defines set of shortcode.
    * - Bridge_Woo_Shortcode_Associated_Courses. Defines output for associated courses.
    *
    * @return void
    * @since    1.0.0
    * @access   private
    */
    private function frontendDependencies()
    {
        
        /**
        * Tha classes responsible for defining shortcodes & templates
        */
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Selective_Sync_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    // private function set_locale() {

    // 	$plugin_i18n = new Selective_Sync_i18n();
    // 	$plugin_i18n->set_domain( $this->getPluginName() );

    // 	$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

    // }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function defineAdminHooks()
    {
        // echo "defineAdminHooks";
        // die();
        global $eb_select_plg_data;

        include_once  plugin_dir_path(__FILE__) . '/class-eb-select-get-plugin-data.php';

        $get_data_from_db = EBSelectGetPluginData::getDataFromDb($eb_select_plg_data);
        if ($get_data_from_db == 'available') {
            $plugin_admin = new eb_admin\SelectiveSyncAdmin($this->getPluginName(), $this->getVersion());
    
            $this->loader->addAction('admin_enqueue_scripts', $plugin_admin, 'enqueueStyles');
            $this->loader->addAction('admin_enqueue_scripts', $plugin_admin, 'enqueueScripts', 15);

            //Add Selective Course synchronization setting
            // $plugin_admin->multipleCourseSynchronizationSection('test,kjkjk');

            // die();
            $this->loader->addFilter('eb_getSections_synchronization', $plugin_admin, 'multipleCourseSynchronizationSection', 12, 1);
            
            $this->loader->addFilter('eb_get_settings_synchronization', $plugin_admin, 'multipleCourseSynchronizationSetting', 10, 2);
        //       echo "defineAdminHooks";
        // die();
            //Action to sync selected courses
            $ajax_handle_obj = new eb_admin\SelectiveAjaxHandlerAdmin($this->getPluginName(), $this->getVersion());

            $this->loader->addAction('wp_ajax_selective_course_sync', $ajax_handle_obj, 'selectedCourseSynchronizationInitiater');

            
        }
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function definePublicHooks()
    {

                    
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function getPluginName()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    SelectiveSyncLoader    Orchestrates the hooks of the plugin.
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function getVersion()
    {
        return $this->version;
    }
}

/**
 * Returns the main instance of SelectiveSync to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return SelectiveSync
 */
function SelectiveSync()
{
    return SelectiveSync::instance();
}
