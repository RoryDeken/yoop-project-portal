<?php

add_action( 'plugins_loaded', 'portal_auto_init', 99999 );
function portal_auto_init() {

    do_action( 'portal_auto_before_init' );

    if( function_exists('portal_get_option') ) {
        require_once( 'init.php' );
    } else {
        add_action( 'admin_notices', 'portal_auto_needs_yoop' );
    }

    do_action( 'portal_auto_after_init' );

}

function portal_auto_needs_yoop() { ?>

    <div class="notice notice-error is-dismissible">
        <p><?php esc_html_e( 'Auto Generate Projects requires Project yoop to run', 'portal_projects' ); ?></p>
    </div>

    <?php
}


 add_action( 'plugins_loaded', 'portal_auto_localize_init' );
 function portal_auto_localize_init() {
     load_plugin_textdomain( 'portal-auto', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
 }

$constants = array(
    'portal_AUTO_URL'        =>  plugin_dir_url( __FILE__ ),
    'portal_AUTO_PATH'       =>  plugin_dir_path( __FILE__ ),
    'portal_AUTO_VER'        =>  '1.1',
);

foreach( $constants as $constant => $val ) {
    if( !defined( $constant ) ) define( $constant, $val );
}
