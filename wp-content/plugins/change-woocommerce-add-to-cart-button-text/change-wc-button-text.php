<?php

/*
Plugin Name: Change WooCommerce Add to Cart Text
Plugin URI: https://wordpress.org/plugins/change-woocommerce-add-to-cart-button-text/
Description: This plugin helps you to change the "Add To Cart" button text to anything from a simple settings page available in wp-admin → Settings → Reading
Author: Rextheme
Author URI: http://rextheme.com/ 
Version: 1.0
*/

add_filter( 'woocommerce_product_add_to_cart_text' , 'custom_woocommerce_product_add_to_cart_text' );
add_filter( 'woocommerce_product_single_add_to_cart_text', 'custom_woocommerce_product_add_to_cart_text' );
add_filter( 'woocommerce_booking_single_add_to_cart_text', 'custom_woocommerce_product_add_to_cart_text' );
/**
 * custom_woocommerce_template_loop_add_to_cart
*/
function custom_woocommerce_product_add_to_cart_text() {
	global $product;
	
	$product_type = $product->product_type;

	if (is_product ()) {
		switch ( $product_type ) {
			case 'external':
				return __( $options = get_option( 'external_single_button_text', false ), 'wctext' );
			break;
			case 'grouped':
				return __( $options = get_option( 'grouped_single_button_text', false ), 'wctext' );
			break;
			case 'simple':
				return __( $options = get_option( 'simple_single_button_text', false ), 'wctext' );
			break;
			case 'variable':
				return __( $options = get_option( 'variable_single_button_text', false ), 'wctext' );
			break;
			default:
				return __( 'Read More', 'wctext' );
		}
	}
	else {
		switch ( $product_type ) {
			case 'external':
				return __( $options = get_option( 'external_button_text', false ), 'wctext' );
			break;
			case 'grouped':
				return __( $options = get_option( 'grouped_button_text', false ), 'wctext' );
			break;
			case 'simple':
				return __( $options = get_option( 'simple_button_text', false ), 'wctext' );
			break;
			case 'variable':
				return __( $options = get_option( 'variable_button_text', false ), 'wctext' );
			break;
			default:
				return __( 'Read More', 'wctext' );
		}
	}
	
}

include_once 'wctext-settings.php';

?>
