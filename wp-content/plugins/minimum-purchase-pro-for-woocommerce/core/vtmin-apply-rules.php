<?php
/*
Author: VarkTech
Author URI: http://varktech.com
*
Copyright 2012 AardvarkPC Services NZ, all rights reserved.  See license.txt for more details.
*
*/

class VTMIN_Apply_Rules{
	
	public function __construct(){
		global $vtmin_cart, $vtmin_rules_set, $vtmin_rule;
    //get pre-formatted rules from options field
    
    $vtmin_rules_set = get_option( 'vtmin_rules_set' );

    // create a new vtmin_cart intermediary area, load with parent cart values.  results in global $vtmin_cart.
    vtmin_load_vtmin_cart_for_processing(); 
    
    $this->vtmin_minimum_purchase_check();
	}


  public function vtmin_minimum_purchase_check() { 
    global $post, $vtmin_setup_options, $vtmin_cart, $vtmin_rules_set, $vtmin_rule, $vtmin_info;
     
     
    //************************************************
    //BEGIN processing to mark product as participating in the rule or not...
    //************************************************
    
    /*  Analyze each rule, and load up any cart products found into the relevant rule
        fill rule array with product cart data :: load inpop info 
    */  
    $sizeof_vtmin_rules_set = sizeof($vtmin_rules_set);
    $sizeof_cart_items      = sizeof($vtmin_cart->cart_items);
     
    for($i=0; $i < $sizeof_vtmin_rules_set; $i++) {                                                               
      if ( $vtmin_rules_set[$i]->rule_status == 'publish' ) {       
        for($k=0; $k < $sizeof_cart_items; $k++) {                 
            switch( $vtmin_rules_set[$i]->inpop_selection ) {  
              case 'cart':                                                                                      
                    //load whole cart into inpop
                    $this->vtmin_load_inpop_found_list($i, $k);
                break;
              case 'groups':
                  //test if product belongs in rule inpop
                  if ( $this->vtmin_product_is_in_inpop_group($i, $k) ) {
                    $this->vtmin_load_inpop_found_list($i, $k);                        
                  }
                break;
              case 'vargroup':
                  if (in_array($vtmin_cart->cart_items[$k]->product_id, $vtmin_rules_set[$i]->var_in_checked )) {   //if IDS is in previously checked_list
                  /*   WOOCommerce - Handled in vtmin-parent-functions
                    //product name on cart is the owning product name.  to get the variation name, get the post title, load it into the cart...
                    $post = get_post($vtmin_cart->cart_items[$k]->product_id);
                    $vtmin_cart->cart_items[$k]->product_name = $post->post_title;
                   */                     
                    $this->vtmin_load_inpop_found_list($i, $k);
                  }
                break;
              case 'single':
                  //one product to rule them all
                  if ($vtmin_cart->cart_items[$k]->product_id == $vtmin_rules_set[$i]->inpop_singleProdID['value']) {
                    $this->vtmin_load_inpop_found_list($i, $k);
                  }
                break;
            } 
                                              
        }   
      } 
    }  //end inpop population processing
    
                                                                                                      
    //************************************************
    //BEGIN processing to mark rules as requiring action y/n
    //************************************************
            
    /*  Analyze each Rule population, and see if they satisfy the rule
    *     identify and label each rule as requiring action = yes/no
    */
    for($i=0; $i < $sizeof_vtmin_rules_set; $i++) {         
        if ( $vtmin_rules_set[$i]->rule_status == 'publish' ) {  
          
          if ( sizeof($vtmin_rules_set[$i]->inpop_found_list) == 0 ) {
             $vtmin_rules_set[$i]->rule_requires_cart_action = 'no';   // cut out unnecessary logic...
          } else {
            
            $vtmin_rules_set[$i]->rule_requires_cart_action = 'pending';
            $sizeof_inpop_found_list = sizeof($vtmin_rules_set[$i]->inpop_found_list);
            /*
                AS only one product can be found with 'single', override to 'all' speeds things along
            */
            if ($vtmin_rules_set[$i]->inpop_selection ==  'single') {
               $vtmin_rules_set[$i]->specChoice_in_selection = 'all' ; 
            }
            
            switch( $vtmin_rules_set[$i]->specChoice_in_selection ) {
               case 'all':  //$specChoice_value = 'all'  => total up everything in the population as a unit  
                    if ($vtmin_rules_set[$i]->amtSelected_selection == 'currency'){   //price total
                        if ($vtmin_rules_set[$i]->inpop_total_price >= $vtmin_rules_set[$i]->minimum_amt['value']) {                                                 
                          $vtmin_rules_set[$i]->rule_requires_cart_action = 'no';
                        } else {
                          $vtmin_rules_set[$i]->rule_requires_cart_action = 'yes';
                        }
                    } else {  //qty total
                        if ($vtmin_rules_set[$i]->inpop_qty_total >= $vtmin_rules_set[$i]->minimum_amt['value']) {
                          $vtmin_rules_set[$i]->rule_requires_cart_action = 'no';
                        } else {
                          $vtmin_rules_set[$i]->rule_requires_cart_action = 'yes';
                        }
                    } 
                    if ($vtmin_rules_set[$i]->rule_requires_cart_action == 'yes') {
                       for($k=0; $k < $sizeof_inpop_found_list; $k++) {
                          $this->vtmin_mark_product_as_requiring_cart_action($i,$k);                          
                       }
                    }  		
              		break;
               case 'each': //$specChoice_value = 'each' => apply the rule to each product individually across all products found         		
              		  for($k=0; $k < $sizeof_inpop_found_list; $k++) {
                        if ($vtmin_rules_set[$i]->amtSelected_selection == 'currency'){   //price total
                            if ($vtmin_rules_set[$i]->inpop_found_list[$k]['prod_total_price'] >= $vtmin_rules_set[$i]->minimum_amt['value']){
                               $vtmin_rules_set[$i]->inpop_found_list[$k]['prod_requires_action'] = 'no';
                            }  else {
                               $this->vtmin_mark_product_as_requiring_cart_action($i,$k);
                            }
                        }  else {
                            if ($vtmin_rules_set[$i]->inpop_found_list[$k]['prod_qty'] >= $vtmin_rules_set[$i]->minimum_amt['value']){
                               $vtmin_rules_set[$i]->inpop_found_list[$k]['prod_requires_action'] = 'no';
                            }  else {
                               $this->vtmin_mark_product_as_requiring_cart_action($i,$k);
                            }
                        }
                    }
                        
                  break;
               case 'any':  //$specChoice_value = 'any'  =>   "You must buy a minimum of $10 for each of any of 2 products from this group."       		
              		  //Version 1.01 completely replaced the original case logic
                    $any_action_cnt = 0;
                    for($k=0; $k < $sizeof_inpop_found_list; $k++) {
                        if ($vtmin_rules_set[$i]->amtSelected_selection == 'currency'){   //price total
                            if ($vtmin_rules_set[$i]->inpop_found_list[$k]['prod_total_price'] < $vtmin_rules_set[$i]->minimum_amt['value']){
                               $vtmin_rules_set[$i]->inpop_found_list[$k]['prod_requires_action'] = 'no';
                            }  else {
                               $this->vtmin_mark_product_as_requiring_cart_action($i,$k);
                               $any_action_cnt++;
                            }
                        }  else {
                            if ($vtmin_rules_set[$i]->inpop_found_list[$k]['prod_qty'] < $vtmin_rules_set[$i]->minimum_amt['value']){
                               $vtmin_rules_set[$i]->inpop_found_list[$k]['prod_requires_action'] = 'no';
                            }  else {
                               $this->vtmin_mark_product_as_requiring_cart_action($i,$k);
                               $any_action_cnt++;
                            }
                        }
                        //if 'any' limit reached, end the loop, don't mark any mor products as requiring cart action
                        if ($any_action_cnt >= $vtmin_rules_set[$i]->anyChoice_max['value']) {
                            $k = $sizeof_inpop_found_list;   
                        }
                    }                  
                  break;
            }
        }        
      }
    }   
    
    //****************************************************************************
    //   IF WE DON'T DO "apply multiple rules to product", rollout the multples   
    //****************************************************************************
    if ($vtmin_setup_options[apply_multiple_rules_to_product] == 'no' )  {
      $sizeof_cart_items = sizeof($vtmin_cart->cart_items);
      for($k=0; $k < $sizeof_cart_items; $k++) {             //$k = 'cart item'
         if ( sizeof($vtmin_cart->cart_items[$k]->product_participates_in_rule) > 1 ) {  
            //*****************************
            //remove product from **2ND** TO NTH rule, roll quantity and price out of totals for that rule
            //***************************** 
            $sizeof_product_participates_in_rule = sizeof($vtmin_cart->cart_items[$k]->product_participates_in_rule);
            for($r=1; $r < $sizeof_product_participates_in_rule; $r++) {   //$r = 'in rule'
              //disambiguation does not apply to products belonging to a varkgroup rule
              if (!$vtmin_cart->cart_items[$k]->product_participates_in_rule[$r]['inpop_selection'] == 'vargroup') {  //does not apply to vargroups!!
                  //use stored occurrences to establish addressability to this rule's info...
                  $rulesetLoc = $vtmin_cart->cart_items[$k]->product_participates_in_rule[$r]['ruleset_occurrence'];
                  $inpopLoc   = $vtmin_cart->cart_items[$k]->product_participates_in_rule[$r]['inpop_occurrence'];
                  //roll the product out of the rule totals, mark as 'no action required' for that rule!  
                  $vtmin_rules_set[$rulesetLoc]->inpop_qty_total   -= $vtmin_rules_set[$rulesetLoc]->inpop_found_list[$inpopLoc]['prod_qty'];
                  $vtmin_rules_set[$rulesetLoc]->inpop_total_price -= $vtmin_rules_set[$rulesetLoc]->inpop_found_list[$inpopLoc]['prod_total_price'];
                  $vtmin_rules_set[$rulesetLoc]->inpop_found_list[$inpopLoc]['prod_requires_action'] = 'no';
                  //if action amounts are 0, turn off action status for rule
                  if ( ($vtmin_rules_set[$rulesetLoc]->inpop_qty_total == 0) && ($vtmin_rules_set[$rulesetLoc]->inpop_total_price == 0) ) {
                    $vtmin_rules_set[$rulesetLoc]->rule_requires_cart_action = 'no'; 
                  }
                  unset ( $vtmin_cart->cart_items[$k]->product_participates_in_rule[$r] );//this array is used later in printing errors in table form 
              }
           }    
         }                                       
      }

    }
     
     
            
    //************************************************
    //BEGIN processing to produce error messages
    //************************************************
    /*
     * For those rules whose product population has failed the rules test,
     *   document the rule failure in an error message
     *   and ***** place the error message into the vtmin cart *****
     *   
     * All of the inpop_found info placed into the rules array during the apply-rules process
     *      is only temporary.  None of that info is stored on the rules array on a 
     *      more permanent basis.  Once the error messages are displayed, they too are discarded
     *      from the rules array (by simply not updating the array on the options table). 
     *      The errors are available to the rules_ui on the error-display go-round because 
     *           the info is held in the global namespace.                                   
    */
    $vtmin_info['error_message_needed'] = 'no';
    for($i=0; $i < $sizeof_vtmin_rules_set; $i++) {               
        if ( $vtmin_rules_set[$i]->rule_status == 'publish' ) {    
            switch( true ) {            
              case ($vtmin_rules_set[$i]->rule_requires_cart_action == 'no'):
                  //no error message for this rule, go to next in loop
                break;  
                  
              case ( ($vtmin_rules_set[$i]->rule_requires_cart_action == 'yes') || ($vtmin_rules_set[$i]->rule_requires_cart_action == 'pending') ):
                                     
                //************************************************
                //Create Error Messages for single or group 
                //************************************************
 
                //errmsg pre-processing
                $this->vtmin_init_recursive_work_elements($i); 
                               
                switch( $vtmin_rules_set[$i]->inpop_selection ) {
                  case 'single': 
                     $vtmin_rules_set[$i]->errProds_total_price = $vtmin_rules_set[$i]->inpop_total_price;
                     $vtmin_rules_set[$i]->errProds_qty         = $vtmin_rules_set[$i]->inpop_qty_total;
                     $vtmin_rules_set[$i]->errProds_ids []      = $vtmin_rules_set[$i]->inpop_found_list[0]['prod_id'];
                     $vtmin_rules_set[$i]->errProds_names []    = $vtmin_rules_set[$i]->inpop_found_list[0]['prod_name'];
                     $this->vtmin_create_text_error_message($i);
                     break; //Error Message Processing *Complete* for this Rule
 
                 default:  // 'groups' or 'cart' or 'vargroup'                                                 
                    
                    if ( $vtmin_rules_set[$i]->inpop_selection  == 'groups' ) {
                    
                      //BEGIN Get Category Names for rule (groups only)
                      $this->vtmin_init_cat_work_elements($i); 
                      
                      if ( ( sizeof($vtmin_rules_set[$i]->prodcat_in_checked) > 0 )  && ($vtmin_setup_options['show_prodcat_names_in_errmsg'] == 'yes' ) ) {  
                        foreach ($vtmin_rules_set[$i]->prodcat_in_checked as $cat_id) { 
                            $cat_info = get_term_by('id', $cat_id, $vtmin_info['parent_plugin_taxonomy'] ) ;
                            If ($cat_info) {
                               $vtmin_rules_set[$i]->errProds_cat_names [] = $cat_info->name;
                            }
                        }
                      }                  
                      if ( ( sizeof($vtmin_rules_set[$i]->rulecat_in_checked) > 0 ) && ($vtmin_setup_options['show_rulecat_names_in_errmsg'] == 'yes' ) ) {  
                        foreach ($vtmin_rules_set[$i]->rulecat_in_checked as $cat_id) { 
                          $cat_info = get_term_by('id', $cat_id, $vtmin_info['rulecat_taxonomy'] ) ;
                          If ($cat_info) {
                             $vtmin_rules_set[$i]->errProds_cat_names [] = $cat_info->name;
                          }
                        }
                      } 
                      //End Category Name Processing (groups only)
                    } 
                    
                    //PROCESS all ERROR products for rule
                    $sizeof_inpop_found_list = sizeof($vtmin_rules_set[$i]->inpop_found_list);
                    for($k=0; $k < $sizeof_inpop_found_list; $k++) {
                      if ($vtmin_rules_set[$i]->inpop_found_list[$k]['prod_requires_action'] == 'yes'){
                        //aggregate totals and add name into list
                        $vtmin_rules_set[$i]->errProds_qty         += $vtmin_rules_set[$i]->inpop_found_list[$k]['prod_qty'];
                        $vtmin_rules_set[$i]->errProds_total_price += $vtmin_rules_set[$i]->inpop_found_list[$k]['prod_total_price'];
                        $vtmin_rules_set[$i]->errProds_ids []       = $vtmin_rules_set[$i]->inpop_found_list[0]['prod_id'];
                        $vtmin_rules_set[$i]->errProds_names []     = $vtmin_rules_set[$i]->inpop_found_list[$k]['prod_name'];
                        
                      
                        switch( $vtmin_rules_set[$i]->specChoice_in_selection ) {
                          case 'all':
                              //Don't create a message now,message applies to the whole population, wait until 'for' loop completes to print
                            break;
                          default:  // 'each' and 'any'
                              //message applies to each product as setup in previous processing
                              $this->vtmin_create_text_error_message($i); 
                              //clear out errProds work elements
                              $this->vtmin_init_recursive_work_elements($i);                            
                            break;
                        }  
                                     
                      }
                    }
                    
                    if ( $vtmin_rules_set[$i]->specChoice_in_selection == 'all' ) {      
                       $this->vtmin_create_text_error_message($i);
                    }    
                       
                 break;       
              }  //end messaging
              
              break; 
            } //end proccessing for this rule
            
                           
        }    
    }   //end rule processing
   
    
    //Show error messages in table format, if desired and needed.
    if ( ( $vtmin_setup_options['show_error_messages_in_table_form'] == 'yes' ) && ($vtmin_info['error_message_needed'] == 'yes') ) {
       $this->vtmin_create_table_error_message();
    }
        
    if ( $vtmin_setup_options['debugging_mode_on'] == 'yes' ){         
      global $woocommerce; 
      error_log( print_r(  '$vtmin_info', true ) );
      error_log( var_export($vtmin_info, true ) );
      error_log( print_r(  '$vtmin_rules_set', true ) );
      error_log( var_export($vtmin_rules_set, true ) );
      error_log( print_r(  '$vtmin_cart', true ) );
      error_log( var_export($vtmin_cart, true ) );
      error_log( print_r(  '$vtmin_setup_options', true ) );
      error_log( var_export($vtmin_setup_options, true ) );
      error_log( print_r(  '$woocommerce', true ) );
      error_log( var_export($woocommerce, true ) );     
    }  
    
     
  }  //end vtmin_minimum_purchase_check
  
   
   
   
        
