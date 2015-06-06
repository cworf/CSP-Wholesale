<?php
/**
 * WooCommerce Jetpack Sorting
 *
 * The WooCommerce Jetpack Sorting class.
 *
 * @class 		WCJ_Sorting
 * @version		1.2.0
 * @category	Class
 * @author 		Algoritmika Ltd.
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_Sorting' ) ) :

class WCJ_Sorting {
	
	/**
	 * WCJ_Sorting Constructor.
	 * @access public
	 */
	public function __construct() {
	
		// HOOKS
		
		// Main hooks
		if ( 'yes' === get_option( 'wcj_sorting_enabled' ) ) {
		
			if ( 'yes' === get_option( 'wcj_more_sorting_enabled' ) ) {
				// Sorting
				add_filter( 'woocommerce_get_catalog_ordering_args', array( $this, 'custom_woocommerce_get_catalog_ordering_args' ), 100 );
				// Front end
				add_filter( 'woocommerce_catalog_orderby', array( $this, 'custom_woocommerce_catalog_orderby' ), 100 );
				// Back end (default sorting)
				add_filter( 'woocommerce_default_catalog_orderby_options', array( $this, 'custom_woocommerce_catalog_orderby' ), 100 );
			}	
			
			if ( 'yes' === get_option( 'wcj_sorting_remove_all_enabled' ) ) {
				// Remove sorting
				add_action( apply_filters( 'wcj_get_option_filter', 'wcj_empty_action', 'init' ), array( $this, 'remove_sorting' ), 100 );
			}			
			// Settings
			// Add 'Remove All Sorting' checkbox to WooCommerce > Settings > Products							
			add_filter( 'woocommerce_product_settings', array( $this, 'add_remove_sorting_checkbox' ), 100 );
		}
		
		// Settings hooks
		// Add section to WooCommerce > Settings > Jetpack
		add_filter( 'wcj_settings_sections', array( $this, 'settings_section' ) );
		// Add the settings
		add_filter( 'wcj_settings_sorting', array( $this, 'get_settings' ), 100 );
		// Add Enable option to Jetpack Settings Dashboard
		add_filter( 'wcj_features_status', array( $this, 'add_enabled_option' ), 100 );
	}

	/**
	 * add_enabled_option.
	 */
	public function add_enabled_option( $settings ) {
	
		$all_settings = $this->get_settings();
		$settings[] = $all_settings[1];
		
		return $settings;
	}	
	
    /**
     * Unlocks - Sorting - remove_sorting.
     */
	public function remove_sorting() {
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
	}	
	
	/*
	 * Add Remove All Sorting checkbox to WooCommerce > Settings > Products.
	 */
	function add_remove_sorting_checkbox( $settings ) {

		$updated_settings = array();

		foreach ( $settings as $section ) {	  
			
			if ( isset( $section['id'] ) && 'woocommerce_cart_redirect_after_add' == $section['id'] ) {

				$updated_settings[] = array(
					'title' 	=> __( 'WooJetpack: Remove All Sorting', 'woocommerce-jetpack' ),					
					'id'		=> 'wcj_sorting_remove_all_enabled',
					'type'		=> 'checkbox',
					'default'	=> 'no',
					'desc'		=> __( 'Completely remove sorting from the shop front end', 'woocommerce-jetpack' ),
					'custom_attributes'	=> apply_filters( 'get_wc_jetpack_plus_message', '', 'disabled' ),
					'desc_tip'	=> apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
				);
			}
			
			$updated_settings[] = $section;
		}
	  
		return $updated_settings;
	}
	
	/*
	 * Custom Init - remove all sorting action
	 *	
	function custom_init() {
	
		if ( get_option( 'wcj_sorting_remove_all_enabled' ) ) 
			do_action( 'wcj_sorting_remove_action' );
	}		
	
	/*
	 * Add new sorting options to Front End and to Back End (in WooCommerce > Settings > Products > Default Product Sorting).
	 */
	function custom_woocommerce_catalog_orderby( $sortby ) {
		
		//if ( get_option( 'wcj_sorting_by_name_asc_enabled' ) == 'yes' )
		if ( '' != get_option( 'wcj_sorting_by_name_asc_text' ) )
			$sortby['title_asc'] = get_option( 'wcj_sorting_by_name_asc_text' );
			
		//if ( get_option( 'wcj_sorting_by_name_desc_enabled' ) == 'yes' )
		if ( '' != get_option( 'wcj_sorting_by_name_desc_text' ) )
			$sortby['title_desc'] = get_option( 'wcj_sorting_by_name_desc_text' );

		//if ( get_option( 'wcj_sorting_by_sku_asc_enabled' ) == 'yes' )
		if ( '' != get_option( 'wcj_sorting_by_sku_asc_text' ) )
			$sortby['sku_asc'] = get_option( 'wcj_sorting_by_sku_asc_text' );
			
		//if ( 'yes' == get_option( 'wcj_sorting_by_sku_desc_enabled' ) )
		if ( '' != get_option( 'wcj_sorting_by_sku_desc_text' ) )
			$sortby['sku_desc'] = get_option( 'wcj_sorting_by_sku_desc_text' );
			
		if ( '' != get_option( 'wcj_sorting_by_stock_quantity_asc_text' ) )
			$sortby['stock_quantity_asc'] = get_option( 'wcj_sorting_by_stock_quantity_asc_text' );	

		if ( '' != get_option( 'wcj_sorting_by_stock_quantity_desc_text' ) )
			$sortby['stock_quantity_desc'] = get_option( 'wcj_sorting_by_stock_quantity_desc_text' );				
			
		return $sortby;
	}
	
	/*
	 * Add new sorting options to WooCommerce sorting.
	 */
	function custom_woocommerce_get_catalog_ordering_args( $args ) {
	
		global $woocommerce;
		// Get ordering from query string unless defined
		$orderby_value = isset( $_GET['orderby'] ) ? woocommerce_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
		// Get order + orderby args from string
		$orderby_value = explode( '-', $orderby_value );
		$orderby       = esc_attr( $orderby_value[0] );

		switch ( $orderby ) :
			case 'title_asc':
				$args['orderby'] = 'title';
				$args['order'] = 'asc';
				$args['meta_key'] = '';
			break;			
			case 'title_desc':
				$args['orderby'] = 'title';
				$args['order'] = 'desc';
				$args['meta_key'] = '';
			break;
			case 'sku_asc':
				$args['orderby'] = 'meta_value';
				$args['order'] = 'asc';
				$args['meta_key'] = '_sku';
			break;			
			case 'sku_desc':
				$args['orderby'] = 'meta_value';
				$args['order'] = 'desc';
				$args['meta_key'] = '_sku';
			break;
			case 'stock_quantity_asc':
				$args['orderby'] = 'meta_value';
				$args['order'] = 'asc';
				$args['meta_key'] = '_stock';
			break;			
			case 'stock_quantity_desc':
				$args['orderby'] = 'meta_value';
				$args['order'] = 'desc';
				$args['meta_key'] = '_stock';
			break;			
		endswitch;
			
		return $args;				
	}	
	
	/*
	 * Add the settings.
	 */
	function get_settings() {

		$settings = array(
		
			array( 'title' 	=> __( 'Sorting Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => '', 'id' => 'wcj_sorting_options' ),
			
			array(
				'title' 	=> __( 'Sorting', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Enable the Sorting feature', 'woocommerce-jetpack' ),
				'desc_tip'	=> __( 'Add more sorting options or remove all sorting including default.', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_sorting_enabled',
				'default'	=> 'yes',
				'type' 		=> 'checkbox'
			),
			
			array( 'type' 	=> 'sectionend', 'id' => 'wcj_sorting_options' ),
		
			array( 'title' 	=> __( 'Remove All Sorting', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => '', 'id' => 'wcj_remove_all_sorting_options' ),
			
			array(
				'title' 	=> __( 'Remove All Sorting', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Remove all sorting (including WooCommerce default)', 'woocommerce-jetpack' ),
				'desc_tip' 	=> apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
				'id' 		=> 'wcj_sorting_remove_all_enabled',
				'default'	=> 'no',
				'type' 		=> 'checkbox',
				'custom_attributes'
							=> apply_filters( 'get_wc_jetpack_plus_message', '', 'disabled' ),
			),

			array( 'type' 	=> 'sectionend', 'id' => 'wcj_remove_all_sorting_options' ),			

			array( 'title'	=> __( 'Add More Sorting', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => '', 'id' => 'wcj_more_sorting_options' ),			
			
			array(
				'title' 	=> __( 'Add More Sorting', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Enable', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_more_sorting_enabled',
				'default'	=> 'yes',
				'type' 		=> 'checkbox'
			),			

			array(
				'title' 	=> __( 'Sort by Name', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Default: ', 'woocommerce-jetpack' ) . __( 'Sort by title: A to Z', 'woocommerce-jetpack' ),				
				'desc_tip' 	=> __( 'Text to show on frontend. Leave blank to disable.', 'woocommerce-jetpack' ),	
				'id' 		=> 'wcj_sorting_by_name_asc_text',
				'default'	=> __( 'Sort by title: A to Z', 'woocommerce-jetpack' ),
				'type' 		=> 'text',
				'css'		=> 'min-width:300px;',
			),

			array(
				'title' 	=> '',//'title' 	=> __( 'Sort by Name - Desc', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Default: ', 'woocommerce-jetpack' ) . __( 'Sort by title: Z to A', 'woocommerce-jetpack' ),
				'desc_tip' 	=> __( 'Text to show on frontend. Leave blank to disable.', 'woocommerce-jetpack' ),	
				'id' 		=> 'wcj_sorting_by_name_desc_text',
				'default'	=> __( 'Sort by title: Z to A', 'woocommerce-jetpack' ),
				'type' 		=> 'text',
				'css'		=> 'min-width:300px;',
			),
			
			array(
				'title' 	=> __( 'Sort by SKU', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Default: ', 'woocommerce-jetpack' ) . __( 'Sort by SKU: low to high', 'woocommerce-jetpack' ),
				'desc_tip' 	=> __( 'Text to show on frontend. Leave blank to disable.', 'woocommerce-jetpack' ),	
				'id' 		=> 'wcj_sorting_by_sku_asc_text',
				'default'	=> __( 'Sort by SKU: low to high', 'woocommerce-jetpack' ),
				'type' 		=> 'text',
				'css'		=> 'min-width:300px;',
			),
			
			array(
				'title' 	=> '',//'title' 	=> __( 'Sort by SKU - Desc', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Default: ', 'woocommerce-jetpack' ) . __( 'Sort by SKU: high to low', 'woocommerce-jetpack' ),
				'desc_tip' 	=> __( 'Text to show on frontend. Leave blank to disable.', 'woocommerce-jetpack' ),	
				'id' 		=> 'wcj_sorting_by_sku_desc_text',
				'default'	=> __( 'Sort by SKU: high to low', 'woocommerce-jetpack' ),
				'type' 		=> 'text',
				'css'		=> 'min-width:300px;',
			),
			
			array(
				'title' 	=> __( 'Sort by stock quantity', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Default: ', 'woocommerce-jetpack' ) . __( 'Sort by stock quantity: low to high', 'woocommerce-jetpack' ),
				'desc_tip' 	=> __( 'Text to show on frontend. Leave blank to disable.', 'woocommerce-jetpack' ),				
				'id' 		=> 'wcj_sorting_by_stock_quantity_asc_text',
				'default'	=> __( 'Sort by stock quantity: low to high', 'woocommerce-jetpack' ),
				'type' 		=> 'text',
				'css'		=> 'min-width:300px;',
			),
			
			array(
				'title' 	=> '',
				'desc' 		=> __( 'Default: ', 'woocommerce-jetpack' ) . __( 'Sort by stock quantity: high to low', 'woocommerce-jetpack' ),
				'desc_tip' 	=> __( 'Text to show on frontend. Leave blank to disable.', 'woocommerce-jetpack' ),				
				'id' 		=> 'wcj_sorting_by_stock_quantity_desc_text',
				'default'	=> __( 'Sort by stock quantity: high to low', 'woocommerce-jetpack' ),
				'type' 		=> 'text',
				'css'		=> 'min-width:300px;',
			),		

			array( 'type' => 'sectionend', 'id' => 'wcj_more_sorting_options' ),
		);
		
		return $settings;
	}
	
	/*
	 * Add settings section to WooCommerce > Settings > Jetpack.
	 */	
	function settings_section( $sections ) {	
		$sections['sorting'] = __( 'Sorting', 'woocommerce-jetpack' );		
		return $sections;
	}	
}

endif;

return new WCJ_Sorting();
