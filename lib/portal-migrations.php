<?php
/**
 * Handles plugin upgrades.
 *
 * @since {{VERSION}}
 */

defined( 'ABSPATH' ) || die();

/**
 * Class portal_DB_Upgrade
 *
 * Handles plugin upgrades.
 *
 * @since {{VERSION}}
 */
class portal_DB_Upgrade {

	/**
	 * portal_DB_Upgrade constructor.
	 *
	 * @since {{VERSION}}
	 *
	 * @return bool True if needs to upgrade, false if does not.
	 */
	function __construct() {
		
		// If we're rolling back, don't check anything else
		if ( isset( $_GET['portal_db_rollback'] ) ) {
			
			add_action( 'admin_init', array( $this, 'database_rollback' ) );
			
		}
		else {
			
			add_action( 'admin_init', array( $this, 'check_upgrades' ) );
			
			if ( isset( $_GET['portal_upgrade_db'] ) ) {

				add_action( 'admin_init', array( $this, 'do_upgrades' ) );

			}

			if ( isset( $_GET['portal_lite_upgrade'] ) ) {

				add_action( 'admin_init', array( $this, 'lite_upgrade' ) );

			}

			if ( isset( $_GET['portal_db_upgraded'] ) ) {

				add_action( 'admin_notices', array( $this, 'show_upgraded_message' ) );

			}
			
		}
		
	}
	
	/**
	 * This is a slight misnomer. It only rolls back the saved Database Version. It does NOT rollback changes made by the upgrade routines.
	 * This can be used to re-run database migration scripts or to skip them. Use at your own risk.
	 * 
	 * @since {{VERSION}}
	 * @access public
	 */
	public function database_rollback() {
		
		$ver = preg_replace( '/\D/', '', $_GET[ 'portal_db_rollback' ] );
		
		update_option( 'portal_database_version', $ver );
		
		wp_safe_redirect( remove_query_arg( 'portal_db_rollback' ) );
		exit();
		
	}

	/**
	 * Checks for upgrades and migrations.
	 *
	 * @since {{VERSION}}
	 * @access public
	 */
	public function check_upgrades() {

		$current_db_version = get_option( 'portal_database_version', 0 );

		// Includes legacy upgrade scripts for those that need them
		if ( $current_db_version ) {

			foreach ( $this->get_upgrades() as $upgrade_version => $upgrade_callback ) {

				if ( $current_db_version < $upgrade_version ) {

					add_action( 'admin_notices', array( $this, 'show_upgrade_nag' ) );
					break;
					
				}
				
			}
			
		}
		else {
			
			if ( get_option( 'portal_lite_migration' ) != 1 ) { // Upgrading from Lite

				$lite_projects = new WP_Query( array( 'post_type' => 'portal_projects', 'meta_key' => '_portal_lite_project', 'meta_value' => '1' ) );

				if ( $lite_projects->found_posts > 0 ) { 
					add_action( 'admin_notices', array( $this, 'show_lite_upgrade_nag' ) );
				}

			}
			else { // New Install
			
				update_option( 'portal_database_version', PORTAL_DB_VER );
				
			}
			
		}

	}

	/**
	 * Runs upgrades.
	 *
	 * @since {{VERSION}}
	 * @access public
	 */
	public function do_upgrades() {

		$current_db_version = get_option( 'portal_database_version', 0 );

		foreach ( $this->get_upgrades() as $upgrade_version => $upgrade_callback ) {

			if ( $current_db_version < $upgrade_version ) {

				call_user_func( $upgrade_callback );
				update_option( 'portal_database_version', $upgrade_version );
				
			}
			
		}
		
		$url = remove_query_arg( 'portal_upgrade_db' );
		
		wp_safe_redirect( add_query_arg( 'portal_db_upgraded', '1', $url ) );
		exit();
		
	}

	/**
	 * Returns an array of all versions that require an upgrade.
	 *
	 * @since {{VERSION}}
	 * @access private
	 *
	 * @return array
	 */
	private function get_upgrades() {

		return array(
			4 => array( $this, 'upgrade_db_4' ),
			5 => array( $this, 'upgrade_db_5' ),
			6 => array( $this, 'upgrade_db_6' ),
			7 => array( $this, 'upgrade_db_7' ),
			8 => array( $this, 'upgrade_db_8' ),
		);
		
	}

