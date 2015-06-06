<?php
/**
 * Plugin Name: WooCommerce Measurement Price Calculator
 * Plugin URI: http://www.woothemes.com/products/measurement-price-calculator/
 * Description: WooCommerce plugin to provide price and quantity calculations based on product measurements
 * Author: SkyVerge
 * Author URI: http://www.skyverge.com
 * Version: 3.5.2
 * Text Domain: woocommerce-measurement-price-calculator
 * Domain Path: /i18n/languages/
 *
 * Copyright: (c) 2012-2014 SkyVerge, Inc. (info@skyverge.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package   WC-Measurement-Price-Calculator
 * @author    SkyVerge
 * @category  Plugin
 * @copyright Copyright (c) 2012-2014, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Required functions
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

// Plugin updates
woothemes_queue_update( plugin_basename( __FILE__ ), 'be4679e3d3b24f513b2266b79e859bab', '18735' );

// WC active check
if ( ! is_woocommerce_active() ) {
	return;
}

// Required library classss
if ( ! class_exists( 'SV_WC_Framework_Bootstrap' ) ) {
	require_once( 'lib/skyverge/woocommerce/class-sv-wc-framework-bootstrap.php' );
}

SV_WC_Framework_Bootstrap::instance()->register_plugin( '3.0.0', __( 'WooCommerce Measurement Price Calculator', 'woocommerce-measurement-price-calculator' ), __FILE__, 'init_woocommerce_measurement_price_calculator', array( 'minimum_wc_version' => '2.1', 'backwards_compatible' => '3.0.0' ) );

function init_woocommerce_measurement_price_calculator() {

/**
 * # Main WooCommerce Measurement Price Calculator Class
 *
 * ## Plugin Overview
 *
 * This measurement price calculator plugin actually provides two seemingly
 * related but distinct operational modes:  a quantity calculator, and a
 * pricing calculator.
 *
 * The quantity calculator operates on the configured product dimensions and
 * allows the customer to specify the measurements they require.  This is
 * useful for a merchant that sells a product like boxed tiles which cover a
 * known square footage.  If a box covers 25 sq ft and the customer requires
 * 30 sq ft then the calculator will set the quantity to '2'.
 *
 * The pricing calculator allows the shopkeeper to define a price per unit
 * (ie $/ft) and then the customer supplies the measurements they want.  This
 * is ideal for a merchant that sells a product which is customized to order,
 * such as fabric.  They define a price per unit, and the customer enters the
 * dimensions required, and the quantity.  The customer-supplied measurements
 * are added as order meta data.
 *
 * ## Terminology
 *
 * + `Total measurement` - the total measurement for a product is the length/width/
 *   height for a dimension product, the area for an area/area (LxW) product,
 *   the volume for a volume/volume (AxH)/volume (LxWxH) and the weight for a
 *   weight product.
 *   Related terms: derived measurement, compound measurement
 *
 * + `Common unit` - a single unit "family" used when deriving a compound measurement from
 *   a set of simple measurements.  For instance when finding the Volume (AxH)
 *   the standard units for area and height could be 'sq. ft.' and 'ft' which
 *   when multiplied yield  the known unit 'cu. ft.'.  Without a common unit
 *   you could end up multiplying acres * cm, and what unit does that yield?
 *
 * + `Standard unit` - one of a limited number of units to which all other units
 *   are converted as an intermediate step before converting to a final desired
 *   unit.  This is used to solve the many-to-many problem of converting between
 *   two arbitrary units.  Using a set of standard units means we only need to
 *   know how to convert any arbitrary unit *to* one of the standard units, and
 *   *from* the set of standard units to any other arbitrary unit, which is a
 *   vastly simpler problem than knowing how to convert directly between any
 *   two arbitrary units.  A set of standard units is defined for each system
 *   of measurement (English and SI) so that unit conversion can generally take
 *   place within a single system of measurement, because converting between
 *   systems of measurments results in a loss of precision and accuracy and
 *   requires complex rounding rules to compensate for.
 *
 * ## Admin Considerations
 *
 * ### Global Settings
 *
 * This plugin adds two product measurements to the WooCommerce > Catalog
 * global configuration: area and volume.  Additionally a few new units are
 * added to the core Weight/Dimension measurements
 *
 * ### Product Configuration
 *
 * In the product edit screen a new tab named Measurement is added to the Product
 * Data panel.  This allows the measurement price calculator to be configured
 * for a given product, and the settings here can change other parts of the edit
 * product admin by changing labels, hiding fields, etc.
 *
 * An area and volume measurement field is added to the Shipping tab.
 *
 * ## Frontend
 *
 * ### Cart Item Data
 *
 * The following cart item data is added for pricing calculator products:
 *
 * pricing_item_meta_data => Array(
 *   _price                   => (float) the total product price,
 *   _measurement_needed      => (float) the total measurment needed,
 *   _measurement_needed_unit => (string) the total measurement units,
 *   _quantity                => (int) the quantity added by the customer,
 *   <measurement name>       => (float) measurement amount provided by the customer and depends on the calculator type.  For instance 'length' => 2
 * )
 *
 * ## Database
 *
 * ### Order Item Meta
 *
 * + `<measurement label> (<unit>)` - Visible measurement label and unit for
 *   the pricing calculator product measurements, with associated value supplied
 *   by the customer Ie: "Length (ft): 2"
 *
 * + `Total <measurement> (<unit>)` - Visble total measurement label and unit
 *   for the pricing calculator product measurements, with associated value supplied
 *   by the customer.  Ie: "Total Area (sq. ft.): 4"
 *
 * + `_measurement_data` - Serialized array of pricing calculator product
 *   measurements so that a customized product can be re-ordered:
 *   Array(
 *     <measurement name> => Array(
 *       value => (numeric) the value,
 *       unit  => (string) the unit,
 *     ),
 *     _measurement_needed      => (numeric) the total product measurement,
 *     _measurement_needed_unit => (string) the unit for _measurement_needed
 *   )
 *
 * TODO: after spending some time adding compatibility with various plugins (Dynamic Pricing, Product Addons, etc) consider rethinking the way we set the calculated price.
 *   Rather than setting it to the session, I think Dynamic Pricing adds it in after the cart is pulled from the session.  Which might make more sense?
 *
 * @since 1.0
 */
