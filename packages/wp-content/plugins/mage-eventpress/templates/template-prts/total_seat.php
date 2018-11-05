<?php 
add_action('mep_event_seat','mep_ev_seat');


function mep_ev_seat(){
	global $post,$event_meta,$total_book;
$mep_event_ticket_type = get_post_meta($post->ID, 'mep_event_ticket_type', true);
// echo "Hello";
// print_r($mep_event_ticket_type);
if($mep_event_ticket_type){

$stc = 0;
$leftt = 0;


foreach ( $mep_event_ticket_type as $field ) {
$qm = $field['option_name_t'];
$tesqn = $post->ID.str_replace(' ', '', $qm);

$tesq = get_post_meta($post->ID,"mep_xtra_$tesqn",true);

$stc = $stc+$field['option_qty_t'];


$llft = ($field['option_qty_t'] - $tesq);
$leftt = $leftt+$llft;
}
?>
	<h5><strong><?php _e('Total Seat:','mage-eventpress'); ?></strong> <?php echo $stc; ?> (<strong><?php echo $leftt; ?></strong> Left)</h5>
<?php

}else{
	if($event_meta['mep_total_seat'][0]){ ?>
	<h5><strong><?php _e('Total Seat:','mage-eventpress'); ?></strong> <?php echo $event_meta['mep_total_seat'][0]; ?> (<strong><?php echo ($event_meta['mep_total_seat'][0]- $total_book); ?></strong> Left)</h5>
	<?php } 
}





}







