<?php
/**
 * WooCommerce Jetpack Order Numbers
 *
 * The WooCommerce Jetpack Order Numbers class.
 *
 * @class		WCJ_Order_Numbers
 * @version		1.0.0
 * @category	Class
 * @author 		Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
if ( ! class_exists( 'WCJ_Order_Numbers' ) ) :
 
class WCJ_Order_Numbers {
    
    /**
     * Constructor.
     */
    public function __construct() {
 
        // Main hooks
		if ( 'yes' === get_option( 'wcj_order_numbers_enabled' ) ) {
			//add_action( 'woocommerce_new_order', 		array( $this, 'add_new_order_number' ), 								100 );
			add_action( 'wp_insert_post',				array( $this, 'add_new_order_number' ), 								100 );
			add_filter( 'woocommerce_order_number', 	array( $this, 'display_order_number' ), 								100, 	2 );
			add_filter( 'wcj_tools_tabs', 				array( $this, 'add_renumerate_orders_tool_tab' ), 						100 );
			add_action( 'wcj_tools_renumerate_orders', 	array( $this, 'create_renumerate_orders_tool' ), 						100 );
		}
		add_action( 	'wcj_tools_dashboard', 			array( $this, 'add_renumerate_orders_tool_info_to_tools_dashboard' ), 	100 );       
    
        // Settings hooks
        add_filter( 'wcj_settings_sections', array( $this, 'settings_section' ) );
        add_filter( 'wcj_settings_order_numbers', array( $this, 'get_settings' ), 100 );
        add_filter( 'wcj_features_status', array( $this, 'add_enabled_option' ), 100 );
    }
	
    /**
     * Display order number.
     */
    public function display_order_number( $order_number, $order ) {
		$order_number_meta = get_post_meta( $order->id, '_wcj_order_number', true );
		if ( '' == $order_number_meta || 'no' === get_option( 'wcj_order_number_sequential_enabled' ) ) 
			$order_number_meta = $order->id;			
		$order_timestamp = strtotime( $order->post->post_date );
		$order_number = apply_filters( 'wcj_get_option_filter', 
			'#' . $order_number_meta, 
			sprintf( '%s%s%0' . get_option( 'wcj_order_number_min_width', 0 ) . 'd%s%s', 
			get_option( 'wcj_order_number_prefix', '' ), 
			date_i18n( get_option( 'wcj_order_number_date_prefix', '' ), $order_timestamp ),				
			$order_number_meta,
			get_option( 'wcj_order_number_suffix', '' ),
			date_i18n( get_option( 'wcj_order_number_date_suffix', '' ), $order_timestamp ) ) );
		return $order_number;
    }
	
	/**
	 * add_renumerate_orders_tool_info_to_tools_dashboard.
	 */
	public function add_renumerate_orders_tool_info_to_tools_dashboard() {
		echo '<tr>';
		if ( 'yes' === get_option( 'wcj_order_numbers_enabled') )		
			$is_enabled = '<span style="color:green;font-style:italic;">' . __( 'enabled', 'woocommerce-jetpack' ) . '</span>';
		else
			$is_enabled = '<span style="color:gray;font-style:italic;">' . __( 'disabled', 'woocommerce-jetpack' ) . '</span>';
		echo '<td>' . __( 'Orders Renumerate', 'woocommerce-jetpack' ) . '</td>';
		echo '<td>' . $is_enabled . '</td>';
		echo '<td>' . __( 'Tool renumerates all orders.', 'woocommerce-jetpack' ) . '</td>';
		echo '</tr>';	
	}

	/**
	 * add_renumerate_orders_tool_tab.
	 */
	public function add_renumerate_orders_tool_tab( $tabs ) {
		$tabs[] = array(
			'id'		=> 'renumerate_orders',
			'title'		=> __( 'Renumerate orders', 'woocommerce-jetpack' ),
		);
		return $tabs;
	}	

    /**
     * Add Renumerate Orders tool to WooCommerce menu (the content).
     */
	public function create_renumerate_orders_tool() {
		$result_message = '';
		if ( isset( $_POST['renumerate_orders'] ) ) {
			$this->renumerate_orders();
			$result_message = '<div class="updated"><p><strong>' . __( 'Orders successfully renumerated!', 'woocommerce-jetpack' ) . '</strong></p></div>';
		}
		?><div>
			<h2><?php echo __( 'WooCommerce Jetpack - Renumerate Orders', 'woocommerce-jetpack' ); ?></h2>
			<p><?php echo __( 'The tool renumerates all orders. Press the button below to renumerate all existing orders starting from order counter settings in WooCommerce > Settings > Jetpack > Order Numbers.', 'woocommerce-jetpack' ); ?></p>
			<?php echo $result_message; ?>
			<form method="post" action="">
				<input class="button-primary" type="submit" name="renumerate_orders" value="Renumerate orders">
			</form>
		</div><?php
	}
	
	public function add_new_order_number( $order_id ) {
		$this->add_order_number_meta( $order_id, false );
	}

    /**
     * Add/update order_number meta to order.
     */
    public function add_order_number_meta( $order_id, $do_overwrite ) {
	
		if ( 'shop_order' !== get_post_type( $order_id ) )
			return;

		if ( true === $do_overwrite || 0 == get_post_meta( $order_id, '_wcj_order_number', true ) ) {
			$current_order_number = get_option( 'wcj_order_number_counter' );
			update_option( 'wcj_order_number_counter', ( $current_order_number + 1 ) );
			update_post_meta( $order_id, '_wcj_order_number', $current_order_number );
		}
	}

    /**
     * Renumerate orders function.
     */
	public function renumerate_orders() {

		$args = array(
			'post_type'			=> 'shop_order',
			'post_status' 		=> 'any',
			'posts_per_page' 	=> -1,
			'orderby'			=> 'date',
			'order'				=> 'ASC',
		);

		$loop = new WP_Query( $args );

		while ( $loop->have_posts() ) : $loop->the_post();

			$order_id = $loop->post->ID;
			$this->add_order_number_meta( $order_id, true );

		endwhile;
	}		
    
    /**
     * add_enabled_option.
     */
    public function add_enabled_option( $settings ) {    
        $all_settings = $this->get_settings();
        $settings[] = $all_settings[1];        
        return $settings;
    }
    
    /**
     * get_settings.
     */    
    function get_settings() {
 
        $settings = array(
 
            array( 'title' => __( 'Order Numbers', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => __( 'This section lets you enable sequential order numbering, set custom number prefix, suffix and width.', 'woocommerce-jetpack' ), 'id' => 'wcj_order_numbers_options' ),

            array(
                'title'    => __( 'Order Numbers', 'woocommerce-jetpack' ),
                'desc'     => '<strong>' . __( 'Enable Module', 'woocommerce-jetpack' ) . '</strong>',
				'desc_tip' => __( 'Sequential order numbering, custom order number prefix, suffix and number width.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_order_numbers_enabled',
                'default'  => 'no',
                'type'     => 'checkbox',
            ),
			
            array(
                'title'    => __( 'Make Order Numbers Sequential', 'woocommerce-jetpack' ),
                'desc'     => __( 'Enable', 'woocommerce-jetpack' ),
                'id'       => 'wcj_order_number_sequential_enabled',
                'default'  => 'yes',
                'type'     => 'checkbox',
            ),			

            array(
                'title'    => __( 'Next Order Number', 'woocommerce-jetpack' ),
                'desc'     => __( 'Next new order will be given this number.', 'woocommerce-jetpack' ) . ' ' . __( 'Use Renumerate Orders tool for existing orders.', 'woocommerce-jetpack' ),
				'desc_tip' => __( 'This will be ignored if sequential order numbering is disabled.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_order_number_counter',
                'default'  => 1,
                'type'     => 'number',
            ),
			
            array(
                'title'    => __( 'Order Number Custom Prefix', 'woocommerce-jetpack' ),
				'desc' 	   => apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
                'desc_tip' => __( 'Prefix before order number (optional). This will change the prefixes for all existing orders.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_order_number_prefix',
                'default'  => '#',
                'type'     => 'text',
				'custom_attributes'
						   => apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ),
            ),				

            array(
                'title'    => __( 'Order Number Date Prefix', 'woocommerce-jetpack' ),
				'desc' 	   => apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
                'desc_tip' => __( 'Date prefix before order number (optional). This will change the prefixes for all existing orders. Value is passed directly to PHP `date` function, so most of PHP date formats can be used. The only exception is using `\` symbol in date format, as this symbol will be excluded from date. Try: Y-m-d- or mdy.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_order_number_date_prefix',
                'default'  => '',
                'type'     => 'text',
				'custom_attributes'
						   => apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ),
            ),
			
            array(
                'title'    => __( 'Order Number Width', 'woocommerce-jetpack' ),
				'desc' 	   => apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
                'desc_tip' => __( 'Minimum width of number without prefix (zeros will be added to the left side). This will change the minimum width of order number for all existing orders. E.g. set to 5 to have order number displayed as 00001 instead of 1. Leave zero to disable.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_order_number_min_width',
                'default'  => 0,
                'type'     => 'number',
				'custom_attributes'
						   => apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ),
            ),
			
            array(
                'title'    => __( 'Order Number Custom Suffix', 'woocommerce-jetpack' ),
				'desc' 	   => apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
                'desc_tip' => __( 'Suffix after order number (optional). This will change the suffixes for all existing orders.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_order_number_suffix',
                'default'  => '',
                'type'     => 'text',
				'custom_attributes'
						   => apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ),
            ),				

            array(
                'title'    => __( 'Order Number Date Suffix', 'woocommerce-jetpack' ),
				'desc' 	   => apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
                'desc_tip' => __( 'Date suffix after order number (optional). This will change the suffixes for all existing orders. Value is passed directly to PHP `date` function, so most of PHP date formats can be used. The only exception is using `\` symbol in date format, as this symbol will be excluded from date. Try: Y-m-d- or mdy.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_order_number_date_suffix',
                'default'  => '',
                'type'     => 'text',
				'custom_attributes'
						   => apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ),
            ),				
			
            array( 'type'  => 'sectionend', 'id' => 'wcj_order_numbers_options' ),			
			
        );
        
        return $settings;
    }
 
    /**
     * settings_section.
     */
    function settings_section( $sections ) {    
        $sections['order_numbers'] = __( 'Order Numbers', 'woocommerce-jetpack' );        
        return $sections;
    }    
}
 
endif;
 
return new WCJ_Order_Numbers();