<?php

/**
 * Call the portal_essentials function and echo it to the screen. Adds it to the page using the portal_the_essentials hook
 *
 *
 * @param NULL
 * @return NULL
 **/

add_action( 'portal_the_essentials', 'portal_echo_essentials' );
function portal_echo_essentials() {

    global $post;
    echo portal_essentials( $post->ID );

}

/**
 * Outputs all the overview information to the page
 *
 *
 * @param $id, int post ID. $style string, $docs string
 * @return HTML output
 **/

function portal_essentials( $id = null, $style = null, $docs = null ) {

    ob_start();

	include( portal_template_hierarchy( '/projects/overview/index.php' ) );

    return ob_get_clean();

}


/**
 * Outputs a doughnut chart of all project progress
 *
 *
 * @param $id (current post ID)
 * @return HTML output
 **/

function portal_short_progress( $id ) {

    include( portal_template_hierarchy( '/parts/short-progress.php' ) );

}

/* Use an action to add the progress indicator to the template */
add_action( 'portal_the_progress', 'portal_echo_total_progress' );
function portal_echo_total_progress( $post_id = NULL ) {

    global $post;

    $post_id = ( isset( $post_id ) ? $post_id : $post->ID );

	echo portal_total_progress( $post_id );

}

function portal_total_progress( $id, $style = null, $options = null ) {

    ob_start();

	if( get_option( 'portal_database_version' ) < 4) {

		include( portal_template_hierarchy( '/projects/milestones/dep/index.php' ) );

	} else {

		include( portal_template_hierarchy( '/projects/milestones/index.php' ) );

	}

    return ob_get_clean();

}

function portal_get_phase_completion( $tasks, $id ) {

    $completed 			= 0;
    $task_count 		= 0;
    $task_completion	= 0;

    if( get_field( 'phases_automatic_progress', $id ) ) {

        foreach( $tasks as $task ) {

            $task_count++;

			$task_completion += $task[ 'status' ];

		}

        if( $task_count >= 1 ) {

            $completed += ceil( $task_completion / $task_count );

		} elseif ( $task_count == 1 ) {

		    $completed = 0;

		} else {

		    $completed += $task_completion;

		}

        return $completed;

    }

}

function portal_get_phase_completed( $id, $post_id = null ) {

    $post_id = ( $post_id == null ? get_the_ID() : $post_id );

    $completed 			= 0;
    $tasks 				= 0;
    $task_completion 	= 0;
    $completed_tasks 	= 0;

    $phases             = get_field( 'phases', $post_id );
    $tasks_array        = $phases[$id]['tasks'];

    if( empty($tasks_array) &&  get_field( 'phases_automatic_progress', $post_id ) ) {
        return array(
            'completed'         =>  0,
            'tasks'             =>  0,
            'completed_tasks'   =>  0
        );
    }

    if( get_field( 'phases_automatic_progress', $post_id ) ) {

        if( is_array( $tasks_array ) ) {
            foreach( $tasks_array as $task ) {
                $tasks++;
                $task_completion += intval($task['status']);
                if( $task['status'] == '100' ) $completed_tasks++;
            }
        }

        if( $tasks >= 1 ) {
			$completed += ceil( $task_completion / $tasks );
		} elseif ( $tasks == 1 ) {
			$completed = 0;
		} else {
			$completed += $task_completion;
		}

    } else {

        if( is_array($tasks_array) ) {
            foreach( $tasks_array as $task ) {
                $tasks++;
    			$task_completion += $task['status'];
    			if( $task['status'] == '100' ) $completed_tasks++;
		    }
        }

        $completed = $phases[$id]['percent_complete'];

    }

    return array(
        'completed'         =>  intval($completed),
        'tasks'             =>  $tasks,
        'completed_tasks'   =>  $completed_tasks
    );

}



add_action( 'portal_the_phases', 'portal_echo_phases' );
function portal_echo_phases() {

    global $post;
    echo portal_phases( $post->ID );

}

function portal_phases( $id, $style = null, $task_style = null ) {

    ob_start();

    include( portal_template_hierarchy( 'projects/phases/index.php' ) );

    return ob_get_clean();

}

add_action( 'portal_the_discussion', 'portal_echo_discussions' );
function portal_echo_discussions() {

    include( portal_template_hierarchy( 'projects/discussion/index.php' ) );

}

