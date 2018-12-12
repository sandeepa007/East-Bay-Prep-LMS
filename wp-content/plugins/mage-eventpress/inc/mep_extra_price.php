<?php
function mep_add_custom_fields_text_to_cart_item( $cart_item_data, $product_id, $variation_id ){
  $tp = get_post_meta($product_id,'_price',true);

  $new = array();
  $user = array();

if(isset($_POST['event_addt_price'])){
  $checked                = $_POST['event_addt_price'];
}else{ $checked=""; } 

 if(isset($_POST['option_name'])){
  $names                  = $_POST['option_name'];
}else{ $names=""; } 

if(isset($_POST['option_qty'])){  
  $qty                    = $_POST['option_qty'];
}else{ $qty=""; } 

if(isset($_POST['option_price'])){  
  $price                  = $_POST['option_price'];
}else{ $price=""; } 

if(isset($_POST['user_name'])){
  $mep_user_name          = $_POST['user_name'];
}else{ $mep_user_name=""; } 

if(isset($_POST['user_email'])){  
  $mep_user_email         = $_POST['user_email'];
}else{ $mep_user_email=""; } 

if(isset($_POST['user_phone'])){  
  $mep_user_phone         = $_POST['user_phone'];
}else{ $mep_user_phone=""; } 

if(isset($_POST['user_address'])){  
  $mep_user_address       = $_POST['user_address'];
}else{ $mep_user_address=""; } 

if(isset($_POST['gender'])){  
  $mep_user_gender        = $_POST['gender'];
}else{ $mep_user_gender=""; } 

if(isset($_POST['tshirtsize'])){  
  $mep_user_tshirtsize    = $_POST['tshirtsize'];
}else{ $mep_user_tshirtsize=""; } 

if(isset($_POST['user_company'])){  
  $mep_user_company       = $_POST['user_company'];
}else{ $mep_user_company=""; } 

if(isset($_POST['user_designation'])){  
  $mep_user_desg          = $_POST['user_designation'];
}else{ $mep_user_desg=""; } 

if(isset($_POST['user_website'])){  
  $mep_user_website       = $_POST['user_website'];
}else{ $mep_user_website=""; } 

if(isset($_POST['vegetarian'])){  
  $mep_user_vegetarian    = $_POST['vegetarian'];
}else{ $mep_user_vegetarian=""; } 

if(isset($_POST['ticket_type'])){  
  $mep_user_ticket_type   = $_POST['ticket_type'];
}else{ $mep_user_ticket_type=""; } 

if(isset($_POST['mep_ucf'])){
  $mep_user_cfd           = $_POST['mep_ucf'];
}else{
  $mep_user_cfd           = "";
}


  $count_user = count($mep_user_name);
  $count = count( $names );
  

 if(isset($_POST['option_name'])){
  for ( $i = 0; $i < $count; $i++ ) {
    
    if ( $names[$i] != '' ) :
      $new[$i]['option_name'] = stripslashes( strip_tags( $names[$i] ) );
      endif;

    if ( $price[$i] != '' ) :
      $new[$i]['option_price'] = stripslashes( strip_tags( $price[$i] ) );
      endif;

    if ( $qty[$i] != '' ) :
      $new[$i]['option_qty'] = stripslashes( strip_tags( $qty[$i] ) );
      endif;

    $opttprice =   ($price[$i]*$qty[$i]);
    $tp = ($tp+$opttprice);
  }
}

  for ( $iu = 0; $iu < $count_user; $iu++ ) {
    
    if ( $mep_user_name[$iu] != '' ) :
      $user[$iu]['user_name'] = stripslashes( strip_tags( $mep_user_name[$iu] ) );
      endif;

    if ( $mep_user_email[$iu] != '' ) :
      $user[$iu]['user_email'] = stripslashes( strip_tags( $mep_user_email[$iu] ) );
      endif;

    if ( $mep_user_phone[$iu] != '' ) :
      $user[$iu]['user_phone'] = stripslashes( strip_tags( $mep_user_phone[$iu] ) );
      endif;

    if ( $mep_user_address[$iu] != '' ) :
      $user[$iu]['user_address'] = stripslashes( strip_tags( $mep_user_address[$iu] ) );
      endif;



    if ( $mep_user_gender[$iu] != '' ) :
      $user[$iu]['user_gender'] = stripslashes( strip_tags( $mep_user_gender[$iu] ) );
      endif;



    if ( $mep_user_tshirtsize[$iu] != '' ) :
      $user[$iu]['user_tshirtsize'] = stripslashes( strip_tags( $mep_user_tshirtsize[$iu] ) );
      endif;



    if ( $mep_user_company[$iu] != '' ) :
      $user[$iu]['user_company'] = stripslashes( strip_tags( $mep_user_company[$iu] ) );
      endif;

    if ( $mep_user_desg[$iu] != '' ) :
      $user[$iu]['user_designation'] = stripslashes( strip_tags( $mep_user_desg[$iu] ) );
      endif;

    if ( $mep_user_website[$iu] != '' ) :
      $user[$iu]['user_website'] = stripslashes( strip_tags( $mep_user_website[$iu] ) );
      endif;

    if ( $mep_user_vegetarian[$iu] != '' ) :
      $user[$iu]['user_vegetarian'] = stripslashes( strip_tags( $mep_user_vegetarian[$iu] ) );
      endif;

    if ( $mep_user_ticket_type[$iu] != '' ) :
      $user[$iu]['user_ticket_type'] = stripslashes( strip_tags( $mep_user_ticket_type[$iu] ) );
      endif;    

$mep_form_builder_data = get_post_meta($product_id, 'mep_form_builder_data', true);
  if ( $mep_form_builder_data ) {
    foreach ( $mep_form_builder_data as $_field ) {
        if ( $mep_user_ticket_type[$iu] != '' ) :
          $user[$iu][$_field['mep_fbc_id']] = stripslashes( strip_tags( $_POST[$_field['mep_fbc_id']][$iu] ) );
          endif; 
    }
  }




  }


if(isset($_POST['mep_event_ticket_type'])){
  $ttp = $_POST['mep_event_ticket_type'];
  $ttpqt = $_POST['tcp_qty'];
  $ticket_type = mep_get_order_info($ttp,1);
  $ticket_type_price = (mep_get_order_info($ttp,0)*$ttpqt);

  $cart_item_data['event_ticket_type'] = $ticket_type;
  $cart_item_data['event_ticket_price'] = $ticket_type_price;
  $cart_item_data['event_ticket_qty'] = $ttpqt;
  $tp = $tp+$ticket_type_price;
}


  $cart_item_data['event_extra_option'] = $new;
  $cart_item_data['event_user_info'] = $user;
  $cart_item_data['event_tp'] = $tp;
  $cart_item_data['line_total'] = $tp;
  $cart_item_data['line_subtotal'] = $tp;
$cart_item_data['event_id'] = $product_id;



  return $cart_item_data;
}
add_filter( 'woocommerce_add_cart_item_data', 'mep_add_custom_fields_text_to_cart_item', 10, 3 );



