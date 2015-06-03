<?php

if ( !defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Smart_Manager_Base' ) ) {
	class Smart_Manager_Base {

		public $dashboard_key = '',
			$default_store_model = array(),
			$terms_val_parent = array(),
			$req_params = array();
        
		// include_once $this->plugin_path . '/class-smart-manager-utils.php';

		function __construct($dashboard_key) {
			$this->dashboard_key = $dashboard_key;
			$this->post_type = $dashboard_key;
			$this->plugin_path  = untrailingslashit( plugin_dir_path( __FILE__ ) );
			$this->req_params  	= (!empty($_REQUEST)) ? $_REQUEST : array();
		}

		public function get_default_store_model() {

			global $wpdb;

			$col_model = array();

			$query_posts_col = "SHOW COLUMNS FROM {$wpdb->prefix}posts";
			$results_posts_col = $wpdb->get_results($query_posts_col, 'ARRAY_A');
			$posts_num_rows = $wpdb->num_rows;

			if ($posts_num_rows > 0) {
				foreach ($results_posts_col as $posts_col) {
					
					$temp = array();
					$field_nm = (!empty($posts_col['Field'])) ? $posts_col['Field'] : '';
					$temp ['src'] = 'posts/'.$field_nm;
					$temp ['index'] = sanitize_title(str_replace('/', '_', $temp ['src'])); // generate slug using the wordpress function if not given 
					$temp ['name'] = __(ucwords(str_replace('_', ' ', $field_nm)));

					$type = 'string';
					$temp ['width'] = 100;

					if (!empty($posts_col['Type'])) {
						$type_strpos = strrpos($posts_col['Type'],'(');
						if ($type_strpos !== false) {
							$type = substr($posts_col['Type'], 0, $type_strpos);
						} else {
							$type = $posts_col['Type'];
						}

						if (substr($type,-3) == 'int') {
							$type = 'number';
							$temp ['width'] = 50;
						} else if ($type == 'text') {
							$temp ['width'] = 130;
						} else if (substr($type,-4) == 'char' || substr($type,-4) == 'text') {
							if ($type == 'longtext') {
								$type = 'longstring';
								$temp ['width'] = 150;
							} else {
								$type = 'string';
							}
						} else if (substr($type,-4) == 'blob') {
							$type = 'longstring';
						} else if ($type == 'datetime' || $type == 'timestamp') {
							$type = 'datetime';
							$temp ['width'] = 102;
						} else if ($type == 'date' || $type == 'year') {
							$type = 'date';
						} else if ($type == 'decimal' || $type == 'float' || $type == 'double' || $type == 'real') {
							$type = 'integer';
							$temp ['width'] = 50;
						} else if ($type == 'boolean') {
							$type = 'toggle';
							$temp ['width'] = 30;
						}

					}

					$temp ['hidden']			= false;
					$temp ['editable']			= true;
					$temp ['batch_editable']	= true; // flag for enabling the batch edit for the column
					$temp ['sortable']			= true;
					$temp ['resizable']			= true;

					//For disabling frozen
					$temp ['frozen']			= false;

					$temp ['allow_showhide']	= true;
					$temp ['exportable']		= true; //default true. flag for enabling the column in export
					$temp ['searchable']		= true;

					//Code fr handling the positioning of the columns
					if ($field_nm == 'ID') {
						$temp ['position'] = 0;
						$temp ['key'] = true;
						$temp ['editable'] = false;
						$temp ['batch_editable'] = false;
						// $temp ['frozen'] = true;
					} else if ($field_nm == 'post_title') {
						$temp ['width'] = 200;
						$temp ['position'] = 1; // based on order of definition if not given or more than one column having same position
						// $temp ['frozen'] = true;
					} else if ($field_nm == 'post_content') {
						$temp ['position'] = 2;
					} else if ($field_nm == 'post_status') {
						$temp ['position'] = 3;
					} else if ($field_nm == 'post_date') {
						$temp ['position'] = 4;
					} else if ($field_nm == 'post_name') {
						$temp ['position'] = 5;
					}

					$temp ['type'] = $type;

					$temp ['values'] = array();
					if ($field_nm == 'post_status') {
						$temp ['type'] = 'list';
						$temp ['values'] = array('publish' => __('Publish'),
												 'draft' => __('Draft'));
					}

					if ( $field_nm == 'ID' || $field_nm == 'post_title' || $field_nm == 'post_date' || $field_nm == 'post_name'
						 || $field_nm == 'post_status' || $field_nm == 'post_content') {
						$temp ['hidden'] = false;
					} else {
						$temp ['hidden'] = true;
					}

					$col_model [] = $temp;

				}
			}

			//Code to get columns from postmeta table

			$post_type_cond = (is_array($this->post_type)) ? " WHERE {$wpdb->prefix}posts.post_type IN ('". implode("','", $this->post_type) ."')" : " WHERE {$wpdb->prefix}posts.post_type = '". $this->post_type ."'";

			$query_postmeta_col = "SELECT DISTINCT {$wpdb->prefix}postmeta.meta_key,
											{$wpdb->prefix}postmeta.meta_value
										FROM {$wpdb->prefix}postmeta 
											JOIN {$wpdb->prefix}posts ON ({$wpdb->prefix}posts.id = {$wpdb->prefix}postmeta.post_id)
										$post_type_cond
										GROUP BY {$wpdb->prefix}postmeta.meta_key";
			$results_postmeta_col = $wpdb->get_results ($query_postmeta_col , 'ARRAY_A');
			$num_rows = $wpdb->num_rows;

			if ($num_rows > 0) {

				$meta_keys = array();

				foreach ($results_postmeta_col as $key => $postmeta_col) {
					if (empty($postmeta_col['meta_value'])) {
						$meta_keys [] = $postmeta_col['meta_key']; //TODO: if possible store in db instead of using an array
					}

					unset($results_postmeta_col[$key]);
					$results_postmeta_col[$postmeta_col['meta_key']] = $postmeta_col;
				}

				if (!empty($meta_keys)) {
					$query_meta_value = "SELECT {$wpdb->prefix}postmeta.meta_key,
													{$wpdb->prefix}postmeta.meta_value
												FROM {$wpdb->prefix}postmeta 
													JOIN {$wpdb->prefix}posts ON ({$wpdb->prefix}posts.id = {$wpdb->prefix}postmeta.post_id)
												WHERE {$wpdb->prefix}posts.post_type  = '". $this->dashboard_key ."'
													AND {$wpdb->prefix}postmeta.meta_value != ''
													AND {$wpdb->prefix}postmeta.meta_key IN ('".implode("','",$meta_keys)."')
												GROUP BY {$wpdb->prefix}postmeta.meta_key";
					$results_meta_value = $wpdb->get_results ($query_meta_value , 'ARRAY_A');
					$num_rows_meta_value = $wpdb->num_rows;

					if ($num_rows_meta_value > 0) {
						foreach ($results_meta_value as $result_meta_value) {
							if (isset($results_postmeta_col [$result_meta_value['meta_key']])) {
								$results_postmeta_col [$result_meta_value['meta_key']]['meta_value'] = $result_meta_value['meta_value'];
							}
						}
					}
				}

				$type = 'string';
				$index = sizeof($col_model);

				//Code for pkey column for postmeta

				$col_model [$index] = array();
				$col_model [$index]['src'] = 'postmeta/post_id';
				$col_model [$index]['index'] = sanitize_title(str_replace('/', '_', $col_model [$index]['src'])); // generate slug using the wordpress function if not given 
				$col_model [$index]['name'] = __(ucwords(str_replace('_', ' ', 'post_id')));
				$col_model [$index]['type'] = 'number';
				$col_model [$index]['hidden']	= true;
				$col_model [$index]['allow_showhide'] = false;
				$col_model [$index]['editable']	= false;

				foreach ($results_postmeta_col as $postmeta_col) {

					$temp = array();

					$meta_key = (!empty($postmeta_col['meta_key'])) ? $postmeta_col['meta_key'] : '';
					$meta_value = (!empty($postmeta_col['meta_value'])) ? $postmeta_col['meta_value'] : '';

					$temp ['src'] = 'postmeta/meta_key='.$meta_key.'/meta_value='.$meta_key;
					$temp ['index'] = sanitize_title(str_replace(array('/','='), '_', $temp ['src'])); // generate slug using the wordpress function if not given 
					$temp ['name'] = __(ucwords(str_replace('_', ' ', $meta_key)));

					$temp ['width'] = 100;

					if (is_numeric($meta_value)) {
						$type = 'number';
						$temp ['width'] = 50;
					} else if ($meta_value == 'yes' || $meta_value == 'no') {
						$type = 'toggle';
						$temp ['width'] = 30;
					} else if (is_serialized($meta_value) === true) {
						$type = 'longstring';
						$temp ['width'] = 200;
					}

					$temp ['type'] = $type;
					$temp ['values'] = array();

					$temp ['hidden'] = false;
					$hidden_col_array = array('_edit_lock','_edit_last');

					if (array_search($meta_key,$hidden_col_array) !== false ) {
						$temp ['hidden'] = true;	
					}

					
					$temp ['editable']			= true;
					$temp ['batch_editable']	= true; // flag for enabling the batch edit for the column
					$temp ['sortable']			= true;
					$temp ['resizable']			= true;
					$temp ['frozen']			= false;
					$temp ['allow_showhide']	= true;
					$temp ['exportable']		= true; //default true. flag for enabling the column in export
					$temp ['searchable']		= true;

					$col_model [] = $temp;
				}
			}

			//Code to get columns from terms

			//Code to get all relevant taxonomy for the post type
			$taxonomy_nm = get_object_taxonomies($this->post_type);

			if (!empty($taxonomy_nm)) {

				$terms_val = array();
				$terms_val_parent = array();

				$index = sizeof($col_model);

				//Code for pkey column for terms

				$col_model [$index] = array();
				$col_model [$index]['src'] = 'terms/object_id';
				$col_model [$index]['index'] = sanitize_title(str_replace('/', '_', $col_model [$index]['src'])); // generate slug using the wordpress function if not given 
				$col_model [$index]['name'] = __(ucwords(str_replace('_', ' ', 'object_id')));
				$col_model [$index]['type'] = 'number';
				$col_model [$index]['allow_showhide'] = false;
				$col_model [$index]['hidden']	= true;
				$col_model [$index]['editable']	= false;

				$taxonomy_terms = get_terms($taxonomy_nm, array('hide_empty'=> 0,'orderby'=> 'id'));

				if (!empty($taxonomy_terms)) {
					foreach ($taxonomy_terms as $term_obj) {

						if (empty($terms_val[$term_obj->taxonomy])) {
							$terms_val[$term_obj->taxonomy] = array();
						}

						$terms_val[$term_obj->taxonomy][$term_obj->term_id] = $term_obj->name;
						$this->terms_val_parent[$term_obj->taxonomy][$term_obj->term_id] = array();
						$this->terms_val_parent[$term_obj->taxonomy][$term_obj->term_id]['term'] = $term_obj->name;
						$this->terms_val_parent[$term_obj->taxonomy][$term_obj->term_id]['parent'] = $term_obj->parent;
					}	
				}

				//Code for defining the col model for the terms
				foreach ($taxonomy_nm as $taxonomy) {

					$terms_col = array();

					$terms_col ['src'] 				= 'terms/'.$taxonomy;
					$terms_col ['index'] 			= sanitize_title(str_replace(array('/','='), '_', $terms_col ['src'])); // generate slug using the wordpress function if not given 
					$terms_col ['name'] 			= __(ucwords(str_replace('_', ' ', $taxonomy)));

					$terms_col ['width'] = 200;

					if (!empty($terms_val[$taxonomy])) {
						$terms_col ['type'] 		= 'list';
						$terms_col ['values'] 		= $terms_val[$taxonomy];	
					} else {
						$terms_col ['type'] 		= 'string';
					}
					

					$terms_col ['hidden'] 			= false;
					$terms_col ['editable']			= true;
					$terms_col ['batch_editable']	= true; // flag for enabling the batch edit for the column
					$terms_col ['sortable']			= true;
					$terms_col ['resizable']		= true;
					$terms_col ['frozen']			= false;
					$terms_col ['allow_showhide']	= true;
					$terms_col ['exportable']		= true; //default true. flag for enabling the column in export
					$terms_col ['searchable']		= true;

					$col_model [] = $terms_col;
				}
			}

			//defining the default col model

			$this->default_store_model = array ();
			$this->default_store_model[$this->dashboard_key] = array( 
																	'display_name' => __(ucwords(str_replace('_', ' ', $this->dashboard_key))),
																	'tables' => array(
																					'posts' 				=> array(
																													'pkey' => 'ID',
																													'join_on' => '',
																													'where' => array( 
																																	'post_type' 	=> $this->post_type,
																																	'post_status' 	=> 'any' // will get all post_status except 'trash' and 'auto-draft'
																																	
																																	// 'post_status' 	=> array('publish', 'draft') // comma seperated for multiple values
																																	//For any other whereition specify, colname => colvalue
																																   )
																												),

																					'postmeta' 				=> array(
																													'pkey' => 'post_id',
																													'join_on' => 'postmeta.post_ID = posts.ID', // format current_table.pkey = joinning table.pkey
																													'where' => array( // provide a wp_query [meta_query]
																																// 'relation' => 'AND', // AND or OR
																																// 	array(
																																// 		'key'     => '',
																																// 		'value'   => '',
																																// 		'compare' => '',
																																// 	),
																																// 	array(
																																// 		'key'     => '',
																																// 		'value'   => 0,
																																// 		'type'    => '',
																																// 		'compare' => '',
																																// 	)
																																)
																												),

																					'term_relationships' 	=> array(
																													'pkey' => 'object_id',
																													'join_on' => 'term_relationships.object_id = posts.ID',
																													'where' => array()
																												),

																					'term_taxonomy' 		=> array(
																													'pkey' => 'term_taxonomy_id',
																													'join_on' => 'term_taxonomy.term_taxonomy_id = term_relationships.term_taxonomy_id',
																													'where' => array()
																												),

																					'terms' 				=> array(
																													'pkey' => 'term_id',
																													'join_on' => 'terms.term_id = term_taxonomy.term_id',
																													'where' => array(
																															// 'relation' => 'AND', // AND or OR
																															// array(
																															// 	'taxonomy' => '',
																															// 	'field'    => '',
																															// 	'terms'    => ''
																															// ),
																														)
																												)

																					), 
																	'columns' => $col_model,
																	'sort_params' 	=> array ( //WP_Query array structure
																							'orderby' => 'ID', //multiple list separated by space
																							'order' => 'DESC' ),

																	// 'sort_params' 		=> array( 'post_parent' => 'ASC', 'ID' => 'DESC' ),
																	'per_page_limit' 	=> '', // blank, 0, -1 all values refer to infinite scroll
																	'treegrid'			=> false // flag for setting the treegrid
										);
		}


		//Function to get the dashboard model
		public function get_dashboard_model() {

			global $wpdb;

			$col_model = array();

			// Load from cache
			$store_model = get_transient( 'sm_dashboard_model_'.$this->dashboard_key );			

			// Valid cache not found
			if ( false === $store_model ) {
				$this->get_default_store_model();
				$store_model = $this->default_store_model;
			}

			//Filter to modify the dashboard model
			$store_model = apply_filters('sm_dashboard_model', $store_model);

			//Code for re-arranging the columns in the final column model based on the set position
			$final_column_model = (!empty($store_model[$this->dashboard_key]['columns'])) ? $final_column_model = &$store_model[$this->dashboard_key]['columns'] : '';

			if (!empty($final_column_model)) {

				$priority_columns = array();

				foreach ($final_column_model as $key => &$column_model) {

					//checking for multilist datatype
					if (!empty($column_model['type']) && $column_model['type'] == 'multilist') {

						$col_exploded = (!empty($column_model['src'])) ? explode("/", $column_model['src']) : array();
						
						if ( sizeof($col_exploded) > 2) {
							$col_meta = explode("=",$col_exploded[1]);
							$col_nm = $col_meta[1];
						} else {
							$col_nm = $col_exploded[1];
						}

						$column_model['values'] = (!empty($this->terms_val_parent[$col_nm])) ? $this->terms_val_parent[$col_nm] : $column_model['values'];
					}

					if (!isset($column_model['position']) && $column_model['position'] == '') continue;
						
					$priority_columns[] = $column_model;
					unset($final_column_model[$key]);
				}

				if (!empty($priority_columns)) {

					usort( $priority_columns, "sm_position_compare" ); //code for sorting as per the position

					foreach ($final_column_model as $column_model) {
						$priority_columns [] = $column_model;
					}

					ksort($priority_columns);
					$store_model[$this->dashboard_key]['columns'] = $priority_columns;
				}
			}

			// Valid cache not found
			if ( false === get_transient( 'sm_dashboard_model_'.$this->dashboard_key ) ) {
				set_transient( 'sm_dashboard_model_'.$this->dashboard_key, $store_model, WEEK_IN_SECONDS );	
			}

			do_action('sm_dashboard_model_saved');

			echo json_encode ( $store_model );
			exit;
		}

		//Function to get the data model for the dashboard
		public function get_data_model() {

			global $wpdb;

			$current_store_model = get_transient( 'sm_dashboard_model_'.$this->dashboard_key );

			$col_model = (!empty($current_store_model[$this->dashboard_key]['columns'])) ? $current_store_model[$this->dashboard_key]['columns'] : array();

			//Code for getting the relevant columns
			if (!empty($col_model)) {

				$data_cols = array();
				$data_cols_serialized = array();
				$data_cols_multilist = array();
				$taxonomy_nm = array();

				foreach ($col_model as $col) {
					$col_exploded = (!empty($col['src'])) ? explode("/", $col['src']) : array();

					if (empty($col_exploded)) continue;
					
					if ( sizeof($col_exploded) > 2) {
						$col_meta = explode("=",$col_exploded[1]);
						$col_nm = $col_meta[1];
					} else {
						$col_nm = $col_exploded[1];
					}

					$data_cols[] = $col_nm;

					//Code for storing the serialized cols
					if($col['type'] == 'longstring') {
						$data_cols_serialized[] = $col_nm;
					} else if ($col['type'] == 'multilist') {
						$data_cols_multilist[] = $col_nm;
					}

					//Code for saving the taxonomy names
					if ($col_exploded[0] == 'terms') {
						$taxonomy_nm [] = $col_nm;
					}
				}
			}

			$data_model = array(); 

			$start = (!empty($this->req_params['start'])) ? $this->req_params['start'] : '';
			$limit = (!empty($this->req_params['limit'])) ? $this->req_params['limit'] : 50;
			$current_page = (!empty($this->req_params['page'])) ? $this->req_params['page'] : '1';

			$start_offset = ($current_page > 1) ? (($current_page - 1) * $limit) : $start;

			$post_cond = (!empty($this->req_params['table_model']['posts']['where'])) ? $this->req_params['table_model']['posts']['where'] : array('post_type' => $this->dashboard_key);
			$meta_query = (!empty($this->req_params['table_model']['postmeta']['where'])) ? $this->req_params['table_model']['postmeta']['where'] : '';
			$tax_query = (!empty($this->req_params['table_model']['terms']['where'])) ? $this->req_params['table_model']['terms']['where'] : '';
			$sort_params = (!empty($this->req_params['sort_params'])) ? $this->req_params['sort_params'] : '';
			$order_by = (!empty($sort_params['orderby'])) ? $sort_params['orderby'] : '';
			$order = (!empty($sort_params['order'])) ? $sort_params['order'] : '';

			//WP_Query to get all the relevant post_ids
			$args = array(
				            'posts_per_page' => $this->req_params['limit'],
				            'offset' => $start_offset,
				            'meta_query' => array( $meta_query ),
				            'tax_query' => array( $tax_query ),
				            'orderby' => $order_by,
				            'order' => $order
			            );

			$args = array_merge($args, $post_cond);

        	$result_posts = new WP_Query( $args );

        	$items = array();
        	$post_ids = array();

        	$posts_data = $result_posts->posts;
        	$total_count = $result_posts->found_posts;

        	$index = 0;
        	$total_pages = 1;

        	if ($total_count > $limit) {
        		$total_pages = ceil($total_count/$limit);
        	}

        	if (!empty($posts_data)) {
        		foreach ($posts_data as $key => $value) {

        			$post = (array) $value;

        			foreach ($post as $post_key => $post_value) {

        				if (array_search($post_key, $data_cols) === false) continue; //cond for checking col in col model

        				$key = 'posts_'.strtolower(str_replace(' ', '_', $post_key));
        				$items [$index][$key] = $post_value;
        			}

        			//Code for getting the postmeta data
        			$postmeta_data = get_post_meta($value->ID);

        			if (!empty($postmeta_data)) {
        				$items [$index]['postmeta_post_id'] = $value->ID;

	        			if (!empty($postmeta_data)) {
	        				foreach ($postmeta_data as $postmeta_key => $postmeta_value) {

	        					if (array_search($postmeta_key, $data_cols) === false) continue; //cond for checking col in col model

	        					//Code for handling serialized data
	        					if (array_search($postmeta_key, $data_cols_serialized) !== false) {
									$postmeta_value[0] = maybe_unserialize($postmeta_value[0]);
									if (!empty($postmeta_value[0])) {
										$postmeta_value[0] = json_encode($postmeta_value[0]);	
									}
									
		        				}

		        				$postmeta_key = 'postmeta_meta_key_'.$postmeta_key.'_meta_value_'.$postmeta_key;
		        				$items [$index][$postmeta_key] = (!empty($postmeta_value[0])) ? $postmeta_value[0] : '';
		        			}
	        			}	
        			}
        			
        			$post_ids [] = $value->ID; //storing the post ids for fetching the terms
        			$index++;
        		}
        	}

        	//Code to get the terms

        	//Code to get all relevant taxonomy for the post type
			$taxonomy_nm = get_object_taxonomies($this->dashboard_key);

        	$terms_objects = wp_get_object_terms( $post_ids, $taxonomy_nm, 'orderby=none&fields=all_with_object_id' );

        	if ( ! empty( $terms_objects ) ) {
				if ( ! is_wp_error( $terms_objects ) ) {
					$terms_data = array();

					//Code for creating the terms data array
					foreach ($terms_objects as $term_obj) {
						if (empty($terms_data[$term_obj->object_id])) {
							$terms_data[$term_obj->object_id] = array();
						}

						$taxonomy_nm = $term_obj->taxonomy;

						//Code for handling multilist data
	        			if (array_search($taxonomy_nm, $data_cols_multilist) !== false) {
	        				if (empty($terms_data[$term_obj->object_id][$taxonomy_nm])) {
	        					$terms_data[$term_obj->object_id][$taxonomy_nm] = $term_obj->name;
	        				} else {
	        					$terms_data[$term_obj->object_id][$taxonomy_nm] .= "<br>" . $term_obj->name;
	        				}
	        			} else {
	        				$terms_data[$term_obj->object_id][$taxonomy_nm] = $term_obj->name;
	        			}

						// $terms_data[$term_obj->object_id][$term_obj->taxonomy] = $term_obj->term_taxonomy_id;
					}

					//Code for merging the terms related data in $items array
					foreach ($items as &$item) {

						$id = (!empty($item['posts_id'])) ? $item['posts_id'] : '';
						if (empty($id)) continue;

						$taxonomy_array = (!empty($terms_data[$id])) ? array_keys($terms_data[$id]) : array();
						if(empty($taxonomy_array)) continue;

						$item ['terms_object_id'] = $item['posts_id'];

						foreach ($taxonomy_array as $taxonomy) {
							$terms_key = 'terms_'.strtolower(str_replace(' ', '_', $taxonomy));
							$item [$terms_key] = $terms_data[$id][$taxonomy];
						}
					}
				}
			}
			
        	$data_model ['items'] = (!empty($items)) ? $items : '';
        	$data_model ['start'] = $start+$limit;
        	$data_model ['page'] = $current_page;
        	$data_model ['total_pages'] = $total_pages;
        	$data_model ['total_count'] = $total_count;

        	//Filter to modify the data model
			$data_model = apply_filters('sm_data_model', $data_model);

			echo json_encode ( $data_model );
			unset($data_model);
		    exit;

		}

		//Function to get the meta data for the given ids
		public function get_meta_data ($ids, $meta_keys, $update_table, $update_table_key = 'post_id') {
			global $wpdb;

			$ids_format = implode(', ', array_fill(0, count($ids), '%s'));
			$meta_keys_format = implode(', ', array_fill(0, count($meta_keys), '%s'));
			$group_by = '';

			if ( $update_table == 'postmeta' ) {
				$group_by = 'GROUP BY '.$update_table_key.' , meta_id';
			}

			$old_meta_data_query = "SELECT *
								  FROM {$wpdb->prefix}$update_table
								  WHERE post_id IN (".implode(',',$ids).")
								  	AND meta_key IN ('".implode("','",$meta_keys)."')
								  $group_by";

			$old_meta_data_results = $wpdb->get_results( $wpdb->prepare( $old_meta_data_query,1), 'ARRAY_A');  // passed 1 to avoid the debug warning
			$meta_data_num_rows = $wpdb->num_rows;

			$old_meta_data = array();

			if ($meta_data_num_rows > 0) {
				foreach ($old_meta_data_results as $meta_data) {

					$post_id = $meta_data[$update_table_key];
					unset($meta_data[$update_table_key]);

					if ( empty($old_meta_data[$post_id]) ) {
						$old_meta_data[$post_id] = array();
					}
					
					$old_meta_data[$post_id][] = $meta_data;
				}
			}

			return $old_meta_data;
		}


		public function inline_update() {
			global $wpdb;

			$edited_data = (!empty($this->req_params['edited_data'])) ? json_decode(stripslashes($this->req_params['edited_data']), true) : array();
			$current_store_model = get_transient( 'sm_dashboard_model_'.$this->dashboard_key );
			$table_model = (!empty($current_store_model[$this->dashboard_key]['tables'])) ? $current_store_model[$this->dashboard_key]['tables'] : array();
			$col_model = (!empty($current_store_model[$this->dashboard_key]['columns'])) ? $current_store_model[$this->dashboard_key]['columns'] : array();

			if (empty($edited_data) || empty($table_model) || empty($col_model)) return;

			$edited_data = apply_filters('sm_inline_update_pre', $edited_data);

			$data_cols_serialized = array();
			$data_cols_multiselect = array();
			$data_cols_multiselect_val = array();
			$data_cols_list = array();
			$data_cols_list_val = array();

			//Code for storing the serialized cols
			foreach ($col_model as $col) {
				$col_exploded = (!empty($col['src'])) ? explode("/", $col['src']) : array();

				if (empty($col_exploded)) continue;
				
				if ( sizeof($col_exploded) > 2) {
					$col_meta = explode("=",$col_exploded[1]);
					$col_nm = $col_meta[1];
				} else {
					$col_nm = $col_exploded[1];
				}

				if($col['type'] == 'longstring') {
					$data_cols_serialized[] = $col_nm;
				} else if($col['type'] == 'multilist') {
					$data_cols_multiselect[] = $col_nm;
					$data_cols_multiselect_val[$col_nm] = (!empty($col['values'])) ? $col['values'] : array();

					if (empty($data_cols_multiselect_val[$col_nm])) continue;

					$final_multiselect_val = array();

					foreach ($data_cols_multiselect_val[$col_nm] as $key => $value) {
						$final_multiselect_val[$key] = $value['term'];
					}

					$data_cols_multiselect_val[$col_nm] = $final_multiselect_val;
					
				} else if ($col['type'] == 'list') {
					$data_cols_list[] = $col_nm;
					$data_cols_list_val[$col_nm] = (!empty($col['values'])) ? $col['values'] : array();
				}

			}

			$update_params_meta = array(); // for all tables with meta_key = meta_value like structure for updating the values
			$insert_params_meta = array(); // for all tables with meta_key = meta_value like structure for inserting the values
			$meta_data_edited = array();
			$meta_index = 0;
			$old_post_id = '';
			$meta_case_cond = 'CASE post_id ';
			$meta_keys_edited = array(); // array for storing the edited meta_keys

			foreach ($edited_data as $id => $edited_row) {

				$update_params_posts = array();
				$update_params_custom = array(); // for custom tables
				$where_cond = array();
				$insert_post = 0;

				//Code for inserting the post
				if ( empty($id) ) {
					$insert_params_posts = array();
					foreach ($edited_row as $key => $value) {
						$edited_value_exploded = explode("/", $key);
						
						if (empty($edited_value_exploded)) continue;

						$update_table = $edited_value_exploded[0];
						$update_column = $edited_value_exploded[1];

						if ($update_table == 'posts') {
							$insert_params_posts [$update_column] = $value;
						}
					}

					if ( !empty($insert_params_posts) ) {
						$inserted_id = wp_insert_post($insert_params_posts);

						if ( !is_wp_error( $inserted_id ) && !empty($inserted_id) ) {
							$id = $inserted_id;
							$insert_post = 1; //Flag for determining whether post has been inserted	
						} else {
							continue;
						}

					} else {
						continue;
					}
				}

				// if (empty($edited_row['posts/ID'])) continue;

				// $id = $edited_row['posts/ID'];

				foreach ($edited_row as $key => $value) {
					$edited_value_exploded = explode("/", $key);

					if (empty($edited_value_exploded)) continue;

					$update_cond = array(); // for handling the where condition
					$update_params_meta_flag = false; // flag for handling the query for meta_key = meta_value like structure

					$update_table = $edited_value_exploded[0];
					$update_column = $edited_value_exploded[1];

					if (empty($where_cond[$update_table])) {
						$where_cond[$update_table] = (!empty($table_model[$update_table]['pkey']) && $update_column == $table_model[$update_table]['pkey']) ? 'WHERE '. $table_model[$update_table]['pkey'] . ' = ' . $value : '';
					}

					if ( sizeof($edited_value_exploded) > 2) {
						$cond = explode("=",$edited_value_exploded[1]);

						if (sizeof($cond) == 2) {
							$update_cond [$cond[0]] = $cond[1];
						}

						$update_column_exploded = explode("=",$edited_value_exploded[2]);
						$update_column = $update_column_exploded[0];

						$update_params_meta_flag = true;
					}
					
					// handling the update array for posts table
					if ( $update_table == 'posts' && $insert_post != 1 ) {

						if ( empty($update_params_posts[$table_model[$update_table]['pkey']]) && !empty($id) ) {
							$update_params_posts[$table_model[$update_table]['pkey']] = $id;
						}

						$update_params_posts [$update_column] = $value;

					} else if ( $update_params_meta_flag === true ) {

						if (empty($id) || empty($update_cond['meta_key'])) continue;

						$meta_key = $update_cond['meta_key'];

						//Code for handling serialized data
    					if (array_search($meta_key, $data_cols_serialized) !== false) {
							if (!empty($value)) {
								$value = json_decode($value,true);
							}
        				}

						// update_post_meta($id, $meta_key, $value );

						//Code for forming the edited data array
						if ( empty($meta_data_edited[$update_table]) ) {
							$meta_data_edited[$update_table] = array();
						}

						if ( empty($meta_data_edited[$update_table][$id]) ) {
							$meta_data_edited[$update_table][$id] = array();
						}

						$meta_data_edited[$update_table][$id][$update_cond['meta_key']] = $value;
						$meta_keys_edited [$update_cond['meta_key']] = '';

					} else if($update_table == 'terms') {
						//code for handling updates for terms

    					$term_ids = array();

						//Code for handling multiselect data
    					if (array_search($update_column, $data_cols_multiselect) !== false) {

    						$actual_val = (!empty($data_cols_multiselect_val[$update_column])) ? $data_cols_multiselect_val[$update_column] : array();
    						if(empty($value) || empty($actual_val)) continue;
							$edited_values = explode("<br>",$value);
							if (empty($edited_values)) continue;

    					} else if (array_search($update_column, $data_cols_list) !== false) {

    						$actual_val = (!empty($data_cols_list_val[$update_column])) ? $data_cols_list_val[$update_column] : array();
    						if(empty($value) || empty($actual_val)) continue;
							$edited_values = explode("<br>",$value);
							if (empty($edited_values)) continue;
    					}


    					if (!empty($edited_values)) {
    						foreach ($edited_values as $edited_value) {
								$term_id = array_search($edited_value, $actual_val);
								
								if ( $term_id === false) continue;
								$term_ids[] = $term_id;
							}							
    					}

    					if (!empty($term_ids)) {
    						wp_set_object_terms($id, $term_ids, $update_column);
    					}
					}
				}

				//Code for updating the posts table
				if ( !empty($update_params_posts) ) {
					wp_update_post($update_params_posts);
				}
			}

			//Code for updating the meta tables
			if (!empty($meta_data_edited)) {

				foreach ($meta_data_edited as $update_table => $update_params) {

					if (empty($update_params)) continue;

					$post_ids = array_keys($update_params);
					$meta_keys_edited = (!empty($meta_keys_edited)) ? array_keys($meta_keys_edited) : '';

					$update_table_key = ''; //pkey for the update table

					if ( $update_table == 'postmeta' ) {
						$update_table_key = 'post_id';
					}

					//Code for getting the old values and meta_ids
					$old_meta_data = $this->get_meta_data($post_ids, $meta_keys_edited, $update_table, $update_table_key);

					$meta_data = array();

					if (!empty($old_meta_data)) {
						foreach ($old_meta_data as $key => $old_values) {
							foreach ($old_values as $data) {
								if ( empty($meta_data[$key]) ) {
									$meta_data[$key] = array();
								}
								$meta_data[$key][$data['meta_key']] = array();
								$meta_data[$key][$data['meta_key']]['meta_id'] = $data['meta_id'];
								$meta_data[$key][$data['meta_key']]['meta_value'] = $data['meta_value'];
							}
						}
					}

					$meta_index = 0;
					$insert_meta_index = 0;
					$index=0;
					$insert_index=0;
					$old_post_id = '';
					$update_params_index = 0;

					//Code for generating the query
					foreach ($update_params as $id => $updated_data) {

						$updated_data_index = 0;
						$update_params_index++;

						foreach ($updated_data as $key => $value) {
							
							$key = wp_unslash($key);
		    				$value = wp_unslash($value);
		    				$meta_type = 'post';
		    				if ( $update_table == 'postmeta' ) {
		    					$value = sanitize_meta( $key, $value, 'post' );	
		    				}
							
							$updated_data_index++;

							// Filter whether to update metadata of a specific type.
							$check = apply_filters( "update_{$meta_type}_metadata", null, $id, $key, $value, '' );
							if ( null !== $check ) {
								continue;
							}

							// Code for handling if the meta key does not exist
							if ( empty($meta_data[$id][$key] ) ) {

								// Filter whether to add metadata of a specific type.
								$check = apply_filters( "add_{$meta_type}_metadata", null, $id, $key, $value, false );
								if ( null !== $check ) {
									continue;
								}

								if ( empty($insert_params_meta[$update_table]) ) {
									$insert_params_meta[$update_table] = array();
									$insert_params_meta[$update_table][$insert_meta_index] = array();
									$insert_params_meta[$update_table][$insert_meta_index]['values'] = array();
								}

								if ( $insert_index >= 5 && $old_post_id != $id ) {
									$insert_index=0;
									$insert_meta_index++;							
								}

								if ( $old_post_id != $id ) {
									$old_post_id = $id;
									$insert_index++;
								}

								$insert_params_meta[$update_table][$insert_meta_index]['values'][] = array('id' => $id,
																											'meta_key' => $key,
																											'meta_value' => $value);

								$value = maybe_serialize( $value );

								if ( empty($insert_params_meta[$update_table][$insert_meta_index]['query']) ) {
									$insert_params_meta[$update_table][$insert_meta_index]['query'] = "(".$id.", '".$key."', '".$value."')";
								} else {
									$insert_params_meta[$update_table][$insert_meta_index]['query'] .= ", (".$id.", '".$key."', '".$value."')";
								}

								continue;

							} else {
								//Checking if edited value is same as old value
								if ( $meta_data[$id][$key]['meta_value'] == $value ) {
									unset($meta_data[$id][$key]);
									if( empty($meta_data[$id]) ) {
										unset($meta_data[$id]);
									}
									continue;
								} else {
									$meta_data[$id][$key]['meta_value'] = $value;
								}
							}

							$value = maybe_serialize( $value );

							if ( empty($update_params_meta[$update_table]) ) {
								$update_params_meta[$update_table] = array();
								$update_params_meta[$update_table][$meta_index] = array();
								$update_params_meta[$update_table][$meta_index]['ids'] = array();
								$update_params_meta[$update_table][$meta_index]['query'] = '';
							}

							if ( $index >= 5 && $old_post_id != $id ) {
								$update_params_meta[$update_table][$meta_index]['query'] .= ' ELSE meta_value END END ';
								$index=0;
								$meta_index++;							
							}					

							if ( empty($update_params_meta[$update_table][$meta_index]['query']) ) {
								$update_params_meta[$update_table][$meta_index]['query'] = ' CASE post_id ';
							}

							if ( $old_post_id != $id ) {
								
								if ( !empty($index) ) {
									$update_params_meta[$update_table][$meta_index]['query'] .= ' ELSE meta_value END ';
								}

								$update_params_meta[$update_table][$meta_index]['query'] .= " WHEN '".$id."' THEN 
																					CASE meta_key ";

								$old_post_id = $id;
								$update_params_meta[$update_table][$meta_index]['ids'][] = $id;

								$index++;
							}

							$update_params_meta[$update_table][$meta_index]['query'] .= " WHEN '".$key."' THEN '". $value ."' ";

							//Code for the last condition
							if ( $update_params_index == sizeof($update_params) &&  $updated_data_index == sizeof($updated_data) ) {
								$update_params_meta[$update_table][$meta_index]['query'] .= ' ELSE meta_value END END ';
							}

						}
					}

					// Start here... update the actions and query in for loop
					if ( !empty($insert_params_meta) ) {
						foreach ($insert_params_meta as $insert_table => $edited_data) {

							if ( empty($edited_data) ) {
								continue;
							}

							$insert_table_key = (empty($insert_table_key)) ? 'post_id' : $insert_table_key;

							foreach ( $edited_data as $insert_params ) {

								if ( empty($insert_params['values']) || empty($insert_params['query']) ) {
									continue;
								}

								$insert_meta_query = "INSERT INTO {$wpdb->prefix}".$insert_table." (".$insert_table_key.",meta_key,meta_value)
														 VALUES ".$insert_params['query'];

								if ( $insert_table == 'postmeta' ) {
									// function to replicate wordpress add_metadata()
									$this->sm_add_post_meta('post', $insert_params['values'], $insert_meta_query);

								} else {
									$result_insert_meta = $wpdb -> query($insert_meta_query);
								}
							}
						}	
					}

					// Inline data updation for meta tables
					if ( !empty($update_params_meta) ) {
						foreach ($update_params_meta as $update_table => $edited_data) {

							if ( empty($edited_data) ) {
								continue;
							}

							$update_table_key = (empty($update_table_key)) ? 'post_id' : $update_table_key;

							foreach ( $edited_data as $update_params ) {

								if ( empty($update_params['ids']) || empty($update_params['query']) ) {
									continue;
								}

								$update_meta_query = "UPDATE {$wpdb->prefix}$update_table
													SET meta_value = ".$update_params['query']."
													WHERE $update_table_key IN (".implode(',',$update_params['ids']).")";

								if ( $update_table == 'postmeta' ) {
									// function to replicate wordpress update_postmeta()
									$this->sm_update_post_meta('post', $update_params['ids'], $meta_data, $update_meta_query);

								} else {
									$result_update_meta = $wpdb -> query($update_meta_query);
								}
							}
						}	
					}
					
				}
			}

			do_action('sm_inline_update_post',$edited_data);

			$msg_str = '';

			if ( sizeof($edited_data) > 1 ) {
				$msg_str = 's';
			}

			echo sizeof($edited_data).' Record'.$msg_str.' Updated Successfully!';
			exit;
		}

		// Function to replicate wordpress add_metadata()
		// Chk if the function can be made static
		public function sm_add_post_meta($meta_type = 'post', $insert_values, $insert_meta_query) {

			global $wpdb;

			if ( empty($insert_values) ) {
				return;
			}

			// Code for executing actions pre insert
			foreach ( $insert_values as $insert_value ) {
				do_action( "add_{$meta_type}_meta", $insert_value['id'], $insert_value['meta_key'], $insert_value['meta_value'] );
			}

			//Code for inserting the values
			$result_insert_meta = $wpdb->query($insert_meta_query);

			$mid = '';

			// Code for executing actions pre insert
			foreach ( $insert_values as $insert_value ) {
				
				if ( empty($first_insert_id) ) {
					$mid = $wpdb->insert_id;
				}

				wp_cache_delete($insert_value['id'], $meta_type . '_meta');
				do_action( "added_{$meta_type}_meta", $mid, $insert_value['id'], $insert_value['meta_key'], $insert_value['meta_value'] );

				$mid++;

			}
			return;
		}

		// Function to replicate wordpress update_postmeta()
		// Chk if the function can be made static
		public function sm_update_post_meta($meta_type = 'post', $update_ids, $meta_data, $update_meta_query) {
			
			global $wpdb;

			if ( empty($update_ids) || empty($meta_data) || empty($update_meta_query) ) {
				return;
			}

			// Code for executing actions pre update
			foreach ( $update_ids as $id ) {
				
				if ( empty($meta_data[$id]) ) {
					continue;
				}

				foreach ( $meta_data[$id] as $meta_key => $value ) {

					do_action( "update_{$meta_type}_meta", $value['meta_id'], $id, $meta_key, $value['meta_value'] );
					$meta_value = maybe_serialize( $value['meta_value'] );

					if ( 'post' == $meta_type ) {
						do_action( 'update_postmeta', $value['meta_id'], $id, $meta_key, $meta_value );
					}
					
				}
			}

			$result_update_meta = $wpdb -> query($update_meta_query);

			// Code for executing actions post update
			foreach ( $update_ids as $id ) {
				
				if ( empty($meta_data[$id]) ) {
					continue;
				}

				wp_cache_delete($id, $meta_type . '_meta');

				foreach ( $meta_data[$id] as $meta_key => $value ) {

					do_action( "updated_{$meta_type}_meta", $value['meta_id'], $id, $meta_key, $value['meta_value'] );
					$meta_value = maybe_serialize( $value['meta_value'] );

					if ( 'post' == $meta_type ) {
						do_action( 'updated_postmeta', $value['meta_id'], $id, $meta_key, $meta_value );
					}
					
				}
			}
			return;
		}

		// Function to handle the delete data functionality
		public function delete() {

			global $wpdb;

			$delete_ids = (!empty($this->req_params['ids'])) ? json_decode(stripslashes($this->req_params['ids']), true) : array();
			
			if (empty($delete_ids)) return;

			// Code for delete the data
			foreach ( $delete_ids as $delete_id ) {
				wp_trash_post ( $delete_id );
			}

			$msg_str = '';

			if ( sizeof($delete_ids) > 1 ) {
				$msg_str = 's';
			}

			echo sizeof($delete_ids).' Record'.$msg_str.' Deleted Successfully!';
			exit;
		}

	}

	// $GLOBALS['smart_manager_base'] = Smart_Manager_Base::getInstance();
	// if ( !isset( $GLOBALS['smart_manager_base'] ) ) {
	// }
}

?>
