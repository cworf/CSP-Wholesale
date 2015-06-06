<?php
/**
 * WooCommerce Jetpack Exchange Rates
 *
 * The WooCommerce Jetpack Exchange Rates class.
 *
 * @class    WCJ_Exchange_Rates
 * @version  1.0.0
 * @category Class
 * @author   Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_Exchange_Rates' ) ) :

class WCJ_Exchange_Rates {

    /**
     * Constructor.
     */
    public function __construct() {

		add_action( 'admin_enqueue_scripts' ,         array( $this, 'enqueue_exchange_rates_script' ) );
		add_action( 'admin_init',                     array( $this, 'register_script' ) );

		add_action( 'woocommerce_admin_field_button', array( $this, 'output_settings_button' ) );
    }

	/**
     * register_script.
     */
    public function register_script() {
		if( isset( $_GET['section'] ) && 'price_by_country' === $_GET['section'] ) {
			wp_register_script( 'wcj-exchange-rates', trailingslashit( WCJ()->plugin_url() ) . 'includes/js/exchange_rates.js', array( 'jquery' ), false, true );
		}
    }

    /**
     * enqueue_exchange_rates_script.
     */
    public function enqueue_exchange_rates_script() {	
        if( isset( $_GET['section'] ) && 'price_by_country' === $_GET['section'] ) {
			wp_enqueue_script( 'wcj-exchange-rates' );
		}
    }

    /**
     * output_settings_button.
     */
    function output_settings_button( $value ) {
	
		// Custom attribute handling
		$custom_attributes = array();
		if ( ! empty( $value['custom_attributes'] ) && is_array( $value['custom_attributes'] ) ) {
			foreach ( $value['custom_attributes'] as $attribute => $attribute_value ) {
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
			}
		}
		$tip = '';
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
				<?php echo $tip; ?>
			</th>
			<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
				<input
					name="<?php echo esc_attr( $value['id'] ); ?>"
					id="<?php echo esc_attr( $value['id'] ); ?>"
					type="<?php echo esc_attr( $value['type'] ); ?>"
					style="<?php echo esc_attr( $value['css'] ); ?>"
					value="<?php echo esc_attr( $value['value'] ); ?>"
					class="<?php echo esc_attr( $value['class'] ); ?>"
					<?php echo implode( ' ', $custom_attributes ); ?>
					/>
			</td>
		</tr>
		<?php
	}

}

endif;

return new WCJ_Exchange_Rates();
