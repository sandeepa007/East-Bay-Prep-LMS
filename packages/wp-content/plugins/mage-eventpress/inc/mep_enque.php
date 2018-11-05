<?php 
// Enqueue Scripts for admin dashboard
add_action('admin_enqueue_scripts', 'mep_event_admin_scripts');
function mep_event_admin_scripts() {
  $user_api = mep_get_option( 'google-map-api', 'general_setting_sec', '');
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_script('jquery-ui-core');   


    wp_enqueue_style('mep-admin-style',plugin_dir_url( __DIR__ ).'css/admin_style.css',array());
    
       

    

if($user_api){
      wp_enqueue_script('gmap-libs','https://maps.googleapis.com/maps/api/js?key='.$user_api.'&libraries=places&callback=initMap',array('jquery','gmap-scripts'),1,true);
    }

}


function add_admin_scripts( $hook ) {

    global $post;

    if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
        if ( 'mep_events' === $post->post_type ) { 
    wp_enqueue_script('jquery-ui-timepicker-addon',plugin_dir_url( __DIR__ ).'js/jquery-ui-timepicker-addon.js',array('jquery','jquery-ui-core'),1,true);
    wp_enqueue_script('jquery-ui-timepicker-addon',plugin_dir_url( __DIR__ ).'js/jquery-ui-sliderAccess.js',array('jquery','jquery-ui-core','jquery-ui-timepicker-addon'),1,true);    
    wp_enqueue_script('mep_datepicker',plugin_dir_url( __DIR__ ).'js/mep_datepicker.js',array('jquery','jquery-ui-core','jquery-ui-timepicker-addon'),1,true);

    wp_enqueue_style('jquery-ui-timepicker-addon',plugin_dir_url( __DIR__ ).'css/jquery-ui-timepicker-addon.css',array());
             wp_enqueue_style('mep-jquery-ui-style',plugin_dir_url( __DIR__ ).'css/jquery-ui.css',array());
             wp_enqueue_script('gmap-scripts',plugin_dir_url( __DIR__ ).'js/mkb-admin.js',array('jquery','jquery-ui-core'),1,true);
        }
    }
}
add_action( 'admin_enqueue_scripts', 'add_admin_scripts', 10, 1 );






// Enqueue Scripts for frontend
add_action('wp_enqueue_scripts', 'mep_event_enqueue_scripts');
function mep_event_enqueue_scripts() {
   wp_enqueue_script('jquery'); 
   wp_enqueue_script('jquery-ui-accordion');
   wp_enqueue_style('mep-jquery-ui-style',plugin_dir_url( __DIR__ ).'css/jquery-ui.css',array());
   wp_enqueue_style('mep-event-style',plugin_dir_url( __DIR__ ).'css/style.css',array());
   wp_enqueue_style ('font-awesome-css-cdn',"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css",null,1);
   wp_enqueue_style('mep-calendar-min-style',plugin_dir_url( __DIR__ ).'css/calendar.min.css',array()); 
   wp_enqueue_script('mep-moment-js',plugin_dir_url( __DIR__ ).'js/moment.js',array(),1,true);
   wp_enqueue_script('mep-calendar-scripts',plugin_dir_url( __DIR__ ).'js/calendar.min.js',array('jquery','mep-moment-js'),1,false); 
   wp_enqueue_script('mep-mixitup-min-js',plugin_dir_url( __DIR__ ).'js/mixitup.min.js',array(),1,true); 
   wp_enqueue_script('mep-event-custom-scripts',plugin_dir_url( __DIR__ ).'js/mkb-scripts.js',array(),1,true);  
}

// Enqueue Scripts for frontend
add_action('at_footer', 'mep_event_custom_enqueue_scripts');
function mep_event_custom_enqueue_scripts() {
     wp_enqueue_script('jquery-barcode',plugin_dir_url( __DIR__ ).'js/jquery-barcode.min.js',array('jquery'),1,true);
}