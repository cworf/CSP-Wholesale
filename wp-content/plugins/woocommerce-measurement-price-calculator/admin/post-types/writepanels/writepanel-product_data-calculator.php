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
 * Product Data Panel - Measurement Price Calculator Tab
 *
 * Functions for displaying the measurement price calculator product data panel tab
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action( 'woocommerce_product_write_panel_tabs', 'wc_price_calculator_product_rates_panel_tab', 99 );

/**
 * Adds the "Calculator" tab to the Product Data postbox in the admin product interface
 */
function wc_price_calculator_product_rates_panel_tab() {
	echo '<li class="measurement_tab hide_if_virtual hide_if_grouped"><a href="#measurement_product_data">' . __( 'Measurement', WC_Measurement_Price_Calculator::TEXT_DOMAIN ) . '</a></li>';
}


add_action( 'woocommerce_product_write_panels', 'wc_price_calculator_product_rates_panel_content' );

/**
 * Adds the Calculator tab panel to the Product Data postbox in the product interface
 */
function wc_price_calculator_product_rates_panel_content() {
	global $post, $wc_measurement_price_calculator;

	$measurement_units = array(
		'weight'    => wc_measurement_price_calculator_get_weight_units(),
		'dimension' => wc_measurement_price_calculator_get_dimension_units(),
		'area'      => wc_measurement_price_calculator_get_area_units(),
		'volume'    => wc_measurement_price_calculator_get_volume_units(),
	);

	?>
	<div id="measurement_product_data" class="panel woocommerce_options_panel">
		<style type="text/css">
			#measurement_product_data hr { height:2px; border-style:none; border-bottom:solid 1px white; color:#DFDFDF; background-color:#DFDFDF; }
			.measurement-subnav { margin:14px 12px; }
			.measurement-subnav a { text-decoration:none; }
			.measurement-subnav a.active { color:black; font-weight:bold; }
			.measurement-subnav a.disabled { color: #8A7F7F; cursor: default; }
			#measurement_product_data .wc-calculator-pricing-table td.wc-calculator-pricing-rule-range input { float:none; width:auto; }
			#measurement_product_data table.wc-calculator-pricing-table { margin: 12px; width: 95%; }
			#measurement_product_data table.wc-calculator-pricing-table td { padding: 10px 7px 10px; cursor: move; }
			#measurement_product_data table.wc-calculator-pricing-table button { font-family: sans-serif; }
			#measurement_product_data table.wc-calculator-pricing-table button.wc-calculator-pricing-table-delete-rules { float: right; }
		</style>
		<div class="measurement-subnav">
			<a class="active" href="#calculator-settings"><?php _e( 'Calculator Settings', WC_Measurement_Price_Calculator::TEXT_DOMAIN ); ?></a> |
			<a class="wc-measurement-price-calculator-pricing-table" href="#calculator-pricing-table"><?php _e( 'Pricing Table', WC_Measurement_Price_Calculator::TEXT_DOMAIN ); ?></a>
		</div>
		<hr/>
		<?php
		$settings = new WC_Price_Calculator_Settings( $post->ID );

		$pricing_weight_wrapper_class = '';
		if ( 'no' === get_option( 'woocommerce_enable_weight', true ) ) {
			$pricing_weight_wrapper_class = 'hidden';
		}

		$settings = $settings->get_raw_settings();  // we want the underlying raw settings array

		$calculator_options = array(
			''                 => __( 'None',                         WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
			'dimension'        => __( 'Dimensions',                   WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
			'area'             => __( 'Area',                         WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
			'area-dimension'   => __( 'Area (LxW)',                   WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
			'area-linear'      => __( 'Perimeter (2L + 2W)',          WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
			'area-surface'     => __( 'Surface Area 2(LW + LH + WH)', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
			'volume'           => __( 'Volume',                       WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
			'volume-dimension' => __( 'Volume (LxWxH)',               WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
			'volume-area'      => __( 'Volume (AxH)',                 WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
			'weight'           => __( 'Weight',                       WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
			'wall-dimension'   => __( 'Room Walls',                   WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
		);

		echo '<div id="calculator-settings" class="calculator-subpanel">';

		// Measurement select
		woocommerce_wp_select( array(
			'id'          => '_measurement_price_calculator',
			'value'       => $settings['calculator_type'],
			'label'       => __( 'Measurement', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
			'options'     => $calculator_options,
			'description' => __( 'Select the product measurement to calculate quantity by or define pricing within.', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
			'desc_tip'    => true,
		) );

		echo '<p id="area-dimension_description" class="measurement_description" style="display:none;">' .   __( "Use this measurement to have the customer prompted for a length and width to calculate the area required.  When pricing is disabled (no custom dimensions) this calculator uses the product area attribute or otherwise the length and width attributes to determine the product area.", WC_Measurement_Price_Calculator::TEXT_DOMAIN ) . '</p>';
		echo '<p id="area-linear_description" class="measurement_description" style="display:none;">' .      __( "Use this measurement to have the customer prompted for a length and width to calculate the linear distance (L * 2 + W * 2).", WC_Measurement_Price_Calculator::TEXT_DOMAIN ) . '</p>';
		echo '<p id="area-surface_description" class="measurement_description" style="display:none;">' .     __( "Use this measurement to have the customer prompted for a length, width and height to calculate the surface area 2 * (L * W + W * H + L * H).", WC_Measurement_Price_Calculator::TEXT_DOMAIN ) . '</p>';
		echo '<p id="volume-dimension_description" class="measurement_description" style="display:none;">' . __( "Use this measurement to have the customer prompted for a length, width and height to calculate the volume required.  When pricing is disabled (no custom dimensions) this calculator uses the product volume attribute or otherwise the length, width and height attributes to determine the product volume.", WC_Measurement_Price_Calculator::TEXT_DOMAIN ) . '</p>';
		echo '<p id="volume-area_description" class="measurement_description" style="display:none;">' .      __( "Use this measurement to have the customer prompted for an area and height to calculate the volume required.  When pricing is disabled (no custom dimensions) this calculator uses the product volume attribute or otherwise the length, width and height attributes to determine the product volume.", WC_Measurement_Price_Calculator::TEXT_DOMAIN ) . '</p>';
		echo '<p id="wall-dimension_description" class="measurement_description" style="display:none;">' .   __( "Use this measurement for applications such as wallpaper; the customer will be prompted for the wall height and distance around the room.  When pricing is disabled (no custom dimensions) this calculator uses the product area attribute or otherwise the length and width attributes to determine the wall surface area.", WC_Measurement_Price_Calculator::TEXT_DOMAIN ) . '</p>';

		echo '<div id="dimension_measurements" class="measurement_fields">';
			woocommerce_wp_checkbox( array(
				'id'            => '_measurement_dimension_pricing',
				'value'         => $settings['dimension']['pricing']['enabled'],
				'class'         => 'checkbox _measurement_pricing',
				'label'         => __( 'Show Product Price Per Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description'   => __( 'Check this box to display product pricing per unit on the frontend', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
			) );
			echo '<div id="_measurement_dimension_pricing_fields" class="_measurement_pricing_fields" style="display:none;">';
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_dimension_pricing_label',
					'value'       => $settings['dimension']['pricing']['label'],
					'label'       => __( 'Pricing Label', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description' => __( 'Label to display next to the product price (defaults to pricing unit)', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_dimension_pricing_unit',
					'value'       => $settings['dimension']['pricing']['unit'],
					'class'       => '_measurement_pricing_unit',
					'label'       => __( 'Pricing Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'options'     => $measurement_units['dimension'],
					'description' => __( 'Unit to define pricing in', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_dimension_pricing_calculator_enabled',
					'class'         => 'checkbox _measurement_pricing_calculator_enabled',
					'value'         => $settings['dimension']['pricing']['calculator']['enabled'],
					'label'         => __( 'Calculated Price', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description'   => __( 'Check this box to define product pricing per unit and allow customers to provide custom measurements', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_dimension_pricing_weight_enabled',
					'value'         => $settings['dimension']['pricing']['weight']['enabled'],
					'class'         => 'checkbox _measurement_pricing_weight_enabled',
					'wrapper_class' => $pricing_weight_wrapper_class . ' _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Weight', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description'   => __( 'Check this box to define the product weight per unit and calculate the item weight based on the product dimension', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_dimension_pricing_inventory_enabled',
					'value'         => $settings['dimension']['pricing']['inventory']['enabled'],
					'class'         => 'checkbox _measurement_pricing_inventory_enabled',
					'wrapper_class' => 'stock_fields _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Inventory', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description'   => __( 'Check this box to define inventory per unit and calculate inventory based on the product dimension', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				) );
			echo '</div>';
			echo '<hr/>';

			// Dimension - Length
			wc_measurement_price_calculator_wp_radio( array(
				'name'        => '_measurement_dimension',
				'id'          => '_measurement_dimension_length',
				'rbvalue'     => 'length',
				'value'       => 'yes' == $settings['dimension']['length']['enabled'] ? 'length' : '',
				'class'       => 'checkbox _measurement_dimension',
				'label'       => __( 'Length', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description' => __( 'Select to display the product length in the price calculator', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
			) );
			echo '<div id="_measurement_dimension_length_fields" style="display:none;">';
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_dimension_length_label',
					'value'       => $settings['dimension']['length']['label'],
					'label'       => __( 'Length Label', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description' => __( 'Length input field label to display on the frontend', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_dimension_length_unit',
					'value'       => $settings['dimension']['length']['unit'] ,
					'label'       => __( 'Length Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'options'     => $measurement_units['dimension'],
					'description' => __( 'The frontend length input field unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_checkbox( array(
					'id'          => '_measurement_dimension_length_editable',
					'value'       => $settings['dimension']['length']['editable'],
					'label'       => __( 'Length Editable', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'class'       => 'checkbox _measurement_editable',
					'description' => __( 'Check this box to allow the needed length to be entered by the customer', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				) );
				woocommerce_wp_text_input( array(
					'id'            => '_measurement_dimension_length_options',
					'value'         => implode( ', ', $settings['dimension']['length']['options'] ),
					'wrapper_class' => '_measurement_pricing_calculator_fields',
					'label'         => __( 'Length Options', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description'   => __( 'Use a single number to set a fixed value for this field on the frontend, or a comma-separated list of numbers to create a select box for the customer to choose between.  Example: 1/8, .5, 2', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'desc_tip'      => true,
				) );
			echo '</div>';
			echo '<hr/>';

			// Dimension - Width
			wc_measurement_price_calculator_wp_radio( array(
				'name'        => '_measurement_dimension',
				'id'          => '_measurement_dimension_width',
				'rbvalue'     => 'width',
				'value'       => 'yes' == $settings['dimension']['width']['enabled'] ? 'width' : '',
				'class'       => 'checkbox _measurement_dimension',
				'label'       => __( 'Width', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description' => __( 'Select to display the product width in the price calculator', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
			) );
			echo '<div id="_measurement_dimension_width_fields" style="display:none;">';
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_dimension_width_label',
					'value'       => $settings['dimension']['width']['label'],
					'label'       => __( 'Width Label', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description' => __( 'Width input field label to display on the frontend', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_dimension_width_unit',
					'value'       => $settings['dimension']['width']['unit'],
					'label'       => __( 'Width Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'options'     => $measurement_units['dimension'],
					'description' => __( 'The frontend width input field unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_checkbox( array(
					'id'          => '_measurement_dimension_width_editable',
					'value'       => $settings['dimension']['width']['editable'],
					'label'       => __( 'Width Editable', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'class'       => 'checkbox _measurement_editable',
					'description' => __( 'Check this box to allow the needed width to be entered by the customer', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				) );
				woocommerce_wp_text_input( array(
					'id'            => '_measurement_dimension_width_options',
					'value'         => implode( ', ', $settings['dimension']['width']['options'] ),
					'wrapper_class' => '_measurement_pricing_calculator_fields',
					'label'         => __( 'Width Options', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description'   => __( 'Use a single number to set a fixed value for this field on the frontend, or a comma-separated list of numbers to create a select box for the customer to choose between.  Example: 1/8, .5, 2', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'desc_tip'      => true,
				) );
			echo '</div>';
			echo '<hr/>';

			// Dimension - Height
			wc_measurement_price_calculator_wp_radio( array(
				'name'        => '_measurement_dimension',
				'id'          => '_measurement_dimension_height',
				'rbvalue'     => 'height',
				'value'       => 'yes' == $settings['dimension']['height']['enabled'] ? 'height' : '',
				'class'       => 'checkbox _measurement_dimension',
				'label'       => __( 'Height', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description' => __( 'Select to display the product height in the price calculator', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
			) );
			echo '<div id="_measurement_dimension_height_fields" style="display:none;">';
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_dimension_height_label',
					'value'       => $settings['dimension']['height']['label'],
					'label'       => __( 'Height Label', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description' => __( 'Height input field label to display on the frontend', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_dimension_height_unit',
					'value'       => $settings['dimension']['height']['unit'],
					'label'       => __( 'Height Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'options'     => $measurement_units['dimension'],
					'description' => __( 'The frontend height input field unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_checkbox( array(
					'id'          => '_measurement_dimension_height_editable',
					'value'       => $settings['dimension']['height']['editable'],
					'label'       => __( 'Height Editable', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'class'       => 'checkbox _measurement_editable',
					'description' => __( 'Check this box to allow the needed height to be entered by the customer', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				) );
				woocommerce_wp_text_input( array(
					'id'            => '_measurement_dimension_height_options',
					'value'         => implode( ', ', $settings['dimension']['height']['options'] ),
					'wrapper_class' => '_measurement_pricing_calculator_fields',
					'label'         => __( 'Height Options', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description'   => __( 'Use a single number to set a fixed value for this field on the frontend, or a comma-separated list of numbers to create a select box for the customer to choose between.  Example: 1/8, .5, 2', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'desc_tip'      => true,
				) );
			echo '</div>';
		echo '</div>';

		// Area
		echo '<div id="area_measurements" class="measurement_fields">';
			woocommerce_wp_checkbox( array(
				'id'            => '_measurement_area_pricing',
				'value'         => $settings['area']['pricing']['enabled'],
				'class'         => 'checkbox _measurement_pricing',
				'label'         => __( 'Show Product Price Per Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description'   => __( 'Check this box to display product pricing per unit on the frontend', WC_Measurement_Price_Calculator::TEXT_DOMAIN )
			) );
			echo '<div id="_measurement_area_pricing_fields" class="_measurement_pricing_fields" style="display:none;">';
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_area_pricing_label',
					'value'       => $settings['area']['pricing']['label'],
					'label'       => __( 'Pricing Label', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description' => __( 'Label to display next to the product price (defaults to pricing unit)', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_area_pricing_unit',
					'value'       => $settings['area']['pricing']['unit'],
					'class'       => '_measurement_pricing_unit',
					'label'       => __( 'Pricing Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'options'     => $measurement_units['area'],
					'description' => __( 'Unit to define pricing in', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_area_pricing_calculator_enabled',
					'class'         => 'checkbox _measurement_pricing_calculator_enabled',
					'value'         => $settings['area']['pricing']['calculator']['enabled'],
					'label'         => __( 'Calculated Price', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description'   => __( 'Check this box to define product pricing per unit and allow customers to provide custom measurements', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_area_pricing_weight_enabled',
					'value'         => $settings['area']['pricing']['weight']['enabled'],
					'class'         => 'checkbox _measurement_pricing_weight_enabled',
					'wrapper_class' => $pricing_weight_wrapper_class . ' _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Weight', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description'   => __( 'Check this box to define the product weight per unit and calculate the item weight based on the product area', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_area_pricing_inventory_enabled',
					'value'         => $settings['area']['pricing']['inventory']['enabled'],
					'class'         => 'checkbox _measurement_pricing_inventory_enabled',
					'wrapper_class' => 'stock_fields _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Inventory', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description'   => __( 'Check this box to define inventory per unit and calculate inventory based on the product area', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				) );
			echo '</div>';
			echo '<hr/>';
			woocommerce_wp_text_input( array(
				'id'          => '_measurement_area_label',
				'value'       => $settings['area']['area']['label'],
				'label'       => __( 'Area Label', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description' => __( 'Area input field label to display on the frontend', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_select( array(
				'id'          => '_measurement_area_unit',
				'value'       => $settings['area']['area']['unit'],
				'label'       => __( 'Area Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'options'     => $measurement_units['area'],
				'description' => __( 'The frontend area input field unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_checkbox( array(
				'id'          => '_measurement_area_editable',
				'value'       => $settings['area']['area']['editable'],
				'label'       => __( 'Editable', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'class'       => 'checkbox _measurement_editable',
				'description' => __( 'Check this box to allow the needed measurement to be entered by the customer', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
			) );
			woocommerce_wp_text_input( array(
				'id'            => '_measurement_area_options',
				'value'         => implode( ', ', $settings['area']['area']['options'] ),
				'wrapper_class' => '_measurement_pricing_calculator_fields',
				'label'         => __( 'Area Options', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description'   => __( 'Use a single number to set a fixed value for this field on the frontend, or a comma-separated list of numbers to create a select box for the customer to choose between.  Example: 1/8, .5, 2', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'      => true,
			) );
		echo '</div>';

		// Area (LxW)
		echo '<div id="area-dimension_measurements" class="measurement_fields">';
			woocommerce_wp_checkbox( array(
				'id'            => '_measurement_area-dimension_pricing',
				'value'         => $settings['area-dimension']['pricing']['enabled'],
				'class'         => 'checkbox _measurement_pricing',
				'label'         => __( 'Show Product Price Per Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description'   => __( 'Check this box to display product pricing per unit on the frontend', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
			) );
			echo '<div id="_measurement_area-dimension_pricing_fields" class="_measurement_pricing_fields" style="display:none;">';
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_area-dimension_pricing_label',
					'value'       => $settings['area-dimension']['pricing']['label'],
					'label'       => __( 'Pricing Label', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description' => __( 'Label to display next to the product price (defaults to pricing unit)', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_area-dimension_pricing_unit',
					'value'       => $settings['area-dimension']['pricing']['unit'],
					'class'       => '_measurement_pricing_unit',
					'label'       => __( 'Pricing Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'options'     => $measurement_units['area'],
					'description' => __( 'Unit to define pricing in', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_area-dimension_pricing_calculator_enabled',
					'class'         => 'checkbox _measurement_pricing_calculator_enabled',
					'value'         => $settings['area-dimension']['pricing']['calculator']['enabled'],
					'label'         => __( 'Calculated Price', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description'   => __( 'Check this box to define product pricing per unit and allow customers to provide custom measurements', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_area-dimension_pricing_weight_enabled',
					'value'         => $settings['area-dimension']['pricing']['weight']['enabled'],
					'class'         => 'checkbox _measurement_pricing_weight_enabled',
					'wrapper_class' => $pricing_weight_wrapper_class . ' _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Weight', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description'   => __( 'Check this box to define the product weight per unit and calculate the item weight based on the product area', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_area-dimension_pricing_inventory_enabled',
					'value'         => $settings['area-dimension']['pricing']['inventory']['enabled'],
					'class'         => 'checkbox _measurement_pricing_inventory_enabled',
					'wrapper_class' => 'stock_fields _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Inventory', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description'   => __( 'Check this box to define inventory per unit and calculate inventory based on the product area', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				) );
			echo '</div>';
			echo '<hr/>';
			woocommerce_wp_text_input( array(
				'id'          => '_measurement_area_length_label',
				'value'       => $settings['area-dimension']['length']['label'],
				'label'       => __( 'Length Label', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description' => __( 'Length input field label to display on the frontend', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_select( array(
				'id'          => '_measurement_area_length_unit',
				'value'       => $settings['area-dimension']['length']['unit'],
				'label'       => __( 'Length Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'options'     => $measurement_units['dimension'],
				'description' => __( 'The frontend length input field unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_text_input( array(
				'id'            => '_measurement_area_length_options',
				'value'         => implode( ', ', $settings['area-dimension']['length']['options'] ),
				'wrapper_class' => '_measurement_pricing_calculator_fields',
				'label'         => __( 'Length Options', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description'   => __( 'Use a single number to set a fixed value for this field on the frontend, or a comma-separated list of numbers to create a select box for the customer to choose between.  Example: 1/8, .5, 2', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'      => true,
			) );
			echo '<hr/>';

			woocommerce_wp_text_input( array(
				'id'          => '_measurement_area_width_label',
				'value'       => $settings['area-dimension']['width']['label'],
				'label'       => __( 'Width Label', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description' => __( 'Width input field label to display on the frontend', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_select( array(
				'id'          => '_measurement_area_width_unit',
				'value'       => $settings['area-dimension']['width']['unit'],
				'label'       => __( 'Width Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'options'     => $measurement_units['dimension'],
				'description' => __( 'The frontend width input field unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_text_input( array(
				'id'            => '_measurement_area_width_options',
				'value'         => implode( ', ', $settings['area-dimension']['width']['options'] ),
				'wrapper_class' => '_measurement_pricing_calculator_fields',
				'label'         => __( 'Width Options', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description'   => __( 'Use a single number to set a fixed value for this field on the frontend, or a comma-separated list of numbers to create a select box for the customer to choose between.  Example: 1/8, .5, 2', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'      => true,
			) );
		echo '</div>';

		// Perimeter (2 * L + 2 * W)
		echo '<div id="area-linear_measurements" class="measurement_fields">';
			woocommerce_wp_checkbox( array(
				'id'            => '_measurement_area-linear_pricing',
				'value'         => $settings['area-linear']['pricing']['enabled'],
				'class'         => 'checkbox _measurement_pricing',
				'label'         => __( 'Show Product Price Per Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description'   => __( 'Check this box to display product pricing per unit on the frontend', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
			) );
			echo '<div id="_measurement_area-linear_pricing_fields" class="_measurement_pricing_fields" style="display:none;">';
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_area-linear_pricing_label',
					'value'       => $settings['area-linear']['pricing']['label'],
					'label'       => __( 'Pricing Label', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description' => __( 'Label to display next to the product price (defaults to pricing unit)', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_area-linear_pricing_unit',
					'value'       => $settings['area-linear']['pricing']['unit'],
					'class'       => '_measurement_pricing_unit',
					'label'       => __( 'Pricing Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'options'     => $measurement_units['dimension'],
					'description' => __( 'Unit to define pricing in', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_area-linear_pricing_calculator_enabled',
					'class'         => 'checkbox _measurement_pricing_calculator_enabled',
					'value'         => $settings['area-linear']['pricing']['calculator']['enabled'],
					'label'         => __( 'Calculated Price', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description'   => __( 'Check this box to define product pricing per unit and allow customers to provide custom measurements', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_area-linear_pricing_weight_enabled',
					'value'         => $settings['area-linear']['pricing']['weight']['enabled'],
					'class'         => 'checkbox _measurement_pricing_weight_enabled',
					'wrapper_class' => $pricing_weight_wrapper_class . ' _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Weight', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description'   => __( 'Check this box to define the product weight per unit and calculate the item weight based on the product area', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_area-linear_pricing_inventory_enabled',
					'value'         => $settings['area-linear']['pricing']['inventory']['enabled'],
					'class'         => 'checkbox _measurement_pricing_inventory_enabled',
					'wrapper_class' => 'stock_fields _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Inventory', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description'   => __( 'Check this box to define inventory per unit and calculate inventory based on the product area', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				) );
			echo '</div>';
			echo '<hr/>';
			woocommerce_wp_text_input( array(
				'id'          => '_measurement_area-linear_length_label',
				'value'       => $settings['area-linear']['length']['label'],
				'label'       => __( 'Length Label', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description' => __( 'Length input field label to display on the frontend', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_select( array(
				'id'          => '_measurement_area-linear_length_unit',
				'value'       => $settings['area-linear']['length']['unit'],
				'label'       => __( 'Length Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'options'     => $measurement_units['dimension'],
				'description' => __( 'The frontend length input field unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_text_input( array(
				'id'            => '_measurement_area-linear_length_options',
				'value'         => implode( ', ', $settings['area-linear']['length']['options'] ),
				'wrapper_class' => '_measurement_pricing_calculator_fields',
				'label'         => __( 'Length Options', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description'   => __( 'Use a single number to set a fixed value for this field on the frontend, or a comma-separated list of numbers to create a select box for the customer to choose between.  Example: 1/8, .5, 2', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'      => true,
			) );
			echo '<hr/>';

			woocommerce_wp_text_input( array(
				'id'          => '_measurement_area-linear_width_label',
				'value'       => $settings['area-linear']['width']['label'],
				'label'       => __( 'Width Label', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description' => __( 'Width input field label to display on the frontend', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_select( array(
				'id'          => '_measurement_area-linear_width_unit',
				'value'       => $settings['area-linear']['width']['unit'],
				'label'       => __( 'Width Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'options'     => $measurement_units['dimension'],
				'description' => __( 'The frontend width input field unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_text_input( array(
				'id'            => '_measurement_area-linear_width_options',
				'value'         => implode( ', ', $settings['area-linear']['width']['options'] ),
				'wrapper_class' => '_measurement_pricing_calculator_fields',
				'label'         => __( 'Width Options', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description'   => __( 'Use a single number to set a fixed value for this field on the frontend, or a comma-separated list of numbers to create a select box for the customer to choose between.  Example: 1/8, .5, 2', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'      => true,
			) );
		echo '</div>';

		// Surface Area 2 * (L * W + W * H + L * H)
		echo '<div id="area-surface_measurements" class="measurement_fields">';
			woocommerce_wp_checkbox( array(
				'id'            => '_measurement_area-surface_pricing',
				'value'         => $settings['area-surface']['pricing']['enabled'],
				'class'         => 'checkbox _measurement_pricing',
				'label'         => __( 'Show Product Price Per Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description'   => __( 'Check this box to display product pricing per unit on the frontend', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
			) );
			echo '<div id="_measurement_area-surface_pricing_fields" class="_measurement_pricing_fields" style="display:none;">';
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_area-surface_pricing_label',
					'value'       => $settings['area-surface']['pricing']['label'],
					'label'       => __( 'Pricing Label', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description' => __( 'Label to display next to the product price (defaults to pricing unit)', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_area-surface_pricing_unit',
					'value'       => $settings['area-surface']['pricing']['unit'],
					'class'       => '_measurement_pricing_unit',
					'label'       => __( 'Pricing Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'options'     => $measurement_units['area'],
					'description' => __( 'Unit to define pricing in', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_area-surface_pricing_calculator_enabled',
					'class'         => 'checkbox _measurement_pricing_calculator_enabled',
					'value'         => $settings['area-surface']['pricing']['calculator']['enabled'],
					'label'         => __( 'Calculated Price', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description'   => __( 'Check this box to define product pricing per unit and allow customers to provide custom measurements', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_area-surface_pricing_weight_enabled',
					'value'         => $settings['area-surface']['pricing']['weight']['enabled'],
					'class'         => 'checkbox _measurement_pricing_weight_enabled',
					'wrapper_class' => $pricing_weight_wrapper_class . ' _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Weight', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description'   => __( 'Check this box to define the product weight per unit and calculate the item weight based on the product area', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_area-surface_pricing_inventory_enabled',
					'value'         => $settings['area-surface']['pricing']['inventory']['enabled'],
					'class'         => 'checkbox _measurement_pricing_inventory_enabled',
					'wrapper_class' => 'stock_fields _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Inventory', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description'   => __( 'Check this box to define inventory per unit and calculate inventory based on the product area', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				) );
			echo '</div>';
			echo '<hr/>';
			woocommerce_wp_text_input( array(
				'id'          => '_measurement_area-surface_length_label',
				'value'       => $settings['area-surface']['length']['label'],
				'label'       => __( 'Length Label', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description' => __( 'Length input field label to display on the frontend', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_select( array(
				'id'          => '_measurement_area-surface_length_unit',
				'value'       => $settings['area-surface']['length']['unit'],
				'label'       => __( 'Length Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'options'     => $measurement_units['dimension'],
				'description' => __( 'The frontend length input field unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_text_input( array(
				'id'            => '_measurement_area-surface_length_options',
				'value'         => implode( ', ', $settings['area-surface']['length']['options'] ),
				'wrapper_class' => '_measurement_pricing_calculator_fields',
				'label'         => __( 'Length Options', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description'   => __( 'Use a single number to set a fixed value for this field on the frontend, or a comma-separated list of numbers to create a select box for the customer to choose between.  Example: 1/8, .5, 2', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'      => true,
			) );
			echo '<hr/>';

			woocommerce_wp_text_input( array(
				'id'          => '_measurement_area-surface_width_label',
				'value'       => $settings['area-surface']['width']['label'],
				'label'       => __( 'Width Label', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description' => __( 'Width input field label to display on the frontend', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_select( array(
				'id'          => '_measurement_area-surface_width_unit',
				'value'       => $settings['area-surface']['width']['unit'],
				'label'       => __( 'Width Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'options'     => $measurement_units['dimension'],
				'description' => __( 'The frontend width input field unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_text_input( array(
				'id'            => '_measurement_area-surface_width_options',
				'value'         => implode( ', ', $settings['area-surface']['width']['options'] ),
				'wrapper_class' => '_measurement_pricing_calculator_fields',
				'label'         => __( 'Width Options', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description'   => __( 'Use a single number to set a fixed value for this field on the frontend, or a comma-separated list of numbers to create a select box for the customer to choose between.  Example: 1/8, .5, 2', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'      => true,
			) );
			echo '<hr/>';

			woocommerce_wp_text_input( array(
				'id'          => '_measurement_area-surface_height_label',
				'value'       => $settings['area-surface']['height']['label'],
				'label'       => __( 'Height Label', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description' => __( 'Height input field label to display on the frontend', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_select( array(
				'id'          => '_measurement_area-surface_height_unit',
				'value'       => $settings['area-surface']['height']['unit'],
				'label'       => __( 'Height Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'options'     => $measurement_units['dimension'],
				'description' => __( 'The frontend height input field unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_text_input( array(
				'id'            => '_measurement_area-surface_height_options',
				'value'         => implode( ', ', $settings['area-surface']['height']['options'] ),
				'wrapper_class' => '_measurement_pricing_calculator_fields',
				'label'         => __( 'Height Options', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description'   => __( 'Use a single number to set a fixed value for this field on the frontend, or a comma-separated list of numbers to create a select box for the customer to choose between.  Example: 1/8, .5, 2', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'      => true,
			) );
		echo '</div>';

		// Volume
		echo '<div id="volume_measurements" class="measurement_fields">';
			woocommerce_wp_checkbox( array(
				'id'            => '_measurement_volume_pricing',
				'value'         => $settings['volume']['pricing']['enabled'],
				'class'         => 'checkbox _measurement_pricing',
				'label'         => __( 'Show Product Price Per Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description'   => __( 'Check this box to display product pricing per unit on the frontend', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
			) );
			echo '<div id="_measurement_volume_pricing_fields" class="_measurement_pricing_fields" style="display:none;">';
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_volume_pricing_label',
					'value'       => $settings['volume']['pricing']['label'],
					'label'       => __( 'Pricing Label', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description' => __( 'Label to display next to the product price (defaults to pricing unit)', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_volume_pricing_unit',
					'value'       => $settings['volume']['pricing']['unit'],
					'class'       => '_measurement_pricing_unit',
					'label'       => __( 'Pricing Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'options'     => $measurement_units['volume'],
					'description' => __( 'Unit to define pricing in', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_volume_pricing_calculator_enabled',
					'class'         => 'checkbox _measurement_pricing_calculator_enabled',
					'value'         => $settings['volume']['pricing']['calculator']['enabled'],
					'label'         => __( 'Calculated Price', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description'   => __( 'Check this box to define product pricing per unit and allow customers to provide custom measurements', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_volume_pricing_weight_enabled',
					'value'         => $settings['volume']['pricing']['weight']['enabled'],
					'class'         => 'checkbox _measurement_pricing_weight_enabled',
					'wrapper_class' => $pricing_weight_wrapper_class . ' _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Weight', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description'   => __( 'Check this box to define the product weight per unit and calculate the item weight based on the product volume', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_volume_pricing_inventory_enabled',
					'value'         => $settings['volume']['pricing']['inventory']['enabled'],
					'class'         => 'checkbox _measurement_pricing_inventory_enabled',
					'wrapper_class' => 'stock_fields _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Inventory', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description'   => __( 'Check this box to define inventory per unit and calculate inventory based on the product volume', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				) );
			echo '</div>';
			echo '<hr/>';
			woocommerce_wp_text_input( array(
				'id'          => '_measurement_volume_label',
				'value'       => $settings['volume']['volume']['label'],
				'label'       => __( 'Volume Label', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description' => __( 'Volume input field label to display on the frontend', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_select( array(
				'id'          => '_measurement_volume_unit',
				'value'       => $settings['volume']['volume']['unit'],
				'label'       => __( 'Volume Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'options'     => $measurement_units['volume'],
				'description' => __( 'The frontend volume input field unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_checkbox( array(
				'id'          => '_measurement_volume_editable',
				'value'       => $settings['volume']['volume']['editable'],
				'label'       => __( 'Editable', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'class'       => 'checkbox _measurement_editable',
				'description' => __( 'Check this box to allow the needed measurement to be entered by the customer', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
			) );
			woocommerce_wp_text_input( array(
				'id'            => '_measurement_volume_options',
				'value'         => implode( ', ', $settings['volume']['volume']['options'] ),
				'wrapper_class' => '_measurement_pricing_calculator_fields',
				'label'         => __( 'Volume Options', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description'   => __( 'Use a single number to set a fixed value for this field on the frontend, or a comma-separated list of numbers to create a select box for the customer to choose between.  Example: 1/8, .5, 2', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'      => true,
			) );
		echo '</div>';

		// Volume (LxWxH)
		echo '<div id="volume-dimension_measurements" class="measurement_fields">';
			woocommerce_wp_checkbox( array(
				'id'            => '_measurement_volume-dimension_pricing',
				'value'         => $settings['volume-dimension']['pricing']['enabled'],
				'class'         => 'checkbox _measurement_pricing',
				'label'         => __( 'Show Product Price Per Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description'   => __( 'Check this box to display product pricing per unit on the frontend', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
			) );
			echo '<div id="_measurement_volume-dimension_pricing_fields" class="_measurement_pricing_fields" style="display:none;">';
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_volume-dimension_pricing_label',
					'value'       => $settings['volume-dimension']['pricing']['label'],
					'label'       => __( 'Pricing Label', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description' => __( 'Label to display next to the product price (defaults to pricing unit)', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_volume-dimension_pricing_unit',
					'value'       => $settings['volume-dimension']['pricing']['unit'],
					'class'       => '_measurement_pricing_unit',
					'label'       => __( 'Pricing Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'options'     => $measurement_units['volume'],
					'description' => __( 'Unit to define pricing in', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_volume-dimension_pricing_calculator_enabled',
					'class'         => 'checkbox _measurement_pricing_calculator_enabled',
					'value'         => $settings['volume-dimension']['pricing']['calculator']['enabled'],
					'label'         => __( 'Calculated Price', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description'   => __( 'Check this box to define product pricing per unit and allow customers to provide custom measurements', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_volume-dimension_pricing_weight_enabled',
					'value'         => $settings['volume-dimension']['pricing']['weight']['enabled'],
					'class'         => 'checkbox _measurement_pricing_weight_enabled',
					'wrapper_class' => $pricing_weight_wrapper_class . ' _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Weight', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description'   => __( 'Check this box to define the product weight per unit and calculate the item weight based on the product volume', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_volume-dimension_pricing_inventory_enabled',
					'value'         => $settings['volume-dimension']['pricing']['inventory']['enabled'],
					'class'         => 'checkbox _measurement_pricing_inventory_enabled',
					'wrapper_class' => 'stock_fields _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Inventory', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description'   => __( 'Check this box to define inventory per unit and calculate inventory based on the product volume', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				) );
			echo '</div>';
			echo '<hr/>';
			woocommerce_wp_text_input( array(
				'id'          => '_measurement_volume_length_label',
				'value'       => $settings['volume-dimension']['length']['label'],
				'label'       => __( 'Length Label', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description' => __( 'Length input field label to display on the frontend', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_select( array(
				'id'          => '_measurement_volume_length_unit',
				'value'       => $settings['volume-dimension']['length']['unit'],
				'label'       => __( 'Length Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'options'     => $measurement_units['dimension'],
				'description' => __( 'The frontend length input field unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_text_input( array(
				'id'            => '_measurement_volume_length_options',
				'value'         => implode( ', ', $settings['volume-dimension']['length']['options'] ),
				'wrapper_class' => '_measurement_pricing_calculator_fields',
				'label'         => __( 'Length Options', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description'   => __( 'Use a single number to set a fixed value for this field on the frontend, or a comma-separated list of numbers to create a select box for the customer to choose between.  Example: 1/8, .5, 2', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'      => true,
			) );
			echo '<hr/>';

			woocommerce_wp_text_input( array(
				'id'          => '_measurement_volume_width_label',
				'value'       => $settings['volume-dimension']['width']['label'],
				'label'       => __( 'Width Label', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description' => __( 'Width input field label to display on the frontend', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_select( array(
				'id'          => '_measurement_volume_width_unit',
				'value'       => $settings['volume-dimension']['width']['unit'],
				'label'       => __( 'Width Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'options'     => $measurement_units['dimension'],
				'description' => __( 'The frontend width input field unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_text_input( array(
				'id'            => '_measurement_volume_width_options',
				'value'         => implode( ', ', $settings['volume-dimension']['width']['options'] ),
				'wrapper_class' => '_measurement_pricing_calculator_fields',
				'label'         => __( 'Width Options', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description'   => __( 'Use a single number to set a fixed value for this field on the frontend, or a comma-separated list of numbers to create a select box for the customer to choose between.  Example: 1/8, .5, 2', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'      => true,
			) );
			echo '<hr/>';

			woocommerce_wp_text_input( array(
				'id'          => '_measurement_volume_height_label',
				'value'       => $settings['volume-dimension']['height']['label'],
				'label'       => __( 'Height Label', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description' => __( 'Height input field label to display on the frontend', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_select( array(
				'id'          => '_measurement_volume_height_unit',
				'value'       => $settings['volume-dimension']['height']['unit'],
				'label'       => __( 'Height Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'options'     => $measurement_units['dimension'],
				'description' => __( 'The frontend height input field unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_text_input( array(
				'id'            => '_measurement_volume_height_options',
				'value'         => implode( ', ', $settings['volume-dimension']['height']['options'] ),
				'wrapper_class' => '_measurement_pricing_calculator_fields',
				'label'         => __( 'Height Options', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description'   => __( 'Use a single number to set a fixed value for this field on the frontend, or a comma-separated list of numbers to create a select box for the customer to choose between.  Example: 1/8, .5, 2', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'      => true,
			) );
		echo '</div>';

		// Volume (AxH)
		echo '<div id="volume-area_measurements" class="measurement_fields">';
			woocommerce_wp_checkbox( array(
				'id'            => '_measurement_volume-area_pricing',
				'value'         => $settings['volume-area']['pricing']['enabled'],
				'class'         => 'checkbox _measurement_pricing',
				'label'         => __( 'Show Product Price Per Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description'   => __( 'Check this box to display product pricing per unit on the frontend', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
			) );
			echo '<div id="_measurement_volume-area_pricing_fields" class="_measurement_pricing_fields" style="display:none;">';
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_volume-area_pricing_label',
					'value'       => $settings['volume-area']['pricing']['label'],
					'label'       => __( 'Pricing Label', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description' => __( 'Label to display next to the product price (defaults to pricing unit)', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_volume-area_pricing_unit',
					'value'       => $settings['volume-area']['pricing']['unit'],
					'class'       => '_measurement_pricing_unit',
					'label'       => __( 'Pricing Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'options'     => $measurement_units['volume'],
					'description' => __( 'Unit to define pricing in', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_volume-area_pricing_calculator_enabled',
					'class'         => 'checkbox _measurement_pricing_calculator_enabled',
					'value'         => $settings['volume-area']['pricing']['calculator']['enabled'],
					'label'         => __( 'Calculated Price', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description'   => __( 'Check this box to define product pricing per unit and allow customers to provide custom measurements', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_volume-area_pricing_weight_enabled',
					'value'         => $settings['volume-area']['pricing']['weight']['enabled'],
					'class'         => 'checkbox _measurement_pricing_weight_enabled',
					'wrapper_class' => $pricing_weight_wrapper_class . ' _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Weight', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description'   => __( 'Check this box to define the product weight per unit and calculate the item weight based on the product volume', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_volume-area_pricing_inventory_enabled',
					'value'         => $settings['volume-area']['pricing']['inventory']['enabled'],
					'class'         => 'checkbox _measurement_pricing_inventory_enabled',
					'wrapper_class' => 'stock_fields _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Inventory', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description'   => __( 'Check this box to define inventory per unit and calculate inventory based on the product volume', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				) );
			echo '</div>';
			echo '<hr/>';
			woocommerce_wp_text_input( array(
				'id'          => '_measurement_volume_area_label',
				'value'       => $settings['volume-area']['area']['label'],
				'label'       => __( 'Area Label', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description' => __( 'Area input field label to display on the frontend', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_select( array(
				'id'          => '_measurement_volume_area_unit',
				'value'       => $settings['volume-area']['area']['unit'],
				'label'       => __( 'Area Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'options'     => $measurement_units['area'],
				'description' => __( 'The frontend area input field unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_text_input( array(
				'id'            => '_measurement_volume_area_options',
				'value'         => implode( ', ', $settings['volume-area']['area']['options'] ),
				'wrapper_class' => '_measurement_pricing_calculator_fields',
				'label'         => __( 'Area Options', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description'   => __( 'Use a single number to set a fixed value for this field on the frontend, or a comma-separated list of numbers to create a select box for the customer to choose between.  Example: 1/8, .5, 2', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'      => true,
			) );
			echo '<hr/>';

			woocommerce_wp_text_input( array(
				'id'          => '_measurement_volume_area_height_label',
				'value'       => $settings['volume-area']['height']['label'],
				'label'       => __( 'Height Label', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description' => __( 'Height input field label to display on the frontend', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_select( array(
				'id'          => '_measurement_volume_area_height_unit',
				'value'       => $settings['volume-area']['height']['unit'],
				'label'       => __( 'Height Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'options'     => $measurement_units['dimension'],
				'description' => __( 'The frontend height input field unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_text_input( array(
				'id'            => '_measurement_volume_area_height_options',
				'value'         => implode( ', ', $settings['volume-area']['height']['options'] ),
				'wrapper_class' => '_measurement_pricing_calculator_fields',
				'label'         => __( 'Height Options', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description'   => __( 'Use a single number to set a fixed value for this field on the frontend, or a comma-separated list of numbers to create a select box for the customer to choose between.  Example: 1/8, .5, 2', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'      => true,
			) );
		echo '</div>';

		// Weight
		echo '<div id="weight_measurements" class="measurement_fields">';
			woocommerce_wp_checkbox( array(
				'id'            => '_measurement_weight_pricing',
				'value'         => $settings['weight']['pricing']['enabled'],
				'class'         => 'checkbox _measurement_pricing',
				'label'         => __( 'Show Product Price Per Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description'   => __( 'Check this box to display product pricing per unit on the frontend', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
			) );
			echo '<div id="_measurement_weight_pricing_fields" class="_measurement_pricing_fields" style="display:none;">';
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_weight_pricing_label',
					'value'       => $settings['weight']['pricing']['label'],
					'label'       => __( 'Pricing Label', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description' => __( 'Label to display next to the product price (defaults to pricing unit)', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_weight_pricing_unit',
					'value'       => $settings['weight']['pricing']['unit'],
					'class'       => '_measurement_pricing_unit',
					'label'       => __( 'Pricing Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'options'     => $measurement_units['weight'],
					'description' => __( 'Unit to define pricing in', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_weight_pricing_calculator_enabled',
					'class'         => 'checkbox _measurement_pricing_calculator_enabled',
					'value'         => $settings['weight']['pricing']['calculator']['enabled'],
					'label'         => __( 'Calculated Price', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description'   => __( 'Check this box to define product pricing per unit and allow customers to provide custom measurements', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_weight_pricing_weight_enabled',
					'value'         => $settings['weight']['pricing']['weight']['enabled'],
					'class'         => 'checkbox _measurement_pricing_weight_enabled',
					'wrapper_class' => $pricing_weight_wrapper_class . ' _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Weight', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description'   => __( 'Check this box to use the customer-configured product weight as the item weight', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_weight_pricing_inventory_enabled',
					'value'         => $settings['weight']['pricing']['inventory']['enabled'],
					'class'         => 'checkbox _measurement_pricing_inventory_enabled',
					'wrapper_class' => 'stock_fields _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Inventory', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description'   => __( 'Check this box to define inventory per unit and calculate inventory based on the product weight', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				) );
			echo '</div>';
			echo '<hr/>';
			woocommerce_wp_text_input( array(
				'id'          => '_measurement_weight_label',
				'value'       => $settings['weight']['weight']['label'],
				'label'       => __( 'Weight Label', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description' => __( 'Weight input field label to display on the frontend', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_select( array(
				'id'          => '_measurement_weight_unit',
				'value'       => $settings['weight']['weight']['unit'],
				'label'       => __( 'Weight Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'options'     => $measurement_units['weight'],
				'description' => __( 'The frontend weight input field unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_checkbox( array(
				'id'          => '_measurement_weight_editable',
				'value'       => $settings['weight']['weight']['editable'],
				'label'       => __( 'Editable', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'class'       => 'checkbox _measurement_editable',
				'description' => __( 'Check this box to allow the needed measurement to be entered by the customer', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
			) );
			woocommerce_wp_text_input( array(
				'id'            => '_measurement_weight_options',
				'value'         => implode( ', ', $settings['weight']['weight']['options'] ),
				'wrapper_class' => '_measurement_pricing_calculator_fields',
				'label'         => __( 'Weight Options', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description'   => __( 'Use a single number to set a fixed value for this field on the frontend, or a comma-separated list of numbers to create a select box for the customer to choose between.  Example: 1/8, .5, 2', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'      => true,
			) );
		echo '</div>';


		// wall dimension is just the area-dimension calculator with different labels
		echo '<div id="wall-dimension_measurements" class="measurement_fields">';
			woocommerce_wp_checkbox( array(
				'id'            => '_measurement_wall-dimension_pricing',
				'value'         => $settings['wall-dimension']['pricing']['enabled'],
				'class'         => 'checkbox _measurement_pricing',
				'label'         => __( 'Show Product Price Per Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description'   => __( 'Check this box to display product pricing per unit on the frontend', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
			) );
			echo '<div id="_measurement_wall-dimension_pricing_fields" class="_measurement_pricing_fields" style="display:none;">';
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_wall-dimension_pricing_label',
					'value'       => $settings['wall-dimension']['pricing']['label'],
					'label'       => __( 'Pricing Label', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description' => __( 'Label to display next to the product price (defaults to pricing unit)', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_wall-dimension_pricing_unit',
					'value'       => $settings['wall-dimension']['pricing']['unit'],
					'class'       => '_measurement_pricing_unit',
					'label'       => __( 'Pricing Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'options'     => $measurement_units['area'],
					'description' => __( 'Unit to define pricing in', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_wall-dimension_pricing_calculator_enabled',
					'class'         => 'checkbox _measurement_pricing_calculator_enabled',
					'value'         => $settings['wall-dimension']['pricing']['calculator']['enabled'],
					'label'         => __( 'Calculated Price', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description'   => __( 'Check this box to define product pricing per unit and allow customers to provide custom measurements', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_wall-dimension_pricing_weight_enabled',
					'value'         => $settings['wall-dimension']['pricing']['weight']['enabled'],
					'class'         => 'checkbox _measurement_pricing_weight_enabled',
					'wrapper_class' => $pricing_weight_wrapper_class . ' _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Weight', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description'   => __( 'Check this box to define the product weight per unit and calculate the item weight based on the product area', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_wall-dimension_pricing_inventory_enabled',
					'value'         => $settings['wall-dimension']['pricing']['inventory']['enabled'],
					'class'         => 'checkbox _measurement_pricing_inventory_enabled',
					'wrapper_class' => 'stock_fields _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Inventory', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
					'description'   => __( 'Check this box to define inventory per unit and calculate inventory based on the product area', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				) );
			echo '</div>';
			echo '<hr/>';
			woocommerce_wp_text_input( array(
				'id'          => '_measurement_wall_length_label',
				'value'       => $settings['wall-dimension']['length']['label'],
				'label'       => __( 'Length Label', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description' => __( 'Wall length input field label to display on the frontend', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_select( array(
				'id'          => '_measurement_wall_length_unit',
				'value'       => $settings['wall-dimension']['length']['unit'],
				'label'       => __( 'Length Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'options'     => $measurement_units['dimension'],
				'description' => __( 'The frontend wall length input field unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_text_input( array(
				'id'            => '_measurement_wall_length_options',
				'value'         => implode( ', ', $settings['wall-dimension']['length']['options'] ),
				'wrapper_class' => '_measurement_pricing_calculator_fields',
				'label'         => __( 'Length Options', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description'   => __( 'Use a single number to set a fixed value for this field on the frontend, or a comma-separated list of numbers to create a select box for the customer to choose between.  Example: 1/8, .5, 2', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'      => true,
			) );
			echo '<hr/>';

			woocommerce_wp_text_input( array(
				'id'          => '_measurement_wall_width_label',
				'value'       => $settings['wall-dimension']['width']['label'],
				'label'       => __( 'Height Label', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description' => __( 'Room wall height input field label to display on the frontend', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_select( array(
				'id'          => '_measurement_wall_width_unit',
				'value'       => $settings['wall-dimension']['width']['unit'],
				'label'       => __( 'Height Unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'options'     => $measurement_units['dimension'],
				'description' => __( 'The frontend room wall height input field unit', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_text_input( array(
				'id'            => '_measurement_wall_width_options',
				'value'         => implode( ', ', $settings['wall-dimension']['width']['options'] ),
				'wrapper_class' => '_measurement_pricing_calculator_fields',
				'label'         => __( 'Height Options', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'description'   => __( 'Use a single number to set a fixed value for this field on the frontend, or a comma-separated list of numbers to create a select box for the customer to choose between.  Example: 1/8, .5, 2', WC_Measurement_Price_Calculator::TEXT_DOMAIN ),
				'desc_tip'      => true,
			) );
		echo '</div>';
		echo '</div>'; // close the subpanel
		echo '<div id="calculator-pricing-table" class="calculator-subpanel">';
		require_once( $wc_measurement_price_calculator->get_plugin_path() . '/admin/post-types/writepanels/writepanel-product_data-pricing_table.php' );
		echo '</div>';
		?>

	</div>
	<?php
}


// Hooked after the WC core handler
add_action( 'woocommerce_process_product_meta', 'wc_measurement_price_calculator_process_product_meta_measurement', 10, 2 );

/**
 * Save the measurement price calculator custom fields
 *
 * @param int $post_id post identifier
 * @param array $post the post object
 */
function wc_measurement_price_calculator_process_product_meta_measurement( $post_id, $post ) {

	// get product type
	$is_virtual = isset( $_POST['_virtual'] ) ? 'yes' : 'no';
	$product_type = sanitize_title( stripslashes( $_POST['product-type'] ) );

	// Dimensions: virtual and grouped products not allowed
	if ( 'no' == $is_virtual && 'grouped' != $product_type ) {

		$settings = array();

		// the type of calculator enabled, one of 'dimension', 'area', etc or empty for disabled
		$settings['calculator_type'] = $_POST['_measurement_price_calculator'];

		$settings['dimension']['pricing'] = array(
			'enabled'  => isset( $_POST['_measurement_dimension_pricing'] ) && $_POST['_measurement_dimension_pricing'] ? 'yes' : 'no',
			'label'    => $_POST['_measurement_dimension_pricing_label'],
			'unit'     => $_POST['_measurement_dimension_pricing_unit'],
			'calculator' => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_dimension_pricing_calculator_enabled' ),
			),
			'inventory' => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_dimension_pricing_inventory_enabled' ),
			),
			'weight' => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_dimension_pricing_weight_enabled' ),
			),
		);
		$settings['dimension']['length'] = array(
			'enabled'  => isset( $_POST['_measurement_dimension'] ) && 'length' == $_POST['_measurement_dimension'] ? 'yes' : 'no',
			'label'    => $_POST['_measurement_dimension_length_label'],
			'unit'     => $_POST['_measurement_dimension_length_unit'],
			'editable' => isset( $_POST['_measurement_dimension_length_editable'] ) && $_POST['_measurement_dimension_length_editable'] ? 'yes' : 'no',
			'options'  => explode( ',', str_replace( ' ', '', $_POST['_measurement_dimension_length_options'] ) ),
		);
		$settings['dimension']['width'] = array(
			'enabled'  => isset( $_POST['_measurement_dimension'] ) && 'width' == $_POST['_measurement_dimension'] ? 'yes' : 'no',
			'label'    => $_POST['_measurement_dimension_width_label'],
			'unit'     => $_POST['_measurement_dimension_width_unit'],
			'editable' => isset( $_POST['_measurement_dimension_width_editable'] ) && $_POST['_measurement_dimension_width_editable'] ? 'yes' : 'no',
			'options'  => explode( ',', str_replace( ' ', '', $_POST['_measurement_dimension_width_options'] ) ),
		);
		$settings['dimension']['height'] = array(
			'enabled'  => isset( $_POST['_measurement_dimension'] ) && 'height' == $_POST['_measurement_dimension'] ? 'yes' : 'no',
			'label'    => $_POST['_measurement_dimension_height_label'],
			'unit'     => $_POST['_measurement_dimension_height_unit'],
			'editable' => isset( $_POST['_measurement_dimension_height_editable'] ) && $_POST['_measurement_dimension_height_editable'] ? 'yes' : 'no',
			'options'  => explode( ',', str_replace( ' ', '', $_POST['_measurement_dimension_height_options'] ) ),
		);

		// simple area calculator
		$settings['area']['pricing'] = array(
			'enabled'  => isset( $_POST['_measurement_area_pricing'] ) && $_POST['_measurement_area_pricing'] ? 'yes' : 'no',
			'label'    => $_POST['_measurement_area_pricing_label'],
			'unit'     => $_POST['_measurement_area_pricing_unit'],
			'calculator' => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_area_pricing_calculator_enabled' ),
			),
			'inventory' => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_area_pricing_inventory_enabled' ),
			),
			'weight' => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_area_pricing_weight_enabled' ),
			),
		);
		$settings['area']['area'] = array(
			'label'    => $_POST['_measurement_area_label'],
			'unit'     => $_POST['_measurement_area_unit'],
			'editable' => isset( $_POST['_measurement_area_editable'] ) && $_POST['_measurement_area_editable'] ? 'yes' : 'no',
			'options'  => explode( ',', str_replace( ' ', '', $_POST['_measurement_area_options'] ) ),
		);

		// area (LxW) calculator
		$settings['area-dimension']['pricing'] = array(
			'enabled'  => isset( $_POST['_measurement_area-dimension_pricing'] ) && $_POST['_measurement_area-dimension_pricing'] ? 'yes' : 'no',
			'label'    => $_POST['_measurement_area-dimension_pricing_label'],
			'unit'     => $_POST['_measurement_area-dimension_pricing_unit'],
			'calculator' => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_area-dimension_pricing_calculator_enabled' ),
			),
			'inventory' => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_area-dimension_pricing_inventory_enabled' ),
			),
			'weight' => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_area-dimension_pricing_weight_enabled' ),
			),
		);
		$settings['area-dimension']['length'] = array(
			'label'    => $_POST['_measurement_area_length_label'],
			'unit'     => $_POST['_measurement_area_length_unit'],
			'editable' => 'yes',
			'options'  => explode( ',', str_replace( ' ', '', $_POST['_measurement_area_length_options'] ) ),
		);
		$settings['area-dimension']['width'] = array(
			'label'    => $_POST['_measurement_area_width_label'],
			'unit'     => $_POST['_measurement_area_width_unit'],
			'editable' => 'yes',
			'options'  => explode( ',', str_replace( ' ', '', $_POST['_measurement_area_width_options'] ) ),
		);

		// Perimeter (2L + 2W) calculator
		$settings['area-linear']['pricing'] = array(
			'enabled'  => isset( $_POST['_measurement_area-linear_pricing'] ) && $_POST['_measurement_area-linear_pricing'] ? 'yes' : 'no',
			'label'    => $_POST['_measurement_area-linear_pricing_label'],
			'unit'     => $_POST['_measurement_area-linear_pricing_unit'],
			'calculator' => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_area-linear_pricing_calculator_enabled' ),
			),
			'inventory' => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_area-linear_pricing_inventory_enabled' ),
			),
			'weight' => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_area-linear_pricing_weight_enabled' ),
			),
		);
		$settings['area-linear']['length'] = array(
			'label'    => $_POST['_measurement_area-linear_length_label'],
			'unit'     => $_POST['_measurement_area-linear_length_unit'],
			'editable' => 'yes',
			'options'  => explode( ',', str_replace( ' ', '', $_POST['_measurement_area-linear_length_options'] ) ),
		);
		$settings['area-linear']['width'] = array(
			'label'    => $_POST['_measurement_area-linear_width_label'],
			'unit'     => $_POST['_measurement_area-linear_width_unit'],
			'editable' => 'yes',
			'options'  => explode( ',', str_replace( ' ', '', $_POST['_measurement_area-linear_width_options'] ) ),
		);

		// Surface Area 2(LW + WH + LH) calculator
		$settings['area-surface']['pricing'] = array(
			'enabled'  => isset( $_POST['_measurement_area-surface_pricing'] ) && $_POST['_measurement_area-surface_pricing'] ? 'yes' : 'no',
			'label'    => $_POST['_measurement_area-surface_pricing_label'],
			'unit'     => $_POST['_measurement_area-surface_pricing_unit'],
			'calculator' => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_area-surface_pricing_calculator_enabled' ),
			),
			'inventory' => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_area-surface_pricing_inventory_enabled' ),
			),
			'weight' => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_area-surface_pricing_weight_enabled' ),
			),
		);
		$settings['area-surface']['length'] = array(
			'label'    => $_POST['_measurement_area-surface_length_label'],
			'unit'     => $_POST['_measurement_area-surface_length_unit'],
			'editable' => 'yes',
			'options'  => explode( ',', str_replace( ' ', '', $_POST['_measurement_area-surface_length_options'] ) ),
		);
		$settings['area-surface']['width'] = array(
			'label'    => $_POST['_measurement_area-surface_width_label'],
			'unit'     => $_POST['_measurement_area-surface_width_unit'],
			'editable' => 'yes',
			'options'  => explode( ',', str_replace( ' ', '', $_POST['_measurement_area-surface_width_options'] ) ),
		);
		$settings['area-surface']['height'] = array(
			'label'    => $_POST['_measurement_area-surface_height_label'],
			'unit'     => $_POST['_measurement_area-surface_height_unit'],
			'editable' => 'yes',
			'options'  => explode( ',', str_replace( ' ', '', $_POST['_measurement_area-surface_height_options'] ) ),
		);

		// Simple volume calculator
		$settings['volume']['pricing'] = array(
			'enabled'  => isset( $_POST['_measurement_volume_pricing'] ) && $_POST['_measurement_volume_pricing'] ? 'yes' : 'no',
			'label'    => $_POST['_measurement_volume_pricing_label'],
			'unit'     => $_POST['_measurement_volume_pricing_unit'],
			'calculator' => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_volume_pricing_calculator_enabled' ),
			),
			'inventory' => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_volume_pricing_inventory_enabled' ),
			),
			'weight' => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_volume_pricing_weight_enabled' ),
			),
		);
		$settings['volume']['volume'] = array(
			'label' => $_POST['_measurement_volume_label'],
			'unit'  => $_POST['_measurement_volume_unit'],
			'editable' => isset( $_POST['_measurement_volume_editable'] ) && $_POST['_measurement_volume_editable'] ? 'yes' : 'no',
			'options'  => explode( ',', str_replace( ' ', '', $_POST['_measurement_volume_options'] ) ),
		);

		// volume (L x W x H) calculator
		$settings['volume-dimension']['pricing'] = array(
			'enabled'  => isset( $_POST['_measurement_volume-dimension_pricing'] ) && $_POST['_measurement_volume-dimension_pricing'] ? 'yes' : 'no',
			'label'    => $_POST['_measurement_volume-dimension_pricing_label'],
			'unit'     => $_POST['_measurement_volume-dimension_pricing_unit'],
			'calculator' => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_volume-dimension_pricing_calculator_enabled' ),
			),
			'inventory' => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_volume-dimension_pricing_inventory_enabled' ),
			),
			'weight' => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_volume-dimension_pricing_weight_enabled' ),
			),
		);
		$settings['volume-dimension']['length'] = array(
			'label'    => $_POST['_measurement_volume_length_label'],
			'unit'     => $_POST['_measurement_volume_length_unit'],
			'editable' => 'yes',
			'options'  => explode( ',', str_replace( ' ', '', $_POST['_measurement_volume_length_options'] ) ),
		);
		$settings['volume-dimension']['width'] = array(
			'label'    => $_POST['_measurement_volume_width_label'],
			'unit'     => $_POST['_measurement_volume_width_unit'],
			'editable' => 'yes',
			'options'  => explode( ',', str_replace( ' ', '', $_POST['_measurement_volume_width_options'] ) ),
		);
		$settings['volume-dimension']['height'] = array(
			'label'    => $_POST['_measurement_volume_height_label'],
			'unit'     => $_POST['_measurement_volume_height_unit'],
			'editable' => 'yes',
			'options'  => explode( ',', str_replace( ' ', '', $_POST['_measurement_volume_height_options'] ) ),
		);

		// volume (A x H) calculator
		$settings['volume-area']['pricing'] = array(
			'enabled'  => isset( $_POST['_measurement_volume-area_pricing'] ) && $_POST['_measurement_volume-area_pricing'] ? 'yes' : 'no',
			'label'    => $_POST['_measurement_volume-area_pricing_label'],
			'unit'     => $_POST['_measurement_volume-area_pricing_unit'],
			'calculator' => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_volume-area_pricing_calculator_enabled' ),
			),
			'inventory' => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_volume-area_pricing_inventory_enabled' ),
			),
			'weight' => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_volume-area_pricing_weight_enabled' ),
			),
		);
		$settings['volume-area']['area'] = array(
			'label'    => $_POST['_measurement_volume_area_label'],
			'unit'     => $_POST['_measurement_volume_area_unit'],
			'editable' => 'yes',
			'options'  => explode( ',', str_replace( ' ', '', $_POST['_measurement_volume_area_options'] ) ),
		);
		$settings['volume-area']['height'] = array(
			'label'    => $_POST['_measurement_volume_area_height_label'],
			'unit'     => $_POST['_measurement_volume_area_height_unit'],
			'editable' => 'yes',
			'options'  => explode( ',', str_replace( ' ', '', $_POST['_measurement_volume_area_height_options'] ) ),
		);

		// simple weight calculator
		$settings['weight']['pricing'] = array(
			'enabled'  => isset( $_POST['_measurement_weight_pricing'] ) && $_POST['_measurement_weight_pricing'] ? 'yes' : 'no',
			'label'    => $_POST['_measurement_weight_pricing_label'],
			'unit'     => $_POST['_measurement_weight_pricing_unit'],
			'calculator' => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_weight_pricing_calculator_enabled' ),
			),
			'inventory' => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_weight_pricing_inventory_enabled' ),
			),
			'weight' => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_weight_pricing_weight_enabled' ),
			),
		);
		$settings['weight']['weight'] = array(
			'label'    => $_POST['_measurement_weight_label'],
			'unit'     => $_POST['_measurement_weight_unit'],
			'editable' => isset( $_POST['_measurement_weight_editable'] ) && $_POST['_measurement_weight_editable'] ? 'yes' : 'no',
			'options'  => explode( ',', str_replace( ' ', '', $_POST['_measurement_weight_options'] ) ),
		);

		// the wall calculator is just a bit of syntactic sugar on top of the Area (LxW) calculator
		$settings['wall-dimension']['pricing'] = array(
			'enabled'  => isset( $_POST['_measurement_wall-dimension_pricing'] ) && $_POST['_measurement_wall-dimension_pricing'] ? 'yes' : 'no',
			'label'    => $_POST['_measurement_wall-dimension_pricing_label'],
			'unit'     => $_POST['_measurement_wall-dimension_pricing_unit'],
			'calculator' => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_wall-dimension_pricing_calculator_enabled' ),
			),
			'inventory' => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_wall-dimension_pricing_inventory_enabled' ),
			),
			'weight' => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_wall-dimension_pricing_weight_enabled' ),
			),
		);
		$settings['wall-dimension']['length'] = array(
			'label'    => $_POST['_measurement_wall_length_label'],
			'unit'     => $_POST['_measurement_wall_length_unit'],
			'editable' => 'yes',
			'options'  => explode( ',', str_replace( ' ', '', $_POST['_measurement_wall_length_options'] ) ),
		);
		$settings['wall-dimension']['width'] = array(
			'label'    => $_POST['_measurement_wall_width_label'],
			'unit'     => $_POST['_measurement_wall_width_unit'],
			'editable' => 'yes',
			'options'  => explode( ',', str_replace( ' ', '', $_POST['_measurement_wall_width_options'] ) ),
		);

		// save settings
		update_post_meta( $post_id, '_wc_price_calculator', $settings );

		// persist any pricing rules
		$rules = array();

		// persist any rules assigned to this product
		if ( ! empty( $_POST['_wc_measurement_pricing_rule_range_start'] ) && is_array( $_POST['_wc_measurement_pricing_rule_range_start'] ) ) {

			foreach ( $_POST['_wc_measurement_pricing_rule_range_start'] as $index => $pricing_rule_range_start ) {

				$pricing_rule_range_end     = $_POST['_wc_measurement_pricing_rule_range_end'][ $index ];
				$pricing_rule_regular_price = $_POST['_wc_measurement_pricing_rule_regular_price'][ $index ];
				$pricing_rule_sale_price    = $_POST['_wc_measurement_pricing_rule_sale_price'][ $index ];
				$pricing_rule_price         = '' !== $pricing_rule_sale_price ? $pricing_rule_sale_price : $pricing_rule_regular_price;

				if ( $pricing_rule_range_start || $pricing_rule_range_end || $pricing_rule_price ) {
					$rules[] = array(
						'range_start'   => $pricing_rule_range_start,
						'range_end'     => $pricing_rule_range_end,
						'price'         => $pricing_rule_price,
						'regular_price' => $pricing_rule_regular_price,
						'sale_price'    => $pricing_rule_sale_price,
					);
				}
			}
		}

		// save settings
		update_post_meta( $post_id, '_wc_price_calculator_pricing_rules', $rules );
	}
}


/**
 * Helper function to safely get a checkbox post value
 *
 * @access private
 * @since 3.0
 * @param string $name the checkbox name
 * @return string "yes" or "no" depending on whether the checkbox named $name
 *         was set
 */
function wc_measurement_price_calculator_get_checkbox_post( $name ) {
	return isset( $_POST[ $name ] ) && $_POST[ $name ] ? 'yes' : 'no';
}
