<?php
/**
 * WooCommerce Jetpack Admin Tools
 *
 * The WooCommerce Jetpack Admin Tools class.
 *
 * @class    WCJ_Admin_Tools
 * @version  1.0.0
 * @category Class
 * @author   Algoritmika Ltd.
 */
 
if ( ! defined( 'ABSPATH' ) ) exit;
 
if ( ! class_exists( 'WCJ_Admin_Tools' ) ) :
 
class WCJ_Admin_Tools {
    
    /**
     * Constructor.
     */
    public function __construct() {
 
        // Main hooks
        if ( 'yes' === get_option( 'wcj_admin_tools_enabled' ) ) {				
			add_filter( 'wcj_tools_tabs',             array( $this, 'add_tool_tab' ), 100 );
			add_action( 'wcj_tools_' . 'admin_tools', array( $this, 'create_tool' ), 100 );					        
        }        
    
        // Settings hooks
        add_filter( 'wcj_settings_sections',    array( $this, 'settings_section' ) );
        add_filter( 'wcj_settings_admin_tools', array( $this, 'get_settings' ), 100 );
        add_filter( 'wcj_features_status',      array( $this, 'add_enabled_option' ), 100 );
		add_action( 'wcj_tools_dashboard',      array( $this, 'add_tool_info_to_tools_dashboard' ), 100 );				
		
		
		

    }	
	

	


	/**
	 * add_tool_info_to_tools_dashboard.
	 */
	public function add_tool_info_to_tools_dashboard() {
		echo '<tr>';
		if ( 'yes' === get_option( 'wcj_admin_tools_enabled') )
			$is_enabled = '<span style="color:green;font-style:italic;">' . __( 'enabled', 'woocommerce-jetpack' ) . '</span>';
		else
			$is_enabled = '<span style="color:gray;font-style:italic;">' . __( 'disabled', 'woocommerce-jetpack' ) . '</span>';
		echo '<td>' . __( 'Admin Tools', 'woocommerce-jetpack' ) . '</td>';
		echo '<td>' . $is_enabled . '</td>';
		echo '<td>' . __( 'Log.', 'woocommerce-jetpack' ) . '</td>';
		echo '</tr>';
	}

	/**
	 * add_tool_tab.
	 */
	public function add_tool_tab( $tabs ) {
		$tabs[] = array(
			'id'		=> 'admin_tools',
			'title'		=> __( 'Admin Tools', 'woocommerce-jetpack' ),
		);
		return $tabs;
	}

