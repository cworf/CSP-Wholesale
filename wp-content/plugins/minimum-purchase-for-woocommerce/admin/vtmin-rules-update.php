<?php
   
class VTMIN_Rule_update {
	

	public function __construct(){  
        $this->vtmin_update_rule();
    }
            
  public  function vtmin_update_rule () {
      global $post, $vtmin_rule; 
      $post_id = $post->ID;                                                                                                                                                          
      $vtmin_rule_new = new VTMIN_Rule();   //  always  start with fresh copy
      $selected = 's';

      $vtmin_rule = $vtmin_rule_new;  //otherwise vtmin_rule is not addressable!
       
     //*****************************************
     //  FILL / upd VTMIN_RULE...
     //*****************************************
     //   Candidate Population
     
     $vtmin_rule->post_id = $post_id;

     if ( ($_REQUEST['post_title'] > ' ' ) ) {
       //do nothing
     }
     else { 
       $vtmin_rule->rule_error_message[] = __('The Rule needs to have a title, but title is empty.', 'vtmin');
     }
      
     $vtmin_rule->inpop_selection = $_REQUEST['popChoice'];
     switch( $vtmin_rule->inpop_selection ) {
        case 'groups':
              $vtmin_rule->inpop[1]['user_input'] = $selected;
              //  $vtmin_checkbox_classes = new VTMIN_Checkbox_classes;
                  //get all checked taxonomies/roles as arrays

             if(!empty($_REQUEST['tax-input-role-in'])) {
                $vtmin_rule->role_in_checked = $_REQUEST['tax-input-role-in'];
             }
             if ((!$vtmin_rule->prodcat_in_checked) && (!$vtmin_rule->rulecat_in_checked) && (!$vtmin_rule->role_in_checked))  {
                $vtmin_rule->rule_error_message[] = __('In Cart Search Criteria Selection Metabox, "Use Selection Groups" was chosen, but no Categories or Roles checked', 'vtmin');
             }                                  
             //   And/Or switch for category/role relationship  
             $vtmin_rule->role_and_or_in_selection  = $_REQUEST['andorChoice']; 
             $this->vtmin_set_default_or_values();            
          break;
        
      }

  
      
          
     //   Population Handling Specifics   
     $vtmin_rule->specChoice_in_selection = $_REQUEST['specChoice'];
     
     switch( $vtmin_rule->specChoice_in_selection ) {
        case 'all':
            $vtmin_rule->specChoice_in[0]['user_input'] = $selected;
          break;
        case 'each':
            $vtmin_rule->specChoice_in[1]['user_input'] = $selected;
          break;
        case 'any':
            $vtmin_rule->specChoice_in[2]['user_input'] = $selected;
            if (empty($_REQUEST['anyChoice-max'])) {
                $vtmin_rule->rule_error_message[] = __('In Select Rule Application cs Metabox, "*Any* in the Population" was chosen, but Maximum products count not filled in', 'vtmin');
            } else { 
                $vtmin_rule->anyChoice_max['value'] = $_REQUEST['anyChoice-max'];
                if ($vtmin_rule->anyChoice_max['value'] == ' '){
                  $vtmin_rule->rule_error_message[] = __('In Select Rule Application  Metabox, "*Any* in the Population" was chosen, but Maximum products count not filled in', 'vtmin');
                } 
                if ( is_numeric($vtmin_rule->anyChoice_max['value'])  === false  ) {
                   $vtmin_rule->rule_error_message[] = __('In Select Rule Application  Metabox, "*Any* in the Population" was chosen, but Maximum products count not numeric', 'vtmin');              
                }
          }    
          break;
      }
              
       
     //   Minimum Amount for this role
     $vtmin_rule->amtSelected_selection = $_REQUEST['amtSelected']; 
     
     switch( $vtmin_rule->amtSelected_selection ) {
        case 'quantity':
            $vtmin_rule->amtSelected[0]['user_input'] = $selected;
          break;
        case 'currency':
            $vtmin_rule->amtSelected[1]['user_input'] = $selected;
          break;
     } 
     if (empty($_REQUEST['amtChoice-count'])) {
        $vtmin_rule->rule_error_message[] = __('In Minimum Amount for this role Metabox, Minimum Amount not filled in', 'vtmin');
     } else { 
        $vtmin_rule->minimum_amt['value'] = $_REQUEST['amtChoice-count'];
        if ($vtmin_rule->minimum_amt['value'] == ' '){
          $vtmin_rule->rule_error_message[] = __('In Minimum Amount for this role Metabox, Minimum Amount not filled in', 'vtmin');
        }  
        if ( is_numeric($vtmin_rule->minimum_amt['value']) === false  ) {
           $vtmin_rule->rule_error_message[] = __('In Minimum Amount for this role Metabox, Minimum Amount not numeric', 'vtmin');              
        }
     }

     //v1.09.6 begin
     $vtmin_rule->repeatingGroups = $_REQUEST['repeating-groups'];
     if ( ( $vtmin_rule->repeatingGroups == '')  ||
          ( $vtmin_rule->repeatingGroups == ' ') ) {
        $vtmin_rule->repeatingGroups = '';   //re-initialize if default msg still there...
     } else {
       if ( is_numeric($vtmin_rule->repeatingGroups)  === false  ) {
           $vtmin_rule->rule_error_message[] = __('If Repeating Groups is chosen, this must be a number greater than 0.', 'vtmin');              
       }
     }  
     //v1.09.6 end
     
     //v1.08 begin
     $vtmin_rule->custMsg_text = $_REQUEST['cust-msg-text'];
     global $vtmin_info; 
     if ( $vtmin_rule->custMsg_text == $vtmin_info['default_full_msg']) {
        $vtmin_rule->custMsg_text = '';   //re-initialize if default msg still there...
     }   
     //v1.08 end
     
    //*****************************************
    //  If errors were found, the error message array will be displayed by the UI on next screen send.
    //*****************************************
    if  ( sizeof($vtmin_rule->rule_error_message) > 0 ) {
      $vtmin_rule->rule_status = 'pending';
    } else {
      $vtmin_rule->rule_status = 'publish';
    }
   
    $rules_set_found = false;
    $vtmin_rules_set = get_option( 'vtmin_rules_set' ); 
    if ($vtmin_rules_set) {
      $rules_set_found = true;
    }
          
    if ($rules_set_found) {
      $rule_found = false;
      $sizeof_rules_set = sizeof($vtmin_rules_set);
      for($i=0; $i < $sizeof_rules_set; $i++) { 
         if ($vtmin_rules_set[$i]->post_id == $post_id) {
            $vtmin_rules_set[$i] = $vtmin_rule;
            $i =  $sizeof_rules_set;
            $rule_found = true; 
         }
      }
      if (!$rule_found) {
         $vtmin_rules_set[] = $vtmin_rule;
      } 
    } else {
      $vtmin_rules_set = array ();
      $vtmin_rules_set[] = $vtmin_rule;
    }
  
    if ($rules_set_found) {
      update_option( 'vtmin_rules_set',$vtmin_rules_set );
    } else {
      add_option( 'vtmin_rules_set',$vtmin_rules_set );
    }
     
  } //end function

 //default to 'OR', as the default value goes away and may be needed if the user switches back to 'groups'...
  public function vtmin_set_default_or_values () {
    global $vtmin_rule;  
    $vtmin_rule->role_and_or_in[1]['user_input'] = 's'; //'s' = 'selected'
    $vtmin_rule->role_and_or_in_selection = 'or'; 
  } 

  
} //end class
