<?php
/**
 * WooCommerce Jetpack Functions
 *
 * The WooCommerce Jetpack Functions.
 *
 * @version  2.1.2
 * @author   Algoritmika Ltd.
 */

/**
 * validate_VAT.
 *
 * @return mixed: bool on successful checking (can be true or false), null otherwise
 */
if ( ! function_exists( 'validate_VAT' ) ) {
	function validate_VAT( $country_code, $vat_number ) {
		try {
			$client = new SoapClient( 
				'http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl',
				array( 'exceptions' => true )
			);
			
			$result = $client->checkVat( array(
				'countryCode' => $country_code,
				'vatNumber'   => $vat_number,
			) );
			
			return ( isset( $result->valid ) ) ? $result->valid : null;				
			
		} catch( Exception $exception ) {
			return null;
		}	
	}
}


/**
 * convert_number_to_words.
 *
 * @return string
 */

if ( ! function_exists( 'convert_number_to_words' ) ) {
	function convert_number_to_words( $number ) {
		$hyphen      = '-';
		$conjunction = ' and ';
		$separator   = ', ';
		$negative    = 'negative ';
		$decimal     = ' point ';
		$dictionary  = array(
			0                   => 'zero',
			1                   => 'one',
			2                   => 'two',
			3                   => 'three',
			4                   => 'four',
			5                   => 'five',
			6                   => 'six',
			7                   => 'seven',
			8                   => 'eight',
			9                   => 'nine',
			10                  => 'ten',
			11                  => 'eleven',
			12                  => 'twelve',
			13                  => 'thirteen',
			14                  => 'fourteen',
			15                  => 'fifteen',
			16                  => 'sixteen',
			17                  => 'seventeen',
			18                  => 'eighteen',
			19                  => 'nineteen',
			20                  => 'twenty',
			30                  => 'thirty',
			40                  => 'fourty',
			50                  => 'fifty',
			60                  => 'sixty',
			70                  => 'seventy',
			80                  => 'eighty',
			90                  => 'ninety',
			100                 => 'hundred',
			1000                => 'thousand',
			1000000             => 'million',
			1000000000          => 'billion',
			1000000000000       => 'trillion',
			1000000000000000    => 'quadrillion',
			1000000000000000000 => 'quintillion'
		);

		if (!is_numeric($number)) {
			return false;
		}

		if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
			// overflow
			trigger_error(
				'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
				E_USER_WARNING
			);
			return false;
		}

		if ($number < 0) {
			return $negative . convert_number_to_words(abs($number));
		}

		$string = $fraction = null;

		if (strpos($number, '.') !== false) {
			list($number, $fraction) = explode('.', $number);
		}

		switch (true) {
			case $number < 21:
				$string = $dictionary[$number];
				break;
			case $number < 100:
				$tens   = ((int) ($number / 10)) * 10;
				$units  = $number % 10;
				$string = $dictionary[$tens];
				if ($units) {
					$string .= $hyphen . $dictionary[$units];
				}
				break;
			case $number < 1000:
				$hundreds  = $number / 100;
				$remainder = $number % 100;
				$string = $dictionary[$hundreds] . ' ' . $dictionary[100];
				if ($remainder) {
					$string .= $conjunction . convert_number_to_words($remainder);
				}
				break;
			default:
				$baseUnit = pow(1000, floor(log($number, 1000)));
				$numBaseUnits = (int) ($number / $baseUnit);
				$remainder = $number % $baseUnit;
				$string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
				if ($remainder) {
					$string .= $remainder < 100 ? $conjunction : $separator;
					$string .= convert_number_to_words($remainder);
				}
				break;
		}

		if (null !== $fraction && is_numeric($fraction)) {
			$string .= $decimal;
			$words = array();
			foreach (str_split((string) $fraction) as $number) {
				$words[] = $dictionary[$number];
			}
			$string .= implode(' ', $words);
		}

		return $string;
	}
}

/**
 * wcj_plugin_url.
 *
 * @todo
 *
if ( ! function_exists( 'wcj_plugin_url' ) ) {
	function wcj_plugin_url() {
		return untrailingslashit( plugin_dir_url( __FILE__ ) );
		//return untrailingslashit( realpath( dirname(__FILE__) . '/..' ) );
	}
}

/**
 * Get the plugin path.
 *
 * @return string
 */
if ( ! function_exists( 'wcj_plugin_path' ) ) {
	function wcj_plugin_path() {
		//return untrailingslashit( plugin_dir_path( __FILE__ ) );
		return untrailingslashit( realpath( plugin_dir_path( __FILE__ ) . '/../..' ) );
	}
}

