<?php
if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.
function mep_cpt_tax(){


	$labels = array(
		'name'                       => _x( 'Event Category','mage-eventpress' ),
		'singular_name'              => _x( 'Event Category','mage-eventpress' ),
		'menu_name'                  => __( 'Category', 'mage-eventpress' ),
		'all_items'                  => __( 'All Event Category', 'mage-eventpress' ),
		'parent_item'                => __( 'Parent Category', 'mage-eventpress' ),
		'parent_item_colon'          => __( 'Parent Category:', 'mage-eventpress' ),
		'new_item_name'              => __( 'New Category Name', 'mage-eventpress' ),
		'add_new_item'               => __( 'Add New Category', 'mage-eventpress' ),
		'edit_item'                  => __( 'Edit Category', 'mage-eventpress' ),
		'update_item'                => __( 'Update Category', 'mage-eventpress' ),
		'view_item'                  => __( 'View Category', 'mage-eventpress' ),
		'separate_items_with_commas' => __( 'Separate Category with commas', 'mage-eventpress' ),
		'add_or_remove_items'        => __( 'Add or remove Category', 'mage-eventpress' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'mage-eventpress' ),
		'popular_items'              => __( 'Popular Category', 'mage-eventpress' ),
		'search_items'               => __( 'Search Category', 'mage-eventpress' ),
		'not_found'                  => __( 'Not Found', 'mage-eventpress' ),
		'no_terms'                   => __( 'No Category', 'mage-eventpress' ),
		'items_list'                 => __( 'Category list', 'mage-eventpress' ),
		'items_list_navigation'      => __( 'Category list navigation', 'mage-eventpress' ),
	);

	$args = array(
		'hierarchical'          => true,
		"public" 				=> true,
		'labels'                => $labels,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		'rewrite'               => array( 'slug' => 'event-category' ),
	);
register_taxonomy('mep_cat', 'mep_events', $args);


	$labelso = array(
		'name'                       => _x( 'Event Organizer','mage-eventpress' ),
		'singular_name'              => _x( 'Event Organizer','mage-eventpress' ),
		'menu_name'                  => __( 'Organizer', 'mage-eventpress' ),
		'all_items'                  => __( 'All Event Organizer', 'mage-eventpress' ),
		'parent_item'                => __( 'Parent Organizer', 'mage-eventpress' ),
		'parent_item_colon'          => __( 'Parent Organizer:', 'mage-eventpress' ),
		'new_item_name'              => __( 'New Organizer Name', 'mage-eventpress' ),
		'add_new_item'               => __( 'Add New Organizer', 'mage-eventpress' ),
		'edit_item'                  => __( 'Edit Organizer', 'mage-eventpress' ),
		'update_item'                => __( 'Update Organizer', 'mage-eventpress' ),
		'view_item'                  => __( 'View Organizer', 'mage-eventpress' ),
		'separate_items_with_commas' => __( 'Separate Organizer with commas', 'mage-eventpress' ),
		'add_or_remove_items'        => __( 'Add or remove Organizer', 'mage-eventpress' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'mage-eventpress' ),
		'popular_items'              => __( 'Popular Organizer', 'mage-eventpress' ),
		'search_items'               => __( 'Search Organizer', 'mage-eventpress' ),
		'not_found'                  => __( 'Not Found', 'mage-eventpress' ),
		'no_terms'                   => __( 'No Organizer', 'mage-eventpress' ),
		'items_list'                 => __( 'Organizer list', 'mage-eventpress' ),
		'items_list_navigation'      => __( 'Organizer list navigation', 'mage-eventpress' ),
	);

	$argso = array(
		'hierarchical'          => true,
		"public" 				=> true,
		'labels'                => $labelso,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		'rewrite'               => array( 'slug' => 'event-organizer' ),
	);
register_taxonomy('mep_org', 'mep_events', $argso);



}
add_action("init","mep_cpt_tax",10);