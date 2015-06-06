<?php
/**
 * WooCommerce Jetpack Price by Country Core
 *
 * The WooCommerce Jetpack Price by Country Core class.
 *
 * @class    WCJ_Price_by_Country_Core
 * @version  2.1.3
 * @category Class
 * @author   Algoritmika Ltd.
 */
 
if ( ! defined( 'ABSPATH' ) ) exit;
 
if ( ! class_exists( 'WCJ_Price_by_Country_Core' ) ) :
 
class WCJ_Price_by_Country_Core {
    
    /**
     * Constructor.
     */
    public function __construct() {
	
		//$this->country_by_ip = include_once( 'class-wcj-country-by-ip.php' );
		$this->customer_country_group_id = null;
	
		add_action( 'woocommerce_loaded', array( $this, 'add_hooks' ) );
    }    
	
	/**
	 * add_hooks.
	 */
	function add_hooks() {		
		// Price hooks
		
		add_filter( 'woocommerce_get_price', 				array( $this, 'change_price_by_country' ),				PHP_INT_MAX, 2 );
		add_filter( 'woocommerce_get_sale_price', 			array( $this, 'change_price_by_country' ), 				PHP_INT_MAX, 2 );
		add_filter( 'woocommerce_get_regular_price', 		array( $this, 'change_price_by_country' ), 				PHP_INT_MAX, 2 );
//		add_filter( 'booking_form_calculated_booking_cost',	array( $this, 'change_price_by_country' ), 				PHP_INT_MAX );
		add_filter( 'woocommerce_get_price_html', 			array( $this, 'fix_variable_product_price_on_sale' ), 	10 , 		 2 );
		
		// Currency hooks
		add_filter( 'woocommerce_currency_symbol', 			array( $this, 'change_currency_symbol' ), 				PHP_INT_MAX, 2 );
		add_filter( 'woocommerce_currency', 				array( $this, 'change_currency_code' ),					PHP_INT_MAX, 1 );		

		add_shortcode( 'wcj_debug_price_by_country', 		array( $this, 'get_debug_info' ) );	
	}
	
	/**
	 * get_debug_info.
	 */
	function get_debug_info( $args ) {	
		$html = '';
		/*$html .= '<p>';
		$html .= __( 'internal: ', 'woocommerce-jetpack' ) . WCJ()->country_by_ip->get_user_country_by_ip( 'internal' );
		$html .= '</p>';
		$html .= '<p>';
		$html .= __( 'external: ', 'woocommerce-jetpack' ) . WCJ()->country_by_ip->get_user_country_by_ip( 'external' );
		$html .= '</p>';*/
		if ( 'yes' === get_option( 'wcj_price_by_country_local_enabled' ) ) {
			$html .= '<p>';
			$html .= __( 'Price by Country on per Product Basis is enabled.', 'woocommerce-jetpack' );
			$html .= '</p>';
		}
		
		$data = array();
		$data[] = array( '#', __( 'Countries', 'woocommerce-jetpack' ), __( 'Focus Country', 'woocommerce-jetpack' ), __( 'Regular Price', 'woocommerce-jetpack' ), __( 'Sale Price', 'woocommerce-jetpack' ) );
		global $product;
		for ( $i = 1; $i <= apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_price_by_country_total_groups_number', 1 ) ); $i++ ) {	
		
			$row = array();			
			
			$row[] = $i;
			
			$country_exchange_rate_group = get_option( 'wcj_price_by_country_exchange_rate_countries_group_' . $i );			
			$country_exchange_rate_group = str_replace( ' ', '', $country_exchange_rate_group );
			$row[] = $country_exchange_rate_group;			
						
			$country_exchange_rate_group = explode( ',', $country_exchange_rate_group );
			$_GET['country'] = $country_exchange_rate_group[0];
			$row[] = $country_exchange_rate_group[0];			
			$currency_code = wcj_get_currency_symbol( get_option( 'wcj_price_by_country_exchange_rate_currency_group_' . $i ) );
			$row[] = $product->get_regular_price() . ' ' . $currency_code;
			$row[] = $product->get_sale_price() . ' ' . $currency_code;			
			
			$data[] = $row;
		}		
		//$html .= wcj_get_table_html( $data, '', false );
		$html = wcj_get_table_html( $data, array( 'table_heading_type' => 'vertical', ) );
		return $html;
	}
	
	/**
	 * fix_variable_product_price_on_sale.
	 */
	public function fix_variable_product_price_on_sale( $price, $product ) {
		if ( $product->is_type( 'variable' ) ) {
			if ( ! $product->is_on_sale() ) {
				$start_position = strpos( $price, '<del>' );
				$length = strpos( $price, '</del>' ) - $start_position;
				// Fixing the price, i.e. removing the sale tags
				return substr_replace( $price, '', $start_position, $length );
			}
		}
		// No changes
		return $price;
	}
	
