<?php
do_action( 'portal_before_menu' );
$post_types     = array( 'portal_projects', 'portal_teams' );
$nav_items      = portal_get_nav_items();
if( ( in_array( get_post_type(), $post_types ) && is_archive() ) || get_post_type() === 'portal_pages' ) $sub_nav_items  = portal_get_section_nav_items(); ?>

<nav id="portal-offcanvas-menu">
    <ul>
        <?php
        do_action( 'portal_before_nav_items' );

        if( !empty( $nav_items ) ):
            foreach( $nav_items as $item ):

                $atts = '';
                $class = ( isset($item['class']) ? $item['class'] : '' );

                if( isset($item['atts']) ) {
                    foreach( $item['atts'] as $attribute => $value ) $atts .= $attribute . '="' . $value . '" ';
                }

                ?>
                <li id="<?php echo esc_attr( $item['id'] ); ?>"><a href="<?php echo esc_url( $item['link'] ); ?>" class="<?php echo esc_attr($class); ?>" <?php echo $atts; ?>><?php if( isset($item['icon']) ) { echo '<i class="' . esc_attr( $item['icon'] ) . '"></i>'; } ?> <?php echo esc_html( $item['title'] ); ?></a></li>

            <?php
            endforeach;
        endif;

        do_action( 'portal_menu_items' ); do_action( 'portal_before_sub_nav_items' );

        if( !empty( $sub_nav_items ) ):
            foreach( $sub_nav_items as $link ):

                $class = apply_filters( 'portal_section_nav_link_class', 'inactive', $link[ 'slug' ] ); ?>

                <li><a class="<?php echo esc_attr( $class . ' ' . $link[ 'icon' ] ); ?>" href="<?php echo esc_url( $link[ 'url' ] ); ?>"><?php echo esc_html( $link[ 'name' ] ); ?></a></li>

            <?php
            endforeach;
        endif;

        if( is_user_logged_in() ): ?>
            <li id="nav-logout"><a href="<?php echo wp_lostpassword_url(); ?>"><i class="portal-fi-notify portal-fi-icon"></i> <?php esc_html_e( 'Reset Password', 'portal_projects' ); ?></a></li>
            <li id="nav-logout"><a href="<?php echo esc_url( wp_logout_url( $_SERVER[ 'REQUEST_URI' ] ) ); ?>"><i class="portal-fi-logout portal-fi-icon"></i> <?php esc_html_e( 'Logout', 'portal_projects' ); ?></a></li>

        <?php endif; ?>

    </ul>
</nav>

<?php
do_action( 'portal_after_menu' );
