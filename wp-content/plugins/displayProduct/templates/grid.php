<?php
if (!defined('ABSPATH'))
    die("Can't load this file directly");

//Style
$result.='<div id="displayProduct" class="product_grid dp-section dp-group woocommerce">';
// Define Variable
$item = 0;
$metagroup = array(
    'sku'      => True,
    'metacategory'    => True,
    'metatag'    => True,
);
while ($r->have_posts()) {
    $r->the_post();
    global $product;
    $first = '';
        $link_start = '<a href="' . get_permalink() . '" title="' . get_the_title() . '">';
        $link_end = '</a>';
        $link_thumb_start = '<a href="' . get_permalink() . '"  title="' . get_the_title() . '" class="dp-product-image">';
    
    if ($item % $columns == 0) {
        $first = 'firstcol';
    }
    $result.='<div class="dp_product_item dp-col dp-col_1_of_' . $columns . ' ' . $first . '">';
    $gridcolumnsArray = explode(',', $grid);
    foreach($gridcolumnsArray AS $gridcolumn){
        switch ($gridcolumn){

        case 'image':
                $result.='<div class="dp_images">';
                $result.=$link_thumb_start;
                $result.=dp_get_image($dpanimatehover);
                if($quickview == 'default'){
                    $result.='<span class="dpquickview dp_quickview_button" data-id="'.  get_the_ID().'"><img src="'.DP_DIR.'/assets/images/quickview.png"></span>';
                }
                $result.=$link_end;
                $result.='</div>';
            break;
        
        case 'sale':
                $result.=dp_get_sale_flash();
            break;
        case 'outofstock':
                if (!$product->is_in_stock()) {
                    $result.='<span class="outofstock">' . __( 'Out of stock', DP_TEXTDOMAN) . '</span>';
                }
            break;
        case 'featured':
                if( $product->is_featured() ) {
                    $result.='<span class="onfeatured">Featured</span>';
                }
            break;
    
        case 'title':
                $result.= '<div class="dp-product-information clearfix">';
                $result.='<h2 class="product-name">' . $link_start . get_the_title() . $link_end . '</h2>';
            break;
        case 'star':
                $result.='<div class="dp-grid-rating">' . $product->get_rating_html() . '</div>';
            break;
        case 'excerpt':
                $result.= '<p class="dp-grid-excerpt">' . wp_trim_words( get_the_content(), $trimwords ) . '</p>';
            break;
        case 'metagroup':
            $result.= '<div class="product_meta">';
                 do_action( 'woocommerce_product_meta_start' );
                        //if($sku=='show'){
                            if ( $product->is_type( array( 'simple', 'variable' ) ) && get_option( 'woocommerce_enable_sku' ) == 'yes' && $product->get_sku() ) :
                                    $result.= '<span itemprop="productID" class="sku_wrapper">'.__( 'SKU:', DP_TEXTDOMAN).'<span class="sku dp-sku">'.$product->get_sku().'</span></span>';
                            endif;
    //                    }
    //
    //                    if($metacategory=='show'){
                            $size = sizeof( get_the_terms( $post->ID, 'product_cat' ) );
                            $result.= $product->get_categories( ', ', '<span class="posted_in">' . _n( 'Category:', 'Categories:', $size, DP_TEXTDOMAN) . ' ', '.</span>' );
    //                    }
    //                    if($metatag=='show'){
                            $size = sizeof( get_the_terms( $post->ID, 'product_tag' ) );
                            $result.= $product->get_tags( ', ', '<span class="tagged_as">' . _n( 'Tag:', 'Tags:', $size, DP_TEXTDOMAN) . ' ', '.</span>' );
    //                    }
                    do_action( 'woocommerce_product_meta_end' );
                $result.= '</div>';
            break;

    //    global $product, $yith_wcwl;
    //    $result.=YITH_WCWL_UI::add_to_wishlist_button( $yith_wcwl->get_wishlist_url(), $product->product_type, $yith_wcwl->is_product_in_wishlist( $product->id ) );
        case 'price':
                $result.='<div class="dp-grid-price dp-price">' . $product->get_price_html() . '</div>';
            break;
        case 'button':
            $result.='<div class="dp-stock"></div>';
                switch ($addtocartbutton) {
                    case 'buttonquantity':
                        ob_start();
                        woocommerce_template_single_add_to_cart();
                        $result.= ob_get_contents();
                        ob_end_clean();
                        break;
                    case 'productDetail':
                        $result.='<div class="dp-grid-button">' . dp_add_to_cart_productdetail(get_permalink(),$addtocarttext) . '</div>';
                        break;
                    case 'customButton':
                        $result.='<div class="dp-grid-button">' . dp_add_to_cart_customButton($addtocarturl,$addtocarttext) . '</div>';
                        break;
                    case 'customText':
                        $result.='<div class="dp-grid-button">' . dp_add_to_cart_customText($addtocarturl,$addtocarttext) . '</div>';
                        break;
                    default:
                        $result.='<div class="dp-grid-button">' . dp_add_to_cart() . '</div>';
                        break;
            }//$button Show / Hide
            break;
    }
    }
    $result.='<div style="clear: both;"></div></div>';
    $result.='</div>';
    $item++;
    
}
$result.='</div>';
?>
