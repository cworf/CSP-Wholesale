<?php
/**
 * WooCommerce General Settings
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_Crm_Settings_Order_Page' ) ) :

/**
 * WC_Crm_Settings_General
 */
class WC_Crm_Settings_Order_Page extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'order_page_crm';
		$this->label = __( 'Orders Page', 'wc_customer_relationship_manager' );

		add_filter( 'wc_crm_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'wc_crm_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'wc_crm_settings_save_' . $this->id, array( $this, 'save' ) );

	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	public function get_settings() {
		global $woocommerce;
		$filters = array(
					'name' => __( 'Customer Link', 'wc_customer_relationship_manager' ),
					'desc' => 'Choose what the link of the customer is on the Orders page, customer or user profile.',
					'id' => 'woocommerce_crm_customer_link',
					'css' => '',
					'std' => '',
					'type' => 'select',
					'options' => array(
							'customer' => __( 'Customer ', 'wc_customer_relationship_manager' ),
							'user_profile' => __( 'User profile', 'wc_customer_relationship_manager' ),
						)
				);
		return apply_filters( 'woocommerce_customer_relationship_general_settings_fields', array(

			array( 'title' => __( '', 'wc_customer_relationship_manager' ), 'type' => 'title', 'desc' => '', 'id' => 'general_crm_options' ),

			$filters,

			array( 'type' => 'sectionend', 'id' => 'general_crm_options'),

		) ); // End general settings

	}

	/**
	 * Save settings
	 */
	public function save() {
		$settings = $this->get_settings();

		WC_Crm_Settings::save_fields( $settings );
	}

}

endif;

return new WC_Crm_Settings_Order_Page();