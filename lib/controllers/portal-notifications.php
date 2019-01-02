<?php
add_action( 'init', 'portal_setup_notifications' );
function portal_setup_notifications() {

	global $portal_notifications;

	$portal_notifications = apply_filters( 'portal_notifications', array() );

	if ( ! $portal_notifications ) {
		return;
	}

	foreach ( $portal_notifications as $notification_ID => $notification_args ) {

		// Init the post types
		portal_create_notification_post_type( $notification_ID, $notification_args );

		// Handle creating and updating feed post types
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'update' ) {
			if ( isset( $_POST["portal_{$notification_ID}_notification_feeds"] ) ) {
				portal_notification_update_feeds( $notification_ID, $notification_args );
			}
		}
	}

	// Deleting feed post types
	if ( isset( $_POST["portal_notification_deleted_feeds"] ) ) {
		portal_notification_delete_feeds();
	}
}

add_action( 'portal_after_settings_table', 'portal_notifications_feeds_section' );
function portal_notifications_feeds_section( $args ) {

	global $portal_notifications;

	if ( ! $portal_notifications || ! isset( $_GET['tab'] ) || $_GET['tab'] != 'portal_settings_notifications' ) {
		return;
	}

	if ( ! isset( $_GET['section'] ) ) {

		$sections = portal_get_registered_settings_sections();
		$section  = key( $sections['portal_settings_notifications'] );

	} else {

		$section = $_GET['section'];
	}

	$notification_ID   = $section;
	$notification_args = $portal_notifications[ $notification_ID ];

	portal_notification_feeds_section( $notification_ID, $notification_args );
}

add_filter( 'portal_settings_sections_notifications', 'portal_notifications_feeds_sections' );
function portal_notifications_feeds_sections( $sections ) {

	global $portal_notifications;

	if ( ! $portal_notifications ) {
		return $sections;
	}

	foreach ( $portal_notifications as $notification_ID => $notification_args ) {
		$sections[ $notification_ID ] = $notification_args['name'];
	}

	return $sections;
}

add_action( 'portal_notify', 'portal_notify_handle', 10, 2 );
function portal_notify_handle( $type, $args, $email_parts = array() ) {

	global $portal_notifications;

	do_action( "portal_notify_$type", $args );

	if ( $portal_notifications ) {
		foreach ( $portal_notifications as $notification_ID => $notification_args ) {
			portal_notifications_do_notification( $notification_ID, $notification_args, $type, $args, $email_parts );
		}
	}

}

add_action( 'portal_users_added_to_project', 'portal_notify_users_of_project_assignment', 10, 2 );
function portal_notify_users_of_project_assignment( $post_id, $user_ids = null ) {

	// Requires database version 7
	if ( ( empty( $user_ids ) ) || ( get_option( 'portal_database_version' ) < 7 ) ) {
		return;
	}

	do_action( 'portal_notify', 'users_assigned', array(
		'post_id'  => $post_id,
		'user_ids' => $user_ids
	) );

	foreach ( $user_ids as $user_id ) {

		do_action( 'portal_notify', 'user_assigned', array(
			'post_id' => $post_id,
			'user_id' => $user_id,
		) );
	}

}

