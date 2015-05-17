<?php

$display  = " {%full_name%} ".__('has updated profile information. ','upme') . "\r\n\r\n";
        
$display .= __('Please find the updated information below','upme').  "\r\n\r\n";
$display .= " {%changed_fields%} \r\n";

$display .= __('Thanks','upme') . "\r\n";
$display .= "{%blog_name%} \r\n\r\n";

echo $display;
?>