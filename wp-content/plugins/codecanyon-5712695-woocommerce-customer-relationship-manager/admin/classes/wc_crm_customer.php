<?php
/**
 * @class 		WC_Crm_Customer
 * @version		1.0
 * @category	Class
 * @author   Actuality Extensions
 * @package  WooCommerce_Customer_Relationship_Manager/Classes
 * @since    2.4.3
 */

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WC_Crm_Customer {
	

	/**
	 * @var WC_Crm_Customer The single instance of the class
	 * @since 2.4.3
	 */
	protected static $_instance = null;

	/**
	 * Main WC_Crm_Customer Instance
	 *
	 * Ensures only one instance of WC_Crm_Customer is loaded or can be loaded.
	 *
	 * @since 2.4.3
	 * @static
	 * @return WC_Shipping Main instance
	 */
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
    add_action( 'wc_crm_restrict_list_customers', array($this, 'restrict_list_customers') );
    add_action( 'admin_post_export_csv', array($this, 'export_csv') );

    add_action( 'save_post_shop_order', array( $this, 'update_shop_order') );
    add_action( 'profile_update', array( $this, 'profile_update'), 10, 2 );
    add_action( 'user_register', array( $this, 'user_register'), 10, 2 );
    add_action( 'delete_user', array( $this, 'delete_customer'), 10, 2 );
	}  

  /**
     * Provides the select boxes to filter Customers, Country and Time Period.
     *
     */
    public function restrict_list_customers() {
      global $woocommerce;
      $woocommerce_crm_filters = get_option( 'woocommerce_crm_filters' );
      $_customer_date_from = '';
      $_user_type = '';
      $_customer_state = '';
      $_customer_city = '';
      $_customer_user = '';
      $_customer_country = '';
      $_customer_product = '';
      $_order_status = '';
      if( !empty($woocommerce_crm_filters) ) :
        ?>
        <div class="alignleft actions">
          <?php
            foreach ($woocommerce_crm_filters as $key => $value) {
                add_action( 'woocommerce_crm_add_filters', array($this, 'woocommerce_crm_'.$value.'_filter') );
            }
            do_action( 'woocommerce_crm_add_filters');
          ?>
        <input type="submit" id="post-query-submit" class="button action" value="Filter"/>

          <?php
          $_customer_date_from = isset( $_REQUEST['_customer_date_from'] ) ? $_REQUEST['_customer_date_from'] : '';
          $_user_type = isset( $_REQUEST['_user_type'] ) ? $_REQUEST['_user_type'] : '';
          $_customer_state = isset( $_REQUEST['_customer_state'] ) ? $_REQUEST['_customer_state'] : '';
          $_customer_city = isset( $_REQUEST['_customer_city'] ) ? $_REQUEST['_customer_city'] : '';
          $_customer_user = isset( $_REQUEST['_customer_user'] ) ? $_REQUEST['_customer_user'] : '';
          $_customer_country = isset( $_REQUEST['_customer_country'] ) ? $_REQUEST['_customer_country'] : '';
          $_customer_product = isset( $_REQUEST['_customer_product'] ) ? $_REQUEST['_customer_product'] : '';
          $_order_status = isset( $_REQUEST['_order_status'] ) ? $_REQUEST['_order_status'] : '';
          ?>
        </div>
        <?php
      endif;

      $js = "
              jQuery('#doaction').click(function(){
                var val = $('select[name=\"action\"]').val();
                if( val == 'export_csv'){
                 location.href='admin-post.php?action=export_csv&_customer_user=$_customer_user&_customer_country=$_customer_country&_customer_date_from=$_customer_date_from&_user_type=$_user_type&_customer_state=$_customer_state&_customer_city=$_customer_city&_customer_product=$_customer_product';
                return false;

               }
              });

                jQuery('select#dropdown_customer_status').css('width', '150px').chosen();

                jQuery('select#dropdown_country').css('width', '150px').chosen();

                jQuery('select#dropdown_state').css('width', '150px').chosen();

                jQuery('select#dropdown_city').css('width', '150px').chosen();

                jQuery('select#dropdown_date_from').css('width', '150px').chosen();

                jQuery('select#dropdown_user_type').css('width', '150px').chosen();

                jQuery('select#dropdown_order_status').css('width', '150px').chosen();

                jQuery('select#dropdown_products_categories').css('width', '400px').chosen();

                jQuery('select#dropdown_products_brands').css('width', '400px').chosen();

                jQuery('select#dropdown_product').css('width', '150px').ajaxChosen({
                    method:     'GET',
                    url:      '" . admin_url( 'admin-ajax.php' ) . "',
                    dataType:     'json',
                    afterTypeDelay: 100,
                    minTermLength:  3,
                    data:   {
                        action:   'woocommerce_crm_json_search_products',
                        security:   '" . wp_create_nonce( "search-products" ) . "',
                        default:  '" . __( 'Show all products', 'wc_customer_relationship_manager' ) . "',
                    }
                }, function (data) {

                    var terms = {};

                    $.each(data, function (i, val) {
                        terms[i] = val;
                    });

                    return terms;
                });

                jQuery('select#dropdown_customers').css('width', '200px').ajaxChosen({
                    method:     'GET',
                    url:      '" . admin_url( 'admin-ajax.php' ) . "',
                    dataType:     'json',
                    afterTypeDelay: 100,
                    minTermLength:  3,
                    data:   {
                        action:   'woocommerce_crm_json_search_customers',
                        security:   '" . wp_create_nonce( "search-customers" ) . "',
                        default:  '" . __( 'Show all customers', 'wc_customer_relationship_manager' ) . "',
                    }
                }, function (data) {

                    var terms = {};

                    $.each(data, function (i, val) {
                        terms[i] = val;
                    });

                    return terms;
                });



                jQuery('select#dropdown_products_and_variations').css('width', '400px').ajaxChosen({
                    method:     'GET',
                    url:      '" . admin_url( 'admin-ajax.php' ) . "',
                    dataType:     'json',
                    afterTypeDelay: 100,
                    minTermLength:  3,
                    data:   {
                        action:   'woocommerce_crm_json_search_variations',
                        security:   '" . wp_create_nonce( "search-products" ) . "',
                    }
                }, function (data) {

                    var terms = {};

                    $.each(data, function (i, val) {
                        terms[i] = val;
                    });

                    return terms;
                });
            ";

      if ( class_exists( 'WC_Inline_Javascript_Helper' ) ) {
        $woocommerce->get_helper( 'inline-javascript' )->add_inline_js( $js );
      } elseif( function_exists('wc_enqueue_js') ){
        wc_enqueue_js($js);
      }  else {
        $woocommerce->add_inline_js( $js );
      }

    }

    public function woocommerce_crm_customer_name_filter() {
      global $wpdb;
      ?>
      <select id="dropdown_customers" name="_customer_user">
          <option value=""><?php _e( 'Show all customers', 'wc_customer_relationship_manager' ) ?></option>
          <?php
          if ( !empty( $_REQUEST['_customer_user'] ) ) {
            $user = $_REQUEST['_customer_user'];
            $user_results = get_customer_by_term($user);

            foreach ($user_results as $user) {
              
              echo '<option value="' . $user->email . '" ';
              selected( 1, 1 );
              echo '>' . $user->first_name . ' ' . $user->last_name . ' (' . ( !empty( $user->user_id ) ? '#' . $user->user_id : __( "Guest", 'wc_customer_relationship_manager' ) ) . ' &ndash; ' . sanitize_email( $user->email ) . ')' . '</option>';

            }
            
          }
          ?>
        </select>
      <?php
    }

    
    public function customer_filter()
    {
      $filter = '';
      
      if(isset($_REQUEST['_user_type']) && !empty($_REQUEST['_user_type']) ){
      
        if($_REQUEST['_user_type'] == 'guest_user')
            $filter .= "AND (capabilities IS NULL OR capabilities = '' )
            ";
        else
            $filter .= "AND capabilities LIKE '%".$_REQUEST['_user_type']."%'
            ";
      }
      if( isset($_REQUEST['_customer_date_from']) && !empty( $_REQUEST['_customer_date_from'] ) ){
        $filter .= "AND  DATE(posts.post_date) >= '".date( 'Y-m-d', strtotime( $_REQUEST['_customer_date_from'] ) ) . "'
              ";
      }
      if( isset($_REQUEST['_customer_state']) && !empty( $_REQUEST['_customer_state'] ) ){
        $filter .= "AND customer.state = '". $_REQUEST['_customer_state'] . "'
        ";
      }
      if( isset($_REQUEST['_customer_city']) && !empty( $_REQUEST['_customer_city'] ) ){
          $filter .= "AND customer.city = '". $_REQUEST['_customer_city'] . "'
          ";
      }
      if( isset($_REQUEST['_customer_country']) && !empty( $_REQUEST['_customer_country'] ) ){
          $filter .= "AND customer.country = '". $_REQUEST['_customer_country'] . "'
          ";
      }
      
      if( isset($_REQUEST['_customer_user']) && !empty( $_REQUEST['_customer_user'] ) ){
        $term  = $_REQUEST['_customer_user'];
        $filter .= "AND customer.email = '$term'
          ";
      }
      if( isset($_REQUEST['s']) && !empty( $_REQUEST['s'] ) ){
        $term = $_REQUEST['s'];
        $filter .= "AND (
          (LOWER(fname.meta_value) LIKE LOWER('%$term%') OR LOWER(lname.meta_value) LIKE LOWER('%$term%') OR LOWER(customer.email) LIKE LOWER('%$term%') OR concat_ws(' ',fname.meta_value,lname.meta_value) LIKE '%$term%' )
          OR
          (LOWER(pfname.meta_value) LIKE LOWER('%$term%') OR LOWER(plname.meta_value) LIKE LOWER('%$term%') OR concat_ws(' ',pfname.meta_value,plname.meta_value) LIKE '%$term%' )
          )
          ";
      }       

      if( isset($_REQUEST['_customer_status']) && !empty( $_REQUEST['_customer_status'] ) ){
        $filter .= "AND customer.status LIKE '". $_REQUEST['_customer_status'] . "'
        ";
      }
      
      return $filter;


    }
    public function customer_join(){
      global $wpdb;
      $join = '';
      if( isset($_REQUEST['s']) && !empty( $_REQUEST['s'] ) ){
        $join = " LEFT JOIN {$wpdb->usermeta} fname ON (customer.user_id = fname.user_id AND fname.meta_key = 'first_name')";
        $join .= " LEFT JOIN {$wpdb->usermeta} lname ON (customer.user_id = lname.user_id AND lname.meta_key = 'last_name')";
        $join .= " LEFT JOIN {$wpdb->postmeta} pfname ON (customer.order_id = pfname.post_id AND pfname.meta_key = '_billing_first_name')";
        $join .= " LEFT JOIN {$wpdb->postmeta} plname ON (customer.order_id = plname.post_id AND plname.meta_key = '_billing_last_name')";
      }
      return $join;
    }
    function get_customer_sql($value='')
    {
      global $wpdb;
      if($value == '') return '';
      $filter = $this->customer_filter();
      $join   = $this->customer_join();

      if($value == 'order_status'){        
        $sql = "SELECT post_status, count(post_status) as count 
        FROM (
        SELECT posts.post_status as post_status FROM {$wpdb->prefix}wc_crm_customer_list as customer
        INNER JOIN {$wpdb->posts} posts ON (customer.order_id = posts.ID AND posts.post_status != 'trash' AND posts.post_status != 'auto-draft'  AND posts.post_type =  'shop_order')
        ) as crm_table
        group by post_status ";
      }else{
        $sql = "SELECT $value, count($value) as count
        FROM (
          SELECT $value FROM {$wpdb->prefix}wc_crm_customer_list as customer
          LEFT JOIN {$wpdb->posts} posts ON (customer.order_id = posts.ID)
          {$join}
          WHERE 1=1
          {$filter}
          ) as crm_table
          group by $value
      " ;
      }
      
      
      return $sql;
    }

    public function woocommerce_crm_customer_status_filter() {
    global $wpdb;
    $sql = $this->get_customer_sql('status');
    $customer_status = $wpdb->get_results($sql);
      ?>
      <select id="dropdown_customer_status" name="_customer_status">
          <option value=""><?php _e( 'Show all customer statuses', 'wc_customer_relationship_manager' ) ?></option>
          <?php
        foreach ($customer_status as $status) {
         if(!$status->status || $status->status == NULL) continue;
            if ( !empty( $_REQUEST['_customer_status'] ) && $_REQUEST['_customer_status'] == $status->status ) {
              echo '<option value="' . $status->status . '" ' . selected( 1, 1, false ) . '>' . $status->status . ' (' . $status->count . ')</option>';
            }else{
              echo '<option value="' . $status->status . '" >' . $status->status . ' (' . $status->count . ')</option>';
            }
          }
          ?>
        </select>
      <?php
    }
    public function woocommerce_crm_products_filter() {
      global $wpdb;
      ?>
      <select name='_customer_product' id='dropdown_product'>
          <option value=""><?php _e( 'Show all products', 'wc_customer_relationship_manager' ); ?></option>
          <?php
            $product_id = $_REQUEST['_customer_product'];
            if ( $product_id ) {
                $product = get_product( $product_id );
                echo '<option value="' . esc_attr( $product_id ) . '" selected="selected">' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
            }
          ?>
        </select>
      <?php
    }
    public function woocommerce_crm_country_filter() {
      global $wpdb, $woocommerce;
      $sql = $this->get_customer_sql('country');
      $order_countries = $wpdb->get_results($sql);
      ?>
      <select name='_customer_country' id='dropdown_country'>
          <option value=""><?php _e( 'Show all countries', 'wc_customer_relationship_manager' ); ?></option>
          <?php

          foreach ( $order_countries as $country ) {
      if(!$country->country || $country->country == NULL) continue;
            echo '<option value="' . $country->country . '" ';
            if ( !empty( $_REQUEST['_customer_country'] ) && $_REQUEST['_customer_country'] == $country->country ) {
              echo 'selected';
            }
            if (isset($woocommerce->countries->countries[$country->country])) 
              echo '>' . esc_html__( $country->country ) . ' - ' . $woocommerce->countries->countries[$country->country] . ' (' . absint( $country->count ) . ')</option>';
            else
              echo '>' . esc_html__( $country->country ) . ' (' . absint( $country->count ) . ')</option>';
          }
          ?>
        </select>
      <?php
    }
    public function woocommerce_crm_state_filter() {
      global $wpdb;
      $sql = $this->get_customer_sql('state');
      $order_states = $wpdb->get_results($sql);
      ?>
      <select name='_customer_state' id='dropdown_state'>
          <option value=""><?php _e( 'Show all states', 'wc_customer_relationship_manager' ); ?></option>
          <?php

          foreach ( $order_states as $state) {
      if(!$state->state || $state->state == NULL) continue;
            echo '<option value="' . $state->state . '" ';
            if ( !empty( $_REQUEST['_customer_state'] ) && $_REQUEST['_customer_state'] == $state->state ) {
              echo 'selected';
            }
            echo '>' . esc_html__( $state->state ) . ' (' . absint( $state->count ) . ')</option>';
          }
          ?>
        </select>
      <?php
    }
    public function woocommerce_crm_city_filter() {
      global $wpdb;
      $sql = $this->get_customer_sql('city');
      $order_city = $wpdb->get_results($sql);
      ?>
      <select name='_customer_city' id='dropdown_city'>
          <option value=""><?php _e( 'Show all cities', 'wc_customer_relationship_manager' ); ?></option>
          <?php

          foreach ( $order_city as $city ) {
      if(!$city->city || $city->city == NULL) continue;
            echo '<option value="' . $city->city . '" ';
            if ( !empty( $_REQUEST['_customer_city'] ) && $_REQUEST['_customer_city'] == $city->city ) {
              echo 'selected';
            }
            echo '>' . esc_html__( $city->city ) . ' (' . absint( $city->count ) . ')</option>';
          }
          ?>
        </select>
      <?php
    }
    public function woocommerce_crm_last_order_filter() {
      ?>
      <select name='_customer_date_from' id='dropdown_date_from'>
          <option value=""><?php _e( 'All time results', 'wc_customer_relationship_manager' ); ?></option>

          <option
            value="<?php echo date( 'Y-m-d H:00:00', strtotime( '-24 hours' ) ); ?>" <?php if ( !empty( $_REQUEST['_customer_date_from'] ) && date( 'Y-m-d H:00:00', strtotime( '-24 hours' ) ) == $_REQUEST['_customer_date_from'] ) {
            echo "selected";
          } ?> ><?php _e( 'Last 24 hours', 'wc_customer_relationship_manager' ); ?></option>

          <option
            value="<?php echo date( 'Y-m-01 00:00:00', strtotime( 'this month' ) ); ?>" <?php if ( !empty( $_REQUEST['_customer_date_from'] ) && date( 'Y-m-01 00:00:00', strtotime( 'this month' ) ) == $_REQUEST['_customer_date_from'] ) {
            echo "selected";
          } ?> ><?php _e( 'This month', 'wc_customer_relationship_manager' ); ?></option>
          <option
            value="<?php echo date( 'Y-m-d 00:00:00', strtotime( '-30 days' ) ); ?>" <?php if ( !empty( $_REQUEST['_customer_date_from'] ) && date( 'Y-m-d 00:00:00', strtotime( '-30 days' ) ) == $_REQUEST['_customer_date_from'] ) {
            echo "selected";
          } ?> ><?php _e( 'Last 30 days', 'wc_customer_relationship_manager' ); ?></option>
          <option
            value="<?php echo date( 'Y-m-d 00:00:00', strtotime( '-6 months' ) ); ?>" <?php if ( !empty( $_REQUEST['_customer_date_from'] ) && date( 'Y-m-d 00:00:00', strtotime( '-6 months' ) ) == $_REQUEST['_customer_date_from'] ) {
            echo "selected";
          } ?> ><?php _e( 'Last 6 months', 'wc_customer_relationship_manager' ); ?></option>
          <option
            value="<?php echo date( 'Y-m-d 00:00:00', strtotime( '-12 months' ) ); ?>" <?php if ( !empty( $_REQUEST['_customer_date_from'] ) && date( 'Y-m-d 00:00:00', strtotime( '-12 months' ) ) == $_REQUEST['_customer_date_from'] ) {
            echo "selected";
          } ?>><?php _e( 'Last 12 months', 'wc_customer_relationship_manager' ); ?></option>
        </select>
      <?php
    }
    public function woocommerce_crm_user_roles_filter() {
      global $wp_roles;
      ?>
      <select name='_user_type' id='dropdown_user_type'>
        <option value=""><?php _e( 'Show all user roles', 'wc_customer_relationship_manager' ); ?></option>
          <?php
          foreach ( $wp_roles->role_names as $role => $name ) : ?>
          <option value="<?php echo strtolower($name); ?>" <?php if ( !empty( $_REQUEST['_user_type'] ) && strtolower($name) == $_REQUEST['_user_type'] ) {
            echo "selected";
          } ?>><?php _e( $name, 'wc_customer_relationship_manager' ); ?>
        </option>

          <?php
          endforeach;
          $add_guest_customers = WC_Admin_Settings::get_option( 'woocommerce_crm_guest_customers', 'yes' );
          if($add_guest_customers == 'yes'){
            ?>
            <option value="guest_user" <?php if ( !empty( $_REQUEST['_user_type'] ) && 'guest_user' == $_REQUEST['_user_type'] ) echo "selected"; ?>>
              <?php _e( 'Guest', 'wc_customer_relationship_manager' ); ?>
            </option>
          <?php  } ?>
        </select>
      <?php
    }
    public function woocommerce_crm_products_variations_filter() {
      ?>
      <select name="_products_variations[]" id="dropdown_products_and_variations" multiple="multiple" data-placeholder="<?php _e( 'Search for a product&hellip;', 'woocommerce' ); ?>" style="width: 400px">
        <?php
            ;
            if ( isset($_REQUEST['_products_variations']) && $product_ids = $_REQUEST['_products_variations'] ) {
              foreach ( $product_ids as $product_id ) {
                $product = get_product( $product_id );
                echo '<option value="' . esc_attr( $product_id ) . '" selected="selected">' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
              }
            }
          ?>
      </select>

      <?php
    }
    public function woocommerce_crm_order_status_filter() {
      global $wpdb;
      $sql            = $this->get_customer_sql('order_status');
      $order_status   = $wpdb->get_results($sql);      
      $wc_statuses    = wc_get_order_statuses();
      ?>
      <select name='_order_status' id='dropdown_order_status'>
        <option value=""><?php _e( 'Show all statuses', 'woocommerce' ); ?></option>
        <?php
          foreach ( $order_status as $status ) {
            
            if(!isset($wc_statuses[$status->post_status])) continue;
            echo '<option value="' . esc_attr( $status->post_status ) . '"';

            if ( isset( $_REQUEST['_order_status'] ) ) {
              selected( $status->post_status, $_REQUEST['_order_status'] );
            }

            echo '>' . esc_html__( $wc_statuses[$status->post_status], 'woocommerce' )  . '</option>';
          }
        ?>
      </select>

      <?php
    }
    /****************/
    public function woocommerce_crm_products_categories_filter() {
      ?>
      <select name='_products_categories[]' id='dropdown_products_categories' multiple="multiple" data-placeholder="<?php _e( 'Search for a category&hellip;', 'woocommerce' ); ?>" >
        <?php
          $cat = array();
          if ( isset( $_REQUEST['_products_categories'] ) ) {
            $cat = $_REQUEST['_products_categories'];
          }
          $all_cat = get_terms( array('product_cat'),  array( 'orderby' => 'name', 'order' => 'ASC')  );
          if(!empty($all_cat)){
            foreach ($all_cat as $key => $value) {
              echo '<option value="'.$value->term_id.'" '.( in_array($value->term_id, $cat) ? 'selected="selected"' : '' ).'>'.$value->name.'</option>';
            }
          }
        ?>
      </select>

      <?php
    }
    public function woocommerce_crm_products_brands_filter() {
      if( class_exists( 'WC_Brands_Admin' ) ) {
        ?>
        <select name='_products_brands[]' id='dropdown_products_brands' multiple="multiple" data-placeholder="<?php _e( 'Search for a brand&hellip;', 'woocommerce' ); ?>" >
          <?php $brand = array();
                if ( isset( $_REQUEST['_products_brands'] ) ) {
                  $brand = $_REQUEST['_products_brands'];
                }
                $all_brands = get_terms( array('product_brand'),  array( 'orderby' => 'name', 'order' => 'ASC')  );
                if(!empty($all_brands)){
                  foreach ($all_brands as $key => $value) {
                    echo '<option value="'.$value->term_id.'" '.( in_array($value->term_id, $brand) ? 'selected="selected"' : '' ).'>'.$value->name.'</option>';
                  }
                }
          ?>
        </select>

        <?php
      }
    }


    /**
     * Handle CSV file download
     */
    function export_csv() {
      header( 'Content-Type: application/csv' );
      header( 'Content-Disposition: attachment; filename=customers_' . date( 'Y-m-d' ) . '.csv' );
      header( 'Pragma: no-cache' );

      $__wc_crm_customer_details = new WC_Crm_Customer_Details(0, 0);
      $__wc_crm_customer_details->init_address_fields('', '', false);
      $__b_address = $__wc_crm_customer_details->billing_fields;
      $__s_address = $__wc_crm_customer_details->shipping_fields;

      $o    = WC_CRM()->orders();
      $data = $o->get_orders();
      

      echo '"Customer name",';
      foreach ($__b_address as $key => $label) {
            if($key=='first_name' || $key=='last_name') continue;
            echo '"Billing ' . $label['label'] . '",';
      }
      foreach ($__s_address as $key => $label) {
        if($key=='first_name' || $key=='last_name') continue;
        echo '"Shipping ' . $label['label'] . '",';
      }
      echo '"Username",';
      echo '"Last purchase date",';
      echo '"Number of orders",';
      echo '"Total value",';
      echo "\"Subscribed\"\n";

      if ( woocommerce_crm_mailchimp_enabled() ) {
        $members = woocommerce_crm_get_members();
      }
      foreach ( $data as $item ) {
      //$item = get_object_vars ( $customer );
        if($item['user_id'] ){
          $user_id = $item['user_id'];
          $wc_crm_customer_details = new WC_Crm_Customer_Details($user_id, 0);
          $wc_crm_customer_details->init_address_fields('', '', false);
          $b_address = $wc_crm_customer_details->billing_fields;

          $s_address = $wc_crm_customer_details->shipping_fields;

          $data = get_user_meta($item['user_id']);

          echo '"' . $data['first_name'][0] . ' '.$data['last_name'][0].'",';

          foreach ($b_address as $key => $value) {
            if($key=='first_name' || $key=='last_name') continue;
            if($key=='country') {
              echo '"' . $item['country'] . '",';
              continue;
            }
            if($key=='email') {
              echo '"' . $item['email'] . '",';
              continue;
            }
            $field_name = 'billing_' . $key;
            $field_value = get_the_author_meta( $field_name , $user_id );
            echo '"' . $field_value . '",';
          }
          foreach ($s_address as $key => $value) {
            if($key=='first_name' || $key=='last_name') continue;
            $field_name = 'shipping_' . $key;
            $field_value = get_the_author_meta( $field_name , $user_id );
            echo '"' . $field_value . '",';
          }
          $user = @get_userdata( $user_id );
          echo '"' . ( isset( $user->user_login ) ? $user->user_login : __( 'Guest', 'wc_customer_relationship_manager' ) )  . '",';


          $item['num_orders']  = wc_crm_get_num_orders($item['user_id']);
          $item['total_spent'] =  wc_crm_get_order_value($item['user_id']);
        }else{
          $order_id = $item['order_id'];
          $user_id = 0;
          $order = new WC_Order( $order_id );
          $wc_crm_customer_details_g = new WC_Crm_Customer_Details(0, $order_id);
          $wc_crm_customer_details_g->init_address_fields('', '', false);
          $b_address = $wc_crm_customer_details_g->billing_fields;
          $s_address = $wc_crm_customer_details_g->shipping_fields;

          $first_name = get_post_meta($item['order_id'], '_billing_first_name', true);
          $last_name  = get_post_meta($item['order_id'], '_billing_last_name', true);

          echo '"' . $first_name . ' '.$last_name.'",';


          foreach ($b_address as $key => $value) {
            if($key=='first_name' || $key=='last_name') continue;
            if($key=='country') {
              echo '"' . $item['country'] . '",';
              continue;
            }
            if($key=='email') {
              echo '"' . $item['email'] . '",';
              continue;
            }
            
            $name_var = 'billing_'.$key;
            $field_value = $wc_crm_customer_details_g->order->$name_var;
            echo '"' . $field_value . '",';
          }
          foreach ($s_address as $key => $value) {
            if($key=='first_name' || $key=='last_name') continue;
            $var_name = 'shipping_'.$key;
            $field_value = $wc_crm_customer_details_g->order->$name_var;
            echo '"' . $field_value . '",';
          }
          echo '"' .  __( 'Guest', 'wc_customer_relationship_manager' )  . '",';

          $item['num_orders']  = wc_crm_get_num_orders( $item['email'], '_billing_email', true);
          $item['total_spent'] = wc_crm_get_order_value($item['email'], '_billing_email', true);
        }


        echo '"' . $item['last_purchase'] ? woocommerce_crm_get_pretty_time( $item['last_purchase'], true ) : ''. '",';
        echo '"' . $item['num_orders'] . '",';        
        if ( woocommerce_crm_mailchimp_enabled() ) {
          $enrolled_plain = in_array( $item['email'], $members ) ? 'yes' : 'no';
          echo '"' . $item['total_spent'] . '",';
          echo '"' . $enrolled_plain . "\"\n";
        }else{
          echo '"' . $item['total_spent'] . "\"\n";
        }
      }

    }

    function profile_update($user_id = ''){
        if(!$user_id || empty($user_id)) return;
        global $wpdb;
        
        $results = $wpdb->get_results("SELECT email FROM {$wpdb->prefix}wc_crm_customer_list WHERE user_id = $user_id LIMIT 1");
        if($results){
          $caps    = $wpdb->get_var("SELECT meta_value FROM {$wpdb->usermeta} WHERE user_id = $user_id AND meta_key = '{$wpdb->prefix}capabilities' LIMIT 1 ");
          $status  = get_user_meta($user_id, 'customer_status', true);
          if(!$status || empty($status)) $status = 'Customer';
          $state   = get_user_meta($user_id, 'billing_state', true);
          $city    = get_user_meta($user_id, 'billing_city', true);
          $country = get_user_meta($user_id, 'billing_country', true);

          $userdata = get_userdata($user_id);
          $email    = $userdata->user_email;
          $sql = "UPDATE {$wpdb->prefix}wc_crm_customer_list
                  SET status = '{$status}',
                  email = '{$email}',
                  capabilities = '{$caps}',
                  state = '$state',
                  city = '$city',
                  country = '$country'
                  WHERE user_id = $user_id
          ";
          $wpdb->query($sql);          
        }else{
          $this->user_register($user_id);
        }
    }

    function user_register($user_id = ''){
        if(!$user_id || empty($user_id)) return;
        global $wpdb;
        
        $user_data = get_userdata($user_id);
        if($user_data){
          $caps    = $wpdb->get_var("SELECT meta_value FROM {$wpdb->usermeta} WHERE user_id = $user_id AND meta_key = '{$wpdb->prefix}capabilities' LIMIT 1 ");
          $status  = get_user_meta($user_id, 'customer_status', true);
          if(!$status) $status = 'Customer';
          $state   = get_user_meta($user_id, 'billing_state', true);
          $city    = get_user_meta($user_id, 'billing_city', true);
          $country = get_user_meta($user_id, 'billing_country', true);
          $email    = $userdata->user_email;

          $sql = "INSERT INTO {$wpdb->prefix}wc_crm_customer_list 
                  (email, user_id, capabilities, status, city, state, country, order_id) 
                  VALUES ('$email', $user_id, '{$caps}', '{$status}', '{$city}', '{$state}', '{$country}', null)
          ";
          $wpdb->query($sql);
        }
    }

    function update_shop_order($post_id){ 
      $post_status = get_post_status( $post_id );
      if ( wp_is_post_revision( $post_id ) || $post_status == 'auto-draft' || $post_status == 'draft' )
        return;
      
      global $wpdb;
      $user_id    = get_post_meta( $post_id, '_customer_user', true );
      $user_email = get_post_meta( $post_id, '_billing_email', true);
      
      if(!$user_id && $user_email){
        
        $state      = get_post_meta( $post_id, '_billing_state', true);
        $city       = get_post_meta( $post_id, '_billing_city', true);
        $country    = get_post_meta( $post_id, '_billing_country', true);

        $customer = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wc_crm_customer_list as customer
         WHERE email = '$user_email' AND customer.user_id = 0 LIMIT 1 ");        

        if($customer){
          $order_id = $customer[0]->order_id;
          if($order_id && $order_id < $post_id){
            
            $sql = "UPDATE {$wpdb->prefix}wc_crm_customer_list
                    SET status = '',
                    email = '$user_email',
                    state = '$state',
                    city = '$city',
                    country = '$country',
                     order_id = $post_id
                    WHERE order_id = $order_id
            ";
            $wpdb->query($sql);
          }
        }else{
          $sql = "INSERT INTO {$wpdb->prefix}wc_crm_customer_list 
                  (email, user_id, capabilities, status, city, state, country, order_id) 
                  VALUES ('$user_email', 0, '', '', '{$city}', '{$state}', '{$country}', $post_id)
          ";
          $wpdb->query($sql);
        }
      }
      else if($user_id){
        $user = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wc_crm_customer_list WHERE user_id = $user_id"); 
        if($user){
          $order_id = $user[0]->order_id;
          if($order_id && $order_id < $post_id){
            $sql = "UPDATE {$wpdb->prefix}wc_crm_customer_list
                    SET order_id = $post_id
                    WHERE user_id = $user_id
            ";
            $wpdb->query($sql);
          }
        }else{
          $this->user_register($user_id);

        }
      }
    }

    function delete_customer($user_id){
      if(!$user_id) return;
      global $wpdb;
        $sql = "DELETE FROM {$wpdb->prefix}wc_crm_customer_list
                WHERE user_id = '$user_id'
        ";
        $wpdb->query($sql);
    }
  
} //end class
new WC_Crm_Customer();