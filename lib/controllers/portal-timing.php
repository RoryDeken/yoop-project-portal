<?php
/**
 * portal-timing.php
 *
 * Controlls all things related to timing.
 *
 * @category controller
 * @package portal-projects
 * @author Ross Johnson
 * @since 1.3.6
 */

/**
 * Get the projects start date
 * @param  [date string] $format  optional start date
 * @param  [int] $post_id projec tID
 * @return formated date
 */
function portal_get_the_start_date( $format = NULL, $post_id = NULL) {

	$post_id 	= ( $post_id == NULL ? get_the_ID() : $post_id );
	$start_date = get_field( 'start_date', $post_id );

	if( !$start_date ) return false;

	$format = ( $format == NULL ? get_option( 'date_format' ) : $format );

	return apply_filters( 'portal_start_date', date_i18n( $format, strtotime($start_date) ), $post_id, $format );

}

function portal_get_the_end_date( $format = NULL, $post_id = NULL) {

	$post_id 	= ( $post_id == NULL ? get_the_ID() : $post_id );
	$end_date 	= get_field( 'end_date', $post_id );

	if( !$end_date ) return false;

	$format = ( $format == NULL ? get_option( 'date_format' ) : $format );

	return apply_filters( 'portal_end_date', date_i18n( $format, strtotime($end_date) ), $post_id, $format );

}

function portal_the_start_date( $post_id = NULL ) {

	global $post;

	$post_id = ( !empty( $post_id ) ? $post_id : $post->ID );

    $months = array(
		__('Jan','portal_projects'),
		__('Feb','portal_projects'),
		__('Mar','portal_projects'),
		__('Apr','portal_projects'),
		__('May','portal_projects'),
		__('Jun','portal_projects'),
		__('Jul','portal_projects'),
		__('Aug','portal_projects'),
		__('Sep','portal_projects'),
		__('Oct','portal_projects'),
		__('Nov','portal_projects'),
		__('Dec','portal_projects')
		);

    $startDate 	= apply_filters( 'portal_the_start_date_cal', get_field( 'start_date', $post_id ), $post_id );

    $s_year 	= substr( $startDate, 0, 4 );
    $s_month 	= substr( $startDate, 4, 2 );
    $s_day 		= substr( $startDate, 6, 2 );

	if( !empty( $startDate ) ): ?>

	    <div class="portal-date">
	        <span class="cal">
	            <span class="month"><?php echo $months[$s_month - 1]; ?></span>
	            <span class="day"><?php echo $s_day; ?></span>
	        </span>
	        <b><?php echo $s_year; ?></b>
	    </div>

	    <?php
		endif;

}

function portal_the_end_date( $post_id ) {

	global $post;

	$post_id = ( !empty( $post_id ) ? $post_id : $post->ID );

	$months = array(
		__('Jan','portal_projects'),
		__('Feb','portal_projects'),
		__('Mar','portal_projects'),
		__('Apr','portal_projects'),
		__('May','portal_projects'),
		__('Jun','portal_projects'),
		__('Jul','portal_projects'),
		__('Aug','portal_projects'),
		__('Sep','portal_projects'),
		__('Oct','portal_projects'),
		__('Nov','portal_projects'),
		__('Dec','portal_projects')
		);

    $endDate = apply_filters( 'portal_the_end_date_cal', get_field( 'end_date', $post_id ), $post_id );

	if( !empty( $endDate ) ):

	    $e_year 	= substr($endDate,0,4);
	    $e_month 	= substr($endDate,4,2);
	    $e_day 		= substr($endDate,6,2); ?>

	    <div class="portal-date">
	        <span class="cal">
		        <span class="month"><?php echo $months[$e_month - 1]; ?></span>
			    <span class="day"><?php echo $e_day; ?></span>
	    	</span>
	        <b><?php echo $e_year; ?></b>
	    </div>

    <?php
	endif;

}

function portal_text_date( $date ) {

	$date 	= strtotime( $date );
	$format = get_option( 'date_format' );

	if( empty( $date ) ) return false;

	return date_i18n( $format, $date );

}

