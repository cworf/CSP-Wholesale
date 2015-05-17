<?php


$display  = __('Someone (hopefully you) has used this email to register at {%blog_name%}','upme'). "\r\n\r\n";
$display .= __('Username:','upme'). "{%username%} \r\n\r\n";
$display .= __('Password:','upme'). "{%password%} \r\n\r\n";

$display .= " {%login_link%}   \r\n\r\n";

$display .= __('Thanks','upme') . "\r\n";
$display .= "{%blog_name%} \r\n\r\n";

echo $display;
?>