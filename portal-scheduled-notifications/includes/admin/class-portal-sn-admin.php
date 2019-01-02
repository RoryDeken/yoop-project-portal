<?php
/**
 * The admin side to Project yoop - Scheduled Notifications.
 *
 * @since 0.1
 *
 * @package ProjectyoopSN
 * @subpackage ProjectyoopSN/core/admin
 */

defined( 'ABSPATH' ) || die();

class portal_SN_Admin {

	/**
	 * portal_SN_Admin constructor.
	 *
	 * @since 0.1
	 */
	function __construct() {

		add_filter( 'portal_notifications', array( $this, 'add_scheduled_notifications' ) );
		add_filter( 'portal_settings_notifications', array( $this, 'add_settings' ) );
		//add_action( 'portal_license', array( $this, 'license_field' ) );
		//add_action( 'portal_support', array( $this, 'support_form' ) );
		add_action( 'portal_do_notification_sn', array( $this, 'notification' ), 10, 6 );
	}

	/**
	 * Add SN settings to notifications area.
	 *
	 * @since 0.1
	 * @access private
	 *
	 * @param $settings array
	 * @return array
	 */
	function add_settings( $settings ) {

		$settings['sn'] = array(/*
			array(
				'id'   => 'license',
				'name' => __( 'Scheduled Notifications License Key', 'portal_sn' ),
				'type' => 'hook',
			),*/
		);

		return $settings;
	}

	/**
	 * Uses the RBP_Support module to add the EDD licensing field to the settings
	 *
	 * @since 1.0
	 */
	 /*
	function license_field() {
		portal_scheduled_notifications()->support->licensing_fields();
	}
	*/

	/**
	 * The fields in the notifications repeater on the settings page.
	 *
	 * @since 0.1
	 * @param $notifications
	 * @return array
	 */
	function add_scheduled_notifications( $notifications ) {

		$notifications['sn'] = array(
			'name'               => __( 'Scheduled', 'portal_sn' ),
			'default_feed_title' => __( 'New scheduled notification', 'portal_sn' ),
			'fields'             => array(
				// The "notification" key is necessary because it replaces the default one in yoop which lists triggers
				'notification' => array(
					'type' => 'select',
					'label' => 'Send this notification when:',
					'args' => array(
						'options' => array(
							'hourly' => __( 'Every Hour', 'portal_sn' ),
							'daily' => __( 'Every Day', 'portal_sn' ),
							'weekly' => __( 'Every Week', 'portal_sn' ),
							'biweekly' => __( 'Every Other Week', 'portal_sn' ),
							'monthly' => __( 'Every Month', 'portal_sn' ),
						),
					),
				),
				'recipients' => array(
					'id'    => 'recipients',
					'label' => __( 'Recipients', 'portal_projects' ),
					'type'  => 'text',
					'args'  => array(
						'desc' => '<span class="portal-label-new-line">' . __( 'Separate emails with commas.', 'portal_projects' ) . '</span>',
					),
				),
				'project' => array(
					'type' => 'select',
					'label' => __( 'Project', 'portal_sn' ),
					'args' => array(
						'options' => portal_sn_get_posts( 'portal_projects' ),
					),
				),
				'message_subject'   => array(
					'type'  => 'text',
					'label' => __( 'Subject', 'portal_sn' ),
				),
				'message_text'    => array(
					'type'  => 'textarea',
					'label' => __( 'Message', 'portal_sn' ),
					'args'  => array(
						'desc' => '<p class="description">' . sprintf(
								__( 'Available text replacements: %s', 'portal_sn' ),
								'<br/><code>' . implode( '</code><code>', array(
									'%project_title%',
									'%project_url%',
									'%dashboard%',
									'%date%',
									'%client%<br />',
									'%progress%',
									'%project_description%',
									'%project_start%',
									'%project_end%<br />',
									'%tasks_table_complete%',
									'%tasks_table_incomplete%<br />',
									'%tasks_table_all%',
									'%milestones_list%',
								) ) . '</code>'
							) . '</p>',
					),
				),
			),
		);

		return $notifications;
	}
}
