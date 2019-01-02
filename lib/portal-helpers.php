<?php
/**
 * portal-helpers.php
 * A library of helper and utility functions for Project yoop
 *
 * @author Ross Johnson
 * @copyright 3.7 MEDIA
 * @license GNU GPL version 3 (or later) {@see license.txt}
 * @package yoop
 **/

/**
 * Looks up a users name by ID and returns their full name (if available) or their display name as a fallback
 *
 * @return HTML table
 **/
function portal_username_by_id( $user_id ) {

    $user = get_user_by( 'id', $user_id );

    if( empty( $user ) ) return false;

    return ( $user->first_name ? $user->first_name . ' ' . $user->last_name : $user->display_name );

}

/**
 * Counts the number of phases in the current project
 *
 * @param int $post_id the id of a project
 * @return int number of phases
 **/
function portal_get_phase_count( $post_id = null ) {

	$post_id = ( $post_id == null ? get_the_ID() : $post_id );

	$phases = get_field( 'phases', $post_id );

	return count( $phases );

}

/**
 * Loads an external .php file in the /lib/pro/fields directory if the file doesn't exist in the theme/yoop/fields folder
 *
 *
 * @param string $$template name of the file with or without .php
 * @return null -- includes file
 **/

function portal_load_field_template( $template ) {

    // Get the template slug
    $template_slug = rtrim( $template, '.php' );

	if( !function_exists( 'update_sub_field' ) ) {

		// This must be ACF4 or bundled, use default fields
    	$template = $template_slug . '.php';

	} else {

		// This must be ACF5, load special fields
	    $template = $template_slug . '-acf5.php';

	}

    // Check if a custom template exists in the theme folder, if not, load the plugin template file
    if ( $theme_file = locate_template( array( 'yoop/fields' . $template ) ) ) {
        $file = $theme_file;
    }
    else {
        $file = portal_BASE_DIR . '/pro/fields/' . $template;
    }

    include_once( $file );

}

function portal_get_nice_username_by_id( $user_id ) {

    $user       = get_user_by( 'ID', $user_id );
    $fullname   = $user->first_name . ' ' . $user->last_name;

    if ( $fullname == ' ' ) {
        $username = $user->display_name;
	} else {
	    $username = $fullname;
	}

	return apply_filters( 'portal_get_nice_username', $username, $user );

}

function portal_get_nice_username( $user ) {

    $fullname   = $user[ 'user_firstname' ] . ' ' . $user[ 'user_lastname' ];

    if ( $fullname == ' ' ) {
        $username = $user[ 'display_name' ];
	} else {
	    $username = $fullname;
	}

	return apply_filters( 'portal_get_nice_username', $username, $user );

}

/* Lookup the current users projects, count their status and return them in an array */
// TODO -- This should probably be cached somehow
function portal_my_projects_overview( $projects = null ) {

	if( empty( $projects ) ) {
		$projects = portal_get_all_my_projects();
	}

	$total_projects 	= $projects->found_posts;
	$completed_projects = 0;
	$inactive_projects 	= 0;

	while( $projects->have_posts() ) { $projects->the_post();

		global $post;

		if( has_term( 'completed', 'portal_status' ) ) {
			$completed_projects++;
		}

		if( portal_compute_progress( $post->ID ) == 0) {
			$inactive_projects++;
		}

	}

	$closed_projects = $completed_projects + $inactive_projects;

	if( ( $total_projects > 0 ) && ( $total_projects > $closed_projects ) ) {
		$active_projects = $total_projects - $completed_projects - $inactive_projects;
	} else {
		$active_projects = 0;
	}

	return apply_filters( 'portal_my_projects_overview', array(
		'total'		=>	$total_projects,
		'completed'	=>	$completed_projects,
		'inactive'	=>	$inactive_projects,
		'active'	=>	$active_projects
	), $projects );

}