	/**
	 * Displays upgrade nag.
	 *
	 * @since {{VERSION}}
	 * @access public
	 */
	public function show_upgrade_nag() {
		?>
        <div class="notice notice-warning">
			<p><img src="<?php echo plugins_url() . '/yoop-project-portal/dist/assets/images/yoop-logo.png'; ?>" width="100" alt="Project yoop"></p>
			<p><?php _e( 'Project yoop needs to update your projects to support new features. <strong>IMPORTANT: Backup your site before continuing.</strong>', 'portal_projects' ); ?> <a href="<?php echo add_query_arg( 'portal_upgrade_db', '1' ); ?>"><?php _e( 'Click here to upgrade', 'portal_projects' ); ?></a>.</p>
        </div>
		<?php
	}
	
	/**
	 * Displays Lite Version upgrade nag.
	 *
	 * @since {{VERSION}}
	 * @access public
	 */
	public function show_lite_upgrade_nag() {
		?>
        <div class="notice notice-warning">
			<p><img src="<?php echo plugins_url() . '/yoop-project-portal/dist/assets/images/yoop-logo.png'; ?>" width="100" alt="Project yoop"></p>
			<p><?php _e( 'Project yoop needs to migrate your Lite projects to premium.', 'portal_projects' ); ?> <a href="<?php echo add_query_arg( 'portal_upgrade_lite', '1' ); ?>"><?php _e( 'Click here to migrate', 'portal_projects' ); ?></a></p>
        </div>
		<?php
	}

	/**
	 * Displays the upgraded complete message.
	 *
	 * @since {{VERSION}}
	 * @access public
	 */
	public function show_upgraded_message() {
		?>
        <div class="notice notice-success">
			<p><img src="<?php echo plugins_url() . '/yoop-project-portal/dist/assets/images/yoop-logo.png'; ?>" width="100" alt="Project yoop"></p>
			<p><?php _e( 'yoop has successfully updated your projects.', 'portal_projects' ); ?></p>
        </div>
		<?php
	}
	
	/**
	 * Upgrades from portal Lite to portal Pro
	 * 
	 * @since {{VERSION}}
	 * @access public
	 */
	public function lite_upgrade() {
		
		$lite_projects = new WP_Query(array('post_type' => 'portal_projects', 'meta_key' => '_portal_lite_project', 'meta_value' => '1','posts_per_page' => -1));

		while($lite_projects->have_posts()): $lite_projects->the_post();

			global $post;

			$phases 	= get_post_meta($post->ID,'_pano_phases',true);
			$documents 	= get_post_meta($post->ID,'_pano_documents',true);

			if(!empty($phases)) {

				$acf_phases = array();

				foreach($phases as $phase) {

					if(isset($phase['percentage_complete'])) {
						$complete = $phase['percentage_complete'];
					} else {
						$complete = 0;
					}

					$phase_fields = array(
						'title'			=>	$phase['title'],
						'description'	=>	$phase['description'],
						'percent_complete'	=>	$complete
					);

					array_push($acf_phases,$phase_fields);

				}

				update_field('field_527d5dc12fa29',$acf_phases,$post->ID);

			}


			if(!empty($documents)) {

				$acf_documents = array();

				foreach($documents as $doc) {

					// Set basics

					if(isset($doc['link'])) { $link = $doc['link']; } else { $link = ''; }
					if(isset($doc['file_id'])) { $file = $doc['file_id']; } else { $file = ''; }

					$doc_array = array(
						'title'			=>		$doc['title'],
						'status'		=>		$doc['status'],
						'description'	=>		$doc['description'],
						'url'			=>		$link,
						'file'			=>		$file
					);

					array_push($acf_documents,$doc_array);

				}

				update_field('field_52a9e4634b147',$acf_documents,$post->ID);

			}

			update_post_meta($post->ID,'_portal_lite_project','0');

		endwhile;

		update_option( 'portal_lite_migration', '1' );
		
		$url = remove_query_arg( 'portal_lite_upgrade' );
		
		wp_safe_redirect( add_query_arg( 'portal_db_upgraded', '1', $url ) );
		exit();
		
	}

