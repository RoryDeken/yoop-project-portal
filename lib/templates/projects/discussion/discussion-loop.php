<div id="portal-comments">

	<?php if ( post_password_required() ) : ?>
			<p class="nopassword"><?php _e( 'This post is password protected. Enter the password to view any comments.', 'portal_projects' ); ?></p>

		</div><!-- #comments -->

		<?php
		/* Stop the rest of comments.php from being processed,
		 * but don't kill the script entirely -- we still have
		 * to fully load the template.
		 */
		return;
	endif; ?>

	<?php
	if ( have_comments() ) :

		global $post;
		$comment_count = portal_get_general_comment_count( $post->ID ); ?>

		<h2>
			<?php esc_html_e( 'Project Discussion', 'portal_projects' ); ?>
			<span>
				<?php printf( _n( 'One Response to %2$s', '%1$s Responses to %2$s', $comment_count, 'portal_projects' ),
		number_format_i18n( $comment_count ), '<em>' . get_the_title() . '</em>' ); ?>
			</span>
		</h2>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
			<div class="navigation">
				<div class="nav-previous">
					<?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Older Comments', 'portal_projects' ) ); ?>
				</div>

				<div class="nav-next">
					<?php next_comments_link( __( 'Newer Comments <span class="meta-nav">&rarr;</span>', 'portal_projects' ) ); ?>
				</div>
			</div> <!-- .navigation -->
		<?php endif; // check for comment navigation ?>

		<ol class="commentlist">
			<?php
				/* Loop through and list the comments.
				 */
				$portal_general_comments	=	portal_get_general_comments( $post->ID );
				wp_list_comments( array( 'callback' => 'project_status_comment', 'avatar_size' => '64', 'reverse_top_level'	=>	true, 'max_depth' => 3 ), $portal_general_comments );
			?>
		</ol>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
			<div class="navigation">
				<div class="nav-previous">
					<?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Older Comments', 'portal_projects' ) ); ?>
				</div>
				<div class="nav-next">
					<?php next_comments_link( __( 'Newer Comments <span class="meta-nav">&rarr;</span>', 'portal_projects' ) ); ?>
				</div>
			</div><!-- .navigation -->
		<?php endif; // check for comment navigation ?>

	<?php else : // or, if we don't have comments: ?>

		<h2><?php esc_html_e( 'Project Discussion', 'portal_projects' ); ?> <span><?php esc_html_e( 'No discussions posted at this time', 'portal_projects' ); ?></span></h2>

		<?php
		/* If there are no comments and comments are closed,
		* let's leave a little note, shall we?
		*/
		if ( ! comments_open() ) : ?>
			<p class="nocomments"><?php _e( 'Discussions are closed.', 'portal_projects' ); ?></p>
		<?php endif; // end ! comments_open() ?>

	<?php endif; // end have_comments() ?>

	<?php comment_form(); ?>

</div><!-- #comments -->
