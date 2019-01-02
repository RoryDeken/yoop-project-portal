<?php $date = get_sub_field( 'date' ); ?>
<div class="portal-col-md-3 portal-enhanced-milestone portal-match-height-item <?php if(get_sub_field('occurs') <= $completed) { echo 'completed'; } ?> <?php echo esc_attr( portal_late_class( $date ) ); ?>" data-milestone="<?php the_sub_field( 'occurs' ); ?>">
	<div class="portal-enhanced-milestone-wrap">
		<?php do_action( 'portal_before_milestone_entry_heading' ); ?>

		<div class="portal-milestone-heading">
			<?php do_action( 'portal_before_milestone_entry_occurs' ); ?>
			<b class="portal-mm-marker"><?php the_sub_field( 'occurs' ); ?>%</b>
			<?php do_action( 'portal_after_milestone_entry_occurs' ); ?>
		</div>

		<?php do_action( 'portal_before_milestone_entry_title' ); ?>

		<h4>
			<?php echo apply_filters( 'portal_milestone_entry_title', get_sub_field( 'title' ) ); ?>
			<?php if( !empty( $date ) ): ?>
				<span><?php portal_the_milestone_due_date( $date ); ?></span>
			<?php endif; ?>
		</h4>

		<?php
		do_action( 'portal_after_milestone_entry_title' );
		echo wpautop( do_shortcode( apply_filters( 'portal_milestone_entry_description', get_sub_field( 'description' ) ) ) );
		do_action( 'portal_after_milestone_entry_description' ); ?>
	</div>
</div>
