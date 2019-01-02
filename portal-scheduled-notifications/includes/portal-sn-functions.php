<?php
/**
 * Provides helper functions.
 *
 * @since      0.1.0
 *
 * @package    ProjectyoopSN
 * @subpackage ProjectyoopSN/core
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Returns the main plugin object
 *
 * @since 0.1.0
 *
 * @return Project_yoop_SN
 */
function portal_scheduled_notifications() {
	return Project_yoop_SN::getInstance();
}

/**
 * Retrieve posts of a given type, returned as key/value pairs of ID and title
 *
 * @since 1.0
 * @param $type
 *
 * @return array
 */
function portal_sn_get_posts( $type ) {
	$args = array(
		'post_type' => $type,
		'numberposts' => -1,
	);
	$notifications = get_posts( $args );
	$ids = array();

	if ( $notifications ) {
		foreach ( $notifications as $notification ) {
			$ids[$notification->ID] = $notification->post_title;
		}
	}
	return $ids;
}

/**
 * Adds new intervals for global WP Cron usage
 *
 * @since 1.0
 * @param $intervals
 *
 * @return array
 */
function portal_sn_cron_intervals( $intervals ) {
	$intervals['weekly'] = array(
		'interval' => WEEK_IN_SECONDS,
		'display'  => esc_html__( __( 'Every Week', 'portal_sn' ) ),
	);
	$intervals['biweekly'] = array(
		'interval' => 2 * WEEK_IN_SECONDS,
		'display'  => esc_html__( __( 'Every Other Week', 'portal_sn' ) ),
	);
	$intervals['monthly'] = array(
		'interval' => MONTH_IN_SECONDS,
		'display'  => esc_html__( __( 'Every Month', 'portal_sn' ) ),
	);

	return $intervals;
}

/**
 * When the settings for this plugin are saved, this function adds or updates any relevant WP Cron events
 *
 * @since 1.0
 * @param $meta_id
 * @param $object_id
 * @param $meta_key
 * @param $meta_value
 */
function portal_sn_save_cron( $meta_id, $object_id, $meta_key, $meta_value ) {

	if ( $meta_key == 'portal_sn_feed_notification' ) {

		$args = array(
			'id' => $object_id,
		);
		$interval = $meta_value;

		if ( ! wp_next_scheduled( 'portal_scheduled_notifications', $args ) ) {
			// Add the event if it isn't scheduled
			wp_schedule_event( time(), $interval, 'portal_scheduled_notifications', $args );
		} else {
			// Change the interval for an event
			$timestamp = wp_next_scheduled( 'portal_scheduled_notifications', $args );
			wp_unschedule_event( $timestamp, 'portal_scheduled_notifications', $args );
			wp_schedule_event( time(), $interval, 'portal_scheduled_notifications', $args );
		}
	}
}

/**
 * Fires when the plugin is deactivated or when a notification is deleted
 *
 * @since 1.0
 * @param int $notification_id
 */
function portal_sn_remove_cron( $notification_id = 0 ) {
	$args = array();
	// Use the 0 default to clear all. Called on plugin deactivation.
	if ( $notification_id == 0 ) {
		wp_unschedule_hook( 'portal_scheduled_notifications' );
	} elseif ( 'portal-sn-feed' == get_post_type( $notification_id ) ) {
		// Delete single notification from scheduled events
		$args['id'] = (int)$notification_id;
		$timestamp = wp_next_scheduled( 'portal_scheduled_notifications', $args );
		wp_unschedule_event( $timestamp, 'portal_scheduled_notifications', $args );
	}
}

/**
 * Sends the email on the cron hook if there are saved notifications
 *
 * @since 1.0
 */
function portal_sn_send_email() {

	$saved_notifications = portal_sn_get_posts( 'portal-sn-feed' );
	$notification_type = 'scheduled_notification';

	if ( $saved_notifications ) {
		foreach ( $saved_notifications as $id => $title ) {
			$scheduled = wp_next_scheduled( 'portal_scheduled_notifications', array( 'id' => $id ) );
			if ( $scheduled !== false ) {
				$subject = stripslashes( get_post_meta( $id, 'portal_sn_feed_message_subject', true ) );
				$message = stripslashes( get_post_meta( $id, 'portal_sn_feed_message_text', true ) );
				$project_id = get_post_meta( $id, 'portal_sn_feed_project', true );
				// Get all the comma separated emails and convert to an array
				$recipients = get_post_meta( $id, 'portal_sn_feed_recipients', true );
				$recipients = explode( ',', $recipients );
				$args = array(
					'recipient_name'  => '',
					'subject'         => $subject,
					'progress'		  => '',
					'message'         => $message,
					'post_id'		  => $project_id,
					'project_id'	  => $project_id,
				);
				$replacements = portal_notifications_replacements(
					array(
						'message'    => $args['message'],
						'subject'   => $args['subject'],
					),
					$notification_type,
					$args
				);

				$args['message']    = '<h1 style="text-align: center">' . $replacements['subject'] . '</h1>' . $replacements['message'];
				$args['subject']   = $replacements['subject'];

				if ( $recipients ) {
					// Loop through the comma separated emails and send to each
					foreach ( $recipients as $recipient ) {
						$args['recipient_email'] = $recipient;
						portal_send_email( array(), $args, $notification_type );
					}
				}
			}
		}
	}
}

