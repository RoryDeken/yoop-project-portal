<?php
/**
 * Custom Templating and routes
 * @var array
 */

/**
 * Important Definitions
 * @var array
 */
$defs = array(
    'portal_BASE_FILE'     =>  __FILE__,
    'portal_BASE_DIR'      =>  dirname( __FILE__ ),
    'portal_PLUGIN_URL'    =>  plugin_dir_url( __FILE__ )
);

foreach( $defs as $definition => $value ) {
    if( !defined( $definition ) ) define( $definition, $value );
}

/**
 * Custom function to mimic get_template_part but specific to project yoop
 * @param  [type] $path [description]
 * @return [type]       [description]
 */
function portal_get_template_part( $path ) {
	include( portal_template_hierarchy( $path . '.php' ) );
}

/**
 * Custom Rewrites for Templates
 * @return null
 */
function portal_custom_page_rewrite_rules() {

    global $wp_rewrite;

	$slug = portal_get_slug();

    if( isset( $wp_rewrite->front ) ) $slug = substr( $wp_rewrite->front, 1 ) . $slug;

    /**
     * Custom task pages
     */
    add_rewrite_rule( '^' . $slug . '/tasks/?$', 'index.php?post_type=portal_projects&portal_tasks_page=home', 'top' );
	add_rewrite_rule( '^' . $slug . '/tasks/([^/]*)/?', 'index.php?post_type=portal_projects&portal_tasks_page=$matches[1]', 'top' );

    /**
     * Custom Status
     */
     add_rewrite_rule( '^' . $slug . '/status/([^/]+)/?$', 'index.php?post_type=portal_projects&portal_status_page=$matches[1]', 'top' );
     add_rewrite_rule( '^' . $slug . '/status/([^/]+)/page/([0-9]+)?/?$', 'index.php?post_type=portal_projects&portal_status_page=$matches[1]&paged=$matches[2]', 'top' );

}
add_action( 'init', 'portal_custom_page_rewrite_rules', 20, 0 );

/**
 * Custom tags for query variables
 */
add_filter( 'query_vars', 'portal_status_page_query_vars' );
function portal_status_page_query_vars( $vars ) {

    $custom_vars = apply_filters( 'portal_custom_query_vars', array(
        'portal_status_page',
        'portal_tasks_page',
    ) );

    foreach( $custom_vars as $var )
        $vars[] = $var;

    return $vars;

}

add_filter( 'template_include', 'portal_template_chooser', 100, 1 );
function portal_template_chooser( $template ) {

    // Post ID
    $post_type    = get_post_type();
    $post_type    = ( is_search() ? 'portal_projects' : $post_type );
    $post_id      = get_the_ID();
    $supported    = apply_filters( 'portal_template_chooser_supported', array(
        'portal_projects'  =>  array(
            'single'    =>  'projects/single',
            'archive'   =>  'archive-portal_projects',
        ),
        'portal_teams'     =>  array(
            'single'    =>  'dashboard/components/teams/single',
            'archive'   =>  'dashboard/components/teams/index'
        )
    ) );

    $supported_post_types = array_keys( $supported );

    // If this isn't a yoop project or yoop archive, return as normal
    if ( ! in_array( get_post_type(), $supported_post_types ) && !is_post_type_archive( $supported_post_types ) && !is_portal_search() ) return $template;

    /**
     * Check to see if the user has a custom template set, return the custom template if so
     */
    $use_custom_template 	= portal_get_option( 'portal_use_custom_template' );
    $custom_template 		= portal_get_option( 'portal_custom_template' );

    if ( $use_custom_template && !empty( $custom_template ) ) return portal_custom_template( $custom_template );

    // If the user doesn't have access, redirect them to a login form
    if( !yoop_check_access() && !$use_custom_template ) {
        add_filter( 'portal_body_classes', 'portal_add_login_template_to_body_class' );
        return apply_filters( 'portal_login_template', portal_template_hierarchy( 'global/index' ) );
    }

    // Is this a single project
    if ( is_single() ) {
      return apply_filters( 'portal_single_template_' . $post_type, portal_template_hierarchy( $supported[$post_type]['single'] ) );
    }

    if( is_portal_search() ) {
        return apply_filters( 'portal_archive_template_search', portal_template_hierarchy( $supported['portal_projects']['archive'] ) );
    }

    if ( is_post_type_archive() ) {
      $post_type_archive = portal_find_archive_post_type( array_keys($supported) );
      return apply_filters( 'portal_archive_template_' . $post_type, portal_template_hierarchy( $supported[$post_type_archive]['archive'] ) );
    }

}

function portal_find_archive_post_type( $supported ) {

    foreach( $supported as $type ) {
        if( is_post_type_archive($type) ) return $type;
    }

    if ( is_post_type_archive( 'portal_teams' ) ) {
      return apply_filters( 'portal_teams_archive_template_portal_projects', portal_template_hierarchy( 'dashboard/components/teams/index.php' ) );
    }

}

/**
* Get the custom template if is set
*
* @since 1.0
*/

