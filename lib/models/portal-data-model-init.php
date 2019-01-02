<?php
/**
 * portal-data-model.php
 *
 * Simplifying, loads the data reorganized data models
 *
 * @category controller
 * @package portal-projects
 * @author Ross Johnson
 * @version 1.0
 * @since 1.3.6
 */

$libs = array(
	'portal-projects',
	'portal-project-types',
	'portal-teams',
	'portal-notifications',
	'portal-priorities'
);

foreach( $libs as $lib ) {

	require_once( $lib . '.php' );

}
