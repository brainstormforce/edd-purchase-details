<?php
/**
 * EDD purchase details Loader class.
 *
 * @package EDDPD
 */

// Block direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Access_Edd_Purchase_Details_Loader' ) ) {

	/**
	 * Class Access_Edd_Purchase_Details_Loader.
	 *
	 * @since 0.0.1
	 */
	final class Access_Edd_Purchase_Details_Loader {

		/**
		 * Class instance.
		 *
		 * @access private
		 * @var $instance Class instance.
		 */
		private static $instance;

		/**
		 * Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 *  Constructor
		 */
		public function __construct() {
			add_filter( 'plugins_loaded', array( $this, 'filter_plugins_loaded' ), 9999, 0 );
		}

		/**
		 * Loads plugin files.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function filter_plugins_loaded() {
			if ( class_exists( 'Easy_Digital_Downloads' ) && class_exists( 'EDD_Software_Licensing' ) ) {
					require_once EDD_PD . 'classes/class-edd-pd-frontend.php';
					require_once EDD_PD . 'classes/class-edd-pd-admin.php';
			} else {
				add_action( 'admin_notices', array( $this, 'fails_to_load' ) );
			}
		}

		/**
		 * Fires admin notice when Elementor is not installed and activated.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public  function fails_to_load() {
			$class = 'notice notice-error';
			/* translators: %s: html tags */
			$message = sprintf( __( 'The %1$s Access to Purchase Details %2$s plugin requires %1$s Easy Digital Downloads %2$s & %1$s Easy Digital Downloads - Software Licensing %2$s  plugin installed & activated.', 'cartflows' ), '<strong>', '</strong>' );

			printf( '<div class="%1$s"><p>%2$s</p>%3$s</div>', esc_attr( $class ), $message, '' );
		}

	}
	Access_Edd_Purchase_Details_Loader::get_instance();
}

