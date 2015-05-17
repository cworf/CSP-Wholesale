<?php
/*
Plugin Name: IgniteWoo Updater
Plugin URI: http://ignitewoo.com/
Description: Manage updates for your purchased IgniteWoo products.
Version: 1.1
Author: IgniteWoo
Author URI: http://ignitewoo.com/
*/
/*
	Copyright (c) 2012 - IgniteWoo.com
	Copyright (c) 2012 - WooThemes

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
	
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    if ( is_admin() ) {
    
		require_once( 'classes/class-ignitewoo-updater.php' );

		global $ignitewoo_updater;
		$ignitewoo_updater = new IgniteWoo_Updater( __FILE__ );
		$ignitewoo_updater->version = '1.0.3';
		$ignitewoo_updater->updater->product_id = 'Updater';
		$ignitewoo_updater->updater->licence_hash = '6471fb9bec3ef8e9dcafe3ba5bd994c8';
    }
    
?>