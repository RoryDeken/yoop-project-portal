<div class="portal-archive-project-list cf">
    <?php while($projects->have_posts()): $projects->the_post();

        global $post;
		
        $start_date = portal_text_date(get_field( 'start_date', $post->ID ));
        $end_date   = portal_text_date(get_field( 'end_date', $post->ID ));
        $priorities = portal_get_priorities_list();
        $priority = ( get_field('_portal_priority') ? get_field('_portal_priority') : 'normal' );
        $priority = $priorities[$priority]; ?>
        <div id="portal-archive-project-<?php echo esc_attr($post->ID); ?>" class="portal-archive-project" data-project="<?php the_title(); ?>" data-client="<?php the_field('client'); ?>" data-url="<?php the_permalink(); ?>">
           <div class="portal-row cf">
               <div class="portal-archive-project-title portal-col-md-12">

                   <hgroup>
                       <h3>
                           <?php the_title(); ?>
                           <?php if( get_field('client') ): ?>
                               <span class="portal-ali-client"><?php the_field('client'); ?></span>
                           <?php endif; ?>
                       </h3>
                       <p class="portal-archive-updated"><em><?php esc_html_e( 'Updated on ', 'portal_projects' ); echo esc_html(get_the_modified_date()); ?></em></p>
                   </hgroup>
				   
				    <?php if( $start_date || $end_date ): ?>
						<p>
							<?php if( $start_date ): ?>
								<strong><?php esc_html_e( 'Start', 'portal_projects' ); ?>:</strong>
								<?php echo esc_html($start_date); ?>
							<?php
							endif;
							if( $start_date && $end_date ) echo '<span class="portal-pipe">|</span>';
							if( $end_date ): ?>
								<strong><?php esc_html_e( 'End', 'portal_projects' ); ?>:</strong>
								<?php echo esc_html($end_date); ?>
							<?php endif; ?>
						</p>
					<?php endif; ?>
               </div>
           </div>
           <div class="portal-row cf">
               <div class="portal-col-md-12">
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
           </div>
           <?php do_action( 'portal_archive_project_listing_before_close', $post->ID ); ?>
		 </div>
    <?php endwhile; ?>
</div>

<p><?php echo get_next_posts_link( '&laquo; More Projects', $projects->max_num_pages ) . ' ' . get_previous_posts_link( 'Previous Projects &raquo;' ); ?></p>