/**
 * Introduces a handful of new string replacements for yoop email body and subject
 *
 * @since 1.0
 * @param $replacements
 * @param $notification_type
 * @param $args
 * @param $notification_ID
 *
 * @return array
 */
function portal_sn_add_new_replacements( $replacements, $notification_type, $args, $notification_ID ) {

	$project_id = $args['project_id'];
	$progress = get_post_meta( $project_id, 'percent_complete', true );
	$description = get_post_meta( $project_id, 'project_description', true );
	$start = portal_get_the_start_date( null, $project_id );
	$end = portal_get_the_end_date( null, $project_id );
	$tasks_completed = portal_sn_tasks_table( $project_id, 'complete' );
	$tasks_incomplete = portal_sn_tasks_table( $project_id, 'incomplete' );
	$tasks_all = portal_sn_tasks_table( $project_id );
	$milestones = portal_sn_milestones_list( $project_id );

	$replacements['%project_title%'] = get_the_title( $project_id );
	$replacements['%progress%'] = $progress . '%';
	$replacements['%project_description%'] = $description;
	$replacements['%project_start%'] = $start;
	$replacements['%project_end%'] = $end;
	$replacements['%tasks_table_complete%'] = $tasks_completed;
	$replacements['%tasks_table_incomplete%'] = $tasks_incomplete;
	$replacements['%tasks_table_all%'] = $tasks_all;
	$replacements['%milestones_list%'] = $milestones;

	return $replacements;
}

/**
 * Renders a table of tasks grouped by phase
 *
 * @since 1.0
 * @param $project_id
 * @param string $condition
 *
 * @return string
 */
function portal_sn_tasks_table( $project_id, $condition = 'all' ) {

	$output = '';

	if ( have_rows('phases', $project_id ) ) {
		while ( have_rows('phases', $project_id ) ) : the_row();
			$phase_title = get_sub_field( 'title' );
			$output .= "
			<h2>" . $phase_title . "</h2>
			<table style='width:100%;'>
				<tr style='text-align: left;'>
					<th>" . __( 'Task', 'portal_sn' ) . "</th>
					<th>" . __( 'Assignee', 'portal_sn' ) . "</th>
					<th>" . __( 'Completion', 'portal_sn' ) . "</th>
					<th>" . __( 'Due date', 'portal_sn' ) . "</th>
				</tr>
				";
			if ( have_rows('tasks', $project_id ) ) {
				$tasks = array();
				while ( have_rows('tasks', $project_id ) ) : the_row();

					$status = get_sub_field('status');
					// Only add task if complete requested and task is complete or vice versa
					if( ( $condition == 'incomplete' ) && ( $status == '100' ) ) { continue; }
					if( ( $condition == 'complete' ) && ( $status != '100' ) ) { continue; }
					$assignee = get_userdata( get_sub_field( 'assigned' ) );
					$tasks[] = array(
						'due' => get_sub_field( 'due_date' ),
						'title' => get_sub_field( 'task' ),
						'status' => $status,
						'assignee' => $assignee->display_name,
					);

				endwhile;
				if ( $tasks ) {
					// Since the date is the first key in the array, this sorts the tasks by that value
					sort( $tasks );
					foreach ( $tasks as $task ) {
						$due = portal_text_date( $task['due'] );
						$output .= "
						<tr>
							<td>" . $task['title'] . "</td>
							<td>" . $task['assignee'] . "</td>
							<td>" . $task['status'] . "%</td>
							<td>" . $due . "</td>
						</tr>
						";
					}
				} else {
					$output .= '<tr>' . __( 'There are no applicable tasks for this phase.', 'portal_sn' ) . '</tr>';
				}
			} else {
				$output .= '<tr>' . __( 'There are no tasks for this phase.', 'portal_sn' ) . '</tr>';
			}
			$output .= '</table>';
		endwhile;
	} else {
		$output = __( 'There are no phases for this project.', 'portal_sn' );
	}
	return $output;
}

/**
 * Renders an ordered list of milestones, sorted by due date
 *
 * @since 1.0
 * @param $project_id
 *
 * @return string
 */
function portal_sn_milestones_list( $project_id ) {

	$output = '';

	if ( have_rows('milestones', $project_id ) ) {
		$milestones = array();
		$output .= '<ol>';
		while ( have_rows('milestones', $project_id ) ) : the_row();

			$milestones[] = array(
				'due' => get_sub_field( 'date' ),
				'title' => get_sub_field( 'title' ),
				'occurs' => get_sub_field( 'occurs' ),
			);

		endwhile;
		if ( $milestones ) {
			sort( $milestones );
			foreach ( $milestones as $milestone ) {
				$due = portal_text_date( $milestone['due'] );
				$output .= '<li><strong>' . $milestone['title'] . '</strong> ' . __( 'occurs at ', 'portal_sn' ) . $milestone['occurs'] . '%' . __( ' and is due on ', 'portal_sn' ) . '<em>' . $due . '</em></li>';
			}
		}
		$output .= '</ol>';
	} else {
		$output = __( 'There are no milestones for this project.', 'portal_sn' );
	}

	return $output;
}