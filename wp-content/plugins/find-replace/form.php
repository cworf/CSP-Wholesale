<?php
/**
 *
 * @category      WordPress Plugins
 * @package    	  Plugins
 * @author        Bas Bosman, MijnPress DE, Ramon Fincken
 * @copyright     Yes, Open source, MijnPress.nl
 */
if (!defined('ABSPATH')) die("Aren't you supposed to come here via WP-Admin?");
global $wpdb;

$message = null;
$showMsg = 'none';

/**
 * If submiting the form
 */
if (isset($_POST['submitbutton']) && isset($_POST['post_type'])){
	if (!isset($_POST['search']) || !$_POST['search']) {
		echo '<div id="message" class="error">No search string</div>';
	}
	else if (!isset($_POST['replace']) || !$_POST['replace']){
		echo '<div id="message" class="error">No replace string</div>';
	}
	elseif (!isset($_POST['post_type']) || !count($_POST['post_type'])){
		echo '<div id="message" class="error">No post type selected</div>';
	} else {
		//Is magic quotes on?
		// http://codex.wordpress.org/Function_Reference/stripslashes_deep
		if ( get_magic_quotes_gpc() ) {
			$_POST = array_map( 'stripslashes_deep', $_POST );
		}

		//logic
		$query	= "";
		$subquery = "";

		// at least 1 post_type is there, so the opening ( will match the ) below this foreach
		foreach ($_POST['post_type'] as $type) {
			$subquery         = $subquery == '' ? 'WHERE p.post_type IN(' : $subquery . ', ';
			$subquery         .= "'" . $type . "'";
		}
		$query         = $subquery.")";

		$field          = 'post_content';
		$search         = $_POST['search'];
		$replace        = $_POST['replace'];
		$prio           = ($_POST['low_priority'] == 'yes') ? ' LOW_PRIORITY ' : '';

		$updatequery = $wpdb->prepare( "UPDATE ".$prio." $wpdb->posts AS p SET p.".$field." = REPLACE(p.".$field.", '%s', '%s') $query", $search, $replace );

		$wpdb->query($updatequery);

		if(isset($_POST['postmeta']) && $_POST['postmeta'] == 'yes')
		{
			$field = 'meta_value';

			$updatequery = $wpdb->prepare( "UPDATE ".$prio." $wpdb->postmeta AS pm, $wpdb->posts AS p SET pm.".$field." = REPLACE(pm.".$field.", '%s', '%s') $query AND pm.post_id = p.ID", $search, $replace );
			$wpdb->query($updatequery);
		}

		if(empty($prio))
		{
			echo '<div id="message" class="updated fade">All instances of \'' . $search . '\' are replaced with \''. $replace .'\'.</div>';
		}
		else
		{
			echo '<div id="message" class="updated fade">All instances of \'' . $search . '\' will be replaced with \''. $replace .'\' when server resources are available.</div>';
		}
	}
}
?>
<h1>Find &amp; Replace plugin</h1>
<p>A simple tool.</p>

<form id="form1" name="form1" method="post" action=""
	onsubmit="return confirm('Are you sure? There is NO undo.')">
<table>
	<tr>
		<td>Include postmeta values:</td>
		<td><input type="radio" name="postmeta" value="yes" checked="checked" />
		Yes (Recommended)<br />
		<input type="radio" name="postmeta" value="no" /> No</td>
	</tr>
	<tr>
		<td>Use <a href="http://dev.mysql.com/doc/refman/5.0/en/update.html"
			target="_blank">LOW_PRIORITY</a> to do the update:</td>
		<td><input type="radio" name="low_priority" value="no"
			checked="checked" /> No (Instant updates to your database)<br />
		<input type="radio" name="low_priority" value="yes" /> Yes (Delayed
		updates, when the server has resources. Could take a long time or even forever!)</td>
	</tr>
	<tr>
		<td>Search string:</td>
		<td><input type="text" name="search" size="60" /></td>
	</tr>
	<tr>
		<td>Replace string:</td>
		<td><input type="text" name="replace" size="60" /></td>
	</tr>
	<tr>
		<td valign="top">Post types:</td>
		<td><?php
		//get all
		$post_types  = get_post_types(array('public' => true), 'object');
		unset($post_types['attachment']);
		foreach ($post_types as $type => $info) {
			echo '<label><input type="checkbox" name="post_type[]" value="' . $type . '"> ' . $info->labels->singular_name . '</label><br>';
		}
		?> <label><input type="checkbox" name="post_type[]" value="trash">
		Trash</label></td>
	</tr>
</table>
<input type="submit" name="submitbutton" value="Search and replace"
	style="margin-top: 2px;"></form>

<h3>How to use?</h3>
<p class="updated">* Search &amp; replace works case sensitive!<br />
&nbsp;&nbsp;&nbsp;A search for "MySearch" will not find content with
"mysearch".<br />
* Only the current version of your page or post will be updated.<br />
* Example when you moved domains and you want to replace all links in
your content:<br />
&nbsp;&nbsp;&nbsp; Search for:<br />
&nbsp;&nbsp;&nbsp; <em>http://www.myoldserver.tld</em><br />
&nbsp;&nbsp;&nbsp; Replace with:<br />
&nbsp;&nbsp;&nbsp; <em>http://www.mynewserver.tld</em></p>
