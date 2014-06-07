<?php
/*
Plugin Name: DM Cycle2 for WordPress
Plugin URI: https://github.com/DesignMissoula/DM-Cycle2-for-WordPress
Description: Used by Millions to make WordPress Better
Version: 0.2.8
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

function dm_enqueue_scripts() { // Our own unique function called dm_enqueue_scripts
	wp_register_script( 'cycl2-js', plugins_url( 'js/jquery.cycle2.min.js', __FILE__ ), array('jquery'),'',true  );
	wp_enqueue_script( 'cycl2-js' );  // Enqueue our first script

}
add_action( 'wp_enqueue_scripts', 'dm_enqueue_scripts' ); //Hooks our custom function into WP's wp_enqueue_scripts function

add_action('admin_menu' , 'dm_settings_menu');

function dm_settings_menu() {
	add_submenu_page('edit.php?post_type=slide', 'Slideshow Settings', 'Slideshow Settings', 'edit_posts', basename(__FILE__), 'slideshow_settings');
}

// display the admin options page
function slideshow_settings() {
?>
<div>
<h2>Slideshow Settings Page</h2>
Options relating to the slideshow settings powered by <a href="http://jquery.malsup.com/cycle2/" target="_blank">jQuery Cycle2</a>.
<form action="options.php" method="post">
<?php settings_fields('dm_slideshow_options'); ?>
<?php do_settings_sections('dm_slideshow'); ?>

<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
</form></div>

<?php
}

// add the admin settings and such
add_action('admin_init', 'plugin_admin_init');
function plugin_admin_init(){
	register_setting( 'plugin_options', 'plugin_options', 'plugin_options_validate' );
	add_settings_section('plugin_main', 'Main Settings', 'plugin_section_text', 'plugin');
	add_settings_field('plugin_text_string', 'Plugin Text Input', 'plugin_setting_string', 'plugin', 'plugin_main');
}

function plugin_section_text() {
	echo '<p>Main description of this section here.</p>';
}

add_settings_field('plugin_text_string', 'Plugin Text Input', 'plugin_setting_string', 'plugin', 'plugin_main');

function plugin_setting_string() {
	$options = get_option('plugin_options');
	echo "<input id='plugin_text_string' name='plugin_options[text_string]' size='40' type='text' value='{$options['text_string']}' />";
}

// validate our options
function plugin_options_validate($input) {
	$options = get_option('plugin_options');
	$options['text_string'] = trim($input['text_string']);
	 //if(!preg_match('/^[a-z0-9]{32}$/i', $options['text_string'])) {
	 //	$options['text_string'] = '';
	 // }
	return $options;
}

function dm_slideshow_func( $atts ){
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