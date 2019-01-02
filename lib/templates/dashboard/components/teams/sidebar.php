<aside id="team-single-sidebar" class="portal-col-md-4">

    <aside class="thumbnail">

        <?php
        if( has_post_thumbnail() ):
            the_post_thumbnail( 'thumbnail' );
        else: ?>
            <img src="<?php echo YOOP_PORTAL_URI; ?>/assets/images/default-team.png" alt="<?php the_title(); ?>">
        <?php endif; ?>

    </aside>

    <?php if( get_field( 'description' ) ): ?>

        <div class="portal-archive-widget portal-widget">

            <h2 class="portal-widget-title"><?php the_title(); ?></h2>

            <?php the_field( 'description' ); ?>

        </div>

    <?php endif; ?>

    <?php $members = portal_get_team_members( $post->ID ); ?>

    <div class="portal-archive-widget portal-widget">

        <h2 class="portal-widget-title"><?php esc_html_e( 'Members', 'portal_projects' ); ?></h2>

        <?php if( $members ): ?>
            <ul class="portal-team-member-list">
                <?php foreach( $members as $user ): ?>
                    <li>
                        <?php echo $user[ 'user_avatar' ]; ?>
                        <strong><?php echo portal_get_nice_username_by_id( $user[ 'ID' ] ); ?></strong>
                        <span class="portal-last-login"><?php echo portal_verbose_login( $user[ 'ID' ] ); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p><?php esc_html_e( 'No users assigned to this team.', 'portal_projects' ); ?></p>
        <?php endif; ?>

    </div>

</aside>
