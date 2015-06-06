<?php
/**
 * WooCommerce Jetpack Reports
 *
 * The WooCommerce Jetpack Reports class.
 *
 * @class 		WCJ_Reports
 * @version		2.0.0
 * @category	Class
 * @author 		Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_Reports' ) ) :

class WCJ_Reports {

	/** @var string Report ID. */
	public $report_id;	

	/** @var int Stock reports - range in days. */
	public $range_days;	

	/** @var string: yes/no Customers reports - group countries. */
	public $group_countries;	

	/**
	 * Constructor.
	 */
	public function __construct() {
		
		// Main hooks
		if ( 'yes' === get_option( 'wcj_reports_enabled' ) ) {
			if ( is_admin() ) {
				add_filter( 'woocommerce_admin_reports', 		array( $this, 'add_customers_by_country_report' ) );	
				add_filter( 'woocommerce_admin_reports', 		array( $this, 'add_stock_reports' ) );	
				add_action( 'init',						 		array( $this, 'catch_arguments' ) );	
				
				include_once( 'reports/wcj-class-reports-customers.php' );
				include_once( 'reports/wcj-class-reports-stock.php' );
				
				add_action( 'admin_bar_menu', 					array( $this, 'add_custom_order_reports_ranges_to_admin_bar' ), PHP_INT_MAX );
			}
		}
		
		// Settings hooks
		add_filter( 'wcj_settings_sections', 					array( $this, 'settings_section' ) ); 			// Add section to WooCommerce > Settings > Jetpack
		add_filter( 'wcj_settings_reports', 					array( $this, 'get_settings' ),       100 );    // Add the settings
		add_filter( 'wcj_features_status', 						array( $this, 'add_enabled_option' ), 100 );	// Add Enable option to Jetpack Settings Dashboard		
	}
	
	
	/**
	 * add_custom_order_reports_ranges_to_admin_bar.
	 */	
	public function add_custom_order_reports_ranges_to_admin_bar( $wp_admin_bar ) {
		$is_reports = ( isset( $_GET['page'] ) && 'wc-reports' === $_GET['page'] ) ? true : false;
		$is_orders_reports = ( isset( $_GET['tab'] ) && 'orders' === $_GET['tab'] || ! isset( $_GET['tab'] ) ) ? true : false;
		if ( $is_reports && $is_orders_reports ) {
		
			$parent = 'reports_orders_more_ranges';
			$args = array(
				'parent' => false, 
				'id' => $parent,
				'title' => __( 'WooJetpack: More Ranges', 'woocommerce-jetpack' ),
				'href'  => false,
				'meta' => array( 'title' => __( 'Select Range', 'woocommerce-jetpack' ), ),
			);			
			$wp_admin_bar->add_node( $args );

			$nodes = array(
				array(
					'parent' => $parent, 
					'id' => $parent . '_' . 'last_14_days',
					'title' => __( 'Last 14 Days', 'woocommerce-jetpack' ),
					'href'  => add_query_arg( array( 'range' => 'custom', 'start_date' => date( 'Y-m-d', strtotime( '-14 days' ) ), 'end_date' => date( 'Y-m-d' ), ) ),
					'meta' => array( 'title' => __( 'Last 14 Days', 'woocommerce-jetpack' ), ),
				),			
				array(
					'parent' => $parent, 
					'id' => $parent . '_' . 'last_30_days',
					'title' => __( 'Last 30 Days', 'woocommerce-jetpack' ),
					'href'  => add_query_arg( array( 'range' => 'custom', 'start_date' => date( 'Y-m-d', strtotime( '-30 days' ) ), 'end_date' => date( 'Y-m-d' ), ) ),
					'meta' => array( 'title' => __( 'Last 30 Days', 'woocommerce-jetpack' ), ),
				),			
				array(
					'parent' => $parent, 
					'id' => $parent . '_' . 'last_3_months',
					'title' => __( 'Last 3 Months', 'woocommerce-jetpack' ),
					'href'  => add_query_arg( array( 'range' => 'custom', 'start_date' => date( 'Y-m-d', strtotime( '-3 months' ) ), 'end_date' => date( 'Y-m-d' ), ) ),
					'meta' => array( 'title' => __( 'Last 3 Months', 'woocommerce-jetpack' ), ),
				),			
				array(
					'parent' => $parent, 
					'id' => $parent . '_' . 'last_6_months',
					'title' => __( 'Last 6 Months', 'woocommerce-jetpack' ),
					'href'  => add_query_arg( array( 'range' => 'custom', 'start_date' => date( 'Y-m-d', strtotime( '-6 months' ) ), 'end_date' => date( 'Y-m-d' ), ) ),
					'meta' => array( 'title' => __( 'Last 6 Months', 'woocommerce-jetpack' ), ),
				),
				array(
					'parent' => $parent, 
					'id' => $parent . '_' . 'last_12_months',
					'title' => __( 'Last 12 Months', 'woocommerce-jetpack' ),
					'href'  => add_query_arg( array( 'range' => 'custom', 'start_date' => date( 'Y-m-d', strtotime( '-12 months' ) ), 'end_date' => date( 'Y-m-d' ), ) ),
					'meta' => array( 'title' => __( 'Last 12 Months', 'woocommerce-jetpack' ), ),
				),		
				array(
					'parent' => $parent, 
					'id' => $parent . '_' . 'last_24_months',
					'title' => __( 'Last 24 Months', 'woocommerce-jetpack' ),
					'href'  => add_query_arg( array( 'range' => 'custom', 'start_date' => date( 'Y-m-d', strtotime( '-24 months' ) ), 'end_date' => date( 'Y-m-d' ), ) ),
					'meta' => array( 'title' => __( 'Last 24 Months', 'woocommerce-jetpack' ), ),
				),	
				array(
					'parent' => $parent, 
					'id' => $parent . '_' . 'same_days_last_month',
					'title' => __( 'Same Days Last Month', 'woocommerce-jetpack' ),
					'href'  => add_query_arg( array( 'range' => 'custom', 'start_date' => date( 'Y-m-01', strtotime( '-1 month' ) ), 'end_date' => date( 'Y-m-d', strtotime( '-1 month' ) ), ) ),
					'meta' => array( 'title' => __( 'Same Days Last Month', 'woocommerce-jetpack' ), ),
				),		
				/*array(
					'parent' => $parent, 
					'id' => $parent . '_' . 'last_week',
					'title' => __( 'Last Week', 'woocommerce-jetpack' ),
					'href'  => add_query_arg( array( 'range' => 'custom', 'start_date' => date( 'Y-m-d', strtotime( 'last monday' ) ), 'end_date' => date( 'Y-m-d', strtotime( 'last sunday' ) ), ) ),
					'meta' => array( 'title' => __( 'Last Week', 'woocommerce-jetpack' ), ),
				),*/	
				array(
					'parent' => $parent, 
					'id' => $parent . '_' . 'last_year',
					'title' => __( 'Last Year', 'woocommerce-jetpack' ),
					'href'  => add_query_arg( array( 'range' => 'custom', 'start_date' => date( 'Y-01-01', strtotime( '-1 year' ) ), 'end_date' => date( 'Y-12-31', strtotime( '-1 year' ) ), ) ),
					'meta' => array( 'title' => __( 'Last Year', 'woocommerce-jetpack' ), ),
				),				
			);			
			foreach ( $nodes as $node ) {
				$wp_admin_bar->add_node( $node );
			}				
		}	
	}	
	
	/**
	 * catch_arguments.
	 */	
	public function catch_arguments() {
		$this->report_id       = isset( $_GET['report'] )                             ? $_GET['report'] : 'on_stock';
		$this->range_days      = isset( $_GET['period'] )                             ? $_GET['period'] : 30;
		$this->group_countries = ( 'customers_by_country_sets' === $this->report_id ) ? 'yes'           : 'no';
	}	
	
	/**
	 * get_report_stock.
	 */		
	public function get_report_stock() {
		$report = new WCJ_Reports_Stock( array ( 
			'report_id'  => $this->report_id, 
			'range_days' => $this->range_days, 
		) );
		echo $report->get_report_html();		
	}	
	
	/**
	 * get_report_customers.
	 */		
	public function get_report_customers() {
		$report = new WCJ_Reports_Customers( array ( 'group_countries' => $this->group_countries ) );
		echo $report->get_report();
	}
	
	/**
	 * Add reports to WooCommerce > Reports > Stock
	 */
	public function add_stock_reports( $reports ) {
	
		$reports['stock']['reports']['on_stock'] = array(
			'title'       => __( 'WooJetpack: All in stock', 'woocommerce-jetpack' ),
			'description' => '',
			'hide_title'  => true,
			'callback'    => array( $this, 'get_report_stock' ),
		);
		
		$reports['stock']['reports']['understocked'] = array(
			'title'       => __( 'WooJetpack: Understocked', 'woocommerce-jetpack' ),
			'description' => '',
			'hide_title'  => true,
			'callback'    => array( $this, 'get_report_stock' ),
		);		
		
		return $reports;
	}	
	
	/**
	 * Add reports to WooCommerce > Reports > Customers
	 */
	public function add_customers_by_country_report( $reports ) {
		
		$reports['customers']['reports']['customers_by_country'] = array(
			'title'       => __( 'WooJetpack: Customers by Country', 'woocommerce-jetpack' ),
			'description' => '',
			'hide_title'  => true,
			'callback'    => array( $this, 'get_report_customers' ),
		);		
		
		$reports['customers']['reports']['customers_by_country_sets'] = array(
			'title'       => __( 'WooJetpack: Customers by Country Sets', 'woocommerce-jetpack' ),
			'description' => '',
			'hide_title'  => true,
			'callback'    => array( $this, 'get_report_customers' ),
		);
		
		return $reports;
	}
	
	/**
	 * Add Enable option to Jetpack Settings Dashboard.
	 */
	public function add_enabled_option( $settings ) {
		$all_settings = $this->get_settings();
		$settings[] = $all_settings[1];
		return $settings;
	}

	/*
	 * Add the settings.
	 */
	function get_settings() {

		$settings = array(

			array( 'title' 	=> __( 'Reports Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => '', 'id' => 'wcj_reports_options' ),

			array(
				'title' 	=> __( 'Reports', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Enable the Reports feature', 'woocommerce-jetpack' ),
				'desc_tip' 	=> __( 'Stock, sales, customers etc. reports.', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_reports_enabled',
				'default'	=> 'yes',
				'type' 		=> 'checkbox'
			),

			array( 'type' 	=> 'sectionend', 'id' => 'wcj_reports_options' ),
			
			array( 
				'title' 	=> __( 'Available Reports', 'woocommerce-jetpack' ),
				'type' 		=> 'title', 
				'desc' 		=> '<p>'
							   . __( 'WooJetpack: Customers by Country. Available in WooCommerce > Reports > Customers.', 'woocommerce-jetpack' )
							   . '</p><p>'
							   . __( 'WooJetpack: Customers by Country Sets. Available in WooCommerce > Reports > Customers.', 'woocommerce-jetpack' )
							   . '</p><p>'							   
							   . __( 'WooJetpack: All in Stock with sales data. Available in WooCommerce > Reports > Stock.', 'woocommerce-jetpack' )
							   . '</p><p>'
							   . __( 'WooJetpack: Understocked products (calculated by sales data). Available in WooCommerce > Reports > Stock.', 'woocommerce-jetpack' )
							   . '</p>',
				'id' 		=> 'wcj_reports_more_options' 
			),

			array( 'type' 	=> 'sectionend', 'id' => 'wcj_reports_more_options' ),			
		);

		return $settings;
	}

	/*
	 * Add settings section to WooCommerce > Settings > Jetpack.
	 */
	function settings_section( $sections ) {
		$sections['reports'] = __( 'Reports', 'woocommerce-jetpack' );
		return $sections;
	}	
}

endif;

return new WCJ_Reports();
