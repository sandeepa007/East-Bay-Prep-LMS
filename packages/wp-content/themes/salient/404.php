<?php get_header(); 

$page_404_bg_color = ( !empty($options['page-404-bg-color']) ) ? $options['page-404-bg-color'] : '';
$page_404_font_color = ( !empty($options['page-404-font-color']) ) ? $options['page-404-font-color'] : '';
$page_404_bg_image = ( !empty($options['page-404-bg-image']) && isset($options['page-404-bg-image']) ) ? nectar_options_img($options['page-404-bg-image']) : null;
$page_404_bg_image_overlay_color = ( !empty($options['page-404-bg-image-overlay-color']) ) ? $options['page-404-bg-image-overlay-color'] : '';
$page_404_home_button = ( !empty($options['page-404-home-button']) ) ? $options['page-404-home-button'] : '';

?>

<div class="container-wrap" <?php if(!empty($page_404_bg_color)) { echo 'style="background-color: '.$page_404_bg_color.';"'; } ?>>
	
	<?php if(!empty($page_404_bg_image)) {
		echo '<div class="error-404-bg-img" style="background-image: url('.$page_404_bg_image.');"></div>';
		
		if(!empty($page_404_bg_image_overlay_color)) {
			 echo '<div class="error-404-bg-img-overlay" style="background-color: '.$page_404_bg_image_overlay_color.';"></div>';
		}
		
	} ?>
	
	<div class="container main-content">
		
		<div class="row">
			
			<div class="col span_12">
				
				<div id="error-404" <?php if(!empty($page_404_font_color)) { echo 'data-cc="true" style="color: '.$page_404_font_color.';"'; } ?> >
					<h1>404</h1>
					<h2><?php echo __('Page Not Found', 'salient'); ?></h2>
					
					<?php if($page_404_home_button == '1') { ?>
						   <a class="nectar-button large regular-button accent-color has-icon" data-color-override="false" data-hover-color-override="false" href="<?php echo home_url(); ?>"><span><?php echo __('Back Home', 'salient'); ?> </span><i class="icon-button-arrow"></i></a>
					 <?php } ?>
				</div>
				
			</div><!--/span_12-->
			
		</div><!--/row-->
		
	</div><!--/container-->

</div>
<?php get_footer(); ?>

