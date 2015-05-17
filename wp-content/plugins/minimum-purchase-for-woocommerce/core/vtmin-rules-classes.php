<?php
 /*
   Rules are stored on the WP database as custom posts with custom field attributes.  At rule store/update
   time, a master rule option array is (re)created, to allow speedier access to rule information at
   product/cart processing time.
 */

class VTMIN_Rule {
	   public  $post_id;
     
     /*    RULE STATUS
     *   rule status = pending or publish  => 
     *        if status is 'pending', the rule will not be executed during cart processing
     *   rule status will be set to 'pending' 
     *      => when errors have been detected during update process
     *      => when the custom post type status has been changed to 'trash'              
     */
     public  $rule_status; 
     
     //candidate population, checked arrays
     public  $inpop;
     public  $inpop_selection;
     
     //single with variations
     public  $inpop_varProdID;
     public  $inpop_varProdID_name;
     public  $var_in_checked;
     
     //single product
     public  $inpop_singleProdID;
     public  $inpop_singleProdID_name;
            
     //group choices       
     public  $prodcat_in_checked;     
     public  $rulecat_in_checked;   
     public  $role_in_checked;
     public  $role_and_or_in;
     public  $role_and_or_in_selection;    
 //    public  $inpop_group_is_based_on;  //prodcat / rulecat / role / multi / null
    
     //candidate population handling
     public  $specChoice_in;
     public  $specChoice_in_selection;
     public  $anyChoice_max;
     
     //minimum amount
     public  $amtSelected;
     public  $amtSelected_selection;
     public  $minimum_amt;
     
     //v1.08 begin
     //custom messaging      
     public  $custMsg_text;
     //v1.08 end 
     public  $repeatingGroups; //v1.09.6    
          
     /*********************
     * error messages during admin rule creation - if error message, 
     *      overall rule status is pending, 
     *           ie inactive relative to ecommerce purchases
     *********************    */
     public  $rule_error_message;
     //*********************
      
     //******************************************
     //temp data loaded only at rule processing time, not retained in storage
     //******************************************
     public  $inpop_found_list;
     public  $inpop_qty_total;
     public  $inpop_total_price;
     public  $rule_requires_cart_action;  // yes=apply rule, no=skip
     public  $errProds_qty;
     public  $errProds_total_price;
     public  $errProds_ids;
     public  $errProds_names;
     public  $errProds_cat_names;
     //******************************************
   
