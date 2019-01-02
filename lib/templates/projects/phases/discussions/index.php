<div class="phase-comments">

	<h4>
		<a href="#" class="comments-list-toggle">
			<?php _e( 'Discussion', 'portal_projects' ); ?>
			<span><i class="fa fa-comment"></i> <?php echo esc_html($comment_count); ?></span>
		</a>
	</h4>

	<div class="phase-comments-wrapper" data-phase-key="<?php echo $phase_comment_key; ?>">


		<?php if ( ! empty( $phase_comment_key ) ) : ?>

			<?php $phase_comments = portal_get_phase_comments( $phase_comment_key, $post_id ); ?>

			<ol class="commentlist">
				<?php wp_list_comments( array( 'callback' => 'project_status_comment', 'max_depth'	=>	2 ), $phase_comments ); ?>
			</ol>

			<?php portal_phase_comment_form( $phase_comment_key ); ?>

		<?php endif; ?>

	</div>

</div><!--/.phase-comments-->
