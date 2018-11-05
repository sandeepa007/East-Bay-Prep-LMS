<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'plugins_loaded', 'woocommerce_credits_payment_gateway', 10 );
function woocommerce_credits_payment_gateway(){
    class WooCommerce_Credits_Gateway extends WC_Payment_Gateway{
        public function __construct() {
            $this->id                 = 'woocommerce_credits';
            $this->icon               = apply_filters( 'woocommerce_cod_icon', '' );
            $this->method_title       = __( 'Credits', 'woocommerce' );
            $this->method_description = __( 'Let customers pay with their Credits balance for credits enabled products.', 'credits-for-woocommerce' );
            $this->has_fields         = false;
            $this->init_form_fields();
            $this->init_settings();
            $this->title              = $this->get_option( 'title' );
            $this->description        = $this->get_option( 'description' );
        }
        
        public function init_form_fields() {
            $this->form_fields = array(
                'enabled' => array(
                    'title'       => __( 'Enable Credits', 'credits-for-woocommerce' ),
                    'label'       => __( 'Enable Credits', 'credits-for-woocommerce' ),
                    'type'        => 'checkbox',
                    'description' => '',
                    'default'     => 'yes'
                ),
                'title' => array(
                    'title'       => __( 'Title', 'credits-for-woocommerce' ),
                    'type'        => 'text',
                    'description' => __( 'Payment method description that the customer will see on your checkout.', 'credits-for-woocommerce' ),
                    'default'     => __( 'Credits', 'credits-for-woocommerce' ),
                    'desc_tip'    => true,
                ),
                'description' => array(
                    'title'       => __( 'Description', 'credits-for-woocommerce' ),
                    'type'        => 'textarea',
                    'description' => __( 'Payment method description that the customer will see on your website.', 'credits-for-woocommerce' ),
                    'default'     => __( 'Pay using your remaining credits.', 'credits-for-woocommerce' ),
                    'desc_tip'    => true,
                ),
            );
        }
        
        public function process_payment( $order_id ){
            if(WooCommerce_Credits_Functions::remaining_credits() < 0.1){
                wc_add_notice( __('<strong>Payment error:</strong>', 'credits-for-woocommerce') . " You don't have enough credits on your account, you only have ".WooCommerce_Credits_Functions::remaining_credits()." credits remaining. Please buy more credits or choose a different payment method.", 'error' );
                return;
            }
            $order = wc_get_order( $order_id );
            $user_id= ( WC()->version < '2.7.0' ) ? $order->user_id : $order->get_user_id();
            $items = $order->get_items();
            $total_credits_amount = 0;
            
            foreach ( $items as $item ) {
                $product = $order->get_product_from_item( $item );
                $prod_id= ( WC()->version < '2.7.0' ) ? $product->id : $product->get_id();
                $credits_amount = get_post_meta( $prod_id, '_credits_amount', true );
                if($credits_amount){
                    $total_credits_amount += $credits_amount;
                }else{
                    wc_add_notice( __('<strong>Payment error:</strong>', 'credits-for-woocommerce') . ' Your cart contains a non-credit purchasable product. Please choose another payment method.', 'error' );
                    return;
                }
            }
            $remaining_credits = floatval(WooCommerce_Credits_Functions::remaining_credits());
            $cart_total = floatval(WC()->cart->total);
            

            if ($total_credits_amount > $remaining_credits){
                $needed_credits = $total_credits_amount - $remaining_credits;
                wc_add_notice( __('<strong>Payment error:</strong>', 'credits-for-woocommerce') . " Insuficient Credits, you only have <b>".floatval(WooCommerce_Credits_Functions::remaining_credits())."</b> credits remaining you need <b>".floatval($needed_credits)."</b> more credits. Please buy more credits or choose a different payment method.", 'error' );
                return;
            }
            
            $new_user_remaining_credits = $remaining_credits - $total_credits_amount;
            WooCommerce_Credits_Functions::remove_credits( $user_id, $total_credits_amount );
            
            if (WooCommerce_Credits_Functions::remaining_credits() != $new_user_remaining_credits){
                wc_add_notice( __('<strong>System error:</strong>', 'credits-for-woocommerce') . ' There was an error procesing the payment. Please try another payment method.', 'error' );
                return;
            }

            $order->update_status( 'completed', __( 'Payment completed using account credits', 'credits-for-woocommerce' ) );
            
            if(WC()->version < '2.7.0'){
                $order->reduce_order_stock();
            }else{
                wc_reduce_stock_levels( $order->get_id() );
            }
            
            WC()->cart->empty_cart();
            return array(
                'result' 	=> 'success',
                'redirect'	=> $this->get_return_url($order)
            );
        }
        
        public function get_icon(){
            $link = null;
            global $woocommerce;
            $remaining_credits = (get_user_meta(get_current_user_id(), '_remaining_credits', true)) ? get_user_meta(get_current_user_id(), '_remaining_credits', true) : 0 ;
            return apply_filters( 'woocommerce_gateway_icon', ' | Your Current Balance: <strong>'.$remaining_credits.' </strong> | <a class="buy-more-credits" href="'.get_permalink(wc_get_page_id( 'myaccount' )).'">Buy More</a> Credits', $this->id );
        }
    }
}
?>