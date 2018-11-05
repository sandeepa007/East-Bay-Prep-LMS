<?php
/**
* Plugin Name: Woocommerce Events Manager
* Plugin URI: http://mage-people.com
* Description: A Complete Event Solution for WordPress by MagePeople..
* Version: 2.1.4
* Author: MagePeople Team
* Author URI: http://www.mage-people.com/
* Text Domain: mage-eventpress
* Domain Path: /languages/
*/


if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.
require_once(dirname(__FILE__) . "/inc/class/mep_settings_api.php");
require_once(dirname(__FILE__) . "/inc/mep_cpt.php");
require_once(dirname(__FILE__) . "/inc/mep_tax.php");
require_once(dirname(__FILE__) . "/inc/mep_event_meta.php");
require_once(dirname(__FILE__) . "/inc/mep_extra_price.php");
require_once(dirname(__FILE__) . "/inc/mep_shortcode.php");
require_once(dirname(__FILE__) . "/inc/admin_setting_panel.php");
require_once(dirname(__FILE__) . "/inc/mep_enque.php");
require_once(dirname(__FILE__) . "/templates/template-prts/templating.php");
require_once(dirname(__FILE__) . "/lib/PHPExcel.php");
require_once(dirname(__FILE__) . "/inc/mep_csv_export.php");
require_once(dirname(__FILE__) . "/inc/mep_user_custom_style.php");

// Language Load
add_action( 'init', 'mep_language_load');
function mep_language_load(){
    $plugin_dir = basename(dirname(__FILE__))."/languages/";
    load_plugin_textdomain( 'mage-eventpress', false, $plugin_dir );
}

