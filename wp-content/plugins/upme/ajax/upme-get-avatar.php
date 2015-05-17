<?php

/* include WordPress */
define( 'WP_USE_THEMES', false );
require('../../../../wp-load.php');

global $upme;

$email = $_GET['email'];

echo $upme->pic($email, 50);