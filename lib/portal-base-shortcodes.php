<?php
/**
 * Description of portal-base-shortcodes
 *
 * Shortcodes that are present in lite and paid versino of yoop
 * @package portal-projects
 *
 */

function portal_current_projects( $atts ) {

    extract( shortcode_atts(
            array(
                'type'      => 'all',
                'status'    => 'all',
                'access'    => 'user',
                'count'     => '10',
				'sort'	    => 'default',
				'order'	    => 'ASC',
                'collapsed' => false,
                'target'    => '',
            ), $atts )
    );

    $paged 	= ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	$cuser 	= wp_get_current_user();
	$cid 	= $cuser->ID;

	// Determine the sorting

	if($sort == 'start') {

		$meta_sort 	= 'start_date';
		$order_by 	= 'meta_value';

	} elseif ($sort == 'end') {

		$meta_sort 	= 'end_date';
		$order_by 	= 'meta_value';

	} elseif ($sort == 'title') {

		$meta_sort 	= NULL;
		$order_by 	= 'title';

	} else {

		$meta_sort 	= 'start_date';
		$order_by 	= 'menu_order';

	}

	// Set the initial arguments

    $args = array(
        'post_type' 		=> 	'portal_projects',
        'paged'				=> 	$paged,
        'posts_per_page'	=> 	$count,
		'meta_key' 			=>	$meta_sort,
		'orderby'			=>	$order_by,
		'order'				=>	$order
    );

    // If a type has been selected, add it to the argument

    if( ( !empty( $type ) ) && ( $type != 'all' ) ) {

        $tax_args 	= array( 'portal_tax' => $type );
        $args 		= array_merge( $args, $tax_args );

    }

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

		$args = array_merge($args,$status_args);

	}

	if($status == 'completed') {
		$status_args = array('tax_query' => array(
			array(
				'taxonomy'	=>	'portal_status',
				'field'		=>	'slug',
				'terms'		=>	'completed',
				)
			)
		);

		$args = array_merge($args,$status_args);

	}


    if( $access == 'user' ) {

		// Just restricting access, not worried about active or complete

        if( !current_user_can( 'manage_options' ) ) {

            $cuser 	    = wp_get_current_user();
            $meta_args  = array(
                'meta_query' 	=> portal_access_meta_query( $cuser->ID ),
				'has_password'	=> false
            );

			$args = array_merge( $args, $meta_args );

		}

    }

    $projects = new WP_Query($args);

	if( ( $access == 'user' ) && ( !is_user_logged_in() ) ) { ?>
	<div id="portal-projects" class="portal-shortcode">
		<div id="portal-overview">

        	<div id="portal-login" class="shortcode-login">

				<h2><?php _e( 'Please Login to View Projects', 'portal_projects' ); ?></h2>

				<?php echo yoop_login_form(); ?>

			</div> <!--/#portal-login-->

		</div>
	</div>
	<?php

		 portal_front_assets(1);

		return;

	}

    if( $projects->have_posts() ): ob_start(); ?>

		<div id="portal-projects">

			<?php
			$template = ( $collapsed ? '/shortcodes/project-list-collapsed.php' : '/shortcodes/project-list.php' );
			include( portal_template_hierarchy( $template ) );
			?>

		</div>

        <?php portal_front_assets( 1 );

		// Clear out this query
		wp_reset_query();

        return ob_get_clean();

    else:

        return '<p>' . __( 'No projects found', 'portal_projects' ) . '</p>';

    endif;

}
add_shortcode( 'project_list', 'portal_current_projects' );

function portal_archive_project_listing( $projects, $page = 1 ) {

    if( $projects->have_posts()):

        ob_start();

        include( portal_template_hierarchy( 'dashboard/components/projects/table' ) );

        portal_front_assets(1);

        return ob_get_clean();

    else:

        return '<div class="portal-notice"><p>' . __( 'No projects found' , 'portal_projects' ) . '</p></div>';

    endif;

}