/* Get all the projects assigned to the current logged in user */
function portal_get_all_my_projects( $status = null, $count = NULL, $paged = 1, $extra_args = NULL ) {

	$cuser 		        = wp_get_current_user();
	$meta_query         = portal_access_meta_query( $cuser->ID );
    $posts_per_page     = ( isset( $count ) ? $count : -1 );

	$args = array(
		'post_type'			=>	    'portal_projects',
		'posts_per_page'	=>		$posts_per_page,
        'paged'             =>      $paged,
	);

	if( !empty( $status ) ) {

		if($status == 'active') {

			$status_args = array('tax_query' => array(
						array(
							'taxonomy'	=>	'portal_status',
							'field'		=>	'slug',
							'terms'		=>	'completed',
							'operator'	=>	'NOT IN'
							)
						)
					);

		} elseif($status == 'completed') {

			$status_args = array('tax_query' => array(
						array(
							'taxonomy'	=>	'portal_status',
							'field'		=>	'slug',
							'terms'		=>	'incomplete',
							'operator'	=>	'NOT IN'
							)
						)
					);

		} else {

			$status_args = array( 'post_status' => $status );

		}

		$args = array_merge( $args, $status_args );

	}

	if( $meta_query ) {
		$args = array_merge( $args, array( 'meta_query' => $meta_query ) );
	}

    if( $extra_args ) {
        $args = array_merge( $args, $extra_args );
    }

    $args = apply_filters( 'portal_get_all_my_projects_args', $args );

	// Query with the above arguments
	$projects = new WP_Query( $args );

	return $projects;

}

function portal_get_all_my_project_ids( $status = null ) {

	$cuser 		= wp_get_current_user();
	$meta_query = portal_access_meta_query( $cuser->ID );

	$args = array(
		'post_type'			=>		'portal_projects',
		'posts_per_page'	=>		-1,
		'fields'			=>		'ids',
	);

	if( !empty( $status ) ) {

		if( $status == 'active' ) {

			$status_args = array( 'tax_query' => array(
						array(
							'taxonomy'	=>	'portal_status',
							'field'		=>	'slug',
							'terms'		=>	'completed',
							'operator'	=>	'NOT IN'
							)
						)
					);

		} elseif( $status == 'completed' ) {

			$status_args = array( 'tax_query' => array(
						array(
							'taxonomy'	=>	'portal_status',
							'field'		=>	'slug',
							'terms'		=>	'incomplete',
							'operator'	=>	'NOT IN'
							)
						)
					);

		} else {

			$status_args = array( 'post_status' => $status );

		}

		$args = array_merge( $args, $status_args );

	}

	if( $meta_query ) {

		$args = array_merge( $args, array( 'meta_query' => $meta_query ) );

	}

	// Query with the above arguments
	$projects = get_posts( $args );

	return $projects;

}

function portal_get_active_projects( $projects = NULL ) {

	$active = array();

	if( $projects->have_posts()) {
		while( $projects->have_posts() ) {
			$projects->the_post();

			if( has_term( 'completed', 'portal_status' ) ) {
				continue;
			} else {
				$title 		= get_the_title();
				$permalink 	= get_permalink();
				array_push( $active, array( 'title' => $title, 'permalink' => $permalink ) );
			}
		}

		return $active;

	}

	return FALSE;

}

function portal_get_completed_projects() {

	$cuser 		= wp_get_current_user();
	$meta_query = portal_access_meta_query( $cuser->ID );

	$args = array(
		'post_type'			=>		'portal_projects',
		'posts_per_page'	=>		-1,
		'tax_query' => 		array(
			array(
				'taxonomy'	=>	'portal_status',
				'field'		=>	'slug',
				'terms'		=>	'completed',
				)
			)
	);

	if( $meta_query ) {
		array_merge( $args, array( 'meta_query' => $meta_query ) );
	}

	$projects = new WP_Query( $args );

	return $projects;

}

/**
 * Get count of phases, tasks, completed tasks and started tasks
 *
 * TOOD: This could be much simpler.
 *
 * @param  [type] $post_id [description]
 * @param  [type] $user_id [description]
 * @return [type]          [description]
 */