/**
 * Convert the php date format string to a js date format.
 * https://gist.github.com/clubduece/4053820
 */
if ( ! function_exists( 'wcj_date_format_php_to_js' ) ) {
	function wcj_date_format_php_to_js( $php_date_format ) {
		$date_formats_php_to_js = array(
			'F j, Y' => 'MM dd, yy',
			'Y/m/d'  => 'yy/mm/dd',
			'm/d/Y'  => 'mm/dd/yy',
			'd/m/Y'  => 'dd/mm/yy',
		);
		return isset( $date_formats_php_to_js[ $php_date_format ] ) ? $date_formats_php_to_js[ $php_date_format ] : 'MM dd, yy';
	}
}

/**
 * wcj_hex2rgb.
 */
if ( ! function_exists( 'wcj_hex2rgb' ) ) {
	function wcj_hex2rgb( $hex ) {
		return sscanf( $hex, "#%2x%2x%2x" );
	}
}

/**
 * wcj_get_the_ip.
 * http://stackoverflow.com/questions/3003145/how-to-get-the-client-ip-address-in-php
 */
if ( ! function_exists( 'wcj_get_the_ip' ) ) {
	function wcj_get_the_ip( ) {
		$ip = null;
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
}

/**
 * wcj_get_shortcodes_atts_list.
 *
if ( ! function_exists( 'wcj_get_shortcodes_atts_list' ) ) {
	function wcj_get_shortcodes_atts_list() {
		return apply_filters( 'wcj_shortcodes_atts', array(
			'before'        => '',
			'after'         => '',
			'visibility'    => '',
		) );
	}
}

/**
 * wcj_get_shortcodes_list.
 */
if ( ! function_exists( 'wcj_get_shortcodes_list' ) ) {
	function wcj_get_shortcodes_list() {
		$the_array = apply_filters( 'wcj_shortcodes_list', array() );
		return implode( ', ', $the_array );
		//return implode( PHP_EOL, $the_array );
	}
}

/**
 * wcj_get_order_statuses.
 */
if ( ! function_exists( 'wcj_get_order_statuses' ) ) {
	function wcj_get_order_statuses( $cut_the_prefix ) {
		$order_statuses = array(
			'wc-pending'    => _x( 'Pending Payment', 'Order status', 'woocommerce' ),
			'wc-processing' => _x( 'Processing', 'Order status', 'woocommerce' ),
			'wc-on-hold'    => _x( 'On Hold', 'Order status', 'woocommerce' ),
			'wc-completed'  => _x( 'Completed', 'Order status', 'woocommerce' ),
			'wc-cancelled'  => _x( 'Cancelled', 'Order status', 'woocommerce' ),
			'wc-refunded'   => _x( 'Refunded', 'Order status', 'woocommerce' ),
			'wc-failed'     => _x( 'Failed', 'Order status', 'woocommerce' ),
		);
		$order_statuses = apply_filters( 'wc_order_statuses', $order_statuses );
		if ( $cut_the_prefix ) {
			$order_statuses_no_prefix = array();
			foreach ( $order_statuses as $status => $desc ) {
				$order_statuses_no_prefix[ substr( $status, 3 ) ] = $desc;
			}
			return $order_statuses_no_prefix;
		}
		return $order_statuses;
	}
}

/**
 * wcj_get_currencies_names_and_symbols.
 */
if ( ! function_exists( 'wcj_get_currencies_names_and_symbols' ) ) {
	function wcj_get_currencies_names_and_symbols() {
		$currencies = include( wcj_plugin_path() . '/includes/currencies/wcj-currencies.php' );
		foreach( $currencies as $data ) {
			$currency_names_and_symbols[ $data['code'] ] = $data['name'] . ' (' . $data['symbol'] . ')';
		}
		return $currency_names_and_symbols;
	}
}

/**
 * wcj_get_currency_symbol.
 */
if ( ! function_exists( 'wcj_get_currency_symbol' ) ) {
	function wcj_get_currency_symbol( $currency_code ) {
		$currencies = include( wcj_plugin_path() . '/includes/currencies/wcj-currencies.php' );
		foreach( $currencies as $data ) {
			if ( $currency_code == $data['code'] )
				return $data['symbol'];
		}
		return false;
	}
}

/**
 * wcj_price.
 */
if ( ! function_exists( 'wcj_price' ) ) {
	function wcj_price( $price, $currency, $hide_currency ) {
		return ( 'yes' === $hide_currency ) ? wc_price( $price, array( 'currency' => 'DISABLED' ) ) : wc_price( $price, array( 'currency' => $currency ) );
	}
}
