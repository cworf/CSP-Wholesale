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
 * @package   WC-Measurement-Price-Calculator/Cart
 * @author    SkyVerge
 * @copyright Copyright (c) 2012-2014, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Measurement Price Calculator Cart Class
 *
 * @since 3.0
 */
class WC_Price_Calculator_Cart {


	/**
	 * @var array $measurements_needed associative array of measurements needed Array( 'value' => (float) $value, 'unit' => $unit, 'common_unit' => $common_unit )
	 */
	private $measurements_needed = array();


	/**
	 * Construct and initialize the class
	 *
	 * @since 3.0
	 */
	public function __construct() {

		// validation for pricing calculator which requires a measurement to be provided
		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'add_to_cart_validation' ), 10, 6 );

		// cart filters/actions to display the user-supplied product measurements and set the correct price for the pricing calculator
		add_filter( 'woocommerce_add_cart_item_data',     array( $this, 'add_cart_item_data' ), 10, 3 );

		// adjust quantity for a measurement price calculator pricing product sold individually
		add_filter( 'woocommerce_add_to_cart_sold_individually_quantity', array( $this, 'add_to_cart_sold_individually_quantity' ), 10, 3 );

		// persist the cart item data, and set the item price (when needed) first, before any other plugins
		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 1, 2 );

		// add compatibility with WooCommerce Dynamic Pricing
		add_action( 'wc_dynamic_pricing_adjusted_price',      array( $this, 'dynamic_pricing_adjusted_price' ), 10, 3 );

		add_filter( 'woocommerce_get_item_data',              array( $this, 'display_product_data_in_cart' ), 10, 2 );
		add_action( 'woocommerce_add_order_item_meta',        array( $this, 'add_order_item_meta' ), 10, 2 );

		// on add to cart set the price when needed, and do it first, before any other plugins
		add_filter( 'woocommerce_add_cart_item', array( $this, 'set_product_prices' ), 1, 1 );

		// returns the cart widget item price html string
		add_filter( 'woocommerce_cart_item_price',      array( $this, 'get_cart_widget_item_price_html' ), 9, 3 );

		// calculated weight handling
		add_action( 'woocommerce_before_calculate_totals',  array( $this, 'calculate_product_weights' ) );
		add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'calculate_product_weights' ) );

		// "order again" handling
		add_filter( 'woocommerce_order_again_cart_item_data',      array( $this, 'order_again_cart_item_data' ), 10, 3 );

	}


	/**
	 * Filter to check whether a product is valid to be added to the cart.
	 * This is used to ensure a measurement is provided when the price
	 * calculator is used
	 *
	 * @since 3.0
	 * @param boolean $valid whether the product as added is valid
	 * @param int $product_id the product identifier
	 * @param int $quantity the amount being added
	 * @param int $variation_id optional variation id
	 * @param array $variations optional variation configuration
	 * @param array $cart_item_data optional cart item data.  This will only be
	 *        supplied when an order is being-ordered, in which case the
	 *        required measurements will not be available from the REQUEST array
	 */
	public function add_to_cart_validation( $valid, $product_id, $quantity, $variation_id = '', $variations = array(), $cart_item_data = array() ) {

		$product = SV_WC_Plugin_Compatibility::wc_get_product( $product_id );

		// is the calculator enabled for this product?
		if ( $valid && WC_Price_Calculator_Product::pricing_calculator_enabled( $product ) ) {

			$settings = new WC_Price_Calculator_Settings( $product );
			$measurements = $settings->get_calculator_measurements();

			// the individual measurements (for simple calculators like the length or weight or area this will be just the length/weight/area/whatever,
			//  while for more complicated ones like area-dimension this will be the length and width
			foreach ( $measurements as $measurement ) {

				$value = null;
				if ( isset( $_REQUEST[ $measurement->get_name() . '_needed' ] ) ) {
					$value = str_replace( get_option( 'woocommerce_price_decimal_sep' ), '.', $_REQUEST[ $measurement->get_name() . '_needed' ] );
				} elseif ( isset( $cart_item_data['pricing_item_meta_data'][ $measurement->get_name() ] ) ) {
					$value = $cart_item_data['pricing_item_meta_data'][ $measurement->get_name() ];
				}

				// save the value of measurement needed
				if ( $value ) {
					$value = WC_Price_Calculator_Measurement::convert_to_float( $value );
					$this->measurements_needed[ $measurement->get_name() ] = array(
						'value'       => $value,
						'unit'        => $measurement->get_unit(),
						'common_unit' => $measurement->get_unit_common(),
					);
				}

				if ( ! $value || ! is_numeric( $value ) || $value <= 0 ) {
					wc_add_notice( sprintf( __( "%s missing", WC_Measurement_Price_Calculator::TEXT_DOMAIN ), $measurement->get_label() ), 'error' );
					$valid = false;
				}
			}

			// get the measurement needed, from the $_POST object for a normal add to cart action, or from the $cart_item_data for a programmatic add-to-cart
			$measurement_needed_value = null;
			if ( isset( $_POST['_measurement_needed'] ) ) {
				$measurement_needed_value = $_POST['_measurement_needed'];
				$measurement_needed_value_unit = $_POST['_measurement_needed_unit'];
			} elseif ( isset( $cart_item_data['pricing_item_meta_data']['_measurement_needed_internal'] ) ) {
				$measurement_needed_value = $cart_item_data['pricing_item_meta_data']['_measurement_needed_internal'];
				$measurement_needed_value_unit = $cart_item_data['pricing_item_meta_data']['_measurement_needed_unit_internal'];
			}

			// if pricing rules are enabled validate there is a matching rule available
			if ( $settings->pricing_rules_enabled() && $measurement_needed_value ) {

				$measurement_needed = new WC_Price_Calculator_Measurement( $measurement_needed_value_unit, $measurement_needed_value );

				if ( is_null( $settings->get_pricing_rules_price( $measurement_needed ) ) ) {
					wc_add_notice( __( "No price available for a product with this measurement, please contact the store for assistance.", WC_Measurement_Price_Calculator::TEXT_DOMAIN ), 'error' );
					$valid = false;
				}
			}

			// allow other code to validate based on the provided measurements
			$valid = apply_filters( 'wc_measurement_price_calculator_add_to_cart_validation', $valid, $product_id, $quantity, $measurements );
		}

		return $valid;
	}


	/**
	 * Add any user-supplied product pricing measurement field data to the
	 * cart item data, to set in the session
	 *
	 * @since 3.0
	 * @param array $cart_item_data associative-array of name/value pairs of cart item data
	 * @param int $product_id the product identifier
	 * @param int $variation_id optional product variation identifer
	 * @return array associative array of name/value pairs of cart item
	 *         data to set in the session
	 */
	public function add_cart_item_data( $cart_item_data, $product_id, $variation_id ) {

		// we want the product, not the variation
		$product = SV_WC_Plugin_Compatibility::wc_get_product( $product_id );

		// is this a product with a pricing calculator?
		if ( WC_Price_Calculator_Product::pricing_calculator_enabled( $product ) ) {

			$settings = new WC_Price_Calculator_Settings( $product );

			// now we want the variation if there is one
			$_product = $variation_id ? SV_WC_Plugin_Compatibility::wc_get_product( $variation_id, array( 'parent_id' => $product_id ) ) : $product;

			// get the measurement needed, from the $_POST object for a normal add to cart action, or from the $cart_item_data for a programmatic add-to-cart
			$measurement_needed_value = $measurement_needed_value_unit = null;

			if ( isset( $_POST['_measurement_needed'] ) && ! empty( $this->measurements_needed ) ) {
				$measurement_needed_value      = $this->calculate_measurement_needed( $product );
				$measurement_needed_value_unit = $_POST['_measurement_needed_unit'];
			} elseif ( isset( $cart_item_data['pricing_item_meta_data']['_measurement_needed_internal'] ) ) {
				$measurement_needed_value      = $cart_item_data['pricing_item_meta_data']['_measurement_needed_internal'];
				$measurement_needed_value_unit = $cart_item_data['pricing_item_meta_data']['_measurement_needed_unit_internal'];
			}

			if ( $measurement_needed_value ) {

				$measurement_needed = new WC_Price_Calculator_Measurement( $measurement_needed_value_unit, (float) $measurement_needed_value );

				// get the product price
				$price = WC_Price_Calculator_Product::calculate_price( $_product, $measurement_needed_value, $measurement_needed_value_unit );

				// save the product total price
				$cart_item_data['pricing_item_meta_data']['_price'] = $price;

				// save the total measurement (length, area, volume, etc)
				$cart_item_data['pricing_item_meta_data']['_measurement_needed']      = $measurement_needed->get_value();
				$cart_item_data['pricing_item_meta_data']['_measurement_needed_unit'] = $measurement_needed->get_unit();
			}

			// record the item quantity
			// NOTE: although it may be more ideal to record item quantity from the 'woocommerce_add_to_cart'
			//  action in case 3rd party plugins modify it, we need to grab it early so prices can be calculated
			//  as needed.  shikata ga nai
			$cart_item_data['pricing_item_meta_data']['_quantity'] = isset( $_REQUEST['quantity'] ) ? (float) $_REQUEST['quantity'] : 1;

			// the individual measurements (for simple calculators like the length or weight or area this will be the same as the measurement needed,
			//  while for more complicated ones like area-dimension this will be the length and width, while measurement_needed will be the total area)
			//  These are recorded so they can be displayed within the cart/checkout/admin
			foreach ( $settings->get_calculator_measurements() as $measurement ) {
				if ( isset( $_POST[ $measurement->get_name() . '_needed' ] ) ) {
					$cart_item_data['pricing_item_meta_data'][ $measurement->get_name() ] = $_POST[ $measurement->get_name() . '_needed' ];
				}
			}
		}

		return $cart_item_data;
	}


	/**
	 * Adjust the quantity for a measurement price calculator pricing product
	 * sold individually
	 *
	 * @since 3.5.0
	 * @param int $sold_individually_quantity
	 * @param int $quantity original quantity of item to add to cart
	 * @param int $product_id the product identifier
	 * @return int quantity
	 */
	public function add_to_cart_sold_individually_quantity( $sold_individually_quantity, $quantity, $product_id ) {

		// we want the product, not the variation
		$product = SV_WC_Plugin_Compatibility::wc_get_product( $product_id );

		// is this a product with a pricing calculator?
		if ( WC_Price_Calculator_Product::pricing_calculator_enabled( $product ) ) {
			return $quantity;
		}

		return $sold_individually_quantity;
	}


	/**
	 * Calculate the total measurement needed
	 *
	 * @since 3.4.0
	 * @param object $product
	 * @return float total measurement needed
	 */
	private function calculate_measurement_needed( $product ) {

		$measurement_needed = null;

		// get measurement type
		$settings = new WC_Price_Calculator_Settings( $product );
		$measurement_type = $settings->get_calculator_type();

		foreach ( $this->measurements_needed as $measurement ) {

			// convert to common unit
			$measurement_value = WC_Price_Calculator_Measurement::convert( $measurement['value'], $measurement['unit'], $measurement['common_unit'] );

			if ( 'area-surface' === $measurement_type ) {

				// get dimensions
				$length = WC_Price_Calculator_Measurement::convert( $this->measurements_needed['length']['value'], $this->measurements_needed['length']['unit'], $this->measurements_needed['length']['common_unit'] );
				$width  = WC_Price_Calculator_Measurement::convert( $this->measurements_needed['width']['value'], $this->measurements_needed['width']['unit'], $this->measurements_needed['width']['common_unit'] );
				$height = WC_Price_Calculator_Measurement::convert( $this->measurements_needed['height']['value'], $this->measurements_needed['height']['unit'], $this->measurements_needed['height']['common_unit'] );

				$measurement_needed = 2 * ( $length * $width + $width * $height + $length * $height );

				/**
				 * Filter surface area value.
				 *
				 * @since 3.5.0
				 * @param float $surface_area The calculated surface area.
				 * @param WP_Product $product
				 * @param float $length
				 * @param float $width
				 * @param float $height
				 */
				$measurement_needed = apply_filters( 'wc_measurement_price_calculator_measurement_needed_surface_area', $measurement_needed, $product, $length, $width, $height );
				break;
			}

			if ( 'area-linear' === $measurement_type ) {

				if ( ! $measurement_needed ) {
					// first or single measurement
					$measurement_needed = 2 * $measurement_value;
				} else {
					// multiply to get either the area or volume measurement
					$measurement_needed += 2 * $measurement_value;
				}
			} else {

				if ( ! $measurement_needed ) {
					// first or single measurement
					$measurement_needed = $measurement_value;
				} else {
					// multiply to get either the area or volume measurement
					$measurement_needed *= $measurement_value;
				}
			}
		}

		// get common unit
		$product_measurement = WC_Price_Calculator_Product::get_product_measurement( $product, $settings );
		$measurements = $settings->get_calculator_measurements();
		list( $measurement ) = $measurements;
		$product_measurement->set_common_unit( $measurement->get_unit_common() );

		/**
		 * Filter the calculated measurement needed.
		 *
		 * @since 3.5.2
		 * @param float $measurement_needed the calculated measurement needed.
		 * @param string $measurement_type the calculator type e.g. "area-linear"
		 * @param WP_Product $product
		 * @param WC_Price_Calculator_Cart $this Measurement Price Calculator Cart instance
		 */
		$measurement_needed = apply_filters( 'wc_measurement_price_calculator_measurement_needed', $measurement_needed, $measurement_type, $product, $this );

		// convert measurment to pricing unit
		$measurement_needed = WC_Price_Calculator_Measurement::convert( $measurement_needed, $product_measurement->get_unit_common(), $settings->get_pricing_unit() );

		return $measurement_needed;
	}


	/**
	 * Persist our custom cart item data (if any) to the session
	 *
	 * @since 3.0
	 * @param array $cart_item associative array of data representing a cart item (product)
	 * @param array $values associative array of data for the cart item, currently in the session
	 * @return associative array of data representing a cart item (product)
	 */
	public function get_cart_item_from_session( $cart_item, $values ) {

		if ( isset( $values['pricing_item_meta_data'] ) ) {
			$cart_item['pricing_item_meta_data'] = $values['pricing_item_meta_data'];

			// set the product price (if needed)
			$cart_item = $this->set_product_prices( $cart_item );
		}

		return $cart_item;
	}


	/**
	 * Adjust the price based on what dynamic pricing has calculated.  This adds compatibility
	 * for WooCommerce Dynamic Pricing, at least for products without calculated quantity enabled.
	 * That may be another whole can of worms.
	 *
	 * @since 3.1.3
	 * @param float $adjusted_price the price calculated by dynamic pricing
	 * @param string $cart_item_key the cart item key
	 * @param float $original price the original price prior to modification of dynamic pricing
	 * @return float the price
	 */
	public function dynamic_pricing_adjusted_price( $adjusted_price, $cart_item_key, $original_price ) {

		$cart_item_data = WC()->cart->cart_contents[ $cart_item_key ];

		if ( isset( $cart_item_data['pricing_item_meta_data']['_measurement_needed'] ) && $cart_item_data['pricing_item_meta_data']['_measurement_needed'] ) {

			$cart_item_data['data']->set_price( $adjusted_price );

			$adjusted_price = WC_Price_Calculator_Product::calculate_price(
				$cart_item_data['data'],
				$cart_item_data['pricing_item_meta_data']['_measurement_needed'],
				$cart_item_data['pricing_item_meta_data']['_measurement_needed_unit']
			);

		}

		return $adjusted_price;

	}


	/**
	 * Display any user-input product data in the cart
	 *
	 * @since 3.0
	 * @param array $data array of name/display pairs of data to display in the cart
	 * @param array $item associative array of a cart item (product)
	 * @return array of name/display pairs of data to display in the cart
	 */
	public function display_product_data_in_cart( $data, $item ) {

		if ( isset( $item['pricing_item_meta_data'] ) ) {

			$display_data = $this->humanize_cart_item_data( $item, $item['pricing_item_meta_data'] );

			foreach ( $display_data as $name => $value ) {
				$data[] = array( 'name' => $name, 'display' => $value, 'hidden' => false );
			}
		}

		return $data;
	}


	/**
	 * Persist our pricing calculator product custom user-input fields to the database order item meta.
	 * This is called during the checkout process for each cart item added
	 * to the order.
	 *
	 * @since 3.0
	 * @param int $item_id item identifier
	 * @param array $values array of data representing a cart item
	 */
	public function add_order_item_meta( $item_id, $values ) {

		// pricing calculator item?
		if ( isset( $values['pricing_item_meta_data'] ) ) {

			$display_data = $this->humanize_cart_item_data( $values, $values['pricing_item_meta_data'] );

			// set any user-input fields to the order item meta data (which can be displayed on the frontend)
			foreach ( $display_data as $name => $value ) {
				wc_add_order_item_meta( $item_id, $name, $value );
			}

			// persist the configured item measurement data such that the exact same item could be re-configured at a later date
			$measurement_data = $this->get_measurement_cart_item_data( $values, $values['pricing_item_meta_data'] );
			wc_add_order_item_meta( $item_id, '_measurement_data', $measurement_data );

			$_product = $values['data'];

			if ( WC_Price_Calculator_Product::pricing_calculator_inventory_enabled( $_product ) && isset( $values['pricing_item_meta_data']['_quantity'] ) ) {
				// set the actual unit quantity (ie *2* fabrics at 3 ft each, rather than '6')
				wc_update_order_item_meta( $item_id, '_qty', $values['pricing_item_meta_data']['_quantity'] );
			}
		}
	}


	/**
	 * If there are any pricing calculator products in the cart, set the product
	 * prices, if pricing inventory management is not enabled.  This is because
	 * if pricing inventory management is not enabled and we have the following
	 * situation: 1 item 10 ft long at $1/foot, we want to set the item price to
	 * $10.  Contrast that with pricing inventory enabled, in which case the 1
	 * 10 ft item is represented as a quantity of 10 (one for each foot) so the price
	 * used per "item" is $1 (the configured price per foot).  Hopefully that made sense
	 *
	 * We hook in as early as possible since unlike other plugins which are
	 * modifying the price (and using the adjust_price() method) we're technically
	 * setting *the* price, so we need to go first.
	 *
	 * @since 3.0
	 * @param array $cart_item the cart item
	 */
	public function set_product_prices( $cart_item ) {

		// always need the actual parent product, not the useless variation product
		$product = isset( $cart_item['variation_id'] ) && $cart_item['variation_id'] ? SV_WC_Plugin_Compatibility::wc_get_product( $cart_item['product_id'] ) : $cart_item['data'];
		$settings = new WC_Price_Calculator_Settings( $product );

		if ( ! WC_Price_Calculator_Product::pricing_calculator_inventory_enabled( $product ) && isset( $cart_item['pricing_item_meta_data']['_price'] ) ) {

			// pricing inventory management *not* enabled so the item price = item unit price (ie 1 item 10 ft long at $1/foot, the price is $10)
			$cart_item['data']->set_price( (float) $cart_item['pricing_item_meta_data']['_price'] );

		} elseif ( WC_Price_Calculator_Product::pricing_calculator_inventory_enabled( $product ) ) {

			if ( $settings->pricing_rules_enabled() ) {
				// a calculated inventory product with pricing rules enabled will have no configured price, so set it based on the measurement
				$measurement = new WC_Price_Calculator_Measurement( $cart_item['pricing_item_meta_data']['_measurement_needed_unit'], $cart_item['pricing_item_meta_data']['_measurement_needed'] );
				$cart_item['data']->set_price( $settings->get_pricing_rules_price( $measurement ) );
			}

			// is there a minimum price to use?
			if ( WC_Price_Calculator_Product::get_product_meta( $product, 'wc_measurement_price_calculator_min_price' ) > $cart_item['data']->get_price() * ( $cart_item['quantity'] / $cart_item['pricing_item_meta_data']['_quantity'] ) ) {
				$price = WC_Price_Calculator_Product::get_product_meta( $product, 'wc_measurement_price_calculator_min_price' );
				$cart_item['data']->set_price( $price / ( $cart_item['quantity'] / $cart_item['pricing_item_meta_data']['_quantity'] ) );
			}

		}

		return $cart_item;

	}


	/**
	 * Returns the price HTML for the given cart item, to display in the
	 * cart Widget
	 *
	 * @since 3.0
	 * @param string $price_html the price html
	 * @param array $cart_item the cart item
	 * @param string $cart_item_key the unique cart item hash
	 * @return string the price html
	 */
	public function get_cart_widget_item_price_html( $price_html, $cart_item, $cart_item_key ) {

		// if this is a pricing calculator item, and WooCommerce Product Addons hasn't already altered the price
		if ( isset( $cart_item['pricing_item_meta_data']['_price'] ) && empty( $cart_item['addons'] ) ) {
			$price_html = wc_price( (float) $cart_item['pricing_item_meta_data']['_price'] );
		}

		// default
		return $price_html;
	}


	/**
	 * Pricing calculator calculated weight handling
	 *
	 * This method is responsible for the Pricing Calculator
	 * products calculated weight handling.  By default, a pricing calculator product's
	 * weight will be defined as would any other, non-customizable product.  Meaning
	 * that if you have a weight of '10 lbs' for custom-sized tiling, an item could
	 * be of any area and still weigh 10 lbs, which probably isn't very realistic.
	 *
	 * With calculated weight enabled, that same weight of '10' would repesent
	 * '10 lbs / sq ft' meaning that the total weight of an item is calculated based
	 * on its weight ratio and total measurement.
	 *
	 * The implementation strategy used to achieve this is to hook into some critical
	 * actions in the WC_Cart class, loop through the cart items and calculate and
	 * set a weight on the relevant products.  Then when the various shipping
	 * methods call the $product->get_weight() the correct, calculated weight will
	 * be returned.
	 *
	 * @since 3.0
	 * @param WC_Cart $cart the cart object
	 */
	public function calculate_product_weights( $cart ) {

		// Loop through the cart items calculating the total weight for any pricing
		//  calculator calculated weight products
		foreach ( $cart->cart_contents as $cart_item_key => &$values ) {

			$product = $_product = $values['data'];

			// need the parent product to retrieve the calculator settings from
			if ( isset( $_product->variation_id ) && $_product->variation_id ) $product = SV_WC_Plugin_Compatibility::wc_get_product( $_product->id );

			if ( WC_Price_Calculator_Product::pricing_calculated_weight_enabled( $_product ) ) {

				$settings = new WC_Price_Calculator_Settings( $product );

				if ( 'weight' == $settings->get_calculator_type() ) {
					// Now, the weight calculator products have to be handled specially
					//  since the customer is actually supplying the weight, but it will
					//  be in pricing units which may not be the same as the globally
					//  configured WooCommerce Weight Unit expected by other plugins and code

					$supplied_weight = new WC_Price_Calculator_Measurement(
						$values['pricing_item_meta_data']['_measurement_needed_unit'],
						$values['pricing_item_meta_data']['_measurement_needed']
					);

					// set the product weight as supplied by the customer, in WC Weight Units
					$_product->weight = $supplied_weight->get_value( get_option( 'woocommerce_weight_unit' ) );

				} elseif ( $_product->get_weight() ) {

					// record the configured weight per unit for future reference
					if ( ! isset( $values['pricing_item_meta_data']['_weight'] ) ) {
						$values['pricing_item_meta_data']['_weight'] = $_product->get_weight();
					}

					// calculate the product weight = unit weight * total measurement (both will be in the same pricing units so we have say lbs/sq. ft. * sq. ft. = lbs)
					$_product->weight = $values['pricing_item_meta_data']['_weight'] * $values['pricing_item_meta_data']['_measurement_needed'];
				}
			}

		}
	}


	/**
	 * Returns the cart item data for the given item being re-ordered.  This is
	 * a somewhat complex process of re-configuring the product based on the
	 * original measurements, taking into account unit changes.  We do not handle
	 * calculator type changes at the moment; in fact there's probably no way
	 * of accounting for this.  (actually we could handle calculator changes as
	 * long as the calculator type was simplified, ie Area (L x W) -> Area,
	 * but aside from that there's nothing we can do)
	 *
	 * @since 3.0
	 * @param array $cart_item_data the cart item data
	 * @param array $item the item
	 * @param WC_Order $order the original order
	 * @return array the cart item data
	 */
	public function order_again_cart_item_data( $cart_item_data, $item, $order ) {

		$product = SV_WC_Plugin_Compatibility::wc_get_product( $item['product_id'] );

		if ( WC_Price_Calculator_Product::pricing_calculator_enabled( $product ) && isset( $item['item_meta']['_measurement_data'][0] ) && $item['item_meta']['_measurement_data'][0] ) {

			$measurement_data = maybe_unserialize( $item['item_meta']['_measurement_data'][0] );

			$settings = new WC_Price_Calculator_Settings( $product );
			$measurements = $settings->get_calculator_measurements();

			// get the old product measurements, converting to the new measurement units as needed
			foreach ( $measurements as $measurement ) {
				if ( isset( $measurement_data[ $measurement->get_name() ] ) ) {

					$current_unit = $measurement->get_unit();

					$measurement->set_value( $measurement_data[ $measurement->get_name() ]['value'] );
					$measurement->set_unit( $measurement_data[ $measurement->get_name() ]['unit'] );

					$cart_item_data['pricing_item_meta_data'][ $measurement->get_name() ] = $measurement->get_value( $current_unit );
				}
			}

			// the product total measurement
			$measurement_needed = new WC_Price_Calculator_Measurement( $measurement_data['_measurement_needed_unit'], $measurement_data['_measurement_needed'] );

			// if this calculator uses pricing rules, retrieve the price based on the product measurements
			if ( $settings->pricing_rules_enabled() ) {
				$product->price = $settings->get_pricing_rules_price( $measurement_needed );
			}

			// calculate the price
			$price = $product->get_price() * $measurement_needed->get_value( $settings->get_pricing_unit() );

			// is there a minimum price to use?
			if ( WC_Price_Calculator_Product::get_product_meta( $product, 'wc_measurement_price_calculator_min_price' ) > $price ) {
				$price = WC_Price_Calculator_Product::get_product_meta( $product, 'wc_measurement_price_calculator_min_price' );
			}

			// set the product price based on the price per unit and the total measurement
			$cart_item_data['pricing_item_meta_data']['_price'] = $price;

			// save the total measurement (length, area, volume, etc) in pricing units
			$cart_item_data['pricing_item_meta_data']['_measurement_needed']      = $measurement_needed->get_value();
			$cart_item_data['pricing_item_meta_data']['_measurement_needed_unit'] = $measurement_needed->get_unit();

			// pick up the item quantity which we set in order_again_item_set_quantity()
			if ( isset( $item['item_meta']['_quantity'][0] ) ) $cart_item_data['pricing_item_meta_data']['_quantity'] = $item['item_meta']['_quantity'][0];
		}

		return $cart_item_data;
	}


	/** API methods ******************************************************/


	/**
	 * Add a measurement product to the cart.  This allows for the programmatic
	 * addition of measurement pricing calculator products to the cart.
	 *
	 * This method expects the single total measurement needed, given by
	 * $measurement_needed, this would be the dimension, area, volume or weight
	 * depending on the type of calculator.
	 *
	 * This method also expects the full set of product measurements, given by
	 * $measurements.  For calculators with a single measurement like the dimension
	 * calculator or simple area, this will contain the same value as
	 * $measurement_needed.  For more complex calculators, like Area (w x l)
	 * this is how the width and length measurements are specified.  For
	 * convenience use <code>WC_Price_Calculator_Product::get_product_measurements( $product )</code>
	 * to get the set of dimensions for your product, along with the correct
	 * units, and set whatever values you need.
	 *
	 * @since 3.0
	 * @param string $product_id contains the id of the product to add to the cart
	 * @param WC_Price_Calculator_Measurement $measurement_needed the total
	 *        measurement desired, ie 1 m, 3 sq. ft., etc
	 * @param array $measurements array of WC_Price_Calculator_Measurement product
	 *        measurements, ie 1 m or 1.5 ft, 1.5 ft.  Defaults to $measurement_needed
	 *        for convenience for calculators with a single measurement like dimension,
	 *        simple area, etc.
	 * @param string $quantity contains the quantity of the item to add
	 * @param int $variation_id optional variation id
	 * @param array $variation optional attribute values
	 * @param array $cart_item_data optional extra cart item data we want to pass into the item
	 * @return bool true on success
	 */
	public function add_to_cart( $product_id, $measurement_needed, $measurements = array(), $quantity = 1, $variation_id = '', $variation = '', $cart_item_data = array() ) {

		// if measurements is empty just use the provided $measurement_needed (this is a shortcut for calculators with only one measurement, ie 'length', 'area', etc)
		if ( empty( $measurements ) )
			$measurements[] = $measurement_needed;

		// build up the cart item data with the required values that would normally come in over the add to cart post request
		$cart_item_data['pricing_item_meta_data']['_measurement_needed_internal']      = $measurement_needed->get_value();
		$cart_item_data['pricing_item_meta_data']['_measurement_needed_unit_internal'] = $measurement_needed->get_unit();
		$cart_item_data['pricing_item_meta_data']['_quantity'] = $quantity;

		$product = SV_WC_Plugin_Compatibility::wc_get_product( $product_id );
		$settings = new WC_Price_Calculator_Settings( $product );

		if ( WC_Price_Calculator_Product::pricing_calculator_inventory_enabled( $product ) ) {
			// pricing calculator product with inventory enabled, means we need to take the item quantity (ie 2) and determine the unit quantity (ie 2 * 3 ft = 6)
			$quantity *= $measurement_needed->get_value( $settings->get_pricing_unit() );
		}

		foreach ( $measurements as $measurement ) {
			$cart_item_data['pricing_item_meta_data'][ $measurement->get_name() ] = $measurement->get_value();
		}

		// initialize the cart_contents member if needed to avoid a warning from cart::find_product_in_cart()
		if ( is_null( WC()->cart->cart_contents ) ) {
			WC()->cart->cart_contents = array();
		}

		return WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation, $cart_item_data );
	}


	/**
	 * API method to get the item price
	 *
	 * @since 3.1
	 * @param array $item the cart item
	 * @return float the item price
	 */
	public function get_item_price( $item ) {

		// special case for calculated inventory products: the actual product price is held by _price (ie $10)
		//  while the $product->get_price() value is the price per unit (ie $1 / foot)
		if ( WC_Price_Calculator_Product::pricing_calculator_inventory_enabled( $item['data'] ) && isset( $item['pricing_item_meta_data']['_price'] ) ) {
			return $item['pricing_item_meta_data']['_price'];
		}

		// return the regular item price
		return $item['data']->get_price();
	}


	/**
	 * Gets the item quantity
	 *
	 * @since 3.1
	 * @param array $item the cart item
	 * @return int the item quantity
	 */
	public function get_item_quantity( $item ) {

		// special case for calculated inventory products: the actual quantity (ie *1* item 10 feet long)
		//  is held in _quantity while $item['quantity'] would be '10' in this example
		if ( WC_Price_Calculator_Product::pricing_calculator_inventory_enabled( $item['data'] ) && isset( $item['pricing_item_meta_data']['_quantity'] ) ) {
			return $item['pricing_item_meta_data']['_quantity'];
		}

		// return the regular item quantity
		return $item['quantity'];
	}


	/** Helper methods ******************************************************/


	/**
	 * Turn the cart item data into an array that fully describes the configured
	 * item such that it can be re-created again in the future as needed.  This
	 * is not possible from the humanized cart item data
	 *
	 * @since 3.0
	 * @param array $item cart item
	 * @param array $cart_item_data the cart item data
	 * @return array measurement cart item data
	 */
	private function get_measurement_cart_item_data( $item, $cart_item_data ) {
		global $wc_measurement_price_calculator;

		$measurement_data = array();

		// always need the actual parent product, not the useless variation product
		$product = isset( $item['variation_id'] ) && $item['variation_id'] ? SV_WC_Plugin_Compatibility::wc_get_product( $item['product_id'] ) : $item['data'];

		$settings = new WC_Price_Calculator_Settings( $product );

		foreach ( $settings->get_calculator_measurements() as $measurement ) {
			if ( isset( $cart_item_data[ $measurement->get_name() ] ) ) {
				$measurement_data[ $measurement->get_name() ] = array(
					'value' => $cart_item_data[ $measurement->get_name() ],
					'unit'  => $measurement->get_unit(),
				);
			}
		}

		// save the total measurement/unit
		$measurement_data['_measurement_needed']      = $cart_item_data['_measurement_needed'];
		$measurement_data['_measurement_needed_unit'] = $cart_item_data['_measurement_needed_unit'];

		return $measurement_data;
	}


	/**
	 * Turn the cart item data into human-readable key/value pairs for
	 * display in the cart
	 *
	 * @since 3.0
	 * @param array $item cart item
	 * @param array $cart_item_data the cart item data
	 * @return array human-readable cart item data
	 */
	private function humanize_cart_item_data( $item, $cart_item_data ) {

		$new_cart_item_data = array();

		// always need the actual parent product, not the useless variation product
		$product = isset( $item['variation_id'] ) && $item['variation_id'] ? SV_WC_Plugin_Compatibility::wc_get_product( $item['product_id'] ) : $item['data'];

		$settings = new WC_Price_Calculator_Settings( $product );

		foreach ( $settings->get_calculator_measurements() as $measurement ) {
			if ( isset( $cart_item_data[ $measurement->get_name() ] ) ) {

				// if the measurement has a set of available options, get the option label for display, if we can determine it
				//  (this way we display "1/8" rather than "0.125", etc)
				if ( count( $measurement->get_options() ) > 0 ) {
					foreach ( $measurement->get_options() as $value => $label ) {
						if ( $cart_item_data[ $measurement->get_name() ] === $value ) $cart_item_data[ $measurement->get_name() ] = $label;
					}
				}

				$label = $measurement->get_unit_label() ?
					sprintf( "%s (%s)", $measurement->get_label(), __( $measurement->get_unit_label(), WC_Measurement_Price_Calculator::TEXT_DOMAIN ) ) :
					__( $measurement->get_label(), WC_Measurement_Price_Calculator::TEXT_DOMAIN );

				$new_cart_item_data[ $label ] = $cart_item_data[ $measurement->get_name() ];
			}
		}

		// render the total measurement if this is a derived calculator (ie "Area (sq. ft.): 10" if the calculator is Area (LxW))
		if ( $settings->is_calculator_type_derived() && isset( $cart_item_data['_measurement_needed'] ) ) {

			// get the product total measurement (ie area or volume)
			$product_measurement = WC_Price_Calculator_Product::get_product_measurement( $product, $settings );
			$product_measurement->set_unit( $cart_item_data['_measurement_needed_unit'] );
			$product_measurement->set_value( $cart_item_data['_measurement_needed'] );

			$total_amount_text = apply_filters(
				'wc_measurement_price_calculator_total_amount_text',
				$product_measurement->get_unit_label() ?
					sprintf( __( 'Total %s (%s)', WC_Measurement_Price_Calculator::TEXT_DOMAIN ), $product_measurement->get_label(), __( $product_measurement->get_unit_label(), WC_Measurement_Price_Calculator::TEXT_DOMAIN ) ) :
					sprintf( __( 'Total %s', WC_Measurement_Price_Calculator::TEXT_DOMAIN ), $product_measurement->get_label() ),
				$item
			);
			$new_cart_item_data[ $total_amount_text ] = $product_measurement->get_value();
		}

		return $new_cart_item_data;
	}
}