class WC_Measurement_Price_Calculator extends SV_WC_Plugin {


	/** plugin version */
	const VERSION = '3.5.2';

	/** the plugin id */
	const PLUGIN_ID = 'measurement_price_calculator';

	/** plugin text domain */
	const TEXT_DOMAIN = 'woocommerce-measurement-price-calculator';


	/**
	 * The pricing calculator inventory handling class
	 * @var WC_Price_Calculator_Inventory
	 */
	private $pricing_calculator_inventory;

	/**
	 * The pricing calculator cart class
	 * @var WC_Price_Calculator_Cart
	 */
	private $cart;

	/**
	 * The pricing calculator frontend product loop class
	 * @var WC_Price_Calculator_Product_loop
	 */
	private $product_loop;

	/**
	 * The pricing calculator frontend product page class
	 * @var WC_Price_Calculator_Product_page
	 */
	private $product_page;


	/**
	 * Construct and initialize the main plugin class
	 *
	 * @see SV_WC_Plugin::__construct()
	 */
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			self::TEXT_DOMAIN
		);

		// include required files
		$this->includes();

		add_action( 'woocommerce_init', array( $this, 'woocommerce_init' ) );
		add_action( 'sv_wc_framework_plugins_loaded', array( $this, 'init' ) );
	}


	/**
	 * Handle localization, WPML compatible
	 *
	 * @see SV_WC_Plugin::load_translation()
	 */
	public function load_translation() {

		// localization (remember symlinks will break this)
		load_plugin_textdomain( 'woocommerce-measurement-price-calculator', false, dirname( plugin_basename( $this->get_file() ) ) . '/i18n/languages' );

	}


	/**
	 * Init Measurement Price Calculator when WooCommerce initializes
	 */
	public function woocommerce_init() {

		// include files which depend on WooCommerce being loaded
		require_once( 'classes/class-wc-price-calculator-inventory.php' );

		// inventory handling
		$this->pricing_calculator_inventory = new WC_Price_Calculator_Inventory();

		// frontend product loop handling
		$this->product_loop = new WC_Price_Calculator_Product_Loop();

		// frontend product page handling
		$this->product_page = new WC_Price_Calculator_Product_Page();

		// frontend cart handling
		$this->cart = new WC_Price_Calculator_Cart();

		// add pricing table shortcode
		add_shortcode( 'wc_measurement_price_calculator_pricing_table', array( $this, 'pricing_table_shortcode' ) );
	}


	/**
	 * Remove the requirement that stock amounts be integers
	 *
	 * @since 3.3
	 */
	public function init() {

		// Stock amounts are *not* integers by default
		remove_filter( 'woocommerce_stock_amount', 'intval' );

		// so let them be
		add_filter( 'woocommerce_stock_amount', 'floatval' );

	}


	/** Shortcodes ******************************************************/


	/**
	 * Pricing table shortcode: renders a table of product prices
	 *
	 * @since 3.0
	 * @param array $atts associative array of shortcode parameters
	 * @return string shortcode content
	 */
	public function pricing_table_shortcode( $atts ) {

		require_once( 'classes/shortcodes/class-wc-price-calculator-shortcode-pricing-table.php' );

		return WC_Shortcodes::shortcode_wrapper( array( 'WC_Price_Calculator_Shortcode_Pricing_Table', 'output' ), $atts, array( 'class' => 'wc-measurement-price-calculator' ) );
	}


	/** Helper methods ******************************************************/


	/**
	 * Include required files
	 */
	private function includes() {

		require_once( 'classes/class-wc-price-calculator-cart.php' );
		require_once( 'classes/class-wc-price-calculator-measurement.php' );
		require_once( 'classes/class-wc-price-calculator-product-loop.php' );
		require_once( 'classes/class-wc-price-calculator-product-page.php' );
		require_once( 'classes/class-wc-price-calculator-product.php' );
		require_once( 'classes/class-wc-price-calculator-settings.php' );

		if ( is_admin() ) {
			$this->admin_includes();
		}
	}


	/**
	 * Include required admin files
	 */
	private function admin_includes() {
		require_once( 'admin/woocommerce-measurement-price-calculator-admin-init.php' );  // Admin section
	}


	/** Getter methods ******************************************************/


	/**
	 * Returns the plugin name, localized
	 *
	 * @since 3.3
	 * @see SV_WC_Plugin::get_plugin_name()
	 * @return string the plugin name
	 */
	public function get_plugin_name() {
		return __( 'WooCommerce Measurement Price Calculator', self::TEXT_DOMAIN );
	}


	/**
	 * Returns __FILE__
	 *
	 * @since 3.3
	 * @see SV_WC_Plugin::get_file()
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {
		return __FILE__;
	}


	/**
	 * Gets the plugin documentation url
	 *
	 * @since 3.3
	 * @return string documentation URL
	 */
	public function get_documentation_url() {
		return 'http://docs.woothemes.com/document/measurement-price-calculator/';
	}


	/** Lifecycle methods ******************************************************/


	/**
	 * Initial plugin install path.  Note that with version 3.3 of the plugin
	 * the database version option name changed, so this also handles the case
	 * of updating in that circumstance
	 *
	 * @since 3.3
	 * @see SV_WC_Plugin::install()
	 */
	protected function install() {

		global $wpdb;

		// check for a pre 3.3 version
		$legacy_version = get_option( 'wc_measurement_price_calculator_db_version' );

		if ( false !== $legacy_version ) {

			// upgrade path from previous version, trash old version option
			delete_option( 'wc_measurement_price_calculator_db_version' );

			// upgrade path
			$this->upgrade( $legacy_version );

			// and we're done
			return;
		}

		// true install
		require_once( 'classes/class-wc-price-calculator-settings.php' );

		// set the default units for our custom measurement types
		add_option( 'woocommerce_area_unit',   WC_Price_Calculator_Settings::DEFAULT_AREA );
		add_option( 'woocommerce_volume_unit', WC_Price_Calculator_Settings::DEFAULT_VOLUME );

		// Upgrade path from pre-versioned 1.x
		// get all old-style measurement price calculator products
		$rows = $wpdb->get_results( "SELECT post_id, meta_value FROM " . $wpdb->postmeta . " WHERE meta_key='_measurement_price_calculator'" );

		foreach ( $rows as $row ) {

			if ( $row->meta_value ) {

				// calculator is enabled
				$product_custom_fields = get_post_custom( $row->post_id );

				// as long as the product doesn't also already have a new-style price calculator settings
				if ( ! isset( $product_custom_fields['_wc_price_calculator'][0] ) || ! $product_custom_fields['_wc_price_calculator'][0] ) {

					$settings = new WC_Price_Calculator_Settings();
					$settings = $settings->get_raw_settings();  // we want the underlying raw settings array

					switch ( $row->meta_value ) {
						case 'dimensions':
							$settings['calculator_type'] = 'dimension';
							// the previous version of the plugin allowed this weird multi-dimension tied input thing,
							//  I don't think anyone actually used it, and it didn't make much sense, so I'm not supporting
							//  it any longer
							if ( 'yes' == $product_custom_fields['_measurement_dimension_length'][0] ) {
								$settings['dimension']['length']['enabled']  = 'yes';
								$settings['dimension']['length']['unit']     = $product_custom_fields['_measurement_display_unit'][0];
								$settings['dimension']['length']['editable'] = $product_custom_fields['_measurement_dimension_length_editable'][0];
							} elseif ( 'yes' == $product_custom_fields['_measurement_dimension_width'][0] ) {
								$settings['dimension']['width']['enabled']  = 'yes';
								$settings['dimension']['width']['unit']     = $product_custom_fields['_measurement_display_unit'][0];
								$settings['dimension']['width']['editable'] = $product_custom_fields['_measurement_dimension_width_editable'][0];
							} elseif ( 'yes' == $product_custom_fields['_measurement_dimension_height'][0] ) {
								$settings['dimension']['height']['enabled']  = 'yes';
								$settings['dimension']['height']['unit']     = $product_custom_fields['_measurement_display_unit'][0];
								$settings['dimension']['height']['editable'] = $product_custom_fields['_measurement_dimension_height_editable'][0];
							}
						break;
						case 'area':
							$settings['calculator_type'] = 'area';
							$settings['area']['area']['unit']     = $product_custom_fields['_measurement_display_unit'][0];
							$settings['area']['area']['editable'] = $product_custom_fields['_measurement_editable'][0];
						break;
						case 'volume':
							$settings['calculator_type'] = 'volume';
							$settings['volume']['volume']['unit']     = $product_custom_fields['_measurement_display_unit'][0];
							$settings['volume']['volume']['editable'] = $product_custom_fields['_measurement_editable'][0];
						break;
						case 'weight':
							$settings['calculator_type'] = 'weight';
							$settings['weight']['weight']['unit']     = $product_custom_fields['_measurement_display_unit'][0];
							$settings['weight']['weight']['editable'] = $product_custom_fields['_measurement_editable'][0];
						break;
						case 'walls':
							$settings['calculator_type'] = 'wall-dimension';
							$settings['wall-dimension']['length']['unit'] = $product_custom_fields['_measurement_display_unit'][0];
							$settings['wall-dimension']['width']['unit']  = $product_custom_fields['_measurement_display_unit'][0];
						break;
					}

					update_post_meta( $row->post_id, '_wc_price_calculator', $settings );
				}
			}
		}
	}


	/**
	 * Perform any version-related changes. Changes to custom db tables should be handled by the migrate() method
	 *
	 * @since 3.0
	 * @see SV_WC_Plugin::upgrade()
	 * @param int $installed_version the currently installed version of the plugin
	 */
	protected function upgrade( $installed_version ) {

		if ( version_compare( $installed_version, "3.0", '<' ) ) {

			global $wpdb;

			require_once( 'classes/class-wc-price-calculator-settings.php' );

			// updating 3.0: From 2.0 to 3.0, the '_wc_price_calculator'
			// product post meta calculator settings structure changed: 'calculator'
			// was added to the 'pricing' option

			$rows = $wpdb->get_results( "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key='_wc_price_calculator'" );

			foreach ( $rows as $row ) {

				if ( $row->meta_value ) {
					// calculator settings found

					$settings = new WC_Price_Calculator_Settings();
					$settings = $settings->set_raw_settings( $row->meta_value );  // we want the updated underlying raw settings array

					$updated = false;
					foreach ( WC_Price_Calculator_Settings::get_measurement_types() as $measurement_type ) {
						if ( isset( $settings[ $measurement_type ]['pricing']['enabled'] ) && 'yes' == $settings[ $measurement_type ]['pricing']['enabled'] ) {
							// enable the pricing calculator in the new settings data structure
							$settings[ $measurement_type ]['pricing']['calculator'] = array( 'enabled' => 'yes' );
							$updated = true;
						}
					}

					if ( $updated ) {
						update_post_meta( $row->post_id, '_wc_price_calculator', $settings );
					}
				}
			}
		}
	}


}


/**
 * The WC_Measurement_Price_Calculator global object
 * @name $wc_measurement_price_calculator
 * @global WC_Measurement_Price_Calculator $GLOBALS['wc_measurement_price_calculator']
 */
$GLOBALS['wc_measurement_price_calculator'] = new WC_Measurement_Price_Calculator();

} // init_woocommerce_measurement_price_calculator()