/**
 *
 * Function portal_documents
 *
 * Stores all of the portal_documents into an unordered list and returns them
 *
 * @param $post_id
 * @return $portal_docs
 *
 */

function portal_documents( $post_id, $style ) {

    ob_start();

    include( portal_template_hierarchy( 'projects/overview/documents/index.php' ) );

    return ob_get_clean();

}

function portal_get_nav_items( $post_id = NULL ) {

    global $post;

    $post_id = ( $post_id == NULL ? $post->ID : $post_id );

    $nav_items  = array();
    $back_opt   = portal_get_option('portal_back');

    if( is_single() && get_post_type() == 'portal_projects' ) {

        $back = ( $back_opt && !empty($back_opt) ? $back_opt : get_post_type_archive_link('portal_projects') );
        $single_nav_items = array();

        $single_nav_items['back'] = array(
            'title' =>  __( 'Dashboard', 'portal_projects' ),
            'id'    =>  'nav-dashboard',
            'link'  =>  $back,
            'icon'  =>  'portal-fi-back portal-fi-icon',
        );

        if( has_nav_menu( 'portal_project_menu' ) ) {
            $single_nav_items = array_merge( $single_nav_items, portal_get_custom_project_menu_items('portal_project_menu') );
        }

        $nav_items = ( empty($nav_items) ? $single_nav_items : array_merge( $nav_items, $single_nav_items ) );

    }

    if( ( is_post_type_archive() ) && (	has_nav_menu( 'portal_archive_menu' ) ) ) {
        $nav_items = ( empty($nav_items) ? portal_get_custom_project_menu_items('portal_archive_menu') : array_merge( $nav_items, portal_get_custom_project_menu_items('portal_archive_menu') ) );
    }

    return apply_filters( 'portal_get_nav_items', $nav_items, $post_id );

}

function portal_get_custom_project_menu_items( $theme_location ) {

	$menu_items = portal_get_menu_items_in_location( $theme_location );
	$menu       = array();
    $i          = 0;

	foreach ( (array) $menu_items as $key => $menu_item ) {

            $item = array(
                'portal_custom_menu_' . $theme_location . '_' . $i => array(
                    'title' =>  $menu_item->title,
                    'id'    =>  'portal_custom_menu_' . $theme_location . '_' . $i,
                    'link'  =>  $menu_item->url,
            ) );

            if( isset( $menu_item->description ) ) {
                $item[ 'portal_custom_menu_' . $theme_location . '_' . $i ][ 'icon' ] = $menu_item->description;
            }

            $menu = array_merge( $menu, $item );

            $i++;

		}

	return apply_filters( 'portal_custom_project_menu_' . $theme_location, $menu );

}

function portal_get_menu_items_in_location( $location_id ) {

	$locations 			= get_registered_nav_menus();
	$menus 				= wp_get_nav_menus();
	$menu_locations 	= get_nav_menu_locations();

	if ( isset( $menu_locations[ $location_id ] ) ) {

		foreach ( $menus as $menu ) {
			// If the ID of this menu is the ID associated with the location we're searching for
			if ( $menu->term_id == $menu_locations[ $location_id ] ) {
				// This is the correct menu

				// Get the items for this menu
				return wp_get_nav_menu_items( $menu );

			}
		}

	}

    return false;

}

/**
 *
 * Function portal_single_template_header
 *
 * Adds the header to the Project yoop single.php template
 *
 * @param
 * @return
 *
 */
add_action( 'portal_the_header', 'portal_single_template_header' );
function portal_single_template_header() {

    global $post;

    $user_has_access = yoop_check_access( $post->ID );

	if( $user_has_access ): ?>

	<div id="portal-primary-header" class="portal-grid-row cf">

		<?php if( ( portal_get_option( 'portal_logo' ) != '' ) && ( portal_get_option( 'portal_logo' ) != 'http://' ) ) { ?>
			<div class="portal-masthead-logo">
				<a href="<?php echo home_url(); ?>" class="portal-single-project-logo"><img src="<?php echo portal_get_option('portal_logo'); ?>"></a>
			</div>
		<?php } ?>

         <?php if( $user_has_access ): ?>

			<?php do_action( 'portal_the_navigation' ); ?>

            	<?php if( is_user_logged_in() ) { ?>
					 <aside class="portal-masthead-user">

						<?php
						$cuser = wp_get_current_user(); ?>

						<p>
                            <?php esc_html_e( 'Hello', 'portal_projects' ); ?> <?php esc_html_e($cuser->display_name); ?>
                            <a href="<?php echo esc_url(wp_logout_url()); ?>">Log Out</a>
                        </p>

						<?php echo get_avatar( $cuser->ID ); ?>

					</aside>
				<?php } ?>

		 <?php endif; ?>

	</div>

<?php endif;

}

