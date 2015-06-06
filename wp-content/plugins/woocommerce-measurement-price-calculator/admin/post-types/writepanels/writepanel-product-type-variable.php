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
 * Product Data Panel - Product Variations
 *
 * Functions to modify the Product Data Panel - Variations panels to add the
 * measurement price calculator area/volume fields
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action( 'woocommerce_product_after_variable_attributes', 'wc_price_calculator_product_after_variable_attributes', 10, 2 );

/**
 * Display our custom product Area/Volume meta fields in the product
 * variation form
 *
 * @param int $loop the loop index
 * @param array $variation_data the variation data
 */
function wc_price_calculator_product_after_variable_attributes( $loop, $variation_data ) {
	global $post;

	// will use the parent area/volume (if set) as the placeholder
	$parent_data = array(
		'area'   => $post ? get_post_meta( $post->ID, '_area', true   ) : null,
		'volume' => $post ? get_post_meta( $post->ID, '_volume', true ) : null,
	);

	// default placeholders
	if ( ! $parent_data['area'] )  $parent_data['area']    = '0.00';
	if ( ! $parent_data['volume'] ) $parent_data['volume'] = '0';
	?>
	<tr>
		<td class="hide_if_variation_virtual">
			<label><?php _e( 'Area', WC_Measurement_Price_Calculator::TEXT_DOMAIN ) . ' (' . esc_html( get_option( 'woocommerce_area_unit' ) ) . '):'; ?> <a class="tips" data-tip="<?php _e( 'Overrides the area calculated from the width/length dimensions for the Measurements Price Calculator.', WC_Measurement_Price_Calculator::TEXT_DOMAIN ); ?>" href="#">[?]</a></label>
			<input type="number" size="5" name="variable_area[<?php echo $loop; ?>]" value="<?php if ( isset( $variation_data['_area'][0] ) ) echo esc_attr( $variation_data['_area'][0] ); ?>" placeholder="<?php echo $parent_data['area']; ?>" step="any" min="0" />
		</td>
		<td class="hide_if_variation_virtual">
			<label><?php _e( 'Volume', WC_Measurement_Price_Calculator::TEXT_DOMAIN ) . ' (' . esc_html( get_option( 'woocommerce_volume_unit' ) ) . '):'; ?> <a class="tips" data-tip="<?php _e( 'Overrides the volume calculated from the width/length/height dimensions for the Measurements Price Calculator.', WC_Measurement_Price_Calculator::TEXT_DOMAIN ); ?>" href="#">[?]</a></label>
			<input type="number" size="5" name="variable_volume[<?php echo $loop; ?>]" value="<?php if ( isset( $variation_data['_volume'][0] ) ) echo esc_attr( $variation_data['_volume'][0] ); ?>" placeholder="<?php echo $parent_data['volume']; ?>" step="any" min="0" />
		</td>
	</tr><?php
}


add_action( 'woocommerce_process_product_meta_variable', 'wc_measurement_price_calculator_process_product_meta_variable' );

/**
 * Save the variable product options.
 *
 * @param mixed $post_id the post identifier
 */
function wc_measurement_price_calculator_process_product_meta_variable( $post_id ) {

	if ( isset( $_POST['variable_sku'] ) ) {

		$variable_post_id = $_POST['variable_post_id'];
		$variable_area    = $_POST['variable_area'];
		$variable_volume  = $_POST['variable_volume'];

		$max_loop = max( array_keys( $_POST['variable_post_id'] ) );

		for ( $i = 0; $i <= $max_loop; $i++ ) {

			if ( ! isset( $variable_post_id[ $i ] ) ) continue;

			$variation_id = (int) $variable_post_id[ $i ];

			// Update post meta
			update_post_meta( $variation_id, '_area',   $variable_area[ $i ] );
			update_post_meta( $variation_id, '_volume', $variable_volume[ $i ] );
		}
	}
}
