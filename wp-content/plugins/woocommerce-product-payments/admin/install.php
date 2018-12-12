<?php
if( !defined('ABSPATH') ){ exit();}
function wppg_free_network_install($networkwide) {
	global $wpdb;

	
	wppg_install_free();
}

function wppg_install_free()
{
	
}


register_activation_hook(WPPG_PLUGIN_FILE,'wppg_free_network_install');
?>