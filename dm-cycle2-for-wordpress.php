<?php
/*
Plugin Name: DM Cycle2 for WordPress
Plugin URI: https://github.com/DesignMissoula/DM-Cycle2-for-WordPress
Description: Used by Millions to make WordPress Better
Version: 0.5.7
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
		// 'public' => true,
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
	
	
	if ( function_exists( 'add_image_size' ) ) { 
		$width = esc_attr( get_option( 'dm-slide-width' ) );
		$height = esc_attr( get_option( 'dm-slide-height' ) );
		add_image_size( 'dm-slideshow-slide', $width, $height, true ); 
		
	}

}

function dm_enqueue_scripts() { // Our own unique function called dm_enqueue_scripts
	wp_register_script( 'cycl2-js', plugins_url( 'js/jquery.cycle2.min.js', __FILE__ ), array('jquery'),'',true  );
	wp_enqueue_script( 'cycl2-js' );  // Enqueue our first script

}
add_action( 'wp_enqueue_scripts', 'dm_enqueue_scripts' ); //Hooks our custom function into WP's wp_enqueue_scripts function

add_action('admin_menu' , 'dm_settings_menu');

function dm_settings_menu() {
	add_submenu_page('edit.php?post_type=slide', 'Slideshow Settings', 'Slideshow Settings', 'edit_posts', basename(__FILE__), 'dm_slideshow_settings_page');
}

function dm_slideshow_settings_page() {
?>
	<div class="wrap">
	<?php screen_icon(); ?>
	<h2>Slideshow Settings</h2>
	<?php
	if( isset($_GET['settings-updated']) ) { ?>
	    <div id="message" class="updated">
	        <p><strong><?php _e('Settings saved.') ?></strong></p>
	    </div>
	<?php } ?>
	<form action="options.php" method="POST">
	<?php settings_fields( 'slides-settings-group' ); ?>
	<?php do_settings_sections( 'slide-settings' ); ?>
	<?php submit_button(); ?>
	</form>
	</div>
<?php
}

add_action( 'admin_init', 'slide_settings_init' );
function slide_settings_init() {
	// Settings
	register_setting( 'slides-settings-group', 'dm-slide-width', 'intval' );
	register_setting( 'slides-settings-group', 'dm-slide-height', 'intval' );
	register_setting( 'slides-settings-group', 'dm-slide-count', 'intval' );

	// Sections
	add_settings_section( 'general-slide-settings', 'General Slide Settings', 'section_one_callback', 'slide-settings' );

	// Fields
	add_settings_field( 'field-one', 'Slide Width', 'field_one_callback', 'slide-settings', 'general-slide-settings' );
	add_settings_field( 'field-two', 'Slide Height', 'field_two_callback', 'slide-settings', 'general-slide-settings' );
	add_settings_field( 'field-three', 'Slide Count', 'field_three_callback', 'slide-settings', 'general-slide-settings' );
}

function section_one_callback() {
	echo "Sitewide settings for the slideshow plugin.";
}

function field_one_callback() {
	$setting_value = esc_attr( get_option( 'dm-slide-width' ) );
	echo "<input class='small-text' type='text' id='field-one' name='dm-slide-width' value='$setting_value' />px
	<p class='description'>The width in pixels for the slideshow slides</p>";
}
function field_two_callback() {
	$setting_value = esc_attr( get_option( 'dm-slide-height' ) );
	echo "<input class='small-text' type='text' id='field-two' name='dm-slide-height' value='$setting_value' />px
	<p class='description'>The height in pixels for the slideshow slides</p>";
}
function field_three_callback() {
	$setting_value = esc_attr( get_option( 'dm-slide-count' ) );
	echo "<input class='small-text' type='text' id='field-three' name='dm-slide-count' value='$setting_value' />
	<p class='description'>The number of slides to be shown in the slideshow</p>";
}


function dm_slideshow_func( $atts ){
	$return = "";
	$return .= '<div class="cycle-slideshow" data-cycle-slides="> div">';
        	
        $slide_count = esc_attr( get_option( 'dm-slide-count' ) );	
        $args = array('post_type'=>'slide', 'post_status'=>'publish', 'posts_per_page'=>$slide_count,);	

        // The Query
		query_posts( $args );
		
		// The Loop
		while ( have_posts() ) : the_post();
		    $return .= '<div class="slide">'.get_the_post_thumbnail(get_the_id(), 'dm-slideshow-slide').'</div>';
		endwhile;
		
		//Reset Query
		wp_reset_query();

    $return .= '</div>';
    
    return $return;
}

add_shortcode( 'slideshow', 'dm_slideshow_func' );


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