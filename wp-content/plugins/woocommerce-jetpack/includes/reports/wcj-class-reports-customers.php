<?php
/**
 * WooCommerce Jetpack Customers Reports
 *
 * The WooCommerce Jetpack Customers Reports class.
 *
 * @class 		WCJ_Reports_Customers
 * @version		1.0.0
 * @category	Class
 * @author 		Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_Reports_Customers' ) ) :

class WCJ_Reports_Customers {

	/** @var array Ccountry groups (sets). */
	public $country_sets;	

	/**
	 * Constructor.
	 */
	public function __construct( $args = null ) {
		$this->country_sets = ( isset( $args['group_countries'] ) && 'yes' === $args['group_countries'] ) ? 
			include( 'countries/wcj-country-sets.php' ) : array();	
	}
	
	/**
	 * get_report function.
	 */
	public function get_report() {
		$customers = get_users( 'role=customer' );
		$total_customers = count( $customers );	
		if ( $total_customers < 1 )
			return '<h5>' . __( 'No customers found.', 'woocommerce-jetpack' ) . '</h5>';		
		$country_counter = $this->get_data( $customers );
		$html = $this->get_html( $country_counter, $total_customers );
		return $html;
	}
	
	
	/**
	 * get_data function.
	 */
	public function get_data( $customers ) {		
		foreach ( $customers as $customer ) {
			// Get country (billing or shipping)
			$user_meta = get_user_meta( $customer->ID );
			$billing_country = isset( $user_meta['billing_country'][0] ) ? $user_meta['billing_country'][0] : '';
			$shipping_country = isset( $user_meta['shipping_country'][0] ) ? $user_meta['shipping_country'][0] : '';
			$customer_country = ( '' == $billing_country ) ? $shipping_country : $billing_country;
			// If available - change to country set instead
			foreach ( $this->country_sets as $id => $countries ) {
				if ( in_array( $customer_country, $countries ) ) {
					$customer_country = $id;
					break;
				}
			}			
			// N/A
			if ( '' == $customer_country )
				$customer_country = 'Non Available';
			// Counter
			$country_counter[ $customer_country ]++;	
		}	
		arsort( $country_counter );
		return $country_counter;
	}
	
	/**
	 * get_data function.
	 */
	public function get_html( $data, $total_customers ) {		
		$html = '<h5>' . __( 'Total customers', 'woocommerce-jetpack' ) . ': ' . $total_customers . '</h5>';
		$html .= '<table class="widefat" style="width:30% !important;"><tbody>';
		$html .= '<tr>';
		$html .= '<th></th>';//'<th>' . __( 'Country Flag', 'woocommerce-jetpack' ) . '</th>';
		$html .= '<th>' . __( 'Country Code', 'woocommerce-jetpack' ) . '</th>';
		$html .= '<th>' . __( 'Customers Count', 'woocommerce-jetpack' ) . '</th>';
		$html .= '<th>' . __( 'Percent of total', 'woocommerce-jetpack' ) . '</th>';
		$html .= '<th></th>';
		$html .= '</tr>';
		$i = 0;
		foreach ( $data as $country_code => $counter ) {
			$html .= '<tr>';
			$html .= '<td>' . ++$i . '</td>';			
			$html .= '<td>' . $country_code . '</td>';
			$html .= '<td>' . $counter . '</td>';
			$html .= ( 0 != $total_customers ) ? '<td>' . number_format( ( $counter / $total_customers ) * 100, 2 ) . '%' . '</td>' : '<td></td>';
			$html .= ( 2 == strlen( $country_code ) ) ? '<td>' . '<img src="' . plugins_url() . '/' . 'woocommerce-jetpack' . '/assets/images/flag-icons/' . strtolower( $country_code ) . '.png" title="' . wcj_get_country_name_by_code( $country_code ) . '">' . ' ' . wcj_get_country_name_by_code( $country_code ) . '</td>' : '<td></td>';
			//$html .= ( 2 == strlen( $country_code ) ) ? '<td>' . '<img src="' . plugin_dir_url ( __FILE__ ) . 'assets/images/flag-icons/' . strtolower( $country_code ) . '.png' . '">' . '</td>' : '<td></td>';
			$html .= '</tr>';
		}
		$html .= '</tbody></table>';
		return $html;		
	}	
}

endif;