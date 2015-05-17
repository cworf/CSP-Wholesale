<?php
/**
 * Login form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( is_user_logged_in() ) 
	return;
?>

<form method="post" class="login login-form" <?php if ( $hidden ) echo 'style="display:none;"'; ?>>
	<div class="login-form-box">
		<?php do_action( 'woocommerce_login_form_start' ); ?>

		<?php if ( $message ) echo wpautop( wptexturize( $message ) ); ?>

		<p class="form-row form-row-first"><?php _e( 'Username or email', 'woocommerce' ); ?> <span class="required">*</span></p>
			<input type="text" class="input-text login-line" name="username" id="username" />
		
		<p class="form-row form-row-last"><?php _e( 'Password', 'woocommerce' ); ?> <span class="required">*</span></p>
			<input class="input-text login-line" type="password" name="password" id="password" />
		
		<div class="clear"></div>

		<?php do_action( 'woocommerce_login_form' ); ?>

		<p class="form-row">
			<?php wp_nonce_field( 'woocommerce-login' ); ?>
			<input type="submit" class="button button-6" name="login" value="<?php _e( 'Login', 'woocommerce' ); ?>" />
			<input type="hidden" name="redirect" value="<?php echo esc_url( $redirect ) ?>" />
			<input name="rememberme" type="checkbox" id="rememberme" value="forever" /> <?php _e( 'Remember me', 'woocommerce' ); ?>
		</p>
		<p class="lost_password">
			<a href="<?php echo esc_url( wc_lostpassword_url() ); ?>" class="lost-password"><?php _e( 'Lost your password?', 'woocommerce' ); ?></a>
		</p>

		<div class="clear"></div>

		<?php do_action( 'woocommerce_login_form_end' ); ?>
	</div>
</form>