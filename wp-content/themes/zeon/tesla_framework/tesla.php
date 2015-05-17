<?php
//define TeslaThemesFramework directory name
define('TTF', dirname(__FILE__));
//Load framework constants
require_once TTF . '/config/constants.php';

//Load theme details
require_once TT_THEME_DIR . '/theme_config/theme-details.php';

define('THEME_OPTIONS', THEME_NAME . '_options');

//Load main framework classes
require_once TTF . '/extensions/twitteroauth/twitteroauth.php';
require_once TTF . '/core/teslaframework.php';
require_once TTF . '/core/tesla_admin.php';
require_once TTF . '/core/tt_load.php';
if(file_exists(TTF . '/core/tt_security.php'))
	require_once TTF . '/core/tt_security.php';
else
	exit();
//TT ENQUEUE
require_once TTF . '/core/tt_enqueue.php';
TT_ENQUEUE::init_enqueue();
//Contact Form Builder
if(file_exists(TT_THEME_DIR . '/theme_config/contact-form-config.php')){
	require_once TTF . '/core/tt_contact_form.php';
	TT_Contact_Form_Builder::init_builder();
}
//Admin load
$TTA = new Tesla_admin;
//Slider
require_once TTF . '/core/tesla_slider.php';
Tesla_slider::init();
//Subscription
if(file_exists(TT_THEME_DIR . '/theme_config/subscription.php')){
	require_once TTF . '/core/tt_subscription.php';
	TT_Subscription::subscription_init();
}