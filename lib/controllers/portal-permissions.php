<?php
/**
 * portal-permissions.php
 *
 * Manage user levels and permissions to projects
 *
 * @category controller
 * @package portal-projects
 * @author Ross Johnson
 * @since 1.3.6
 */

function portal_get_project_users( $post_id = NULL ) {

	// Extra caution
	global $post;
	$post_id = ( $post_id == NULL ? $post->ID : $post_id );

	$project_teams	= get_field( 'teams' , $post_id );
	$users 			= array();

	// TODO: Could this be simplified with an array_merge?
	while( have_rows( 'allowed_users', $post_id ) ) { the_row();
		$users[]	= get_sub_field( 'user' );
	}

	if( !empty( $project_teams ) ) {
		foreach( $project_teams as $team ) {

			$team_users = portal_get_team_members( $team );

			// TODO: Could this be simplified with an array_merge?
			if( ( !empty( $team_users ) ) && ( is_array( $team_users ) ) ) {
				foreach( $team_users as $team_user ) $users[]	=	$team_user;
			}

		}
	}

	return $users;

}

/*
 * Adds two roles for users
 *
 */

add_action( 'admin_init', 'portal_add_project_roles' );
function portal_add_project_roles() {

    add_role( 'portal_project_owner',
        __( 'Project Owner', 'portal_projects' ),
        array(
            'read' 			=> true,
            'edit_posts' 	=> false,
            'delete_posts' 	=> false,
            'publish_posts' => false,
            'upload_files' 	=> true,
        )
    );

    add_role( 'portal_project_creator',
        __( 'Project Creator', 'portal_projects' ),
        array(
            'read' 			=> true,
            'edit_posts' 	=> false,
            'delete_posts' 	=> false,
            'publish_posts' => false,
            'upload_files' 	=> true,
        )
    );

    add_role('portal_project_manager',
        __( 'Project Manager', 'portal_projects' ),
        array(
            'read' 			=> true,
            'edit_posts' 	=> false,
            'delete_posts' 	=> false,
            'publish_posts' => false,
            'upload_files' 	=> true,
        )
    );

}

/*
* Assigns capabilities to the project roles
*/
function portal_make_role_project_owner( $role ) {

    $caps = array(
        'edit_portal_project',
        'edit_portal_projects',
        'edit_others_portal_projects',
        'read_portal_project',
        'read_private_portal_project',
		'see_priority_portal_projects',
		'read_portal_private_comments'
    );

    foreach( $caps as $cap ) {
        $role->add_cap( $cap );
    }

}

function portal_make_role_project_creator( $role ) {

    $caps = array(
        'edit_portal_project',
        'edit_portal_projects',
        'edit_portal_projects',
        'edit_published_posts',
        'read_portal_project',
        'read_private_portal_project',
        'edit_published_portal_projects',
        'publish_portal_projects',
        'delete_portal_projects',
		'see_priority_portal_projects',
		'read_portal_private_comments',
		'copy_posts'
    );

	$rems = array( 'edit_others_portal_projects' );

    foreach( $caps as $cap ) {
        $role->add_cap( $cap );
    }

	foreach( $rems as $rem ) {
		$role->remove_cap( $rem );
	}

}

function portal_make_role_project_manager($role) {

    $caps = array(
        'read',
        'read_portal_project',
        'read_private_portal_projects',
        'edit_portal_project',
        'edit_portal_projects',
        'edit_private_portal_projects',
        'edit_others_portal_projects',
        'edit_published_portal_projects',
        'publish_portal_projects',
        'delete_portal_projects',
        'delete_others_portal_projects',
        'delete_private_portal_projects',
        'delete_published_portal_projects',
        'copy_posts',
		'see_priority_portal_projects',
		'read_portal_private_comments',
		// Teams
		'read_portal_team',
		'read_private_portal_teams',
		'edit_portal_team',
		'edit_portal_teams',
		'edit_others_portal_teams',
		'edit_published_portal_teams',
		'publish_portal_teams',
		'delete_others_portal_teams',
		'delete_private_portal_teams',
		'delete_published_portal_teams',
    );

    foreach( $caps as $cap ) {

        $role->add_cap( $cap );

    }

}

