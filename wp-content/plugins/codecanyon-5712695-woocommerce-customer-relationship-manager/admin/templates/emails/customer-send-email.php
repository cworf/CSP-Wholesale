<?php
/**
 * Email template
 *
 * @author   Actuality Extensions
 * @package  WooCommerce_Customer_Relationship_Manager
 * @since    1.0
 */

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<?php do_action( 'woocommerce_email_header', $email_heading ); ?>

	<p><strong><?php printf( get_option( 'blogname' ) ); ?></strong></p>
	<?php echo $email_message; ?>

<?php do_action( 'woocommerce_email_footer' ); ?>