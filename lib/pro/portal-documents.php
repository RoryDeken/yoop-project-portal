<?php
/**
 * Description of portal-documents
 *
 * Controllers and helper functions to manage documents
 * @package portal-projects
 *
 *
 */

function portal_the_document_form_fields( $post_id, $user_id ) { ?>

    <input type="hidden" name="portal-project-id" value="<?php esc_attr_e($post_id); ?>">
    <input type="hidden" name="portal-document-id" value="">
    <input type="hidden" name="portal-document-name" value="">
    <input type="hidden" name="portal-current-user" value="<?php esc_attr_e($user_id); ?>">

<?php
}

function portal_get_icon_class( $file ) {

    $site_url = site_url();
    $site_url = substr($site_url,7);

    $domain = parse_url($file, PHP_URL_HOST);

    if($site_url != $domain) {
        return 'fa-link';
    }

    $ext = substr($file,-3);

    if(($ext == 'doc') || ($ext == 'odc') || ($ext == 'txt')) {
        return 'fa-file-text-o';
    }

    if(($ext == 'pdf')) {
        return 'fa-file-pdf-o';
    }

    if(($ext == 'jpg') || ($ext == 'bmp') || ($ext == 'png') || ($ext == 'tif') || ($ext == 'peg')) {
        return 'fa-file-image-o';
    }

    if(($ext == '.ai') || ($ext == 'psd') || ($ext == 'eps')) {
        return 'fa-paint-brush';
    }

    return 'fa-file-o';

}

function portal_get_doc_type( $class ) {

	switch( $class ) {
		case 'fa-link':
			return __( 'Link', 'portal_projects' );
			break;
		case 'fa-file-text-o':
			return __( 'Text Document', 'portal_projects' );
			break;
		case 'fa-file-pdf-o':
			return __( 'PDF Document', 'portal_projects' );
			break;
		case 'fa-file-image-o':
			return __( 'Image', 'portal_projects' );
			break;
		case 'fa-paint-brush';
			return __( 'Design File', 'portal_projects' );
			break;
		default:
			return __( 'Download', 'portal_projects' );
	}

}

add_action( 'portal_footer', 'portal_add_document_status_modal' );
function portal_add_document_status_modal() {
    include( portal_template_hierarchy( 'global/document-status-modal' ) );
}
