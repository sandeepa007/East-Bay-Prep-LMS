<?php 
add_action('mep_event_date','mep_ev_datetime');


function mep_ev_datetime(){
global $event_meta;	
?>
<p><?php echo date_i18n('D, d M Y h:i A', strtotime($event_meta['mep_event_start_date'][0]));  ?> - <?php echo  date_i18n('D, d M Y h:i A', strtotime($event_meta['mep_event_end_date'][0]));  ?></p>
<?php
}



add_action('mep_event_date_only','mep_ev_date');
function mep_ev_date(){
global $event_meta;	
?>
<p><?php echo date_i18n('d M Y', strtotime($event_meta['mep_event_start_date'][0])); ?> </p>
<?php
}

add_action('mep_event_time_only','mep_ev_time');
function mep_ev_time(){
global $event_meta;	
?>
<p><?php echo date_i18n('h:i A', strtotime($event_meta['mep_event_start_date'][0])); ?> </p>
<?php
}