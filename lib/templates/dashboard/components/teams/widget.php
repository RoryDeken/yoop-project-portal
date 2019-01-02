<?php
$teams = portal_get_user_teams( $cuser->ID );
if( !empty( $teams ) ): ?>

    <?php do_action( 'portal_before_teams_dashboard_widget' ); ?>

        <div id="portal-dashboard-widget" class="portal-archive-section">

            <h2><?php echo esc_html( _n( 'Team', 'Teams', 'portal_projects' ) ); ?></h2>

            <ul class="portal-team-list">
                <?php foreach( $teams as $team ): ?>
                    <li>
                        <div class="portal-team-thumbnail">
                            <a href="<?php echo esc_url( get_the_permalink( $team ) ); ?>">
                                <?php portal_the_team_thumbnail( $team ); ?>
                            </a>
                        </div>
                        <div class="portal-team-description">
                            <a href="<?php echo esc_url( get_the_permalink( $team ) ); ?>">
                                <?php echo esc_html( get_the_title( $team ) ); ?>
                            </a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>

        </div>

        <?php do_action( 'portal_after_teams_dashboard_widget' ); ?>

<?php endif; ?>
