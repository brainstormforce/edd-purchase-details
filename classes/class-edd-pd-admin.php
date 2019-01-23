<?php
/**
 * Admin  loader file.
 *
 * @package EDD Purchase details
 */

// Block direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
			add_action( 'admin_init', array( $this, 'save_access_payment_history' ) );
			add_action( 'admin_menu', array( $this, 'add_submenu' ) );

		}

		/**
		 * Add submenu under "Downloads" in dashbord
		 *
		 * @since 0.0.1
		 */
		function add_submenu() {
			add_submenu_page( 'edit.php?post_type=download', __( ' Access Payment History', 'edd-purchase-details' ), __( 'Access Payment History', 'edd-purchase-details' ), 'manage_shop_settings', 'admin-setting-user-access', array( $this, 'setting_page' ) );
		}


		/**
		 * Setting page render
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
		function save_access_payment_history() {
			register_setting( 'options_access_payment_history', 'user_access' );
		}
	}
}
$masterpage_obj = new EDD_PD_Admin();