function portal_the_timebar( $id ) {

    $startDate 	= get_field( 'start_date', $id );
    $endDate 	= get_field( 'end_date', $id );

	if( ( empty( $startDate ) ) || ( empty( $endDate ) ) ) { return; }

    $s_year 	= substr( $startDate, 0, 4 );
    $s_month 	= substr( $startDate, 4, 2 );
    $s_day 		= substr( $startDate, 6, 2 );

    $e_year 	= substr( $endDate, 0, 4 );
    $e_month 	= substr( $endDate, 4, 2 );
    $e_day 		= substr( $endDate, 6, 2 );

    $textStartDate 	= portal_text_date( $startDate );
    $textEndDate 	= portal_text_date( $endDate );

    $all_time 			= portal_calculate_timing( $id );
	$project_completion = portal_compute_progress( $id );

    if( $all_time[ 'percentage_complete' ] < 0 ) {
        $all_time[ 'percentage_complete' ] = 100;
    }

	$marks = array( 10, 20, 30, 40, 50, 60, 70, 80, 90 );

	$progress_class = portal_get_the_schedule_status_class( $all_time[ 'percentage_complete' ], $project_completion ); ?>

	 	<div class="portal-timebar">

		 <?php
		 if( $all_time[ 'days_ellapsed' ] > $all_time[ 'total_days' ] ) {
		 	$days_left = ' <span class="portal-time-details">' . $all_time[ 'days_ellapsed' ] . __( 'days past project end date.', 'portal_projects' ) . '</span>';
	   	 } else {
		 	$days_left = ' <span class="portal-time-details">' . $all_time[ 'days_ellapsed' ] . __( 'days remaining', 'portal_projects' ) . '</span>';
	 	 } ?>

    		 <p class="portal-time-start-end"><?php echo $textStartDate; ?> <span><?php echo $textEndDate; ?></span></p>

    		 <div class="portal-time-progress">

       		  	<p class="portal-time-bar <?php echo esc_attr( $progress_class ); ?>"><span class="portal-<?php echo $all_time[ 'percentage_complete' ]; ?>"></span></p>

      			<ol class="portal-time-ticks <?php echo esc_attr( $progress_class ); ?>">
					<?php foreach( $marks as $mark ): ?>
						<li class="portal-tt-<?php echo esc_attr( $mark ); ?> <?php if( $all_time[ 'percentage_complete' ] >= $mark ) { echo esc_attr( 'active' ); } ?>"></li>
					<?php endforeach; ?>
        		</ol>

        		<span class="portal-time-indicator <?php echo esc_attr( $progress_class ); ?>" style="left: <?php echo $all_time[ 'percentage_complete' ]; ?>%"><span></span><?php echo $all_time[ 'percentage_complete' ]; ?>%</span>

   	 	  </div> <!--/.portal-time-progress-->

	</div> <!--/.portal-timebar-->

	<?php

}

function portal_the_simplified_timebar( $post_id ) {

    $start_date = get_field( 'start_date', $post_id );
    $end_date 	= get_field( 'end_date', $post_id );

	if( empty($start_date) || empty($end_date) ) return;

	$start = array(
		'year'	=>	substr( $start_date, 0, 4 ),
		'month'	=>	substr( $start_date, 4, 2 ),
		'day'	=>	substr( $start_date, 6, 2 )
	);

	$end = array(
		'year'	=>	substr( $end_date, 0, 4 ),
		'month'	=>	substr( $end_date, 4, 2 ),
		'day'	=>	substr( $end_date, 6, 2 )
	);

    $all_time 			= portal_calculate_timing( $post_id );
	$project_completion = portal_compute_progress( $post_id );

    if( $all_time['percentage_complete'] < 0 ) {
        $all_time['percentage_complete'] = 100;
    }

	$progress_class = portal_get_the_schedule_status_class( $all_time['percentage_complete'], $project_completion ); ?>

 	<div class="portal-simplified-timebar">
		<p class="portal-tb-progress <?php echo esc_attr($progress_class); ?>">
			<span class="portal-<?php echo esc_attr($all_time['percentage_complete']); ?>" data-toggle="portal-tooltip" data-placement="top" title="<?php echo esc_attr($all_time['percentage_complete'] . '% ' . __( 'Time Ellapsed', 'portal_projects' )); ?>">
				<b><?php echo esc_html($all_time['percentage_complete']); ?>%</b>
			</span>
			<i class="portal-progress-label"> <?php esc_html_e('Timing','portal_projects'); ?> </i>
		</p>
	</div>

	<?php

}

