<?php
/**
 * Progress email template.
 *
 * @since {{VERSION}}
 *
 * @var string $progress Progress % completion.
 * @var string|int $post_id Project post ID.
 */

defined( 'ABSPATH' ) || die();

$title_styles = array(
	'text-align'     => 'center',
	'text-transform' => 'uppercase',
	'font-size'      => '12px',
	'color'          => '#444',
	'font-weight'    => 'bold',
	'margin-top'     => '40px',
);

$container_styles = array(
	'background' => '#f1f1f1',
	'margin'     => '20px 0',
	'height'     => '30px',
);

$progress_bar_styles = array(
	'height'     => '30px',
	'width'      => "$progress%",
	'background' => '#3299bb',
);

if ( $progress < 10 ) {
	$progress_bar_styles['display'] = 'inline-block;';
}

if ( $progress >= 10 ) {

	$progress_text_styles = array(
		'color'         => '#fff',
		'line-height'   => '30px',
		'text-align'    => 'right',
		'padding-right' => '10px',
	);
} else {

	$progress_text_styles = array(
		'color'       => '#666',
		'display'     => 'inline-block',
		'margin-left' => '10px',
		'font-weight' => 'bold',
	);
}

if ( ! empty( $progress ) ) : ?>
	<p style="<?php echo portal_build_style( $title_styles ); ?>">
		<?php _e( 'Current Status', 'portal_projects' ); ?>
	</p>

	<div style="<?php echo portal_build_style( $container_styles ); ?>">
		<?php if ( $progress >= 10 ) : ?>
			<div style="<?php echo portal_build_style( $progress_bar_styles ); ?>">
				<div style="<?php echo portal_build_style( $progress_text_styles ); ?>">
					<?php echo $progress; ?>%
				</div>
			</div>
		<?php endif; ?>
	</div>
<?php endif; ?>
