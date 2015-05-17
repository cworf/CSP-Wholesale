<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product, $woocommerce_loop,$no_product_columns,$no_product_rows;

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) )
	$woocommerce_loop['loop'] = 0;

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) )
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );

// Ensure visibility
if ( ! $product || ! $product->is_visible() )
	return;

// Increase loop count
$woocommerce_loop['loop']++;

// Extra post classes
$classes = array();
if ( 0 == ( $woocommerce_loop['loop'] - 1 ) % $woocommerce_loop['columns'] || 1 == $woocommerce_loop['columns'] )
	$classes[] = 'first';
if ( 0 == $woocommerce_loop['loop'] % $woocommerce_loop['columns'] )
	$classes[] = 'last';
$classes = 'product';
?>
<?php if (!$no_product_columns) : ?>
<div class="col-md-<?php echo !is_shop() && !is_product_category() && !is_product_tag() ? "3" : "4" ?> col-xs-6">
<?php endif; ?>
	<div <?php post_class( $classes ); ?>>

		<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>
		<div class="product-cover">
			<div class="product-cover-hover">
				<span>
					<a href="<?php the_permalink() ?>"><?php _e('View','zeon') ?></a>
				</span>
			</div>
			<?php
				/**
				 * woocommerce_before_shop_loop_item_title hook
				 *
				 * @hooked woocommerce_show_product_loop_sale_flash - 10
				 * @hooked woocommerce_template_loop_product_thumbnail - 10
				 */
				do_action( 'woocommerce_before_shop_loop_item_title' );
			?>
		</div>
		<div class="product-details">    
			<h1>
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</h1>
			<?php echo apply_filters( 'woocommerce_short_description', get_the_excerpt() ) ?>
			<?php
				/**
				 * woocommerce_after_shop_loop_item_title hook
				 *
				 * @hooked woocommerce_template_loop_rating - 5
				 * @hooked woocommerce_template_loop_price - 10
				 */
				do_action( 'woocommerce_after_shop_loop_item_title' );
			?>

			<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
		</div>
	</div>
<?php if (!$no_product_columns) : ?>
	</div>
<?php endif; ?>
<?php if(!$no_product_rows): ?>
	<?php if((!is_shop() && !is_product_category() && !is_product_tag()) && $woocommerce_loop['loop'] % 4 == 0) : ?>
		</div>
		<div class="row">
	<?php elseif((is_shop() || is_product_category() || is_product_tag()) && $woocommerce_loop['loop'] % 3 == 0) : ?>
		</div>
		<div class="row">
	<?php endif; ?>
<?php endif; ?>