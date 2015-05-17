<?php
/* =============================
    Front-end Scripts & Styles 
   ============================= */
add_action( 'wp_ajax_nopriv_dp', 'dp_content');
add_action( 'wp_ajax_dp', 'dp_content' );

add_action( 'wp_enqueue_scripts', 'dp_sands' );

/** Build dp_quickview_single_product_summary **/
    add_action( 'dp_quickview_single_product_summary', 'woocommerce_template_single_title', 5 );
    add_action( 'dp_quickview_single_product_summary', 'woocommerce_template_single_price', 10 );
    add_action( 'dp_quickview_mobView_price', 'woocommerce_template_single_price', 10 );
    add_action( 'dp_quickview_single_product_summary', 'woocommerce_template_single_excerpt', 20 ); 

    add_action( 'dp_quickview_single_product_summary', 'woocommerce_template_single_meta', 40 );
    add_action( 'dp_quickview_single_product_summary', 'woocommerce_template_single_sharing', 50 );
    add_action( 'dp_quickview_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
function dp_sands() {
        /** Scripts **/
            wp_register_script('magnific', DP_DIR .'/plugin/magnific/magnific.js' );
            wp_register_script('dp_quickview',DP_DIR .'/plugin/magnific/magnific-custom.js');

            wp_enqueue_script('jquery');
            wp_enqueue_script('magnific');
            wp_enqueue_script('dp_quickview');

            $jsglobals = array( 
                    'gallery_enabled' => 1,
                    'ajaxurl' => admin_url( 'admin-ajax.php' ),
                    'nonce' => wp_create_nonce( 'dp_quickview_ajax' )
            );
            wp_localize_script( 'dp_quickview', 'dp_globals', $jsglobals );

        /** Styles **/
            wp_register_style( 'magnific', DP_DIR .'/plugin/magnific/magnific.css' );
            wp_register_style( 'dp_quickview', DP_DIR .'/plugin/magnific/magnific-custom.css' );

            wp_enqueue_style( 'magnific' );
            wp_enqueue_style( 'dp_quickview' );
}

/* Search Results AJAX */
function dp_content() {
		if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( $_REQUEST['nonce'], 'dp_quickview_ajax' ) ) { die ( 'Invalid Nonce' ); }
		
		global $post, $product, $woocommerce;
    	$post = get_post($_GET['id']);
    	$product = get_product( $post->ID );
    	
    	$options = get_option( 'dp_woo_quickview', '' );
    	
    	echo '<div class="dp_quickview dp_product_item">';
    		
                        echo '<script src="'.plugins_url().'/woocommerce/assets/js/frontend/add-to-cart-variation.min.js"></script>';
			echo '<script src="'.plugins_url().'/woocommerce/assets/js/frontend/single-product.min.js"></script>';
			echo '<script src="'.plugins_url().'/woocommerce/assets/js/frontend/add-to-cart.min.js"></script>';
                        echo '<script src="'.plugins_url().'/displayProduct/assets/js/dp-front-variation.js?ver=1.0"></script>';
			echo '<script>
				jQuery(document).ready(function($) {
					$("div.quantity:not(.buttons_added), td.quantity:not(.buttons_added)").addClass("buttons_added").append(\'<input type="button" value="+" class="plus" />\').prepend(\'<input type="button" value="-" class="minus" />\');
				});
			</script>';
			
			echo '<div class="mobView product">';
				echo '<h1 class="product_title entry-title">'.$post->post_title.'</h1>';
			echo '</div>';
    
                        echo '<div class="images">';

                                    if ( has_post_thumbnail() ) :
                                            echo get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) ) ;
                                    else :
                                            echo '<img src="'.woocommerce_placeholder_img_src().'" alt="Placeholder" class="attachment-shop_single wp-post-image" />';
                                    endif;

                                    $attachment_ids = $product->get_gallery_attachment_ids();

                                    if ( $attachment_ids) {
                                            ?>
                                            <div class="thumbnails"><?php

                                                    if(has_post_thumbnail()) {
                                                            array_unshift($attachment_ids, get_post_thumbnail_id($post->ID));
                                                    }

                                                    $loop = 0;
                                                    $columns = apply_filters( 'woocommerce_product_thumbnails_columns', 3 );

                                                    foreach ( $attachment_ids as $attachment_id ) {

                                                            $wrapClasses = array('quickviewThumbs-'.$columns.'col', 'dp_quickview_thumb');

                                                            $classes = array('attachment-shop_thumbnail');

                                                            if ( $loop == 0 || $loop % $columns == 0 )
                                                                    $wrapClasses[] = 'first';

                                                            if( $loop == 0 ) {
                                                                    $wrapClasses[] = 'firstThumb';
                                                            }

                                                            if ( ( $loop + 1 ) % $columns == 0 )
                                                                    $wrapClasses[] = 'last';

                                                            $image_class = esc_attr( implode( ' ', $classes ) );

                                                            $lrgImg = wp_get_attachment_image_src($attachment_id, 'shop_single');

                                                            echo '<a href="'.$lrgImg[0].'" class="'.esc_attr( implode( ' ', $wrapClasses ) ).'">'.wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ), false, array('class' => $image_class) ).'</a>';

                                                            $loop++;
                                                    }

                                            ?></div>
                                            <?php
                                    }

                            echo '</div>'; // .images
			
			echo '<div class="product summary entry-summary">';
	
				do_action( 'dp_quickview_single_product_summary' );
				
				echo '<a href="'.get_permalink($post->ID).'" rel="nofollow" class="button viewProduct">'.__('View Product &rarr;', 'dp_woo_quickview').'</a>';
	
			echo '</div>'; // .summary
    	
    	echo '</div>'; // .dp_quickview
    	
    	exit;
}
        
?>
