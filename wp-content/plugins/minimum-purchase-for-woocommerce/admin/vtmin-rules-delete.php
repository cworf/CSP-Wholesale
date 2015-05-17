<?php
class VTMIN_Rule_delete {
	
	public function __construct(){
     
    }
    
  public  function vtmin_delete_rule () {
    global $post, $vtmin_info, $vtmin_rules_set, $vtmin_rule;
    $post_id = $post->ID;    
    $vtmin_rules_set = get_option( 'vtmin_rules_set' ) ;
    $sizeof_rules_set = sizeof($vtmin_rules_set);
    for($i=0; $i < $sizeof_rules_set; $i++) { 
       if ($vtmin_rules_set[$i]->post_id == $post_id) {
          unset ($vtmin_rules_set[$i]);   //this is the 'delete'
          $i =  $sizeof_rules_set; 
       }
    }
   
    if (count($vtmin_rules_set) == 0) {
      delete_option( 'vtmin_rules_set' );
    } else {
      update_option( 'vtmin_rules_set', $vtmin_rules_set );
    }
 }  
 
  /* Change rule status to 'pending'
        if status is 'pending', the rule will not be executed during cart processing 
  */ 
  public  function vtmin_trash_rule () {
    global $post, $vtmin_info, $vtmin_rules_set, $vtmin_rule;
    $post_id = $post->ID;    
    $vtmin_rules_set = get_option( 'vtmin_rules_set' ) ;
    $sizeof_rules_set = sizeof($vtmin_rules_set);
    for($i=0; $i < $sizeof_rules_set; $i++) { 
       if ($vtmin_rules_set[$i]->post_id == $post_id) {
          if ( $vtmin_rules_set[$i]->rule_status =  'publish' ) {    //only update if necessary, may already be pending
            $vtmin_rules_set[$i]->rule_status =  'pending';
            update_option( 'vtmin_rules_set', $vtmin_rules_set ); 
          }
          $i =  $sizeof_rules_set; //set to done
       }
    }
 }  

  /*  Change rule status to 'publish' 
        if status is 'pending', the rule will not be executed during cart processing  
  */
  public  function vtmin_untrash_rule () {
    global $post, $vtmin_info, $vtmin_rules_set, $vtmin_rule;
    $post_id = $post->ID;     
    $vtmin_rules_set = get_option( 'vtmin_rules_set' ) ;
    $sizeof_rules_set = sizeof($vtmin_rules_set);
    for($i=0; $i < $sizeof_rules_set; $i++) { 
       if ($vtmin_rules_set[$i]->post_id == $post_id) {
          if  ( sizeof($vtmin_rules_set[$i]->rule_error_message) > 0 ) {   //if there are error message, the status remains at pending
            //$vtmin_rules_set[$i]->rule_status =  'pending';   status already pending
            global $wpdb;
            $wpdb->update( $wpdb->posts, array( 'post_status' => 'pending' ), array( 'ID' => $post_id ) );    //match the post status to pending, as errors exist.
          }  else {
            $vtmin_rules_set[$i]->rule_status =  'publish';
            update_option( 'vtmin_rules_set', $vtmin_rules_set );  
          }
          $i =  $sizeof_rules_set;   //set to done
       }
    }
 }  
 
     
  public  function vtmin_nuke_all_rules() {
    global $post, $vtmin_info;
    
   //DELETE all posts from CPT
   $myPosts = get_posts( array( 'post_type' => 'vtmin-rule', 'number' => 500, 'post_status' => array ('draft', 'publish', 'pending', 'future', 'private', 'trash' ) ) );
   //$mycustomposts = get_pages( array( 'post_type' => 'vtmin-rule', 'number' => 500) );
   foreach( $myPosts as $mypost ) {
     // Delete's each post.
     wp_delete_post( $mypost->ID, true);
    // Set to False if you want to send them to Trash.
   }
    
   //DELETE matching option array
   delete_option( 'vtmin_rules_set' );
 }  
     
  public  function vtmin_nuke_all_rule_cats() {
    global $vtmin_info;
    
   //DELETE all rule category entries
   $terms = get_terms($vtmin_info['rulecat_taxonomy'], 'hide_empty=0&parent=0' );
   $count = count($terms);
   if ( $count > 0 ){  
       foreach ( $terms as $term ) {
          wp_delete_term( $term->term_id, $vtmin_info['rulecat_taxonomy'] );
       }
   } 
 }  
      
  public  function vtmin_repair_all_rules() {
    global $wpdb, $post, $vtmin_info, $vtmin_rules_set, $vtmin_rule;    
    $vtmin_rules_set = get_option( 'vtmin_rules_set' ) ;
    $sizeof_rules_set = sizeof($vtmin_rules_set);
    for($i=0; $i < $sizeof_rules_set; $i++) { 
       $test_post = get_post($vtmin_rules_set[$i]->post_id );
       if ( !$test_post ) {
           unset ($vtmin_rules_set[$i]);   //this is the 'delete'
       }
    } 
    
    if (count($vtmin_rules_set) == 0) {
      delete_option( 'vtmin_rules_set' );
    } else {
      update_option( 'vtmin_rules_set', $vtmin_rules_set );
    }
 } 
 
} //end class