function portal_get_item_count( $post_id, $user_id = NULL ) {

	$phases 	= 0;
	$tasks 		= 0;
	$completed 	= 0;
	$started 	= 0;

	// Just in case a post object is passed through
	if( !is_int( $post_id ) ) {
		$post_id = $post_id->ID;
	}

	while( have_rows( 'phases', $post_id ) ) {

		$phase = the_row();

		$phases++;

		while( have_rows( 'tasks' ) ) {

			$task = the_row();

			if($user_id == NULL) {

				$tasks++;

				if ( get_sub_field( 'status' ) == 100 ) {
					$completed++;
				}
				else if ( get_sub_field( 'status' ) != 0 ) {
					$started++;
				}

			} elseif( $user_id == get_sub_field( 'assigned' ) ) {

				$tasks++;

				if ( get_sub_field( 'status' ) == 100 ) {
					$completed++;
				}
				else if ( get_sub_field( 'status' ) != 0 ) {
					$started++;
				}

			}

			// This lets us "Filter" three items at once by passing them by reference
			do_action_ref_array( 'portal_get_item_count_task_loop', array( &$phases, &$tasks, &$completed, &$started, $phase, $task, $user_id, $post_id ) );

		}

	}

	return apply_filters( 'portal_get_item_count', array( 'phases' => $phases, 'tasks' => $tasks, 'completed' => $completed, 'started' => $started ) );

}


/**
 * portal_add_field()
 *
 * Custom filter to replace '=' with 'LIKE' in a query so we can query by tasks assigned to a user
 *
 *
 */

function portal_the_login_title( $post_id = NULL ) {

	if( $post_id == NULL ) { global $post; $post_id = $post->ID; }

	echo portal_get_login_title( $post_id );

}

function portal_get_login_title( $post_id = NULL ) {

	if( $post_id == NULL ) { global $post; $post_id = $post->ID; }

	if( ( get_post_type( $post_id) == 'portal_projects' ) && ( is_single() ) ) {

		if( get_field( 'restrict_access_to_specific_users', $post_id ) ) {

			$login_title = __( 'This Project Requires a Login', 'portal_projects' );

		}

		if( post_password_required() ) {

			$login_title = __( 'This Project is Password Protected', 'portal_projects' );

		}

	} elseif( is_post_type_archive( 'portal_projects' ) ) {

		$login_title = __( 'This Area Requires a Login', 'portal_projects' );

	} else {

		$login_title = __( 'This Area Requires a Login', 'portal_projects' );

	}

	return apply_filters( 'portal_login_title', $login_title, $post_id );

}

/**
 * Mimincing the WP Enqueue / Register style until I can build something more sophisticated
 * @param  string $handle Custom handle / ID
 * @param  string $src    URL to the script
 * @param  array  $deps   Dependencies, currently not used
 * @return HTML           Returns markup
 */
function portal_register_style( $handle, $src, $deps = array(), $ver = 1, $media = 'all' ) {

	echo '<link rel="stylesheet" type="text/css" id="' . esc_attr( $handle ) . '-css" href="' . esc_url( $src ) . '?ver=' . $ver .'" media="' . esc_attr( $media ) .'">';

}

function portal_enqueue_style( $handle, $src, $deps = array(), $ver = 1, $media = 'all' ) {

	echo '<link rel="stylesheet" type="text/css" id="' . esc_attr( $handle ) . '-css" href="' . esc_url( $src ) . '?ver=' . $ver .'" media="' . esc_attr( $media ) .'">';

}

/**
 * Mimincing the WP Enqueue / Register style until I can build something more sophisticated
 * @param  string $handle Custom handle / ID
 * @param  string $src    URL to the script
 * @param  array  $deps   Dependencies, currently not used
 * @return HTML           Returns markup
 */
function portal_register_script( $handle, $src, $deps = array(), $ver = 1, $footer = false ) {

	echo '<script id="' . esc_attr( $handle ) . '-js" src="' . esc_url( $src ) . '?ver=' . $ver . '"></script>';

}

