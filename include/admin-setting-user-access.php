<?php
/**
 * Admin setting for user access
 *
 * @package EDD Purchase
 */

?>

<div class="wrap">
	<h1><?php _e( 'Give Purchase access to', 'edd_pd' ); ?> </h1>
	<p><?php _e( 'Use these settings to limit which features users can access Purchase details.', 'edd_pd' ); ?></p>
	<form action="options.php" method="post" class="edd_pd_setting_form" >
		<div class="edd_pd_save_setting">
		<?php
		$roles = get_option( 'user_access' );
		settings_fields( 'edd_pd_save_setting' );
		do_settings_sections( 'edd_pd_save_setting' );
		foreach ( get_editable_roles() as $role_name => $role_info ) :
			if ( ! empty( $roles ) ) {
				$checked = in_array( $role_name, $roles ) ? 'checked' : '';
			} else {
				$checked = '';
			}
			?>
			<fieldset>
				<input type="checkbox" name="user_access[]" value="<?php echo $role_name; ?>" <?php echo $checked; ?>> <?php echo $role_name; ?>
			</fieldset>
			<?php
		endforeach;
		submit_button();
		wp_nonce_field( 'edd-ps-user-access', 'user-access-nonce' );
		?>
		</div>
	</form>
</div>
