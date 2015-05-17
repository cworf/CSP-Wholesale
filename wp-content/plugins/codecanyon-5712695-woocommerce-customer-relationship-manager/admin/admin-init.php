<?php
/**
 * Admin init logic
 *
 * @author   Actuality Extensions
 * @package  WooCommerce_Customer_Relationship_Manager
 * @since    1.0
 */


if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

include( WC_CRM()->plugin_path().'/admin/group-functions.php' );
require_once( WC_CRM()->plugin_path().'/admin/customers.php' );
require_once( WC_CRM()->plugin_path().'/admin/groups.php' );
require_once( WC_CRM()->plugin_path().'/admin/logs.php' );
require_once( WC_CRM()->plugin_path().'/admin/customer_details.php' );
require_once( WC_CRM()->plugin_path().'/admin/statuses.php' );
require_once( WC_CRM()->plugin_path().'/admin/import.php' );
require_once( WC_CRM()->plugin_path().'/admin/settings.php' );