// Class for Linking with Woocommerce with Event Pricing 
add_action('plugins_loaded', 'mep_load_wc_class');
function mep_load_wc_class() {

  if ( class_exists('WC_Product_Data_Store_CPT') ) {

   class MEP_Product_Data_Store_CPT extends WC_Product_Data_Store_CPT {

    public function read( &$product ) {

        $product->set_defaults();

        if ( ! $product->get_id() || ! ( $post_object = get_post( $product->get_id() ) ) || ! in_array( $post_object->post_type, array( 'mep_events', 'product' ) ) ) { // change birds with your post type
            throw new Exception( __( 'Invalid product.', 'woocommerce' ) );
        }

        $id = $product->get_id();

        $product->set_props( array(
            'name'              => $post_object->post_title,
            'slug'              => $post_object->post_name,
            'date_created'      => 0 < $post_object->post_date_gmt ? wc_string_to_timestamp( $post_object->post_date_gmt ) : null,
            'date_modified'     => 0 < $post_object->post_modified_gmt ? wc_string_to_timestamp( $post_object->post_modified_gmt ) : null,
            'status'            => $post_object->post_status,
            'description'       => $post_object->post_content,
            'short_description' => $post_object->post_excerpt,
            'parent_id'         => $post_object->post_parent,
            'menu_order'        => $post_object->menu_order,
            'reviews_allowed'   => 'open' === $post_object->comment_status,
        ) );

        $this->read_attributes( $product );
        $this->read_downloads( $product );
        $this->read_visibility( $product );
        $this->read_product_data( $product );
        $this->read_extra_data( $product );
        $product->set_object_read( true );
    }

    /**
     * Get the product type based on product ID.
     *
     * @since 3.0.0
     * @param int $product_id
     * @return bool|string
     */
    public function get_product_type( $product_id ) {
        $post_type = get_post_type( $product_id );
        if ( 'product_variation' === $post_type ) {
            return 'variation';
        } elseif ( in_array( $post_type, array( 'mep_events', 'product' ) ) ) { // change birds with your post type
            $terms = get_the_terms( $product_id, 'product_type' );
            return ! empty( $terms ) ? sanitize_title( current( $terms )->name ) : 'simple';
        } else {
            return false;
        }
    }
}



function mep_get_order_info($info,$id){
  $stock_msg  = $info;
  $koba = explode("_", $stock_msg);
  return $koba[$id];
}



add_filter( 'woocommerce_data_stores', 'mep_woocommerce_data_stores' );
function mep_woocommerce_data_stores ( $stores ) {     
      $stores['product'] = 'MEP_Product_Data_Store_CPT';
      return $stores;
  }

  } else {

    add_action('admin_notices', 'wc_not_loaded');

  }



add_action('woocommerce_before_checkout_form', 'mep_displays_cart_products_feature_image');

function mep_displays_cart_products_feature_image() {
    foreach ( WC()->cart->get_cart() as $cart_item ) {
        $item = $cart_item['data'];
    }
}


// Send Confirmation email to customer
function mep_event_confirmation_email_sent($event_id,$sent_email){
$values = get_post_custom($event_id);

$global_email_text = mep_get_option( 'mep_confirmation_email_text', 'email_setting_sec', '');
$global_email_form_email = mep_get_option( 'mep_email_form_email', 'email_setting_sec', '');
$global_email_form = mep_get_option( 'mep_email_form_name', 'email_setting_sec', '');
$global_email_sub = mep_get_option( 'mep_email_subject', 'email_setting_sec', '');
$event_email_text = $values['mep_event_cc_email_text'][0];
$admin_email = get_option( 'admin_email' );
$site_name = get_option( 'blogname' );


  if($global_email_sub){
    $email_sub = $global_email_sub;
  }else{
    $email_sub = 'Confirmation Email';
  }

  if($global_email_form){
    $form_name = $global_email_form;
  }else{
    $form_name = $site_name;
  }

  if($global_email_form_email){
    $form_email = $global_email_form_email;
  }else{
    $form_email = $admin_email;
  }

  if($event_email_text){
    $email_body = $event_email_text;
  }else{
    $email_body = $global_email_text;
  }

  $headers[] = "From: $form_name <$form_email>";

  if($email_body){
  $sent = wp_mail( $sent_email, $email_sub, $email_body, $headers );
  }
}





// Get user information and save to attendee list after order confirmation
add_action( 'woocommerce_order_status_completed_notification', 'mep_set_event_attendee_data' );
function mep_set_event_attendee_data( $order_id ) {

    if ( ! $order_id )
        return;

    // Getting an instance of the order object
    $order      = wc_get_order( $order_id );
    $order_meta = get_post_meta($order_id); 

    $order_meta_text = "_stock_msg_".$order_id;
    $order_processing = "processing_".$order_id;
    $order_completed = "completed_".$order_id;
    $order_cancelled = "cancelled_".$order_id;

   # Iterating through each order items (WC_Order_Item_Product objects in WC 3+)
    foreach ( $order->get_items() as $item_id => $item_values ) {
        $product_id     = $item_values->get_product_id(); 
        $item_data      = $item_values->get_data();
        $product_id     = $item_data['product_id'];
        $item_quantity  = $item_values->get_quantity();
        $product        = get_page_by_title( $item_data['name'], OBJECT, 'mep_events' );
        $event_name     = $item_data['name'];
        $event_id       = $product->ID;
        $item_id        = $item_id;
    }

$user_info_arr = wc_get_order_item_meta($item_id,'_event_user_info',true);


    $first_name       = $order_meta['_billing_first_name'][0];
    $last_name        = $order_meta['_billing_last_name'][0];
    $company_name     = $order_meta['_billing_company'][0];
    $address_1        = $order_meta['_billing_address_1'][0];
    $address_2        = $order_meta['_billing_address_2'][0];
    $city             = $order_meta['_billing_city'][0];
    $state            = $order_meta['_billing_state'][0];
    $postcode         = $order_meta['_billing_postcode'][0];
    $country          = $order_meta['_billing_country'][0];
    $email            = $order_meta['_billing_email'][0];
    $phone            = $order_meta['_billing_phone'][0];
    $billing_intotal  = $order_meta['_billing_address_index'][0];
    $payment_method   = $order_meta['_payment_method_title'][0];
    $user_id          = $order_meta['_customer_user'][0];

$mep_atnd = "_mep_atnd_".$order_id;

    $mep_stock_msgc   = get_post_meta($event_id,$mep_atnd, true);

    mep_event_confirmation_email_sent($event_id,$email);

if($mep_stock_msgc!='a2'){

  foreach ($user_info_arr as $_user_info) {
    $uname          = $_user_info['user_name'];
    $email          = $_user_info['user_email'];
    $phone          = $_user_info['user_phone'];
    $address        = $_user_info['user_address'];
    $gender         = $_user_info['user_gender'];
    $company        = $_user_info['user_company'];
    $designation    = $_user_info['user_designation'];
    $website        = $_user_info['user_website'];
    $vegetarian     = $_user_info['user_vegetarian'];
    $tshirtsize     = $_user_info['user_tshirtsize'];
    $ticket_type    = $_user_info['user_ticket_type'];
    $mep_ucf        = $_user_info['mep_ucf'];

      // ADD THE FORM INPUT TO $new_post ARRAY
      $new_post = array(
      'post_title'    =>   $uname,
      'post_content'  =>   '',
      'post_category' =>   array(),  // Usable for custom taxonomies too
      'tags_input'    =>   array(),
      'post_status'   =>   'publish', // Choose: publish, preview, future, draft, etc.
      'post_type'     =>   'mep_events_attendees'  //'post',page' or use a custom post type if you want to
      );

      //SAVE THE POST
      $pid                = wp_insert_post($new_post);
      $update_fname       = update_post_meta( $pid, 'ea_name', $uname);
      $update_uid         = update_post_meta( $pid, 'ea_user_id', $user_id);
      $update_ad1         = update_post_meta( $pid, 'ea_address_1', $address);
      $update_email       = update_post_meta( $pid, 'ea_email', $email);
      $update_phone       = update_post_meta( $pid, 'ea_phone', $phone);
      $update_gender      = update_post_meta( $pid, 'ea_gender', $gender);
      $update_company     = update_post_meta( $pid, 'ea_company', $company);
      $update_desg        = update_post_meta( $pid, 'ea_desg', $designation);
      $update_web         = update_post_meta( $pid, 'ea_website', $website);
      $update_veg         = update_post_meta( $pid, 'ea_vegetarian', $vegetarian);
      $update_teesize     = update_post_meta( $pid, 'ea_tshirtsize', $tshirtsize);
      $update_ticket_type = update_post_meta( $pid, 'ea_ticket_type', $ticket_type);
      $update_pym         = update_post_meta( $pid, 'ea_payment_method', $payment_method);
      $update_event_name  = update_post_meta( $pid, 'ea_event_name', $event_name);
      $update_eid         = update_post_meta( $pid, 'ea_event_id', $event_id);
      $update_oid         = update_post_meta( $pid, 'ea_order_id', $order_id);
      // Checking if the form builder addon is active and have any custom fields
      $mep_form_builder_data = get_post_meta($event_id, 'mep_form_builder_data', true);
        if ( $mep_form_builder_data ) {
          foreach ( $mep_form_builder_data as $_field ) {
            update_post_meta( $pid, "ea_".$_field['mep_fbc_id'], $_user_info[$_field['mep_fbc_id']]); 
          }
      }


  }
}

}
}


