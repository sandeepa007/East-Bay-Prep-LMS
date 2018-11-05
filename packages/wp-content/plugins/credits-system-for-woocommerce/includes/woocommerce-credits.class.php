<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

class WooCommerce_Credits_Front{
    public static $_instance = null;

    public function __construct(){
        add_action('woocommerce_before_my_account', array( $this, 'before_my_account' ) );
        add_action('woocommerce_payment_complete', array( $this, 'woocommerce_payment_complete' ) );
        add_action('woocommerce_order_status_completed', array( $this, 'woocommerce_payment_complete' ) );
        add_action('woocommerce_order_status_completed', array( $this, 'order_status_completed_add_credits' ) );
        add_action('woocommerce_single_product_summary', array($this, 'single_product_summary'),31);

        add_filter('woocommerce_available_payment_gateways', array( $this, 'available_payment_gateways' ) );
        add_filter('woocommerce_get_price_html', array($this,'get_price_html'), 100, 2 );
        add_filter('woocommerce_cart_total',array($this,'cart_total'),10,1);
        add_filter('woocommerce_cart_subtotal',array($this,'cart_total'),10,1);
        add_filter('woocommerce_cart_item_quantity',array($this,'cart_item_quantity'),10,2);
        add_filter('woocommerce_checkout_cart_item_quantity',array($this,'checkout_cart_item_quantity'),10,3);
        add_filter('woocommerce_order_item_quantity_html',array($this,'order_item_quantity_html'),10,2);
        add_filter('woocommerce_cart_item_subtotal',array($this,'cart_item_subtotal'),10,3);
        add_filter('woocommerce_get_formatted_order_total',array($this,'get_formatted_order_total'),10,2);
        add_filter('woocommerce_order_formatted_line_subtotal',array($this,'order_formatted_line_subtotal'),10,3);
        add_filter('woocommerce_order_subtotal_to_display',array($this,'order_subtotal_to_display'),10,3);
        add_filter('woocommerce_get_item_count',array($this,'get_item_count'),10,3);

        add_action('woocommerce_before_checkout_process',array($this,'before_checkout_process'));
        add_action('woocommerce_thankyou_woocommerce_credits',array($this,'thankyou_woocommerce_credits_note'),10,1);
    }

    public static function instance(){
        if ( is_null( self::$_instance ) )
            self::$_instance = new self();
        return self::$_instance;
    }
    
    public function before_my_account() {
        $user_id =  get_current_user_id();
        $credits = floatval(get_user_meta($user_id, "_remaining_credits", true));
        echo '<h3 style="margin-bottom:0px;">'. __( 'Credits', 'credits-for-woocommerce' ) .'</h3>';
        echo '<p>'. sprintf( __( 'You have <strong>%s</strong> Credits.', 'woocommerce-woocredits' ), $credits ) . '</p>';
    ?>
        <?php 
            $query = new WP_Query(array(
                'post_type' => 'credit-bundle',
                'post_status' => 'publish',
            ));
            if($query->post_count > 0){
                echo "<p><b>Need more credits?</b> Please see below recommended bundles for you.</p>
                <table style='margin-bottom: 10px;'>
                    <thead>
                        <tr>
                            <th class='text-left' style='padding-left: 10px;'>Bundle</th>
                            <th>Credits</th>
                            <th>Price</th>
                            <th style='width:160px;'>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                ";
            }
            foreach($query->posts as $post): setup_postdata($post);
            $post_id = $post->ID;
        ?>
            <tr>
                <td class="text-left" style="padding-left: 10px;"><b><?php echo get_the_title($post_id );?></b></td>
                <td><?php echo get_post_meta($post_id,'credit_number',true);?> Credits</td>
                <td>$<?php echo floatval(get_post_meta($post_id,'credit_price',true));?></td>
                <td style="text-align: center; padding: 3px;"><?php echo do_shortcode('[credit_bundle_link id="'.$post_id.'"]');?></td>
            </tr>
        <?php
            endforeach;
            if($query->post_count > 0){
                echo "
                    </tbody>
                </table>";
            }
            wp_reset_query();
        ?>
    <?php
    }

