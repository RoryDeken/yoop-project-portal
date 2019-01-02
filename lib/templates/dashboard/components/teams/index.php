<?php
/**
 * Teams Listing Page
 *
 * Lists all the teams on the site that you have access to
 * @var post_type	portal_teams
 */
include( portal_template_hierarchy( 'dashboard/header.php' ) );
include( portal_template_hierarchy( 'global/header/navigation-sub' ) ); ?>

<div id="portal-archive-container" class="portal-grid-container-fluid">

	<div class="portal-grid-row">

		<div id="portal-archive-content" class="portal-col-md-12">
	        <div class="portal-teams-section">

				<?php
				$i		= 0;
				$teams 	= ( current_user_can( 'delete_others_portal_projects' ) ? portal_get_the_teams() : portal_get_user_teams_query( $cuser->ID ) );

				if( $teams->have_posts() ): ?>
					<div class="portal-row portal-teams">
						<?php while( $teams->have_posts() ): $teams->the_post();

							if( $i %3 == 0 && $i > 1 ) echo '</div><div class="portal-row portal-teams">'; ?>

							<div class="portal-col-lg-4 portal-col-sm-6">
								<div class="portal-team-card">
									<aside class="thumbnail">
										<?php
										if( has_post_thumbnail() ):
											the_post_thumbnail( 'thumbnail' );
										else: ?>
											<img src="<?php echo YOOP_PORTAL_URI; ?>/assets/images/default-team.png" alt="<?php the_title(); ?>">
										<?php endif; ?>
									</aside>
									<article>

										<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

										<ul class="team-meta">
											<li><strong><?php echo count( get_field( 'team_members', $post->ID ) ); ?></strong> <span><?php esc_html_e( 'Members', 'portal_projects' ); ?></span></li>
											<li><strong><?php echo count( portal_get_team_projects( $post->ID, 'incomplete' ) ); ?></strong> <span><?php esc_html_e( 'Active', 'portal_projects' ); ?></span></li>
											<li><strong><?php echo count( portal_get_team_projects( $post->ID, 'completed' ) ); ?></strong> <span><?php esc_html_e( 'Completed', 'portal_projects' ); ?></span> </li>
										</ul>

										<div class="team-members">
											<?php portal_team_user_icons( $post->ID, 10 ); ?>
										</div>

									</article>
								</div>
							</div>

						<?php endwhile; ?>
					</div>

				<?php else: ?>

					<p class="portal-notice portal-notice-alert"><?php esc_html_e( 'No teams found.', 'portal_projects' ); ?></p>

				<?php endif; ?>

            </div>
        </div>
    </div>

<?php include( portal_template_hierarchy( 'dashboard/footer.php' ) );
