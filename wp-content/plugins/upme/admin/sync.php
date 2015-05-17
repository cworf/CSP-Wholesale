<h3><?php _e('Auto Sync with WooCommerce','upme'); ?></h3>

<?php if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) { ?>

<p><?php _e('Syncing with WooCommerce will automatically add WooCommerce customer profile fields to your UPME. A quick way to have a WooCommerce account page integrated with UPME. Just click the following button and let UPME do the work for you.','upme'); ?></p>

<p><a href="<?php echo add_query_arg( array('sync' => 'woocommerce') ); ?>" class="button button-secondary"><?php _e('Sync and keep existing fields','upme'); ?></a> 
<a href="<?php echo add_query_arg( array('sync' => 'woocommerce_clean') ); ?>" class="button button-secondary"><?php _e('Sync and delete existing fields','upme'); ?></a></p>

<?php } else { ?>

<p><?php _e('Please install WooCommerce plugin first.','upme'); ?></p>

<?php } ?>