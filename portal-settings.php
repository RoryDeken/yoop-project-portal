<?php
/**
 * Update old settings with new single setting optoin
 * @return [array] [Array of all settings]
 */
function portal_get_settings() {

	$settings = get_option( 'portal_settings' );

	if ( empty( $settings ) ) {

		// Update old settings with new single option

		$general_settings      = is_array( get_option( 'portal_settings_general' ) ) ? get_option( 'portal_settings_general' ) : array();
		$appearance_settings   = is_array( get_option( 'portal_settings_appearance' ) ) ? get_option( 'portal_settings_appearance' ) : array();
		$notification_settings = is_array( get_option( 'portal_settings_notifications' ) ) ? get_option( 'portal_settings_notifications' ) : array();
		//$advanced_settings     = is_array( get_option( 'portal_settings_advanced' ) ) ? get_option( 'portal_settings_advanced' ) : array();
		$addon_settings        = is_array( get_option( 'portal_settings_addons' ) ) ? get_option( 'portal_settings_addons' ) : array();

		$settings = array_merge( $general_settings, $appearance_settings, $notification_settings, /*$advanced_settings, */ $addon_settings );

		update_option( 'portal_settings', $settings );

	}

	return apply_filters( 'portal_get_settings', $settings );

}

// Global $portal_options
$portal_options = portal_get_settings();

/**
 * get an option from the new portal settings, if one doesn't exist try the default key
 *
 * @param  string $key [option name]
 * @param  [type] $default [set a default fallback]
 *
 * @return [mixed]          [value]
 */
function portal_get_option( $key = '', $default = false ) {

	$portal_db_ver = intval( get_option( 'portal_database_version' ) );

	// Check to see if the settings data has been migrated and use the old system if not...
	if ( $portal_db_ver < 6 ) {

		$value = get_option( $key, $default );

	} else {

		global $portal_options;

		$value = ! empty( $portal_options[ $key ] ) ? $portal_options[ $key ] : $default;

		$value = apply_filters( 'portal_get_option', $value, $key, $default );

	}

	return apply_filters( 'portal_get_option_' . $key, $value, $key, $default );

}

/**
 * Add the admin menu
 */
add_action( 'admin_menu', 'edd_yoop_license_menu' );
function edd_yoop_license_menu() {

	global $portal_settings_page;

	$portal_settings_page = add_submenu_page( 'options-general.php', 'Project yoop Settings', __( 'Settings', 'portal_projects'), 'manage_options', 'yoop-license', 'edd_yoop_license_page' );

	global $submenu;

	$settings_index = null;

	if( empty( $submenu['options-general.php'] ) ) return;

	foreach ( $submenu['options-general.php'] as $key => $menu_item ) {

		// Index 2 is always the child page slug
		if ( $menu_item[2] == 'yoop-license' ) {
			$settings_index = $key;
			break;
		}

	}

	// We need to make the path more absolute
	$submenu['options-general.php'][ $settings_index ][2] = 'options-general.php?page=yoop-license';

	// Move the Menu Item
	$submenu['edit.php?post_type=portal_projects'][] = $submenu['options-general.php'][ $settings_index ];
	unset( $submenu['options-general.php'][ $settings_index ] );

}

add_filter( 'parent_file', 'portal_highlight_settings_parent_page', 10, 1 );
function portal_highlight_settings_parent_page( $parent_file ){

	global $current_screen;
	global $self;

	if ( $current_screen->base == 'settings_page_yoop-license' ) {

		// Render this as the Active Page Menu
		$parent_file = 'edit.php?post_type=portal_projects';

		// Ensure the top-level "Settings" doesn't show as active
		$self = 'edit.php?post_type=portal_projects';

	}

	return $parent_file;

}

add_filter( 'submenu_file', 'portal_highlight_settings_submenu_page', 10, 2 );
function portal_highlight_settings_submenu_page( $submenu_file, $parent_file ) {

	global $current_screen;

	if ( $current_screen->base == 'settings_page_yoop-license' ) {
		$submenu_file = 'options-general.php?page=yoop-license';
	}

	return $submenu_file;

}

add_action( 'adminmenu', 'portal_reset_options_general_menu' );
function portal_reset_options_general_menu() {

	global $current_screen;
	global $parent_file;

	if ( $current_screen->base == 'settings_page_yoop-license' ) {

		// We have to reset this after the Menu is generated so Settings Errors still appear
		$parent_file = 'options-general.php';

	}

}

/**
 * Return an array of all the settings tabs and their titles
 * @return array
 */
function portal_get_settings_tabs() {

	// $settings = edd_get_registered_settings();

	$tabs                               = array();
	$tabs['portal_settings_general']       = __( 'General', 'portal_projects' );
	$tabs['portal_settings_appearance']    = __( 'Appearance', 'portal_projects' );
	$tabs['portal_settings_notifications'] = __( 'Notifications', 'portal_projects' );
	//$tabs['portal_settings_advanced']      = __( 'Advanced', 'portal_projects' );

	$addon_settings = apply_filters( 'portal_settings_addons', array() );

	if ( ! empty( $addon_settings ) ) {

		$tabs['portal_settings_addons'] = __( 'Project Templates', 'portal_projects' );

	}

	return apply_filters( 'portal_settings_tabs', $tabs );

}

/**
 * Get the content of each setting tab
 *
 * @param  [string] $tab [ID of the setting tab to return]
 *
 * @return [markup?]      [description]
 */
function portal_get_settings_tab_sections( $tab = false ) {
	$tabs     = false;
	$sections = portal_get_registered_settings_sections();
	if ( $tab && ! empty( $sections[ $tab ] ) ) {
		$tabs = $sections[ $tab ];
	} else if ( $tab ) {
		$tabs = false;
	}

	return $tabs;
}

function portal_get_registered_settings_sections() {
	static $sections = false;
	if ( false !== $sections ) {
		return $sections;
	}
	$sections = array(
		'portal_settings_general'       => apply_filters( 'edd_settings_sections_general', array(
			'main' => __( 'General', 'portal_projects' ),
		) ),
		'portal_settings_appearance'    => apply_filters( 'edd_settings_sections_appearance', array(
			'main'           => __( 'General', 'portal_projects' ),
			'header'         => __( 'Header', 'portal_projects' ),
			'body'           => __( 'Body', 'portal_projects' ),
			'phases'         => __( 'Phases', 'portal_projects' ),
			'custom_styling' => __( 'Custom Styling', 'portal_projects' )/*
			'calendar'       => __( 'Calendar', 'portal_projects' )*/
		) ),
		'portal_settings_notifications' => apply_filters( 'portal_settings_sections_notifications', array(
			'email' => __( 'Email', 'portal_projects' ),
		) ),
		/*
		'portal_settings_advanced'      => apply_filters( 'portal_settings_sections_advanced', array(
			'main' => __( 'General', 'portal_projects' ),
		) ),*/
		'portal_settings_addons'        => apply_filters( 'portal_settings_sections_addons', array() )
	);
	$sections = apply_filters( 'portal_settings_sections', $sections );

	return $sections;
}

function edd_yoop_register_options() {
	if ( false == get_option( 'portal_settings' ) ) {
		add_option( 'portal_settings' );
	}
	foreach ( portal_get_registered_settings() as $tab => $sections ) {
		foreach ( $sections as $section => $settings ) {
			// Check for backwards compatibility
			$section_tabs = portal_get_settings_tab_sections( $tab );
			if ( ! is_array( $section_tabs ) || ! array_key_exists( $section, $section_tabs ) ) {
				$section  = 'main';
				$settings = $sections;
			}
			add_settings_section(
				'portal_settings_' . $tab . '_' . $section,
				__return_null(),
				'__return_false',
				'portal_settings_' . $tab . '_' . $section
			);
			foreach ( $settings as $key => $option ) {
				// For backwards compatibility
				if ( empty( $option['id'] ) ) {
					continue;
				}

				$name = isset( $option['name'] ) ? $option['name'] : '';
				$args = wp_parse_args( $option, array(
					'section' => $section,
					'id'      => null,
					'desc'    => '',
					'name'    => null,
					'default' => null,
				) );

				$callback = $option['type'] == 'custom' && isset( $option['callback'] ) ? $option['callback'] : "portal_$option[type]_callback";
				add_settings_field(
					'portal_settings[' . $option['id'] . ']',
					$name,
					is_callable( $callback ) ? $callback : 'portal_missing_callback',
					'portal_settings_' . $tab . '_' . $section,
					'portal_settings_' . $tab . '_' . $section,
					$args
				);
			}
		}
	}
	// Creates our settings in the options table
	register_setting( 'portal_settings', 'portal_settings', 'portal_settings_sanitize' );
}