add_action( 'admin_init', 'portal_add_role_caps', 999 );
function portal_add_role_caps() {

	$has_run = get_option( 'portal_has_set_permissions', false );

	if( $has_run ) {
		return;
	}

    $owners = get_role( 'portal_project_owner' );
	if( !empty( $owners ) ) {
		portal_make_role_project_owner( $owners );
	}

    $manager = get_role( 'portal_project_manager' );
    if( !empty( $manager ) ) {
		portal_make_role_project_manager( $manager );
	}

    $admin = get_role( 'administrator' );
    if( !empty( $admin ) ) {
		portal_make_role_project_manager( $admin );
	}

    $editor = get_role( 'editor' );
    if( !empty( $editor ) ) {
		portal_make_role_project_manager( $editor );
	}

    $creator = get_role( 'portal_project_creator' );
    if( !empty( $creator ) ) {
        portal_make_role_project_creator( $creator );
    }

	update_option( 'portal_has_set_permissions', true );

}

/*
    Remove the add button for project owners
*/
add_action( 'admin_menu', 'portal_remove_add_project' );
function portal_remove_add_project() {

    global $submenu;

    if( portal_get_current_user_role() == 'Project Owner' ) {
        $submenu['edit.php?post_type=portal_projects'][10][1] = '';
    }

}

/**
 * Returns the translated role of the current user. If that user has
 * no role for the current blog, it returns false.
 *
 * @return string The name of the current role
 **/
function portal_get_current_user_role() {

    global $wp_roles;

    $current_user 	= wp_get_current_user();
    $roles 			= $current_user->roles;
    $role 			= array_shift( $roles );

    return isset( $wp_roles->role_names[$role]) ? translate_user_role($wp_roles->role_names[$role] ) : false;
}

/**
 * Outputs a list of projects assigned to a particular user
 *
 * @return HTML table
 **/
add_action( 'admin_menu', 'register_portal_user_project_list' );
function register_portal_user_project_list() {
    add_submenu_page( NULL, 'Projects By User', 'Projects by User', 'manage_options', 'portal_user_list', 'portal_user_project_list' );
}

/**
 * Hook into the failed login and redirect to our login form and display an error
 * @param  string $user WordPress user login
 * @return NULL
 */

add_action( 'wp_authenticate', 'portal_login_failed' );
add_action( 'wp_login_failed', 'portal_login_failed' );
function portal_login_failed( $user ) {

	if( isset( $_POST[ 'portal-login-form' ] ) ) {

		$referrer 	= $_SERVER[ 'HTTP_REFERER' ];
		$retry		= strpos( $referrer, '?login=failed' );

		if( !empty( $retry ) ) {
			wp_redirect( $_SERVER[ 'HTTP_REFERER' ] );
		} else {
			wp_redirect( $_SERVER[ 'HTTP_REFERER' ] . '?login=failed' );
		}

	}

}

/**
 * Add custom hidden field so we know this login field is coming form a portal page
 */
add_filter( 'login_form_middle', 'portal_add_login_field' );
function portal_add_login_field( $content ) {

	if( ( get_post_type() == 'portal_projects' ) || ( is_post_type_archive( 'portal_projects' ) ) ) {
		$content .= '<input type="hidden" name="portal-login-form" value="true">';
	}

	return $content;

}

/**
 * Checks to see if the user has access to the project, returns 1 if access is granted, 0 if false
 *
 *
 * @param integer $post_id post ID
 * @return int 1 or 0
 **/