  public function vtmin_create_table_error_message () { 
      global $vtmin_setup_options, $vtmin_cart, $vtmin_rules_set, $vtmin_rule, $vtmin_info; 
      
      $vtmin_info['line_cnt']++; //line count used in producing height parameter when messages sent to js.
      
      $vtmin_info['cart_color_cnt'] = 0;
      
      $rule_id_list = ' ';
      
      $cart_count = sizeof($vtmin_cart->cart_items);
      
      $message = __('<span id="table-error-messages">', 'vtmin');
      
      $sizeof_rules_set = sizeof($vtmin_rules_set);
      for($i=0; $i < $sizeof_rules_set; $i++) {               
        if ( $vtmin_rules_set[$i]->rule_requires_cart_action == 'yes' ) { 
          
          //v1.08 begin
          if ( $vtmin_rules_set[$i]->custMsg_text > ' ') { //custom msg override              
              /*
              ==>> text error msg function always executed, so msg already loaded there - don't load here
              $vtmin_cart->error_messages[] = array (
                'msg_from_this_rule_id' => $vtmin_rules_set[$i]->post_id, 
                'msg_from_this_rule_occurrence' => $i, 
                'msg_text'  => $vtmin_rules_set[$i]->custMsg_text,
                'msg_is_custom'   => 'yes' 
              );
              $this->vtmin_set_custom_msgs_status ('customMsg');
              */
              continue;
           }           
          //v1.08 end         
        
          switch ( $vtmin_rules_set[$i]->specChoice_in_selection ) {
            case  'all' :
                 $vtmin_info['action_cnt'] = 0;
                 $sizeof_inpop_found_list = sizeof($vtmin_rules_set[$i]->inpop_found_list);
                 for($k=0; $k < $sizeof_inpop_found_list; $k++) { 
                    if ($vtmin_rules_set[$i]->inpop_found_list[$k]['prod_requires_action'] == 'yes'){
                       $vtmin_info['action_cnt']++;
                    }
                 }
                switch (true) {
                  case ( ( $vtmin_rules_set[$i]->inpop_selection == ('cart' || 'groups' || 'vargroup') ) && ( $vtmin_info['action_cnt'] > 1 ) ) : 
                      //this rule = whole cart                      
                      $vtmin_info['bold_the_error_amt_on_detail_line'] = 'no';
                      $message .= $this->vtmin_table_detail_lines_cntl($i);   
                      $message .= $this->vtmin_table_totals_line($i);
                      $message .= $this->vtmin_table_text_line($i);
                    break;

                  case $vtmin_info['action_cnt'] == 1 :
                      $vtmin_info['bold_the_error_amt_on_detail_line'] = 'yes';
                      $message .= $this->vtmin_table_detail_lines_cntl($i);
                      $message .= $this->vtmin_table_text_line($i);
                    break;
                } 
              break;
            case  'each' :
                $vtmin_info['bold_the_error_amt_on_detail_line'] = 'yes';
                $message .= $this->vtmin_table_detail_lines_cntl($i);
                $message .= $this->vtmin_table_text_line($i);
              break;
            case  'any' :
                $vtmin_info['bold_the_error_amt_on_detail_line'] = 'yes';
                $message .= $this->vtmin_table_detail_lines_cntl($i);
                $message .= $this->vtmin_table_text_line($i);
              break;
          
          } 
          $message .= __('<br /><br />', 'vtmin');  //empty line between groups
        }
        
        //new color for next rule
        $vtmin_info['cart_color_cnt']++; 
      } 
    
      //close up owning span
      $message .= __('</span>', 'vtmin'); //end "table-error-messages"
            
      $vtmin_cart->error_messages[] = array (
        'msg_from_this_rule_id' => $rule_id_list, 
        'msg_from_this_rule_occurrence' => '', 
        'msg_text'  => $message,
        'msg_is_custom'   => 'no'    //v1.08 
      );       
      $this->vtmin_set_custom_msgs_status ('standardMsg');     //v1.08 
       
  } 
  
  
        
