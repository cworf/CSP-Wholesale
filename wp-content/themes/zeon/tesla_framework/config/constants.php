<?php

define('TT_PREFIX', 'TT_');
define('TT_LOW', 'tt_'); //prefix lowered
define('TT_THEME_DIR', get_template_directory());
define('TT_THEME_URI', get_template_directory_uri());
define('TT_STYLE_DIR', get_stylesheet_directory());
define('TT_STYLE_URI', get_stylesheet_directory_uri());

define ('TT_FW',TT_THEME_URI . '/tesla_framework');
define ('TT_FW_DIR',TT_THEME_DIR . '/tesla_framework');
define('TT_FW_VERSION', '1.9');

$tt_theme = wp_get_theme();
define('THEME_FOLDER_NAME', $tt_theme->template);
$tt_parent_theme = wp_get_theme(THEME_FOLDER_NAME);
define('THEME_VERSION', $tt_parent_theme->version);