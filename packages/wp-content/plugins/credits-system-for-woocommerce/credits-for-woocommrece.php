<?php if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * Plugin Name:  Credits System for WooCommerce
 * Description:  Create credit bundles and let your members purchase products using their account credits.
 * Version:      1.0
 * Author:       Joemar Asiado
 * Author URI:   http://joemarnudoasiado.890m.com/
 * License:      GPL2
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  credits-for-woocommerce
 * Domain Path:  /languages
 * Requires at least: 4.4
 * Tested up to: 4.9
*/

if (! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) return;

include_once dirname( __FILE__ ) . '/includes/functions.class.php';
include_once dirname( __FILE__ ) . '/includes/payment-gateway.php';
include_once dirname( __FILE__ ) . '/includes/product-type.php';
include_once dirname( __FILE__ ) . '/includes/admin.class.php';
include_once dirname( __FILE__ ) . '/includes/woocommerce-credits.class.php';

function donate_link( $links, $file ) {
    if ( plugin_basename( __FILE__ ) == $file ) {
        $row_meta = array(
          'donate'    => '<a href="' . esc_url( 'https://www.paypal.me/joemarasiado' ) . '" target="_blank" aria-label="' . esc_attr__( 'Plugin Additional Links', 'woocommerce-credits' ) . '" style="color:#fff; padding:3px 10px; background-color: #12128a; border-radius: 3px;">' . esc_html__( 'Donate', 'woocommerce-credits' ) . '</a>'
        );
        return array_merge( $links, $row_meta );
    }
    return (array) $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'donate_link',10,2 );
?>