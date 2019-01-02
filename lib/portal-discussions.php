<?php
add_filter( 'comments_template', 'portal_custom_discussion_template' );

function portal_custom_discussion_template( $comment_template ) {

	global $post;

	if( get_post_type() == 'portal_projects' ) {
		return dirname( __FILE__ ) . '/templates/projects/discussion/discussion-loop.php';
	}

	return $comment_template;

}