function yoop_check_access( $post_id = NULL ) {

    $access_level 	= get_field( 'restrict_access_to_specific_users' , $post_id );
	$project_teams	= get_field( 'teams' , $post_id );
    $current_user 	= wp_get_current_user();
	$result			= FALSE; // default to false unless
    $author         = get_post_field( 'post_author', $post_id );

	if( is_post_type_archive() && !is_user_logged_in() ) {

		$result = FALSE;

	} elseif( is_post_type_archive() && is_user_logged_in() ) {

		$result = TRUE;

	} elseif( current_user_can( 'delete_others_portal_projects' ) ) {

		// Admin or PM, can view / edit anything
	    $result = TRUE;

    } elseif( post_password_required() ) {

		// Password required, require password entry
        $result = FALSE;

	} elseif( ( $access_level ) && ( !is_user_logged_in() ) ) {

		// Post is private and the user isn't logged in
        $result = FALSE;

    } elseif( ( current_user_can( 'publish_portal_projects' ) ) && ( $author == $current_user->ID ) ) {

        // The user can publish projects and they are the post author
        $result = TRUE;

    } elseif( ( !$access_level ) && ( current_user_can( 'edit_others_portal_projects' ) ) ) {

		// This project isn't private, user can view
        $result = TRUE;

	} elseif( ( $access_level ) && ( is_user_logged_in() ) ) {

		// Post is private, user is logged in and they are not an admin. Check to see if they have access.

		if( $project_teams ) {

			foreach( $project_teams as $team ) {

				// $team is the CPT ID

				$team_users = get_field( 'team_members' , $team );

				// TODO: Simplify this with in_array();

				if( ( !empty( $team_users ) ) && ( is_array( $team_users ) ) ) {

					foreach( $team_users as $user ) {

						if( $user[ 'team_member' ][ 'ID' ] == $current_user->ID ) {

							$result = TRUE;

						}

					}

				}

			}

		}

		// If we've gotten this far users are not part of a team, check if they are individually added

		$allowed_users 	= array(); // Array of all users assigned to this project

        while ( have_rows( 'allowed_users' , $post_id ) ) { the_row();

			$user = get_sub_field( 'user' );

			if( isset( $user[ 'ID' ] ) && $user[ 'ID' ] == $current_user->ID ) $result = TRUE;

        }


    } else {

		// Return true
        $result = TRUE;

	}

	return apply_filters( 'yoop_check_access', $result, $post_id );

}

add_action( 'add_meta_boxes' , 'portal_project_edit_restrictions' );
function portal_project_edit_restrictions() {

	global $post;

	if( ( get_post_type() == 'portal_projects' ) && ( !yoop_check_access( $post->ID ) ) ) {
		wp_die( 'Sneaky, you don\'t have access to this project!' );
	}

}

/**
 * portal_can_edit_task()
 * Checks to see if the user can edit a task
 *
 * @param integer $post_id 		Post ID
 * @param integer $phase_id 	Phase ID of Post ID
 * @param integer $task_id 		Task ID of Phase ID
 *
 * @return bool
 **/
function portal_can_edit_task( $post_id, $phase_id, $task_id ) {

	// Check to see if the user has permissions to edit the project, if so they can complete the task
	if( portal_can_edit_project( $post_id ) ) return true;

	if( !is_user_logged_in() ) return false;

	$current_user 	= wp_get_current_user();
	$phases 		= get_field( 'phases', $post_id );

	// If this task is assigned to the current user then they can edit it
	if( $phases[$phase_id]['tasks'][$task_id]['assigned'] == $current_user->ID ) {
		return apply_filters( 'portal_can_edit_task', true, $post_id, $phase_id, $task_id );
	}

	return apply_filters( 'portal_can_edit_task', false, $post_id, $phase_id, $task_id );

}

/**
 * Checks to see if the user can edit the current project, returns true or false
 *
 *
 * @param integer $id post ID
 * @return bool
 **/
function portal_can_edit_project( $post_id = NULL ) {

	$post_id = ( $post_id == NULL ? get_the_ID() : $post_id );

    if( current_user_can( 'delete_others_portal_projects' ) ) {

		// User can publish projects, so they can edit all projects
        return true;

	} elseif( current_user_can( 'edit_portal_projects' ) ) {

		// User can edit projects but not publish, see if they are assigned to this project

		$current_user 	= wp_get_current_user();
		$allowed_users 	= portal_get_project_users( $post_id );

		if( get_post_meta( $post_id, '_portal_post_author', true ) == $current_user->ID ) return true;

		// Loop through all the allowed users and add them to an array
		if( !empty( $allowed_users ) ) {
			foreach ( $allowed_users as $allowed_user ) {

				if( !isset( $allowed_user[ 'ID' ] ) ) return false;

				// If the current user matches this user ID return true
            	if( $current_user->ID == $allowed_user[ 'ID' ] ) {
					return true;
				}

			} //end.foreach
		} //end.if

    }

	// User can't edit this project, return false
    return false;

}

