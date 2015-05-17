<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div id="message" class="updated displayproduct-message dp-connect">
	<div class="squeezer">
		<h4><?php _e( '<strong>Welcome to Display Product for WooCommerce</strong> &#8211; We\'re glad you like it :). Warning! If you not sure please click "Skip setup" button.', DP_TEXTDOMAN ); ?></h4>
		<p class="submit"><a href="<?php echo add_query_arg('install_dp_pages', 'true', admin_url('admin.php?page=display-product-page') ); ?>" class="button-primary"><?php _e( 'Install Display Product Pages', DP_TEXTDOMAN ); ?></a> <a class="skip button-primary" href="<?php echo add_query_arg('skip_install_dp_pages', 'true', admin_url('admin.php?page=display-product-page') ); ?>"><?php _e( 'Skip setup', DP_TEXTDOMAN ); ?></a></p>
	</div>
</div>