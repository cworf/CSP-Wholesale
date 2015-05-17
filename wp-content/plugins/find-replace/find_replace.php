<?php
/*
Plugin Name: Find and replace
Plugin URI: http://www.websitefreelancers.nl
Description: Lets you find and replace pages and posts with a GUI.
Version: 1.8
Author: Ramon Fincken, Bas Bosman
Author URI: http://www.websitefreelancers.nl
*/
if (!defined('ABSPATH')) die("Aren't you supposed to come here via WP-Admin?");

if(!class_exists('mijnpress_plugin_framework'))
{
	include('mijnpress_plugin_framework.php');
}

class plugin_findreplace extends mijnpress_plugin_framework
{
	function __construct()
	{
		$this->showcredits = true;
		$this->showcredits_fordevelopers = true;
		$this->plugin_title = 'Find and replace';
		$this->plugin_class = 'plugin_findreplace';
		$this->plugin_filename = 'find-replace/find_replace.php';
		$this->plugin_config_url = 'plugins.php?page='.$this->plugin_filename;
	}

	function plugin_findreplace()
	{
		$args= func_get_args();
		call_user_func_array
		(
		    array(&$this, '__construct'),
		    $args
		);
	}

	function addPluginSubMenu()
	{
		$plugin = new plugin_findreplace();
		parent::addPluginSubMenu($plugin->plugin_title,array($plugin->plugin_class, 'admin_menu'),__FILE__);
	}

	/**
	 * Additional links on the plugin page
	 */
	function addPluginContent($links, $file) {
		$plugin = new plugin_findreplace();		
		$links = parent::addPluginContent($plugin->plugin_filename,$links,$file,$plugin->plugin_config_url);
		return $links;
	}

	public function admin_menu()
	{
		$plugin = new plugin_findreplace();		
		$plugin->content_start();		
		include('form.php');
		$plugin->content_end();
	}
}

if(mijnpress_plugin_framework::is_admin())
{
	add_action('admin_menu',  array('plugin_findreplace', 'addPluginSubMenu'));
	add_filter('plugin_row_meta',array('plugin_findreplace', 'addPluginContent'), 10, 2);
}
?>
