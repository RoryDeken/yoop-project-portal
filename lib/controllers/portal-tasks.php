<?php
/**
 * portal-tasks.php
 *
 * Controls tasks and their related needs
 *
 * @category controller
 * @package portal-projects
 * @author Ross Johnson
 * @since 1.3.6
 */

 /**
  * Builds a list of tasks and returns an array of list items and task count
  *
  *
  * @param integer $id post ID
  * @param string $taskStyle (optional) for shortcodes, the type of tasks to return
  * @return array including a collection of tasks in list format and a count of items
  **/

 function portal_populate_tasks( $id , $task_style , $phase_id ) {

 	if( empty( $id ) ) {
 		global $post;
 		$id = $post->ID;
 	}

 	include( portal_template_hierarchy( '/parts/tasks.php' ) );

 	return $taskList;

 }

 function portal_get_task_title_by_key( $task_key = null, $post_id = null ) {

 	if( $task_key == null ) {
        return false;
    }

 	$post_id 	= ( $post_id == null ? get_the_ID() : $post_id );

    $phases = get_field( 'phases', $post_id );

    if( !$phases ) {
        return false;
    }

    foreach( $phases as $phase ) {

        if( !isset($phase['tasks']) || empty($phase['tasks']) ) {
            continue;
        }

        foreach( $phase['tasks'] as $task ) {

            if( $task['task_id'] == $task_key ) {
                return $task['task'];
            }

        }

    }

 	return false;

 }

 /**
  * portal_posts_where_tasks()
  *
  * Custom filter to replace '=' with 'LIKE' in a query so we can query by tasks assigned to a user
  *
  *
  */

 add_filter('posts_where', 'portal_posts_where_tasks');
 function portal_posts_where_tasks( $where ) {

    global $wpdb;

    if( method_exists( $wpdb, 'remove_placeholder_escape') ) {
        $where = str_replace("meta_key = 'tasks_%_assigned'", "meta_key LIKE 'tasks_%_assigned'", $wpdb->remove_placeholder_escape($where) );
    } else {
        $where = str_replace("meta_key = 'tasks_%_assigned'", "meta_key LIKE 'tasks_%_assigned'", $where );
    }

 	return $where;

 }

 /**
  *
  * Function portal_task_table
  *
  * Returns a table of tasks which can be open, complete or all
  *
  * @param $post_id (int), $shortcode (BOOLEAN), $taskStyle (string)
  * @return $output
  *
  */

 function portal_task_table( $post_id, $shortcode = null, $task_style = null ) {

     $output = '
     <table class="portal-task-table">
             <tr>
                 <th class="portal-tt-tasks">' . __( 'Task', 'portal_projects' ) . '</th>
                 <th class="portal-tt-phase">' . __( 'Phase', 'portal_projects') . '</th>';

     if($task_style != 'complete') {
         $output .= '<th class="portal-tt-complete">' . __( 'Completion', 'portal_projects' ) . '</th>';
     }

     $output .= '</tr>';

     while(has_sub_field('phases',$post_id)):

         $phaseTitle = get_sub_field('title');

         while(has_sub_field('tasks',$post_id)):

             $taskCompleted = get_sub_field('status');

             // Continue if you want to show incomplete tasks only and this task is complete
             if( ( $task_style == 'incomplete' ) && ( $taskCompleted == '100' ) ) { continue; }

             // Continue if you want to show completed tasks and this task is not complete
             if( ( $task_style == 'completed' ) && ( $taskCompleted != '100' ) ) { continue; }

             $output .= '<tr><td>'.get_sub_field('task').'</td><td>'.$phaseTitle.'</td>';

             if( $task_style != 'complete') { $output .= '<td><span class="portal-task-bar"><em class="status portal-' . get_sub_field( 'status' ) . '"></em></span></td></tr>'; }

         endwhile;

     endwhile;

     $output .= '</table>';

     return $output;

}

function portal_the_task_heading( $style, $count, $post_id = NULL ) {

    $post_id = ( $post_id == NULL ? get_the_ID() : $post->ID );

	if( get_sub_field( 'tasks', $post_id ) ) {

		if( $style == 'complete' ) {

			$title = '<span>'.$count[1].' '.__('completed tasks').'</span>';

		} elseif ( $style == 'incomplete' ) {

			$title = '<span>'.$count[1].' '.__('open tasks').'</span>';

		} else {

			$remaing_tasks = $tasks - $completed_tasks;

			$taskbar = '<span><b>'.$completed_tasks.'</b> '.__('of','portal_projects').' '.$tasks.' '.__('completed','portal_projects').'</span>';

		}

	} else {

		$taskbar = __( 'None assigned', 'portal_projects');

	}

	return $title;

}

