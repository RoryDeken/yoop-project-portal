<?php
$link_container_styles = array(
	'padding-top' => '30px',
	'margin-top'  => '30px',
	'border-top'  => '1px solid #efefef',
	'text-align'  => 'center',
);

$link_styles = array(
	'font-weight'     => 'bold',
	'color'           => '#0074a2',
	'text-align'      => 'center',
	'padding'         => '10px 25px',
	'border'          => '1px solid #0074a2',
	'border-radius'   => '3px',
	'display'         => 'inline-block',
	'text-decoration' => 'none',
);
?>

<p style="<?php echo portal_build_style( $link_container_styles ); ?>">
	<a href="<?php echo get_permalink( $post_id ); ?>" style="<?php echo portal_build_style( $link_styles ); ?>">
		<?php _e( 'Click here to view.', 'portal_projects' ); ?>
	</a>
</p>
