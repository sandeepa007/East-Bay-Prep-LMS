<?php 
add_action('add_meta_boxes_page', 'nectar_metabox_page');
function nectar_metabox_page(){
    
	$options = get_nectar_theme_options(); 
	if(!empty($options['transparent-header']) && $options['transparent-header'] == '1') {
		$disable_transparent_header = array( 
					'name' =>  __('Disable Transparency From Navigation', 'salient'),
					'desc' => __('You can use this option to force your navigation header to stay a solid color even if it qualifies to trigger the','salient') . '<a target="_blank" href="'. admin_url('?page=Salient#16_section_group_li_a') .'"> transparent effect</a> ' . __('you have activated in the Salient options panel.', 'salient'),
					'id' => '_disable_transparent_header',
					'type' => 'checkbox',
	                'std' => ''
				);
		$force_transparent_header = array( 
					'name' =>  __('Force Transparency On Navigation', 'salient'),
					'desc' => __('You can use this option to force your navigation header to start transparent even if it does not qualify to trigger the','salient') . '<a target="_blank" href="'. admin_url('?page=Salient#16_section_group_li_a') .'"> transparent effect</a> ' . __('you have activated in the Salient options panel.', 'salient'),
					'id' => '_force_transparent_header',
					'type' => 'checkbox',
	                'std' => ''
				);
    $force_transparent_header_color = array( 
      'name' => __('Transparent Header Navigation Color', 'salient'),
      'desc' => __('Choose your header navigation logo & color scheme that will be used at the top of the page when the transparent effect is active. <br/> This option pulls from the settings "Header Starting Dark Logo" & "Header Dark Text Color" in the','salient') . ' <a target="_blank" href="'. admin_url('?page=Salient#16_section_group_li_a') .'">transparency tab</a>.',
      'id' => '_force_transparent_header_color',
      'type' => 'select',
      'std' => 'light',
      'options' => array(
        "light" => "Light (default)",
        "dark" => "Dark",
      )
    );
    
	} else {
		$disable_transparent_header = null;
		$force_transparent_header = null;
    $force_transparent_header_color = null;
	}
	
	#-----------------------------------------------------------------#
	# Fullscreen rows
	#-----------------------------------------------------------------#
    $meta_box = array(
		'id' => 'nectar-metabox-fullscreen-rows',
		'title' => __('Page Full Screen Rows', 'salient'),
		'description' => __('Here you can configure your page fullscreen rows', 'salient'),
		'post_type' => 'page',
		'context' => 'normal',
		'priority' => 'high',
		'fields' => array(

				array( 
					'name' => __('Activate Fullscreen Rows', 'salient'),
					'desc' => __('This will cause all WPBakery Page Builder rows to be fullscreen. Some functionality and options within the WPBakery Page Builder will be changed when this is active.', 'salient'),
					'id' => '_nectar_full_screen_rows',
					'type' => 'choice_below',
					'options' => array(
						'off' => 'Off',
						'on' => 'On'
					),
					'std' => 'off'
				),
				array( 
					'name' => __('Animation Bewteen Rows', 'salient'),
					'desc' => __('Select your desired animation between rows', 'salient'),
					'id' => '_nectar_full_screen_rows_animation',
					'type' => 'select',
					'std' => 'none',
					'options' => array(
						"none" => "Default Scroll",
				  		 "zoom-out-parallax" => "Zoom Out + Parallax",
				  		 "parallax" => "Parallax"
					)
				),
				array( 
					'name' => __('Animation Speed', 'salient'),
					'desc' => __('Selection your desired animation speed', 'salient'),
					'id' => '_nectar_full_screen_rows_animation_speed',
					'type' => 'select',
					'std' => 'medium',
					'options' => array(
						"slow" => "Slow",
				  		 "medium" => "Medium",
				  		 "fast" => "Fast"
					)
				),
				array( 
					'name' => __('Overall BG Color', 'salient'),
					'desc' => __('Set your desired background color which will be seen when transitioning through rows. Defaults to #333333', 'salient'),
					'id' => '_nectar_full_screen_rows_overall_bg_color',
					'type' => 'color',
					'std' => ''
				),
				array(
					'name' =>  __('Add Row Anchors to URL', 'salient'),
					'desc' => __('Enable this to add anchors into your URL for each row.', 'salient'),
					'id' => '_nectar_full_screen_rows_anchors',
					'type' => 'checkbox',
	                'std' => '0'
				),
				array(
					'name' =>  __('Disable On Mobile', 'salient'),
					'desc' => __('Check this to disable the page full screen rows when viewing on a mobile device.', 'salient'),
					'id' => '_nectar_full_screen_rows_mobile_disable',
					'type' => 'checkbox',
	                'std' => '0'
				),
				array( 
					'name' => __('Row BG Image Animation', 'salient'),
					'desc' => __('Select your desired row BG image animation', 'salient'),
					'id' => '_nectar_full_screen_rows_row_bg_animation',
					'type' => 'select',
					'std' => 'none',
					'options' => array(
						"none" => "None",
				  		 "ken_burns" => "Ken Burns Zoom"
					)
				),
				array( 
					'name' => __('Dot Navigation', 'salient'),
					'desc' => __('Select your desired dot navigation style', 'salient'),
					'id' => '_nectar_full_screen_rows_dot_navigation',
					'type' => 'select',
					'std' => 'tooltip',
					'options' => array(
						"transparent" => "Transparent",
				  		 "tooltip" => "Tooltip",
				  		 "tooltip_alt" => "Tooltip Alt",
				  		 "hidden" => "None (Hidden)"
					)
				),
				array( 
					'name' => __('Row Overflow', 'salient'),
					'desc' => __('Select how you would like rows to be handled that have content taller than the users window height. This only applies to desktop (mobile will automatically get scrollbars)', 'salient'),
					'id' => '_nectar_full_screen_rows_content_overflow',
					'type' => 'select',
					'std' => 'tooltip',
					'options' => array(
						"scrollbar" => "Provide Scrollbar",
				  		"hidden" => "Hide Extra Content",
					)
				),
				array( 
					'name' => __('Page Footer', 'salient'),
					'desc' => __('This option allows you to define what will be used for the footer after your fullscreen rows', 'salient'),
					'id' => '_nectar_full_screen_rows_footer',
					'type' => 'select',
					'std' => 'none',
					'options' => array(
						"default" => "Default Footer",
						"last_row" => "Last Row",
						"none" => "None"
					)
				),
		)
	);
	//$callback = create_function( '$post,$meta_box', 'nectar_create_meta_box( $post, $meta_box["args"] );' );
  
  function nectar_metabox_page_meta_callback($post,$meta_box) {
    nectar_create_meta_box( $post, $meta_box["args"] );
  }
  
	add_meta_box( $meta_box['id'], $meta_box['title'], 'nectar_metabox_page_meta_callback', $meta_box['post_type'], $meta_box['context'], $meta_box['priority'], $meta_box );
	

	#-----------------------------------------------------------------#
	# Header Settings
	#-----------------------------------------------------------------#
    $meta_box = array(
		'id' => 'nectar-metabox-page-header',
		'title' => __('Page Header Settings', 'salient'),
		'description' => __('Here you can configure how your page header will appear. <br/> For a full width background image behind your header text, simply upload the image below. To have a standard header just fill out the fields below and don\'t upload an image.', 'salient'),
		'post_type' => 'page',
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
						'video_bg' => 'Video Background',
						'particle_bg' => 'HTML5 Canvas Background'
					),
					'std' => 'image_bg'
				),
			
			array( 
					'name' => __('Particle Images', 'salient'),
					'desc' => 'Add images here that will be used to create the particle shapes.',
					'id' => '_nectar_canvas_shapes',
					'type' => 'canvas_shape_group',
					'class' => 'nectar_slider_canvas_shape',
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


			array( 
					'name' => __('Page Header Image', 'salient'),
					'desc' => __('The image should be between 1600px - 2000px wide and have a minimum height of 475px for best results. Click "Browse" to upload and then "Insert into Post".', 'salient'),
					'id' => '_nectar_header_bg',
					'type' => 'file',
					'std' => ''
				),
			array(
					'name' =>  __('Parallax Header', 'salient'),
					'desc' => __('This will cause your header to have a parallax scroll effect.', 'salient'),
					'id' => '_nectar_header_parallax',
					'type' => 'checkbox',
					'extra' => 'first2',
	                'std' => 1
				),	
			array(
					'name' =>  __('Box Roll Header', 'salient'),
					'desc' => __('This will cause your header to have a 3d box roll on scroll. (deactivated for boxed layouts)', 'salient'),
					'id' => '_nectar_header_box_roll',
					'type' => 'checkbox',
					'extra' => 'last',
	                'std' => ''
				),
			array( 
					'name' => __('Page Header Height', 'salient'),
					'desc' => __('How tall do you want your header? <br/>Don\'t include "px" in the string. e.g. 350 <br/><strong>This only applies when you are using an image/bg color.</strong>', 'salient'),
					'id' => '_nectar_header_bg_height',
					'type' => 'text',
					'std' => ''
				),
			array( 
					'name' => __('Fullscreen Height', 'salient'),
					'desc' => __('Chooseing this option will allow your header to always remain fullscreen on all devices/screen sizes.', 'salient'),
					'id' => '_nectar_header_fullscreen',
					'type' => 'checkbox',
					'std' => ''
				),
			array( 
					'name' => __('Page Header Title', 'salient'),
					'desc' => __('Enter in the page header title', 'salient'),
					'id' => '_nectar_header_title',
					'type' => 'text',
					'std' => ''
				),
			array( 
					'name' => __('Page Header Subtitle', 'salient'),
					'desc' => __('Enter in the page header subtitle', 'salient'),
					'id' => '_nectar_header_subtitle',
					'type' => 'text',
					'std' => ''
				),
			array( 
					'name' => __('Text Effect', 'salient'),
					'desc' => __('Please select your desired text effect', 'salient'),
					'id' => '_nectar_page_header_text-effect',
					'type' => 'select',
					'std' => 'none',
					'options' => array(
						"none" => "None",
				  		 "rotate_in" => "Rotate In"
					)
				),
			array( 
					'name' => __('Shape Autorotate Timing', 'salient'),
					'desc' => __('Enter your desired autorotation time in milliseconds e.g. "5000". Leaving this blank will disable the functionality.', 'salient'),
					'id' => '_nectar_particle_rotation_timing',
					'type' => 'text',
					'std' => ''
				),
			array(
					'name' =>  __('Disable Chance For Particle Explosion', 'salient'),
					'desc' => __('By default there\'s a 50% chance on autorotation that your particles will explode. Checking this box disables that.', 'salient'),
					'id' => '_nectar_particle_disable_explosion',
					'type' => 'checkbox',
	                'std' => ''
				),
			array( 
					'name' => __('Content Alignment', 'salient'),
					'desc' => __('Horizontal Alignment', 'salient'),
					'id' => '_nectar_page_header_alignment',
					'type' => 'caption_pos',
					'options' => array(
						'left' => 'Left',
						'center' => 'Centered',
						'right' => 'Right',
					),
					'std' => 'left',
					'extra' => 'first2'
				),
				
			array( 
					'name' => __('Content Alignment', 'salient'),
					'desc' => __('Vertical Alignment', 'salient'),
					'id' => '_nectar_page_header_alignment_v',
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
					'name' => __('Background Alignment', 'salient'),
					'desc' => __('Please choose how you would like your header background to be aligned', 'salient'),
					'id' => '_nectar_page_header_bg_alignment',
					'type' => 'select',
					'std' => 'center',
					'options' => array(
						"top" => "Top",
				  		 "center" => "Center",
				  		 "bottom" => "Bottom"
					)
				),
			array( 
					'name' => __('Page Header Background Color', 'salient'),
					'desc' => __('Set your desired page header background color if not using an image', 'salient'),
					'id' => '_nectar_header_bg_color',
					'type' => 'color',
					'std' => ''
				),
			array( 
					'name' => __('Page Header Font Color', 'salient'),
					'desc' => __('Set your desired page header font color', 'salient'),
					'id' => '_nectar_header_font_color',
					'type' => 'color',
					'std' => ''
				),
			array( 
					'name' => __('Page Header Overlay Color', 'salient'),
					'desc' => __('This will be applied ontop on your page header BG image (if supplied).', 'salient'),
					'id' => '_nectar_header_bg_overlay_color',
					'type' => 'color',
					'std' => ''
				),
		    $disable_transparent_header,
		    $force_transparent_header,
        $force_transparent_header_color
		)
	);
	//$callback = create_function( '$post,$meta_box', 'nectar_create_meta_box( $post, $meta_box["args"] );' );
	add_meta_box( $meta_box['id'], $meta_box['title'], 'nectar_metabox_page_meta_callback', $meta_box['post_type'], $meta_box['context'], $meta_box['priority'], $meta_box );
	
	
	#-----------------------------------------------------------------#
	# Portfolio Display Settings
	#-----------------------------------------------------------------#
	$portfolio_types = get_terms('project-type');

	$types_options = array("all" => "All");
	
	foreach ($portfolio_types as $type) {
		$types_options[$type->slug] = $type->name;
	}
			
    $meta_box = array(
		'id' => 'nectar-metabox-portfolio-display',
		'title' => __('Portfolio Display Settings', 'salient'),
		'description' => __('Here you can configure which categories will display in your portfolio.', 'salient'),
		'post_type' => 'page',
		'context' => 'side',
		'priority' => 'core',
		'fields' => array(
			array( 
					'name' => 'Portfolio Categories',
					'desc' => '',
					'id' => 'nectar-metabox-portfolio-display',
					'type' => 'multi-select',
					'options' => $types_options,
					'std' => 'all'
				),
			array( 
					'name' => 'Display Sortable',
					'desc' => 'Should these portfolio items be sortable?',
					'id' => 'nectar-metabox-portfolio-display-sortable',
					'type' => 'checkbox',
					'std' => '1'
				)
		)
	);
	//$callback = create_function( '$post,$meta_box', 'nectar_create_meta_box( $post, $meta_box["args"] );' );
	add_meta_box( $meta_box['id'], $meta_box['title'], 'nectar_metabox_page_meta_callback', $meta_box['post_type'], $meta_box['context'], $meta_box['priority'], $meta_box );
	
	
}


?>