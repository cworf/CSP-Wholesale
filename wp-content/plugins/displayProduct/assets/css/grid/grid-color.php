<?php
function dp_grid_color_css($display_id,$tablebackground,$tableheadbackground,$tableheadtextcolor,$tablerowhovercolor,$bordercolor,$productnamecolor,$productnamehovercolor,$pricecolor,$textcolor,$linkcolor,$linkhovercolor,$buttoncolor,$buttonhovercolor,$backgroundcolor,$featuredcolor,$salecolor) {
        $output = '';
        $output ='
            #'.$display_id.'.displayProduct-shortcode .dp-grid-price del span.amount,
            #'.$display_id.'.displayProduct-shortcode .dp-grid-price span.amount,
            #'.$display_id.'.displayProduct-shortcode .dp-grid-price ins span.amount
            { 
                color: '.$pricecolor.';
            }/* dp col*/
            #'.$display_id.'.displayProduct-shortcode .product_grid .dp-col{
                background: '.$backgroundcolor.';
                color: '.$textcolor.';
            }
            #'.$display_id.'.displayProduct-shortcode .product_grid .dp-col:hover{
                border-color: '.$bordercolor.';
            }
            
            /* Product Name */
            #'.$display_id.'.displayProduct-shortcode.displayProduct-Container .product_grid .product-name a{
                color: '.$productnamecolor.';
            }
            #'.$display_id.'.displayProduct-shortcode.displayProduct-Container .product_grid .product-name a:hover{
                color: '.$productnamehovercolor.';
            }
            
            /* Link */
            #'.$display_id.'.displayProduct-shortcode .product_grid a, 
            #'.$display_id.'.displayProduct-shortcode .product_grid a:active, 
            #'.$display_id.'.displayProduct-shortcode .product_grid a:visited,
            #'.$display_id.'.displayProduct-shortcode .product_meta a,
            #'.$display_id.'.displayProduct-shortcode .db_customtext
            {
                color: '.$linkcolor.';
            }
            #'.$display_id.'.displayProduct-shortcode .product_grid a:hover,
            #'.$display_id.'.displayProduct-shortcode .product_meta a:hover,
            #'.$display_id.'.displayProduct-shortcode .db_customtext:hover
            {
                color: '.$linkhovercolor.';
            }
            
            
            /* Button */
            #'.$display_id.'.displayProduct-shortcode.displayProduct-Container a.dp-button,
            #'.$display_id.'.displayProduct-shortcode.displayProduct-Container button.single_add_to_cart_button.alt,
            #main #'.$display_id.'.displayProduct-shortcode.displayProduct-Container button.comment-submit,
            #'.$display_id.'.displayProduct-shortcode.displayProduct-Container a.single_add_to_cart_button.alt,
                .dp_quickview .cart a.button,.dp_quickview form.cart .button{
                background: '.$buttoncolor.';
                color: #ffffff !important;
                border:0;
                -webkit-border-radius: 0px;
                -moz-border-radius: 0px;
                -ms-border-radius: 0px;
                -o-border-radius: 0px;
                border-radius: 0px; 
                padding:0;
                width: 100%;
                text-shadow: none;
                font-weight: normal;
                line-height: 26px;
            }
            #'.$display_id.'.displayProduct-shortcode.displayProduct-Container a.dp-button:hover ,
                .dp_quickview .cart a.button:hover,.dp_quickview form.cart .button.alt:hover,
            #'.$display_id.'.displayProduct-shortcode.displayProduct-Container button.single_add_to_cart_button.alt:hover,
            #main #'.$display_id.'.displayProduct-shortcode.displayProduct-Container button.comment-submit:hover,
            #'.$display_id.'.displayProduct-shortcode.displayProduct-Container a.single_add_to_cart_button.alt:hover,
            #'.$display_id.'.displayProduct-shortcode.displayProduct-Container .dp-section.woocommerce button.single_add_to_cart_button.button.alt:hover,
            #'.$display_id.'.displayProduct-shortcode.displayProduct-Container .dp-section.woocommerce a.single_add_to_cart_button.button.alt:hover,
            .woocommerce button.button.alt:hover{
                background: '.$buttonhovercolor.' !important;
                color: #ffffff !important;
                text-shadow: none;
            }
            #'.$display_id.'.displayProduct-shortcode.displayProduct-Container .dp-section.woocommerce span.onfeatured, 
            #'.$display_id.'.displayProduct-shortcode.displayProduct-Container .dp-section.woocommerce-page span.onfeatured{
               background: '.$featuredcolor.';
            }
            /* Onsale */
            #'.$display_id.'.displayProduct-shortcode.displayProduct-Container .dp-section.woocommerce span.onsale, 
            #'.$display_id.'.displayProduct-shortcode.displayProduct-Container .dp-section.woocommerce-page span.onsale {
                background: '.$salecolor.';
                text-shadow:none
            }';
        
        // Output styles
        if ($output <> '') {
                $output = "<!-- Custom Styling --><style type=\"text/css\">" . $output . "</style>";
                return $output;
        }

}
?>
