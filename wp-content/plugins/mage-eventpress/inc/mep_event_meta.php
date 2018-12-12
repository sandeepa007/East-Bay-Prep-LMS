<?php 
if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.

add_action( 'add_meta_boxes', 'mep_event_meta_box_add' );
function mep_event_meta_box_add(){
    add_meta_box( 'mep-event-meta', 'Event Venue', 'mep_event_venue_meta_box_cb', 'mep_events', 'normal', 'high' );

    add_meta_box( 'mep-event-price', 'Event Price (Event Base price, It will not work if you add Event Ticket type Price)', 'mep_event_price_meta_box_cb', 'mep_events', 'normal', 'high' );

    add_meta_box( 'mep-event-extra-price', 'Event Extra Service (Extra Service as Product that you can sell and it is not included on event package)', 'mep_event_extra_price_option', 'mep_events', 'normal', 'high' );

    add_meta_box( 'mep-event-ticket-type', 'Event Ticket Type', 'mep_event_ticket_type', 'mep_events', 'normal', 'high' );

    add_meta_box( 'mep-event-date', 'Event Date & Time', 'mep_event_date_meta_box_cb', 'mep_events', 'normal', 'high' );

    add_meta_box( 'mep-event-emails', 'Event Email text', 'mep_event_email_meta_box_cb', 'mep_events', 'normal', 'high' );

    add_meta_box( 'mep-event-template', 'Template', 'mep_event_template_meta_box_cb', 'mep_events', 'side', 'low' );

 

    add_meta_box( 'mep-event-faq-box', 'Event F.A.Q', 'mep_event_faq_meta_box_cb', 'mep_events', 'normal', 'high' );


 add_meta_box( 'mep-event-reg-on-off', 'Registration Status', 'mep_event_change_reg_status_cb', 'mep_events', 'side', 'low' );

  add_meta_box( 'mep-event-available-set-on-off', 'Show Available Seat Count?', 'mep_event_available_seat_cb', 'mep_events', 'side', 'low' );

}

function mep_event_change_reg_status_cb($post){
  $values = get_post_custom( $post->ID );
?>
<div class='sec'>
    <label for="mep_ev_20988"> <?php _e('Registration On/Off:','mage-eventpress'); ?> 
<label class="switch">
  <input type="checkbox" id="mep_ev_20988" name='mep_reg_status' <?php if(array_key_exists('mep_reg_status', $values)){ if($values['mep_reg_status'][0]=='on'){ echo 'checked'; } }else{ echo 'Checked'; } ?>/>
  <span class="slider round"></span>
</label> 
    </label>
</div>
  <?php
}

function mep_event_available_seat_cb($post){
  $values = get_post_custom( $post->ID );
?>
<div class='sec'>
    <label for="mep_ev_209882"> <?php _e('Show Available Seat?','mage-eventpress'); echo $values['mep_available_seat'][0]; ?> 
      <label class="switch">
        <input type="checkbox" id="mep_ev_209882" name='mep_available_seat' <?php if(array_key_exists('mep_available_seat', $values)){ if($values['mep_available_seat'][0]=='on'){ echo 'checked'; } }else{ echo 'Checked'; } ?>/>
        <span class="slider round"></span>
      </label> 
    </label>
</div>
  <?php
}

add_action('save_post','mep_reg_status_meta_save');
function mep_reg_status_meta_save($post_id){
  
if($_POST['mep_reg_status']){
    $mep_reg_status     = strip_tags($_POST['mep_reg_status']);
}else{
  $mep_reg_status     = 'off';
}  


if($_POST['mep_available_seat']){
    $mep_available_seat     = strip_tags($_POST['mep_available_seat']);
}else{
  $mep_available_seat     = 'off';
}

$update_ava_seat    = update_post_meta( $post_id, 'mep_available_seat', $mep_available_seat);
$update_seat        = update_post_meta( $post_id, 'mep_reg_status', $mep_reg_status);
}

 





