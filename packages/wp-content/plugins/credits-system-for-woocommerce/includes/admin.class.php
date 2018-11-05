<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

class WooCommerce_Credits_Admin{
    public static $_instance = null;
    
    public function __construct(){
        add_action('init',array($this,'init_credit_bundle'));
        add_action('add_meta_boxes',array($this,'credit_bundle_configuration'));
        add_action('add_meta_boxes',array($this,'donate_meta_box'));
        add_action('manage_credit-bundle_posts_custom_column',array($this,'credit_bundle_column_content'),10,2);
        add_action('publish_webcasts',array($this,'create_bundle'),10,2);
        add_action('save_post',array($this,'save_credit_bundle'));

        add_filter('manage_edit-credit-bundle_columns',array($this,'credit_bundle_columns'));
        add_shortcode('credit_bundle_link',array($this,'shortcode_bundle_link'));
        add_shortcode('remaining_credits',array($this,'shortcode_remaining_credits'));

        add_action( 'parse_query', array($this,'hide_credit_products'));
        add_action( 'woocommerce_product_options_general_product_data', array( $this,'add_custom_general_fields') );
        add_action( 'woocommerce_process_product_meta', array( $this,'add_custom_general_fields_save') );

        add_filter('woocommerce_payment_gateways', array($this,'woocommerce_credit_gateway'));
        add_filter('woocommerce_product_class', array( $this, 'woocommerce_product_class_for_credits' ), 10, 4 );
    }
    
    public static function instance(){
        if ( is_null( self::$_instance ) )
            self::$_instance = new self();
        return self::$_instance;
    }

    public function init_credit_bundle(){
        register_post_type('credit-bundle',array(
            'labels' => array(
    			'menu_name'	          =>	'Credits Bundle',
    			'singular_name'       =>	'Credits Bundle',
    		 	'edit_item'           =>	'Edit Bundle',
    		 	'new_item'            =>	'New Bundle',
    		 	'view_item'           =>	'View Bundle',
    		 	'items_archive'       =>	'Bundle Archive',
    		 	'search_items'        =>	'Search Bundle',
    		 	'not_found'	          =>	'No bundles found',
    		 	'not_found_in_trash'  =>	'No bundles found in trash'
            ),
            'public'      => true,
            'has_archive' => true,
            'show_in_menu' => 'woocommerce',
        ));
        wp_enqueue_style('credit-bundle',plugins_url("../assets/css/style.css", __FILE__ ));
    }
    
    public function credit_bundle_columns(){
        $columns = array(
    		'cb' => '<input type="checkbox" />',
    		'title' => __( 'Bundle' ),
    		'credits_amount' => __( 'Credits Amount' ),
    		'price' => __( 'Price' ),
    		'link_shortcode' => __( 'Link/Shortcode' ),
    		'date' => __( 'Date' )
    	);
    	return $columns;
    }

    public function credit_bundle_column_content($column,$post_id){
        global $post;
        $product_id = WooCommerce_Credits_Functions::get_post_id_by_meta_key_and_value('post_id',$post_id);
        switch($column){
            case 'credits_amount':
                echo get_post_meta($product_id,'_credit_number',true). " Credits";
                break;
            case 'price':
                echo "$".get_post_meta($product_id,'_credit_price',true);
                break;
            case 'link_shortcode':
                echo "<p>[credit_bundle_link id='".$product_id."' text='Add to Cart' redirect='cart']</p>
                <p><a href='".site_url('/cart/?add-to-cart='.$product_id)."'>".site_url('/cart/?add-to-cart='.$product_id)."</a></p>
                ";
                break;
            default:
                break;
        }
    }
    
    public function credit_bundle_configuration(){
    	add_meta_box('credit_bundle_fields','Bundle Configuration',array($this,'credit_bundle_fields'),'credit-bundle', 'normal','high');
    }

