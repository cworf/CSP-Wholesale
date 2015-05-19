<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'SA_WC_Compatibility_2_3' ) ) {
	
	/**
	 * Class to check for WooCommerce version & return variables accordingly
	 *
	 */
	class SA_WC_Compatibility_2_3 extends SA_WC_Compatibility_2_2 {

		/**
		 * Is WooCommerce Greater Than And Equal To 2.3
		 * 
		 * @return boolean 
		 */
		public static function is_wc_gte_23() {
			return self::is_wc_greater_than( '2.2.11' );
		}

	}

}