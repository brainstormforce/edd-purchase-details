<?php
/**
 * Admin setting for user access
 *
 * @package EDD-PD
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<div class="wrap">
	<h1><?php esc_html_e( 'Access Payment History ', 'edd-purchase-details' ); ?> </h1>
	<h3><?php esc_html_e( 'Manage which user roles can access purchase details information.', 'edd-purchase-details' ); ?></h3>
	<form action="options.php" method="post" class="edd_pd_setting_form" >
		<div class="epf_save_setting">
		<?php
		$roles = get_option( 'edd_pd_user_access' );
		settings_fields( 'options_access_payment_history' );
		do_settings_sections( 'options_access_payment_history' );
		foreach ( get_editable_roles() as $role_name => $role_info ) :
			$name = translate_user_role( $role_name );
			if ( ! empty( $roles ) ) {
				$checked = in_array( $role_name, $roles, true ) ? 'checked' : '';
			} elseif ( 'administrator' === $role_name ) {
				$checked = 'checked';
			} else {
				$checked = '';
			}
			?>
			<fieldset>
				<input type="checkbox" name="edd_pd_user_access[]" value="<?php echo esc_attr( $role_name ); ?>" <?php echo esc_attr( $checked ); ?>>
				<?php echo esc_html( ucwords( str_replace( '_', ' ', $role_name ) ) ); ?>
			</fieldset>
			<?php
		endforeach;
		submit_button();
		wp_nonce_field( 'edd-pd-user-access', 'edd-pd-user-access-nonce' );
		?>
		</div>
	</form>
</div>
<div class="clear"></div>
<hr>
<div class="epf-user-guides">
	<h3> <?php esc_html_e( 'Getting Started', 'edd-purchase-details' ); ?> </h3>
	<fieldset>
		<legend class="screen-reader-text"><span>Note : </span></legend>
		<p><label for="comment_max_links"> <?php esc_html_e( 'Copy this shortcode and paste it into your post or page', 'edd-purchase-details' ); ?> </p>
		<span class="shortcode"><input type="text" onfocus="this.select();" size ="30" readonly="readonly" value="[access_to_purchase_details]" class="code"></span>
	</fieldset>
</div>