    public function credit_bundle_fields(){
          global $post;
          $custom = get_post_custom($post->ID);
          ?>
          <div class="field-row">
            <div class="col-2">
                <p><label><b>Credits:</b></label></p>
                <input type="number" name="credit_number" required="required" style="width: 100%;" value="<?php echo $custom["credit_number"][0]; ?>"/></p>
            </div>
            <div class="col-2">
                <p><label><b>Bundle Price:</b></label></p>
                <input type="number" name="credit_price" required="required" style="width: 100%;" value="<?php echo $custom["credit_price"][0]; ?>"/></p>
            </div>
          </div>
          <style>
            #wpseo_meta{
                display:none;
            }
            .field-row .col-2{
                width: 48%;
                display: inline-block;
                margin-right: 10px;
            }            
          </style>
    <?php
    }
    public function save_credit_bundle($post_id){
        global $post;
        if('credit-bundle' == $post->post_type){
            update_post_meta($post->ID, "credit_number", $_POST["credit_number"]);
            update_post_meta($post->ID, "credit_price", $_POST["credit_price"]);
            $bundle_id = WooCommerce_Credits_Functions::get_post_id_by_meta_key_and_value('post_id',$post->ID);
            $bundle = array(
                'post_type' => 'product',
                'post_status' => 'publish',
                'post_content' => '',
                'post_title' => $post->post_title,
                'meta_input' => array(
                    '_credit_bundle_product' => 1,
                    '_sold_individually' => 'yes',
                    '_credit_name' => $post->post_title,
                    '_credit_number' => $_POST["credit_number"],
                    '_credit_price' => $_POST["credit_price"],
                    '_downloadable' => 'yes',
                    '_virtual' => 'yes',
                    '_price' => $_POST["credit_price"],
                    'post_id' => $post->ID,
                    '_visibility' => 'hidden',
                ),
            );
            if(!empty($bundle_id)){
                $bundle['ID'] = $bundle_id;
            }
            remove_action('save_post',array($this,'save_credit_bundle'));
            $post_id = wp_insert_post($bundle);
            wp_set_object_terms( $post_id, 'credits', 'product_type');
            add_action('save_post',array($this,'save_credit_bundle'));
        }
    }
    
    public function shortcode_bundle_link($atts){
        $val = shortcode_atts(array(
            'id' => '',
            'text' => 'Add to Cart',
            'redirect' => 'cart',
        ),$atts);
        $product_id = WooCommerce_Credits_Functions::get_post_id_by_meta_key_and_value('post_id',$val['id']);
        $link = ($val['redirect'] == 'cart') ? site_url('/cart/?add-to-cart='.$product_id) : site_url('/checkout/?add-to-cart='.$product_id);
        return "<a href='".$link."' class='credit-bundle-link'>".$val['text']."</a>";
    }
    
    public function shortcode_remaining_credits(){
        $credits = (get_user_meta(get_current_user_id(), "_remaining_credits", true)) ? floatval(get_user_meta(get_current_user_id(), "_remaining_credits", true)) : 0;
        return $credits;
    }

    public function woocommerce_credit_gateway( $gateways ) {
        $gateways[] = 'WooCommerce_Credits_Gateway';
        return $gateways;
    }

    public function woocommerce_product_class_for_credits( $classname, $product_type, $post_type, $product_id ) {
        if ( 'product'  === get_post_type( $product_id ) && WooCommerce_Credits_Functions::is_simple_credit_product($product_id)){
            return 'WooCommerce_Credits_Product_Type';
        }
        return $classname;
    }
    
    public function add_custom_general_fields(){
        global $woocommerce, $post;
        $product_id = $post->ID;
        $_product = wc_get_product( $product_id );
         if($_product->get_type() == 'simple'):
            echo '<div class="options_group woocommerce-credit-price">';
            woocommerce_wp_text_input(
                array(
                    'id' => '_credits_amount',
                    'label' => __('Credits Price ', 'credits-for-woocommerce'),
                    'placeholder' => '',
                    'desc_tip' => 'true',
                    'description' => __('The credits price for this product.', 'credits-for-woocommerce'),
                    'type' => 'text',
                )
            );
			echo '</div>';
		 endif;
    }
    public function add_custom_general_fields_save ( $post_id ){
		$woocommerce_credits_amount = $_POST['_credits_amount'];
		$_product = wc_get_product( $post_id );
        if(!empty( $woocommerce_credits_amount) && $_product->get_type() == 'simple'  ){
            update_post_meta( $post_id, '_credits_amount', esc_attr( $woocommerce_credits_amount ) );
        }else{
            delete_post_meta( $post_id, '_credits_amount');
        }
    }

    public function hide_credit_products($query){
        if ( ! is_admin() || !$query->is_main_query()){
            return $query;
        }
        global $pagenow, $post_type;
        if($pagenow == 'edit.php' && $post_type == 'product' ){
            $bundles = self::get_credit_bundle_products();
            if($bundles){
                $credit_ids = wp_list_pluck( $bundles, 'ID' );
                $query->query_vars['post__not_in'] = $credit_ids;
            }
        }
        return $query;
    }

    public function get_credit_bundle_products(){
      $posts = get_posts(array('post_type' => 'product','posts_per_page'=> -1,'post_status'   => 'publish','orderby' => 'date', 'order'  => 'ASC','meta_key' => '_credit_bundle_product','meta_value' => 1,));
      if($posts){
        return $posts;
      }
      return false;
    }

    public function donate_meta_box(){
    	add_meta_box('credit_bundle_donate','Donate for WooCommerce Credits',array($this,'credit_bundle_donate'),'credit-bundle', 'side','high');
    }
    
    public function credit_bundle_donate(){
?>
      <div class="credits-for-woocommerce donate">
        <a href="https://www.paypal.me/joemarasiado" style="display: block; width: 100%; text-align:center; margin-top: 20px;"><img src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" style="display-inline-block;margin-bottom: 10px;"/></a>
        <p>Any amount will be appreciated, support the developer to keep the development of the plugin and to keep the plugin offered for free.</p>
      </div>
      <style>
        #credit_bundle_donate .hndle.ui-sortable-handle{
            background-color: #0073aa !important;
        }
        #credit_bundle_donate .hndle.ui-sortable-handle span{
            color: #fff !important;
        }
        #credit_bundle_donate .toggle-indicator{
            color: #fff !important;
        }
      </style>
<?php
    }
}
WooCommerce_Credits_Admin::instance();
?>