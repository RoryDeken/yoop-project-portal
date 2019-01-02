            <footer class="portal-grid-container-fluid" id="portal-colophon">
                <div class="portal-grid-row">
                    <div class="portal-col-md-4">
                        <p><?php bloginfo( 'name' ); ?> - <?php echo date( 'Y' ); ?></p>
                    </div>
                    <div class="portal-col-md-8">

                        <?php
                        $portal_slug = portal_get_option( 'portal_slug' );

                        $nav = ( has_nav_menu( 'portal_footer_menu' ) ? portal_get_custom_project_menu_items( 'portal_footer_menu' ) : apply_filters( 'portal_footer_nav', array(
                            array(
                                'link'  =>  home_url(),
                                'title' =>  __( 'home', 'portal_projects' )
                            ),
                            array(
                                'link'  =>  get_post_type_archive_link('portal_projects'),
                                'title' =>  __( 'dashboard', 'portal_projects' )
                            ),
                        ) ) ); ?>

                        <nav class="footer-nav">
                            <ul>
                                <?php foreach( $nav as $link ): ?>
                                    <li><a href="<?php echo esc_url( $link['link'] ); ?>"><?php echo esc_html( $link['title'] ); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </nav>

                    </div>
                </div>
            </footer>

        <?php
        if( is_user_logged_in() ) include( portal_template_hierarchy( 'global/navigation-off.php' ) );

        do_action( 'portal_footer' );
        do_action( 'portal_footer_' . __FILE__ ); ?>

        </div> <!--/.portal-container-->
    </div>

</body>
</html>