add_action( 'admin_init', 'edd_yoop_register_options' );

function portal_settings_sanitize( $input = array() ) {
	global $portal_options;

	if ( empty( $_POST['_wp_http_referer'] ) ) {
		return $input;
	}

	parse_str( $_POST['_wp_http_referer'], $referrer );

	$settings = portal_get_registered_settings();
	$tab      = isset( $referrer['tab'] ) ? $referrer['tab'] : 'portal_settings_general';
	$section  = isset( $referrer['section'] ) ? $referrer['section'] : 'main';

	$input = $input ? $input : array();
	$input = apply_filters( 'portal_settings_' . $tab . '-' . $section . '_sanitize', $input );

	if ( 'main' === $section ) {
		// Check for extensions that aren't using new sections
		$input = apply_filters( 'portal_settings_' . $tab . '_sanitize', $input );
	}

	// Loop through each setting being saved and pass it through a sanitization filter
	foreach ( $input as $key => $value ) {
		// Get the setting type (checkbox, select, etc)
		$type = isset( $settings[ $tab ][ $key ]['type'] ) ? $settings[ $tab ][ $key ]['type'] : false;
		if ( $type ) {
			// Field type specific filter
			$input[ $key ] = apply_filters( 'portal_settings_sanitize_' . $type, $value, $key );
		}
		// General filter
		$input[ $key ] = apply_filters( 'portal_settings_sanitize', $input[ $key ], $key );
	}

	// Loop through the whitelist and unset any that are empty for the tab being saved
	$main_settings    = $section == 'main' ? $settings[ $tab ] : array(); // Check for extensions that aren't using new sections
	$section_settings = ! empty( $settings[ $tab ][ $section ] ) ? $settings[ $tab ][ $section ] : array();
	$found_settings   = array_merge( $main_settings, $section_settings );

	if ( ! empty( $found_settings ) ) {
		foreach ( $found_settings as $key => $value ) {
			// settings used to have numeric keys, now they have keys that match the option ID. This ensures both methods work
			if ( is_numeric( $key ) ) {
				$key = $value['id'];
			}
			if ( empty( $input[ $key ] ) ) {
				unset( $portal_options[ $key ] );
			}
		}
	}

	// Merge our new settings with the existing
	$output = array_merge( $portal_options, $input );

	add_settings_error( 'portal-notices', '', __( 'Settings Updated.', 'portal_projects' ), 'updated' );

	return $output;
}

function portal_text_callback( $args ) {

	global $portal_options;

	$args = wp_parse_args( $args, array(
		'value'       => false,
		'id'          => '',
		'desc'        => '',
		'default'     => '',
		'faux'        => false,
		'readonly'    => false,
		'size'        => 'regular',
		'placeholder' => false,
	) );

	if ( $args['value'] ) {

		$value = $args['value'];

	} elseif ( isset( $portal_options[ $args['id'] ] ) ) {

		$value = $portal_options[ $args['id'] ];

	} else {

		$value = $args['default'];
	}

	if ( $args['faux'] === true ) {
		$args['readonly'] = true;
		$value            = $args['default'];
		$name             = '';
	} else {
		$name = 'name="portal_settings[' . esc_attr( $args['id'] ) . ']"';
	}

	$readonly = $args['readonly'] === true ? ' readonly="readonly"' : '';
	$html     = '<input type="text" class="' . sanitize_html_class( $args['size'] ) . '-text" id="portal_settings[' . portal_sanitize_key( $args['id'] ) . ']" ' . $name . ' value="' . esc_attr( stripslashes( $value ) ) . '"' . $readonly . ( $args['placeholder'] ? "placeholder=\"$args[placeholder]\"" : '' ) . '/>';
	$html .= '<label for="portal_settings[' . portal_sanitize_key( $args['id'] ) . ']"> ' . wp_kses_post( $args['desc'] ) . '</label>';

	echo $html;
}

function portal_html_callback( $args ) {

	$html = '<label for="portal_settings[' . portal_sanitize_key( $args['id'] ) . ']"> ' . wp_kses_post( $args['desc'] ) . '</label>';

	echo $html;

}

function portal_hidden_callback( $args ) {

	global $portal_options;

	$args = wp_parse_args( $args, array(
		'value'   => false,
		'id'      => '',
		'name'    => false,
		'default' => '',
	) );

	if ( $args['value'] ) {

		$value = $args['value'];

	} elseif ( isset( $portal_options[ $args['id'] ] ) ) {

		$value = $portal_options[ $args['id'] ];

	} else {

		$value = $args['default'];
	}

	$name = $args['name'] !== false ? esc_attr( $args['name'] ) : 'portal_settings[' . esc_attr( $args['id'] ) . ']';
	?>
	<input type="hidden" name="<?php echo $name; ?>" value="<?php echo $value; ?>"/>
	<?php
}

function portal_button_link_callback( $args ) {

	if ( $args['button_text'] !== '' ) {
		$text = $args['button_text'];
	} else {
		$text = $args['name'];
	}

	echo '<a id="' . $args['button_id'] . '" class="button button-primary" href="' . $args['href'] . '" title="' . $args['name'] . '">' . $text . '</a>';

}

function portal_color_callback( $args ) {

	global $portal_options;

	$args = wp_parse_args( $args, array(
		'id'      => '',
		'default' => '',
		'desc'    => '',
		'value'   => false,
	) );

	if ( $args['value'] ) {

		$value = $args['value'];

	} elseif ( isset( $portal_options[ $args['id'] ] ) ) {

		$value = $portal_options[ $args['id'] ];

	} else {

		$value = $args['default'];
	}

	if ( strpos( $value, '#' ) === false ) {
		$value = "#$value";
	}

	$html = '<input class="color-field" id="portal_settings[' . portal_sanitize_key( $args['id'] ) . ']" name="portal_settings[' . esc_attr( $args['id'] ) . ']" value="' . esc_attr( $value ) . '" rel="' . esc_attr( $args['default'] ) . '" />';
	$html .= '<label for="portal_settings[' . portal_sanitize_key( $args['id'] ) . ']"> ' . wp_kses_post( $args['desc'] ) . '</label>';

	echo $html;

}

function portal_missing_callback( $args ) {
	printf(
		__( 'The callback function used for the %s setting is missing.', 'portal_projects' ),
		'<strong>' . $args['id'] . '</strong>'
	);
}

function portal_dummy_callback( $args ) {
	printf(
		__( 'The "dummy" Setting Type used on %s is meant to be used in conjunction with the %s filter set to "__return_false" in order to use a custom &lt;form&gt; rather than the included one.', 'portal_projects' ),
		'<strong>' . $args['id'] . '</strong>',
		'<em>portal_settings_section_' . $args['section'] . '_form</em>'
	);
}