    /**
     * create_tool.
     */
	public function create_tool() {
	
		$the_notice = '';		
		if ( isset( $_GET['wcj_delete_log'] ) && is_super_admin() ) {
			update_option( 'wcj_log', '' );
			$the_notice .= __( 'Log deleted successfully.', 'woocommerce-jetpack' );
		}	
		
		$the_tools = '';
		$the_tools .= '<a href="' . add_query_arg( 'wcj_delete_log', '1' ) . '">' . __( 'Delete Log', 'woocommerce-jetpack' ) . '</a>';
	
		$the_log = '';
		//if ( isset( $_GET['wcj_view_log'] ) ) {
			$the_log .= '<pre>' . get_option( 'wcj_log', '' ) . '</pre>';
		//}
		
		echo '<p>' . $the_tools . '</p>';
		
		echo '<p>' . $the_notice . '</p>';		
		
		echo '<p>' . $the_log . '</p>';
		
		/**
		// Show invoices
		$data = array();
		$data[] = array( 
			'Invoice Nr.', 
			'Invoice Date',
			'Order ID', 	
			'Customer Country',				
			'Tax %',			
			'Order Total Tax Excl.',
			'Order Taxes',
			'Order Total',			
		);
		
		$total_sum = 0;
		$total_sum_excl_tax = 0;
		$total_tax = 0;
		
		$args = array(
			'post_type'			=> 'shop_order',
			'post_status' 		=> 'any',
			'posts_per_page' 	=> -1,
			'orderby'			=> 'date',
			'order'				=> 'ASC',
			
			'year'				=> 2015,
			'monthnum'		    => 1,
		);
		$loop = new WP_Query( $args );
		while ( $loop->have_posts() ) : $loop->the_post();
			$order_id = $loop->post->ID;
			$invoice_type_id = 'invoice';
		
			
			if ( wcj_is_invoice_created( $order_id, $invoice_type_id ) ) {
			
				$the_order = wc_get_order( $order_id );
			
				$user_meta = get_user_meta( $the_order->get_user_id() );
				$billing_country  = isset( $user_meta['billing_country'][0] )  ? $user_meta['billing_country'][0]  : '';
				$shipping_country = isset( $user_meta['shipping_country'][0] ) ? $user_meta['shipping_country'][0] : '';
				$customer_country = ( '' == $billing_country ) ? $shipping_country : $billing_country;				
				
				$order_total = $the_order->get_total();
				
				$order_tax = apply_filters( 'wcj_order_total_tax', $the_order->get_total_tax(), $the_order );				
				//$order_tax_percent = ( isset( $taxes_by_countries_eu[ $customer_country ] ) ) ? $taxes_by_countries_eu[ $customer_country ] : 0;
				//$order_tax_percent /= 100;
				//$order_tax = $order_total * $order_tax_percent;
				$order_total_exlc_tax = $order_total - $order_tax;
				$order_tax_percent = ( 0 == $order_total ) ? 0 : $order_tax / $order_total_exlc_tax;	
				
				$total_sum += $order_total;
				$total_sum_excl_tax += $order_total_exlc_tax;
				$total_tax += $order_tax;
				
				//$order_tax_html = ( 0 == $order_tax ) ? '' : sprintf( '$ %.2f', $order_tax );
				$order_tax_html = sprintf( '$ %.2f', $order_tax );
			
				$data[] = array( 					
					wcj_get_invoice_number( $order_id, $invoice_type_id ), 
					wcj_get_invoice_date( $order_id, $invoice_type_id, 0, get_option( 'date_format' ) ),
					$order_id, 
					$customer_country,
					sprintf( '%.0f %%', $order_tax_percent * 100 ),	
					sprintf( '$ %.2f', $order_total_exlc_tax ),					
					$order_tax_html,															
					sprintf( '$ %.2f', $order_total ),					
				);
			}
		endwhile;
		echo '<h3>' . 'Total Sum Excl. Tax: ' . sprintf( '$ %.2f', $total_sum_excl_tax ) . '</h3>';
		echo '<h3>' . 'Total Sum: ' . sprintf( '$ %.2f', $total_sum ) . '</h3>';
		echo '<h3>' . 'Total Tax: ' . sprintf( '$ %.2f', $total_tax ) . '</h3>';
		echo wcj_get_table_html( $data, array( 'table_class' => 'widefat', ) );
		/**/
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
 
            array( 'title' => __( 'Admin Tools Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => '', 'id' => 'wcj_admin_tools_options' ),
            
            array(
                'title'    => __( 'Admin Tools', 'woocommerce-jetpack' ),
                'desc'     => '<strong>' . __( 'Enable Module', 'woocommerce-jetpack' ) . '</strong>',
                'desc_tip' => __( 'Debug and log tools.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_admin_tools_enabled',
                'default'  => 'no',
                'type'     => 'checkbox',
            ),
			
            array(
                'title'    => __( 'Logging', 'woocommerce-jetpack' ),
                'desc'     => __( 'Enable', 'woocommerce-jetpack' ),
                'id'       => 'wcj_logging_enabled',
                'default'  => 'no',
                'type'     => 'checkbox',
            ), 

			array(
                'title'    => __( 'Debug', 'woocommerce-jetpack' ),
                'desc'     => __( 'Enable', 'woocommerce-jetpack' ),
                'id'       => 'wcj_debuging_enabled',
                'default'  => 'no',
                'type'     => 'checkbox',
            ),		

			/*array(
                'title'    => __( 'Custom Shortcode', 'woocommerce-jetpack' ),
                'id'       => 'wcj_custom_shortcode_1',
                'default'  => '',
                'type'     => 'textarea',
            ),*/					
        
            array( 'type'  => 'sectionend', 'id' => 'wcj_admin_tools_options' ),
        );
        
        return $settings;
    }
 
    /**
     * settings_section.
     */
    function settings_section( $sections ) {    
        $sections['admin_tools'] = __( 'Admin Tools', 'woocommerce-jetpack' );        
        return $sections;
    }    
}
 
endif;
 
return new WCJ_Admin_Tools();
