<?php
/**
 * WooCommerce Jetpack Price by Country
 *
 * The WooCommerce Jetpack Price by Country class.
 *
 * @class       WCJ_Price_By_Country
 * @version		2.1.3
 * @category	Class
 * @author 		Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_Price_By_Country' ) ) :

class WCJ_Price_By_Country {

    /**
     * Constructor.
     */
    public function __construct() {

        // Main hooks
        if ( 'yes' === get_option( 'wcj_price_by_country_enabled' ) ) {
			if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				//wcj_log( 'WCJ_Price_By_Country: frontend' );
				include_once( 'price-by-country/class-wcj-price-by-country-core.php' );
			} else {
				//wcj_log( 'WCJ_Price_By_Country: backend' );
				include_once( 'price-by-country/class-wcj-exchange-rates.php' );
				include_once( 'price-by-country/class-wcj-price-by-country-reports.php' );
				if ( 'yes' === get_option( 'wcj_price_by_country_local_enabled' ) ) {
					include_once( 'price-by-country/class-wcj-price-by-country-local.php' );
				}
			}			
        }
		include_once( 'price-by-country/class-wcj-exchange-rates-crons.php' );

        // Settings hooks
        add_filter( 'wcj_settings_sections',		 array( $this, 'settings_section' ) );
        add_filter( 'wcj_settings_price_by_country', array( $this, 'get_settings' ), 	   100 );
        add_filter( 'wcj_features_status', 			 array( $this, 'add_enabled_option' ), 100 );
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
     * get_settings.
     */
    function get_settings() {
	
        $settings = array(

            array(
				'title' => __( 'Price by Country Options', 'woocommerce-jetpack' ),
				'type' => 'title',
				'desc' => __( 'Change product\'s price and currency by customer\'s country. Customer\'s country is detected automatically by IP.', 'woocommerce-jetpack' )
						  /*. '<br>'
						  . '<span style="color:gray;font-size:smaller;">'
						  //. wcj_get_ip_db_status_html()
						  . apply_filters( 'wcj_get_ip_db_status_html', '' )
						  . '<br>'
						  . ( ( ini_get( 'allow_url_fopen' ) ) ? 'allow_url_fopen: enabled' : 'allow_url_fopen: disabled' )
						  . '</span>'*/,
				'id' => 'wcj_price_by_country_options' ),

            array(
                'title'    => __( 'Prices and Currencies by Country', 'woocommerce-jetpack' ),
                'desc'     => '<strong>' . __( 'Enable Module', 'woocommerce-jetpack' ) . '</strong>',
                'desc_tip' => __( 'Change product\'s price and currency automatically by customer\'s country.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_price_by_country_enabled',
                'default'  => 'no',
                'type'     => 'checkbox',
            ),
/*
            array(
                'title'    => __( 'Country by IP Method', 'woocommerce-jetpack' ),
                'desc'     => __( 'Select which method to use for detecting customers country by IP.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_price_by_country_by_ip_detection_type',
                'default'  => 'internal_wc',
                'type'     => 'select',
				'options'  => array(
								'internal_wc' => __( 'Internal DB - since WooCommerce 2.3 (recommended)', 'woocommerce-jetpack' ),
								'internal'    => __( 'Internal DB (depreciated)', 'woocommerce-jetpack' ),
								'hostip_info' => __( 'External server:', 'woocommerce-jetpack' ) . ' '  . 'api.hostip.info',
				),
            ),
*/
            array(
                'title'    => __( 'Price Rounding', 'woocommerce-jetpack' ),
                'desc'     => __( 'If you choose to multiply price, set rounding options here.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_price_by_country_rounding',
                'default'  => 'none',
                'type'     => 'select',
				'options'  => array(
								'none'  => __( 'No rounding', 'woocommerce-jetpack' ),
								'round' => __( 'Round', 'woocommerce-jetpack' ),
								'floor' => __( 'Round down', 'woocommerce-jetpack' ),
								'ceil'  => __( 'Round up', 'woocommerce-jetpack' ),
				),
            ),
			
			array(
                'title'    => __( 'Price by Country on per Product Basis', 'woocommerce-jetpack' ),
				'desc'     => __( 'Enable', 'woocommerce-jetpack' ),
				'desc_tip' => __( 'This will add meta boxes in product edit.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_price_by_country_local_enabled',
                'default'  => 'yes',
                'type'     => 'checkbox',
			),			

            array( 'type'  => 'sectionend', 'id' => 'wcj_price_by_country_options' ),			

			array( 'title' => __( 'Country Groups', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => '', 'id' => 'wcj_price_by_country_country_groups_options' ),

            array(
                'title'    => __( 'Groups Number', 'woocommerce-jetpack' ),
                'id'       => 'wcj_price_by_country_total_groups_number',
                'default'  => 1,
                'type'     => 'number',
				'desc'     => apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
				'custom_attributes'
				           => array_merge(
								is_array( apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ) ) ? apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ) : array(),
								array(
									'step' 	=> '1',
									'min'	=> '1',
								) ),
				),
		);

		for ( $i = 1; $i <= apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_price_by_country_total_groups_number', 1 ) ); $i++ ) {

			$settings[] = array(
                'title'    => __( 'Group', 'woocommerce-jetpack' ) . ' #' . $i,
				'desc'	   => __( 'Countries. List of comma separated country codes.<br>For country codes and predifined sets visit <a href="http://woojetpack.com/features/prices-and-currencies-by-customers-country">WooJetpack.com</a>', 'woocommerce-jetpack' ),
                'id'       => 'wcj_price_by_country_exchange_rate_countries_group_' . $i,
                'default'  => '',
                'type'     => 'textarea',
				'css'	   => 'width:50%;min-width:300px;height:100px;',
            );      

            $settings[] = array(
                'title'    => '',
				'desc'	   => __( 'Currency', 'woocommerce-jetpack' ),
                'id'       => 'wcj_price_by_country_exchange_rate_currency_group_' . $i,
                'default'  => 'EUR',
                'type'     => 'select',
				'options'  => wcj_get_currencies_names_and_symbols(),
            );
		}	
		
		$settings[] = array( 'type'  => 'sectionend', 'id' => 'wcj_price_by_country_country_groups_options' );
		
		$settings[] = array( 'title' => __( 'Exchange Rates', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => '', 'id' => 'wcj_price_by_country_exchange_rate_options' );
		
		$settings[] = array(
			'title'    => __( 'Exchange Rates Updates', 'woocommerce-jetpack' ),
			//'desc'     => __( '', 'woocommerce-jetpack' ),
			//'desc_tip' => __( '', 'woocommerce-jetpack' ),
			'id'       => 'wcj_price_by_country_auto_exchange_rates',
			'default'  => 'manual',
			'type'     => 'select',
			'options'  => array(
				'manual'     => __( 'Enter Rates Manually', 'woocommerce-jetpack' ),
				'hourly'     => __( 'Automatically: Update Hourly', 'woocommerce-jetpack' ),
				'twicedaily' => __( 'Automatically: Update Twice Daily', 'woocommerce-jetpack' ),
				'daily'      => __( 'Automatically: Update Daily', 'woocommerce-jetpack' ),
				'weekly'     => __( 'Automatically: Update Weekly', 'woocommerce-jetpack' ),
				'minutely'   => __( 'Automatically: Update Every Minute', 'woocommerce-jetpack' ),
			),
			'desc' 	   => apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
			'custom_attributes'	
					   => apply_filters( 'get_wc_jetpack_plus_message', '', 'disabled' ),				
		);			

		$currency_from = apply_filters( 'woocommerce_currency', get_option('woocommerce_currency') );
		for ( $i = 1; $i <= apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_price_by_country_total_groups_number', 1 ) ); $i++ ) {

			$currency_to = get_option( 'wcj_price_by_country_exchange_rate_currency_group_' . $i );          

            $settings[] = array(
                'title'    => __( 'Group', 'woocommerce-jetpack' ) . ' #' . $i,
				'desc'	   => __( 'Multiply Price by', 'woocommerce-jetpack' ),
                'id'       => 'wcj_price_by_country_exchange_rate_group_' . $i,
				'default'  => 1,
				'type'     => 'number',
				'css'	   => 'width:100px;',
				'custom_attributes'	=> array(
					'step' 	=> '0.000001',
					'min'	=> '0',
				),
            );
			
			$custom_attributes = array(
				'currency_from' => $currency_from,
				'currency_to'   => $currency_to,
				'multiply_by_field_id'   => 'wcj_price_by_country_exchange_rate_group_' . $i,
			);
			if ( $currency_from == $currency_to )
				$custom_attributes['disabled'] = 'disabled';
			$settings[] = array(
				'title'    => '',
				//'id'       => 'wcj_price_by_country_exchange_rate_refresh_group_' . $i,
				'class'    => 'exchage_rate_button',
				'type'     => 'button',
				'css'	   => 'width:300px;',
				'value'    => sprintf( __( '%s rate from Yahoo.com', 'woocommerce-jetpack' ),  $currency_from . '/' . $currency_to ),
				'custom_attributes'	=> $custom_attributes,
			);
			
            $settings[] = array(
                'title'    => '',
				'desc'	   => __( 'Make empty price', 'woocommerce-jetpack' ),
                'id'       => 'wcj_price_by_country_make_empty_price_group_' . $i,
				'default'  => 'no',
				'type'     => 'checkbox',
            );			
		}

		$settings[] = array( 'type'  => 'sectionend', 'id' => 'wcj_price_by_country_exchange_rate_options' );

        return $settings;
    }

    /**
     * settings_section.
     */
    function settings_section( $sections ) {
        $sections['price_by_country'] = __( 'Prices and Currencies by Country', 'woocommerce-jetpack' );
        return $sections;
    }
}

endif;

return new WCJ_Price_By_Country();