function portal_upload_callback( $args ) {

	global $portal_options;

	$args = wp_parse_args( $args, array(
		'id'        => '',
		'default'   => '',
		'desc'      => '',
		'value'     => false,
		'size'      => 'regular',
		'button_id' => '',
	) );

	if ( $args['value'] ) {

		$value = $args['value'];

	} elseif ( isset( $portal_options[ $args['id'] ] ) ) {

		$value = $portal_options[ $args['id'] ];

	} else {

		$value = $args['default'];
	}

	$html = '<input type="text" id="' . $args['id'] . '" class="' . sanitize_html_class( $args['size'] ) . '-text" id="portal_settings[' . portal_sanitize_key( $args['id'] ) . ']" name="portal_settings[' . esc_attr( $args['id'] ) . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
	$html .= '<span>&nbsp;<input type="button" id="' . $args['button_id'] . '" class="portal_settings_upload_button button-secondary" value="' . __( 'Upload File', 'portal_projects' ) . '"/></span>';
	$html .= '<label for="portal_settings[' . portal_sanitize_key( $args['id'] ) . ']"> ' . wp_kses_post( $args['desc'] ) . '</label>';

	echo $html;

}

function portal_checkbox_callback( $args ) {

	global $portal_options;

	$args = wp_parse_args( $args, array(
		'id'      => '',
		'default' => '',
		'desc'    => '',
		'value'   => false,
		'faux'    => false,
	) );

	if ( $args['value'] ) {

		$value = $args['value'];

	} elseif ( isset( $portal_options[ $args['id'] ] ) ) {

		$value = $portal_options[ $args['id'] ];

	} else {

		$value = $args['default'];
	}

	if ( true === $args['faux'] ) {
		$name = '';
	} else {
		$name = 'name="portal_settings[' . portal_sanitize_key( $args['id'] ) . ']"';
	}

	$checked = checked( 1, $value, false );

	$html = '<input type="checkbox" id="portal_settings[' . portal_sanitize_key( $args['id'] ) . ']"' . $name . ' value="1" ' . $checked . '/>';
	$html .= '<label for="portal_settings[' . portal_sanitize_key( $args['id'] ) . ']"> ' . wp_kses_post( $args['desc'] ) . '</label>';

	echo $html;

}

function portal_select_callback( $args ) {

	global $portal_options;

	$args = wp_parse_args( $args, array(
		'id'          => '',
		'default'     => '',
		'desc'        => '',
		'value'       => false,
		'placeholder' => '',
		'chosen'      => false,
		'options'     => array(),
	) );

	if ( $args['value'] ) {

		$value = $args['value'];

	} elseif ( isset( $portal_options[ $args['id'] ] ) ) {

		$value = $portal_options[ $args['id'] ];

	} else {

		$value = $args['default'];
	}

	if ( $args['chosen'] ) {
		$chosen = 'class="portal-chosen"';
	} else {
		$chosen = '';
	}

	$html = '<select id="portal_settings[' . portal_sanitize_key( $args['id'] ) . ']" name="portal_settings[' . esc_attr( $args['id'] ) . ']" ' . $chosen . 'data-placeholder="' . esc_html( $args['placeholder'] ) . '" />';

	foreach ( $args['options'] as $option => $name ) {
		$selected = selected( $option, $value, false );
		$html .= '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( $name ) . '</option>';
	}

	$html .= '</select>';

	$html .= '<label for="portal_settings[' . portal_sanitize_key( $args['id'] ) . ']"> ' . wp_kses_post( $args['desc'] ) . '</label>';

	echo $html;

}

function portal_header_callback( $args ) {
	echo '';
}

function portal_textarea_callback( $args ) {

	global $portal_options;

	$args = wp_parse_args( $args, array(
		'id'      => '',
		'default' => '',
		'desc'    => '',
		'value'   => false,
	) );

	if ( $args['value'] ) {

		$value = $args['value'];

	} elseif ( isset( $portal_options[ $args['id'] ] ) ) {

		$value = $portal_options[ $args['id'] ];

	} else {

		$value = $args['default'];
	}

	$html = '<textarea class="large-text" cols="50" rows="5" id="portal_settings[' . portal_sanitize_key( $args['id'] ) . ']" name="portal_settings[' . esc_attr( $args['id'] ) . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
	$html .= '<label for="portal_settings[' . portal_sanitize_key( $args['id'] ) . ']"> ' . wp_kses_post( $args['desc'] ) . '</label>';

	echo $html;

}
/*
function portal_license_key_callback( $args ) {

	global $portal_options;

	$status 	= get_option( 'edd_yoop_license_status' );
	$license 	= get_option( 'edd_yoop_license_key' );
	$options 	= get_option( 'portal_options' );

	if ( $status !== false && $status == 'valid' && isset( $portal_options['edd_yoop_license_key'] ) && !empty( $portal_options['edd_yoop_license_key'] ) ) {
		$html = '<span style="color:green;" class="portal-activation-notice">' . __( 'Active', 'portal_projects' ) . '</span>';
		$html .= wp_nonce_field( 'edd_yoop_nonce', 'edd_yoop_nonce', true, false );
		$html .= ' <input type="submit" class="button-secondary" name="edd_license_deactivate" value="' . __( 'Deactivate License', 'portal_projects' ) . '"/>';
	} else {
		$html = '<span style="color:red;" class="portal-activation-notice">' . __( 'Inactive', 'portal_projects' ) . '</span>';
		$html .= wp_nonce_field( 'edd_yoop_nonce', 'edd_yoop_nonce', true, false );
		if( isset( $portal_options['edd_yoop_license_key'] ) && !empty( $portal_options['edd_yoop_license_key'] ) ) {
			$html .= ' <input type="submit" class="button-secondary" name="edd_license_activate" value="' . __( 'Activate License', 'portal_projects' ) . '"/>';
			$html .= ' <a class="button" href="' . admin_url() . 'options-general.php?page=yoop-license&portal_activate_response=true">' . __( 'Check Activation Message', 'portal_projects' ) . '</a>';
		} else {
			$html .= '<strong>&nbsp;&nbsp;' . __( 'Please enter a license key and save to activate.', 'portal_projects' ) . '</strong>';
		}

	}

	echo $html;
}
*/
function portal_repeater_callback( $args ) {

	global $portal_options;

	$args = wp_parse_args( $args, array(
		'id'                        => '',
		'input_name'                => false,
		'default'                   => '',
		'desc'                      => false,
		'fields'                    => array(),
		'values'                    => false,
		'sortable'                  => true,
		'collapsable'               => false,
		'collapsable_title'         => false,
		'collapsable_title_default' => '',
		'add_item_text'             => __( 'Add Item', 'portal_projects' ),
		'delete_item_text'          => __( 'Delete', 'portal_projects' ),
	) );

	if ( $args['values'] !== false ) {

		$values = $args['values'];

	} elseif ( isset( $portal_options[ $args['id'] ] ) ) {

		$values = (array) $portal_options[ $args['id'] ];

	} else {

		$values = isset( $args['default'] ) ? (array) $args['default'] : array();
	}

	$name = $args['input_name'] !== false ? $args['input_name'] : 'portal_settings[' . esc_attr( $args['id'] ) . ']';

	$field_count = count( $values ) >= 1 ? count( $values ) : 1;

	$repeater_classes = array(
		'portal-repeater',
	);

	if ( $args['collapsable'] ) {
		$repeater_classes[] = 'portal-repeater-collapsable';
	}

	if ( $args['sortable'] ) {
		$repeater_classes[] = 'portal-repeater-sortable';
	}
	?>
	<div class="<?php echo implode( ' ', $repeater_classes ); ?>" data-portal-repeater
		<?php echo $args['collapsable'] ? 'data-repeater-collapsable' : ''; ?>
		<?php echo $args['sortable'] ? 'data-repeater-sortable' : ''; ?>>
		<div class="portal-repeater-list" data-repeater-list="<?php echo $name; ?>">
			<?php for ( $i = 0; $i < $field_count; $i ++ ) : ?>
				<div class="portal-repeater-item closed" data-repeater-item
					<?php echo ! isset( $values[ $i ] ) ? 'data-repeater-item-dummy style="display: none;"' : '' ?>>
					<table class="portal-repeater-item-header">
						<tr>
							<td class="portal-repeater-item-handle" data-repeater-item-handle>
								<?php echo $i + 1; ?>
							</td>

							<td class="portal-repeater-collapsable-handle" data-repeater-collapsable-handle>
								<?php if ( $args['collapsable'] ) : ?>
									<h3>
										<?php
										if ( $args['collapsable_title'] !== false ) {
											if ( isset( $values[ $i ][ $args['collapsable_title'] ] ) ) : ?>
												<span data-repeater-collapsable-handle-title>
													<?php echo $values[ $i ][ $args['collapsable_title'] ]; ?>
												</span>
											<?php endif; ?>

											<span data-repeater-collapsable-handle-default
												<?php echo isset( $values[ $i ][ $args['collapsable_title'] ] ) ?
													'style="display: none;"' : ''; ?>>
												<?php echo $args['collapsable_title_default']; ?>
											</span>
											<?php
										} else {
											echo __( 'Collapse / Expand', 'portal_projects' );
										}
										?>

										<span class="portal-repeater-collapsable-handle-arrow">
											<span class="opened dashicons dashicons-arrow-up"></span>
											<span class="closed dashicons dashicons-arrow-down"></span>
										</span>
									</h3>
								<?php endif; ?>
							</td>

							<td class="portal-repeater-item-actions">
								<input data-repeater-delete type="button" class="portal-repeater-delete button"
								       value="<?php echo $args['delete_item_text']; ?>"/>
							</td>
						</tr>
					</table>

					<div class="portal-repeater-item-content">
						<div class="portal-repeater-item-fields">
							<?php
							foreach ( (array) $args['fields'] as $field_ID => $field ) {

								$field = wp_parse_args( $field, array(
									'type'    => 'text',
									'label'   => $field_ID,
									'classes' => array(),
									'args'    => array(),
								) );

								$field['args'] = wp_parse_args( $field['args'], array(
									'id'    => $field_ID,
									'value' => isset( $values[ $i ][ $field_ID ] ) ? $values[ $i ][ $field_ID ] : null,
									'desc'  => '',
								) );

								$field['classes'][] = 'portal-repeater-item-field';
								$field['classes'][] = "portal-repeater-item-field-$field[type]";
								$field['classes'][] = "portal-repeater-item-field-$field_ID";


								if ( is_callable( "portal_{$field['type']}_callback" ) ) : ?>
									<div class="<?php echo implode( ' ', $field['classes'] ); ?>">

										<label class="portal-repeater-item-field-label">
											<?php echo esc_attr( $field['label'] ); ?>
										</label>
										<br/>

										<?php call_user_func( "portal_{$field['type']}_callback", $field['args'] ); ?>

									</div>
								<?php endif;
							}
							?>
						</div>
					</div>
				</div>
			<?php endfor; ?>
		</div>

		<input data-repeater-create type="button" class="portal-repeater-add button"
		       value="<?php echo $args['add_item_text']; ?>"/>

		<?php if ( $args['desc'] ) : ?>
			<p class="description">
				<?php echo strip_tags( $args['desc'], '<br><em><strong><i><b>' ); ?>
			</p>
		<?php endif; ?>
	</div>
	<?php
}

