<?php

/**
 *   based on code from the following:  (example is a tabbed settings page)
 *  http://wp.tutsplus.com/series/the-complete-guide-to-the-wordpress-settings-api/   
 *    (code at    https://github.com/tommcfarlin/WordPress-Settings-Sandbox) 
 *  http://www.chipbennett.net/2011/02/17/incorporating-the-settings-api-in-wordpress-themes/?all=1 
 *  http://www.presscoders.com/2010/05/wordpress-settings-api-explained/  
 */
class VTMIN_Setup_Plugin_Options { 
	
	public function __construct(){ 
  
    add_action( 'admin_init', array( &$this, 'vtmin_initialize_options' ) );
    add_action( 'admin_menu', array( &$this, 'vtmin_add_admin_menu_setup_items' ) );
    
  } 


function vtmin_add_admin_menu_setup_items() {
 // add items to the minimum purchase custom post type menu structure
	add_submenu_page(
		'edit.php?post_type=vtmin-rule',	// The ID of the top-level menu page to which this submenu item belongs
		__( 'Rules Options Settings', 'vtmin' ), // The value used to populate the browser's title bar when the menu page is active                           
		__( 'Rules Options Settings', 'vtmin' ),					// The label of this submenu item displayed in the menu
		'administrator',					// What roles are able to access this submenu item
		'vtmin_setup_options_page',	// The slug used to represent this submenu item
		array( &$this, 'vtmin_setup_options_cntl' ) 				// The callback function used to render the options for this submenu item
	);
  
 if(!defined('VTMIN_PRO_DIRNAME')) {  //update to pro version...
       add_submenu_page(
    		'edit.php?post_type=vtmin-rule',	// The ID of the top-level menu page to which this submenu item belongs
    		__( 'Upgrade to Minimum Purchase Pro', 'vtmin' ), // The value used to populate the browser's title bar when the menu page is active                           
    		__( 'Upgrade to Pro', 'vtmin' ),					// The label of this submenu item displayed in the menu
    		'administrator',					// What roles are able to access this submenu item
    		'vtmin_pro_upgrade',	// The slug used to represent this submenu item
    		array( &$this, 'vtmin_pro_upgrade_cntl' ) 				// The callback function used to render the options for this submenu item
    	);
  } 

  //v1.09.1 begin
  //Add a DUPLICATE custom tax URL to be in the main Pricing Deals menu as well as in the PRODUCT menu
  //post_type=product => PARENT plugin post_type
    add_submenu_page(
		'edit.php?post_type=vtmin-rule',	// The ID of the top-level menu page to which this submenu item belongs
		__( 'Minimum Purchase Categories', 'vtmin' ), // The value used to populate the browser's title bar when the menu page is active                           
		__( 'Minimum Purchase Categories', 'vtmin' ),					// The label of this submenu item displayed in the menu
		'administrator',					// What roles are able to access this submenu item
		'edit-tags.php?taxonomy=vtmin_rule_category&post_type=product',	// The slug used to represent this submenu item
    //                                          PARENT PLUGIN POST TYPE      
		''  				// NO CALLBACK FUNCTION REQUIRED
	);
  //v1.09.1 end  
} 

function vtmin_pro_upgrade_cntl() {

    //PRO UPGRADE PAGE
 ?>
  <style type="text/css">
      #upgrade-div {
                float: left;
                margin:40px 0 0 100px;
               /* width: 2.5%;     */
                border: 1px solid #CCCCCC;
                border-radius: 5px 5px 5px 5px;
                padding: 15px;
                font-size:14px;
                width:500px;
            }
      #upgrade-div h3, #upgrade-div h4 {margin-left:20px;}
      #upgrade-div ul {list-style-type: square;margin-left:50px;}
      #upgrade-div ul li {font-size:16px;}
      #upgrade-div a {font-size:16px; margin-left:23%;font-weight: bold;} 
  </style>
   
	<div class="wrap">
		<div id="icon-themes" class="icon32"></div>
    
		<h2><?php esc_attr_e('Upgrade to Minimum Purchase Pro', 'vtmin'); ?></h2>
    
    <div id="upgrade-div">
        <h3><?php _e('Minimum Purchase Pro offers considerable versatility in creating minimum purchase rules.', 'vtmin') ?></h3>
        <h4><?php _e('In Minimum Purchase Pro, you can choose to apply the rule to:', 'vtmin') ?></h4>
        <ul>
          <li><?php _e('the entire contents of the cart.', 'vtmin') ?></li>
          <li><?php _e('an individual product.', 'vtmin') ?></li>
          <li><?php _e('the variations for an individual product.', 'vtmin') ?></li>
          <li><?php _e('those products in a particular Product category or group of categories.', 'vtmin') ?></li>
          <li><?php _e('those products in a particular Minumum Purchase category or group of categories. (particularly useful if you need to define a group outside of existing Product Categories)', 'vtmin') ?></li>
          <li><?php _e('Membership Status, inclusive or exclusive of category participation.', 'vtmin') ?></li>
          <li><?php _e('<em>Set cumulative lifetime limits on rule purchases by customer.</em>', 'vtmin') ?></li>
        </ul>
        <a  href=" <?php echo VTMIN_PURCHASE_PRO_VERSION_BY_PARENT ; ?> "  title="Access Plugin Documentation"> Upgrade to Minimum Purchase Pro</a>                  
    </div>  
  </div>
 
 <?php
}

/**
 * Renders a simple page to display for the menu item added above.
 */