add_action( 'woocommerce_thankyou','mep_set_first_order_sts');
function mep_set_first_order_sts($order_id ){

   // Getting an instance of the order object
    $order      = wc_get_order( $order_id );
    $order_meta = get_post_meta($order_id); 


   # Iterating through each order items (WC_Order_Item_Product objects in WC 3+)
    foreach ( $order->get_items() as $item_id => $item_values ) {
        $product_id = $item_values->get_product_id(); 
        $item_data = $item_values->get_data();
        $product_id = $item_data['product_id'];
        $item_quantity = $item_values->get_quantity();
        $product = get_page_by_title( $item_data['name'], OBJECT, 'mep_events' );
        $event_name = $item_data['name'];
        $event_id = $product->ID;
        $item_id = $item_id;
    // $item_data = $item_values->get_data();
    }


$mep_atnd = "_mep_atnd_".$order_id;
update_post_meta( $event_id, $mep_atnd, "a1");
}









add_action('woocommerce_order_status_changed', 'mep_event_seat_management', 10, 4);
function mep_event_seat_management( $order_id, $from_status, $to_status, $order ) {
global $wpdb;


   // Getting an instance of the order object
    $order      = wc_get_order( $order_id );
    $order_meta = get_post_meta($order_id); 


$c = 1;

   # Iterating through each order items (WC_Order_Item_Product objects in WC 3+)
    foreach ( $order->get_items() as $item_id => $item_values ) {
        $product_id = $item_values->get_product_id(); 
        $item_data = $item_values->get_data();
        $product_id = $item_data['product_id'];
        $item_quantity = $item_values->get_quantity();
        $product = get_page_by_title( $item_data['name'], OBJECT, 'mep_events' );
        $event_name = $item_data['name'];
        $event_id = $product->ID;
        $item_id = $item_id;
    // $item_data = $item_values->get_data();
    }

$table_name = $wpdb->prefix . 'woocommerce_order_itemmeta';
$result = $wpdb->get_results( "SELECT * FROM $table_name WHERE order_item_id=$item_id" );


    $mep_total    = get_post_meta($event_id,'total_booking', true);
    if($mep_total){
      $mep_total_booking = $mep_total;
    }else{
      $mep_total_booking =0;
    }
    


    $order_meta_text  = "_stock_msg_".$order_id;
    $order_processing = "processing_".$order_id;
    $order_completed  = "completed_".$order_id;
    $order_cancelled  = "cancelled_".$order_id;
    $mep_atnd         = "_mep_atnd_".$order_id;





if($order->has_status( 'processing' ) || $order->has_status( 'pending' )) {
// update_post_meta( $event_id, $mep_atnd, "a2");

$mep_stock_msgc = mep_get_order_info(get_post_meta($event_id,$order_meta_text, true),0);
$mep_stock_orderc = mep_get_order_info(get_post_meta($event_id,$order_meta_text, true),1);

if($mep_stock_orderc==$order_id){
      if($mep_stock_msgc=='cancelled'){

        foreach ( $result as $page ){
          if (strpos($page->meta_key, '_') !== 0) {

             $order_option_name = $event_id.str_replace(' ', '', mep_get_string_part($page->meta_key,0));

             $order_option_qty = mep_get_string_part($page->meta_key,1);
             $tes = get_post_meta($event_id,"mep_xtra_$order_option_name",true);
          $ntes = ($tes+$order_option_qty);
          update_post_meta( $event_id, "mep_xtra_$order_option_name",$ntes);
           }
        }



      }
    }




    update_post_meta( $event_id, $order_meta_text, $order_processing);
    

    $mep_stock_msg = mep_get_order_info(get_post_meta($event_id,$order_meta_text, true),0);
    $mep_stock_order = mep_get_order_info(get_post_meta($event_id,$order_meta_text, true),1);


if($mep_stock_order==$order_id){
      if($mep_stock_msg=='completed'){
          update_post_meta( $event_id, $order_meta_text, $order_processing);
      }
      else{
          update_post_meta( $event_id, 'total_booking', ($mep_total_booking+$item_quantity));
          update_post_meta( $event_id, $order_meta_text, $order_processing);

      }
    }



    
}





if($order->has_status( 'cancelled' )) {
  update_post_meta( $event_id,$mep_atnd, "a2");
  update_post_meta( $event_id, $order_meta_text, $order_cancelled);
  $mep_stock_msg = mep_get_order_info(get_post_meta($event_id,$order_meta_text, true),0);
  $mep_stock_order = mep_get_order_info(get_post_meta($event_id,$order_meta_text, true),1);


    if($mep_stock_order==$order_id){        
        $update_total_booking    = update_post_meta( $event_id, 'total_booking', ($mep_total_booking-$item_quantity));

    foreach ( $result as $page ){
      if (strpos($page->meta_key, '_') !== 0) {
       $order_option_name = $event_id.str_replace(' ', '', mep_get_string_part($page->meta_key,0));
       $order_option_qty = mep_get_string_part($page->meta_key,1);
       $tes = get_post_meta($event_id,"mep_xtra_$order_option_name",true);
    $ntes = ($tes-$order_option_qty);
    if($tes>0){
    update_post_meta( $event_id, "mep_xtra_$order_option_name",$ntes);
  }
     }
    }
    }

}







if( $order->has_status( 'completed' )) {
update_post_meta( $event_id, $mep_atnd, "a2");
      // update_post_meta( $event_id, $order_meta_text, $order_completed);
      $mep_stock_msg = mep_get_order_info(get_post_meta($event_id,$order_meta_text, true),0);
      $mep_stock_order = mep_get_order_info(get_post_meta($event_id,$order_meta_text, true),1);


    if($mep_stock_order==$order_id){

      if($mep_stock_msg=='processing'){
          update_post_meta( $event_id, $order_meta_text, $order_completed);
      }
      else{
          update_post_meta( $event_id, 'total_booking', ($mep_total_booking+$item_quantity));
          update_post_meta( $event_id, $order_meta_text, $order_completed);
          
          foreach ( $result as $page ){
          if (strpos($page->meta_key, '_') !== 0) {
           $order_option_name = $event_id.str_replace(' ', '', mep_get_string_part($page->meta_key,0));
           $order_option_qty = mep_get_string_part($page->meta_key,1);
           $tes = get_post_meta($event_id,"mep_xtra_$order_option_name",true);
        $ntes = ($tes+$order_option_qty);
        update_post_meta( $event_id, "mep_xtra_$order_option_name",$ntes);
         }
        }
      }

    }


  }

}



