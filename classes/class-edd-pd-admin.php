<?php
/**
 * Admin  loader file.
 *
 * @package EDD Purchase details
 */

if ( ! class_exists( 'EDD_PD_Admin' ) ) {
	/**
	 * EDD Purchase admin details initial setup
	 *
	 * @since 0.0.1
	 */
	class EDD_PD_Admin {
		/**
		 * Constructor
		 *
		 * @since 0.0.1
		 * @return void
		 */
		public function __construct() {
			// Activation hook.
			add_action( 'admin_menu', array( $this, 'setup_menu' ) );
			add_action( 'admin_init', array( $this, 'save_edd_pd_options' ) );
		}

		/**
		 * Display menu in dashbord
		 *
		 * @since 0.0.1
		 */
		function setup_menu() {
			add_menu_page( 'Plugin Settings', 'EDD PS Settings', 'manage_options', 'Settings-page-dashboard', array( $this, 'setting_page' ) );
		}

		/**
		 * Admin setting user access page display
		 *
		 * @since 0.0.1
		 */
		function setting_page() {
			require_once EDD_PD . 'include/admin-setting-user-access.php';
		}

		/**
		 * Add option data
		 *
		 * @since 0.0.1
		 */
		function save_edd_pd_options() {
			register_setting( 'edd_pd_save_setting', 'user_access' );
		}
	}
}
$masterpage_obj = new EDD_PD_Admin();

