<?php
/**
 * portal-assets.php
 * Register and enqueue styles and scripts for Project yoop
 *
 * @author Ross Johnson
 * @copyright 3.7 MEDIA
 * @license GNU GPL version 3 (or later) {@see license.txt}
 * @package yoop
 **/

function portal_custom_template_assets() {

    $post_types = array(
        'portal_projects',
        'portal_teams'
    );


    if( in_array( get_post_type(), $post_types ) && portal_get_option('portal_use_custom_template') ) portal_front_assets(true);

}
add_action( 'wp_enqueue_scripts', 'portal_custom_template_assets' );

function portal_enqueue_calendar_assets() {

    wp_register_script( 'portal-admin-lib' , plugins_url() . '/' . portal_PLUGIN_DIR . '/dist/assets/js/portal-admin-lib.min.js' , array( 'jquery' ) , PORTAL_VER , false );
    wp_enqueue_script( 'portal-admin-lib' );
    wp_enqueue_script( 'portal-frontend' );
    wp_enqueue_script( 'portal-custom' );

}

// Frontend Style and Behavior
// add_action( 'wp_enqueue_scripts', 'portal_front_assets');
function portal_front_assets( $add_portal_scripts = null ) {

    if( ( get_post_type() == 'portal_projects' && portal_get_option('portal_use_custom_template') ) || ( $add_portal_scripts == 1 ) ) {

        // Frontend styling

        wp_register_style( 'portal-frontend', plugins_url() . '/' . portal_PLUGIN_DIR . '/dist/assets/css/portal-frontend.css', false, PORTAL_VER );
        wp_register_style( 'portal-custom', plugins_url() . '/' . portal_PLUGIN_DIR . '/dist/assets/css/portal-custom.css.php', false, PORTAL_VER );
        wp_register_style( 'lato', '//fonts.googleapis.com/css?family=Lato' );

		wp_register_script( 'portal-admin-lib' , plugins_url() . '/' . portal_PLUGIN_DIR . '/dist/assets/js/portal-admin-lib.min.js' , array( 'jquery' ) , PORTAL_VER , false );

        wp_enqueue_style( 'portal-frontend' );
        wp_enqueue_style( 'portal-custom' );
        wp_enqueue_style( 'lato' );

        // Frontend Scripts
        wp_register_script( 'portal-frontend-library', plugins_url() . '/' . portal_PLUGIN_DIR . '/dist/assets/js/portal-frontend-lib.min.js', array( 'jquery' ), PORTAL_VER, false );
        wp_register_script( 'portal-frontend-behavior', plugins_url() . '/' . portal_PLUGIN_DIR . '/dist/assets/js/portal-frontend-behavior.js', array( 'jquery' ), PORTAL_VER, false );

        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'portal-frontend-library' );
        wp_enqueue_script( 'portal-admin-lib' );
        wp_enqueue_script( 'portal-frontend-behavior' );

    }

}

// Admin Style and Behavior

add_action( 'admin_enqueue_scripts', 'portal_admin_assets' );
function portal_admin_assets( $hook ) {

	global $post_type;
    $screen = get_current_screen();

    // Admin Styling

    wp_register_style( 'portal-admin' , plugins_url() . '/' . portal_PLUGIN_DIR . '/dist/assets/css/portal-admin.css', false, PORTAL_VER );
    wp_register_style( 'jquery-ui-portal' , plugins_url() . '/' . portal_PLUGIN_DIR . '/dist/assets/css/jquery-ui-custom.css');

    wp_enqueue_media();
    wp_enqueue_style('portal-admin');
    wp_enqueue_style('wp-color-picker');

	// Determine if we need wp-color-picker or not

	if( $hook == 'settings_page_yoop-license') {
		wp_register_script( 'portaladmin' , plugins_url() . '/' . portal_PLUGIN_DIR . '/dist/assets/js/portal-admin-behavior.js' , array( 'jquery' , 'wp-color-picker' ) , PORTAL_VER , true );
	} else {
		wp_register_script( 'portaladmin' , plugins_url() . '/' . portal_PLUGIN_DIR . '/dist/assets/js/portal-admin-behavior.js' , array( 'jquery' ) , PORTAL_VER , true );
	}

	// Standard Needs
	wp_register_script( 'portal-admin-lib' , plugins_url() . '/' . portal_PLUGIN_DIR . '/dist/assets/js/portal-admin-lib.min.js' , array( 'jquery' ) , PORTAL_VER , false );
	
	// portal determines whether we load this or not. Keeping as a separate file just simplifies things for now, but localizing the value into JS may be better for lowering requests
	wp_register_script( 'portal-wysiwyg' , plugins_url() . '/' . portal_PLUGIN_DIR . '/dist/assets/js/portal-wysiwyg.js' , array( 'jquery' ) , PORTAL_VER , false );


	// If this is the dashboard load dependencies
    if( $screen->id == 'dashboard' || $screen->id == 'portal_projects_page_yoop-calendar' ) {

        $assets = array(
            'scripts'   =>  array(
                'portal-frontend-library',
                'portal-admin-lib',
            ),
            'styles'    =>  array(
                'portal-frontend',
            )
        );

        foreach( $assets['scripts'] as $script ) wp_enqueue_script($script);
        foreach( $assets['styles'] as $style ) wp_enqueue_style($style);

    }

 	// If this is a yoop project load dependencies
	if( $post_type == 'portal_projects' ) {
	    wp_enqueue_script( 'jquery-ui-datepicker' );
	    wp_enqueue_script( 'jquery-ui-slider' );
        wp_enqueue_style( 'jquery-ui-portal' );
	}

	// If this is a project page or settings page load the admin scripts
 	if( ( $post_type == 'portal_projects' ) || ( $hook == 'settings_page_yoop-license' ) ) {
	    wp_enqueue_script( 'portaladmin' );
	}
	
	if ( $hook == 'settings_page_yoop-license' ) {
		wp_enqueue_script( 'portal-admin-lib' );
	}

	// If the shortcode helpers are not disabled load the WYSIWYG buttons
	if((portal_get_option('portal_disable_js') === '0') || (portal_get_option('portal_disable_js') == NULL)) {
		wp_enqueue_script( 'portal-wysiwyg' );
	}

}