add_action('restrict_manage_posts', 'mep_filter_post_type_by_taxonomy');
function mep_filter_post_type_by_taxonomy() {
  global $typenow;
  $post_type = 'mep_events'; // change to your post type
  $taxonomy  = 'mep_cat'; // change to your taxonomy
  if ($typenow == $post_type) {
    $selected      = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
    $info_taxonomy = get_taxonomy($taxonomy);
    wp_dropdown_categories(array(
      'show_option_all' => __("Show All {$info_taxonomy->label}"),
      'taxonomy'        => $taxonomy,
      'name'            => $taxonomy,
      'orderby'         => 'name',
      'selected'        => $selected,
      'show_count'      => true,
      'hide_empty'      => true,
    ));
  };
}




add_filter('parse_query', 'mep_convert_id_to_term_in_query');
function mep_convert_id_to_term_in_query($query) {
  global $pagenow;
  $post_type = 'mep_events'; // change to your post type
  $taxonomy  = 'mep_cat'; // change to your taxonomy
  $q_vars    = &$query->query_vars;

  if ( $pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0 ) {
    $term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
    $q_vars[$taxonomy] = $term->slug;
  }

}



add_filter('parse_query', 'mep_attendee_filter_query');
function mep_attendee_filter_query($query) {
  global $pagenow;
  $post_type = 'mep_events_attendees'; 
  $q_vars    = &$query->query_vars;

  if ( $pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == $post_type && isset($_GET['meta_value']) && $_GET['meta_value'] != 0) {

    $q_vars['meta_key'] = 'ea_event_id';
    $q_vars['meta_value'] = $_GET['meta_value'];

  }
}