function portal_send_email( $email_parts = array(), $args = array(), $notification_type = null ) {

	$portal_settings = get_option( 'portal_settings' );

	$from_name  = isset( $portal_settings['portal_from_name'] ) ? $portal_settings['portal_from_name'] : __( 'Project yoop', 'portal_projects' );
	$from_email = isset( $portal_settings['portal_from_email'] ) ? $portal_settings['portal_from_email'] : get_option( 'admin_email' );
	$logo       = isset( $portal_settings['portal_logo'] ) ? $portal_settings['portal_logo'] : false;
	$cuser      = wp_get_current_user();

	if ( $from_name == '%current_user%' || $from_email == '%current_user%' ) {
		$from_email = ( $from_email == '%current_user%' ? $cuser->user_email : $from_email );
		$from_name  = ( $from_name == '%current_user%' ? portal_get_nice_username_by_id( $cuser->ID ) : $from_name );
	}

	/**
	 * Extracts variables for use in templates. There may also be more, for extended templates.
	 *
	 * @var string $from_email
	 * @var string $from_name
	 * @var string $from_logo
	 * @var string $recipient_email
	 * @var string $recipient_name
	 * @var string $subject
	 * @var string $message
	 */
	extract( $args = wp_parse_args( $args, array(
		'from_email'      => $from_email,
		'from_name'       => $from_name,
		'logo'            => $logo,
		'recipient_email' => '',
		'recipient_cc'	  => '',
		'recipient_bcc'   => '',
		'recipient_name'  => '',
		'subject'         => '',
		'progress'        => '',
		'message'         => '',
		'post_id'         => '',
		'user_ids'        => '',
	) ) );

	/**
	 * Dynamically insert current username if applicable
	 */

	$cuser = wp_get_current_user();

	if ( is_user_logged_in() ) {
		$from_email = ( $from_email == '%current_user%' ? $cuser->user_email : $from_email );
		$from_name  = ( $from_name == '%current_user%' ? portal_get_nice_username_by_id( $cuser->ID ) : $from_name );
	} else {
		$from_email = ( $from_email == '%current_user%' ? get_option( 'admin_email' ) : $from_email );
		$from_name  = ( $from_name == '%current_user%' ? __( 'Project yoop', 'portal_projects' ) : $from_name );
	}

	$headers         = "From: $from_name <$from_email>\r\n";

	$recipient_email = apply_filters( 'portal_notification_recipient_email', $recipient_email, $post_id, $user_ids );
	$recipient_cc 	 = apply_filters( 'portal_notification_recipient_cc_email', $recipient_cc, $post_id, $user_ids );
	$recipient_bcc	 = apply_filters( 'portal_notification_recipient_bcc_email', $recipient_bcc, $post_id, $user_ids );

	if( !empty($recipient_cc) ) {
		$headers .= " Cc: " . $recipient_cc . "\r\n";
	}

	if( !empty($recipient_bcc) ) {
		$headers .= " Bcc: " . $recipient_bcc . "\r\n";
	}

	ob_start();

	include( portal_template_hierarchy( '/email/index.php' ) );

	$message = ob_get_clean();

	add_filter( 'wp_mail_content_type', 'portal_set_mail_content_type' );

	wp_mail( $recipient_email, $subject, $message, $headers );

	remove_filter( 'wp_mail_content_type', 'portal_set_mail_content_type' );

}

/**
 * Looks to see if notifications are turned on and if so, notify the users
 *
 *
 * @param integer $post_id post ID
 * @param integer $post Post Object
 *
 * @return NULL
 **/

add_action( 'save_post', 'portal_notify_users', 0 );
function portal_notify_users( $post_id ) {

	// Bail if we're doing an auto save
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// if our current user can't edit this post, bail
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( get_post_type( $post_id ) == 'portal_projects' ) {

		// Check to see if notifications were turned on
		if ( isset( $_POST['portal-notify-users'] ) ) {

			// Get an array of users
			$user_ids = portal_sanitize_integers( $_POST['portal-user'] );
			$progress = portal_compute_progress( $post_id );

			$subject = apply_filters( 'portal_notification_subject', stripslashes( $_POST['portal-notification-subject'] ) );
			$message = apply_filters( 'portal_notification_message', stripslashes( $_POST['portal-notify-message'] ) );

			foreach ( $user_ids as $user_id ) {

				$subject = apply_filters( 'portal_notification_subject_user', $subject, $user_id );
				$message = apply_filters( 'portal_notification_message_user', $message, $user_id );

				portal_send_progress_email( $user_id, $subject, $message, $post_id, $progress );

			}
		}
	}
}

/*
 * Replace project template variables with the correct data
 *
 * @param string $text The text potentially containing template variables
 * @return string $text The text with all template variables replaced
 */
