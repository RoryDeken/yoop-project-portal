<?php
/**
 * portal-documents.php
 *
 * Controls documents and document management.
 *
 * @category controller
 * @package portal-projects
 * @author Ross Johnson
 * @since 1.3.6
 */

function portal_documents_upload_directory( $param ) {

    $mydir = '/yoop';

    $param['path'] 	= $param['basedir'] . $mydir;
    $param['url'] 	= $param['baseurl'] . $mydir;

    return $param;

}

add_action( 'wp', 'portal_download_redirect' );
function portal_download_redirect() {

	if( ( isset( $_GET[ 'portal_download' ] ) ) && ( get_post_type() == 'portal_projects' ) ) {

		global $post;

		if( !yoop_check_access( $post->ID ) ) {

			wp_die( __( 'You don\'t have access to this file', 'portal_projects' ) );

		}

		$i				= $_GET[ 'portal_download' ];
		$files 			= get_field( 'documents', $post->ID );
		$attachment_id	= $files[ $i ][ 'file' ][ 'id' ];
		$attachment		= get_attached_file( $attachment_id );

		if( ! file_exists( $attachment ) ) {

			wp_die( __( 'File no longer exists', 'portal_projects' ) );

		}

		// required for IE
		if(ini_get('zlib.output_compression')) { ini_set('zlib.output_compression', 'Off');	}

		// get the file mime type using the file extension
		switch(strtolower(substr(strrchr($attachment, '.'), 1))) {
			case 'pdf': $mime = 'application/pdf'; break;
			case 'zip': $mime = 'application/zip'; break;
			case 'jpeg':
			case 'jpg': $mime = 'image/jpg'; break;
			default: $mime = 'application/force-download';
		}

		header('Pragma: public'); 	// required
		header('Expires: 0');		// no cache
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime ($attachment)).' GMT');
		header('Cache-Control: private',false);
		header('Content-Type: '.$mime);
		header('Content-Disposition: attachment; filename="'.basename($attachment).'"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: '.filesize($attachment));	// provide file size
		header('Connection: close');

        ob_clean();   // discard any data in the output buffer (if possible)
        flush();      // flush headers (if possible)

		readfile( $attachment );		// push it out

		exit();

	}

}

function portal_translate_doc_status( $status ) {

    $translation = '';

 	if( $status == 'Approved' ) {
	 	$translation = __( 'Approved' , 'portal_projects' );
 	}

 	if( $status == 'In Review' ) {
	 	$translation = __( 'In Review', 'portal_projects' );
 	}

 	if( $status == 'Revisions' ) {
	 	$translation = __( 'Revisions','portal_projects' );
 	}

 	if( $status == 'Rejected' ) {
		$translation = __( 'Rejected', 'portal_projects' );
	}

    return apply_filters( 'portal_translate_doc_status', $translation, $status );

}

function portal_parse_phase_documents( $documents = null , $phase_comment_key = null, $tasks = null ) {

    if( empty($documents) || empty($phase_comment_key) ) return false;

    $i = 0;
    $phase_docs = array(
        'all'   => array(),
        'phase' => array(),
        'tasks' => array()
    );

    foreach( $documents as $doc ) {

        if( $doc['document_phase'] == $phase_comment_key ) {

            $doc['index'] = $i;

            $phase_docs['phase'][]  = $doc;
            $phase_docs['all'][]    = $doc;

        }

        $i++;

    }

    // Count tasks if they exist
    if( $tasks && !empty($tasks) ) {

        $task_keys = array();

        foreach( $tasks as $task ) {

            if( !isset($task['task_id']) ) {
                continue;
            }

            $task_keys[] = $task['task_id'];
        }

        foreach( $documents as $doc ) {

            if( !isset($doc['document_task']) ) {
                continue;
            }

            if( in_array($doc['document_task'], $task_keys ) ) {
                $phase_docs['tasks'][]  = $doc;
                $phase_docs['all'][]    = $doc;
            }

        }

    }

    return $phase_docs;

}

/**
 * Grab all Documents that are assigned to a Task
 *
 * @param		array  $documents Documents within a Project
 * @param		string $task_id   Task ID
 *
 * @since		{{VERSION}}
 * @return		array  Documents within the Task
 */
function portal_parse_task_documents( $documents = null , $task_id = null ) {

    if( empty($documents) || empty($task_id) ) return false;

    $i = 0;
    $task_docs = array();

    foreach( $documents as $doc ) {

        if( $doc['document_task'] == $task_id ) {
            $doc['index'] = $i;
            $task_docs[] = $doc;
        }

        $i++;

    }

    return $task_docs;

}

function portal_count_approved_documents( $documents = null ) {

    if( empty($documents) ) {
        return 0;
    }

    $approved   = 0;

    foreach( $documents as $document ) {
        if( $document['status'] == 'Approved' || $document['status'] == 'none' ) {
            $approved++;
        }
    }

    return $approved;

}

add_filter( 'upload_dir', 'portal_modify_portal_upload_directory' );
function portal_modify_portal_upload_directory( $param ) {

    $id = ( isset( $_REQUEST['post_id'] ) ? $_REQUEST['post_id'] : '' );

    if( !$id ) return $param;

    if( get_post_type( $id ) != 'portal_projects' ) return $param;

    $portal_dir        = '/portal';
    $param['path']  = $param['basedir'] . $portal_dir;
    $param['url']   = $param['baseurl'] . $portal_dir;

    return $param;

}

add_action( 'init', 'portal_add_index_file_in_portal_uploads' );
function portal_add_index_file_in_portal_uploads() {

    $has_run = get_option( 'portal_uploads_add_index_file' );

    if( $has_run ) {
        return;
    }

    $uploads_dir = wp_upload_dir();

    touch( $uploads_dir['basedir'] . '/portal/index.php' );

    update_option( 'portal_uploads_add_index_file', 1 );

}
