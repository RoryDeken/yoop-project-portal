<?php
$i			= 0;
$post_id 	= ( isset( $post_id ) ? $post_id : get_the_ID() );
$date 		= get_sub_field( 'date' ); ?>

<li class="<?php echo esc_attr( portal_milestone_marker_classes( $milestones, $completed ) ); ?>" data-milestone="<?php echo esc_attr($milestones[0]['occurs']); ?>">

	<b class="portal-milestone-dot"><?php echo esc_html_e( $milestones[0]['occurs'] . '%' ); ?></b>

	<?php do_action( 'portal_before_milestone_marker_text', $milestones, $post_id ); ?>

	<?php
	foreach( $milestones as $milestone ):

		$id 	= 'portal-milestone-' . $milestones[0]['occurs'] . '-' . $id;
		$class 	= 'portal-single-milestone ' . portal_late_class($milestone['date']); ?>

		<strong class="<?php echo esc_attr($class); ?>">

			<span class="portal-marker-title">
				<?php
				echo esc_html($milestone['title']);
				if( !empty($milestone['date']) && $milestone['date'] ) portal_the_milestone_due_date($milestone['date']); ?>
			</span>

			<?php if( !empty($milestone['description']) ): ?>
				<span class="portal-hide portal-milestone-description" id="<?php echo esc_attr($id); ?>">
					<?php echo wp_kses_post( do_shortcode($milestone['description']) ); ?>
				</span>
			<?php endif; ?>

		</strong>

	<?php $i++; endforeach; ?>

	<?php do_action( 'portal_after_milestone_marker_text', $milestones, $post_id ); ?>
</li>