// Enqeue All
function portal_add_script( $script ) {
	echo '<script src="' . plugins_url() . '/' . portal_PLUGIN_DIR . '/dist/assets/js/' . $script . '?ver=' . PORTAL_VER . '"></script> ';
}

function portal_add_style( $style ) {
	echo '<link rel="stylesheet" type="text/css" href="' . plugins_url() . '/' . portal_PLUGIN_DIR . '/dist/assets/css/' .$style .'?ver=' . PORTAL_VER .'"> ';
}

add_action( 'portal_enqueue_scripts' , 'portal_add_assets_to_templates');
function portal_add_assets_to_templates() {

	$global_scripts = apply_filters( 'portal_global_scripts', array(
		'jquery.js', // Ensures it is easily available to other plugins without crazy scope problems
		'portal-frontend-lib.min.js',
		'portal-frontend-behavior.js'
	) );

	$pdf_scripts = apply_filters( 'portal_pdf_scripts', array(
		'jspdf.min.js',
		'vendor/html2canvas.js',
		'vendor/html2canvas.svg.js'
	) );

	$global_styles = apply_filters( 'portal_global_styles', array(
		'portal-frontend.css',
		'portal-custom.css.php',
	) );

    $portal_settings = get_option('portal_settings');

    if( isset($portal_settings['portal_use_rtl']) && $portal_settings['portal_use_rtl'] ) {
        $global_styles[] = 'portal-rtl.css';
    }

	$pdf_styles = apply_filters( 'portal_pdf_styles', array(
		'portal-print.css'
	) );

	/* If this is a PDF view, load the necissary assets */

	if( isset( $_GET['pdfview'] ) ) {

		add_action( 'portal_body_classes', 'portal_add_pdf_view_body_class' );

		$global_scripts   = array_merge( $global_scripts, $pdf_scripts);
		$global_styles    = array_merge( $global_styles, $pdf_styles );

	}

	/* If this is the dashboard page, load the necissary assets */

	if( is_archive() ) {

		$global_styles[] .= 'portal-calendar.css';

		$global_scripts[] .= 'portal-admin-lib.min.js';

	}

    $global_scripts = apply_filters( 'portal_global_scripts', $global_scripts );
    $global_styles  = apply_filters( 'portal_global_styles', $global_styles );

	foreach( $global_scripts as $script ) {
		portal_add_script( $script );
	}

	foreach( $global_styles as $style ) {
		portal_add_style( $style );
	}
	
	portal_localize_js(
		'projectyoop',
		array(
			'portal_slug' => portal_get_option( 'portal_slug' , 'yoop' ),
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		)
	);

}

add_filter( 'portal_body_classes' , 'portal_add_pdf_view_body_class' );
function portal_add_pdf_view_body_class( $classes ) {

	if( isset ( $_GET['pdfview'] ) ) {
		$classes .= 'portal-pdf-view ';
	}

	return $classes;

}

add_filter( 'portal_project_wrapper_classes' , 'portal_add_pdf_view_single_row_class' );
function portal_add_pdf_view_single_row_class( $classes ) {

	if( isset ( $_GET['pdfview'] ) ) {
		$classes .= 'portal-width-single ';
	}

	return $classes;

}

add_action( 'portal_js_variables', 'portal_js_translation_strings' );
function portal_js_translation_strings() {

    echo 'var portal_js_label_more = "' . __( 'more', 'portal_projects' ) . '"';

}

add_action( 'admin_footer', 'portal_hide_add_button_from_owners' );
function portal_hide_add_button_from_owners() {

    $screen = get_current_screen();

    if( $screen->parent_file != 'edit.php?post_type=portal_projects' ) return;

    $user = wp_get_current_user();
    if ( in_array( 'portal_project_owner', (array) $user->roles ) ) : ?>

        <style type="text/css">
            .page-title-action {
                display: none;
            }
        </style>

    <?php endif;

}

add_action( 'portal_head', 'portal_add_typeface_style' );
function portal_add_typeface_style() {
    portal_register_style( 'lato', 'https://fonts.googleapis.com/css?family=Lato:400,400i,700,700i' );
}
