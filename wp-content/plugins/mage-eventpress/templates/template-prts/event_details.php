<?php 
add_action('mep_event_details','mep_ev_details');


function mep_ev_details(){
global $event_meta;	
the_content();
}