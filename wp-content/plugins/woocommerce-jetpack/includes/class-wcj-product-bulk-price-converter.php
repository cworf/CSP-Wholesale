<?php
/**
 * WooCommerce Jetpack Bulk Price Converter
 *
 * The WooCommerce Jetpack Bulk Price Converter class.
 *
 * @class    WCJ_Bulk_Price_Converter
 * @version  1.0.0
 * @category Class
 * @author   Algoritmika Ltd.
 */
 
if ( ! defined( 'ABSPATH' ) ) exit;
 
if ( ! class_exists( 'WCJ_Bulk_Price_Converter' ) ) :
 
class WCJ_Bulk_Price_Converter {
    
    /**
     * Constructor.
     */
    public function __construct() {
 
		$the_priority = 100;
        // Main hooks
        if ( 'yes' === get_option( 'wcj_bulk_price_converter_enabled' ) ) {
			add_filter( 'wcj_tools_tabs', 				array( $this, 'add_bulk_price_converter_tool_tab' ), $the_priority );
			add_action( 'wcj_tools_bulk_price_converter', array( $this, 'create_bulk_price_converter_tool' ), $the_priority );        
        } 
		add_action( 'wcj_tools_dashboard', 			    array( $this, 'add_bulk_price_converter_tool_info_to_tools_dashboard' ), $the_priority );		
    
        // Settings hooks
        add_filter( 'wcj_settings_sections', array( $this, 'settings_section' ) );
        add_filter( 'wcj_settings_bulk_price_converter', array( $this, 'get_settings' ), 100 );
        add_filter( 'wcj_features_status', array( $this, 'add_enabled_option' ), 100 );
    }
	
	/**
	 * add_bulk_price_converter_tool_tab.
	 */
	public function add_bulk_price_converter_tool_tab( $tabs ) {
		$tabs[] = array(
			'id'		=> 'bulk_price_converter',
			'title'		=> __( 'Bulk Price Converter', 'woocommerce-jetpack' ),
		);
		return $tabs;
	}	
	
	/**
	 * change_price_by_type.
	 */	
	public function change_price_by_type( $product_id, $multiply_price_by, $price_type, $is_preview ) {
		$the_price = get_post_meta( $product_id, '_' . $price_type, true );
		if ( '' != $the_price ) {
			$precision = get_option( 'woocommerce_price_num_decimals', 2 );
			$the_modified_price = round( $the_price * $multiply_price_by, $precision );
			/*if ( isset( $_POST['make_pretty_prices'] ) )
				$the_modified_price = $this->make_pretty_price( $the_modified_price );*/
			if ( ! $is_preview )
				update_post_meta( $product_id, '_' . $price_type, $the_modified_price );
		}
		
		echo '<tr>' . 
				'<td>' . get_the_title( $product_id ) . '</td>' . 
				'<td><em>' . $price_type . '</em></td>' . 
				'<td>' . $the_price . '</td>' . 
				'<td>' . $the_modified_price . '</td>' . 
			 '</tr>';		
	}
	
	/**
	 * change_price_all_types.
	 */
	public function change_price_all_types( $product_id, $multiply_price_by, $is_preview ) {
		$this->change_price_by_type( $product_id, $multiply_price_by, 'price', $is_preview );
		$this->change_price_by_type( $product_id, $multiply_price_by, 'sale_price', $is_preview );
		$this->change_price_by_type( $product_id, $multiply_price_by, 'regular_price', $is_preview );
	}	

	/**
	 * change_product_price.
	 */
	public function change_product_price( $product_id, $multiply_price_by, $is_preview ) {
		$this->change_price_all_types( $product_id, $multiply_price_by, $is_preview );
		// Handling variable products
		$product = wc_get_product( $product_id );
		if ( $product->is_type( 'variable' ) ) {
			$variations = $product->get_available_variations();
			foreach( $variations as $variation ) {
				$this->change_price_all_types( $variation['variation_id'], $multiply_price_by, $is_preview );
			}
		}
	}		