add_filter( 'post_row_actions', 'mep_remove_row_actions', 10, 1 );
function mep_remove_row_actions( $actions )
{
    if( get_post_type() === 'mep_events_attendees' )
        // unset( $actions['edit'] );
        // unset( $actions['view'] );
        unset( $actions['trash'] );
        unset( $actions['inline hide-if-no-js'] );
    return $actions;
}





// Add the custom columns to the book post type:
add_filter( 'manage_mep_events_posts_columns', 'mep_set_custom_edit_event_columns' );
function mep_set_custom_edit_event_columns($columns) {

    unset( $columns['date'] );

    $columns['mep_status'] = __( 'Status', 'mage-eventpress' );
    $columns['mep_atten'] = __( 'Attendees', 'mage-eventpress' );

    return $columns;
}


// Add the data to the custom columns for the book post type:
add_action( 'manage_mep_events_posts_custom_column' , 'mep_custom_event_column', 10, 2 );
function mep_custom_event_column( $column, $post_id ) {
switch ( $column ) {
case 'mep_status' :          
$values = get_post_custom( $post_id );        
echo mep_get_event_status($values['mep_event_start_date'][0]);
 break;

        case 'mep_atten' :
            echo '<a class="button button-primary button-large" href="'.get_site_url().'/wp-admin/edit.php?post_type=mep_events_attendees&meta_value='.$post_id.'">Attendees List</a>'; 
            break;
    }
}


// Getting event exprie date & time
function mep_get_event_status($startdatetime){

  $time = strtotime($startdatetime);
  $newformat = date('Y-m-d H:i:s',$time);
  $datetime1 = new DateTime();
  $datetime2 = new DateTime($newformat);
  $interval = $datetime1->diff($datetime2);
// print_r($newformat);
  if(time() > strtotime($newformat)){
    return "<span class=err>Expired</span>";
  }
  else{
  $days = $interval->days;
  $hours = $interval->h;
  $minutes = $interval->i;
  if($days>0){ $dd = $days." days "; }else{ $dd=""; }
  if($hours>0){ $hh = $hours." hours "; }else{ $hh=""; }
  if($minutes>0){ $mm = $minutes." minutes "; }else{ $mm=""; }
   return "<span class='active'>$dd $hh $mm</span>";
  }
}





// Redirect to Checkout after successfuly event registration
add_filter ('woocommerce_add_to_cart_redirect', 'mep_event_redirect_to_checkout');
function mep_event_redirect_to_checkout() {
    global $woocommerce;
    $checkout_url = wc_get_checkout_url();
    return $checkout_url;
}



// Add the custom columns to the book post type:
add_filter( 'manage_mep_events_attendees_posts_columns', 'mep_set_custom_events_attendees_columns' );
function mep_set_custom_events_attendees_columns($columns) {

    unset( $columns['title'] );
    unset( $columns['date'] );

    $columns['mep_uid'] = __( 'Unique ID', 'mepevvent' );
    $columns['mep_fn'] = __( 'Full Name', 'mage-eventpress' );
    // $columns['mep_email'] = __( 'email', 'mep' );
    // $columns['mep_phone'] = __( 'Phone', 'mep' );
    // $columns['mep_address'] = __( 'Addresss', 'mep' );
    // $columns['mep_tsize'] = __( 'Tee Size', 'mep' );
    $columns['mep_ttype'] = __( 'Ticket', 'mage-eventpress' );
    $columns['mep_evnt'] = __( 'Event', 'mage-eventpress' );

    return $columns;
}


