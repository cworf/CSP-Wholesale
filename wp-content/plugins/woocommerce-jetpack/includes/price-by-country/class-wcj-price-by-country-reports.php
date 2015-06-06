<?php
/**
 * WooCommerce Jetpack Price By Country Reports
 *
 * The WooCommerce Jetpack Price By Country Reports class.
 *
 * @class    WCJ_Price_By_Country_Reports
 * @version  1.0.0
 * @category Class
 * @author   Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_Price_By_Country_Reports' ) ) :

class WCJ_Price_By_Country_Reports {

    /**
     * Constructor.
     */
    function __construct() {

		add_filter( 'woocommerce_reports_get_order_report_data_args', 	array( $this, 'filter_reports'), 						PHP_INT_MAX, 		1 );
		add_filter( 'woocommerce_currency_symbol', 						array( $this, 'change_currency_symbol_reports'), 		PHP_INT_MAX, 		2 );
		add_action( 'admin_bar_menu', 									array( $this, 'add_reports_currency_to_admin_bar' ), 	PHP_INT_MAX );
    }
	
    /**
     * add_reports_currency_to_admin_bar.
     */	
	function add_reports_currency_to_admin_bar( $wp_admin_bar ) {

		if ( isset( $_GET['page'] ) && 'wc-reports' === $_GET['page'] ) {
			$the_current_code = isset( $_GET['currency'] ) ? $_GET['currency'] : get_woocommerce_currency();
			$parent = 'reports_currency_select';
			$args = array(
				'parent' => false,
				'id' => $parent,
				'title' => __( 'Reports currency:', 'woocommerce-jetpack' ) . ' ' . $the_current_code,
				'href'  => false,
				'meta' => array( 'title' => __( 'Show reports only in', 'woocommerce-jetpack' ) . ' ' . $the_current_code, ),
			);

			$wp_admin_bar->add_node( $args );

			$currency_symbols = array();
			$currency_symbols[ $the_current_code ] = '';
			$currency_symbols[ get_woocommerce_currency() ] = '';
			for ( $i = 1; $i <= apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_price_by_country_total_groups_number', 1 ) ); $i++ ) {
				$currency_symbols[ get_option( 'wcj_price_by_country_exchange_rate_currency_group_' . $i ) ] = '';
			}
			$this->reports_currency_symbols = $currency_symbols;

			foreach ( $this->reports_currency_symbols as $code => $symbol ) {
				//if ( $code === $the_current_code )
				//	continue;
				$args = array(
					'parent' => $parent,
					'id' => $parent . '_' . $code,
					'title' => $code,// . ' ' . $symbol,
					'href'  => add_query_arg( 'currency', $code),
					'meta' => array( 'title' => __( 'Show reports only in', 'woocommerce-jetpack' ) . ' ' . $code, ),
				);

				$wp_admin_bar->add_node( $args );
			}
		}
	}

	/**
	 * change_currency_symbol_reports.
	 */
	function change_currency_symbol_reports( $currency_symbol, $currency ) {
		if ( isset( $_GET['page'] ) && 'wc-reports' === $_GET['page'] ) {
			if ( isset( $_GET['currency'] ) ) {
				if ( isset( $this->currency_symbols[ strtoupper( $_GET['currency'] ) ] ) ) {
					return $this->currency_symbols[ strtoupper( $_GET['currency'] ) ];
				}
			}
		}
		return $currency_symbol;
	}

	/**
	 * filter_reports.
	 */
	function filter_reports( $args ) {
		$args['where_meta'] = array(
			array(
				'meta_key' 	 => '_order_currency',
				'meta_value' => isset( $_GET['currency'] ) ? $_GET['currency'] : get_woocommerce_currency(),
				'operator' => '=',
			),
		);
		return $args;
	}	
}

endif;

return new WCJ_Price_By_Country_Reports();



	