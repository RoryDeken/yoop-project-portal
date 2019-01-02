<div class="task-comments">

	<div class="task-comments-wrapper" data-task-key="<?php echo $task_comment_key; ?>">


		<?php if ( ! empty( $task_comment_key ) ) : ?>

			<?php $task_comments = portal_get_task_comments( $task_comment_key, $post_id ); ?>

			<ol class="commentlist">
				<?php wp_list_comments( array( 'callback' => 'project_status_comment', 'max_depth'	=>	2, 'post_id' => $post_id ), $task_comments ); ?>
			</ol>

			<?php portal_task_comment_form( $task_comment_key, array(), $post_id ); ?>

		<?php endif; ?>

	</div>

</div><!--/.task-comments-->
