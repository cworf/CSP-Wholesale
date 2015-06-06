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
 * @package   WC-Measurement-Price-Calculator/Classes
 * @author    SkyVerge
 * @copyright Copyright (c) 2012-2014, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Measurement Price Calculator Product Page View Class
 *
 * @since 3.0
 */
class WC_Price_Calculator_Product_Page {


	/**
	 * Construct and initialize the class
	 *
	 * @since 3.0
	 */
	public function __construct() {

		// make all product variations visible for pricing calculator with pricing table products
		add_filter( 'woocommerce_product_is_visible', array( $this, 'variable_product_is_visible' ), 1, 2 );

		// make all pricing calculator with pricing table products purchasable
		add_filter( 'woocommerce_is_purchasable',       array( $this, 'product_is_purchasable' ), 1, 2 );
		add_filter( 'woocommerce_variation_is_visible', array( $this, 'variation_is_visible' ), 10, 3 );

		// display the pricing calculator price per unit on the frontend (catalog and product page)
		$this->add_price_html_filters();

		// add the price and product measurements into the variation JSON object
		add_filter( 'woocommerce_available_variation', array( $this, 'available_variation' ), 10, 3 );

		// display the calculator styling, html and javascript on the frontend product detail page
		add_action( 'wp_print_styles',    array( $this, 'render_embedded_styles' ), 1 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts' ) );

		add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'render_price_calculator' ), 5 );

	}


	/**
	 * Add all price_html product filters
	 *
	 * @since 3.0
	 */
	private function add_price_html_filters() {
		add_filter( 'woocommerce_sale_price_html',           array( $this, 'price_per_unit_html' ), 10, 2 );
		add_filter( 'woocommerce_price_html',                array( $this, 'price_per_unit_html' ), 10, 2 );
		add_filter( 'woocommerce_empty_price_html',          array( $this, 'price_per_unit_html' ), 10, 2 );
		add_filter( 'woocommerce_variable_sale_price_html',  array( $this, 'price_per_unit_html' ), 10, 2 );
		add_filter( 'woocommerce_variable_price_html',       array( $this, 'price_per_unit_html' ), 10, 2 );
		add_filter( 'woocommerce_variable_empty_price_html', array( $this, 'price_per_unit_html' ), 10, 2 );
		add_filter( 'woocommerce_variation_sale_price_html', array( $this, 'price_per_unit_html' ), 10, 2 );
		add_filter( 'woocommerce_variation_price_html',      array( $this, 'price_per_unit_html' ), 10, 2 );

		remove_filter( 'woocommerce_get_variation_regular_price', array( $this, 'get_variation_regular_price' ), 10, 4 );
		remove_filter( 'woocommerce_get_variation_sale_price',    array( $this, 'get_variation_sale_price' ), 10, 4 );
		remove_filter( 'woocommerce_get_variation_price',         array( $this, 'get_variation_price' ), 10, 4 );

		// Fix sale flash on pricing rules products

		// WC 2.3+ only
		add_filter( 'woocommerce_product_is_on_sale', array( $this, 'is_on_sale' ), 10, 2 );

		// WC <= 2.2
		add_filter( 'woocommerce_get_price',         array( $this, 'get_price' ), 10, 2 );
		add_filter( 'woocommerce_get_regular_price', array( $this, 'get_regular_price' ), 10, 2 );
		add_filter( 'woocommerce_get_sale_price',    array( $this, 'get_sale_price' ), 10, 2 );
	}


	/**
	 * Remove all price_html product filters
	 *
	 * @since 3.0
	 */
	private function remove_price_html_filters() {
		remove_filter( 'woocommerce_sale_price_html',           array( $this, 'price_per_unit_html' ), 10, 2 );
		remove_filter( 'woocommerce_price_html',                array( $this, 'price_per_unit_html' ), 10, 2 );
		remove_filter( 'woocommerce_empty_price_html',          array( $this, 'price_per_unit_html' ), 10, 2 );
		remove_filter( 'woocommerce_variable_sale_price_html',  array( $this, 'price_per_unit_html' ), 10, 2 );
		remove_filter( 'woocommerce_variable_price_html',       array( $this, 'price_per_unit_html' ), 10, 2 );
		remove_filter( 'woocommerce_variable_empty_price_html', array( $this, 'price_per_unit_html' ), 10, 2 );
		remove_filter( 'woocommerce_variation_sale_price_html', array( $this, 'price_per_unit_html' ), 10, 2 );
		remove_filter( 'woocommerce_variation_price_html',      array( $this, 'price_per_unit_html' ), 10, 2 );

		add_filter( 'woocommerce_get_variation_regular_price', array( $this, 'get_variation_regular_price' ), 10, 4 );
		add_filter( 'woocommerce_get_variation_sale_price',    array( $this, 'get_variation_sale_price' ), 10, 4 );
		add_filter( 'woocommerce_get_variation_price',         array( $this, 'get_variation_price' ), 10, 4 );

		// Fix sale flash on pricing rules products

		// WC 2.3+ only
		remove_filter( 'woocommerce_product_is_on_sale', array( $this, 'is_on_sale' ), 10, 2 );

		// WC <= 2.2
		remove_filter( 'woocommerce_get_price',         array( $this, 'get_price' ), 10, 2 );
		remove_filter( 'woocommerce_get_regular_price', array( $this, 'get_regular_price' ), 10, 2 );
		remove_filter( 'woocommerce_get_sale_price',    array( $this, 'get_sale_price' ), 10, 2 );
	}


	/**
	 * Very temporary filter to return the regular price per unit min/max
	 * for a variable measurement price calculator product.
	 *
	 * Ok, so WC 2.1 totally changed up how the variation min/max pricing is
	 * stored in the db and accessed from the model.  To shoehorn 2.1
	 * compatibility in we'll just take advantage of the 2.0.x variation price
	 * sync code to return the price per unit that we've already calculated.
	 * There may be a better way to do this, now that 2.0.x compatibility has
	 * been removed
	 *
	 * @since 3.3.1
	 * @param string $price the variation regular price
	 * @param WC_Product $product the product
	 * @param string $min_or_max - min or max
	 * @param boolean  $display Whether the value is going to be displayed
	 * @return string the variation regular price
	 */
	public function get_variation_regular_price( $price, $product, $min_or_max, $display ) {

		if ( $display ) {
			$variation_regular_price = "{$min_or_max}_variation_regular_price";
			$price = round( $product->$variation_regular_price, 2 );
		}

		return $price;
	}


	/**
	 * Very temporary filter to return the sale price per unit min/max
	 * for a variable measurement price calculator product.
	 *
	 * @since 3.3.1
	 * @param string $price the variation sale price
	 * @param WC_Product $product the product
	 * @param string $min_or_max - min or max
	 * @param boolean  $display Whether the value is going to be displayed
	 * @return string the variation sale price
	 */
	public function get_variation_sale_price( $price, $product, $min_or_max, $display ) {

		if ( $display ) {
			$variation_sale_price = "{$min_or_max}_variation_sale_price";
			$price = round( $product->$variation_regular_price, 2 );
		}

		return $price;
	}


	/**
	 * Very temporary filter to return the price per unit min/max
	 * for a variable measurement price calculator product.
	 *
	 * @since 3.3.1
	 * @param string $price the variation price
	 * @param WC_Product $product the product
	 * @param string $min_or_max - min or max
	 * @param boolean  $display Whether the value is going to be displayed
	 * @return string the variation price
	 */
	public function get_variation_price( $price, $product, $min_or_max, $display ) {

		if ( $display ) {
			$variation_price = "{$min_or_max}_variation_price";
			$price = round( $product->$variation_price, 2 );
		}

		return $price;
	}


	/** Price methods *********************************************************/

	/**
	 * Set product on sale. (WC 2.3+ only)
	 *
	 * Fixes sale flash on pricing rules products
	 *
	 * @since 3.5.2
	 * @param string $is_on_sale the price
	 * @param WC_Product $product the product
	 * @return string the price
	 */
	public function is_on_sale( $is_on_sale, $product ) {

		$settings = new WC_Price_Calculator_Settings( $product );

		if ( $settings->pricing_rules_enabled() ) {
			$is_on_sale = $settings->pricing_rules_is_on_sale();
		}

		return $is_on_sale;
	}

	/**
	 * Filter the price for pricing rules products.
	 *
	 * Fixes sale flash on pricing rules products
	 *
	 * @since 3.5.2
	 * @param string $price the price
	 * @param WC_Product $product the product
	 * @return string the price
	 */
	public function get_price( $price, $product ) {

		$settings = new WC_Price_Calculator_Settings( $product );

		if ( $settings->pricing_rules_enabled() ) {
			$price = $settings->get_pricing_rules_minimum_price();
		}

		return $price;
	}

	/**
	 * Filter the regular price for pricing rules products.
	 *
	 * Fixes sale flash on pricing rules products
	 *
	 * @since 3.5.2
	 * @param string $regular_price the regular price
	 * @param WC_Product $product the product
	 * @return string the regular price
	 */
	public function get_regular_price( $regular_price, $product ) {

		$settings = new WC_Price_Calculator_Settings( $product );

		if ( $settings->pricing_rules_enabled() ) {
			$regular_price = $settings->get_pricing_rules_minimum_regular_price();
		}

		return $regular_price;
	}


	/**
	 * Filter the sale price for pricing rules products.
	 *
	 * Fixes sale flash on pricing rules products
	 *
	 * @since 3.5.2
	 * @param string $sale_price the sale price
	 * @param WC_Product $product the product
	 * @return string the sale price
	 */
	public function get_sale_price( $sale_price, $product ) {

		$settings = new WC_Price_Calculator_Settings( $product );

		if ( $settings->pricing_rules_enabled() ) {
			$sale_price = $settings->get_pricing_rules_minimum_price();
		}

		return $sale_price;
	}


	/** Frontend methods ******************************************************/


	/**
	 * Returns true if $product is purchasable.  We mark pricing table products
	 * as being purchasable, as they wouldn't be otherwise without a price set
	 *
	 * This is one of the few times where we are altering this filter in a
	 * positive manner, and so we try to hook into it first.
	 *
	 * @since 3.0
	 * @param boolean $is_purchasable true if the product is purchasable, false otherwise
	 * @param WC_Product $product the product
	 * @return boolean true if the product is purchasable, false otherwise
	 */
	public function product_is_purchasable( $is_purchasable, $product ) {

		// even if the product isn't purchasable, if it has pricing rules set, then we'll change that
		if ( ! $is_purchasable && WC_Price_Calculator_Product::pricing_calculator_enabled( $product ) ) {
			$settings = new WC_Price_Calculator_Settings( $product );
			if ( $settings->pricing_rules_enabled() ) $is_purchasable = true;
		}

		return $is_purchasable;
	}


	/**
	 * Returns true if the identified variation is visible.  We mark pricing
	 * table products as being visible, as they wouldn't be otherwise without a
	 * price set
	 *
	 * This is one of the few times where we are altering this filter in a
	 * positive manner, and so we try to hook into it first.
	 *
	 * @since 3.3.2
	 * @param boolean $visible whether the variation is visible
	 * @param int $variation_id the variation identifier
	 * @param int $parent_id the parent product identifier
	 * @return boolean true if the variation is visible, false otherwise
	 */
	public function variation_is_visible( $visible, $variation_id, $parent_id ) {
		return $this->variable_product_is_visible( $visible, $parent_id );
	}


	/**
	 * Make product variations visible even if they don't have a price, as long
	 * as they are priced with a pricing table
	 *
	 * This is one of the few times where we are altering this filter in a
	 * positive manner, and so we try to hook into it first.
	 *
	 * @since 3.0
	 * @param boolean $visible whether the product is visible
	 * @param int $product_id the product id
	 * @return boolean true if the product is visible, false otherwise.
	 */
	public function variable_product_is_visible( $visible, $product_id ) {

		$product = SV_WC_Plugin_Compatibility::wc_get_product( $product_id );

		if ( ! $visible && $product && $product->is_type( 'variable' ) && WC_Price_Calculator_Product::pricing_calculator_enabled( $product ) ) {

			$settings = new WC_Price_Calculator_Settings( $product );
			if ( $settings->pricing_rules_enabled() )  $visible = true;
		}

		return $visible;
	}


	/**
	 * Renders the price/sale price in terms of a unit of measurement for display
	 * on the catalog/product pages
	 *
	 * @since 3.0
	 * @param $price string the formatted sale price
	 * @param $product WC_Product the product
	 * @return string the formatted sale price, per unit
	 */
	public function price_per_unit_html( $price_html, $product ) {

		// if this is a product variation, get the parent product which holds the calculator settings
		$_product = $product;
		if ( isset( $product->variation_id ) && $product->variation_id ) { $_product = SV_WC_Plugin_Compatibility::wc_get_product( $product->id ); }

		if ( WC_Price_Calculator_Product::pricing_per_unit_enabled( $_product ) ) {

			$settings = new WC_Price_Calculator_Settings( $product );

			// if this is a quantity calculator, the displayed price per unit will have to be calculated from
			//  the product price and pricing measurement.  alternatively, for a pricing calculator product,
			//  the price set in the admin *is* the price per unit, so we just need to format it by adding the units
			if ( $settings->is_quantity_calculator_enabled() ) {

				$measurement = null;

				// for variable products we must go synchronize price levels to our per unit price
				if ( $product->is_type( 'variable' ) ) {

					// synchronize to the price per unit pricing
					WC_Price_Calculator_Product::variable_product_sync( $product, $settings );

					// get price suffix and remove it from the price html
					$price_suffix = $product->get_price_suffix();
					add_filter( 'woocommerce_get_price_suffix', '__return_empty_string' );

					// remove the price_html filters, get the appropriate price html, then re-add them
					$this->remove_price_html_filters();
					$price_html = $product->get_price_html();
					$this->add_price_html_filters();

					// re-add price suffix
					remove_filter( 'woocommerce_get_price_suffix', '__return_empty_string' );

					// add units
					$price_html .= ' ' . __( $settings->get_pricing_label(), WC_Measurement_Price_Calculator::TEXT_DOMAIN );

					// add price suffix
					$price_html .= $price_suffix;

					// restore the original values
					WC_Price_Calculator_Product::variable_product_unsync( $product, $settings );

				} else {
					// other product types
					$measurement = WC_Price_Calculator_Product::get_product_measurement( $product, $settings );
					$measurement->set_unit( $settings->get_pricing_unit() );

					if ( $measurement && $measurement->get_value() && '' !== $price_html ) {

						// save the original price and remove the filter that we're currently within, to avoid an infinite loop
						$original_price         = $product->price;
						$original_regular_price = $product->regular_price;
						$original_sale_price    = $product->sale_price;

						// calculate the price per unit, then format it
						$product->price         = $product->price         / $measurement->get_value();
						$product->regular_price = $product->regular_price / $measurement->get_value();
						$product->sale_price    = $product->sale_price    / $measurement->get_value();

						$product = apply_filters( 'wc_measurement_price_calculator_quantity_price_per_unit', $product, $measurement );

						// get price suffix and remove it from the price html
						$price_suffix = $product->get_price_suffix();
						add_filter( 'woocommerce_get_price_suffix', '__return_empty_string' );

						// remove the price_html filters, get the appropriate price html, then re-add them
						$this->remove_price_html_filters();
						$price_html = $product->get_price_html();
						$this->add_price_html_filters();

						// re-add price suffix
						remove_filter( 'woocommerce_get_price_suffix', '__return_empty_string' );

						// restore the original product price and price_html filters
						$product->price         = $original_price;
						$product->regular_price = $original_regular_price;
						$product->sale_price    = $original_sale_price;

						// add units
						$price_html .= ' ' . __( $settings->get_pricing_label(), WC_Measurement_Price_Calculator::TEXT_DOMAIN );

						// add price suffix
						$price_html .= $price_suffix;
					}
				}
			} else {
				// pricing calculator

				if ( $settings->pricing_rules_enabled() ) {
					// pricing rules product
					$price_html = WC_Price_Calculator_Product::get_pricing_rules_price_html( $product );
				} elseif ( '' !== $price_html ) {
					// normal pricing calculator non-empty price: add units
					$price_html .= ' ' . __( $settings->get_pricing_label(), WC_Measurement_Price_Calculator::TEXT_DOMAIN );
				}
			}

		}

		return $price_html;
	}


	/**
	 * Add product 'price', measurement value and measurement unit attributes to the variations JSON
	 *
	 * @since 3.0
	 * @param array $variation_data associative array of variation data
	 * @param WC_Product variation parent product
	 * @param WC_Product_Variation variation product
	 * @return array $variation_data
	 */
	public function available_variation( $variation_data, $product, $variation ) {

		// is the calculator enabled for this product?
		if ( ! $product || ! WC_Price_Calculator_Product::calculator_enabled( $product ) ) return $variation_data;

		$variation_data['price'] = $variation->get_price();

		$settings = new WC_Price_Calculator_Settings( $variation );

		// this is the measurement that represents one quantity of the product
		$product_measurement = WC_Price_Calculator_Product::get_product_measurement( $variation, $settings );

		// if we have the required product physical attributes
		if ( $product_measurement && $product_measurement->get_value() ) {
			$variation_data['product_measurement_value'] = $product_measurement->get_value();
			$variation_data['product_measurement_unit']  = $product_measurement->get_unit();
		} else {
			$variation_data['product_measurement_value'] = '';
			$variation_data['product_measurement_unit']  = '';
		}

		return $variation_data;
	}


	/**
	 * Output the price calculator CSS styling inline within the page head.
	 *
	 * @since 3.0
	 */
	public function render_embedded_styles() {
		global $post;

		$product = null;
		if ( is_product() ) $product = SV_WC_Plugin_Compatibility::wc_get_product( $post->ID );

		// is the calculator enabled for this product?
		if ( ! $product || ! WC_Price_Calculator_Product::calculator_enabled( $product ) ) return;

		?>
		<style type="text/css">
			#price_calculator { border-style:none; }
			#price_calculator td { border-style:none; vertical-align:middle; }
			#price_calculator input, #price_calculator span { float:right; }
			#price_calculator input { width:64px;text-align:right; }
			.variable_price_calculator { display:none; }
			#price_calculator .calculate td { text-align:right; }
			#price_calculator .calculate button { margin-right:0; }
		</style>
		<?php
	}


	/**
	 * Register/queue frontend scripts.
	 *
	 * @since 3.0
	 */
	public function enqueue_frontend_scripts() {
		global $post, $wc_measurement_price_calculator;

		$product = null;
		if ( is_product() ) $product = SV_WC_Plugin_Compatibility::wc_get_product( $post->ID );

		// is the calculator enabled for this product?
		if ( ! $product || ! WC_Price_Calculator_Product::calculator_enabled( $product ) ) return;

		$settings = new WC_Price_Calculator_Settings( $product );

		wp_enqueue_script( 'wc-price-calculator', $wc_measurement_price_calculator->get_plugin_url() . '/assets/js/frontend/wc-measurement-price-calculator.min.js' );

		// Variables for JS scripts
		$wc_price_calculator_params = array(
			'woocommerce_currency_symbol'    => get_woocommerce_currency_symbol(),
			'woocommerce_price_num_decimals' => (int) get_option( 'woocommerce_price_num_decimals' ),
			'woocommerce_currency_pos'       => get_option( 'woocommerce_currency_pos' ),
			'woocommerce_price_decimal_sep'  => stripslashes( get_option( 'woocommerce_price_decimal_sep' ) ),
			'woocommerce_price_thousand_sep' => stripslashes( get_option( 'woocommerce_price_thousand_sep' ) ),
			'woocommerce_price_trim_zeros'   => get_option( 'woocommerce_price_trim_zeros' ),
			'unit_normalize_table'           => WC_Price_Calculator_Measurement::get_normalize_table(),
			'unit_conversion_table'          => WC_Price_Calculator_Measurement::get_conversion_table(),
			'measurement_precision'          => apply_filters( 'wc_measurement_price_calculator_measurement_precision', 3 ),
			'minimum_price'                  => WC_Price_Calculator_Product::get_product_meta( $product, 'wc_measurement_price_calculator_min_price' ),
			'measurement_type'               => $settings->get_calculator_type(),
		);

		// information required for either pricing or quantity calculator to function
		$wc_price_calculator_params['product_price'] = $product->is_type( 'variable' ) ? '' : $product->get_price();

		// get the product total measurement (ie Area), get a measurement (ie length), and determine the product total measurement common unit based on the measurements common unit
		$product_measurement = WC_Price_Calculator_Product::get_product_measurement( $product, $settings );
		$measurements = $settings->get_calculator_measurements();
		list( $measurement ) = $measurements;
		$product_measurement->set_common_unit( $measurement->get_unit_common() );

		// this is the unit that the product total measurement will be in, ie it's how we know what unit we get for the Volume (AxH) calculator after multiplying A * H
		$wc_price_calculator_params['product_total_measurement_common_unit'] = $product_measurement->get_unit_common();

		if ( WC_Price_Calculator_Product::pricing_calculator_enabled( $product ) ) {
			// product information required for the pricing calculator javascript to function
			$wc_price_calculator_params['calculator_type'] = 'pricing';
			$wc_price_calculator_params['product_price_unit'] = $settings->get_pricing_unit();

			// if there are pricing rules, include them on the page source
			if ( $settings->pricing_rules_enabled() ) {

				$wc_price_calculator_params['pricing_rules'] = $settings->get_pricing_rules();

				// generate the pricing html
				foreach ( $wc_price_calculator_params['pricing_rules'] as $index => $rule ) {
					$price_html = $settings->get_pricing_rule_price_html( $rule );

					$wc_price_calculator_params['pricing_rules'][ $index ]['price_html'] = '<span class="price">' . $price_html . '</span>';
				}
			}
		} else {
			// product information required for the quantity calculator javascript to function
			$wc_price_calculator_params['calculator_type'] = 'quantity';

			$quantity_range = WC_Price_Calculator_Product::get_quantity_range( $product );
			$wc_price_calculator_params['quantity_range_min_value'] = $quantity_range['min_value'];
			$wc_price_calculator_params['quantity_range_max_value'] = $quantity_range['max_value'];

			if ( $product->is_type( 'simple' ) ) {

				// product_measurement represents one quantity of the product, bail if missing required product physical attributes
				if ( ! $product_measurement->get_value() ) return;

				$wc_price_calculator_params['product_measurement_value'] = $product_measurement->get_value();
				$wc_price_calculator_params['product_measurement_unit']  = $product_measurement->get_unit();
			} else {
				// provided by the available_variation() method
				$wc_price_calculator_params['product_measurement_value'] = '';
				$wc_price_calculator_params['product_measurement_unit']  = '';
			}
		}

		wp_localize_script( 'wc-price-calculator', 'wc_price_calculator_params', $wc_price_calculator_params );

	}


	/**
	 * Render the price calculator on the product page
	 *
	 * @since 3.0
	 */
	public function render_price_calculator() {
		global $product, $wc_measurement_price_calculator;

		// is the calculator enabled for this product?
		if ( ! $product || ! WC_Price_Calculator_Product::calculator_enabled( $product ) ) {
			return;
		}

		$settings = new WC_Price_Calculator_Settings( $product );

		if ( WC_Price_Calculator_Product::pricing_calculator_enabled( $product ) ) {
			// Pricing calculator with custom dimensions and a price "per unit"

			// get the product total measurement (ie Area or Volume, etc)
			$product_measurement = WC_Price_Calculator_Product::get_product_measurement( $product, $settings );
			$product_measurement->set_unit( $settings->get_pricing_unit() );

			// get the product measurements, get a measurement, and set the product total measurement common unit based on the measurements common unit
			$measurements = $settings->get_calculator_measurements();
			list( $measurement ) = $measurements;
			$product_measurement->set_common_unit( $measurement->get_unit_common() );

			// pricing calculator enabled, get the template
			wc_get_template(
				'single-product/price-calculator.php',
				array(
					'product_measurement' => $product_measurement,
					'settings'            => $settings,
					'measurements'        => $measurements,
				),
				'',
				$wc_measurement_price_calculator->get_plugin_path() . '/templates/' );

				// need an element to contain the price for simple pricing rule products
				if ( $product->is_type( 'simple' ) && $settings->pricing_rules_enabled() ) {
					echo '<div class="single_variation"></div>';
				}
		} else {
			// quantity calculator.  where the quantity of product needed is based on the configured product dimensions.  This is a actually bit more complex

			// get the starting quantity, max quantity, and total product measurement in product units
			$quantity_range = WC_Price_Calculator_Product::get_quantity_range( $product );

			// set the product measurement based on the minimum quantity value, and set the unit to the frontend calculator unit
			$measurements = $settings->get_calculator_measurements();

			// The product measurement will be used to create the 'amount actual' field.
			$product_measurement = WC_Price_Calculator_Product::get_product_measurement( $product, $settings );

			// see whether all calculator measurements are defined in the same units (ie 'in', 'sq. in.' are considered the same)
			$measurements_unit = null;
			foreach ( $measurements as $measurement ) {
				if ( ! $measurements_unit ) $measurements_unit = $measurement->get_unit();
				else if ( ! WC_Price_Calculator_Measurement::compare_units( $measurements_unit, $measurement->get_unit() ) ) {
					$measurements_unit = false;
					break;
				}
			}

			// All calculator measurements use the same base units, so lets use those for the 'amount actual' field
			//  area/volume product measurement can have a calculator measurement defined in units of length, so it
			//  will need to be converted to units of area or volume respectively
			if ( $measurements_unit ) {
				switch( $product_measurement->get_type() ) {
					case 'area':   $measurements_unit = WC_Price_Calculator_Measurement::to_area_unit( $measurements_unit );   break;
					case 'volume': $measurements_unit = WC_Price_Calculator_Measurement::to_volume_unit( $measurements_unit ); break;
				}
			}

			// if the price per unit is displayed for this product, default to the pricing units for the 'amount actual' field
			if ( WC_Price_Calculator_Product::pricing_per_unit_enabled( $product ) ) {
				$measurements_unit = $settings->get_pricing_unit();
			}

			// if a measurement unit other than the default was determined, set it
			if ( $measurements_unit ) {
				$product_measurement->set_unit( $measurements_unit );
			}

			$total_price = '';

			if ( $product->is_type( 'simple' ) ) {
				// If the product type is simple we can set an initial 'Amount Actual' and 'total price'
				//  we can't do this for variable products because we don't know which will be configured
				//  initially (actually I guess a default product can be configured, so maybe we can do something here)

				// not enough product physical attributes defined to get our measurement, so bail
				if ( ! $product_measurement->get_value() ) return;

				// figure out the starting measurement amount
				// multiply the starting quantity by the measurement value
				$product_measurement->set_value( round( $quantity_range['min_value'] * $product_measurement->get_value(), 2 ) );

				$total_price = wc_price( $quantity_range['min_value'] * $product->get_price(), 2 );
			} elseif ( $product->is_type( 'variable' ) ) {
				// clear the product measurement value for variable products, since we can't really know what it is ahead of time (except for when a default is set)
				$product_measurement->set_value( '' );
			}

			// pricing calculator enabled, get the template
			wc_get_template(
				'single-product/quantity-calculator.php',
				array(
					'calculator_type'     => $settings->get_calculator_type(),
					'product_measurement' => $product_measurement,
					'measurements'        => $measurements,
					'total_price'         => $total_price,
				),
				'',
				$wc_measurement_price_calculator->get_plugin_path() . '/templates/' );
		}
	}
}
