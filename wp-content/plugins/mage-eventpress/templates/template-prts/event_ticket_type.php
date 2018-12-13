<?php 

add_action('mep_event_ticket_types','mep_ev_ticket_type');

function mep_ev_ticket_type(){
global $post, $product,$event_meta;
$pid = $post->ID;
$count=1;

if(array_key_exists('mep_available_seat', $event_meta)){ 
  $mep_available_seat = $event_meta['mep_available_seat'][0];
}else{
  $mep_available_seat = 'on';
}

$mep_event_ticket_type = get_post_meta($post->ID, 'mep_event_ticket_type', true);
if($mep_event_ticket_type){
?>
<?php echo "<h3 class='ex-sec-title'>".mep_get_label($pid,'mep_event_ticket_type_text','Ticket Type:
')."</h3>"; ?>
<table>
<?php 
$count =1;
foreach ( $mep_event_ticket_type as $field ) {
$qm = $field['option_name_t'];
$tesqn = $pid.str_replace(' ', '', $qm);
$tesq = get_post_meta($pid,"mep_xtra_$tesqn",true);
$llft = ($field['option_qty_t'] - (int)$tesq);
$qty_t_type = $field['option_qty_t_type'];
  ?>
<tr>
<td align="Left"><?php echo $field['option_name_t']; ?>
  <?php if($mep_available_seat=='on'){ ?><div class="xtra-item-left"><?php echo $llft; ?> <?php _e('Seats are available ','mage-eventpress'); ?></div> <?php } ?>
</td>
<td class="ticket-qty">
<span class="tkt-qty"> <?php _e('Ticket Qty:','mage-eventpress'); ?> </span>

<?php 
if($llft>0){
if($qty_t_type=='dropdown'){ ?>
<select name="option_qty[]" id="eventpxtp_<?php echo $count; ?>" class='extra-qty-box etp'>
  <?php for ($i = 0; $i <= $llft; $i++) { ?>
    <option value="<?php echo $i; ?>"><?php echo $i; ?> <?php _e('Ticket','mage-eventpress'); ?></option>
  <?php } ?>  
</select>
<?php }else{ ?> 
<input id="eventpxtp_<?php echo $count; ?>" <?php //if($ext_left<=0){ echo "disabled"; } ?> size="4" pattern="[0-9]*" inputmode="numeric" type="number" class='extra-qty-box etp' name='option_qty[]' data-price='<?php echo $field['option_price_t']; ?>' value='1' min="0" max="<?php echo $llft; ?>">
<?php } } ?>  








</td>
<td class="ticket-price"><span class="tkt-pric"><?php _e('Per Session Price:','mep'); ?></span>  <strong><?php echo get_woocommerce_currency_symbol().$field['option_price_t']; ?></strong>

  <p style="display: none;" class="price_jq"><?php echo $field['option_price_t']; ?></p>
  <input type="hidden" name='option_name[]' value='<?php echo $field['option_name_t']; ?>'>
<input type="hidden" name='option_price[]' value='<?php echo $field['option_price_t']; ?>'>
</td>
</tr>
<tr>
  <td colspan="3" class='user-innnf'> <div class="user-info-sec">
      <div id="dadainfo_<?php echo $count; ?>" class="dada-info">
    <div id="newDiv1">
        <div class="mep-user-info-sec">
            <h5>Attendee info:</h5>
            <input type="text" required="required" name="user_name[]" value="jony jan" '="" class="mep_input" readonly="" placeholder="Enter Your Name"><input type="email" required="required" name="user_email[]" value="manoj@khacreation.com" readonly="" class="mep_input" placeholder="Enter Your Email"><input type="hidden" name="user_phone[]" class="mep_input" placeholder="Enter Your Phone"><textarea name="user_address[]" class="mep_input mep-hidden" rows="3" placeholder="Enter you address"></textarea><label class="mep-hidden" for="gen" style="text-align: left;">T-Shirt Size<select name="tshirtsize[]" id="gen"><option value="">Please Select</option><option value="S">S</option><option value="M">M</option><option value="L">L</option><option value="XL">XL</option></select></label><label class="mep-hidden" for="gen" style="text-align: left;">Gender<select name="gender[]" id="gen"><option value="">Please Select</option><option value="Male">Male</option><option value="Female">Female</option></select></label><input type="hidden" name="user_company[]" class="mep_input" placeholder="Company"><input type="hidden" name="user_designation[]" class="mep_input" placeholder="Designation"><input type="hidden" name="user_website[]" class="mep_input" placeholder="Website"><label class="mep-hidden" for="veg" style="text-align: left;">Vegetarian<select name="vegetarian[]" id="veg"><option value="">Please Select</option><option value="Yes">Yes</option><option value="No">No</option></select></label><input type="hidden" name="ticket_type[]" class="mep_input" value="Semi Private">
        </div>
    </div>
</div>
    </div>
  </td>
</tr>
  <?php $count++; } ?>
</table>
<?php
}

}