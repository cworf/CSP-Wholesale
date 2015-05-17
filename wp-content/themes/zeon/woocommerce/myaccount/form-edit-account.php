<?php
/**
 * Edit account form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;
?>

<?php wc_print_notices(); ?>

<form action="" method="post">

	<p class="form-row form-row-first"><?php _e( 'First name', 'woocommerce' ); ?> <span class="required">*</span></p>
		<input type="text" class="input-text input-line" name="account_first_name" id="account_first_name" value="<?php esc_attr_e( $user->first_name ); ?>" />
	
	<p class="form-row form-row-last"><?php _e( 'Last name', 'woocommerce' ); ?> <span class="required">*</span></p>
		<input type="text" class="input-text input-line" name="account_last_name" id="account_last_name" value="<?php esc_attr_e( $user->last_name ); ?>" />
	
	<p class="form-row form-row-wide"><?php _e( 'Email address', 'woocommerce' ); ?> <span class="required">*</span></p>
		<input type="email" class="input-text input-line" name="account_email" id="account_email" value="<?php esc_attr_e( $user->user_email ); ?>" />
	
	<p class="form-row form-row-first"><?php _e( 'Password (leave blank to leave unchanged)', 'woocommerce' ); ?></p>
		<input type="password" class="input-text input-line" name="password_1" id="password_1" />
	
	<p class="form-row form-row-last"><?php _e( 'Confirm new password', 'woocommerce' ); ?></p>
		<input type="password" class="input-text input-line" name="password_2" id="password_2" />
	
	<div class="clear"></div>

	<p><input type="submit" class="button button-6" name="save_account_details" value="<?php _e( 'Save changes', 'woocommerce' ); ?>" /></p>

	<?php wp_nonce_field( 'save_account_details' ); ?>
	<input type="hidden" name="action" value="save_account_details" />
</form>