function portal_get_tasks( $post_id, $phase_id, $style = 'all' ) {

	$tasks_array 	=	array();
	$phases 		= 	get_field( 'phases', $post_id );
	$tasks 			= 	$phases[ $phase_id ]['tasks'];

	$count = 0;

	foreach( $tasks as $task ) {

		$completion	= $task[ 'status' ];
		$task['ID'] = $count;

		$count++;

        if( ( $style == 'incomplete' ) && ( $completion == '100' ) ) { continue; }

        // Continue if you want to show completed tasks and this task is not complete
        if( ( $style == 'complete' ) && ( $completion != '100') ) { continue; }

		$tasks_array[] = $task;

	}

	return $tasks_array;

}

function portal_get_task_item_classes( $post_id = null ) {

    $post_id = ( $post_id == NULL ? get_the_ID() : $post_id );
	$classes = '';

	if( ( portal_can_edit_project( $post_id ) ) && ( get_post_type() == 'portal_projects') ) {
		$classes .= 'portal-can-edit ';
	}

	return apply_filters( 'portal_task_item_classes', $classes );

}

function portal_the_task_item_classes( $post_id = NULL ) {

    $post_id = ( $post_id == NULL ? get_the_ID() : $post_id );

	echo portal_get_task_item_classes( $post_id );

}

function portal_get_all_my_tasks() {

	if( !is_user_logged_in() ) return FALSE;

	// Get the current logged in WordPress user object
	$cuser 	= 	wp_get_current_user();
	$tasks	=	array();

	// Query all the projects where this user has been assigned a task
	$args = array(
		'post_type'		  => 'portal_projects',
		'posts_per_page'  => -1,
		'tax_query' 	  => array(
				array(
					'taxonomy'	=>	'portal_status',
					'field'		=>	'slug',
					'terms'		=>	'completed',
					'operator'	=>	'NOT IN'
				)
		),
        'meta_query' 	=> array(
            'key'         => '%_assigned',
            'value'     => $cuser->ID,
        )
	);

	$args = apply_filters( 'portal_get_all_my_tasks_args', $args );

	// Query with the above arguments
	$projects 	= new WP_Query($args);

	while( $projects->have_posts()): $projects->the_post();

		global $post;
		$phases 		= array();
		$task_id 		= 0;
		$phase_count 	= 0;

		while( have_rows( 'phases' ) ) {

			$phase = the_row();

			$phase_name		=	get_sub_field( 'title' );
			$phase_tasks	=	array();

			while( have_rows( 'tasks' ) ) {

				$task = the_row();

				if ( get_sub_field( 'assigned' ) == $cuser->ID ) {
					$phase_tasks[] = array(
						'task'		=>	get_sub_field( 'task' ),
						'status'	=>	get_sub_field( 'status' )
					);
				}

				// Allows adding to $phase_tasks by reference
				do_action_ref_array( 'portal_get_all_my_tasks_loop', array( &$phase_tasks, $phase, $task ) );

			}

			if( !empty( $phase_tasks ) ) {
				$phases[] = array(
					'phase'		=>	get_sub_field( 'title' ),
					'tasks'		=>	$phase_tasks
				);
			}

		}

		if( !empty( $phases ) ) {
			$tasks[] = array(
				'project_id'	=>		$post->ID,
				'project_name'	=>		get_the_title( $post->ID ),
				'phases'		=>		$phases
			);
		}

	endwhile;

	return $tasks;

}

function portal_get_status_percentages() {

    return apply_filters( 'portal_status_percentages', array(
        '0'     =>  '0%',
        '5'     =>  '5%',
        '10'    =>  '10%',
        '15'    =>  '15%',
        '20'    =>  '20%',
        '25'    =>  '25%',
        '30'    =>  '30%',
        '35'    =>  '35%',
        '40'    =>  '40%',
        '45'    =>  '45%',
        '50'    =>  '50%',
        '55'    =>  '55%',
        '60'    =>  '60%',
        '65'    =>  '65%',
        '70'    =>  '70%',
        '75'    =>  '75%',
        '80'    =>  '80%',
        '85'    =>  '85%',
        '90'    =>  '95%',
        '100'   =>  '100%'
    ) );

}

