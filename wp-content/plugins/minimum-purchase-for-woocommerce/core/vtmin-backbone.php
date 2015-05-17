<?php

class VTMIN_Backbone{   
	
	public function __construct(){
		  $this->vtmin_register_post_types();
      $this->vtmin_add_dummy_rule_category();
   //   add_filter( 'post_row_actions', array(&$this, 'vtmin_remove_row_actions'), 10, 2 );

	}
  
  public function vtmin_register_post_types() {
   global $vtmin_info;
  
  $tax_labels = array(
		'name' => _x( 'Minimum Purchase Categories', 'taxonomy general name', 'vtmin' ),
		'singular_name' => _x( 'Minimum Purchase Category', 'taxonomy singular name', 'vtmin' ),
		'search_items' => __( 'Search Minimum Purchase Category', 'vtmin' ),
		'all_items' => __( 'All Minimum Purchase Categories', 'vtmin' ),
		'parent_item' => __( 'Minimum Purchase Category', 'vtmin' ),
		'parent_item_colon' => __( 'Minimum Purchase Category:', 'vtmin' ),
		'edit_item' => __( 'Edit Minimum Purchase Category', 'vtmin' ),
		'update_item' => __( 'Update Minimum Purchase Category', 'vtmin' ),
		'add_new_item' => __( 'Add New Minimum Purchase Category', 'vtmin' ),
		'new_item_name' => __( 'New Minimum Purchase Category', 'vtmin' )
  ); 	

  
  $tax_args = array(
    'hierarchical' => true,
		'labels' => $tax_labels,
		'show_ui' => true,
		'query_var' => false,
    'rewrite' => array( 'slug' => 'vtmin_rule_category',  'with_front' => false, 'hierarchical' => true )
  ) ;            

  $taxonomy_name =  'vtmin_rule_category';
 
  
   //REGISTER TAXONOMY 
  	register_taxonomy($taxonomy_name, $vtmin_info['applies_to_post_types'], $tax_args); 
    
        
 //REGISTER POST TYPE
 $post_labels = array(
				'name' => _x( 'Minimum Purchase Rules', 'post type name', 'vtmin' ),
        'singular_name' => _x( 'Minimum Purchase Rule', 'post type singular name', 'vtmin' ),
        'add_new' => _x( 'Add New', 'admin menu: add new Minimum Purchase Rule', 'vtmin' ),
        'add_new_item' => __('Add New Minimum Purchase Rule', 'vtmin' ),
        'edit_item' => __('Edit Minimum Purchase Rule', 'vtmin' ),
        'new_item' => __('New Minimum Purchase Rule', 'vtmin' ),
        'view_item' => __('View Minimum Purchase Rule', 'vtmin' ),
        'search_items' => __('Search Minimum Purchase Rules', 'vtmin' ),
        'not_found' =>  __('No Minimum Purchase Rules found', 'vtmin' ),
        'not_found_in_trash' => __( 'No Minimum Purchase Rules found in Trash', 'vtmin' ),
        'parent_item_colon' => '',
        'menu_name' => __( 'Minimum Purchase Rules', 'vtmin' )
			);
      
	register_post_type( 'vtmin-rule', array(
		  'capability_type' => 'post',
      'hierarchical' => true,
		  'exclude_from_search' => true,
      'labels' => $post_labels,
			'public' => true,
			'show_ui' => true,
      'query_var' => true,
      'rewrite' => false,     
      'supports' => array('title' )	 //remove 'revisions','editor' = no content/revisions boxes 
		)
	);
 
//	$role = get_role( 'administrator' );    //v1.09.1 removed for conflict
//	$role->add_cap( 'read_vtmin-rule' );      //v1.09.1 removed for conflict 
}

  public function vtmin_add_dummy_rule_category () {
      $category_list = get_terms( 'vtmin_rule_category', 'hide_empty=0&parent=0' );
    	if ( count( $category_list ) == 0 ) {
    		wp_insert_term( __( 'Minimum Purchase Category', 'vtmin' ), 'vtmin_rule_category', "parent=0" );
      }
  }


/*------------------------------------------------------------------------------------
  	remove quick edit for custom post type 
  ------------------------------------------------------------------------------------*/
 /*
  public function vtmin_remove_row_actions( $actions, $post )
  {
    global $current_screen;
  	if( $current_screen->post_type = 'vtmin-rule' ) {
    	unset( $actions['edit'] );
    	unset( $actions['view'] );
    	unset( $actions['trash'] );
    	unset( $actions['inline hide-if-no-js'] );
  	//$actions['inline hide-if-no-js'] .= __( 'Quick&nbsp;Edit' );
     }
  	return $actions;
  }
*/





function vtmin_register_settings() {
    register_setting( 'vtmin_options', 'vtmin_rules' );
} 


} //end class
$vtmin_backbone = new VTMIN_Backbone;
  
  
  
  class VTMIN_Functions {   
	
	public function __construct(){

	}
    
  function vtmin_getSystemMemInfo() 
  {       
      /*  Throws errors...
      $data = explode("\n", file_get_contents("/proc/meminfo"));
      $meminfo = array();
      foreach ($data as $line) {
          list($key, $val) = explode(":", $line);
          $meminfo[$key] = trim($val);
      }
      */
      $meminfo = array();
      return $meminfo;
  }
  
  } //end class