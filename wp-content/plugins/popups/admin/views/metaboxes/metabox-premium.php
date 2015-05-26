<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
?>
<?php

$today 		= strtotime(date("Y-m-d H:i:s"));
$blackbegin = strtotime("2014-11-28");
$blackend 	= strtotime("2014-12-02");
if($today > $blackbegin && $today < $blackend) : ?>
	<div class="alert-premium">
		<p><strong>Happy Black Friday!</strong> Get any Timersys Plugin with a 40% discount using the <code>BLACKFRIDAY</code> coupon code </p>
	</div>	
<?php endif;?>
	
<p><?php _e( 'Take the best WordPress Popups plugin to the next level with Popups Premium extension.', $this->plugin_slug );?></p>
<h2><?php _e( 'Popups Premium Features:', $this->plugin_slug );?></h2>
<ul>
	<li><?php _e( 'Beautiful optin forms for popular mail providers like MailChimp', $this->plugin_slug );?></li>
	<li><?php _e( 'Track impressions and Conversions of social likes and forms submissions like Contact Form 7, Gravity forms, etc', $this->plugin_slug );?></li>
	<li><?php _e( 'Track impressions and Conversions also in Google Analytics', $this->plugin_slug );?></li>
	<li><?php _e( 'Exit Intent technology', $this->plugin_slug );?></li>
	<li><?php _e( '8 New animations effects', $this->plugin_slug );?> - <a href="http://wp.timersys.com/popups/?utm_source=Plugin&utm_medium=demo-button&utm_campaign=Popups%20Premium">Online demo</a></li>
	<li><?php _e( 'Exit Intent technology', $this->plugin_slug );?></li>
	<li><?php _e( 'New trigger methods', $this->plugin_slug );?></li>
	<li><?php _e( 'Timer for auto closing', $this->plugin_slug );?></li>
	<li><?php _e( 'Ability to disable close button', $this->plugin_slug );?></li>
	<li><?php _e( 'Ability to disable Advanced close methods like esc or clicking outside of the popup', $this->plugin_slug );?></li>
	<li><?php _e( 'Premium support', $this->plugin_slug );?></li>
</ul>
<p><strong>Hurry up and get your copy!</strong> Take advantage of this <span style="color:red">launch offer</span> before the price goes up. We have a <strong>lot of new features</strong> to be added soon like aweber integration, etc</p>
<p style="text-align:center">
	<a class="button-primary" href="http://wp.timersys.com/downloads/popups-premium/?utm_source=Plugin&utm_medium=buy-button&utm_campaign=Popups%20Premium"><?php _e( 'Buy Now!', $this->plugin_slug );?></a>
</p>