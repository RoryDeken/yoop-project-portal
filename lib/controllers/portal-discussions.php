<?php
add_action( 'comment_post', 'portal_comment_notify', 11, 3 );

/**
 * Fires when a new comment is created and potentially pushes to Slack.
 *
 * @since {{VERSION}}
 * @access private
 *
 * @param $comment_ID string The comment ID.
 * @param $comment_approved int Whether or not the comment is approved.
 * @param $commentdata array Data about the comment.
 */
function portal_comment_notify( $comment_ID, $comment_approved, $commentdata ) {

	$comment_post = get_post( $commentdata['comment_post_ID'] );

	if ( get_post_type( $comment_post ) == 'portal_projects' ) {

		do_action( 'portal_notify', 'new_comment', array(
			'commentdata'      => $commentdata,
			'comment_ID'       => $comment_ID,
			'comment_approved' => $comment_approved,
			'comment_post'     => $comment_post,
			'post_id'		   => $commentdata[ 'comment_post_ID' ],
			'phase_title'	   => portal_get_phase_title_by_key( get_comment_meta( $comment_ID, 'phase-key', true ), $commentdata[ 'comment_post_ID' ] ),
			'task_title'	   => portal_get_task_title_by_key( get_comment_meta( $comment_ID, 'task-key', true ), $commentdata['comment_post_ID'] )
		) );

	}
}

/**
 * portal-discussions.php
 *
 * Controls custom comments and discussion modifications.
 *
 * @category controller
 * @package portal-projects
 * @author Ross Johnson & Kellen Mace
 * @since 1.3.6
 */

function portal_comment_template( $comment_template ) {

	global $post;

	if ( $post->post_type == 'portal_projects' ) {
		return PROJECT_YOOP_DIR . '/lib/templates/projects/discussion/discussion-loop.php';
	} else {
		return $comment_template;
	}
}
add_filter( 'comments_template', 'portal_comment_template', 9 );


function project_status_comment( $comment, $args, $depth ) {

	$GLOBALS['comment'] = $comment;

	if ( ! empty( $args ) ) {

		extract( $args, EXTR_SKIP );

		if ( 'div' == $args['style'] ) {

			$tag       = 'div';
			$add_below = 'comment';

		} else {

			$tag       = 'li';
			$add_below = 'div-comment';
		}

	}

	$private_comment = get_comment_meta( get_comment_ID(), '_portal_private_comment', true );

	// If the user can't read private comments and this is one, continue
	if( $private_comment && !current_user_can('read_portal_private_comments') ) {
		return;
	}

	$comment_body_class = ( $private_comment ? 'comment-body portal-private-comment' : 'comment-body' );

	$post_id = get_the_ID();

	// Fix comment by post author coloring for Task Discussions
	if ( ! $post_id &&
		isset( $args['post_id'] ) ) {
		$post_id = $args['post_id'];
	}

	?>

	<<?php echo $tag ?> <?php comment_class( ( empty( $args['has_children'] ) ? '' : 'parent' ), get_comment_ID(), $post_id ); ?> id="comment-<?php comment_ID() ?>">
	<?php if ( 'div' != $args['style'] ) : ?>
		<div id="div-comment-<?php comment_ID() ?>" class="<?php echo esc_attr( $comment_body_class ); ?>">
	<?php endif; ?>

	<?php if ( $comment->comment_approved == '0' ) : ?>
		<p class="portal-comment-notice"><em class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'portal_projects' ) ?></em></p>
	<?php endif; ?>

	<?php if( $private_comment ): ?>
		<p class="portal-comment-notice"><?php esc_html_e( 'Private Message', 'portal_projects' ); ?></p>
	<?php endif; ?>

	<div class="comment-text">

		<header class="comment-header">
			<div class="comment-date">
				<?php
				/* translators: 1: date, 2: time */
				printf( __( '%1$s at %2$s' ), get_comment_date(), get_comment_time() ) ?><?php edit_comment_link( __( '(Edit)' ), '  ', '' );
				?>
			</div>
			<?php printf( __( '<cite class="fn">%s</cite>' ), get_comment_author_link() ); ?>
		</header>

		<?php comment_text() ?>

		<div class="reply">
			<a class="comment-reply-link" href="#" rel="nofollow"
			   data-comment-parent="<?php comment_ID(); ?>"><?php esc_html_e( 'Reply', 'portal_projects' ); ?></a>
		</div>

	</div>
	<span class="portal-bubble"></span>

	<div class="cf">
		<div class="comment-author vcard">
			<?php if ( $args['avatar_size'] != 0 ) {
				echo get_avatar( $comment, $args['avatar_size'] );
			} ?>
		</div>
	</div>

	<?php if ( 'div' != $args['style'] ) : ?>

		</div>

	<?php endif; ?>

