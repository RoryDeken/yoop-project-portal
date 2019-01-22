<?php
do_action( 'login_head' );
do_action( 'login_enqueue_scripts' );
?>

<div id="overview" class="portal-comments-wrapper">

	<?php if( ( portal_get_option( 'portal_logo' ) != '' ) && ( portal_get_option( 'portal_logo' ) != 'http://' ) ) { ?>
		<div class="portal-login-logo">
			<img src="<?php echo portal_get_option( 'portal_logo' ); ?>">
		</div>
	<?php } ?>

	<?php do_action( 'portal_login_form_before' ); ?>

	<div id="portal-login">
		<h2><?php apply_filters( 'portal_login_form_title', portal_the_login_title() ); ?></h2>

		<?php if( ( isset($_GET['login']) && ( $_GET['login'] == 'failed' ) ) ) { ?>
			<div class="portal-login-error">
				<p><?php esc_html_e( 'Incorrect username or password.', 'portal_projects'); ?><br> <?php esc_html_e( 'Please try again', 'portal_projects' ); ?></p>
			</div>
		<?php } ?>

		<?php do_action( 'portal_login_form_content' ); ?>

		<?php
		if( !is_user_logged_in() ):

			$password_required = post_password_required();

			echo yoop_login_form( $password_required );

		else:

			echo "<p>" . __( 'You don\'t have permission to access this project' , 'portal_projects' ) . "</p>";

		endif; ?>
	</div>

	<?php do_action( 'portal_login_form_after' ); ?>

	<p class="portal-text-center"><a href="<?php echo esc_url(wp_lostpassword_url(site_url().$_SERVER['REQUEST_URI'])); ?>"><?php esc_html_e( 'Lost your password?', 'portal_projects' ); ?></a></p>
	<p class="portal-text-center"><a href="<?php echo get_permalink(526);?>"><?php esc_html_e( 'Not a customer?', 'portal_projects' ); ?></a></p>

</div>
<?php do_action( 'login_footer' ); ?>