   public function vtmin_table_detail_lines_cntl ($i) {
      global $vtmin_setup_options, $vtmin_cart, $vtmin_rules_set, $vtmin_rule, $vtmin_info;
      
      $message_details = $this->vtmin_table_titles();
      
      $sizeof_inpop_found_list = sizeof($vtmin_rules_set[$i]->inpop_found_list);
      //Version 1.01  new IF structure  replaced straight 'for' loop
      if ( $vtmin_rules_set[$i]->specChoice_in_selection == 'all' ) {
         for($r=0; $r < $sizeof_inpop_found_list; $r++) { 
            $k = $vtmin_rules_set[$i]->inpop_found_list[$r]['prod_id_cart_occurrence'];
            $message_details .= $this->vtmin_table_line ($i, $k);  
          }
      } else {    // each or any
        for($r=0; $r < $sizeof_inpop_found_list; $r++) { 
            if ($vtmin_rules_set[$i]->inpop_found_list[$r]['prod_requires_action'] == 'yes'){
              $k = $vtmin_rules_set[$i]->inpop_found_list[$r]['prod_id_cart_occurrence'];
              $message_details .= $this->vtmin_table_line ($i, $k);
           }  
        }
      }
      
      return $message_details;
   }
        
   public function vtmin_table_line ($i, $k){
      global $vtmin_setup_options, $vtmin_cart, $vtmin_rules_set, $vtmin_rule, $vtmin_info;
     
     
     $message_line;
     $vtmin_info['line_cnt']++;
       
     $message_line .= __('<span class="table-msg-line">', 'vtmin');
     $message_line .= __('<span class="product-column color-grp', 'vtmin');  //mwnt     removed product   woo conflict
     $message_line .= $vtmin_info['cart_color_cnt'];  //append the count which corresponds to a css color...
     $message_line .= __('">', 'vtmin');
     $message_line .= $vtmin_cart->cart_items[$k]->product_name;
     $message_line .= __('</span>', 'vtmin'); //end "product" end "color-grp"
     
     if ($vtmin_rules_set[$i]->amtSelected_selection == 'quantity')   {
        $message_line .= __('<span class="quantity-column color-grp', 'vtmin');      //mwnt     removed quantity, woo conflict
        $message_line .= $vtmin_info['cart_color_cnt'];  //append the count which corresponds to a css color...
        if ( $vtmin_info['bold_the_error_amt_on_detail_line'] == 'yes') {
           $message_line .= __(' bold-this', 'vtmin');
        }
        $message_line .= __('">', 'vtmin');
      } else {
        $message_line .= __('<span class="quantity-column">', 'vtmin');   // mwnt     removed quantity, woo conflict
      }
     $message_line .= $vtmin_cart->cart_items[$k]->quantity;
     if ( ($vtmin_rules_set[$i]->amtSelected_selection == 'quantity') && ($vtmin_info['bold_the_error_amt_on_detail_line'] == 'yes') ) {
       $message_line .= __(' &nbsp;(Error)', 'vtmin');
     }
     $message_line .= __('</span>', 'vtmin'); //end "quantity" end "color-grp"
     
     $message_line .= __('<span class="price-column">', 'vtmin');//mwnt     removed price
     $message_line .= vtmin_format_money_element($vtmin_cart->cart_items[$k]->unit_price);
     $message_line .= __('</span>', 'vtmin'); //end "price"
     
     if ($vtmin_rules_set[$i]->amtSelected_selection == 'currency')   {
        $message_line .= __('<span class="total-column color-grp', 'vtmin');   // mwnt     removed total , woo conflict
        $message_line .= $vtmin_info['cart_color_cnt'];
        if ( $vtmin_info['bold_the_error_amt_on_detail_line'] == 'yes') {
           $message_line .= __(' bold-this', 'vtmin');
        }
        $message_line .= __('">', 'vtmin');
      } else {
        $message_line .= __('<span class="total-column">', 'vtmin');     // mwnt     removed total , woo conflict
      }
     //$message_line .= $vtmin_cart->cart_items[$k]->total_price;
     $message_line .= vtmin_format_money_element($vtmin_cart->cart_items[$k]->total_price);
     if ( ($vtmin_rules_set[$i]->amtSelected_selection == 'currency') && ($vtmin_info['bold_the_error_amt_on_detail_line'] == 'yes') ) {
       $message_line .= __(' &nbsp;(Error)', 'vtmin');
     }     
     $message_line .= __('</span>', 'vtmin'); //end "total-column"  end "color-grp"
     $message_line .= __('</span>', 'vtmin'); //end "table-msg-line"
     
     //keep a running total
     $vtmin_info['cart_grp_info']['qty']   += $vtmin_cart->cart_items[$k]->quantity; 
     $vtmin_info['cart_grp_info']['price'] += $vtmin_cart->cart_items[$k]->total_price; 
     
     return  $message_line;
   }
   
         
   public function vtmin_table_totals_line ($i){
      global $vtmin_setup_options, $vtmin_cart, $vtmin_rules_set, $vtmin_rule, $vtmin_info;
      
     $message_totals;
     $vtmin_info['line_cnt']++;
      
     $message_totals .= __('<span class="table-totals-line">', 'vtmin');
     $message_totals .= __('<span class="product-column">', 'vtmin');
     $message_totals .= __('&nbsp;', 'vtmin');
     $message_totals .= __('</span>', 'vtmin'); //end "product"
     
     if ($vtmin_rules_set[$i]->amtSelected_selection == 'quantity')   {
        $message_totals .= __('<span class="quantity-column quantity-column-total color-grp', 'vtmin');
        $message_totals .= $vtmin_info['cart_color_cnt'];
        $message_totals .= __('">(', 'vtmin');
        //grp total qty
        $message_totals .= $vtmin_info['cart_grp_info']['qty'];
        $message_totals .= __(') Error', 'vtmin');
      } else {
        $message_totals .= __('<span class="quantity-column">', 'vtmin');
        $message_totals .= __('&nbsp;', 'vtmin');                                                                                    
      }     
     $message_totals .= __('</span>', 'vtmin'); //end "quantity" "color-grp"
     
     $message_totals .= __('<span class="price-column">', 'vtmin'); //mwnt     removed price
     $message_totals .= __('&nbsp;', 'vtmin');
     $message_totals .= __('</span>', 'vtmin'); //end "price"
     
     if ($vtmin_rules_set[$i]->amtSelected_selection == 'currency')   {
        $message_totals .= __('<span class="quantity-column total-column-total color-grp', 'vtmin');
        $message_totals .= $vtmin_info['cart_color_cnt'];
        $message_totals .= __('">(', 'vtmin');
        //grp total price
        $message_totals .= vtmin_format_money_element($vtmin_info['cart_grp_info']['price']);
        $message_totals .= __(') Error', 'vtmin'); 
      } else {
        $message_totals .= __('<span class="quantity-column ">', 'vtmin');    // mwnt     removed total , woo conflict
        $message_totals .= __('&nbsp;', 'vtmin');
      }
     $message_totals .= __('</span>', 'vtmin'); //end "total" "color-grp"
     $message_totals .= __('</span>', 'vtmin'); //end "table-totals-line"
     
     return $message_totals;
   }
   