    public function woocommerce_payment_complete( $order_id ) {
        $order       = wc_get_order( $order_id );
        $customer_id = $order->get_user_id();

        if ( $customer_id && !get_post_meta( $order_id, '_credits_removed', true ) ) {
            if ( $credits = get_post_meta( $order_id, '_credits_used', true ) ) {
                WooCommerce_Credits_Functions::remove_credits( $customer_id, $credits );
                $order->add_order_note( sprintf( __( 'Removed %s credits from user #%d', 'credits-for-woocommerce' ), wc_price( $credits ), $customer_id ) );
            }
            update_post_meta( $order_id, '_credits_removed', 1 );
        }
    }

    public function order_status_completed_add_credits($order_id){
        $order          = wc_get_order( $order_id );
        $items          = $order->get_items();
        $customer_id    = $order->get_user_id();
        foreach ( $items as $item ) {
            $product = $order->get_product_from_item( $item );
            if ( $product &&  get_post_meta($item['product_id'],'_credit_number',true)) {
                $amount = get_post_meta($item['product_id'],'_credit_number',true);
                WooCommerce_Credits_Functions::add_credits( $customer_id, $amount );
                update_post_meta( $order_id, '_credits_added', 1 );
            }
        }
    }
    
    public function available_payment_gateways($available_gateways){
		global $woocommerce;
		$arrayKeys = array_keys($available_gateways);			
        
        if((!WooCommerce_Credits_Functions::purchasable_with_credits() AND !WooCommerce_Credits_Functions::user_has_credit_history()) OR !is_user_logged_in()){
            unset($available_gateways['woocommerce_credits']);
        }
        return $available_gateways;
    }		
    