function portal_calculate_timing( $post_id = NULL ) {

	$post_id = ( $post_id == NULL ? get_the_ID() : $post_id );

    $start_date 	= get_field( 'start_date', $post_id);
    $end_date 		= get_field( 'end_date', $post_id);

    $today 		= time();
    $s_year 	= substr( $start_date, 0, 4 );
    $s_month 	= substr( $start_date, 4, 2 );
    $s_day 		= substr( $start_date, 6, 2 );

    $e_year 	= substr( $end_date, 0, 4 );
    $e_month 	= substr( $end_date, 4, 2 );
    $e_day 		= substr( $end_date, 6, 2 );

    $start_date = strtotime( $s_year . '-' . $s_month . '-' . $s_day );
    $end_date 	= strtotime( $e_year . '-' . $e_month . '-' . $e_day );

    $total_days = abs( $start_date - $end_date);
    $total_days = floor( $total_days / ( 60 * 60 * 24 ) );

    $datediff 	= abs( $today - $end_date );

    $time_completed = floor( $datediff / ( 60 * 60 * 24 ) );

	if( $start_date > $today ) {
        $time_percentage = 0;
    } elseif( $end_date < $today || $total_days == 0 ) {
        $time_percentage = 100;
	} else {
	    $time_percentage = floor( 100 - ( $time_completed / $total_days * 100 ) );
	}

	// Check to make sure we don't round up unintentionally
	if( $time_percentage == 100 && $end_date > $today ) { $time_percentage = 99; }

    $all_time = array( 'percentage_complete' => $time_percentage, 'total_days' => $total_days, 'days_ellapsed' => $time_completed );

    return apply_filters( 'portal_calculate_timing', $all_time, $post_id );

}

function portal_verbal_status( $all_time, $calc_completed ) {

    if($all_time[ 'percentage_complete' ] > $calc_completed) { return 'behind'; } else { return 'time'; }

}

function portal_the_timing_bar( $post_id ) {

    $time_elapsed 	= portal_calculate_timing( $post_id );
    $completed 		= portal_compute_progress( $post_id );

	$progress_class = ( $completed < $time_elapsed[ 'percentage_complete' ] ? 'portal-behind' : 'portal-ontime' );

    if( $time_elapsed[ 'percentage_complete' ] < 0 ) {
        $time_elapsed[ 'percentage_complete' ] = 100;
    }

    echo '<p class="portal-timing-progress portal-progress ' . $progress_class . '"><span class="portal-' . $time_elapsed[ 'percentage_complete' ] . '"><strong>%' . $time_elapsed[ 'percentage_complete' ] . '</strong></span></p>';

}

function portal_output_project_calendar( $user_id = null ) {

	$cuser 		= wp_get_current_user();
	$user_id 	= ( $user_id == null ? $cuser->ID : $user_id );

	if(!is_admin()) {
		// portal_enqueue_calendar_assets();
	}

	$date_url 	= ( get_option( 'permalink_structure' ) ? home_url() . '/portal-dates/' . $user_id . '/' : home_url() . '/index.php?portal_dates=' . $user_id );
	$hashed_id	=	portal_get_hashed_id( wp_get_current_user() );

	ob_start(); ?>

		<div id="portal-project-calendar"></div>

		<p><a class="portal-ical-link" href="<?php echo portal_get_ical_link(); ?>" target="_new"><?php echo esc_html_e( 'iCal Feed', 'portal_projects' ); ?></a></p>

		<script>

			// Fixes odd problem with calendar on-load
			// https://stackoverflow.com/a/22723412
			( function( $ ) {

				$( window ).on( 'load', function() {

					$('#portal-project-calendar').fullCalendar({
						events: '<?php echo esc_url($date_url); ?>',
						<?php if( portal_get_option( 'portal_calendar_language' ) ) { ?>
							lang: '<?php echo portal_get_option( 'portal_calendar_language' ); ?>',
						<?php } ?>
						eventRender: function(event, element) {
							element.popover({
								animation	: false,
								title		: event.title,
								placement	: 'left',
								trigger		: 'hover',
								content		: event.description,
								container	: '#portal-project-calendar',
								html		: true
							});
							element.hover(function() {
								$(this).css( 'zIndex', 1001 );
								var parents = $(this).parents('.fc-row .fc-content-skeleton');
								$(element).parents('.fc-row').css( 'zIndex', 1000 );
							},function(){
								$(element).parents('.fc-row').css( 'zIndex', 1 );
							});
						}
					});

				} );

			} )( jQuery );

		</script>

	<?php
	return ob_get_clean();

}

// Add a rewrite rule to output JSON
add_action( 'init', 'portal_calendar_rewrite_rules' );
function portal_calendar_rewrite_rules() {

	global $wp_rewrite;

	$slug 	= portal_get_slug();
	$front 	= '';

	if( isset( $wp_rewrite->front ) ) $slug 	= substr( $wp_rewrite->front, 1 ) . $slug;

	/**
	 * Allow the user ID to be passed in to create a JSON feed or iCAL feed
	 */
	add_rewrite_tag( '%portal_dates%', '([^&]+)' );
	add_rewrite_rule( '^portal-dates/([^&]+)/?', 'index.php?portal_dates=$matches[1]', 'top' );

	/**
	 * Setup a page to just output the calendar
	 */
	add_rewrite_rule( '^' . $slug . '/calendar/([^/]*)/?', 'index.php?post_type=portal_projects&portal_calendar_page=$matches[1]', 'top' );
	add_rewrite_rule( '^' . $slug . '/ical/([^/]*)/?', 'index.php?post_type=portal_projects&portal_ical_page=$matches[1]', 'top' );

}

