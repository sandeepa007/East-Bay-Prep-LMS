<?php
/**
* Plugin Name: Woocommerce Event Manager Addon: Form Builder
* Plugin URI: http://mage-people.com
* Description: This plugin will add a Event Attendee Form Builder in Event Page.
* Version: 1.0
* Author: MagePeople Team
* Author URI: http://www.mage-people.com/
*/



// remove_meta_box( '');


add_action('admin_enqueue_scripts', 'mep_event_builder_admin_scripts');
function mep_event_builder_admin_scripts() {
    wp_enqueue_style('mep-event-gallery-style',plugin_dir_url( __FILE__ ).'css/admin-event_gallery_style.css',array());
    wp_enqueue_script('mep-event-gallery-scripts',plugin_dir_url( __FILE__ ).'js/admin-event-gallery.js',array('jquery'),1,true);
}


// Enqueue Scripts for frontend
add_action('wp_enqueue_scripts', 'mep_event_builder_enqueue_scripts');
function mep_event_builder_enqueue_scripts() {
	wp_enqueue_script('jquery');  
}



// Create MKB CPT
function mep_pro_cpt() {

    $argsl = array(
        'public'   => true,
        'label'    => 'Event Attendees',
        'menu_icon'  => 'dashicons-id',
        'supports'  => array('title'),
        'show_in_menu' => 'edit.php?post_type=mep_events'

    );
    register_post_type( 'mep_events_attendees', $argsl );

}
add_action( 'init', 'mep_pro_cpt' );