function portal_enqueue_script( $handle, $src, $deps = array(), $ver = 1, $footer = false ) {

    echo '<script id="' . esc_attr( $handle ) . '-js" src="' . esc_url( $src ) . '?ver=' . $ver . '"></script>';

}

/**
 * Localizes Script in the same way WP does, but whenever we could need it
 *
 * @since		{{VERSION}}
 * @return		void
 */
function portal_localize_js( $object_name, $l10n ) {

	foreach ( $l10n as $key => $value ) {

		if ( ! is_scalar( $value ) )
			continue;

		$l10n[$key] = html_entity_decode( (string) $value, ENT_QUOTES, 'UTF-8' );

	}

	$script = "var $object_name = " . wp_json_encode( $l10n ) . ';';

	$script = "/* <![CDATA[ */\n" . $script . "\n/* ]]> */";

	?>

	<script type="text/javascript"><?php echo $script; ?></script>

	<?php

}

/*
function portal_documents_acf_upload_prefilter( $errors, $file, $field ) {

	die( 'fired' );

    // only allow admin
    if( !current_user_can('manage_options') ) {

        // this returns value to the wp uploader UI
        // if you remove the ! you can see the returned values
        $errors[] = 'test prefilter';
        $errors[] = print_r($_FILES,true);
        $errors[] = $_FILES['async-upload']['name'] ;

    }
    //this filter changes directory just for item being uploaded
    add_filter('upload_dir', 'my_upload_directory');

    // return
    return $errors;

}
add_filter('acf/upload_prefilter/key=field_52a9e4964b14a', 'portal_documents_acf_upload_prefilter');
*/

function portal_get_post_counts() {

	$cuser 		= wp_get_current_user();
	$meta_query = portal_access_meta_query( $cuser->ID );

	$publish 	= 0;
	$draft		= 0;
	$trash		= 0;
	$complete	 = 0;

	$args = array(
		'post_type'					=>	'portal_projects',
		'posts_per_page'			=>	-1,
		'post_status'				=>	'any',
        'no_found_rows'             =>  true
	);

    $projects = wp_cache_get( 'portal_project_post_counts_' . $cuser->ID );
    if ( false === $projects ) {
	       $projects = get_posts( $args );
           wp_cache_set(  'portal_project_breakdown_' . $cuser->ID, $projects, 3600 );
     }

	foreach( $projects as $project ) {

		if( $project->post_status == 'publish' )
			$publish++;

		if( $project->post_status == 'draft' )
			$draft++;

		if( $project->post_status == 'trash' )
			$trash++;

		if( has_term( 'completed', 'portal_status', $project ) )
			$complete++;

	}

	return array(
		'publish'		=>		$publish,
		'active'		=>		$publish - $complete,
		'draft'			=>		$draft,
		'trash'			=>		$trash,
		'complete'		=>		$complete
	);

}

function portal_convert_time( $timestring ) {

    $timestring = strtotime( $timestring );
    $format     = get_option( 'date_format' );

    return date( $format, $timestring );

}

add_action( 'portal_head', 'portal_register_js_variables' );
function portal_register_js_variables() {

    ob_start(); ?>

    <script>
        <?php do_action( 'portal_js_variables' ); ?>
    </script>

    <?php
    echo ob_get_clean();

}

