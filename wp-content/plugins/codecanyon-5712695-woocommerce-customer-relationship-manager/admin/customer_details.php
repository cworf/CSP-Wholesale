<?php
/**
 * Logic related to displaying CRM new Customer page.
 *
 * @author   Actuality Extensions
 * @package  WooCommerce_Customer_Relationship_Manager
 * @since    1.0
 */

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
require_once( 'classes/wc_crm_customer_details.php' );


function wc_customer_relationship_manager_new_customer_add_options(){
	wc_customer_relationship_manager_new_customer_helper();

}
function wc_customer_relationship_manager_new_customer_helper() {
	$screen = get_current_screen();
	//print_R($screen);
	if ( $screen->id == 'customers_page_wc_new_customer'){
		$help = '<p>' . __('To add a new customer to your site, fill in the form on this screen and click the Add New Customer button at the bottom.') . '</p>';
		$help .= '<p>' . __('You must assign a password to the new user, which they can change after logging in. The username, however, cannot be changed.') . '</p>' .
		'<p>' . __('New users will receive an email letting them know they&#8217;ve been added as a user for your site. By default, this email will also contain their password. Uncheck the box if you don&#8217;t want the password to be included in the welcome email.') . '</p>';
		$help .= '<p>' . __('Remember to click the Add New Customer button at the bottom of this screen when you are finished.') . '</p>';

		$screen->add_help_tab( array(
				'id'      => 'overview',
				'title'   => __('Overview'),
				'content' => $help,
			) );
    return $help;
	}
}
/**
 * Renders "Add new Customer"  page.
 */

function wc_customer_relationship_manager_render_new_customer_page() {
  if( isset($_GET['user_id']) && !empty($_GET['user_id'])){
    $user_id = $_GET['user_id'];
    $order_id = 0;
  }elseif (isset($_GET['order_id']) && !empty($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    $user_id = 0;
  }
  else{
    $user_id = 0;
    $order_id = 0;
  }

  $wc_crm_customer_details = new WC_Crm_Customer_Details($user_id, $order_id);
  if (isset($_GET['screen']) && $_GET['screen'] == 'customer_notes') {
    $wc_crm_customer_details->display_notes();
  }else{
	  $wc_crm_customer_details->display();
  }
}

add_action( 'admin_init', 'wc_crm_actions_customer_detail' );
function wc_crm_actions_customer_detail() {
    if(isset($_GET['page']) && $_GET['page'] != 'wc_new_customer') return;
    $wc_crm_customer_details = new WC_Crm_Customer_Details(0, 0);
    
    if (isset($_POST['wc_crm_customer_action']) ) {
      if(isset($_POST['customer_user']) && !empty( $_POST['customer_user'] )){
        $wc_crm_customer_details->save($_POST['customer_user']);
      }else{
        $wc_crm_customer_details->create_user();
      }
    }
}