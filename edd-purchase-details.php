<?php
/**
 * Plugin Name: Easy Digital Downloads - Purchase Details
 * Plugin URI: https://www.brainstormforce.com
 * Author: Brainstorm Force
 * Author URI: https://www.brainstormforce.com
 * Version: 0.0.1
 * Description: Easy Digital Downloads Purchase Details of user
 * Text Domain: edd-purchase-details
 *
 * @package EDD-PD
 */

/**
 * Define constants.
 */
define( 'EDD_PD_VER', '0.0.1' );
define( 'EDD_PD_FILE', __FILE__ );
define( 'EDD_PD', plugin_dir_path( __FILE__ ) );
define( 'EDD_PD_URL', plugin_dir_url( __FILE__ ) );

require_once 'classes/class-edd-pd-loader.php';
require_once 'classes/class-edd-pd-admin.php';


// Activation hook.
register_activation_hook( __FILE__, 'edd_pd_add_my_custom_page_suport' );
register_activation_hook( __FILE__, 'edd_pd_add_my_custom_page_history' );

/**
 * Create support page with shortcode in EDD Suport.
 *
 * @since 0.0.1
 * @return void
 */
function edd_pd_add_my_custom_page_suport() {
	// Create post object.
	if ( get_page_by_title( 'support' !== null ) ) {
		$user_id   = get_current_user_id();
		$post_data = array(
			'post_title'   => wp_strip_all_tags( 'support' ),
			'post_content' => '[edd_pd_product_details]',
			'post_status'  => 'publish',
			'post_author'  => $user_id,
			'post_type'    => 'page',
		);
		// Insert the post into the database.
		wp_insert_post( $post_data );
	}
}

/**
 * Create History page with shortcode in EDD Suport
 *
 * @since 0.0.1
 * @return void
 */
function edd_pd_add_my_custom_page_history() {
	// Create post object.
	if ( get_page_by_title( 'history' !== null ) ) {
		$user_id   = get_current_user_id();
		$post_data = array(
			'post_title'   => wp_strip_all_tags( 'history' ),
			'post_content' => '[edd_pd_product_history]',
			'post_status'  => 'publish',
			'post_author'  => $user_id,
			'post_type'    => 'page',
		);

		// Insert the post into the database.
		wp_insert_post( $post_data );
	}
}