function vtmin_setup_options_cntl() {
  //add help tab to this screen...
  //$vtmin_backbone->vtmin_add_help_tab ();
    $content = '<br><a  href="' . VTMIN_DOCUMENTATION_PATH . '"  title="Access Plugin Documentation">Access Plugin Documentation</a>';
    $screen = get_current_screen();
    $screen->add_help_tab( array( 
       'id' => 'vtmin-help-options',            //unique id for the tab
       'title' => 'Minimum Purchase Options Help',      //unique visible title for the tab
       'content' => $content  //actual help text
      ) );

    //OPTIONS PAGE
?>
  <style type="text/css">
      .form-table th {
          width: 350px;
      }
      .form-table td {
          padding: 8px 30px;
      }
      #help-all {font-size: 12px; text-decoration:none; border: 1px solid #DFDFDF; padding:3px;}
      #help-all span {font-style:normal; text-decoration:underline; font-weight:normal;}
      .help-anchor {margin-left:30px;}
      .help-text {display:none; font-style:italic; }
       h3 {margin-top:40px;}
       h4 {font-style:italic;}
      .form-table, h4 {margin-left:30px;font-size:14px;}
      .form-table td p {width: 95%;}
      #nuke-rules-button, #nuke-cats-button, #nuke-hist-button, #repair-button {color:red; margin-left:30px}
      #nuke-rules-button:hover, #nuke-cats-button:hover, #nuke-hist-button:hover, #repair-button:hover {cursor:hand; cursor:pointer; font-weight:bold;}
      
      #system-info-title {float:left; margin-top:70px;}
      .system-info-subtitle {clear:left;float:left;}
      .system-info {float:left;margin-bottom:15px; margin-left:30px;}
      .system-info-line {width:95%; float:left; margin-bottom:10px;}
      .system-info-label {width:40%; float:left; font-style:italic;}
      .system-info-data  {width:60%; float:left; font-weight:bold;}
      #custom_error_msg_css_at_checkout {width:500px;height:100px;}
  </style>
   
  <script type="text/javascript" language="JavaScript"> 
      jQuery(document).ready(function($) {
          $("#help-all").click(function(){
              $(".help-text").toggle("slow");                         
          });
          $("#help1").click(function(){
              $("#help1-text").toggle("slow");                           
          });
          $("#help2").click(function(){
              $("#help2-text").toggle("slow");                           
          });
          $("#help3").click(function(){
              $("#help3-text").toggle("slow");                           
          });  
          $("#help4").click(function(){
              $("#help4-text").toggle("slow");                           
          });
          $("#help5").click(function(){
              $("#help5-text").toggle("slow");                           
          });
          $("#help6").click(function(){
              $("#help6-text").toggle("slow");                           
          }); 
          $("#help7").click(function(){
              $("#help7-text").toggle("slow");                           
          }); 
          $("#help8").click(function(){
              $("#help8-text").toggle("slow");                           
          }); 
          $("#help9").click(function(){
              $("#help9-text").toggle("slow");                           
          }); 
          $("#help10").click(function(){
              $("#help10-text").toggle("slow");                           
          });
          $("#help11").click(function(){
              $("#help11-text").toggle("slow");                           
          });
          $("#help12").click(function(){
              $("#help12-text").toggle("slow");                           
          });
          $("#help13").click(function(){
              $("#help13-text").toggle("slow");                           
          });
          $("#help14").click(function(){
              $("#help14-text").toggle("slow");                           
          });
          $("#help15").click(function(){
              $("#help15-text").toggle("slow");                           
          });
          $("#help16").click(function(){
              $("#help16-text").toggle("slow");                           
          });
          $("#help17").click(function(){
              $("#help17-text").toggle("slow");                           
          });
          $("#help18").click(function(){
              $("#help18-text").toggle("slow");                           
          });
          $("#help19").click(function(){
              $("#help19-text").toggle("slow");                           
          });
          $("#help20").click(function(){
              $("#help20-text").toggle("slow");                           
          });
                
      });  
  
  </script>
  
  <?php
  if(!defined('VTMIN_PRO_DIRNAME')) {  
        // **********************************************
      // also disable and grey out options on free version
      // **********************************************
        ?>
        <style type="text/css">
             #show_prodcat,
             #show_rulecat
             {color:#aaa;}  /*grey out unavailable choices*/
        </style>
        <script type="text/javascript">
            jQuery.noConflict();
            jQuery(document).ready(function($) {                                                        
              // To disable 
              //  $('.someElement').attr('disabled', 'disabled');  
              $('#show_prodcat').attr('disabled', 'disabled');
              $('#show_rulecat').attr('disabled', 'disabled');

            }); //end ready function 
        </script>
  <?php } ?>
  
	<div class="wrap">
		<div id="icon-themes" class="icon32"></div>
    
		<h2>
      <?php 
        if(defined('VTMIN_PRO_DIRNAME')) { 
          esc_attr_e('Minimum Purchase Pro Options', 'vtmin'); 
        } else {
          esc_attr_e('Minimum Purchase Options', 'vtmin'); 
        }    
      ?>    
    </h2>
    
		<?php settings_errors(); ?>
    
    <?php 
    /*if ( isset( $_GET['settings-updated'] ) ) {
         echo "<div class='updated'><p>Theme settings updated successfully.</p></div>";
    } */
    ?>
		
		<form method="post" action="options.php">
			<?php
          //WP functions to execute the registered settings!
					settings_fields( 'vtmin_setup_options_group' );     //activates the field settings setup below
					do_settings_sections( 'vtmin_setup_options_page' );   //activates the section settings setup below 
				
				submit_button();        			
			?>
      
      <input name="vtmin_setup_options[options-reset]"      type="submit" class="button-secondary"  value="<?php esc_attr_e('Reset to Defaults', 'vtmin'); ?>" />
      
      <p id="system-buttons">
        <h3><?php esc_attr_e('Minimum Purchase Rules Repair and Delete Buttons', 'vtmin'); ?></h3> 
        <h4><?php esc_attr_e('Repair reknits the Rules Custom Post Type with the Minimum Purchase rules option array, if out of sync.', 'vtmin'); ?></h4>        
        <input id="repair-button"       name="vtmin_setup_options[rules-repair]"  type="submit" class="button-fourth"     value="<?php esc_attr_e('Repair Rules Structures', 'vtmin'); ?>" /> 
        <h4><?php esc_attr_e('Nuke Rules deletes all Minimum Purchase Rules.', 'vtmin'); ?></h4>
        <input id="nuke-rules-button"   name="vtmin_setup_options[rules-nuke]"     type="submit" class="button-third"      value="<?php esc_attr_e('Nuke all Rules', 'vtmin'); ?>" />
        <h4><?php esc_attr_e('Nuke Rule Cats deletes all Minimum Purchase Rule Categories', 'vtmin'); ?></h4>
        <input id="nuke-cats-button"    name="vtmin_setup_options[cats-nuke]"      type="submit" class="button-fifth"      value="<?php esc_attr_e('Nuke all Rule Cats', 'vtmin'); ?>" />
      </p>       
		</form>
    
    
    <?php 
    global $vtmin_setup_options, $wp_version;
    $vtmin_setup_options = get_option( 'vtmin_setup_options' );	  
    $vtmin_functions = new VTMIN_Functions;
    $your_system_info = $vtmin_functions->vtmin_getSystemMemInfo();
    ?>
    
    <h3 id="system-info-title">Plugin Info</h3>
    
    <h4 class="system-info-subtitle">System Info</h4>
    <span class="system-info">
       <span class="system-info-line"><span class="system-info-label">FREE_VERSION: </span> <span class="system-info-data"><?php echo VTMIN_VERSION;  ?></span> </span>
       <span class="system-info-line"><span class="system-info-label">FREE_LAST_UPDATE_DATE: </span> <span class="system-info-data"><?php echo VTMIN_LAST_UPDATE_DATE;  ?></span></span>
       <span class="system-info-line"><span class="system-info-label">FREE_DIRNAME: </span> <span class="system-info-data"><?php echo VTMIN_DIRNAME;  ?></span></span>
       <span class="system-info-line"><span class="system-info-label">URL: </span> <span class="system-info-data"><?php echo VTMIN_URL;  ?></span></span>
       <span class="system-info-line"><span class="system-info-label">EARLIEST_ALLOWED_WP_VERSION: </span> <span class="system-info-data"><?php echo VTMIN_EARLIEST_ALLOWED_WP_VERSION;  ?></span></span>
       <span class="system-info-line"><span class="system-info-label">WP VERSION: </span> <span class="system-info-data"><?php echo $wp_version; ?></span> </span>
       <span class="system-info-line"><span class="system-info-label">EARLIEST_ALLOWED_PHP_VERSION: </span> <span class="system-info-data"><?php echo VTMIN_EARLIEST_ALLOWED_PHP_VERSION ;?></span> </span>
       <span class="system-info-line"><span class="system-info-label">FREE_PLUGIN_SLUG: </span> <span class="system-info-data"><?php echo VTMIN_PLUGIN_SLUG;  ?></span></span>
     </span> 
    
    <h4 class="system-info-subtitle">Parent Plugin Info</h4>
    <span class="system-info">
       <span class="system-info-line"><span class="system-info-label">PARENT_PLUGIN_NAME: </span> <span class="system-info-data"><?php echo VTMIN_PARENT_PLUGIN_NAME;  ?></span> </span>
       <span class="system-info-line"><span class="system-info-label">EARLIEST_ALLOWED_PARENT_VERSION: </span> <span class="system-info-data"><?php echo VTMIN_EARLIEST_ALLOWED_PARENT_VERSION;  ?></span></span>
       
       <?php if(defined('WPSC_VERSION')        && (VTMIN_PARENT_PLUGIN_NAME == 'WP E-Commerce') ) { ?>
       <span class="system-info-line"><span class="system-info-label">PARENT_VERSION (WPSC): </span> <span class="system-info-data"><?php echo WPSC_VERSION;  ?></span></span>
       <?php } ?>
       
       <?php if(defined('WOOCOMMERCE_VERSION') && (VTMIN_PARENT_PLUGIN_NAME == 'WooCommerce')) { ?>
       <span class="system-info-line"><span class="system-info-label">PARENT_VERSION (WOOCOMMERCE): </span> <span class="system-info-data"><?php echo WOOCOMMERCE_VERSION;  ?></span></span>
       <?php } ?>
       
       <?php if(defined('JIGOSHOP_VERSION') && (VTMIN_PARENT_PLUGIN_NAME == 'JigoShop')) {  ?>
       <span class="system-info-line"><span class="system-info-label">PARENT_VERSION (JIGOSHOP): </span> <span class="system-info-data"><?php echo JIGOSHOP_VERSION;  ?></span></span>
       <?php } ?>
       
       <span class="system-info-line"><span class="system-info-label">TESTED_UP_TO_PARENT_VERSION: </span> <span class="system-info-data"><?php echo VTMIN_TESTED_UP_TO_PARENT_VERSION;  ?></span></span>
       <span class="system-info-line"><span class="system-info-label">CHECKOUT_PRODUCTS_SELECTOR_BY_PARENT: </span> <span class="system-info-data"><?php echo VTMIN_CHECKOUT_PRODUCTS_SELECTOR_BY_PARENT;  ?></span></span>
       <span class="system-info-line"><span class="system-info-label">CHECKOUT_ADDRESS_SELECTOR_BY_PARENT: </span> <span class="system-info-data"><?php echo VTMIN_CHECKOUT_ADDRESS_SELECTOR_BY_PARENT;  ?></span></span>
        
     </span> 

     <?php   if (defined('VTMIN_PRO_DIRNAME')) {  ?> 
      <h4 class="system-info-subtitle">Pro Info</h4>
      <span class="system-info">      
       <span class="system-info-line"><span class="system-info-label">PRO_PLUGIN_NAME: </span> <span class="system-info-data"><?php echo VTMIN_PRO_PLUGIN_NAME; ?></span> </span>
       <span class="system-info-line"><span class="system-info-label">PRO_FREE_PLUGIN_NAME: </span> <span class="system-info-data"><?php echo VTMIN_PRO_FREE_PLUGIN_NAME; ?></span> </span>
       <span class="system-info-line"><span class="system-info-label">PRO_VERSION: </span> <span class="system-info-data"><?php echo VTMIN_PRO_VERSION; ?></span> </span>
       <span class="system-info-line"><span class="system-info-label">PRO_LAST_UPDATE_DATE: </span> <span class="system-info-data"><?php echo VTMIN_PRO_LAST_UPDATE_DATE;  ?></span></span>
       <span class="system-info-line"><span class="system-info-label">PRO_DIRNAME: </span> <span class="system-info-data"><?php echo VTMIN_PRO_DIRNAME;  ?></span></span>
       <span class="system-info-line"><span class="system-info-label">PRO_MINIMUM_REQUIRED_FREE_VERSION: </span> <span class="system-info-data"><?php echo VTMIN_PRO_MINIMUM_REQUIRED_FREE_VERSION;  ?></span></span>
       <span class="system-info-line"><span class="system-info-label">PRO_BASE_NAME: </span> <span class="system-info-data"><?php echo VTMIN_PRO_BASE_NAME; ?></span> </span>
       <span class="system-info-line"><span class="system-info-label">PRO_PLUGIN_SLUG: </span> <span class="system-info-data"><?php echo VTMIN_PLUGIN_SLUG; ?></span> </span>
       <span class="system-info-line"><span class="system-info-label">PRO_REMOTE_VERSION_FILE: </span> <span class="system-info-data"><?php echo VTMIN_PRO_REMOTE_VERSION_FILE; ?></span> </span>
      </span> 
     <?php   }  ?>   

        
     <?php   if ( $vtmin_setup_options['debugging_mode_on'] == 'yes' ){  ?> 
     <h4 class="system-info-subtitle">Debug Info</h4>
      <span class="system-info">                  
       <span class="system-info-line"><span class="system-info-label">PHP VERSION: </span> <span class="system-info-data"><?php echo phpversion(); ?></span> </span>
       <span class="system-info-line"><span class="system-info-label">SYSTEM MEMORY: </span> <span class="system-info-data"><?php echo '<pre>'.print_r( $your_system_info , true).'</pre>' ;  ?></span> </span>    
       <span class="system-info-line"><span class="system-info-label">Setup Options: </span> <span class="system-info-data"><?php echo '<pre>'.print_r( $vtmin_setup_options , true).'</pre>' ;  ?></span> </span> 
     </span>
     <?php   }    ?>
	</div><!-- /.wrap -->

<?php
} // end vtmin_display  


/* ------------------------------------------------------------------------ *
 * Setting Registration
 * ------------------------------------------------------------------------ */ 

/**
 * Initializes the theme's display options page by registering the Sections,
 * Fields, and Settings.
 *
 * This function is registered with the 'admin_init' hook.
 */ 

function vtmin_initialize_options() {
  
	// If the theme options don't exist, create them.
	if( false == get_option( 'vtmin_setup_options' ) ) {
		add_option( 'vtmin_setup_options', $this->vtmin_get_default_options() );  //add the option into the table based on the default values in the function.
	} // end if


  //****************************
  //  DISPLAY OPTIONS Area
  //****************************

	// First, we register a section. This is necessary since all future options must belong to a 
	add_settings_section(
		'general_settings_section',			// ID used to identify this section and with which to register options
		__( 'Display Options', 'vtmin' ),	// Title to be displayed on the administration page
		array(&$this, 'vtmin_general_options_callback'),	// Callback used to render the description of the section
		'vtmin_setup_options_page'		// Page on which to add this section of options
	);
		
	// show error msg = yes/no
	add_settings_field(	           //opt1
		'show_error_messages_in_table_form',						// ID used to identify the field throughout the theme
		__( 'Show Error Messages in Table Format ("no" = text format)', 'vtmin' ),		// The label to the left of the option interface element        
		array(&$this, 'vtmin_error_in_table_format_callback'), // The name of the function responsible for rendering the option interface
		'vtmin_setup_options_page',	// The page on which this option will be displayed
		'general_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Error messages can be shown in table formats.', 'vtmin' )
		)
	);                                                        
	// show error msg = yes/no
	add_settings_field(	           //opt2
		'show_error_before_checkout_products',						// ID used to identify the field throughout the theme
		__( 'Show Error Messages Just Before Checkout Products List', 'vtmin' ),							// The label to the left of the option interface element    
		array(&$this, 'vtmin_before_checkout_products_callback'), // The name of the function responsible for rendering the option interface
		'vtmin_setup_options_page',	// The page on which this option will be displayed
		'general_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			__( 'Error messages are shown in one place at checkout by default.', 'vtmin' )
		)
	);
       // customize error selector 1
    add_settings_field(	         //opt11
		'show_error_before_checkout_products_selector',						// ID used to identify the field throughout the theme
		__( 'Show Error Messages Just Before Checkout Products List - HTML Selector <em>(see => "more info")</em>', 'vtmin' ),							// The label to the left of the option interface element
		array(&$this, 'vtmin_before_checkout_products_selector_callback'), // The name of the function responsible for rendering the option interface
		'vtmin_setup_options_page',	// The page on which this option will be displayed
		'general_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			__( 'For the Product area, Supplies the ID or Class HTML selector this message appears before', 'vtmin' )
		)
	);
  	// show error msg = yes/no
    add_settings_field(	         //opt3
		'show_error_before_checkout_address',						// ID used to identify the field throughout the theme
		__( 'Show 2nd Set of Error Messages at Checkout Address Area', 'vtmin' ),							// The label to the left of the option interface element
		array(&$this, 'vtmin_before_checkout_address_callback'), // The name of the function responsible for rendering the option interface
		'vtmin_setup_options_page',	// The page on which this option will be displayed
		'general_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Error messages are shown in one place at checkout by default.', 'vtmin' )
		)
	);
         // customize error selector 2
    add_settings_field(	         //opt12
		'show_error_before_checkout_address_selector',						// ID used to identify the field throughout the theme
		__( 'Show Error Messages Just Before Checkout Address List - HTML Selector <em>(see => "more info")</em>', 'vtmin' ),							// The label to the left of the option interface element
		array(&$this, 'vtmin_before_checkout_address_selector_callback'), // The name of the function responsible for rendering the option interface
		'vtmin_setup_options_page',	// The page on which this option will be displayed
		'general_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			__( 'For the Address area, Supplies the ID or Class HTML selector this message appears before', 'vtmin' )
		)
	);
    	// show vtmin ID = yes/no
    add_settings_field(	         //opt10
		'show_rule_ID_in_errmsg',						// ID used to identify the field throughout the theme
		__( 'Show Rule ID in Error Message', 'vtmin' ),							// The label to the left of the option interface element
		array(&$this, 'vtmin_rule_ID_in_errmsg_callback'), // The name of the function responsible for rendering the option interface
		'vtmin_setup_options_page',	// The page on which this option will be displayed
		'general_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Show minimum amount rule id in error message.', 'vtmin' )
		)
	);
  	// show prod cats = yes/no
    add_settings_field(	         //opt4
		'show_prodcat_names_in_errmsg',						// ID used to identify the field throughout the theme
		__( 'Show Product Category Names in Minimum Purchase Error Message (Pro Only)', 'vtmin' ),							// The label to the left of the option interface element
		array(&$this, 'vtmin_prodcat_names_callback'), // The name of the function responsible for rendering the option interface
		'vtmin_setup_options_page',	// The page on which this option will be displayed
		'general_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			__( 'If Product Categories are used, show their names in any error messages based on the search criteria.', 'vtmin' )
		)                         
	);
    	// show rule cats = yes/no
    add_settings_field(	         //opt5
		'show_rulecat_names_in_errmsg',						// ID used to identify the field throughout the theme
		__( 'Show Rule Category Names in Minimum Purchase Error Message (Pro Only)', 'vtmin' ),							// The label to the left of the option interface element
		array(&$this, 'vtmin_rulecat_names_callback'), // The name of the function responsible for rendering the option interface
		'vtmin_setup_options_page',	// The page on which this option will be displayed
		'general_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			__( 'If Rule Categories are used, show their names in any error messages based on the search criteria.', 'vtmin' )
		)
	);                        
     // custom error msg css at checkout time
    add_settings_field(	         //opt9
		'custom_error_msg_css_at_checkout',						// ID used to identify the field throughout the theme
		__( 'Custom Minimum Purchase Error Message CSS, used at checkout time', 'vtmin' ),							// The label to the left of the option interface element
		array(&$this, 'vtmin_custom_error_msg_css_at_checkout_callback'), // The name of the function responsible for rendering the option interface
		'vtmin_setup_options_page',	// The page on which this option will be displayed
		'general_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			__( 'Realtime CSS override for minimum amount error messages shown at checkout time.  Supply CSS statements only.', 'vtmin' )
		)
	);
       

 
      
  //****************************
  //  PROCESSING OPTIONS Area
  //****************************
  
  	add_settings_section(
		'processing_settings_section',			// ID used to identify this section and with which to register options
		__( 'Processing Options', 'vtmin' ),// Title to be displayed on the administration page
		array(&$this, 'vtmin_processing_options_callback'), // Callback used to render the description of the section
		'vtmin_setup_options_page'		// Page on which to add this section of options
	);
	
 /* v1.07 
    add_settings_field(	         //opt6
		'use_this_currency_sign',						// ID used to identify the field throughout the theme
		__( 'Select a Currency Sign', 'vtmin' ),			// The label to the left of the option interface element
		array(&$this, 'vtmin_currency_sign_callback'), // The name of the function responsible for rendering the option interface
		'vtmin_setup_options_page',	// The page on which this option will be displayed
		'processing_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			__( 'Select a Currency Sign.', 'vtmin' )
		)
	);    
 */ 
    add_settings_field(	        //opt7
		'apply_multiple_rules_to_product',						// ID used to identify the field throughout the theme
		__( 'Apply More Than 1 Rule to Each Product', 'vtmin' ),			// The label to the left of the option interface element
		array(&$this, 'vtmin_mult_rules_processing_callback'), // The name of the function responsible for rendering the option interface
		'vtmin_setup_options_page',	// The page on which this option will be displayed
		'processing_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Do we apply multiple rules to a given product?', 'vtmin' )
		)
	);                
 
 
 

  //****************************
  //  SYSTEM AND DEBUG OPTIONS Area
  //****************************
  
  	add_settings_section(
		'internals_settings_section',			// ID used to identify this section and with which to register options
		__( 'System and Debug Options', 'vtmin' ),		// Title to be displayed on the administration page
		array(&$this, 'vtmin_internals_options_callback'), // Callback used to render the description of the section
		'vtmin_setup_options_page'		// Page on which to add this section of options
	);
	
    add_settings_field(	        //opt8
		'debugging_mode_on',						// ID used to identify the field throughout the theme
		__( 'Test Debugging Mode Turned On <br>(Use Only during testing)', 'vtmin' ),							// The label to the left of the option interface element
		array(&$this, 'vtmin_debugging_mode_callback'), // The name of the function responsible for rendering the option interface
		'vtmin_setup_options_page',	// The page on which this option will be displayed
		'internals_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			__( 'Show any built-in debug info for Rule processing.', 'vtmin' )
		)
	);                    
  /*	
  
 */
	
	// Finally, we register the fields with WordPress
	register_setting(
		'vtmin_setup_options_group',
		'vtmin_setup_options' ,
    array(&$this, 'vtmin_validate_setup_input')
	);
	
} // end vtmin_initialize_options

   
  //****************************
  //  DEFAULT OPTIONS INITIALIZATION
  //****************************
