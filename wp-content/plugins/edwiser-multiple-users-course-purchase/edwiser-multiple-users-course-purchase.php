<?php
namespace app\wisdmlabs\edwiserBridge\BulkPurchase;

/*
 *
 * @link    www.wisdmlabs.com
 * @since   1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       Bulk Purchase and Group Registration
 * Plugin URI:        www.wisdmlabs.com
 * Description:       This plugin allows to group enrollment of users in moodle.
 * Version:           2.0.1
 * Author:            WisdmLabs, India
 * Author URI:        www.wisdmlabs.com
 * License:           GNU General Public License v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ebbp-textdomain
 * Domain Path:       /languages/
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/*
 * Define necessary constants.
 * @author Pandurang
 * @since 1.0.1
 */
define('EBBP_TD', 'ebbp-textdomain');
define('EBBP_DIR_NAME', dirname(plugin_basename(__FILE__)));
define('EBBP_MDL_PLUGIN_DOWNLOAD_LINK', "https://edwiser.org/wp-content/uploads/edd/2017/06/wdmgroupregistration.zip");
include_once 'includes/class-eb-bp-activator.php';
include_once 'includes/class-eb-bp-deactivate.php';
include_once 'includes/class-eb-select-add-plugin-data-in-db.php';
//Email template manager
include_once 'includes/class-eb-bp-email-template-manager.php';
// Cohort manager functoinality
include_once 'includes/class-eb-bp-manage-cohort.php';
//Migraton functionality for versoin 1.0.2 to 2.0.0
include_once 'migrate/class-eb-bp-db-backup.php';
include_once 'migrate/class-eb-bp-migrate.php';

/*
 * Register activation and deactivation hooks.
 */
register_activation_hook(__FILE__, 'app\wisdmlabs\edwiserBridge\BulkPurchase\activatePlugin');
register_deactivation_hook(__FILE__, 'app\wisdmlabs\edwiserBridge\BulkPurchase\deactivatePlugin');

function activatePlugin()
{
    BPPluginActivator::activate(false);
}
/*
 * Process plugin upgrade on admin init action.
 */
add_action('admin_init', 'app\wisdmlabs\edwiserBridge\BulkPurchase\processUpgrade');

/**
 * This will provides the functionality to execute the initial settings on plugin upgrade
 * since from wordpress version 3.4 activation is not runing on plugin update.
 * To handel the upgrade process we have added the following tweek.
 * It is required to update the $newVersion variable to latest version in every release
 * This function will store the updated versoin number in database and compair it with stored
 * version number and executes the intialisation code.
 */
function processUpgrade()
{
    $flag = ebbpCheckPluginDependency();

    if ($flag) {
        $newVersion = '2.0.1';
        $currentVersion = get_option('eb_bp_plugin_version');

        if ($currentVersion == false || $newVersion != $currentVersion) {
            BPPluginActivator::activate(false);
            update_option('eb_bp_plugin_version', $newVersion);
        }
    }
}

/**
 * Initilises the plugin deactivation process
 * This will run on the plugin deactivation hook.
 */
function deactivatePlugin()
{
    EBBpDeactivate::deactivate();
}

/**
 * This will loads the textdomain and initates the trnslation functonality
 * This will load the langauge files.
 *
 * Load plugin textdomain.
 *
 * @author Pandurang
 *
 * @since 1.0.1
 */
function ebbpLoadTxtDomain()
{
    load_plugin_textdomain('ebbp-textdomain', false, EBBP_DIR_NAME.'/languages');
}
add_action('plugins_loaded', 'app\wisdmlabs\edwiserBridge\BulkPurchase\ebbpLoadTxtDomain');

function edb_bcp_is_session_started()
{
    if (php_sapi_name() !== 'cli') {
        if (version_compare(phpversion(), '5.4.0', '>=')) {
            return session_status() === PHP_SESSION_ACTIVE ? true : false;
        } else {
            return session_id() === '' ? false : true;
        }
    }

    return false;
}
// start the Session
if (edb_bcp_is_session_started() === false) {
    session_start();
}

