<?php
/**
 * WooCommerce Jetpack PDF Invoicing Display
 *
 * The WooCommerce Jetpack PDF Invoicing Display class.
 *
 * @class    WCJ_PDF_Invoicing_Display
 * @version  1.0.0
 * @category Class
 * @author   Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_PDF_Invoicing_Display' ) ) :

class WCJ_PDF_Invoicing_Display {

    /**
     * Constructor.
     */
    function __construct() {

		if ( 'yes' === get_option( 'wcj_pdf_invoicing_enabled' ) ) {

			// Columns on Admin's Orders page
			add_filter( 'manage_edit-shop_order_columns',           array( $this, 'add_order_column' ),              PHP_INT_MAX );
			add_action( 'manage_shop_order_posts_custom_column',    array( $this, 'render_order_columns' ),          2 );

			// Action Links on Admin's Orders page
			//add_filter( 'woocommerce_admin_order_actions',          array( $this, 'add_pdf_invoices_action_links' ), PHP_INT_MAX, 2 );
			//add_action( 'admin_head',                               array( $this, 'add_pdf_invoice_icon_css' ) );

			// Action Links on Customer's My Account page
			add_filter( 'woocommerce_my_account_my_orders_actions', array( $this, 'add_pdf_invoices_action_links' ), PHP_INT_MAX, 2 );
		}

        // Settings hooks
        add_filter( 'wcj_settings_sections',                        array( $this, 'settings_section' ) );
        add_filter( 'wcj_settings_pdf_invoicing_display',           array( $this, 'get_settings' ),                  100 );
    }

    /**
     * add_order_column.
     */
	function add_order_column( $columns ) {
		$invoice_types = wcj_get_enabled_invoice_types();
		foreach ( $invoice_types as $invoice_type ) {
			if ( 'yes' === get_option( 'wcj_invoicing_' . $invoice_type['id'] . '_admin_orders_page_column', 'yes' ) ) {
				$columns[ $invoice_type['id'] ] = $invoice_type['title'];
			}
		}
		return $columns;
	}

	/**
	 * Ouput custom columns for products
	 * @param  string $column
	 */
	public function render_order_columns( $column ) {

		$invoice_types_ids = wcj_get_enabled_invoice_types_ids();
		if ( ! in_array( $column, $invoice_types_ids ) ) {
			return;
		}
		
		$order_id = get_the_ID();
		$invoice_type_id = $column;		
		
		if ( ! wcj_is_invoice_created( $order_id, $invoice_type_id ) )
			return;	

		$the_invoice = wcj_get_invoice( $order_id, $invoice_type_id );
		//$the_number = $the_invoice->get_invoice_full_number();
		$the_number = $the_invoice->get_invoice_number();
		//$the_url = $the_invoice->get_invoice_url();
		//$the_link = $the_invoice->get_invoice_link();
		$query_args = array( 'order_id' => $order_id, 'invoice_type_id' => $invoice_type_id, 'get_invoice' => '1', );
		$the_link = '<a href="' . add_query_arg( $query_args ) . '">' . $the_number . '</a>';

		echo $the_link;
	}

    /**
     * add_pdf_invoice_icon_css.
     *
	function add_pdf_invoice_icon_css() {
		$invoice_types = wcj_get_enabled_invoice_types();
		foreach ( $invoice_types as $invoice_type ) {
			echo '<style> a.button.tips.view.' .      $invoice_type['id'] . ':after { content: "' . $invoice_type['icon'] . '" !important; } </style>';
		}
	}

    /**
     * add_pdf_invoices_action_links.
     */
    function add_pdf_invoices_action_links( $actions, $the_order ) {
	


		$invoice_types = wcj_get_enabled_invoice_types();
		foreach ( $invoice_types as $invoice_type ) {
		
			if ( ! wcj_is_invoice_created( $the_order->id, $invoice_type['id'] ) )
				continue;		

			//$admin_option_name = 'wcj_invoicing_' . $invoice_type['id'] . '_admin_orders_page';
			$my_account_option_name = 'wcj_invoicing_' . $invoice_type['id'] . '_enabled_for_customers';

			/*if ( ( 'woocommerce_admin_order_actions'          === current_filter() && 'add_action_link' === get_option( $admin_option_name, 'add_column' ) ) ||
			     ( 'woocommerce_my_account_my_orders_actions' === current_filter() && 'yes' === apply_filters( 'wcj_get_option_filter', 'no', get_option( $my_account_option_name, 'no' ) ) ) ) {*/
				 
			//if ( 'woocommerce_my_account_my_orders_actions' === current_filter() && 'yes' === apply_filters( 'wcj_get_option_filter', 'no', get_option( $my_account_option_name, 'no' ) ) ) {
			if ( 'yes' === get_option( $my_account_option_name, 'no' ) ) {

				$the_action_id = $invoice_type['id'];
				
				//$the_url = basename( $_SERVER['REQUEST_URI'] ) . '&order_id=' . $the_order->id . '&invoice_type_id=' . $invoice_type['id'] . '&get_invoice=1';				
				$query_args = array( 'order_id' => $the_order->id, 'invoice_type_id' => $invoice_type['id'], 'get_invoice' => '1', );
				$the_url = add_query_arg( $query_args );
				
				$the_name = $invoice_type['desc'];
				$the_action = 'view ' . $invoice_type['id'];

				if ( 'yes' === get_option( 'wcj_invoicing_' . $invoice_type['id'] . '_save_as_enabled', 'no' ) ) {
					$the_url .= '&save_pdf_invoice=1';
				}

				$actions[ $the_action_id ] = array(	'url' => $the_url, 'name' => $the_name, 'action' => $the_action, );
			}
		}

		return $actions;
    }

    /**
     * get_settings.
     */
    function get_settings() {

		$settings = array();
		$invoice_types = wcj_get_invoice_types();
		foreach ( $invoice_types as $invoice_type ) {

			$settings[] = array( 'title' => strtoupper( $invoice_type['desc'] ), 'type' => 'title', 'desc' => '', 'id' => 'wcj_invoicing_' . $invoice_type['id'] . '_display_options' );

			$settings = array_merge( $settings, array(
				array(
					'title'    => __( 'Admin\'s "Orders" Page', 'woocommerce-jetpack' ),
					'desc'     => __( 'Add Column', 'woocommerce-jetpack' ),
					'id'       => 'wcj_invoicing_' . $invoice_type['id'] . '_admin_orders_page_column',
					'default'  => 'yes',
					'type'     => 'checkbox',
					/*'class'    => 'chosen_select',
					'options'  => array(
						'add_column'       => __( 'Add Column', 'woocommerce-jetpack' ),
						'add_action_link'  => __( 'Add Action Link', 'woocommerce-jetpack' ),
					),*/
				),

				array(
					'title'    => __( 'Customer\'s "My Account" Page', 'woocommerce-jetpack' ),
					//'desc'     => __( 'Enable the PDF Invoices in customers account', 'woocommerce-jetpack' ),
					'desc'     => __( 'Add link', 'woocommerce-jetpack' ),
					'desc_tip'	=> apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
					'id'       => 'wcj_invoicing_' . $invoice_type['id'] . '_enabled_for_customers',
					'default'  => 'no',
					'type'     => 'checkbox',
					'custom_attributes'	=> apply_filters( 'get_wc_jetpack_plus_message', '', 'disabled' ),
				),

				array(
					'title'    => __( 'Enable "Save as"', 'woocommerce-jetpack' ),
					'desc'     => __( 'Enable', 'woocommerce-jetpack' ),
					'desc_tip'     => __( 'Enable "save as" pdf instead of view pdf in browser', 'woocommerce-jetpack' ),
					//'desc_tip'	=> apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
					'id'       => 'wcj_invoicing_' . $invoice_type['id'] . '_save_as_enabled',
					'default'  => 'no',
					'type'     => 'checkbox',
					//'custom_attributes'	=> apply_filters( 'get_wc_jetpack_plus_message', '', 'disabled' ),
				),
				
				array(
					'title'    => __( 'PDF File Name', 'woocommerce-jetpack' ),
					'desc'     => __( 'Enter file name for PDF documents. You can use shortcodes here, e.g. [wcj_' . $invoice_type['id'] . '_number]', 'woocommerce-jetpack' ),
					'id'       => 'wcj_invoicing_' . $invoice_type['id'] . '_file_name',
					'default'  => '[wcj_' . $invoice_type['id'] . '_number]',
					'type'     => 'text',
				),
			) );

			$settings[] = array( 'type'  => 'sectionend', 'id' => 'wcj_invoicing_' . $invoice_type['id'] . '_display_options' );
		}

        return $settings;
    }

    /**
     * settings_section.
     */
    function settings_section( $sections ) {
        $sections['pdf_invoicing_display'] = __( 'Misc.', 'woocommerce-jetpack' );
        return $sections;
    }
}

endif;

return new WCJ_PDF_Invoicing_Display();