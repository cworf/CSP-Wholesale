<?php
if (!defined('ABSPATH'))
    die("Can't load this file directly");
$metagroup = array(
    'sku'      => True,
    'metacategory'    => True,
    'metatag'    => True,
);
$enable = explode(',', $table);
$result.='<table id="displayProduct" class="product_table displayProduct woocommerce">';
$result.='<thead>';
if (in_array( 'image',$enable)) {
    $result.='<th class="dp-table-thumbnail"></th>';
}
if (in_array( 'title',$enable)) {
    $result.='<th class="dp-table-name">Product</th>';
}
if (in_array( 'star',$enable)) {
    $result.='<th class="dp-table-rating">Rating</th>';
}
if (in_array( 'sku',$enable)) {
    $result.='<th class="dp-table-sku">SKU</th>';
}
if (in_array( 'outofstock',$enable)) {
    $result.='<th class="dp-table-stock">Stock</th>';
}
if (in_array( 'price',$enable)) {
    $result.='<th class="dp-table-price">Price</th>';
}
if (in_array( 'button',$enable)) {
    $result.='<th class="dp-table-button"> </th>';
}
$result.='</thead>';
// Define Variable
while ($r->have_posts()) {
    $r->the_post();
    global $product;
    $first = '';

    $result.='<tr id="displayProduct-' . $r->post->ID . '" class="dp_product_item dp-table-tr">';
    if (in_array( 'link',$enable)) {
        $link_start = '<a href="' . get_permalink() . '" title="' . get_the_title() . '">';
        $link_end = '</a>';
        $link_thumb_start = '<a href="' . get_permalink() . '"  title="' . get_the_title() . '" itemprop="image"  data-rel="prettyPhoto' . $gallery . '" class="woocommerce-main-image zoom dp-product-image">';
    }
    if (in_array( 'image',$enable)) {
        $result.='<td class="dp-table-td dp-table-thumb" style="width:32px"><div class="dp_images">';
        $result.=$link_thumb_start;
        $result.=dp_get_image($dpanimatehover);
        if ($sale == 'show') {
            $result.=dp_get_sale_flash();
        }
        if(in_array( 'quickview',$enable)){
            $result.='<span class="dpquickview dp_quickview_button" data-id="'.  get_the_ID().'"><img src="'.DP_DIR.'/assets/images/quickview.png"></span>';
        }
        $result.=$link_end . '</div></td>';
    }
    if (in_array( 'title',$enable)) {
        $result.='<td class="dp-table-td dp-table-title"><h2 class="product-name">' . $link_start . get_the_title() . $link_end . '</h2>';
        if (in_array( 'excerpt',$enable)) {
            $result.= '<p>' . wp_trim_words( get_the_content(), $trimwords ) . '</p>';
        }
        if(in_array( 'metacategory',$enable)){
            $size = sizeof( get_the_terms( $post->ID, 'product_cat' ) );
            $result.= $product->get_categories( ', ', '<span class="posted_in">' . _n( 'Category:', 'Categories:', $size, DP_TEXTDOMAN) . ' ', '.</span>' );
        }
        if(in_array( 'metatag',$enable)){
            $size = sizeof( get_the_terms( $post->ID, 'product_tag' ) );
            $result.= $product->get_tags( ', ', '<span class="tagged_as">' . _n( 'Tag:', 'Tags:', $size, DP_TEXTDOMAN) . ' ', '.</span>' );
        }
        $result.='</td>';
    }
    if (in_array( 'star',$enable)) {
        $result.='<td class="dp-table-td dp-table-rating">' . $product->get_rating_html() . '</td>';
    }
    if (in_array( 'sku',$enable)) {
        $result.='<td class="dp-table-td dp-table-sku dp-sku">';
        if ( $product->is_type( array( 'simple', 'variable' ) ) && get_option( 'woocommerce_enable_sku' ) == 'yes' && $product->get_sku() ) :
                $result.=$product->get_sku();
        endif;
        $result.='</td>';
    }
    if (in_array( 'outofstock',$enable)) {
        $result.='<td class="dp-table-td dp-table-stock dp-stock">';
        if (!$product->is_in_stock()) {
            $result.= __( 'Out of stock', DP_TEXTDOMAN);
        }else{
            $result.=__( 'In stock', DP_TEXTDOMAN);
        }
        $result.='</td>';
    }
    if (in_array( 'price',$enable)) {
        $result.='<td class="dp-table-td dp-table-price dp-price">' . $product->get_price_html() . '</td>';
    }
    if (in_array( 'button',$enable)) {
        $result.='<td class="dp-table-td dp-table-button">';
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
        $result.='</td>';
    }//$button Show / Hide
    $result.='</tr>';
}
$result.='</table>';
?>
