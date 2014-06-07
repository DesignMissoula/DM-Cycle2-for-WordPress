<?php
/*
Plugin Name: DM Cycle2 for WordPress
Plugin URI: https://github.com/DesignMissoula/DM-Cycle2-for-WordPress
Description: Used by Millions to make WordPress Better
Version: 0.3.2
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
	add_submenu_page('edit.php?post_type=slide', 'Slideshow Settings', 'Slideshow Settings', 'edit_posts', basename(__FILE__), 'dm_slideshow_settings_page_fn');
}


/*
 * Define Constants
 */
define('DM_SHORTNAME', 'dm_slideshow'); // used to prefix the individual setting field id see dm_slideshow_options_page_fields()
define('DM_PAGE_BASENAME', 'dm-slideshow-settings'); // the settings page slug

/*
 * Specify Hooks/Filters
 */
// add_action( 'admin_menu', 'dm_slideshow_add_menu' );
add_action( 'admin_init', 'dm_slideshow_register_settings' );

 /**
 * Helper function for defining variables for the current page
 *
 * @return array
 */
function dm_slideshow_get_settings() {
	
	$output = array();
	
	// put together the output array 
	$output['dm_slideshow_option_name'] 		= 'dm_slideshow_options'; // the option name as used in the get_option() call.
	$output['dm_slideshow_page_title'] 		= __( 'dm_slideshow Settings Page','dm_slideshow_textdomain'); // the settings page title
	$output['dm_slideshow_page_sections'] 	= ''; // the setting section
	$output['dm_slideshow_page_fields'] 		= ''; // the setting fields
	$output['dm_slideshow_contextual_help'] 	= ''; // the contextual help
	
return $output;
}

/*
 * Register our setting
 */
function dm_slideshow_register_settings(){
	
	// get the settings sections array
	$settings_output 	= dm_slideshow_get_settings();
	$dm_slideshow_option_name = $settings_output['dm_slideshow_option_name'];
	
	//setting
	//register_setting( $option_group, $option_name, $sanitize_callback );
	register_setting($dm_slideshow_option_name, $dm_slideshow_option_name, 'dm_slideshow_validate_options' );
}



// ************************************************************************************************************

// Callback functions

/*
 * Admin Settings Page HTML
 * 
 * @return echoes output
 */
function dm_slideshow_settings_page_fn() {
// get the settings sections array
	$settings_output = dm_slideshow_get_settings();
?>
	<div class="wrap">
		<div class="icon32" id="icon-options-general"></div>
		<h2><?php echo $settings_output['dm_slideshow_page_title']; ?></h2>
		
		<form action="options.php" method="post">
			<p class="submit">
				<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes','dm_slideshow_textdomain'); ?>" />
			</p>
			
		</form>
	</div><!-- wrap -->
<?php }

/*
 * Validate input
 * 
 * @return array
 */
function dm_slideshow_validate_options($input) {
	
	// for enhanced security, create a new empty array
	$valid_input = array();
	
return $valid_input; // return validated input
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