	/**
	 * DB v4 upgrade script.
	 *
	 * @since {{VERSION}}
	 * @access public
	 */
	public function upgrade_db_4() {

		$projects = new WP_Query(array('post_type' => 'portal_projects', 'posts_per_page' => -1));

		while ($projects->have_posts()): $projects->the_post();

			global $post;

			$auto_progress = 0;

			// Update the phases progress

			while (have_rows('phases')): the_row();

				if (get_sub_field('auto_progress')) {
					$auto_progress = 1;
				}

			endwhile;

			// Update auto progress

			if ($auto_progress == 1) {
				update_field('field_5436e7f4e06b4', 'Yes',$post->ID);
			}

			// Mark the project as complete or not

			if(portal_compute_progress($post->ID) == 100) {

				wp_set_post_terms($post->ID,'completed','portal_status');

			}

			// Check for old milestones

			$milestones = array();

			if((get_field('milestone_frequency') == 'quarters') && (get_field('display_milestones'))) {

				$milestones[0] = array(
					'occurs'		=>		'25',
					'title'			=>		get_field('25%_title'),
					'description'	=>		get_field('25%_description')
				);

				$milestones[1] = array(
					'occurs'		=>		'50',
					'title'			=>		get_field('50%_title'),
					'description'	=>		get_field('50%_description')
				);

				$milestones[2] = array(
					'occurs'		=>		'75',
					'title'			=>		get_field('75%_title'),
					'description'	=>		get_field('75%_description')
				);

			} elseif((get_field('milestone_frequency') == 'fifths') && (get_field('display_milestones'))) {

				$milestones[0] = array(
					'occurs'		=>		'20',
					'title'			=>		get_field('25%_title'),
					'description'	=>		get_field('25%_description')
				);

				$milestones[1] = array(
					'occurs'		=>		'40',
					'title'			=>		get_field('50%_title'),
					'description'	=>		get_field('50%_description')
				);

				$milestones[2] = array(
					'occurs'		=>		'60',
					'title'			=>		get_field('75%_title'),
					'description'	=>		get_field('75%_description')
				);

				$milestones[3] = array(
					'occurs'		=>		'80',
					'title'			=>		get_field('100%_title'),
					'description'	=>		get_field('100%_description')
				);

			}

			if(count($milestones) > 0) {

				update_field('field_563d1e50786e6',$milestones,$post->ID);

			}

		endwhile;
		
	}
	
	/**
	 * DB v5 upgrade script.
	 *
	 * @since {{VERSION}}
	 * @access public
	 */
	public function upgrade_db_5() {
		
		$projects = new WP_Query(array('post_type' => 'portal_projects', 'posts_per_page' => -1));

		while( $projects->have_posts() ) { $projects->the_post();

			$phases         = get_field( 'phases' );
			$new_phases     = array();

			global $post;

			foreach( $phases as $phase ) {

				if( empty( $phase['phase-comment-key'] ) ) {

					$phase['phase-comment-key'] = uniqid();

				}

				$new_phases[] = $phase;

			}

			update_field( 'field_527d5dc12fa29', $new_phases, $post->ID );

		}
		
	}
	
	/**
	 * DB v6 upgrade script.
	 *
	 * @since {{VERSION}}
	 * @access public
	 */
	public function upgrade_db_6() {
		
		$old_settings = array(
			'edd_yoop_license_key',
			'edd_yoop_license_status',
			'portal_slug',
			'portal_logo',
			'portal_flush_rewrites',
			'portal_rewrites_flushed',
			'portal_back',
			'portal_from_name',
			'portal_from_email',
			'portal_default_subject',
			'portal_default_message',
			'portal_include_logo',
			'portal_header_background',
			'portal_header_text',
			'portal_menu_background',
			'portal_menu_text',
			'portal_header_accent',
			'portal_body_background',
			'portal_body_text',
			'portal_body_link',
			'portal_body_heading',
			'portal_footer_background',
			'portal_accent_color_1',
			'portal_accent_color_1_txt',
			'portal_accent_color_2',
			'portal_accent_color_2_txt',
			'portal_accent_color_3',
			'portal_accent_color_3_txt',
			'portal_accent_color_4',
			'portal_accent_color_4_txt',
			'portal_accent_color_5',
			'portal_accent_color_5_txt',
			'portal_timeline_color',
			'portal_use_custom_template',
			'portal_custom_template',
			'portal_open_css',
			'portal_disable_js',
			'portal_disable_clone_post',
			'portal_calendar_language'
		);

		$portal_settings = array();

		foreach( $old_settings as $setting ) {

			$value = get_option( $setting );

			if( !empty( $value ) ) {

				// Convert old checkboxes to new checkboxes
				if( $value == 'on' ) { $value = 1; }

				$portal_settings = array_merge( $portal_settings, array( $setting => $value ) );

			}

		}

		update_option( 'portal_settings', $portal_settings );
		
	}
	
