<?php 
	
/***********************************************************************************************/
/* Shortcodes */
/***********************************************************************************************/


/* Shorcode row (Template structure)
============================================*/
    add_shortcode('container', 'container');

function container($atts, $content = null) {
    extract(shortcode_atts(array(
        'addclass' => ''
    ), $atts));
    
    return '<div class="container '.$addclass.'">'. do_shortcode($content) .'</div>';
}

	add_shortcode('row', 'row');

function row($atts, $content = null) {
	extract(shortcode_atts(array(
		'addclass' => ''
	), $atts));
	
	return '<div class="row '.$addclass.'">'. do_shortcode($content) .'</div>';
}


/* Shorcode fluid (Template structure)
============================================*/
	add_shortcode('fluid', 'fluid');

function fluid($atts, $content = null) {
	extract(shortcode_atts(array(
		'addclass' => ''
	), $atts));

	return '<div class="row-fluid '.$addclass.'">'. do_shortcode($content) .'</div>';
}

/* Shorcode span (Template structure)
============================================*/

	add_shortcode('column', 'column');

function column($atts, $content = null) {
	extract(shortcode_atts(array(
		'size' => '12',
		'addclass' => ''
	), $atts));

	$content = wpautop(trim($content));
	
	return '<div class="col-md-'.$size.' '.$addclass.'">'. do_shortcode($content) .'</div>';
}


/* Shorcode alert (Typography)
============================================*/

add_shortcode('alert', 'alert');

function alert($atts, $content = null) {
	extract(shortcode_atts(array(
		'type' => 'warning'
	), $atts));
    return '<div class="alert alert-'.$type.'">
				<button type="button" class="close" data-dismiss="alert">×</button>
				<h4>'.do_shortcode($content).'</h4>
			</div>';
}

//=================BASIC SHORTCODES===========================================
function button($atts, $content = 'Button') {
    extract(shortcode_atts(array(
                'type' => '',
                'link' => '#'
                    ), $atts));
    $output = "<a href='{$link}' class='button-{$type}'>" . do_shortcode($content) . "</a>";
    return $output;
}

add_shortcode('button', 'button');

function tesla_ad($atts, $content = 'Button') {
    extract(shortcode_atts(array(
                'type' => '',
                'link' => '#',
                'img'  => '#'
                    ), $atts));
    return '<div class="adds">
                <a href="'.$link.'"><img src="'.$img.'" alt="banner"></a>
            </div>';
}

add_shortcode('tesla_ad', 'tesla_ad');


function tesla_title($atts, $content = null) {
    return '<div class="site-title"><div class="site-inside"><span>'. do_shortcode($content) .'</span></div></div>';
}
add_shortcode('tesla_title', 'tesla_title');

function tesla_box($atts, $content = null) {
    return '</div><div class="box color-2"><div class="container">'. do_shortcode($content) .'</div></div><div class="container">';
}
add_shortcode('tesla_box', 'tesla_box');

function tesla_hr($atts, $content = null) {
    return '</div><hr><div class="container">';
}
add_shortcode('tesla_hr', 'tesla_hr');

//===================latest posts================================
function tesla_latest_posts($atts, $content = null) {
    extract(shortcode_atts(array(
                'nr_posts' => 2,
                'title' => 'From the blog'
                ), $atts));
    $args = array(            
            //Type & Status Parameters
            'post_type'   => 'post',
            'post_status' => 'publish',
            'posts_per_page' => $nr_posts,
            'ignore_sticky_posts' => 1,
            
            //Order & Orderby Parameters
            'order'               => 'DESC',
            'orderby'             => 'date',
            
        );
    
    $recent  = new WP_Query( $args );
    ob_start();
    if($recent->have_posts()) : ?>
        <div class="site-title"><div class="site-inside"><span><?php echo do_shortcode($title) ?></span></div></div>
        <div class="row home-blog-show">
            <?php while ($recent->have_posts()) : $recent->the_post(); ?>
                <div class="col-md-<?php echo 12/$nr_posts ?>">
                    <div class="blog-entry">
                        <div class="entry-cover">
                            <?php tt_video_or_image_featured(  ) ?>
                        </div>
                        <div class="entry-header">
                            <h1><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h1>
                            <ul class="blog-entry-details">
                                <li><?php _e('Posted by','zeon') ?> <?php the_author_link(); ?>&nbsp;&nbsp;&nbsp;/</li>
                                <li><?php _e('Date','zeon') ?> <a href="<?php the_permalink() ?>"><?php the_time( get_option('date_format') ); ?></a>/</li>
                                <li><?php _e('Category','zeon') ?> <?php the_category( ' / ' ); ?></li>
                            </ul>
                        </div>
                        <div class="entry-content">
                            <?php the_excerpt() ?>
                        </div>
                        <div class="entry-footer">
                            <a href="<?php the_permalink() ?>" class="button-4"><?php _e('read more','zeon') ?></a>
                        </div>
                    </div>
                </div>
            <?php endwhile;?>
        </div>
    <?php endif;
    wp_reset_postdata();

    return ob_get_clean() ;
}