add_filter( 'query_vars', 'portal_calendar_page_query_vars' );
function portal_calendar_page_query_vars( $vars ) {

	$vars[] = 'portal_calendar_page';
	$vars[] = 'portal_ical_page';

	return $vars;

}

add_action( 'template_redirect', 'portal_dates_endpoint_data' );
function portal_dates_endpoint_data() {

    global $wp_query;

    $date_tag 	= $wp_query->get( 'portal_dates' );
 	$cuser 		= wp_get_current_user();

    if ( ( ! $date_tag ) || ( $date_tag != $cuser->ID ) ) return;

	$meta_query	= portal_access_meta_query( $cuser->ID );
    $date_data 	= array();

	// Update to make more custom

    $args = array(
        'post_type'      => 'portal_projects',
        'posts_per_page' => -1,
    );

    if( ! current_user_can('delete_others_portal_projects') ) {

        $meta_args = array(
            'meta_query' 	=> $meta_query,
			'has_password' 	=> false
        );
		$args = array_merge( $args, $meta_args );

	}

    $projects = new WP_Query( $args );

	$date_data = portal_get_project_dates($projects);

    wp_send_json( apply_filters( 'portal_date_data_json', $date_data, $projects ) );

}

function portal_get_project_dates( $projects = NULL ) {

	if( $projects == NULL ) return false;

	$date_data = array();

	if( $projects->have_posts() ): while( $projects->have_posts() ): $projects->the_post();

		global $post;

		/* Start and end dates */
		if( get_field( 'start_date' ) || get_field( 'end_date' ) ) {

			$start_date 	= get_field( 'start_date' );
			$end_date 		= get_field( 'end_date' );
			$title 			= get_the_title();

			$s_year 	= substr( $start_date, 0, 4 );
			$s_month 	= substr( $start_date, 4, 2 );
			$s_day 		= substr( $start_date, 6, 2 );

			$e_year 	= substr( $end_date, 0, 4 );
			$e_month 	= substr( $end_date, 4, 2 );
			$e_day 		= substr( $end_date, 6, 2 );

			if( $start_date ) {
				$date_data[] = apply_filters( 'portal_project_start_ical_date', array(
					'title'  		=>  __( 'Project Start', 'portal_projects' ) . ' ' . html_entity_decode(get_the_title()),
					'start'			=>	$s_year . '-' . $s_month . '-' . $s_day,
					'url' 			=> 	get_permalink(),
					'description' 	=>  '<h3>' . esc_html(get_the_title()) . '</h3>' . ' - ' . esc_html(get_field('client')),
					'ical_desc'		=>	esc_html(get_the_title()) . ' - ' . __( 'Client:', 'portal_project' ) . esc_html(get_field('client')),
					'color'			=>	apply_filters( 'portal_calendar_start_date', '#3299BB' ),
					'ID'			=>	$post->ID
				), $post->ID );
			}

			if( $end_date ) {
				$date_data[] = apply_filters( 'portal_project_end_ical_date', array(
					'title'  		=> 	__( 'Project End', 'portal_projects' ) . ' ' . html_entity_decode(get_the_title()),
					'start'			=>	$e_year . '-' . $e_month . '-' . $e_day,
					'url' 			=> 	get_permalink(),
					'description' 	=> '<h3>' . get_the_title() . '</h3>' . ' - ' . get_field('client'),
					'ical_desc'		=>	get_the_title() . ' - ' . __( 'Client:', 'portal_project' ) . get_field('client'),
					'color'			=>	apply_filters( 'portal_calendar_end_date', '#C44D58' ),
					'ID'			=>	$post->ID
				), $post->ID );
			}

		}

		/**
		 * Milestones
		 */

		if( get_field( 'milestones', $post->ID ) ) {
			while( have_rows( 'milestones', $post->ID ) ) { the_row();

				if( !get_sub_field( 'date' ) ) continue;

				$date	=	strtotime( get_sub_field( 'date' ) );
				$date	=	date( 'Y-m-d', $date );

				$date_data[] = apply_filters( 'portal_milestone_ical_date', array(
					'title'			=>		__( 'Project Milestone', 'portal_projects' ),
					'start'			=>		$date,
					'url'			=>		get_permalink( $post->ID ),
					'description'	=>		'<h3>' . get_sub_field( 'title' ) . '</h3><br><p><strong>' . __( 'Project:', 'portal_projects' ) . '</strong> ' . get_the_title( $post->ID ) . '</p><p><strong>' . __( 'Client:', 'portal_projects' ) . '</strong> ' . get_field( 'client', $post->ID ) . '</p>',
					'ical_desc'		=>		__('Milestone: ','portal_project') . get_sub_field('title') . "\n" . __('Client:', 'portal_projects') . get_field('client', $post->ID),
					'color'			=>		'#2a3542',
					'ID'			=>		$post->ID
				), $post->ID );

			}
		}

		/**
		 * Tasks
		 */

		if( !get_field( 'phases', $post->ID ) ) continue;

		while( have_rows( 'phases', $post->ID ) ) { the_row();

			if( !get_sub_field( 'tasks' ) ) continue;

			while( have_rows( 'tasks' ) ) { the_row();

				if( !get_sub_field('due_date') || get_sub_field('status') == '100' ) {
					continue;
				}

				$date 	= strtotime( get_sub_field( 'due_date' ) );
				$date	= date( 'Y-m-d', $date );

				$date_data[] = apply_filters( 'portal_task_ical_date', array(
					'title'			=>		__( 'Task Due', 'portal_projects' ),
					'start'			=>		$date,
					'url'			=>		get_permalink( $post->ID ),
					'description'	=>		'<h3>' . get_sub_field( 'task' ) . ' - ' . get_sub_field( 'status' ) . '%</h3><br><p><strong>' . __( 'Project:', 'portal_projects' ) . '</strong> ' . get_the_title( $post->ID ) . '</p><p><strong>' . __( 'Client:', 'portal_projects' ) . '</strong> ' . get_field( 'client', $post->ID ) . '</p>',
					'ical_desc'		=>		__( 'Task', 'portal_projects' ) . ": " . get_sub_field( 'task' ) . "(" . get_sub_field('status') . "%)\n" . __( 'Project', 'portal_projects' ) . ": " . get_the_title($post->ID) . "\n" . __('Client', 'portal_projects') . ": " . get_field('client', $post->ID ),
					'color'			=>		'#99c262',
					'ID'			=>		$post->ID
				), $post->ID );

			}

		}

	endwhile; wp_reset_postdata();

	else:

		return false;

	endif;

	return $date_data;


}