<?php }

/*
 * Save unique comment key with each phase
 */
function portal_save_phase_comment_keys() {

	if ( 'portal_projects' != $_POST['post_type'] ) {
		return;
	}

	$phases = $_POST['fields']['field_527d5dc12fa29'];

	if ( is_array( $phases ) ) {
		foreach ( $phases as $key => $fields ) {

			if ( 'acfcloneindex' === $key ) {
				continue;
			}

			if ( empty( $fields['portal_phase_id'] ) ) {
				$_POST['fields']['field_527d5dc12fa29'][ $key ]['portal_phase_id'] = portal_generate_phase_id();
			}
		}
	}
}
add_action( 'acf/save_post', 'portal_save_phase_comment_keys', 9 );

/*
 * Save comment phase key meta when comment submitted
 */
function portal_save_comment_phase_key_meta( $comment_id ) {

	if ( ! empty( $_POST['phase-key'] ) ) {
		$phase_key = wp_filter_nohtml_kses( $_POST['phase-key'] );
		add_comment_meta( $comment_id, 'phase-key', $phase_key );
	}
}
add_action( 'comment_post', 'portal_save_comment_phase_key_meta' );

function portal_save_comment_task_key_meta( $comment_id ) {

	if ( ! empty( $_POST['task-key'] ) ) {
		$task_key = wp_filter_nohtml_kses( $_POST['task-key'] );
		add_comment_meta( $comment_id, 'task-key', $task_key );
	}
}
add_action( 'comment_post', 'portal_save_comment_task_key_meta' );


/*
 * Get the comments for a phase
 */
function portal_get_phase_comments( $phase_comment_key, $post_id = null ) {

	$post_id = ( $post_id == null ? get_the_ID() : $post_id );

	return get_comments( array(
		'meta_key'   => 'phase-key',
		'meta_value' => sanitize_text_field( $phase_comment_key ),
		'order'      => 'ASC',
		'post_id'	 => $post_id
	) );
}

/**
 * Get Comments for a Task
 * The Meta Key format of "-key" was kept to make it the same as Phase Comments
 *
 * @param		string  $task_comment_key Task ID
 * @param		integer $post_id          Project Post ID
 *
 * @since		{{VERSION}}
 * @return		array   Array of Comment Objects
 */
function portal_get_task_comments( $task_comment_key, $post_id = null ) {

	$post_id = ( $post_id == null ? get_the_ID() : $post_id );

	return get_comments( array(
		'meta_key'   => 'task-key',
		'meta_value' => sanitize_text_field( $task_comment_key ),
		'order'      => 'ASC',
		'post_id'	 => $post_id
	) );
}

function portal_get_general_comments( $post_id = null ) {

	$post_id = ( $post_id == null ? get_the_ID() : $post_id );

	return get_comments( array(
		'post_id'    => $post_id,
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key'     => 'phase-key',
				'compare' => 'NOT EXISTS'
			),
			array(
				'key'     => 'task-key',
				'compare' => 'NOT EXISTS'
			)
		)
	) );

}

function portal_get_general_comment_count( $post_id = null ) {

	$post_id = ( $post_id == null ? get_the_ID() : $post_id );

	return count( portal_get_general_comments( $post_id ) );

}

