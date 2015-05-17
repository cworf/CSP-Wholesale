<?php
/**
 * Checkout coupon form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;

if ( ! WC()->cart->coupons_enabled() )
	return;

$info_message = apply_filters( 'woocommerce_checkout_coupon_message', __( 'Have a coupon?', 'woocommerce' ) );
$info_message .= ' <a href="#" class="showcoupon">' . __( 'Click here to enter your code', 'woocommerce' ) . '</a>';
wc_print_notice( $info_message, 'notice' );
?>
<div class="row">
	<div class="col-md-6">
		<form class="checkout_coupon" method="post" style="display:none">

			<p class="form-row form-row-first">
				<input type="text" name="coupon_code" class="input-text input-line" placeholder="<?php _e( 'Coupon code', 'woocommerce' ); ?>" id="coupon_code" value="" />
			</p>

			<p class="form-row form-row-last">
				<input type="submit" class="button button-6" name="apply_coupon" value="<?php _e( 'Apply Coupon', 'woocommerce' ); ?>" />
			</p>

			<div class="clear"></div>
		</form>
	</div>
</div>
