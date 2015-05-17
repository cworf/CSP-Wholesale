<?php

$display = __('New user registration on ','upme'). "{%blog_name%}: \r\n\r\n";


$display .= __('Username:','upme'). "{%username%} \r\n\r\n";
$display .= __('E-mail:','upme'). "{%email%} \r\n\r\n";

$display .= __('Thanks','upme') . "\r\n";
$display .= "{%blog_name%} \r\n\r\n";

echo $display;

?>