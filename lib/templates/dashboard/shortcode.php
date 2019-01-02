<div id="portal-projects">
    <div id="portal-archive-container" class="portal-grid-container-fluid">

    	<div class="portal-grid-row">

    		<div id="portal-archive-content" class="portal-col-md-12">

    			<div class="portal-grid-row">

    				<div class="portal-col-lg-9 portal-col-md-8">

    					<?php
    					do_action( 'portal_dashboard_before_my_projects' );

    					include( portal_template_hierarchy( 'dashboard/components/projects/my-projects.php' ) );

    					do_action( 'portal_dashboard_after_my_projects' );

    					if( $tasks ) include( portal_template_hierarchy( 'dashboard/components/tasks/dashboard.php' ) );

    					do_action( 'portal_dashboard_after_my_tasks' );

    					?>

    				</div> <!--/.portal-col-md-8-->

    				<aside id="portal-archive-sidebar" class="portal-col-lg-3 portal-col-md-4">

    					<?php do_action( 'portal_before_dashboard_widgets' ); ?>

                        <?php do_action( 'portal_dashboard_widgets' ); ?>

    					<div class="portal-archive-widget cf">

    						<h4><?php esc_html_e( 'Overview', 'portal_projects' ); ?></h4>

    						<?php echo portal_get_project_breakdown(); ?>

    					</div>

    					<?php
    					$teams = portal_get_user_teams();
    					if( $teams ): ?>
    						<div class="portal-archive-widget">

    							<h4><?php esc_html_e( 'My Teams', 'portal_projects' ); ?></h4>

    							<ul class="portal-team-list">
    								<?php foreach( $teams as $team ): $members = get_field( 'team_members', $team->ID ); ?>
    									<li>
    										<?php portal_team_user_icons( $team->ID ); ?>
    										<a href="<?php echo esc_url( get_the_permalink( $team->ID ) ); ?>">
    											<span>
    												<strong class="portal-accent-color-1"><?php echo esc_html( get_the_title( $team->ID ) ); ?></strong>
    												<em><?php echo count( $members ) . ' ' . __( 'Members', 'portal_projects' ); ?></em>
    											</span>
    										</a>
    									</li>
    								<?php endforeach; ?>
    							</ul>

    						</div>
    					<?php endif; ?>

    					<div class="portal-archive-widget">
    						<p><a class="portal-ical-link pull-right portal-archive-ical-link" href="<?php echo portal_get_ical_link(); ?>" target="_new"><?php echo esc_html_e( 'iCal Feed', 'portal_projects' ); ?></a></p>
    						<h4><?php esc_html_e( 'Calendar', 'portal_projects' ); ?></h4>
    						<?php echo portal_output_project_calendar(); ?>
    					</div> <!--/.portal-archive-widget-->

                        <?php do_action( 'portal_after_dashboard_widgets' ); ?>

    				</aside>

    			</div>
    		</div>
    	</div>
    </div>
</div>
