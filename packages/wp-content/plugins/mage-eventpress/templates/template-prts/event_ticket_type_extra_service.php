<?php 
function mep_output_add_to_cart_custom_fields() {
do_action('mep_event_ticket_types');
do_action('mep_event_extra_service');
}
add_action( 'mep_event_ticket_type_extra_service', 'mep_output_add_to_cart_custom_fields', 10 );
