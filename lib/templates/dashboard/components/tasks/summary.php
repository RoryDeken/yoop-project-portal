<?php
/**
 * Project tiles on the dashboard
 * @var [type]
 */
$post_id 			= $task['project_id'];
$cuser				= wp_get_current_user();
$phases_and_tasks 	= portal_get_item_count( $post_id, $cuser->ID );
$project_logo		= get_field( 'client_project_logo', $post_id );
$start_date 		= portal_text_date(get_field( 'start_date', $post_id ));
$end_date   		= portal_text_date(get_field( 'end_date', $post_id ));
?>

<div class="portal-task-project portal-masonry-item portal-col-lg-4 portal-col-sm-6 <?php echo esc_attr( 'portal-task-project-' . $post_id ); ?> portal-masonry-item" id="<?php echo esc_attr( 'portal-task-project-' . $post_id ); ?>">
	<div class="portal-task-project-wrapper">

		<hgroup class="portal-task-project-header">

			<h2 class="portal-task-project-title">
				<a href="<?php echo esc_url(get_the_permalink( $task['project_id'] )); ?>">
					<?php if( !empty($project_logo) ): ?>
						<img src="<?php echo esc_url( $project_logo['sizes']['medium'] ); ?>" alt="<?php the_field( 'client', $post_id ); ?>" class="portal-client-summary-logo">
					<?php endif; ?>
					<b><?php echo get_the_title($post_id); ?></b>
					<?php if( get_field('client') ) echo '<strong>' . get_field( 'client', $post_id ) . '</strong>'; ?>
				</a>
			</h2>

			<?php
			$completed = portal_compute_progress($post_id);
			if( !$completed ) $completed = 0; ?>

			<p class="portal-progress">
				<span class="portal-<?php echo esc_attr($completed); ?>" data-toggle="portal-tooltip" data-placement="top" title="<?php echo esc_attr($completed . '% ' . __( 'Complete', 'portal_projects' ) ); ?>">
					<b><?php echo esc_html($completed); ?>%</b>
				</span>
				<i class="portal-progress-label"> <?php esc_html_e('Progress','portal_projects'); ?> </i>
			</p>

			<?php if( $start_date && $end_date ) portal_the_simplified_timebar($post_id); ?>

			<ul class="portal-grid-row cf portal-task-breakdown">
				<?php
				$breakdown = array(
					array(
						'class'	=>	'portal-element-tally-all',
						'count'	=>	$phases_and_tasks['tasks'],
						'label'	=>	__( 'Assigned', 'portal_projects' )
					),
					array(
						'class'	=>	'portal-element-tally-started',
						'count'	=>	$phases_and_tasks['started'],
						'label'	=>	__( 'In Progress', 'portal_projects' )
					),
					array(
						'class'	=>	'portal-element-tally-completed',
						'count'	=>	$phases_and_tasks['completed'],
						'label'	=>	__( 'Completed', 'portal_projects' )
					),
				);
				foreach( $breakdown as $stat ): ?>
					<li class="portal-col-xs-4 portal-element-tally <?php echo esc_attr($stat['class']); ?>" data-count="<?php echo esc_attr($stat['count']); ?>">
						<strong><?php echo esc_html( $stat['count'] ); ?></strong>
						<span><?php echo esc_html( $stat['label'] ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>

		</hgroup> <!--/.portal-task-project-header-->

		<div class="portal-my-tasks portal-task-section">

			<input id="portal-ajax-url" type="hidden" value="<?php echo admin_url(); ?>admin-ajax.php">

			<?php
			$phase_index 		= 0;
			$overall_auto	= get_field( 'automatic_progress', $post_id );
			$phase_auto		= get_field( 'phases_automatic_progress', $post_id );
			$phases			= get_field( 'phases', $post_id );

			while( have_rows( 'phases', $post_id ) ) { the_row();

				$tasks = portal_get_tasks( $post_id, $phase_index );
				$phase = $phases[$phase_index];

				if( !empty($tasks) ):

					$t = 0;
					foreach ( $tasks as $task ) { 
						
						$show_task = $task[ 'assigned' ] == $cuser->ID;
						$show_task = apply_filters( 'portal_show_task_on_dashboard', $show_task, $phase, $task );
						
						if ( $show_task ) {
							$t++;
						}
						
					}

					if( $t > 0 ): ?>

						<div class="portal-tasks-phase" data-phase_id=<?php echo $phase['phase_id']; ?>>

							<h3><?php the_sub_field( 'title' ); ?></h3>

							<ul class="portal-task-list">
								<?php
								foreach( $tasks as $task ):
													  
									$show_task = $task[ 'assigned' ] == $cuser->ID;
									$show_task = apply_filters( 'portal_show_task_on_dashboard', $show_task, $phase, $task );
													  
									if ( $show_task ) {
										include( portal_template_hierarchy( 'projects/phases/tasks/single/entry.php' ) );
									}
													  
								endforeach; ?>
							</ul>

							<?php do_action( 'portal_after_dashboard_phase_tasks', $phase_index, $post_id ); ?>

						</div>

					<?php endif;

				endif;

				$phase_index++;

			} ?>

		</div> <!-- /.portal-my-tasks -->

	</div>
</div>
