<?php $table_headers = apply_filters( 'portal_project_listing_headers', array(
    'portal_pl_col_1'  =>  __( 'Projects', 'portal_projects' )
) ); ?>

<table class="portal_project_list">
    <thead>
        <tr>
            <?php foreach( $table_headers as $class => $content ): ?>
                <th class="<?php echo esc_attr( $class ); ?>"><?php echo esc_html( $content ); ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
    <?php
    while($projects->have_posts()): $projects->the_post();

        global $post;
        $completed = portal_compute_progress($post->ID); ?>
        <tr>
            <td>
                <table>
                    <tr>
                        <td class="portal-list-header" colspan="3">

                            <?php do_action( 'portal_project_listing_summary_cell_before' ); ?>

                            <a <?php if( !empty( $target ) ) { echo 'target="_new"'; } ?> href="<?php the_permalink() ?>"><?php the_title(); ?></a>

                            <span class="portal-client"><?php the_field( 'client' ); ?></span> <span class="portal-updated"><?php _e('updated ','portal_projects'); ?> <?php the_modified_date(); ?></span>

                            <?php do_action( 'portal_project_listing_summary_cell_after' ); ?>

                        </td>
                    </tr>
                    <tr>
                        <td class="portal-list-time-progress">

                            <?php do_action( 'portal_project_listing_before_progress' ); ?>

                            <?php if( !empty( $completed ) ) {

                                echo '<p class="portal-progress"><span class="portal-' . $completed . '"><b>' . $completed . '%</b></span></p>';

                            } else {

                                _e( 'No progress recorded thus far', 'portal_projects' );

                            }?>

                            <?php do_action( 'portal_project_listing_after_progress' ); ?>

                            <?php portal_the_timebar( $post->ID ); ?>

                            <?php do_action( 'portal_project_listing_after_timing' ); ?>

                        </td>
                        <td class="portal-date-cell">

                            <?php do_action( 'portal_project_listing_before_start_date' ); ?>

                            <?php portal_the_start_date( $post->ID ); ?>

                            <?php do_action( 'portal_project_listing_after_start_date' ); ?>

                        </td>
                        <td class="portal-date-cell">

                            <?php do_action( 'portal_project_listing_before_end_date' ); ?>

                            <?php portal_the_end_date($post->ID); ?>

                            <?php do_action( 'portal_project_listing_after_start_date' ); ?>

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<p><?php echo get_next_posts_link( '&laquo; More Projects', $projects->max_num_pages ) . ' ' . get_previous_posts_link( 'Previous Projects &raquo;' ); ?></p>
