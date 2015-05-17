<?php
/**
 * Single Product tabs
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Filter tabs and allow third parties to add their own
 *
 * Each tab is an array containing title, callback and priority.
 * @see woocommerce_default_product_tabs()
 */
$tabs = apply_filters( 'woocommerce_product_tabs', array() );

if ( ! empty( $tabs ) ) : ?>
	<div class="woocommerce-tabs">
		<ul class="tabs nav nav-tabs<?php if(count($tabs) == 3) echo " three-tabs" ?>">
			<?php foreach ( $tabs as $key => $tab ) : ?>

				<li class="<?php echo $key ?>_tab" data-toggle="tab">
					<a href="#tab-<?php echo $key ?>"><?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', $tab['title'], $key ) ?></a>
				</li>

			<?php endforeach; ?>
		</ul>
		<div class="tab-content">
			<?php foreach ( $tabs as $key => $tab ) : ?>

				<div class="panel entry-content tab-pane" id="tab-<?php echo $key ?>">
					<?php call_user_func( $tab['callback'], $key, $tab ) ?>
				</div>

			<?php endforeach; ?>
			
			<?php tt_share(); ?>

		</div>
	</div>

<?php endif; ?>
</div><!-- #product-<?php the_ID(); ?> -->
</div>
</div>