/**
 * portal_allowed_posts_where()
 *
 * Alters the query string so we can query for projects assigned to a specific user
 *
 * @param $where
 * @return $where
 *
 **/

add_filter( 'posts_where', 'portal_allowed_posts_where' );
function portal_allowed_posts_where( $where ) {

	global $wpdb;

	if( method_exists( $wpdb, 'remove_placeholder_escape' ) ) {
    	$where = str_replace("meta_key = 'allowed_users_%_user'", "meta_key LIKE 'allowed_users_%_user'", $wpdb->remove_placeholder_escape($where) );
	} else {
		$where = str_replace("meta_key = 'allowed_users_%_user'", "meta_key LIKE 'allowed_users_%_user'", $where );
	}

    return $where;

}

add_filter( 'posts_where', 'portal_tasks_assigned_posts_where' );
function portal_tasks_assigned_posts_where( $where ) {

	global $wpdb;

	if( method_exists( $wpdb, 'remove_placeholder_escape' ) ) {
    	$where = str_replace( "meta_key = 'phases_*_tasks_%_assigned'", "meta_key LIKE 'phases_*_tasks_%_assigned'", $wpdb->remove_placeholder_escape($where) );
	} else {
		$where = str_replace( "meta_key = 'phases_*_tasks_%_assigned'", "meta_key LIKE 'phases_*_tasks_%_assigned'", $where );
	}

    return $where;

}


/*
 * limit_portal_to_granted_users()
 *
 * Limits what projects are available to the ones the user has access to
 * TODO: Rewrite this using SQL query
 */

add_filter( 'pre_get_posts', 'limit_portal_projects_list_to_granted_users', 999 );
function limit_portal_projects_list_to_granted_users( $query ) {

    global $pagenow;

    // Check to see if were in the admin panel and project edit page
	if( !isset( $_GET[ 'post_type'] ) ) return;

    if( ( $query->is_admin ) && ( $pagenow == 'edit.php' ) && ( $query->query[ 'post_type' ] == 'portal_projects' ) ) {

		$user_id 			= get_current_user_id();
		$portal_meta_query 	= portal_access_meta_query( $user_id );

		if( $portal_meta_query ) {
			$query->set( 'meta_query', $portal_meta_query );
		}

    }

}

function portal_access_meta_query( $user_id = NULL ) {

	if( $user_id == NULL ) {
		$user 		= wp_get_current_user();
		$user_id	= $user->ID;
	}

	if( ( !current_user_can( 'publish_pages' ) ) && ( portal_get_current_user_role() != 'Project Manager' ) ) {

		$team_meta_query	= portal_team_dynamic_meta_query( portal_get_team_ids( $user_id ) );

		$meta_query = array(
			'relation'	=>	'OR',
	       	array(
				'key' 	=> 'allowed_users_%_user',
				'value' => $user_id
			),
			array(
				'key' 	=> 'restrict_access_to_specific_users',
				'value' => ''
			),
			array(
				'key'	=>	'_portal_post_author',
				'value'	=>	$user_id
			)
		);

		if( $team_meta_query ) {
			$meta_query = array_merge( $meta_query, $team_meta_query );
		}

		return apply_filters( 'portal_project_access_meta_query', $meta_query );

	} else {

		return FALSE;

	}

}

