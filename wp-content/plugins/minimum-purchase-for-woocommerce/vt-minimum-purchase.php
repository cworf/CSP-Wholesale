<?php
/*
Plugin Name: VarkTech Minimum Purchase for WooCommerce
Plugin URI: http://varktech.com
Description: An e-commerce add-on for WooCommerce, supplying minimum purchase functionality.
Version: 1.09.6
Author: Vark
Author URI: http://varktech.com
*/


/*
** define Globals 
*/
   $vtmin_info;  //initialized in VTMIN_Parent_Definitions
   $vtmin_rules_set;
   $vtmin_rule;
   $vtmin_cart;
   $vtmin_cart_item;
   $vtmin_setup_options;
//   $vtmin_error_msg;
   
   //initial setup only, overriden later in function vtprd_debug_options

error_reporting(E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR); //v1.09.2
         
class VTMIN_Controller{
	
	public function __construct(){    
   
		define('VTMIN_VERSION',                               '1.09.6');
    define('VTMIN_MINIMUM_PRO_VERSION',                   '1.08.2'); 
    define('VTMIN_LAST_UPDATE_DATE',                      '2015-05-16');
    define('VTMIN_DIRNAME',                               ( dirname( __FILE__ ) ));
    define('VTMIN_URL',                                   plugins_url( '', __FILE__ ) );
    define('VTMIN_EARLIEST_ALLOWED_WP_VERSION',           '3.3');   //To pick up wp_get_object_terms fix, which is required for vtmin-parent-functions.php
    define('VTMIN_EARLIEST_ALLOWED_PHP_VERSION',          '5');
    define('VTMIN_PLUGIN_SLUG',                           plugin_basename(__FILE__));
    define('VTMIN_PRO_PLUGIN_NAME',                      'VarkTech Minimum Purchase Pro for WooCommerce');  //V1.09.2 
    
    require ( VTMIN_DIRNAME . '/woo-integration/vtmin-parent-definitions.php');
   
    /*  =============+++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
    //  these control the rules ui, add/save/trash/modify/delete
    /*  =============+++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
    add_action('init',          array( &$this, 'vtmin_controller_init' )); 
    add_action('admin_init',    array( &$this, 'vtmin_admin_init' ));
    
    //v1.08 begin
    add_action( 'draft_to_publish',       array( &$this, 'vtmin_admin_update_rule' )); 
    add_action( 'auto-draft_to_publish',  array( &$this, 'vtmin_admin_update_rule' ));
    add_action( 'new_to_publish',         array( &$this, 'vtmin_admin_update_rule' )); 			
    add_action( 'pending_to_publish',     array( &$this, 'vtmin_admin_update_rule' ));    
    //v1.08 end
    
    //standard mod/del/trash/untrash    
    add_action('save_post',     array( &$this, 'vtmin_admin_update_rule' ));
    add_action('delete_post',   array( &$this, 'vtmin_admin_delete_rule' ));    
    add_action('trash_post',    array( &$this, 'vtmin_admin_trash_rule' ));
    add_action('untrash_post',  array( &$this, 'vtmin_admin_untrash_rule' ));

    
    /*  =============+++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
    
    //get rid of bulk actions on the edit list screen, which aren't compatible with this plugin's actions...
    add_action('bulk_actions-edit-vtmin-rule', array($this, 'vtmin_custom_bulk_actions') ); 

	}   //end constructor

  	                                                             
 /* ************************************************
 **   Overhead and Init
 *************************************************** */
	public function vtmin_controller_init(){
    global $vtmin_setup_options;

  
    load_plugin_textdomain( 'vtmin', null, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 

    require ( VTMIN_DIRNAME . '/core/vtmin-backbone.php' );    
    require ( VTMIN_DIRNAME . '/core/vtmin-rules-classes.php');
    require ( VTMIN_DIRNAME . '/woo-integration/vtmin-parent-functions.php');
    require ( VTMIN_DIRNAME . '/woo-integration/vtmin-parent-cart-validation.php');

    //moved here v1.07
    if (get_option( 'vtmin_setup_options' ) ) {
      $vtmin_setup_options = get_option( 'vtmin_setup_options' );  //put the setup_options into the global namespace
    }        
    vtmin_debug_options();  //v1.07
        
    if (is_admin()){
        //fix 02-132013 - register_activation_hook now at bottom of file, after class instantiates
        require ( VTMIN_DIRNAME . '/admin/vtmin-setup-options.php');
        
        if(defined('VTMIN_PRO_DIRNAME')) {
          require ( VTMIN_PRO_DIRNAME . '/admin/vtmin-rules-ui.php' );
          require ( VTMIN_PRO_DIRNAME . '/admin/vtmin-rules-update.php');
        } else {
          require ( VTMIN_DIRNAME .     '/admin/vtmin-rules-ui.php' );
          require ( VTMIN_DIRNAME .     '/admin/vtmin-rules-update.php');
        }
        
        require ( VTMIN_DIRNAME . '/admin/vtmin-checkbox-classes.php');
        require ( VTMIN_DIRNAME . '/admin/vtmin-rules-delete.php');
        
        //v1.09.2 begin
        if ( (defined('VTMIN_PRO_DIRNAME')) &&
             (version_compare(VTMIN_PRO_VERSION, VTMIN_MINIMUM_PRO_VERSION) < 0) ) {    //'<0' = 1st value is lower  
          add_action( 'admin_notices',array(&$this, 'vtmin_admin_notice_version_mismatch') );            
        }
        //v1.09.2 end 
                
    }
    
    //unconditional branch for these resources needed for WOOCommerce, at "place order" button time
    require ( VTMIN_DIRNAME . '/core/vtmin-cart-classes.php');
    
    if(defined('VTMIN_PRO_DIRNAME')) {
      require ( VTMIN_PRO_DIRNAME . '/core/vtmin-apply-rules.php' );
    } else {
      require ( VTMIN_DIRNAME .     '/core/vtmin-apply-rules.php' );
    }
    
    wp_enqueue_script('jquery'); 


  }
  
         
  /* ************************************************
  **   Admin - Remove bulk actions on edit list screen, actions don't work the same way as onesies...
  ***************************************************/ 
  function vtmin_custom_bulk_actions($actions){
    
    ?> 
    <style type="text/css"> #delete_all {display:none;} /*kill the 'empty trash' buttons, for the same reason*/ </style>
    <?php
    
    unset( $actions['edit'] );
    unset( $actions['trash'] );
    unset( $actions['untrash'] );
    unset( $actions['delete'] );
    return $actions;
  }
    
  /* ************************************************
  **   Admin - Show Rule UI Screen
  *************************************************** 
  *  This function is executed whenever the add/modify screen is presented
  *  WP also executes it ++right after the update function, prior to the screen being sent back to the user.   
  */  
	public function vtmin_admin_init(){
     if ( !current_user_can( 'edit_posts', 'vtmin-rule' ) )
          return;

     $vtmin_rules_ui = new VTMIN_Rules_UI; 
  }
  
 
  /* ************************************************
  **   Admin - Update Rule 
  *************************************************** */
	public function vtmin_admin_update_rule(){
    /* *****************************************************************
         The delete/trash/untrash actions *will sometimes fire save_post*
         and there is a case structure in the save_post function to handle this.
    
          the delete/trash actions are sometimes fired twice, 
               so this can be handled by checking 'did_action'
     ***************************************************************** */
      
      global $post, $vtmin_rules_set;
	    if( !isset( $post ) ) {  //v1.09.3  
        return;
      } 
      if ( !( 'vtmin-rule' == $post->post_type )) {
        return;
      }  
      if (( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
            return; 
      }
     if (isset($_REQUEST['vtmin_nonce']) ) {     //nonce created in vtmin-rules-ui.php  
          $nonce = $_REQUEST['vtmin_nonce'];
          if(!wp_verify_nonce($nonce, 'vtmin-rule-nonce')) { 
            return;
          }
      } 
      if ( !current_user_can( 'edit_posts', 'vtmin-rule' ) ) {
          return;
      }

      
      /* ******************************************
       The 'SAVE_POST' action is fired at odd times during updating.
       When it's fired early, there's no post data available.
       So checking for a blank post id is an effective solution.
      *************************************************** */      
      if ( !( $post->ID > ' ' ) ) { //a blank post id means no data to proces....
        return;
      } 
      //AND if we're here via an action other than a true save, do the action and exit stage left
      $action_type = $_REQUEST['action'];
      if ( in_array($action_type, array('trash', 'untrash', 'delete') ) ) {
        switch( $action_type ) {
            case 'trash':
                $this->vtmin_admin_trash_rule();  
              break;
            case 'untrash':
                $this->vtmin_admin_untrash_rule();
              break;
            case 'delete':
                $this->vtmin_admin_delete_rule();  
              break;
        }
        return;
      }
                 
      $vtmin_rule_update = new VTMIN_Rule_update;
  }
   
  
 /* ************************************************
 **   Admin - Delete Rule
 *************************************************** */
	public function vtmin_admin_delete_rule(){
     global $post, $vtmin_rules_set; 
	    if( !isset( $post ) ) {  //v1.09.3  
        return;
      } 
      if ( !( 'vtmin-rule' == $post->post_type )) {
        return;
      }         

     if ( !current_user_can( 'delete_posts', 'vtmin-rule' ) )  {
          return;
     }
    
    $vtmin_rule_delete = new VTMIN_Rule_delete;            
    $vtmin_rule_delete->vtmin_delete_rule();
  }
  
  
  /* ************************************************
  **   Admin - Trash Rule
  *************************************************** */   
	public function vtmin_admin_trash_rule(){
     global $post, $vtmin_rules_set; 
	    if( !isset( $post ) ) {  //v1.09.3  
        return;
      } 
      if ( !( 'vtmin-rule' == $post->post_type )) {
        return;
      }          
  
     if ( !current_user_can( 'delete_posts', 'vtmin-rule' ) )  {
          return;
     }  
     
     if(did_action('trash_post')) {    
         return;
    }
    
    $vtmin_rule_delete = new VTMIN_Rule_delete;            
    $vtmin_rule_delete->vtmin_trash_rule();

  }
  
  
 /* ************************************************
 **   Admin - Untrash Rule
 *************************************************** */   
	public function vtmin_admin_untrash_rule(){
     global $post, $vtmin_rules_set; 
	    if( !isset( $post ) ) {  //v1.09.3  
        return;
      } 
      if ( !( 'vtmin-rule' == $post->post_type )) {
        return;
      }          

     if ( !current_user_can( 'delete_posts', 'vtmin-rule' ) )  {
          return;
     }       
    $vtmin_rule_delete = new VTMIN_Rule_delete;            
    $vtmin_rule_delete->vtmin_untrash_rule();
  }


  /* ************************************************
  **   Admin - Activation Hook
  *************************************************** */  
  function vtmin_activation_hook() {
     //the options are added at admin_init time by the setup_options.php as soon as plugin is activated!!!
    
    //verify the requirements for Vtmin.
    global $wp_version;
		if((float)$wp_version < 3.3){
			// delete_option('vtmin_setup_options');
			 wp_die( __('<strong>Looks like you\'re running an older version of WordPress, you need to be running at least WordPress 3.3 to use the Varktech Minimum Purchase plugin.</strong>', 'vtmin'), __('VT Minimum Purchase not compatible - WP', 'vtmin'), array('back_link' => true));
			return;
		}
    
    //fix 2-13-2013 - changed php version_compare, altered error msg   
   if (version_compare(PHP_VERSION, VTMIN_EARLIEST_ALLOWED_PHP_VERSION) < 0) {    //'<0' = 1st value is lower 
			wp_die( __('<strong><em>PLUGIN CANNOT ACTIVATE &nbsp;&nbsp;-&nbsp;&nbsp;     Varktech Minimum Purchase </em>
      <br><br>&nbsp;&nbsp;&nbsp;&nbsp;   Your installation is running on an older version of PHP 
      <br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   - your PHP version = ', 'vtmin') .PHP_VERSION. __(' . 
      <br><br>&nbsp;&nbsp;&nbsp;&nbsp;   You need to be running **at least PHP version 5** to use this plugin.  
      <br><br>&nbsp;&nbsp;&nbsp;&nbsp;   Please contact your host and request an upgrade to PHP 5+ . 
      <br><br>&nbsp;&nbsp;&nbsp;&nbsp;   Then activate this plugin following the upgrade.</strong>', 'vtmin'), __('VT Min and Max Purchase not compatible - PHP', 'vtmin'), array('back_link' => true));
			return; 
		}
    
    if(defined('WPSC_VERSION') && (VTMIN_PARENT_PLUGIN_NAME == 'WP E-Commerce') ) { 
      $new_version =      VTMIN_EARLIEST_ALLOWED_PARENT_VERSION;
      $current_version =  WPSC_VERSION;
      if( (version_compare(strval($new_version), strval($current_version), '>') == 1) ) {   //'==1' = 2nd value is lower 
  			// delete_option('vtmin_setup_options');
  			 wp_die( __('<strong>Looks like you\'re running an older version of WP E-Commerce. <br>You need to be running at least ** WP E-Commerce 3.8 **, to use the Varktech Minimum Purchase plugin.</strong>', 'vtmin'), __('VT Minimum Purchase not compatible - WPEC', 'vtmin'), array('back_link' => true));
  			return;
  		}
    }  else 
    if (VTMIN_PARENT_PLUGIN_NAME == 'WP E-Commerce') {
        wp_die( __('<strong>Varktech Minimum Purchase for WP E-Commerce requires that WP E-Commerce be installed and activated.</strong>', 'vtmin'), __('WP E-Commerce not installed or activated', 'vtmin'), array('back_link' => true));
  			return;
    }

    if(defined('WOOCOMMERCE_VERSION') && (VTMIN_PARENT_PLUGIN_NAME == 'WooCommerce')) { 
      $new_version =      VTMIN_EARLIEST_ALLOWED_PARENT_VERSION;
      $current_version =  WOOCOMMERCE_VERSION;
      if( (version_compare(strval($new_version), strval($current_version), '>') == 1) ) {   //'==1' = 2nd value is lower 
  			// delete_option('vtmin_setup_options');
  			 wp_die( __('<strong>Looks like you\'re running an older version of WooCommerce. <br>You need to be running at least ** WooCommerce 1.0 **, to use the Varktech Minimum Purchase plugin.</strong>', 'vtmin'), __('VT Minimum Purchase not compatible - WooCommerce', 'vtmin'), array('back_link' => true));
  			return;
  		}
    }   else 
    if (VTMIN_PARENT_PLUGIN_NAME == 'WooCommerce') {
        wp_die( __('<strong>Varktech Minimum Purchase for WooCommerce requires that WooCommerce be installed and activated.</strong>', 'vtmin'), __('WooCommerce not installed or activated', 'vtmin'), array('back_link' => true));
  			return;
    }
    

    if(defined('JIGOSHOP_VERSION') && (VTMIN_PARENT_PLUGIN_NAME == 'JigoShop')) { 
      $new_version =      VTMIN_EARLIEST_ALLOWED_PARENT_VERSION;
      $current_version =  JIGOSHOP_VERSION;
      if( (version_compare(strval($new_version), strval($current_version), '>') == 1) ) {   //'==1' = 2nd value is lower
  			// delete_option('vtmin_setup_options');
  			 wp_die( __('<strong>Looks like you\'re running an older version of JigoShop. <br>You need to be running at least ** JigoShop 3.8 **, to use the Varktech Minimum Purchase plugin.</strong>', 'vtmin'), __('VT Minimum Purchase not compatible - JigoShop', 'vtmin'), array('back_link' => true));
  			return;
  		}
    }  else 
    if (VTMIN_PARENT_PLUGIN_NAME == 'JigoShop') {
        wp_die( __('<strong>Varktech Minimum Purchase for JigoShop requires that JigoShop be installed and activated.</strong>', 'vtmin'), __('JigoShop not installed or activated', 'vtmin'), array('back_link' => true));
  			return;
    }
     
  }

   //v1.09.2 begin                          
   public function vtmin_admin_notice_version_mismatch() {
      $message  =  '<strong>' . __('Please also update plugin: ' , 'vtmin') . ' &nbsp;&nbsp;'  .VTMIN_PRO_PLUGIN_NAME . '</strong>' ;
      $message .=  '<br>&nbsp;&nbsp;&bull;&nbsp;&nbsp;' . __('Your Pro Version = ' , 'vtmin') .VTMIN_PRO_VERSION. ' &nbsp;&nbsp;' . __(' The Minimum Required Pro Version = ' , 'vtmin') .VTMIN_MINIMUM_PRO_VERSION ;      
      $message .=  '<br>&nbsp;&nbsp;&bull;&nbsp;&nbsp;' . __('Please delete the old Pro plugin from your installation via ftp.'  , 'vtmin');
      $message .=  '<br>&nbsp;&nbsp;&bull;&nbsp;&nbsp;' . __('Go to ', 'vtmin');
      $message .=  '<a target="_blank" href="http://www.varktech.com/download-pro-plugins/">Varktech Downloads</a>';
      $message .=   __(', download and install the newest <strong>'  , 'vtmin') .VTMIN_PRO_PLUGIN_NAME. '</strong>' ;
      
      $admin_notices = '<div id="message" class="error fade" style="background-color: #FFEBE8 !important;"><p>' . $message . ' </p></div>';
      echo $admin_notices;
      
      //v1.09.2 added
      $plugin = VTMIN_PRO_PLUGIN_SLUG;
			if( is_plugin_active($plugin) ) {
			   deactivate_plugins( $plugin );
      }      
      
      return;    
  }   
   //v1.09.2 end    
  
  /* ************************************************
  **   Admin - **Uninstall** Hook and cleanup
  *************************************************** */ 
  function vtmin_uninstall_hook() {
      
      if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
      	return;
        //exit ();
      }
  
      delete_option('vtmin_setup_options');
      $vtmin_nuke = new VTMIN_Rule_delete;            
      $vtmin_nuke->vtmin_nuke_all_rules();
      $vtmin_nuke->vtmin_nuke_all_rule_cats();
      
  }
  
} //end class
$vtmin_controller = new VTMIN_Controller;

  //***************************************************************************************
  //fix 2-13-2013  -  problems with activation hook and class, solved herewith...
  //   FROM http://website-in-a-weekend.net/tag/register_activation_hook/
  //***************************************************************************************
  if (is_admin()){ 
        register_activation_hook(__FILE__, array($vtmin_controller, 'vtmin_activation_hook'));
        register_activation_hook(__FILE__, array($vtmin_controller, 'vtmin_uninstall_hook'));                                   
  }