function portal_build_style( $styles ) {

	$style = '';
	foreach ( $styles as $name => $value ) {
		$style .= "$name: $value;";
	}

	return $style;
}
function portal_setup_all_project_args( $vars ) {

    $args = array();

    if( isset( $_GET[ 's' ] ) ) {
        $vars = array_merge( $vars, array( 's' => $_GET[ 's' ] ) );
    }

    if( ( isset( $_GET[ 'type' ] ) ) && ( $_GET[ 'type' ] != 'all' ) ) {
        $vars = array_merge( $vars, array( 'portal_tax' => $_GET[ 'type' ] ) );
    }

    $sort_by    = portal_get_option( 'portal_dashboard_sorting', 'default' );
    $sort_order = portal_get_option( 'portal_dashboard_sort_order', 'desc' );
    $orderby    = array(
        'order' =>  $sort_order
    );

    if( $sort_by == 'start_date' || $sort_by == 'end_date' ) {
        $orderby['orderby']     = 'meta_value';
        $orderby['meta_key']    = $sort_by;
        $orderby['meta_type']   = 'DATETIME';
    } elseif( $sort_by == 'alphabetical' ) {
        $orderby['orderby'] = 'title';
    }

    $vars = array_merge( $vars, $orderby );

    return apply_filters( 'portal_setup_all_project_args', $vars );

}

function portal_get_slug() {

    $slug = portal_get_option( 'portal_slug', 'yoop' );

    // If it's never been set...
    $slug = ( empty( $slug ) ? 'yoop' : $slug );

    return $slug;

}

function portal_in_array_r($needle, $haystack, $strict = false) {
    foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && portal_in_array_r($needle, $item, $strict))) {
            return true;
        }
    }

    return false;
}

function portal_just_keys( $array ) {

    $keys = array();

    foreach( $array as $key => $value )
        $keys[] = $key;

    return $keys;

}

function portal_die( $message = NULL ) {

    $message = ( $message == NULL ? __( 'Something went wrong, please try again.', 'portal_projects' ) : $message ); ?>

    <div class="portal-error-notice">
        <?php echo wpautop( $message ); ?>
    </div>

    <?php
}

add_action('wp_login', 'portal_set_last_login');
function portal_set_last_login( $login ) {

   $user = get_user_by( 'login', $login );

   //add or update the last login value for logged in user
   update_user_meta( $user->ID, 'portal_last_login', current_time('mysql') );

}

//function for getting the last login
function portal_get_last_login( $user_id ) {

   $last_login      = get_user_meta( $user_id, 'portal_last_login', true );
   $date_format     = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
   $the_last_login  = mysql2date ($date_format, $last_login, false );

   return $the_last_login;

}

function portal_verbose_login( $user_id ) {

    $last_login = portal_get_last_login( $user_id );
    $message    = '';


    if( $last_login ) {
        $message = __( 'Last logged in at', 'portal_projects' ) . ' ' . $last_login;
    } else {
        $message = __( 'Hasn\'t logged in recently...', 'portal_projects' );
    }

    return apply_filters( 'portal_verbose_login_message', $message, $user_id );

}

function portal_get_supported_post_types() {

    return apply_filters( 'portal_supported_post_types', array(
        'portal_projects',
        'portal_teams'
    ) );

}

function portal_get_the_title() {

    $title      = '';
    $cuser      = wp_get_current_user();

    if( is_post_type_archive( 'portal_projects' ) ) {
        $title .= __( 'Dashboard', 'portal_projects' );
    }

    if( is_post_type_archive( 'portal_teams' ) ) {
        $title .= __( 'Teams:', 'portal_projects' );
    }

    if( is_single() && get_post_type() == 'portal_projects' ) {
        $title .= __( 'Project:', 'portal_projects' ) . ' ' . get_the_title();
        if( get_field( 'client' ) ) $title .= ' - ' . get_field( 'client' );
    }

    if( is_single() && get_post_type() == 'portal_teams' ) {
        $title .= __( 'Team:', 'portal_projects' ) . ' ' . get_the_title();
    }

    if( is_user_logged_in() ) {
        $title .= ' | ' . portal_get_nice_username_by_id( $cuser->ID );
    }

    return apply_filters( 'portal_get_the_title', $title );

}

function portal_the_title() {
    echo esc_attr( portal_get_the_title() );
}

function portal_the_archive_title() {
    echo esc_html( portal_get_archive_title() );
}

