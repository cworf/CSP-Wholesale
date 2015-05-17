<?php
/**
 * Related Products
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product, $woocommerce_loop;

$related = $product->get_related( $posts_per_page );

if ( sizeof( $related ) == 0 ) return;

$args = apply_filters( 'woocommerce_related_products_args', array(
	'post_type'				=> 'product',
	'ignore_sticky_posts'	=> 1,
	'no_found_rows' 		=> 1,
	'posts_per_page' 		=> $posts_per_page,
	'orderby' 				=> $orderby,
	'post__in' 				=> $related,
	'post__not_in'			=> array( $product->id )
) );

$products = new WP_Query( $args );

$woocommerce_loop['columns'] = $columns;

if ( $products->have_posts() ) : ?>
<div class="related products">
    <div class="tesla-carousel" data-tesla-plugin="carousel" data-tesla-container=".tesla-carousel-items" data-tesla-item=">div" data-tesla-autoplay="false" data-tesla-rotate="false">
        <div class="site-title">
            <ul class="wrapper-arrows">
                <li><i class="icon-517 prev" title="left arrow"></i></li>
                <li><i class="icon-501 next" title="right arrow"></i></li>
            </ul>
            <div class="site-inside"><span><?php _e( 'We also recommend', 'woocommerce' ); ?></span></div>
        </div>
		<div class="row">
            <div class="tesla-carousel-items">
				<?php //woocommerce_product_loop_start(); ?>

					<?php while ( $products->have_posts() ) : $products->the_post(); ?>

						<?php wc_get_template_part( 'content', 'product' ); ?>

					<?php endwhile; // end of the loop. ?>

				<?php //woocommerce_product_loop_end(); ?>

			</div>
		</div>
	</div>
</div>
<?php endif;

wp_reset_postdata();
