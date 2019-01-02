<?php
/**
 * portal-project-types.php
 *
 * Register custom taxonomies for Project yoop and any specific management of this taxonomy.
 *
 * @category controller
 * @package portal-projects
 * @author Ross Johnson
 * @version 1.0
 * @since 1.3.6
 */

function portal_project_taxonomy() {

    $portal_slug = portal_get_option( 'portal_slug' , 'yoop' );

    $labels = array(
        'name'                       => _x( 'Project Types', 'Taxonomy General Name', 'portal_projects' ),
        'singular_name'              => _x( 'Project Type', 'Taxonomy Singular Name', 'portal_projects' ),
        'menu_name'                  => __( 'Project Types', 'portal_projects' ),
        'all_items'                  => __( 'All Project Types', 'portal_projects' ),
        'parent_item'                => __( 'Parent Project Type', 'portal_projects' ),
        'parent_item_colon'          => __( 'Parent Project Type:', 'portal_projects' ),
        'new_item_name'              => __( 'New Project Type', 'portal_projects' ),
        'add_new_item'               => __( 'Add Project Type', 'portal_projects' ),
        'edit_item'                  => __( 'Edit Project Type', 'portal_projects' ),
        'update_item'                => __( 'Update Project Type', 'portal_projects' ),
        'separate_items_with_commas' => __( 'Separate items with commas', 'portal_projects' ),
        'search_items'               => __( 'Search Project Types', 'portal_projects' ),
        'add_or_remove_items'        => __( 'Add or remove project types', 'portal_projects' ),
        'choose_from_most_used'      => __( 'Choose from the most used items', 'portal_projects' ),
        'not_found'                  => __( 'Not Found', 'portal_projects' ),
    );
    $rewrite = array(
        'slug'                       => $portal_slug . '-projects',
        'with_front'                 => true,
        'hierarchical'               => true,
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'rewrite'                    => $rewrite
    );
    register_taxonomy( 'portal_tax', array( 'portal_projects' ), $args );

    $labels = array(
                'name'                       => _x( 'Project Status', 'Taxonomy General Name', 'portal_projects' ),
                'singular_name'              => _x( 'Project Status', 'Taxonomy Singular Name', 'portal_projects' ),
                'menu_name'                  => __( 'Project Status', 'portal_projects' ),
                'all_items'                  => __( 'All Project Statuses', 'portal_projects' ),
                'parent_item'                => __( 'Parent Project Status', 'portal_projects' ),
                'parent_item_colon'          => __( 'Parent Project Status:', 'portal_projects' ),
                'new_item_name'              => __( 'New Project Status', 'portal_projects' ),
                'add_new_item'               => __( 'Add Project Status', 'portal_projects' ),
                'edit_item'                  => __( 'Edit Project Status', 'portal_projects' ),
                'update_item'                => __( 'Update Project Status', 'portal_projects' ),
                'separate_items_with_commas' => __( 'Separate items with commas', 'portal_projects' ),
                'search_items'               => __( 'Search Project Statuses', 'portal_projects' ),
                'add_or_remove_items'        => __( 'Add or remove project statues', 'portal_projects' ),
                'choose_from_most_used'      => __( 'Choose from the most used items', 'portal_projects' ),
                'not_found'                  => __( 'Not Found', 'portal_projects' ),
            );
            $rewrite = array(
                'slug'                       => 'yoop-status',
                'with_front'                 => true,
                'hierarchical'               => false,
            );
            $args = array(
                'labels'                     => $labels,
                'hierarchical'               => false,
                'public'                     => true,
                'show_ui'                    => false,
                'show_admin_column'          => false,
                'show_in_nav_menus'          => false,
                'show_tagcloud'              => false,
                'rewrite'                    => $rewrite
            );
            register_taxonomy( 'portal_status', array( 'portal_projects' ), $args );

}

// Hook into the 'init' action
add_action( 'init', 'portal_project_taxonomy', 0 );
