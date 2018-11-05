<?php 
add_action('mep_add_to_cart','mep_get_event_reg_btn');

// Get Event Registration Button
function mep_get_event_reg_btn(){
global $post,$event_meta;
    $post_id = $post->ID;
// $event_meta = get_post_meta($post_id, 'mep_event_meta',true);
    $event_meta           = get_post_custom($post_id);
    $event_expire_date    = $event_meta['mep_event_start_date'][0];
    $event_sqi            = $event_meta['mep_sqi'][0];

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
    $event_ecternal_link   = '';
    $book_count            = get_post_meta($post_id,'total_booking', true);

if($book_count){ $total_book = $book_count; }else{ $total_book = 0; } 

    $seat_left  = ((int)$event_meta['mep_total_seat'][0]- (int)$total_book);
    $time       = strtotime($event_expire_date);
    $newformat  = date('Y-m-d H:i:s',$time);
    $datetime1  = new DateTime();
    $datetime2  = new DateTime($newformat);
    $interval   = $datetime1->diff($datetime2);
    $mep_event_ticket_type = get_post_meta($post_id, 'mep_event_ticket_type', true);

$stc = 0;
$leftt = 0;
if (is_array($mep_event_ticket_type) || is_object($mep_event_ticket_type)){
foreach ($mep_event_ticket_type as $field ) {
  $qm = $field['option_name_t'];
  $tesqn = $post_id.str_replace(' ', '', $qm);
  $tesq = get_post_meta($post_id,"mep_xtra_$tesqn",true);
  $stc = $stc+$field['option_qty_t'];
  $llft = ($field['option_qty_t'] - $tesq);
  $leftt = $leftt+$llft;
}
}else{$qm='';}
if($mep_event_ticket_type){
  $seat_left = $leftt;
}else{
  $seat_left = $seat_left;
}

if(time() > strtotime($newformat)){
    _e('"<span class=event-expire-btn>Event Expired</span>"','mage-eventpress');
  }
elseif($seat_left<=0){
    _e('"<span class=event-expire-btn>No Seat Available</span>"','mage-eventpress');
  }
else{
  $days = $interval->d;
  $hours = $interval->h;
  $minutes = $interval->i;
  if($days>0){ $dd = $days." days "; }else{ $dd=""; }
  if($hours>0){ $hh = $hours." hours "; }else{ $hh=""; }
  if($minutes>0){ $mm = $minutes." minutes "; }else{ $mm=""; }
$qty_typec = $event_meta['qty_box_type'][0];






if(array_key_exists('mep_reg_status', $event_meta)){ 
  $reg_status = $event_meta['mep_reg_status'][0];
}else{
  $reg_status = '';
}



// echo $reg_status;


if($reg_status!='off'){
  ?>
  <h4 class="mep-cart-table-title"> <?php _e('Register Now:','mage-eventpress'); ?></h4>
<form action="" method='post'>
<?php do_action('mep_event_ticket_type_extra_service');  ?>
<input type='hidden' id='rowtotal' value="<?php echo get_post_meta($post_id,"_price",true); ?>"/>
<table>


<tr>
<td align="left" class='total-col'><?php _e('Quantity:','mage-eventpress'); ?> <?php if($event_sqi==1){ 
$mep_event_ticket_type = get_post_meta($post_id, 'mep_event_ticket_type', true);
if($mep_event_ticket_type){
  ?>
 <input id="quantity_5a7abbd1bff73" class="input-text qty text extra-qty-box" step="1" min="1" max="<?php echo ($event_meta['mep_total_seat'][0]- $total_book); ?>" name="quantity" value="1" title="Qty" size="4" pattern="[0-9]*" inputmode="numeric" type="hidden">
 <span id="ttyttl"></span>
<?php
}else{
$qmx = ($event_meta['mep_total_seat'][0]- $total_book);
if($qty_typec=='dropdown'){ ?>
<select name="quantity" id="quantity_5a7abbd1bff73" class='input-text qty text extra-qty-box'>
  <?php for ($i = 1; $i <= $qmx; $i++) { ?>
    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
  <?php } ?>  
</select>
<?php }else{ ?> 
<input id="quantity_5a7abbd1bff73" class="input-text qty text extra-qty-box" step="1" min="1" max="<?php echo ($event_meta['mep_total_seat'][0]- $total_book); ?>" name="quantity" value="1" title="Qty" size="4" pattern="[0-9]*" inputmode="numeric" type="number">
<?php } } }else { echo 1; } ?> <span class='the-total'><?php _e('Total','mage-eventpress'); ?> <span id="usertotal"></span></span></td>




<td align="right"> <button type="submit" name="add-to-cart" value="<?php echo esc_attr($post_id); ?>" class="single_add_to_cart_button button alt btn-mep-event-cart"><?php _e(mep_get_label($post_id,'mep_cart_btn_text','Register This Event'),'mage-eventpress'); ?> </button></td>
</tr>
</table>
<?php 
$mep_event_ticket_type = get_post_meta($post->ID, 'mep_event_ticket_type', true);
if(!$mep_event_ticket_type){
    if($qm){$qm=$qm;}else{$qm='';}
?>
  <div class="user-info-sec">
      <div id="divParent"><div class='mep-user-info-sec'><h5><?php echo $qm; ?> <?php _e('Attendee info','mage-eventpress'); ?>:1</h5><input type='<?php if($mep_full_name){ echo 'text'; }else{ echo 'hidden'; } ?>' <?php if($mep_full_name){ ?> required='required' <?php } ?> name='user_name[]' class='mep_input' placeholder='<?php _e('Enter Your Name','mage-eventpress'); ?>'/><input type='<?php if($mep_reg_email){ echo 'email'; }else{ echo 'hidden'; } ?>' <?php if($mep_reg_email){ ?> required='required' <?php } ?> name='user_email[]' class='mep_input' placeholder='<?php _e('Enter Your Email','mage-eventpress'); ?>'/><input type='<?php if($mep_reg_phone){ echo 'text'; }else{ echo 'hidden'; } ?>' <?php if($mep_reg_phone){ ?> required='required' <?php } ?> name='user_phone[]' class='mep_input' placeholder='<?php _e('Enter Your Phone','mage-eventpress'); ?>'/><textarea name='user_address[]' class='mep_input <?php if($mep_reg_address){ echo 'mep-show'; }else{ echo 'mep-hidden'; } ?>' rows='3' <?php if($mep_reg_address){ ?> required='required' <?php } ?> placeholder='<?php _e('Enter you address','mage-eventpress'); ?>'></textarea><label class='<?php if($mep_reg_tshirtsize){ echo "mep-show"; }else{ echo "mep-hidden"; } ?>' for='gen' style='text-align: left;'><?php _e('T-Shirt Size','mage-eventpress'); ?><select name='tshirtsize[]' id='gen'><option value=''><?php _e('Please Select','mage-eventpress'); ?></option><?php echo mep_get_tshirts_sizes($post->ID); ?></select></label><label class='<?php if($mep_reg_gender){ echo 'mep-show'; }else{ echo 'mep-hidden'; } ?>' for='gen' style='text-align: left;'><?php _e('Gender','mage-eventpress'); ?><select name='gender[]' id='gen'><option value=''><?php _e('Please Select','mage-eventpress'); ?></option><option value='Male'><?php _e('Male','mage-eventpress'); ?></option><option value='Female'><?php _e('Female','mage-eventpress'); ?></option></select></label><input type='<?php if($mep_reg_company){ echo 'text'; }else{ echo 'hidden'; } ?>' name='user_company[]' class='mep_input' placeholder='<?php _e('Company','mage-eventpress'); ?>'/><input type='<?php if($mep_reg_designation){ echo 'text'; }else{ echo 'hidden'; } ?>' name='user_designation[]' class='mep_input' placeholder='<?php _e('Designation','mage-eventpress'); ?>'/><input type='<?php if($mep_reg_website){ echo 'text'; }else{ echo 'hidden'; } ?>' name='user_website[]' class='mep_input' placeholder='<?php _e('Website','mage-eventpress'); ?>'/><label class='<?php if($mep_reg_veg){ echo 'mep-show'; }else{ echo 'mep-hidden'; } ?>' for='veg' style='text-align: left;'><?php _e('Vegetarian','mage-eventpress'); ?><select name='vegetarian[]' id='veg'><option value=''><?php _e('Please Select','mage-eventpress'); ?></option><option value='Yes'><?php _e('Yes','mage-eventpress'); ?></option><option value='No'><?php _e('No','mage-eventpress'); ?></option></select></label><input type='hidden' name='ticket_type[]' class='mep_input' value='<?php echo $qm; ?>' /><?php do_action('mep_after_reg_form_front'); ?></div>
  </div>
</div>
  <?php } ?>

 
</form>

  <?php
  
  }
}
}