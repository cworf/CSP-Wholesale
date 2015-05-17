<?php
/**
 * WooCommerce API Orders Class
 *
 * Handles requests to the /orders endpoint
 *
 * @author      WooThemes
 * @category    API
 * @package     WooCommerce/API
 * @since       2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_Crm_Orders {

  protected static $_instance = null;

  public static function instance() {
    if ( is_null( self::$_instance ) )
      self::$_instance = new self();
    return self::$_instance;
  }
  /**
   * Cloning is forbidden.
   *
   */
  public function __clone() {
    _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce' ), '2.4.3' );
  }

  /**
   * Unserializing instances of this class is forbidden.
   *
   */
  public function __wakeup() {
    _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce' ), '2.4.3' );
  }

  /**
   * __construct function.
   *
   * @access public
   * @return void
   */
  public function __construct() {
    
  }

	public function get_sql() {

		global $wpdb;

		$woocommerce_crm_user_roles = get_option('woocommerce_crm_user_roles');
    if(!$woocommerce_crm_user_roles || empty($woocommerce_crm_user_roles)){
      $woocommerce_crm_user_roles[] = 'customer';
    }
    $add_guest_customers = WC_Admin_Settings::get_option( 'woocommerce_crm_guest_customers', 'yes' );
    $user_role_filter = '';
    foreach ($woocommerce_crm_user_roles as $value) {
      if ( !empty($user_role_filter)) $user_role_filter .=  ' OR ';
      $user_role_filter .= "customer.capabilities LIKE '%{$value}%'";
    }

    /******************/
    
    $filter = '';
    $join   = '';
    $inner  = '';


    /*****************/

    if( ( isset($_REQUEST['group']) && !empty( $_REQUEST['group'] ) ) ){
      $group_id = $_REQUEST['group'];
      $group_data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wc_crm_groups WHERE ID = $group_id");
      if($group_data[0]->group_type == 'static'){
        $inner .= "
        inner join {$wpdb->prefix}wc_crm_groups_relationships groups_rel on (groups_rel.customer_email = customer.email AND groups_rel.group_id = {$group_id} )
        ";
      }else if( ( isset($_REQUEST['group']) && !empty( $_REQUEST['group'] ) ) ){
        $group_id = $_REQUEST['group'];
        $group_data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wc_crm_groups WHERE ID = $group_id");
        if($group_data[0]->group_type == 'dynamic'){
          if(!empty($group_data[0]->group_total_spent)){
            $spent = $group_data[0]->group_total_spent;
            $mark  = $group_data[0]->group_total_spent_mark;
            switch ($mark) {
              case 'greater':
                $mark = '>';
                break;            
              case 'less':
                $mark = '<';
                break;            
              case 'greater_or_equal':
                $mark = '>=';
                break;
              case 'less_or_equal':
                $mark = '<=';
                break;
              default:
                $mark = '=';
                break;
            }
            #$filter .= " AND {$wpdb->prefix}wc_crm_customers.total_spent $mark $spent 
            #";
            
          }
          if( !empty($group_data[0]->group_user_role) ){
            $group_user_role = $group_data[0]->group_user_role;
              if($group_user_role != 'any'){
                if($group_user_role == 'guest_user')
                    $filter .= "AND (capabilities IS NULL OR capabilities = '' )
                    ";
                else
                    $filter .= "AND capabilities LIKE '%".$group_user_role."%'
                    ";
              }
          }
          if( !empty($group_data[0]->group_customer_status) ){
            $group_customer_status = unserialize($group_data[0]->group_customer_status);
            if(!empty($group_customer_status)){
              if(count($group_customer_status) > 1 || !empty($group_customer_status[0]) )
                $filter .= "AND  status IN( '". implode("', '", $group_customer_status) . "' )
                ";
            }
          }

          if( !empty($group_data[0]->group_order_status) ){
            $group_order_status = unserialize($group_data[0]->group_order_status);
            if(!empty($group_order_status)){
              if(count($group_order_status) > 1 || !empty($group_order_status[0]) )
                $_REQUEST['_order_status'] = $group_order_status;
            }
          }
          $d_from = false;
          if( !empty($group_data[0]->group_last_order_from) &&  strtotime( $group_data[0]->group_last_order_from ) !== false ){            
              $d_from = strtotime( $group_data[0]->group_last_order_from );
          }
          $d_to = false;
          if( !empty($group_data[0]->group_last_order_to) &&  strtotime( $group_data[0]->group_last_order_to ) !== false ){            
              $d_to = strtotime( $group_data[0]->group_last_order_to );
          }
          if( $d_to || $d_from ){
              $mark = $group_data[0]->group_last_order;
              switch ($mark) {
                case 'before':
                  $filter .= "AND  DATE(posts.post_date) <= '".date( 'Y-m-d', $d_from ) . "'
                  ";
                  break;
                case 'after':
                  $filter .= "AND  DATE(posts.post_date) >= '".date( 'Y-m-d', $d_from ) . "'
                  ";
                  break;
                case 'between':
                  $filter .= "AND  DATE(posts.post_date) >= '".date( 'Y-m-d', $d_from ) . "' AND  DATE(posts.post_date) <= '".date( 'Y-m-d', $d_to ) . "'
                  ";
                  break;
              }
          }

          /****************/
        }
      }
    }
    /*****************/


    if( (isset($_REQUEST['_customer_product']) && !empty( $_REQUEST['_customer_product'] ) ) 
      || (isset($_REQUEST['_products_variations']) && !empty( $_REQUEST['_products_variations'] ))
      || (isset($_REQUEST['_order_status']) && !empty( $_REQUEST['_order_status'] ))
      || (isset($_REQUEST['_products_categories']) && !empty( $_REQUEST['_products_categories'] ))
      || (isset($_REQUEST['_products_brands']) && !empty( $_REQUEST['_products_brands'] ))
      ){
      $inner .= "
      inner join {$wpdb->postmeta} on ({$wpdb->postmeta}.meta_value = customer.email AND {$wpdb->postmeta}.meta_key = '_billing_email' AND customer.email != '' )
      ";
    }
    if( (isset($_REQUEST['_customer_product']) && !empty( $_REQUEST['_customer_product'] )) 
      || (isset($_REQUEST['_products_variations']) && !empty( $_REQUEST['_products_variations'] ))
      || (isset($_REQUEST['_products_categories']) && !empty( $_REQUEST['_products_categories'] ))
      || (isset($_REQUEST['_products_brands']) && !empty( $_REQUEST['_products_brands'] ))
      ){
      $inner .= "
      inner join {$wpdb->prefix}woocommerce_order_items on {$wpdb->prefix}woocommerce_order_items.order_id = {$wpdb->postmeta}.post_id
      ";
    }
    if( (isset($_REQUEST['_customer_product']) && !empty( $_REQUEST['_customer_product'] )) 
      || (isset($_REQUEST['_products_categories']) && !empty( $_REQUEST['_products_categories'] ))
      || (isset($_REQUEST['_products_brands']) && !empty( $_REQUEST['_products_brands'] ))
      ){
      $inner .= "     
      inner join  {$wpdb->prefix}woocommerce_order_itemmeta as product on ( product.order_item_id = {$wpdb->prefix}woocommerce_order_items.order_item_id and product.meta_key = '_product_id' ) ";
    }

    if((isset($_REQUEST['_products_categories']) && !empty( $_REQUEST['_products_categories'] ))
      || (isset($_REQUEST['_products_brands']) && !empty( $_REQUEST['_products_brands'] ))
      ){
      $tax = '';
      if(isset($_REQUEST['_products_categories'])) $tax .= "taxonomy.taxonomy = 'product_cat'";
      if(isset($_REQUEST['_products_brands'])){
        if(!empty($tax))
          $tax .= ' OR ';
        $tax .= "taxonomy.taxonomy = 'product_brand'";
      }
      $inner .= "
          inner join  {$wpdb->prefix}term_relationships as relationships on (relationships.object_id =  product.meta_value ) 
          inner join  {$wpdb->prefix}term_taxonomy as taxonomy on (relationships.term_taxonomy_id = taxonomy.term_taxonomy_id AND ($tax) ) 
          ";            
    }

    if( isset($_REQUEST['_order_status']) && !empty( $_REQUEST['_order_status'] ) ){
      $request = $_REQUEST['_order_status'];

      if(is_array($request)){
        $inner .= "
              inner JOIN {$wpdb->posts} posts_status
              ON ({$wpdb->postmeta}.post_id= posts_status.ID AND posts_status.post_status IN( '". implode("', '", $request) . "') AND posts_status.post_type =  'shop_order' )
        ";  
      }else if(is_string($request)){
        $inner .= "
              inner JOIN {$wpdb->posts} posts_status
              ON ({$wpdb->postmeta}.post_id= posts_status.ID AND posts_status.post_status = '{$request}'  AND posts_status.post_type =  'shop_order' )
          ";
      }
      
    }
    if( isset($_REQUEST['_products_categories']) && !empty( $_REQUEST['_products_categories'] ) ){
        $y = '';
        foreach ($_REQUEST['_products_categories'] as $v){
          if ($y){
            $ff .= ' OR ';
          }
          else{
            $y = 'OR';
            $ff = '
            AND (';
          }
          $ff .= " (taxonomy.term_id = " . $v . " AND taxonomy.taxonomy = 'product_cat' )";
        }
        $filter .= $ff . ')';
        
    }
    if( isset($_REQUEST['_products_brands']) && !empty( $_REQUEST['_products_brands'] ) ){
      $y = '';
        foreach ($_REQUEST['_products_brands'] as $v){
          if ($y){
            $ff .= ' OR ';
          }
          else{
            $y = 'OR';
            $ff = '
            AND (';
          }
          $ff .= " (taxonomy.term_id = " . $v . " AND taxonomy.taxonomy = 'product_brand' )";
        }
        $filter .= $ff . ')';
    }

    if( isset($_REQUEST['_customer_product']) && !empty( $_REQUEST['_customer_product'] ) ){
      $filter.= " AND product.meta_value = " . $_REQUEST['_customer_product'];
    }

    if( isset($_REQUEST['_products_variations']) && !empty( $_REQUEST['_products_variations'] ) ){
      $y = '';
      foreach ($_REQUEST['_products_variations'] as $v){
        if ($y){
          $ff .= ' OR ';
        }
        else{
          $y = ' OR ';
          $ff = ' AND (';
        }
        $ff .= 'variation.meta_value = ' . $v;
      }
      $filter .= $ff . ')';
      $inner .= "
        inner join  {$wpdb->prefix}woocommerce_order_itemmeta as variation on (variation.order_item_id =  {$wpdb->prefix}woocommerce_order_items.order_item_id and variation.meta_key = '_variation_id' ) 
        ";
    }
    /*****************/
    if( isset($_REQUEST['_customer_date_from']) && !empty( $_REQUEST['_customer_date_from'] ) ){
      $filter .= " AND  DATE(posts.post_date) >= '".date( 'Y-m-d', strtotime( $_REQUEST['_customer_date_from'] ) ) . "'
            ";
    }
    if( isset($_REQUEST['_customer_state']) && !empty( $_REQUEST['_customer_state'] ) ){
      $filter .= " AND customer.state = '". $_REQUEST['_customer_state'] . "'
      ";
    }
    if( isset($_REQUEST['_customer_city']) && !empty( $_REQUEST['_customer_city'] ) ){
        $filter .= " AND customer.city = '". $_REQUEST['_customer_city'] . "'
        ";
    }
    if( isset($_REQUEST['_customer_country']) && !empty( $_REQUEST['_customer_country'] ) ){
        $filter .= " AND customer.country = '". $_REQUEST['_customer_country'] . "'
        ";
    }
    if( isset($_REQUEST['_customer_status']) && !empty( $_REQUEST['_customer_status'] ) ){
          $filter .= " AND  customer.status LIKE '". $_REQUEST['_customer_status'] . "'
          ";
      }
    if( isset($_REQUEST['_customer_user']) && !empty( $_REQUEST['_customer_user'] ) ){
      $term  = $_REQUEST['_customer_user'];
      $filter .= " AND customer.email = '$term'
        ";
    }
    if( isset($_REQUEST['s']) && !empty( $_REQUEST['s'] ) ){
      $term = $_REQUEST['s'];
      $join = " LEFT JOIN {$wpdb->usermeta} fname ON (customer.user_id = fname.user_id AND fname.meta_key = 'first_name')";
      $join .= " LEFT JOIN {$wpdb->usermeta} lname ON (customer.user_id = lname.user_id AND lname.meta_key = 'last_name')";
      $join .= " LEFT JOIN {$wpdb->postmeta} pfname ON (customer.order_id = pfname.post_id AND pfname.meta_key = '_billing_first_name')";
      $join .= " LEFT JOIN {$wpdb->postmeta} plname ON (customer.order_id = plname.post_id AND plname.meta_key = '_billing_last_name')";

      $filter .= " AND (
        (LOWER(fname.meta_value) LIKE LOWER('%$term%') OR LOWER(lname.meta_value) LIKE LOWER('%$term%') OR LOWER(customer.email) LIKE LOWER('%$term%') OR concat_ws(' ',fname.meta_value,lname.meta_value) LIKE '%$term%' )
        OR
        (LOWER(pfname.meta_value) LIKE LOWER('%$term%') OR LOWER(plname.meta_value) LIKE LOWER('%$term%') OR concat_ws(' ',pfname.meta_value,plname.meta_value) LIKE '%$term%' )
        )
        ";
    }
    /******************/ 
    if(!empty($user_role_filter)){

      if($add_guest_customers == 'yes'){
        $user_role_filter = ' AND ('.$user_role_filter.' OR customer.user_id IS NULL OR customer.user_id = 0 ) ';
      }else{
        $user_role_filter = ' AND ('.$user_role_filter.') ';
      }

    }
    if(isset($_REQUEST['_user_type']) && !empty($_REQUEST['_user_type']) ){
      if($_REQUEST['_user_type'] == 'guest_user'){
        $user_role_filter = " AND ( customer.user_id IS NULL  OR customer.user_id = 0 )
        ";
      }
      else{
        $user_role_filter = " AND (customer.capabilities LIKE '%".$_REQUEST['_user_type']."%') ";
      }
    }
    $filter .= $user_role_filter;

    /******************/

		$sql = "SELECT customer.*, posts.post_date as last_purchase FROM {$wpdb->prefix}wc_crm_customer_list as customer
      LEFT JOIN {$wpdb->posts} posts ON (customer.order_id = posts.ID)
      {$inner}
			{$join}

			WHERE 1=1
			{$filter}

      GROUP BY customer.email

		 ";
     #echo '<textarea name="" id="" style="width: 100%; height: 200px; ">'.$sql.'</textarea>';
		return $sql;
	}
	public function get_orders( $page = 1, $limit = 10 ) {
		global $wpdb;

		$sql = $this->get_sql();
		

		$result = $wpdb->get_results($sql, ARRAY_A );
    $this->orders_ount = count($result);
		return $result;
	}

	public function get_orders_ount() {

		global $wpdb;
		$sql = $this->get_sql();

		$result = count($wpdb->get_results($sql) );

		return $result;
	}



}
