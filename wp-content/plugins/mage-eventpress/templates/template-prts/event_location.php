<?php 
add_action('mep_event_location','mep_ev_location');


function mep_ev_location(){
global $event_meta;	
?>
<p><?php echo $event_meta['mep_location_venue'][0]; ?>,</p>
					<?php if($event_meta['mep_street'][0]){ ?><p><?php echo $event_meta['mep_street'][0]; ?>,</p> <?php } ?> 
					<?php if($event_meta['mep_city'][0]){ ?> <p><?php echo $event_meta['mep_city'][0]; ?>,</p> <?php } ?>
					<?php if($event_meta['mep_state'][0]){ ?> <p><?php echo $event_meta['mep_state'][0]; ?>,</p> <?php } ?>
					<?php if($event_meta['mep_postcode'][0]){ ?> <p><?php echo $event_meta['mep_postcode'][0]; ?>,</p> <?php } ?>
					<?php if($event_meta['mep_country'][0]){ ?> <p><?php echo $event_meta['mep_country'][0]; ?></p> <?php } ?>
<?php
}




add_action('mep_event_location_venue','mep_ev_venue');
function mep_ev_venue(){
global $event_meta;	
?>
	<span><?php echo $event_meta['mep_location_venue'][0]; ?></span>
<?php
}


add_action('mep_event_location_street','mep_ev_street');
function mep_ev_street(){
global $event_meta;	
?>
	<span><?php echo $event_meta['mep_street'][0]; ?></span>
<?php
}


add_action('mep_event_location_city','mep_ev_city');
function mep_ev_city(){
global $event_meta;	
?>
	<span><?php echo $event_meta['mep_city'][0]; ?></span>
<?php
}


add_action('mep_event_location_state','mep_ev_state');
function mep_ev_state(){
global $event_meta;	
?>
	<span><?php echo $event_meta['mep_state'][0]; ?></span>
<?php
}


add_action('mep_event_location_postcode','mep_ev_postcode');
function mep_ev_postcode(){
global $event_meta;	
?>
	<span><?php echo $event_meta['mep_postcode'][0]; ?></span>
<?php
}



add_action('mep_event_location_country','mep_ev_country');
function mep_ev_country(){
global $event_meta;	
?>
	<span><?php echo $event_meta['mep_country'][0]; ?></span>
<?php
}