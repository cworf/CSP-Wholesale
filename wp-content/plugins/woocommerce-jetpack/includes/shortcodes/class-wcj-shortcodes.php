<?php
/**
 * WooCommerce Jetpack Shortcodes
 *
 * The WooCommerce Jetpack Shortcodes class.
 *
 * @class    WCJ_Shortcodes
 * @version  1.0.0
 * @category Class
 * @author   Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_Shortcodes' ) ) :

class WCJ_Shortcodes {

    /**
     * Constructor.
     */
    public function __construct() {

		foreach( $this->the_shortcodes as $the_shortcode ) {
			add_shortcode( $the_shortcode, array( $this, 'wcj_shortcode' ) );
		}

		add_filter( 'wcj_shortcodes_list', array( $this, 'add_shortcodes_to_the_list' ) );
    }

    /**
     * add_extra_atts.
     */
	function add_extra_atts( $atts ) {
		$final_atts = array_merge( $this->the_atts, $atts );
		return $final_atts;
	}

    /**
     * init_atts.
     */
	function init_atts( $atts ) {
		return $atts;
	}

    /**
     * add_shortcodes_to_the_list.
     */
	function add_shortcodes_to_the_list( $shortcodes_list ) {
		foreach( $this->the_shortcodes as $the_shortcode ) {
			$shortcodes_list[] = $the_shortcode;
		}
		return $shortcodes_list;
	}

    /**
     * wcj_shortcode.
     */
	function wcj_shortcode( $atts, $content, $shortcode ) {

		// Init
		if ( empty( $atts ) ) $atts = array();
		$global_defaults = array(
			'before'        => '',
			'after'         => '',
			'visibility'    => '',
		);
		$atts = array_merge( $global_defaults, $atts );

		// Check if privileges are ok
		if ( 'admin' === $atts['visibility'] && ! is_super_admin() ) return '';

		// Add child class specific atts
		$atts = $this->add_extra_atts( $atts );

		// Check for required atts
		if ( false === ( $atts = $this->init_atts( $atts ) ) ) return '';

		// Run the shortcode function
		$shortcode_function = $shortcode;
		if ( '' !== ( $result = $this->$shortcode_function( $atts, $content ) ) )
			return $atts['before'] . $result . $atts['after'];
		return '';
	}
}

endif;
