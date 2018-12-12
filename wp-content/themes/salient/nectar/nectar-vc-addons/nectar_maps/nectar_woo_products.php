<?php 

$is_admin = is_admin();

$woo_args = array(
	'taxonomy' => 'product_cat',
);
$woo_types = ($is_admin) ? get_categories($woo_args) : array('All' => 'all');
$woo_options = array("All" => "all");

$order_by_values = array(
	'',
	__( 'Date', 'js_composer' ) => 'date',
	__( 'ID', 'js_composer' ) => 'ID',
	__( 'Author', 'js_composer' ) => 'author',
	__( 'Title', 'js_composer' ) => 'title',
	__( 'Modified', 'js_composer' ) => 'modified',
	__( 'Random', 'js_composer' ) => 'rand',
	__( 'Comment count', 'js_composer' ) => 'comment_count',
	__( 'Menu order', 'js_composer' ) => 'menu_order',
);

$order_way_values = array(
	'',
	__( 'Descending', 'js_composer' ) => 'DESC',
	__( 'Ascending', 'js_composer' ) => 'ASC',
);

if($is_admin) {
	foreach ($woo_types as $type) {
		$woo_options[$type->name] = $type->slug;
	}
} else {
	$woo_options['All'] = 'all';
}

////recent products
return array(
  "name" => __("WooCommerce Products", "js_composer"),
  "base" => "nectar_woo_products",
  "weight" => 8,
  "icon" => "icon-wpb-recent-products",
  "category" => __('Nectar Elements', 'js_composer'),
  "description" => __('Display your products', 'js_composer'),
  "params" => array(
  	array(
	  "type" => "dropdown",
	  "heading" => __("Product Type", "js_composer"),
	  "param_name" => "product_type",
	  "value" => array(
	  	'All' => 'all',
	  	'Sale Only' => 'sale',
	  	'Featured Only' => 'featured',
	  	'Best Selling Only' => 'best_selling'
	  ),
	  'save_always' => true,
	  "description" => __("Please select the type of products you would like to display.", "js_composer")
	),
	array(
	  "type" => "dropdown_multi",
	  "heading" => __("Product Categories", "js_composer"),
	  "param_name" => "category",
	  "admin_label" => true,
	  "value" => $woo_options,
	  'save_always' => true,
	  "description" => __("Please select the categories you would like to display in your products. <br/> You can select multiple categories too (ctrl + click on PC and command + click on Mac).", "js_composer")
	),
	array(
	  "type" => "dropdown",
	  "heading" => __("Number Of Columns", "js_composer"),
	  "param_name" => "columns",
	  "value" => array(
	  	'4' => '4',
	  	'3' => '3',
	  	'2' => '2',
	  	'1' => '1',
			'Dynamic' => 'dynamic',
	  ),
	  'save_always' => true,
	  "description" => __("Please select the number of columns you would like to display. <br/> \"Dynamic\" will only be used on flickity full width carousels", "js_composer")
	),
	array(
      "type" => "textfield",
      "heading" => __("Number Of Products", "js_composer"),
      "param_name" => "per_page",
       "admin_label" => true,
      "description" => __("How many posts would you like to display? <br/> Enter as a number example \"4\"", "js_composer")
    ),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Order by', 'js_composer' ),
			'param_name' => 'orderby',
			'value' => $order_by_values,
			'std' => 'date',
			// default WC value
			'save_always' => true,
			'description' => sprintf( __( 'Select how to sort retrieved products. More at %s.', 'js_composer' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
		),
		array(
			'type' => 'dropdown',
			'heading' => __( 'Sort order', 'js_composer' ),
			'param_name' => 'order',
			'value' => $order_way_values,
			'std' => 'DESC',
			// default WC value
			'save_always' => true,
			'description' => sprintf( __( 'Designates the ascending or descending order. More at %s.', 'js_composer' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
		),
		array(
      "type" => 'checkbox',
      "heading" => __("Enable Pagination", "js_composer"),
      "param_name" => "pagination",
      "description" => __("Would you like to enable pagination for this product display? (requires WooCommerce 3.2+)", "js_composer"),
      "value" => Array(__("Yes, please", "js_composer") => true),
    ),
		
    array(
      "type" => 'checkbox',
      "heading" => __("Enable Carousel Display", "js_composer"),
      "param_name" => "carousel",
      "description" => __("This will override your column choice - <b>Will not be used when Enable Pagination is on.</b>", "js_composer"),
      "value" => Array(__("Yes, please", "js_composer") => true),
    ),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => "Carousel Script",
			'save_always' => true,
			"param_name" => "script",
			"value" => array(
				"carouFredSel" => "carouFredSel",
				"Flickity" => "flickity"
			),
			"dependency" => array('element' => "carousel", 'value' => "1"),
			"description" => __("Flickity is reccomended over carouFredSel - however carouFredSel is still available for legacy users who prefer it." , "js_composer")
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => "Carousel Controls",
			'save_always' => true,
			"param_name" => "flickity_controls",
			"value" => array(
				"Bottom Pagination" => "bottom-pagination",
				"Next/Prev Arrows and Text" => "arrows-and-text"
			),
			"dependency" => Array('element' => "script", 'value' => "flickity"),
			"description" => __("" , "js_composer")
		),
		array(
			"type" => "checkbox",
			"class" => "",
			"heading" => __("Autorotate?", "js_composer"),
			"param_name" => "autorotate",
			"value" => Array(__("Yes", "js_composer") => 'true'),
			"dependency" => Array('element' => "script", 'value' => "flickity"),
			"description" => ""
		),
	array(
			"type" => "textfield",
			"heading" => __("Autorotation Speed", "js_composer"),
			"param_name" => "autorotation_speed",
			"dependency" => Array('element' => "autorotate", 'not_empty' => true),
			"description" => __("Enter in milliseconds (default is 5000)" , "js_composer")
		),
		array(
			"admin_label" => true,
			"type" => "textfield",
			"heading" => __("Carousel Heading Text", "js_composer"),
			"param_name" => "flickity_heading_text",
			"dependency" => Array('element' => "flickity_controls", 'value' => "arrows-and-text"),
			"description" => __("Enter the heading text here.", "js_composer")
		),
		array(
		"type" => "dropdown",
		"class" => "",
		'save_always' => true,
		"heading" => "Carousel Heading Tag",
		"param_name" => "flickity_heading_tag",
		"dependency" => Array('element' => "flickity_controls", 'value' => "arrows-and-text"),
		"value" => array(		
			"H2" => "h2",
			"H3" => "h3",
			"H4" => "h4",
			"H5" => "h5",
			"H6" => "h6",
		)),
		array(
			"admin_label" => false,
			"type" => "textfield",
			"heading" => __("Carousel Link Text", "js_composer"),
			"param_name" => "flickity_link_text",
			"dependency" => Array('element' => "flickity_controls", 'value' => "arrows-and-text"),
			"description" => __("If you'd like a link to be displayed under your heading text, enter the text for it here.", "js_composer")
		),
		array(
			"admin_label" => false,
			"type" => "textfield",
			"heading" => __("Carousel Link URL", "js_composer"),
			"param_name" => "flickity_link_url",
			"dependency" => Array('element' => "flickity_controls", 'value' => "arrows-and-text"),
			"description" => __("Enter the URL for your optional link.", "js_composer")
		),
		array(
      "type" => 'checkbox',
      "heading" => __("Add Item Shadow", "js_composer"),
      "param_name" => "item_shadow",
      "dependency" => Array('element' => "script", 'value' => "flickity"),
      "description" => __("This will add a small shadow to each item within the carousel", "js_composer"),
      "value" => Array(__("Yes, please", "js_composer") => true),
    ),
    array(
      "type" => 'checkbox',
      "heading" => __("Enable Controls On Hover", "js_composer"),
      "param_name" => "controls_on_hover",
      "dependency" => Array('element' => "script", 'value' => "carouFredSel"),
      "description" => __("This will add buttons for additional user control over your product carousel", "js_composer"),
      "value" => Array(__("Yes, please", "js_composer") => true),
    )
  )
);

?>