function mep_event_venue_meta_box_cb($post){
$values = get_post_custom( $post->ID );
$user_api = mep_get_option( 'google-map-api', 'general_setting_sec', '');

?>

<div class='sec'>
    <label for="mep_ev_2"> <?php _e('Location/Venue:','mage-eventpress'); ?> </label>
    <span><input id='mep_ev_2' type="text" name='mep_location_venue' value='<?php if(array_key_exists('mep_location_venue', $values)){ echo $values['mep_location_venue'][0]; } ?>'> </span>
</div>




<div class='sec'>
    <label for="mep_ev_3"> <?php _e('Street:','mage-eventpress'); ?> </label>
    <span><input id='mep_ev_3' type="text" name='mep_street' value='<?php if(array_key_exists('mep_street', $values)){ echo $values['mep_street'][0]; } ?>'> </span>
</div>



<div class='sec'>
    <label for="mep_ev_4"> <?php _e('City:','mage-eventpress'); ?> </label>
    <span><input id='mep_ev_4' type="text" name='mep_city' value='<?php if(array_key_exists('mep_city', $values)){ echo $values['mep_city'][0]; } ?>'> </span>
</div>



<div class='sec'>
    <label for="mep_ev_5"> <?php _e('State:','mage-eventpress'); ?> </label>
    <span><input id='mep_ev_5' type="text" name='mep_state' value='<?php if(array_key_exists('mep_state', $values)){ echo $values['mep_state'][0]; } ?>'> </span>
</div>



<div class='sec'>
    <label for="mep_ev_6"> <?php _e('Postcode:','mage-eventpress'); ?> </label>
    <span><input id='mep_ev_6' type="text" name='mep_postcode' value='<?php if(array_key_exists('mep_postcode', $values)){ echo $values['mep_postcode'][0]; } ?>'> </span>
</div>



<div class='sec'>
    <label for="mep_ev_7"> <?php _e('Country:','mage-eventpress'); ?> </label>
    <span><input id='mep_ev_7' type="text" name='mep_country' value='<?php if(array_key_exists('mep_country', $values)){ echo $values['mep_country'][0]; } ?>'> </span>
</div>

<?php 
if($user_api){
?>
<div class='sec'>
    <label for="mep_ev_989"> <?php _e('Show Google Map:','mage-eventpress'); ?> </label>
    <span><input style='text-align: left;width: auto;' id='mep_ev_989' type="checkbox" name='mep_sgm' value='1' <?php if(array_key_exists('mep_sgm', $values)){ $mep_sgm = $values['mep_sgm'][0]; if($mep_sgm==1){ echo 'checked'; } } ?> > Yes</span>
</div>
<div class='sec'>
<input id="pac-input" name='location_name' value='<?php //echo $values['location_name'][0]; ?>'/>
</div>


<input type="hidden" class="form-control" required name="latitude" value="<?php if(array_key_exists('latitude', $values)){ echo $values['latitude'][0]; } ?>">
<input type="hidden" class="form-control" required name="longitude" value="<?php if(array_key_exists('longitude', $values)){ echo $values['longitude'][0]; } ?>">



<div id="map"></div>

<?php 
}else{
    echo "<span class=mep_status><span class=err>No Google MAP API Key Found. Please enter API KEY <a href=".get_site_url()."/wp-admin/options-general.php?page=mep_event_settings_page>Here</a></span></span>";
}



if(array_key_exists('latitude', $values)){
    $lat = $values['latitude'][0];
}else{ $lat = '37.0902'; }


if(array_key_exists('longitude', $values)){
    $lon = $values['longitude'][0];
}else{ $lon = '95.7129'; }

?>
<script>


function initMap() {
  var map = new google.maps.Map(document.getElementById('map'), {
    center: {
      lat: <?php echo $lat; ?>,
      lng: <?php echo $lon; ?>
    },
    zoom: 17
  });



  var input = /** @type {!HTMLInputElement} */ (
    document.getElementById('pac-input'));

  var types = document.getElementById('type-selector');
  map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
  map.controls[google.maps.ControlPosition.TOP_LEFT].push(types);

  var autocomplete = new google.maps.places.Autocomplete(input);
  autocomplete.bindTo('bounds', map);

  var infowindow = new google.maps.InfoWindow();
  var marker = new google.maps.Marker({
    map: map,
    anchorPoint: new google.maps.Point(0, -29),
    draggable: true,
    position: {lat: <?php echo $lat; ?>, lng: <?php echo $lon; ?>}
  });

  google.maps.event.addListener(marker, 'dragend', function() {
     document.getElementsByName('latitude')[0].value = marker.getPosition().lat();
     document.getElementsByName('longitude')[0].value = marker.getPosition().lng();
  })



  autocomplete.addListener('place_changed', function() {
    infowindow.close();
    marker.setVisible(false);
    var place = autocomplete.getPlace();
    if (!place.geometry) {
      window.alert("Autocomplete's returned place contains no geometry");
      return;
    }

    // If the place has a geometry, then present it on a map.
    if (place.geometry.viewport) {
      map.fitBounds(place.geometry.viewport);
    } else {
      map.setCenter(place.geometry.location);
      map.setZoom(17); // Why 17? Because it looks good.
    }
    marker.setIcon( /** @type {google.maps.Icon} */ ({
      url: 'http://maps.google.com/mapfiles/ms/icons/red.png',
      size: new google.maps.Size(71, 71),
      origin: new google.maps.Point(0, 0),
      anchor: new google.maps.Point(17, 34),
      scaledSize: new google.maps.Size(35, 35)
    }));
    marker.setPosition(place.geometry.location);
    marker.setVisible(true);

    var address = '';
    if (place.address_components) {
      address = [
        (place.address_components[0] && place.address_components[0].short_name || ''),
        (place.address_components[1] && place.address_components[1].short_name || ''),
        (place.address_components[2] && place.address_components[2].short_name || '')
      ].join(' ');
    }

    var latitude = place.geometry.location.lat();
    var longitude = place.geometry.location.lng();

    $("input[name=coordinate]").val(address);
    $("input[name=latitude]").val(latitude);
    $("input[name=longitude]").val(longitude);

    //infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
    //infowindow.open(map, marker);
  });
}
google.maps.event.addDomListener(window, "load", initMap);
</script>
<?php
}