    /**
     * change_all_products_prices
     */
	public function change_all_products_prices( $multiply_prices_by, $is_preview ) {		
		$multiply_prices_by = floatval( $multiply_prices_by );
		if ( $multiply_prices_by <= 0 )
			return;		
			
		ob_start();

		$args = array(
			'post_type'			=> 'product',
			'post_status' 		=> 'any',
			'posts_per_page' 	=> -1,
		);
		$loop = new WP_Query( $args );
		echo '<table class="widefat" style="width:50%; min-width: 300px;">';
		echo '<tr>' . 
				'<th>' . __( 'Product', 'woocommerce-jetpack' ) . '</th>' . 
				'<th>' . __( 'Price Type', 'woocommerce-jetpack' ) . '</th>' . 				
				'<th>' . __( 'Price', 'woocommerce-jetpack' ) . '</th>' . 
				'<th>' . __( 'Modified Price', 'woocommerce-jetpack' ) . '</th>' . 
			 '</tr>';			
		while ( $loop->have_posts() ) : $loop->the_post();		
			$this->change_product_price( $loop->post->ID, $multiply_prices_by, $is_preview );
		endwhile;		
		echo '</table>';
		
		return ob_get_clean();
	}

    /**
     * create_bulk_price_converter_tool
     */
	public function create_bulk_price_converter_tool() {
	
		$result_message = '';		
		$multiply_prices_by = isset( $_POST['multiply_prices_by'] ) ? $_POST['multiply_prices_by'] : 1;
		$is_preview = isset( $_POST['bulk_change_prices_preview'] ) ? true : false;
		
		if ( $multiply_prices_by <= 0 ) {
			$result_message = '<p><div class="error"><p><strong>' . __( 'Multiply value must be above zero.', 'woocommerce-jetpack' ) . '</strong></p></div></p>';
			$multiply_prices_by = 1;
		}
		else {			
			if ( isset( $_POST['bulk_change_prices'] ) || isset( $_POST['bulk_change_prices_preview'] ) ) {			
				$result_changing_prices = $this->change_all_products_prices( $multiply_prices_by, $is_preview );
				if ( ! $is_preview ) {				
					$result_message = '<p><div class="updated"><p><strong>' . __( 'Prices changed successfully!', 'woocommerce-jetpack' ) . '</strong></p></div></p>';
					$multiply_prices_by = 1;
				}
			}
		}
		?>
		<div>
			<h2><?php echo __( 'WooCommerce Jetpack - Bulk Price Converter', 'woocommerce-jetpack' ); ?></h2>
			<p><?php echo __( 'Bulk Price Converter Tool.', 'woocommerce-jetpack' ); ?></p>
			<?php echo $result_message; ?>
			<p><form method="post" action="">
				<?php echo __( 'Multiply all product prices by', 'woocommerce-jetpack' ); ?> <input class="" type="text" name="multiply_prices_by" id="multiply_prices_by" value="<?php echo $multiply_prices_by; ?>">
				<input class="button-primary" type="submit" name="bulk_change_prices_preview" id="bulk_change_prices_preview" value="Preview Prices">
				<input class="button-primary" type="submit" name="bulk_change_prices" id="bulk_change_prices" value="Change Prices">
				<?php /*<input type="checkbox" name="make_pretty_prices" id="make_pretty_prices" value="">Make Pretty Prices*/ ?>
			</form></p>
			<?php if ( $is_preview ) echo $result_changing_prices; ?>
		</div>
		<?php
	}

	
	/**
	 * make_pretty_price.
	 *
	function make_pretty_price( $price ) {
	
		if ( 0 == $price )
			return $price;

		$the_price = $price;
		$the_multiplied_price = $price;
		
		if ( $the_price < 20 ) {
		
			
			$mod_10_cents = ( $the_multiplied_price * 10 - floor( $the_multiplied_price * 10 ) ) / 10;
			// E.g. 14.44 -> 14.39
			if ( $mod_10_cents < 0.05 )
				$the_multiplied_price = $the_multiplied_price - ( $mod_10_cents + 0.01 );			
			// E.g. 14.45 -> 14.49
			else if ( $mod_10_cents >= 0.05 )
				$the_multiplied_price = $the_multiplied_price + ( 0.1 - ( $mod_10_cents + 0.01 ) );	
					
			$mod_100_cents = ( $the_multiplied_price - floor( $the_multiplied_price ) );
			// E.g. 14.09 -> 13.99
			if ( $mod_100_cents < 0.10 )
				$the_multiplied_price = $the_multiplied_price - ( $mod_100_cents + 0.01 );
		}

	
		if ( $the_price < 99 && $the_price >= 20 )
			// E.g. 45.36 -> 44.99
			// E.g. 45.60 -> 45.99
			$the_multiplied_price = round( $the_multiplied_price ) - 0.01;
				
		if ( $the_price >= 100 ) {
			
			$the_multiplied_price = round( $the_multiplied_price );
		
			$mod_10 = $the_multiplied_price % 10;
			if ( $mod_10 < 5 )
			// E.g. 114.00 -> 109.00
				$the_multiplied_price = $the_multiplied_price - ( $mod_10 + 1 );				
			else if ( $mod_10 >= 5 )
			// E.g. 115.00 -> 119.00
				$the_multiplied_price = $the_multiplied_price + ( 10 - ( $mod_10 + 1 ) );		

			if ( $the_price >= 200 ) {
				$mod_100 = $the_multiplied_price % 100;
				if ( $mod_100 < 10 )
			// E.g. 209.00 -> 199.00
					$the_multiplied_price = $the_multiplied_price - ( $mod_100 + 1 );
			}
		}	
	
		return $the_multiplied_price;
	}		
	
	/**
	 * add_bulk_price_converter_tool_info_to_tools_dashboard.
	 */
	public function add_bulk_price_converter_tool_info_to_tools_dashboard() {
		echo '<tr>';
		if ( 'yes' === get_option( 'wcj_bulk_price_converter_enabled') )		
			$is_enabled = '<span style="color:green;font-style:italic;">' . __( 'enabled', 'woocommerce-jetpack' ) . '</span>';
		else
			$is_enabled = '<span style="color:gray;font-style:italic;">' . __( 'disabled', 'woocommerce-jetpack' ) . '</span>';
		echo '<td>' . __( 'Bulk Price Converter', 'woocommerce-jetpack' ) . '</td>';
		echo '<td>' . $is_enabled . '</td>';
		echo '<td>' . __( 'Bulk Price Converter Tool.', 'woocommerce-jetpack' ) . '</td>';
		echo '</tr>';	
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
 
            array( 
				'title' => __( 'Bulk Price Converter Options', 'woocommerce-jetpack' ), 
				'type' => 'title', 
				'desc' => __( 'When enabled, the tool is accessible through WooCommerce > Jetpack Tools > Bulk Price Converter.', 'woocommerce-jetpack' ), 
				'id' => 'wcj_bulk_price_converter_options' 
			),
            
            array(
                'title'    => __( 'Bulk Price Converter', 'woocommerce-jetpack' ),
                 'desc'     => '<strong>' . __( 'Enable Module', 'woocommerce-jetpack' ) . '</strong>',
                'desc_tip' => __( 'Multiply all products prices by set value.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_bulk_price_converter_enabled',
                'default'  => 'yes',
                'type'     => 'checkbox',
            ),
        
            array( 'type'  => 'sectionend', 'id' => 'wcj_bulk_price_converter_options' ),
        );
        
        return $settings;
    }
 
    /**
     * settings_section.
     */
    function settings_section( $sections ) {    
        $sections['bulk_price_converter'] = __( 'Bulk Price Converter', 'woocommerce-jetpack' );        
        return $sections;
    }    
}
 
endif;
 
return new WCJ_Bulk_Price_Converter();