function portal_populate_project_template_variables( $text, $post_id = null ) {

	$post_id = ( isset( $post_id ) ? $post_id : get_the_ID() );

	$project_title = get_the_title( $post_id );
	$client        = sanitize_text_field( get_field( 'client', $post_id ) );
	$project_url   = get_the_permalink( $post_id );
	$dashboard     = portal_get_option( 'portal_slug', 'yoop' );
	$date          = current_time( get_option( 'date_format' ) );

	$template_var_replacements = array(
		'%project_title%' => ! empty( $project_title ) ? $project_title : '',
		'%client%'        => ! empty( $client ) ? $client : '',
		'%project_url%'   => ! empty( $project_url ) ? $project_url : '',
		'%dashboard%'     => ! empty( $dashboard ) ? $dashboard : '',
		'%date%'          => ! empty( $date ) ? $date : '',
	);

	foreach ( $template_var_replacements as $template_variable => $replacement ) {
		$text = str_replace( $template_variable, $replacement, $text );
	}

	return $text;
}

add_filter( 'portal_notification_subject', 'portal_populate_project_template_variables' );
add_filter( 'portal_notification_message', 'portal_populate_project_template_variables' );

/*
 * Replace template variables particular to each user with the correct data
 *
 * @param string $text The text potentially containing template variables
 * @param int $user_id The user ID
 * @return string $text The text with all template variables replaced
 */
function portal_populate_user_template_variables( $text, $user_id ) {

	$user_data    = get_userdata( $user_id );
	$display_name = ! empty( $user_data->display_name ) ? $user_data->display_name : '';

	return str_replace( '%name%', $display_name, $text );

}

add_filter( 'portal_notification_subject_user', 'portal_populate_user_template_variables', 10, 2 );
add_filter( 'portal_notification_message_user', 'portal_populate_user_template_variables', 10, 2 );

/*
 * Sanitize integers
 *
 * @param mixed $integers integers to be sanitized
 * @return mixed $integers sanitized integers
 */
function portal_sanitize_integers( $integers ) {

	if ( ! is_array( $integers ) ) {
		$integers = (int) sanitize_text_field( $integers );
	} else {
		foreach ( $integers as $index => $integer ) {
			$integers[ $index ] = (int) sanitize_text_field( $integer );
		}
	}

	return $integers;
}

function portal_set_mail_content_type() {
	return 'text/html';
}

add_filter( 'portal_notification_recipient_bcc_email', 'portal_replace_recipient_variables', 10, 3 );
add_filter( 'portal_notification_recipient_cc_email', 'portal_replace_recipient_variables', 10, 3 );
add_filter( 'portal_notification_recipient_email', 'portal_replace_recipient_variables', 10, 3 );
function portal_replace_recipient_variables( $recipient_email, $post_id, $user_ids = null ) {

	$user_groups = array(
		'%users%'            => null,
		'%subscribers%'      => 'subscriber',
		'%project_owners%'   => 'portal_project_owner',
		'%project_managers%' => 'portal_project_manager'
	);

	if ( in_array( $recipient_email, array_keys( $user_groups ) ) ) {

		$key             = $recipient_email;
		$recipient_email = '';
		$users           = portal_get_project_users( $post_id );
		$cuser           = wp_get_current_user();

		if ( $users ) {
			foreach ( $users as $user ) {
				if ( ( $key == '%users%' || portal_user_has_role( $user_groups[ $key ], $user['ID'] ) ) && $user['ID'] != $cuser->ID ) {
					$recipient_email .= $user['user_email'] . ',';
				}
			}
		}

	}

	if ( $user_ids != null ) {

		$recipient_email = '';

		foreach ( $user_ids as $user_id ) {
			$user            = get_user_by( 'id', $user_id );
			$recipient_email .= $user->user_email . ', ';
		}

	}

	return $recipient_email;

}

