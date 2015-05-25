<?php
 /**
 * Go - Responsive Pricing & Compare Tables
 *
 * @package   Go - Responsive Pricing & Compare Tables
 * @author    Granth <granthweb@gmail.com>
 * @link      http://granthweb.com
 * @copyright 2014 Granth
 */

/**
 * Visual Composer Extend class
 */


/* Prevent direct call */
if ( ! defined( 'WPINC' ) ) { die; }

class GW_GoPricing_VCExtend {

	protected static $instance = null;

	public function __construct() {
         add_action( 'init', array( $this, 'integrateWithVC' ) );
    }
 
	/**
	 * Return an instance of this class
	 */
	 
	public static function get_instance() {
		if ( self::$instance == null ) {
			self::$instance = new self;
		}
		
		return self::$instance;
	}
 
	/**
	 * Add to Visual Composer
	 */ 
 
    public function integrateWithVC() {

		/* Get pricing data */
		$pricing_tables = get_option( GW_GO_PREFIX . 'tables', array() );

		if ( !empty( $pricing_tables ) ) {
			foreach ( $pricing_tables as $pricing_table ) {
				$dropdown_data[$pricing_table['table-name']] = $pricing_table['table-id'];
			}
		} else {
			$dropdown_data[0] = __('No table found!', GW_GO_TEXTDOMAN );
		}
		
		if ( function_exists( 'vc_map' ) ) {
		
			vc_map( array (
				'name' => __('Go Pricing', GW_GO_TEXTDOMAN ),
				'description' => __( 'Awesome responsive pricing tables', GW_GO_TEXTDOMAN ),
				'base' => 'go_pricing',
				'category' => __( 'Content', GW_GO_TEXTDOMAN ),	
				'class' => '',
				'controls' => 'full',
				'icon' => plugin_dir_url( __FILE__ ) . 'assets/go_pricing_32x32.png',
				'params' => array(
					array(
						"type" => "dropdown",
						'heading' => __( 'Table Name', GW_GO_TEXTDOMAN ),
						'param_name' => 'id',
						'value' => $dropdown_data,
						'description' => __('Select Pricing Table', GW_GO_TEXTDOMAN ),
						'admin_label' => true
					)
				)
			) );

		}
				
    }

}

/* Init */
GW_GoPricing_VCExtend::get_instance();