function portal_find_tasks_by_due_date( $date = NULL, $notification = NULL ) {

    $tasks      = array();
    $date 		= ( $date == NULL ? date('Ymd') : $date );

    global $wpdb;

    $rows = $wpdb->get_results($wpdb->prepare(
            "
            SELECT *
            FROM {$wpdb->prefix}postmeta
            WHERE meta_key LIKE %s
                AND meta_value = %s
            ",
            'phases_%_tasks_%_due_date', // meta_name: $ParentName_$RowNumber_$ChildName
            $date // meta_value: 'type_3' for example
        ) );

    if( !$rows ) return false;

    foreach( $rows as $row ) {

        // Skip any unpublished projects
        if( get_post_status( $row->post_id ) != 'publish' ) continue;

        preg_match_all( '_([0-9]+)_', $row->meta_key, $matches );

        $phase_id   = $matches[0][0];
        $task_id    = $matches[0][1];

        $phases = get_field( 'phases', $row->post_id );

        // Skip any completed tasks
        if( $phases[$phase_id]['tasks'][$task_id]['status'] == 100 ) continue;

        // Confirm the task is assigned to the current user
        if( $phases[$phase_id]['tasks'][$task_id]['due_date'] != $date ) continue;

        $this_task = array(
            'name'          =>  $phases[$phase_id]['tasks'][$task_id]['task'],
            'assigned'      =>  $phases[$phase_id]['tasks'][$task_id]['assigned'],
            'task_id'       =>  $task_id,
            'phase_id'      =>  $phase_id,
            'post_id'       =>  $row->post_id,
            'project_id'    =>  $row->post_id,
            'phase'         =>  $phases[$phase_id]['title'],
            'due_date'      =>  $phases[$phase_id]['tasks'][$task_id]['due_date'],
            'status'        =>  $phases[$phase_id]['tasks'][$task_id]['status'],
            'user_id'       =>  $phases[$phase_id]['tasks'][$task_id]['assigned'],
        );

        $tasks[] = $this_task;

        // If this is a notification, send it
        if( $notification ) do_action( 'portal_notify', $notification, $this_task );

    }

    return $tasks;

}

add_filter( 'acf/save_post', 'portal_notify_new_task_assignment', 9, 3 );
function portal_notify_new_task_assignment( $post_id ) {


    // Skip if not a portal Project
    if( get_post_type($post_id) != 'portal_projects' ) return;


    /*
     * Reasons to skip - no fields, no phases
     */
    if( !isset( $_POST['fields']) || empty( $_POST['fields']) || !isset($_POST['fields']['field_527d5dc12fa29']) ) return;

    $notifications  = array();
    $old_phases     = get_field( 'phases', $post_id );
    $phases         = $_POST['fields']['field_527d5dc12fa29'];

    $tasks = array();

    // Loop through each phase and then each task within a phase
    $phase_id = 0;
    foreach( $phases as $phase ) {

        $task_id = 0;

        if( empty( $phase['field_527d5dfd2fa2d'] ) ) continue;

        // Loop through each task to try and find new tasks or assignment switches
        foreach( $phase['field_527d5dfd2fa2d'] as $task ) {

            $do_notify = true;

            // If this task isn't assigned to anyone, continue
            if( !isset($task['field_532b8da69c46e'] ) || empty($task['field_532b8da69c46e']) || $task['field_532b8da69c46e'] == 'unassigned' ) $do_notify = false;

            $tasks[] = $task;

            if( $old_phases ): foreach( $old_phases as $old_phase ):

                    if( $old_phase['phase_id'] != $phase['portal_phase_id'] ) continue;

                    foreach( $old_phase['tasks'] as $old_task ) {
                        if( $old_task['task'] == $task['field_527d5e072fa2e'] && $old_task['assigned'] == $task['field_532b8da69c46e'] ) $do_notify = false;
                    }

            endforeach; endif;

            if( $do_notify ) {

                /**
                 * Create a space for this users notifications if they haven't already been set
                 *
                 */
                if( !isset( $notifications[$task['field_532b8da69c46e']] ) ) {
                    $notifications[$task['field_532b8da69c46e']] = array(
                        'post_id'       =>  $post_id,
                        'project_id'    =>  $post_id,
                        'user_id'       =>  $task['field_532b8da69c46e'],
                        'user_ids'      =>  array( $task['field_532b8da69c46e'] ),
                        'phases'        =>  array()
                    );
                }

                /**
                 * Populate the phase information if it doesn't already exist
                 *
                 */
                if( !isset( $notifications[$task['field_532b8da69c46e']]['phases'][$phase_id] ) ) {
                    $notifications[$task['field_532b8da69c46e']]['phases'][$phase_id] = array(
                        'phase_title'   =>  $phase['field_527d5dd02fa2a']
                    );
                }

                /**
                 * Add the task to the phase for notification
                 *
                 */
                $notifications[$task['field_532b8da69c46e']]['phases'][$phase_id]['tasks'][] = array(
                    'name'          =>  $task['field_527d5e072fa2e'],
                    'task_id'       =>  $task_id,
                    'due_date'      =>  $task['portal_task_due_date'],
                    'status'        =>  $task['field_527d5e0e2fa2f'],
                );

            }

            $task_id++;

        }

        $phase_id++;

    }

    if( !empty($notifications) ) {
        foreach( $notifications as $notification ) do_action( 'portal_notify', 'task_assigned', $notification );
    }

}