add_shortcode('tesla_latest_posts', 'tesla_latest_posts');

function tesla_featured_products( $atts ) {
    global $woocommerce_loop;

    extract( shortcode_atts( array(
        'per_page'  => '12',
        'orderby'   => 'date',
        'order'     => 'desc',
        'title'     => 'Featured Products'
    ), $atts ) );

    $args = array(
        'post_type'             => 'product',
        'post_status'           => 'publish',
        'ignore_sticky_posts'   => 1,
        'posts_per_page'        => $per_page,
        'orderby'               => $orderby,
        'order'                 => $order,
        'meta_query'            => array(
            array(
                'key'       => '_visibility',
                'value'     => array('catalog', 'visible'),
                'compare'   => 'IN'
            ),
            array(
                'key'       => '_featured',
                'value'     => 'yes'
            )
        )
    );

    ob_start();

    $products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );
    
    global $no_product_rows;
    $no_product_rows = true;
    if ( $products->have_posts() ) : ?>
        </div>
        <div class="box">
            <div class="container">
                <div class="tesla-carousel" data-tesla-plugin="carousel" data-tesla-container=".tesla-carousel-items" data-tesla-item=">div" data-tesla-autoplay="false" data-tesla-rotate="false">
                    <div class="site-title">
                        <ul class="wrapper-arrows">
                            <li><i class="icon-517 prev" title="left arrow"></i></li>
                            <li><i class="icon-501 next" title="right arrow"></i></li>
                        </ul>
                        <div class="site-inside">
                            <span><?php echo $title ?></span>
                        </div>
                    </div> 
                    <div class="row">
                        <div class="tesla-carousel-items">
                            <?php while ( $products->have_posts() ) : $products->the_post(); ?>
                                
                                    <?php wc_get_template_part( 'content', 'product' ); ?>

                            <?php endwhile; // end of the loop. ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container"> <!-- Featured re-opening container -->
    <?php endif;
    $no_product_rows = NULL;
    wp_reset_postdata();

    return ob_get_clean() ;
}
add_shortcode('tesla_featured_products', 'tesla_featured_products');

function tesla_recent_products( $atts ) {
    global $woocommerce_loop;

    extract( shortcode_atts( array(
        'per_page'  => '12',
        'columns'   => '4',
        'orderby'   => 'date',
        'order'     => 'desc',
        'title'     => 'Latest Products'
    ), $atts ) );

    $meta_query = WC()->query->get_meta_query();

    $args = array(
        'post_type'             => 'product',
        'post_status'           => 'publish',
        'ignore_sticky_posts'   => 1,
        'posts_per_page'        => $per_page,
        'orderby'               => $orderby,
        'order'                 => $order,
        'meta_query'            => $meta_query
    );

    ob_start();

    $products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );
    global $no_product_rows;
    $no_product_rows = true;
    if ( $products->have_posts() ) : ?>
        </div>
        <div class="box">
            <div class="container">
                <div class="tesla-carousel" data-tesla-plugin="carousel" data-tesla-container=".tesla-carousel-items" data-tesla-item=">div" data-tesla-autoplay="false" data-tesla-rotate="false">
                    <div class="site-title">
                        <ul class="wrapper-arrows">
                            <li><i class="icon-517 prev" title="left arrow"></i></li>
                            <li><i class="icon-501 next" title="right arrow"></i></li>
                        </ul>
                        <div class="site-inside">
                            <span><?php echo $title ?></span>
                        </div>
                    </div> 
                    <div class="row">
                        <div class="tesla-carousel-items">
                            <?php while ( $products->have_posts() ) : $products->the_post(); ?>
                                
                                    <?php wc_get_template_part( 'content', 'product' ); ?>

                            <?php endwhile; // end of the loop. ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container"> <!-- Recent re-opening container -->

    <?php endif;
    $no_product_rows = NULL;
    wp_reset_postdata();

    return ob_get_clean() ;
}
add_shortcode('tesla_recent_products', 'tesla_recent_products');

