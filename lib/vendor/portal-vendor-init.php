<?php
/**
 * portal-vendor-init.php
 *
 * Simplifying, loads the vendor libraries
 *
 * @category controller
 * @package portal-projects
 * @author Ross Johnson
 * @version 1.0
 * @since 1.3.6
 */

$library = array(
    'acf/slider/acf-slider',					// Custom slider field for ACF
    'acf/lite-wysiwyg/lite-wysiwyg',            // Lite WYSIWYG
    'acf/project-users/project-users',		    // Custom field of all project users
    'acf/project-phases/project-phases', // Custom field for all project Phases
	'acf/project-tasks/project-tasks' // Custom field for all project Tasks
);

foreach( $library as $lib ) require_once( $lib . '.php' );