/*
 * Defines the plugin information to send to the server to activate the plugin.
 */
$ebBpPluginData = array(
    'plugin_short_name' => 'Bulk Purchase',
    'plugin_slug' => 'bulk-purchase',
    'plugin_version' => '2.0.1',
    'plugin_name' => 'Bulk Purchase',
    'store_url' => 'https://edwiser.org/check-update',
    'author_name' => 'WisdmLabs',
    'pluginTextDomain' => 'ebbp-textdomain',
);

new EBMUSelectAddPluginDataInDB($ebBpPluginData);

/*
 * This code checks if new version is available
 */
if (!class_exists('EBMUSelectPluginUpdater')) {
    include 'includes/class-eb-select-plugin-updater.php';
}
$l_key = trim(get_option('edd_'.$ebBpPluginData['plugin_slug'].'_license_key'));

// setup the updater
new EBMUSelectPluginUpdater($ebBpPluginData['store_url'], __FILE__, array(
    'version' => $ebBpPluginData['plugin_version'], // current version number
    'license' => $l_key, // license key (used get_option above to retrieve from DB)
    'item_name' => $ebBpPluginData['plugin_name'], // name of this plugin
    'author' => $ebBpPluginData['author_name'], //author of the plugin
        ));

/*
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */

$activatedPlugins = apply_filters('active_plugins', get_option('active_plugins'));
$pluginDependancy = array('woocommerce/woocommerce.php', 'edwiser-bridge/edwiser-bridge.php', 'woocommerce-integration/bridge-woocommerce.php');

/*
 * This will check is the plugin licens is activated or not and initilises the plugin functionality
 */
if (count(array_diff($pluginDependancy, $activatedPlugins)) <= 0) {
    include 'includes/class-eb-select-get-plugin-data.php';
    $getDataFromDB = EBMUSelectGetPluginData::getDataFromDB($ebBpPluginData);
    if ('available' == $getDataFromDB) {
        $activatedPlugins = apply_filters('active_plugins', get_option('active_plugins'));

        require plugin_dir_path(__FILE__).'includes/class-edwiser-multiple-users-course-purchase.php';

        /**
         * Begins execution of the plugin.
         *
         * Since everything within the plugin is registered via hooks,
         * then kicking off the plugin from this point in the file does
         * not affect the page life cycle.
         *
         * @since 1.0.0
         */
        function run_edwiser_multiple_users_course_purchase()
        {
            $plugin = new EdwiserMultipleUsersCoursePurchase();
            $plugin->run();
        }
        run_edwiser_multiple_users_course_purchase();
        add_action('admin_menu', 'app\wisdmlabs\edwiserBridge\BulkPurchase\migrationSubmenu');
        add_action('admin_init', 'app\wisdmlabs\edwiserBridge\BulkPurchase\redirectOnActivation');
    }
}

/*
 * check dependencies with Edwiser bridge, Woocommerce and woocommerce integration plugin
 */
