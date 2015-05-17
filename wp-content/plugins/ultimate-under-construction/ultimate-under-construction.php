<?php
/*
Plugin Name: Ultimate Under Construction page
Plugin URI: http://www.morrowmedia.co.uk/plugins.html
Description: Once Active this will replace your Wordpress site with a customizable Under Construction holding page. Admins will still be able to log in and see the original site.
Author: Morrowmedia
Author URI: http://www.morrowmedia.co.uk/
Version: 1.8
*/

/*
 This file is part of ultimateUnderConstruction.
 ultimateUnderConstruction is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.
 ultimateUnderConstruction is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.
 You should have received a copy of the GNU General Public License
 along with ultimateUnderConstruction.  If not, see <http://www.gnu.org/licenses/>.
 */


/***************************
* global variables
***************************/

$my_prefix = 'uuc_';
$my_plugin_name = 'Ultimate Under Construction page';

//Retrieve settings from Admin Options table
$uuc_options = get_option('uuc_settings');

/***************************
* includes
***************************/

include('includes/scripts.php'); //includes all JS and CSS
include('includes/display-functions.php'); //display content functions
include('includes/uucadmin.php'); //plugin admin options