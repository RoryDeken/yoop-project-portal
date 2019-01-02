<div class="portal-archive-section cf portal-my-tasks">

	<input id="portal-ajax-url" type="hidden" value="<?php echo admin_url(); ?>admin-ajax.php">
	<input id="portal-task-style" type="hidden" value="Yes">

	<h2 class="portal-box-title"><?php esc_html_e( 'Your Tasks', 'portal_projects' ); ?></h2>

	<div class="portal-grid-row portal-masonry">
		<?php
		$phases = get_field('phases');
		foreach( $tasks as $task ) {
			include( portal_template_hierarchy( 'dashboard/components/tasks/summary.php' ) );
		} ?>
	</div> <!--/.portal-grid-row-->

</div> <!--/.portal-archive-section.portal-my-tasks-->
