<?php

define( 'ACF_SELECT_2_URI', plugins_url( '', __FILE__ ) );

function include_field_types_acf_project_users( $version ) {
    include_once('acf-project-users-v5.php');
}

add_action('acf/include_field_types', 'include_field_types_acf_project_users');




// 3. Include field type for ACF4
function register_fields_acf_project_users() {
    include_once('acf-project-users-v4.php');
}

add_action('acf/register_fields', 'register_fields_acf_project_users');