function portal_template_hierarchy( $template ) {

  $template	= ( substr( $template, -4, 4 ) == '.php' ? $template : $template . '.php' );
  $base_dir = ( $template == 'archive-portal_projects.php' ? 'projects/' : '' );

  if ( $theme_file = locate_template( array( 'yoop/' . $base_dir . $template ) ) ) {
  	$file = $theme_file;
  } else {
  	$file = portal_BASE_DIR . '/templates/' . $base_dir . $template;
  }

  return apply_filters( 'portal_standard_template_' . $template, $file );

}

function portal_custom_template( $template ) {

  if($theme_file = locate_template( array( $template ))) {

  	$file = $theme_file;

    return apply_filters( 'portal_custom_template_' . $template, $file );

  } else {

	  portal_template_hierarchy( $template );

  }

}

function portal_yoop_inject_into_custom_template( $content ) {

  if( is_admin() ) return $content;

  global $post;

  if ( empty( $post ) ) return $content;

  $use_custom_template 	= portal_get_option( 'portal_use_custom_template' );
  $custom_template 		= portal_get_option( 'portal_custom_template' );
  $custom_template 		= !empty( $custom_template ) ? $custom_template : false;
  $template             = NULL;

  if( !$use_custom_template ) {
	  return $content;
  }

  $archives = apply_filters( 'portal_inject_into_archive_templates', array(
      'portal_projects'    =>      portal_BASE_DIR . '/templates/projects/archive-portal_projects.php',
      'portal_teams'       =>      portal_BASE_DIR . '/templates/dashboard/components/teams/index.php'
  ) );

  /**
   * If this is a single project, then return the single template and close comments
   */
  if ( is_single() && ( bool ) $use_custom_template && $custom_template && get_post_type( $post->ID ) == 'portal_projects') {
        $template = portal_BASE_DIR . '/templates/projects/custom-template-single.php';
  }

  $portal_custom_archive_templates = apply_filters( 'portal_custom_archive_templates', array(
      array(
          'query_var' => 'portal_calendar_page',
          'template'  => portal_BASE_DIR . '/templates/dashboard/components/calendar/index.php'
      ),
      array(
          'query_var' =>  'portal_ical_page',
          'template'  =>  portal_BASE_DIR . '/templates/dashboard/components/calendar/ical.php'
      ),
      array(
          'query_var' =>  'portal_tasks_page',
          'template'  =>  portal_BASE_DIR . '/templates/dashboard/components/tasks/index.php'
      ),
  ) );

  if( !yoop_check_access() ) {
      $template = portal_BASE_DIR . '/templates/global/index.php';
  } else {

      $is_custom_template = false;
      foreach( $portal_custom_archive_templates as $custom_template ) {

          if( get_query_var( $custom_template['query_var'] ) && is_post_type_archive('portal_projects') ) {
              $is_custom_template = true;
              $template = $custom_template['template'];
          }

      }

      if( is_post_type_archive() && in_array( get_post_type(), array_keys($archives) ) && !$is_custom_template ) {

          foreach( $archives as $slug => $path ) {
              if( $slug == get_post_type() ) $template = $path;
          }

      }

  }

  if( !empty( $template ) ) {

      ob_start();
      include $template;
      $content = ob_get_contents();
      ob_end_clean();

  }

  return $content;

}
add_filter( 'the_content', 'portal_yoop_inject_into_custom_template' );


function portal_disable_custom_template_comments( $comment_template ) {

    if( is_singular() && get_post_type() == 'portal_projects' ) {
        return portal_BASE_DIR . '/templates/parts/blank.php';
    }

}

add_filter( 'portal_standard_template_dashboard/header.php', 'portal_custom_archive_header', 999, 1 );
function portal_custom_archive_header( $file ) {

    $use_custom_template 	= portal_get_option( 'portal_use_custom_template' );
    $custom_template 		= portal_get_option( 'portal_custom_template' );

    if ( ( bool ) $use_custom_template && $custom_template ) {
        return portal_BASE_DIR . '/templates/parts/wrapper-start.php';
    }

    return $file;

}

add_filter( 'portal_standard_template_dashboard/footer.php', 'portal_custom_archive_footer' );
function portal_custom_archive_footer( $file ) {

    $use_custom_template 	= portal_get_option( 'portal_use_custom_template' );
    $custom_template 		= portal_get_option( 'portal_custom_template' );

    if ( ( bool ) $use_custom_template && $custom_template ) {
        return portal_BASE_DIR . '/templates/parts/wrapper-end.php';
    }

    return $file;

}

add_action( 'template_redirect', 'portal_custom_archive_redirects', 900, 1 );
function portal_custom_archive_redirects( $template ) {

    if( portal_get_option('portal_use_custom_template') ) return $template;

    $portal_custom_archive_templates = apply_filters( 'portal_custom_archive_templates', array(
        array(
            'query_var' => 'portal_calendar_page',
            'callback'  => 'portal_return_calendar_template'
        ),
        array(
            'query_var' =>  'portal_ical_page',
            'callback'  =>  'portal_return_ical_template'
        ),
        array(
            'query_var' =>  'portal_tasks_page',
            'callback'  =>  'portal_return_tasks_template'
        )
    ) );

    foreach( $portal_custom_archive_templates as $custom_template ) {

        if( get_query_var( $custom_template['query_var'] ) && is_post_type_archive('portal_projects') && isset( $custom_template['callback'] ) ) {
            return add_filter( 'template_include', $custom_template['callback'], 101 );
        }

    }

}

