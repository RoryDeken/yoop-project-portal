<?php
/**
 * Teams Listing Page
 *
 * Lists all the teams on the site that you have access to
 * @var post_type	portal_teams
 */
include( portal_template_hierarchy( 'dashboard/header.php' ) );
include( portal_template_hierarchy( 'global/header/navigation-sub' ) );
?>

<div id="portal-archive-container" class="portal-grid-container-fluid">

	<?php
    if( have_posts() ): while( have_posts() ): the_post(); global $post;

        if( portal_current_user_can_access_team( $post->ID ) ): ?>

            	<div id="portal-archive-content" class="portal-grid-row">

            		<div class="portal-grid-row">

                        <div class="portal-col-md-8">

                            <div class="portal-archive-section">

                                <h2 class="portal-box-title"><?php esc_html_e( 'Active Projects Assigned to', 'portal_projects' ); ?> <?php the_title(); ?></h2>

                                <?php
                                $projects = portal_get_team_projects( $post->ID );

                                echo portal_archive_project_listing( $projects );

                                wp_reset_postdata(); ?>

                            </div>

                        </div>

                        <?php include( portal_template_hierarchy( 'dashboard/components/teams/sidebar.php' ) ); ?>

                    </div> <!--/.portal-grid-row-->

                </div> <!--/#portal-archive-content-->

        <?php
        else:

            wp_die( __( 'You don\'t have access to this team.', 'portal_projects' ) );

        endif;

        endwhile; endif;

include( portal_template_hierarchy( 'dashboard/footer.php' ) );
