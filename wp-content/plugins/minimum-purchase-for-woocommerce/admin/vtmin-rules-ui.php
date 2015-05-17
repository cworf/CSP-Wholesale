<?php
 /*
   Rule CPT rows are stored.  At rule store/update
   time, a master rule option array is (re)created, to allow speedier access to rule information at
   product/cart processing time.
 */
class VTMIN_Rules_UI { 
	
	public function __construct(){       
    global $post, $vtmin_info;
    add_action( 'add_meta_boxes_vtmin-rule', array(&$this, 'vtmin_remove_meta_boxes') );   
    add_action( 'add_meta_boxes_vtmin-rule', array(&$this, 'vtmin_add_metaboxes') );
    add_action( "admin_enqueue_scripts", array($this, 'vtmin_enqueue_script') );
   
    //all in one seo fix
    add_action( 'add_meta_boxes_vtmin-rule', array($this, 'vtmin_remove_all_in_one_seo_aiosp') ); 
    
    //AJAX actions
    add_action( 'wp_ajax_vtmin_ajax_load_variations', array($this, 'vtmin_ajax_load_variations') ); 
    add_action( 'wp_ajax_noprov_vtmin_ajax_load_variations', array($this, 'vtmin_ajax_load_variations') ); 
	}
 
    
  public function vtmin_enqueue_script() {
    global $post_type;
    if( 'vtmin-rule' == $post_type ){ 
        wp_register_style( 'vtmin-admin-style', VTMIN_URL.'/admin/css/vtmin-admin-style.css' );  
        wp_enqueue_style('vtmin-admin-style');
        wp_register_script( 'vtmin-admin-script', VTMIN_URL.'/admin/js/vtmin-admin-script.js' );  
        wp_enqueue_script('vtmin-admin-script');
        
        //AJAX resources
        // see http://wp.smashingmagazine.com/2011/10/18/how-to-use-ajax-in-wordpress/
        //     http://wpmu.org/how-to-use-ajax-with-php-on-your-wp-site-without-a-plugin/
        wp_register_script( "vtmin_variations_script", plugin_dir_url( __FILE__ ).'/admin/js/vtmin-variations-script.js', array('jquery') );
        //  "variationsInAjax"  used in URL jquery statement: "url : variationsInAjax.ajaxurl"
        wp_localize_script( 'vtmin_variations_script', 'variationsInAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'location' => 'post', 'manual' => 'false' ));        
        wp_enqueue_script( 'vtmin_variations_script' );

    }
  }    
  
  public function vtmin_remove_meta_boxes() {
     if(!current_user_can('administrator')) {  
      	remove_meta_box( 'revisionsdiv', 'post', 'normal' ); // Revisions meta box
        remove_meta_box( 'commentsdiv', 'vtmin-rule', 'normal' ); // Comments meta box
      	remove_meta_box( 'authordiv', 'vtmin-rule', 'normal' ); // Author meta box
      	remove_meta_box( 'slugdiv', 'vtmin-rule', 'normal' );	// Slug meta box        	
      	remove_meta_box( 'postexcerpt', 'vtmin-rule', 'normal' ); // Excerpt meta box
      	remove_meta_box( 'formatdiv', 'vtmin-rule', 'normal' ); // Post format meta box
      	remove_meta_box( 'trackbacksdiv', 'vtmin-rule', 'normal' ); // Trackbacks meta box
      	remove_meta_box( 'postcustom', 'vtmin-rule', 'normal' ); // Custom fields meta box
      	remove_meta_box( 'commentstatusdiv', 'vtmin-rule', 'normal' ); // Comment status meta box
      	remove_meta_box( 'postimagediv', 'vtmin-rule', 'side' ); // Featured image meta box
      	remove_meta_box( 'pageparentdiv', 'vtmin-rule', 'side' ); // Page attributes meta box
        remove_meta_box( 'categorydiv', 'vtmin-rule', 'side' ); // Category meta box
        remove_meta_box( 'tagsdiv-post_tag', 'vtmin-rule', 'side' ); // Post tags meta box
        remove_meta_box( 'tagsdiv-vtmin_rule_category', 'vtmin-rule', 'side' ); // vtmin_rule_category tags  
        remove_meta_box('relateddiv', 'vtmin-rule', 'side');                  
      } 
 
  }
        
        
  public  function vtmin_add_metaboxes() {
      global $post, $vtmin_info, $vtmin_rule, $vtmin_rules_set;        

      $found_rule = false; 
                                 
      if ($post->ID > ' ' ) {
        $post_id =  $post->ID;
        $vtmin_rules_set   = get_option( 'vtmin_rules_set' ) ;
        $sizeof_rules_set = sizeof($vtmin_rules_set);
        if ($sizeof_rules_set > 0)  {   //1.09.3 
          for($i=0; $i < $sizeof_rules_set; $i++) { 
             if ($vtmin_rules_set[$i]->post_id == $post_id) {
                $vtmin_rule = $vtmin_rules_set[$i];  //load vtmin-rule               
                $found_rule = true;
                $found_rule_index = $i; 
                $i =  $sizeof_rules_set;
             }
          }
        }                             //1.09.3 
      } 

      if (!$found_rule) {
        //initialize rule
        $vtmin_rule = new VTMIN_Rule;  
         //fill in standard default values not already supplied
        $selected = 's';
        $vtmin_rule->inpop[1]['user_input'] = $selected; //’use selection groups’ by default
        $vtmin_rule->specChoice_in[0]['user_input'] = $selected;
        $vtmin_rule->amtSelected[0]['user_input'] = $selected; 
        $vtmin_rule->role_and_or_in[1]['user_input'] = $selected;  // 'or'
      }
                  
      if ( sizeof($vtmin_rule->rule_error_message ) > 0 ) {    //these error messages are from the last upd action attempt, coming from vtmin-rules-update.php
           add_meta_box('vtmin-errmsg', __('Update Error Messages :: The rule is not active until these are resolved ::', 'vtmin'), array(&$this, 'vtmin_error_messages'), 'vtmin-rule', 'normal', 'high');
      }    
      
      add_meta_box('vtmin-pop-in-select', __('Cart Search Criteria', 'vtmin'), array(&$this, 'vtmin_pop_in_select'), 'vtmin-rule', 'normal', 'high');                      
      add_meta_box('vtmin-pop-in-specifics', __('Rule Application Method', 'vtmin'), array(&$this, 'vtmin_pop_in_specifics'), 'vtmin-rule', 'normal', 'high');
      add_meta_box('vtmin-rule-amount', __('Quantity or Price Minimum Amount', 'vtmin'), array(&$this, 'vtmin_rule_amount'), 'vtmin-rule', 'normal', 'high');
      add_meta_box('vtmin-rule-custom-message', __('Custom Message', 'vtmin'), array(&$this, 'vtmin_rule_custom_message'), 'vtmin-rule', 'normal', 'default');  //v1.08
      add_meta_box('vtmin-rule-id', __('Minimum Purchase Rule ID', 'vtmin'), array(&$this, 'vtmin_rule_id'), 'vtmin-rule', 'side', 'low'); //low = below Publish box
      add_meta_box('vtmin-rule-resources', __('Resources', 'vtmin'), array(&$this, 'vtmin_rule_resources'), 'vtmin-rule', 'side', 'low'); //low = below Publish box  
            
      //add help tab to this screen... 
      $content = '<br><a  href="' . VTMIN_DOCUMENTATION_PATH_PRO_BY_PARENT . '"  title="Access Plugin Documentation">Access Plugin Documentation</a>';
      $screen = get_current_screen();
      $screen->add_help_tab( array( 
         'id' => 'vtmin-help',            //unique id for the tab
         'title' => 'Minimum Purchase Help',      //unique visible title for the tab
         'content' => $content  //actual help text
        ) );  
  }                   
   
                                                    
  public function vtmin_error_messages() {     
      global $post, $vtmin_rule;
      echo "<div class='alert-message alert-danger'>" ;       
      for($i=0; $i < sizeof($vtmin_rule->rule_error_message); $i++) {                                   
            echo '<div class="vtmin-error"><p>'; 
            echo $vtmin_rule->rule_error_message[$i];
            echo '</p></div>';            
      } //end for loop
          
      echo "</div>";    
      if( $post->post_status == 'publish') { //if post status not = pending, make it so  
          $post_id = $post->ID;
          global $wpdb;
          $wpdb->update( $wpdb->posts, array( 'post_status' => 'pending' ), array( 'ID' => $post_id ) );
      } 

  }  
   
      
   public    function vtmin_pop_in_select( ) {
       global $post, $vtmin_info, $vtmin_rule; $vtmin_rules_set;
       $checked = 'checked="checked"'; 
       $vtminNonce = wp_create_nonce("vtmin-rule-nonce"); //nonce verified in vt-minimum-purchase.php
       
       $disabled = 'disabled="disabled"' ;       
       ?>
         
        <style type="text/css">
           /*Free version*/
           #cartChoice,
           #cartChoice-label,
           #varChoice,
           #varChoice-label,
           #singleChoice,
           #singleChoice-label,
           #prodcat-in,
           #prodcat-in h3,
           .and-or,
           #rulecat-in,
           #rulecat-in h3,
           #andChoice-label         
           {color:#aaa;}  /*grey out unavailable choices*/
           #wpsc_product_category-adder,
           #vtmin_rule_category-adder {
            display:none;
           }
           #vtmin-pop-in-cntl {margin-bottom:15px;}
           /*v1.06 begin*/
           .pro-anchor {
              border: 1px solid #CCCCCC;
              clear: both;
              color: #000000;
              float: left;
              font-size: 14px;
              margin-bottom: 10px;
              margin-left: 2%;
              margin-top: 20px;
              padding: 5px 10px;
              text-decoration: none;
              width: auto;        
           }
           #inpopDescrip-more-help {color: #0074A2 !important;font-size: 15px;}
           /*v1.06 end*/
        </style>
                   
        <input type="hidden" id="vtmin_nonce" name="vtmin_nonce" value="<?php echo $vtminNonce; ?>" />
        
        <input type="hidden" id="fullMsg" name="fullMsg" value="<?php echo $vtmin_info['default_full_msg'];?>" />  <?php //v1.08  ?>
                            
        <div class="column1" id="inpopDescrip">
            <h4> <?php _e('Choose how to look at the Candidate Population', 'vtmin') ?></h4>
            <p> <?php _e('Minimum Amount rules will only look at the contents of the cart at checkout.
            Minimum Amount rules define a candidate group within the cart. The Free version of the plugin
            applies only to logged-in user membership status.', 'vtmin') ?>           
            </p>
            <?php //v1.06 msg moved below ?>
        </div>

        
        <div class="column2" id="inpopChoice">       
          <h3><?php _e('Select Search Type', 'vtmin')?></h3>
          <div id="inpopRadio">
          <?php
           $sizeof_rule_inpop = sizeof($vtmin_rule->inpop);
           for($i=0; $i < $sizeof_rule_inpop; $i++) { 
           ?>                 
              
              <input id="<?php echo $vtmin_rule->inpop[$i]['id']; ?>" class="<?php echo $vtmin_rule->inpop[$i]['class']; ?>" type="<?php echo $vtmin_rule->inpop[$i]['type']; ?>" name="<?php echo $vtmin_rule->inpop[$i]['name']; ?>" value="<?php echo $vtmin_rule->inpop[$i]['value']; ?>" <?php if ( $vtmin_rule->inpop[$i]['user_input'] > ' ' ) { echo $checked; } else { echo $disabled; } ?> /><span id="<?php echo $vtmin_rule->inpop[$i]['id'] . '-label'; ?>"> <?php echo $vtmin_rule->inpop[$i]['label']; ?></span><br />

           <?php } ?>                 
          </div>

          <span class="" id="singleChoice-span">                                  
            <span id="inpop-singleProdID-label"><?php _e('&nbsp; Enter Product ID Number', 'vtmin')?></span><br />                    
             <input id="<?php echo $vtmin_rule->inpop_singleProdID['id']; ?>" class="<?php echo $vtmin_rule->inpop_singleProdID['class']; ?>" type="<?php echo $vtmin_rule->inpop_singleProdID['type']; ?>" name="<?php echo $vtmin_rule->inpop_singleProdID['name']; ?>" value="<?php echo $vtmin_rule->inpop_singleProdID['value']; ?>">
             <br /> 
            <?php if ($vtmin_rule->inpop_singleProdID['value'] > ' ' ) { ?>           
                <span id="inpop-singleProdID-name-label"><?php _e('&nbsp; Product Name', 'vtmin')?></span><br /> 
                <span id="inpop-singleProdID-name" ><?php echo $vtmin_rule->inpop_singleProdID_name; ?></span><br />
            <?php } ?>                                         
          </span>
          
        </div>
         
        <div class="column3 inpopExplanation" id="cartChoice-chosen">
            <h4><?php _e('Apply to all products in the cart', 'vtmin')?><span> - <?php _e('explained', 'vtmin')?></span></h4>
            <p><?php _e('No threshhold group is chosen, and the initial rule logic applies to all products
            to be found in the cart.', 'vtmin')?>              
            </p>
        </div>
        <div class="column3 inpopExplanation" id="groupChoice-chosen">
            <h4><?php _e('Use Selection Groups', 'vtmin')?><span> - <?php _e('explained', 'vtmin')?></span></h4> 
            <p><?php _e('Using selection groups, you can specify the initial focus of the rule, focusing on some products found in the cart.  
            A selection group can be considered a threshhold, which when reached the other
            aspects of the rule is applied.  For example, if you specify category Auto Parts, then
            if products in categories other than Auto Parts are in the cart, the rule would not apply to them.', 'vtmin')?>           
            </p>
          </div>
        <div class="column3 inpopExplanation" id="varChoice-chosen">
            <h4><?php _e('Single Product with Variations', 'vtmin')?><span> - <?php _e('explained', 'vtmin')?></span></h4>
            <p><?php _e('Apply rule to the variations for a single product found in the cart, whose ID is supplied in the "Product ID" box.  Enter the Product ID and hit the "Product and Variations" button (The product ID can be found in the URL
            of the product during a product edit session).  Select any/all of the variations belonging to the product.', 'vtmin')?>           
            </p>
        </div>  
        <div class="column3 inpopExplanation" id="singleChoice-chosen">
            <h4><?php _e('Single Product Only', 'vtmin')?><span> - <?php _e('explained', 'vtmin')?></span></h4>
            <p><?php _e('Only apply rule to a single product found in the cart, whose ID is supplied in the "Product ID" box.  The product ID can be found in the URL
            of the product during a product edit session.', 'vtmin')?>  
            <br /> <br /> 
            <?php _e('For example, in the product edit session url:', 'vtmin')?> 
            <br /><br />  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <?php _e('http://www.xxxx.com/wp-admin/post.php?post=872&action=edit', 'vtmin')?> 
            <br /><br />
            <?php _e('The product id is in the "post=872" portion of the address, and hence the number is 872. You would enter 
            that number in the box to the left labeled "Enter Product ID Number".', 'vtmin')?>
            <br /><br />
            <?php _e('NB: If **single** is chosen, the value of "All" is applied to the Rule Application Method, regardless of what is chosen below.', 'vtmin')?> 
           </p>
        </div>        

        <div id="inpop-varProdID-cntl">            
          <a id="inpop-varProdID-more" class="help-anchor" href="javascript:void(0);">Single Product with Variations - <span id="pop-in-more-help">More Info</span></a>
         <p id="inpop-varProdID-descrip" class="help-text"><?php _e('When "Single Product with Variations" is chosen, at least one variation must be selected.
         <br><br> NB - PLEASE NOTE: If the product variation structure is changed in any way, you MUST return to the matching rule and reselect your variation choices.
         <br><br>Multiple rules may be created to apply to individual variations, or groups of variations within a product.           
         <br><br>Please be sure to prevent any rule overlap when applied to a given
            product-variation combination.  An overlap example: if there is a category-level rule covering an entire product, and an individual rule applying to any of the product variations.
         <br><br>Rule overlap in variation rules is Not removed by the rule processing engine, it must be prevented here.'   , 'vtmin')?>             
         </p> 
          <div id="inpopVarBox">
              <h3>Single Product with Variations</h3>
              <div id="inpopVarProduct">
                <span id="inpop-varProdID-label"><?php _e('&nbsp; Enter Product ID Number', 'vtmin')?></span><br />                    
                 <input id="<?php echo $vtmin_rule->inpop_varProdID['id']; ?>" class="<?php echo $vtmin_rule->inpop_varProdID['class']; ?>" type="<?php echo $vtmin_rule->inpop_varProdID['type']; ?>" name="<?php echo $vtmin_rule->inpop_varProdID['name']; ?>" value="<?php echo $vtmin_rule->inpop_varProdID['value']; ?>">
                 <br />                            
              </div>
              <div id="inpopVarButton">
                 <?php
                    $product_ID = $vtmin_rule->inpop_varProdID['value'];
                    $product_variation_IDs = vtmin_get_variations_list($product_ID);
                    /* ************************************************
                    **   Get Variations Button for Rule screen
                    *     ==>>> get the product id from $_REQUEST['varProdID'];  in the receiving ajax routine. 
                    ************************************************ */                     
                 ?>
                                                        
                 <div class="inpopVar-loading-animation">
										<img title="Loading" alt="Loading" src="<?php echo VTMIN_URL;?>/admin/images/indicator.gif" />
										<?php _e('Getting Variations ...', 'vtmin'); ?>
								 </div>
                 
                 
                 <a id="ajaxVariationIn" href="javascript:void(0);">
                    <?php if ($product_ID > ' ') {   ?>
                      <?php _e('Refresh Variations', 'vtmin')?>                      
                    <?php } else {   ?>
                      <?php _e('Get Variations', 'vtmin')?> 
                    <?php } ?>
                  </a>
                 
              </div>
          </div>
          <div id="variations-in">
          <?php              
           if ($product_variation_IDs) { //if product still has variations, expose them here
           ?>
              <h3><?php _e('Product Variations', 'vtmin')?></h3>                  
            <?php
              //********************************
              $this->vtmin_post_category_meta_box($post, array( 'args' => array( 'taxonomy' => 'variations', 'tax_class' => 'var-in', 'checked_list' => $vtmin_rule->var_in_checked, 'product_ID' => $product_ID, 'product_variation_IDs' => $product_variation_IDs )));
              // ********************************
            }                               
          ?>
           </div>  <?php//end variations-in ?>
        </div>  <?php //end inpopVarProdID ?>       

        
        <?php //v1.06 moved here, changed msg?>
        <a id="" class="pro-anchor" target="_blank"  href="<?php echo VTMIN_PURCHASE_PRO_VERSION_BY_PARENT ; ?>">( Greyed-out Options are available in the <span id="inpopDescrip-more-help">Pro Version</span> &nbsp;)</a>
         

       <div class="<?php //echo $groupPop_vis ?> " id="vtmin-pop-in-cntl">                                                  
         <a id="pop-in-more" class="help-anchor" href="javascript:void(0);">Selection Groups - <span id="pop-in-more-help">More Info</span></a>
         <p id="pop-in-descrip" class="help-text"><?php _e("Role/Membership is used within Wordpress to control access and capabilities, when a role is given to a user.  
         Wordpress assigns certain roles by default such as Subscriber for new users or Administrator for the site's owner. Roles can also be used to associate a user 
         with a pricing level.  Use a role management plugin like http://wordpress.org/extend/plugins/user-role-editor/ to establish custom roles, which you can give 
         to a user or class of users.  Then you can associate that role with a Minimum Purchase Rule.  So when the user logs into your site, their Role interacts with the appropriate Rule.
         <br><br>
         In the Pro version, you may use an existing category to identify the group of products to which you wish to apply the rule.  
         If you'd rather, use a Minimum Purchase Category to identify products - this avoids disturbing the store categories. Just add a Minimum Purchase Category, go to the product screen,
         and add the product to the correct minimum purchase category.  (On your product add/update screen, the Mininimum purchase 
         category metabox is just below the default product category box.)  You can also apply the rule using User Membership or Roles  
         as a solo selection, or you can use any combination of all three.  
         <br><br>
         Please take note of the relationship choice 'and/or'
         when using roles.  The default is 'or', while choosing 'and' requires that both a role and a category be selected, before a rule
         can be published.", 'vtmin')?>
         </p> 
    
        <div id="prodcat-in">
          <h3><?php _e('Product Categories', 'vtmin')?></h3>
          
          <?php
          // ********************************
          $this->vtmin_post_category_meta_box($post, array( 'args' => array( 'taxonomy' => $vtmin_info['parent_plugin_taxonomy'], 'tax_class' => 'prodcat-in', 'checked_list' => $vtmin_rule->prodcat_in_checked)));
          // ********************************
          ?>
        
        </div>  <?php//end prodcat-in ?>
        <h4 class="and-or"><?php _e('Or', 'vtmin') //('And / Or', 'vtmin')?></h4>
        <div id="rulecat-in">
          <h3><?php _e('Minimum Purchase Categories', 'vtmin')?></h3>
          
          <?php
          // ********************************
          $this->vtmin_post_category_meta_box($post, array( 'args' => array( 'taxonomy' => $vtmin_info['rulecat_taxonomy'], 'tax_class' => 'rulecat-in', 'checked_list' => $vtmin_rule->rulecat_in_checked )));
          // ********************************
          ?> 
                         
        </div>  <?php//end rulecat-in ?>
        
        
        <div id="and-or-role-div">
          <?php
           $checked = 'checked="checked"'; 
           for($i=0; $i < sizeof($vtmin_rule->role_and_or_in); $i++) { 
           ?>                               
              <input id="<?php echo $vtmin_rule->role_and_or_in[$i]['id']; ?>" class="<?php echo $vtmin_rule->role_and_or_in[$i]['class']; ?>" type="<?php echo $vtmin_rule->role_and_or_in[$i]['type']; ?>" name="<?php echo $vtmin_rule->role_and_or_in[$i]['name']; ?>" value="<?php echo $vtmin_rule->role_and_or_in[$i]['value']; ?>" <?php if ( $vtmin_rule->role_and_or_in[$i]['user_input'] > ' ' ) { echo $checked; } else { echo $disabled; }?>    /><span id="<?php echo $vtmin_rule->role_and_or_in[$i]['id'] . '-label'; ?>"> <?php echo $vtmin_rule->role_and_or_in[$i]['label']; ?></span><br /> 
           <?php } 
           //if neither 'and' nor 'or' selected, select 'or'
         /*  if ( (!$vtmin_rule->role_and_or_in[0]['user_input'] == 's') && (!$vtmin_rule->role_and_or_in[1]['user_input'] == 's') )   {
               $vtmin_rule->role_and_or_in[1]['user_input'] = 's';
           }   */
                      
           ?>                 
          </div>
        
        
        <div id="role-in">
          <h3><?php _e('Membership List by Role', 'vtmin')?></h3>
          
          <?php
          // ********************************
          $this->vtmin_post_category_meta_box($post, array( 'args' => array( 'taxonomy' => 'roles', 'tax_class' => 'role-in', 'checked_list' => $vtmin_rule->role_in_checked  )));
          // ********************************
          ?>
        </div>
        <div class="back-to-top">
            <a title="Back to Top" href="#wpbody"><?php _e('Back to Top', 'vtmin')?><span class="back-to-top-arrow">&nbsp;&uarr;</span></a>
        </div>
      </div> <?php //end vtmin-pop-in-cntl ?>

   <?php   
}
      
  
    public    function vtmin_pop_in_specifics( ) {                     
       global $post, $vtmin_info, $vtmin_rule; $vtmin_rules_set;
       $checked = 'checked="checked"';      
  ?>
        
       <div class="column1" id="specDescrip">
          <h4><?php _e('How is the Rule applied to the search results?', 'vtmin')?></h4>
          <p><?php _e("Once we've figured out the population we're working on (cart only or specified groups),
          how do we apply the rule?  Do we look at each product individually and apply the rule to
          each product we find?  Or do we look at the population as a group, and apply the rule to the
          group as a tabulated whole?  Or do we apply the rule to any we find, and limit the application 
          of the rule to a certain number of products?", 'vtmin')?>           
          </p>
       </div>
       <div class="column2" id="specChoice">
          <h3><?php _e('Select Rule Application Method', 'vtmin')?></h3>
          <div id="specRadio">
            <span id="Choice-input-span">
                <?php
               for($i=0; $i < sizeof($vtmin_rule->specChoice_in); $i++) { 
               ?>                 

                  <input id="<?php echo $vtmin_rule->specChoice_in[$i]['id']; ?>" class="<?php echo $vtmin_rule->specChoice_in[$i]['class']; ?>" type="<?php echo $vtmin_rule->specChoice_in[$i]['type']; ?>" name="<?php echo $vtmin_rule->specChoice_in[$i]['name']; ?>" value="<?php echo $vtmin_rule->specChoice_in[$i]['value']; ?>" <?php if ( $vtmin_rule->specChoice_in[$i]['user_input'] > ' ' ) { echo $checked; } ?> /><?php echo $vtmin_rule->specChoice_in[$i]['label']; ?><br />

               <?php
                }
               ?>  
            </span>
            <span class="" id="anyChoice-span">
                <span><?php _e('*Any* applies to a *required*', 'vtmin')?></span><br />
                 <?php _e('Maximum of:', 'vtmin')?>                      
                 <input id="<?php echo $vtmin_rule->anyChoice_max['id']; ?>" class="<?php echo $vtmin_rule->anyChoice_max['class']; ?>" type="<?php echo $vtmin_rule->anyChoice_max['type']; ?>" name="<?php echo $vtmin_rule->anyChoice_max['name']; ?>" value="<?php echo $vtmin_rule->anyChoice_max['value']; ?>">
                 <?php _e('Products', 'vtmin')?>
            </span>           
          </div>                
       </div>                                                
       <div class="column3 specExplanation" id="allChoice-chosen">
          <h4><?php _e('Treat the Selected Group as a Single Entity', 'vtmin')?><span> - <?php _e('explained', 'vtmin')?></span></h4>
          <p><?php _e("Using *All* as your method, you choose to look at all the products from your cart search results.  That means we add
          all the quantities and/or price across all relevant products in the cart, to test against the rule's requirements.", 'vtmin')?>           
          </p>
       </div>
       <div class="column3 specExplanation" id="eachChoice-chosen">
          <h4><?php _e('Each in the Selected Group', 'vtmin')?><span> - <?php _e('explained', 'vtmin')?></span></h4>
          <p><?php _e("Using *Each* as your method, we apply the rule to each product from your cart search results.
          So if any of these products fail to meet the rule's requirements, the cart as a whole receives an error message.", 'vtmin')?>           
          </p>
       </div>
       <div class="column3 specExplanation" id="anyChoice-chosen">
          <h4><?php _e('Apply the rule to any Individual Product in the Cart', 'vtmin')?><span> - <?php _e('explained', 'vtmin')?></span></h4>
          <p><?php _e("Using *Any*, we can apply the rule to any product in the cart from your cart search results, similar to *Each*.  However, there is a
          maximum number of products to which the rule is applied. The product group is checked to see if any of the group fail to reach the minimum amount
          threshhold.  If so, the error will be applied to products in the cart based on cart order, up to the maximum limit supplied.", 'vtmin')?>
          <br /> <br /> 
          <?php _e('For example, the rule might be something like:', 'vtmin')?>
          <br /> <br />
          <?php _e('&nbsp;&nbsp;"You must buy a minimum of $10 for each of any of 2 products from this group."', 'vtmin')?>              
          </p>               
       </div> 
       <div class="back-to-top">
            <a title="Back to Top" href="#wpbody"><?php _e('Back to Top', 'vtmin')?><span class="back-to-top-arrow">&nbsp;&uarr;</span></a>
       </div>
      <?php
  }  
      
                                                                            
    public    function vtmin_rule_amount( ) {
        global $post, $vtmin_info, $vtmin_rule, $vtmin_rules_set;
        $checked = 'checked="checked"';           
          ?>
        <div class="column1" id="amtDescrip">
            <h4><?php _e('What are the Rule Amount Options?', 'vtmin')?></h4>
          <p><?php _e('Minimum Purchase Rules can be applied to the quantity or the price of the products from 
          your cart search results.', 'vtmin')?>        
          </p>
      </div>
      <div class="column2" id="amtChoice">
          <h3><?php _e('Select Rule Amount Option', 'vtmin')?></h3>
          <div id="amtRadio">
            <span id="amt-selected">
             <?php
             for($i=0; $i < sizeof($vtmin_rule->amtSelected); $i++) { 
             ?>                 

                <input id="<?php echo $vtmin_rule->amtSelected[$i]['id']; ?>" class="<?php echo $vtmin_rule->amtSelected[$i]['class']; ?>" type="<?php echo $vtmin_rule->amtSelected[$i]['type']; ?>" name="<?php echo $vtmin_rule->amtSelected[$i]['name']; ?>" value="<?php echo $vtmin_rule->amtSelected[$i]['value']; ?>" <?php if ( $vtmin_rule->amtSelected[$i]['user_input'] > ' ' ) { echo $checked; } ?> /><?php echo $vtmin_rule->amtSelected[$i]['label']; ?><br />

             <?php
              }
             ?>
            </span>
            <span id="amtChoice-span">
                 <?php _e('Minimum Amount:', 'vtmin')?>
                 <input id="<?php echo $vtmin_rule->minimum_amt['id']; ?>" class="<?php echo $vtmin_rule->minimum_amt['class']; ?>" type="<?php echo $vtmin_rule->minimum_amt['type']; ?>" name="<?php echo $vtmin_rule->minimum_amt['name']; ?>" value="<?php echo $vtmin_rule->minimum_amt['value']; ?>">
            </span>
          </div>                
       </div>
      <div class="column3 amtExplanation" id="qtyChoice-chosen">
          <h4><?php _e('Apply to Quantity', 'vtmin')?><span> - <?php _e('explained', 'vtmin')?></span></h4>
          <p><?php _e('With Quantity chosen, we total up the units amount across indivual products, candidate groups in the cart, or the cart
          in total.  Then we compare that total against the minimum amount for the rule.', 'vtmin')?>        
          </p>
       </div>
       <div class="column3 amtExplanation" id="amtChoice-chosen">
          <h4><?php _e('Apply to Price', 'vtmin')?><span> - <?php _e('explained', 'vtmin')?></span></h4>
          <p><?php _e('With Price chosen, we total up the price across indivual products, candidate groups in the cart, or the cart
          in total.  Then we compare that total against the minimum amount for the rule.', 'vtmin')?>            
          </p>
       </div>
       <div class="back-to-top">
            <a title="Back to Top" href="#wpbody"><?php _e('Back to Top', 'vtmin')?><span class="back-to-top-arrow">&nbsp;&uarr;</span></a>
       </div>
      <?php
  }       
   
   //V1.08 NEW FUNCTION 
   //Custom Message overriding default messaging                                                                        
    public    function vtmin_rule_custom_message() {
        global $post, $vtmin_info, $vtmin_rule, $vtmin_rules_set;                   
          ?>
        <div class="rule_message clear-left" id="cust-msg-text-area">
           <span class="newColumn1" id=cust-msg-text-label-area>
              <h3><?php _e('Custom Message Text', 'vtmin')?></h3>
              <span id='cust-msg-optional'>(optional)</span>
              <span class="clear-left" id='cust-msg-comment'>(overrides default message)</span>
           </span>   
            <textarea name="cust-msg-text" type="text" class="msg-text newColumn2" id="cust-msg-text" cols="50" rows="2"><?php echo $vtmin_rule->custMsg_text; ?></textarea>          
       </div>

      <?php
  }  
  //v1.08 end
                                                                              
    public    function vtmin_rule_id( ) {
        global $post;           
        echo '<span id="vtmin-rule-postid">' . $post->ID . '</span>';
  } 
  
    public    function vtmin_rule_resources ( ) {          
        echo '<span id="vtmin-rr-text">Read documentation, learn the functions and find some tips & tricks.</span><br>';
        echo '<a id="vtmin-rr-doc"  href="' . VTMIN_DOCUMENTATION_PATH_PRO_BY_PARENT . '"  title="Access Plugin Documentation">Plugin Documentation</a>';
        echo '<span id="vtmin-rr-box">';
        echo '<span id="vtmin-rr-created">by VarkTech.com</span>';
        echo '<a id="vtmin-rr-vote"  href="' . VTMIN_DOWNLOAD_FREE_VERSION_BY_PARENT . '"  title="Vote for the Plugin">Vote</a>';
        echo '</span>'; //end rr-box
  }   
      
/*
source: http://www.ilovecolors.com.ar/avoid-hierarchical-taxonomies-to-loose-hierarchy/ 
==> pasted from wp-admin/includes/meta-boxes.php -> post_categories_meta_box()
**  plugin with same code in http://scribu.net/wordpress/category-checklist-tree 
*/
       
  public  function vtmin_post_category_meta_box( $post, $box ) {
      $defaults = array('taxonomy' => 'category');
      if ( !isset($box['args']) || !is_array($box['args']) )
          $args = array();
      else
          $args = $box['args'];
      extract( wp_parse_args($args, $defaults), EXTR_SKIP );
      $tax = get_taxonomy($taxonomy);
   
   //vark => removed the divs with the tabs for 'all' and 'most popular'
      ?>
      <div id="taxonomy-<?php echo $taxonomy; ?>" class="categorydiv">
   
          <div id="<?php echo $taxonomy; ?>-pop" class="tabs-panel" style="display: none;">
              <ul id="<?php echo $taxonomy; ?>checklist-pop" class="categorychecklist form-no-clear" >
                  <?php $popular_ids = wp_popular_terms_checklist($taxonomy); ?>
              </ul>
          </div>
   
          <div id="<?php echo $taxonomy; ?>-all" class="tabs-panel">
              <?php
              $name = ( $taxonomy == 'category' ) ? 'post_category' : 'tax_input[' .  $tax_class . ']';     //vark replaced $taxonomy with $tax_class
              echo "<input type='hidden' name='{$name}[]' value='0' />"; // Allows for an empty term set to be sent. 0 is an invalid Term ID and will be ignored by empty() checks.
              ?>
              <ul id="<?php echo $taxonomy; ?>checklist" class="list:<?php echo $taxonomy?> categorychecklist form-no-clear">
      <?php    

            switch( $taxonomy ) {
              case 'roles': 
                  $vtmin_checkbox_classes = new VTMIN_Checkbox_classes; 
                  $vtmin_checkbox_classes->vtmin_fill_roles_checklist($tax_class, $checked_list);
                break;
              case 'variations':                  
                  vtmin_fill_variations_checklist($tax_class, $checked_list, $product_ID, $product_variation_IDs);                            
                break;
              default:  //product category or vtmin category...
                  $this->vtmin_build_checkbox_contents ($taxonomy, $tax_class, $checked_list);                             
                break;
            }
            
      ?>  
              </ul>
          </div>
           
      <?php //wp-hidden-children div removed, no longer functions as/of WP3.5 ?>

      </div>
      <?php
}

    //remove conflict with all-in-one seo pack!!  
    //  from http://wordpress.stackexchange.com/questions/55088/disable-all-in-one-seo-pack-for-some-custom-post-types
    function vtmin_remove_all_in_one_seo_aiosp() {
        $cpts = array( 'vtmin-rule' );
        foreach( $cpts as $cpt ) {
            remove_meta_box( 'aiosp', $cpt, 'advanced' );
        }
    }


    
  /*
    *  taxonomy (r) - registered name of taxonomy
    *  tax_class (r) - name options => 'prodcat-in' 'prodcat-out' 'rulecat-in' 'rulecat-out'
    *             refers to product taxonomy on the candidate or action categories,
    *                       rulecat taxonomy on the candidate or action categories
    *                         :: as there are only these 4, they are unique   
    *  checked_list (o) - selection list from previous iteration of rule selection                              
    *                          
   */

  public function vtmin_build_checkbox_contents ($taxonomy, $tax_class, $checked_list = NULL) {
        global $wpdb, $vtmin_info;         
        $sql = "SELECT terms.`term_id`, terms.`name`  FROM `" . $wpdb->prefix . "terms` as terms, `" . $wpdb->prefix . "term_taxonomy` as term_taxonomy WHERE terms.`term_id` = term_taxonomy.`term_id` AND term_taxonomy.`taxonomy` = '" . $taxonomy . "' ORDER BY terms.`term_id` ASC";                         
		    $categories = $wpdb->get_results($sql,ARRAY_A) ;

        foreach ($categories as $category) {
            $output  = '<li id='.$taxonomy.'-'.$category['term_id'].'>' ;
            $output  .= '<label class="selectit">' ;
            $output  .= '<input id="'.$tax_class.'_'.$taxonomy.'-'.$category['term_id'].' " ';
            $output  .= 'type="checkbox" name="tax-input-' .  $tax_class . '[]" ';
            $output  .= 'value="'.$category['term_id'].'" ';
            if ($checked_list) {
                if (in_array($category['term_id'], $checked_list)) {   //if cat_id is in previously checked_list  
                   $output  .= 'checked="checked"';
                }
            }
            if ( ($taxonomy == $vtmin_info['parent_plugin_taxonomy']) || ($taxonomy == $vtmin_info['rulecat_taxonomy']) )           {       
                  $output  .= ' disabled="disabled"';
            }
            $output  .= '>'; //end input statement
            $output  .= '&nbsp;' . $category['name'];
            $output  .= '</label>';            
            $output  .= '</li>';
              echo $output ;
         }
         return;
    }



      
} //end class