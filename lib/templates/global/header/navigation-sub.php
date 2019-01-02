<div class="portal-archive-navigation">
    <hgroup class="portal-sub-masthead cf">
        <nav class="portal-section-nav portal-grid-container-fluid">

            <?php do_action( 'portal_before_sub_navigation' ); ?>

            <?php
            if( is_single() && get_post_type() == 'portal_projects' && portal_can_edit_project() ):
                $link = apply_filters( 'portal_project_edit_post_link', portal_get_edit_post_link() ); ?>
                <a href="<?php echo esc_url($link); ?>" class="portal-pull-right portal-btn"><i class="fa fa-pencil"></i> <?php esc_html_e( 'Edit Project', 'portal_projects' ); ?></a>
            <?php endif; ?>

            <ul>
                <?php
                $nav_items = portal_get_section_nav_items();

                foreach( $nav_items as $link ):

                    $class = apply_filters( 'portal_section_nav_link_class', 'inactive', $link[ 'slug' ] ); ?>

                    <li id="portal-sub-nav-<?php echo esc_attr($link['slug']); ?>"><a class="<?php echo esc_attr( $class ); ?>" href="<?php echo esc_url( $link[ 'url' ] ); ?>"><i class="<?php echo esc_attr( $link[ 'icon' ] ); ?>"></i> <?php echo esc_html( $link[ 'name' ] ); ?></a></li>

                <?php endforeach; ?>
            </ul>

            <?php do_action( 'portal_after_sub_navigation' ); ?>

        </nav>
    </hgroup>
</div> <!--/.portal-archive-navigation-->
