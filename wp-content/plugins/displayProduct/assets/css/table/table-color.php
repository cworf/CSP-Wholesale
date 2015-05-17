<?php
function dp_table_color_css($display_id,$tablebackground,$tableheadbackground,$tableheadtextcolor,$tablerowhovercolor,$bordercolor,$productnamecolor,$productnamehovercolor,$pricecolor,$textcolor,$linkcolor,$linkhovercolor,$buttoncolor,$buttonhovercolor) {
        $output = '';
        $output ='
            #'.$display_id.'.displayProduct-shortcode .dp-table-price del span.amount,
            #'.$display_id.'.displayProduct-shortcode .dp-table-price span.amount,
            #'.$display_id.'.displayProduct-shortcode .dp-table-price ins span.amount
            { 
                color: '.$pricecolor.';
            }
            
            /* Table Style */
            .displayProduct-Container table.product_table ,
            .displayProduct-Container table.product_table td{
                border: 1px solid '.$bordercolor.';
            }
            .displayProduct-Container table.product_table thead tr th {
                background-color: '.$tableheadbackground.'!important;
                color:'.$tableheadtextcolor.';
            }
            #'.$display_id.'.displayProduct-shortcode.displayProduct-Container  table.product_table tr {
                background: '.$tablebackground.'!important;
            }
            #'.$display_id.'.displayProduct-shortcode.displayProduct-Container table.product_table tr:hover {
                background-color: '.$tablerowhovercolor.' !important;
            }
            /* dp col*/
            #'.$display_id.'.displayProduct-shortcode .product_table{
                color: '.$textcolor.';
            }
            #'.$display_id.'.displayProduct-shortcode .product_table{
                border-color: '.$bordercolor.';
            }
            
            /* Product Name */
            #'.$display_id.'.displayProduct-shortcode.displayProduct-Container .product_table .product-name a{
                color: '.$productnamecolor.';
            }
            #'.$display_id.'.displayProduct-shortcode.displayProduct-Container .product_table .product-name a:hover{
                color: '.$productnamehovercolor.' !important;
            }
            
            /* Link */
            #'.$display_id.'.displayProduct-shortcode .product_table a, 
            #'.$display_id.'.displayProduct-shortcode .product_table a:active, 
            #'.$display_id.'.displayProduct-shortcode .product_table a:visited,
            #'.$display_id.'.displayProduct-shortcode .product_meta a,
            #'.$display_id.'.displayProduct-shortcode .db_customtext
            {
                color: '.$linkcolor.';
            }
            #'.$display_id.'.displayProduct-shortcode .product_table a:hover,
            #'.$display_id.'.displayProduct-shortcode .product_meta a:hover,
            #'.$display_id.'.displayProduct-shortcode .db_customtext:hover
            {
                color: '.$linkhovercolor.';
            }
            
            
            /* Button */
            #'.$display_id.'.displayProduct-shortcode.displayProduct-Container a.dp-button,
            #'.$display_id.'.displayProduct-shortcode.displayProduct-Container button.single_add_to_cart_button.alt,
            #'.$display_id.'.displayProduct-shortcode.displayProduct-Container a.single_add_to_cart_button.alt,
            .dp_quickview .cart a.button,.dp_quickview form.cart .button{
                background: '.$buttoncolor.';
                color: #ffffff;
                padding:0 5px;
            }
            #'.$display_id.'.displayProduct-shortcode.displayProduct-Container a.dp-button:hover ,
            .dp_quickview .cart a.button:hover,.dp_quickview form.cart .button.alt:hover,
            #'.$display_id.'.displayProduct-shortcode.displayProduct-Container button.single_add_to_cart_button.alt:hover,
            #'.$display_id.'.displayProduct-shortcode.displayProduct-Container a.single_add_to_cart_button.alt:hover,
            #'.$display_id.'.displayProduct-shortcode.displayProduct-Container .dp-section.woocommerce button.single_add_to_cart_button.button.alt:hover,
            #'.$display_id.'.displayProduct-shortcode.displayProduct-Container .dp-section.woocommerce a.single_add_to_cart_button.button.alt:hover,
            .woocommerce button.button.alt:hover{
                background: '.$buttonhovercolor.';
                color: #ffffff;
                text-shadow: none;
            }
            
            /* Onsale */
             .woocommerce span.onsale, .woocommerce-page span.onsale {
                background: #A88aF5C;
                background: -webkit-gradient(linear,left top,left bottom,from(#A88F5C),to(#A88F5C));
                background: -webkit-linear-gradient(#A88F5C,#A88F5C);
                background: -moz-linear-gradient(center top,#A88F5C 0,#A88F5C 100%);
                background: -moz-gradient(center top,#A88F5C 0,#A88F5C 100%);
                text-shadow:0 -1px 0 #A88F5C;
                color: #fff;
            }
            
            .displayProduct-Container table.product_table thead tr {
                background-color: #1aa1e1 !important;
            }
            .displayProduct-Container table.product_table tr:nth-child(even) {
                background-color: #f6fff7;
            }
            .displayProduct-Container table.product_table tr:nth-child(even):hover{
                background-color: #eee;
            }
            ';
        // Output styles
        if ($output <> '') {
                $output = "<!-- Custom Styling -->\n<style type=\"text/css\">\n" . $output . "</style>\n";
                return $output;
        }

}
?>
