<?php
/**
 * Teams Listing Page
 *
 * Lists all the teams on the site that you have access to
 * @var post_type	portal_teams
 */
include( portal_template_hierarchy( 'dashboard/header.php' ) );

$calendar_id 	= get_query_var( 'portal_calendar_page' );
$cuser 			= wp_get_current_user();
$calendar_id	= ( $calendar_id == 'home' ? $cuser->ID : $calendar_id ); ?>

<?php include( portal_template_hierarchy( 'global/header/navigation-sub' ) ); ?>

<div id="portal-archive-container" class="portal-grid-container-fluid">

	<div class="portal-grid-row">

        <div id="portal-archive-content" class="portal-col-md-12">

			<?php
            if( ( $cuser->ID == $calendar_id ) || ( current_user_can( 'edit_others_portal_projects' ) ) && ( get_user_by( 'id', $calendar_id )) ): ?>

                <div class="portal-archive-section">

                    <h2 class="portal-box-title"><?php esc_html_e( 'Calendar', 'portal_projects' ); ?></h2>

                    <?php echo portal_output_project_calendar( $calendar_id ); ?>

                </div>

            <?php else: ?>

                <div class="portal-col-md-6 portal-col-md-offset-3">

                    <div class="portal-error">
                        <p><em><?php esc_html_e( 'You do not have access to this calendar', 'portal_projects' ); ?></em></p>
                    </div>

            </div>

        <?php endif; ?>

    </div>
<?php
include( portal_template_hierarchy( 'dashboard/footer.php' ) );