// Add the data to the custom columns for the book post type:
add_action( 'manage_mep_events_attendees_posts_custom_column' , 'mep_events_attendees_column', 10, 2 );
function mep_events_attendees_column( $column, $post_id ) {
    switch ( $column ) {

        case 'mep_uid' :          
          echo get_post_meta( $post_id, 'ea_user_id', true ).get_post_meta( $post_id, 'ea_order_id', true ).$post_id;           
        break; 

        case 'mep_fn' :          
          echo get_post_meta( $post_id, 'ea_name', true );           
        break; 

        case 'mep_email' :          
          echo get_post_meta( $post_id, 'ea_email', true );           
        break;        

        case 'mep_phone' :          
          echo get_post_meta( $post_id, 'ea_phone', true );           
        break;

        case 'mep_tsize' :          
          echo get_post_meta( $post_id, 'ea_tshirtsize', true );           
        break;

        case 'mep_address' :          
          echo get_post_meta( $post_id, 'ea_address_1', true )."<br/>".get_post_meta( $post_id, 'ea_address_2', true )."<br/>".get_post_meta( $post_id, 'ea_state', true ).", ".get_post_meta( $post_id, 'ea_city', true ).", ".get_post_meta( $post_id, 'ea_country', true );           
        break;

        case 'mep_ttype' :          
          echo get_post_meta( $post_id, 'ea_ticket_type', true );           
        break;

        case 'mep_evnt' :          
          echo get_post_meta( $post_id, 'ea_event_name', true );           
        break;

        case 'mep_atten' :
            echo '<a class="button button-primary button-large" href="'.get_site_url().'/wp-admin/edit.php?post_type=mep_events_attendees&meta_value='.$post_id.'">Attendees List</a>'; 
            break;

    }
}

function mep_disable_new_posts() {
// Hide sidebar link
  global $submenu;
  unset($submenu['edit.php?post_type=mep_events_attendees'][10]);
// // Hide link on listing page
  if (isset($_GET['post_type']) && $_GET['post_type'] == 'mep_events_attendees') {
      echo '<style type="text/css">
      #favorite-actions, .add-new-h2, .tablenav, .page-title-action { display:none; }
      </style>';
  }
}
add_action('admin_menu', 'mep_disable_new_posts');



function mep_load_events_templates($template) {
    global $post;
  if ($post->post_type == "mep_events"){
          $template_name = 'single-events.php';
          $template_path = 'mage-events/';
          $default_path = plugin_dir_path( __FILE__ ) . 'templates/'; 
          $template = locate_template( array($template_path . $template_name) );
        if ( ! $template ) :
          $template = $default_path . $template_name;
        endif;
    return $template;
  }

    if ($post->post_type == "mep_events_attendees"){
        $plugin_path = plugin_dir_path( __FILE__ );
        $template_name = 'templates/single-mep_events_attendees.php';
        if($template === get_stylesheet_directory() . '/' . $template_name
            || !file_exists($plugin_path . $template_name)) {
            return $template;
        }
        return $plugin_path . $template_name;
    }

    return $template;
}
add_filter('single_template', 'mep_load_events_templates');





add_filter('template_include', 'mep_organizer_set_template');
function mep_organizer_set_template( $template ){

    if( is_tax('mep_org')){
        $template = plugin_dir_path( __FILE__ ).'templates/taxonomy-organozer.php';
    }

    if( is_tax('mep_cat')){
        $template = plugin_dir_path( __FILE__ ).'templates/taxonomy-category.php';
    }    

    return $template;
}



function mep_social_share(){
?>
<ul class='mep-social-share'>
       <li> <a data-toggle="tooltip" title="" class="facebook" onclick="window.open('https://www.facebook.com/sharer.php?u=<?php the_permalink(); ?>','Facebook','width=600,height=300,left='+(screen.availWidth/2-300)+',top='+(screen.availHeight/2-150)+''); return false;" href="http://www.facebook.com/sharer.php?u=<?php the_permalink(); ?>" data-original-title="Share on Facebook"><i class="fa fa-facebook"></i></a></li>

        <li><a data-toggle="tooltip" title="" class="gpuls" onclick="window.open('https://plus.google.com/share?url=<?php the_permalink(); ?>','Google plus','width=585,height=666,left='+(screen.availWidth/2-292)+',top='+(screen.availHeight/2-333)+''); return false;" href="https://plus.google.com/share?url=<?php the_permalink(); ?>" data-original-title="Share on Google Plus"><i class="fa fa-google-plus"></i></a> </li>                  

        <li><a data-toggle="tooltip" title="" class="twitter" onclick="window.open('https_ssl_verify://twitter.com/share?url=<?php the_permalink(); ?>&amp;text=<?php the_title(); ?>','Twitter share','width=600,height=300,left='+(screen.availWidth/2-300)+',top='+(screen.availHeight/2-150)+''); return false;" href="http://twitter.com/share?url=<?php the_permalink(); ?>&amp;text=<?php the_title(); ?>" data-original-title="Twittet it"><i class="fa fa-twitter"></i></a></li>
        </ul>
<?php
}

