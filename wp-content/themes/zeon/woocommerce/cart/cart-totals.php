<?php
/**
 * Cart totals
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="cart_totals <?php if ( WC()->customer->has_calculated_shipping() ) echo 'calculated_shipping'; ?>">

	<?php do_action( 'woocommerce_before_cart_totals' ); ?>

		<p class="cart-subtotal">
			<?php _e( 'Sub-total', 'woocommerce' ); ?> :
			<?php wc_cart_totals_subtotal_html(); ?>
		</p>

		<?php foreach ( WC()->cart->get_coupons( 'cart' ) as $code => $coupon ) : ?>
			<p class="cart-discount coupon-<?php echo esc_attr( $code ); ?>">
				<?php wc_cart_totals_coupon_label( $coupon ); ?> :
				<?php wc_cart_totals_coupon_html( $coupon ); ?>
			</p>
		<?php endforeach; ?>

		<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>

			<?php do_action( 'woocommerce_cart_totals_before_shipping' ); ?>

			<?php wc_cart_totals_shipping_html(); ?>

			<?php do_action( 'woocommerce_cart_totals_after_shipping' ); ?>

		<?php endif; ?>

		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<p class="fee">
				<?php echo esc_html( $fee->name ); ?> :
				<?php wc_cart_totals_fee_html( $fee ); ?>
			</p>
		<?php endforeach; ?>

		<?php if ( WC()->cart->tax_display_cart == 'excl' ) : ?>
			<?php if ( get_option( 'woocommerce_tax_total_display' ) == 'itemized' ) : ?>
				<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
					<p class="tax-rate tax-rate-<?php echo sanitize_title( $code ); ?>">
						<?php echo esc_html( $tax->label ); ?>
						<?php echo wp_kses_post( $tax->formatted_amount ); ?>
					</p>
				<?php endforeach; ?>
			<?php else : ?>
				<p class="tax-total">
					<?php echo esc_html( WC()->countries->tax_or_vat() ); ?> :
					<?php echo wc_price( WC()->cart->get_taxes_total() ); ?>
				</p>
			<?php endif; ?>
		<?php endif; ?>

		<?php foreach ( WC()->cart->get_coupons( 'order' ) as $code => $coupon ) : ?>
			<p class="order-discount coupon-<?php echo esc_attr( $code ); ?>">
				<?php wc_cart_totals_coupon_label( $coupon ); ?> :
				<?php wc_cart_totals_coupon_html( $coupon ); ?>
			</p>
		<?php endforeach; ?>

		<?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>

		<p class="order-total">
			<?php _e( 'Order Total', 'woocommerce' ); ?>
			<?php wc_cart_totals_order_total_html(); ?>
		</p>

		<?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>

	<?php if ( WC()->cart->get_cart_tax() ) : ?>
		<p><small><?php

			$estimated_text = WC()->customer->is_customer_outside_base() && ! WC()->customer->has_calculated_shipping()
				? sprintf( ' ' . __( ' (taxes estimated for %s)', 'woocommerce' ), WC()->countries->estimated_for_prefix() . __( WC()->countries->countries[ WC()->countries->get_base_country() ], 'woocommerce' ) )
				: '';

			printf( __( 'Note: Shipping and taxes are estimated%s and will be updated during checkout based on your billing and shipping information.', 'woocommerce' ), $estimated_text );

		?></small></p>
	<?php endif; ?>

	<?php do_action( 'woocommerce_after_cart_totals' ); ?>

</div>