/**
 *
 * Function portal_add_dashboard_widgets
 *
 * Defines the dashboard widget slug, title and display function
 *
 * @param
 * @return
 *
 */

add_action( 'wp_dashboard_setup', 'portal_add_dashboard_widgets' );
function portal_add_dashboard_widgets() {

    // Make sure the user has the right permissions

    if(current_user_can('publish_portal_projects')) {

        wp_add_dashboard_widget(
            'portal_dashboard_overview',         // Widget slug.
            'Projects',         // Title.
            'portal_dashboard_overview_widget_function' // Display function.
        );

		wp_add_dashboard_widget(
			'portal_dashboard_timing',
			'Project Calendar',
			'portal_dashboard_calendar_widget'
		);

    }

}

/**
 *
 * Function portal_dashboard_overview_widget_function
 *
 * Echo's the output of portal_populate_dashboard_widget
 *
 * @param
 * @return contents of portal_populate_dashboard_widget
 *
 */


function portal_dashboard_overview_widget_function() {
    echo portal_populate_dashboard_widget();
}


function portal_get_project_breakdown() {

	$cuser 			= wp_get_current_user();

    /*
     * Cache this query
     */
    $projects = wp_cache_get( 'portal_project_breakdown_' . $cuser->ID );
    if ( false === $projects ) {
    	$projects = portal_get_all_my_projects();
    	wp_cache_set(  'portal_project_breakdown_' . $cuser->ID, $projects, 7200 );
    }

    $total_projects = $projects->found_posts;
    $taxonomies 	= get_terms('portal_tax','fields=count');
    $colors         = apply_filters( 'portal_project_breakdown_colors', array(
            'complete'      =>  '#2a3542',
            'incomplete'    =>  '#3299bb',
            'unstarted'     =>  '#666666'
    ) );

    // Calculate the number of completed projects

    $completed_projects = 0;
    $not_started 		= 0;
    $active 			= 0;

    while( $projects->have_posts() ) { $projects->the_post();

		global $post;

        if( portal_compute_progress( $post->ID ) == '100') {
            $completed_projects++;
        } elseif( portal_compute_progress( $post->ID ) == 0) {
            $not_started++;
		} else {
			$active++;
		}

    } wp_reset_postdata();

	if ( ( $completed_projects != 0 ) && ( $total_projects != 0 ) ) {
	    $percent_complete = floor( $completed_projects / $total_projects * 100 );
	} else {
		$percent_complete = 0;
	}

	if ( ( $not_started != 0 ) && ( $total_projects != 0 ) ) {
	    $percent_not_started = floor( $not_started / $total_projects * 100 );
	} else {
		$percent_not_started = 0;
	}

    $percent_remaining = 100 - $percent_complete - $percent_not_started;

	ob_start(); ?>

	<div class="portal-chart">
		<canvas id="portal-dashboard-chart" width="100%" height="150"></canvas>
	</div>

	<script>

        jQuery(document).ready(function() {

			var chartOptions = {
				responsive: true,
				percentageInnerCutout : <?php echo esc_js( apply_filters( 'portal_graph_percent_inner_cutout', 92 ) ); ?>
			}

            var data = [
                {
                    value: <?php echo $percent_complete; ?>,
                    color: "<?php echo $colors[ 'complete' ]; ?>",
                    label: "Completed"
                },
                {
                    value: <?php echo $percent_remaining; ?>,
                    color: "<?php echo $colors[ 'incomplete' ]; ?>",
                    label: "In Progress"
                },
                {
                    value: <?php echo $percent_not_started; ?>,
                    color: "<?php echo $colors[ 'unstarted' ]; ?>",
                    label: "Not Started"
                }
            ];


            var portal_dashboard_chart = document.getElementById("portal-dashboard-chart").getContext("2d");

            new Chart(portal_dashboard_chart).Doughnut(data,chartOptions);

        });

	</script>


	<ul data-pie-id="portal-dashboard-chart" class="dashboard-chart-legend">
		<li data-value="<?php echo esc_attr( $percent_not_started ); ?>">
            <span><?php echo esc_html( $percent_not_started ); ?>% <?php esc_html_e( 'Not Started', 'portal_projects' ); ?></span>
        </li>
		<li data-value="<?php echo esc_attr( $percent_remaining ); ?>">
            <span><?php echo esc_html( $percent_remaining ); ?>% <?php esc_html_e( 'In Progress', 'portal_projects' ); ?></span>
        </li>
		<li data-value="<?php echo esc_attr( $percent_complete ); ?>">
            <span><?php echo esc_html( $percent_complete ); ?>% <?php esc_html_e( 'Complete', 'portal_projects' ); ?></span>
        </li>
	</ul>

	 <ul class="portal-projects-overview">
			<li><span class="portal-dw-projects"><?php echo $total_projects; ?></span> <strong><?php esc_html_e( 'Projects', 'portal_projects' ); ?></strong> </li>
			<li><span class="portal-dw-completed"><?php echo $completed_projects; ?></span> <strong><?php esc_html_e( 'Completed', 'portal_projects' ); ?></strong></li>
			<li><span class="portal-dw-active"><?php echo $active; ?></span> <strong><?php esc_html_e( 'Active', 'portal_projects' ); ?></strong></li>
			<li><span class="portal-dw-types"><?php echo $taxonomies; ?></span> <strong><?php esc_html_e( 'Types', 'portal_projects' ); ?></strong></li>
	  </ul>

	<?php
}

