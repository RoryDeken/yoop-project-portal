<?php do_action( 'portal_before_archive_project_listing' ); ?>

<div class="portal-archive-project-list cf">
    <?php
    while( $projects->have_posts() ): $projects->the_post(); global $post;

        $start_date = portal_text_date(get_field( 'start_date', $post->ID ));
        $end_date   = portal_text_date(get_field( 'end_date', $post->ID ));

        $priorities = portal_get_priorities_list();
        $priority = ( get_field('_portal_priority') ? get_field('_portal_priority') : 'normal' );
        $priority = $priorities[$priority];

        do_action( 'portal_archive_project_listing_before_row' ); ?>

        <div id="portal-archive-project-<?php echo esc_attr($post->ID); ?>" class="portal-archive-project" data-project="<?php the_title(); ?>" data-client="<?php the_field('client'); ?>" data-url="<?php the_permalink(); ?>">

            <?php do_action( 'portal_archive_project_listing_before_open', $post->ID ); ?>

            <div class="portal-row cf">
                <div class="portal-archive-project-title portal-col-md-12">

                    <?php if( current_user_can('see_priority_portal_projects') ): ?>
                        <span class="portal-priority portal-priority-<?php echo esc_attr($priority['slug']); ?>" data-placement="left" data-toggle="portal-tooltip" title="<?php echo esc_attr($priority['label']) . ' ' . esc_html( 'Priority', 'portal_projects' ); ?>" style="background-color: <?php echo $priority['color']; ?>"></span>
                    <?php endif; ?>

                    <?php do_action( 'portal_archive_project_listing_before_summary', $post->ID ); ?>

                    <hgroup>
                        <h3>
                            <a href="<?php the_permalink(); ?>">
                                <?php the_title(); ?>
                                <?php if( get_field('client') ): ?>
                                    <span class="portal-ali-client"><?php the_field('client'); ?></span>
                                <?php endif; ?>
                            </a>
                        </h3>
                        <p class="portal-archive-updated"><?php esc_html_e( 'Updated on ', 'portal_projects' ); echo esc_html(get_the_modified_date()); ?></p>
                    </hgroup>

                    <?php do_action( 'portal_archive_project_listing_after_summary', $post->ID ); ?>

                </div>
            </div>
            <div class="portal-row cf">
                <div class="portal-col-md-6 portal-col-sm-6">
                    <?php
                    do_action( 'portal_archive_project_listing_before_progress' );

                    $completed = portal_compute_progress($post->ID);
                    if( !$completed ) $completed = 0; ?>

                    <p class="portal-progress">
                        <span class="portal-<?php echo esc_attr($completed); ?>" data-toggle="portal-tooltip" data-placement="top" title="<?php echo esc_attr($completed . '% ' . __( 'Complete', 'portal_projects' ) ); ?>">
                            <b><?php echo esc_html($completed); ?>%</b>
                        </span>
                        <i class="portal-progress-label"> <?php esc_html_e('Progress','portal_projects'); ?> </i>
                    </p>

                    <?php
                    do_action( 'portal_archive_project_listing_before_timing' );

                    portal_the_simplified_timebar($post->ID);

                    do_action( 'portal_archive_project_listing_after_timing' );  ?>
                </div>
                <div class="portal-col-md-2 portal-col-sm-3 portal-col-xs-6 portal-archive-list-dates">
                    <?php if( $start_date ): ?>
                        <h5><?php esc_html_e( 'Start Date', 'portal_projects' ); ?></h5>
                        <p><?php echo esc_html($start_date); ?></p>
                    <?php endif; ?>
                </div>
                <div class="portal-col-md-2 portal-col-sm-3 portal-col-xs-6 portal-archive-list-dates">
                    <?php if( $end_date ): ?>
                        <h5><?php esc_html_e( 'End Date', 'portal_projects' ); ?></h5>
                        <p><?php echo esc_html($end_date); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php do_action( 'portal_archive_project_listing_before_close', $post->ID ); ?>
        </div>
    <?php endwhile; ?>
</div>

<?php do_action( 'portal_after_archive_project_listing' ); ?>

<?php if( $projects->max_num_pages > 1 ): ?>

    <p class="portal-project-pager"><?php echo get_next_posts_link( '<span class="portal-ajax-more-projects pano-btn">&laquo; More Projects</span>', $projects->max_num_pages ) . ' ' . get_previous_posts_link( '<span class="portal-ajax-prev-projects pano-btn">Previous Projects &raquo;</span>' ); ?></p>

<?php endif; ?>