	/**
	 * get_customer_country_group_id.
	 */
	public function get_customer_country_group_id() {
	
		// We already know the group - nothing to calculate - return group
//		if ( null != $this->customer_country_group_id && $this->customer_country_group_id > 0 )
//			return $this->customer_country_group_id;
		
		// We've already tried - no country was detected, no need to try again
		if ( -1 === $this->customer_country_group_id )
			return null;
			
			
		if ( isset( $_GET['country'] ) && '' != $_GET['country'] && is_super_admin() ) {		
			$country = $_GET['country'];			
		} else {				
			// Get the country by IP
			$location = WC_Geolocation::geolocate_ip();
			// Base fallback
			if ( empty( $location['country'] ) ) {
				$location = wc_format_country_state_string( apply_filters( 'woocommerce_customer_default_location', get_option( 'woocommerce_default_country' ) ) );
			}
			//wcj_log( $location );			
			$country = ( isset( $location['country'] ) ) ? $location['country'] : null; 
		}		
		
		if ( null === $country ) {
			$this->customer_country_group_id = -1;
			return null;
		}
		

		// Get the country group id - go through all the groups, first found group is returned
		for ( $i = 1; $i <= apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_price_by_country_total_groups_number', 1 ) ); $i++ ) {		
			$country_exchange_rate_group = get_option( 'wcj_price_by_country_exchange_rate_countries_group_' . $i );
			$country_exchange_rate_group = str_replace( ' ', '', $country_exchange_rate_group );
			$country_exchange_rate_group = explode( ',', $country_exchange_rate_group );
			if ( in_array( $country, $country_exchange_rate_group ) ) {				
				$this->customer_country_group_id = $i;
				//wcj_log( 'customer_country_group_id=' . $this->customer_country_group_id );
				return $i;
			}
		}
		// No country group found
		
		$this->customer_country_group_id = -1;
		return null;
	}

	/**
	 * change_currency_symbol.
	 */
	public function change_currency_symbol( $currency_symbol, $currency ) {
		if ( null != ( $group_id = $this->get_customer_country_group_id() ) ) {			
			$country_currency_code = get_option( 'wcj_price_by_country_exchange_rate_currency_group_' . $group_id );
			//wcj_log( 'country_currency_code=' . $country_currency_code );
			if ( '' != $country_currency_code )
				return wcj_get_currency_symbol( $country_currency_code );
				//return $this->currency_symbols[ $country_currency_code ];
		}
		return $currency_symbol;
	}
	
	/**
	 * change_currency_code.
	 */
	public function change_currency_code( $currency ) {
		if ( null != ( $group_id = $this->get_customer_country_group_id() ) ) {
			$country_currency_code = get_option( 'wcj_price_by_country_exchange_rate_currency_group_' . $group_id );
			//wcj_log( 'country_currency_code=' . $country_currency_code );
			if ( '' != $country_currency_code )
				return $country_currency_code;
		}
		return $currency;
	}

	/**
	 * change_price_by_country.
	 */
	public function change_price_by_country( $price, $product ) {
	
		$the_product_id = ( isset( $product->variation_id ) ) ? $product->variation_id : $product->id;
	
		if ( null != ( $group_id = $this->get_customer_country_group_id() ) ) {	
			
			$is_price_modified = false;		
		
			if ( 'yes' === get_option( 'wcj_price_by_country_local_enabled' ) ) {			
				// Per product
				$meta_box_id = 'price_by_country';
				$scope = 'local';
				
				$meta_id = '_' . 'wcj_' . $meta_box_id . '_make_empty_price_' . $scope . '_' . $group_id;
				if ( 'on' === get_post_meta( $the_product_id, $meta_id, true ) ) {
					return '';
				}				
				
				if ( 'woocommerce_get_price' == current_filter() ) {
					
					$regular_or_sale = '_regular_price_';
					$meta_id = '_' . 'wcj_' . $meta_box_id . $regular_or_sale . $scope . '_' . $group_id;
					$regular_price = get_post_meta( $the_product_id, $meta_id, true );					
					
					$regular_or_sale = '_sale_price_';
					$meta_id = '_' . 'wcj_' . $meta_box_id . $regular_or_sale . $scope . '_' . $group_id;
					$sale_price = get_post_meta( $the_product_id, $meta_id, true );
					
					if ( ! empty( $sale_price ) && $sale_price < $regular_price )
						$price_by_country = $sale_price;
					else
						$price_by_country = $regular_price;
					
				}
				elseif ( 'woocommerce_get_regular_price' == current_filter() || 'woocommerce_get_sale_price' == current_filter() ) {
					$regular_or_sale = ( 'woocommerce_get_regular_price' == current_filter() ) ? '_regular_price_' : '_sale_price_';
					$meta_id = '_' . 'wcj_' . $meta_box_id . $regular_or_sale . $scope . '_' . $group_id;
					$price_by_country = get_post_meta( $the_product_id, $meta_id, true );
				}
				
				if ( '' != $price_by_country ) {
					$modified_price = $price_by_country;
					$is_price_modified = true;
				}
			}
			
			if ( ! $is_price_modified ) {				
				if ( 'yes' === get_option( 'wcj_price_by_country_make_empty_price_group_' . $group_id, 1 ) ) {
					return '';
				}
			}
			
			if ( ! $is_price_modified ) {		
				// Globally
				$country_exchange_rate = get_option( 'wcj_price_by_country_exchange_rate_group_' . $group_id, 1 );
				if ( 1 != $country_exchange_rate ) {
					$modified_price = $price * $country_exchange_rate;
					$is_price_modified = true;
				}
			}
			
			if ( $is_price_modified ) {
				$rounding = get_option( 'wcj_price_by_country_rounding', 'none' );				
				$precision = get_option( 'woocommerce_price_num_decimals', 2 );
				switch ( $rounding ) {
					case 'none':
						//return ( $modified_price );
						return round( $modified_price, $precision );
					case 'round':						
						return round( $modified_price );
					case 'floor':
						return floor( $modified_price );
					case 'ceil':
						return ceil( $modified_price );					
				}
			}
		}
		// No changes
		return $price;
	}	
}
 
endif;
 
return new WCJ_Price_by_Country_Core();