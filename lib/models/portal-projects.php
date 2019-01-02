<?php
/**
 * portal-projects.php
 *
 * Register custom projects post type and any admin management around the CPT
 *
 * @category controller
 * @package portal-projects
 * @author Ross Johnson
 * @version 1.1
 * @since 1.3.6
 */

add_action( 'init', 'portal_create_portal_projects' );
function portal_create_portal_projects() {

    $portal_slug = portal_get_option( 'portal_slug' , 'yoop' );

    $labels = apply_filters( 'portal_project_post_type_labels', array(
        'name'                => _x( 'Projects', 'Post Type General Name', 'portal_projects' ),
        'singular_name'       => _x( 'Project', 'Post Type Singular Name', 'portal_projects' ),
        'menu_name'           => __( 'Projects', 'portal_projects' ),
        'parent_item_colon'   => __( 'Parent Project:', 'portal_projects' ),
        'all_items'           => __( 'All Projects', 'portal_projects' ),
        'view_item'           => __( 'View Project', 'portal_projects' ),
        'add_new_item'        => __( 'Add New Project', 'portal_projects' ),
        'add_new'             => __( 'New Project', 'portal_projects' ),
        'edit_item'           => __( 'Edit Project', 'portal_projects' ),
        'update_item'         => __( 'Update Project', 'portal_projects' ),
        'search_items'        => __( 'Search Projects', 'portal_projects' ),
        'not_found'           => __( 'No projects found', 'portal_projects' ),
        'not_found_in_trash'  => __( 'No projects found in Trash', 'portal_projects' ),
    ) );

    $rewrite = apply_filters( 'portal_project_post_type_rewrites', array(
        'slug'                => $portal_slug,
        'with_front'          => true,
        'pages'               => true,
        'feeds'               => true,
    ) );

    $args = apply_filters( 'portal_project_post_type_args', array(
        'label'               => __( 'portal_projects', 'portal_projects' ),
        'description'         => __( 'Projects', 'portal_projects' ),
        'labels'              => $labels,
        'supports'            => array( 'title', 'comments', 'revisions', 'author' ),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 20,
        'menu_icon'           => '',
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'rewrite'             => $rewrite,
        'capability_type'     => array( 'portal_project', 'portal_projects' ),
//            'capability_type'     => array('portal_project','portal_projects'),
        'map_meta_cap'        => true,
    ) );

    register_post_type( 'portal_projects', $args );

    do_action( 'portal_loaded_post_type_project' );

}

add_filter( 'manage_portal_projects_posts_columns', 'portal_project_header' );
function portal_project_header( $defaults ) {

    $new = array();

    foreach( $defaults as $key => $title ) {

        if( $key == 'title' ) {

		    $new[$key] 			= $title;
            $new['client'] 		= __( 'Client', 'portal_projects' );
            $new['assigned'] 	= __( 'Users', 'portal_projects' );
            $new['td-progress'] = __( '% Complete', 'portal_projects' );
            $new['timing'] 		= __( 'Time Elapsed', 'portal_projects' );

		} else {
			$new[ $key ] = $title;
		}

    }

    return $new;

}

add_action( 'manage_portal_projects_posts_custom_column', 'portal_project_header_content', 10, 2);
function portal_project_header_content( $column_name, $post_ID ) {

    if( $column_name == 'client' ) {

	    echo get_field( 'client' );

	} elseif ( $column_name == 'td-progress' ) {

        $completed = portal_compute_progress( $post_ID );

		if($completed > 10) {
            echo '<p class="portal-progress"><span class="portal-' . esc_attr($completed) . '"><strong>%' . esc_html($completed) . '</strong></span></p>';
		} else {
		    echo '<p class="portal-progress"><span class="portal-' . esc_attr($completed) . '"></span></p>';
		}

	} elseif ( $column_name == 'assigned' ) {

        $users = portal_get_project_users( $post_ID );

        echo portal_get_user_icons( $users );

    } elseif ( $column_name == 'timing' ) {

       portal_the_timing_bar( $post_ID );

    }
}

function portal_user_name( $user ) {

    if ( empty($user) ) return;

    if( !empty( $user['user_firstname'] ) && !empty( $user['user_lastname'] ) ) {
        $name = $user['user_firstname']. ' '. $user['user_lastname'];
    } else {
        $name = $user[ 'user_nicename' ];
    }

    return '<p class="portal_user_assigned"><a href="edit.php?post_type=portal_projects&page=portal_user_list&user=' . $user[ 'ID' ] . '">' . $user[ 'user_avatar' ] . ' <span>' . $name . '</span></a></p>';

}

add_action( 'save_post', 'portal_project_save_routines', 100, 2);
function portal_project_save_routines( $post_id, $post ) {

	// Bail if this is an autosave
    if( wp_is_post_revision($post_id) || wp_is_post_autosave($post_id) ) return;

	// Bail if this is not a yoop project
	if( get_post_type() != 'portal_projects' ) return;

	do_action( 'portal_save_post', $post_id, $post );

    $author_id = get_post_field( 'post_author', $post_id );

    update_post_meta( $post_id, '_portal_post_author', $author_id );

}