   public function vtmin_table_titles() {
     global $vtmin_info;
     $message_title;       
          $message_title  .= __('<span class="table-titles">', 'vtmin');
             $message_title .= __('<span class="product-column product-column-title">Product:</span>', 'vtmin');
             $message_title .= __('<span class="quantity-column quantity-column-title">Quantity:</span>', 'vtmin');
             $message_title .= __('<span class="price-column price-column-title">Price:</span>', 'vtmin');
             $message_title .= __('<span class="total-column total-column-title">Total:</span>', 'vtmin');           
          $message_title .= __('</span>', 'vtmin'); //end "table-titles"
        
      $this->vtmin_init_grp_info();
      
      return $message_title;
   }
   
   public function vtmin_init_grp_info() {
     global $vtmin_info;
     $vtmin_info['cart_grp_info'] = array( 'qty'    => 0,
                                           'price'    => 0
                                          );
   }

           
   public function vtmin_table_text_line ($i){
      global $vtmin_setup_options, $vtmin_cart, $vtmin_rules_set, $vtmin_rule, $vtmin_info;
      
      $message_text;
      $vtmin_info['line_cnt']++;
     
       //SHOW TARGET MIN $/QTY AND CURRENTLY REACHED TOTAL
      
      $message_text .= __('<span class="table-error-msg"><span class="bold-this color-grp', 'vtmin');
      $message_text .= $vtmin_info['cart_color_cnt'];  //append the count which corresponds to a css color...
      $message_text .= __('">', 'vtmin');
      $message_text .= __('Error => ', 'vtmin');
      $message_text .= __('</span>Minimum Purchase ', 'vtmin');  //end "color-grp"
      
      
      if ($vtmin_rules_set[$i]->amtSelected_selection == 'currency') {
        if ( $vtmin_rules_set[$i]->specChoice_in_selection == 'all' ) {
          $message_text .= __('total', 'vtmin');
        }
      } else {
        $message_text .= __(' <span class="color-grp', 'vtmin');
        $message_text .= $vtmin_info['cart_color_cnt'];  //append the count which corresponds to a css color...
        $message_text .= __('">', 'vtmin');
        $message_text .= __('quantity</span>', 'vtmin');    //end "color-grp"
      }
      $message_text .= __(' of <span class="color-grp', 'vtmin'); 
      $message_text .= $vtmin_info['cart_color_cnt'];  //append the count which corresponds to a css color...
      $message_text .= __('">', 'vtmin');
      
      if ($vtmin_rules_set[$i]->amtSelected_selection == 'currency') {
        $message_text .= vtmin_format_money_element($vtmin_rules_set[$i]->minimum_amt['value']);
        $message_text .= __('</span> required ', 'vtmin');     //if branch end "color-grp"
      } else {
        $message_text .= $vtmin_rules_set[$i]->minimum_amt['value']; 
        $message_text .= __(' </span>units required  ', 'vtmin');    //if branch end "color-grp"
      } 
      
      switch( $vtmin_rules_set[$i]->inpop_selection ) {      
         case 'single' : 
            $message_text .= __('for this product.', 'vtmin');
            break;
         case 'vargroup' :                                              //mwnt begin
            switch( $vtmin_rules_set[$i]->specChoice_in_selection ) {
                case 'all': 
                    $message_text .= __('for this group.', 'vtmin');
                  break;
                case 'each':
                    $message_text .= __('for each product within the group.', 'vtmin');                             
                  break;
                case 'any':
                    $message_text .= __('for the first ', 'vtmin');
                    $message_text .= __('<span class="color-grp', 'vtmin');
                    $message_text .= $vtmin_info['cart_color_cnt'];  //append the count which corresponds to a css color...
                    $message_text .= __('">', 'vtmin'); 
                    $message_text .= $vtmin_rules_set[$i]->anyChoice_max['value']; 
                    $message_text .= __(' </span>product(s) found within the product group.', 'vtmin');   //end "color-grp"
                                               
                  break;
              }                                             //mwnt end
            break;
         case  'groups' :
             switch( $vtmin_rules_set[$i]->specChoice_in_selection ) {
                case 'all': 
                    $message_text .= __('for this group.', 'vtmin');
                  break;
                case 'each':
                    $message_text .= __('for each product within the group.', 'vtmin');  //mwnt                           
                  break;
                case 'any':
                    $message_text .= __('for the first ', 'vtmin');
                    $message_text .= __('<span class="color-grp', 'vtmin');
                    $message_text .= $vtmin_info['cart_color_cnt'];  //append the count which corresponds to a css color...
                    $message_text .= __('">', 'vtmin'); 
                    $message_text .= $vtmin_rules_set[$i]->anyChoice_max['value']; 
                    $message_text .= __(' </span>product(s) found within the product group.', 'vtmin');   //end "color-grp"
                                               
                  break;
              }
            break;
         case  'cart' : 
             switch( $vtmin_rules_set[$i]->specChoice_in_selection ) {
                case 'all': 
                    $message_text .= __('for the cart.', 'vtmin');
                  break;
                case 'each':
                    $message_text .= __('for each product the cart.', 'vtmin');   //mwnt                          
                  break;
                case 'any':
                    $message_text .= __('for the first ', 'vtmin');
                    $message_text .= __('<span class="color-grp', 'vtmin');
                    $message_text .= $vtmin_info['cart_color_cnt'];  //append the count which corresponds to a css color...
                    $message_text .= __('">', 'vtmin'); 
                    $message_text .= $vtmin_rules_set[$i]->anyChoice_max['value']; 
                    $message_text .= __(' </span>product(s) found within the cart.', 'vtmin');  //end "color-grp"                            
                  break;
              }
            break;
      }
      
      //show rule id in error msg      
      if ( ( $vtmin_setup_options['show_rule_ID_in_errmsg'] == 'yes' ) ||  ( $vtmin_setup_options['debugging_mode_on'] == 'yes' ) ) {
        $message_text .= __('<span class="rule-id"> (Rule ID = ', 'vtmin');
        $message_text .= $vtmin_rules_set[$i]->post_id;
        $message_text .= __(') </span>', 'vtmin');
      }
      
          
      $message_text .= __('</span>', 'vtmin'); //end "table-error-msg"  

    
     //SHOW CATEGORIES TO WHICH THIS MSG APPLIES IN GENERAL, IF RELEVANT
      if ( ( $vtmin_rules_set[$i]->inpop_selection <> 'single'  ) && ( sizeof($vtmin_rules_set[$i]->errProds_cat_names) > 0 ) ) {
        $vtmin_info['line_cnt']++;
        $message_text .= __('<span class="table-text-line">', 'vtmin');
        $vtmin_rules_set[$i]->errProds_size = sizeof($vtmin_rules_set[$i]->errProds_cat_names);
        $message_text .= __('<span class="table-text-cats">The minimum purchase rule applies to any products in the following categories: </span><span class="black-font-italic">', 'vtmin');
        for($k=0; $k < $vtmin_rules_set[$i]->errProds_size; $k++) {
            $message_text .= __(' "', 'vtmin');
            $message_text .= $vtmin_rules_set[$i]->errProds_cat_names[$k];
            $message_text .= __('" ', 'vtmin');  
        }        
        $message_text .= __('</span>', 'vtmin');  //end "table-text-cats"
        $message_text .= __('</span>', 'vtmin');  //end "table-text-line"
      } 
        
      return $message_text;     
   }
  
        
   public function vtmin_create_text_error_message ($i) { 
     global $vtmin_setup_options, $vtmin_cart, $vtmin_rules_set, $vtmin_rule, $vtmin_info; 
     
     $vtmin_rules_set[$i]->rule_requires_cart_action = 'yes';
          
      //v1.08 begin
      if ( $vtmin_rules_set[$i]->custMsg_text > ' ') { //custom msg override              
          $vtmin_cart->error_messages[] = array (
            'msg_from_this_rule_id' => $vtmin_rules_set[$i]->post_id, 
            'msg_from_this_rule_occurrence' => $i, 
            'msg_text'  => $vtmin_rules_set[$i]->custMsg_text,
            'msg_is_custom'   => 'yes' 
          );
          $this->vtmin_set_custom_msgs_status('customMsg'); 
          return;
       }           
      //v1.08 end
             
     if  ( $vtmin_setup_options['show_error_messages_in_table_form'] == 'yes' ) {
        $vtmin_info['error_message_needed'] = 'yes';
        //   $vtmin_cart->error_messages[] = array ('msg_from_this_rule_id' => $vtmin_rules_set[$i]->post_id, 'msg_from_this_rule_occurrence' => $i,'msg_text'  => '' );  
     } else {     
        //SHOW PRODUCT NAME(S) IN ERROR
        $message; //initialize $message
        switch( $vtmin_rules_set[$i]->inpop_selection ) {  
          case 'cart':
              $message .= __('<span class="errmsg-begin">Minimum Purchase Required -</span> for ', 'vtmin');  //mwnt
              switch( $vtmin_rules_set[$i]->specChoice_in_selection ) {
                case 'all': 
                    //$message .= __('all', 'vtmin');
                  break;
                case 'each':
                    $message .= __('each of', 'vtmin');                             
                  break;
                case 'any':
                    $message .= __('each of', 'vtmin');                             
                  break;
              } 
              $message .= __(' the product(s) in this group: <span class="red-font-italic">', 'vtmin');    //mwnt
              $message .= $this->vtmin_list_out_product_names($i);
              $message .= __('</span>', 'vtmin'); 
            break;
          case 'groups':                    
              $message .= __('<span class="errmsg-begin">Minimum Purchase Required -</span> for ', 'vtmin');  //mwnt
              // mwnt begin
              switch( $vtmin_rules_set[$i]->specChoice_in_selection ) {
                case 'all': 
                    $message .= __(' the products in this group: <span class="red-font-italic">', 'vtmin');
                  break;
                default:
                    $message .= __(' this product: <span class="red-font-italic">', 'vtmin');;                             
                  break;
              }
              //mwnt end
              // $message .= __(' the products in this group: <span class="red-font-italic">', 'vtmin');     //mwnt
              $message .= $this->vtmin_list_out_product_names($i);
              $message .= __('</span>', 'vtmin'); 
            break;
          case 'vargroup':
              $message .= __('<span class="errmsg-begin">Minimum Purchase Required -</span> for ', 'vtmin'); //mwnt
              switch( $vtmin_rules_set[$i]->specChoice_in_selection ) {
                case 'all': 
                    //$message .= __('all', 'vtmin');
                  break;
                case 'each':
                    $message .= __('each of', 'vtmin');                             
                  break;
                case 'any':
                    $message .= __('each of', 'vtmin');                             
                  break;
              }
              $message .= __(' the products in this group: <span class="red-font-italic">', 'vtmin'); //mwnt
              $message .= $this->vtmin_list_out_product_names($i);
              $message .= __('</span>', 'vtmin'); 
            break;
          case 'single':
              $message .= __('For this product: <span class="red-font-italic">"', 'vtmin');   //mwnt
              $message .= $vtmin_rules_set[$i]->errProds_names [0];
              $message .= __('"</span>  ', 'vtmin');
            break;
        }                    
                        
        //SHOW TARGET MIN $/QTY AND CURRENTLY REACHED TOTAL
        if ($vtmin_rules_set[$i]->amtSelected_selection == 'currency')   {
          $message .= __('<br /><span class="errmsg-text">A minimum of &nbsp;<span class="errmsg-amt-required"> ', 'vtmin');    //mwnt
          $message .= vtmin_format_money_element( $vtmin_rules_set[$i]->minimum_amt['value'] );
          // $message .= $vtmin_rules_set[$i]->minimum_amt['value']; mwnt
          // $message .= __(' total dollars</span> must be purchased.  The current total ', 'vtmin'); mwnt
          switch( $vtmin_rules_set[$i]->specChoice_in_selection ) {
            case 'all': 
                $message .= __('</span> &nbsp;for the total group must be purchased.  The current total ', 'vtmin');   //mwnt
                $message .= __('for all the products ', 'vtmin'); //$message .= __('for the products ', 'vtmin');  mwnt
                $message .= __('in the group is: <span class="errmsg-amt-current"> ', 'vtmin'); //mwnt
              break;
            default: //each or any
                $message .= __('</span> &nbsp;for this product must be purchased.  The current total ', 'vtmin'); //mwnt
                $message .= __('for this product is: ', 'vtmin');  //$message .= __('for this product ', 'vtmin');  mwnt                          
              break;
           //case 'any':
           //     $message .= __(' dollars</span> &nbsp;for each product in the group must be purchased.  The current total ', 'vtmin'); //mwnt
            //    $message .= __('for each product ', 'vtmin');  //$message .= __('for this product ', 'vtmin');  mwnt                              
            //  break;
          }
          //$message .= __('in the group is: <span class="errmsg-amt-current"> ', 'vtmin'); //mwnt
          $message .= vtmin_format_money_element( $vtmin_rules_set[$i]->errProds_total_price ); //mwnt
          $message .= __(' </span></span> ', 'vtmax'); //mwnt
          //mwnt begin
          //$message .= $vtmin_rules_set[$i]->errProds_total_price;
          //if ($vtmin_rules_set[$i]->errProds_total_price > 1 ) {
          //  $message .= __(' dollars.</span></span> ', 'vtmin');
          //} else {
          //  $message .= __(' dollar.</span></span> ', 'vtmin');
          //}  mwnt end
          
        } else {
          $message .= __('<br /><span class="errmsg-text">A minimum quantity of &nbsp;<span class="errmsg-amt-required"> ', 'vtmin');  //mwnt
          $message .= $vtmin_rules_set[$i]->minimum_amt['value'];
          //$message .= __(' total units</span> must be purchased.  The current total ', 'vtmin');   mwnt
          switch( $vtmin_rules_set[$i]->specChoice_in_selection ) {
            case 'all': 
                $message .= __(' units</span> &nbsp;&nbsp;for the total group must be purchased.  The current total ', 'vtmin');   //mwnt
                $message .= __('for all the products ', 'vtmin'); //$message .= __('for the products ', 'vtmin');  mwnt
                $message .= __('in the group is: <span class="errmsg-amt-current"> ', 'vtmin'); //mwnt
              break;
            default:  //each or any
                $message .= __(' units</span> &nbsp;&nbsp;for each product in the group must be purchased.  The current total ', 'vtmin'); //mwnt
                $message .= __('for this product is: ', 'vtmin');  //$message .= __('for this product ', 'vtmin');  mwnt                          
              break;
          //  case 'any':
          //      $message .= __(' units</span> &nbsp;&nbsp;for each product in the group must be purchased.  The current total ', 'vtmin'); //mwnt
          //      $message .= __('for each product ', 'vtmin');  //$message .= __('for this product ', 'vtmin');  mwnt                              
          //    break;
          }
          //$message .= __('in the group is: <span class="errmsg-amt-current"> ', 'vtmin'); //mwnt
          $message .= $vtmin_rules_set[$i]->errProds_qty;
          if ($vtmin_rules_set[$i]->errProds_qty > 1) {
            $message .= __(' units.</span></span> ', 'vtmin');
          } else {
            $message .= __(' unit.</span></span> ', 'vtmin');
          }
        }
                                                       
      
        //show rule id in error msg      
        if ( ( $vtmin_setup_options['show_rule_ID_in_errmsg'] == 'yes' ) ||  ( $vtmin_setup_options['debugging_mode_on'] == 'yes' ) ) {
          $message .= __('<span class="rule-id"> (Rule ID = ', 'vtmin');
          $message .= $vtmin_rules_set[$i]->post_id;
          $message .= __(') </span>', 'vtmin');
        }
  
        //SHOW CATEGORIES TO WHICH THIS MSG APPLIES IN GENERAL, IF RELEVANT
        if ( ( $vtmin_rules_set[$i]->inpop_selection <> 'single'  ) && ( sizeof($vtmin_rules_set[$i]->errProds_cat_names) > 0 ) ) {
          $vtmin_rules_set[$i]->errProds_size = sizeof($vtmin_rules_set[$i]->errProds_cat_names);
          $message .= __('<br />:: <span class="black-font">The minimum purchase rule applies to any products in the following categories: </span><span class="black-font-italic">', 'vtmin');
          for($k=0; $k < $vtmin_rules_set[$i]->errProds_size; $k++) {
              $message .= __(' "', 'vtmin');
              $message .= $vtmin_rules_set[$i]->errProds_cat_names[$k];
              $message .= __('" ', 'vtmin');
           //   if ( $k < $vtmin_rules_set[$i]->errProds_size ) {        //mwnt
              $message .= __('</span>', 'vtmin');
           //   }
          }
          //$message .= __('" ', 'vtmin');        //mwnt
        }
                                
        //queue the message to go back to the screen     
        $vtmin_cart->error_messages[] = array (
            'msg_from_this_rule_id' => $vtmin_rules_set[$i]->post_id,  
            'msg_from_this_rule_occurrence' => $i, 
            'msg_text'  => $message,
            'msg_is_custom'   => 'no'    //v1.08 
          );         
        $this->vtmin_set_custom_msgs_status ('standardMsg');     //v1.08 
       
      }  //end text message formatting
      /*
      if ( $vtmin_setup_options['debugging_mode_on'] == 'yes' ){   
        echo '$message'; echo '<pre>'.print_r($message, true).'</pre>' ;
        echo '$vtmin_rules_set[$i]->errProds_qty = '; echo '<pre>'.print_r($vtmin_rules_set[$i]->errProds_qty, true).'</pre>' ;
        echo '$vtmin_rules_set[$i]->errProds_total_price = ' ; echo '<pre>'.print_r($vtmin_rules_set[$i]->errProds_total_price, true).'</pre>' ;
        echo '$vtmin_rules_set[$i]->errProds_names = '; echo '<pre>'.print_r($vtmin_rules_set[$i]->errProds_names, true).'</pre>' ;
        echo '$vtmin_rules_set[$i]->errProds_cat_names = '; echo '<pre>'.print_r($vtmin_rules_set[$i]->errProds_cat_names, true).'</pre>' ;   
      } 
      */
     
  } 

      
   //*************************************  
   //v1.08 new function 
   //*************************************    
   public function vtmin_set_custom_msgs_status ($message_state) { 
      global $vtmin_cart;
      switch( $vtmin_cart->error_messages_are_custom ) {  
        case 'all':
             if ($message_state == 'standardMsg') {
                $vtmin_cart->error_messages_are_custom = 'some';
             }
          break;
        case 'some':
          break;          
        case 'none':
             if ($message_state == 'customMsg') {
                $vtmin_cart->error_messages_are_custom = 'some';
             }
          break; 
        default:  //no state set yet
             if ($message_state == 'standardMsg') {
                $vtmin_cart->error_messages_are_custom = 'none';
             } else {
                $vtmin_cart->error_messages_are_custom = 'all';
             }
          break;                    
      }

      return;
   }      
   //v1.08 end
 
        
   public function vtmin_product_is_in_inpop_group ($i, $k) { 
      global $vtmin_cart, $vtmin_rules_set, $vtmin_rule, $vtmin_info, $vtmin_setup_options;
      /* at this point, the checked list produced at rule store time could be out of sync with the db, as the cats/roles originally selected to be
      *  part of this rule could have been deleted.  this won't affect these loops, as the deleted cats/roles will simply not be in the 
      *  'get_object_terms' list. */

      $vtmin_is_role_in_list  = $this->vtmin_is_role_in_list_test ($i, $k);
      $vtmin_are_cats_in_list = $this->vtmin_are_cats_in_list_test ($i, $k);
            
      if ( $vtmin_rules_set[$i]->role_and_or_in_selection == 'and' ) {
         if ($vtmin_is_role_in_list && $vtmin_are_cats_in_list) {
            return true;
         } else {
            return false;
         }
      }
      //otherwise this is an 'or' situation, and any participation = 'true'
      if ($vtmin_is_role_in_list || $vtmin_are_cats_in_list) {
         return true;
      }
      
      return false;
   } 
  
