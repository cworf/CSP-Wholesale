<?php


class VTMIN_Cart {	
    public $cart_items;
    public $cart_item;
    
    //error messages at rule application time
    public $error_messages;
    //flag to prevent multiple processing iterations
    public $error_messages_processed;
    public $error_messages_are_custom;   //v1.08
    
    
	public function __construct(){
    $this->cart_items = array();
    $this->cart_item;
    $this->error_messages  = array(
       /* **The following array structure is created on-the-fly during the apply process**
        array(
          'msg_from_this_rule_id'    => '',
          'msg_from_this_rule_occurrence' => '',
          'msg_text'  => '',
          'msg_is_custom'   => ''    //v1.08
        )
        */
    ); 
    $this->error_messages_processed; 
    $this->error_messages_are_custom;     //v1.08      
  }
  

} //end class

class VTMIN_Cart_Item {

    public $product_id;  
    public $product_name;
    public $quantity;
    public $unit_price;
    public $total_price;
    public $prod_cat_list;
    public $rule_cat_list; 
    
    //used during rule process logic
    public $product_participates_in_rule;                                  
  
	public function __construct(){
    $this->product_id;  
    $this->product_name;
    $this->quantity = 0.00;
    $this->unit_price = 0.00;
    $this->total_price = 0.00;
    $this->prod_cat_list= array();
    $this->rule_cat_list= array();
    $this->product_participates_in_rule = array(
        /* **The following array structure is created on-the-fly during the apply process**
        array(
          'post_id'    => '',
          'inpop_selection'    => $vtmin_rules_set[$i]->inpop_selection, //needed to test for 'vargroup'
          'ruleset_occurrence',    => $i, //saves having to look for this later
          'inpop_occurrence'    => $k  //saves having to look for this later
        )
        */    
     );
                                            
	}

} //end class


class VTMIN_Cart_Functions{
	
	public function __construct(){
		
	}


    public function vtmin_destroy_cart() { 
        global $vtmin_cart;
        unset($vtmin_cart);
    }
    
    /*
     Template Function
     In your theme, execute the function
     where you want the amount to show
    */
    public function vtmin_cart_oldprice() { 
        global $vtmin_cart;
        echo '$vtmin_cart->$cart_oldprice';
    }

    /*
     Template Function
     In your theme, execute the function
     where you want the amount to show
    */    
    public function vtmin_cart_yousave() { 
        global $vtmin_cart;
        echo '$vtmin_cart->$cart_yousave';
    }
    
    /*
     Template Function
     In your theme, execute the function
     where you want the amount to show
    */
    public function vtmin_cart_unit_oldprice($product_id) { 
        global $vtmin_cart;
        foreach($vtmin_cart->vtmin_cart_items as $key => $vtmin_cart_item) {
           if ($vtmin_cart_item->product_id == $product_id) {
              echo $vtmin_cart->cart_unit_oldprice;
              break;
           }
        }
    }
    
    /*
     Template Function
     In your theme, execute the function
     where you want the amount to show
    */    
    public function vtmin_cart_total_oldprice($product_id) { 
        global $vtmin_cart;
        foreach($vtmin_cart->vtmin_cart_items as $key => $vtmin_cart_item) {
           if ($vtmin_cart_item->product_id == $product_id) {
              echo $vtmin_cart_item->cart_total_oldprice;
              break;
           }
        }
    }
    
    /*
     Template Function
     In your theme, execute the function
     where you want the amount to show
    */    
    public function vtmin_cart_total_yousave($product_id) { 
        global $vtmin_cart;
        foreach($vtmin_cart->vtmin_cart_items as $key => $vtmin_cart_item) {
           if ($vtmin_cart_item->product_id == $product_id) {
              echo $vtmin_cart->cart_total_yousave;
              break;
           }
        }
    }    

} //end class
$vtmin_cart_functions = new VTMIN_Cart_Functions;

