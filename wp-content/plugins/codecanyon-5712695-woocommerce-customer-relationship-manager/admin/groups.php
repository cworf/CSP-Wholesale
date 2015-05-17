<?php
/**
 * Logic related to displaying Groups page.
 *
 * @author   Actuality Extensions
 * @package  WooCommerce_Customer_Relationship_Manager
 * @since    1.0
 */

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function wc_customer_relationship_manager_groups_add_options() {

	if(isset($_GET['page']) && $_GET['page'] == 'wc_user_grps'){
		global $wc_crm_customer_groups;
		$option = 'per_page';
		$args = array(
			'label' => __( 'Groups', 'wc_customer_relationship_manager' ),
			'default' => 20,
			'option' => 'groups_per_page'
		);
		add_screen_option( $option, $args );
		include_once( 'classes/wc_crm_user_groups_table.php' );
		$wc_crm_customer_groups_table = new WC_Pos_Groups_Table;
	}
}

/**
 * Renders CRM Group page.
 */
function wc_customer_relationship_manager_render_groups_list_page() {
	global $wc_crm_customer_groups, $wc_crm_customer_groups_table;
	include_once( 'classes/wc_crm_user_groups_table.php' );
	$wc_crm_customer_groups = include( 'classes/wc_crm_user_groups.php' );
	$wc_crm_customer_groups_table = new WC_Pos_Groups_Table;
	$wc_crm_customer_groups->output();
}