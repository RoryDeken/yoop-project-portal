<?php
$date_format_opt 	= get_option( 'date_format' );
$date_format 		= ( substr( $date_format_opt, 0, 1 ) == 'd' ? 'dd/mm/yy' : 'mm/dd/yy' );
$wysiwyg_format		= ( portal_get_option( 'portal_lazyload_wysiwyg' ) ? 'lite_wysiwyg' : 'wysiwyg' );

	// Load the fields for phases

	$phase_fields = array (
        'id' => 'acf_phases',
        'title' => __('Phases','portal_projects'),
        'fields' => array (
            array (
                'key' => 'field_5436e7cae06b2',
                'label' 	=> __('Break projects into multiple phases with specific tasks.','portal_projects'),
                'name' 		=> 'phases_message',
				'type'		=> 'message',
            ),
            array (
                'key' => 'field_527d5dc12fa29',
                'label' => __('Phases','portal_projects'),
                'name' => 'phases',
                'type' => 'repeater',
                'sub_fields' => array (
                    array (
                        'key' => 'field_527d5dd02fa2a',
                        'label' => __('Title','portal_projects'),
                        'name' => 'title',
                        'type' => 'text',
                        'column_width' => '',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'formatting' => 'html',
                        'maxlength' => '',
                    ),
					array (
                        'key' => 'portal_phase_id',
                        'label' => __('Phase ID','portal_projects'),
                        'name' => 'phase_id',
                        'type' => 'hidden',
                        'column_width' => '',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'formatting' => 'html',
                        'maxlength' => '',
                    ),
                    array (
                        'key' => 'field_53207efc069cb',
                        'label' => __('Weight','portal_projects'),
                        'name' => 'weight',
                        'type' => 'select',
                        'instructions' => __('Specify how much shorter this phase is compared to the rest, i.e. selecting 95% will reduce this phases contribution to the total completion by 5%','portal_projects'),
                        'required' => 1,
                        'conditional_logic' => array (
                            'status' => 1,
                            'rules' => array (
                                array (
                                    'field' => 'field_5436e7f4e06b4',
                                    'operator' => '==',
                                    'value' => 'Yes',
                                ),
                                array (
                                    'field' => 'field_5436e85ee06b5',
                                    'operator' => '==',
                                    'value' => 'Weighting',
                                ),
                            ),
                            'allorany' => 'all',
                        ),
                        'column_width' => '',
                        'choices' => array (
                            '1'   => '1',
                            '.95' => '.95',
                            '.90' => '.90',
                            '.85' => '.85',
                            '.80' => '.80',
                            '.75' => '.75',
                            '.70' => '.70',
                            '.65' => '.65',
                            '.60' => '.60',
                            '.55' => '.55',
                            '.50' => '.50',
                            '.45' => '.45',
                            '.40' => '.40',
                            '.35' => '.35',
                            '.30' => '.30',
                            '.25' => '.25',
                            '.20' => '.20',
                            '.15' => '.15',
                            '.10' => '.10',
                            '.05' => '.05',
                        ),
                        'default_value' => '',
                        'allow_null' => 0,
                        'multiple' => 0,
                    ),
                    array (
                        'key' => 'field_5436eab7a2238',
                        'label' => __('Hours','portal_projects'),
                        'name' => 'hours',
                        'type' => 'text',
                        'instructions' => __('Enter the total number of hours this phase will take','portal_projects'),
                        'required' => 1,
                        'conditional_logic' => array (
                            'status' => 1,
                            'rules' => array (
                                array (
                                    'field' => 'field_5436e7f4e06b4',
                                    'operator' => '==',
                                    'value' => 'Yes',
                                ),
                                array (
                                    'field' => 'field_5436e85ee06b5',
                                    'operator' => '==',
                                    'value' => 'Hours',
                                ),
                            ),
                            'allorany' => 'all',
                        ),
                        'column_width' => '',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'formatting' => 'none',
                        'maxlength' => '',
                    ),
					array (
						'key' => 'field_phase_percentage',
						'label' => __('Percentage','portal_projects'),
						'name' => 'percentage',
						'type' => 'text',
						'instructions' => __('Enter the percentage of the project this phase represents.','portal_projects'),
						'required' => 1,
						'conditional_logic' => array (
							'status' => 1,
							'rules' => array (
								array (
									'field' => 'field_5436e7f4e06b4',
									'operator' => '==',
									'value' => 'Yes',
								),
								array (
									'field' => 'field_5436e85ee06b5',
									'operator' => '==',
									'value' => 'Percentage',
								),
							),
							'allorany' => 'all',
						),
						'column_width' => '',
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'none',
						'maxlength' => '',
					),
                    array (
                        'key' => 'field_527d5dd82fa2b',
                        'label' => __('Percent Complete','portal_projects'),
                        'name' => 'percent_complete',
                        'type' => 'select',
                        'conditional_logic' => array (
                            'status' => 1,
                            'rules' => array (
                                array (
                                    'field' => 'field_5436e7f4e06b4',
                                    'operator' => '!=',
                                    'value' => 'Yes',
                                ),
                            ),
                            'allorany' => 'all',
                        ),
                        'column_width' => '',
                        'choices' => array (
                            0 => '0%',
                            5 => '5%',
                            10 => '10%',
                            15 => '15%',
                            20 => '20%',
                            25 => '25%',
                            30 => '30%',
                            35 => '35%',
                            40 => '40%',
                            45 => '45%',
                            50 => '50%',
                            55 => '55%',
                            60 => '60%',
                            65 => '65%',
                            70 => '70%',
                            75 => '75%',
                            80 => '80%',
                            85 => '85%',
                            90 => '90%',
                            95 => '95%',
                            100 => '100%',
                        ),
                        'default_value' => '',
                        'allow_null' => 0,
                        'multiple' => 0,
                    ),
                    array (
                        'key' => 'field_527d5dea2fa2c',
                        'label' => __('Description','portal_projects'),
                        'name' => 'description',
                        'type' => $wysiwyg_format,
                        'column_width' => '90',
                        'default_value' => '',
                        'toolbar' => 'full',
                        'media_upload' => 'no',
                    ),
                    array (
                        'key' => 'field_527d5dfd2fa2d',
                        'label' => __('Tasks','portal_projects'),
                        'name' => 'tasks',
                        'type' => 'repeater',
                        'column_width' => '',
                        'sub_fields' => array (
                            array (
                                'key' => 'field_527d5e072fa2e',
                                'label' => __('Task','portal_projects'),
                                'name' => 'task',
                                'type' => 'text',
                                'column_width' => '',
                                'default_value' => '',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'formatting' => 'html',
                                'maxlength' => '',
                            ),
							array (
                                'key' => 'portal_task_id',
                                'label' => __('Task ID','portal_projects'),
                                'name' => 'task_id',
                                'type' => 'hidden',
                                'column_width' => '',
                                'default_value' => '',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'formatting' => 'html',
                                'maxlength' => '',
                            ),
							array (
								'key' => 'field_532b8da69c46e',
								'label' => __('Assigned to','portal_projects'),
								'name' => 'assigned',
								'type' => 'project_users',
								'column_width' => '',
								'role' => array (
									0 => 'all',
								),
								'field_type' => 'select',
								'allow_null' => 1,
							),
							array (
								'key' => 'portal_task_due_date',
								'label' => __('Due Date','portal_projects'),
								'name' => 'due_date',
								'type' => 'date_picker',
								'date_format' => 'yymmdd',
								'display_format' => $date_format,
								'first_day' => 0,
							),
                            array (
                                'key' => 'field_527d5e0e2fa2f',
                                'label' => __('Completion','portal_projects'),
                                'name' => 'status',
                                'type' => 'select',
                                'column_width' => '10',
                                'choices' => array (
                                    0 => '0%',
									5 => '5%',
                                    10 => '10%',
                                    15 => '15%',
                                    20 => '20%',
                                    25 => '25%',
                                    30 => '30%',
									35 => '35%',
                                    40 => '40%',
                                    45 => '45%',
                                    50 => '50%',
                                    55 => '55%',
                                    60 => '60%',
                                    65 => '65%',
                                    70 => '70%',
                                    75 => '75%',
                                    80 => '80%',
                                    85 => '85%',
                                    90 => '90%',
                                    95 => '95%',
                                    100 => '100%',
                                ),
                                'default_value' => '',
                                'allow_null' => 0,
                                'multiple' => 0,
                            ),
                        ),
                        'row_min' => '',
                        'row_limit' => '',
                        'layout' => 'row',
                        'button_label' => __('Add Task','portal_projects'),
                    ),
					array (
	                     'key' => 'field_527d5dd02fa2z',
	                     'label' => __( 'Unique Comment Key','portal_projects' ),
	                     'name' => 'phase-comment-key',
	                     'type' => 'text',
	                     'column_width' => '',
	                     'default_value' => '',
	                     'placeholder' => '',
	                     'prepend' => '',
	                     'append' => '',
	                     'formatting' => 'html',
	                     'maxlength' => '',
	                 ),
                ),
                'row_min' => '',
                'row_limit' => '',
                'layout' => 'row',
                'button_label' => __('Add Phase','portal_projects'),
            ),
        ),
        'location' => array (
            array (
                array (
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'portal_projects',
                    'order_no' => 0,
                    'group_no' => 0,
                ),
            ),
        ),
        'options' => array (
            'position' => 'normal',
            'layout' => 'default',
            'hide_on_screen' => array (
            ),
        ),
        'menu_order' => 3,
    );

$phase_fields = apply_filters( 'portal_phase_fields' , $phase_fields );

register_field_group( $phase_fields );
