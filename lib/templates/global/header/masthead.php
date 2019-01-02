<?php do_action( 'portal_before_primary_bar' ); ?>
<div id="portal-primary-header" class="portal-grid-row cf">

	<div class="portal-primary-bar">

		<?php
		do_action( 'portal_primary_bar_before_logo' );

		if( portal_get_option('portal_logo') != '' && portal_get_option('portal_logo') != 'http://' ):

			$link = ( portal_get_option('portal_logo_link') ? portal_get_option('portal_logo_link') : get_post_type_archive_link('portal_projects') ); ?>

			<div class="portal-masthead-logo">
				<a href="<?php echo esc_url($link); ?>" class="portal-single-project-logo"><img src="<?php echo portal_get_option('portal_logo'); ?>"></a>
			</div>
		<?php endif; ?>

		<?php
		do_action( 'portal_primary_bar_before_menu' ); ?>

		<nav class="nav portal-masthead-nav" id="portal-main-nav">
			<ul>
				<li id="nav-menu"><a href="#">Menu</a></li>
			</ul>
		</nav>

		<?php
		do_action( 'portal_primary_bar_before_user' );

		include( portal_template_hierarchy( 'global/header/user' ) );

		do_action( 'portal_primary_bar_before_edit' );

		do_action( 'portal_primary_bar_after_edit' ); ?>

	</div>

</div>
<?php
do_action( 'portal_after_primary_bar'); ?>
