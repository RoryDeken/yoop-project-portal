<?php
$post_id = ( isset($post_id) ? $post_id : get_the_ID() ); ?>

<div id="portal-documents" class="<?php echo esc_attr($style); ?> portal-col-md-5  portal-overview-col">

	<?php do_action( 'portal_before_documents', $post_id ); ?>

	<div class="portal-documents-wrap">

		<?php do_action( 'portal_before_document_section_title', $post_id ); ?>
		<h4><?php esc_html_e( 'Documents', 'portal_projects' ); ?></h4>
		<?php do_action( 'portal_after_document_section_title', $post_id ); ?>

		<?php
		include( portal_template_hierarchy( 'projects/overview/documents/documents-loop' ) );

		if( $style != 'shortcode' ) do_action( 'portal_after_documents', $post_id ); ?>

	</div> <!--/.portal-documents-wrap-->

	<?php // do_action( 'portal_after_documents', $post_id ); ?>

</div> <!--/#project-documents-->