    public function vtmin_is_role_in_list_test ($i, $k) {
    	global $vtmin_cart, $vtmin_rules_set, $vtmin_rule, $vtmin_info, $vtmin_setup_options;     
      if ( sizeof($vtmin_rules_set[$i]->role_in_checked) > 0 ) {
            if (in_array($this->vtmin_get_current_user_role(), $vtmin_rules_set[$i]->role_in_checked )) {   //if role is in previously checked_list
                  /*
                  if ( $vtmin_setup_options['debugging_mode_on'] == 'yes' ){ 
                    echo 'current user role= <pre>'.print_r($this->vtmin_get_current_user_role(), true).'</pre>' ;
                    echo 'rule id= <pre>'.print_r($vtmin_rules_set[$i]->post_id, true).'</pre>' ;  
                    echo 'role_in_checked= <pre>'.print_r($vtmin_rules_set[$i]->role_in_checked, true).'</pre>' ; 
                    echo 'i= '.$i . '<br>'; echo 'k= '.$k . '<br>';
                  }
                  */
              return true;                                
            } 
      } 
      return false;
    }
    
    public function vtmin_are_cats_in_list_test ($i, $k) {
    	global $vtmin_cart, $vtmin_rules_set, $vtmin_rule, $vtmin_info, $vtmin_setup_options;     
      if ( ( sizeof($vtmin_cart->cart_items[$k]->prod_cat_list) > 0 ) && ( sizeof($vtmin_rules_set[$i]->prodcat_in_checked) > 0 ) ){   
        //$vtmin_cart->cart_items[$k]->prod_cat_list = wp_get_object_terms( $vtmin_cart->cart_items[$k]->product_id, $vtmin_info['parent_plugin_taxonomy'] );
        if ( array_intersect($vtmin_rules_set[$i]->prodcat_in_checked, $vtmin_cart->cart_items[$k]->prod_cat_list ) ) {   //if any in array1 are in array 2
            return true;                                                  
        }
      } 
      if ( ( sizeof($vtmin_cart->cart_items[$k]->rule_cat_list) > 0 ) && ( sizeof($vtmin_rules_set[$i]->rulecat_in_checked) > 0 ) ) {
       // $vtmin_cart->cart_items[$k]->rule_cat_list = wp_get_object_terms( $vtmin_cart->cart_items[$k]->product_id, $vtmin_info['rulecat_taxonomy'] );
        if ( array_intersect($vtmin_rules_set[$i]->rulecat_in_checked, $vtmin_cart->cart_items[$k]->rule_cat_list ) ) {   //if any in array1 are in array 2
            return true;
        }
      }
      return false;
    }    