add_action( 'portal_save_post', 'portal_process_project_progress', 10, 2 );
function portal_process_project_progress( $post_id, $post ) {

	$current_progress 	= get_post_meta( $post_id, '_portal_current_progress', true );
	$new_progress		= portal_compute_progress( $post_id );

    if( $new_progress != $current_progress ) {
        // Progress is different so we fire an action for progress change
        do_action( 'portal_project_progress_change', $post_id, $new_progress );
    }

	if( $new_progress > $current_progress ) {
		// Progress has moved forward so we fire an acction for the current progress
		do_action( 'portal_project_completion', $post_id, $new_progress );
	}

	update_post_meta( $post_id, '_portal_current_progress', $new_progress );

}

add_action( 'portal_project_completion', 'portal_fire_project_completed_hook', 10, 2 );
function portal_fire_project_completed_hook( $post_id, $new_progress ) {

    if( $new_progress == '100' ) {

        do_action( 'portal_notify', 'project_complete', array(
            'project_title' => get_the_title( $post_id ),
            'post_id'       => $post_id,
        ) );

    }

}

add_action( 'portal_save_post', 'portal_mark_as_complete', 10 , 2 );
function portal_mark_as_complete( $post_id, $post ) {

	$project_completion = portal_compute_progress( $post->ID) ;
	$current_status 	= get_post_meta( $post->ID,'_portal_completed', true );

	if( $current_status == '' ) { update_post_meta( $post->ID, '_portal_completed' , '0' ); }

	if( $project_completion == '100') {

		update_post_meta( $post->ID, '_portal_completed', '1' );
		wp_set_post_terms( $post->ID, 'completed', 'portal_status' );

	} else {

		update_post_meta( $post->ID, '_portal_completed', '0' );
		wp_set_post_terms( $post->ID, 'incomplete', 'portal_status' );

	}

}

add_action( 'portal_save_post', 'portal_reorder_milestones', 10, 2 );
function portal_reorder_milestones( $post_id, $post ) {

	$milestones = get_field( 'milestones', $post_id );

	// Bail if there are no milestones
	if( empty( $milestones ) ) return;

	$order = array();

	foreach( $milestones as $i => $row ) {
		$order[ $i ] = $row[ 'occurs' ];
	}

	array_multisort( $order, SORT_ASC, $milestones );

	update_field( 'milestones', $milestones, $post_id );

}

add_action( 'portal_save_post', 'portal_assign_users_to_project', 10, 2);
function portal_assign_users_to_project( $post_id, $post ) {

    // Bail if this is an autosave
    if( wp_is_post_revision($post_id) || wp_is_post_autosave($post_id) ) return;

	// Bail if this is not a yoop project
	if( get_post_type() != 'portal_projects' ) return;

    $current_users      = portal_get_project_users( $post_id );
    $existing_users     = (array) get_post_meta( $post_id, '_portal_assigned_users', true );
    $current_user_ids   = array();

    foreach( $current_users as $user ) {
        $current_user_ids[] = $user['ID'];
    }

    if( empty( $existing_users ) ) $existing_users = array();

    $new_users = array_diff( $current_user_ids, $existing_users );

    if( !empty( $new_users ) ) {
        do_action( 'portal_users_added_to_project', $post_id, $new_users );
    }

    update_post_meta( $post_id, '_portal_assigned_users', $current_user_ids );

}

add_filter( 'views_edit-portal_projects', 'portal_update_project_quicklinks' , 1 );
function portal_update_project_quicklinks( $views ) {

	$post_counts		= portal_get_post_counts();

	// Reset defaults
	$completed_class 	= '';
	$publish_class 		= '';
	$draft_class 		= '';
	$trash_class 		= '';
	$active_class 		= '';

	if( isset( $_GET[ 'post_status' ] ) ) {

		$post_status = $_GET['post_status'];

		switch($post_status) {

			case 'completed':
				$completed_class 	= 'current';
				break;
			case 'publish':
				$publish_class 		= 'current';
				break;
			case 'draft':
				$draft_class 		= 'current';
				break;
			case 'trash':
				$trash_class 		= 'current';
				break;
			default:
				$active_class 		= 'current';
				break;
		}

	} else {

		$active_class 				= 'current';

	}

	$views['all'] = '<a class="' . $active_class . '" href="edit.php?post_type=portal_projects">Active <span class="count">(' . $post_counts[ 'active' ] . ')</span></a>';

	if( $post_counts[ 'publish' ] > 0) {
		$views['publish'] = '<a class="' . $publish_class . '" href="edit.php?post_status=publish&post_type=portal_projects">Published <span class="count">(' . $post_counts[ 'publish' ] . ')</span></a>';
	}

	if( $post_counts[ 'draft' ] > 0) {
		$views['draft'] = '<a class="' . $draft_class . '" href="edit.php?post_status=draft&post_type=portal_projects">Draft <span class="count">(' . $post_counts[ 'draft' ] . ')</span></a>';
	}

	if( $post_counts[ 'trash' ] > 0) {
		$views['trash'] = '<a class="' . $trash_class . '" href="edit.php?post_status=trash&post_type=portal_projects">Trash <span class="count">(' . $post_counts[ 'trash' ] . ')</span></a>';
	}

	array_splice($views, 1, 0, "<a class='" . $completed_class . "' href='edit.php?post_type=portal_projects&post_status=completed'>".__('Completed','portal_projects')." <span class='count'>  (" . $post_counts[ 'complete' ] . ")</span></a>");

	return $views;

}

