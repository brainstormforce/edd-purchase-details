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
			add_shortcode( 'access_to_purchase_details', array( $this, 'epf_load_plugin' ) );
			add_filter( 'the_content', array( $this, 'epf_purchase_history' ), 9999 );
		}


		/**
		 * Initialization of EDD PD plugin
		 *
		 * @since 1.0.0
		 * @return obj
		 */
		public function epf_load_plugin() {
			if ( $this->epf_check_valid_user() ) {
				ob_start();
				$this->epf_load_css_file();
				$this->epf_form_render_get_customer_data();
				$this->epf_load_puchase_details();
				return ob_get_clean();
			}
		}

		/**
		 * Render the form for get user data.
		 *
		 * @since 0.0.1
		 * @return void
		 */
		public function epf_form_render_get_customer_data() {

			$value = ( isset( $_GET['epf_customer_email'] ) ? sanitize_email( $_GET['epf_customer_email'] ) : '' );// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			?>
				<div class="epf-search">
					<form  class="epf-search-form" action="<?php echo esc_url( $_SERVER['REQUEST_URI'] ); ?>" method="get">
						<?php wp_nonce_field( 'epf_search_form', 'epf_nonce_search_form' ); ?>
						<input type="email" class="epf-search-textbox" name="epf_customer_email" placeholder=" <?php esc_html_e( 'Enter customer email address', 'edd-purchase-details' ); ?>" value="<?php echo esc_attr( $value ); ?>" required/>
						<input type="submit" class="epf-search-button" value="<?php esc_html_e( 'Search', 'edd-purchase-details' ); ?> "  />
					</form>

				</div>
				<?php

		}

		/**
		 * Add css file
		 *
		 * @since 0.0.1
		 */
		public function epf_load_css_file() {

			wp_enqueue_style( 'EDD_PD_stylesheet', EDD_PD_URL . 'assets/css/frontend.css', array(), EDD_PD_VER, 'all' );

		}

		/**
		 * Call methods with request condition check.
		 *
		 * @since 0.0.1
		 * @param  string $content  For clear old content.
		 * @return content
		 */
		public function epf_purchase_history( $content ) {

			$action = isset( $_GET['action'] ) ? $_GET['action'] : '';

			if ( isset( $_GET['payment_id'] ) && 'epf_view_history' === $action && isset( $_GET['epf_nonce_search_form'] ) && wp_verify_nonce( $_GET['epf_nonce_search_form'], 'epf_search_form' ) ) {
				ob_start();
				$this->epf_view_history( intval( $_GET['payment_id'] ) );
				$content = ob_get_clean();
			}
			return $content;
		}


		/**
		 * Call purchase details methods form with validate.
		 *
		 * @since 0.0.1
		 */
		public function epf_load_puchase_details() {

			if ( ! empty( $_REQUEST['epf_nonce_search_form'] ) ) {
				if ( wp_verify_nonce( $_REQUEST['epf_nonce_search_form'], 'epf_search_form' ) ) {
					if ( is_email( $_GET['epf_customer_email'] ) ) {
						$this->epf_view_product_details( sanitize_email( $_GET['epf_customer_email'] ) );
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
		public function epf_check_valid_user() {
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
		public function epf_view_product_details( $email ) {

			if ( ! is_user_logged_in() ) {
				?>
				<div><p class="epf-error-no-login"><?php esc_html_e( ' You are not logged in. Please log in and try again.', 'edd-purchase-details' ); ?></p></div>
				<?php

			} else {

				if ( ! $this->epf_check_valid_user() ) {
					?>
					<div><p class="epf-error-no-permission"><?php esc_html_e( 'You do not have permission to access purchase history!', 'edd-purchase-details' ); ?></p></div>
					<?php

				} else {
					$customer_details = get_user_by( 'email', $email );

					if ( empty( $customer_details ) ) {
						?>
							<div><p class="epf-error-no-email"><?php esc_html_e( 'The email you have entered does not exist. ', 'edd-purchase-details' ); ?></p></div>
						<?php
					} else {
						$payment_ids = edd_get_users_purchases( $customer_details->ID, 50, true, 'any' );
						if ( $payment_ids ) :
							do_action( 'access_to_purchase_history_start', $payment_ids );
							?>
							<div class="entry-content epf-purchase-details clear">
								<table class="edd-table">
									<thead>
										<tr class="edd-purchase-row">
											<?php do_action( 'access_to_purchase_details_header_before' ); ?>
											<th class="edd_purchase_id"><?php esc_html_e( 'ID', 'edd-purchase-details' ); ?></th>
											<th class="edd_purchase_date"><?php esc_html_e( 'Date', 'edd-purchase-details' ); ?></th>
											<th class="edd_purchase_products"><?php esc_html_e( 'Products', 'edd-purchase-details' ); ?></th>
											<th class="edd_purchase_amount"><?php esc_html_e( 'Amount', 'edd-purchase-details' ); ?></th>
											<th class="edd_purchase_status"><?php esc_html_e( 'Status', 'edd-purchase-details' ); ?></th>
											<th class="edd_purchase_details"><?php esc_html_e( 'License Keys', 'edd-purchase-details' ); ?></th>
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
											<td class="edd_purchase_id">#<?php echo esc_attr( $purchase->number ); ?></td>
											<td class="edd_purchase_date"><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $purchase->date ) ) ); ?></td>
											<td class="edd_purchase_products"><?php echo esc_html( $purchase->cart_details[0]['name'] ); ?></span>
											</td>
											<td class="edd_purchase_amount"><?php echo esc_html( edd_currency_filter( edd_format_amount( $purchase->total ) ) ); ?></span>
											</td>
											<td class="edd_purchase_details">
													<?php echo esc_attr( $purchase->status_nicename ); ?>
											</td>
											<td class="edd_purchase_key">
											<?php
											$history = esc_url(
												add_query_arg(
													array(
														'action'     => 'epf_view_history',
														'payment_id' => $purchase->ID,
													)
												)
											);
											if ( function_exists( 'edd_software_licensing' ) ) {
												if ( edd_is_payment_complete( $payment->ID ) && edd_software_licensing()->get_licenses_of_purchase( $payment->ID ) ) {
													echo '<a href="' . esc_url( $history ) . '">' . esc_html__( 'View Licenses', 'edd-purchase-details' ) . '</a>';
												} else {
													echo ' - ';
												}
											} else {
												echo ' - ';
											}
											do_action( 'access_to_purchase_history_end', $purchase->ID, $payment->payment_meta );
											?>
										</td>
									</tr>
									<?php endforeach; ?>
									</tbody>
								</table>
							</div>

						<?php else : ?>

						<div><p class="epf-error-no-purchases"><?php esc_html_e( 'This user hasn\'t purchased anything!', 'edd-purchase-details' ); ?></p></div>
								<?php
						endif;
					}
				}
			}
		}

		/**
		 * Display the Product History.
		 *
		 * @since 0.0.1
		 * @param  Int $payment_id   View history by payment_id.
		 * @return void|bool
		 */
		public function epf_view_history( $payment_id ) {

			if ( ! is_user_logged_in() ) {
				?>
				<div><p class="epf-error-no-login"><?php esc_html_e( ' You are not logged in. Please log in and try again.', 'edd-purchase-details' ); ?></p></div>
				<?php
			} else {
				if ( ! $this->epf_check_valid_user() ) {

					?>
					<div><p class="epf-error-no-permission"><?php esc_html_e( 'You do not have permission to access purchase history!', 'edd-purchase-details' ); ?></p></div>
					<?php

				} else {

					$color = edd_get_option( 'checkout_color', 'gray' );
					$color = ( 'inherit' === $color ) ? '' : $color;

					?>
					<p><a href="<?php echo esc_url( remove_query_arg( array( 'action', 'payment_id' ) ) ); ?>" class="epf-view-license-back edd-submit button <?php echo esc_attr( $color ); ?>"><?php esc_html_e( 'Go back', 'edd-purchase-details' ); ?></a></p>
					<?php
					if ( ! function_exists( 'edd_software_licensing' ) ) {
						return false;
					} else {
						if ( ! isset( $payment_id ) ) {
							echo esc_html( $payment_id );

						} else {
							$child_keys = edd_software_licensing()->get_licenses_of_purchase( $payment_id );
							if ( ! empty( $child_keys ) ) {
								?>
								<div class="entry-content epf-purchase-history clear" itemprop="text">
								<?php do_action( 'epf_before_download_history' ); ?>
									<table class="edd-table">
										<thead>
											<tr class="edd_purchase_row">
								<?php do_action( 'epf_download_history_header_start' ); ?>
											<th class=" edd_purchase_amount"><?php esc_html_e( 'Item', 'edd-purchase-details' ); ?></th>
											<th class="edd_purchase_details"><?php esc_html_e( 'Key', 'edd-purchase-details' ); ?></th>
											<th class="edd_license_key"><?php esc_html_e( 'Status', 'edd-purchase-details' ); ?></th>
											<th class="edd_license_key"><?php esc_html_e( 'Activations', 'edd-purchase-details' ); ?></th>
											<th class="edd_purchase_date"><?php esc_html_e( 'Expiration', 'edd-purchase-details' ); ?></th>
								<?php do_action( 'epf_download_history_header_end' ); ?>
											</tr>
										</thead>
										<tbody>
								<?php
								foreach ( $child_keys as $child_key ) {
									?>
												<tr class="edd_sl_license_row">
									<?php do_action( 'epf_download_history_row_start', $child_key->ID ); ?>
													<td class="edd_sl_item"><?php echo esc_html( $child_key->download->post_title ); ?></td>
													<td class="edd_sl_key"> <?php echo esc_attr( $child_key->key ); ?></td>
													<td class="edd_sl_status"> <?php echo esc_attr( $child_key->status ); ?> </td>
													<td class="edd_sl_limit"><?php echo esc_attr( $child_key->activation_count ); ?> / <?php echo esc_attr( $child_key->activation_limit ); ?></td>
													<td class="edd_sl_expiration">
									<?php
									if ( 0 === $child_key->expiration ) {
										echo esc_attr( $child_key->expiration );
									} elseif ( 'lifetime' === $child_key->expiration ) {

										esc_html_e( 'Lifetime', 'edd-purchase-details' );
									} else {
										echo esc_html( date_i18n( 'F j, Y', $child_key->expiration ) );
									}
									?>
													</td> 
									<?php do_action( 'epf_download_history_row_end', $child_key->ID ); ?>
												</tr>
							<?php	} ?>
										</tbody>
									</table>
									<?php do_action( 'epf_after_download_history' ); ?>
								</div>
								<?php
							}
						}
					}
				}
			}

		}

	}
	EDD_Purchase_Details_Frontend::get_instance();
}