    public function vtmin_get_current_user_role() {
    	global $current_user;     
    	$user_roles = $current_user->roles;
    	$user_role = array_shift($user_roles);
      if  ($user_role <= ' ') {
        $user_role = 'notLoggedIn';
      }      
    	return $user_role;
      }
      
    public function vtmin_list_out_product_names($i) {
      $prodnames;
    	global $vtmin_rules_set;     
    	for($p=0; $p < sizeof($vtmin_rules_set[$i]->errProds_names); $p++) {
          $prodnames .= __(' "', 'vtmin');
          $prodnames .= $vtmin_rules_set[$i]->errProds_names[$p];
          $prodnames .= __('"  ', 'vtmin');
      } 
    	return $prodnames;
    }
      
   public function vtmin_load_inpop_found_list($i, $k) {
    	global $vtmin_cart, $vtmin_rules_set;
      $vtmin_rules_set[$i]->inpop_found_list[] = array('prod_id' => $vtmin_cart->cart_items[$k]->product_id,
                                                       'prod_name' => $vtmin_cart->cart_items[$k]->product_name,
                                                       'prod_qty' => $vtmin_cart->cart_items[$k]->quantity, 
                                                       'prod_total_price' => $vtmin_cart->cart_items[$k]->total_price,
                                                       'prod_cat_list' => $vtmin_cart->cart_items[$k]->prod_cat_list,
                                                       'rule_cat_list' => $vtmin_cart->cart_items[$k]->rule_cat_list,
                                                       'prod_id_cart_occurrence' => $k, //used to mark product in cart if failed a rule
                                                       'prod_requires_action'  => '' 
                                                      );
     $vtmin_rules_set[$i]->inpop_qty_total   += $vtmin_cart->cart_items[$k]->quantity;
     $vtmin_rules_set[$i]->inpop_total_price += $vtmin_cart->cart_items[$k]->total_price;
   }
     