// Add the option to the publish metabox
add_action( 'post_submitbox_misc_actions', 'portal_notify_metabox' );
function portal_notify_metabox() {

	global $post;

	if ( 'portal_projects' != get_post_type( $post ) ) {
		return;
	}

	if ( get_field( 'allowed_users', $post->ID ) ): ?>

        <div class="misc-pub-section misc-pub-section-last" style="border-top: 1px solid #eee;">
			<?php wp_nonce_field( plugin_basename( __FILE__ ), 'article_or_box_nonce' ); ?>
            <input type="checkbox" name="portal-notify-users" id="portal-notify-users" value="yes"/>
            <label for="portal-notify-users" class="select-it">
				<?php esc_html_e( 'Notify users of update', 'portal_projects' ); ?>
                <a href="#portal-notification-modal"
                   class="portal-notification-edit"><?php esc_html_e( 'Edit', 'portal_projects' ); ?></a>
            </label>
        </div>

        <div class="portal-notification-modal" id="portal-notification-modal">

            <div class="portal-notify-warning">
                <p><?php esc_html_e( "Save this project to notify recently added users.", 'portal_projects' ); ?></p>
            </div>

            <p>
                <strong><?php esc_html_e( "Select which users you'd like to notify upon save.", "portal_projects" ); ?></strong>
            </p>

            <ul class="portal-notify-list">
                <li class="all-line"><input type="checkbox" class="all-checkbox" name="portal-notify-all"
                                            value="all"> <?php esc_html_e( 'All Users', 'portal_projects' ); ?></li>
				<?php
				// Get the project ID
				$project_id = $post->ID;

				$users = portal_get_project_users( $post->ID );
				if ( $users ): foreach ( $users as $user ): ?>
                    <li><input type="checkbox" name="portal-user[]" value="<?php echo esc_attr( $user['ID'] ); ?>"
                               class="portal-notify-user-box"><?php echo esc_html( portal_get_nice_username_by_id( $user['ID'] ) ); ?>
                    </li>
				<?php endforeach; endif; ?>
            </ul>

            <p><label for="portal-notification-subject"><?php esc_html_e( 'Subject', 'portal_projects' ); ?></label></p>
            <p><input type="text" id="portal-notification-subject" name="portal-notification-subject"
                      value="<?php echo portal_get_option( 'portal_default_subject' ); ?>"></p>
            <p><label for="portal-notify-message"><?php esc_html_e( 'Message', 'portal_projects' ); ?></label></p>
            <p><textarea name="portal-notify-message"
                         class="portal-notify-message"><?php echo portal_get_option( 'portal_default_message' ); ?></textarea>
            <p><label for="portal-notify-message"
                      class="portal-label-description"><?php esc_html_e( 'Available dynamic variables: %project_title% %client% %project_url% %dashboard% %date%', 'portal_projects' ); ?></label>
            </p>
            <p><?php esc_html_e( 'Selected users will be sent this notice next time you save this project.', 'portal_projects' ); ?></p>
            <p><a class="button-primary portal-notify-ok" href="#"><?php esc_html_e( 'OK', 'portal_projects' ); ?></a></p>

        </div>

	<?php else: ?>

        <div class="misc-pub-section misc-pub-section-last" style="border-top: 1px solid #eee;">
			<?php wp_nonce_field( plugin_basename( __FILE__ ), 'article_or_box_nonce' ); ?>
            <input type="checkbox" name="portal-notify-users" id="portal-notify-users" value="yes" disabled/>
            <label for="portal-notify-users"
                   class="select-it portal-disabled"><?php _e( 'Notify users of update', 'portal_projects' ); ?>
                <a href="#portal-no-users" class="portal-notification-help"><?php esc_html_e( 'Help', 'portal-projects' ); ?></a>
            </label>
        </div>

        <div class="portal-notification-modal" id="portal-notification-modal">
            <p>
                <strong><?php esc_html_e( 'Users must be assigned to this project before you can notify them.', 'portal-projects' ); ?></strong>
            </p>
            <p><?php esc_html_e( 'Assign users to this project by through the access tab under project overview. Once you save the project you can notify users on future updates.' ); ?></p>
            <p><?php esc_html_e( 'Example:', 'portal-projects' ); ?></p>
            <p>
                <img src="<?php echo site_url(); ?>/wp-content/plugins/yoop-project-portal/assets/images/help/notification-help.png">
            </p>
        </div>

	<?php
	endif;

}

add_action( 'portal_project_progress_change', 'portal_project_change_notification', 10, 2 );
function portal_project_change_notification( $post_id, $new_progress ) {

	do_action( 'portal_notify', 'project_progress', array(
		'post_id'       => $post_id,
		'project_title' => get_the_title( $post_id ),
		'new_progress'  => $new_progress
	) );

}
