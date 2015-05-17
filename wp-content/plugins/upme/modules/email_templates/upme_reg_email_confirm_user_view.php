<?php


$display = __('Someone (hopefully you) has used this email to register at {%blog_name%}','upme'). "\r\n\r\n";
$display .= __('Username:','upme'). "{%username%} \r\n\r\n";
$display .= __('Password:','upme'). "{%password%} \r\n\r\n";
$display .= __('Please click the link below to verify your ownership of this email:','upme') . "\r\n\r\n";
$display .= "{%activation_link%} \r\n\r\n";


$display .= __('Thanks','upme') . "\r\n";
$display .= "{%blog_name%} \r\n\r\n";

echo $display;
?>