function mep_event_price_meta_box_cb($post){
$values = get_post_custom( $post->ID );
?>


  <table id="" width="100%">
  <thead>
    <tr>
      <th width="20%"><?php _e('Price Label','mage-eventpress'); ?></th>
      <th width="20%"><?php _e('Price','mage-eventpress'); ?></th>
      <th width="20%"><?php _e('Quantity','mage-eventpress'); ?></th>
      <th width="20%"><?php _e('Input Type','mage-eventpress'); ?></th>
      <th width="20%"><?php _e('Show Quantity Box','mage-eventpress'); ?></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><input id='mep_ev_8' type="text" name='mep_price_label' value='<?php if(array_key_exists('mep_price_label', $values)){ echo $values['mep_price_label'][0];} ?>'></td>
      <td><input id='mep_ev_9' type="number" name='_price' required min='0' value='<?php if(array_key_exists('_price', $values)){ echo $values['_price'][0]; } else{ echo 0; } ?>'></td>
      <td><input id='mep_ev_1' type="number" name='mep_total_seat' value='<?php if(array_key_exists('mep_total_seat', $values)){ echo $values['mep_total_seat'][0]; } ?>'> </td>
      <td>  <?php if(array_key_exists('qty_box_type', $values)){ $qty_typec = $values['qty_box_type'][0]; }else{ $qty_typec=""; } ?>
      <select name="qty_box_type" id="mep_ev_9800" class=''>
    <option value="inputbox" <?php if($qty_typec=='inputbox'){ echo "Selected"; } ?>><?php _e('Input Box','mage-eventpress'); ?></option>
    <option value="dropdown" <?php if($qty_typec=='dropdown'){ echo "Selected"; } ?>><?php _e('Dropdown List','mage-eventpress'); ?></option>
      </select></td>
      <td>    <span><input style='text-align: left;width: auto;' id='mep_ev_98' type="checkbox" name='mep_sqi' value='1' <?php if(array_key_exists('mep_sqi', $values)){ $sqi = $values['mep_sqi'][0]; }else{ $sqi =0; } if($sqi==1){ echo 'checked'; } ?> > <?php _e('Yes','mage-eventpress'); ?></span></td>
    </tr>
  </tbody>
</table>







<?php
}


















