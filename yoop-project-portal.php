<?php
/**
 * Plugin Name: Yoop Project Portal
 * Description: Yoop Project Portal
 * Version: 1.0.0
 */

/**
 * Initialize the plugin by loading the initial files
 *
 *
 * @param NULL
 * @return NULL
 */
$library = array(
	'lib/portal-init.php', 		// Primary initilization file
	//'lib/portal-welcome.php',		// Welcome screen
	'portal-settings.php',  		// settings file
	'portal-scheduled-notifications/portal-scheduled-notifications.php',
	'portal-new-user-project/portal-new-user-project.php'
);

foreach( $library as $book ) include_once( $book );
/*
if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	// load our custom updater
	include( dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php' );
}
*/


/**
 * Important definitions used throughout the application
 *
 */
$constants = array(
	'YOOP_PORTAL_URI'			=>	plugins_url( '', __FILE__ ),
	'PROJECT_YOOP_DIR'			=>	__DIR__,
	'PROJECT_YOOP_STORE_URL'	=> 'https://www.projectyoop.com',
	'EDD_PROJECT_YOOP'			=> 'Project yoop Single',
	'PORTAL_VER'						=>	'1.6.2',
	'PORTAL_ACF_VER'					=>	( function_exists( 'update_sub_field' ) ? 5 : 4 ),
	'PORTAL_PLUGIN_LICENSE_PAGE'		=>	'yoop-license',
	'PORTAL_DB_VER'					=>	8
);

foreach( $constants as $constant => $val ) {
	if( !defined( $constant ) ) define( $constant, $val );
}

/***
  *
  * Initalization and activiation hooks
  *
  */
$plugin = plugin_basename( __FILE__ );

//add_filter( "plugin_action_links_$plugin", "add_license_link" );
//add_action( 'after_plugin_row_yoop-project-portal/yoop-project-portal.php', 'add_license_after_row' );

register_activation_hook( __FILE__, 'portal_activation_hook' );
function portal_activation_hook() {
	add_action( 'portal_loaded_post_type_project', 'flush_rewrite_rules' );
	//portal_welcome_screen_activate();
}

/***
  *
  * Localization for translations
  *
  */
	/*
add_action( 'plugins_loaded', 'portal_localize_init', 901 );
function portal_localize_init() {
    load_plugin_textdomain( 'portal_projects', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
*/
/**
  *
  * Licensing and update script
  *
  * @return NULL
  *
  */
	/*
add_action( 'admin_init', 'edd_project_yoop_plugin_updater' );
function edd_project_yoop_plugin_updater() {

	// retrieve our license key from the DB
	$license_key = trim( portal_get_option( 'edd_yoop_license_key' ) );

	// setup the updater
	$edd_updater = new EDD_SL_Plugin_Updater( PROJECT_YOOP_STORE_URL, __FILE__, array(
			'version' 	=> PORTAL_VER, 				// current version number
			'license' 	=> $license_key, 		// license key (used get_option above to retrieve from DB)
			'item_name' => EDD_PROJECT_YOOP, 	// name of this plugin
			'author' 	=> 'SnapOrbital',  // author of this plugin
			'url'       => home_url()
		)
	);

}
*/

/*
function add_license_link( $links ) {

	$license_key 	= trim( portal_get_option('edd_yoop_license_key') );
	$label 			= ( !$license_key ? __( 'Register License', 'portal_projects' ) : __( 'Settings', 'portal_projects' ) );
	$settings_link 	= '<a href="' . site_url() . '/wp-admin/options-general.php?page=yoop-license">' . $label . '</a>';

	array_unshift( $links, $settings_link );

	return $links;

}
*/
/**
  * Add a row after the yoop plugin row, reminding users to activate their license
  *
  *
  * @return NULL
  *
  **/
/*
function add_license_after_row() {

	$license_key = trim( portal_get_option( 'edd_yoop_license_key' ) );
	if( !$license_key ) {
		echo '</tr><tr class="plugin-update-tr"><td colspan="3"><div class="update-message"><a href="' . site_url() . '/wp-admin/options-general.php?page=yoop-license">' . __( 'Activate your license', 'portal_projects' ) . '</a> ' . __( 'for automatic upgrades. Need a license?', 'portal_projects' ) . ' <a href="http://www.projectyoop.com" target="_new">' . __( 'Purchase one', 'portal_projects' ) . '</a></div></td>';
	}

}
*/
/**
 * Deactivation Clean-up
 */

 register_deactivation_hook(__FILE__, 'portal_plugin_deactivation');
 function portal_plugin_deactivation() {
 	wp_clear_scheduled_hook('portal_send_cron_notifications');
 }