function vtmin_get_default_options() {
     $options = array(
          'show_error_messages_in_table_form' => 'yes',  //opt1
          'show_error_before_checkout_products' => 'yes', //opt2
          'show_error_before_checkout_address' => 'yes', //opt3
          'show_prodcat_names_in_errmsg' => 'no',  //opt4
          'show_rulecat_names_in_errmsg' => 'no',  //opt5
          'use_this_currency_sign' => 'USD',  //opt6
          'apply_multiple_rules_to_product' => 'no', //opt7
          'debugging_mode_on' => 'no',  //opt8
          'custom_error_msg_css_at_checkout'  => '',  //opt9
          'show_rule_ID_in_errmsg' => 'yes',  //opt10
          'show_error_before_checkout_products_selector' => VTMIN_CHECKOUT_PRODUCTS_SELECTOR_BY_PARENT,  //opt11
          'show_error_before_checkout_address_selector'  => VTMIN_CHECKOUT_ADDRESS_SELECTOR_BY_PARENT  //opt12
     );
     return $options;
}
   
function vtmin_processing_options_callback () {
    ?>
    <h4><?php esc_attr_e('These options control rule error processing during checkout.', 'vtmin'); ?></h4>
    <?php                                                                                                                                                                                      
}
   
function vtmin_lifetime_rule_options_callback () {
    ?>
    <h4><?php esc_attr_e('Lifetime rule Options apply to Lifetime Customer Max Purchases. (Lifetime processing rules are available with the Pro version)', 'vtmin'); ?></h4>
    <h4><?php esc_attr_e('These options control how comparisons are made, to see if a customer has purchased products associated with a given rule prior to the current purchase.', 'vtmin'); ?></h4>
    
    <?php                                                                                                                                                                                      
}