add_filter( 'portal_settings_sanitize_license_key', 'portal_license_key_sanitize', 10, 2 );
function portal_license_key_sanitize( $value, $key ) {
	$old = get_option( 'edd_yoop_license_key' );
	if ( $old && $old != $value ) {
		delete_option( 'edd_yoop_license_status' ); // new license has been entered, so must reactivate
	}

	return $value;
}

function portal_hook_callback( $args ) {
	do_action( 'portal_' . $args['id'], $args );
}

function portal_sanitize_key( $key ) {
	$raw_key = $key;
	$key     = preg_replace( '/[^a-zA-Z0-9_\-\.\:\/]/', '', $key );

	return apply_filters( 'portal_sanitize_key', $key, $raw_key );
}

function portal_get_registered_settings() {

	/**
	 * 'Whitelisted' Project yoop settings, filters are provided for each settings
	 * section to allow extensions and other plugins to add their own settings
	 */
	$portal_settings = array(
		/** General Settings */
		'portal_settings_general'       => apply_filters( 'portal_settings_general',
			array(
				'main' => array(
					'main_message' => array(
						'id'   => 'portal_main_message',
						'name' => '<h3>' . __( 'Portal Settings', 'portal_projects' ) . '</h3>',
						'type' => 'header',
					)/*
					'edd_yoop_license_key'      => array(
						'id'   => 'edd_yoop_license_key',
						'name' => __( 'License Key', 'portal_projects' ),
						'desc' => __( 'Enter your License Key', 'portal_projects' ),
						'type' => 'text',
					),
					'edd_yoop_activate_license' => array(
						'id'   => 'edd_yoop_activate_license',
						'name' => __( 'Activate License', 'portal_projects' ),
						'type' => 'license_key',
					),
					'portal_slug'                      => array(
						'id'      => 'portal_slug',
						'name'    => __( 'Project Slug', 'portal_projects' ),
						'type'    => 'text',
						'default' => 'yoop',
					),
					'portal_back'                      => array(
						'id'   => 'portal_back',
						'name' => __( 'Back Button Link', 'portal_projects' ),
						'type' => 'text',
						'desc' => __( 'Leave blank for dashboard', 'portal_projects' ),
					)*/
				),
			)
		),
		'portal_settings_appearance'    => apply_filters( 'portal_settings_appearance',
			array(
				'main'           => array(
					'portal_settings_appearance_general' => array(
						'id'   => 'portal_settings_appearance_general',
						'name' => '<h3>' . __( 'General Settings', 'portal_projects' ) . '</h3>',
						'desc' => '',
						'type' => 'header',
					),
					'portal_logo'                        => array(
						'id'        => 'portal_logo',
						'name'      => __( 'Logo for Portal Pages', 'portal_projects' ),
						'type'      => 'upload',
						'button_id' => 'portal_upload_image_button',
					),
					'portal_logo_link'                        => array(
						'id'        => 'portal_logo_link',
						'name'      => __( 'Logo Link', 'portal_projects' ),
						'desc'		=>	__( 'Where would you like the logo to link?', 'portal_projects' ),
						'type'      => 'text',
					),
					'portal_favicon'					=> array(
						'id'		=>	'portal_favicon',
						'name'		=>	__( 'Favicon', 'portal_projects' ),
						'desc'		=>	__( 'Upload a 128 x 128 png or ICO to use as a favicon on yoop pages', 'portal_projects' ),
						'type'		=>	'upload',
						'button_id'	=>	'portal_upload_favicon_button'
					),
					'portal_dashboard_sorting'	=>	array(
						'id'		=>	'portal_dashboard_sorting',
						'name'		=>	__( 'Dashboard sort by', 'portal_projects' ),
						'desc'		=>	__( 'How do you want dashboard projects to be sorted?', 'portal_projects' ),
						'type'		=>	'select',
						'options'	=>	array(
							'default'		=>	__( 'Creation date', 'portal_projects' ),
							'start_date'	=>	__( 'Start date', 'portal_projects' ),
							'end_date'		=>	__( 'End date', 'portal_projects' ),
							'alphabetical'	=>	__( 'Alphabetical', 'portal_projects' ),
						),
					),
					'portal_dashboard_sort_order' => array(
						'id'	=>	'portal_dashboard_sort_order',
						'name'	=>	__( 'Dashboard sort order', 'portal_projects' ),
						'type'	=>	'select',
						'options'	=>	array(
							'asc'	=>	__( 'Ascending', 'portal_projects' ),
							'desc'	=>	__( 'Descending', 'portal_projects' )
						),
					),
					'portal_use_custom_template'         => array(
						'id'   => 'portal_use_custom_template',
						'name' => __( 'Use Custom Template', 'portal_projects' ),
						'type' => 'checkbox',
					),
					'portal_custom_template'             => array(
						'id'      => 'portal_custom_template',
						'name'    => __( 'Choose Custom Template', 'portal_projects' ),
						'type'    => 'select',
						'options' => portal_get_project_templates(),
						'desc'    => __( '', 'portal_projects' )
					),
					/*
					'portal_use_rtl'	=>	array(
						'id'	=>	'portal_use_rtl',
						'name'	=>	__( 'Display in RTL', 'portal_projects' ),
						'type'	=>	'checkbox'
					),*/
				),
				'header'         => array(
					'portal_settings_appearance_header' => array(
						'id'   => 'portal_settings_appearance_header',
						'name' => '<h3>' . __( 'Header Settings', 'portal_projects' ) . '</h3>',
						'desc' => '',
						'type' => 'header',
					),
					'portal_menu_background'            => array(
						'id'      => 'portal_menu_background',
						'name'    => __( 'Menu Background', 'portal_projects' ),
						'type'    => 'color',
						'default' => '#2a3542'
					),
					'portal_menu_text'                  => array(
						'id'      => 'portal_menu_text',
						'name'    => __( 'Menu Text', 'portal_projects' ),
						'type'    => 'color',
						'default' => '#fff'
					),
					'portal_reset_colors'               => array(
						'id'          => 'portal_reset_colors',
						'name'        => __( 'Reset Colors to Default', 'portal_projects' ),
						'type'        => 'button_link',
						'href'        => '',
						'button_id'   => 'portal-reset-colors',
						'button_text' => __( 'Reset', 'portal_projects' )
					)
				),
				'body'           => array(
					'portal_settings_appearance_body' => array(
						'id'   => 'portal_settings_appearance_body',
						'name' => '<h3>' . __( 'Body Settings', 'portal_projects' ) . '</h3>',
						'desc' => '',
						'type' => 'header',
					),
					'portal_body_background'          => array(
						'id'      => 'portal_body_background',
						'name'    => __( 'Body Background', 'portal_projects' ),
						'type'    => 'color',
						'default' => '#f1f2f7'
					),
					'portal_body_heading'             => array(
						'id'      => 'portal_body_heading',
						'name'    => __( 'Background Heading', 'portal_projects' ),
						'type'    => 'color',
						'default' => '#999'
					),
					'portal_background_link'             => array(
						'id'      => 'portal_background_link',
						'name'    => __( 'Background Link', 'portal_projects' ),
						'type'    => 'color',
						'default' => '#3299bb'
					),
					'portal_sub_nav_link'             => array(
						'id'      => 'portal_sub_nav_link',
						'name'    => __( 'Subnav Link', 'portal_projects' ),
						'type'    => 'color',
						'default' => '#555555'
					),
					'portal_sub_nav_link_active'             => array(
						'id'      => 'portal_sub_nav_link_active',
						'name'    => __( 'Subnav Link Active', 'portal_projects' ),
						'type'    => 'color',
						'default' => '#000000'
					),
					'portal_sub_nav_link_accent'             => array(
						'id'      => 'portal_sub_nav_link_accent',
						'name'    => __( 'Subnav Link Accent', 'portal_projects' ),
						'type'    => 'color',
						'default' => '#4ecdc4'
					),
					'portal_body_text'                => array(
						'id'      => 'portal_body_text',
						'name'    => __( 'Body Text', 'portal_projects' ),
						'type'    => 'color',
						'default' => '#333'
					),
					'portal_body_link'                => array(
						'id'      => 'portal_body_link',
						'name'    => __( 'Body Link', 'portal_projects' ),
						'type'    => 'color',
						'default' => '#000'
					),
					'portal_footer_background'        => array(
						'id'      => 'portal_footer_background',
						'name'    => __( 'Footer Background', 'portal_projects' ),
						'type'    => 'color',
						'default' => '#2a3542'
					),
					'portal_timeline_color'           => array(
						'id'      => 'portal_timeline_color',
						'name'    => __( 'Timeline', 'portal_projects' ),
						'type'    => 'color',
						'default' => '#99C262'
					),
					'portal_reset_colors'             => array(
						'id'          => 'portal_reset_colors',
						'name'        => __( 'Reset Colors to Default', 'portal_projects' ),
						'type'        => 'button_link',
						'href'        => '',
						'button_id'   => 'portal-reset-colors',
						'button_text' => __( 'Reset', 'portal_projects' )
					)
				),
				'phases'         => array(
					'portal_settings_appearance_phases' => array(
						'id'   => 'portal_settings_appearance_phases',
						'name' => '<h3>' . __( 'Phases Settings', 'portal_projects' ) . '</h3>',
						'desc' => '',
						'type' => 'header',
					),
					'portal_accent_color_1'             => array(
						'id'      => 'portal_accent_color_1',
						'name'    => __( 'Accent Color #1', 'portal_projects' ),
						'type'    => 'color',
						'default' => '#3299BB'
					),
					'portal_accent_color_1_txt'         => array(
						'id'      => 'portal_accent_color_1_txt',
						'name'    => __( 'Accent Color #1 Text', 'portal_projects' ),
						'type'    => 'color',
						'default' => '#FFFFFF'
					),
					'portal_accent_color_2'             => array(
						'id'      => 'portal_accent_color_2',
						'name'    => __( 'Accent Color #2', 'portal_projects' ),
						'type'    => 'color',
						'default' => '#4ECDC4'
					),
					'portal_accent_color_2_txt'         => array(
						'id'      => 'portal_accent_color_2_txt',
						'name'    => __( 'Accent Color #2 Text', 'portal_projects' ),
						'type'    => 'color',
						'default' => '#FFFFFF'
					),
					'portal_accent_color_3'             => array(
						'id'      => 'portal_accent_color_3',
						'name'    => __( 'Accent Color #3', 'portal_projects' ),
						'type'    => 'color',
						'default' => '#CBE86B'
					),
					'portal_accent_color_3_txt'         => array(
						'id'      => 'portal_accent_color_3_txt',
						'name'    => __( 'Accent Color #3 Text', 'portal_projects' ),
						'type'    => 'color',
						'default' => '#FFFFFF'
					),
					'portal_accent_color_4'             => array(
						'id'      => 'portal_accent_color_4',
						'name'    => __( 'Accent Color #4', 'portal_projects' ),
						'type'    => 'color',
						'default' => '#FF6B6B'
					),
					'portal_accent_color_4_txt'         => array(
						'id'      => 'portal_accent_color_4_txt',
						'name'    => __( 'Accent Color #4 Text', 'portal_projects' ),
						'type'    => 'color',
						'default' => '#FFFFFF'
					),
					'portal_accent_color_5'             => array(
						'id'      => 'portal_accent_color_5',
						'name'    => __( 'Accent Color #5', 'portal_projects' ),
						'type'    => 'color',
						'default' => '#C44D58'
					),
					'portal_accent_color_5_txt'         => array(
						'id'      => 'portal_accent_color_5_txt',
						'name'    => __( 'Accent Color #5 Text', 'portal_projects' ),
						'type'    => 'color',
						'default' => '#FFFFFF'
					),
					'portal_reset_colors'               => array(
						'id'          => 'portal_reset_colors',
						'name'        => __( 'Reset Colors to Default', 'portal_projects' ),
						'type'        => 'button_link',
						'href'        => '',
						'button_id'   => 'portal-reset-colors',
						'button_text' => __( 'Reset', 'portal_projects' )
					)
				),
				'custom_styling' => array(
					'portal_open_css' => array(
						'id'   => 'portal_open_css',
						'name' => __( 'Custom CSS', 'portal_projects' ),
						'type' => 'textarea'
					)
				),/*
				'calendar'       => array(
					'portal_calendar_language' => array(
						'id'      => 'portal_calendar_language',
						'name'    => __( 'Calendar Language', 'portal_projects' ),
						'type'    => 'select',
						'options' => portal_calendar_langauges(),
					),
				),*/
			)
		),
		'portal_settings_notifications' => apply_filters( 'portal_settings_notifications', array(
			'email' => array(
				'portal_email_header'    => array(
					'id'   => 'portal_email_header',
					'name' => '<h2>' . __( 'Default Email Settings', 'portal_projects' ) . '</h2>',
					'type' => 'header',
				),
				'portal_from_name'       => array(
					'id'   => 'portal_from_name',
					'name' => __( 'From Name', 'portal_projects' ),
					'type' => 'text',
					'desc'	=> __( 'Use <code>%current_user%</code> for current user\'s name', 'portal_projects' )
				),
				'portal_from_email'      => array(
					'id'   	=> 'portal_from_email',
					'name' 	=> __( 'From E-Mail', 'portal_projects' ),
					'type' 	=> 'text',
					'desc'	=> __( 'Use <code>%current_user%</code> for current user\'s e-mail', 'portal_projects' )
				),
				'portal_include_logo'    => array(
					'id'   => 'portal_include_logo',
					'name' => __( 'Include Logo', 'portal_projects' ),
					'type' => 'select',
					'options'	=>	array(
						'0'		=>	__( 'No', 'portal_projects' ),
						'1'		=>	__( 'Yes', 'portal_projects' ),
					),

				),
				'portal_default_subject' => array(
					'id'   => 'portal_default_subject',
					'name' => __( 'Subject Line', 'portal_projects' ),
					'type' => 'text',
				),
				'portal_default_message' => array(
					'id'   => 'portal_default_message',
					'name' => __( 'Default Message', 'portal_projects' ),
					'type' => 'textarea',
					'desc' => __( 'Available dynamic variables: <code>%project_title%</code> <code>%client%</code> <code>%project_url%</code> <code>%dashboard%</code> <code>%date%</code>', 'portal_projects' ),
				),
				'portal_message_1' => array(
				  'id'   => 'portal_message_1',
				  'name' => __( 'Message 1', 'portal_projects' ),
				  'type' => 'textarea',
				  'desc' => __( 'Message to be used throughout the notifications system. Use <code>%portal_message_1%</code> to include it.', 'portal_projects' ),
				),
				'portal_message_2' => array(
				  'id'   => 'portal_message_2',
				  'name' => __( 'Message 2', 'portal_projects' ),
				  'type' => 'textarea',
				  'desc' => __( 'Message to be used throughout the notifications system. Use <code>%portal_message_2%</code> to include it.', 'portal_projects' ),
				),
				'portal_message_3' => array(
				  'id'   => 'portal_message_3',
				  'name' => __( 'Message 3', 'portal_projects' ),
				  'type' => 'textarea',
				  'desc' => __( 'Message to be used throughout the notifications system. Use <code>%portal_message_3%</code> to include it.', 'portal_projects' ),
				),
				'portal_message_4' => array(
				  'id'   => 'portal_message_4',
				  'name' => __( 'Message 4', 'portal_projects' ),
				  'type' => 'textarea',
				  'desc' => __( 'Message to be used throughout the notifications system. Use <code>%portal_message_4%</code> to include it.', 'portal_projects' ),
				),
				'portal_message_5' => array(
				  'id'   => 'portal_message_5',
				  'name' => __( 'Message 5', 'portal_projects' ),
				  'type' => 'textarea',
				  'desc' => __( 'Message to be used throughout the notifications system. Use <code>%portal_message_5%</code> to include it.', 'portal_projects' ),
				),
				'portal_message_6' => array(
				  'id'   => 'portal_message_6',
				  'name' => __( 'Message 6', 'portal_projects' ),
				  'type' => 'textarea',
				  'desc' => __( 'Message to be used throughout the notifications system. Use <code>%portal_message_6%</code> to include it.', 'portal_projects' ),
				),
				'portal_message_7' => array(
				  'id'   => 'portal_message_7',
				  'name' => __( 'Message 7', 'portal_projects' ),
				  'type' => 'textarea',
				  'desc' => __( 'Message to be used throughout the notifications system. Use <code>%portal_message_7%</code> to include it.', 'portal_projects' ),
				),
				'portal_message_8' => array(
				  'id'   => 'portal_message_8',
				  'name' => __( 'Message 8', 'portal_projects' ),
				  'type' => 'textarea',
				  'desc' => __( 'Message to be used throughout the notifications system. Use <code>%portal_message_8%</code> to include it.', 'portal_projects' ),
				),
				'portal_message_9' => array(
				  'id'   => 'portal_message_9',
				  'name' => __( 'Message 9', 'portal_projects' ),
				  'type' => 'textarea',
				  'desc' => __( 'Message to be used throughout the notifications system. Use <code>%portal_message_9%</code> to include it.', 'portal_projects' ),
				),
				'portal_message_10' => array(
				  'id'   => 'portal_message_10',
				  'name' => __( 'Message 10', 'portal_projects' ),
				  'type' => 'textarea',
				  'desc' => __( 'Message to be used throughout the notifications system. Use <code>%portal_message_10%</code> to include it.', 'portal_projects' ),
				),
				'portal_message_11' => array(
				  'id'   => 'portal_message_11',
				  'name' => __( 'Message 11', 'portal_projects' ),
				  'type' => 'textarea',
				  'desc' => __( 'Message to be used throughout the notifications system. Use <code>%portal_message_11%</code> to include it.', 'portal_projects' ),
				),
				'portal_message_12' => array(
				  'id'   => 'portal_message_12',
				  'name' => __( 'Message 12', 'portal_projects' ),
				  'type' => 'textarea',
				  'desc' => __( 'Message to be used throughout the notifications system. Use <code>%portal_message_12%</code> to include it.', 'portal_projects' ),
				),
				'portal_message_13' => array(
				  'id'   => 'portal_message_13',
				  'name' => __( 'Message 13', 'portal_projects' ),
				  'type' => 'textarea',
				  'desc' => __( 'Message to be used throughout the notifications system. Use <code>%portal_message_13%</code> to include it.', 'portal_projects' ),
				),
				'portal_message_14' => array(
				  'id'   => 'portal_message_14',
				  'name' => __( 'Message 14', 'portal_projects' ),
				  'type' => 'textarea',
				  'desc' => __( 'Message to be used throughout the notifications system. Use <code>%portal_message_14%</code> to include it.', 'portal_projects' ),
				),
				'portal_message_15' => array(
				  'id'   => 'portal_message_15',
				  'name' => __( 'Message 15', 'portal_projects' ),
				  'type' => 'textarea',
				  'desc' => __( 'Message to be used throughout the notifications system. Use <code>%portal_message_15%</code> to include it.', 'portal_projects' ),
				),
				'portal_message_16' => array(
				  'id'   => 'portal_message_16',
				  'name' => __( 'Message 16', 'portal_projects' ),
				  'type' => 'textarea',
				  'desc' => __( 'Message to be used throughout the notifications system. Use <code>%portal_message_16%</code> to include it.', 'portal_projects' ),
				),
				'portal_message_17' => array(
				  'id'   => 'portal_message_17',
				  'name' => __( 'Message 17', 'portal_projects' ),
				  'type' => 'textarea',
				  'desc' => __( 'Message to be used throughout the notifications system. Use <code>%portal_message_17%</code> to include it.', 'portal_projects' ),
				),
				'portal_message_18' => array(
				  'id'   => 'portal_message_18',
				  'name' => __( 'Message 18', 'portal_projects' ),
				  'type' => 'textarea',
				  'desc' => __( 'Message to be used throughout the notifications system. Use <code>%portal_message_18%</code> to include it.', 'portal_projects' ),
				),
				'portal_message_19' => array(
				  'id'   => 'portal_message_19',
				  'name' => __( 'Message 19', 'portal_projects' ),
				  'type' => 'textarea',
				  'desc' => __( 'Message to be used throughout the notifications system. Use <code>%portal_message_19%</code> to include it.', 'portal_projects' ),
				),
				'portal_message_20' => array(
				  'id'   => 'portal_message_20',
				  'name' => __( 'Message 20', 'portal_projects' ),
				  'type' => 'textarea',
				  'desc' => __( 'Message to be used throughout the notifications system. Use <code>%portal_message_20%</code> to include it.', 'portal_projects' ),
				)
			)
		) ),
		'portal_settings_advanced'      => apply_filters( 'portal_settings_advanced',
			array(
				'main' => array(
					'portal_enable_wp_footer'	=>	array(
						'id'	=>	'portal_enable_wp_footer',
						'name'	=>	__( 'Enable wp_footer call on project pages', 'portal_projects' ),
						'type'	=>	'checkbox',
						'desc'	=>	__( 'If you\'re having issues with shortcodes try turning this on first.', 'portal_projects' )
					),
					'portal_restrict_media_gallery'	=>	array(
						'id'	=>	'portal_restrict_media_gallery',
						'name'	=>	__( 'Project Specific Media Gallery', 'portal_projects' ),
						'type'	=>	'checkbox',
						'desc'	=>	__( 'Enable this if you\'d only like files uploaded to a specific project to show up in the media gallery', 'portal_projects' ),
					),
					'portal_enable_wp_head'	=>	array(
						'id'	=>	'portal_enable_wp_head',
						'name'	=>	__( 'Enable wp_head call on project pages', 'portal_projects' ),
						'type'	=>	'checkbox',
						'desc'	=>	__( 'If you\'re having issues with shortcodes and wp_footer didnt work, try turning this on.', 'portal_projects' )
					),
					'portal_disable_file_obfuscation'	=>	array(
						'id'	=>	'portal_disable_file_obfuscation',
						'name'	=>	__( 'Turn off file link obfuscation', 'portal_projects' ),
						'type'	=>	'checkbox',
						'desc'	=>	__( 'If you\'re having trouble with people downloading documents, try turning this on.', 'portal_projects' )
					),
					'portal_lazyload_wysiwyg'	=>	array(
						'id'	=>	'portal_lazyload_wysiwyg',
						'name'	=>	__( 'Lazy load WYSIWYG editors', 'portal_projects' ),
						'type'	=>	'checkbox',
						'desc'	=>	__( 'If you\'re having trouble with performance while editing projects, try enabling this', 'portal_projects' )
					),
					'portal_disable_clone_post' => array(
						'id'   => 'portal_disable_clone_post',
						'name' => __( 'Disable Clone Post', 'portal_projects' ),
						'type' => 'checkbox',
						'desc' => __( 'If you\'re using the Duplicate Post plugin and getting errors, check this box.', 'portal_projects' )
					),
				)
			)
		),
		'portal_settings_addons'        => apply_filters( 'portal_settings_addons', array() ),
	);

	if( !portal_get_option('edd_yoop_license_key') ) unset( $portal_settings['portal_settings_general']['edd_yoop_activate_license'] );

	return apply_filters( 'portal_registered_settings', $portal_settings );
}