if (!function_exists('app\wisdmlabs\edwiserBridge\BulkPurchase\ebbpCheckPluginDependency')) {
    function ebbpCheckPluginDependency()
    {
        $flag = 1;
        unset($_GET['activate']);
        if (current_user_can('activate_plugins')) {
            global $ebBpPluginData;
            $active_plugins = apply_filters('active_plugins', get_option('active_plugins'));
            if (!in_array('woocommerce/woocommerce.php', $active_plugins) && !in_array('edwiser-bridge/edwiser-bridge.php', $active_plugins) && !in_array('woocommerce-integration/bridge-woocommerce.php', $active_plugins)) {
                deactivate_plugins(plugin_basename(__FILE__));
                ?>
                <div id="message" class="error">
                    <p>
                        <?php
                        printf(__('%s %s is inactive.%s Install and activate %sWooCommerce%s,
                        %sEdwiserBridge%s and %sWoocommerce Integration%s for %s to work.', 'ebbp-textdomain'), '<strong>', $ebBpPluginData['plugin_name'], '</strong>', '<a href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', '<a href="http://wordpress.org/extend/plugins/edwiser-bridge/">', '</a>', '<a href="https://wisdmlabs.com/woocommerce-moodle-integration-solution">', '</a>', $ebBpPluginData['plugin_name']);
                ?>
                    </p>
                </div>
                <?php
                $flag = 0;
            } elseif (!in_array('edwiser-bridge/edwiser-bridge.php', $active_plugins)) {
                deactivate_plugins(plugin_basename(__FILE__));
                ?>
                <div id="message" class="error">
                    <p>
                        <?php
                        printf(__('%s %s is inactive.%s Install and activate %sEdwiserBridge%s for %s to work.', 'ebbp-textdomain'), '<strong>', $ebBpPluginData['plugin_name'], '</strong>', '<a href="http://wordpress.org/extend/plugins/edwiser-bridge/">', '</a>', $ebBpPluginData['plugin_name']);
                ?>
                    </p>
                </div>
                <?php
                $flag = 0;
            } elseif (!in_array('woocommerce/woocommerce.php', $active_plugins)) {
                deactivate_plugins(plugin_basename(__FILE__));
                ?>
                <div id="message" class="error">
                    <p>
                        <?php
                        printf(__('%s %s is inactive.%s Install and activate %sWooCommerce%s for %s to work.', 'ebbp-textdomain'), '<strong>', $ebBpPluginData['plugin_name'], '</strong>', '<a href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', $ebBpPluginData['plugin_name']);
                ?>
                    </p>
                </div>
                <?php
                $flag = 0;
            } elseif (!in_array('woocommerce-integration/bridge-woocommerce.php', $active_plugins)) {
                deactivate_plugins(plugin_basename(__FILE__));
                ?>
                <div id="message" class="error">
                    <p>
                        <?php
                        printf(__('%s %s is inactive.%s Install and activate %sWoocommerce Integration%s for %s to work.', 'ebbp-textdomain'), '<strong>', $ebBpPluginData['plugin_name'], '</strong>', '<a href="https://wisdmlabs.com/woocommerce-moodle-integration-solution"/>', '</a>', $ebBpPluginData['plugin_name']);
                ?>
                    </p>
                </div>

                <?php
                $flag = 0;
            }
            return $flag;
        }
    }
}

/**
 * submenu in the settings page.
 */
function migrationSubmenu()
{
    $flag = get_option('ebbp_migration_completion');
    if (!$flag) {
        add_submenu_page(
            'edwiserbridge_lms',
            __('Migration', 'eb-textdomain'),
            __('Migration', 'eb-textdomain'),
            'manage_options',
            'eb-migration',
            'app\wisdmlabs\edwiserBridge\BulkPurchase\migrationContent'
        );
    }
}

function migrationContent()
{
    ?>
<div>
    <h2>Update Bulk Purchase Table </h2>
    <input id="ebbp_migrate" class='ebbp-migrate-button' type="button" name="ebbp_migrate" value="Start Migration">
    <input id="ebbp_delete_migrate" class='ebbp-migrate-button' type="button" name="ebbp_delete_migrate" value="Delete Migration Menu">
    <div class="ebbp_migrate_notices"></div>
</div>
<?php
}

//add_action('admin_init', 'app\wisdmlabs\edwiserBridge\BulkPurchase\redirectOnActivation');

/**
 * This will redirects user to the migration page on plugin activation.
 *
 * @return type
 */
function redirectOnActivation()
{
    if (!get_transient('_ebbp_activation_redirect')) {
        return;
    }
    // Delete transient used for redirection
    delete_transient('_ebbp_activation_redirect');

    // return if activating from network, or bulk
    if (is_network_admin() || isset($_GET['activate-multi'])) {
        return;
    }
    if (!get_option('ebbp_migration_completion')) {
        wp_redirect(admin_url('admin.php?page=eb-migration'));
        exit;
    }
}
