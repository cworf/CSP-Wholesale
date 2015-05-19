<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'BVM_Operation_Old' ) ) {

	class BVM_Operation_Old {

		function __construct() {

			add_action( 'wp_ajax_sa_bulk_add_update_attributes_old', array( $this, 'bulk_add_update_attributes' ) );
            add_action( 'wp_ajax_get_product_ids_from_categories_old', array( $this, 'get_product_ids_from_categories' ) );
            add_action( 'wp_ajax_add_update_product_attributes_old', array( $this, 'add_update_product_attributes' ) );
            add_action( 'wp_ajax_bulk_add_new_products_and_attributes_old', array( $this, 'bulk_add_new_products_and_attributes' ) );
            add_action( 'wp_ajax_get_possible_variations_old', array( $this, 'get_possible_variations' ) );
            add_action( 'wp_ajax_create_update_variation_old', array( $this, 'create_update_variation' ) );
            add_action( 'wp_ajax_sync_created_updated_variable_product_old', array( $this, 'sync_created_updated_variable_product' ) );
            add_action( 'wp_ajax_finalize_bulk_create_update_variations_old', array( $this, 'finalize_bulk_create_update_variations' ) );

		}

		/**
         * to handle WC compatibility related function call from appropriate class
         * 
         * @param $function_name string
         * @param $arguments array of arguments passed while calling $function_name
         * @return result of function call
         * 
         */
        public function __call( $function_name, $arguments = array() ) {

            if ( ! is_callable( 'SA_WC_Compatibility_2_3', $function_name ) ) return;

            if ( ! empty( $arguments ) ) {
                return call_user_func_array( 'SA_WC_Compatibility_2_3::'.$function_name, $arguments );
            } else {
                return call_user_func( 'SA_WC_Compatibility_2_3::'.$function_name );
            }

        }

        function woocommerce_variations_page() {
			global $wpdb;

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
            $assets_path    = str_replace( array( 'http:', 'https:' ), '', $this->global_wc()->plugin_url() ) . '/assets/';

            if ( $this->is_wc_gte_21() ) {

                // Register scripts
                wp_register_script( 'woocommerce_admin', $this->global_wc()->plugin_url() . '/assets/js/admin/woocommerce_admin' . $suffix . '.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip' ), $this->global_wc()->version );
				wp_register_script( 'woocommerce_admin_meta_boxes', $this->global_wc()->plugin_url() . '/assets/js/admin/meta-boxes' . $suffix . '.js', array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable', 'accounting', 'round' ), $this->get_wc_version() );

                $params = array ('ajax_url' => admin_url( 'admin-ajax.php' ), 'search_products_nonce' => wp_create_nonce( "search-products" ) );

				if ( $this->is_wc_gte_23() ) {
					wp_register_script( 'select2', '//cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.min.js', array( 'jquery' ), '3.5.2' );
					wp_register_script( 'wc-enhanced-select', WC()->plugin_url() . '/assets/js/admin/wc-enhanced-select' . $suffix . '.js', array( 'jquery', 'select2' ), WC_VERSION );
					wp_localize_script( 'select2', 'wc_select_params', array(
						'i18n_matches_1'            => _x( 'One result is available, press enter to select it.', 'enhanced select', 'wc_smart_coupons' ),
						'i18n_matches_n'            => _x( '%qty% results are available, use up and down arrow keys to navigate.', 'enhanced select', 'wc_smart_coupons' ),
						'i18n_no_matches'           => _x( 'No matches found', 'enhanced select', 'wc_smart_coupons' ),
						'i18n_ajax_error'           => _x( 'Loading failed', 'enhanced select', 'wc_smart_coupons' ),
						'i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters', 'enhanced select', 'wc_smart_coupons' ),
						'i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters', 'enhanced select', 'wc_smart_coupons' ),
						'i18n_input_too_long_1'     => _x( 'Please delete 1 character', 'enhanced select', 'wc_smart_coupons' ),
						'i18n_input_too_long_n'     => _x( 'Please delete %qty% characters', 'enhanced select', 'wc_smart_coupons' ),
						'i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', 'wc_smart_coupons' ),
						'i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', 'wc_smart_coupons' ),
						'i18n_load_more'            => _x( 'Loading more results&hellip;', 'enhanced select', 'wc_smart_coupons' ),
						'i18n_searching'            => _x( 'Searching&hellip;', 'enhanced select', 'wc_smart_coupons' ),
					) );
					wp_localize_script( 'wc-enhanced-select', 'wc_enhanced_select_params', $params );

					$locale  = localeconv();
					$decimal = isset( $locale['decimal_point'] ) ? $locale['decimal_point'] : '.';

					$woocommerce_admin_params = array(
						'i18n_decimal_error'                => sprintf( __( 'Please enter in decimal (%s) format without thousand separators.', 'wc_smart_coupons' ), $decimal ),
						'i18n_mon_decimal_error'            => sprintf( __( 'Please enter in monetary decimal (%s) format without thousand separators and currency symbols.', 'wc_smart_coupons' ), wc_get_price_decimal_separator() ),
						'i18n_country_iso_error'            => __( 'Please enter in country code with two capital letters.', 'wc_smart_coupons' ),
						'i18_sale_less_than_regular_error'  => __( 'Please enter in a value less than the regular price.', 'wc_smart_coupons' ),
						'decimal_point'                     => $decimal,
						'mon_decimal_point'                 => wc_get_price_decimal_separator()
					);

					wp_localize_script( 'woocommerce_admin', 'woocommerce_admin', $woocommerce_admin_params );
				} else {
					wp_register_script( 'ajax-chosen', $this->global_wc()->plugin_url() . '/assets/js/chosen/ajax-chosen.jquery' . $suffix . '.js', array('jquery', 'chosen'), $this->global_wc()->version );
	                wp_register_script( 'chosen', $this->global_wc()->plugin_url() . '/assets/js/chosen/chosen.jquery' . $suffix . '.js', array('jquery'), $this->global_wc()->version );
	            }
                
                wp_enqueue_script( 'woocommerce_admin' );
                wp_enqueue_script( 'woocommerce_admin_meta_boxes' );
                
                if ( $this->is_wc_gte_23() ) {
					wp_enqueue_script( 'select2' );
					wp_enqueue_script( 'wc-enhanced-select' );
				} else {
					wp_enqueue_script( 'ajax-chosen' );
	                wp_enqueue_script( 'chosen' );
	            }

                wp_localize_script( 'woocommerce_admin_meta_boxes', 'woocommerce_admin_meta_boxes', $params );
                
				if ( $this->is_wc_gte_23() ) {
					wp_enqueue_style( 'select2', $assets_path . 'css/select2.css' );
				} else {
					wp_enqueue_style( 'woocommerce_chosen_styles', $assets_path . 'css/chosen.css' );
				}
                
            } else {

                // Register scripts
				wp_register_script( 'woocommerce_admin', $this->global_wc()->plugin_url() . '/assets/js/admin/woocommerce_admin' . $suffix . '.js', array ('jquery', 'jquery-ui-widget', 'jquery-ui-core' ), '1.0' );
				wp_register_script( 'woocommerce_writepanel', $this->global_wc()->plugin_url() . '/assets/js/admin/write-panels' . $suffix . '.js', array ('jquery' ) );
				wp_register_script( 'ajax-chosen', $this->global_wc()->plugin_url() . '/assets/js/chosen/ajax-chosen.jquery' . $suffix . '.js', array ('jquery' ), '1.0' );
				
				wp_enqueue_script( 'woocommerce_admin' );
				wp_enqueue_script( 'woocommerce_writepanel' );
				wp_enqueue_script( 'ajax-chosen' );
				
				$woocommerce_witepanel_params = array ('ajax_url' => admin_url( 'admin-ajax.php' ), 'search_products_nonce' => wp_create_nonce( "search-products" ) );
				
				wp_localize_script( 'woocommerce_writepanel', 'woocommerce_writepanel_params', $woocommerce_witepanel_params );
				
				wp_enqueue_style( 'woocommerce_chosen_styles', $this->global_wc()->plugin_url() . '/assets/css/chosen.css' );
                
            }

			wp_enqueue_style( 'woocommerce_admin_styles', $this->global_wc()->plugin_url() . '/assets/css/admin.css' );
			wp_enqueue_style( 'jquery-ui-style', (is_ssl()) ? 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' : 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );
                            
            // Adding style for help tip for WC 2.0
            if ( $this->is_wc_gte_20() ) {
                $style = "width:16px;height=16px;" ; 
            } else {
                $style = '';
            }

			$query = "SELECT wat.attribute_label AS attribute_label, tt.taxonomy AS taxonomy, tt.term_taxonomy_id AS term_taxonomy_id, t.name AS term_name, t.slug AS term_slug
						FROM {$wpdb->prefix}woocommerce_attribute_taxonomies AS wat
						LEFT JOIN {$wpdb->prefix}term_taxonomy AS tt ON ( tt.taxonomy = CONCAT( 'pa_', wat.attribute_name ) )
						LEFT JOIN {$wpdb->prefix}terms AS t ON ( t.term_id = tt.term_id )
						";
			$attribute_results = $wpdb->get_results($query, 'ARRAY_A');
			
			$attributes = array();
			$attributes_to_terms = array();
			foreach ( $attribute_results as $attribute_result ) {
				if ( !in_array( $attribute_result['attribute_label'], $attributes, true ) ) {
					$attributes[$attribute_result['taxonomy']] = $attribute_result['attribute_label'];
				}
				if ( !isset( $attributes_to_terms[$attribute_result['taxonomy']] ) ) {
					$attributes_to_terms[$attribute_result['taxonomy']] = array();
				}
				$attributes_to_terms[$attribute_result['taxonomy']][$attribute_result['term_taxonomy_id']] = array(
																			'term_name' => $attribute_result['term_name'],
																			'term_slug' => $attribute_result['term_slug']
																		);
			}

			if ( !wp_script_is( 'jquery' ) ) {
				wp_enqueue_script( 'jquery' );
				wp_enqueue_style( 'jquery' );
			}

            if ( !wp_script_is( 'thickbox' ) ) {
                if ( !function_exists( 'add_thickbox' ) ) {
                    require_once ABSPATH . 'wp-includes/general-template.php';
                }
                add_thickbox();
            }

            if ( !wp_script_is( 'jquery-ui-progressbar' ) ) {
            	wp_enqueue_script( 'jquery-ui-progressbar' );
            }

			?>
			<div class="wrap">
			<div id="icon-index" class="icon32"><br/></div>
			<h2><?php _e( 'WooCommerce Bulk Variations Manager', 'sa_bulk_variation' ); ?></h2>
			<small><?php _e( 'This will use earlier logic which may be slow but is better tested.', 'sa_bulk_variation' ); ?></small>
            <div>
				<p style="text-align: right;">
                    <a href="<?php echo remove_query_arg( 'bvm_version', admin_url( 'edit.php?'.$_SERVER['QUERY_STRING'] ) ); ?>" title="<?php _e( 'Switch to new version', 'sa_bulk_variation' ); ?>"><?php echo __( 'Switch back to new version', 'sa_bulk_variation' ); ?></a>
                    | <a href="<?php echo admin_url() . '#TB_inline?inlineId=sa_bulk_variations_post_query_form&height=550&width=600'; ?>" class="thickbox" title="<?php _e( 'Send your query', 'sa_bulk_variation' ); ?>" target="_blank"><?php echo __( 'Need Help?', 'sa_bulk_variation' ); ?></a>
                    | <a href="http://www.storeapps.org/support/documentation/bulk-variations-manager/" title="Documentation" target="_blank"><?php echo __( 'Docs', 'sa_bulk_variation' ); ?></a>
                    | <a href="http://demo.storeapps.org/" title="Bulk Variations Manager Demo" target="_blank"><?php echo __( 'Demo', 'sa_bulk_variation' ); ?></a>
				</p>
            </div>
			
			<?php
				
				if ( isset( $_POST['create_variations'] ) && !empty( $_POST['create_variations'] ) ) {
					
					if ( $return ) {
						?>
						<div id="notice" class="error">
							<p><?php echo '<strong>'.__( 'Error', 'sa_bulk_variation' ).': </strong> '.implode( ', ', $reason ); ?></p>
						</div>
						<?php
					} else {

						

					}
				}

			?>

			<form id="bulk_variations_manager_form" action="" method="post">
			<style>
			form#bulk_variations_manager_form {
 				padding-bottom: 5em;
 			}
			td.col1 {
				width: 25%;
			}
			td.col2 {
				width: 75%;
			}
			textarea#product_names {
				vertical-align: top;
			}
			div#product_names,
			div#search,
			div#search_result,
			div#categories {
				display: none;
			}
			#product_names_table th {
				text-align: left;
			}
			div#search_result,
			div#categories {
				max-height: 300px;
				margin-top: 10px;
				overflow-y: scroll;
			}
			div#additional_field {
				margin-top: 10px;
			}
			input[id^="price_"] {
				float: right;
			}
			ul.terms_list,
			div#attribute_header {
				width: 40%;
			}
			ul.categorychecklist li {
				line-height: 2em;
			}
			div#search_result ul,
			ul#product_catchecklist {
				padding: 0em 1.3em;
			}
			div#search_result,
			div#categories {
				border-style: solid;
				border-width: 2px;
				border-color: lightgrey;
			}
			div#attribute_header .right {
				float: right;
			}
			div#attribute_header label {
				font-size: 1.1em;
			}
			ul.terms_list li label {
				/*max-width: 10%;*/
			}
			img.help_tip{
				<?php echo $style; ?>
			}
			div#price {
				display: none;
			}
			input.add_row,
			input.remove_row {
				width: 30px;
			}
            table.attributes_to_price {
                width: 100%;

            }
            table.attributes_to_price tr td {
                vertical-align: top;
            }
            .close:before {
            	content: "\f153";
				display: inline-block;
				-webkit-font-smoothing: antialiased;
				font: normal 30px/1 'dashicons';
				vertical-align: top;
				float: right;
            }
            .ui-progressbar {
		      	position: relative;
		    }
		    .progress-label {
		      	position: absolute;
		      	height: 50%;
		      	left: 40%;
		      	top: 4px;
		      	font-weight: bold;
		      	text-shadow: 1px 1px 0 #fff;
		      	padding-left: 20px;
				padding-bottom: 5px;
		    }
		    #modal {
		    	position: absolute;
		        top: 0%;
		        left: 0%;
		        width: 150%;
		        height: 100%;
		        margin-top: 0; 
		        margin-left: -50%; 
		        z-index: 99;
		        background-color: black;
		        opacity: 0.6;
		        display: none;
		    }
		    #progressbar {
		    	position: fixed;
		        bottom: 50%;
		        left: 25%;
		        width: 50%;
		        border: 0px solid #ccc;
		        background-color: white;
		        z-index: 100;
		        display: none;
		    }
		    #progressbar .status {
		    	position: absolute;
				overflow-wrap: break-word;
				width: 96%;
				height: 50%;
				left: 2%;
				top: 125%;
				color: white;
				display: none;
		    }
            </style>
			<script type="text/javascript">
				jQuery(function(){
					jQuery('input#search_button').live('click', function(){
						var search_text = jQuery('input#search_text').val();
						jQuery('img#loader').show();
						jQuery.ajax({
							url: '<?php echo admin_url( "admin-ajax.php" ); ?>',
							type: 'GET',
							dataType: 'json',
							data: {
								action: 'woocommerce_json_search_products',
								security: '<?php echo wp_create_nonce("search-products"); ?>',
								term: search_text
							},
							success: function( data ) {
								var search_content = '';
								if ( jQuery.isEmptyObject(data) ) {
									search_content += '<?php _e( "No match found", "sa_bulk_variation" ); ?>';
								} else {
									search_content += '<ul>';
									jQuery.each( data, function( index, value ){
										search_content += '<li><input type="checkbox" id="product-'+index+'" name="product[]" value="'+index+'"> <label for="product-'+index+'">'+value+'</label></li>';
									});
									search_content += '</ul>';
								}
								jQuery('div#search_result').text('');
								jQuery('div#search_result').append(search_content);
								jQuery('div#search_result').show();
								jQuery('img#loader').hide();
							}
						});
					});

					var isShowBasePrice = function( isShow ) {
						if ( isShow ) {
							jQuery('div#price').show();
						} else {
							jQuery('div#price').hide();
						}
					};

					jQuery('input[name=selected_option]').on('click', function(){
						jQuery('div#search_result').text('');
						var selected_value = jQuery(this).val();
						switch ( selected_value ) {
							case 'product_names':
								jQuery('div#search').slideUp();
								jQuery('div#categories').slideUp();
								jQuery('div#product_names').slideDown();
								isShowBasePrice( false );
								break;
							case 'search':
								jQuery('div#product_names').slideUp();
								jQuery('div#categories').slideUp();
								jQuery('div#search').slideDown();
								isShowBasePrice( true );
								break;
							case 'categories':
								jQuery('div#search').slideUp();
								jQuery('div#product_names').slideUp();
								jQuery('div#categories').slideDown();
								isShowBasePrice( true );
								break;
							default:
								jQuery('div#search').slideUp();
								jQuery('div#product_names').slideUp();
								jQuery('div#categories').slideUp();
								isShowBasePrice( false );
								break;
						}
					});

					jQuery('input.attribute').click(function(){
		                var isChecked = jQuery(this).is(':checked');
		                if ( isChecked == true ) {
		                    jQuery(this).parents('li').find('input.term').attr('checked', 'checked');
		                    jQuery(this).parents('li').find('input.price').removeAttr('readonly');
		                } else {
		                    jQuery(this).parents('li').find('input.term').removeAttr('checked');
		                    jQuery(this).parents('li').find('input.price').attr('readonly', 'readonly');
		                }
		            });
		            
		            jQuery('input.term').click(function(){
		                var isChecked = jQuery(this).is(':checked');
		                if ( isChecked == false ) {
		                    jQuery(this).parents('li').find('input.attribute').removeAttr('checked');
		                } else {
		                    var countCheckedItems = jQuery(this).parents('ul.terms_list').children().find('input.term:checked').length;
		                    var countTotalItems = jQuery(this).parents('ul.terms_list').children().find('input.term').length;
		                    
		                    if ( countCheckedItems == countTotalItems ) {
		                        jQuery(this).parents('li').find('input.attribute').attr( 'checked', 'checked' );
		                    }
		                }
		            });

		            jQuery('input[id^="terms_"]').on('click', function(){
		            	var term_id = jQuery(this).attr('id').substring(6);
		            	if ( jQuery(this).is(':checked') ) {
		            		jQuery('input[id$="_'+term_id+'"]').removeAttr('readonly');
		            	} else {
		            		jQuery('input[id$="_'+term_id+'"]').attr('readonly', 'readonly');
		            	}
		            });

		            jQuery('input.add_row').on('click', function(){
		            	jQuery('table#product_names_table tbody').append(
		            			'<tr>\
									<td><input type="text" name="product_names[]" size="50" value="" placeholder="<?php _e( 'Enter a product&lsquo;s name', 'sa_bulk_variation' ); ?>..." /></td>\
									<td><input type="number" step="any" name="base_price[]" min="0" value="" placeholder="0.00" /></td>\
									<td><input type="button" class="remove_row" value="&#215;" /></td>\
								</tr>'
		            		);
		            });

		            jQuery('input.remove_row').live('click', function(){
		            	jQuery(this).parent().parent().remove();
		            });

		            <?php if ( $this->is_wc_gte_23() ) { ?>

						if ( typeof getEnhancedSelectFormatString == "undefined" ) {
							function getEnhancedSelectFormatString() {
								var formatString = {
									formatMatches: function( matches ) {
										if ( 1 === matches ) {
											return wc_select_params.i18n_matches_1;
										}

										return wc_select_params.i18n_matches_n.replace( '%qty%', matches );
									},
									formatNoMatches: function() {
										return wc_select_params.i18n_no_matches;
									},
									formatAjaxError: function( jqXHR, textStatus, errorThrown ) {
										return wc_select_params.i18n_ajax_error;
									},
									formatInputTooShort: function( input, min ) {
										var number = min - input.length;

										if ( 1 === number ) {
											return wc_select_params.i18n_input_too_short_1
										}

										return wc_select_params.i18n_input_too_short_n.replace( '%qty%', number );
									},
									formatInputTooLong: function( input, max ) {
										var number = input.length - max;

										if ( 1 === number ) {
											return wc_select_params.i18n_input_too_long_1
										}

										return wc_select_params.i18n_input_too_long_n.replace( '%qty%', number );
									},
									formatSelectionTooBig: function( limit ) {
										if ( 1 === limit ) {
											return wc_select_params.i18n_selection_too_long_1;
										}

										return wc_select_params.i18n_selection_too_long_n.replace( '%qty%', number );
									},
									formatLoadMore: function( pageNumber ) {
										return wc_select_params.i18n_load_more;
									},
									formatSearching: function() {
										return wc_select_params.i18n_searching;
									}
								};

								return formatString;
							}
						}

						// Ajax product search box
						jQuery( ':input.wc-product-with-status-search' ).filter( ':not(.enhanced)' ).each( function() {
							var select2_args = {
								allowClear:  jQuery( this ).data( 'allow_clear' ) ? true : false,
								placeholder: jQuery( this ).data( 'placeholder' ),
								minimumInputLength: jQuery( this ).data( 'minimum_input_length' ) ? jQuery( this ).data( 'minimum_input_length' ) : '3',
								escapeMarkup: function( m ) {
									return m;
								},
								ajax: {
							        url:         '<?php echo admin_url("admin-ajax.php"); ?>',
							        dataType:    'json',
							        quietMillis: 250,
							        data: function( term, page ) {
							            return {
											term:     term,
											action:   jQuery( this ).data( 'action' ) || 'json_search_products_with_status',
											status: 		'<?php echo serialize( array( "publish", "draft" ) ); ?>',
											security: 		'<?php echo wp_create_nonce( "ajax-search-products-with-status" ); ?>'
							            };
							        },
							        results: function( data, page ) {
							        	var terms = [];
								        if ( data ) {
											jQuery.each( data, function( id, text ) {
												terms.push( { id: id, text: text } );
											});
										}
							            return { results: terms };
							        },
							        cache: true
							    }
							};

							if ( jQuery( this ).data( 'multiple' ) === true ) {
								select2_args.multiple = true;
								select2_args.initSelection = function( element, callback ) {
									var data     = jQuery.parseJSON( element.attr( 'data-selected' ) );
									var selected = [];

									jQuery( element.val().split( "," ) ).each( function( i, val ) {
										selected.push( { id: val, text: data[ val ] } );
									});
									return callback( selected );
								};
								select2_args.formatSelection = function( data ) {
									return '<div class="selected-option" data-id="' + data.id + '">' + data.text + '</div>';
								};
							} else {
								select2_args.multiple = false;
								select2_args.initSelection = function( element, callback ) {
									var data = {id: element.val(), text: element.attr( 'data-selected' )};
									return callback( data );
								};
							}

							select2_args = jQuery.extend( select2_args, getEnhancedSelectFormatString() );

							jQuery( this ).select2( select2_args ).addClass( 'enhanced' );
						});

					<?php } else { ?>

						jQuery("select.ajax_chosen_select_products_with_status").ajaxChosen({
						    method: 	'GET',
						    url: 		'<?php echo admin_url( "admin-ajax.php" ); ?>',
						    dataType: 	'json',
						    afterTypeDelay: 100,
						    data:		{
						    	action: 		'json_search_products_with_status',
						    	status: 		'<?php echo serialize( array( "publish", "draft" ) ); ?>',
								security: 		'<?php echo wp_create_nonce( "ajax-search-products-with-status" ); ?>'
						    }
						}, function (data) {

							var terms = {};

						    jQuery.each(data, function (i, val) {
						        terms[i] = val;
						    });

						    return terms;
						});

					<?php } ?>

		            var increment_progress = function( current, total ) {
		            	try{
			            	var progressbar = jQuery('#progressbar'),
						      progressLabel = jQuery('.progress-label');
						 
						 	progressbar.progressbar({
						      	value: false,
						      	change: function() {
						      	  	progressLabel.text( progressbar.progressbar('value') + '%' );
						      	},
						      	complete: function() {
						      	  	progressLabel.text('Completed!');
						      	  	setTimeout( function(){
						      	  		hideProgressbar();
						      	  		jQuery('div#form_options').find('input[type="text"], input[type="number"]').val('');
						      	  		document.title = "<?php _e( 'WooCommerce Bulk Variations Manager', 'sa_bulk_variation' ); ?>";
						      	  	}, 2000 );
						      	}
						    });

							var new_value = current * 100 / total;

							if ( new_value.toFixed ) {
								new_value = Number(new_value.toFixed(2));
							} else {
								new_value = Math.round( new_value );
							}
							document.title = new_value + '% completed...';
						    progressbar.progressbar( 'value', new_value );
					    } catch( error ) {
							jQuery('.progress-label').text('Failed!');
							jQuery('span.close').show();
							jQuery('#progressbar .status').text('Error: '+error.toString());
							return false;
						}
		            };

		            function getNewProductIds( ajax_url, next_action, post, product_names, product_attributes, progress, final_progress_value ) {
					    try {
						    var product_ids;
						    jQuery.ajax({
			            		url: ajax_url,
			            		type: 'post',
			            		dataType: 'json',
			            		async: false,
			            		data: {
			            			action: next_action,
			            			post: post,
			            			product_names: product_names,
			            			product_attributes: product_attributes
			            		},
			            		success: function( response ) {
			            			if ( response.error == 'true' ) {
			            				jQuery('.progress-label').text('Failed!');
			            				jQuery('span.close').show();
			            				jQuery('#progressbar .status').text('Error: '+response.data.msg);
			            			} else {
			            				product_ids = response.data.product_ids;
			            			}
			            		}
			            	});
		            	} catch( error ) {
							jQuery('.progress-label').text('Failed!');
							jQuery('span.close').show();
							jQuery('#progressbar .status').text('Error: '+error.toString());
							return false;
						}
					    return product_ids;
					}

		            function getProductCountFromCategories( ajax_url, form_data ) {
					    try {
						    var product_count;
						    jQuery.ajax({
			            		url: ajax_url,
			            		type: 'post',
			            		dataType: 'json',
			            		async: false,
			            		data: {
			            			action: 'get_product_ids_from_categories_old',
			            			post: form_data
			            		},
			            		success: function( response ) {
			            			product_count = response;
			            		}
			            	});
		            	} catch( error ) {
							jQuery('.progress-label').text('Failed!');
							jQuery('span.close').show();
							jQuery('#progressbar .status').text('Error: '+error.toString());
							return false;
						}
					    return product_count;
					}

					function get_estimated_time( final_progress_value ) {
						var estimated_seconds = final_progress_value * 1;
						
						var dd = Math.floor(estimated_seconds/86400);
						var hh = Math.floor(((estimated_seconds/86400)%1)*24);
						var mm = Math.floor(((estimated_seconds/3600)%1)*60);
						var ss = Math.round(((estimated_seconds/60)%1)*60);

						var time = [];
						if ( dd != 0 ) {
							time.push( dd + "d" );
						}
						if ( hh != 0 ) {
							time.push( hh + "h" );
						}
						if ( mm != 0 ) {
							time.push( mm + "m" );
						}
						if ( ss != 0 ) {
							time.push( ss + "s" );
						}
						return time.join( ' ' );
					}

		            var hideProgressbar = function() {
                    	jQuery('#progressbar').hide();
                        jQuery('.progress-label').hide();
                        jQuery('#progressbar .status').hide();
                        jQuery('#modal').hide();
                        jQuery('span.close').hide();
                    };

                    jQuery('span.close').on('click', function(){
                    	hideProgressbar();
                    });

                    jQuery('input#create_variations').on('click', function(){
		            	try {

            		        jQuery('#progressbar').progressbar({
            		        	value: 0
            		        }).show();
            		        jQuery('.progress-label').text('<?php _e( "Please wait...", "sa_bulk_variation" ) ?>').show();
			            	jQuery('#progressbar .status').show();
			            	jQuery('#modal').show();
			            	jQuery('span.close').hide();

			            	var form_data = jQuery('form#bulk_variations_manager_form').serialize();
			            	var ajax_url = '<?php echo admin_url("admin-ajax.php"); ?>';
			            	var selected_value = jQuery('input[name=selected_option]:checked').val();
			            	var multiplying_factor;
			            	var product_count;
			            	var chosen_text = '<?php echo ( $this->is_wc_gte_21() ) ? "chosen" : "chzn" ?>';

			            	if ( selected_value == 'product_names' ) {
			            		multiplying_factor = 6;
			            		product_count = jQuery('table#product_names_table tbody tr').length - 1;
			            	} else if ( selected_value == 'categories' ) {
			            		multiplying_factor = 5;
			            		product_count = getProductCountFromCategories( ajax_url, form_data );
			            	} else {
			            		multiplying_factor = 5;
			            		<?php if ( $this->is_wc_gte_23() ) { ?>
                                	product_count = jQuery('div.wc-product-with-status-search ul.select2-choices li.select2-search-choice').length;
                            	<?php } else { ?>
			            			product_count = jQuery('div#product_ids_' + chosen_text + ' ul.' + chosen_text + '-choices li.search-choice span').length;
                            	<?php } ?>
			            	}

			            	var attribute_count = jQuery('ul.attribute_list li').length;
			            	var term_counts = [];
			            	var k = 0;
			            	var term_count;

			            	for ( k = 1; k <= attribute_count; k++ ) {
			            		term_count = jQuery('ul.attribute_list li:nth-child('+k+') ul.terms_list li table tbody tr td label input[id^="terms_"]:checked').length;	
			            		if ( term_count > 0 ) {
				            		term_counts.push( term_count );	
				            	}
			            	}

			            	var l = 0;
			            	var variations_count = 1;

			            	for ( l = 0; l < term_counts.length; l++ ) {
			            		variations_count = Number( variations_count ) * Number( term_counts[l] );
			            	}

			            	var final_progress_value = ( multiplying_factor * product_count ) + ( product_count * variations_count );

			            	// var estimated_time = get_estimated_time( final_progress_value );
			            	// jQuery('#progressbar .status').text('<?php _e( "Estimated time ' + estimated_time + ' It may vary depending on your system\'s processing speed", "sa_bulk_variation" ) ?>').attr('title', '<?php _e( "Estimated time may vary depending on your system\'s processing power", "sa_bulk_variation" ); ?>').show();

			            	var progress = 0;
			            	var updated = 0;
			            	var added = 0;

			            	setTimeout( function(){
				            	jQuery.ajax({
				            		async: false,
				            		url: ajax_url,
				            		type: 'post',
				            		dataType: 'json',
				            		data: {
				            			action: 'sa_bulk_add_update_attributes_old',
				            			post: form_data
				            		},
				            		success: function( response ) {
				            			try {
					            			if ( response.error == 'true' ) {
				            					jQuery('.progress-label').text('Failed!');
				            					jQuery('span.close').show();
				            					jQuery('#progressbar .status').text('Error: '+response.data.msg);
					            			} else {
					            				var product_ids;
					            				var next_action;

					            				jQuery('#progressbar').css('border-width', '1px');

					            				if ( response.data.product_names != undefined ) {
					            					product_ids = getNewProductIds( ajax_url, response.data.next_action, response.data.post, response.data.product_names, response.data.product_attributes, progress, final_progress_value );
					            					next_action = 'add_update_product_attributes_old';
					            					progress += product_ids.length;
						            				increment_progress( progress, final_progress_value );
					            				} else {
					            					product_ids = response.data.product_ids;
					            					next_action = response.data.next_action;
					            				}

					            				var product_id;
					            				var post = response.data.post;
					            				var i = 0;

					            				for ( i = 0; i < product_ids.length; i++ ) {
					            					product_id = product_ids[i];
					            					jQuery.ajax({
					            						async: false,
									            		url: ajax_url,
									            		type: 'post',
									            		dataType: 'json',
									            		data: {
									            			action: next_action,
									            			post: post,
									            			product_id: product_id
									            		},
									            		success: function( response ) {
									            			try {
										            			if ( response.error == 'true' ) {
					            									jQuery('.progress-label').text('Failed!');
					            									jQuery('span.close').show();
					            									jQuery('#progressbar .status').text('Error: '+response.data.msg);
										            			} else {
										            				var variations = response.data.variations;
										            				var _product = response.data._product;
										            				var available_variations = response.data.available_variations;
																	var id_to_variations = response.data.id_to_variations;
																	var parent_product_price = response.data.parent_product_price;
																	var variation_post_data = response.data.variation_post_data;
																	var variation_ids = response.data.variation_ids;

										            				jQuery.ajax({
										            					async: false,
													            		url: ajax_url,
													            		type: 'post',
													            		dataType: 'json',
													            		data: {
													            			action: response.data.next_action,
													            			post: post,
													            			variations: variations,
													            			product_id: product_id
													            		},
													            		success: function( response ) {
													            			try {
														            			if ( response.error == 'true' ) {
						            												jQuery('.progress-label').text('Failed!');
						            												jQuery('span.close').show();
						            												jQuery('#progressbar .status').text('Error: '+response.data.msg);
														            			} else {
														            				var possible_variations = response.data.possible_variations;
														            				var j = 0;

														            				for ( j = 0; j < possible_variations.length; j++ ) {
															            				jQuery.ajax({
															            					async: false,
																		            		url: ajax_url,
																		            		type: 'post',
																		            		dataType: 'json',
																		            		data: {
																		            			action: response.data.next_action,
																		            			post: post,
																		            			updated: updated,
																								added: added,
																								variation: possible_variations[j],
																								available_variations: available_variations,
																								id_to_variations: id_to_variations,
																								parent_product_price: parent_product_price,
																								variation_post_data: variation_post_data,
																								variation_ids: variation_ids
																		            		},
																		            		success: function( response ) {
																		            			try {
																			            			if ( response.error == 'true' ) {
							            																jQuery('.progress-label').text('Failed!');
							            																jQuery('span.close').show();
							            																jQuery('#progressbar .status').text('Error: '+response.data.msg);
																			            			} else {
																			            				variation_ids = response.data.variation_ids;
																			            				updated = response.data.updated;
																			            				added = response.data.added;
																			            				progress++;
																			            				increment_progress( progress, final_progress_value );
																			            			}
																		            			} catch( error ) {
																									jQuery('.progress-label').text('Failed!');
																									jQuery('span.close').show();
																									jQuery('#progressbar .status').text('Error: '+error.toString());
																									return false;
																								}
																		            		}
																		            	});
																					}
																					
																					jQuery.ajax({
																						async: false,
																	            		url: ajax_url,
																	            		type: 'post',
																	            		dataType: 'json',
																	            		data: {
																	            			action: 'sync_created_updated_variable_product_old',
																	            			product_id: product_id
																	            		},
																	            		success: function( response ) {
																	            			try {
																		            			if ( response.error == 'true' ) {
							            															jQuery('.progress-label').text('Failed!');
							            															jQuery('span.close').show();
							            															jQuery('#progressbar .status').text('Error: '+response.data.msg);
																		            			} else {
																		            				jQuery.ajax({
																		            					async: false,
																					            		url: ajax_url,
																					            		type: 'post',
																					            		dataType: 'json',
																					            		data: {
																					            			action: response.data.next_action,
																					            			product_id: product_id,
																					            			product_ids: product_ids,
																					            			post: post,
																					            			variations: variations,
																					            			updated: updated,
																					            			added: added
																					            		},
																					            		success: function( response ) {
																					            			try {
																						            			if ( response.error == 'true' ) {
								            																		jQuery('.progress-label').text('Failed!');
								            																		jQuery('span.close').show();
								            																		jQuery('#progressbar .status').text('Error: '+response.data.msg);
																						            			} else {
																						            				if ( response.data.next_action != undefined && response.data.next_action == 'completed' ) {
																						            					progress++;
																						            					increment_progress( progress, final_progress_value );
																						            				}
																						            			}
																					            			} catch( error ) {
																												jQuery('.progress-label').text('Failed!');
																												jQuery('span.close').show();
																												jQuery('#progressbar .status').text('Error: '+error.toString());
																												return false;
																											}
																					            		}
																					            	});
																									progress++;
																									increment_progress( progress, final_progress_value );
																		            			}
																	            			} catch( error ) {
																								jQuery('.progress-label').text('Failed!');
																								jQuery('span.close').show();
																								jQuery('#progressbar .status').text('Error: '+error.toString());
																								return false;
																							}
																	            		}
																	            	});
																					progress++;
																					increment_progress( progress, final_progress_value );
														            			}
													            			} catch( error ) {
																				jQuery('.progress-label').text('Failed!');
																				jQuery('span.close').show();
																				jQuery('#progressbar .status').text('Error: '+error.toString());
																				return false;
																			}
													            		}
													            	});
																	progress++;
																	increment_progress( progress, final_progress_value );
										            			}
									            			} catch( error ) {
																jQuery('.progress-label').text('Failed!');
																jQuery('span.close').show();
																jQuery('#progressbar .status').text('Error: '+error.toString());
																return false;
															}
									            		}
									            	});
													progress++;
													increment_progress( progress, final_progress_value );
					            				}
					            			}
				            			} catch( error ) {
											jQuery('.progress-label').text('Failed!');
											jQuery('span.close').show();
											jQuery('#progressbar .status').text('Error: '+error.toString());
											return false;
										}
				            		}
				            	});
							}, 10 );
						} catch( error ) {
							jQuery('.progress-label').text('Failed!');
							jQuery('span.close').show();
							jQuery('#progressbar .status').text('Error: '+error.toString());
							return false;
						}
		            });
				});
			</script>
			<h3><?php _e( 'Step 1: Select Base Products', 'sa_bulk_variation' ); ?></h3>
			<div id="form_options">
				<p>
					<input type="radio" id="product_names" name="selected_option" value="product_names" /> <label for="product_names"><?php _e( 'Create new base product/s', 'sa_bulk_variation' ); ?></label>
				</p>
				<div id="product_names">
					<table id="product_names_table">
						<tbody>
							<tr>
								<td><strong><?php _e( 'Product\'s name', 'sa_bulk_variation' ); ?></strong></td>
								<th><strong><?php echo __( 'Base Price', 'sa_bulk_variation' ) . ' (' . get_woocommerce_currency_symbol() . ')'; ?></strong></th>
								<td></td>
							</tr>
							<tr>
								<td><input type="text" name="product_names[]" size="50" value="" placeholder="<?php _e( 'Enter a product\'s name', 'sa_bulk_variation' ); ?>..." /></td>
								<td><input type="number" step="any" name="base_price[]" min="0" value="" placeholder="0.00" /></td>
								<td><input type="button" class="add_row" value="+" /></td>
							</tr>
						</tbody>
					</table>
				</div>
				<p>
					<input type="radio" id="categories" name="selected_option" value="categories" /> <label for="categories"><?php _e( 'Use all products from selected categories as base products', 'sa_bulk_variation' ); ?></label>
				</p>
				<div id="categories" class="categorydiv">
					<?php
                        $category_count = wp_count_terms( 'product_cat' );
                        if ( $category_count > 0 ) {
                    ?>
                    <ul id="product_catchecklist" data-wp-lists="list:product_cat" class="categorychecklist form-no-clear">
					<?php
						wp_terms_checklist( 0, array( 'taxonomy' => 'product_cat' ) );
					?>
					</ul>
                    <?php } else { ?>
                        <ul id="product_catchecklist"><li><?php echo '<strong>'.__( 'No categories found. Please select other option', 'sa_bulk_variation' ).'</strong>'; ?></li></ul>
                    <?php } ?>
				</div>
				<p>
					<input type="radio" id="search" name="selected_option" value="search" /> <label for="search"><?php _e( 'Let me choose base products', 'sa_bulk_variation' ); ?></label>
				</p>
				<div id="search">
					<div class="woocommerce_options_panel">
						<div class="options_group">
							<p class="form-field">
								<label for="product_ids"><?php _e( 'Products', 'sa_bulk_variation' ) ?></label>
								<?php if ( $this->is_wc_gte_23() ) { ?>
									<input type="hidden" class="wc-product-with-status-search" data-multiple="true" style="width: 75%;" name="product_ids" data-placeholder="<?php _e( 'Search for a product&hellip;', 'wc_smart_coupons' ); ?>" data-action="json_search_products_with_status" data-selected="" value="" />
								<?php } else { ?>
									<select id="product_ids" name="product_ids[]" class="ajax_chosen_select_products_with_status" multiple="multiple" data-placeholder="<?php _e( 'Search for a product&hellip;', 'sa_bulk_variation' ); ?>"></select> 
								<?php } ?>
								<img class="help_tip" data-tip='<?php _e( 'Base products for which new variations will be added or existing will be updated', 'sa_bulk_variation' ) ?>' src="<?php echo $this->global_wc()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
							</p>
						</div>
					</div>
				</div>
			</div>
			<div id="search_result"></div>
			<br />
			<div id="price"><label for="price"><?php echo __( 'Base Price', 'sa_bulk_variation' ) . ' (' . get_woocommerce_currency_symbol() . ')'; ?></label> <input type="number" step="any" min="0" id="price" name="price" placeholder="<?php _e( '0.00', 'sa_bulk_variation' ); ?>" value="" /></div>
			<div id="modal"></div>
			<div id="progressbar">
				<div class="progress-label"><?php _e( 'Starting', 'sa_bulk_variation' ); ?>...</div>
				<div class="status"></div>
				<span class="close"></span>
			</div>
			<h3><?php _e( 'Step 2: Setup Variations & Prices', 'sa_bulk_variation' ); ?></h3>
			<p class="description"><?php _e( 'Select attributes for variations & optionally enter differential price. Differential prices will be added to base price and the final price will be set as price of variation.', 'sa_bulk_variation' ); ?></p>
			<br>
            <?php if ( is_array( $attributes_to_terms ) && count( $attributes_to_terms ) > 0 ) { ?>
				<div id="attribute_header">
					<label><strong><?php _e( 'Attributes', 'sa_bulk_variation' ); ?></strong></label>
					<label class="right"><strong><?php echo __( 'Differential price', 'sa_bulk_variation' ) . ' (' . get_woocommerce_currency_symbol() . ')'; ?></strong></label>
				</div>
				<div id="attributes_hierarchy" class="categorydiv">
					<ul class="attribute_list categorychecklist">
						<?php foreach ( $attributes_to_terms as $attribute_slug => $attribute_terms ) { ?>
								<li>
									<input type="checkbox" id="attributes_<?php echo $attribute_slug; ?>"  class="attribute" name="attributes[]" value="<?php echo $attribute_slug; ?>" />
									<label for="attributes_<?php echo $attribute_slug; ?>"><?php echo ( isset( $attributes[$attribute_slug] ) && !empty( $attributes[$attribute_slug] ) ) ? $attributes[$attribute_slug] : substr( $attribute_slug, 3 ); ?></label>
								<?php if ( is_array( $attribute_terms ) && count( $attribute_terms ) > 0 ) { ?>
									<ul class="terms_list children">
									<?php foreach ( $attribute_terms as $term_taxonomy_id => $terms ) { ?>
										<li><table class="attributes_to_price"><tr>
											<td><label for="terms_<?php echo $term_taxonomy_id; ?>">
                                                <input type="checkbox" id="terms_<?php echo $term_taxonomy_id; ?>"  class="term" name="<?php echo $attribute_slug . '[' . $term_taxonomy_id . ']'; ?>" value="<?php echo $terms['term_slug']; ?>" />
                                                <span><?php echo $terms['term_name']; ?></span>
                                            </label></td>
											<td><input type="number" step="any" id="price_<?php echo $term_taxonomy_id; ?>" class="price" name="<?php echo $attribute_slug . '-price[' . $term_taxonomy_id . ']'; ?>" placeholder="<?php _e( '0.00', 'sa_bulk_variation' ); ?>" value="" readonly="readonly" /></td></tr></table>
										</li>
									<?php } ?>
									</ul>
								<?php } ?>
								</li>
							<?php } ?>
					</ul>
				</div>
            <?php } else { ?>
                <div id="notice" class="error">
                    <p><?php echo '<strong>'.__( 'Important', 'sa_bulk_variation' ).':</strong> '.__( 'Please add some attributes before creating product variations', 'sa_bulk_variation' ) . ' <a href="'.admin_url( 'edit.php?post_type=product&page=' . ( ( $this->is_wc_gte_21() ) ? 'product_attributes' : 'woocommerce_attributes' ) ).'" target="_blank">'.__( 'Add Attributes', 'sa_bulk_variation' ).'</a>'; ?></p>
                </div>
            <?php } ?>
			<input id="create_variations" name="create_variations" type="button" class="button-primary" value="<?php _e( 'Create / Update Variations', 'sa_bulk_variation' ); ?>" />
			</form>
			</div>
			<?php
		}

		function bulk_add_update_attributes() {
			global $wpdb;

			if ( !isset( $_POST['post'] ) ) {
				die( json_encode( array( 'error' => 'true', 'data' => array( 'msg' => __( 'Form data not found' ) ) ) ) );
			}

			parse_str($_POST['post'], $post);

			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                $bvm_debug_data = get_option( 'bvm_debug_data' );
                if ( $bvm_debug_data === false || !is_array( $bvm_debug_data ) ) {
                    $bvm_debug_data = array();
                }
                if ( count( $bvm_debug_data ) >= 3 ) {
                    array_shift( $bvm_debug_data );
                }
                $bvm_debug_data[] = $post;
                update_option( 'bvm_debug_data', $bvm_debug_data );
            }
            
			$return = false;
			$reason = array();
			$product_attributes = array();
			$position = 0;

			foreach ( $post as $attribute_key => $attribute_value ) {
				if ( substr( $attribute_key, 0, 3 ) !== 'pa_' || strpos( $attribute_key, '-price' ) !== false ) continue;
				$product_attributes[$attribute_key] = array();
				$product_attributes[$attribute_key]['name'] = $attribute_key;
				$product_attributes[$attribute_key]['value'] = '';
				$product_attributes[$attribute_key]['position'] = "$position";
				$product_attributes[$attribute_key]['is_visible'] = 0;
				$product_attributes[$attribute_key]['is_variation'] = 1;
				$product_attributes[$attribute_key]['is_taxonomy'] = 1;
				$position++;
			}

			if ( count( $product_attributes ) <= 0 ) {
				$return = true;
				$reason[] = __( 'Please select some attributes', 'sa_bulk_variation' );
			}
            
			switch( $post['selected_option'] ) {
				case 'product_names':
					if ( isset($post['product_names'] ) && empty( $post['product_names'] ) ) {
						$return = true;
						$reason[] = __( 'Please add some product names', 'sa_bulk_variation' );
					} else {
						$product_names = $post['product_names'];
					}
					break;

				case 'categories':
					if ( isset( $post['tax_input']['product_cat'] ) && count( $post['tax_input']['product_cat'] ) > 0 ) {
						$product_ids = $wpdb->get_col( "SELECT object_id FROM {$wpdb->prefix}term_relationships AS tr LEFT JOIN {$wpdb->prefix}term_taxonomy AS tt ON ( tt.term_taxonomy_id = tr.term_taxonomy_id ) WHERE term_id IN ( " . implode( ',', $post['tax_input']['product_cat'] ) . " )" );
					}
					if ( count( $product_ids ) <= 0 ) {
						$return = true;
						$reason[] = __( 'No product found in the category', 'sa_bulk_variation' );
						break;
					}
					$this->update_product_attributes( $product_ids, $product_attributes );
					break;

				case 'search':
					$product_ids = ( $this->is_wc_gte_23() ) ? explode( ',', $post['product_ids'] ) : $post['product_ids'];
					if ( count( $product_ids ) <= 0 ) {
						$return = true;
						$reason[] = __( 'No product selected', 'sa_bulk_variation' );
						break;
					}
					$this->update_product_attributes( $product_ids, $product_attributes );
					break;
			}

			if ( $return ) {
				$return_data = array( 
									'error' => 'true',
									'data' => array( 'msg' => $reason )
								);
			} elseif ( $post['selected_option'] == 'product_names' ) {
				$return_data = array( 
									'error' => 'false',
									'data' => array( 
													'next_action' => 'bulk_add_new_products_and_attributes_old',
													'post' => $post, 
													'product_names' => $product_names, 
													'product_attributes' => $product_attributes
												) 
								);
			} else {
				$return_data = array( 
									'error' => 'false', 
									'data' => array( 
													'next_action' => 'add_update_product_attributes_old', 
													'post' => $post, 
													'product_ids' => $product_ids, 
													'product_attributes' => $product_attributes 
												) 
								);
			}
			echo json_encode( $return_data );
			die();
		}

		function get_product_ids_from_categories() {
			global $wpdb;
			
			parse_str($_POST['post'], $post);
			
			if ( isset( $post['tax_input']['product_cat'] ) && count( $post['tax_input']['product_cat'] ) > 0 ) {
				$product_ids = $wpdb->get_col( "SELECT object_id FROM {$wpdb->prefix}term_relationships AS tr LEFT JOIN {$wpdb->prefix}term_taxonomy AS tt ON ( tt.term_taxonomy_id = tr.term_taxonomy_id ) WHERE term_id IN ( " . implode( ',', $post['tax_input']['product_cat'] ) . " )" );
			}

			echo json_encode( count( $product_ids ) );
			die();
		}

		function bulk_add_new_products_and_attributes() {
			
			$post = $_POST['post'];
			$product_names = $_POST['product_names'];
			$product_attributes = $_POST['product_attributes'];
			$product_ids = array();

			foreach ( $product_names as $index => $product_name ) {
				$product_post_data = array(
					'post_title' => trim( $product_name ),
					'post_content' => '',
					'post_status' => 'publish',
					'post_author' => get_current_user_id(),
					'post_parent' => 0,
					'post_type' => 'product'
				);
				$product_id = wp_insert_post( $product_post_data );
				$price = ( isset( $post['base_price'][$index] ) && $post['base_price'][$index] !== '' ) ? $post['base_price'][$index] : '';
                update_post_meta( $product_id, '_product_attributes', $product_attributes );
				update_post_meta( $product_id, '_visibility', 'visible' );
				if ( $price > 0 ) {
					update_post_meta( $product_id, '_regular_price', $price );
					update_post_meta( $product_id, '_price', $price );
				}
				$product_ids[] = $product_id;
			}

			$return_data = array( 
								'error' => 'false', 
								'data' => array( 
												'next_action' => 'add_update_product_attributes_old', 
												'post' => $post, 
												'product_ids' => $product_ids, 
												'product_attributes' => $product_attributes 
											) 
							);
			echo json_encode( $return_data );
			die();
		}

		function add_update_product_attributes() {
			$post = $_POST['post'];
			$product_id = $_POST['product_id'];

			wp_set_object_terms( $product_id, 'variable', 'product_type' );

			$_product = $this->get_product( $product_id, array( 'product_type' => 'variable' ) );

			$variations = array();
            $update_attributes = false;
			
			foreach ( $_product->get_attributes() as $attribute ) {
				$attribute_field_name = 'attribute_' . sanitize_title( $attribute['name'] );
				$post_terms = wp_get_post_terms( $product_id, $attribute['name'] );
				$options = array();
				if ( count( $post_terms ) > 0 ) {
					foreach ( $post_terms as $term ) {
						$options[] = $term->slug;
					}
				}
                $new_options = array();
				if ( isset( $post[$attribute['name']] ) && is_array( $post[$attribute['name']] ) && count( $post[$attribute['name']] ) > 0 ) {
                    $new_options = array_diff( array_values( $post[$attribute['name']] ), $options );
				}
                if ( empty( $new_options ) ) {
                    $variations[ $attribute_field_name ] = ( isset( $post[$attribute['name']] ) ) ? array_values( $post[$attribute['name']] ) : array();
                } else {
                    $variations[ $attribute_field_name ] = array_values( $new_options );
                    $update_attributes = true;
                }
			}
			// Quit out if none were found
			if ( sizeof( $variations ) == 0 ) continue;

			if ( $update_attributes ) {
                foreach ( $variations as $attribute_name => $terms ) {
					$taxonomy = substr( $attribute_name, 10 );
                    $existing_attribute_terms = wp_get_object_terms( $product_id, $taxonomy );
                    if ( is_wp_error( $existing_attribute_terms ) ) continue;
                    if ( !is_array( $terms ) && !empty( $terms ) ) {
                        $terms = array( $terms );
                    }
                    $existing_terms = array();
                    if ( !empty( $existing_attribute_terms ) ) {
                        foreach ( $existing_attribute_terms as $existing_attribute_term ) {
                            if ( isset( $existing_attribute_term->slug ) && !empty( $existing_attribute_term->slug ) ) {
                                $existing_terms[] = $existing_attribute_term->slug;
                            }
                        }
                    }
                    $new_attribute_terms = array_unique( array_merge( $existing_terms, array_values( $terms ) ) );
					wp_set_object_terms( $product_id, $new_attribute_terms, $taxonomy );
				}
            }

			// Get existing variations so we don't create duplicates
		    $available_variations = array();
		    $id_to_variations = array();
		    foreach( $_product->get_children() as $child_id ) {
		    	$child = $_product->get_child( $child_id );
		        if ( ! empty( $child->variation_id ) ) {
		            $available_variations[] = $child->get_variation_attributes();
		            $id_to_variations[$child_id] = $child->get_variation_attributes();
		        }
		    }
		    
			// Created posts will all have the following data
			$variation_post_data = array(
				'post_title' => 'Product #' . $product_id . ' Variation',
				'post_content' => '',
				'post_status' => 'publish',
				'post_author' => get_current_user_id(),
				'post_parent' => $product_id,
				'post_type' => 'product_variation'
			);
            
            if ( isset( $post['selected_option'] ) && $post['selected_option'] == 'product_names' ) {
                $parent_product_price = $_product->get_price();
            } else {
                $parent_product_price = ( isset( $post['price'] ) && $post['price'] !== '' ) ? $post['price'] : '';
            }
            $return_data = array( 
            					'error' => 'false', 
            					'data' => array( 
            									'next_action' => 'get_possible_variations_old', 
            									'post' => $post, 
            									'product_id' => $product_id, 
            									'_product' => $_product, 
            									'variations' => $variations, 
            									'available_variations' => $available_variations, 
            									'id_to_variations' => $id_to_variations, 
            									'variation_post_data' => $variation_post_data, 
            									'parent_product_price' => $parent_product_price
            								) 
            				);
            echo json_encode( $return_data );
			die();
		}

		function get_possible_variations() {
			$variations = $_POST['variations'];
			$possible_variations = SA_Bulk_Variations::array_cartesian( $variations );
			$return_data = array( 
            					'error' => 'false', 
            					'data' => array( 
            									'next_action' => 'create_update_variation_old', 
            									'possible_variations' => $possible_variations
            								) 
            				);
            echo json_encode( $return_data );
			die();
		}

		function create_update_variation() {
			$post = $_POST['post'];
			$updated = $_POST['updated'];
			$added = $_POST['added'];
			$variation = $_POST['variation'];
			$available_variations = ( isset( $_POST['available_variations'] ) ) ? $_POST['available_variations'] : array();
			$id_to_variations = ( isset( $_POST['id_to_variations'] ) ) ? $_POST['id_to_variations'] : array();
			$parent_product_price = $_POST['parent_product_price'];
			$variation_post_data = $_POST['variation_post_data'];
			$variation_ids = ( isset( $_POST['variation_ids'] ) ) ? $_POST['variation_ids'] : array();
			$continue = false;
			if ( in_array( $variation, $available_variations, true ) ) {
				$is_update_price = false;
				$variation_id = array_search( $variation, $id_to_variations, true );

				$old_regular_price = get_post_meta( $variation_id, '_regular_price', true );
				$old_sale_price = get_post_meta( $variation_id, '_sale_price', true );
				$old_price = get_post_meta( $variation_id, '_price', true );
				$sale_price_dates_from = get_post_meta( $variation_id, '_sale_price_dates_from', true );
				$sale_price_dates_to = get_post_meta( $variation_id, '_sale_price_dates_to', true );

				$child_product_price = 0;
				foreach ( $variation as $attribute_name => $term ) {
					$taxonomy = substr( $attribute_name, 10 );
					if ( !isset( $post[$taxonomy] ) ) continue;
					$term_id = array_search( $term, $post[$taxonomy], true );
					if ( !empty( $term_id ) && isset( $post[$taxonomy . '-price'][$term_id] ) ) {
						$child_product_price += (float)$post[$taxonomy . '-price'][$term_id];
					}
				}
				if ( $old_price == $old_sale_price ) {
					$update_field = '_sale_price';
                    if ( $parent_product_price === '' ) {
                        $sale_price = $child_product_price + $old_sale_price;
                    } else {
                        $sale_price = $child_product_price + $parent_product_price;
                    }
                    $new_variation_price = $sale_price;
					$old_variation_price = $old_sale_price;
					$regular_price = $old_regular_price;
				} else {
					$update_field = '_regular_price';
                    if ( $parent_product_price === '' ) {
                        $regular_price = $child_product_price + $old_regular_price;
                    } else {
                        $regular_price = $child_product_price + $parent_product_price;
                    }
                    $new_variation_price = $regular_price;
					$old_variation_price = $old_regular_price;
					$sale_price = $old_sale_price;
				}
				$price = SA_Bulk_Variations::get_price( $regular_price, $sale_price, $sale_price_dates_from, $sale_price_dates_to );
				if ( $new_variation_price > 0 && $old_variation_price != $new_variation_price ) {
                    update_post_meta( $variation_id, $update_field, $new_variation_price );
					$is_update_price = true;
				}
				if ( $price > 0 && $old_price != $price ) {
                    update_post_meta( $variation_id, '_price', $price );
					$is_update_price = true;
				}
				if ( $is_update_price ) {
					$updated++;
				}
				$continue = true;
			}

			if ( !$continue ) {

				$variation_id = wp_insert_post( $variation_post_data );

				$variation_ids[] = $variation_id;

				$child_product_price = 0;
				foreach ( $variation as $key => $value ) {
					$taxonomy = substr( $key, 10 );
					$term_id = array_search( $value, $post[$taxonomy], true );
					if ( isset( $post[$taxonomy . '-price'][$term_id] ) ) {
						$child_product_price += (float)$post[$taxonomy . '-price'][$term_id];
					}
					update_post_meta( $variation_id, $key, $value );
				}
                
				$final_price = $child_product_price + $parent_product_price;
				if ( $final_price > 0 ) {
					update_post_meta( $variation_id, '_regular_price', $final_price );
					update_post_meta( $variation_id, '_price', $final_price );
				}

				$added++;

			}
			$return_data = array( 
            					'error' => 'false', 
            					'data' => array( 
            									'next_action' => 'create_update_variation_old', 
            									'post' => $post, 
            									'added' => $added, 
            									'updated' => $updated, 
            									'variation_ids' => $variation_ids
            								) 
            				);
            echo json_encode( $return_data );
			die();
		}

		function sync_created_updated_variable_product() {
			$product_id = $_POST['product_id'];
			$_product = $this->get_product( $product_id, array( 'product_type' => 'variable' ) );
			$_product->variable_product_sync();
			$return_data = array( 
            					'error' => 'false', 
            					'data' => array( 
            									'next_action' => 'finalize_bulk_create_update_variations_old'
            								) 
            				);
            echo json_encode( $return_data );
			die();
		}

		function finalize_bulk_create_update_variations() {
			$post = $_POST['post'];
			$product_ids = $_POST['product_ids'];
			$variations = $_POST['variations'];
			$updated = $_POST['updated'];
			$added = $_POST['added'];
			if ( !function_exists( '_woocommerce_term_recount' ) ) {
				require_once ( WP_PLUGIN_DIR . '/woocommerce/woocommerce-core-functions.php' );
			}
			foreach ( $variations as $attribute_name => $attribute_values ) {
				$taxonomy_name = substr( $attribute_name, 10 );
				$taxonomy = get_taxonomy( $taxonomy_name );
				if ( !isset( $post[$taxonomy_name] ) ) continue;
				$terms = array_flip( $post[$taxonomy_name] );
				_woocommerce_term_recount( $terms, $taxonomy, true, true );
			}
			$final_message = array();
			$final_message[] = __( 'Updated Successfully! ', 'sa_bulk_variation' );
			if ( isset( $post['selected_option'] ) && ( $post['selected_option'] == 'product_names' ) ) {
				$final_message[] = __( sprintf( '%s new %s added. ', count( $product_ids ), _n( 'product', 'products', count( $product_ids ) ) ), 'sa_bulk_variation' );
			}
			if ( $updated > 0 ) {
				$final_message[] = __( sprintf( '%s %s updated. ', $updated, _n( 'variation', 'variations', $updated ) ), 'sa_bulk_variation' );
			}
			if ( $added > 0 ) {
				$final_message[] = __( sprintf( '%s new %s added. ', $added, _n( 'variation', 'variations', $added ) ), 'sa_bulk_variation' );
			}
			$return_data = array( 
            					'error' => 'false', 
            					'data' => array( 
            									'next_action' => 'completed', 
            									'post' => $post, 
            									'final_message' => $final_message
            								) 
            				);
            echo json_encode( $return_data );
			die();

		}

		function update_product_attributes( $product_ids, $product_attributes ) {
            if ( is_array( $product_ids ) && count( $product_ids ) > 0 ) {
				foreach ( $product_ids as $product_id ) {
					$old_product_attributes = get_post_meta( $product_id, '_product_attributes', true );
					$position = count( $old_product_attributes );
					foreach ( $product_attributes as $attribute_key => $product_attribute ) {
						if ( isset( $old_product_attributes[$attribute_key] ) ) continue;
						$product_attribute['position'] = "$position";
						$old_product_attributes[$attribute_key] = $product_attribute;
						$position++;
					}
					update_post_meta( $product_id, '_product_attributes', $old_product_attributes );
				}
			}
		}

	}

}

global $bvm_operation_old;

$bvm_operation_old = new BVM_Operation_Old();