function mep_calender_date($datetime){
  $time       = strtotime($datetime);
  $newdate    = date('Ymd',$time);
  $newtime    = date('Hi',$time);
  $newformat  = $newdate."T".$newtime."00Z";
return $newformat;
}



function mep_add_to_google_calender_link($pid){
  $event        = get_post($pid);
  $event_meta   = get_post_custom($pid);
  $event_start  = $event_meta['mep_event_start_date'][0];
  $event_end    = $event_meta['mep_event_end_date'][0];

$location = $event_meta['mep_location_venue'][0]." ".$event_meta['mep_street'][0]." ".$event_meta['mep_city'][0]." ".$event_meta['mep_state'][0]." ".$event_meta['mep_postcode'][0]." ".$event_meta['mep_country'][0];


  ob_start();



?>
<a href="http://www.google.com/calendar/event?
action=TEMPLATE
&text=<?php echo $event->post_title; ?>
&dates=<?php echo mep_calender_date($event_start); ?>/<?php echo mep_calender_date($event_end); ?>
&details=<?php echo strip_tags($event->post_content); ?>
&location=<?php echo $location; ?>
&trp=false
&sprop=
&sprop=name:"
target="_blank" class='mep-add-calender' rel="nofollow"> <i class="fa fa-calendar"></i> <?php echo mep_get_label($pid,'mep_calender_btn_text','Add To Your Calendar'); ?></a>
<?php
  $content = ob_get_clean();
  echo $content;
}




function mep_get_item_name($name){
  $explode_name = explode('_', $name, 2);
  $the_item_name = str_replace('-', ' ', $explode_name[0]);
  return $the_item_name;
}



function mep_get_item_price($name){
  $explode_name = explode('_', $name, 2);
  $the_item_name = str_replace('-', ' ', $explode_name[1]);
  return $the_item_name;
}



function mep_get_string_part($data,$string){  
  $pieces = explode(" x ", $data);
return $pieces[$string]; // piece1
}


function mep_get_event_order_metadata($id,$part){
global $wpdb;
$table_name = $wpdb->prefix . 'woocommerce_order_itemmeta';
$result = $wpdb->get_results( "SELECT * FROM $table_name WHERE order_item_id=$id" );

foreach ( $result as $page )
{
  if (strpos($page->meta_key, '_') !== 0) {
   echo mep_get_string_part($page->meta_key,$part).'<br/>';
 }
}

}

add_action('woocommerce_account_dashboard','mep_ticket_lits_users');
function mep_ticket_lits_users(){
ob_start();
?>
<div class="mep-user-ticket-list">
  <table>
    <tr>
      <th><?php _e('Name','mage-eventpress'); ?></th>
      <th><?php _e('Ticket','mage-eventpress'); ?></th>
      <th><?php _e('Event','mage-eventpress'); ?></th>
      <th><?php _e('Download','mage-eventpress'); ?></th>
    </tr>
    <?php 
     $args_search_qqq = array (
                     'post_type'        => array( 'mep_events_attendees' ),
                     'posts_per_page'   => -1,
    'meta_query' => array(
        array(
            'key' => 'ea_user_id',
            'value' => get_current_user_id()
        )
    )
  );
  $loop = new WP_Query( $args_search_qqq );
  while ($loop->have_posts()) {
  $loop->the_post(); 
$event_id = get_post_meta( get_the_id(), 'ea_event_id', true );
  $event_meta = get_post_custom($event_id);

  $time = strtotime($event_meta['mep_event_start_date'][0]);
    $newformat = date('Y-m-d H:i:s',$time);


 if(time() < strtotime($newformat)){
?>
    <tr>
      <td><?php echo get_post_meta( get_the_id(), 'ea_name', true ); ?></td>
      <td><?php echo get_post_meta( get_the_id(), 'ea_ticket_type', true ); ?></td>
      <td><?php echo get_post_meta( get_the_id(), 'ea_event_name', true ); ?></td>
      <td><a href="<?php the_permalink(); ?>"><?php _e('Download','mage-eventpress'); ?></a></td>
    </tr>
<?php
  } 
  }    
    ?>
  </table>
</div>
<?php
$content = ob_get_clean();
echo $content;
}