add_filter( 'pre_get_posts', 'portal_limit_to_completed_projects', 999 );
function portal_limit_to_completed_projects( $query ) {

	global $pagenow;

	if(!isset($_GET['post_type'])) {
		return $query;
	}

	if(isset($_GET['post_status'])) {

		if(($pagenow == 'edit.php') && ($_GET['post_type'] == 'portal_projects') && ($query->is_main_query())) {

			if($_GET['post_status'] == 'completed') {

				$query->set('tax_query', array(
						array(
							'taxonomy'	=>	'portal_status',
							'field'		=>	'slug',
							'terms'		=>	'completed',
						)
					)
				);
			}

		}

	} elseif (($pagenow == 'edit.php') && ($_GET['post_type'] == 'portal_projects') && ($query->is_main_query())) {

		$query->set('tax_query',array(
				array(
					'taxonomy'	=>	'portal_status',
					'field'		=>	'slug',
					'terms'		=>	'completed',
					'operator'	=>	'NOT IN'
				),
			)
		);

		$query->set('post_status','publish');

	}

	return $query;

}

function yoop_login_form( $password = NULL, $redirect = NULL ) {

	$redirect = ( $redirect == NULL ? portal_full_url( $_SERVER ) : $redirect );

	if( empty( $password ) ):

    	return wp_login_form( apply_filters( 'portal_login_form', array( 'redirect' => $redirect ) ) );

	else:

		ob_start(); ?>

		<form action="<?php echo esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ); ?>" method="post">

			<p><label for="post_password"><?php esc_html_e( 'Password', 'portal_projects' ); ?></label>

			<input name="post_password" type="password"></p>

			<p><input type="submit" name="Submit" value="<?php _e( 'Submit', 'portal_projects' ); ?>" class="pano-btn portal-btn"></p>

		</form>

		<?php

	endif;

}

add_filter( 'login_form_middle', 'portal_inject_login_form_hook' );
function portal_inject_login_form_hook( $content ) {

	if( get_post_type() == 'portal_projects' || is_post_type_archive( 'portal_projects' ) ) {

		ob_start();

			do_action( 'login_form' );

		$content = ob_get_clean();

	}

	return $content;

}

add_filter( 'ajax_query_attachments_args', 'portal_show_current_user_attachments' );
function portal_show_current_user_attachments( $query ) {

    $user_id = get_current_user_id();

    if ( $user_id && ( portal_user_has_role('portal_project_owner') || portal_user_has_role('portal_project_creator') ) ) {
        $query['author'] = $user_id;
    }

    return $query;
}

function portal_get_hashed_id( $cuser = NULL ) {

	$cuser = ( $cuser == NULL ? wp_get_current_user() : $cuser );

	return str_rot13($cuser->user_nicename);

}

add_filter( 'acf/prepare_field/key=field_569707ee2c384', 'portal_author_remove_user_access_controls' );
add_filter( 'acf/prepare_field/key=field_532b8d8b9c46b', 'portal_author_remove_user_access_controls' );
function portal_author_remove_user_access_controls( $field ) {

	$user_id = get_current_user_id();

	if ( $user_id && ( portal_user_has_role('portal_project_owner') || portal_user_has_role('portal_project_creator') ) ) {

		return false;

	}

	return $field;

}

add_action( 'admin_head', 'portal_author_hide_user_access_controls' );
add_action( 'portal_head', 'portal_author_hide_user_access_controls' );
function portal_author_hide_user_access_controls() {

	$user_id = get_current_user_id();

	if( is_admin() && get_post_type() !== 'portal_projects' ) {
		return;
	}

	if ( $user_id && ( portal_user_has_role('portal_project_owner') || portal_user_has_role('portal_project_creator') ) ) { ?>
		<style type="text/css">
			#acf-teams,
			#acf-allowed_users {
				display: none;
			}
		</style>
	<?php }

}


function portal_get_edit_post_link( $post_id = null, $action = '', $context = '' ) {

	$post_id = ( $post_id == null ? get_the_ID() : $post_id );

	if ( ! $post = get_post( $post_id ) )
        return;

    if ( 'revision' === $post->post_type )
        $action = '';
    else
        $action = '&action=edit';

    $post_type_object = get_post_type_object( $post->post_type );
    if ( !$post_type_object )
        return;

    if ( $post_type_object->_edit_link ) {
        $link = admin_url( sprintf( $post_type_object->_edit_link . $action, $post->ID ) );
    } else {
        $link = '';
    }

	return $link;

}