function portal_get_archive_title( $post_id = NULL ) {

    $post_id = ( $post_id == NULL ? get_the_ID() : $post_id );

    $archives = apply_filters( 'portal_archive_titles', array(
        'portal_projects'  =>  __( 'Dashboard', 'portal_projects' ),
        'portal_teams'     =>  __( 'Teams',     'portal_projects' ),
    ) );

    $query_vars = apply_filters( 'portal_archive_query_vars', array(
        'portal_calendar_page'  =>  __( 'Calendar', 'portal_projects' ),
        'portal_tasks_page'     =>  __( 'Tasks', 'portal_projects' )
    ) );

    foreach( $query_vars as $var => $title ) {
        if( get_query_var( $var ) ) return $title;
    }

    foreach( $archives as $post_type => $title ) {
        if( get_post_type() == $post_type ) return $title;
    }

    return __( 'Dashboard', 'portal_projects' );

}


function portal_user_has_role( $role, $user_id = NULL ) {
    $user_id = ( $user_id == NULL ? get_current_user_id() : $user_id );
    return in_array( $role, portal_get_user_roles_by_user_id( $user_id ) );
}

function portal_get_user_roles_by_user_id( $user_id ) {
    $user = get_userdata( $user_id );
    return empty( $user ) ? array() : $user->roles;
}

function portal_strip_http( $link ) {

    $link = str_replace( 'http://', '', $link );
    $link = str_replace( 'https://', '', $link );

    return $link;

}

function is_portal_search() {

    if( is_search() && get_post_type() == 'portal_projects' ) {
        return true;
    }

    if( isset($_GET['s']) && ( isset($_GET['post_type']) && $_GET['post_type'] == 'portal_projects' ) ) {
        return true;
    }

    return false;

}

function portal_organize_milestones( $milestones = null, $post_id = null ) {

    $post_id    = ( $post_id == NULL ? get_the_ID() : $post_id );
    $milestones = ( $milestones == NULL ? get_field( 'milestones', $post_id ) : $milestones );
    $completed  = portal_compute_progress($post_id);

    $data = array(
        'milestones'    =>  array(),
        'completed'     =>  0
    );

    if( empty($milestones) ) {
        $data['milestones'] = false;
        return $data;
    }

    foreach( $milestones as $key => $value ) {

        // Group milestones by occuring
        $data['milestones'][$value['occurs']][] = $value;

        if( $value['occurs'] <= $completed ) $data['completed']++;

    }

    ksort($data['milestones']);

    return $data;


}

function portal_split_milestones( $milestones = null, $post_id = null ) {

    $post_id    = ( $post_id == NULL ? get_the_ID() : $post_id );
    $milestones = ( $milestones == NULL ? get_field( 'milestones', $post_id ) : $milestones );
    $completed  = portal_compute_progress($post_id);

    $data = array(
        'even'      => array(),
        'odd'       => array(),
        'completed' => 0
    );

    if( !empty( $milestones ) ) {

        foreach( $milestones as $k => $v ) {

            if( $k % 2 == 0 ) {
                $data['even'][] = $v;
            } else {
                $data['odd'][] = $v;
            }

            if( $v['occurs'] <= $completed ) $data['completed']++;

        }
    }

    return $data;

}

function portal_get_phase_summary( $phases ) {

    $summary = array(
        'completed' =>  0,
        'total'     =>  count($phases),
    );

    if( empty($phases) ) return $summary;

    $i = 0;

    foreach( $phases as $phase ) {
        $phase_summary = portal_get_phase_completed($i);
        if( $phase_summary['completed'] == 100 ) $summary['completed']++;
        $i++;
    }

    return $summary;

}

function portal_milestone_marker_classes( $milestones, $completed ) {

    $classes = array(
        'portal-milestone-dot',
        'portal-milestone-' . $milestones[0]['occurs']
    );

    if( $milestones[0]['occurs'] <= $completed ) {
        $classes[] = 'completed';
    } else {
        foreach( $milestones as $milestone ) {
            if( !$milestone['date'] || empty($milestone['date']) ) continue;
            if( strtotime($milestone['date']) < strtotime('today') ) $classes[] = 'has_late';
        }
    }

    // Add filter for modularity
    $classes        = apply_filters( 'portal_milestone_marker_classes', $classes );
    $combined_class = '';

    foreach( $classes as $class ) $combined_class .= ' ' . $class;

    return $combined_class;

}


