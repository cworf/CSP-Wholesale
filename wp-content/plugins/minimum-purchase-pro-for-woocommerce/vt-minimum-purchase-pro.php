<?php
/*
Plugin Name: VarkTech Minimum Purchase Pro for WooCommerce
Plugin URI: http://varktech.com
Description: Pro e-commerce add-on for WooCommerce, supplying minimum purchase functionality.
Version: 1.08
Author: VarkTech
Author URI: http://varktech.com
Network: true
*
Copyright 2013 AardvarkPC Services NZ, all rights reserved.  See license.txt for more details.
*/

class VTMIN_Pro_Controller{
	
	public function __construct(){    
    
    
    add_action('init', array( &$this, 'vtmin_pro_controller_init' ));
        
		define('VTMIN_PRO_PLUGIN_NAME',                       'Minimum Purchase Pro for WooCommerce');
    define('VTMIN_PRO_FREE_PLUGIN_NAME',                  'Minimum Purchase for WooCommerce');
    define('VTMIN_PRO_VERSION',                           '1.08');
    define('VTMIN_PRO_MINIMUM_REQUIRED_FREE_VERSION',     '1.09.1');  //required version of vt-minimum-purchase...
    define('VTMIN_PRO_LAST_UPDATE_DATE',                  '2014-05-11');    
    define('VTMIN_PRO_DIRNAME',                           ( dirname( __FILE__ ) ));
    define('VTMIN_PRO_BASE_NAME',                          basename(VTMIN_PRO_DIRNAME));
    define('VTMIN_PRO_REMOTE_VERSION_FILE',               'http://www.varktech.com/pro/vtmin-pro-for-woocommerce-version.txt');
    define('VTMIN_PRO_DOWNLOAD_FREE_VERSION_BY_PARENT',   'http://wordpress.org/extend/plugins/minimum-purchase-for-woocommerce/');
    define('VTMIN_PRO_PLUGIN_SLUG',                        plugin_basename(__FILE__)); 
	}   //end constructor

	                                                             
 /* ************************************************
 **   Overhead and Init
 *************************************************** */
	public function vtmin_pro_controller_init(){
     if (is_admin()){
        register_activation_hook(__FILE__, array( $this, 'vtmin_pro_check_for_free_version'));
        add_action('after_plugin_row', array( &$this, 'vtmin_pro_check_plugin_version' ));
        add_action('admin_init', array( &$this, 'vtmin_pro_check_for_free_version' ));
     }
  }
    

  function vtmin_pro_check_for_free_version() {
  
    global $wp_version;
    $plugin = VTMIN_PRO_PLUGIN_SLUG;
    $free_plugin_download = '<a  href="' . VTMIN_PRO_DOWNLOAD_FREE_VERSION_BY_PARENT . '"  title="Download from wordpress.org"> WordPress.org </a>';
    $plugin_name = VTMIN_PRO_PLUGIN_NAME;
    $free_plugin_name = VTMIN_PRO_FREE_PLUGIN_NAME;
    
    if(!defined('VTMIN_VERSION')) { 
  			if( is_plugin_active($plugin) ) {
  			   deactivate_plugins( $plugin );
        }
        //add_action('admin_head', array( &$this, 'vtmin_pro_addAlert_message1' ));
        $message =  __('<strong>Please Download and/or Activate ' .$free_plugin_name.' (the Free version). </strong><br>It must be installed and active, before the Pro version can be activated.  The Free version can be downloaded from '  . $free_plugin_download , 'vtminpro');
        $admin_notices = '<div id="message" class="error fade" style="background-color: #FFEBE8 !important;"><p>' . $message . ' </p></div>';
        add_action( 'admin_notices', create_function( '', "echo '$admin_notices';" ) );

        return;
    }
  
    $new_version =      VTMIN_PRO_MINIMUM_REQUIRED_FREE_VERSION;
    $current_version =  VTMIN_VERSION;
    if( (version_compare(strval($new_version), strval($current_version), '>') == 1) ) {   //'==1' = 2nd value is lower  
        if( is_plugin_active($plugin) ) {
  			   deactivate_plugins( $plugin );
        }
        $message =  __('<strong>Please Update ' .$free_plugin_name.' (the Free version). </strong><br>It must be current, before the Pro version can be activated.  The Free version can be downloaded from '  . $free_plugin_download , 'vtminpro');
        $admin_notices = '<div id="message" class="error fade" style="background-color: #FFEBE8 !important;"><p>' . $message . ' </p></div>';
        add_action( 'admin_notices', create_function( '', "echo '$admin_notices';" ) );
        return;
    }    
  }
 
  /* ************************************************
  **   Admin - Uninstall Hook and cleanup
  *************************************************** */ 
  function vtmin_pro_uninstall_hook() {
      if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
      	exit ();
      }

  }


function vtmin_pro_check_plugin_version( $plugin ) {
  /*
    $plugin is system supplied if 
    add_action('after_plugin_row', array( &$this, 'vtmin_pro_check_plugin_version' ));
    is used.  
    
    $plugin = 'vt-minimum-purchase-pro/vt-minimum-purchase-pro.php';
  */  
 // echo '<br>plugin name= '  .$plugin;  //mwn
  
  if( strpos( VTMIN_PRO_BASE_NAME.'/'.__FILE__,$plugin ) !== false ) {  
  
    
    $new_version = wp_remote_fopen(VTMIN_PRO_REMOTE_VERSION_FILE, 'r');
    
 //   echo '<br>External file version= ' .$new_version;  //mwn
 
    if( $new_version ) {      
      $current_version = VTMIN_PRO_VERSION;
      $installation_location = VTMIN_PRO_INSTALLATION_INSTRUCTIONS_BY_PARENT;
      if( (version_compare(strval($new_version), strval($current_version), '>') == 1) ) {   //'==1' = 2nd value is lower 
        
 //    echo '<br>new version found, current version= ' .$current_version; //mwn
    
        $update_msg = __('There is a new version of ', 'vtmin') . VTMIN_PRO_PLUGIN_NAME . __(' available.', 'vtmin') ;
        echo ' <td colspan="5" class="plugin-update" style="line-height:1.2em; font-size:11px; padding:1px;">
                <div style="color:#000; font-weight:bold; margin:4px; padding:6px 5px; background-color:#fffbe4; border-color:#dfdfdf; border-width:1px; border-style:solid; -moz-border-radius:5px; -khtml-border-radius:5px; -webkit-border-radius:5px; border-radius:5px;">'.  strip_tags( $update_msg ) .' <a href="'.$installation_location.'" target="_blank">View version ' . $new_version . ' for details</a>.</div	>
              </td>';
      } else {
        return;
      }
    }
  }
}

  
} //end class



//****************************************
//V1.08 BEGIN
//FOR SOME HOSTS, WARNINGS ARE GENERATED **BEFORE** ACTIVATION...   
/*
** define Globals 
*/
 $vtmin_setup_options;  //from FREE version
vtmin_pro_debug_options();

  function vtmin_pro_debug_options(){   
    global $vtmin_setup_options;
    if ( ( isset( $vtmin_setup_options['debugging_mode_on'] )) &&
         ( $vtmin_setup_options['debugging_mode_on'] == 'yes' ) ) {  
      error_reporting(E_ALL);  
    }  else {
      error_reporting(E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR);   //only allow FATAL error types
    }
  }
//V1.08 END  
//****************************************     


$vtmin_pro_controller = new VTMIN_Pro_Controller;

 