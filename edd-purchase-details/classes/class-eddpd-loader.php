<?php
/**
 * edd-pd Loader.
 *
 * @package EDDPD
 */

if ( ! class_exists( 'EDDPD_Loader' ) ) {

	/**
	 * Class EDDPD_Loader.
	 */
	final class EDDPD_Loader {

	/**
	/**
	 * Member Variable
	 *
	 * @var instance
	 */
	private static $instance;

	/**
	 *  Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		// Activation hook.
		//register_activation_hook( init , array( $this, 'edd_pd_add_my_custom_page_suport'));
		//register_activation_hook( init ,  array( $this,'edd_pd_add_my_custom_page_history'));
		add_shortcode ( 'edd_pd_product_history' , array( $this,  'edd_pd_product_history' ) );
        add_shortcode ( 'edd_pd_product_details' , array( $this,  'edd_pd_load_plugin' ) );
       
		}


	/**
	 * Load the localization files
	 *
	 * @since 1.0.0
	 * @return void
	 */
     public function localization() {
			load_plugin_textdomain( 'edd_pd', false, dirname( plugin_basename( EDD_PD_PLUGIN_FILE ) ) . '/languages/' );
      }


	/**
	 * Render the form on suport page 
	 *
	 * @since 1.0.0
	 * @return void
	 */
	 function edd_cs_form_render() {	
	    echo '<div><form action="' . esc_url( $_SERVER['REQUEST_URI']) . '" method="post">';
	    echo '<input type="email" name="edd_pd_email"  class="edd_pd_seach_textbox" size="116" placeholder="Enter customer email address" />';
	  	echo '<input type="submit" class="edd_pd_seach_Button" name="edd-pd-submitted" value="Submit"/></div>';
	    echo '</form><hr><br></div>';
	   
	}


	 /**
	 * Initialization of EDD PD plugin 
	 *
	 * @since 1.0.0
	 * @return void
	 */
		function edd_pd_load_plugin() {
			ob_start();
			$this->edd_cs_form_render();
			$this->edd_pd_product_details();
			$this->load_css_file();
			return ob_get_clean();
		}





	function load_css_file()
	{
		wp_enqueue_style( 'EDD_PD_stylesheet', EDD_PD_URL.'css/style.css' , false, '1.0.0', 'all');
	}






	/**
	 * Display the purchase history of the current user.
	 *
	 * @since 1.0.0
	 * @return void
	 */
		function edd_pd_product_details(){
		if ( isset( $_POST['edd-pd-submitted'] ) ){
		  if( is_user_logged_in() ) {
		     $user_info = wp_get_current_user();
		     if( ! empty ( get_option('user_access'))){
			 if ( ! empty ( array_intersect( $user_info->roles, get_option('user_access')))) {
				$cs_email         =  sanitize_email( $_POST["edd_pd_email"] );
			    $customer_details =  get_user_by( 'email',$cs_email );

		  	    if( ! empty( $customer_details ) ) {
	    	    	$payments = edd_get_users_purchases(  $customer_details->ID , 50, true, 'any' );
			 		if ( $payments ) :
						do_action( 'edd_before_purchase_history', $payments ); ?>
						<div class="entry-content clear" itemprop="text">
						<table id="edd_pd_user_history" class="edd-table">
							<thead>
								<tr class="edd_purchase_row">
									<?php do_action('edd_ps_purchase_history_header_before'); ?>
									<th class="edd_purchase_id"><?php _e('ID','edd_pd' ); ?></th>
									<th class="edd_purchase_date"><?php _e('Date','edd_pd' ); ?></th>
									<th class="edd_purchase_products"><?php _e('Products','edd_pd' ); ?></th>
									<th class="edd_purchase_amount"><?php _e('Amount','edd_pd' ); ?></th>
									<th class="edd_purchase_status"><?php _e('Status','edd_pd' ); ?></th>
									<th class="edd_purchase_details"><?php _e('key','edd_pd' ); ?></th>
									<?php do_action('edd_ps_purchase_history_header_after'); ?>
								</tr>
							</thead>
							<?php foreach ( $payments as $payment ) : ?>
								<?php $payment = new EDD_Payment( $payment->ID );?>
								<tr class="edd_purchase_row">
									<?php do_action( 'edd_pd_product_history_row_start', $payment->ID, $payment->payment_meta ); ?>
									<td class="edd_purchase_id">#<?php echo $payment->number ?></td>
									<td class="edd_purchase_date"><?php echo date_i18n( get_option('date_format'), strtotime( $payment->date ) ); ?></td>
		                            <td class="edd_purchase_products">
										<span class="edd_purchase_products"><?php echo  $payment->cart_details[0]['name'] ; ?></span>
									</td>
									<td class="edd_purchase_amount">
										<span class="edd_purchase_amount"><?php echo edd_currency_filter( edd_format_amount( $payment->total ) ); ?></span>
									</td>
									<td class="edd_purchase_details">
									   <?php echo $payment->status_nicename; ?>
									</td>
									<td class="edd_purchase_key">
								   <a href= "<?php echo esc_url(add_query_arg( 'payment_id', base64_encode ($payment->ID), $this->get_uri() ) )?>" > <?php echo $payment->key; ?></a>

								</td>
									
								</tr>
							<?php endforeach; ?>
						</table></div>
					<?php else : ?>
						<div><p class="edd-no-purchases"><?php _e('You have not made any purchases','easy-digital-downloads' ); ?></p></div>
					<?php endif;
				} else {
					echo 'Email dosen\'t exists ' ;
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
		}

	/**
	 * create url .
	 *
	 * @since 1.0.0
	 * @return void
	 */
		function get_uri( $query_string = null)
		{
			return home_url( 'history');
		}


	/**
	 *  Display the Product History .
	 *
	 * @since 1.0.0
	 * @return void
	 */
		function edd_pd_product_history(){
		    if( is_user_logged_in() )  {
		     $user_info = wp_get_current_user();
		     if( ! empty ( get_option('user_access'))){
			 if (!empty(array_intersect($user_info->roles, get_option('user_access'))))  { ?>
			  <p><a href="<?php echo home_url(); ?>/support" class="edd-manage-license-back edd-submit button"><?php _e( 'Go back', 'edd_pd' ); ?></a></p>
			   <?php
				if( isset( $_GET['payment_id']) )	{
				$child_keys = edd_software_licensing()->get_licenses_of_purchase(base64_decode($_GET['payment_id']));
				if( ! empty($child_keys )){?>

				<div class="entry-content clear" itemprop="text">
					<?php do_action( 'edd_before_download_history' ); ?>
				   <table id="edd_user_history" class="edd-table">
				      <thead>
				         <tr class="edd_purchase_row">
				         	<?php do_action( 'edd_download_history_header_start' ); ?>
				         	<th class=" edd_purchase_amount"><?php _e('Item','edd-pd')?></th>
				            <th class="edd_purchase_details"><?php _e('Key','edd_pd')?></th>
				             <th class="edd_license_key"><?php _e('Status','edd_pd')?></th>
				            <th class="edd_license_key"><?php _e('Activations','edd_pd')?></th>
				            <th class="edd_purchase_date"><?php _e('Expiration','edd_pd')?></th>
				            <?php do_action( 'edd_download_history_header_end' ); ?>
						 </tr>
				      </thead>
				      <tbody>
				      <?php foreach ( $child_keys as $child_key ) { ?>
						<tr class="edd_sl_license_row">
						    <?php do_action( 'edd_download_history_row_start', $child_key->ID );?>
							<td class="edd_sl_item"><?php echo $child_key->download->post_title ?></td>
							<td class="edd_sl_key"> <?php echo  $child_key->key ?></td>
							<td class="edd_sl_status"> <?php echo $child_key->status ?> </td>
							<td class="edd_sl_limit"><?php echo $child_key->activation_count ?> / <?php echo$child_key->activation_limit ?></td>
							<td class="edd_sl_expiration">
							<?php  if ( $child_key->expiration == 0 ) {
								  echo  $child_key->expiration ;
							 } else {
							 	echo  date_i18n( 'F j, Y', $child_key->expiration) ;
							 }  ?> </td> 
						  <?php do_action( 'edd_download_history_row_end', $child_key->ID );	?>
						</tr>
                <?php	} ?>
					  </tbody>
				   </table>
				</div>
				<?php
			} else {
				echo 'Invalid Request....!!!';
			}
			}
			} else {
			  	echo 'Not valid user...';
			  }
			} else {
				echo "Access Denied";
			}
		  } else {
			   echo 'User not login ...';
		 }
		}
	
}

	EDDPD_Loader::get_instance();

}
	?>