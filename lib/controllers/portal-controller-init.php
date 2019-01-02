<?php
/**
 * portal-controller-init.php
 *
 * Master controller file, builds all the controllers.
 */

$library = apply_filters( 'portal_controller_files', array(
    'portal-discussions',					// Comment management
    'portal-permissions',					// User and permission management
    'portal-documents',					// Document management
    'portal-timing',					    // Timing management
    'portal-progress',                     // Progress management
    'portal-phases',                       // Phase controller
    'portal-tasks',
    'portal-notifications',
    'portal-notification-email',
    'portal-activity'
) );

do_action( 'portal_before_controllers_loaded' );

foreach( $library as $lib ) {

    require_once( $lib . '.php' );

}

do_action( 'portal_after_controllers_loaded' );
