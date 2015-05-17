<?php

$display  = __('Your account has been approved successfully. ','upme') . "\r\n\r\n";
        
$display .= __('Username:','upme').  " {%username%} \r\n\r\n";
$display .= __('E-mail:','upme'). " {%email%} \r\n";

$display .= __('You can now log in to use your account using the following link.','upme') . "\r\n\r\n";
$display .= " {%login_link%} \r\n\r\n";
$display .= __('Thanks','upme') . "\r\n";
$display .= "{%blog_name%} \r\n\r\n";

echo $display;

?>