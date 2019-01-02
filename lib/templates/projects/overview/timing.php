<div id="portal-time-overview">
    <?php
    $post_id        = ( isset( $post_id ) ? $post_id : get_the_ID() );
    $start_date     = portal_get_the_start_date( NULL, $post_id );
    $end_date       = portal_get_the_end_date( NULL, $post_id );

    do_action( 'portal_timing_before_timebar', $post_id );

    portal_the_simplified_timebar($post_id);

    do_action( 'portal_timing_after_timebar', $post_id );

    if( $start_date || $end_date ): ?>
        <div class="portal-row">
            <?php if( $start_date ): ?>
                <div class="portal-col-sm-6 portal-archive-list-dates">
                    <h5><?php esc_html_e( 'Start Date', 'portal_projects' ); ?></h5>
                    <p><?php echo esc_html($start_date); ?></p>
                </div>
            <?php endif;
            if( $end_date ): ?>
                <div class="portal-col-sm-6 portal-archive-list-dates">
                    <h5><?php esc_html_e( 'End Date', 'portal_projects' ); ?></h5>
                    <p><?php echo esc_html($end_date); ?></p>
                </div>
            <?php endif; ?>
        </div>
    <?php endif;

    do_action( 'portal_timing_before_header', $post_id );

    do_action( 'portal_timing_after_header', $post_id );

    do_action( 'portal_timing_after_dates', $post_id ); ?>
</div>
