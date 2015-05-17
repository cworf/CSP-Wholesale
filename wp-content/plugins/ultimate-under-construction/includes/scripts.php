<?php 

/***************************
* script control
***************************/
global $wp_version;

//All Actions to be added.
add_action('init', 'uuc_load_scripts');

if ( $wp_version >= 3.5 ){
add_action('init', 'uuc_admin_enqueue_scripts_cp');
} else {
add_action('init', 'uuc_admin_enqueue_scripts_farb');
}

//All functions mentioned above to be added below here only!

function uuc_load_scripts() {
	wp_enqueue_style('uuc-styles', plugin_dir_url(__FILE__) . 'css/plugin_styles.css');
}

function uuc_admin_enqueue_scripts_cp() {
    wp_enqueue_script( 'wp-color-picker' );
    // load the minified version of custom script
    wp_enqueue_script( 'uuc-custom', plugin_dir_url(__FILE__) . 'js/uuc-script.js', array( 'jquery', 'wp-color-picker' ), '1.1', true );
    wp_enqueue_style( 'wp-color-picker' );
}

function uuc_admin_enqueue_scripts_farb() {
    wp_enqueue_script( 'farbtastic' );
    wp_enqueue_script( 'uuc-custom-farb', plugin_dir_url(__FILE__) . '/js/uuc-script-farb.js', array( 'farbtastic', 'jquery' ) );
    wp_enqueue_style( 'farbtastic' );
}