<?php 

add_action( 'wp_enqueue_scripts', 'salient_child_enqueue_styles');
function salient_child_enqueue_styles() {
	
		$nectar_theme_version = nectar_get_theme_version();
		
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css', array('font-awesome'), $nectar_theme_version);

    if ( is_rtl() ) 
   		wp_enqueue_style(  'salient-rtl',  get_template_directory_uri(). '/rtl.css', array(), '1', 'screen' );
}

add_action( 'woocommerce_order_status_completed', 'wc_add_credits_to_wallet' );
function wc_add_credits_to_wallet( $order_id ) {
//from $order you can get all the item information etc 
//above is just a simple example how it works
//your code to send data
	//error_reporting(E_ALL);
	$order = wc_get_order( $order_id ); 
	$pmethod = $order->get_payment_method();
	if($pmethod != 'wpuw')
	{
		$transfer = wpuw_add_credits_to_user_account_custom($order->get_user_id(), $order->get_total());
	}else{
		//$transfer = wpuw_remove_credits_to_user_account_custom($order->get_user_id(), $order->get_total());
	}
	
	return true;	
	
	//exit;
}

function wpuw_add_credits_to_user_account_custom( $user_id, $cc ) {
			$credit_amount                = $cc;
			$current_users_wallet_balance = floatval( get_user_meta( $user_id, "_uw_balance", true ) );
			update_user_meta( $user_id, "_uw_balance", ( $credit_amount + $current_users_wallet_balance ) );
//			do_action( 'uwcs_wallet_adjustment', $user_id, $current_users_wallet_balance, $credit_amount );
}


function misha_remove_default_gateway( $load_gateways){
	$productIds = get_option('woocommerce_product_apply', array());
	if (is_array($productIds)) {
        foreach ($productIds as $key => $product) {
            if (!get_post($product) || !count(get_post_meta($product, 'sd_payments', true))) {
                unset($productIds[$key]);
            }
        }
    }
	$var_diable = false;
	
	if(WC()->cart)
	{
		foreach( WC()->cart->get_cart() as $cart_item ){
	    $product_id = $cart_item['product_id'];
	    if(in_array($product_id, $productIds))
		    {
		    	$var_diable = true;
		    }
		}
		unset( $load_gateways[0] ); // WC_Gateway_BACS
		unset( $load_gateways[1] ); // WC_Gateway_Cheque
		unset( $load_gateways[2] ); // WC_Gateway_COD (Cash on Delivery)
		//unset( $load_gateways[3] ); // WC_Gateway_Paypal
		if($var_diable)
		{
			unset( $load_gateways[4] ); // WC_Gateway_COD (Cash on Delivery)
		}else{
			unset( $load_gateways[3] ); // WC_Gateway_COD (Cash on Delivery)
		}	
	}
	
	return $load_gateways;
}
 
add_filter( 'woocommerce_payment_gateways', 'misha_remove_default_gateway', 100, 1);

// Note the low hook priority, this should give to your other plugins the time to add their own items...
add_filter( 'woocommerce_account_menu_items', 'add_my_menu_items', 99, 1 );

function add_my_menu_items( $items ) {
    $my_items = array(
    //  endpoint   => label
        '/?eb-active-link=eb-my-courses' => __( 'My Course' ),
        '/class-schedule/' => __( 'Class Schedule'),
    );

    $my_items = array_slice( $items, 0, 1, true ) +
        $my_items +
        array_slice( $items, 1, count( $items ), true );

    return $my_items;
}

add_filter( 'woocommerce_get_endpoint_url', 'misha_hook_endpoint', 10, 4 );
function misha_hook_endpoint( $url, $endpoint, $value, $permalink ){
 
	if( $endpoint === '/?eb-active-link=eb-my-courses' ) {
 
		// ok, here is the place for your custom URL, it could be external
		$url = site_url().'/user-account/?eb-active-link=eb-my-courses';
 
	}
	if( $endpoint === '/class-schedule/' ) {
 
		// ok, here is the place for your custom URL, it could be external
		$url = site_url().'/class-schedule/';
 
	}
	return $url;
 
}

function filter_plugin_updates( $value ) {
    unset( $value->response['edwiser-bridge/edwiser-bridge.php'] );
    return $value;
}
add_filter( 'site_transient_update_plugins', 'filter_plugin_updates' );
?>