<?php
/**
 * @class 		WC_Crm_Install
 * @version		1.0
 * @category	Class
 * @author   Actuality Extensions
 * @package  WooCommerce_Customer_Relationship_Manager/Classes
 * @since    2.4.3
 */

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WC_Crm_Install {
	

	/**
	 * @var WC_Crm_Install The single instance of the class
	 * @since 2.4.3
	 */
	protected static $_instance = null;

	/**
	 * Main WC_Crm_Install Instance
	 *
	 * Ensures only one instance of WC_Crm_Install is loaded or can be loaded.
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
		// Run this on activation.
		register_activation_hook( WC_CRM_PLUGIN_FILE, array( $this, 'activation' ) );
    add_action( 'wpmu_new_blog', array( $this, 'new_blog'), 10, 6);

		// Hooks
		add_action( 'admin_init', array( $this, 'check_version' ), 5 );
		add_filter( 'plugin_action_links_' . WC_CRM_PLUGIN_BASENAME, array( $this, 'action_links' ) );

    add_action( 'admin_menu', array($this, 'add_menu') );
    add_action( 'admin_print_footer_scripts', array($this, 'highlight_menu_item') );
	}

  /**
   * check_version function.
   *
   * @access public
   * @return void
   */
  public function check_version() {
    if ( ! defined( 'IFRAME_REQUEST' ) && ( get_option( 'wc_crm_version' ) != WC_CRM()->version || get_option( 'wc_crm_db_version' ) != WC_CRM()->db_version ) ) {
      $this->install();

      do_action( 'wc_crm_updated' );
    }
  }

   public function new_blog($blog_id, $user_id, $domain, $path, $site_id, $meta ) {
        global $wpdb;
        $crm_path = basename(dirname(__FILE__));
        if (is_plugin_active_for_network($crm_path.'/woocommerce-customer-relationship-manager.php')) {
            $old_blog = $wpdb->blogid;
            switch_to_blog($blog_id);
            $this->install();
            switch_to_blog($old_blog);
        }
    }

  public function activation($networkwide)
  {
    global $wpdb;
                 
    if (function_exists('is_multisite') && is_multisite()) {
        // check if it is a network activation - if so, run the activation function for each blog id
        if ($networkwide) {
            $old_blog = $wpdb->blogid;
            // Get all blog ids
            $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
            foreach ($blogids as $blog_id) {
                switch_to_blog($blog_id);
                $this->install();
            }
            switch_to_blog($old_blog);
            return;
        }   
    } 
    $this->install();
  }

	/**
	 * Install WC_Crm_Install
	 */
	public function install() {
    global $wpdb;
    $this->create_tables();
    $this->insert_customers();
    
    update_option( "wc_crm_db_version", WC_CRM()->db_version );    
    update_option( 'wc_crm_version', WC_CRM()->version );    
	}

  /**
   * Add the menu item
   */
  function add_menu() {
    $hook = add_menu_page(
      __( 'Customers', 'wc_customer_relationship_manager' ), // page title
      __( 'Customers', 'wc_customer_relationship_manager' ), // menu title
      'manage_woocommerce', // capability
      WC_CRM()->id, // unique menu slug
      'wc_customer_relationship_manager_render_list_page',
      null,
      '56.3'
    );

    $new_customer_hook = add_submenu_page( WC_CRM()->id, ( (isset($_GET['user_id']) && !empty($_GET['user_id'])) ? __( "Customer Profile", 'wc_customer_relationship_manager' ) : __( "Add New Customer", 'wc_customer_relationship_manager' ) ) , '<span id="wc_crm_add_new_customer">'.__( "Add New", 'wc_customer_relationship_manager').'</span>', 'manage_woocommerce', 'wc_new_customer', 'wc_customer_relationship_manager_render_new_customer_page' );


    $logs_hook = add_submenu_page(WC_CRM()->id, __( "Activity", 'wc_customer_relationship_manager' ), __( "Activity", 'wc_customer_relationship_manager'), 'manage_woocommerce', 'wc_crm_logs', 'wc_customer_relationship_manager_render_logs_page' );

    $groups_hook = add_submenu_page( WC_CRM()->id, __( "Groups", 'wc_customer_relationship_manager' ), __( "Groups", 'wc_customer_relationship_manager'), 'manage_woocommerce', 'wc_user_grps', 'wc_customer_relationship_manager_render_groups_list_page' );

    add_submenu_page( WC_CRM()->id, __( "Customer Status", 'wc_customer_relationship_manager' ), __( "Customer Status", 'wc_customer_relationship_manager'), 'manage_woocommerce', 'wc_crm_statuses', 'wc_customer_relationship_manager_render_statuses_page' );

    add_submenu_page( WC_CRM()->id, __( "Settings", 'wc_customer_relationship_manager' ), __( "Settings", 'wc_customer_relationship_manager'), 'manage_woocommerce', 'wc_crm_settings', 'wc_customer_relationship_manager_render_settings_page' );

    add_submenu_page( WC_CRM()->id, __( "Import", 'wc_customer_relationship_manager' ), __( "Import", 'wc_customer_relationship_manager'), 'manage_woocommerce', 'wc_crm_import', 'wc_customer_relationship_manager_render_import_page' );

    add_action( "load-$hook", 'wc_customer_relationship_manager_add_options' );
    add_action( "load-$logs_hook", 'wc_customer_relationship_manager_logs_add_options' );
    add_action( "load-$new_customer_hook", 'wc_customer_relationship_manager_new_customer_add_options' );
    add_action( "load-$groups_hook", 'wc_customer_relationship_manager_groups_add_options' );
  }

  function highlight_menu_item(){
     if( isset($_GET['page']) && $_GET['page'] == 'wc_new_customer' && ( isset($_GET['user_id']) || isset($_GET['order_id']) ) ){

      ?>
          <script type="text/javascript">
              jQuery(document).ready( function($) {
                  jQuery('#wc_crm_add_new_customer').parent().removeClass('current').parent().removeClass('current').prev().addClass('current').children().addClass('current');
              });
          </script>
      <?php
      }
  }

  /**
   * Add action links under WordPress > Plugins
   *
   * @param $links
   * @return array
   */
  public function action_links( $links ) {

    $plugin_links = array(
      '<a href="' . admin_url( 'admin.php?page=wc_crm_settings' ) . '">' . __( 'Settings', 'woocommerce' ) . '</a>',
    );

    return array_merge( $plugin_links, $links );
  }

  private function create_tables(){
    global $wpdb;

    $wpdb->hide_errors();

    $collate = '';
            if ($wpdb->has_cap('collation')) {
                if (!empty($wpdb->charset))
                    $collate .= "DEFAULT CHARACTER SET $wpdb->charset";
                if (!empty($wpdb->collate))
                    $collate .= " COLLATE $wpdb->collate";
            }

    // initial install
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    $table_name = $wpdb->prefix . "wc_crm_log";
    $sql = "CREATE TABLE $table_name (
            ID bigint(20) NOT NULL AUTO_INCREMENT,
            subject text NOT NULL,
            activity_type VARCHAR(50) DEFAULT '' NOT NULL,
            user_id bigint(20) NOT NULL,
            created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            created_gmt datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            message text NOT NULL,
            user_email text NOT NULL,
            phone text NOT NULL,
            call_type text NOT NULL,
            call_purpose text NOT NULL,
            related_to text NOT NULL,
            number_order_product text NOT NULL,
            call_duration text NOT NULL,
            log_status text NOT NULL,
            PRIMARY KEY  (ID)
    )" . $collate;
    dbDelta( $sql );

    $table_name = $wpdb->prefix . "wc_crm_groups";
    $sql = "CREATE TABLE $table_name (
            ID bigint(20) NOT NULL AUTO_INCREMENT,
            group_name VARCHAR(200) NOT NULL,
            group_slug TEXT,
            group_type VARCHAR(200) NOT NULL,
            group_total_spent_mark VARCHAR(200) NOT NULL,
            group_total_spent FLOAT(20) NOT NULL,
            group_user_role VARCHAR(200) NOT NULL,
            group_customer_status LONGTEXT NOT NULL,
            group_product_categories LONGTEXT NOT NULL,
            group_order_status LONGTEXT NOT NULL,
            group_last_order VARCHAR(200) NOT NULL,
            group_last_order_from DATETIME NOT NULL,
            group_last_order_to DATETIME NOT NULL,
            PRIMARY KEY  (ID)
    )" . $collate;
    dbDelta( $sql );

    $table_name = $wpdb->prefix . "wc_crm_groups_relationships";
    $sql = "CREATE TABLE $table_name (
            group_id bigint(20) NOT NULL,
            customer_email VARCHAR(200) NOT NULL,
            UNIQUE KEY relation (group_id,customer_email)
    )" . $collate;
    dbDelta( $sql );

    $table_name = $wpdb->prefix . "wc_crm_customers";
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
    
    $table_name = $wpdb->prefix . "wc_crm_customer_list";
    $sql = "CREATE TABLE $table_name (
            email VARCHAR(200) NOT NULL,
            user_id bigint(20),
            capabilities VARCHAR(200),
            status VARCHAR(200),
            city VARCHAR(200),
            state VARCHAR(200),
            country VARCHAR(200),
            order_id bigint(20),
            PRIMARY KEY  (email)
    )" . $collate;
    dbDelta( $sql );

    $table_name = $wpdb->prefix . "wc_crm_statuses";
    $sql = "CREATE TABLE $table_name (
            status_id bigint(20) NOT NULL AUTO_INCREMENT,
            status_name   VARCHAR(200),
            status_slug   VARCHAR(200),
            status_icon   VARCHAR(50),
            status_colour VARCHAR(7),
            PRIMARY KEY  (status_id)
    )" . $collate;
    dbDelta( $sql );

    $installed_ver = get_option( "wc_crm_db_version" );
    if ( version_compare( $installed_ver, '3.2', '<=' ) ) {
      $group_sql = "SELECT * FROM {$wpdb->prefix}wc_crm_groups";
      $groups = $wpdb->get_results($group_sql);
      if($groups && !empty($groups)){
        $new_data = array();
        foreach ($groups as $group) {
          if(!empty($group->group_customer_status)){
            $data = @unserialize($group->group_customer_status);
            if ($data === false) {
              $new_data['group_customer_status'] = serialize(array($group->group_customer_status));
              $wpdb->update( $wpdb->prefix."wc_crm_groups", $new_data, array( 'ID' => $group->ID ) );
            }
          }
        }
      }
    };

  }

  function insert_customers(){
    global $wpdb;
    $posts_query = "SELECT * FROM (
        SELECT 
          post_id,
          postmeta.meta_value as user_id,
          users.user_email as user_email,
          null as city,
          null as state,
          null as country
        FROM {$wpdb->postmeta} as postmeta
        LEFT JOIN {$wpdb->users} users ON ( users.ID = postmeta.meta_value )
        WHERE postmeta.meta_key = '_customer_user' AND postmeta.meta_value != '' AND postmeta.meta_value != 0 

        UNION 
        SELECT
          null as post_id,
          users.ID as user_id,
          users.user_email as user_email,
          null as city,
          null as state,
          null as country
        FROM {$wpdb->users} as users

        ORDER BY post_id DESC

        ) as pp GROUP BY user_id
        
        UNION

        SELECT * FROM (
        SELECT  postmeta.post_id as post_id,
                postmeta.meta_value as user_id,
                users.meta_value as user_email, 
                city.meta_value as city, 
                state.meta_value as state, 
                country.meta_value as country 
          FROM {$wpdb->postmeta} as postmeta
            LEFT JOIN {$wpdb->postmeta} users   ON ( users.post_id = postmeta.post_id AND users.meta_key = '_billing_email' )
            LEFT JOIN {$wpdb->postmeta} city    ON ( city.post_id = postmeta.post_id AND city.meta_key = '_billing_city' )
            LEFT JOIN {$wpdb->postmeta} state   ON ( state.post_id = postmeta.post_id AND state.meta_key = '_billing_state' )
            LEFT JOIN {$wpdb->postmeta} country ON ( country.post_id = postmeta.post_id AND country.meta_key = '_billing_country' )
        WHERE postmeta.meta_key = '_customer_user' AND (postmeta.meta_value = '' OR postmeta.meta_value = 0 ) AND users.meta_value != ''
        ORDER BY post_id DESC
        ) as pp GROUP BY user_email
      ";

    $sql = "SELECT 
                postmeta.user_email as email,
                postmeta.user_id as user_id,
                usermeta_role.meta_value as capabilities,
                IF(usermeta_status.meta_value IS NULL AND postmeta.user_id IS NOT NULL, 'Customer', usermeta_status.meta_value) as status,
                IF(usermeta_city.meta_value IS NULL AND postmeta.city  IS NOT NULL, postmeta.city, usermeta_city.meta_value) as city,
                IF(usermeta_state.meta_value IS NULL AND postmeta.state  IS NOT NULL, postmeta.state, usermeta_state.meta_value) as state,
                IF(usermeta_country.meta_value IS NULL AND postmeta.country  IS NOT NULL, postmeta.country, usermeta_country.meta_value) as country,
                postmeta.post_id as post_id
      FROM (
        {$posts_query}
        ) as postmeta
      LEFT JOIN {$wpdb->usermeta} usermeta_role    ON (usermeta_role.user_id = postmeta.user_id AND usermeta_role.meta_key = '{$wpdb->prefix}capabilities' )
      LEFT JOIN {$wpdb->usermeta} usermeta_status  ON (usermeta_status.user_id = postmeta.user_id AND usermeta_status.meta_key = 'customer_status' )
      LEFT JOIN {$wpdb->usermeta} usermeta_city    ON (usermeta_city.user_id = postmeta.user_id AND usermeta_city.meta_key = 'billing_city' )
      LEFT JOIN {$wpdb->usermeta} usermeta_state   ON (usermeta_state.user_id = postmeta.user_id AND usermeta_state.meta_key = 'billing_state' )
      LEFT JOIN {$wpdb->usermeta} usermeta_country ON (usermeta_country.user_id = postmeta.user_id AND usermeta_country.meta_key = 'billing_country' )
      WHERE postmeta.user_email IS NOT NULL
      AND postmeta.user_email != ''
     ";


    $inser_sql = "INSERT INTO {$wpdb->prefix}wc_crm_customer_list 
                  (email, user_id, capabilities, status, city, state, country, order_id) {$sql}
                  ON DUPLICATE KEY UPDATE 
                    email=postmeta.user_email,
                    user_id=postmeta.user_id, 
                    capabilities=usermeta_role.meta_value,
                    status=IF(usermeta_status.meta_value IS NULL AND postmeta.user_id IS NOT NULL, 'Customer', usermeta_status.meta_value), 
                    city=IF(usermeta_city.meta_value IS NULL AND postmeta.city  IS NOT NULL, postmeta.city, usermeta_city.meta_value), 
                    state=IF(usermeta_state.meta_value IS NULL AND postmeta.state  IS NOT NULL, postmeta.state, usermeta_state.meta_value), 
                    country=IF(usermeta_country.meta_value IS NULL AND postmeta.country  IS NOT NULL, postmeta.country, usermeta_country.meta_value), 
                    order_id=postmeta.post_id
                  ";

    //echo '<textarea style="width: 100%; height: 200px;" >'.$inser_sql.'</textarea>'; die;
    
    $wpdb->query('SET OPTION SQL_BIG_SELECTS = 1');
    $wpdb->query($inser_sql);
  }  


}
return new WC_Crm_Install;