function portal_project_listing_dialog() {

    $portal_taxes      = get_terms('portal_tax');
    $portal_tax_list   = '';

    foreach($portal_taxes as $tax) {
        $portal_tax_list .= '<option value="'.$tax->slug.'">'.$tax->name.'</option>';
    }

    $output = '

			<style type="text/css">
				#TB_Window { z-index: 9000 !important; }
			</style>

			<div class="portal-dialog" style="display:none">
					<div id="portal-project-listing-dialog">
						<h3>'.__('Project Listing','portal_projects').'</h3>
						<p>'.__('Select from the options below to output a list of projects.','portal_projects').'</p>
						<table class="form-table">
							<tr>
								<th><label for="portal-project-taxonomy">'.__('Project Type','portal_projects').'</label></th>
								<td>
									<select id="portal-project-taxonomy" name="portal-project-taxonomy">
										<option value="all">Any</option>
										'.$portal_tax_list.'
									</select>
								</td>
							</tr>
							<tr>
								<th><label for="portal-project-status">'.__('Project Status','portal_projects').'</label></th>
								<td>
									<select id="portal-project-status" name="portal-project-status">
										<option value="all">'.__('All','portal_projects').'</option>
										<option value="active">'.__('Active','portal_projects').'</option>
										<option value="completed">'.__('Completed','portal_projects').'</option>
									</select>
								</td>
							</tr>
							<tr>
							    <th colspan="2">
							        <input type="checkbox" name="portal-user-access" id="portal-user-access" checked>
							        <label for="portal-user-access">'.__('Only display projects current user has permission to access','portal_projects').'</label>
							    </th>
							</tr>
							<tr>
								<th><label for="portal-project-sort">'.__('Order By','portal_projects').'</label></th>
								<td>
									<select id="portal-project-sort" name="portal-project-sort">
										<option value="none">'.__('Creation Date','portal_projects').'</option>
										<option value="start">'.__('Start Date','portal_projects').'</option>
										<option value="end">'.__('End Date','portal_projects').'</option>
										<option value="title">'.__('Title','portal_projects').'</option>
									</select>
								</td>
							</tr>
							<tr>
							    <th><label for="portal-project-count">'.__('Projects to show','portal_projects').'</label></th>
                                <td>
                                    <select id="portal-project-count" name="portal-project-count">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                        <option value="-1">All</option>
                                    </select>
                                </td>
							</tr>
						</table>';

    $output .= '<p><input class="button-primary" type="button" onclick="InsertportalProjectList();" value="'.__('Insert Project List','portal_projects').'"> <a class="button" onclick="tb_remove(); return false;" href="#">'.__('Cancel','portal_projects').'</a></p>';

    $output .= '</div></div>';

    echo $output;

}

function portal_buttons() {

	// Make sure the buttons are enabled

	if( ( portal_get_option( 'portal_disable_js' ) === '0') || ( portal_get_option( 'portal_disable_js' ) == NULL ) ) {

		add_filter( 'mce_external_plugins', 'portal_add_buttons' );
    	add_filter( 'mce_buttons', 'portal_register_buttons' );
	}

}

function portal_add_buttons( $plugin_array ) {
    $plugin_array[ 'portalbuttons' ] = plugins_url(). '/' . portal_PLUGIN_DIR . '/dist/assets/js/portal-buttons.js';
    return $plugin_array;
}

function portal_register_buttons( $buttons ) {

    array_push( $buttons, 'currentprojects', 'singleproject' );

    return $buttons;
}

function portal_refresh_mce( $ver ) {
    $ver += 3;
    return $ver;
}

add_filter( 'tiny_mce_version', 'portal_refresh_mce');
add_action( 'init', 'portal_buttons' );


/**
 *
 * Function portal_dashboard_shortcode
 *
 * Outputs the Dashboard Widget in Shortcode Format
 *
 * @param 	(variable) ($atts) 	Attributes from the shortcode - currently none
 * @return 	($output) 			(Content from portal_populate_dashboard_widget() )
 *
 */

add_shortcode( 'yoop_dashboard', 'portal_dashboard_shortcode' );
function portal_dashboard_shortcode( $atts ) {

    $output = '<div class="portal-dashboard-widget">' . portal_populate_dashboard_widget() . '</div>';
    return $output;

}

add_shortcode( 'yoop_login', 'portal_login_form' );
function portal_login_form( $atts, $content ) {

    if( is_user_logged_in() ) return;

    $redirect = ( isset( $atts['redirect'] ) ? $atts['redirect'] : get_post_type_archive_link('portal_projects') );

    ob_start();

        yoop_login_form( null, $redirect );

    return ob_get_clean();

}