function portal_get_project_summary( $post_id = NULL ) {

    $post_id = ( $post_id == NULL ? get_the_ID() : $post_id );

    $progress   = portal_compute_progress($post_id);
    $timing     = portal_calculate_timing($post_id);
    $docs       = portal_count_documents($post_id);
    $milestones = portal_count_milestones($post_id);
    $phases     = portal_count_phases($post_id);
    $tasks      = portal_count_tasks($post_id);

    return apply_filters( 'portal_get_project_summary', array(
        'progress'      =>  array(
            'total'     =>  $progress,
            'remaining' =>  ( 100 - $progress ),
        ),
        'time'          =>  array(
            'total'     =>  $timing['percentage_complete'],
            'remaining' =>  ( 100 - $timing['percentage_complete'] ),
        ),
        'milestones'    =>  array(
            'total'     =>  $milestones['total'],
            'complete'  =>  $milestones['complete']
        ),
        'phases'        =>  array(
            'total'     =>  $phases['total'],
            'complete'  =>  $phases['complete']
        ),
        'tasks'         =>  array(
            'total'     =>  $tasks['total'],
            'complete'  =>  $tasks['complete']
        ),
        'documents'     =>  array(
            'total'     =>  $docs['total'],
            'approved'  =>  $docs['approved']
        ),
        'comments'      =>  array(
            'total'     =>  get_comments_number($post_id),
        )
    ), $post_id );

}

function portal_count_documents( $post_id, $type = null ) {

    $post_id = ( $post_id == NULL ? get_the_ID() : $post_id );

    if( $type == null ) {
        $type = array( 'project' => $post_id );
    }

    $count  = array(
        'total'     =>  0,
        'approved'  =>  0,
    );

    if( isset($type['project']) || isset($type['task']) ) {

        while( have_rows( 'documents', $post_id ) ) { the_row();


            if( isset($type['task']) && $type['task'] != get_sub_field('document_task') ) {
                continue;
            }

            $count['total']++;

            if( get_sub_field('status') == 'Approved' || get_sub_field('status') == 'none' ) {
                $count['approved']++;
            }

        }

    }

    if( isset($type['phase']) || isset($type['phase_tasks']) ) {

        $phases = get_field( 'phases', $post_id );
        $docs   = get_field( 'documents', $post_id );

        $phase_id = ( isset($type['phase']) ? $type['phase'] : $type['phase_tasks'] );

        if( !$phases || empty($phases) || !$docs || empty($docs) ) {
            return $count;
        }

        foreach( $phases as $phase ) {

            if( $phase['phase_id'] != $phase_id ) {
                continue;
            }

            $phase_keys = array();

            if( isset($phase['tasks']) && !empty($phase['tasks']) ) {

                foreach( $phase['tasks'] as $task ) {

                    if( !isset($task['task_id']) ) {
                        continue;
                    }

                    $phase_keys[] = $task['task_id'];

                }

            }

            foreach( $docs as $doc ) {


                if( $doc['document_phase'] != $phase_id && isset($type['phase']) ) {
                    continue;
                }

                if( !in_array( $doc['document_task'], $phase_keys ) && isset($type['phase_tasks']) ) {
                    continue;
                }

                $count['total']++;
                if( $doc['status'] == 'Approved' || $doc['status'] == 'none' ) {
                    $count['approved']++;
                }

            }

        } //endforeach

    } //endif.is_phase


    return apply_filters( 'portal_count_documents', $count, $post_id );

}