function vtmin_general_options_callback () {
    ?>
    <h4><?php esc_attr_e('These options control rule error message display at checkout time.', 'vtmin'); ?> 
      <a id="help-all" class="help-anchor" href="javascript:void(0);" >
      <?php esc_attr_e('Show All:', 'vtmin'); ?> 
      &nbsp; <span> <?php esc_attr_e('More Info', 'vtmin'); ?> </span></a> 
    </h4> 
    <?php
}

function vtmin_internals_options_callback () {
    ?>
    <h4><?php esc_attr_e('These options control internal functions within the plugin.', 'vtmin'); ?></h4>
    <?php  
}




function vtmin_before_checkout_products_callback() {   //opt2
	$options = get_option( 'vtmin_setup_options' );	
	$html = '<select id="before_checkout_products" name="vtmin_setup_options[show_error_before_checkout_products]">';
	$html .= '<option value="yes"' . selected( $options['show_error_before_checkout_products'], 'yes', false) . '>Yes &nbsp;</option>';
	$html .= '<option value="no"'  . selected( $options['show_error_before_checkout_products'], 'no', false) . '>No &nbsp;</option>';
	$html .= '</select>';
  
	$more_info = __('More Info', 'vtmin');
  $html .= '<a id="help2" class="help-anchor" href="javascript:void(0);" >' ;  $html .= $more_info;   $html .= '</a>';
  
  $html .= '<p id="help2-text" class = "help-text" >'; 
  $help = __('"Show Error Messages Just Before Checkout Products List" => This is the standard place to show the error messages, just above the product list area.', 'vtmin'); 
  $html .= $help;
  $html .= '</p>';
    
	echo $html;
}

