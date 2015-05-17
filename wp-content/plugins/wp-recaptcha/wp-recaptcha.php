<?php
/*
Plugin Name: WP-reCAPTCHA
Description: Integrates reCAPTCHA anti-spam solutions with wordpress
Version: 4.1
Email: support@recaptcha.net
*/

// this is the 'driver' file that instantiates the objects and registers every hook

define('ALLOW_INCLUDE', true);

require_once('recaptcha.php');

$recaptcha = new ReCAPTCHAPlugin('recaptcha_options');

?>
