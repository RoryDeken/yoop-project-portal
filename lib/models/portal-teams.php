<?php
/**
 * portal-teams.php
 *
 * Register custom teams post type and any admin management around the CPT
 *
 * @category controller
 * @package portal-projects
 * @author Ross Johnson
 * @version 1.0
 * @since 1.3.6
 */

add_action( 'init', 'portal_create_portal_teams' );
function portal_create_portal_teams() {

    $portal_slug = portal_get_option( 'portal_slug' , 'yoop' );

    $labels = array(
        'name'                => _x( 'Teams', 'Post Type General Name', 'portal_projects' ),
        'singular_name'       => _x( 'Team', 'Post Type Singular Name', 'portal_projects' ),
        'menu_name'           => __( 'Teams', 'portal_projects' ),
        'parent_item_colon'   => __( 'Parent Team:', 'portal_projects' ),
        'all_items'           => __( 'All Teams', 'portal_projects' ),
        'view_item'           => __( 'View Team', 'portal_projects' ),
        'add_new_item'        => __( 'Add New Team', 'portal_projects' ),
        'add_new'             => __( 'New Team', 'portal_projects' ),
        'edit_item'           => __( 'Edit Team', 'portal_projects' ),
        'update_item'         => __( 'Update Team', 'portal_projects' ),
        'search_items'        => __( 'Search Teams', 'portal_projects' ),
        'not_found'           => __( 'No teams found', 'portal_projects' ),
        'not_found_in_trash'  => __( 'No teams found in Trash', 'portal_projects' ),
    );

    $rewrite = array(
        'slug'                => $portal_slug . '-teams',
        'with_front'          => true,
        'pages'               => true,
        'feeds'               => true,
    );

    $args = array(
        'label'               => __( 'portal_teams', 'portal_projects' ),
        'description'         => __( 'Teams', 'portal_projects' ),
        'labels'              => $labels,
        'supports'            => array( 'title', 'revisions', 'thumbnail'),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => false,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 20,
        'menu_icon'           => '',
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'rewrite'             => $rewrite,
        'capability_type'     => array( 'portal_team', 'portal_teams' ),
        'map_meta_cap'        => true,
    );

    register_post_type( 'portal_teams' , $args);

}

add_action( 'admin_menu' , 'portal_add_teams_submenu_page' );
function portal_add_teams_submenu_page() {

	add_submenu_page( 'edit.php?post_type=portal_projects', 'Teams', 'Teams', 'publish_portal_teams', 'edit.php?post_type=portal_teams', NULL );
    add_submenu_page( 'post-new.php?post_type=portal_teams', 'Teams', 'Teams', 'publish_portal_teams', 'post-new.php?post_type=portal_teams', NULL );

}

add_filter( 'manage_portal_teams_posts_columns', 'portal_teams_admin_header', 999, 1 );
function portal_teams_admin_header( $defaults ) {

    $new = array();

    foreach( $defaults as $key => $title ) {

        if( $key == 'title' ) {

		    $new[$key] 			= __( 'Team', 'portal_projects' );
            $new['users'] 		= __( 'Users','portal_projects');
            $new['active']      = __( 'Active Projects', 'portal_projects' );

		} else {

			// Clear out other irrelevant headers
			continue;

		}
    }

    return $new;

}

add_action('manage_portal_teams_posts_custom_column', 'portal_teams_header_content', 10, 2);
function portal_teams_header_content( $column_name, $post_id ) {

	if( $column_name == 'users' ) {

		$team_members   = portal_get_team_members( $post_id );

        if( !$team_members ) return;

        $limit          = apply_filters( 'portal_teams_header_content_user_display_limit', 10 );
        $i              = 1;
        $total          = count( $team_members );

		foreach( $team_members as $member ) {

            if( $i < $limit ) { ?>

				<div class="portal_user_assigned">
					<?php
                    $link = ( current_user_can('list_users') ? array('<a href="' . get_edit_user_link( $member['ID'] ) . '">', '</a>' ) : array('','') );
                    echo $link[0] . $member[ 'user_avatar' ] . $link[1]; ?>
					<span><?php echo portal_username_by_id( $member[ 'ID' ] ); ?></span>
				</div>

	        <?php
            }
            $i++;
        }

        if( $total > $limit ) echo '<div class="portal_user_assigned overage"><strong>+' . ( $total - $limit ) . '</strong></div>';

	}

    if( $column_name == 'active' ) {
        echo count( portal_get_team_projects( $post_id, 'completed' ) );
    }

}

add_action('do_meta_boxes', 'portal_relabel_team_featured_image_box');
function portal_relabel_team_featured_image_box()
{
    remove_meta_box( 'postimagediv', 'portal_teams', 'side' );
    add_meta_box('postimagediv', __('Team Thumbnail', 'portal_projects'), 'post_thumbnail_meta_box', 'portal_teams', 'side' );
}
