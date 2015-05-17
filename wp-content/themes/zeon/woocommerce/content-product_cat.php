<?php
/**
 * The template for displaying product category thumbnails within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product_cat.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce_loop,$no_product_columns,$no_product_rows;

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) )
	$woocommerce_loop['loop'] = 0;

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) )
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );

// Increase loop count
$woocommerce_loop['loop']++;
$classes = 'product';
?>
<?php if (!$no_product_columns) : ?>
<div class="col-md-<?php echo !is_shop() && !is_product_category() && !is_product_tag() ? "3" : "4" ?> col-xs-6 product-category product<?php
    if ( ( $woocommerce_loop['loop'] - 1 ) % $woocommerce_loop['columns'] == 0 || $woocommerce_loop['columns'] == 1 )
        echo ' first';
	if ( $woocommerce_loop['loop'] % $woocommerce_loop['columns'] == 0 )
		echo ' last';
	?>">
<?php endif; ?>
	<?php do_action( 'woocommerce_before_subcategory', $category ); ?>
	<div <?php post_class( $classes ); ?>>
		<div class="product-cover">
			<div class="product-cover-hover">
				<span>
					<a href="<?php echo get_term_link( $category->slug, 'product_cat' ); ?>"><?php _e('View','zeon') ?></a>
				</span>
			</div>
		

			<?php
				/**
				 * woocommerce_before_subcategory_title hook
				 *
				 * @hooked woocommerce_subcategory_thumbnail - 10
				 */
				do_action( 'woocommerce_before_subcategory_title', $category );
			?>
		</div>
		<div class="product-details">    
			<h1>
				<a href="<?php echo get_term_link( $category->slug, 'product_cat' ); ?>"><?php
					echo $category->name;

					if ( $category->count > 0 )
						echo apply_filters( 'woocommerce_subcategory_count_html', ' <span>(' . $category->count . ')</span>', $category );
				?></a>
			</h1>
			
		</div>

		<?php
			/**
			 * woocommerce_after_subcategory_title hook
			 */
			do_action( 'woocommerce_after_subcategory_title', $category );
		?>
		
		<?php do_action( 'woocommerce_after_subcategory', $category ); ?>
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