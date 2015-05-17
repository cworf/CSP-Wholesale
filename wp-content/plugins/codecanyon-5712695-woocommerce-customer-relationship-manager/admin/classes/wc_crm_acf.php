<?php

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WC_Crm_ACF {
	

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
    add_filter( 'init', array($this, 'init__')  );
    add_filter( 'acf/location/rule_types', array($this, 'acf_location_rule_types'), 10, 1  );
    add_filter( 'acf/location/rule_values/ef_crm_customers', array($this, 'ef_crm_customers_rule_values'), 10, 1  );

    add_action('admin_enqueue_scripts',				array($this, 'admin_enqueue_scripts'));

    #add_filter('acf/location/match_field_groups', array($this, 'match_field_groups'), 15, 2);
    add_filter('acf/location/rule_match/ef_crm_customers', array($this, 'rule_match_customer_type'), 10, 3);
    add_filter('acf/get_post_id', array($this, 'get_post_id'), 20, 1);
    add_filter('acf/input/admin_head', array($this, 'admin_head'), 20);
   
	}
  function init__(){
    if(isset($_GET['page']) && $_GET['page'] == 'wc_new_customer'  && !isset($_GET['order_id'])  ){
      global $post;
      $post = NULL;
      if( isset($_GET['user_id']) )
        $post->ID = 'user_'.$_GET['user_id'];
      else
        $post->ID = 'user_';
    }
  }
	function get_post_id($post_id){
		if(isset($_GET['page']) && $_GET['page'] == 'wc_new_customer'  && !isset($_GET['order_id']) ){
      if(isset($_GET['user_id']) && !empty($_GET['user_id']))
        $post_id = 'user_'.$_GET['user_id'];
      else
        $post_id = 'user_';
		}
		return $post_id;
	}
	function admin_head(){
		if(isset($_GET['page']) && $_GET['page'] == 'wc_new_customer'  && !isset($_GET['order_id'])){
			global $post;
			$post = NULL;
		}
	}

	function rule_match_customer_type( $match, $rule, $options )
	{
		if(isset($_GET['page']) && $_GET['page'] == 'wc_new_customer'  && !isset($_GET['order_id'])  )
		{
      if( isset($_GET['user_id']) && !empty($_GET['user_id']) ){
  			$user = $_GET['user_id'];
  			if($rule['operator'] == "==")
        {
        	$match = ( user_can($user, $rule['value']) );
        	
        	// override for "all"
          if( $rule['value'] === "all" )
  				{
  					$match = true;
  				}
        }
        elseif($rule['operator'] == "!=")
        {
        	$match = ( !user_can($user, $rule['value']) );
        	
        	// override for "all"
  	      if( $rule['value'] === "all" )
  				{
  					$match = false;
  				}
        }
  		}else{
        $match = true;
      }
    }
    return $match;
        
  }
  
  public function acf_location_rule_types($choices)
  {
   $choices[__("Other",'acf')]['ef_crm_customers'] = __("Customer",'wc_customer_relationship_manager');
   return $choices;
  }
  
  public function ef_crm_customers_rule_values($choices)
  {
  	global $wp_roles;
   	$choices = array_merge( array('all' => __('All', 'acf')), $wp_roles->get_names() );
   	return $choices;
  }

  public function admin_enqueue_scripts()
	{
		// validate page
		if(isset($_GET['page']) && $_GET['page'] == 'wc_new_customer' && !isset($_GET['order_id']) ){
			global $typenow, $post;
      if(isset($_GET['user_id']) && !empty($_GET['user_id']))
			 $post->ID = 'user_'.$_GET['user_id'];
      else
        $post->ID = 'user_';
			$typenow = 'crm_customers';
			do_action('acf/input/admin_enqueue_scripts');
			
			$acf_controller_post = new acf_controller_post;
			add_action('admin_head', array($acf_controller_post, 'admin_head') );
		}
	}

} //end class
new WC_Crm_ACF;