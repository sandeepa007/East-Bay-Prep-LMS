<?php
if (!function_exists('is_plugin_active_for_network')) {
    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
}
if (file_exists(dirname( __FILE__ ) . '../include/settings.php')) {
    require_once( dirname( __FILE__ ) . '../include/settings.php' );
}

?>

<div style="width: 100%">

<?php 
	dfm_wcpgpp_product_payments_settings(); 
?>
</div>