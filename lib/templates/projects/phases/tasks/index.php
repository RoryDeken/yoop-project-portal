<?php do_action( 'portal_before_task_wrapper', $post_id, $phase_index );

if( ( portal_can_edit_project( $post_id ) && $task_style != 'no' ) || ( get_sub_field('tasks') && ( $task_style != 'no' ) ) ): ?>

<div class="portal-task-list-wrapper">

	<?php do_action( 'portal_before_task_title', $post_id, $phase_index ); ?>

		<?php
		/**
		 * Setup necissary variables for the tasks
		 * @var $tasks 	Array of all the task_status
		 * @var $overall_auto	If automatic progress is enabled
		 * @var $phase_auto		If phase progress is enabled
		 * @var $task_docs		A count of the number of documents attached to these tasks
		 */
		$tasks 			= portal_get_tasks( $post_id, $phase_index, $task_style );
		$overall_auto	= get_field( 'automatic_progress', $post_id );
		$phase_auto		= get_field( 'phases_automatic_progress', $post_id );
		$task_docs		= portal_count_documents( $post_id, array( 'phase_tasks' => $phase['phase_id'] ) );

		if( get_sub_field('tasks') ):

			$taskbar = '<span>';

			if( $task_style == 'complete' ) {
				$taskbar .= count( $tasks ) . ' ' . __( 'completed tasks' );
			} elseif ( $task_style == 'incomplete' ) {
				$taskbar .= count( $tasks ) . ' ' . __( 'open tasks' );
			} else {
				$remaing_tasks = count( $tasks ) - $phase_data['completed_tasks'];

				if( $task_docs && !empty($task_docs) && $task_docs['total'] > 0 ) {
					$taskbar .= '<b class="task-doc-count portal-mini-stat" data-toggle="portal-tooltip" data-placement="top" title="' . __( 'Task Documents Approved', 'portal_projects' ) . '"><i class="fa fa-files-o"></i> ' . $task_docs['approved'] . '/' . $task_docs['total'] . '</b> ';
				}
				$taskbar .= '<b class="task-comp-count portal-mini-stat" data-toggle="portal-tooltip" data-placement="top" title="' . __( 'Tasks Completed', 'portal_projects' ) .'"><b class="tasks-completed">' .  $phase_data['completed_tasks'] . '</b>' . __('/','portal_projects') . '<strong class="total-task-count">' . count( $tasks ) . '</strong> <i class="fa fa-tasks"></i></b>';
			}

			$taskbar .= '</span>';

		else:

			$taskbar = '<span data-toggle="portal-tooltip" data-placement="top" title="' . __( 'Tasks Completed', 'portal_projects' ) .'"><b>0</b> ' . __( '/', 'portal_projects' ) . '  <strong class="total-task-count">0</strong> <i class="fa fa-tasks"></i>';

		endif; ?>

		<h4><a href="#" class="task-list-toggle"><?php echo $taskbar; esc_html_e('Tasks','portal_projects'); ?></a></h4>

		<?php do_action( 'portal_before_task_list', $post_id, $phase_index ); ?>

		<ul class="portal-task-list">

			<?php
			do_action( 'portal_start_of_task_list', $post_id, $phase_index );

			foreach( $tasks as $task ) include( portal_template_hierarchy( 'projects/phases/tasks/single/entry.php' ) );

			do_action( 'portal_end_of_task_list', $post_id, $phase_index ); ?>

		</ul>

	<?php do_action( 'portal_after_task_list', $post_id, $phase_index ); ?>

</div>
<?php endif;

do_action( 'portal_after_task_wrapper', $post_id, $phase_index );