/**
 *
 * Function portal_populate_dashboard_widget
 *
 * Gathers the dashboard content and returns it in a variable
 *
 * @param
 * @return (variable) ($output)
 *
 */

// TODO: This should be a template file
function portal_populate_dashboard_widget() {

    $args = apply_filters( 'portal_populate_dashboard_widget_args', array(
        'post_type'         =>  'portal_projects',
        'posts_per_page'    =>  '10',
        'orderby'           =>  'modified',
        'order'             =>  'DESC',
        'post_status'       =>  'publish'
    ) );

    $recent = new WP_Query( $args );

	echo portal_get_project_breakdown(); ?>

			  <hr>

			 <h4><?php _e('Recently Updated','portal_projects'); ?></h4>
			 <table class="portal-dashboard-widget-table">
				<tr>
					<th><?php esc_html_e( 'Project', 'portal_projects' ); ?></th>
					<th><?php esc_html_e( 'Progress', 'portal_projects' ); ?></th>
					<th>&nbsp;</th>
				</tr>

    			<?php while($recent->have_posts()): $recent->the_post(); global $post; ?>
        			<tr>
					   <td>
						   <a href="<?php echo get_edit_post_link(); ?>"><?php the_title(); ?></a>
						   <p class="portal-dashboard-widget-updated"><?php esc_html_e( 'Updated on', 'portal_projects' ); ?> <?php echo get_the_modified_date( 'm/d/Y' ); ?></p>

					   </td>
					   <td>
						   <?php
						   $completed = portal_compute_progress( $post->ID );

						   	if($completed > 10): ?>
          						<p class="portal-progress"><span class="portal-<?php echo $completed; ?>"><strong>%<?php echo $completed; ?></strong></span></p>
							<?php else: ?>
            					<p class="portal-progress"><span class="portal-<?php echo $completed; ?>"></span></p>
        					<?php endif; ?>
  					  </td>
					  <td class="portal-dwt-date"><a href="<?php the_permalink(); ?>" target="_new" class="portal-dw-view"><?php esc_html_e( 'View', 'portal_projects' ); ?></a></td>
				</tr>
    			<?php endwhile; ?>
		</table>

	<?php
    return ob_get_clean();

}

// Function to output the project calendar
function portal_dashboard_calendar_widget() {

	echo portal_output_project_calendar();

}