add_action('admin_menu', 'portal_add_calendar_page');
function portal_add_calendar_page() {

	global $portal_add_calendar_page;

	$portal_add_calendar_page = add_submenu_page( 'edit.php?post_type=portal_projects','Project Calendar', __('Calendar', 'portal_projects'), 'manage_options', 'yoop-calendar', 'portal_project_calendar_page' );

}

function portal_project_calendar_page() { ?>

	<div class="wrap">

		<h1><?php _e('Project Calendar','portal_projects'); ?></h1>

		<br>

        <?php echo portal_output_project_calendar(); ?>

	</div>

	<?php
}

add_shortcode( 'portal_my_calendar', 'portal_my_calendar_shortcode' );
function portal_my_calendar_shortcode() {

	portal_enqueue_calendar_assets();

	ob_start();

	echo portal_output_project_calendar();

	return ob_get_clean();

}

function portal_project_schedule_status( $time_ellapsed, $project_progress ) {

	if( $time_ellapsed > $project_progress ) {

		$status = 'behind';

	} else {

		$status = 'ontime';

	}

	return apply_filters( 'portal_project_schedule_status', $status, $time_ellapsed, $project_progress );

}

function portal_get_the_schedule_status_class( $time_ellapsed, $project_progress ) {

	return apply_filters( 'portal_schedule_status_class', 'portal-' . portal_project_schedule_status( $time_ellapsed, $project_progress ), $time_ellapsed, $project_progress );

}

function portal_the_milestone_due_date( $date ) {

	$date 	= strtotime( $date );
	$format = get_option( 'date_format' );

	$date_class = ( $date < strtotime( 'today' ) ? 'late' : '' );

	echo '<b class="portal-mm-marker-date ' . $date_class . '">' . date_i18n( $format, $date ) . '</b>';

}


function portal_late_class( $date = NULL ) {

    if( empty( $date ) )
        return;

    $date = strtotime( $date );

	return ( $date < strtotime( 'today' ) ? 'late' : '' );

}

function portal_get_ical_link() {

	$hashed_id 	= portal_get_hashed_id( wp_get_current_user() );
	$link 		= portal_strip_http( get_post_type_archive_link('portal_projects') . 'ical/' . $hashed_id );

	return 'webcal://' . $link;

}