//function

function edd_yoop_license_page() {

	flush_rewrite_rules();

	$settings_tabs = portal_get_settings_tabs();
	$settings_tabs = empty( $settings_tabs ) ? array() : $settings_tabs;
	$active_tab    = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $settings_tabs ) ? $_GET['tab'] : 'portal_settings_general';
	$sections      = portal_get_settings_tab_sections( $active_tab );
	$key           = 'main';
	if ( is_array( $sections ) ) {
		$key = key( $sections );
	}
	$registered_sections = portal_get_settings_tab_sections( $active_tab );
	$section             = isset( $_GET['section'] ) && ! empty( $registered_sections ) && array_key_exists( $_GET['section'], $registered_sections ) ? $_GET['section'] : $key;
	ob_start();
	?>
	<div class="wrap">
		<h1 class="nav-tab-wrapper">
			<?php
			foreach ( portal_get_settings_tabs() as $tab_id => $tab_name ) {
				$tab_url = add_query_arg( array(
					'settings-updated' => false,
					'tab'              => $tab_id,
				) );
				// Remove the section from the tabs so we always end up at the main section
				$tab_url = remove_query_arg( 'section', $tab_url );
				$active  = $active_tab == $tab_id ? ' nav-tab-active' : '';
				echo '<a href="' . esc_url( $tab_url ) . '" title="' . esc_attr( $tab_name ) . '" class="nav-tab' . $active . '">';
				echo esc_html( $tab_name );
				echo '</a>';
			}
			?>
		</h1>
		<?php
		$number_of_sections = count( $sections );
		$number             = 0;
		if ( $number_of_sections > 1 ) {
			echo '<div><ul class="subsubsub">';
			foreach ( $sections as $section_id => $section_name ) {
				echo '<li>';
				$number ++;
				$tab_url = add_query_arg( array(
					'settings-updated' => false,
					'tab'              => $active_tab,
					'section'          => $section_id
				) );
				$class   = '';
				if ( $section == $section_id ) {
					$class = 'current';
				}
				echo '<a class="' . $class . '" href="' . esc_url( $tab_url ) . '">' . $section_name . '</a>';
				if ( $number != $number_of_sections ) {
					echo ' | ';
				}
				echo '</li>';
			}
			echo '</ul></div>';
		}
		?>
		<div id="tab_container">
			<?php if ( apply_filters( 'portal_settings_section_' . $section . '_form', true ) ) : ?>
			<form method="post" action="options.php">
				<?php do_action( 'portal_before_settings_table' ); ?>

				<table class="form-table">
					<?php endif;

					if ( ( 'portal_settings_general' == $active_tab ) && ( 'main' === $section ) ) {
						if ( isset( $_GET['portal_activate_response'] ) ) : ?>
							<div class="portal-status-message">
                            <pre>
                                <?php var_dump( portal_check_activation_response() ); ?>
                            </pre>
							</div>
						<?php endif;
					}

					settings_fields( 'portal_settings' );
					if ( 'main' === $section ) {
						do_action( 'portal_settings_tab_top', $active_tab );
					}
					do_action( 'portal_settings_tab_top_' . $active_tab . '_' . $section );

					if ( apply_filters( 'portal_settings_section_' . $section . '_form', true ) ) {
						do_settings_sections( 'portal_settings_' . $active_tab . '_' . $section );
					} else {
						// If Form is disabled, allow users to chuck whatever they'd like in here
						do_action( 'portal_settings_section_' . $section );
					}

					do_action( 'portal_settings_tab_bottom_' . $active_tab . '_' . $section );
					// For backwards compatibility
					if ( 'main' === $section ) {
						do_action( 'portal_settings_tab_bottom', $active_tab );
					}
					?>
					<?php if ( apply_filters( 'portal_settings_section_' . $section . '_form', true ) ) : ?>
				</table>

				<?php do_action( 'portal_after_settings_table' ); ?>
				<?php

				// WordPress automatically gives the Submit Button an ID and Name of "submit"
				// This is not necessary and causes problems with jQuery submit()
				// https://api.jquery.com/submit/ under "Additional Notes"
				submit_button( null, 'primary', false );

				?>
			</form>
		<?php endif; ?>
		</div><!-- #tab_container-->
	</div><!-- .wrap -->
	<?php
	echo ob_get_clean();

}

