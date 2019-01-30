<?php
/**
 * EDD purchase details Loader class.
 *
 * @package EDD-PD
 */

// Block direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Edd_Purchase_Details_Loader' ) ) {

	/**
	 * Class Edd_Purchase_Details_Loader.
	 *
	 * @since 0.0.1
	 */
	final class Edd_Purchase_Details_Loader {

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
		 * @since 0.0.1
		 *
		 * @return void
		 */
		public function filter_plugins_loaded() {
			if ( class_exists( 'Easy_Digital_Downloads' ) ) {
					require_once EDD_PD . 'classes/class-edd-purchase-details-frontend.php';
					require_once EDD_PD . 'classes/class-edd-purchase-details-admin.php';
					add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
			} else {
				add_action( 'admin_notices', array( $this, 'fails_to_load' ) );
			}
		}

		/**
		 * Fires admin notice when Elementor is not installed and activated.
		 *
		 * @since 0.0.1
		 *
		 * @return void
		 */
		public  function fails_to_load() {
			$class = 'notice notice-error';
			/* translators: %s: html tags */
			$message = sprintf( __( 'The %1$s EDD Purchase Details %2$s plugin requires %1$s Easy Digital Downloads %2$s plugin installed & activated.', 'edd-purchase-details' ), '<strong>', '</strong>' );

			printf( '<div class="%1$s"><p>%2$s</p>%3$s</div>', esc_attr( $class ), $message, '' );
		}


		/**
		 * Load Edd Purchase Details
		 * This will load the translation textdomain depending on the file priorities.
		 *      1. Global Languages /wp-content/languages/edd-purchase-details/ folder.
		 *      2. Local dorectory /wp-content/plugins/edd-purchase-details/languages/ folder.
		 *
		 * @since  0.0.1
		 * @return void
		 */
		public function load_textdomain() {
			// Default languages directory for edd-purchase-details.
			$lang_dir = EDD_PD_PLUGIN_FILE . 'languages/';

			/**
			 * Filters the languages directory path to use for edd-purchase-details.
			 *
			 * @param string $lang_dir The languages directory path.
			 */
			$lang_dir = apply_filters( 'edd_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter.
			global $wp_version;

			$get_locale = get_locale();

			if ( $wp_version >= 4.7 ) {
				$get_locale = get_user_locale();
			}

			/**
			 * Language Locale for EDD purchase details
			 *
			 * @var $get_locale The locale to use. Uses get_user_locale()` in WordPress 4.7 or greater,
			 *                  otherwise uses `get_locale()`.
			 */
			$locale = apply_filters( 'plugin_locale', $get_locale, 'edd-purchase-details' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'edd-purchase-details', $locale );

			// Setup paths to current locale file.
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/plugins/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/edd-purchase-details/ folder.
				load_textdomain( 'edd-purchase-details', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/edd-purchase-details/languages/ folder.
				load_textdomain( 'edd-purchase-details', $mofile_local );
			} else {
				// Load the default language files.
				load_plugin_textdomain( 'edd-purchase-details', false, $lang_dir );
			}
		}


	}
	Edd_Purchase_Details_Loader::get_instance();
}
