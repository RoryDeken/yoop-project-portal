<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants
define( 'portal_SN_VERSION', '1.0.3' );
define( 'portal_SN_DIR', plugin_dir_path( __FILE__ ) );
define( 'portal_SN_URL', plugins_url( '', __FILE__ ) );
define( 'portal_SN_FILE', __FILE__ );
define( 'portal_SN_ITEM_ID', 4503 );

if ( ! class_exists( 'Project_yoop_SN' ) ) {

	/**
	 * Class Project_yoop_SN
	 *
	 * Initiates the plugin.
	 *
	 * @since   0.1.0
	 *
	 * @package Project_yoop_SN
	 */
	class Project_yoop_SN {

		/**
		 * @var			array $plugin_data Holds Plugin Header Info
		 * @since		1.0.0
		 */
		public $plugin_data;

		/**
		 * Admin notices to show.
		 *
		 * @since 1.0.0
		 *
		 * @var array
		 */
		private $admin_notices;

		/**
		 * Admin module.
		 *
		 * @since 1.0.0
		 *
		 * @var portal_SN_Admin
		 */
		public $admin;

		/**
		 * Support module.
		 *
		 * @since 1.0.0
		 */
		public $support;

		private function __clone() {
		}

		/**
		 * Returns the *Singleton* instance of this class.
		 *
		 * @since     0.1.0
		 *
		 * @staticvar Singleton $instance The *Singleton* instances of this class.
		 *
		 * @return Project_yoop_SN The *Singleton* instance.
		 */
		public static function getInstance() {

			static $instance = null;

			if ( null === $instance ) {
				$instance = new static();
			}

			return $instance;
		}

		/**
		 * Initializes the plugin.
		 *
		 * @since 0.1.0
		 */
		protected function __construct() {

			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once ABSPATH . '/wp-admin/includes/plugin.php';
			}

			// Only call this once, accessible always
			$this->plugin_data = get_plugin_data( __FILE__ );
			/**
			 * Minimum Project yoop version required for Project yoop SN to load.
			 *
			 * @since 0.1
			 */
			$portal_min_ver = apply_filters( 'portal_sn_min_portal_ver', '1.5.1' );

			if ( ! defined( 'portal_PLUGIN_TYPE' ) || strtolower( portal_PLUGIN_TYPE ) != 'professional' ) {

			//	$this->admin_notices[] = __( 'Project yoop - Scheduled Notifications requires the professional version of Project yoop.', 'portal_sn' );
/*
				if ( ! has_action( 'admin_notices', array( $this, 'admin_notices' ) ) ) {
					add_action( 'admin_notices', array( $this, 'admin_notices' ) );
				}
*/
				return;
			}

			if ( ! defined( 'PORTAL_VER' ) || version_compare( PORTAL_VER, $portal_min_ver ) < 0 ) {
/*
				$this->admin_notices[] = sprintf(
					__( 'Project yoop - Scheduled Notifications requires at least version %s of Project yoop.', 'portal_sn' ),
					"<code>$portal_min_ver</code>"
				);

				if ( ! has_action( 'admin_notices', array( $this, 'admin_notices' ) ) ) {
					add_action( 'admin_notices', array( $this, 'admin_notices' ) );
				}
*/
				return;
			}

			$this->require_necessities();
			$this->hooks();
		}

		public function hooks() {
			// Create new time intervals for cron scheduling
			add_filter( 'cron_schedules', 'portal_sn_cron_intervals' );
			// Create or update scheduled events when they're saved in the plugin settings
			add_action( 'added_post_meta', 'portal_sn_save_cron', 11, 4 );
			add_action( 'updated_post_meta', 'portal_sn_save_cron', 11, 4 );
			// Trigger the emails when the scheduled event is run
			add_action( 'portal_scheduled_notifications', 'portal_sn_send_email' );
			// Remove scheduled events when they're deleted or the plugin is deactivated
			add_action( 'delete_post', 'portal_sn_remove_cron', 10, 1 );
			register_deactivation_hook( __FILE__, 'portal_sn_remove_cron' );
			// Add new email string replacements
            add_filter( 'portal_notifications_replacements', 'portal_sn_add_new_replacements', 10, 4 );
		}

		/**
		 * Requires necessary base files.
		 *
		 * @since 0.1.0
		 */
		public function require_necessities() {

			if ( is_admin() ) {

				require_once portal_SN_DIR . 'includes/admin/class-portal-sn-admin.php';
				$this->admin = new portal_SN_Admin();
			}
/*
			// Change Prefix for RBP Support Object
			add_filter( 'rbp_support_prefix', array( $this, 'change_rbp_support_prefix' ) );

			require_once __DIR__ . '/includes/rbp-support/rbp-support.php';
			$this->support = new RBP_Support( portal_SN_FILE, array() );

			// Revert this change so that it won't harm any future potential instances of the object
			remove_filter( 'rbp_support_prefix', array( $this, 'change_rbp_support_prefix' ) );
			*/
		}

		/**
		 * Show admin notices.
		 *
		 * @since 1.0.0
		 * @access private
		 */

		function admin_notices() {
			?>
			<div class="error">
				<?php foreach ( $this->admin_notices as $notice ) : ?>
					<p>
						<?php echo $notice; ?>
					</p>
				<?php endforeach; ?>
			</div>
			<?php
		}

		/**
		 * We are going to alter the Prefix to match what it was before the Support Module was included
		 *
		 * @param		string $prefix RBP_Support Prefix
		 *
		 * @access		public
		 * @since		1.0.0
		 * @return		string RBP_Support Prefix
		 */
		public function change_rbp_support_prefix( $prefix ) {

			return 'portal_sn';

		}
	}

	add_action( 'plugins_loaded', 'portal_sn_load', 1000 ); // Load after portal (999)

	/**
	 * Loads the plugin.
	 *
	 * @since 0.1
	 * @access private
	 */
	function portal_sn_load() {
		require_once __DIR__ . '/includes/portal-sn-functions.php';
		portal_scheduled_notifications();
	}
}
