<?php
/* Adds classes to the body tag of yoop templates, this will become more robust over time */
function portal_body_classes() {

	global $post;

	$classes		= array();
	$classes[] 		= 'portal-standalone-page';
	$classes[]		= 'portal-acf-ver-' . PORTAL_ACF_VER;
	$all_classes	= '';

	if( is_archive() ) {
		$classes[] = 'portal-dashboard-page';
	}

	if( is_single() ) {
		$classes[] = 'portal-single portal-single-' . $post->ID . ' portal-single-' . get_post_type();
	}

	if( is_admin_bar_showing() ) {
		$classes[] = 'admin-bar';
	}

	if( get_field( 'expand_tasks_by_default' ) ) {
		$classes[] = 'portal-task-expanded';
	}

	if( get_page_template_slug($post->ID) ) {
		$classes[] = get_page_template_slug($post->ID);
	}


	$classes = apply_filters( 'portal_body_classes_array', $classes );

	foreach( $classes as $class ) {
		$all_classes .= $class . ' ';
	}

	return apply_filters( 'portal_body_classes' , $all_classes );

}

add_filter( 'body_class', 'portal_custom_template_body_class' );
function portal_custom_template_body_class( $classes ) {

	if( get_post_type() == 'portal_projects' ) {

		global $post;

		if( get_field( 'expand_tasks_by_default', $post->ID ) ) {
			$classes[] = 'portal-task-expanded';
		}

	}

	return $classes;

}

function portal_the_body_classes() {
	echo esc_attr( portal_body_classes() );
}

function portal_project_wrapper_classes() {

	$classes[]		= '';
	$all_classes	= ''; // Eventually this will be an array, when needed

	return apply_filters( 'portal_project_wrapper_classes' , $all_classes );

}

function portal_the_project_wrapper_classes() {
	echo esc_attr( portal_project_wrapper_classes() );
}

add_filter( 'portal_body_classes', 'portal_user_role_body_classes' );
function portal_user_role_body_classes( $classes ) {

	if( !is_user_logged_in() ) return $classes;

	$cuser = wp_get_current_user();

	foreach( $cuser->roles as $role ) {
		$classes .= 'role-' . $role . ' ';
	}

	return $classes;

}

add_filter( 'portal_body_classes', 'portal_add_template_to_body_class' );
function portal_add_template_to_body_class( $classes ) {

	global $template;
	$template_class = sanitize_title(str_replace( '.php', '', basename($template) ));

	$classes .= $template_class . ' ';

	return $classes;

}

function portal_add_login_template_to_body_class( $classes ) {

	$classes .= 'portal-login-template ';

	return $classes;

}
