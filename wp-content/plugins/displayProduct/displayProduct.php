<?php
/*
  Plugin Name: Display Product for WooCommerce
  Plugin URI: http://sureshopress.com
  Description: A simple user interface for Display Product shortcode
  Version: 1.9.7
  Author: Sureshopress
  Author URI: http://sureshopress.com
 */


if (!defined('ABSPATH'))
    die("Can't load this file directly");

define( 'DP_VER', '1.9.7' );
define( 'DP_PREFIX', 'displayproduct_' );
define( 'DP_TEXTDOMAN', 'displayproduct' ); 
define( 'DP_DIR', plugin_dir_url(__FILE__) ); 
define( 'DP_URL',plugins_url().'/displayProduct/');

require_once(plugin_dir_path(__FILE__) . '/include/wp-updates-plugin.php');
new WPUpdatesPluginUpdater_615( 'http://wp-updates.com/api/2/plugin', plugin_basename(__FILE__));

class displayProduct {

    function __construct() {
         
	$plugins = get_option('active_plugins');
	$required_woo_plugin = 'woocommerce/woocommerce.php';
        
	if (in_array( $required_woo_plugin , $plugins ) ) {
            load_plugin_textdomain(DP_TEXTDOMAN, false, '/displayProduct/languages');
            require_once( plugin_dir_path(__FILE__) . '/admin/shortcode_generator.php' );
            
            add_action('admin_init', array($this, 'action_admin_init'));
            register_activation_hook(__FILE__, array($this,'dpactivate') );
            require_once( plugin_dir_path(__FILE__) . 'displayProduct-shortcodes.php' );
            require_once( plugin_dir_path(__FILE__) . 'displayProduct-hooks.php' );
            require_once( plugin_dir_path(__FILE__) . '/include/displayProduct-init.php' );
            require_once( plugin_dir_path(__FILE__) . '/include/dp-quickview.php' );
            require_once( plugin_dir_path(__FILE__) . '/plugin/BFI_Thumb.php');
            add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'dp_action_links' ) );
            add_filter('widget_text', 'do_shortcode');
        }else{
            load_plugin_textdomain(DP_TEXTDOMAN, false, '/displayProduct/languages');
        }
    }

    function action_admin_init() {

        if (!current_user_can('edit_posts') && !current_user_can('edit_pages'))
            return;

        if (get_user_option('rich_editing') == 'true') {
            add_filter('mce_external_plugins', array($this, 'filter_mce_plugin'));
            add_filter('mce_buttons', array($this, 'filter_mce_button'));
            /* Style */
            wp_enqueue_style('my_custom_script', plugin_dir_url(__FILE__) . '/style.css');
            wp_enqueue_script('wp-color-picker');
            wp_enqueue_style( 'wp-color-picker' );
            //wp_enqueue_script('dp-backend-sortable-script', DP_DIR . 'assets/js/jquery-sortable.js');
            wp_enqueue_script('jquery-ui-sortable');
            add_action( 'wp_ajax_nopriv_dpshortcodegenerator','dp_shortcode_generator_template' );
            add_action( 'wp_ajax_dpshortcodegenerator', 'dp_shortcode_generator_template' );

            /* Pharse Variable to Javascript */
            $variable_to_js=array('plugin_folder' => plugin_dir_url(__FILE__),'ajax_url' => admin_url( 'admin-ajax.php' ));
            $variable_to_js_merge =array_merge($variable_to_js, displayproduct_textdomain());
            wp_localize_script('jquery', 'displayProduct', $variable_to_js_merge);
            
        }
    }

    function filter_mce_button($buttons) {
       // array_push($buttons, '|', 'displayProduct_button');
        $buttons[] = 'displayProduct_button';
        return $buttons;
    }

    function filter_mce_plugin($plugins) {
        $plugins['displayProduct'] = plugin_dir_url(__FILE__) . '/assets/js/displayProduct_plugin.js';
        return $plugins;
    }
    //Installation
    function dpactivate() {
        $dpCheckpage=get_option("dp_product_shop_page");
        $dp_needs_pages=get_option("dp_needs_pages");
        if ( empty( $dpCheckpage )&& $dp_needs_pages!=1 ){
            update_option( 'dp_needs_pages', 1 );
        }
    }
    public function dp_action_links( $links ) {

		$plugin_links = array(
			'<a target="_blank" href="' . admin_url( 'admin.php?page=display-product-page' ) . '">' . __( 'Settings', DP_TEXTDOMAN ) . '</a>',
			'<a target="_blank" href="http://sureshopress.com/display-product-for-woocommerce/document/">' . __( 'Docs', DP_TEXTDOMAN ) . '</a>',
		);

		return array_merge( $plugin_links, $links );
	}
}

$myproduct = new displayProduct();

$displayimage_width = get_option("display_product_thumbnail_image_size-width");
$displayimage_height = get_option("display_product_thumbnail_image_size-height");
$displayimage_crop = get_option("display_product_thumbnail_image_size-crop");
if(empty($displayimage_width)){
    $displayimage_width=250;
}
if(empty($displayimage_height)){
    $displayimage_height=250;
}
if(empty($displayimage_crop)){
    $displayimage_crop=true;
}
add_image_size( 'display_product_thumbnail', $displayimage_width, $displayimage_height, $displayimage_crop );
?>