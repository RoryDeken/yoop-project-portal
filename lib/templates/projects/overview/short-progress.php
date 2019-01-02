<?php
/**
 * Created by PhpStorm.
 * User: rossjohnson
 * Date: 1/3/15
 * Time: 1:18 PM
 */

if( !isset( $post_id ) ) { global $post; $post_id = $post->ID; }
$completed = portal_compute_progress( $post_id ); ?>

<div id="portal-short-progress">
	<h4><?php _e('Project Progress','portal_projects'); ?></h4>
	<p class="portal-progress">
		<span class="portal-<?php echo esc_attr($completed); ?>" data-toggle="portal-tooltip" data-placement="top" title="<?php echo esc_attr($completed . '% ' . __( 'Complete', 'portal_projects' ) ); ?>">
			<b><?php echo esc_html($completed); ?>%</b>
		</span>
		<i class="portal-progress-label"> <?php esc_html_e('Progress','portal_projects'); ?> </i>
	</p>
</div>
