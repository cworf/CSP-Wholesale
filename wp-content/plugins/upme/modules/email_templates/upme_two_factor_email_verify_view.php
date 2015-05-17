<?php


$display = __('Someone (hopefully you) has used this email to verify the login at {%blog_name%}','upme'). "\r\n\r\n";
$display .= __('Username:','upme'). "{%username%} \r\n\r\n";
$display .= __('Email:','upme'). "{%email%} \r\n\r\n";
$display .= __('Please click the link below to login automatically:','upme') . "\r\n\r\n";
$display .= "{%email_two_factor_login_link%} \r\n\r\n";


$display .= __('Thanks','upme') . "\r\n";
$display .= "{%blog_name%} \r\n\r\n";

echo $display;
?>