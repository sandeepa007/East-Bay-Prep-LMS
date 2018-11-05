<?php 
get_header();
the_post();
global $post;
global $woocommerce;
$event_meta = get_post_custom(get_the_id());
$author_terms = get_the_terms(get_the_id(), 'mep_org');
$book_count = get_post_meta(get_the_id(),'total_booking', true);
$user_api = mep_get_option( 'google-map-api', 'general_setting_sec', '');
if($book_count){ $total_book = $book_count; }else{ $total_book = 0; } 
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

  $global_template = mep_get_option( 'mep_global_single_template', 'general_setting_sec', 'theme-2');
$current_template = $event_meta['mep_event_template'][0];
if($current_template){
  $_current_template = $current_template;
}else{
  $_current_template = $global_template;
}  

?>



<div class="mep-events-wrapper">
<?php require_once(dirname(__FILE__) . "/themes/$_current_template"); ?>
</div>
<script>
jQuery('#quantity_5a7abbd1bff73').click(function() {
var $form = jQuery('form'); //on a real app it would be better to have a class or ID
var $totalQuant = jQuery('#quantity_5a7abbd1bff73', $form);
jQuery('#quantity_5a7abbd1bff73', $form).change(calculateTotal);


function calculateTotal() {
   var sum = jQuery('#rowtotal').val();
   jQuery('#usertotal').html('<?php echo get_woocommerce_currency_symbol(); ?>' + sum * parseInt( $totalQuant.val() || 0, 10));
}

});


