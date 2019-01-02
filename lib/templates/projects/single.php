<?php
/* Custom Single.php for project only view */
global $post, $doctype; ?>
<!DOCTYPE html>
<html <?php language_attributes( $doctype ); ?>>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php portal_the_title( $post ); ?></title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php if( get_field( 'hide_from_search_engines' , $post->ID ) ): ?>
        <meta name="robots" content="noindex, nofollow">
	<?php endif; ?>

	<?php do_action( 'portal_enqueue_scripts' ); ?>

    <!--[if lte IE 9]>
        <script src="<?php echo plugins_url() . '/' . PROJECT_YOOP_DIR; ?>/assets/js/html5shiv.min.js"></script>
        <script src="<?php echo plugins_url() . '/' . PROJECT_YOOP_DIR; ?>/assets/js/css3-mediaqueries.js"></script>
    <![endif]-->

    <!--[if IE]>
        <link rel="stylesheet" type="text/css" src="<?php echo plugins_url() . '/' . PROJECT_YOOP_DIR; ?>/assets/css/ie.css">
    <![endif]-->

    <?php do_action( 'portal_head' );  ?>

</head>
<body id="portal-projects" class="<?php portal_the_body_classes(); ?>">

<?php $yoop_access = yoop_check_access( $post->ID ); ?>

<div class="portal-standard-template <?php portal_the_project_wrapper_classes(); ?>">

    <?php
    while( have_posts() ): the_post(); ?>

    <?php include( portal_template_hierarchy( 'global/header/masthead' ) ); ?>

        <?php if ( $yoop_access ) : ?>

            <?php
            if( is_user_logged_in() ) {
                include( portal_template_hierarchy( 'global/header/navigation-sub' ) );
            } ?>

            <?php do_action( 'portal_before_overview' ); ?>

            <section id="overview" class="wrapper portal-section">

                <?php do_action( 'portal_before_essentials' ); ?>
                <?php do_action( 'portal_the_essentials' ); ?>
				<?php do_action( 'portal_after_essentials' ); ?>

            </section> <!--/#overview-->

            <?php do_action( 'portal_between_overview_progress' ); ?>

            <?php if( get_field( 'milestones', $post->ID ) ): ?>
                <section id="portal-progress" class="wrapper cf portal-section">

                    <?php do_action( 'portal_before_progress' ); ?>
                    <?php do_action( 'portal_the_progress' ); ?>
                    <?php do_action( 'portal_after_progress' ); ?>

                </section> <!--/#progress-->
            <?php endif; ?>

            <?php do_action( 'portal_between_progress_phases' ); ?>
	
			<?php $phase_auto = get_field( 'phases_automatic_progress', $post->ID ); ?>

            <section id="portal-phases" class="wrapper portal-section" data-phase-auto="<?php echo ( isset($phase_auto[0]) && $phase_auto[0] !== NULL ? $phase_auto[0] : 'No' ); ?>">

                <?php do_action( 'portal_before_phases' ); ?>
                <?php do_action( 'portal_the_phases' ); ?>
                <?php do_action( 'portal_after_phases' ); ?>

            </section>

            <?php do_action( 'portal_between_phases_discussion' ); ?>

            <!-- Discussion -->
            <?php if ( comments_open() ) : ?>
                <section id="portal-discussion" class="portal-section cf">

                    <?php
                    do_action( 'portal_before_discussion' );
                    do_action( 'portal_the_discussion' );
                    do_action( 'portal_after_discussion' );
                    ?>

                </section>
            <?php endif; ?>

        <?php endif; ?>

        <?php if( ! $yoop_access ) { ?>

			<?php include( portal_template_hierarchy( 'global/login.php' ) ); ?>

		<?php } ?>

    <?php endwhile; // ends the loop ?>

</div> <!--/#portal-project-->

<?php
include( portal_template_hierarchy( 'global/navigation-off.php' ) );
do_action( 'portal_footer' ); ?>

</body>
</html>
