<?php
/**
 * Logic related to displaying Groups page.
 *
 * @author   Actuality Extensions
 * @package  WooCommerce_Customer_Relationship_Manager
 * @since    1.0
 */

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Renders CRM Settings page.
 */
function wc_customer_relationship_manager_render_settings_page() {
	include_once( 'classes/wc_crm_settings.php' );
	$crm_settings = new WC_Crm_Settings;
	$crm_settings->output();
}