<?php
/**
 * WooCommerce Jetpack Products Shortcodes
 *
 * The WooCommerce Jetpack Products Shortcodes class.
 *
 * @class    WCJ_Products_Shortcodes
 * @version  2.1.3
 * @category Class
 * @author   Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_Products_Shortcodes' ) ) :

class WCJ_Products_Shortcodes extends WCJ_Shortcodes {

    /**
     * Constructor.
     */
    public function __construct() {

		$this->the_shortcodes = array(
			'wcj_product_image',
			'wcj_product_price',
			'wcj_product_sku',
			'wcj_product_title',
			'wcj_product_weight',
		);

		$this->the_atts = array(
			'product_id'    => 0,
			'image_size'    => 'shop_thumbnail',
			'multiply_by'   => '',
			'hide_currency' => 'no',
		);

		parent::__construct();
    }

    /**
     * Inits shortcode atts and properties.
	 *
	 * @param array $atts Shortcode atts.
	 *
	 * @return array The (modified) shortcode atts.
     */
	function init_atts( $atts ) {

		// Atts
		$atts['product_id'] = ( 0 == $atts['product_id'] ) ? get_the_ID() : $atts['product_id'];
		if ( 0 == $atts['product_id'] ) return false;
		if ( 'product' !== get_post_type( $atts['product_id'] ) ) return false;

		// Class properties
		$this->the_product = wc_get_product( $atts['product_id'] );
		if ( ! $this->the_product ) return false;

		return $atts;
	}

    /**
     * Returns product (modified) price.
	 *
	 * @todo Variable products: not range and price by country.
	 *
	 * @return string The product (modified) price
     */
	function wcj_product_price( $atts ) {
		// Variable
		if ( $this->the_product->is_type( 'variable' ) ) {
			$min = $this->the_product->get_variation_price( 'min', false );
			$max = $this->the_product->get_variation_price( 'max', false );
			if ( '' !== $atts['multiply_by'] && is_numeric( $atts['multiply_by'] ) ) {
				$min = $min * $atts['multiply_by'];
				$max = $max * $atts['multiply_by'];
			}
			if ( 'yes' !== $atts['hide_currency'] ) {
				$min = wc_price( $min );
				$max = wc_price( $max );
			}
			return sprintf( '%s-%s', $min, $max );
		}
		// Simple etc.
		else {
			$the_price = $this->the_product->get_price();
			if ( '' !== $atts['multiply_by'] && is_numeric( $atts['multiply_by'] ) ) $the_price = $the_price * $atts['multiply_by'];
			return ( 'yes' === $atts['hide_currency'] ) ? $the_price : wc_price( $the_price );
		}
	}

	/**
	 * Get SKU (Stock-keeping unit) - product unique ID.
	 *
	 * @return string
	 */
	function wcj_product_sku( $atts ) {
		return $this->the_product->get_sku();
	} 
	
	/**
	 * Get the title of the product.
	 *
	 * @return string
	 */
	function wcj_product_title( $atts ) {
		return $this->the_product->get_title();
	}
	
	/**
	 * Get the product's weight.
	 *
	 * @return string
	 */
	function wcj_product_weight( $atts ) {
		return $this->the_product->get_weight();
	}    
	
	
	
	/**
     * wcj_product_image.
     */
	function wcj_product_image( $atts ) {
		return $this->the_product->get_image( $atts['image_size'] );
	}
}

endif;

return new WCJ_Products_Shortcodes();
