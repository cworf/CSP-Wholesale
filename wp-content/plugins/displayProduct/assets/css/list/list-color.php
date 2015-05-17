<?php
function dp_list_color_css($display_id,$tablebackground,$tableheadbackground,$tableheadtextcolor,$tablerowhovercolor,$bordercolor,$productnamecolor,$productnamehovercolor,$pricecolor,$textcolor,$linkcolor,$linkhovercolor,$buttoncolor,$buttonhovercolor) {
        $output = '';
        $output ='
            #'.$display_id.'.displayProduct-shortcode .product_list del span.amount,
            #'.$display_id.'.displayProduct-shortcode .product_list span.amount,
            #'.$display_id.'.displayProduct-shortcode .product_list ins span.amount
            { 
                color: '.$pricecolor.';
            }
            
            /* dp col*/
            #'.$display_id.'.displayProduct-shortcode .product_list .dp-section{
                color: '.$textcolor.';
            }
            #'.$display_id.'.displayProduct-shortcode .product_list .dp-section:hover .dp-col{
                border-color: '.$bordercolor.';
            }
            
            /* Product Name */
            #'.$display_id.'.displayProduct-shortcode.displayProduct-Container .product_list .product-name a{
                color: '.$productnamecolor.';
            }
            #'.$display_id.'.displayProduct-shortcode.displayProduct-Container .product_list .product-name a:hover{
                color: '.$productnamehovercolor.';
            }
            
            /* Link */
            #'.$display_id.'.displayProduct-shortcode .product_list a, 
            #'.$display_id.'.displayProduct-shortcode .product_list a:active, 
            #'.$display_id.'.displayProduct-shortcode .product_list a:visited,
            #'.$display_id.'.displayProduct-shortcode .product_meta a,
            #'.$display_id.'.displayProduct-shortcode .db_customtext
            {
                color: '.$linkcolor.';
            }
            #'.$display_id.'.displayProduct-shortcode .product_list a:hover,
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
                display: block;
                padding: 2px 0px;
                text-align: center;
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
            }';
        // Output styles
        if ($output <> '') {
                $output = "<!-- Custom Styling -->\n<style type=\"text/css\">\n" . $output . "</style>\n";
                return $output;
        }

}
?>
