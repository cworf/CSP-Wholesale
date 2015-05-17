<?php
/**
 * Single Product Rating
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product;

if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' )
	return;

$count   = $product->get_rating_count();
$average = $product->get_average_rating();
$full_stars = intval($average);
if ( $count > 0 ) : ?>

	<div class="product-rate">
		<?php for($i = 1;$i <= 5; $i++) : ?>
        	<i class="icon-<?php echo $full_stars >= $i ? '423' : '421' ?>" title="<?php echo $i==1 ? "1 star" : $i . " stars" ?>"></i>
    	<?php endfor; ?>
    </div>

<?php endif; ?>