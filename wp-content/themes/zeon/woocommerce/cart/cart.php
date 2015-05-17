<?php
/**
 * Cart Page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce; ?>


<h2><?php _e('Shopping cart','zeon') ;?></h2>

<?php wc_print_notices();

do_action( 'woocommerce_before_cart' ); ?>

<form action="<?php echo esc_url( WC()->cart->get_cart_url() ); ?>" method="post">

    <?php do_action( 'woocommerce_before_cart_table' ); ?>

    <div class="shopping-cart">
        <div class="shopping-cart-products">
            <ul class="shopping-product-detail">
                <li class="shopping-1"><?php _e('Product image','zeon') ?></li>
                <li class="shopping-2"><?php _e('Product name','zeon') ?></li>
                <li class="shopping-3"><?php _e('Description','zeon') ?></li>
                <li class="shopping-4"><?php _e('Quantity','zeon') ?></li>
                <li class="shopping-5"><?php _e('Total','zeon') ?></li>
                <li class="shopping-6"><?php _e('Remove','zeon') ?></li>
            </ul>
    	
    		<?php do_action( 'woocommerce_before_cart_contents' ); ?>

            <?php
    		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
    			$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
    			$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

    			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
    				?>
                    <ul class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?> shopping-product-detail">
	
                        <li class="product-thumbnail shopping-1">
    						<?php
    							$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

    							if ( ! $_product->is_visible() )
    								echo $thumbnail;
    							else
    								printf( '<a href="%s">%s</a>', $_product->get_permalink(), $thumbnail );
    						?>
    					</li>

    					<li class="product-name shopping-2">
    						<?php
    							if ( ! $_product->is_visible() )
    								echo apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key );
    							else
    								echo apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', $_product->get_permalink(), $_product->get_title() ), $cart_item, $cart_item_key );

    							// Meta data
    							echo WC()->cart->get_item_data( $cart_item );

                   				// Backorder notification
                   				if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) )
                   					echo '<p class="backorder_notification">' . __( 'Available on backorder', 'woocommerce' ) . '</p>';
    						?>
    					</li>

    					<li class="product-price shopping-3">
    						<?php
    							echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
    						?>
    					</li>

    					<li class="product-quantity shopping-4">
    						<?php
    							if ( $_product->is_sold_individually() ) {
    								$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
    							} else {
    								$product_quantity = woocommerce_quantity_input( array(
    									'input_name'  => "cart[{$cart_item_key}][qty]",
    									'input_value' => $cart_item['quantity'],
    									'max_value'   => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
    								), $_product, false );
    							}

    							echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key );
    						?>
    					</li>

    					<li class="product-subtotal shopping-5">
    						<?php
    							echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
    						?>
    					</li>

                        <li class="product-remove shopping-6">
                            <?php
                                echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf( '<a href="%s" class="remove" title="%s">&times;</a>', esc_url( WC()->cart->get_remove_url( $cart_item_key ) ), __( 'Remove this item', 'woocommerce' ) ), $cart_item_key );
                            ?>
                        </li>

    				</ul>
    				<?php
    			}
    		}?>

        </div>

		<?php do_action( 'woocommerce_cart_contents' );?>

		<div class="row">
			<div class="col-md-3">
                <a href="<?php echo get_permalink( woocommerce_get_page_id( 'shop' ) ) ?>" class="button-7"><?php _e('Continue shopping','zeon') ?></a>
            </div>
            
			<?php if ( WC()->cart->coupons_enabled() ) { ?>
				<div class="col-md-5">
                    <div class="coupon">
                        <input type="text" name="coupon_code" class="input-line input-text" id="coupon_code" value="" placeholder="<?php _e( 'Coupon code', 'woocommerce' ); ?>" >
                        <input type="submit" class="button button-6" name="apply_coupon" value="<?php _e( 'Apply Coupon', 'woocommerce' ); ?>" />

				        <?php do_action('woocommerce_cart_coupon'); ?>

				    </div>
                </div>
			<?php } ?>
            
            <div class="col-md-4">
                <div class="checkout-total">
                    

                    <?php woocommerce_cart_totals(); ?>

                    <?php woocommerce_shipping_calculator(); ?>
                    
                    <input type="submit" class="button-6 checkout-button button alt wc-forward" name="proceed" value="<?php _e( 'Checkout', 'woocommerce' ); ?>" />
                    <input type="submit" class="button button-6" name="update_cart" value="<?php _e( 'Update Cart', 'woocommerce' ); ?>" />
                    <?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
                </div>
            </div>

			<?php wp_nonce_field( 'woocommerce-cart' ); ?>
			
		</div>

		<?php do_action( 'woocommerce_after_cart_contents' ); ?>
	
        <?php do_action( 'woocommerce_after_cart_table' ); ?>
        </div>
    </form>

<?php do_action( 'woocommerce_after_cart' ); ?>

<?php do_action( 'woocommerce_cart_collaterals' ); ?>