<?php
/**
 * 2AM Awesome loginbar Settings Controls
 *
 * @version 1.0
 *
 */
if ( !class_exists('MAGE_Events_Setting_Controls' ) ):
class MAGE_Events_Setting_Controls {

    private $settings_api;

    function __construct() {
        $this->settings_api = new MAGE_Setting_API;

        add_action( 'admin_init', array($this, 'admin_init') );
        add_action( 'admin_menu', array($this, 'admin_menu') );
    }

    function admin_init() {

        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );

        //initialize settings
        $this->settings_api->admin_init();
    }

    function admin_menu() {
        add_options_page( 'Event Settings', 'Event Settings', 'delete_posts', 'mep_event_settings_page', array($this, 'plugin_page') );
    }

    function get_settings_sections() {

        $sections = array(
            array(
                'id' => 'general_setting_sec',
                'title' => __( 'General Settings', 'mep' )
            ),
            array(
                'id' => 'email_setting_sec',
                'title' => __( 'Email Settings', 'mep' )
            ),
            array(
                'id' => 'style_setting_sec',
                'title' => __( 'Style Settings', 'mep' )
            ),            
            array(
                'id' => 'label_setting_sec',
                'title' => __( 'Label Settings', 'mep' )
            ) 
        );



        return $sections;
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    function get_settings_fields() {
        $settings_fields = array(
            'general_setting_sec' => array(

                array(
                    'name' => 'google-map-api',
                    'label' => __( 'Google Map API Key', 'mep' ),
                    'desc' => __( 'Enter Your Google Map API key. <a href=https://developers.google.com/maps/documentation/javascript/get-api-key target=_blank>Get KEY</a>', 'mep' ),
                    'type' => 'text',
                    'default' => ''
                ),

                array(
                    'name' => 'mep_global_single_template',
                    'label' => __( 'Event Details Template', 'mep' ),
                    'desc' => __( 'Event Details Template', 'mep' ),
                    'type' => 'select',
                    'default' => 'no',
                    'options' =>  event_template_name()
                ),
            ),

            'email_setting_sec' => array(


                array(
                    'name' => 'mep_email_form_name',
                    'label' => __( 'Email Form Name', 'mep' ),
                    'desc' => __( 'Email Form Name', 'mep' ),
                    'type' => 'text'
                ),

                array(
                    'name' => 'mep_email_form_email',
                    'label' => __( 'Form Email', 'mep' ),
                    'desc' => __( 'Form Email', 'mep' ),
                    'type' => 'text'
                ),

                array(
                    'name' => 'mep_email_subject',
                    'label' => __( 'Email Subject', 'mep' ),
                    'desc' => __( 'Email Subject', 'mep' ),
                    'type' => 'text'
                ),




                array(
                    'name' => 'mep_confirmation_email_text',
                    'label' => __( 'Confirmation Email Text', 'mep' ),
                    'desc' => __( 'Confirmation Email Text', 'mep' ),
                    'type' => 'textarea',
                    'default' => '',
                ),
            ),

            'label_setting_sec' => array(


                array(
                    'name' => 'mep_event_ticket_type_text',
                    'label' => __( 'Ticket Type Table Label', 'mep' ),
                    'desc' => __( 'Enter the text which you want to display as ticket type table in event details page.', 'mep' ),
                    'type' => 'text',
                    'default' => 'Ticket Type:'
                ),
                array(
                    'name' => 'mep_event_extra_service_text',
                    'label' => __( 'Extra Service Table Label', 'mep' ),
                    'desc' => __( 'Enter the text which you want to display as extra service table in event details page.', 'mep' ),
                    'type' => 'text',
                    'default' => 'Extra Service:'
                ),
                array(
                    'name' => 'mep_cart_btn_text',
                    'label' => __( 'Cart Button Label', 'mep' ),
                    'desc' => __( 'Enter the text which you want to display in Cart button in event details page.', 'mep' ),
                    'type' => 'text',
                    'default' => 'Register This Event'

                ),
                
                array(
                    'name' => 'mep_calender_btn_text',
                    'label' => __( 'Add Calender Button Label', 'mep' ),
                    'desc' => __( 'Enter the text which you want to display in Add you calender in event details page.', 'mep' ),
                    'type' => 'text',
                    'default' => 'ADD TO YOUR CALENDAR'
                ),

                array(
                    'name' => 'mep_share_text',
                    'label' => __( 'Social Share Label', 'mep' ),
                    'desc' => __( 'Enter the text which you want to display as share button title in event details page.', 'mep' ),
                    'type' => 'text',
                    'default' => 'Share This Event'
                ),

            ),
'style_setting_sec' => array(
                array(
                    'name' => 'mep_base_color',
                    'label' => __( 'Base Color', 'mep' ),
                    'desc' => __( 'Select a Basic Color, It will chanage the icon background color, border color', 'mep' ),
                    'type' => 'color',
                    
                ),                
                array(
                    'name' => 'mep_title_bg_color',
                    'label' => __( 'Label Background Color', 'mep' ),
                    'desc' => __( 'Select a Color Label Background', 'mep' ),
                    'type' => 'color',
                    
                ),                
                array(
                    'name' => 'mep_title_text_color',
                    'label' => __( 'Label Text Color', 'mep' ),
                    'desc' => __( 'Select a Color Label Text', 'mep' ),
                    'type' => 'color',
                    
                ),
                array(
                    'name' => 'mep_cart_btn_bg_color',
                    'label' => __( 'Cart Button Background Color', 'mep' ),
                    'desc' => __( 'Select a color for Cart Button Background', 'mep' ),
                    'type' => 'color',
                    
                ),   
                array(
                    'name' => 'mep_cart_btn_text_color',
                    'label' => __( 'Cart Button Text Color', 'mep' ),
                    'desc' => __( 'Select a color for Cart Button Text', 'mep' ),
                    'type' => 'color',
                    
                ),
                array(
                    'name' => 'mep_calender_btn_bg_color',
                    'label' => __( 'Calender Button Background Color', 'mep' ),
                    'desc' => __( 'Select a color for Calender Button Background', 'mep' ),
                    'type' => 'color',
                    
                ),   
                array(
                    'name' => 'mep_calender_btn_text_color',
                    'label' => __( 'Calender Button Text Color', 'mep' ),
                    'desc' => __( 'Select a color for Calender Button Text', 'mep' ),
                    'type' => 'color',
                    
                ), 
                array(
                    'name' => 'mep_faq_title_bg_color',
                    'label' => __( 'FAQ Title Background Color', 'mep' ),
                    'desc' => __( 'Select a color for FAQ title Background', 'mep' ),
                    'type' => 'color',
                    
                ),   
                array(
                    'name' => 'mep_faq_title_text_color',
                    'label' => __( 'FAQ Title Text Color', 'mep' ),
                    'desc' => __( 'Select a color for FAQ Title Text', 'mep' ),
                    'type' => 'color',
                    
                ),                               
            )


        );

        return $settings_fields;
    }

    function plugin_page() {
        echo '<div class="wrap">';

        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();

        echo '</div>';
    }

    /**
     * Get all the pages
     *
     * @return array page names with key value pairs
     */
    function get_pages() {
        $pages = get_pages();
        $pages_options = array();
        if ( $pages ) {
            foreach ($pages as $page) {
                $pages_options[$page->ID] = $page->post_title;
            }
        }

        return $pages_options;
    }

}
endif;

$settings = new MAGE_Events_Setting_Controls();


function mep_get_option( $option, $section, $default = '' ) {
    $options = get_option( $section );

    if ( isset( $options[$option] ) ) {
        return $options[$option];
    }
    
    return $default;
}