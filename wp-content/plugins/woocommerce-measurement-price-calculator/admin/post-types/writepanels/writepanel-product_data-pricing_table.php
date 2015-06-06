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
 * @package   WC-Measurement-Price-Calculator/Admin/Write-Panels
 * @author    SkyVerge
 * @copyright Copyright (c) 2012-2014, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

/**
 * Product Data Panel - Measurement Price Calculator Tab - Pricing Table Sub Panel
 *
 * Functions for displaying the measurement price calculator product data panel tab
 * pricing table sub panel
 *
 * @since 3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;

?>

<table class="widefat wc-calculator-pricing-table">
	<thead>
	<tr>
		<th class="check-column"><input type="checkbox"></th>
		<th class="measurement-range-column">
			<span data-text="<?php _e( 'Measurement Range', WC_Measurement_Price_Calculator::TEXT_DOMAIN ); ?>"><?php _e( 'Measurement Range', WC_Measurement_Price_Calculator::TEXT_DOMAIN ); ?></span>
			<img class="help_tip" data-tip='<?php _e( 'Configure the starting-ending range, inclusive, of measurements to match this rule.  The first matched rule will be used to determine the price.  The final rule can be defined without an ending range to match all measurements greater than or equal to its starting range.', WC_Measurement_Price_Calculator::TEXT_DOMAIN ) ?>' src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
		</th>
		<th class="price-per-unit-column">
			<span data-text="<?php _e( 'Price per Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ); ?>"><?php _e( 'Price per Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ); ?></span>
			<img class="help_tip" data-tip='<?php _e( 'Set the price per unit for the configured range.', WC_Measurement_Price_Calculator::TEXT_DOMAIN ) ?>' src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
		</th>
		<th class="sale-price-per-unit-column">
			<span data-text="<?php _e( 'Sale Price per Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ); ?>"><?php _e( 'Sale Price per Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ); ?></span>
			<img class="help_tip" data-tip='<?php _e( 'Set a sale price per unit for the configured range.', WC_Measurement_Price_Calculator::TEXT_DOMAIN ) ?>' src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
		</th>
	</tr>
	</thead>
	<tbody>
	<?php

	$rules = get_post_meta( $post->ID, '_wc_price_calculator_pricing_rules', true );

	if ( ! empty( $rules ) ) :
		$index = 0;
		foreach ( $rules as $rule ) :

			?>
			<tr class="wc-calculator-pricing-rule">
				<td class="check-column">
					<input type="checkbox" name="select" />
				</td>
				<td class="wc-calculator-pricing-rule-range">
					<input type="text" name="_wc_measurement_pricing_rule_range_start[<?php echo $index; ?>]" value="<?php echo $rule['range_start']; ?>" /> -
					<input type="text" name="_wc_measurement_pricing_rule_range_end[<?php echo $index; ?>]" value="<?php echo $rule['range_end']; ?>" />
				</td>
				<td>
					<input type="text" name="_wc_measurement_pricing_rule_regular_price[<?php echo $index; ?>]" value="<?php echo $rule['regular_price']; ?>" />
				</td>
				<td>
					<input type="text" name="_wc_measurement_pricing_rule_sale_price[<?php echo $index; ?>]" value="<?php echo $rule['sale_price']; ?>" />
				</td>
			</tr>
			<?php
			$index++;
		endforeach;
	endif;
	?>
	</tbody>
	<tfoot>
	<tr>
		<th colspan="4">
			<button type="button" class="button button-primary wc-calculator-pricing-table-add-rule"><?php _e( 'Add Rule', WC_Measurement_Price_Calculator::TEXT_DOMAIN ); ?></button>
			<button type="button" class="button button-secondary wc-calculator-pricing-table-delete-rules"><?php _e( 'Delete Selected', WC_Measurement_Price_Calculator::TEXT_DOMAIN ); ?></button>
		</th>
	</tr>
	</tfoot>
</table>
