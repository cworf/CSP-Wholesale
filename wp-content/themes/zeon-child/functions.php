<?php 
add_filter( 'woocommerce_variable_free_price_html',  'hide_free_price_notice' );
 
add_filter( 'woocommerce_free_price_html',           'hide_free_price_notice' );
 
add_filter( 'woocommerce_variation_free_price_html', 'hide_free_price_notice' );
 
 
 
/**
 * Hides the 'Free!' price notice
 */
function hide_free_price_notice( $price ) {
 
  return '<span class="price-replace">Choose your garment for final price</span>';
}
?>