<?php 
add_action('add_meta_boxes', 'nectar_metabox_nectar_slider');
function nectar_metabox_nectar_slider(){
    
    $meta_box = array(
		'id' => 'nectar-metabox-nectar-slider',
		'title' => __('Slide Settings', 'salient'),
		'description' => __('Please fill out & configure the fileds below to create your slide. The only mandatory field is the "Slide Image".', 'salient'),
		'post_type' => 'nectar_slider',
		'context' => 'normal',
		'priority' => 'high',
		'fields' => array(
		
			array( 
					'name' => __('Background Type', 'salient'),
					'desc' => __('Please select the background type you would like to use for your slide.', 'salient'),
					'id' => '_nectar_slider_bg_type',
					'type' => 'choice_below',
					'options' => array(
						'image_bg' => 'Image Background',
						'video_bg' => 'Video Background'
					),
					'std' => 'image_bg'
				),
			array( 
					'name' => __('Slide Image', 'salient'),
					'desc' => __('Click the "Upload" button to begin uploading your image, followed by "Select File" once you have made your selection.', 'salient'),
					'id' => '_nectar_slider_image',
					'type' => 'file',
					'std' => ''
				),
			array( 
					'name' => __('Video WebM Upload', 'salient'),
					'desc' => __('Browse for your WebM video file here.<br/> This will be automatically played on load so make sure to use this responsibly for enhancing your design, rather than annoy your user. e.g. A video loop with no sound.<br/><strong>You must include this format & the mp4 format to render your video with cross browser compatibility. OGV is optional.</strong> <br/><strong>Video must be in a 16:9 aspect ratio.</strong>', 'salient'),
					'id' => '_nectar_media_upload_webm',
					'type' => 'media',
					'std' => ''
				),
			array( 
					'name' => __('Video MP4 Upload', 'salient'),
					'desc' => __('Browse for your mp4 video file here.<br/> See the note above for recommendations on how to properly use your video background.', 'salient'),
					'id' => '_nectar_media_upload_mp4',
					'type' => 'media',
					'std' => ''
				),
			array( 
					'name' => __('Video OGV Upload', 'salient'),
					'desc' => __('Browse for your OGV video file here.<br/>  See the note above for recommendations on how to properly use your video background.', 'salient'),
					'id' => '_nectar_media_upload_ogv',
					'type' => 'media',
					'std' => ''
				),
			array( 
					'name' => __('Preview Image', 'salient'),
					'desc' => __('This is the image that will be seen in place of your video on mobile devices & older browsers before your video is played.', 'salient'),
					'id' => '_nectar_slider_preview_image',
					'type' => 'file',
					'std' => ''
				),
        /*array(
  					'name' =>  __('Mute Video', 'salient'),
  					'desc' => __('Do you want to mute the video? (recommended). <strong>Required to autoplay on mobile devices.</strong>', 'salient'),
  					'id' => '_nectar_slider_video_muted',
  					'type' => 'checkbox',
            'std' => 1
  				),  */
			array(
					'name' =>  __('Add texture overlay to background', 'salient'),
					'desc' => __('If you would like a slight texture overlay on your background, activate this option.', 'salient'),
					'id' => '_nectar_slider_video_texture',
					'type' => 'checkbox',
	                'std' => 1
				),
      	
			
			array( 
					'name' => __('Background Alignment', 'salient'),
					'desc' => __('Please choose how you would like your slides background to be aligned', 'salient'),
					'id' => '_nectar_slider_slide_bg_alignment',
					'type' => 'select',
					'std' => 'center',
					'options' => array(
						"top" => "Top",
				  		 "center" => "Center",
				  		 "bottom" => "Bottom"
					)
				),
				
			array( 
					'name' => __('Slide Font Color', 'salient'),
					'desc' => __('This gives you an easy way to make sure your text is visible regardless of the background.', 'salient'),
					'id' => '_nectar_slider_slide_font_color',
					'type' => 'select',
					'std' => '',
					'options' => array(
						'light' => 'Light',
						'dark' => 'Dark'
					)
				),
				
			array( 
					'name' => __('Heading', 'salient'),
					'desc' => __('Please enter in the heading for your slide.', 'salient'),
					'id' => '_nectar_slider_heading',
					'type' => 'text',
					'std' => ''
				),
			array( 
					'name' => __('Caption', 'salient'),
					'desc' => __('If you have a caption for your slide, enter it here', 'salient'),
					'id' => '_nectar_slider_caption',
					'type' => 'textarea',
					'std' => ''
				),
			array(
					'name' =>  __('Caption Background', 'salient'),
					'desc' => __('If you would like to add a semi transparent background to your caption, activate this option.', 'salient'),
					'id' => '_nectar_slider_caption_background',
					'type' => 'checkbox',
	                'std' => ''
				),	
        
        array( 
  					'name' => __('Slide Content Desktop Width', 'salient'),
  					'desc' => __('Releative to the site content container', 'salient'),
  					'id' => '_nectar_slider_slide_content_width_desktop',
  					'type' => 'select',
  					'std' => '',
  					'options' => array(
  						'auto' => 'Auto',
  						'90%' => '90%',
              '80%' => '80%',
              '70%' => '70%',
              '60%' => '60%',
              '50%' => '50%'
  					)
  				),
        
          array( 
    					'name' => __('Slide Content Tablet Width', 'salient'),
    					'desc' => __('Releative to the site content container', 'salient'),
    					'id' => '_nectar_slider_slide_content_width_tablet',
    					'type' => 'select',
    					'std' => '',
    					'options' => array(
    						'auto' => 'Auto',
    						'90%' => '90%',
                '80%' => '80%',
                '70%' => '70%',
                '60%' => '60%',
                '50%' => '50%'
    					)
    				),
          
        array( 
  					'name' => __('Background Overlay Color', 'salient'),
  					'desc' => __('This will be applied ontop on your BG image (if supplied).', 'salient'),
  					'id' => '_nectar_slider_bg_overlay_color',
  					'type' => 'color',
  					'std' => ''
  				),
			array( 
					'name' => __('Insert Down Arrow That Leads to Content Below?', 'salient'),
					'desc' => __('This is particularly useful when using tall sliders to let the user know there\'s content underneath.', 'salient'),
					'id' => '_nectar_slider_down_arrow',
					'type' => 'checkbox',
					'std' => ''
				),	
			array( 
					'name' => __('Link Type', 'salient'),
					'desc' => __('Please select how you would like to link your slide.', 'salient'),
					'id' => '_nectar_slider_link_type',
					'type' => 'choice_below',
					'options' => array(
						'button_links' => 'Button Links',
						'full_slide_link' => 'Full Slide Link'
					),
					'std' => 'button_links'
				),	
			array( 
					'name' => __('Button Text', 'salient'),
					'desc' => __('Enter the text for your button here.', 'salient'),
					'id' => '_nectar_slider_button',
					'type' => 'slider_button_textarea',
					'std' => '',
					'extra' => 'first'
				),
			array( 
					'name' => __('Button Link', 'salient'),
					'desc' => __('Enter a URL here.', 'salient'),
					'id' => '_nectar_slider_button_url',
					'type' => 'slider_button_textarea',
					'std' => '',
					'extra' => 'inline'
				),
			array( 
					'name' => __('Button Style', 'salient'),
					'desc' => __('Desired button style', 'salient'),
					'id' => '_nectar_slider_button_style',
					'type' => 'slider_button_select',
					'std' => '',
					'options' => array(
						'solid_color' => __('Solid Color BG', 'salient'),
						'solid_color_2' => __('Solid Color BG W/ Tilt Hover', 'salient'),
						'transparent' => __('Transparent With Border', 'salient'),
						'transparent_2' => __('Transparent W/ Solid BG Hover', 'salient')
					),
					'extra' => 'inline'
				),
			array( 
					'name' => __('Button Color', 'salient'),
					'desc' => __('Desired color', 'salient'),
					'id' => '_nectar_slider_button_color',
					'type' => 'slider_button_select',
					'std' => '',
					'options' => array(
						'primary-color' => __('Primary Color', 'salient'),
						'extra-color-1' => __('Extra Color #1', 'salient'),
						'extra-color-2' => __('Extra Color #2', 'salient'),
						'extra-color-3' => __('Extra Color #3', 'salient'),
            "extra-color-gradient-1" => __("Color Gradient 1", 'salient'),
    		 		"extra-color-gradient-2" => __("Color Gradient 2", 'salient'),
            "white" => "White & Black Text"
					),
					'extra' => 'last'
				),
				
			
			array( 
					'name' => __('Button Text', 'salient'),
					'desc' => __('Enter the text for your button here.', 'salient'),
					'id' => '_nectar_slider_button_2',
					'type' => 'slider_button_textarea',
					'std' => '',
					'extra' => 'first'
				),
			array( 
					'name' => __('Button Link', 'salient'),
					'desc' => __('Enter a URL here.', 'salient'),
					'id' => '_nectar_slider_button_url_2',
					'type' => 'slider_button_textarea',
					'std' => '',
					'extra' => 'inline'
				),
			array( 
					'name' => __('Button Style', 'salient'),
					'desc' => __('Desired button style', 'salient'),
					'id' => '_nectar_slider_button_style_2',
					'type' => 'slider_button_select',
					'std' => '',
					'options' => array(
						'solid_color' => __('Solid Color Background', 'salient'),
						'solid_color_2' => __('Solid Color BG W/ Tilt Hover', 'salient'),
						'transparent' => __('Transparent With Border', 'salient'),
						'transparent_2' => __('Transparent W/ Solid BG Hover', 'salient')
					),
					'extra' => 'inline'
				),
			array( 
					'name' => __('Button Color', 'salient'),
					'desc' => __('Desired color', 'salient'),
					'id' => '_nectar_slider_button_color_2',
					'type' => 'slider_button_select',
					'std' => '',
					'options' => array(
						'primary-color' => __('Primary Color', 'salient'),
						'extra-color-1' => __('Extra Color #1', 'salient'),
						'extra-color-2' => __('Extra Color #2', 'salient'),
						'extra-color-3' => __('Extra Color #3', 'salient'),
            "extra-color-gradient-1" => __("Color Gradient 1", 'salient'),
    		 		"extra-color-gradient-2" => __("Color Gradient 2", 'salient'),
            "white" => "White & Black Text"
					),
					'extra' => 'last'
				),
				
			array( 
					'name' => __('Slide Link', 'salient'),
					'desc' => __('Please enter your URL that will be used to link the slide.', 'salient'),
					'id' => '_nectar_slider_entire_link',
					'type' => 'text',
					'std' => ''
				),
				
			array( 
					'name' => __('Slide Video Popup', 'salient'),
					'desc' => __('Enter in an embed code from Youtube or Vimeo that will be used to display your video in a popup. (You can also use the WordPress video shortcode)', 'salient'),
					'id' => '_nectar_slider_video_popup',
					'type' => 'textarea',
					'std' => ''
				),
				
			array( 
					'name' => __('Slide Content Alignment', 'salient'),
					'desc' => __('Horizontal Alignment', 'salient'),
					'id' => '_nectar_slide_xpos_alignment',
					'type' => 'caption_pos',
					'options' => array(
						'left' => 'Left',
						'centered' => 'Centered',
						'right' => 'Right',
					),
					'std' => 'left',
					'extra' => 'first'
				),
				
			array( 
					'name' => __('Slide Content Alignment', 'salient'),
					'desc' => __('Vertical Alignment', 'salient'),
					'id' => '_nectar_slide_ypos_alignment',
					'type' => 'caption_pos',
					'options' => array(
						'top' => 'Top',
						'middle' => 'Middle',
						'bottom' => 'Bottom',
					),
					'std' => 'middle',
					'extra' => 'last'
				),
			array( 
				'name' => __('Extra Class Name', 'salient'),
				'desc' => __('If you would like to enter a custom class name to this slide for css purposes, enter it here.', 'salient'),
				'id' => '_nectar_slider_slide_custom_class',
				'type' => 'text',
				'std' => ''
			)
		)
	);
	//$callback = create_function( '$post,$meta_box', 'nectar_create_meta_box( $post, $meta_box["args"] );' );
  
  function nectar_metabox_nectar_slider_callback($post,$meta_box) {
    nectar_create_meta_box( $post, $meta_box["args"] );
  }
  
	add_meta_box( $meta_box['id'], $meta_box['title'], 'nectar_metabox_nectar_slider_callback', $meta_box['post_type'], $meta_box['context'], $meta_box['priority'], $meta_box );
	
	
	
	
	
}


?>