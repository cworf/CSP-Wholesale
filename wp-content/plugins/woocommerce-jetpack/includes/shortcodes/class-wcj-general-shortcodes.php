<?php
/**
 * WooCommerce Jetpack General Shortcodes
 *
 * The WooCommerce Jetpack General Shortcodes class.
 *
 * @class    WCJ_General_Shortcodes
 * @version  1.0.0
 * @category Class
 * @author   Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_General_Shortcodes' ) ) :

class WCJ_General_Shortcodes extends WCJ_Shortcodes {

    /**
     * Constructor.
     */
    public function __construct() {

		$this->the_shortcodes = array(
			'wcj_current_date',
			//'wcj_image',
		);

		$this->the_atts = array(
			'date_format' => get_option( 'date_format' ),
			/*'url'         => '',
			'class'       => '',
			'width'       => '',
			'height'      => '',*/
		);

		parent::__construct();

    }

    /**
     * wcj_current_date.
     */	
	function wcj_current_date( $atts ) {
		return date_i18n( $atts['date_format'] );
	}

    /**
     * wcj_image.
     */	
	/*function wcj_image( $atts ) {
		return '<img src="' . $atts['url'] . '" class="' . $atts['class'] . '" width="' . $atts['width'] . '" height="' . $atts['height'] . '">';
	}*/
}

endif;

return new WCJ_General_Shortcodes();
