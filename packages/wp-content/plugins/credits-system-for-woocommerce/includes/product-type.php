<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'plugins_loaded', 'woocomerce_credits_product_type', 10 );

function woocomerce_credits_product_type(){
    if( class_exists('WC_Product') ){
        class WooCommerce_Credits_Product_Type extends WC_Product {
            
            public $virtual = 'yes';
            public $downloadable = 'yes';
            public $sold_individually = 'yes';
            public $product_type = 'credits';
            protected $post_type = 'product';
            
            public function __construct( $product ) {
                parent::__construct( $product );
                $this->product_type = 'credits';
            }
            
            public function exists() {
                return true;
            }
            
            public function is_purchasable() {
                return true;
            }
            
            public function get_title() {
                $credit_name = get_post_meta($this->get_id() , '_credit_name',true);
                return $credit_name;
            }
            public function get_type() {
                return isset( $this->product_type ) ? $this->product_type : 'credits';
            }
            
            public function is_visible() {
                return false;
            }
            public function set_product_visibility($opt) {
                if(method_exists($this,'set_catalog_visibility')){
                    $this->set_catalog_visibility($opt);
                }else{
                    update_post_meta ( $this->get_id(), '_visibility', "hidden" );
                }
            }
        }
    }
}

?>