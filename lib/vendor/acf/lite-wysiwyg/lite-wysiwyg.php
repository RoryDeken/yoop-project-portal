<?php

$deps = array(
    'lite-wysiwyg-controller.php'
);

foreach( $deps as $dep ) include_once( $dep );

// 2. Include field type for ACF5
// $version = 5 and can be ignored until ACF6 exists
function include_field_types_acf_lite_wysiwyg( $version ) {
    include_once( 'lite-wysiwyg-v5.php' );
}
add_action('acf/include_field_types', 'include_field_types_acf_lite_wysiwyg');

// 3. Include field type for ACF4
function register_fields_acf_lite_wysiwyg() {
    include_once( 'lite-wysiwyg-v4.php' );
}
add_action('acf/register_fields', 'register_fields_acf_lite_wysiwyg' );
