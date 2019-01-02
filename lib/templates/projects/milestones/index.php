<?php
$post_id 		= isset( $post_id ) ? $post_id : get_the_ID();

$completed 		= portal_compute_progress( $post_id );
$all_milestones	= portal_organize_milestones( get_field( 'milestones', $post_id ) );

if( !empty( $all_milestones ) ): ?>

	<div class="<?php echo esc_attr($style); ?>">

		<hgroup class="portal-section-heading">
			<?php do_action( 'portal_before_milestone_title', $all_milestones, $completed, $post_id ); ?>
			<h2 class="portal-section-title"><?php esc_html_e( 'Milestones', 'portal_projects' ); ?></h2>
			<?php do_action( 'portal_after_milestone_title', $all_milestones, $completed, $post_id ); ?>
			<p class="portal-section-data"><?php echo esc_html($all_milestones['completed']); ?> / <?php echo esc_html( count( get_field('milestones', $post_id )) ) . ' ' . __( 'Completed', 'portal_projects' ); ?></p>
			<?php do_action( 'portal_after_milestone_data', $all_milestones, $completed, $post_id ); ?>
		</hgroup> <!--/.portal-section-heading-->

		<div class="portal-milestone-timeline">

			<p class="portal-progress"><span class="portal-<?php echo esc_attr($completed); ?>"><b><?php echo esc_html($completed); ?>%</b></span></p>

			<div class="portal-enhanced-milestones">
				<ul class="portal-milestone-dots">
					<?php foreach( $all_milestones['milestones'] as $milestones ) include( portal_template_hierarchy( 'projects/milestones/single/marker' ) ); ?>
				</ul> <!--/.portal-milestone-dots-->
			</div> <!--/.portal-enhanced-milestones-->

		</div> <!--/.portal-milestone-timeline-->

	</div>

<?php endif; ?>