    public function single_product_summary(){
        global $product;
        $prod_id= ( WC()->version < '2.7.0' ) ? $product->id : $product->get_id();
        $credits_amount = get_post_meta( $prod_id, '_credits_amount', true );
        if($credits_amount && WooCommerce_Credits_Functions::has_credit_amount($prod_id)){
            if(is_user_logged_in()){
                $user_id = get_current_user_id();
                $credits = floatval(get_user_meta($user_id, "_remaining_credits", true));
                echo '<p class="credits-for-woocommerce"> You have '.$credits.' credits remaining <br/><a class="buy-more-credits" href="'.get_permalink(wc_get_page_id( 'myaccount' )).'">Buy More</a> Credits</p>';
            }
            echo "<style>
                #product-".$prod_id." .quantity{
                    display: none !important;
                }
            </style>";
        }
    }
    
    public function get_price_html($price, $product){
        $credits_amount = 0;
        $prod_id= ( WC()->version < '2.7.0' ) ? $product->id : $product->get_id();
        $credits_amount = get_post_meta( $prod_id, '_credits_amount', true );
        if($credits_amount && WooCommerce_Credits_Functions::has_credit_amount($prod_id)){
            $price .= '<span class="amount credit-amount">' . ' (or '.$credits_amount.' credits)</span>';
        }
        return $price;
    }

    public function cart_total($total){
       $total_credits_amount = 0;

        foreach ( WC()->cart->get_cart() as $item ) {
            if ( !$item['data']->is_type( 'credits' ) ) {
                $prod_id = ( isset( $item['product_id'] ) && $item['product_id'] != 0 ) ? $item['product_id'] : false;
                $credits_amount = get_post_meta( $prod_id, '_credits_amount', true );
				if( WooCommerce_Credits_Functions::has_credit_amount($prod_id) AND WooCommerce_Credits_Functions::user_has_credit_history()){   
					$total_credits_amount += $credits_amount;
				 }
            }
        }
        if($total_credits_amount > 0 AND WooCommerce_Credits_Functions::user_has_credit_history()){
            $total .= '&nbsp; (or '.$total_credits_amount.' credits)';
        }
        return $total;
    }

    public function cart_item_quantity($product_quantity, $cart_item_key){
        $cart_item = WC()->cart->cart_contents[$cart_item_key];
           if ( get_post_meta($cart_item['product_id'],'_credit_number',true) ) {
               $credit_number = get_post_meta($cart_item['product_id'],'_credit_number',true);
               $product_quantity = $credit_number. ' credits';
           }else if(WooCommerce_Credits_Functions::has_credit_amount($cart_item['product_id']) AND WooCommerce_Credits_Functions::user_has_credit_history()){
               $product_quantity = 1;
           }
        return $product_quantity;
    }

    public function checkout_cart_item_quantity($product_quantity, $cart_item, $cart_item_key){
        $cart_item = WC()->cart->cart_contents[$cart_item_key];
           if ( get_post_meta($cart_item['product_id'],'_credit_number',true)) {
               $credit_number = get_post_meta($cart_item['product_id'],'_credit_number',true);
               $product_quantity = ' <strong class="product-quantity credit-quantity"> x ' . $credit_number. ' credits' . '</strong>' ;
           }
        return $product_quantity;
    }

    public function order_item_quantity_html($product_quantity, $item){
        $product_id = $item['product_id'];
        $product = wc_get_product( $product_id );
           if (get_post_meta($product_id,'_credit_number',true)) {
               $credit_number = get_post_meta($product_id,'_credit_number',true);
               $product_quantity = ' <strong class="product-quantity"> x ' . $credit_number. ' credits' . '</strong>' ;
           }
        return $product_quantity;
    }

    public function cart_item_subtotal($sub_total, $cart_item, $cart_item_key){
        $prod_id = ( isset( $cart_item['product_id'] ) && $cart_item['product_id'] != 0 ) ? $cart_item['product_id'] : false;
        
        if(WooCommerce_Credits_Functions::has_credit_amount($prod_id) AND WooCommerce_Credits_Functions::user_has_credit_history()){
            $credits_amount = get_post_meta( $prod_id, '_credits_amount', true );
            if($credits_amount){
                $sub_total .= ' &nbsp; (or '.$credits_amount.' credits)';
            }
        }
        return $sub_total;
    }

    public function get_formatted_order_total($formatted_total, $order){
    	$order_id= ( WC()->version < '2.7.0' ) ? $order->id : $order->get_id(); 
        if($order->payment_method == 'woocommerce_credits'){
            $credits_used = WooCommerce_Credits_Functions::order_get_total_used_credits($order_id);
            $formatted_total = $credits_used.' credits';
        }
        return $formatted_total;
    }

    public function order_formatted_line_subtotal($subtotal, $item, $order){
        $order_id= ( WC()->version < '2.7.0' ) ? $order->id : $order->get_id(); 
        $prod_id = ( isset( $item['product_id'] ) && $item['product_id'] != 0 ) ? $item['product_id'] : false;
        $credits_amount = get_post_meta( $prod_id, '_credits_amount', true );
        if($order->payment_method == 'woocommerce_credits'){
            $subtotal = $credits_amount.' credits';
        }
        return $subtotal;
    }

    public function order_subtotal_to_display($subtotal, $compound, $order){
    	$order_id= ( WC()->version < '2.7.0' ) ? $order->id : $order->get_id(); 
        if($order->payment_method == 'woocommerce_credits'){
            $credits_used = WooCommerce_Credits_Functions::order_get_total_used_credits($order_id);
            $subtotal = $credits_used.' credits';
        }
        return $subtotal;
    }

    public function get_item_count($count, $type, $order){
    	$order_id = ( WC()->version < '2.7.0' ) ? $order->id : $order->get_id(); 
            if ( get_post_meta($order_id,'_credits_removed',true) ) {
                  $credit_number = get_post_meta($product_id,'_credit_number',true);
                  $credit_number = $credit_number*$count;
                $count = '&nbsp; '.$credit_number.' credit';
            }
        return $count;
    }
    
    public function before_checkout_process(){
          if(!WooCommerce_Credits_Functions::cart_contains_credits() && WooCommerce_Credits_Functions::cart_contains_credits()){
            wc_add_notice( __('<strong>WooCommerce Credits error:</strong>', 'credits-for-woocommerce') . ' While buying credits, you cannot buy non-credit products.', 'error' );
            return;
          }
    }
    
    public function thankyou_woocommerce_credits_note($order_id){
        $order = wc_get_order($order_id);
        $user_id =  $order->get_user_id();
        if ($order->payment_method == 'woocommerce_credits') {
            $credits = floatval(get_user_meta($user_id, "_remaining_credits", true));
            ?>
                <ul class="order_details credits_remaining">
                    <li class="method">
                        <?php _e( 'Credits Remaining:', 'credits-for-woocommerce' ); ?>
                        <strong><?php echo $credits; ?></strong>
                    </li>
                </ul>
           <?php
        }
    }

}
WooCommerce_Credits_Front::instance();
?>