function portal_get_project_templates() {

	$templates = wp_get_theme()->get_page_templates();

	if ( ! empty( $templates ) ) :

		$templates['page.php'] = __( 'Standard Page', 'portal_projects' );

	else:

		$templates['single.php'] = __( 'Single Post', 'portal_projects' );

	endif;

	array_unshift( $templates, __( 'Choose Template', 'portal_projects' ) );

	return $templates;

}

function portal_calendar_langauges() {

	$languages = array(
		'en'      => 'en',
		'ar-ma'   => 'ar-ma',
		'ar-sa'   => 'ar-sa',
		'ar-tn'   => 'ar-tn',
		'ar'      => 'ar',
		'bg'      => 'bg',
		'ca'      => 'ca',
		'cs'      => 'cs',
		'da'      => 'da',
		'de-at'   => 'de-at',
		'de'      => 'de',
		'el'      => 'el',
		'en-au'   => 'en-au',
		'en-ca'   => 'en-ca',
		'en-gb'   => 'en-gb',
		'en-ie'   => 'en-ie',
		'en-nz'   => 'en-nz',
		'es'      => 'es',
		'fa'      => 'fa',
		'fi'      => 'fi',
		'fr-ca'   => 'fr-ca',
		'fr-ch'   => 'fr-ch',
		'fr'      => 'fr',
		'he'      => 'he',
		'hi'      => 'hi',
		'hr'      => 'hr',
		'hu'      => 'hu',
		'id'      => 'id',
		'is'      => 'is',
		'it'      => 'it',
		'ja'      => 'ja',
		'ko'      => 'ko',
		'lt'      => 'lt',
		'lv'      => 'lv',
		'nb'      => 'nb',
		'nl'      => 'nl',
		'pl'      => 'pl',
		'pt-br'   => 'pt-br',
		'pt'      => 'pt',
		'ro'      => 'ro',
		'ru'      => 'ru',
		'sk'      => 'sk',
		'sl'      => 'sl',
		'sr-cyrl' => 'sr-cyrl',
		'sr'      => 'sr',
		'sv'      => 'sv',
		'th'      => 'th',
		'tr'      => 'tr',
		'uk'      => 'uk',
		'vi'      => 'vi',
		'zh-cn'   => 'zh-cn',
		'zh-tw'   => 'zh-tw',
	);

	return $languages;

}