function tesla_best_selling_products( $atts ) {
    global $woocommerce_loop;

    extract( shortcode_atts( array(
        'per_page'  => '12',
        'columns'   => '4',
        'orderby'   => 'date',
        'order'     => 'desc',
        'title'     => 'Best Sellers'
    ), $atts ) );

    $meta_query = WC()->query->get_meta_query();

    $args = array(
        'post_type'             => 'product',
        'post_status'           => 'publish',
        'ignore_sticky_posts'   => 1,
        'posts_per_page'        => $per_page,
        'orderby'               => $orderby,
        'order'                 => $order,
        'meta_query'            => $meta_query
    );

    ob_start();

    $products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );

    if ( $products->have_posts() ) : ?>
        <div class="site-title">
            <div class="site-inside">
                <span><?php echo $title ?></span>
            </div>
        </div> 
        <div class="row">
            <div class="col-md-6 col-xs-6">
                
                <?php while ( $products->have_posts() ) : 
                    $products->the_post(); 
                    global $product;
                    if ( $products->current_post == $per_page/2 ) : ?>
                        </div>
                        <div class="col-md-6 col-xs-6">
                    <?php endif; ?>
                    <div class="item">
                        <div class="item-cover">
                            <?php echo woocommerce_get_product_thumbnail(); ?>
                        </div>
                        <h5><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
                        <?php echo apply_filters( 'woocommerce_short_description', get_the_excerpt() ) ?>
                        <?php if ( $price_html = $product->get_price_html() ) : ?>
                            <div class="item-price"><?php echo $price_html; ?></div>
                        <?php endif; ?>
                    </div>

                <?php endwhile; // end of the loop. ?>

            </div>
        </div>

    <?php endif;

    wp_reset_postdata();

    return ob_get_clean() ;
}
add_shortcode('tesla_best_selling_products', 'tesla_best_selling_products');

function tesla_best_selling_products_2( $atts ) {
    global $woocommerce_loop;

    extract( shortcode_atts( array(
        'per_page'  => '12',
        'columns'   => '4',
        'orderby'   => 'date',
        'order'     => 'desc',
        'title'     => 'Best Sellers'
    ), $atts ) );

    $meta_query = WC()->query->get_meta_query();

    $args = array(
        'post_type'             => 'product',
        'post_status'           => 'publish',
        'ignore_sticky_posts'   => 1,
        'posts_per_page'        => $per_page,
        'orderby'               => $orderby,
        'order'                 => $order,
        'meta_query'            => $meta_query
    );

    ob_start();

    $products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );
    global $no_product_rows;
    $no_product_rows = true;
    if ( $products->have_posts() ) : ?>
        </div>
        <div class="box">
            <div class="container">
                <div class="tesla-carousel" data-tesla-plugin="carousel" data-tesla-container=".tesla-carousel-items" data-tesla-item=">div" data-tesla-autoplay="false" data-tesla-rotate="false">
                    <div class="site-title">
                        <ul class="wrapper-arrows">
                            <li><i class="icon-517 prev" title="left arrow"></i></li>
                            <li><i class="icon-501 next" title="right arrow"></i></li>
                        </ul>
                        <div class="site-inside">
                            <span><?php echo $title ?></span>
                        </div>
                    </div> 
                    <div class="row">
                        <div class="tesla-carousel-items">
                            <?php while ( $products->have_posts() ) : $products->the_post(); ?>
                                
                                    <?php wc_get_template_part( 'content', 'product' ); ?>

                            <?php endwhile; // end of the loop. ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container"> <!-- Recent re-opening container -->

    <?php endif;
    $no_product_rows = NULL;
    wp_reset_postdata();

    return ob_get_clean() ;
}
add_shortcode('tesla_best_selling_products_2', 'tesla_best_selling_products_2');