function portal_get_phase_comment_count( $phase_comment_key, $post_id = null ) {

	$post_id = ( $post_id == null ? get_the_ID() : $post_id );

	return count( portal_get_phase_comments( $phase_comment_key, $post_id ) );

}

/**
 * Get Count of Comments on a Task
 *
 * @param		string  $task_comment_key Task ID
 * @param		integer $post_id          Project Post ID
 *
 * @since		{{VERSION}}
 * @return		integer Number of Task Comments
 */
function portal_get_task_comment_count( $task_comment_key, $post_id = null ) {

	$post_id = ( $post_id == null ? get_the_ID() : $post_id );

	return count( portal_get_task_comments( $task_comment_key, $post_id ) );

}

/**
 * Output a complete commenting form for use within each phase
 *
 * Based on the WordPress comment_form() function
 *
 * @param string $phase Which phase of the project.
 * @param array $args Optional. Default arguments and form fields to override.
 * @param int|WP_Post $post_id Post ID or WP_Post object to generate the form for. Default current post.
 */
function portal_phase_comment_form( $phase = '', $args = array(), $post_id = null ) {

	$post_id = ( $post_id == null ? get_the_ID() : $post_id );

	$commenter     = wp_get_current_commenter();
	$user          = wp_get_current_user();
	$user_identity = $user->exists() ? $user->display_name : '';

	$args = wp_parse_args( $args );
	if ( ! isset( $args['format'] ) ) {
		$args['format'] = current_theme_supports( 'html5', 'comment-form' ) ? 'html5' : 'xhtml';
	}

	$req      = get_option( 'require_name_email' );
	$aria_req = ( $req ? " aria-required='true'" : '' );
	$html_req = ( $req ? " required='required'" : '' );
	$html5    = 'html5' === $args['format'];
	$fields   = array(
		'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
		            '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . $html_req . ' /></p>',
		'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
		            '<input id="email" name="email" ' . ( $html5 ? 'type="email"' : 'type="text"' ) . ' value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" aria-describedby="email-notes"' . $aria_req . $html_req . ' /></p>',
		'url'    => '<p class="comment-form-url"><label for="url">' . __( 'Website' ) . '</label> ' .
		            '<input id="url" name="url" ' . ( $html5 ? 'type="url"' : 'type="text"' ) . ' value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>',
	);

	$required_text = sprintf( ' ' . __( 'Required fields are marked %s' ), '<span class="required">*</span>' );

	/**
	 * Filter the default comment form fields.
	 *
	 * @since 3.0.0
	 *
	 * @param array $fields The default comment fields.
	 */
	$fields   = apply_filters( 'comment_form_default_fields', $fields );
	$defaults = array(
		'fields'               => $fields,
		'comment_field'        => '<p class="comment-form-comment"><label for="comment">' . _x( 'Comment', 'noun' ) . '</label> <textarea id="comment" name="comment" cols="45" rows="8"  aria-required="true" required="required"></textarea></p>',
		/** This filter is documented in wp-includes/link-template.php */
		'must_log_in'          => '<p class="must-log-in">' . sprintf( __( 'You must be <a href="%s">logged in</a> to post a comment.' ), wp_login_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ) . '</p>',
		/** This filter is documented in wp-includes/link-template.php */
		'logged_in_as'         => '<p class="logged-in-as">' . sprintf( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>' ), get_edit_user_link(), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ) . '</p>',
		'comment_notes_before' => '<p class="comment-notes"><span id="email-notes">' . __( 'Your email address will not be published.' ) . '</span>' . ( $req ? $required_text : '' ) . '</p>',
		'comment_notes_after'  => '',
		'id_form'              => '',
		'class_form'           => 'portal-comment-form',
		'id_submit'            => 'submit',
		'class_submit'         => 'submit',
		'name_submit'          => 'submit',
		'title_reply'          => __( 'Leave a Reply' ),
		'title_reply_to'       => __( 'Leave a Reply to %s' ),
		'cancel_reply_link'    => __( 'Cancel reply' ),
		'label_submit'         => __( 'Post Comment' ),
		'submit_button'        => '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" />',
		'submit_field'         => '<p class="form-submit">%1$s %2$s %3$s</p>',
		'format'               => 'xhtml',
	);

	/**
	 * Filter the comment form default arguments.
	 *
	 * Use 'comment_form_default_fields' to filter the comment fields.
	 *
	 * @since 3.0.0
	 *
	 * @param array $defaults The default comment form arguments.
	 */
	$args = wp_parse_args( $args, apply_filters( 'comment_form_defaults', $defaults ) );

	// Ensure that the filtered args contain all required default values.
	$args = array_merge( $defaults, $args );

	if ( comments_open( $post_id ) ) : ?>
		<?php
		/**
		 * Fires before the comment form.
		 *
		 * @since 3.0.0
		 */
		do_action( 'comment_form_before' );
		?>
		<div id="respond" class="comment-respond">
			<h3 id="reply-title"
			    class="comment-reply-title"><?php comment_form_title( $args['title_reply'], $args['title_reply_to'] ); ?>
				<small><?php cancel_comment_reply_link( $args['cancel_reply_link'] ); ?></small>
			</h3>
			<?php if ( get_option( 'comment_registration' ) && ! is_user_logged_in() ) : ?>
				<?php echo $args['must_log_in']; ?>
				<?php
				/**
				 * Fires after the HTML-formatted 'must log in after' message in the comment form.
				 *
				 * @since 3.0.0
				 */
				do_action( 'comment_form_must_log_in_after' );
				?>
			<?php else : ?>
				<form action="<?php echo site_url( '/wp-comments-post.php' ); ?>" method="post"
				      id="<?php echo esc_attr( $args['id_form'] ); ?>"
				      class="comment-form portal-comment-form"<?php echo $html5 ? ' novalidate' : ''; ?>>
					<?php
					/**
					 * Fires at the top of the comment form, inside the form tag.
					 *
					 * @since 3.0.0
					 */
					do_action( 'comment_form_top' );
					?>
					<?php if ( is_user_logged_in() ) : ?>
						<?php
						/**
						 * Filter the 'logged in' message for the comment form for display.
						 *
						 * @since 3.0.0
						 *
						 * @param string $args_logged_in The logged-in-as HTML-formatted message.
						 * @param array $commenter An array containing the comment author's
						 *                               username, email, and URL.
						 * @param string $user_identity If the commenter is a registered user,
						 *                               the display name, blank otherwise.
						 */
						echo apply_filters( 'comment_form_logged_in', $args['logged_in_as'], $commenter, $user_identity );
						?>
						<?php
						/**
						 * Fires after the is_user_logged_in() check in the comment form.
						 *
						 * @since 3.0.0
						 *
						 * @param array $commenter An array containing the comment author's
						 *                              username, email, and URL.
						 * @param string $user_identity If the commenter is a registered user,
						 *                              the display name, blank otherwise.
						 */
						do_action( 'comment_form_logged_in_after', $commenter, $user_identity );
						?>
					<?php else : ?>
						<?php echo $args['comment_notes_before']; ?>
						<?php
						/**
						 * Fires before the comment fields in the comment form.
						 *
						 * @since 3.0.0
						 */
						do_action( 'comment_form_before_fields' );
						foreach ( (array) $args['fields'] as $name => $field ) {
							/**
							 * Filter a comment form field for display.
							 *
							 * The dynamic portion of the filter hook, `$name`, refers to the name
							 * of the comment form field. Such as 'author', 'email', or 'url'.
							 *
							 * @since 3.0.0
							 *
							 * @param string $field The HTML-formatted output of the comment form field.
							 */
							echo apply_filters( "comment_form_field_{$name}", $field ) . "\n";
						}
						/**
						 * Fires after the comment fields in the comment form.
						 *
						 * @since 3.0.0
						 */
						do_action( 'comment_form_after_fields' );
						?>
					<?php endif; ?>
					<?php
					/**
					 * Filter the content of the comment textarea field for display.
					 *
					 * @since 3.0.0
					 *
					 * @param string $args_comment_field The content of the comment textarea field.
					 */
					echo apply_filters( 'comment_form_field_comment', $args['comment_field'] );
					?>
					<?php echo $args['comment_notes_after']; ?>

					<?php
					$submit_button = sprintf(
						$args['submit_button'],
						esc_attr( $args['name_submit'] ),
						esc_attr( $args['id_submit'] ),
						esc_attr( $args['class_submit'] ),
						esc_attr( $args['label_submit'] )
					);

					/**
					 * Filter the submit button for the comment form to display.
					 *
					 * @since 4.2.0
					 *
					 * @param string $submit_button HTML markup for the submit button.
					 * @param array $args Arguments passed to `comment_form()`.
					 */
					$submit_button = apply_filters( 'comment_form_submit_button', $submit_button, $args );

					$submit_field = sprintf(
						$args['submit_field'],
						$submit_button,
						get_comment_id_fields( $post_id ),
						'<input id="phase-key" type="hidden" name="phase-key" value="' . esc_attr( $phase ) . '">'
					);

					/**
					 * Filter the submit field for the comment form to display.
					 *
					 * The submit field includes the submit button, hidden fields for the
					 * comment form, and any wrapper markup.
					 *
					 * @since 4.2.0
					 *
					 * @param string $submit_field HTML markup for the submit field.
					 * @param array $args Arguments passed to comment_form().
					 */
					echo apply_filters( 'comment_form_submit_field', $submit_field, $args );

					/**
					 * Fires at the bottom of the comment form, inside the closing </form> tag.
					 *
					 * @since 1.5.0
					 *
					 * @param int $post_id The post ID.
					 */
					do_action( 'comment_form', $post_id );
					?>
				</form>
			<?php endif; ?>
		</div><!-- #respond -->
		<?php
		/**
		 * Fires after the comment form.
		 *
		 * @since 3.0.0
		 */
		do_action( 'comment_form_after' );
	else :
		/**
		 * Fires after the comment form if comments are closed.
		 *
		 * @since 3.0.0
		 */
		do_action( 'comment_form_comments_closed' );
	endif;
}

/**
 * Output a complete commenting form for use within each task
 *
 * Based on the WordPress comment_form() function
 *
 * At first I thought of putting a little conditional in the portal_phase_comment_form() function to support Tasks because all that gets changed is the hidden field's name attribute to make this work, but then I decided against it to make it easily possible to change the two of them independently of each other.
 *
 * @param		string      $task    Which Task of the project.
 * @param		array       $args    Optional. Default arguments and form fields to override.
 * @param 		int|WP_Post $post_id Post ID or WP_Post object to generate the form for. Default current post.
 *
 * @since		{{VERSION}}
 * @return		void
 */
function portal_task_comment_form( $task = '', $args = array(), $post_id = null ) {

	$post_id = ( $post_id == null ? get_the_ID() : $post_id );

	$commenter     = wp_get_current_commenter();
	$user          = wp_get_current_user();
	$user_identity = $user->exists() ? $user->display_name : '';

	$args = wp_parse_args( $args );
	if ( ! isset( $args['format'] ) ) {
		$args['format'] = current_theme_supports( 'html5', 'comment-form' ) ? 'html5' : 'xhtml';
	}

	$req      = get_option( 'require_name_email' );
	$aria_req = ( $req ? " aria-required='true'" : '' );
	$html_req = ( $req ? " required='required'" : '' );
	$html5    = 'html5' === $args['format'];
	$fields   = array(
		'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
		            '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . $html_req . ' /></p>',
		'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
		            '<input id="email" name="email" ' . ( $html5 ? 'type="email"' : 'type="text"' ) . ' value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" aria-describedby="email-notes"' . $aria_req . $html_req . ' /></p>',
		'url'    => '<p class="comment-form-url"><label for="url">' . __( 'Website' ) . '</label> ' .
		            '<input id="url" name="url" ' . ( $html5 ? 'type="url"' : 'type="text"' ) . ' value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>',
	);

	$required_text = sprintf( ' ' . __( 'Required fields are marked %s' ), '<span class="required">*</span>' );

	/**
	 * Filter the default comment form fields.
	 *
	 * @since 3.0.0
	 *
	 * @param array $fields The default comment fields.
	 */
	$fields   = apply_filters( 'comment_form_default_fields', $fields );
	$defaults = array(
		'fields'               => $fields,
		'comment_field'        => '<p class="comment-form-comment"><label for="comment">' . _x( 'Comment', 'noun' ) . '</label> <textarea id="comment" name="comment" cols="45" rows="8"  aria-required="true" required="required"></textarea></p>',
		/** This filter is documented in wp-includes/link-template.php */
		'must_log_in'          => '<p class="must-log-in">' . sprintf( __( 'You must be <a href="%s">logged in</a> to post a comment.' ), wp_login_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ) . '</p>',
		/** This filter is documented in wp-includes/link-template.php */
		'logged_in_as'         => '<p class="logged-in-as">' . sprintf( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>' ), get_edit_user_link(), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ) . '</p>',
		'comment_notes_before' => '<p class="comment-notes"><span id="email-notes">' . __( 'Your email address will not be published.' ) . '</span>' . ( $req ? $required_text : '' ) . '</p>',
		'comment_notes_after'  => '',
		'id_form'              => '',
		'class_form'           => 'portal-comment-form',
		'id_submit'            => 'submit',
		'class_submit'         => 'submit',
		'name_submit'          => 'submit',
		'title_reply'          => __( 'Leave a Reply' ),
		'title_reply_to'       => __( 'Leave a Reply to %s' ),
		'cancel_reply_link'    => __( 'Cancel reply' ),
		'label_submit'         => __( 'Post Comment' ),
		'submit_button'        => '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" />',
		'submit_field'         => '<p class="form-submit">%1$s %2$s %3$s</p>',
		'format'               => 'xhtml',
	);

	/**
	 * Filter the comment form default arguments.
	 *
	 * Use 'comment_form_default_fields' to filter the comment fields.
	 *
	 * @since 3.0.0
	 *
	 * @param array $defaults The default comment form arguments.
	 */
	$args = wp_parse_args( $args, apply_filters( 'comment_form_defaults', $defaults ) );

	// Ensure that the filtered args contain all required default values.
	$args = array_merge( $defaults, $args );

	if ( comments_open( $post_id ) ) : ?>
		<?php
		/**
		 * Fires before the comment form.
		 *
		 * @since 3.0.0
		 */
		do_action( 'comment_form_before' );
		?>
		<div id="respond" class="comment-respond">
			<h3 id="reply-title"
			    class="comment-reply-title"><?php comment_form_title( $args['title_reply'], $args['title_reply_to'] ); ?>
				<small><?php cancel_comment_reply_link( $args['cancel_reply_link'] ); ?></small>
			</h3>
			<?php if ( get_option( 'comment_registration' ) && ! is_user_logged_in() ) : ?>
				<?php echo $args['must_log_in']; ?>
				<?php
				/**
				 * Fires after the HTML-formatted 'must log in after' message in the comment form.
				 *
				 * @since 3.0.0
				 */
				do_action( 'comment_form_must_log_in_after' );
				?>
			<?php else : ?>
				<form action="<?php echo site_url( '/wp-comments-post.php' ); ?>" method="post"
				      id="<?php echo esc_attr( $args['id_form'] ); ?>"
				      class="comment-form portal-comment-form"<?php echo $html5 ? ' novalidate' : ''; ?>>
					<?php
					/**
					 * Fires at the top of the comment form, inside the form tag.
					 *
					 * @since 3.0.0
					 */
					do_action( 'comment_form_top' );
					?>
					<?php if ( is_user_logged_in() ) : ?>
						<?php
						/**
						 * Filter the 'logged in' message for the comment form for display.
						 *
						 * @since 3.0.0
						 *
						 * @param string $args_logged_in The logged-in-as HTML-formatted message.
						 * @param array $commenter An array containing the comment author's
						 *                               username, email, and URL.
						 * @param string $user_identity If the commenter is a registered user,
						 *                               the display name, blank otherwise.
						 */
						echo apply_filters( 'comment_form_logged_in', $args['logged_in_as'], $commenter, $user_identity );
						?>
						<?php
						/**
						 * Fires after the is_user_logged_in() check in the comment form.
						 *
						 * @since 3.0.0
						 *
						 * @param array $commenter An array containing the comment author's
						 *                              username, email, and URL.
						 * @param string $user_identity If the commenter is a registered user,
						 *                              the display name, blank otherwise.
						 */
						do_action( 'comment_form_logged_in_after', $commenter, $user_identity );
						?>
					<?php else : ?>
						<?php echo $args['comment_notes_before']; ?>
						<?php
						/**
						 * Fires before the comment fields in the comment form.
						 *
						 * @since 3.0.0
						 */
						do_action( 'comment_form_before_fields' );
						foreach ( (array) $args['fields'] as $name => $field ) {
							/**
							 * Filter a comment form field for display.
							 *
							 * The dynamic portion of the filter hook, `$name`, refers to the name
							 * of the comment form field. Such as 'author', 'email', or 'url'.
							 *
							 * @since 3.0.0
							 *
							 * @param string $field The HTML-formatted output of the comment form field.
							 */
							echo apply_filters( "comment_form_field_{$name}", $field ) . "\n";
						}
						/**
						 * Fires after the comment fields in the comment form.
						 *
						 * @since 3.0.0
						 */
						do_action( 'comment_form_after_fields' );
						?>
					<?php endif; ?>
					<?php
					/**
					 * Filter the content of the comment textarea field for display.
					 *
					 * @since 3.0.0
					 *
					 * @param string $args_comment_field The content of the comment textarea field.
					 */
					echo apply_filters( 'comment_form_field_comment', $args['comment_field'] );
					?>
					<?php echo $args['comment_notes_after']; ?>

					<?php
					$submit_button = sprintf(
						$args['submit_button'],
						esc_attr( $args['name_submit'] ),
						esc_attr( $args['id_submit'] ),
						esc_attr( $args['class_submit'] ),
						esc_attr( $args['label_submit'] )
					);

					/**
					 * Filter the submit button for the comment form to display.
					 *
					 * @since 4.2.0
					 *
					 * @param string $submit_button HTML markup for the submit button.
					 * @param array $args Arguments passed to `comment_form()`.
					 */
					$submit_button = apply_filters( 'comment_form_submit_button', $submit_button, $args );

					$submit_field = sprintf(
						$args['submit_field'],
						$submit_button,
						get_comment_id_fields( $post_id ),
						'<input id="task-key" type="hidden" name="task-key" value="' . esc_attr( $task ) . '">'
					);

					/**
					 * Filter the submit field for the comment form to display.
					 *
					 * The submit field includes the submit button, hidden fields for the
					 * comment form, and any wrapper markup.
					 *
					 * @since 4.2.0
					 *
					 * @param string $submit_field HTML markup for the submit field.
					 * @param array $args Arguments passed to comment_form().
					 */
					echo apply_filters( 'comment_form_submit_field', $submit_field, $args );

					/**
					 * Fires at the bottom of the comment form, inside the closing </form> tag.
					 *
					 * @since 1.5.0
					 *
					 * @param int $post_id The post ID.
					 */
					do_action( 'comment_form', $post_id );
					?>
				</form>
			<?php endif; ?>
		</div><!-- #respond -->
		<?php
		/**
		 * Fires after the comment form.
		 *
		 * @since 3.0.0
		 */
		do_action( 'comment_form_after' );
	else :
		/**
		 * Fires after the comment form if comments are closed.
		 *
		 * @since 3.0.0
		 */
		do_action( 'comment_form_comments_closed' );
	endif;
}

add_action( 'comment_post', 'portal_ajax_comments', 25, 2 );
function portal_ajax_comments( $comment_id, $comment_status ) {

	$comment              = get_comment( $comment_id );

	if ( ( ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ) && ( strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' ) && ( get_post_type( $comment->comment_post_ID ) == 'portal_projects' ) ) {

		//If AJAX Request Then
		switch ( $comment_status ) {

			case '0':

				//notify moderator of unapproved comment
				wp_notify_moderator( $comment_id );

			case '1': //Approved comment

				// echo "success";

				$commentdata = get_comment( $comment_id, ARRAY_A );
				$permaurl = get_permalink( $comment->comment_post_ID );
				$url      = str_replace( 'http://', '/', $permaurl );

				if ( $commentdata['comment_parent'] == 0 ) {

					ob_start(); ?>

					<li <?php comment_class( 'portal-hide', $comment_id ); ?> id="comment-<?php echo $comment_id; ?>">

						<div class="comment-text">

							<header class="comment-header">
								<div class="comment-date">
									<?php echo get_comment_date( 'F j, Y \a\t g:i a', $commentdata['comment_ID'] ); ?>
								</div>
								<?php printf( __( '<cite class="fn">%s</cite>' ), $commentdata['comment_author'] ); ?>
							</header>

							<?php echo wpautop( $commentdata['comment_content'] ); ?>

						</div>

						<span class="portal-bubble"></span>

						<div class="comment-author vcard">
							<?php echo get_avatar( $commentdata['comment_author_email'], 32 ); ?>
						</div>

					</li>

					<?php

					// echo ob_get_clean();

				} else {

					echo '<ul class="children">' . project_status_comment( $commentdata, array( 'style' => 'li' ), 2 ) . '</ul>';

				}

				$post = get_post( $commentdata['comment_post_ID'] );

				wp_notify_postauthor( $comment_id );

				break;

			default:

				echo "error";

		}

		exit;

	}

}

add_action( 'portal_head', 'portal_translation_variables' );
add_action( 'wp_head', 'portal_translation_variables' );
function portal_translation_variables() {

	if( get_post_type() == 'portal_projects' ): ?>

		<script>
			var portal_translate_processesing = '<?php _e( 'Adding your comment...', 'portal_projects' ); ?>';
			var portal_translate_comment_failed = '<?php _e( 'Your comment failed, you either are posting a duplicate comment or have tried to comment again too quickly.', 'portal_projects' ); ?>';
			var portal_translate_comment_success = '<?php _e( 'Comment added...', 'portal_projects' ); ?>';
			var portal_translate_comment_error = '<?php _e( 'Comment failed, you might be having connectivity issues', 'portal_projects' ); ?>';
		</script>

	<?php
	endif;

}

function portal_validate_or_generate_comment_key( $comment_key = NULL, $phase_id, $phase_offset_id, $post_id = NULL ) {

	// If the comment key exists, just return it
	if( $comment_key != NULL ) return $comment_key;

	$post_id = ( $post_id == NULL ? get_the_ID() : $post_id );

	// Otherwise we need to generate and save it.

	$comment_key 	= portal_generate_phase_id();
	$phases 		= get_field( 'phases', $post_id );
	$phases[$phase_id]['phase_id'] = $comment_key;

	update_field( 'phases', $phases, $post_id );

	return $comment_key;

}

add_filter( 'post_password_required', 'portal_bypass_password_protection_for_logged_in_users', 10, 2 );
function portal_bypass_password_protection_for_logged_in_users( $required, $post ) {

	if( is_user_logged_in() && get_post_type($post) == 'portal_projects' ) return false;

	return $required;

}

// add_action( 'comment_form_after_fields', 'portal_private_comment_field' );
function portal_private_comment_field() {

	if( get_post_type() !== 'portal_projects' || !current_user_can('read_portal_private_comments') ) return;

 	echo '<p class="portal-private-comment-field"><label for="portal-private-comment"><input type="checkbox" value="true" name="portal-private-comment"> ' . __( 'Private Message', 'portal_projects' ) . '</label></p>';

}
