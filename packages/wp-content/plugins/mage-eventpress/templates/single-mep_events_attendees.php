<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php wp_head(); ?>
<style>
	.mep-reg-user-details {
    width: 400px;
    margin: 0 auto;
    border-top: 1px solid #ddd;
    border-left: 1px solid #ddd;
    background: #fff;
    border-right: 1px solid #ddd;
}

.mep-reg-user-details table tr td {
    border-color: #ddd;
        padding: 0px 10px;
    border-bottom: 1px solid #ddd;
}

.mep-reg-user-details table tr td img {
    border-radius: 100%;
    margin: 20px 0;
    width: 100px;
    height: auto;
}

.mep-reg-user-details table tr td h2 {
    font-size: 25px;
    margin: 0;
    padding: 0;
}

.mep-reg-user-details table tr td h3 {
    font-size: 14px;
    margin: 10px 0;
    padding: 0;
    font-weight: bold;
}

.mep-reg-user-details table tr td h4 {
    font-size: 16px;
}
.mep-reg-user-details table {
    width: 100%;
}
.mep-reg-user-details h4, .mep-reg-user-details h2 {
    padding: 0;
    margin: 0;
}
</style>	
</head>
<body>
<?php 
the_post();
$values = get_post_custom(get_the_id());


?>
	<div class="mep-wrapper">
	<div class="mep-reg-user-details">
		<table>
			<tr>
				<td colspan="2" align="center">
					<?php echo get_avatar( $values['ea_email'][0], 128 ); ?>
					<h2><?php echo $values['ea_name'][0]; ?></h2>
					<!-- <h3>ID: <?php //echo $values['ea_user_id'][0].$values['ea_order_id'][0].get_the_id(); ?></h3> -->
					<div id="bcTarget"></div>   
					<h4><?php echo $values['ea_event_name'][0]; ?></h4>
				</td>
			</tr>
			<?php if($values['ea_email'][0]){ ?>
			<tr>
				<td><?php _e('Email','mage-eventpress'); ?></td>
				<td><?php echo $values['ea_email'][0]; ?></td>
			</tr>
			<?php } if($values['ea_phone'][0]){ ?>
			<tr>
				<td><?php _e('Phone','mage-eventpress'); ?></td>
				<td><?php echo $values['ea_phone'][0]; ?></td>
			</tr>
			<?php } if($values['ea_address_1'][0]){ ?>
			<tr>
				<td><?php _e('Address','mage-eventpress'); ?></td>
				<td><?php echo $values['ea_address_1'][0]; ?> </td>
			</tr>
			<?php } if($values['ea_desg'][0]){ ?>
			<tr>
				<td><?php _e('Designation','mage-eventpress'); ?></td>
				<td><?php echo $values['ea_desg'][0]; ?></td>
			</tr>
			<?php } if($values['ea_company'][0]){ ?>
			<tr>
				<td><?php _e('Company','mage-eventpress'); ?></td>
				<td><?php echo $values['ea_company'][0]; ?></td>
			</tr>
			<?php } if($values['ea_website'][0]){ ?>
			<tr>
				<td><?php _e('Website','mage-eventpress'); ?></td>
				<td><?php echo $values['ea_website'][0]; ?> </td>
			</tr>
			<?php } if($values['ea_gender'][0]){ ?>
			<tr>
				<td><?php _e('Gender','mage-eventpress'); ?></td>
				<td><?php echo $values['ea_gender'][0]; ?> </td>
			</tr>

			<?php } if($values['ea_vegetarian'][0]){ ?>
			<tr>
				<td><?php _e('Vegetarian','mage-eventpress'); ?></td>
				<td><?php echo $values['ea_vegetarian'][0]; ?> </td>
			</tr>	
		

			<?php } if($values['ea_tshirtsize'][0]){ ?>
			<tr>
				<td><?php _e('T Shirt Size','mage-eventpress'); ?></td>
				<td><?php echo $values['ea_tshirtsize'][0]; ?> </td>
			</tr>	
		

			<?php } if($values['ea_ticket_type'][0]){ ?>
			<tr>
				<td><?php _e('Ticket Type','mage-eventpress'); ?></td>
				<td><?php echo $values['ea_ticket_type'][0]; ?> </td>
			</tr>	
			<?php } 
			$mep_form_builder_data = get_post_meta($values['ea_event_id'][0], 'mep_form_builder_data', true);
			  if ( $mep_form_builder_data ) {
			    foreach ( $mep_form_builder_data as $_field ) {
			        if ( $mep_user_ticket_type[$iu] != '' ) :
			          $user[$iu][$_field['mep_fbc_id']] = stripslashes( strip_tags( $_POST[$_field['mep_fbc_id']][$iu] ) );
			          endif; 
			$vname = "ea_".$_field['mep_fbc_id']; 
			$vals = $values[$vname][0];
			if($vals){
			?>
			<tr>
				<td><?php echo $_field['mep_fbc_label']; ?></td>
				<td><?php echo $vals; ?></td>
			</tr>	
		<?php
		}
	}
}
?>
		</table>
	</div>
</div>
<?php 



do_action('at_footer'); 
wp_footer();
?>
<script>
	jQuery("#bcTarget").barcode("<?php echo $values['ea_user_id'][0].$values['ea_order_id'][0].get_the_id(); ?>", "codabar");     
</script>
</body>
</html>