<?php
/**
 * Description of portal-pro-init
 *
 * Load all the dependencies for paid version of yoop
 * @package portal-projects
 *
 *
 */

$profesional_library = array(
	'portal-pro-shortcodes',		// Shortcodes specific to PRO
	'portal-notifications',		// Notification management
	'portal-documents',			// Document management
	'portal-ajax',					// Front end modifications / updating
	'portal-teams'					// Populate team list
);

foreach( $profesional_library as $book ) {

	include_once( $book . '.php' );

}

// ACF doesn't support hidden fields by default
if ( ! class_exists( 'acf_field_hidden' ) ) {
	
	require_once __DIR__ . '/fields/acf-hidden/acf-hidden.php';
	
}

/**
 * portal_load_meta_fields
 *
 * Load custom meta fields via a hook so they can be adjusted later.
 * Uses portal_load_field_template() so users can override fields in their theme directory
 *
 */

add_action( 'init' , 'portal_load_meta_fields' , 999 );
function portal_load_meta_fields() {

	// Make sure ACF is running in some capacity
	if(function_exists("register_field_group")) {

		// Load the fields via portal_load_field_template so users can import their own

		portal_load_field_template( 'overview' );
		portal_load_field_template( 'milestones' );
		portal_load_field_template( 'phases' );
		portal_load_field_template( 'teams' );

	}

}
