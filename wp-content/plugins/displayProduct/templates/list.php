<?php
if (!defined('ABSPATH'))
    die("Can't load this file directly");

//Style
$result.='<div id="displayProduct" class="product_list woocommerce">';
$metagroup = array(
    'sku'      => True,
    'metacategory'    => True,
    'metatag'    => True,
);
$enable = explode(',', $list);
while ($r->have_posts()) {
    $r->the_post();
    global $product;
    $result.='<div id="displayProduct-' . $r->post->ID . '" class="dp-section dp-group dp_product_item">';
    if (in_array( 'link',$enable)) {
        $link_start = '<a href="' . get_permalink() . '" title="' . get_the_title() . '">';
        $link_end = '</a>';
        $link_thumb_start = '<a href="' . get_permalink() . '"  title="' . get_the_title() . '" class="dp-product-image">';
    }
    if (in_array( 'image',$enable)) {
        $result.='<div class="dp-col dp-list-thumb dp-col_1_of_5 dp_images">';
        $result.=$link_thumb_start;
        $result.=dp_get_image($dpanimatehover);
        if(in_array( 'quickview',$enable)){
            $result.='<span class="dpquickview dp_quickview_button" data-id="'.  get_the_ID().'"><img src="'.DP_DIR.'/assets/images/quickview.png"></span>';
        }
        $result.=$link_end;
        if ($sale == 'show') {
            $result.=dp_get_sale_flash();
        }
        if($outofstock == 'show'){
            if (!$product->is_in_stock()) {
                $result.='<span class="outofstock">' . __( 'Out of stock', DP_TEXTDOMAN) . '</span>';
            }
        }
        if($featured == 'show'){
            if( $product->is_featured() ) {
                $result.='<span class="onfeatured">Featured</span>';
            }
        }
        $result.='</div>';
    }
    if (in_array( 'title',$enable)) {
        $result.='<div class="dp-col dp-list-desctiption dp-col_3_of_5"><h2 class="product-name">' . $link_start . get_the_title() . $link_end . '</h2>';
        if (in_array( 'star',$enable)) {
            $result.='<div class="dp-list-rating">' . $product->get_rating_html() . '</div>';
        }
        if (in_array( 'excerpt',$enable)) {
            $result.= '<p>' . wp_trim_words( get_the_content(), $trimwords ) . '</p>';
        }
        if(in_array( 'metagroup',$enable)){
            $result.= '<div class="product_meta">';
                do_action( 'woocommerce_product_meta_start' );
                    if($sku=='show'){
                        if ( $product->is_type( array( 'simple', 'variable' ) ) && get_option( 'woocommerce_enable_sku' ) == 'yes' && $product->get_sku() ) :
                                $result.= '<span itemprop="productID" class="sku_wrapper">'.__( 'SKU:', DP_TEXTDOMAN).'<span class="sku dp-sku">'.$product->get_sku().'</span></span>';
                        endif;
                    }
                    if($metacategory=='show'){
                        $size = sizeof( get_the_terms( $post->ID, 'product_cat' ) );
                        $result.= $product->get_categories( ', ', '<span class="posted_in">' . _n( 'Category:', 'Categories:', $size, DP_TEXTDOMAN) . ' ', '.</span>' );
                    }
                    if($metatag=='show'){
                        $size = sizeof( get_the_terms( $post->ID, 'product_tag' ) );
                        $result.= $product->get_tags( ', ', '<span class="tagged_as">' . _n( 'Tag:', 'Tags:', $size, DP_TEXTDOMAN) . ' ', '.</span>' );
                    }
                 do_action( 'woocommerce_product_meta_end' );
            $result.= '</div>';
        }
        $result.='</div>';
    }
    if (in_array( 'price',$enable) || in_array( 'button',$enable)) {
        $result.='<div class="dp-col dp-list-add-to-cart dp-col_1_of_5">';
        if (in_array( 'price',$enable)) {
            $result.='<div class="dp-list-price dp-price">' . $product->get_price_html() . '</div>';
        }
        $result.='<div class="dp-stock"></div>';
        if (in_array( 'button',$enable)) {
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
            }
        }//$button Show / Hide
        $result.='</div>';
    }
    $result.='</div>';
}
$result.='</div>';
?>
