<?php
/**
 * Login Form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<?php wc_print_notices(); ?>

<?php do_action( 'woocommerce_before_customer_login_form' ); ?>
<div class="container">
    <div class="site-title">
    	<div class="site-inside">
    		<span><?php _e( 'Login / Register', 'woocommerce' ); ?></span>
    	</div>
    </div>         
    <div class="row">

		<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>

			<div class="col-md-6">

		<?php endif; ?>

		<div class="forms-separation">
	        <div class="login-form-box">
	            <form method="post" class="login-form login">
					<h3><?php _e( 'Login Now', 'woocommerce' ); ?></h3>

					<?php do_action( 'woocommerce_login_form_start' ); ?>

					<p class="form-row form-row-wide">
						<?php _e( 'Username or e-mail', 'woocommerce' ); ?>
					</p>
						<input type="text" class="input-text login-line" name="username" id="username" />
					
					<p class="form-row form-row-wide">
						<?php _e( 'Password', 'woocommerce' ); ?>
					</p>
						<input class="input-text login-line" type="password" name="password" id="password" />
					

					<?php do_action( 'woocommerce_login_form' ); ?>

					<p class="form-row">
						<?php wp_nonce_field( 'woocommerce-login' ); ?>
						<input type="submit" class="button button-6" name="login" value="<?php _e( 'Login', 'woocommerce' ); ?>" /> 
						<label for="rememberme" class="inline">
							<input name="rememberme" type="checkbox" id="rememberme" value="forever" /> <?php _e( 'Remember me', 'woocommerce' ); ?>
						</label>
					</p>
					<p class="lost_password">
						<a href="<?php echo esc_url( wc_lostpassword_url() ); ?>" class="lost-password"><?php _e( 'Lost password', 'woocommerce' ); ?></a>
					</p>

					<?php do_action( 'woocommerce_login_form_end' ); ?>

				</form>
			</div>
		</div>
		

		<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>
			</div> <!-- /Column -->
			<div class="col-md-6">

				<h3><?php _e( 'Register', 'woocommerce' ); ?></h3>

				<form method="post" class="register register-form">

					<?php do_action( 'woocommerce_register_form_start' ); ?>

					<?php if ( get_option( 'woocommerce_registration_generate_username' ) === 'no' ) : ?>

						<p class="form-row form-row-wide">
							<?php _e( 'Username', 'woocommerce' ); ?> 
						</p>
						<input type="text" class="input-text input-line" name="username" id="reg_username" value="<?php if ( ! empty( $_POST['username'] ) ) esc_attr( $_POST['username'] ); ?>" />
						

					<?php endif; ?>

					<p class="form-row form-row-wide">
						<?php _e( 'E-mail', 'woocommerce' ); ?>
					</p>
					<input type="email" class="input-text input-line" name="email" id="reg_email" value="<?php if ( ! empty( $_POST['email'] ) ) esc_attr( $_POST['email'] ); ?>" />
					
					
					<p class="form-row form-row-wide">
						<?php _e( 'Password', 'woocommerce' ); ?>
					</p>
						<input type="password" class="input-text input-line" name="password" id="reg_password" value="<?php if ( ! empty( $_POST['password'] ) ) esc_attr( $_POST['password'] ); ?>" />
					

					<!-- Spam Trap -->
					<div style="left:-999em; position:absolute;"><label for="trap"><?php _e( 'Anti-spam', 'woocommerce' ); ?></label><input type="text" name="email_2" id="trap" tabindex="-1" /></div>

					<?php do_action( 'woocommerce_register_form' ); ?>
					<?php do_action( 'register_form' ); ?>

					<p class="form-row">
						<?php wp_nonce_field( 'woocommerce-register', 'register' ); ?>
						<input type="submit" class="button button-6" name="register" value="<?php _e( 'Register', 'woocommerce' ); ?>" />
					</p>

					<?php do_action( 'woocommerce_register_form_end' ); ?>

				</form>

			</div>
		
		<?php endif; ?>
	</div>
</div>	<!-- Container -->

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>