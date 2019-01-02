<?php do_action( 'portal_dashboard_before_my_projects' ); ?>

<div class="portal-archive-section">

	<?php do_action( 'portal_dashboard_my_projects_before_heading' ); ?>

	<div class="portal-table-header cf">
		<ul class="portal-sub-nav portal-pull-left">
			<?php
			do_action( 'portal_dashboard_my_projects_before_nav' );

			$active_link 	= get_post_type_archive_link('portal_projects') . ( get_option( 'permalink_structure' ) ? 'status/active' : '&portal_status_page=active' );
			$completed_link = get_post_type_archive_link('portal_projects') . ( get_option( 'permalink_structure' ) ? 'status/completed' : '&portal_status_page=completed' ); ?>
			<li><a href="<?php echo esc_url($active_link); ?>" class="portal-archive-link <?php if( ( get_query_var( 'portal_status_page' ) == 'active' ) || ( !get_query_var( 'portal_status_page' ) ) ) { echo 'active'; } ?>"><?php esc_html_e( 'Active Projects', 'portal_projects' ); ?></a></li>
			<li><a href="<?php echo esc_url($completed_link); ?>" class="portal-archive-link <?php if( get_query_var( 'portal_status_page' ) == 'completed' ) { echo 'active'; } ?>"><?php esc_html_e( 'Completed Projects', 'portal_projects' ); ?></a></li>
			<?php do_action( 'portal_dashboard_my_projects_after_nav' ); ?>
		</ul>
		<?php include( portal_template_hierarchy( 'global/search-form.php' ) ); ?>
	</div>

	<?php do_action( 'portal_dashboardmy_projects_after_heading' ); ?>

	<div class="portal-archive-list-wrapper">
		<?php echo portal_archive_project_listing( $projects ); ?>
	</div>

</div>

<?php do_action( 'portal_dashboard_after_my_projects' ); ?>
