<?php
/**
 * Email template.
 *
 * @since {{VERSION}}
 *
 * @var array $email_parts All template parts to go in the email.
 */

defined( 'ABSPATH' ) || die();

$email_parts = $email_parts ? $email_parts : array(
	'logo',
	'heading',
	'message',
);

$wrapper_styles = array(
	'background' => '#f1f2f7',
	'padding' => '30px',
);

$container_styles = array(
	'background'    => '#fff',
	'padding'       => '4%',
	'border-radius' => '12px',
	'font-family'   => "'Arial','Helvetica','San-Serif'",
	'width'         => '92%',
	'max-width'     => '640px',
	'margin'        => '0 auto',
);
?>

<html>
	<div style="<?php echo portal_build_style( $wrapper_styles ); ?>">
		<div style="<?php echo portal_build_style( $container_styles ); ?>">
			<?php
			foreach ( $email_parts as $part ) {
				include portal_template_hierarchy( "/email/$part.php" );
			}
			?>
		</div>
	</div>
</html>
