<?php
$date_format_opt 	= get_option( 'date_format' );
$date_format 		= ( substr( $date_format_opt, 0, 1 ) == 'd' ? 'dd/mm/yy' : 'mm/dd/yy' );
// If the database hasn't been updated use the old milestone style

if( get_option('portal_database_version') < 4) {

register_field_group(array (
	'id' => 'acf_milestones',
	'title' => __('Milestones','portal_projects'),
	'fields' => array (
		array (
			'key' => 'field_53138cd18335f',
			'label' => __('Milestones','portal_projects'),
			'name' => '',
			'type' => 'message',
			'message' => __('If desired, describe what will be completed at key points in the project.','portal_projects'),
		),
		array (
			'key' => 'field_528170f6552e1',
			'label' => __('Display Milestones','portal_projects'),
			'name' => 'display_milestones',
			'type' => 'checkbox',
			'choices' => array (
				'Yes' => __('Yes','portal_projects'),
			),
			'default_value' => '',
			'layout' => 'vertical',
		),
		array (
			'key' => 'field_52817036552d8',
			'label' => __('Milestone Frequency','portal_projects'),
			'name' => 'milestone_frequency',
			'type' => 'select',
			'conditional_logic' => array (
				'status' => 1,
				'rules' => array (
					array (
						'field' => 'field_528170f6552e1',
						'operator' => '==',
						'value' => 'Yes',
					),
				),
				'allorany' => 'all',
			),
			'choices' => array (
				'null' => '---',
				'quarters' => '25% / 50% / 75%',
				'fifths' => '20% / 40% / 60% / 80%',
			),
			'default_value' => '',
			'allow_null' => 0,
			'multiple' => 0,
		),
		array (
			'key' => 'field_52a9e020fba90',
			'label' => __('1st Milestone','portal_projects'),
			'name' => '',
			'type' => 'tab',
			'conditional_logic' => array (
				'status' => 1,
				'rules' => array (
					array (
						'field' => 'field_528170f6552e1',
						'operator' => '==',
						'value' => 'Yes',
					),
				),
				'allorany' => 'all',
			),
		),
		array (
			'key' => 'field_53138d853bbf8',
			'label' => __('Note:','portal_projects'),
			'name' => '',
			'type' => 'message',
			'conditional_logic' => array (
				'status' => 1,
				'rules' => array (
					array (
						'field' => 'field_528170f6552e1',
						'operator' => '!=',
						'value' => 'Yes',
					),
				),
				'allorany' => 'all',
			),
			'message' => __('If you\'d like to label key milestones please turn "Display Milestones" on (above).','portal_projects'),
		),
		array (
			'key' => 'field_5281706e552d9',
			'label' => __('Title','portal_projects'),
			'name' => '25%_title',
			'type' => 'text',
			'conditional_logic' => array (
				'status' => 1,
				'rules' => array (
					array (
						'field' => 'field_528170f6552e1',
						'operator' => '==',
						'value' => 'Yes',
					),
				),
				'allorany' => 'all',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'formatting' => 'none',
			'maxlength' => '',
		),
		array (
			'key' => 'field_528170a1552da',
			'label' => __('Description','portal_projects'),
			'name' => '25%_description',
			'type' => 'textarea',
			'conditional_logic' => array (
				'status' => 1,
				'rules' => array (
					array (
						'field' => 'field_528170f6552e1',
						'operator' => '==',
						'value' => 'Yes',
					),
				),
				'allorany' => 'all',
			),
			'default_value' => '',
			'placeholder' => '',
			'maxlength' => '',
			'formatting' => 'br',
		),
		array (
			'key' => 'field_52a9e067fba92',
			'label' => __('2nd Milestone','portal_projects'),
			'name' => '',
			'type' => 'tab',
			'conditional_logic' => array (
				'status' => 1,
				'rules' => array (
					array (
						'field' => 'field_528170f6552e1',
						'operator' => '==',
						'value' => 'Yes',
					),
				),
				'allorany' => 'all',
			),
		),
		array (
			'key' => 'field_53138db13bbf9',
			'label' => __('Note:','portal_projects'),
			'name' => '',
			'type' => 'message',
			'conditional_logic' => array (
				'status' => 1,
				'rules' => array (
					array (
						'field' => 'field_528170f6552e1',
						'operator' => '!=',
						'value' => 'Yes',
					),
				),
				'allorany' => 'all',
			),
			'message' => '',
		),
		array (
			'key' => 'field_528170ac552db',
			'label' => __('Title','portal_projects'),
			'name' => '50%_title',
			'type' => 'text',
			'conditional_logic' => array (
				'status' => 1,
				'rules' => array (
					array (
						'field' => 'field_528170f6552e1',
						'operator' => '==',
						'value' => 'Yes',
					),
				),
				'allorany' => 'all',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'formatting' => 'none',
			'maxlength' => '',
		),
		array (
			'key' => 'field_528170b6552dc',
			'label' => __('Description','portal_projects'),
			'name' => '50%_description',
			'type' => 'textarea',
			'conditional_logic' => array (
				'status' => 1,
				'rules' => array (
					array (
						'field' => 'field_528170f6552e1',
						'operator' => '==',
						'value' => 'Yes',
					),
				),
				'allorany' => 'all',
			),
			'default_value' => '',
			'placeholder' => '',
			'maxlength' => '',
			'formatting' => 'br',
		),
		array (
			'key' => 'field_52a9e084fba93',
			'label' => __('3rd Milestone','portal_projects'),
			'name' => '',
			'type' => 'tab',
			'conditional_logic' => array (
				'status' => 1,
				'rules' => array (
					array (
						'field' => 'field_528170f6552e1',
						'operator' => '==',
						'value' => 'Yes',
					),
				),
				'allorany' => 'all',
			),
		),
		array (
			'key' => 'field_53138dbe3bbfa',
			'label' => __('Note: ','portal_projects'),
			'name' => '',
			'type' => 'message',
			'conditional_logic' => array (
				'status' => 1,
				'rules' => array (
					array (
						'field' => 'field_528170f6552e1',
						'operator' => '!=',
						'value' => 'Yes',
					),
				),
				'allorany' => 'all',
			),
			'message' => '',
		),
		array (
			'key' => 'field_528170c2552dd',
			'label' => __('Title','portal_projects'),
			'name' => '75%_title',
			'type' => 'text',
			'conditional_logic' => array (
				'status' => 1,
				'rules' => array (
					array (
						'field' => 'field_528170f6552e1',
						'operator' => '==',
						'value' => 'Yes',
					),
				),
				'allorany' => 'all',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'formatting' => 'html',
			'maxlength' => '',
		),
		array (
			'key' => 'field_528170cc552de',
			'label' => __('Description','portal_projects'),
			'name' => '75%_description',
			'type' => 'textarea',
			'conditional_logic' => array (
				'status' => 1,
				'rules' => array (
					array (
						'field' => 'field_528170f6552e1',
						'operator' => '==',
						'value' => 'Yes',
					),
				),
				'allorany' => 'all',
			),
			'default_value' => '',
			'placeholder' => '',
			'maxlength' => '',
			'formatting' => 'br',
		),
		array (
			'key' => 'field_52a9e03afba91',
			'label' => __('4th Milestone','portal_projects'),
			'name' => '',
			'type' => 'tab',
			'conditional_logic' => array (
				'status' => 1,
				'rules' => array (
					array (
						'field' => 'field_528170f6552e1',
						'operator' => '==',
						'value' => 'Yes',
					),
					array (
						'field' => 'field_52817036552d8',
						'operator' => '==',
						'value' => 'fifths',
					),
				),
				'allorany' => 'all',
			),
		),
		array (
			'key' => 'field_53138dcc3bbfb',
			'label' => __('Note:','portal_projects'),
			'name' => '',
			'type' => 'message',
			'conditional_logic' => array (
				'status' => 1,
				'rules' => array (
					array (
						'field' => 'field_528170f6552e1',
						'operator' => '!=',
						'value' => 'Yes',
					),
				),
				'allorany' => 'all',
			),
			'message' => '',
		),
		array (
			'key' => 'field_53138d1583360',
			'label' => __('Note:','portal_projects'),
			'name' => '',
			'type' => 'message',
			'conditional_logic' => array (
				'status' => 1,
				'rules' => array (
					array (
						'field' => 'field_52817036552d8',
						'operator' => '==',
						'value' => 'quarters',
					),
				),
				'allorany' => 'all',
			),
			'message' => __('These fields are only available if you select "Fifths" on the milestone frequency field.','portal_projects'),
		),
		array (
			'key' => 'field_528170db552df',
			'label' => __('Title','portal_projects'),
			'name' => '100%_title',
			'type' => 'text',
			'conditional_logic' => array (
				'status' => 1,
				'rules' => array (
					array (
						'field' => 'field_528170f6552e1',
						'operator' => '==',
						'value' => 'Yes',
					),
					array (
						'field' => 'field_52817036552d8',
						'operator' => '==',
						'value' => 'fifths',
					),
				),
				'allorany' => 'all',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'formatting' => 'none',
			'maxlength' => '',
		),
		array (
			'key' => 'field_528170e6552e0',
			'label' => __('Description','portal_projects'),
			'name' => '100%_description',
			'type' => 'textarea',
			'conditional_logic' => array (
				'status' => 1,
				'rules' => array (
					array (
						'field' => 'field_528170f6552e1',
						'operator' => '==',
						'value' => 'Yes',
					),
					array (
						'field' => 'field_52817036552d8',
						'operator' => '==',
						'value' => 'fifths',
					),
				),
				'allorany' => 'all',
			),
			'default_value' => '',
			'placeholder' => '',
			'maxlength' => '',
			'formatting' => 'br',
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
	'menu_order' => 1,
));

	}

	if( ( get_option('portal_database_version') >= 4 ) || ( isset( $_GET[ 'portal_upgrade_db' ] ) ) ) {

	$milestone_fields = array (
	'key' => 'group_563d1e4aac15c',
	'title' => __('Milestones','portal_projects'),
	'fields' => array (
		array (
			'key' => 'field_563d1e50786e6',
			'label' => __('Milestones','portal_projects'),
			'name' => 'milestones',
			'prefix' => '',
			'type' => 'repeater',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'min' => '',
			'max' => '',
			'layout' => 'row',
			'button_label' => __('Add Milestone','portal_projects'),
			'sub_fields' => array (
				array (
					'key' => 'field_563d1e62786e7',
					'label' => __('Occurs','portal_projects'),
					'name' => 'occurs',
					'prefix' => '',
					'type' => 'select',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
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
					'default_value' => array (
					),
					'allow_null' => 0,
					'multiple' => 0,
					'ui' => 0,
					'ajax' => 0,
					'placeholder' => '',
					'disabled' => 0,
					'readonly' => 0,
				),
				array (
					'key' => 'field_563d1eab786e8',
					'label' => __('Title','portal_projects'),
					'name' => 'title',
					'prefix' => '',
					'type' => 'text',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'column_width' => '',
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
					'readonly' => 0,
					'disabled' => 0,
				),
				array (
					'key' => 'field_563d1eb2786e9',
					'label' => __('Description','portal_projects'),
					'name' => 'description',
					'prefix' => '',
					'type' => 'textarea',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'column_width' => '',
					'formating'	=> 'html',
					'default_value' => '',
					'placeholder' => '',
					'maxlength' => '',
					'rows' => '',
					'new_lines' => 'wpautop',
					'readonly' => 0,
					'disabled' => 0,
				),
				array (
					'key' => 'portal_milestone_date',
					'label' => __('Date','portal_projects'),
					'name' => 'date',
					'type' => 'date_picker',
					'return_format' => 'Ymd',
					'display_format' => get_option( 'date_format' ),
					'first_day' => 0,
				),
			),
		),
	),
	'location' => array (
		array (
			array (
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'portal_projects',
				'order_no' => 2,
				'group_no' => 2,
			),
		),
	),
	'position' => 'normal',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
    'menu_order' => 2,
);

$milestone_fields = apply_filters( 'portal_milestone_fields' , $milestone_fields );

register_field_group( $milestone_fields );

}
