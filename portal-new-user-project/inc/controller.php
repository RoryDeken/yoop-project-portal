<?php
add_action( 'user_register', 'portal_check_new_register' );
function portal_check_new_register( $user_id ) {

    $portal_settings = get_option( 'portal_settings' );
    $user     = get_user_by( 'id', $user_id );

    if( !$user->roles ) {
        return;
    }

    foreach( $user->roles as $role ) {

        if( !isset( $portal_settings[$role . '_project'] ) || $portal_settings[$role . '_project'] == 'false' ) {
            continue;
        }

        $clone_id = intval( $portal_settings[$role . '_project'] );

        $created_projects = get_user_meta( $user_id, '_portal_auto_projects', false );

        if( in_array( $clone_id, $created_projects ) ) {
            continue;
        }
        add_user_meta( $user_id, '_portal_auto_projects' , $clone_id );

        require_once( PROJECT_YOOP_DIR . '/lib/vendor/clone/duplicate-post-admin.php' );

        $post   = get_post( $clone_id );
        $new_id = portal_auto_create_duplicate( $post, 'publish', $user );

        if ( 0 !== $new_id ) {

            update_post_meta( $new_id, '_portal_assigned_users', array( $user_id ) );
            update_post_meta( $new_id, 'allowed_users_0_user', $user_id );
            update_post_meta( $new_id, 'allowed_users', 1 );
            update_post_meta( $clone_id, '_portal_cloned', 1 );
            update_post_meta( $new_id, 'client', $user->first_name . ' ' . $user->last_name );

            update_field( 'restrict_access_to_specific_users', array( 'Yes' ), $new_id );

            $new_project = array(
                'ID'          	=> $new_id,
                'post_status' 	=> 'publish',
                'post_title'	=> $user->first_name . ' ' . $user->last_name . ': ' . get_the_title($new_id),
                'post_name'		=>	''
            );

            wp_update_post( $new_project );

        }

    }

}

function portal_auto_create_duplicate( $post, $status = null , $new_post_author = null ) {

    // We don't want to clone revisions
    if ($post->post_type == 'revision') return;

    if ($post->post_type != 'attachment'){
        $prefix = get_option('duplicate_post_title_prefix');
        $suffix = get_option('duplicate_post_title_suffix');
        if (!empty($prefix)) $prefix.= " ";
        if (!empty($suffix)) $suffix = " ".$suffix;
        if (get_option('duplicate_post_copystatus') == 0) $status = 'publish';
    }

    if( !$new_post_author ) {
        $new_post_author = portal_duplicate_post_get_current_user();
    }

    $new_post = array(
        'menu_order' 		=> $post->menu_order,
        'comment_status' 	=> $post->comment_status,
        'ping_status' 		=> $post->ping_status,
        'post_author' 		=> $new_post_author->ID,
        'post_content' 		=> $post->post_content,
        'post_excerpt' 		=> (get_option('duplicate_post_copyexcerpt') == '1') ? $post->post_excerpt : "",
        'post_mime_type' 	=> $post->post_mime_type,
        'post_parent' 		=> $new_post_parent = empty($parent_id)? $post->post_parent : $parent_id,
        'post_password' 	=> $post->post_password,
        'post_status' 		=> $new_post_status = (empty($status))? $post->post_status: $status,
        'post_title' 		=> $prefix.$post->post_title.$suffix,
        'post_type' 		=> $post->post_type,
    );

    if(get_option('duplicate_post_copydate') == 1){
        $new_post['post_date'] = $new_post_date =  $post->post_date ;
        $new_post['post_date_gmt'] = get_gmt_from_date($new_post_date);
    }

    $new_post_id = wp_insert_post($new_post);

    // If you have written a plugin which uses non-WP database tables to save
    // information about a post you can hook this action to dupe that data.
    if ($post->post_type == 'page' || (function_exists('is_post_type_hierarchical') && is_post_type_hierarchical( $post->post_type )))
    do_action( 'portal_nup_duplicate_page', $new_post_id, $post );
    else
    do_action( 'portal_nup_duplicate_page', $new_post_id, $post );

    delete_post_meta($new_post_id, '_dp_original');
    delete_post_meta($new_post_id, '_portal_fe_global_template' );
    add_post_meta($new_post_id, '_dp_original', $post->ID);

    // If the copy is published or scheduled, we have to set a proper slug.
    if ($new_post_status == 'publish' || $new_post_status == 'future'){
        $post_name = wp_unique_post_slug($post->post_name, $new_post_id, $new_post_status, $post->post_type, $new_post_parent);

        $new_post = array();
        $new_post['ID'] = $new_post_id;
        $new_post['post_name'] = $post_name;

        // Update the post into the database
        wp_update_post( $new_post );
    }

    return $new_post_id;

}

add_action( 'portal_nup_duplicate_page', 'portal_nup_duplicate_post_copy_post_meta_info', 10, 2 );
add_action( 'portal_nup_duplicate_page', 'portal_nup_duplicate_post_copy_post_meta_info', 10, 2 );
function portal_nup_duplicate_post_copy_post_meta_info($new_id, $post) {

	$post_meta_keys = get_post_custom_keys($post->ID);

	if (empty($post_meta_keys)) return;

	foreach ($post_meta_keys as $meta_key) {
		$meta_values = get_post_custom_values($meta_key, $post->ID);
		foreach ($meta_values as $meta_value) {
			$meta_value = maybe_unserialize($meta_value);
			add_post_meta($new_id, $meta_key, $meta_value);
		}
	}
}

add_action( 'post_submitbox_misc_actions', 'portal_auto_template_metabox' );
function portal_auto_template_metabox() {

	global $post;

	if ( 'portal_projects' != get_post_type($post ) ) {
		return;
	}

	$value = get_post_meta( $post->ID, '_portal_auto_template', true ); ?>

	<div class="misc-pub-section misc-pub-section-last" style="border-top: 1px solid #eee;">
		<?php wp_nonce_field( plugin_basename( __FILE__ ), 'portal-auto-template' ); ?>
		<input type="checkbox" name="portal-auto-template" value="yes" <?php checked( 'yes', $value ); ?> />
		<label for="portal-auto-template">
			<?php esc_html_e( 'Use As Project Template', 'portal_projects' ); ?>
		</label>
	</div>

	<?php
}

add_action( 'save_post', 'portal_auto_save_meta' );
function portal_auto_save_meta( $post_id ) {

    if( 'portal_projects' != get_post_type($post_id) ) {
        return;
    }

    if( isset($_POST['portal-auto-template']) && $_POST['portal-auto-template'] == 'yes' ) {
        update_post_meta( $post_id, '_portal_auto_template', 'yes' );
    } else {
        delete_post_meta( $post_id, '_portal_auto_template' );
    }

}
