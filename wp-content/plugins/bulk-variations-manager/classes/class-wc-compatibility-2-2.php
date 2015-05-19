<?php
if ( !defined( 'ABSPATH' ) ) exit;
if ( !class_exists( 'SA_WC_Compatibility_2_2' ) ) {

/**
 * Compatibility class for WooCommerce 2.2+
 * 
 * @version 1.0.0
 * @since 2.3.0 17-Sep-2014
 *
 */
	class SA_WC_Compatibility_2_2 extends SA_WC_Compatibility {

		/**
		 * @since 1.0.0 of SA_WC_Compatibility_2_2
		 */
		public static function get_product( $the_product = false, $args = array() ) {

			if ( self::is_wc_gte_22() ) {
				return wc_get_product( $the_product, $args );
			} elseif ( self::is_wc_21() ) {
				return get_product( $the_product, $args );
			} else {
				return new WC_Product( $the_product );
			}

		}

		/**
		 * @since 1.0.0 of SA_WC_Compatibility_2_2
		 */
		public static function wc_get_formatted_name( $product = false ) {
			return self::get_formatted_product_name( $product );
		}

		/**
		 * @since 1.0.0 of SA_WC_Compatibility_2_2
		 */
		public static function get_formatted_product_name( $product = false ) {

			if ( self::is_wc_gte_21() ) {
				return $product->get_formatted_name();
			} else {
				return woocommerce_get_formatted_product_name( $product );
			}
		}

		/**
		 * @since 1.0.0 of SA_WC_Compatibility_2_2
		 */
		public static function get_formatted_variation( $variation, $flat = false ) {

			if ( self::is_wc_gte_21() ) {
				return wc_get_formatted_variation( $variation, $flat );
			} else {
				return woocommerce_get_formatted_variation( $variation, $flat );
			}
		}

		/**
		 * @since 1.0.0 of SA_WC_Compatibility_2_2
		 */
		public static function mail( $to, $subject, $message ) {

			if ( self::is_wc_gte_21() ) {
				return wc_mail( $to, $subject, $message );
			} else {
				return woocommerce_mail( $to, $subject, $message );
			}
		}

		/**
		 * @since 1.0.0 of SA_WC_Compatibility_2_2
		 */
		public static function get_order( $the_order = false ) {

			if ( self::is_wc_gte_22() ) {
				return wc_get_order( $the_order );
			} else {

				global $post;

				if ( false === $the_order ) {
					$order_id = $post->ID;
				} elseif ( $the_order instanceof WP_Post ) {
					$order_id = $the_order->ID;
				} elseif ( is_numeric( $the_order ) ) {
					$order_id = $the_order;
				}

				return new WC_Order( $order_id );

			}

		}

		/**
		 * @since 1.0.0 of SA_WC_Compatibility_2_2
		 */
		public static function enqueue_js( $js = false ) {

			if ( self::is_wc_gte_21() ) {
				wc_enqueue_js( $js );
			} else {
				global $woocommerce;
				$woocommerce->add_inline_js( $js );
			}

		}

		/**
		 * @since 1.0.0 of SA_WC_Compatibility_2_2
		 */
		public static function wc_get_template( $template_path, $args = array() ) {

			if ( self::is_wc_gte_21() ) {
				return wc_get_template( $template_path, $args );
			} else {
				return woocommerce_get_template( $template_path, $args );
			}
		}

		/**
		 * @since 1.0.0 of SA_WC_Compatibility_2_2
		 */
		public static function is_wc_21() {
			return self::is_wc_greater_than( '2.0.20' );
		}

		/**
		 * @since 1.0.0 of SA_WC_Compatibility_2_2
		 */
		public static function is_wc_gte_22() {
			return self::is_wc_greater_than( '2.1.12' );
		}
		
		/**
		 * @since 1.0.0 of SA_WC_Compatibility_2_2
		 */
		public static function is_wc_gte_21() {
			return self::is_wc_greater_than( '2.0.20' );
		}
		
		/**
		 * @since 1.0.0 of SA_WC_Compatibility_2_2
		 */
		public static function is_wc_gte_20() {
			return self::is_wc_greater_than( '1.6.6' );
		}

	}

}