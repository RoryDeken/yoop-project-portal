<?php
add_filter( 'portal_settings_sections_addons', 'portal_auto_settings_section' );
add_filter( 'portal_settings_addons', 'portal_auto_settings' );

function portal_auto_settings_section( $sections ) {

    $sections['portal_auto_project_settings'] = __( 'Portal Project Templates By User', 'portal-auto' );

    return $sections;

}

function portal_auto_settings( $settings ) {

    $portal_auto_settings['portal_auto_project_settings'] = array(
        'portal_auto_title'    =>  array(
            'id'    =>  'portal_auto_title',
            'name'  =>  '<h2>' . __( 'Portal Project Templates By User', 'portal-auto' ) . '</h2>',
            'type'  =>  'html'
        ),
    );

    global $wp_roles;

    if ( ! isset( $wp_roles ) ) {
        $wp_roles = new WP_Roles();
    }

    $args = array(
        'post_type'         =>  'portal_projects',
        'posts_per_page'    =>  -1,
        'meta_key'          =>  '_portal_auto_template',
    );
    $all_projects = new WP_Query( $args );

    $project_options = array(
        'false' =>  __( 'None', 'portal-auto' ),
    );

    while( $all_projects->have_posts() ) {

        $all_projects->the_post();

        $project_options[get_the_ID()] = get_the_title();

    }

    foreach( $wp_roles->roles as $key => $name ) {

        $portal_auto_settings['portal_auto_project_settings'][ $key . '_project' ] = array(
            'id'    =>  $key . '_project',
            'name'  =>  $name['name'],
            'desc'   =>  __( 'Project to Clone' ),
            'type'  =>  'select',
            'options'   =>  $project_options
        );

    }

    return apply_filters( 'wp_auto_settings', array_merge( $settings, $portal_auto_settings ) );


}
