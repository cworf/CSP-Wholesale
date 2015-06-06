<?php
/**
 * WooCommerce Jetpack Exchange Rates Crons
 *
 * The WooCommerce Jetpack Exchange Rates Crons class.
 *
 * @class    WCJ_Exchange_Rates_Crons
 * @version  1.0.0
 * @category Class
 * @author   Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_Exchange_Rates_Crons' ) ) :

class WCJ_Exchange_Rates_Crons {

    /**
     * Constructor.
     */
    public function __construct() {
		add_action( 'wp',                                   array( $this, 'schedule_the_events' ) );		
		$this->update_intervals  = array(
			'manual'     => __( 'Enter Rates Manually', 'woocommerce-jetpack' ),
			'hourly'     => __( 'Automatically: Update Hourly', 'woocommerce-jetpack' ),
			'twicedaily' => __( 'Automatically: Update Twice Daily', 'woocommerce-jetpack' ),
			'daily'      => __( 'Automatically: Update Daily', 'woocommerce-jetpack' ),
			'weekly'     => __( 'Automatically: Update Weekly', 'woocommerce-jetpack' ),
			'minutely'   => __( 'Automatically: Update Every Minute', 'woocommerce-jetpack' ),
		);
		/*foreach ( $this->update_intervals as $interval => $desc ) {
			if ( 'manual' === $interval )
				continue;
			add_action( 'auto_update_exchange_rates_hook_' . $interval,
                                                            array( $this, 'update_the_exchange_rates' ) );	
		}*/
		$selected_interval = get_option( 'wcj_price_by_country_auto_exchange_rates', 'manual' );
		if ( 'manual' != $selected_interval ) {
			//add_action( 'auto_update_exchange_rates_hook_' . $selected_interval,
			add_action( 'auto_update_exchange_rates_hook',
                                                            array( $this, 'update_the_exchange_rates' ) );			
		}
		add_filter( 'cron_schedules',                       array( $this, 'cron_add_custom_intervals' ) );		
    }
	
	
	/**
	 * On an early action hook, check if the hook is scheduled - if not, schedule it.
	 */
	function schedule_the_events() {
	
		$selected_interval = apply_filters( 'wcj_get_option_filter', 'manual', get_option( 'wcj_price_by_country_auto_exchange_rates', 'manual' ) );
		foreach ( $this->update_intervals as $interval => $desc ) {
			if ( 'manual' === $interval )
				continue;
			$event_hook = 'auto_update_exchange_rates_hook';//_' . $interval;
			$event_timestamp = wp_next_scheduled( $event_hook, array( $interval ) );
			if ( ! $event_timestamp && $selected_interval === $interval ) {
				wp_schedule_event( time(), $selected_interval, $event_hook, array( $selected_interval ) );
			} elseif ( $event_timestamp && $selected_interval !== $interval ) {
				wp_unschedule_event( $event_timestamp, $event_hook, array( $interval ) );
			}
		}
		
		////wcj_log( _get_cron_array() );
		//wcj_log( get_option( 'cron' ) );
	}

	/*
	 * Functions gets currency exchange rate from rate-exchange.appspot.com server.
	 * returns rate on success, else 0
	 */
	function get_exchange_rate( $currency_from, $currency_to ) {
	
		
		
		$url = "http://query.yahooapis.com/v1/public/yql?q=select%20rate%2Cname%20from%20csv%20where%20url%3D'http%3A%2F%2Fdownload.finance.yahoo.com%2Fd%2Fquotes%3Fs%3D" . $currency_from . $currency_to . "%253DX%26f%3Dl1n'%20and%20columns%3D'rate%2Cname'&format=json";	
		//$url = 'http://rate-exchange.appspot.com/currency?from=' . $currency_from . '&to=' . $currency_to;
		
		ob_start();
		$max_execution_time = ini_get( 'max_execution_time' );
		set_time_limit( 2 );
		
		$exchange_rate = json_decode( file_get_contents( $url ) );		
		
		set_time_limit( $max_execution_time );
		ob_end_clean();
		
		return ( isset( $exchange_rate->query->results->row->rate ) ) ? floatval( $exchange_rate->query->results->row->rate ) : 0;
		//return ( isset( $exchange_rate->rate ) ) ? $exchange_rate->rate : 0;
	}	
	
	/**
	 * On the scheduled action hook, run a function.
	 */
	function update_the_exchange_rates( $interval ) {
	
		for ( $i = 1; $i <= apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_price_by_country_total_groups_number', 1 ) ); $i++ ) {	
			$currency_from = get_option( 'woocommerce_currency' );
			$currency_to   = get_option( 'wcj_price_by_country_exchange_rate_currency_group_' . $i );
			$the_rate = $this->get_exchange_rate( $currency_from, $currency_to );			
			if ( 0 != $the_rate ) {
				if ( $currency_from != $currency_to ) {
					update_option( 'wcj_price_by_country_exchange_rate_group_' . $i, $the_rate );
					$result_message = __( 'Cron job: exchange rates successfully updated', 'woocommerce-jetpack' );				
				} else {
					$result_message = __( 'Cron job: exchange rates not updated, as currency_from == currency_to', 'woocommerce-jetpack' );
				}
			} else {
				$result_message = __( 'Cron job: exchange rates update failed', 'woocommerce-jetpack' );
			}
			wcj_log( $result_message . ': ' . $currency_from . $currency_to . ': ' . $the_rate . ': ' . 'update_the_exchange_rates: ' . $interval );
		}
	}
	
	/**
	 * cron_add_custom_intervals.
	 */	
	function cron_add_custom_intervals( $schedules ) {
		
		$schedules['weekly'] = array(
			'interval' => 604800,
			'display' => __( 'Once Weekly', 'woocommerce-jetpack' )
		);
		
		$schedules['minutely'] = array(
			'interval' => 60,
			'display' => __( 'Once a Minute', 'woocommerce-jetpack' )
		);		
		
		return $schedules;
	}		
}

endif;

return new WCJ_Exchange_Rates_Crons();
