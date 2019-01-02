<?php
$deps = array(
	'inc/settings',
	'inc/controller',
);

if( defined( 'PORTAL_VER' ) ) {
	foreach( $deps as $dep ) require_once( $dep . '.php' );
}
