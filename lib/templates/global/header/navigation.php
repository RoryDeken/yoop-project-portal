<?php $back = portal_get_option('portal_back'); ?>

<nav class="nav portal-masthead-nav" id="portal-main-nav">
	<ul>
		<li id="nav-menu">
			<a href="#">
				<button id="my-icon" class="hamburger hamburger--collapse" type="button">
				   <span class="hamburger-box">
				      <span class="hamburger-inner"></span>
				   </span>
				</button>
			</a>
			<ul>

				<?php
				$nav_items = portal_get_nav_items();

				foreach( $nav_items as $item ):

					$atts = '';
					$class = isset( $item['class'] ? $item['class'] : '' );
					if( isset($item['atts']) ) {
						foreach( $item['atts'] as $attribute => $value ) {
							$atts .= $attribute . '="' . $value . '" ';
						}
					} ?>

					<li id="<?php echo esc_attr( $item['id'] ); ?>">
						<a href="<?php echo esc_url( $item['link'] ); ?>" class="<?php echo esc_attr($class); ?>" <?php echo $atts; ?>>
							<?php if( isset($item['icon']) ) echo '<i class="' . esc_attr( $item['icon'] ) . '"></i>'; ?>
							<?php echo esc_html( $item['title'] ); ?>
						</a>
					</li>

				<?php
				endforeach;

				do_action( 'portal_menu_items' );

				if( is_user_logged_in() ): ?>

					<li id="nav-logout"><a href="<?php echo esc_url( wp_logout_url( $_SERVER[ 'REQUEST_URI' ] ) ); ?>"><i class="portal-fi-logout portal-fi-icon"></i> <?php esc_html_e( 'Logout', 'portal_projects' ); ?></a></li>

				<?php endif; ?>

			</ul>

		</li>
	</ul>
</nav>
