<?php
/**
 * Project overview index
 *
 * Part template for the project overview, includes all the other sub templates
 *
 * @post_type  portal_project
 * @hierarchy  single
 */

$post_id 	= ( isset( $post_id ) ? $post_id : get_the_ID() );
$priorities = portal_get_priorities_list();
$priority 	= ( get_field('_portal_priority') ? get_field('_portal_priority') : 'normal' );
$priority 	= $priorities[$priority];

/**
 * @end vars
 */ ?>

<?php if( get_field( 'client_project_logo' ) ): $project_logo = get_field( 'client_project_logo' ); ?>
	<header id="portal-project-header">
		<?php echo '<img src="'.$project_logo['sizes']['medium'].'" alt="'.get_field( 'client' ).'" class="portal-client-project-logo">'; ?>
	</header>
<?php endif; ?>

<div id="portal-essentials" class="<?php echo esc_attr($style); ?> cf">

	<hgroup class="portal-section-heading">
		<?php do_action( 'portal_before_project_title', $post_id ); ?>
		<h2 class="portal-section-title"><?php the_title(); ?> <span class="portal-client"><?php the_field('client'); ?></span></h2>
		<?php do_action( 'portal_after_project_title', $post_id ); ?>
		<p class="portal-section-data">
			<?php echo esc_html(portal_compute_progress( get_the_id() )); ?>% <?php esc_html_e( 'Complete', 'portal_projects' ); ?>
			<?php if( current_user_can('see_priority_portal_projects') ): ?>
				<span class="portal-priority portal-priority-<?php echo esc_attr($priority['slug']); ?>" data-placement="left" data-toggle="portal-tooltip" title="<?php echo esc_attr($priority['label']) . ' ' . esc_html('Priority', 'portal_projects'); ?>" style="background-color: <?php echo $priority['color']; ?>"></span>
			<?php endif; ?>
		</p>
		<?php do_action( 'portal_after_project_data', $post_id ); ?>
	</hgroup>

	<?php do_action( 'portal_before_description', $post_id ); ?>

	<div class="portal-row">

		<div id="portal-description-documents" class="portal-col-md-8">
			<div class="portal-overview-box cf">
				<div class="portal-row">
					<?php include( portal_template_hierarchy( '/projects/overview/description.php' ) ); ?>
					<?php include( portal_template_hierarchy( '/projects/overview/documents/index' ) ); ?>
				</div>
			</div> <!--/.portal-overview-box-->
			<?php do_action( 'portal_before_quick_overview', $post_id ); ?>
		</div>

		<div id="portal-quick-overview" class="portal-col-md-4">
			<?php
			do_action( 'portal_before_short_progress', $post_id );
				include( portal_template_hierarchy( '/projects/overview/short-progress' ) );
			do_action( 'portal_between_short_progress_and_overview_timing', $post_id );
				include( portal_template_hierarchy( '/projects/overview/timing' ) );
			do_action( 'portal_after_overview_timing', $post_id );
			 	include( portal_template_hierarchy( '/projects/overview/summary' ) );
			do_action( 'portal_after_project_summary', $post_id );
			?>
		</div>

	</div>

	<?php do_action( 'portal_after_quick_overview', $post_id ); ?>

</div> <!--/#portal-essentials-->