function portal_count_milestones( $post_id ) {

    $post_id    = ( $post_id == NULL ? get_the_ID() : $post_id );
    $progress   = portal_compute_progress($post_id);
    $timing     = portal_calculate_timing($post_id);

    $count  = array(
        'total'     =>  0,
        'complete'  =>  0,
        'late'      =>  0,
    );

    while( have_rows( 'milestones', $post_id ) ) { the_row();
        $count['total']++;
        if( get_sub_field('occurs') <= $progress ) $count['complete']++;
        if( portal_late_class( get_sub_field('date') ) == 'late' ) $count['late']++;
    }

    return apply_filters( 'portal_count_milestones', $count, $post_id );

}

function portal_count_phases( $post_id ) {

    $post_id    = ( $post_id == NULL ? get_the_ID() : $post_id );

    $count  = array(
        'total'     =>  0,
        'complete'  =>  0,
    );

    $phases = get_field( 'phases', $post_id );
    if( !empty($phases) ) {
        foreach( $phases as $phase ) {
            $phase_summary = portal_get_phase_completed($count['total']);
            $count['total']++;
            if( $phase_summary['completed'] == 100 ) $count['complete']++;
        }
    }

    return apply_filters( 'portal_count_phases', $count, $post_id );

}

function portal_count_tasks( $post_id ) {

    $post_id    = ( $post_id == NULL ? get_the_ID() : $post_id );

    $count  = array(
        'total'     =>  0,
        'complete'  =>  0,
        'late'      =>  0,
    );

    $phases = get_field( 'phases', $post_id );
    if( !empty($phases ) ) {
        foreach( $phases as $phase ) {
            foreach( $phase['tasks'] as $task ) {

                $count['total']++;

                if( $task['status'] == 100 ) $count['complete']++;

                if( isset($task['due_date']) && !empty( $task['due_date'] ) && strtotime($task['due_date']) < strtotime( 'today' ) ) $count['late']++;

            }
        }
    }

    return apply_filters( 'portal_count_tasks', $count, $post_id );

}

function portal_parse_data_atts( $atts ) {

    $output = '';

    foreach( $atts as $label => $value ) {
        $output .= 'data-' . $label . '="'.$value.'"';
    }

    echo $output;

}

function portal_remove_wp_seo_meta_box() {
    remove_meta_box( 'wpseo_meta', 'portal_projects', 'normal' );
}
add_action( 'add_meta_boxes', 'portal_remove_wp_seo_meta_box', 100 );

function portal_url_origin( $s, $use_forwarded_host = false )
{
    $ssl      = ( ! empty( $s['HTTPS'] ) && $s['HTTPS'] == 'on' );
    $sp       = strtolower( $s['SERVER_PROTOCOL'] );
    $protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
    $port     = $s['SERVER_PORT'];
    $port     = ( ( ! $ssl && $port=='80' ) || ( $ssl && $port=='443' ) ) ? '' : ':'.$port;
    $host     = ( $use_forwarded_host && isset( $s['HTTP_X_FORWARDED_HOST'] ) ) ? $s['HTTP_X_FORWARDED_HOST'] : ( isset( $s['HTTP_HOST'] ) ? $s['HTTP_HOST'] : null );
    $host     = isset( $host ) ? $host : $s['SERVER_NAME'] . $port;
    return $protocol . '://' . $host;
}

function portal_full_url( $s, $use_forwarded_host = false )
{
    return portal_url_origin( $s, $use_forwarded_host ) . $s['REQUEST_URI'];
}

function portal_get_phase_by_task( $task_id = null, $post_id = null ) {

    if( !$task_id ) {
        return false;
    }

    $post_id = ( isset($post_id) ? $post_id : get_the_ID() );

    $phases = get_field( 'phases', $post_id );

    if( !$phases || empty($phases) ) {
        return false;
    }

    $i = 0;

    foreach( $phases as $phase ) {

        $phase['index'] = $i;

        if( !isset($phase['tasks']) || empty($phase['tasks']) ) {
            continue;
        }

        foreach( $phase['tasks'] as $task ) {
            if( $task['task_id'] == $task_id ) {
                return $phase;
            }
        }

        $i++;

    }

    return false;

}
