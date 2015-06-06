<?php
/**
 * WooCommerce Measurement Price Calculator
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Measurement Price Calculator to newer
 * versions in the future. If you wish to customize WooCommerce Measurement Price Calculator for your
 * needs please refer to http://docs.woothemes.com/document/measurement-price-calculator/ for more information.
 *
 * @package   WC-Measurement-Price-Calculator/Templates
 * @author    SkyVerge
 * @copyright Copyright (c) 2012-2014, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

/**
 * Product page measurement pricing calculator
 *
 * @version 3.1.2
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product;

$total_amount_text = apply_filters(
	'wc_measurement_price_calculator_total_amount_text',
	$product_measurement->get_unit_label() ?
		sprintf( __( 'Total %s (%s)', WC_Measurement_Price_Calculator::TEXT_DOMAIN ), $product_measurement->get_label(), __( $product_measurement->get_unit_label(), WC_Measurement_Price_Calculator::TEXT_DOMAIN ) ) :
		sprintf( __( 'Total %s', WC_Measurement_Price_Calculator::TEXT_DOMAIN ), $product_measurement->get_label() ),
	$product
);

?>
<table id="price_calculator" class="<?php echo $product->product_type . "_price_calculator" ?>">
	<?php foreach ( $measurements as $measurement ) : ?>
	<tr>
		<td><label for="<?php echo $measurement->get_name(); ?>_needed"><?php echo ( $measurement->get_unit_label() ? sprintf( __( '%s (%s)', WC_Measurement_Price_Calculator::TEXT_DOMAIN ), $measurement->get_label(), __( $measurement->get_unit_label(), WC_Measurement_Price_Calculator::TEXT_DOMAIN ) ) : __( $measurement->get_label(), WC_Measurement_Price_Calculator::TEXT_DOMAIN ) ); ?></label></td>
		<td style="text-align:right;">
			<?php if ( 0 == count( $measurement->get_options() ) ) : ?>
				<input type="text" data-unit="<?php echo $measurement->get_unit(); ?>" data-common-unit="<?php echo $measurement->get_unit_common(); ?>" name="<?php echo $measurement->get_name(); ?>_needed" id="<?php echo $measurement->get_name(); ?>_needed" class="amount_needed" autocomplete="off" />
			<?php elseif ( 1 == count( $measurement->get_options() ) ) : ?>
				<?php
					$measurement_options = $measurement->get_options();
					$measurement_options_keys = array_keys( $measurement_options );
				?>
				<?php echo array_pop( $measurement_options ); ?> <input type="hidden" value="<?php echo array_pop( $measurement_options_keys ); ?>" data-unit="<?php echo $measurement->get_unit(); ?>" data-common-unit="<?php echo $measurement->get_unit_common(); ?>" name="<?php echo $measurement->get_name(); ?>_needed" id="<?php echo $measurement->get_name(); ?>_needed" class="amount_needed" />
			<?php else : ?>
				<select data-unit="<?php echo $measurement->get_unit(); ?>" data-common-unit="<?php echo $measurement->get_unit_common(); ?>"  name="<?php echo $measurement->get_name(); ?>_needed" id="<?php echo $measurement->get_name(); ?>_needed" class="amount_needed">
					<?php foreach ( $measurement->get_options() as $value => $label ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>"><?php echo $label; ?></option>
					<?php endforeach; ?>
				</select>
			<?php endif; ?>
		</td>
	</tr>
	<?php endforeach; ?>
	<?php if ( $settings->is_calculator_type_derived() ) : ?>
		<tr><td><?php echo $total_amount_text; ?></td><td><span class="wc-measurement-price-calculator-total-amount" data-unit="<?php echo $product_measurement->get_unit(); ?>"></span></td></tr>
	<?php endif; ?>
	<tr>
		<td><?php _e( 'Product Price', WC_Measurement_Price_Calculator::TEXT_DOMAIN ); ?></td>
		<td>
			<span class="product_price"></span>
			<input type="hidden" id="_measurement_needed" name="_measurement_needed" value="" />
			<input type="hidden" id="_measurement_needed_unit" name="_measurement_needed_unit" value="" />
			<?php if ( $product->is_sold_individually() ) : ?>
				<input type="hidden" name="quantity" value="1" />
			<?php endif; ?>
		</td>
	</tr>
</table>
