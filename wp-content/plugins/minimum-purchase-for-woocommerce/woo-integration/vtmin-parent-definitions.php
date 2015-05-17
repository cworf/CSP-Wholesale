<?php
/*
VarkTech Minimum Purchase for WooCommerce
Woo-specific functions
Parent Plugin Integration
*/


class VTMIN_Parent_Definitions {
	
	public function __construct(){
    
    define('VTMIN_PARENT_PLUGIN_NAME',                      'WooCommerce');
    define('VTMIN_EARLIEST_ALLOWED_PARENT_VERSION',         '2.1.0');  //v1.0.9.5  plugin now uses WOO messaging to send messages to screen
    define('VTMIN_TESTED_UP_TO_PARENT_VERSION',             '1.6.6');
    define('VTMIN_DOCUMENTATION_PATH_PRO_BY_PARENT',        'http://www.varktech.com/woocommerce/minimum-purchase-pro-for-woocommerce/?active_tab=tutorial');                                                                                                     //***
    define('VTMIN_DOCUMENTATION_PATH_FREE_BY_PARENT',       'http://www.varktech.com/woocommerce/minimum-purchase-for-woocommerce/?active_tab=tutorial');      
    define('VTMIN_INSTALLATION_INSTRUCTIONS_BY_PARENT',     'http://www.varktech.com/woocommerce/minimum-purchase-for-woocommerce/?active_tab=instructions');
    define('VTMIN_PRO_INSTALLATION_INSTRUCTIONS_BY_PARENT', 'http://www.varktech.com/woocommerce/minimum-purchase-pro-for-woocommerce/?active_tab=instructions');
    define('VTMIN_PURCHASE_PRO_VERSION_BY_PARENT',          'http://www.varktech.com/woocommerce/minimum-purchase-pro-for-woocommerce/');
    define('VTMIN_DOWNLOAD_FREE_VERSION_BY_PARENT',         'http://wordpress.org/extend/plugins/minimum-purchase-for-woocommerce/');
    
    //html default selector locations in checkout where error message will display before.
    define('VTMIN_CHECKOUT_PRODUCTS_SELECTOR_BY_PARENT',    '.shop_table');        // PRODUCTS TABLE on BOTH cart page and checkout page
    define('VTMIN_CHECKOUT_ADDRESS_SELECTOR_BY_PARENT',     '#customer_details');      //  address area on checkout page    default = on
        
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED ); // v1.0.9

    global $vtmin_info;      
    $default_full_msg   =  __('Enter Custom Message (optional)', 'vtmin');   //v1.08 fixed v1.09
    $vtmin_info = array(                                                                    
      	'parent_plugin' => 'woo',
      	'parent_plugin_taxonomy' => 'product_cat',
        'parent_plugin_taxonomy_name' => 'Product Categories',
        'parent_plugin_cpt' => 'product',
        'applies_to_post_types' => 'product', //rule cat only needs to be registered to product, not rule as well...
        'rulecat_taxonomy' => 'vtmin_rule_category',
        'rulecat_taxonomy_name' => 'Minimum Purchase Rules',
        
        //elements used in vtmin-apply-rules.php at the ruleset level
        'error_message_needed' => 'no',
        'cart_grp_info' => '',
          /*  cart_grp_info will contain the following:
            array(
              'qty'    => '',
              'price'    => ''
            )
          */
        'cart_color_cnt' => '',
        'rule_id_list' => '',
        'line_cnt' => 0,
        'action_cnt'  => 0,
        'bold_the_error_amt_on_detail_line'  => 'no',
        'currPageURL'  => '',
        'woo_cart_url'  => '',
        'woo_checkout_url'  => '',
        'woo_pay_url'  => '',
        'default_full_msg'  => $default_full_msg //v1.08
      );

	}

} //end class
$vtmin_parent_definitions = new VTMIN_Parent_Definitions;
