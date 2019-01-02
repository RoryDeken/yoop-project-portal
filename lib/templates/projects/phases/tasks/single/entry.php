<?php
$post_id		= ( isset($post_id) && !empty($post_id ) ? $post_id : get_the_ID() );
$task_class 	=	'task-item task-item-' . $task['ID'];
$task_class 	.= 	( $task['status'] == 100 ? ' complete' : '' );
$assigned 		= 	$task['assigned'];
$user			=	FALSE;
$task['status']	=	( empty( $task['status'] ) ? 0 : $task['status'] );
$task['status'] = 	apply_filters( 'portal_task_status', $task['status'], $post_id, $phase_index, $task['ID'] );
$task_documents = 	portal_parse_task_documents( get_field( 'documents', $post_id ), $task['task_id'] );

if( ( !empty($assigned) ) && ( $assigned != 'unassigned' ) && ( $assigned != 'null' ) ) {
	$user	=	get_userdata( $assigned );
}

do_action( 'portal_before_task_entry', $post_id, $phase_index, $task['ID'], $phases, $phase ); ?>

<li class="<?php echo esc_attr( apply_filters( 'portal_task_classes', $task_class, $post_id, $phase_index, $task['ID'], $phases, $phase ) ); ?>" data-progress="<?php echo $task['status']; ?>">

	<?php do_action( 'portal_before_task_name', $post_id, $phase_index, $task['ID'], $phases, $phase ); ?>

	<?php

	$task_name = apply_filters( 'portal_task_name', $task['task'], $post_id, $phase_index, $task['ID'] );
	$target_id = 'phase-' . $phase_index . '-task-' . $task['ID'];

	$task_panel_atts = apply_filters( 'portal_task_panel_dashboard_attributes', array(
		'task_index'		=>	$task['ID'],
		'task_id'			=>  $task['task_id'],
		'phase_index'		=>	$phase_index,
		'phase_id'			=>  $phase['phase_id'],
		'project'			=>	$post_id,
		'project_name'		=>  get_the_title( $post_id ),
		'project_permalink' =>	get_permalink( $post_id ),
	), $post_id, $phase_index, $task['ID'] ); ?>

	<a id="<?php echo esc_attr($target_id); ?>" class="portal-task-title" href="#portal-open-task-panel"
		<?php foreach( $task_panel_atts as $att => $val ): ?>
			data-<?php echo $att; ?>="<?php echo esc_attr( $val ); ?>"
		<?php endforeach; ?>
			>
		<strong><?php echo esc_html($task_name); ?></strong>
		<span class="portal-view-link"><i class="fa fa-angle-right"></i></span>
	</a>

	<?php do_action( 'portal_after_task_name', $post_id, $phase_index, $task['ID'], $phases, $phase ); ?>

	<b class="after-task-name">

		<?php $after_task_name_items_count = 0; ?>

		<?php if($user) : ?>

			<b class="portal-assigned-to">
				<i class="fa fa-fw fa-user"></i>
				<span class="text"><?php echo apply_filters( 'portal_task_assigned', portal_username_by_id( $user->ID ), $post_id, $phase_index, $task[ 'ID' ] ); ?></span>
			</b>

			<?php $after_task_name_items_count++; ?>

		<?php endif; ?>

		<?php
		if( isset($task['due_date']) && !empty($task['due_date']) ) :

			$date 	= strtotime( $task[ 'due_date' ] );
			$format = get_option( 'date_format' );

			$date_class = ( $date < strtotime( 'today' ) ? 'late' : '' ); ?>

			<b class="portal-task-due-date <?php echo $date_class; ?>">
				<i class="portal-fi-icon portal-fi-calendar"></i>
				<span class="text"><?php echo date_i18n( $format, $date ); ?></span>
			</b>

			<?php $after_task_name_items_count++;

		endif;

		if ( $task_documents && $document_count = count( $task_documents ) ): ?>

			<b class="portal-task-documents js-open-task-panel" data-target="<?php echo esc_attr($target_id); ?>">
				<i class="fa fa-files-o"></i>
				<span class="text"><?php echo esc_html($document_count); ?></span>
			</b>

			<?php $after_task_name_items_count++;

		endif;

		// We always "show" comment count since this could be updated from 0 to 1
		$task_comment_count = portal_get_task_comment_count( $task['task_id'], $post_id ); ?>

		<b class="portal-task-discussions js-open-task-panel<?php echo ( ! $task_comment_count ) ? ' hidden' : ''; ?>" data-target="<?php echo esc_attr($target_id); ?>">
			<i class="portal-fi-icon portal-fi-discussion"></i>
			<span class="text"><?php echo esc_html($task_comment_count); ?></span>
		</b>

		<?php $after_task_name_items_count++;

		// $after_task_name_items_count is passed by reference in the chance that none of the above were true, so that if one extension were to add an item, then it could let other extensions know that it did and they may want to add a separator
		do_action_ref_array( 'portal_after_task_assigned', array( $post_id, $phase_index, $task['ID'], $phases, $phase, &$after_task_name_items_count ) ); ?>

	</b> <!--/.after-task-name-->

	<?php if(portal_can_edit_task( $post_id, $phase_index, $task['ID'] )) { ?>

		<span class="portal-task-edit-links">

			<?php do_action( 'portal_task_edit_links_start', $post_id, $phase_index, $task['ID'] ); ?>

			<a href="#edit-task-<?php echo $task['ID']; ?>" class="task-edit-link"><b class="fa fa-adjust"></b> <?php _e('update','portal_projects'); ?></a>

			<?php $task_atts = apply_filters( 'portal_task_attributes', array(
				'target'			=>	$task['ID'],
				'task'				=>	$task['ID'],
				'phase'				=>	$phase_index,
				'project'			=>	$post_id,
				'phase-auto'		=>	( isset($phase_auto[0]) && $phase_auto[0] !== NULL ? $phase_auto[0] : 'No' ),
				'overall-auto'		=>	( isset($overall_auto[0]) && $overall_auto[0] !== NULL ? $overall_auto[0] : 'No' ),
			), $post_id, $phase_index, $task['ID'] ); ?>

			<a href="#" class="complete-task-link"
				<?php foreach( $task_atts as $att => $val ): ?>
					data-<?php echo $att; ?>="<?php echo esc_attr( $val ); ?>"
				<?php endforeach; ?>
				>
				<b class="fa fa-check"></b>
				<?php esc_html_e( 'complete', 'portal_projects' ); ?>
			</a>

			<?php do_action( 'portal_task_edit_links_end', $post_id, $phase_index, $task['ID'], $phases, $phase ); ?>

		</span>

		<div id="edit-task-<?php echo $task['ID']; ?>" class="task-select">

			<select id="edit-task-select-<?php echo $phase_index . '-' . $task['ID']; ?>" class="edit-task-select">
				<option value="<?php echo esc_attr( $task['status'] ); ?>"><?php echo $task['status']; ?>%</option>
				<?php $values = apply_filters( 'portal_task_values', array(
					'0'		=>	'0%',
					'5'		=>	'5%',
					'10'	=>	'10%',
					'15'	=>	'15%',
					'20'	=>	'20%',
					'25'	=>	'25%',
					'30'	=>	'30%',
					'35'	=>	'35%',
					'40'	=>	'40%',
					'45'	=>	'45%',
					'50'	=>	'50%',
					'55'	=>	'55%',
					'60'	=>	'60%',
					'65'	=>	'65%',
					'70'	=>	'70%',
					'75'	=>	'75%',
					'80'	=>	'80%',
					'85'	=>	'85%',
					'90'	=>	'90%',
					'95'	=>	'95%',
					'100'	=>	'100%'
				) );
				foreach( $values as $value => $label ): ?>
					<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>

			<?php
			$data_atts = apply_filters( 'portal_single_task_data_atts', array(
				'target'	=>	$task['ID'],
				'task'		=>	$task['ID'],
				'phase'		=>	$phase_index,
				'project'	=>	$post_id,
				'phase-auto'	=>	$phase_auto[0],
				'overall-auto'	=>	$overall_auto[0]
			)); ?>

			<input type="submit" name="save" value="save" class="task-save-button" <?php foreach( $data_atts as $att => $value ) echo 'data-' . $att . '="' . esc_attr($value) . '" '; ?>>

        </div>
	<?php } ?>

	<?php do_action( 'portal_before_task_progress', $post_id, $phase_index, $task['ID'], $phases, $phase ); ?>

	<span class="portal-progress-bar"><em class="status portal-<?php echo $task[ 'status' ]; ?>"></em></span>

	<?php do_action( 'portal_after_task_progress', $post_id, $phase_index, $task['ID'], $phases, $phase ); ?>

</li>

<?php do_action( 'portal_after_task_entry', $post_id, $phase_index, $task['ID'], $phases, $phase ); ?>
