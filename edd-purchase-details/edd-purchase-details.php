<?php
/**
 * Plugin Name: Easy Digital Downloads - Purchase Details 
 * Plugin URI: https://www.brainstormforce.com
 * Author: Brainstorm Force
 * Author URI: https://www.brainstormforce.com
 * Version: 0.0.1
 * Description: Easy Digital Downloads Purchase Details of user 
 * Text Domain: edd_pd
 *
 * @package EDD-PD
 */

define( 'EDD-PD_FILE', __FILE__ );
define( 'EDD_PD', plugin_dir_path( __FILE__ ) );
require_once 'classes/class-eddpd-loader.php';
require_once 'classes/class-edd-pd-admin.php';
?>
