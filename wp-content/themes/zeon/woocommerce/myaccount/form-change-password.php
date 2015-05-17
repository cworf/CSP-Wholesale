<?php
/**
 * Change password form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;
?>

<?php wc_print_notices(); ?>

<form action="<?php echo esc_url( get_permalink( wc_get_page_id( 'change_password' ) ) ); ?>" method="post">

	<p class="form-row form-row-first"><?php _e( 'New password', 'woocommerce' ); ?> <span class="required">*</span></p>
		<input type="password" class="input-text input-line" name="password_1" id="password_1" />
	
	<p class="form-row form-row-last"><?php _e( 'Re-enter new password', 'woocommerce' ); ?> <span class="required">*</span></p>
		<input type="password" class="input-text input-line" name="password_2" id="password_2" />
	
	<div class="clear"></div>

	<p><input type="submit" class="button button-6" name="change_password" value="<?php _e( 'Save', 'woocommerce' ); ?>" /></p>

	<?php wp_nonce_field( 'woocommerce-change_password' ); ?>
	<input type="hidden" name="action" value="change_password" />

</form>