<?php
/**
 * EDD purchase details Loader class.
 *
 * @package EDDPD
 */

if ( ! class_exists( 'EDD_PD_Loader' ) ) {

	/**
	 * Class EDDPD_Loader.
	 *
	 * @since 0.0.1
	 */
	final class EDD_PD_Loader {

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
			// Activation hook.
			add_shortcode( 'access_to_purchase_details', array( $this, 'load_css_file' ) );
			add_filter( 'the_content', array( $this, 'override_history_content' ), 9999 );

		}

		/**
		 * Load the localization files
		 *
		 * @since 0.0.1
		 * @return void
		 */
		public function localization() {
			load_plugin_textdomain( 'edd_pd', false, dirname( plugin_basename( EDD_PD_PLUGIN_FILE ) ) . '/languages/' );
		}

		/**
		 * Render the form for get user data.
		 *
		 * @since 0.0.1
		 * @return void
		 */
		function edd_form_render_get_user_data() {
			echo '<div><form action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="get">';
			echo '<input type="email" name="user_email"  class="edd_pd_seach_textbox" size="116" placeholder="Enter customer email address"  value="' . ( isset( $_GET['user_email'] ) ? esc_attr( $_GET['user_email'] ) : '' ) . '"/>';
			echo '<input type="submit" class="edd_pd_seach_Button" name="edd-pd-submitted" value="Submit"/></div>';
			echo '</form><hr><br></div>';
		}

		/**
		 * Add css file
		 *
		 * @since 0.0.1
		 */
		function load_css_file() {

			wp_enqueue_style( 'EDD_PD_stylesheet', EDD_PD_URL . 'assets/css/unminified/style.css', false, null, 'all' );
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
					$this->view_history( $_GET['payment_id'] );
					$content = ob_get_clean();
				}
			} elseif ( ! empty( $_GET['user_email'] ) ) {
				ob_start();
				$this->edd_form_render_get_user_data();
				$this->edd_pd_product_details( $_GET['user_email'] );
				$content = ob_get_clean();

			} else {
				ob_start();
				$this->edd_form_render_get_user_data();
				$content = ob_get_clean();
			}
			return $content;
		}

		/**
		 * Display the purchase history of the current user.
		 *
		 * @since 0.0.1
		 * @param  string $email  For user purchase details.
		 * @return void
		 */
		function edd_pd_product_details( $email ) {
			if ( is_user_logged_in() ) {
				$user_info = wp_get_current_user();
				if ( count( get_option( 'user_access' ) ) > 0 ) {
					if ( count( array_intersect( $user_info->roles, get_option( 'user_access' ) ) ) > 0 ) {
						$user_email       = sanitize_email( $email );
						$customer_details = get_user_by( 'email', $user_email );

						if ( ! empty( $customer_details ) ) {
							$payments = edd_get_users_purchases( $customer_details->ID, 50, true, 'any' );
							if ( $payments ) :
								do_action( 'edd_before_purchase_history', $payments ); ?>
								<div class="entry-content clear">
									<table id="edd_pd_user_history" class="edd-table">
										<thead>
											<tr class="edd_purchase_row">
												<?php do_action( 'access_to_purchase_details_header_before' ); ?>
												<th class="edd_purchase_id"><?php _e( 'ID', 'edd-purchase-details' ); ?></th>
												<th class="edd_purchase_date"><?php _e( 'Date', 'edd-purchase-details' ); ?></th>
												<th class="edd_purchase_products"><?php _e( 'Products', 'edd-purchase-details' ); ?></th>
												<th class="edd_purchase_amount"><?php _e( 'Amount', 'edd-purchase-details' ); ?></th>
												<th class="edd_purchase_status"><?php _e( 'Status', 'edd-purchase-details' ); ?></th>
												<th class="edd_purchase_details"><?php _e( 'key', 'edd-purchase-details' ); ?></th>
												<?php do_action( 'access_to_purchase_details_header_after' ); ?>
											</tr>
										</thead>
										<tbody>
										<?php
										foreach ( $payments as $payment ) :
											$payment = new EDD_Payment( $payment->ID );
											?>
											<tr class="edd_purchase_row">
												<?php do_action( 'access_to_purchase_details_row_start', $payment->ID, $payment->payment_meta ); ?>
												<td class="edd_purchase_id">#<?php echo $payment->number; ?></td>
												<td class="edd_purchase_date"><?php echo date_i18n( get_option( 'date_format' ), strtotime( $payment->date ) ); ?></td>
												<td class="edd_purchase_products"><?php echo  $payment->cart_details[0]['name']; ?></span>
												</td>
												<td class="edd_purchase_amount"><?php echo edd_currency_filter( edd_format_amount( $payment->total ) ); ?></span>
												</td>
												<td class="edd_purchase_details">
														<?php echo $payment->status_nicename; ?>
												</td>
												<td class="edd_purchase_key">
												<?php
												$history = esc_url(
													add_query_arg(
														array(
															'action'     => 'view_history',
															'payment_id' => $payment->ID,
														)
													)
												);
												?>
												<a href='<?php echo esc_url( $history ); ?>'><?php echo $payment->key; ?> </a>
												<?php do_action( 'access_to_purchase_details_row_end', $payment->ID, $payment->payment_meta ); ?>
											</td>
										</tr>
										<?php endforeach; ?>
										</tbody>
									</table>
								</div>
						<?php else : ?>
						<div><p class="edd-no-purchases"><?php _e( 'You have not made any purchases', 'edd-purchase-details' ); ?></p></div>
								<?php
						endif;
						} else {
							echo 'Email address does not exist ';
						}
					} else {
						echo 'Not valid user';
					}
				} else {
					echo 'Access Denied';
				}
			} else {
				echo 'User not login ';
			}
		}

		/**
		 * Display the Product History  .
		 *
		 * @since 0.0.1
		 * @param  Int $payment_id   View spacific Payment_id For view history.
		 * @return void
		 */
		function view_history( $payment_id ) {
			if ( is_user_logged_in() ) {
				$user_info = wp_get_current_user();
				if ( count( get_option( 'user_access' ) ) > 0 ) {
					if ( count( array_intersect( $user_info->roles, get_option( 'user_access' ) ) ) > 0 ) {
						?>
						<p><a href="<?php echo esc_url( remove_query_arg( array( 'action', 'payment_id' ) ) ); ?>" class="edd-manage-license-back edd-submit button <?php echo esc_attr( $color ); ?>"><?php _e( 'Go back', 'edd-purchase-details' ); ?></a></p>

						<?php
						if ( isset( $_GET['payment_id'] ) ) {
								$child_keys = edd_software_licensing()->get_licenses_of_purchase( $payment_id );
							if ( ! empty( $child_keys ) ) {
								?>
								<div class="entry-content clear" itemprop="text">
								<?php do_action( 'edd_before_download_history' ); ?>
									<table id="edd_user_history" class="edd-table">
										<thead>
											<tr class="edd_purchase_row">
											<?php do_action( 'edd_download_history_header_start' ); ?>
											<th class=" edd_purchase_amount"><?php _e( 'Item', 'edd-purchase-details' ); ?></th>
											<th class="edd_purchase_details"><?php _e( 'Key', 'edd-purchase-details' ); ?></th>
											<th class="edd_license_key"><?php _e( 'Status', 'edd-purchase-details' ); ?></th>
											<th class="edd_license_key"><?php _e( 'Activations', 'edd-purchase-details' ); ?></th>
											<th class="edd_purchase_date"><?php _e( 'Expiration', 'edd-purchase-details' ); ?></th>
											<?php do_action( 'edd_download_history_header_end' ); ?>
											</tr>
										</thead>
										<tbody>
											<?php
											foreach ( $child_keys as $child_key ) {
												?>
												<tr class="edd_sl_license_row">
													<?php do_action( 'edd_download_history_row_start', $child_key->ID ); ?>
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
													<?php do_action( 'edd_download_history_row_end', $child_key->ID ); ?>
												</tr>
										<?php	} ?>
										</tbody>
									</table>
								</div>

							<?php } else {
								echo 'Invalid Request....!!!';
							}
						}
					} else {
						echo 'Not valid user...';
					}
				} else {
					echo 'Access Denied';
				}
			} else {
				echo 'User not login ...';
			}
		}
	}
	EDD_PD_Loader::get_instance();
}
?>