// event_template_name();
function event_template_name(){

          $template_name = 'index.php';
          $template_path = get_stylesheet_directory().'/mage-events/themes/';
          $default_path = plugin_dir_path( __FILE__ ) . 'templates/themes/'; 

        $template = locate_template( array($template_path . $template_name) );

       if ( ! $template ) :
         $template = $default_path . $template_name;
       endif;

// echo $template_path;
if (is_dir($template_path)) {
  $thedir = glob($template_path."*");
}else{
$thedir = glob($default_path."*");
// file_get_contents('./people.txt', FALSE, NULL, 20, 14);
}

$theme = array();
foreach($thedir as $filename){
    //Use the is_file function to make sure that it is not a directory.
    if(is_file($filename)){
      $file = basename($filename);
     $naame = str_replace("?>","",strip_tags(file_get_contents($filename, FALSE, NULL, 24, 14))); 
    }   
     $theme[$file] = $naame;
}
return $theme;
}



function event_single_template_list($current_theme){
$themes = event_template_name();
        $buffer = '<select name="mep_event_template">';
        foreach ($themes as $num=>$desc){
          if($current_theme==$num){ $cc = 'selected'; }else{ $cc = ''; }
            $buffer .= "<option value=$num $cc>$desc</option>";
        }//end foreach
        $buffer .= '</select>';
        echo $buffer;
}

function mep_title_cutoff_words($text, $length){
    if(strlen($text) > $length) {
        $text = substr($text, 0, strpos($text, ' ', $length));
    }

    return $text;
}

function mep_get_tshirts_sizes($event_id){
  $event_meta   = get_post_custom($event_id);
  $tee_sizes  = $event_meta['mep_reg_tshirtsize_list'][0];
  $tszrray = explode(',', $tee_sizes);
$ts = "";
  foreach ($tszrray as $value) {
    $ts .= "<option value='$value'>$value</option>";
  }
return $ts;
}


function my_function_meta_deta() {
  global $order;

$order_id = $_GET['post'];
    // Getting an instance of the order object
    $order      = wc_get_order( $order_id );
    $order_meta = get_post_meta($order_id); 

   # Iterating through each order items (WC_Order_Item_Product objects in WC 3+)
    foreach ( $order->get_items() as $item_id => $item_values ) {
        $product_id     = $item_values->get_product_id(); 
        $item_data      = $item_values->get_data();
        $product_id     = $item_data['product_id'];
        $item_quantity  = $item_values->get_quantity();
        $product        = get_page_by_title( $item_data['name'], OBJECT, 'mep_events' );
        $event_name     = $item_data['name'];
        $event_id       = $product->ID;
        $item_id        = $item_id;
    }

$user_info_arr = wc_get_order_item_meta($item_id,'_event_user_info',true);

// print_r($user_info_arr);

 ob_start();
?>
<div class='event-atendee-infos'>
<table class="atendee-info">
  <tr>
    <th>Name</th>
    <th>City</th>
  </tr>
  <?php 
  foreach ($user_info_arr as $_user_info) {
    $uname          = $_user_info['user_name'];
    $email          = $_user_info['user_email'];
    $phone          = $_user_info['user_phone'];
    $address        = $_user_info['user_address'];
    $gender         = $_user_info['user_gender'];
    $company        = $_user_info['user_company'];
    $designation    = $_user_info['user_designation'];
    $website        = $_user_info['user_website'];
    $vegetarian     = $_user_info['user_vegetarian'];
    $tshirtsize     = $_user_info['user_tshirtsize'];
    $ticket_type    = $_user_info['user_ticket_type'];
?>
<tr><td><?php echo $uname; ?></td><td><?php echo $address; ?></td></tr>
<?php
  }
  ?>
</table>
</div>
<?php
 $content = ob_get_clean();
 echo $content;
}
 // add_action( 'woocommerce_admin_order_totals_after_refunded','my_function_meta_deta', $order->id );



// add_action( 'woocommerce_thankyou', 'woocommerce_thankyou_change_order_status', 10, 1 );
function woocommerce_thankyou_change_order_status( $order_id ){
    if( ! $order_id ) return;

    $order = wc_get_order( $order_id );

    if( $order->get_status() == 'processing' )
        $order->update_status( 'completed' );
}




function mep_event_list_price($pid){
global $post;
  $cur = get_woocommerce_currency_symbol();
  $mep_event_ticket_type = get_post_meta($pid, 'mep_event_ticket_type', true);
  $mep_events_extra_prices = get_post_meta($pid, 'mep_events_extra_prices', true);
  $n_price = get_post_meta($pid, '_price', true);
  if($n_price==0){
    $gn_price = "Free";
  }else{
    $gn_price =$cur.$n_price;
  }
  if($mep_events_extra_prices){
    $gn_price = $cur.$mep_events_extra_prices[0]['option_price'];
  }
  if($mep_event_ticket_type){
    $gn_price = $cur.$mep_event_ticket_type[0]['option_price_t'];
  }
return $gn_price;
}

function mep_get_label($pid,$label_id,$default_text){
 return  mep_get_option( $label_id, 'label_setting_sec', $default_text);
}