add_action( 'woocommerce_before_calculate_totals', 'add_custom_price' );
function add_custom_price( $cart_object ) {

    foreach ( $cart_object->cart_contents as $key => $value ) {
$eid = $value['event_id'];
if (get_post_type($eid) == 'mep_events') {      
            $cp = $value['event_tp'];
            $value['data']->set_price($cp);
            $new_price = $value['data']->get_price();
    }
  }
}





function mep_display_custom_fields_text_cart( $item_data, $cart_item ) {
$mep_events_extra_prices = $cart_item['event_extra_option'];
if($mep_events_extra_prices){
echo "<ul class='event-custom-price'>";

  foreach ( $mep_events_extra_prices as $field ) {
    if($field['option_qty']>0){
  ?>
  <li><?php echo esc_attr( $field['option_name'] ); ?> x <?php echo esc_attr( $field['option_qty'] ); ?>: <?php echo get_woocommerce_currency_symbol().($field['option_qty'] *$field['option_price'] ); ?>  </li>

  <?php
  }
}
}
if(array_key_exists('event_ticket_type', $cart_item)){
// if($cart_item['event_ticket_type']){
echo "<li> Ticket: ".$cart_item['event_ticket_type']." x ".$cart_item['event_ticket_qty'].": ".get_woocommerce_currency_symbol().$cart_item['event_ticket_price']."</li>";
}
    echo "</ul>";
  return $item_data;

}
add_filter( 'woocommerce_get_item_data', 'mep_display_custom_fields_text_cart', 10, 2 );




function mep_add_custom_fields_text_to_order_items( $item, $cart_item_key, $values, $order ) {
$eid = $values['event_id'];
if (get_post_type($eid) == 'mep_events') { 
$mep_events_extra_prices = $values['event_extra_option'];
$event_user_info         = $values['event_user_info'];
$event_ticket_type       = $values['event_ticket_type'];
$event_ticket_price      = $values['event_ticket_price'];
$event_ticket_qty        = $values['event_ticket_qty'];
$product_id              = $values['product_id'];

if (is_array($mep_events_extra_prices) || is_object($mep_events_extra_prices)){
foreach ( $mep_events_extra_prices as $field ) {
    if($field['option_qty']>0){

      $item->add_meta_data(esc_attr( $field['option_name'] )." x ".$field['option_qty'], get_woocommerce_currency_symbol().($field['option_qty'] *$field['option_price'] ) );


      $opt_name =  $product_id.str_replace(' ', '', $field['option_name']);
      $opt_qty = $field['option_qty'];

// $tes = 0;
$tes = get_post_meta($product_id,"mep_xtra_$opt_name",true);
$ntes = ($tes+$opt_qty);
update_post_meta( $product_id, "mep_xtra_$opt_name",$ntes);

  }

} 
}

if($event_ticket_type){

$event_ticket_type = "Ticket:".$event_ticket_type;
$item->add_meta_data( $event_ticket_type." x ".$event_ticket_qty,get_woocommerce_currency_symbol().$event_ticket_price);
$tck_name = $product_id.str_replace(' ', '', $event_ticket_type);
$tesqt = get_post_meta($product_id,"mep_xtra_$tck_name",true);
$ntesqt = ($tesqt+$event_ticket_qty);
update_post_meta( $product_id, "mep_xtra_$tck_name",$ntesqt);
}


$item->add_meta_data('_event_user_info',$event_user_info);


}
}
add_action( 'woocommerce_checkout_create_order_line_item', 'mep_add_custom_fields_text_to_order_items', 10, 4 );