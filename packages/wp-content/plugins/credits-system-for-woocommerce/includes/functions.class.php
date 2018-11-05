<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

class WooCommerce_Credits_Functions{
    public static function get_post_id_by_meta_key_and_value($key, $value) {
		global $wpdb;
		$meta = $wpdb->get_results("SELECT * FROM `".$wpdb->postmeta."` WHERE meta_key='".$wpdb->escape($key)."' AND meta_value='".$wpdb->escape($value)."'");
		if (is_array($meta) && !empty($meta) && isset($meta[0])) {
			$meta = $meta[0];
		}		
		if (is_object($meta)) {
			return $meta->post_id;
		}
		else {
			return false;
		}
	}

    public static function cart_contains_credits() {
        foreach ( WC()->cart->get_cart() as $item ) {
            if ( $item['data']->is_type( 'credits' ) ) {
                return true;
            }
        }
        return false;
    }

    public static function is_simple_credit_product($product_id){
        if(get_post_meta( $product_id, '_credit_bundle_product', true )){
            return true;
        }else{
            return false;
        }
    }

    public static function has_credit_amount($product_id){
        if(get_post_meta( $product_id, '_credits_amount', true )){
            return true;
        }else{
            return false;
        }
    }

    public static function purchasable_with_credits(){
    	 if(WC()->cart){
            $cart = WC()->cart->get_cart();
    		foreach ( $cart as $cart_item_key => $cart_item ) {
    			$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
                $credits_amount = get_post_meta( $product_id, '_credits_amount', true );
                if(!$credits_amount){
                    return false;
                }
            }
        }
        return true;
    }
    
    public static function user_has_credit_history($user_id=null){
        $user_id = $user_id ? $user_id : get_current_user_id();
        if(get_user_meta($user_id, "_remaining_credits", true)){
            return true;
        }else{
            false;
        }
    }
    
    public static function remaining_credits($user_id=null){
        $user_id = $user_id ? $user_id : get_current_user_id();
        if($user_id){
            $credits = floatval(get_user_meta($user_id, "_remaining_credits", true));
            return $credits;
        }else{
            return 0;
        }
    }

    public static function add_credits( $customer_id, $amount ) {
        $credits = floatval(get_user_meta($customer_id, "_remaining_credits", true));
        $credits = $credits ? $credits : 0;
        $credits += floatval( $amount );
        update_user_meta( $customer_id, '_remaining_credits', $credits );
    }

    public static function remove_credits( $customer_id, $amount ) {
        $credits = floatval(get_user_meta($customer_id, "_remaining_credits", true));
        $credits = $credits ? $credits : 0;
        $credits = $credits - floatval( $amount );
        update_user_meta( $customer_id, '_remaining_credits', max( 0, $credits ) );
    }

    public static function order_get_total_used_credits( $order_id ) {
        $order           = wc_get_order( $order_id );
        $credits_used = 0;
        foreach ( $order->get_items() as $item ) {
            $product = $order->get_product_from_item( $item );
            $prod_id= ( WC()->version < '2.7.0' ) ? $product->id : $product->get_id();
            if ( ($credits_amount = get_post_meta( $prod_id, '_credits_amount', true ))) {
                $credits_used += $credits_amount;
            }
        }
        return $credits_used;
	}
}
$GLOBALS['WooCommerce_Credits_Functions'] = new WooCommerce_Credits_Functions();
?>