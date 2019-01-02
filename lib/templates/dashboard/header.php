<?php
global $post, $doctype;
$cuser  = wp_get_current_user(); ?>
<!DOCTYPE html>
<html <?php language_attributes( $doctype ); ?>>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title><?php portal_the_title( $post ); ?></title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

	<meta name="robots" content="noindex, nofollow">

    <?php // wp_head(); // Removed for visual consistency ?>

	<?php do_action( 'portal_enqueue_scripts' ); ?>

    <!--[if lte IE 9]>
    	<script src="<?php echo plugins_url() . '/' . PROJECT_YOOP_DIR; ?>/assets/js/html5shiv.min.js"></script>
    	<script src="<?php echo plugins_url() . '/' . PROJECT_YOOP_DIR; ?>/assets/js/css3-mediaqueries.js"></script>
    <![endif]-->

	<!--[if IE]>
    	<link rel="stylesheet" type="text/css" src="<?php echo plugins_url() . '/' . PROJECT_YOOP_DIR; ?>/assets/css/ie.css">
    <![endif]-->

    <?php do_action( 'portal_head' ); ?>

    <script>
        <?php do_action( 'portal_localize' ); ?>
    </script>

</head>
<body id="portal-projects" class="<?php portal_the_body_classes(); ?>">

	<?php
    do_action( 'portal_dashboard_page' );
    do_action( 'portal_dashboard_page_' . __FILE__ );

    if( is_user_logged_in() ):
        include( portal_template_hierarchy( 'global/header/masthead' ) );
    endif; ?>