function vtmin_before_checkout_address_callback() {    //opt3
	$options = get_option( 'vtmin_setup_options' );	
	$html = '<select id="before_checkout_adress" name="vtmin_setup_options[show_error_before_checkout_address]">';
	$html .= '<option value="yes"' . selected( $options['show_error_before_checkout_address'], 'yes', false) . '>Yes &nbsp;</option>';
	$html .= '<option value="no"'  . selected( $options['show_error_before_checkout_address'], 'no', false) . '>No &nbsp;</option>';
	$html .= '</select>';
  
	$more_info = __('More Info', 'vtmin');
  $html .= '<a id="help3" class="help-anchor" href="javascript:void(0);" >' ;  $html .= $more_info;   $html .= '</a>';
  
  $html .= '<p id="help3-text" class = "help-text" >'; 
  $help = __('"Show Error Messages Just Before Checkout Address Area" => This is the second 
  (duplicate) place to show error messages, just above the address area. It is particularly useful 
  if your checkout has multiple panes or pages, rather than a single full-display screen', 'vtmin'); 
  $html .= $help;
  $html .= '</p>';
  	
	echo $html;
}

function vtmin_rulecat_names_callback () {    //opt5
	$options = get_option( 'vtmin_setup_options' );	
	$html = '<select id="show_rulecat" name="vtmin_setup_options[show_rulecat_names_in_errmsg]">';
	$html .= '<option value="yes"' . selected( $options['show_rulecat_names_in_errmsg'], 'yes', false) . '>Yes &nbsp;</option>';
	$html .= '<option value="no"'  . selected( $options['show_rulecat_names_in_errmsg'], 'no', false) . '>No &nbsp;</option>';
	$html .= '</select>';
	
	$more_info = __('More Info', 'vtmin');
  $html .= '<a id="help5" class="help-anchor" href="javascript:void(0);" >' ;  $html .= $more_info;   $html .= '</a>';
  
  $html .= '<p id="help5-text" class = "help-text" >'; 
  $help = __('"Show Minimum Purchase Rule Category Names in Error Message (Pro Only)" => 
  If you choose to use the group input search criteria option, and if you employ a Minimum Purchase Category to group the products, you can choose here 
  whether to include that Rule category name in any error messages produced.', 'vtmin'); 
  $html .= $help;
  $html .= '</p>'; 
  
	echo $html;
}


function vtmin_debugging_mode_callback () {    //opt8
	$options = get_option( 'vtmin_setup_options' );	
	$html = '<select id="debugging-mode" name="vtmin_setup_options[debugging_mode_on]">';
	$html .= '<option value="yes"' . selected( $options['debugging_mode_on'], 'yes', false) . '>Yes &nbsp;</option>';
	$html .= '<option value="no"'  . selected( $options['debugging_mode_on'], 'no', false) . '>No &nbsp;</option>';
	$html .= '</select>';
	
	$more_info = __('More Info', 'vtmin');
  $html .= '<a id="help8" class="help-anchor" href="javascript:void(0);" >' ;  $html .= $more_info;   $html .= '</a>';
  
  $html .= '<p id="help8-text" class = "help-text" >'; 
  $help = __('"Test Debugging Mode Turned On" => 
  Set this to "yes" if you want to see the full rule structures which produce any error messages. **ONLY** should be used during testing.
  <br><br>NB => IF this switch is SET and the "purchase" button is depressed, the following warning may result:
  <br> "Warning: Cannot modify header information - headers already sent by" ... You will still have debug info available, however.
  ', 'vtmin'); 
  $html .= $help;
  $html .= '</p>';  
  
	echo $html;
}

function vtmin_prodcat_names_callback () {    //opt4
	$options = get_option( 'vtmin_setup_options' );	
	$html = '<select id="show_prodcat" name="vtmin_setup_options[show_prodcat_names_in_errmsg]">';
	$html .= '<option value="yes"' . selected( $options['show_prodcat_names_in_errmsg'], 'yes', false) . '>Yes &nbsp;</option>';
	$html .= '<option value="no"'  . selected( $options['show_prodcat_names_in_errmsg'], 'no', false) . '>No &nbsp;</option>';
	$html .= '</select>';
	
	$more_info = __('More Info', 'vtmin');
  $html .= '<a id="help4" class="help-anchor" href="javascript:void(0);" >' ;  $html .= $more_info;   $html .= '</a>';

  $html .= '<p id="help4-text" class = "help-text" >'; 
  $help = __('"Show Minimum Purchase Product Category Names in Error Message (Pro Only)" => 
  If you choose to use the group input search criteria option, and if you employ a Minimum Purchase Category to group the products, you can choose here 
  whether to include that Product category name in any error messages produced.', 'vtmin'); 
  $html .= $help;
  $html .= '</p>';  
  
	echo $html;
}
  
function vtmin_mult_rules_processing_callback() {   //opt7
	$options = get_option( 'vtmin_setup_options' );	
	$html = '<select id="before_checkout_products" name="vtmin_setup_options[apply_multiple_rules_to_product]">';
	$html .= '<option value="yes"' . selected( $options['apply_multiple_rules_to_product'], 'yes', false) . '>Yes &nbsp;</option>';
	$html .= '<option value="no"'  . selected( $options['apply_multiple_rules_to_product'], 'no', false) . '>No &nbsp;</option>';
	$html .= '</select>';
  
	$more_info = __('More Info', 'vtmin');
  $html .= '<a id="help7" class="help-anchor" href="javascript:void(0);" >' ;  $html .= $more_info;   $html .= '</a>';
 
  $html .= '<p id="help7-text" class = "help-text" >'; 
  $help = __('"Apply More Than 1 Rule to Each Product" => Do we apply multiple minimum purchase rules to EACH product in the cart?  If not,
  we apply the FIRST rule we process which applies to a given product.  <strong>It is ***Strongly Suggested*** that this option be set to "NO", as otherwise the compounding error messages
  could be quite confusing for the ecommerce customer.</strong>', 'vtmin'); 
  $html .= $help;
  $html .= '</p>';
  
	echo $html;   
}
  
function vtmin_error_in_table_format_callback() {   //opt1
	$options = get_option( 'vtmin_setup_options' );	
	$html = '<select id="table_format" name="vtmin_setup_options[show_error_messages_in_table_form]">';
	$html .= '<option value="yes"' . selected( $options['show_error_messages_in_table_form'], 'yes', false) . '>Yes &nbsp;</option>';
	$html .= '<option value="no"'  . selected( $options['show_error_messages_in_table_form'], 'no', false) . '>No &nbsp;</option>';
	$html .= '</select>';
  
	$more_info = __('More Info', 'vtmin');
  $html .= '<a id="help1" class="help-anchor" href="javascript:void(0);" >' ;  $html .= $more_info;   $html .= '</a>';

  $html .= '<p id="help1-text" class = "help-text" >'; 
  $help = __('"Show Error Messages in Table Format" => Error messages can be shown in text or table format ("yes" = table format, "no" = text format).  If table format is desired,
  set this option to "yes". ', 'vtmin'); 
  $html .= $help;
  $html .= '</p>';
  
	echo $html;
}
/*  v1.07
function vtmin_currency_sign_callback() {    //opt6
  $options = get_option( 'vtmin_setup_options' );
  $html = '<select id="currency_sign" name="vtmin_setup_options[use_this_currency_sign]">';
	$html .= '<option value="USD"' .  selected( $options['use_this_currency_sign'], 'USD', false) . '>$ &nbsp;&nbsp;(Dollar Sign) &nbsp;</option>';
  $html .= '<option value="EUR"' .  selected( $options['use_this_currency_sign'], 'EUR', false) . '>&euro; &nbsp;&nbsp;(Euro) &nbsp;</option>';
  $html .= '<option value="GBP"' .  selected( $options['use_this_currency_sign'], 'GBP', false) . '>&pound; &nbsp;&nbsp;(Pound Sterling) &nbsp;</option>';
  $html .= '<option value="JPY"' .  selected( $options['use_this_currency_sign'], 'JPY', false) . '>&yen; &nbsp;&nbsp;(Yen) &nbsp;</option>';
  $html .= '<option value="CZK"' .  selected( $options['use_this_currency_sign'], 'CZK', false) . '>&#75;&#269; &nbsp;&nbsp;(Czech Koruna) &nbsp;</option>';
  $html .= '<option value="DKK"' .  selected( $options['use_this_currency_sign'], 'DKK', false) . '>&#107;&#114; &nbsp;&nbsp;(Danish Krone) &nbsp;</option>';
  $html .= '<option value="HUF"' .  selected( $options['use_this_currency_sign'], 'HUF', false) . '>&#70;&#116; &nbsp;&nbsp;(Hungarian Forint) &nbsp;</option>';
  $html .= '<option value="ILS"' .  selected( $options['use_this_currency_sign'], 'ILS', false) . '>&#8362; &nbsp;&nbsp;(Israeli Shekel) &nbsp;</option>';
  $html .= '<option value="MYR"' .  selected( $options['use_this_currency_sign'], 'MYR', false) . '>&#82;&#77; &nbsp;&nbsp;(Malaysian Ringgits) &nbsp;</option>';
  $html .= '<option value="NOK"' .  selected( $options['use_this_currency_sign'], 'NOK', false) . '>&#107;&#114; &nbsp;&nbsp;(Norwegian Krone) &nbsp;</option>';
  $html .= '<option value="PHP"' .  selected( $options['use_this_currency_sign'], 'PHP', false) . '>&#8369; &nbsp;&nbsp;(Philippine Pesos) &nbsp;</option>';
  $html .= '<option value="PLN"' .  selected( $options['use_this_currency_sign'], 'PLN', false) . '>&#122;&#322; &nbsp;&nbsp;(Polish Zloty) &nbsp;</option>';
  $html .= '<option value="SEK"' .  selected( $options['use_this_currency_sign'], 'SEK', false) . '>&#107;&#114; &nbsp;&nbsp;(Swedish Krona) &nbsp;</option>';
  $html .= '<option value="CHF"' .  selected( $options['use_this_currency_sign'], 'CHF', false) . '>&#67;&#72;&#70; &nbsp;&nbsp;(Swiss Franc) &nbsp;</option>';
  $html .= '<option value="TWD"' .  selected( $options['use_this_currency_sign'], 'TWD', false) . '>&#78;&#84;&#36; &nbsp;&nbsp;(Taiwan New Dollars) &nbsp;</option>';
  $html .= '<option value="THB"' .  selected( $options['use_this_currency_sign'], 'THB', false) . '>&#3647; &nbsp;&nbsp;(Thai Baht) &nbsp;</option>';
  $html .= '<option value="TRY"' .  selected( $options['use_this_currency_sign'], 'TRY', false) . '>&#84;&#76; &nbsp;&nbsp;(Turkish Lira) &nbsp;</option>';
  $html .= '<option value="ZAR"' .  selected( $options['use_this_currency_sign'], 'ZAR', false) . '>&#82; &nbsp;&nbsp;(South African Rand) &nbsp;</option>';
  $html .= '<option value="RON"' .  selected( $options['use_this_currency_sign'], 'RON', false) . '>lei &nbsp;&nbsp;(Romanian Leu) &nbsp;</option>';
	$html .= '</select>';
  
  $more_info = __('More Info', 'vtmin');
  $html .= '<a id="help6" class="help-anchor" href="javascript:void(0);" >' ;  $html .= $more_info;   $html .= '</a>';
   
  $html .= '<p id="help6-text" class = "help-text" >'; 
  $help = __('"Select the Currncy Sign for Error Messages" => 
  This currency sign is used whend displaying Minimum Amount rule error messages. If the desired currency symbol is not available, please inform Varktech and 
  it will be added.', 'vtmin'); 
  $html .= $help;
  $html .= '</p>';  
  
	echo $html;
}
*/
function vtmin_custom_error_msg_css_at_checkout_callback() {    //opt9
  $options = get_option( 'vtmin_setup_options' );
  $html = '<textarea type="text" id="custom_error_msg_css_at_checkout"  rows="200" cols="40" name="vtmin_setup_options[custom_error_msg_css_at_checkout]">' . $options['custom_error_msg_css_at_checkout'] . '</textarea>';
  
  $more_info = __('More Info', 'vtmin');
  $html .= '<a id="help9" class="help-anchor" href="javascript:void(0);" >' ;  $html .= $more_info;   $html .= '</a>';
   
  $html .= '<p id="help9-text" class = "help-text" >'; 
  $help = __('"Custom Error Message CSS at Checkout Time" => 
  The CSS used for minimum amount error messages is supplied.  If you want to override any of the css, supply just your overrides here. <br>For Example => 
   div.vtmin-error .red-font-italic {color: green;}', 'vtmin'); 
  $html .= $help;
  $html .= '</p>';  
  
	echo $html;
}

  
function vtmin_rule_ID_in_errmsg_callback() {   //opt10
	$options = get_option( 'vtmin_setup_options' );	
	$html = '<select id="vtmin-id" name="vtmin_setup_options[show_rule_ID_in_errmsg]">';
	$html .= '<option value="yes"' . selected( $options['show_rule_ID_in_errmsg'], 'yes', false) . '>Yes &nbsp;</option>';
	$html .= '<option value="no"'  . selected( $options['show_rule_ID_in_errmsg'], 'no', false) . '>No &nbsp;</option>';
	$html .= '</select>';
  
	$more_info = __('More Info', 'vtmin');
  $html .= '<a id="help10" class="help-anchor" href="javascript:void(0);" >' ;  $html .= $more_info;   $html .= '</a>';

  $html .= '<p id="help10-text" class = "help-text" >'; 
  $help = __('"Show Rule ID in Error Message" => Append the Minimum Amount Rule ID (from the rule entry screen) at the end of
  an error message, to help identify what rule generated the message. ', 'vtmin'); 
  $html .= $help;
  $html .= '</p>';
  
	echo $html;
}


function vtmin_before_checkout_products_selector_callback() {    //opt11
  $options = get_option( 'vtmin_setup_options' );
  $html = '<textarea type="text" id="show_error_before_checkout_products_selector"  rows="1" cols="20" name="vtmin_setup_options[show_error_before_checkout_products_selector]">' . $options['show_error_before_checkout_products_selector'] . '</textarea>';
  
  $more_info = __('More Info', 'vtmin');
  $html .= '<a id="help11" class="help-anchor" href="javascript:void(0);" >' ;  $html .= $more_info;   $html .= '</a>';
   
  $html .= '<p id="help11-text" class = "help-text" >'; 
  $help = __('"Show Error Messages Just Before Checkout Products List - HTML Selector" => 
  <strong>This option controls the location of the message display, ***handle with care***.</strong>  For the Product area error message, this option supplies the ID  or Class HTML selector this message appears before.  This selector would appear in your theme"s checkout area,
  just above the products display area.  Be sure to include the "." or "#" selector identifier before the selector name. Default = "' .VTMIN_CHECKOUT_PRODUCTS_SELECTOR_BY_PARENT . '".  If you"ve changed this value and can"t get it to work, you can use the "reset to defaults" button (just below the "save changes" button) to get the value back (snapshot your other settings first to help you quickly set the other settings back the way to what you had before.)', 'vtmin'); 
  $html .= $help;
  $html .= '</p>';  
  
	echo $html;
}

function vtmin_before_checkout_address_selector_callback() {    //opt12
  $options = get_option( 'vtmin_setup_options' );
  $html = '<textarea type="text" id="show_error_before_checkout_address_selector"  rows="1" cols="20" name="vtmin_setup_options[show_error_before_checkout_address_selector]">' . $options['show_error_before_checkout_address_selector'] . '</textarea>';
  
  $more_info = __('More Info', 'vtmin');
  $html .= '<a id="help12" class="help-anchor" href="javascript:void(0);" >' ;  $html .= $more_info;   $html .= '</a>';
   
  $html .= '<p id="help12-text" class = "help-text" >'; 
  $help = __('"Show Error Messages Just Before Checkout Address List - HTML Selector" => 
  <strong>This option controls the location of the message display, ***handle with care***.</strong>  For the Product area error message, this option supplies the ID  or Class HTML selector this message appears before.  This selector would appear in your theme"s checkout area,
  just above the address display area.  Be sure to include the "." or "#" selector identifier before the selector name. Default = "' .VTMIN_CHECKOUT_ADDRESS_SELECTOR_BY_PARENT . '".  If you"ve changed this value and can"t get it to work, you can use the "reset to defaults" button (just below the "save changes" button) to get the value back (snapshot your other settings first to help you quickly set the other settings back the way to what you had before.)', 'vtmin'); 
  $html .= $help;
  $html .= '</p>';  
  
	echo $html;
}


function vtmin_validate_setup_input( $input ) {

  //did this come from on of the secondary buttons?
  $reset        = ( ! empty($input['options-reset']) ? true : false );
  $repair       = ( ! empty($input['rules-repair']) ? true : false );
  $nuke_rules   = ( ! empty($input['rules-nuke']) ? true : false );
  $nuke_cats    = ( ! empty($input['cats-nuke']) ? true : false );

 
  
  switch( true ) { 
    case $reset        === true :    //reset options
        $output = $this->vtmin_get_default_options();  //load up the defaults
        //as default options are set, no further action, just return
        return apply_filters( 'vtmin_validate_setup_input', $output, $input );
      break;
    case $repair       === true :    //repair rules
        $vtmin_nuke = new VTMIN_Rule_delete;            
        $vtmin_nuke->vtmin_repair_all_rules();
        $output = get_option( 'vtmin_setup_options' );  //fix 2-13-2013 - initialize output, otherwise all Options go away...
      break;
    case $nuke_rules   === true :
        $vtmin_nuke = new VTMIN_Rule_delete;            
        $vtmin_nuke->vtmin_nuke_all_rules();
        $output = get_option( 'vtmin_setup_options' );  //fix 2-13-2013 - initialize output, otherwise all Options go away...
      break;
    case $nuke_cats    === true :    
        $vtmin_nuke = new VTMIN_Rule_delete;            
        $vtmin_nuke->vtmin_nuke_all_rule_cats();
        $output = get_option( 'vtmin_setup_options' );  //fix 2-13-2013 - initialize output, otherwise all Options go away...
      break;
    default:   //standard update button hit...                 
        //$output = array();
        $output = get_option( 'vtmin_setup_options' );  //v1.06
      	foreach( $input as $key => $value ) {
      		if( isset( $input[$key] ) ) {
      			$output[$key] = strip_tags( stripslashes( $input[ $key ] ) );	
      		} // end if		
      	} // end foreach        
      break;
  }
   
   /* alternative to add_settings_error
        $message =  __('<strong>Please Download and/or Activate ' .$free_plugin_name.' (the Free version). </strong><br>It must be installed and active, before the Pro version can be activated.  The Free version can be downloaded from '  . $free_plugin_download , 'vtminpro');
        $admin_notices = '<div id="message" class="error fade" style="background-color: #FFEBE8 !important;"><p>' . $message . ' </p></div>';
        add_action( 'admin_notices', create_function( '', "echo '$admin_notices';" ) );
   */
  
 
  //NO Object-based code on the apply_filters statement needed or wanted!!!!!!!!!!!!!
  return apply_filters( 'vtmin_validate_setup_input', $output, $input );                       
} 


} //end class
 $vtmin_setup_plugin_options = new VTMIN_Setup_Plugin_Options;
  