function mep_event_faq_meta_box_cb() {
  global $post;
  $mep_event_faq = get_post_meta($post->ID, 'mep_event_faq', true);
  wp_nonce_field( 'mep_event_faq_nonce', 'mep_event_faq_nonce' );
  ?>
<script type="text/javascript">
  jQuery(document).ready(function( $ ){
    $( '#add-faq-row' ).on('click', function() {
      var row = $( '.empty-row-faq.screen-reader-text' ).clone(true);
      row.removeClass( 'empty-row-faq screen-reader-text' );
      row.insertBefore( '#repeatable-fieldset-faq-one tbody>tr:last' );
      return false;
    });
    
    $( '.remove-faq-row' ).on('click', function() {
      $(this).parents('tr').remove();
      return false;
    });
  });
  </script>
  
  <table id="repeatable-fieldset-faq-one" width="100%">

  <tbody>
  <?php
  if ( $mep_event_faq ) :
  foreach ( $mep_event_faq as $field ) {
  ?>
  <tr>
    <td>
    <div id='mep_event_faq_r' class="">
      <input placeholder="FAQ Title" type="text" class="mep-faq-input" value="<?php if($field['mep_faq_title'] != '') echo esc_attr( $field['mep_faq_title'] ); ?>" name="mep_faq_title[]">
      <textarea placeholder="FAQ Contents" name="mep_faq_content[]" id="" cols="50" rows="4" class="mep-faq-input"><?php if($field['mep_faq_content'] != '') echo esc_attr( $field['mep_faq_content'] ); ?></textarea>
      <a class="button remove-faq-row" href="#"><?php _e('Remove','mage-eventpress'); ?></a>
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
  <tr class="empty-row-faq screen-reader-text">
    <td>
    <div id='mep_event_faq_r' class="">
      <input placeholder="FAQ Title" type="text" class="mep-faq-input" name="mep_faq_title[]">
      <textarea placeholder="FAQ Contents" name="mep_faq_content[]" id="" cols="50" rows="4" class="mep-faq-input"></textarea>
      <a class="button remove-faq-row" href="#"><?php _e('Remove','mage-eventpress'); ?></a>
    </div>
      
    </td>
    
  </tr>
  </tbody>
  </table>
  <p><a id="add-faq-row" class="button" href="#"><?php _e('Add New F.A.Q','mage-eventpress'); ?></a></p>
  
  <?php
}



add_action('save_post', 'mep_event_faq_save');
function mep_event_faq_save($post_id) {
  global $wpdb;
  
  
  if ( ! isset( $_POST['mep_event_faq_nonce'] ) ||
  ! wp_verify_nonce( $_POST['mep_event_faq_nonce'], 'mep_event_faq_nonce' ) )
    return;
  
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
    return;
  
  if (!current_user_can('edit_post', $post_id))
    return;
  
  $old = get_post_meta($post_id, 'mep_event_faq', true);
  $new = array();
  // $options = hhs_get_sample_options();
  
  $names = $_POST['mep_faq_title'];
  $cntent = $_POST['mep_faq_content'];

  $order_id = 0;
  $count = count( $names );
  
  for ( $i = 0; $i < $count; $i++ ) {
    
    if ( $names[$i] != '' ) :
      $new[$i]['mep_faq_title'] = stripslashes( strip_tags( $names[$i] ) );
      endif;

    if ( $cntent[$i] != '' ) :
      $new[$i]['mep_faq_content'] = stripslashes( strip_tags( $cntent[$i] ) );
      endif;

  }

  if ( !empty( $new ) && $new != $old )
    update_post_meta( $post_id, 'mep_event_faq', $new );
  elseif ( empty($new) && $old )
    delete_post_meta( $post_id, 'mep_event_faq', $old );
}













function mep_event_extra_price_option() {
  global $post;
  $mep_events_extra_prices = get_post_meta($post->ID, 'mep_events_extra_prices', true);
  wp_nonce_field( 'mep_events_extra_price_nonce', 'mep_events_extra_price_nonce' );
  ?>
  <script type="text/javascript">
  jQuery(document).ready(function( $ ){
    $( '#add-row' ).on('click', function() {
      var row = $( '.empty-row.screen-reader-text' ).clone(true);
      row.removeClass( 'empty-row screen-reader-text' );
      row.insertBefore( '#repeatable-fieldset-one tbody>tr:last' );
      return false;
    });
    
    $( '.remove-row' ).on('click', function() {
      $(this).parents('tr').remove();
      return false;
    });
  });
  </script>
  
  <table id="repeatable-fieldset-one" width="100%">
  <thead>
    <tr>
      <th width="30%"><?php _e('Extra Service Name','mage-eventpress'); ?></th>
      <th width="30%"><?php _e('Service Price','mage-eventpress'); ?></th>
      <th width="20%"><?php _e('Available Qty','mage-eventpress'); ?></th>
      <th width="10%"><?php _e('Qty Box Type','mage-eventpress'); ?></th>
      <th width="10%"></th>
    </tr>
  </thead>
  <tbody>
  <?php
  
  if ( $mep_events_extra_prices ) :
  
  foreach ( $mep_events_extra_prices as $field ) {
    $qty_type = esc_attr( $field['option_qty_type'] );
  ?>
  <tr>
    <td><input type="text" class="widefat" name="option_name[]" value="<?php if($field['option_name'] != '') echo esc_attr( $field['option_name'] ); ?>" /></td>

    <td><input type="number" class="widefat" name="option_price[]" value="<?php if ($field['option_price'] != '') echo esc_attr( $field['option_price'] ); else echo ''; ?>" /></td>

    <td><input type="number" class="widefat" name="option_qty[]" value="<?php if ($field['option_qty'] != '') echo esc_attr( $field['option_qty'] ); else echo ''; ?>" /></td>

 <td align="center">
<select name="option_qty_type[]" id="mep_ev_9800kj8" class=''>
    <option value="inputbox" <?php if($qty_type=='inputbox'){ echo "Selected"; } ?>><?php _e('Input Box','mage-eventpress'); ?></option>
    <option value="dropdown" <?php if($qty_type=='dropdown'){ echo "Selected"; } ?>><?php _e('Dropdown List','mage-eventpress'); ?></option>
</select>
    </td> 
    <td><a class="button remove-row" href="#"><?php _e('Remove','mage-eventpress'); ?></a></td>
  </tr>
  <?php
  }
  else :
  // show a blank one
 endif; 
 ?>
  
  <!-- empty hidden one for jQuery -->
  <tr class="empty-row screen-reader-text">
    <td><input type="text" class="widefat" name="option_name[]" /></td>
    <td><input type="number" class="widefat" name="option_price[]" value="" /></td>
    <td><input type="number" class="widefat" name="option_qty[]" value="" /></td>
    
<td><select name="option_qty_type[]" id="mep_ev_9800kj8" class=''>
  <option value=""><?php _e('Please Select Type','mage-eventpress'); ?></option>
    <option value="inputbox"><?php _e('Input Box','mage-eventpress'); ?></option>
    <option value="dropdown"><?php _e('Dropdown List','mage-eventpress'); ?></option>
</select></td>
    <td><a class="button remove-row" href="#"><?php _e('Remove','mage-eventpress'); ?></a></td>
    
  </tr>
  </tbody>
  </table>
  <p><a id="add-row" class="button" href="#"><?php _e('Add Extra Price','mage-eventpress'); ?></a></p>
  <?php
}














function mep_event_ticket_type() {
  global $post;
  $mep_event_ticket_type = get_post_meta($post->ID, 'mep_event_ticket_type', true);
  wp_nonce_field( 'mep_event_ticket_type_nonce', 'mep_event_ticket_type_nonce' );
  ?>
  <script type="text/javascript">
  jQuery(document).ready(function( $ ){
    $( '#add-row-t' ).on('click', function() {
      var row = $( '.empty-row-t.screen-reader-text' ).clone(true);
      row.removeClass( 'empty-row-t screen-reader-text' );
      row.insertBefore( '#repeatable-fieldset-one-t tbody>tr:last' );
      return false;
    });
    
    $( '.remove-row-t' ).on('click', function() {
      $(this).parents('tr').remove();
      return false;
    });
  });
  </script>
  
  <table id="repeatable-fieldset-one-t" width="100%">
  <thead>
    <tr>
      <th width="30%"><?php _e('Ticket Type Name','mage-eventpress'); ?></th>
      <th width="30%"><?php _e('Ticket Price','mage-eventpress'); ?></th>
      <th width="20%"><?php _e('Available Qty','mage-eventpress'); ?></th>
      <th width="10%"><?php _e('Qty Box Type','mage-eventpress'); ?></th>
      <th width="10%"></th>
    </tr>
  </thead>
  <tbody>
  <?php
  
  if ( $mep_event_ticket_type ) :
  
  foreach ( $mep_event_ticket_type as $field ) {
    $qty_t_type = esc_attr( $field['option_qty_t_type'] );
  ?>
  <tr>
    <td><input type="text" class="widefat" name="option_name_t[]" value="<?php if($field['option_name_t'] != '') echo esc_attr( $field['option_name_t'] ); ?>" /></td>

    <td><input type="number" class="widefat" name="option_price_t[]" value="<?php if ($field['option_price_t'] != '') echo esc_attr( $field['option_price_t'] ); else echo ''; ?>" /></td>

    <td><input type="number" class="widefat" name="option_qty_t[]" value="<?php if ($field['option_qty_t'] != '') echo esc_attr( $field['option_qty_t'] ); else echo ''; ?>" /></td>

<td><select name="option_qty_t_type[]" id="mep_ev_9800kj8" class=''>
    <option value="inputbox" <?php if($qty_t_type=='inputbox'){ echo "Selected"; } ?>><?php _e('Input Box','mage-eventpress'); ?></option>
    <option value="dropdown" <?php if($qty_t_type=='dropdown'){ echo "Selected"; } ?>><?php _e('Dropdown List','mage-eventpress'); ?></option>
</select></td>

    <td><a class="button remove-row-t" href="#"><?php _e('Remove','mage-eventpress'); ?></a></td>
  </tr>
  <?php
  }
  else :
  // show a blank one
 endif; 
 ?>
  
  <!-- empty hidden one for jQuery -->
  <tr class="empty-row-t screen-reader-text">
    <td><input type="text" class="widefat" name="option_name_t[]" /></td>
    <td><input type="number" class="widefat" name="option_price_t[]" value="" /></td>
    <td><input type="number" class="widefat" name="option_qty_t[]" value="" /></td>
    <td><select name="option_qty_t_type[]" id="mep_ev_9800kj8" class=''><option value=""><?php _e('Please Select Type','mage-eventpress'); ?></option><option value="inputbox"><?php _e('Input Box','mage-eventpress'); ?></option><option value="dropdown"><?php _e('Dropdown List','mage-eventpress'); ?></option></select></td>    
    <td><a class="button remove-row-t" href="#"><?php _e('Remove','mage-eventpress'); ?></a></td>
  </tr>
  </tbody>
  </table>
  <p><a id="add-row-t" class="button" href="#"><?php _e('Add New Ticket Type','mage-eventpress'); ?></a></p>
  <?php
}













function mep_event_date_meta_box_cb($post){
$values = get_post_custom( $post->ID );
?>

<div class='sec'>
    <label for="event_start_date"> <?php _e('Start Date & Time:','mage-eventpress'); ?> </label>
    <span><input class='event_start' id='event_start_date' type="text" name='mep_event_start_date' value='<?php if(array_key_exists('mep_event_start_date', $values)){ echo $values['mep_event_start_date'][0]; } ?>'> </span>
</div>


<div class='sec'>
    <label for="event_end_date"> <?php _e('End Date & Time:','mage-eventpress'); ?> </label>
    <span><input class='event_end' id='event_end_date' type="text" name='mep_event_end_date' value='<?php if(array_key_exists('mep_event_end_date', $values)){ echo $values['mep_event_end_date'][0]; } ?>'> </span>
</div>


<?php
}



function mep_event_email_meta_box_cb($post){
$values = get_post_custom( $post->ID );
?>
<div class='sec'>
    <label for="event_start_date"> <?php _e('Confirmation Email Text:','mage-eventpress'); ?> </label>
    <span><textarea style='border: 1px solid #ddd;width: 100%;min-height: 200px;margin: 10px 0;padding: 5px;' class='' id='' type="text" name='mep_event_cc_email_text'><?php if(array_key_exists('mep_event_cc_email_text', $values)){ echo $values['mep_event_cc_email_text'][0]; } ?></textarea> </span>
</div>
<?php
}








function mep_event_template_meta_box_cb($post){
$values = get_post_custom( $post->ID );
$global_template = mep_get_option( 'mep_global_single_template', 'general_setting_sec', 'theme-2');
if(array_key_exists('mep_event_template', $values)){
$current_template = $values['mep_event_template'][0];
}else{
    $current_template='';
}
if($current_template){
  $_current_template = $current_template;
}else{
  $_current_template = $global_template;
}
?>
<div class='sec'>
    <span><?php event_single_template_list($_current_template); ?></span>
</div>
<?php
}

















add_action('save_post', 'mep_events_ticket_type_save');
function mep_events_ticket_type_save($post_id) {
  global $wpdb;
  $table_name = $wpdb->prefix . 'mep_event_ticket_type';
  
  if ( ! isset( $_POST['mep_event_ticket_type_nonce'] ) ||
  ! wp_verify_nonce( $_POST['mep_event_ticket_type_nonce'], 'mep_event_ticket_type_nonce' ) )
    return;
  
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
    return;
  
  if (!current_user_can('edit_post', $post_id))
    return;
  
  $old = get_post_meta($post_id, 'mep_event_ticket_type', true);
  $new = array();
  // $options = hhs_get_sample_options();
  
  $names = $_POST['option_name_t'];
  // $selects = $_POST['select'];
  $urls = $_POST['option_price_t'];
  $qty = $_POST['option_qty_t'];
  $qty_type = $_POST['option_qty_t_type'];
  // $required = $_POST['option_required_t'];
  // $total_sold = $_POST['option_sold'];

  $order_id = 0;
  $count = count( $names );
  
  for ( $i = 0; $i < $count; $i++ ) {
    
    if ( $names[$i] != '' ) :
      $new[$i]['option_name_t'] = stripslashes( strip_tags( $names[$i] ) );
      endif;

    if ( $urls[$i] != '' ) :
      $new[$i]['option_price_t'] = stripslashes( strip_tags( $urls[$i] ) );
      endif;

    if ( $qty[$i] != '' ) :
      $new[$i]['option_qty_t'] = stripslashes( strip_tags( $qty[$i] ) );
      endif;

    if ( $qty_type[$i] != '' ) :
      $new[$i]['option_qty_t_type'] = stripslashes( strip_tags( $qty_type[$i] ) );
      endif;

    // if ( $required[$i] != '' ) :
    //   $new[$i]['option_required_t'] = stripslashes( strip_tags( $required[$i] ) );
    //   endif;

 

    $opt_name =  $post_id.str_replace(' ', '', $names[$i]);

    // update_post_meta( $post_id, "mep_xtra_$opt_name",0 );

  }

  if ( !empty( $new ) && $new != $old )
    update_post_meta( $post_id, 'mep_event_ticket_type', $new );
  elseif ( empty($new) && $old )
    delete_post_meta( $post_id, 'mep_event_ticket_type', $old );
}











add_action('save_post', 'mep_events_repeatable_meta_box_save');
function mep_events_repeatable_meta_box_save($post_id) {
  global $wpdb;
  $table_name = $wpdb->prefix . 'event_extra_options';
  if ( ! isset( $_POST['mep_events_extra_price_nonce'] ) ||
  ! wp_verify_nonce( $_POST['mep_events_extra_price_nonce'], 'mep_events_extra_price_nonce' ) )
    return;
  
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
    return;
  
  if (!current_user_can('edit_post', $post_id))
    return;
  
  $old = get_post_meta($post_id, 'mep_events_extra_prices', true);
  $new = array();
  // $options = hhs_get_sample_options();
  
  $names = $_POST['option_name'];
  // $selects = $_POST['select'];
  $urls = $_POST['option_price'];
  $qty = $_POST['option_qty'];
  $qty_type = $_POST['option_qty_type'];
  // $required = $_POST['option_required'];
  // $total_sold = $_POST['option_sold'];

  $order_id = 0;
  $count = count( $names );
  
  for ( $i = 0; $i < $count; $i++ ) {
    
    if ( $names[$i] != '' ) :
      $new[$i]['option_name'] = stripslashes( strip_tags( $names[$i] ) );
      endif;

    if ( $urls[$i] != '' ) :
      $new[$i]['option_price'] = stripslashes( strip_tags( $urls[$i] ) );
      endif;

    if ( $qty[$i] != '' ) :
      $new[$i]['option_qty'] = stripslashes( strip_tags( $qty[$i] ) );
      endif;

    if ( $qty_type[$i] != '' ) :
      $new[$i]['option_qty_type'] = stripslashes( strip_tags( $qty_type[$i] ) );
      endif;

    // if ( $required[$i] != '' ) :
    //   $new[$i]['option_required'] = stripslashes( strip_tags( $required[$i] ) );
    //   endif;

 

    $opt_name =  $post_id.str_replace(' ', '', $names[$i]);

    // update_post_meta( $post_id, "mep_xtra_$opt_name",0 );

  }

  if ( !empty( $new ) && $new != $old )
    update_post_meta( $post_id, 'mep_events_extra_prices', $new );
  elseif ( empty($new) && $old )
    delete_post_meta( $post_id, 'mep_events_extra_prices', $old );
}










add_action('save_post','mep_events_meta_save');
function mep_events_meta_save($post_id){
    global $post; 
if($post){
    $pid = $post->ID;
    if ($post->post_type != 'mep_events'){
        return;
    }
}else{
    $pid='';
}
    //if you get here then it's your post type so do your thing....
    if(isset($_POST['mep_total_seat'])){
    $seat                           = strip_tags($_POST['mep_total_seat']);
    $mep_location_venue             = strip_tags($_POST['mep_location_venue']);
    $mep_street                     = strip_tags($_POST['mep_street']);
    $mep_city                       = strip_tags($_POST['mep_city']);
    $mep_state                      = strip_tags($_POST['mep_state']);
    $mep_postcode                   = strip_tags($_POST['mep_postcode']);
    $mep_country                    = strip_tags($_POST['mep_country']);
    $mep_price_label                = strip_tags($_POST['mep_price_label']);
    $mep_sqi                        = strip_tags($_POST['mep_sqi']);
    $qty_box_type                   = strip_tags($_POST['qty_box_type']);
    $mep_sgm                        = strip_tags($_POST['mep_sgm']);
    $_price                         = strip_tags($_POST['_price']);
    $mep_event_start_date           = strip_tags($_POST['mep_event_start_date']);
    $mep_event_end_date             = strip_tags($_POST['mep_event_end_date']);
    $mep_event_cc_email_text             = strip_tags($_POST['mep_event_cc_email_text']);

    $latitude                       = strip_tags($_POST['latitude']);
    $longitude                      = strip_tags($_POST['longitude']);
    $location_name                  = strip_tags($_POST['location_name']);

    $mep_full_name                  = strip_tags($_POST['mep_full_name']);
    $mep_reg_email                  = strip_tags($_POST['mep_reg_email']);
    $mep_reg_phone                  = strip_tags($_POST['mep_reg_phone']);
    $mep_reg_address                = strip_tags($_POST['mep_reg_address']);
    $mep_reg_designation            = strip_tags($_POST['mep_reg_designation']);
    $mep_reg_website                = strip_tags($_POST['mep_reg_website']);
    $mep_reg_veg                    = strip_tags($_POST['mep_reg_veg']);
    $mep_reg_company                = strip_tags($_POST['mep_reg_company']);
    $mep_reg_gender                 = strip_tags($_POST['mep_reg_gender']);
    $mep_reg_tshirtsize             = strip_tags($_POST['mep_reg_tshirtsize']);
    $mep_reg_tshirtsize_list        = strip_tags($_POST['mep_reg_tshirtsize_list']);
    $mep_event_template             = strip_tags($_POST['mep_event_template']);









$update_reg_name                   = update_post_meta( $pid, 'mep_full_name', $mep_full_name);
$update_reg_email                   = update_post_meta( $pid, 'mep_reg_email', $mep_reg_email);
$update_reg_phone       = update_post_meta( $pid, 'mep_reg_phone', $mep_reg_phone);
$update_reg_address     = update_post_meta( $pid, 'mep_reg_address', $mep_reg_address);
$update_reg_desg        = update_post_meta( $pid, 'mep_reg_designation', $mep_reg_designation);
$update_reg_web         = update_post_meta( $pid, 'mep_reg_website', $mep_reg_website);
$update_reg_veg         = update_post_meta( $pid, 'mep_reg_veg', $mep_reg_veg);
$update_reg_comapny     = update_post_meta( $pid, 'mep_reg_company', $mep_reg_company);
$update_reg_gender      = update_post_meta( $pid, 'mep_reg_gender', $mep_reg_gender);
$update_tshirtsize      = update_post_meta( $pid, 'mep_reg_tshirtsize', $mep_reg_tshirtsize);
$mep_reg_tshirtsize_list      = update_post_meta( $pid, 'mep_reg_tshirtsize_list', $mep_reg_tshirtsize_list);
$update_template        = update_post_meta( $pid, 'mep_event_template', $mep_event_template);





$mep_event_ticket_type = get_post_meta($pid, 'mep_event_ticket_type', true);


if($mep_event_ticket_type){
  $st_msg = 'no';
  $seat = "";
  $_price =0;
}else{
  $st_msg = 'yes';
  $_price = $_price;
    $seat = $seat;

}




    $update_seat                    = update_post_meta( $pid, 'mep_total_seat', $seat);

    $update_seat_stock_status       = update_post_meta( $pid, '_manage_stock', $st_msg);

    $update_seat_stock          = update_post_meta( $pid, '_stock', $seat);
$sts_msg = update_post_meta( $pid, '_stock_msg', 'new');
// $ttl_booking = update_post_meta( $pid, 'total_booking', '0');

    $longitude              = update_post_meta( $pid, 'longitude', $longitude);
    $latitude               = update_post_meta( $pid, 'latitude', $latitude);
    $location_name          = update_post_meta( $pid, 'location_name', $location_name);



    $update_location        = update_post_meta( $pid, 'mep_location_venue', $mep_location_venue);
    $update_mep_street      = update_post_meta( $pid, 'mep_street', $mep_street);

    $update_city          = update_post_meta( $pid, 'mep_city', $mep_city);
    $update_mep_state       = update_post_meta( $pid, 'mep_state', $mep_state);
    $update_postcode        = update_post_meta( $pid, 'mep_postcode', $mep_postcode);
    $update_conuntry        = update_post_meta( $pid, 'mep_country', $mep_country);
    $update_sqi     = update_post_meta( $pid, 'mep_sqi', $mep_sqi);
    $qty_box_type     = update_post_meta( $pid, 'qty_box_type', $qty_box_type);
    $update_mep_sgm     = update_post_meta( $pid, 'mep_sgm', $mep_sgm);
    $update_price_label     = update_post_meta( $pid, 'mep_price_label', $mep_price_label);
    $update_price         = update_post_meta( $pid, '_price', $_price);
    $update_start         = update_post_meta( $pid, 'mep_event_start_date', $mep_event_start_date);
    $update_virtual       = update_post_meta( $pid, '_virtual', 'yes');
    $update_end           = update_post_meta( $pid, 'mep_event_end_date', $mep_event_end_date);
    $mep_event_cc_email_text           = update_post_meta( $pid, 'mep_event_cc_email_text', $mep_event_cc_email_text);
    }
}


add_action( 'add_meta_boxes', 'mep_meta_box_add' );
function mep_meta_box_add(){
    add_meta_box( 'my-meta-box-id', 'Information', 'mep_meta_box_cb', 'mep_events_attendees', 'normal', 'high' );
}


function mep_meta_box_cb($post){
$values = get_post_custom( $post->ID );

$event_meta           = get_post_custom($values['ea_event_id'][0]);

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
?>

<div class="mep-attendee-sec-details">
<div class='sec'>
    <span class="ea-label"><?php _e('Event:','mage-eventpress'); ?> </span>
    <span class="ea-value"><?php echo $values['ea_event_name'][0]; ?> </span>
</div>

<div class='sec'>
    <span class="ea-label"><?php _e('UserID:','mage-eventpress'); ?> </span>
    <span class="ea-value"><?php echo $values['ea_user_id'][0]; ?></span>
</div>


<?php if($mep_full_name){ ?>
<div class='sec'>
    <span class="ea-label"><?php _e('Full Name:','mage-eventpress'); ?> </span>
    <span class="ea-value"><?php echo $values['ea_name'][0]; ?></span>
</div>
<?php } if($mep_reg_email){ ?>
<div class='sec'>
    <span class="ea-label"><?php _e('Email:','mage-eventpress'); ?> </span>
    <span class="ea-value"><?php echo $values['ea_email'][0]; ?></span>  
</div>
<?php } if($mep_reg_phone){ ?>
<div class='sec'>
    <span class="ea-label"><?php _e('Phone:','mage-eventpress'); ?> </span>
    <span class="ea-value"><?php echo $values['ea_phone'][0]; ?></span>  
</div>
<?php } if($mep_reg_address){ ?>
<div class='sec'>
    <span class="ea-label"><?php _e('Addres:','mage-eventpress'); ?> </span>
    <span class="ea-value">
    <?php echo $values['ea_address_1'][0]; ?>  
    </span>  
</div>
<?php } if($mep_reg_gender){ ?>
<div class='sec'>
    <span class="ea-label"><?php _e('Gender:','mage-eventpress'); ?> </span>
    <span class="ea-value">
    <?php echo $values['ea_gender'][0]; ?>  
    </span>  
</div>
<?php } if($mep_reg_company){ ?>
<div class='sec'>
    <span class="ea-label"><?php _e('Company:','mage-eventpress'); ?> </span>
    <span class="ea-value">
    <?php echo $values['ea_company'][0]; ?>  
    </span>  
</div>
<?php } if($mep_reg_designation){ ?>
<div class='sec'>
    <span class="ea-label"><?php _e('Designation:','mage-eventpress'); ?> </span>
    <span class="ea-value">
    <?php echo $values['ea_desg'][0]; ?>  
    </span>  
</div>
<?php } if($mep_reg_website){ ?>
<div class='sec'>
    <span class="ea-label"><?php _e('Website:','mage-eventpress'); ?> </span>
    <span class="ea-value">
    <?php echo $values['ea_website'][0]; ?>  
    </span>  
</div>

<?php } if($mep_reg_veg){ ?>
<div class='sec'>
    <span class="ea-label"><?php _e('Vegetarian?:','mage-eventpress'); ?> </span>
    <span class="ea-value">
    <?php echo $values['ea_vegetarian'][0]; ?>  
    </span>  
</div>

<?php } if($mep_reg_tshirtsize){ ?>
<div class='sec'>
    <span class="ea-label"><?php _e('T-Shirt Size?:','mage-eventpress'); ?> </span>
    <span class="ea-value">
    <?php echo $values['ea_tshirtsize'][0]; ?>  
    </span>  
</div>
<?php } ?>

<?php 
$mep_form_builder_data = get_post_meta($values['ea_event_id'][0], 'mep_form_builder_data', true);
  if ( $mep_form_builder_data ) {
    foreach ( $mep_form_builder_data as $_field ) {
        if ( $mep_user_ticket_type[$iu] != '' ) :
          $user[$iu][$_field['mep_fbc_id']] = stripslashes( strip_tags( $_POST[$_field['mep_fbc_id']][$iu] ) );
          endif; 

?>
<div class='sec'>
    <span class="ea-label"><?php echo $_field['mep_fbc_label']; ?>:</span>
    <span class="ea-value">
    <?php $vname = "ea_".$_field['mep_fbc_id']; echo $values[$vname][0]; ?>  
    </span>  
</div>
<?php

    }
  }
?>



<div class='sec'>
    <span class="ea-label"><?php _e('Ticket Type:','mage-eventpress'); ?> </span>
    <span class="ea-value">
    <?php echo $values['ea_ticket_type'][0]; ?>  
    </span>  
</div>
<div class='sec'>
    <span class="ea-label"><?php _e('Order ID:','mage-eventpress'); ?> </span>
    <span class="ea-value">
    <?php echo $values['ea_order_id'][0]; ?>  
    </span>  
</div>
</div>
<div class='sec'>
    <span>
    <a href="<?php echo get_site_url(); ?>/wp-admin/post.php?post=<?php echo $values['ea_order_id'][0]; ?>&action=edit" class='button button-primary button-large'><?php _e('View Order','mage-eventpress'); ?></a>
    </span>  
</div>
<?php
}