  public function vtmin_init_recursive_work_elements($i){ 
    global $vtmin_rules_set;
    $vtmin_rules_set[$i]->errProds_qty = 0 ;
    $vtmin_rules_set[$i]->errProds_total_price = 0 ;
    $vtmin_rules_set[$i]->errProds_ids = array() ;
    $vtmin_rules_set[$i]->errProds_names = array() ;    
  }
  public function vtmin_init_cat_work_elements($i){ 
    global $vtmin_rules_set;
    $vtmin_rules_set[$i]->errProds_cat_names = array() ;             
  }     

  public function vtmin_mark_product_as_requiring_cart_action($i,$k){ 
    global $vtmin_rules_set, $vtmin_cart;
    //mark the product in the rules_set
    $vtmin_rules_set[$i]->inpop_found_list[$k]['prod_requires_action'] = 'yes';
    $z = $vtmin_rules_set[$i]->inpop_found_list[$k]['prod_id_cart_occurrence'];
    //prepare for future rollout needs if a rule population conflict ensues
    $vtmin_cart->cart_items[$z]->product_participates_in_rule[] =  
        array(
          'post_id'            => $vtmin_rules_set[$i]->post_id,
          'inpop_selection'    => $vtmin_rules_set[$i]->inpop_selection, //needed to test for 'vargroup'
          'ruleset_occurrence' => $i,
          'inpop_occurrence'   => $k 
        ) ;           
  }     
  

} //end class