	public function __construct(){
  
     $this->post_id = ' ';  //id of custom post rule
     $this->rule_status = ' ';  //pending or publish
     $this->inpop =  array (
        array(  
            'id'    => 'cartChoice',
            'class'  => '',
            'type'   => 'radio',
            'name'    => 'popChoice',  
            'value'  => 'cart', //checked, selected, contents, etc 
            'label'  =>  __(' Apply to all Products in the Cart', 'vtmin'), 
            'user_input'  => ''  //checked, selected, contents, etc 
        ) , 
        array(  
            'id'    => 'groupChoice',
            'class'  => '',
            'type'   => 'radio',
            'name'    => 'popChoice',  
            'value'  => 'groups', //checked, selected, contents, etc 
            'label'  => __( ' Use Selection Groups', 'vtmin'), 
            'user_input'  => '' //checked, selected, contents, etc 
        ) ,
         array(  
            'id'    => 'varChoice',
            'class'  => '',
            'type'   => 'radio',
            'name'    => 'popChoice',  
            'value'  => 'vargroup', //checked, selected, contents, etc 
            'label'  =>  __(' Single Product with Variations', 'vtmin'), 
            'user_input'  => ''  //checked, selected, contents, etc 
        ),
         array(  
            'id'    => 'singleChoice',
            'class'  => '',
            'type'   => 'radio',
            'name'    => 'popChoice',  
            'value'  => 'single', //checked, selected, contents, etc 
            'label'  =>  __(' Single Product Only', 'vtmin'), 
            'user_input'  => ''  //checked, selected, contents, etc 
        )  
      );
      $this->inpop_selection; // cart or single or groups
            
      $this->inpop_varProdID = array (     
        'id'    => 'inVarProdID',
        'class'  => 'text',
        'type'  => 'text',
        'name'  => 'inVarProdID',  
        'value'  => ''                     
      );
      $this->inpop_varProdID_name;
      $this->var_in_checked;
      
      $this->inpop_singleProdID = array (     
        'id'    => 'singleProdID',
        'class'  => 'text',
        'type'  => 'text',
        'name'  => 'singleProdID',  
        'value'  => ''                     
      );
      $this->inpop_singleProdID_name;
          
      $this->prodcat_in_checked;
      $this->rulecat_in_checked;
      $this->role_in_checked;       
      $this->role_and_or_in =  array ( //role and/or as combined with cats
        array(  
            'id'    => 'andChoice',
            'class'  => '',
            'type'   => 'radio',
            'name'    => 'andorChoice',  
            'value'  => 'and', //checked, selected, contents, etc 
            'label'  =>  __(' And', 'vtmin'), 
            'user_input'  => ''  //checked, selected, contents, etc 
        ) , 
         array(  
            'id'    => 'orChoice',
            'class'  => '',
            'type'   => 'radio',
            'name'    => 'andorChoice',  
            'value'  => 'or', //checked, selected, contents, etc 
            'label'  =>  __(' Or', 'vtmin'), 
            'user_input'  => ''  //checked, selected, contents, etc 
        )  
      );
      $this->role_and_or_in_selection; //and/or
      

  //    $this->inpop_group_is_based_on;
      //don't need a 'role_in_checked_name' array, as there can only ever be 1 per user, and the user role name will not be used in the error message. 
      $this->specChoice_in =  array (   
        array(  
            'id'    => 'allChoice',
            'class'  => 'allChosen',
            'type'   => 'radio',
            'name'    => 'specChoice',  
            'value'  => 'all', 
            'label'  =>  __(' *All* in the Population', 'vtmin'), 
            'user_input'  => ''  //checked, selected, contents, etc 
        ) , 
        array(  
            'id'    => 'eachChoice',
            'class'  => 'eachChosen',
            'type'   => 'radio',
            'name'    => 'specChoice',  
            'value'  => 'each', 
            'label'  =>  __(' *Each* in the Population', 'vtmin'), 
            'user_input'  => ''  //checked, selected, contents, etc 
        ) ,
        array(  
            'id'    => 'anyChoice',
            'class'  => 'anyChosen',
            'type'   => 'radio',
            'name'    => 'specChoice',  
            'value'  => 'any', 
            'label'  =>  __(' *Any* in the Population with limits', 'vtmin'), 
            'user_input'  => ''  //checked, selected, contents, etc 
        ) 
      );
     $this->specChoice_in_selection; // all or each or any 
     $this->anyChoice_max = array ( 
            'id'    => 'anyChoice-max',
            'class'  => 'text',
            'type'  => 'text',
            'name'  => 'anyChoice-max',  
            'value'  => '1'                     
          );
     $this->amtSelected = array  (
        array(  
            'id'    => 'qtySelected',
            'class'  => 'qtySelectedClass',
            'type'   => 'radio',
            'name'    => 'amtSelected',  
            'value'  => 'quantity', 
            'label'  =>  __(' Apply to Quantity Total', 'vtmin'), 
            'user_input'  => ''  //checked, selected, contents, etc 
        ) ,
        array( 
            'id'    => 'amtSelected',
            'class'  => 'amtSelectedClass',
            'type'   => 'radio',
            'name'    => 'amtSelected',  
            'value'  => 'currency', 
            'label'  =>  __(' Apply to Price', 'vtmin'), 
            'user_input'  => ''  //checked, selected, contents, etc 
        ) 
      );
     $this->amtSelected_selection; //quantity or currency 
     $this->minimum_amt = array ( 
            'id'    => 'amtChoice-count',
            'class'  => 'text',
            'type'  => 'text',
            'name'  => 'amtChoice-count',  
            'value'  => '1'                     
          );
          
     $this->custMsg_text; //v1.08 
     $this->repeatingGroups; //v1.09.6
      
     $this->rule_error_message = array();
           
     /* ************************************************* */
     /* Rule Processing at Purchase
     *  data is loaded here only at purchase processing time
     *    category info covers both product cats and rule cats
     */
     /* ************************************************* */
     $this->inpop_found_list = array(
        /* **The following array structure is created on-the-fly during the apply process**
        array(
          'prod_id'    => '',
          'prod_name'    => '',
          'prod_qty'  => '',
          'prod_total_price'  => '',
          'prod_cat_list' => array(),
          'rule_cat_list' => array(),
          'prod_id_cart_occurrence' => '', //used to mark product in cart if failed a rule
          'prod_requires_action'  => '' //rule may require cart action, but some of the pop may not.... 
        )
        */
      ); 
      $this->inpop_qty_total = 0.00;
      $this->inpop_total_price = 0.00;
      $this->rule_requires_cart_action;
      $this->errProds_qty = 0.00 ;
      $this->errProds_total_price = 0.00 ;
      $this->errProds_ids = array() ;
      $this->errProds_names = array() ;
      $this->errProds_cat_names = array() ;
     
  } //end function 
    
} //end class   