function tesla_products( $atts ) {
    global $woocommerce_loop;

    if ( empty( $atts ) ) return '';

    extract( shortcode_atts( array(
        'columns'   => '4',
        'orderby'   => 'title',
        'order'     => 'asc',
        'title'     => 'Products'
    ), $atts ) );

    $args = array(
        'post_type'             => 'product',
        'post_status'           => 'publish',
        'ignore_sticky_posts'   => 1,
        'orderby'               => $orderby,
        'order'                 => $order,
        'posts_per_page'        => -1,
        'meta_query'            => array(
            array(
                'key'       => '_visibility',
                'value'     => array('catalog', 'visible'),
                'compare'   => 'IN'
            )
        )
    );

    if ( isset( $atts['skus'] ) ) {
        $skus = explode( ',', $atts['skus'] );
        $skus = array_map( 'trim', $skus );
        $args['meta_query'][] = array(
            'key'       => '_sku',
            'value'     => $skus,
            'compare'   => 'IN'
        );
    }

    if ( isset( $atts['ids'] ) ) {
        $ids = explode( ',', $atts['ids'] );
        $ids = array_map( 'trim', $ids );
        $args['post__in'] = $ids;
    }

    ob_start();

    $products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );

    if ( $products->have_posts() ) : ?>
        <div class="site-title">
            <div class="site-inside">
                <span><?php echo $title ?></span>
            </div>
        </div> 
        <?php woocommerce_product_loop_start(); ?>

            <?php while ( $products->have_posts() ) : $products->the_post(); ?>
                
                    <?php wc_get_template_part( 'content', 'product' ); ?>

            <?php endwhile; // end of the loop. ?>

        <?php woocommerce_product_loop_end(); ?>

    <?php endif;

    wp_reset_postdata();

    return ob_get_clean();
}
add_shortcode('tesla_products', 'tesla_products');

function tesla_clearance( $atts ) {
    global $woocommerce_loop,$no_product_columns;

    if ( empty( $atts ) ) return '';

    extract( shortcode_atts( array(
        'columns'   => '4',
        'orderby'   => 'title',
        'order'     => 'asc',
        'title'     => 'Clearance',
        'boxed'     => ''
    ), $atts ) );

    $args = array(
        'post_type'             => 'product',
        'post_status'           => 'publish',
        'ignore_sticky_posts'   => 1,
        'orderby'               => $orderby,
        'order'                 => $order,
        'posts_per_page'        => -1,
        'meta_query'            => array(
            array(
                'key'       => '_visibility',
                'value'     => array('catalog', 'visible'),
                'compare'   => 'IN'
            )
        )
    );

    if ( isset( $atts['skus'] ) ) {
        $skus = explode( ',', $atts['skus'] );
        $skus = array_map( 'trim', $skus );
        $args['meta_query'][] = array(
            'key'       => '_sku',
            'value'     => $skus,
            'compare'   => 'IN'
        );
    }

    if ( isset( $atts['ids'] ) ) {
        $ids = explode( ',', $atts['ids'] );
        $ids = array_map( 'trim', $ids );
        $args['post__in'] = $ids;
    }

    ob_start();

    $products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );
    global $no_product_rows;
    $no_product_rows = true;
    
    if ( $products->have_posts() ) : 
        remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail' ,10);
        add_action( 'woocommerce_before_shop_loop_item_title', 'tesla_template_loop_product_thumbnail' ,10);
        $no_product_columns = true;?>
        <!-- START -->
        <?php if(!empty($boxed)): ?>
            </div><!-- container -->
            <div class="box color-3">
                <div class="container">
        <?php endif; ?>
        <div class="site-title">
            <div class="site-inside">
                <span><?php echo $title ?></span>
            </div>
        </div>

        <?php woocommerce_product_loop_start(); ?>

            <div class="col-md-6">
                <?php while ( $products->have_posts() ) : $products->the_post(); ?>
                    <?php if($products->current_post == 1) : ?>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6 col-xs-6">
                    <?php elseif ($products->current_post == 3) : ?>
                        </div>
                        <div class="col-md-6 col-xs-6">
                    <?php endif; ?>
                    <?php wc_get_template_part( 'content', 'product' ); ?>

                <?php endwhile; ?>
                <?php if($products->post_count >= 2) : ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

        <?php woocommerce_product_loop_end(); ?>
        <?php if(!empty($boxed)): ?>
            </div>
        </div>
        <div class="container">
        <?php endif; ?>
    <!-- END -->

    <?php $no_product_columns = false;
    remove_action( 'woocommerce_before_shop_loop_item_title', 'tesla_template_loop_product_thumbnail' ,10);
    add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail' ,10);
    endif;
    $no_product_rows = NULL;
    wp_reset_postdata();

    return ob_get_clean();
}
add_shortcode('tesla_clearance', 'tesla_clearance');

function tesla_template_loop_product_thumbnail(){
    echo woocommerce_get_product_thumbnail( $size = 'shop_single');
}