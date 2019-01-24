<?php
/**
 * Plugin Name: Easy Digital Downloads - Access to Purchase Details
 * Plugin URI: https://www.brainstormforce.com
 * Author: Brainstorm Force
 * Author URI: https://www.brainstormforce.com
 * Version: 0.0.1
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
define( 'EDD_PD_VER', '0.0.1' );
define( 'EDD_PD_FILE', __FILE__ );
define( 'EDD_PD', plugin_dir_path( __FILE__ ) );
define( 'EDD_PD_URL', plugin_dir_url( __FILE__ ) );

require_once 'classes/class-edd-pd-loader.php';
require_once 'classes/class-edd-pd-admin.php';


/*
if ( ! class_exists( 'Easy_Digital_Downloads' ) ) {

	$url = network_admin_url() . 'plugin-install.php?s=easy+digital+downloads&tab=search';
	// translators: %s: plugin install link.
	printf( __( '<div class="update-nag bsf-update-nag">Please install and activate <i><a href="%s">Easy Digital Downloads</a></i> plugin in order to use Automate Mautic for Easy Digital Downloads.</div>', 'automateplug-mautic-addon' ), $url );
}
*/

