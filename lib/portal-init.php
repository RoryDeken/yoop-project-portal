<?php
/**
 * portal-init.php
 *
 * Master file, builds everything.
 * @package portal-projects
 *
 *  NOTE: Premium "Repeater Field" Add-on is NOT to be used or distributed outside of this plugin per original copyright information from ACF
 *	http://www.advancedcustomfields.com/resources/getting-started/including-lite-mode-in-a-plugin-theme/
 *
 */

add_action( 'plugins_loaded', 'portal_core_init', 900 );

function portal_core_init() {

	do_action( 'portal_before_yoop_loaded' );

	// Add menus locations for the dashboard and single project
	$menus = apply_filters( 'portal_menu_locations', array(
		'portal_project_menu'	=>	__('Add to the yoop single project settings menu' ),
		'portal_section_menu'	=>	__( 'Add to the yoop section menu' ),
		'portal_archive_menu'	=>	__('Add to the yoop dashboard settings menu' ),
		'portal_footer_menu'	=>	__( 'Links in the yoop footer' ),
	) );

	foreach( $menus as $id => $description ) register_nav_menu( $id, $description );

	$library = array(
		'portal-migrations',								// Migraiton scripts to upgrade from lite or lower versions
		'vendor/portal-vendor-init',						// All the outside vendor libraries
		'controllers/portal-controller-init',				// Controllers
		'models/portal-data-model-init',					// Builds all the data models
	    'portal-templates',								// Template management
	    'portal-view',										// Hooks to add templates to specific places
	    'portal-assets',									// Asset management, style sheets and JS
	    'portal-helpers',									// Utility and helper functions
	    'portal-base-shortcodes',							// Standard shortcodes for LITE and PRO
	    'portal-widgets',									// Custom widgets
		'portal-hooks',									// Slow consildation of all hooks
	    'portal-admin',									// Admin management
	);

	// Check to see if advanced custom fields is already installed, if not add it
	global $acf;

	if( !$acf ) {

		if( !defined( 'ACF_LITE' ) ) define( 'ACF_LITE' , true );
		$library[] = 'vendor/acf/master/acf';

	}

	if( ( !function_exists( 'duplicate_post_is_current_user_allowed_to_copy' ) ) && ( portal_get_option( 'portal_disable_clone_post' ) != '1' ) ) {
		include_once( 'vendor/clone/duplicate-post.php' );
	}

	// Check to see if this is a paid version of yoop
	if( file_exists( dirname( __FILE__ ) . '/pro/portal-pro-init.php' ) ) {

		// This is a professional version, define constants and load libraries
	    define( 'portal_PLUGIN_TYPE', 'professional' );
	    define( 'portal_PLUGIN_DIR', 'yoop-project-portal' );

		$library[] = 'pro/portal-pro-init';

	    include_once( 'pro/portal-pro-init.php' );

		// Check to see if the ACF Repeater field or ACF Repeater collapser are installed
	    if( ( !class_exists( 'acf_field_repeater' ) ) && ( !file_exists( ABSPATH . '/wp-content/plugins/acf-repeater/acf-repeater.php' ) ) ) {
			$library[] = 'vendor/acf/repeater/acf-repeater';
		}

	    if( !function_exists( 'acf_repeater_collapser_assets' ) ) {
			$library[] = 'vendor/acf/collapse/acf_repeater_collapser';
		}

	} else {

		// This is a free version, load the stripped down libraries
	    define( 'portal_PLUGIN_TYPE' , 'lite' );
	    define( 'portal_PLUGIN_DIR' , 'yoop-project-portal-lite' );

		$library[] = 'lite/portal-lite-init';

	}

	// Loop through the library of resources and load them
	foreach ( $library as $book ) include_once( $book . '.php' );

	do_action( 'portal_after_yoop_loaded' );

	if( !get_option( 'portal_data_models' ) || get_option( 'portal_data_models' ) < PORTAL_VER ) {
		add_action( 'init', 'flush_rewrite_rules' );
		update_option( 'portal_data_models', PORTAL_VER );
	}

}

add_action( 'admin_init', 'portal_optimize_acf_performance' );
function portal_optimize_acf_performance() {

	if( 'portal_projects' == get_post_type() && function_exists('update_sub_field') ) {
		add_filter('acf/setting/remove_wp_meta_box', '__return_true');
	}

}

function my_login_redirect( $url, $request, $user ){
if( $user && is_object( $user ) && is_a( $user, 'WP_User' ) ) {
if( $user->has_cap( 'administrator')) {
$url = admin_url();
} else {
$portal_slug = portal_get_option( 'portal_slug' , 'yoop' );
$url = home_url($portal_slug);
}
}
return $url;
}
add_filter('login_redirect', 'my_login_redirect', 10, 3 );