function portal_get_section_nav_items() {

    $slug   = portal_get_option( 'portal_slug' );
    $cuser  = wp_get_current_user();

    $defaults = apply_filters( 'portal_section_nav_items', array(
        array(
            'name'  =>  __( 'Dashboard', 'portal_projects' ),
            'url'   =>  get_post_type_archive_link( 'portal_projects' ),
            'slug'  =>  'dashboard',
            'icon'  =>  'portal-fi-back portal-fi-icon',
        ),
        array(
            'name'  =>  __( 'Teams', 'portal_projects' ),
            'url'   =>  get_post_type_archive_link( 'portal_teams' ),
            'slug'  =>  'teams',
            'icon'  =>  'portal-fi-icon portal-fi-teams'
        ),
        array(
            'name'  =>  __( 'Calendar', 'portal_projects' ),
            'url'   =>  get_post_type_archive_link('portal_projects') . ( get_option( 'permalink_structure' ) ? 'calendar/' : '&portal_calendar_page=' ) . $cuser->ID,
            'slug'  =>  'calendar',
            'icon'  =>  'portal-fi-icon portal-fi-calendar'
        ),
        array(
            'name'  =>  __( 'Tasks', 'portal_projects' ),
            'url'   =>  get_post_type_archive_link('portal_projects') . ( get_option( 'permalink_structure' ) ? 'tasks/' : '&portal_tasks_page=' ) . $cuser->ID,
            'slug'  =>  'tasks',
            'icon'  =>  'portal-fi-icon portal-fi-tasks',
        )
    ) );

    $teams = portal_get_user_teams( $cuser->ID );

    if( empty( $teams ) ) unset( $defaults[1] );

    if( has_nav_menu('portal_section_menu') ) {
        $defaults = ( array_merge( $defaults, portal_get_custom_section_menu_items('portal_section_menu') ) );
    }

    return $defaults;

}

function portal_get_custom_section_menu_items( $menu_slug ) {

     $menu_items = portal_get_menu_items_in_location( $menu_slug );
     $menu       = array();

     foreach ( (array) $menu_items as $key => $menu_item ) {

          $item = array(
              'name'    =>  $menu_item->title,
              'url'     =>  $menu_item->url,
              'slug'    =>  urlencode( $menu_item->title ),
              'icon'    =>  ''
          );

          if( isset( $menu_item->description ) ) $item['icon'] = $menu_item->description;

          $menu[] = $item;

     }

     return apply_filters( 'portal_custom_section_menu_' . $menu_slug, $menu );

}

add_filter( 'portal_section_nav_link_class', 'portal_section_nav_active_states', 10, 2 );
function portal_section_nav_active_states( $class, $slug ) {

    $custom_templates = array(
        'portal_calendar_page',
        'portal_tasks_page'
    );

    $conditions = apply_filters( 'portal_section_nav_active_states', array(
        array(
            'condition' => get_query_var('portal_calendar_page'),
            'slug'      =>  'calendar'
        ),
        array(
            'condition' =>  is_post_type_archive('portal_teams'),
            'slug'      =>  'teams'
        ),
        array(
            'condition' =>  is_post_type_archive('portal_projects') && get_query_var('portal_tasks_page'),
            'slug'      =>  'tasks'
        ),
        array(
            'condition' =>  is_post_type_archive('portal_projects') && !get_query_var('portal_tasks_page') && !get_query_var('portal_calendar_page'),
            'slug'      =>  'dashboard'
        )
    ) );

    foreach( $conditions as $condition ) {
        if( $condition['condition'] && $slug == $condition['slug'] ) return 'active';
    }

    if( get_post_type() == 'portal_' . $slug ) return 'active';

    return $class;

}

add_action( 'portal_head', 'portal_favicon_markup' );
function portal_favicon_markup() {
    if( portal_get_option('portal_favicon') ) {

        $favicon = portal_get_option('portal_favicon');
        $ext     = explode( '.', $favicon );
        $ext     = array_pop($ext);
        $media   = '';

        if( $ext == 'png' ) {
            $media = 'png';
        } elseif( $ext == 'gif' ) {
            $media = 'gif';
        } elseif( $ext == 'ico' ) {
            $media = 'x-icon';
        } else {
            $media = $ext;
        }

        echo '<link rel="icon" type="' . esc_attr( $media . '/image' ) . '" href="' . esc_url( portal_get_option('portal_favicon') ) .'">';

    }
}


add_action( 'wp_login_failed', 'portal_login_failed_redirect' );  // hook failed login
function portal_login_failed_redirect( $username ) {

     $referrer = $_SERVER['HTTP_REFERER'];  // where did the post submission come from?

     // if there's a valid referrer, and it's not the default log-in screen
     if ( !empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin') ) {
          wp_redirect( $_SERVER['HTTP_REFERER'] . '/?login=failed' );  // let's append some information (login=failed) to the URL for the theme to use
          exit;
     }

 }