// Get user information and save to attendee list after order confirmation
add_action( 'woocommerce_order_status_completed_notification', 'mep_pro_set_event_attendee_data' );
function mep_pro_set_event_attendee_data( $order_id ) {

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

    //mep_event_confirmation_email_sent($event_id,$email);

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































add_action( 'add_meta_boxes', 'mep_event_pro_meta_box_add' );
function mep_event_pro_meta_box_add(){
    add_meta_box( 'mep-event-reg-form', 'Event Registration Form', 'mep_event_pro_reg_form_meta_box_cb', 'mep_events', 'normal', 'high' );
}



function mep_event_pro_reg_form_meta_box_cb($post){
$values = get_post_custom( $post->ID );
?>

<div class='sec'>
    <label for="mep_ev_98">  
    <span><input style='text-align: left;width: auto;' id='mep_ev_98' type="checkbox" name='mep_full_name' <?php if(array_key_exists('mep_full_name', $values)){ $sqi = $values['mep_full_name'][0]; }else{$sqi=0; } if($sqi==1){ echo 'checked'; } ?> value='1'  />  <?php _e('Full Name','mage-eventpress'); ?> </span></label>
</div>

<div class='sec'>
    <label for="mep_ev_981">  
    <span><input style='text-align: left;width: auto;' id='mep_ev_981' type="checkbox" name='mep_reg_email' <?php if(array_key_exists('mep_reg_email', $values)){ $sqi1 = $values['mep_reg_email'][0]; }else{$sqi1=0;}  if($sqi1==1){ echo 'checked'; } ?> value='1'  />  <?php _e('Email Address','mage-eventpress'); ?> </span></label>
</div>

<div class='sec'>
    <label for="mep_ev_982">  
    <span><input style='text-align: left;width: auto;' id='mep_ev_982' type="checkbox" name='mep_reg_phone' <?php if(array_key_exists('mep_reg_phone', $values)){ $sqi2 = $values['mep_reg_phone'][0];}else{$sqi2=0;} if($sqi2==1){ echo 'checked'; } ?> value='1'  />  <?php _e('Phone Number','mage-eventpress'); ?> </span></label>
</div>

<div class='sec'>
    <label for="mep_ev_983">  
    <span><input style='text-align: left;width: auto;' id='mep_ev_983' type="checkbox" name='mep_reg_address' <?php if(array_key_exists('mep_reg_address', $values)){ $sqi3 = $values['mep_reg_address'][0];}else{$sqi3=0;}  if($sqi3==1){ echo 'checked'; } ?> value='1'  /><?php _e('Address','mage-eventpress'); ?> </span></label>
</div>


<div class='sec'>
    <label for="mep_ev_98309">  
    <span><input style='text-align: left;width: auto;' id='mep_ev_98309' type="checkbox" name='mep_reg_tshirtsize' <?php if(array_key_exists('mep_reg_tshirtsize', $values)){ $sqi312 = $values['mep_reg_tshirtsize'][0]; }else{$sqi312=0;}  if($sqi312==1){ echo 'checked'; } ?> value='1'  /><?php _e('T-Shirt Size','mage-eventpress'); ?> </span></label>
     </label>
     <label for="">
      <?php _e('Input Tshirts size, separetd by comma (M,L,XL)','mage-eventpress'); ?>
      <?php 
if(array_key_exists('mep_reg_tshirtsize', $values)){ $tsizes = $values['mep_reg_tshirtsize_list'][0]; }else{$tsizes='';}
      ?>
       <input style='' id='' type="text" name='mep_reg_tshirtsize_list'  value='<?php if($tsizes){ echo $tsizes; }else{ echo "S,M,L,XL"; } ?>'  />
     </label>
</div>



<div class='sec'>
    <label for="mep_ev_984">  
    <span><input style='text-align: left;width: auto;' id='mep_ev_984' type="checkbox" name='mep_reg_designation' <?php if(array_key_exists('mep_reg_designation', $values)){ $sqi4 = $values['mep_reg_designation'][0]; }else{$sqi4=0;} if($sqi4==1){ echo 'checked'; } ?> value='1'  />  <?php _e('Designation','mage-eventpress'); ?> </span></label>
</div>
<div class='sec'>
    <label for="mep_ev_985">  
    <span><input style='text-align: left;width: auto;' id='mep_ev_985' type="checkbox" name='mep_reg_website' <?php if(array_key_exists('mep_reg_website', $values)){ $sqi5 = $values['mep_reg_website'][0]; }else{$sqi5=0;}  if($sqi5==1){ echo 'checked'; } ?> value='1'  /> <?php _e('Website','mage-eventpress'); ?> </span></label>
</div>

<div class='sec'>
    <label for="mep_ev_986">  
    <span><input style='text-align: left;width: auto;' id='mep_ev_986' type="checkbox" name='mep_reg_veg' <?php if(array_key_exists('mep_reg_veg', $values)){ $sqi6 = $values['mep_reg_veg'][0];}else{$sqi6=0; } if($sqi6==1){ echo 'checked'; } ?> value='1'  />  <?php _e('Vegetarian','mage-eventpress'); ?> </span></label>
</div>
<div class='sec'>
    <label for="mep_ev_987">  
    <span><input style='text-align: left;width: auto;' id='mep_ev_987' type="checkbox" name='mep_reg_company' <?php if(array_key_exists('mep_reg_company', $values)){ $sqi7 = $values['mep_reg_company'][0]; }else{ $sqi7=0; } if($sqi7==1){ echo 'checked'; } ?> value='1'  />  <?php _e('Company Name','mage-eventpress'); ?> </span></label>
</div>
<div class='sec'>
    <label for="mep_ev_988">  
    <span><input style='text-align: left;width: auto;' id='mep_ev_988' type="checkbox" name='mep_reg_gender' <?php if(array_key_exists('mep_reg_gender', $values)){ $sqi8 = $values['mep_reg_gender'][0]; }else{$sqi8=0;}  if($sqi8==1){ echo 'checked'; } ?> value='1'  />  <?php _e('Gender','mage-eventpress'); ?> </span></label>
</div>

<?php
do_action('mep_after_reg_form');
}













function remove_post_custom_fields() {
  global $post;
  $mep_form_builder_data = get_post_meta($post->ID, 'mep_form_builder_data', true);
  wp_nonce_field( 'mep_event_form_builder_nonce', 'mep_event_form_builder_nonce' );
  ?>
<script type="text/javascript">
  jQuery(document).ready(function( $ ){
    $( '#add-fbc-row' ).on('click', function() {
      var row = $( '.empty-row-fbc.screen-reader-text' ).clone(true);
      row.removeClass( 'empty-row-fbc screen-reader-text' );
      row.insertBefore( '#repeatable-fieldset-fbc-one tbody>tr:last' );
      return false;
    });
    
    $( '.remove-fbc-row' ).on('click', function() {
      $(this).parents('tr').remove();
      return false;
    });
  });
  </script>
  
  <table id="repeatable-fieldset-fbc-one" width="100%">

  <tbody>
  <?php
  if ( $mep_form_builder_data ) :
  foreach ( $mep_form_builder_data as $_field ) {
  ?>
  <tr>
    <td>
    <div id='mep_event_fbc_r' class="">
        <ul>
            <li class="mep-fbc-label"><input placeholder="Field Label" type="text" class="mep-fbc-input" name="mep_fbc_label[]" value="<?php if($_field['mep_fbc_label'] != '') echo esc_attr( $_field['mep_fbc_label'] ); ?>"></li>
            <li class="mep-fbc-id"><input placeholder="Unique ID" type="text" class="mep-fbc-input" name="mep_fbc_filed_id[]" value="<?php if($_field['mep_fbc_id'] != '') echo esc_attr( $_field['mep_fbc_id'] ); ?>"></li>
            <li class="mep-fbc-type">
                <select name="mep_fbc_filed_type[]" id="mep_fbc_types">
                   <option value="">Select Type</option> 
                   <option value="text" <?php if($_field['mep_fbc_type']=='text'){ echo "selected"; } ?>>Text Box</option> 
                   <option value="textarea" <?php if($_field['mep_fbc_type']=='textarea'){ echo "selected"; } ?>>Textarea</option> 
                   <option value="radio" <?php if($_field['mep_fbc_type']=='radio'){ echo "selected"; } ?>>Radio Box</option> 
                   <option value="checkbox" <?php if($_field['mep_fbc_type']=='checkbox'){ echo "selected"; } ?>>Check Box</option> 
                   <option value="select" <?php if($_field['mep_fbc_type']=='select'){ echo "selected"; } ?>>Dropdown Box</option> 
                </select>
                <input type="text" class="mep-fbc-input" name='mep_fbc_dropdown_data[]' id="dropdown-values" class="text" value="<?php if($_field['mep_fbc_dp_data'] != '') echo esc_attr( $_field['mep_fbc_dp_data'] ); ?>"/>
            </li>
            <li class="mep-fbc-req">

                <select name="mep_fbc_filed_required[]" id="">
                   <option value="">Not Required</option> 
                   <option value="1" <?php if($_field['mep_fbc_required']){ echo "selected"; } ?>>Required</option> 
                </select>
            </li>
            <li class="mep-fbc-remove"><a class="button remove-fbc-row" href="#">X</a></li>
        </ul>
    </div>
    </td>
  </tr>
  <?php
  }
  else :
  // show a blank one
 endif; 
 ?>
  
  <!-- empty hidden one for jQuery -->
  <tr class="empty-row-fbc screen-reader-text">
    <td>
    <div id='mep_event_fbc_r' class="">
        <ul>
            <li class="mep-fbc-label"><input placeholder="Field Label" type="text" class="mep-fbc-input" name="mep_fbc_label[]"></li>
            <li class="mep-fbc-id"><input placeholder="Unique ID" type="text" class="mep-fbc-input" name="mep_fbc_filed_id[]"></li>
            <li class="mep-fbc-type">
                <select name="mep_fbc_filed_type[]" id="">
                   <option value="">Select Type</option> 
                   <option value="text">Text Box</option> 
                   <option value="textarea">Textarea</option> 
                   <!-- <option value="radio">Radio Box</option>  -->
                   <option value="checkbox">Check Box</option> 
                   <option value="select">Dropdown Box</option> 
                </select>
                <input type="text" class='mep-fbc-input' name='mep_fbc_dropdown_data[]' id="dropdown-values" class="text">
            </li>
            <li class="mep-fbc-req">
                <select name="mep_fbc_filed_required[]" id="">
                   <option value="">Not Required</option> 
                   <option value="1">Required</option> 
                </select>

            </li>
            <li class="mep-fbc-remove"><a class="button remove-fbc-row" href="#">X</a></li>
        </ul>
    </div>
    </td>
    
  </tr>
  </tbody>
  </table>
  <p><a id="add-fbc-row" class="button" href="#">Add New Field</a></p>
  <?php
}
add_action( 'mep_after_reg_form' , 'remove_post_custom_fields' );








add_action('save_post', 'mep_event_fbc_save');
function mep_event_fbc_save($post_id) {
  global $wpdb;
  
  if ( ! isset( $_POST['mep_event_form_builder_nonce'] ) ||
  ! wp_verify_nonce( $_POST['mep_event_form_builder_nonce'], 'mep_event_form_builder_nonce' ) )
    return;
  
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
    return;
  
  if (!current_user_can('edit_post', $post_id))
    return;
  
  $old = get_post_meta($post_id, 'mep_form_builder_data', true);
  $new = array();
  // $options = hhs_get_sample_options();
  
 $label                    = $_POST['mep_fbc_label'];
 $fbc_id                   = $_POST['mep_fbc_filed_id'];
 $fbc_type                 = $_POST['mep_fbc_filed_type'];
 $fbc_required             = $_POST['mep_fbc_filed_required'];
 $mep_fbc_dropdown_data    = $_POST['mep_fbc_dropdown_data'];

// die();
  $count = count( $label );
  
  for ( $i = 0; $i < $count; $i++ ) {
    
    if ( $label[$i] != '' ) :
      $new[$i]['mep_fbc_label'] = stripslashes( strip_tags( $label[$i] ) );
      endif;

    if ( $fbc_id[$i] != '' ) :
      $new[$i]['mep_fbc_id'] = stripslashes( strip_tags( $fbc_id[$i] ) );
      endif;

    if ( $fbc_type[$i] != '' ) :
      $new[$i]['mep_fbc_type'] = stripslashes( strip_tags( $fbc_type[$i] ) );
      endif;

    if ( $fbc_required[$i] != '') :
      $new[$i]['mep_fbc_required'] = stripslashes( strip_tags( $fbc_required[$i] ) );
      endif;

    if ( $mep_fbc_dropdown_data[$i] != '') :
      $new[$i]['mep_fbc_dp_data'] = stripslashes( strip_tags( $mep_fbc_dropdown_data[$i] ) );
      endif;

  }

  if ( !empty( $new ) && $new != $old )
    update_post_meta( $post_id, 'mep_form_builder_data', $new );
  elseif ( empty($new) && $old )
    delete_post_meta( $post_id, 'mep_form_builder_data', $old );
}


function mep_fbc_dropdown_list($data){
  $tee_sizes  = $data;
  $tszrray = explode(',', $tee_sizes);
  $ts = "";
  foreach ($tszrray as $value) {
    $ts .= "<option value='$value'>$value</option>";
  }
return trim($ts);
}


add_action('mep_after_reg_form_front','mep_users_reg_forms');

function mep_users_reg_forms(){
    global $post;
    $mep_form_builder_data = get_post_meta($post->ID, 'mep_form_builder_data', true);
    if ( $mep_form_builder_data ) {
  foreach ( $mep_form_builder_data as $_field ) {

    $label      = $_field['mep_fbc_label'];
    $uid        = $_field['mep_fbc_id']."[]";
    $type       = $_field['mep_fbc_type'];
    $required   = $_field['mep_fbc_required'];
    $dp_data   = $_field['mep_fbc_dp_data'];
    
    if($required){ 
        $req ='required'; 
    }else{ 
        $req =''; 
    }

if($type=='textarea'){
    echo "<textarea name='$uid' col='10' row='3' class='mep_input' placeholder='$label' $req></textarea>";
}
if($type=='radio' || $type=='checkbox'){
    echo "<label><input type='$type' value='Yes' class='mep-checkbox' name='$uid' class='mep_input' $req/> $label</label>";
}
if($type=='select'){  
   echo "<label for='$uid'><select name='$uid' $req><option value=''>Please Select $label</option>".mep_fbc_dropdown_list($dp_data)."</select></label>";
}
if($type=='text'){
    echo "<input type='$type' name='$uid' placeholder='$label' class='mep_input' $req/>";
}


  }
 }
}



add_action('mep_reg_fields','mep_pro_reg_form_fileds');

function mep_pro_reg_form_fileds(){
  global $post,$qm;
  $event_meta = get_post_custom($post->ID);
 // print_r($event_meta);
    $mep_full_name         = strip_tags($event_meta['mep_full_name'][0]);
    $mep_reg_email         = strip_tags($event_meta['mep_reg_email'][0]);
    $mep_reg_phone         = strip_tags($event_meta['mep_reg_phone'][0]);
    $mep_reg_address       = strip_tags($event_meta['mep_reg_address'][0]);
    $mep_reg_designation   = strip_tags($event_meta['mep_reg_designation'][0]);
    $mep_reg_website       = strip_tags($event_meta['mep_reg_website'][0]);
    $mep_reg_veg           = strip_tags($event_meta['mep_reg_veg'][0]);
    $mep_reg_company       = strip_tags($event_meta['mep_reg_company'][0]);
    $mep_reg_gender        = strip_tags($event_meta['mep_reg_gender'][0]);
    $mep_reg_tshirtsize    = strip_tags($event_meta['mep_reg_tshirtsize'][0]);
  ob_start();
?>
<div class='mep-user-info-sec'><h5><?php echo $qm; ?> <?php _e('Attendee info:','mage-eventpress'); ?>"+i+"</h5><input type='<?php if($mep_full_name){ echo 'text'; }else{ echo 'hidden'; } ?>' <?php if($mep_full_name){ ?> required='required' <?php } ?> name='user_name[]' class='mep_input' placeholder='<?php _e('Enter Your Name','mage-eventpress'); ?>'/><input type='<?php if($mep_reg_email){ echo 'email'; }else{ echo 'hidden'; } ?>' <?php if($mep_reg_email){ ?> required='required' <?php } ?> name='user_email[]' class='mep_input' placeholder='<?php _e('Enter Your Email','mage-eventpress'); ?>'/><input type='<?php if($mep_reg_phone){ echo 'text'; }else{ echo 'hidden'; } ?>' <?php if($mep_reg_phone){ ?> required='required' <?php } ?> name='user_phone[]' class='mep_input' placeholder='<?php _e('Enter Your Phone','mage-eventpress'); ?>'/><textarea name='user_address[]' class='mep_input <?php if($mep_reg_address){ echo 'mep-show'; }else{ echo 'mep-hidden'; } ?>' rows='3' <?php if($mep_reg_address){ ?> required='required' <?php } ?> placeholder='<?php _e('Enter you address','mage-eventpress'); ?>'></textarea><label class='<?php if($mep_reg_tshirtsize){ echo "mep-show"; }else{ echo "mep-hidden"; } ?>' for='gen' style='text-align: left;'><?php _e('T-Shirt Size','mage-eventpress'); ?><select name='tshirtsize[]' id='gen'><option value=''><?php _e('Please Select','mage-eventpress'); ?></option><?php echo mep_get_tshirts_sizes($post->ID); ?></select></label><label class='<?php if($mep_reg_gender){ echo 'mep-show'; }else{ echo 'mep-hidden'; } ?>' for='gen' style='text-align: left;'><?php _e('Gender','mage-eventpress'); ?><select name='gender[]' id='gen'><option value=''><?php _e('Please Select','mage-eventpress'); ?></option><option value='Male'><?php _e('Male','mage-eventpress'); ?></option><option value='Female'><?php _e('Female','mage-eventpress'); ?></option></select></label><input type='<?php if($mep_reg_company){ echo 'text'; }else{ echo 'hidden'; } ?>' name='user_company[]' class='mep_input' placeholder='<?php _e('Company','mage-eventpress'); ?>'/><input type='<?php if($mep_reg_designation){ echo 'text'; }else{ echo 'hidden'; } ?>' name='user_designation[]' class='mep_input' placeholder='<?php _e('Designation','mage-eventpress'); ?>'/><input type='<?php if($mep_reg_website){ echo 'text'; }else{ echo 'hidden'; } ?>' name='user_website[]' class='mep_input' placeholder='<?php _e('Website','mage-eventpress'); ?>'/><label class='<?php if($mep_reg_veg){ echo 'mep-show'; }else{ echo 'mep-hidden'; } ?>' for='veg' style='text-align: left;'><?php _e('Vegetarian','mage-eventpress'); ?><select name='vegetarian[]' id='veg'><option value=''><?php _e('Please Select','mage-eventpress'); ?></option><option value='Yes'><?php _e('Yes','mage-eventpress'); ?></option><option value='No'><?php _e('No','mage-eventpress'); ?></option></select></label><input type='hidden' name='ticket_type[]' class='mep_input' value='<?php echo $qm; ?>' /><?php do_action('mep_after_reg_form_front'); ?>
<?php
$content = ob_get_clean();
echo $content;
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
add_filter( 'manage_mep_events_posts_columns', 'mep_pro_set_custom_edit_event_columns' );
function mep_pro_set_custom_edit_event_columns($columns) {

    unset( $columns['date'] );

    // $columns['mep_status'] = __( 'Status', 'mage-eventpress' );
    $columns['mep_atten'] = __( 'Attendees', 'mage-eventpress' );

    return $columns;
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