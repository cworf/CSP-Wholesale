<?php 

$display = __('Someone requested that the password be reset for the following account:','upme') . "\r\n\r\n";
$display .= " {%network_home_url%} \r\n\r\n";
$display .= __('Username:','upme') . " {%username%} \r\n\r\n";
$display .= __('If this was a mistake, just ignore this email and nothing will happen.','upme') . "\r\n\r\n";
$display .= __('To reset your password, visit the following address:','upme') . "\r\n\r\n";
$display .= " {%reset_page_url%} ";

$display .= __('Thanks','upme') . "\r\n";
$display .= "{%blog_name%} \r\n\r\n";

echo $display;