add_action( 'admin_init', 'portal_check_if_rewrites_flushed' );
function portal_check_if_rewrites_flushed() {

	$flushed = get_option( 'portal_rewrites_flushed' );

	if ( $flushed != 'yes' ) {
		flush_rewrite_rules();
		update_option( 'portal_rewrites_flushed', 'yes' );
	}

}


/************************************
 * this illustrates how to activate
 * a license key
 *************************************/

function edd_yoop_activate_license() {

	// listen for our activate button to be clicked
	if ( isset( $_POST['edd_license_activate'] ) ) {

		// run a quick security check
		if ( ! check_admin_referer( 'edd_yoop_nonce', 'edd_yoop_nonce' ) ) {
			return;
		} // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = get_option( 'portal_settings' );
		$license = trim( $license['edd_yoop_license_key'] );

		if( isset($_POST['edd_yoop_license_key']) ) $license = $_POST['edd_yoop_license_key'];

		if( isset( $_POST['portal_settings']['edd_yoop_license_key'] ) ) {
			$license = $_POST['portal_settings']['edd_yoop_license_key'];
		}

		// data to send in our API request
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_name'  => urlencode( EDD_PROJECT_YOOP ), // the name of our product in EDD
			'url'        => home_url()
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, PROJECT_YOOP_STORE_URL ), array(
			'timeout'   => 15,
			'sslverify' => false
		) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

				$error_message = $response->get_error_message();

				if( is_wp_error( $response ) && ! empty( $error_message ) ) {
					$message = $response->get_error_message();
				} else {
					$message = __( 'An error occurred, please try again.', 'portal_projects' );
				}

			} else {
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );
				if ( false === $license_data->success ) {
					switch( $license_data->error ) {
						case 'expired' :
							$message = sprintf(
								__( 'Your license key expired on %s.' ),
								date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
							);
							break;
						case 'revoked' :
							$message = __( 'Your license key has been disabled.', 'portal_projects' );
							break;
						case 'missing' :
							$message = __( 'Invalid license.', 'portal_projects' );
							break;
						case 'invalid' :
						case 'site_inactive' :
							$message = __( 'Your license is not active for this URL.', 'portal_projects' );
							break;
						case 'item_name_mismatch' :
							$message = sprintf( __( 'This appears to be an invalid license key for %s.', 'portal_projects' ), EDD_PROJECT_YOOP );
							break;
						case 'no_activations_left':
							$message = __( 'Your license key has reached its activation limit.', 'portal_projects' );
							break;
						default :
							$message = __( 'An error occurred, please try again.', 'portal_projects' );
							break;
					}
				}
			}
			// Check if anything passed on a message constituting a failure
			if ( ! empty( $message ) ) {
				$base_url = admin_url( 'options-general.php?page=' . PORTAL_PLUGIN_LICENSE_PAGE );
				$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );
				wp_redirect( $redirect );
				exit();
			}

		// $license_data->license will be either "active" or "inactive"
		update_option( 'edd_yoop_license_status', $license_data->license );

	}


}

