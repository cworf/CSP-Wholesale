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
 * Product Data Panel - General Tab
 *
 * Functions to modify the Product Data Panel - General Tab to add the
 * measurement price calculator fields
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action( 'woocommerce_product_options_dimensions', 'wc_price_calculator_product_options_dimensions' );

/**
 * Display our custom product Area/Volume meta fields in the product edit page
 */
function wc_price_calculator_product_options_dimensions() {
	global $thepostid;

	woocommerce_wp_text_input( array( 'id' => '_area', 'label' => __( 'Area', WC_Measurement_Price_Calculator::TEXT_DOMAIN ) . ' (' . get_option( 'woocommerce_area_unit' ) . ')', 'description' => __( 'Overrides the area calculated from the width/length dimensions for the Measurements Price Calculator.', WC_Measurement_Price_Calculator::TEXT_DOMAIN ) ) );

	woocommerce_wp_text_input( array( 'id' => '_volume', 'label' => __( 'Volume', WC_Measurement_Price_Calculator::TEXT_DOMAIN ) . ' (' . get_option( 'woocommerce_volume_unit' ) . ')', 'description' => __( 'Overrides the volume calculated from the width/length/height dimensions for the Measurements Price Calculator.', WC_Measurement_Price_Calculator::TEXT_DOMAIN ) ) );
}


add_action( 'woocommerce_process_product_meta', 'wc_measurement_price_calculator_process_product_meta', 10, 2 );

/**
 * Save our custom product meta fields
 */
function wc_measurement_price_calculator_process_product_meta( $post_id, $post ) {

	$is_virtual = isset( $_POST['_virtual'] ) ? 'yes' : 'no';

	// Dimensions
	if ( 'no' == $is_virtual ) {
		update_post_meta( $post_id, '_area',   stripslashes( $_POST['_area'] ) );
		update_post_meta( $post_id, '_volume', stripslashes( $_POST['_volume'] ) );
	} else {
		update_post_meta( $post_id, '_area',   '' );
		update_post_meta( $post_id, '_volume', '' );
	}

	// compensate for non-integral stock quantities enforced by WC core
	$product_type = empty( $_POST['product-type'] ) ? 'simple' : sanitize_title( stripslashes( $_POST['product-type'] ) );

	if ( 'yes' == get_option( 'woocommerce_manage_stock' ) ) {

		if ( ! empty( $_POST['_manage_stock'] ) && 'grouped' != $product_type && 'external' != $product_type ) {

			// Manage stock
			update_post_meta( $post_id, '_stock', is_numeric( $_POST['_stock'] ) ? $_POST['_stock'] : (int) $_POST['_stock'] );

			// Check stock level (allowing stock quantities between 0 and 1 to be accepted, ie 0.5
			if ( 'variable' !== $product_type && 'no' == $_POST['_backorders'] && $_POST['_stock'] < 1 && $_POST['_stock'] > 0 )
				update_post_meta( $post_id, '_stock_status', stripslashes( $_POST['_stock_status'] ) );

		}

	}
}


// add a minimum price field to simple products under the 'General' tab
add_action( 'woocommerce_product_options_pricing', 'wc_measurement_price_calculator_product_minimum_price' );

/**
 * Display the minimum price field for simple pricing calculator products
 *
 * @since 3.1
 */
function wc_measurement_price_calculator_product_minimum_price() {

	woocommerce_wp_text_input(
		array(
			'id'                => '_wc_measurement_price_calculator_min_price',
			'wrapper_class'     => 'show_if_pricing_calculator',
			'class'             => 'wc_input_price short',
			'label'             => sprintf( __( 'Minimum Price (%s)', WC_Measurement_Price_Calculator::TEXT_DOMAIN ), get_woocommerce_currency_symbol() ),
			'type'              => 'number',
			'custom_attributes' => array(
				'step' => 'any',
				'min'  => '0',
			),
		)
	);
}


// save the minimum price field for simple products
add_action( 'woocommerce_process_product_meta', 'wc_measurement_price_calculator_product_minimum_price_save' );

/**
 * save the minimum price field for simple products
 *
 * @since 3.1
 */
function wc_measurement_price_calculator_product_minimum_price_save( $post_id ) {

	update_post_meta( $post_id, '_wc_measurement_price_calculator_min_price', stripslashes( $_POST['_wc_measurement_price_calculator_min_price'] ) );
}
