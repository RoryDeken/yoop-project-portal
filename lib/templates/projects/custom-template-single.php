<?php $yoop_access = yoop_check_access($post->ID); ?>

<div id="portal-projects" class="portal-theme-template portal-reset portal-shortcode">

		<input type="hidden" id="portal-task-style" value="<?php the_field('expand_tasks_by_default',$post->ID); ?>">

        <?php // do_action('portal_the_header'); ?>

        <?php if ( $yoop_access ) : ?>

            <?php do_action('portal_before_overview'); ?>

            <section id="overview" class="wrapper portal-section">

                <?php do_action('portal_before_essentials'); ?>
                <?php do_action('portal_the_essentials'); ?>
                <?php do_action('portal_after_essentials'); ?>

            </section> <!--/#overview-->

            <?php do_action('portal_between_overview_progress'); ?>

            <section id="portal-progress" class="cf portal-section">

                <?php do_action('portal_before_progress'); ?>
                <?php do_action('portal_the_progress'); ?>
                <?php do_action('portal_after_progress'); ?>

            </section> <!--/#progress-->

            <?php do_action('portal_between_progress_phases'); ?>

            <section id="portal-phases" class="wrapper portal-section">

                <?php do_action('portal_before_phases'); ?>
                <?php do_action('portal_the_phases'); ?>
                <?php do_action('portal_after_phases'); ?>

            </section>

            <?php do_action('portal_between_phases_discussion'); ?>

			<!-- Discussion -->
            <?php if ( comments_open() ) : ?>
                <section id="portal-discussion" class="portal-section cf">

                    <?php
                    do_action( 'portal_before_discussion' );
                    do_action( 'portal_the_discussion' );
                    do_action( 'portal_after_discussion' );
                    ?>

                </section>
            <?php
			add_filter( 'comments_template', 'portal_disable_custom_template_comments' );
			endif; ?>


        <?php endif; ?>

        <?php if( ! $yoop_access ): ?>
            <div id="overview" class="wrapper">
                <div id="portal-login">
                    <?php if( ( ! $yoop_access ) && (get_field('restrict_access_to_specific_users'))): ?>
                        <h2><?php _e('This Project Requires a Login','portal_projects'); ?></h2>
                        <?php if(!is_user_logged_in()) {
                            echo yoop_login_form();
                        } else {
                            echo "<p>".__('You don\'t have permission to access this project','portal_projects')."</p>";
                        }
                        ?>
                    <?php endif; ?>
                    <?php if((post_password_required()) && (!current_user_can('manage_options'))): ?>
                        <h2><?php _e('This Project is Password Protected','portal_projects'); ?></h2>
                        <?php echo get_the_password_form(); ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

</div> <!--/#portal-project-->
