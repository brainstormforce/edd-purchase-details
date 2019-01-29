<?php
/**
 * EDD purchase details Frontend class.
 *
 * @package EDD-PD
 */

// Block direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'EDD_Purchase_Details_Frontend' ) ) {

	/**
	 * Class EDD_Purchase_Details_Frontend.
	 *
	 * @since 0.0.1
	 */
	final class EDD_Purchase_Details_Frontend {

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
			add_shortcode( 'access_to_purchase_details', array( $this, 'load_plugin' ) );
			add_filter( 'the_content', array( $this, 'override_history_content' ), 9999 );
		}


		/**
		 * Initialization of EDD PD plugin
		 *
		 * @since 1.0.0
		 * @return obj
		 */
		public function load_plugin() {
			ob_start();
			$this->load_css_file();
			$this->edd_form_render_get_user_data();
			$this->load_puchase_details();
			return ob_get_clean();

		}

		/**
		 * Render the form for get user data.
		 *
		 * @since 0.0.1
		 * @return void
		 */
		function edd_form_render_get_user_data() {

			$value = ( isset( $_GET['user_email'] ) ? sanitize_email( $_GET['user_email'] ) : '' ); ?>
			<div class="widget artwork-seachform search" rol="search">
				<form role="search" action="<?php echo esc_url( $_SERVER['REQUEST_URI'] ); ?>" method="get">
					<?php wp_nonce_field( 'edd_pd_handle_custom_form', 'edd_pd_nonce_custom_form' ); ?>
					<input type="email" class="edd_pd_seach_textbox" name="user_email" placeholder=" <?php _e( 'Enter customer email address', 'edd-purchase-details' ); ?>" value="<?php echo $value; ?>" required/ >

					<input type="submit" alt="Search" value=" <?php _e( 'Search', 'edd-purchase-details' ); ?> "  class="edd_pd_seach_Button"  />

				</form>

			</div>
			<?php
		}

		/**
		 * Add css file
		 *
		 * @since 0.0.1
		 */
		function load_css_file() {

			wp_enqueue_style( 'EDD_PD_stylesheet', EDD_PD_URL . 'assets/css/frontend.css', false, null, 'all' );
		}

		/**
		 * Call methods with request condition check.
		 *
		 * @since 0.0.1
		 * @param  string $content  For clear old content.
		 * @return content
		 */
		function override_history_content( $content ) {

			if ( isset( $_GET['action'] ) ) {
				if ( ! empty( $_GET['action'] ) || 'view_history' == $_GET['action'] ) {
					ob_start();
					$this->view_history( intval( $_GET['payment_id'] ) );
					$content = ob_get_clean();
				}
			}
			return $content;
		}


		/**
		 * Call purchase details methods form with validate.
		 *
		 * @since 0.0.1
		 */
		function load_puchase_details() {

			if ( ! empty( $_REQUEST['edd_pd_nonce_custom_form'] ) ) {
				if ( wp_verify_nonce( $_REQUEST['edd_pd_nonce_custom_form'], 'edd_pd_handle_custom_form' ) ) {
					if ( isset( $_GET['user_email'] ) ) {
						$this->view_product_details( sanitize_email( $_GET['user_email'] ) );
					}
				}
			}
		}


		/**
		 * Check user is valid or not current user.
		 *
		 * @since 0.0.1
		 * @return bool
		 */
		function check_valid_user() {
			if ( is_array( get_option( 'edd_pd_user_access' ) ) ) {
				$user_info = wp_get_current_user();
				if ( count( array_intersect( $user_info->roles, get_option( 'edd_pd_user_access' ) ) ) > 0 ) {
					return true;
				}
			} elseif ( current_user_can( 'administrator' ) ) {
					return true;
			} else {
					return false;
			}
		}

		/**
		 * Display the purchase history of the current user.
		 *
		 * @since 0.0.1
		 * @param  string $email  For user purchase details.
		 * @return void
		 */
		function view_product_details( $email ) {

			if ( is_user_logged_in() ) {

				if ( $this->check_valid_user() ) {

					$customer_details = get_user_by( 'email', $email );

					if ( ! empty( $customer_details ) ) {
						$payment_ids = edd_get_users_purchases( $customer_details->ID, 50, true, 'any' );
						if ( $payment_ids ) :
							do_action( 'access_to_purchase_before_purchase_history', $payment_ids );
							?>
							<div class="entry-content product_details clear">
								<table id="edd_pd_user_history" class="product_details_table">
									<thead>
										<tr class="edd_purchase_row">
											<?php do_action( 'access_to_purchase_details_header_before' ); ?>
											<th class="edd_purchase_id"><?php _e( 'ID', 'edd-purchase-details' ); ?></th>
											<th class="edd_purchase_date"><?php _e( 'Date', 'edd-purchase-details' ); ?></th>
											<th class="edd_purchase_products"><?php _e( 'Products', 'edd-purchase-details' ); ?></th>
											<th class="edd_purchase_amount"><?php _e( 'Amount', 'edd-purchase-details' ); ?></th>
											<th class="edd_purchase_status"><?php _e( 'Status', 'edd-purchase-details' ); ?></th>
											<th class="edd_purchase_details"><?php _e( 'License Keys', 'edd-purchase-details' ); ?></th>
											<?php do_action( 'access_to_purchase_details_header_after' ); ?>
										</tr>
									</thead>
									<tbody>
									<?php
									foreach ( $payment_ids as $payment ) :
										$purchase = new EDD_Payment( $payment->ID );

										?>
										<tr class="edd_purchase_row">
											<?php do_action( 'access_to_purchase_details_row_start', $payment->ID, $payment->payment_meta ); ?>
											<td class="edd_purchase_id">#<?php echo $purchase->number; ?></td>
											<td class="edd_purchase_date"><?php echo date_i18n( get_option( 'date_format' ), strtotime( $purchase->date ) ); ?></td>
											<td class="edd_purchase_products"><?php echo  $purchase->cart_details[0]['name']; ?></span>
											</td>
											<td class="edd_purchase_amount"><?php echo edd_currency_filter( edd_format_amount( $purchase->total ) ); ?></span>
											</td>
											<td class="edd_purchase_details">
													<?php echo $purchase->status_nicename; ?>
											</td>
											<td class="edd_purchase_key">
											<?php
											$history = esc_url(
												add_query_arg(
													array(
														'action'     => 'view_history',
														'payment_id' => $purchase->ID,
													)
												)
											);
											if ( function_exists( 'edd_software_licensing' ) ) {
												if ( edd_is_payment_complete( $payment->ID ) && edd_software_licensing()->get_licenses_of_purchase( $payment->ID ) ) {
													echo '<a href="' . esc_url( $history ) . '">' . __( 'View Licenses', 'edd-purchase-details' ) . '</a>';
												} else {
													echo ' - ';
												}
											} else {
												echo ' - ';
											}
											do_action( 'access_to_purchase_details_row_end', $purchase->ID, $payment->payment_meta );
											?>
										</td>
									</tr>
									<?php endforeach; ?>
									</tbody>
								</table>
							</div>

						<?php else : ?>

						<div><p class="edd-no-purchases"><?php _e( 'This user hasn\'t purchased anything!', 'edd-purchase-details' ); ?></p></div>
								<?php
						endif;

					} else {
						?>
						<div><p class="edd-no-purchases"><?php _e( 'This user hasn\'t purchased anything!', 'edd-purchase-details' ); ?></p></div>
						<?php
					}
				} else {
					?>
					<div><p class="edd-pd-no-permission"><?php _e( 'You do not have permission to access this page', 'edd-purchase-details' ); ?></p></div>
					<?php
				}
			} else {
				?>
				<div><p class="edd-pd-no-login"><?php _e( ' You are not logged in. Please log in and try again.', 'edd-purchase-details' ); ?></p></div>
				<?php
			}
		}

		/**
		 * Display the Product History.
		 *
		 * @since 0.0.1
		 * @param  Int $payment_id   View history by payment_id.
		 * @return void
		 */
		function view_history( $payment_id ) {

			if ( is_user_logged_in() ) {

				if ( $this->check_valid_user() ) {
					$color = edd_get_option( 'checkout_color', 'gray' );
					$color = ( 'inherit' == $color ) ? '' : $color;

					?>
						<p><a href="<?php echo esc_url( remove_query_arg( array( 'action', 'payment_id' ) ) ); ?>" class="edd-view-license-back edd-submit button <?php echo esc_attr( $color ); ?>"><?php _e( 'Go back', 'edd-purchase-details' ); ?></a></p>
						<?php
						if ( function_exists( 'edd_software_licensing' ) ) {
							if ( isset( $payment_id ) ) {
								$child_keys = edd_software_licensing()->get_licenses_of_purchase( $payment_id );
								if ( ! empty( $child_keys ) ) {
									?>
									<div class="entry-content clear" itemprop="text">
									<?php do_action( 'edd_before_download_history' ); ?>
										<table id="edd_user_history" class="edd-table">
											<thead>
												<tr class="edd_purchase_row">
									<?php do_action( 'edd_pd_download_history_header_start' ); ?>
												<th class=" edd_purchase_amount"><?php _e( 'Item', 'edd-purchase-details' ); ?></th>
												<th class="edd_purchase_details"><?php _e( 'Key', 'edd-purchase-details' ); ?></th>
												<th class="edd_license_key"><?php _e( 'Status', 'edd-purchase-details' ); ?></th>
												<th class="edd_license_key"><?php _e( 'Activations', 'edd-purchase-details' ); ?></th>
												<th class="edd_purchase_date"><?php _e( 'Expiration', 'edd-purchase-details' ); ?></th>
									<?php do_action( 'edd_pd_download_history_header_end' ); ?>
												</tr>
											</thead>
											<tbody>
									<?php
									foreach ( $child_keys as $child_key ) {
										?>
													<tr class="edd_sl_license_row">
										<?php do_action( 'edd_pd_download_history_row_start', $child_key->ID ); ?>
														<td class="edd_sl_item"><?php echo $child_key->download->post_title; ?></td>
														<td class="edd_sl_key"> <?php echo  $child_key->key; ?></td>
														<td class="edd_sl_status"> <?php echo $child_key->status; ?> </td>
														<td class="edd_sl_limit"><?php echo $child_key->activation_count; ?> / <?php echo$child_key->activation_limit; ?></td>
														<td class="edd_sl_expiration">
										<?php
										if ( 0 == $child_key->expiration ) {
											echo  $child_key->expiration;
										} else {
											echo  date_i18n( 'F j, Y', $child_key->expiration );
										}
										?>
														</td> 
										<?php do_action( 'edd_pd_download_history_row_end', $child_key->ID ); ?>
													</tr>
								<?php	} ?>
											</tbody>
										</table>
									</div>

									<?php

								} else {
									?>
									<div><p class="edd-invalid-request"><?php _e( 'Invalid Request.', 'edd-purchase-details' ); ?></p></div>	
									<?php
								}
							}
						} else {
							?>
								<div><p class="edd-invalid-request"><?php _e( 'Invalid Request.', 'edd-purchase-details' ); ?></p></div>	
							<?php
						}
				} else {

					?>
					<div><p class="edd-pd-no-permission"><?php _e( 'You do not have permission to access this page', 'edd-purchase-details' ); ?></p></div>
					<?php
				}
			} else {

				?>
				<div><p class="edd-pd-no-login"><?php _e( ' You are not logged in. Please log in and try again.', 'edd-purchase-details' ); ?></p></div>
				<?php
			}
		}

	}
	EDD_Purchase_Details_Frontend::get_instance();
}