/**
 * Custom dashboard template callbacks
 */

function portal_return_calendar_template() {
	return portal_template_hierarchy( 'dashboard/components/calendar/index.php' );
}

function portal_return_tasks_template() {
    return portal_template_hierarchy( 'dashboard/components/tasks/index.php' );
}

function portal_return_ical_template() {
    return portal_template_hierarchy( 'dashboard/components/calendar/ical.php' );
}

/**
 * Custom dashboard limit posts
 */

add_action( 'pre_get_posts', 'portal_custom_template_limit_archive_posts' );
function portal_custom_template_limit_archive_posts( $query ) {

    if( ( is_post_type_archive('portal_projects') || is_post_type_archive('portal_teams') ) && !is_admin() && portal_get_option('portal_use_custom_template') && $query->is_main_query() ) {
        $query->set( 'posts_per_page', 1 );
    }

}

add_action( 'portal_head', 'portal_check_wp_head_enabled' );
function portal_check_wp_head_enabled() {
	if( portal_get_option( 'portal_enable_wp_head' ) ) wp_head();
}

add_action( 'init', 'portal_custom_routes_rewrite_rules' );
function portal_custom_routes_rewrite_rules() {

	$slug = portal_get_slug();

	/**
	 * @package dates JSON
	 *
	 * Allow the user ID to be passed in to create a JSON feed
	 */
    add_rewrite_tag( '%portal_dates%', '([^&]+)' );
    add_rewrite_rule( 'portal-dates/([^&]+)/?', 'index.php?portal_dates=$matches[1]', 'top' );

    /**
     * @package Status
     *
     * Filter all projects by active or complete
     */

     add_rewrite_rule( '^' . $slug . '/status/([^/]+)/?$', 'index.php?post_type=portal_projects&portal_status_page=$matches[1]', 'top' );
     add_rewrite_rule( '^' . $slug . '/status/([^/]+)/page/([0-9]+)?/?$', 'index.php?post_type=portal_projects&portal_status_page=$matches[1]&paged=$matches[2]', 'top' );

    /**
     * @package Calendar
     *
     * Pull up a calendar by user ID
     */

     add_rewrite_rule( '^' . $slug . '/calendar/?$', 'index.php?post_type=portal_projects&portal_calendar_page=home', 'top' );
     add_rewrite_rule( '^' . $slug . '/calendar/([^/]*)/?', 'index.php?post_type=portal_projects&portal_calendar_page=$matches[1]', 'top' );

}

add_filter( 'query_vars', 'portal_archive_query_vars' );
function portal_archive_query_vars( $vars ) {

	$new_vars = apply_filters( 'portal_archive_query_vars', array(
		'portal_calendar_page',
		'portal_user_page',
        'portal_status_page'
	) );

	foreach( $new_vars as $var ) {

		$vars[] = $var;

	}

	return $vars;

}

add_action( 'template_redirect', 'portal_archive_template_redirects', 10 );
function portal_archive_template_redirects( $template ) {

	$archive_templates = apply_filters( 'portal_archive_template_redirects', array(
		'portal_calendar_page',
		'portal_user_page',
	) );

	foreach( $archive_templates as $query_var ) {

		if( get_query_var( $query_var ) && is_post_type_archive( 'portal_projects' ) ) {

			return add_filter( 'template_include', $query_var . '_template_redirect' );

		}

	}

}

function portal_calendar_page_template_redirect() {
    return portal_template_hierarchy( 'dashboard/components/calendar/index.php' );
}

function portal_user_page_template_redirect() {
    return portal_template_hierarchy( 'dashboard/components/user/index.php' );
}

add_action( 'portal_footer', 'portal_check_wp_footer_enabled' );
function portal_check_wp_footer_enabled() {
	if( portal_get_option( 'portal_enable_wp_footer' ) ) wp_footer();
}

add_filter( 'body_class', 'portal_custom_template_class' );
function portal_custom_template_class( $classes ) {

    $post_types = apply_filters( 'portal_post_types', array(
        'portal_projects',
        'portal_team'
    ) );

    if( in_array( get_post_type(), $post_types ) && portal_get_option('portal_use_custom_template') ) {
        $classes[] = 'portal-custom-template';
    }

    return $classes;

}

/**
 * Adds the Task Panel into Single, Dashboard, and Your Tasks Views
 * 
 * @since {{VERSION}}
 * @return void
 */
add_action( 'portal_before_menu', 'portal_add_task_panel' );
function portal_add_task_panel() {
	
	include( portal_template_hierarchy( 'global/task-panel.php' ) );
	
}