jQuery(document).ready(function () {



  jQuery( "#mep-event-accordion" ).accordion({
        collapsible: true,
        active: false
  });





jQuery(document).on("change", ".etp", function() {
    var sum = 0;
    jQuery(".etp").each(function(){
        sum += +jQuery(this).val();
    });
    jQuery("#ttyttl").html(sum);
});



jQuery("#ttypelist").change(function () {
    vallllp = jQuery(this).val()+"_";
    var n = vallllp.split('_');
    var price = n[0];
    var ctt = 99;
if(vallllp!="_"){

   var currentValue = parseInt(ctt);
   jQuery('#rowtotal').val(currentValue += parseFloat(price));
}
if(vallllp=="_"){
    jQuery('#eventtp').attr('value', 0);
    jQuery('#eventtp').attr('max', 0);
    jQuery("#ttypeprice_show").html("")
}


});
   

function updateTotal() {
    var total = 0;
    vallllp = jQuery(this).val()+"_";
    var n = vallllp.split('_');
    var price = n[0];
    total += parseFloat(price);
     jQuery('#rowtotal').val(total);
}


//Bind the change event
jQuery(".extra-qty-box").on('change', function() {
        var sum = 0;
        var total = <?php if($event_meta['_price'][0]){ echo $event_meta['_price'][0]; }else{ echo 0; } ?>;

        jQuery('.price_jq').each(function () {
            var price = jQuery(this);
            var count = price.closest('tr').find('.extra-qty-box');
            sum = (price.html() * count.val());
            total = total + sum;
            // price.closest('tr').find('.cart_total_price').html(sum + "₴");
        });

        jQuery('#usertotal').html("<?php echo get_woocommerce_currency_symbol(); ?>" + total);
        jQuery('#rowtotal').val(total);

    }).change(); //trigger change event on page load




<?php 
$mep_event_ticket_type = get_post_meta($post->ID, 'mep_event_ticket_type', true);
if($mep_event_ticket_type){
$count =1;
foreach ( $mep_event_ticket_type as $field ) {
$qm = $field['option_name_t'];
?>


jQuery('#eventpxtp_<?php echo $count; ?>').on('change', function () {
        
        // var inputs = jQuery("#ttyttl").html() || 0;
        var inputs = jQuery('#eventpxtp_<?php echo $count; ?>').val() || 0;
        var input = parseInt(inputs);
        var children=jQuery('#dadainfo_<?php echo $count; ?> > div').size() || 0;     
        
        if(input < children){
            jQuery('#dadainfo_<?php echo $count; ?>').empty();
            children=0;
        }
        
        for (var i = children+1; i <= input; i++) {
            jQuery('#dadainfo_<?php echo $count; ?>').append(
            jQuery('<div/>')
                .attr("id", "newDiv" + i)
                .html("<div class='mep-user-info-sec'><h5><?php echo $qm; ?> <?php _e('Attendee info:','mage-eventpress'); ?>"+i+"</h5><input type='<?php if($mep_full_name){ echo 'text'; }else{ echo 'hidden'; } ?>' <?php if($mep_full_name){ ?> required='required' <?php } ?> name='user_name[]' class='mep_input' placeholder='<?php _e('Enter Your Name','mage-eventpress'); ?>'/><input type='<?php if($mep_reg_email){ echo 'email'; }else{ echo 'hidden'; } ?>' <?php if($mep_reg_email){ ?> required='required' <?php } ?> name='user_email[]' class='mep_input' placeholder='<?php _e('Enter Your Email','mage-eventpress'); ?>'/><input type='<?php if($mep_reg_phone){ echo 'text'; }else{ echo 'hidden'; } ?>' <?php if($mep_reg_phone){ ?> required='required' <?php } ?> name='user_phone[]' class='mep_input' placeholder='<?php _e('Enter Your Phone','mage-eventpress'); ?>'/><textarea name='user_address[]' class='mep_input <?php if($mep_reg_address){ echo 'mep-show'; }else{ echo 'mep-hidden'; } ?>' rows='3' <?php if($mep_reg_address){ ?> required='required' <?php } ?> placeholder='<?php _e('Enter you address','mage-eventpress'); ?>'></textarea><label class='<?php if($mep_reg_tshirtsize){ echo "mep-show"; }else{ echo "mep-hidden"; } ?>' for='gen' style='text-align: left;'><?php _e('T-Shirt Size','mage-eventpress'); ?><select name='tshirtsize[]' id='gen'><option value=''><?php _e('Please Select','mage-eventpress'); ?></option><?php echo mep_get_tshirts_sizes($post->ID); ?></select></label><label class='<?php if($mep_reg_gender){ echo 'mep-show'; }else{ echo 'mep-hidden'; } ?>' for='gen' style='text-align: left;'><?php _e('Gender','mage-eventpress'); ?><select name='gender[]' id='gen'><option value=''><?php _e('Please Select','mage-eventpress'); ?></option><option value='Male'><?php _e('Male','mage-eventpress'); ?></option><option value='Female'><?php _e('Female','mage-eventpress'); ?></option></select></label><input type='<?php if($mep_reg_company){ echo 'text'; }else{ echo 'hidden'; } ?>' name='user_company[]' class='mep_input' placeholder='<?php _e('Company','mage-eventpress'); ?>'/><input type='<?php if($mep_reg_designation){ echo 'text'; }else{ echo 'hidden'; } ?>' name='user_designation[]' class='mep_input' placeholder='<?php _e('Designation','mage-eventpress'); ?>'/><input type='<?php if($mep_reg_website){ echo 'text'; }else{ echo 'hidden'; } ?>' name='user_website[]' class='mep_input' placeholder='<?php _e('Website','mage-eventpress'); ?>'/><label class='<?php if($mep_reg_veg){ echo 'mep-show'; }else{ echo 'mep-hidden'; } ?>' for='veg' style='text-align: left;'><?php _e('Vegetarian','mage-eventpress'); ?><select name='vegetarian[]' id='veg'><option value=''><?php _e('Please Select','mage-eventpress'); ?></option><option value='Yes'><?php _e('Yes','mage-eventpress'); ?></option><option value='No'><?php _e('No','mage-eventpress'); ?></option></select></label><input type='hidden' name='ticket_type[]' class='mep_input' value='<?php echo $qm; ?>' /><?php do_action('mep_after_reg_form_front'); ?>")
                );
        }
       

    });


<?php 
$count++;
    }
 }else{
?>
jQuery('#quantity_5a7abbd1bff73').on('change', function () {
        
        var input = jQuery('#quantity_5a7abbd1bff73').val() || 0;
        var children=jQuery('#divParent > div').size() || 0;     
        
        if(input < children){
            jQuery('#divParent').empty();
            children=0;
        }
        
        for (var i = children+1; i <= input; i++) {
            jQuery('#divParent').append(
            jQuery('<div/>')
                .attr("id", "newDiv" + i)
                .html("<div class='mep-user-info-sec'><h5><?php echo $qm; ?> <?php _e('Attendee info:','mage-eventpress'); ?>"+i+"</h5><input type='<?php if($mep_full_name){ echo 'text'; }else{ echo 'hidden'; } ?>' <?php if($mep_full_name){ ?> required='required' <?php } ?> name='user_name[]' class='mep_input' placeholder='<?php _e('Enter Your Name','mage-eventpress'); ?>'/><input type='<?php if($mep_reg_email){ echo 'email'; }else{ echo 'hidden'; } ?>' <?php if($mep_reg_email){ ?> required='required' <?php } ?> name='user_email[]' class='mep_input' placeholder='<?php _e('Enter Your Email','mage-eventpress'); ?>'/><input type='<?php if($mep_reg_phone){ echo 'text'; }else{ echo 'hidden'; } ?>' <?php if($mep_reg_phone){ ?> required='required' <?php } ?> name='user_phone[]' class='mep_input' placeholder='<?php _e('Enter Your Phone','mage-eventpress'); ?>'/><textarea name='user_address[]' class='mep_input <?php if($mep_reg_address){ echo 'mep-show'; }else{ echo 'mep-hidden'; } ?>' rows='3' <?php if($mep_reg_address){ ?> required='required' <?php } ?> placeholder='<?php _e('Enter you address','mage-eventpress'); ?>'></textarea><label class='<?php if($mep_reg_tshirtsize){ echo "mep-show"; }else{ echo "mep-hidden"; } ?>' for='gen' style='text-align: left;'><?php _e('T-Shirt Size','mage-eventpress'); ?><select name='tshirtsize[]' id='gen'><option value=''><?php _e('Please Select','mage-eventpress'); ?></option><?php echo mep_get_tshirts_sizes($post->ID); ?></select></label><label class='<?php if($mep_reg_gender){ echo 'mep-show'; }else{ echo 'mep-hidden'; } ?>' for='gen' style='text-align: left;'><?php _e('Gender','mage-eventpress'); ?><select name='gender[]' id='gen'><option value=''><?php _e('Please Select','mage-eventpress'); ?></option><option value='Male'><?php _e('Male','mage-eventpress'); ?></option><option value='Female'><?php _e('Female','mage-eventpress'); ?></option></select></label><input type='<?php if($mep_reg_company){ echo 'text'; }else{ echo 'hidden'; } ?>' name='user_company[]' class='mep_input' placeholder='<?php _e('Company','mage-eventpress'); ?>'/><input type='<?php if($mep_reg_designation){ echo 'text'; }else{ echo 'hidden'; } ?>' name='user_designation[]' class='mep_input' placeholder='<?php _e('Designation','mage-eventpress'); ?>'/><input type='<?php if($mep_reg_website){ echo 'text'; }else{ echo 'hidden'; } ?>' name='user_website[]' class='mep_input' placeholder='<?php _e('Website','mage-eventpress'); ?>'/><label class='<?php if($mep_reg_veg){ echo 'mep-show'; }else{ echo 'mep-hidden'; } ?>' for='veg' style='text-align: left;'><?php _e('Vegetarian','mage-eventpress'); ?><select name='vegetarian[]' id='veg'><option value=''><?php _e('Please Select','mage-eventpress'); ?></option><option value='Yes'><?php _e('Yes','mage-eventpress'); ?></option><option value='No'><?php _e('No','mage-eventpress'); ?></option></select></label><input type='hidden' name='ticket_type[]' class='mep_input' value='<?php echo $qm; ?>' /><?php do_action('mep_after_reg_form_front'); ?>")
                );
        }
       

    });
<?php
} 
?>
});
</script>
<?php get_footer(); ?>