add_action( 'restrict_manage_posts', 'portal_add_types_post_filter_to_admin' );
function portal_add_types_post_filter_to_admin(){

    //execute only on the 'post' content type
    global $post_type;
    if( $post_type == 'portal_projects' ){

        //get a listing of all users that are 'author' or above
        $tax_args = array(
            'show_option_all'   => __( 'All Types', 'portal_projects' ),
            'orderby'           => 'name',
            'order'             => 'ASC',
            'name'              => 'portal_project_types',
            'taxonomy'          => 'portal_tax',
            'include_selected'  => true
        );

        //determine if we have selected a user to be filtered by already
        if( isset( $_GET[ 'portal_project_types' ] ) ) {
            //set the selected value to the value of the author
            $tax_args[ 'selected' ] = (int)sanitize_text_field( $_GET[ 'portal_project_types' ] );
        }

        //display the users as a drop down
        wp_dropdown_categories( $tax_args );
    }

}

add_filter( 'parse_query', 'portal_do_types_post_filter_in_admin' );
function portal_do_types_post_filter_in_admin( $query ) {

    global $post_type, $pagenow;

    //if we are currently on the edit screen of the post type listings
    if( $pagenow == 'edit.php' && $post_type == 'portal_projects' ){

        if( isset( $_GET[ 'portal_project_types' ] ) ) {

            //get the desired post format
            $post_format = sanitize_text_field( $_GET[ 'portal_project_types' ] );
            //if the post format is not 0 (which means all)
            if( $post_format != 0 ) {

                $query->query_vars[ 'tax_query' ] = array(
                    array(
                        'taxonomy'  => 'portal_tax',
                        'field'     => 'ID',
                        'terms'     => array( $post_format )
                    )
                );


            }
        }
    }

}

function portal_get_user_icons( $users = null, $limit = 5 ) {

    if( empty( $users )  ) return false;

    ob_start();
    $i = 0;

    foreach( $users as $user ) {

        if( $i < $limit ) {
            echo portal_user_name( $user );
        }

        $i++;

    }

    if( count($users) > $limit ) {
        echo '<div class="portal_user_assigned overage"><strong>+' . ( count( $users ) - $limit ) . '</strong></div>';
    }

    return ob_get_clean();

}

function portal_custom_project_post_statuses() {

    $statuses = array(
        array(
            'slug'  =>  'portal-completed',
            'label' =>  __( 'Completed', 'portal_projects' ),
        ),
        array(
            'slug'  =>  'portal-hold',
            'label' =>  __( 'On Hold', 'portal_projects' ),
        ),
        array(
            'slug'  =>  'portal-canceled',
            'label' =>  __( 'Canceled', 'portal_projects' ),
        ),
    );

    $args = array(
        'public'                    =>  true,
        'exclude_from_search'       =>  false,
        'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
    );

    foreach( $statuses as $status ) {

        $settings = array_merge( $status['label'], $args );

        register_post_status( $status['slug'], $settings );

    }

}

/*
function portal_get_project_breakdown( $post_id = NULL ) {

    // Get the global post ID if it wasn't passed
    $post_id = $post_id == NULL ? get_the_ID() : $post_id;

    // Get the phases so we can count them later
    $phases = get_field( 'phases', $post_id );

    // The project array, pre-populated as best we can
    $project = array(
        'post_id'   =>  $post_id,
        'title'     =>  get_the_title($post_id),
        'client'    =>  get_field( 'client', $post_id ),
        'progress'  =>  portal_compute_progress($post_id),
        'timing'    =>  array(
            'elappsed'  =>  portal_calculate_timing($post_id),
            'start_date'    =>  portal_get_the_start_date($post_id),
            'end_date'      =>  portal_get_the_end_date($post_id),
        ),
        'milestones'    =>  array(
            'total'     =>  count( get_field( 'milestones', $post_id ) ),
            'complete'  =>  0,
            'overdue'   =>  0
        ),
        'phases'  =>  array(
            'total'     =>  count( $phases ),
            'complete'  =>  0,
        ),
        'tasks' =>  array(
            'total'     =>  0,
            'assigned'  =>  0,
            'complete'  =>  0,
            'overdue'   =>  0
        ),
        'discussions'   =>  array(
            'total'     =>  get_comment_count( $post_id ),
        )
    );

    foreach( $phases as $phase ) {



    }






}
*/
