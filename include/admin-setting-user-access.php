<?php
/**
 * Admin setting for user access
 *
 * @package EDD Purchase
 */

?>

<div class="wrap">
	<h1><?php _e( 'Access Payment History ', 'edd-purchase-details' ); ?> </h1>
	<h3><?php _e( 'Use these settings to limit which features users can Access Payment History.', 'edd-purchase-details' ); ?></h3>
	<form action="options.php" method="post" class="edd_pd_setting_form" >
		<div class="edd_pd_save_setting">
		<?php
		$roles = get_option( 'user_access' );
		settings_fields( 'options_access_payment_history' );
		do_settings_sections( 'options_access_payment_history' );
		foreach ( get_editable_roles() as $role_name => $role_info ) :
			$name = translate_user_role( $role_name );
			if ( ! empty( $roles ) ) {
				$checked = in_array( $role_name, $roles ) ? 'checked' : '';
			} elseif ( 'administrator' == $role_name ) {
				$checked = 'checked';
			} else {
				$checked = '';
			}
			?>
			<fieldset>
				<input type="checkbox" name="user_access[]" value="<?php echo $role_name; ?>" <?php echo $checked; ?>>
				<?php echo esc_html( ucwords( str_replace( '_', ' ', $role_name ) ) ); ?>
			</fieldset>
			<?php
		endforeach;
		submit_button();
		wp_nonce_field( 'edd-ps-user-access', 'user-access-nonce' );
		?>
		</div>
	</form>

<div class="clear"></div>
<hr>
<h3> How to use?</h3>
<fieldset>
<legend class="screen-reader-text"><span>Note : </span></legend>
<p><label for="comment_max_links">  Add this shortcode for view access details </p>
<p><label for="comment_max_links">  'access_to_purchase_details'   </p>

</fieldset>
</div>


