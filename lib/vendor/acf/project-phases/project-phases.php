<?php

// 2. Include field type for ACF5
// $version = 5 and can be ignored until ACF6 exists
function include_field_types_acf_project_phases( $version ) {
    include_once('acf-project-phases-v5.php');
}
add_action('acf/include_field_types', 'include_field_types_acf_project_phases');

// 3. Include field type for ACF4
function register_fields_acf_project_phases() {
    include_once('acf-project-phases-v4.php');
}

add_action('acf/register_fields', 'register_fields_acf_project_phases');
