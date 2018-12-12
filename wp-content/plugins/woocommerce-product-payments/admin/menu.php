<?php
if( !defined('ABSPATH') ){ exit();}


function wppg_admin_scripts()
{
	wp_enqueue_script('jquery');
}

function wppg_menu()
{
	add_menu_page('Woocommerce Product Payment - Manage settings', 'Woocommerce Product Payment', 'manage_options', 'woocommerce-product-payment-gateway-settings', 'wppg_settings');
	
}


function wppg_settings()
{
	$_POST = stripslashes_deep($_POST);
	$_GET = stripslashes_deep($_GET);	
	
	
	require( dirname( __FILE__ ) . '/header.php' );
	require( dirname( __FILE__ ) . '/settings.php' );
	require( dirname( __FILE__ ) . '/footer.php' );
}
