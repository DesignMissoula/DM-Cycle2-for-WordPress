<?php
/*
Plugin Name: DM Cycle2 for WordPress
Plugin URI: https://github.com/DesignMissoula/DM-Cycle2-for-WordPress
Description: Used by Millions to make WordPress Better
Version: 0.2.3
Author: Bradford Knowlton
Author URI: http://bradknowlton.com/
License: GPLv2
*/

define( 'WP_GITHUB_FORCE_UPDATE', true );

if(!class_exists('WP_GitHub_Updater')){
	include_once plugin_dir_path( __FILE__ ) . 'includes/github-updater.php';
}

add_action( 'init', 'register_cpt_slide' );
function register_cpt_slide() {
	$labels = array(
		'name' => _x( 'Slides', 'slide' ),
		'singular_name' => _x( 'Slide', 'slide' ),
		'add_new' => _x( 'Add New', 'slide' ),
		'add_new_item' => _x( 'Add New Slide', 'slide' ),
		'edit_item' => _x( 'Edit Slide', 'slide' ),
		'new_item' => _x( 'New Slide', 'slide' ),
		'view_item' => _x( 'View Slide', 'slide' ),
		'search_items' => _x( 'Search Slides', 'slide' ),
		'not_found' => _x( 'No slides found', 'slide' ),
		'not_found_in_trash' => _x( 'No slides found in Trash', 'slide' ),
		'parent_item_colon' => _x( 'Parent Slide:', 'slide' ),
		'menu_name' => _x( 'Slides', 'slide' ),
	);
	$args = array(
		'labels' => $labels,
		'hierarchical' => false,
		'supports' => array( 'title', 'thumbnail' ),
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'show_in_nav_menus' => true,
		'publicly_queryable' => true,
		'exclude_from_search' => false,
		'has_archive' => true,
		'query_var' => true,
		'can_export' => true,
		'rewrite' => true,
		'capability_type' => 'post',
		'menu_icon' => 'dashicons-format-gallery'
	);
	register_post_type( 'slide', $args );
}

function dw_enqueue_scripts() { // Our own unique function called dw_enqueue_scripts
	wp_register_script( 'cycl2-js', plugins_url( 'js/jquery.cycle2.min.js', __FILE__ ), array('jquery'),'',true  );
	wp_enqueue_script( 'cycl2-js' );  // Enqueue our first script

}
add_action( 'wp_enqueue_scripts', 'dw_enqueue_scripts' ); //Hooks our custom function into WP's wp_enqueue_scripts function

function dw_slideshow_func( $atts ){
	return '
	<div class="cycle-slideshow" data-cycle-slides="> div">
        	<div><img src="'.get_bloginfo('stylesheet_directory').'/images/slider1.png" alt="slider1"/></div>
             <div><img src="'.get_bloginfo('stylesheet_directory').'/images/slider2.png" alt="slider2"/></div>
             <div><img src="'.get_bloginfo('stylesheet_directory').'/images/slider3.png" alt="slider3"/></div>
             <div><img src="'.get_bloginfo('stylesheet_directory').'/images/slider4.png" alt="slider4"/></div>
             <div><img src="'.get_bloginfo('stylesheet_directory').'/images/slider5.png" alt="slider5"/></div>
             <div><img src="'.get_bloginfo('stylesheet_directory').'/images/slider6.png" alt="slider6"/></div>
             <div><img src="'.get_bloginfo('stylesheet_directory').'/images/slider7.png" alt="slider7"/></div>

     </div>
	';
}
add_shortcode( 'slideshow', 'dw_slideshow_func' );


add_action( 'init', 'ss_github_plugin_updater_test_init' );
function ss_github_plugin_updater_test_init() {

	include_once plugin_dir_path( __FILE__ ) . 'includes/github-updater.php';

	if ( is_admin() ) { // note the use of is_admin() to double check that this is happening in the admin

		$config = array(
			'slug' => plugin_basename( __FILE__ ),
			'proper_folder_name' => 'DM-Cycle2-for-WordPress-master',
			'api_url' => 'https://api.github.com/repos/DesignMissoula/DM-Cycle2-for-WordPress',
			'raw_url' => 'https://raw.github.com/DesignMissoula/DM-Cycle2-for-WordPress/master',
			'github_url' => 'https://github.com/DesignMissoula/DM-Cycle2-for-WordPress',
			'zip_url' => 'https://github.com/DesignMissoula/DM-Cycle2-for-WordPress/archive/master.zip',
			'sslverify' => true,
			'requires' => '3.8',
			'tested' => '3.9.1',
			'readme' => 'README.md',
			'access_token' => '',
		);

		new WP_GitHub_Updater( $config );

	}

}