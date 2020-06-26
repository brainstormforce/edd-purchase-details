<?php
/**
 * Plugin Name: Easy Digital Downloads - Purchase Details
 * Author: Brainstorm Force
 * Author URI: https://www.brainstormforce.com
 * Version: 1.0.3
 * Description: Easy Digital Downloads Access to Purchase Details
 * Text Domain: edd-purchase-details
 *
 * @package EDD-PD
 */

// Block direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define constants.
 */
define( 'EDD_PD_VER', '1.0.3' );
define( 'EDD_PD_FILE', __FILE__ );
define( 'EDD_PD', plugin_dir_path( __FILE__ ) );
define( 'EDD_PD_URL', plugin_dir_url( __FILE__ ) );

/**
 * Plugin loader
 */
require_once 'classes/class-edd-purchase-details-loader.php';
