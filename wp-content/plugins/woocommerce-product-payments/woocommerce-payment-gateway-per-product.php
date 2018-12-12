<?php
/**
 * Plugin Name: Woocommerce Payment Gateway Per Product
 * Plugin URI: https://www.dreamfoxmedia.com/shop/plugins/woocommerce-payment-gateway-per-product-premium/
 * Version: 2.5.5
 * Author: Dreamfox Media
 * Author URI: www.dreamfoxmedia.com 
 * Description: Extend Woocommerce plugin to add payments methods to a product
 * Requires at least: 4.0
 * Tested up to: 4.9.8
 * WC requires at least: 3.0.0
 * WC tested up to: 3.4.5
 * Text Domain: softdev
 * Domain Path: /languages
 * @Developer : Anand Rathi / Marco van Loghum Slaterus
 */
/**
 * For multi Network
 */
if (!function_exists('is_plugin_active_for_network')) {
    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
}

define('WPPG_PLUGIN_FILE',__FILE__);

$include_files['include'] = array('settings', 'payment', 'notification', 'footer', 'install', 'loader');
$include_files['admin'] = array('install', 'menu');
foreach($include_files as $dir => $files) {
    foreach ($files as $file) {
        $file = dirname( __FILE__ ). "/{$dir}/{$file}.php";
        require_once $file;
    }    
}

function run_wppg_installer() {
    $spmm = new Woocommerce_Product_Payment_Install(__FILE__);
    $spmm->run();
}
run_wppg_installer();