add_action( 'admin_init', 'edd_yoop_activate_license', 0 );

function portal_check_activation_response() {

	// retrieve the license from the database
	$license = get_option( 'portal_settings' );
	$license = trim( $license['edd_yoop_license_key'] );

	// data to send in our API request
	$api_params = array(
		'edd_action' => 'activate_license',
		'license'    => $license,
		'item_name'  => urlencode( EDD_PROJECT_YOOP ), // the name of our product in EDD
		'url'        => home_url()
	);

	// Call the custom API.
	$response = wp_remote_get( add_query_arg( $api_params, PROJECT_YOOP_STORE_URL ), array(
		'timeout'   => 15,
		'sslverify' => false
	) );

	return $response;

}


/***********************************************
 * Illustrates how to deactivate a license key.
 * This will descrease the site count
 ***********************************************/

function edd_yoop_deactivate_license() {

	// listen for our activate button to be clicked
	if ( isset( $_POST['edd_license_deactivate'] ) ) {

		// run a quick security check
		if ( ! check_admin_referer( 'edd_yoop_nonce', 'edd_yoop_nonce' ) ) {
			return;
		} // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = get_option( 'portal_settings' );
		$license = trim( $license['edd_yoop_license_key'] );

		// data to send in our API request
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license,
			'item_name'  => urlencode( EDD_PROJECT_YOOP ) // the name of our product in EDD
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, PROJECT_YOOP_STORE_URL ), array(
			'timeout'   => 15,
			'sslverify' => false
		) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) ) {
			return false;
		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if ( $license_data->license == 'deactivated' ) {
			delete_option( 'edd_yoop_license_status' );
		}

	}
}

add_action( 'admin_init', 'edd_yoop_deactivate_license' );


/************************************
 * this illustrates how to check if
 * a license key is still valid
 * the updater does this for you,
 * so this is only needed if you
 * want to do something custom
 *************************************/

function edd_yoop_check_license() {

	global $wp_version;

	$license = get_option( 'portal_settings' );
	$license = trim( $license['edd_yoop_license_key'] );

	$api_params = array(
		'edd_action' => 'check_license',
		'license'    => $license,
		'item_name'  => urlencode( EDD_PROJECT_YOOP )
	);

	// Call the custom API.
	$response = wp_remote_get( add_query_arg( $api_params, PROJECT_YOOP_STORE_URL ), array(
		'timeout'   => 15,
		'sslverify' => false
	) );

	if ( is_wp_error( $response ) ) {
		return false;
	}

	$license_data = json_decode( wp_remote_retrieve_body( $response ) );

	if ( $license_data->license == 'valid' ) {
		echo 'valid';
		exit;
		// this license is still valid
	} else {
		echo 'invalid';
		exit;
		// this license is no longer valid
	}
}

function portal_license_admin_notices() {
	if ( isset( $_GET['sl_activation'] ) && ! empty( $_GET['message'] ) ) {
		switch( $_GET['sl_activation'] ) {
			case 'false':
				$message = urldecode( $_GET['message'] );
				?>
				<div class="error">
					<p><?php echo $message; ?></p>
				</div>
				<?php
				break;
			case 'true':
			default:
				// Developers can put a custom success message here for when activation is successful if they way.
				break;
		}
	}
}
add_action( 'admin_notices', 'portal_license_admin_notices' );