	/**
	 * DB v7 upgrade script.
	 *
	 * @since {{VERSION}}
	 * @access public
	 */
	public function upgrade_db_7() {
		
		$args = array(
			'post_type'			=>	'portal_projects',
			'posts_per_page'	=>	-1,
			'fields'			=>	'ids',
			'cache_results'		=>	false,
			'no_found_rows'		=>	true
		);

		$projects = get_posts( $args );

		foreach( $projects as $post_id ) {

			if( !get_field( 'restrict_access_to_specific_users', $post_id ) ) continue;

			$current_users      = portal_get_project_users( $post_id );

			foreach( $current_users as $user ) {

				$current_user_ids[] = $user[ 'ID' ];

			}

			update_post_meta( $post_id, '_portal_assigned_users', $current_user_ids );

		}
		
	}
	
	/**
	 * DB v8 upgrade script.
	 *
	 * @since {{VERSION}}
	 * @access public
	 */
	public function upgrade_db_8() {
		
		global $wpdb;
		
		$args = array(
			'post_type'			=>	'portal_projects',
			'posts_per_page'	=>	-1,
			'fields'			=>	'ids',
			'cache_results'		=>	false,
			'no_found_rows'		=>	true
		);

		$projects = get_posts( $args );
		
		foreach ( $projects as $project_id ) {
			
			$phases = get_field( 'phases', $project_id );
			$documents = get_field( 'documents', $project_id );
			
			foreach ( $phases as $phase_index => $phase ) {
				
				$phases[ $phase_index ]['phase_id'] = portal_generate_phase_id();
				
				// Phase Comment Key was basically identical in function to Phase ID, but less clear in naming
				// The new Phase ID is more unique
				$phase_comment_key = get_post_meta( $project_id, 'phases_' . $phase_index . '_phase-comment-key', true );
				$phase_comments = portal_get_phase_comments( $phase_comment_key, $project_id );
				
				if ( $phase_comments ) {
					
					foreach ( $phase_comments as $phase_comment ) {
						
						// Update the Phase Comment Key to equal the new Phase ID
						update_comment_meta( $phase_comment->comment_ID, 'phase-key', $phases[ $phase_index ]['phase_id'] );
						
					}
					
				}
				
				if ( $documents ) {
					
					foreach ( $documents as $document_index => $document ) {
						
						// Same here with converting the Phase Comment Key into the Phase ID
						if ( $document['document_phase'] == $phase_comment_key ) {
							
							$documents[ $document_index ]['document_phase'] = $phases[ $phase_index ]['phase_id'];
							
						}
						
					}
					
				}
				
				foreach ( $phases[ $phase_index ]['tasks'] as $task_index => $task ) {
					
					$phases[ $phase_index ]['tasks'][ $task_index ]['task_id'] = portal_generate_task_id();
					
				}
				
			}
			
			update_field( 'phases', $phases, $project_id );
			update_field( 'documents', $documents, $project_id );
			
		}
				
		// Phase Comment Key now removed from DB
		$sql = $wpdb->delete( 
			$wpdb->postmeta, 
			array(
				'meta_key' => '_phases_%s_phase-comment-key',
				'meta_key' => 'phases_%s_phase-comment-key',
			)
		);
		
	}
	
}

$instance = new portal_DB_Upgrade();