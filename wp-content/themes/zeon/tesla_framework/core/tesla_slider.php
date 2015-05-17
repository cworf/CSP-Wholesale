<?php

class Tesla_slider{

	private static $slider_config;
	private static $load;

	private function __construct(){}

	public static function init(){

		$slider_config_file = locate_template('theme_config/slider-options.php');

		if(!isset(self::$slider_config)&&$slider_config_file!==''){

			self::$slider_config = include $slider_config_file;

			self::$load = new TT_Load;

			self::slider_autoload();

		}

	}

	private static function slider_autoload(){

		add_action( 'init', array('Tesla_slider','generate_custom_fields') );

		add_action( 'add_meta_boxes', array('Tesla_slider','generate_meta_boxes') );

		add_action('save_post', array('Tesla_slider','metabox_save') );

		self::generate_shortcodes();

		add_action('wp_enqueue_scripts', array('Tesla_slider','slider_enqueue') );

		add_action('admin_enqueue_scripts', array('Tesla_slider','slider_admin_enqueue') );

		self::register_ajax();

	}

	public static function slider_enqueue(){

		wp_enqueue_script('tesla-image-holder', tesla_locate_uri('tesla_framework/static/js/holder.js'),array(),null);

	}

	public static function slider_admin_enqueue($hook){

		global $post;

		$slider_options_keys = array_keys(self::$slider_config);

		if('post-new.php'===$hook||'post.php'===$hook)
			if(in_array($post->post_type, $slider_options_keys)){

		    	wp_enqueue_style('tesla-slider-admin', tesla_locate_uri('tesla_framework/static/css/tesla_slider.css'),false,null);
		    	wp_enqueue_style('tesla-slider-admin-ui', tesla_locate_uri('tesla_framework/static/ui/smoothness/jquery-ui-1.10.3.custom.min.css'),false,null);
		    	wp_enqueue_style('wp-color-picker');

				wp_enqueue_media();
		    	wp_enqueue_script('wp-color-picker');
				wp_enqueue_script('jquery-ui-core');
				wp_enqueue_script('jquery-ui-sortable');
				wp_enqueue_script('jquery-ui-datepicker');
				wp_enqueue_script('tesla-image-holder', tesla_locate_uri('tesla_framework/static/js/holder.js'),array(),null);
				wp_enqueue_script('tesla-slider-admin', tesla_locate_uri('tesla_framework/static/js/tesla_slider.js'),array(),null);

			}

	}

	private static function generate_shortcodes(){

		$slider_options = self::$slider_config;

		foreach($slider_options as $slider_id => $slider){

			foreach($slider['output'] as $output_id => $output){

				if(isset($output['shortcode']))
					add_shortcode( $output['shortcode'], array('Tesla_slider','shortcode_view') );

			}

		}

	}

	public static function register_ajax(){

		add_action('wp_enqueue_scripts', array('Tesla_slider','ajax_scripts_enqueue'), 10000 );

		add_action( "wp_ajax_tesla_slider", array('Tesla_slider','ajax_content') );
		add_action( "wp_ajax_nopriv_tesla_slider", array('Tesla_slider','ajax_content') );

	}

	public static function ajax_scripts_enqueue(){

		$slider_options = self::$slider_config;

		$ajax_string = 'tesla_ajax.actions = {';

		foreach($slider_options as $slider_id => $slider){

			$ajax_string .= $slider_id.':{';

			foreach($slider['output'] as $output_id => $output){

				if(isset($output['ajax_javascript']))
					$ajax_string .= $output['ajax_javascript'].':
						function(offset,nr,callback,category){
							jQuery.post(tesla_ajax.url, {action:"tesla_slider",id:"'.$slider_id.'",view:"'.$output_id.'","offset":offset,"nr":nr,"category":category===undefined?"":category,nonce:tesla_ajax.nonce}, callback);
						},'."\n";

			}

			$ajax_string .= '},'."\n";

		}

		$ajax_string .= '}';

		wp_enqueue_script('jquery');
		wp_localize_script('jquery','tesla_ajax',array('url'=>admin_url( 'admin-ajax.php' ),'nonce'=>wp_create_nonce(),'l10n_print_after'=> $ajax_string));

	}

	public static function ajax_content(){

		if ( wp_verify_nonce( $_POST['nonce'])) {
      	
			$slider_options = self::$slider_config;

			$slider_id = $_POST['id'];
			$output_id = $_POST['view'];
			$category = $_POST['category'];
			$offset = $_POST['offset'];
			$nr = $_POST['nr'];

			if(array_key_exists($slider_id, $slider_options)){
				$data = array();
				$posts_array = get_posts(array(
					'post_type' => $slider_id,
					$slider_id.'_tax' => $category,
					'offset' => $offset,
					'numberposts' => $nr,
					'order' => isset($slider_options[$slider_id]['order'])?$slider_options[$slider_id]['order']:'DESC',
					'suppress_filters' => false
				));
				$values_array = array();
				foreach($posts_array as $post){
					$meta_array = apply_filters('tesla_slide_options', get_post_meta($post->ID, 'slide_options', true), $post->ID, 'ajax_content');
					foreach($meta_array as $meta_id => $meta_value){
						if(empty($meta_value)&&isset($slider_options[$slider_id]['options'][$meta_id]['default']))
							$meta_array[$meta_id] = $slider_options[$slider_id]['options'][$meta_id]['default'];
					}
					$post_cats = get_the_terms($post->ID, $slider_id.'_tax');
					$post_cats_slugs = array();
					if(is_array($post_cats))
						foreach($post_cats as $cat)
							$post_cats_slugs[] = $cat->slug;
					$values_array[] = array(
						'post' => $post,
						'options' => $meta_array,
						'categories' => $post_cats_slugs //array of slugs
					);
				}
				$data['slides'] = $values_array;
				$data['all_categories'] = Tesla_slider::get_categories($slider_id, true);
				if($output_id===null){
					if(isset($slider_options[$slider_id]['output_default'])&&isset($slider_options[$slider_id]['output'][$slider_options[$slider_id]['output_default']])){
						$view = $slider_options[$slider_id]['output'][$slider_options[$slider_id]['output_default']]['view'];
					}else{
						$view_array = reset($slider_options[$slider_id]['output']);
						$view = $view_array['view'];
					}
				}else{
					$view = $slider_options[$slider_id]['output'][$output_id]['view'];
				}
				echo self::$load->view($view,$data,true,true);
			}

	   }  

		die();

	}

	public static function shortcode_view($atts, $content, $tag){

		$atts = (array)$atts;

		$atts_default = array(
            'category' => ''
        );

        $atts = array_merge($atts_default,$atts);

        $atts_default = shortcode_atts($atts_default,$atts);

		extract($atts_default);

        $slider_options = self::$slider_config;

        foreach($slider_options as $slider_id => $slider){

        	foreach($slider['output'] as $output_id => $output){

	        	if(isset($output['shortcode'])&&$output['shortcode']===$tag)
	        		return self::get_slider_html($slider_id,$category,$output_id,null,$atts);

        	}

        }

		return '';
		
	}

	public static function generate_custom_fields(){

		$slider_options = self::$slider_config;
		
		foreach($slider_options as $slider_id => $slider){

			$slider_term = isset($slider['term'])?$slider['term']:'slide';
			$slider_term_plural = isset($slider['term_plural'])?$slider['term_plural']:$slider_term.'s';
			$slider_queryable = isset($slider['has_single'])?$slider['has_single']:false;
			$slider_url_rewrite = isset($slider['url'])?$slider['url']:false;

			$post_options = array(
				'label' => $slider_id,
				'labels' => array(
					'name' => ucwords($slider_term_plural),
					'singular_name' => ucwords($slider_term),
					'menu_name' => $slider['name'],
					'all_items' => 'All '.ucwords($slider_term_plural),
					'add_new' => 'Add New',
					'add_new_item' => 'Add New '.ucwords($slider_term),
					'edit_item' => 'Edit '.ucwords($slider_term),
					'new_item' => 'New '.ucwords($slider_term),
					'view_item' => 'View '.ucwords($slider_term),
					'search_items' => 'Search '.ucwords($slider_term_plural),
					'not_found' => 'No '.$slider_term_plural.' found.',
					'not_found_in_trash' => 'No '.$slider_term_plural.' found.',
					'parent_item_colon' => 'Parent '.ucwords($slider_term)
				),
				'description' => 'Manage '.ucwords($slider_term_plural),
				'public' => true,
				'exclude_from_search' => true,
				'publicly_queryable' => $slider_queryable,
				'show_ui' => true,
				'show_in_nav_menus' => $slider_queryable,
				'show_in_menu' => true,
				'show_in_admin_bar' => true,
				'menu_position' => 100,
				'menu_icon' => isset($slider['icon'])?TT_THEME_URI.'/theme_config/'.$slider['icon']:null,
				'capability_type' => 'post',
				'hierarchical' => false,
				'supports' => $slider_queryable?array(
					'title','comments'
				):array(
					'title'
				),
				'rewrite' => $slider_url_rewrite?array('slug'=>$slider_url_rewrite):true
			);

			if(isset($slider['post_options']))
				$post_options = array_merge($post_options,$slider['post_options']);

			register_post_type($slider_id,$post_options);

			$taxonomy_options = array(
				'label' => 'Categories',
				'labels' => array(
					'name' => 'Categories',
					'singular_name' => 'Category',
					'menu_name' => 'Categories',
					'all_items' => 'All Categories',
					'edit_item' => 'Edt Categories',
					'view_item' => 'View Category',
					'update_item' => 'Update Category',
					'add_new_item' => 'Add New Category',
					'new_item_name' => 'New Category',
					'parent_item' => 'Parent Category',
					'parent_item_colon' => 'Parent Category:',
					'search_items' => 'Search Categories',
					'popular_items' => 'Popular Categories',
					'separate_items_with_commas' => 'Separate categories with commas',
					'add_or_remove_items' => 'Add or remove categories',
					'choose_from_most_used' => 'Choose from the most used categories',
					'not_found' => 'No categories found'
				),
				'public' => true,
				'show_ui' => true,
				'show_in_nav_menus' => false,
				'show_tagcloud' => false,
				'show_admin_column' => true,
				'hierarchical' => true
			);

			if(isset($slider['taxonomy_options']))
				$taxonomy_options = array_merge($taxonomy_options,$slider['taxonomy_options']);

			register_taxonomy($slider_id.'_tax',$slider_id,$taxonomy_options);

		}
	}

	public static function generate_meta_boxes(){

		$slider_options = self::$slider_config;

		foreach($slider_options as $slider_id => $slider){

			if(count($slider['options'])){

				$slider_term = isset($slider['term'])?$slider['term']:'slide';
				$slider_term = ucwords($slider_term).' Options';

				add_meta_box('slide_options',$slider_term,array('Tesla_slider','metabox_view'),$slider_id,'normal','default',array(
					'options' => $slider['options']
				));

			}

		}
		
	}

	public static function metabox_view($post, $params){

		wp_nonce_field(-1, 'slide_options_nonce');

		$values_array = apply_filters('tesla_slide_options', get_post_meta($post->ID, 'slide_options', true), $post->ID, 'metabox_view');

		foreach($params['args']['options'] as $option_id => $option){

			$option_multiple = isset($option['multiple'])?$option['multiple']:false;
			$option_value = is_array($values_array)&&array_key_exists($option_id,$values_array)?$values_array[$option_id]:($option_multiple?array():'');
			
			$option_value_default = isset($option['default'])?$option['default']:'';
			
			echo self::generate_option($option_id, $option, $option_value);

		}

	}

	private static function generate_option($name, $args, $value, $name_original=null, $disabled=false){

		if(!isset($args['type']))
			return;

		if(isset($args['multiple'])){
			$multiple = $args['multiple'];
		}else{
			$multiple = false;
		}

		if(isset($args['levels']))
			$levels = $args['levels'];
		else
			$levels = false;

		$type = $args['type'];

		$title = isset($args['title'])?$args['title']:'';
		$description = isset($args['description'])?$args['description']:'';
		$placeholder = isset($args['placeholder'])?$args['placeholder']:'';
		$label = isset($args['label'])?$args['label']:'';
		$default = isset($args['default'])?$args['default']:'';

		$name_original = !is_null($name_original) ? 'data-option="'.$name_original.'"' : '';

		$output = '';

		$output .= '<fieldset class="tesla-option" '.$name_original.' '.($disabled?'style="display:none;"':'').' >';
		if(!empty($title))
			$output .= '<legend>'.$title.'</legend>';

		if(is_array($type)&&(!$multiple||!empty($value))){

			if(isset($args['group']))
				$group = $args['group'];
			else
				$group = true;

			if($multiple){
				if(is_array($value))
					foreach($value as $id => $item){
						$output .= '<div class="tesla-option-container">';
						if(!$group){
							$output .= '<select>';
							$type_keys = array_keys($type);
							if(is_array($item)){
								$group_keys = array_keys($item);
								if(count($group_keys))
									$group_id = $group_keys[0];
								else
									$group_id = $type_keys[0];
							}else{
								$group_id = $type_keys[0];
							}
							foreach($type as $type_id => $type_item){
								$type_name = isset($type_item['title']) ? $type_item['title'] : $type_id;
								$output .= '<option value="'.$type_id.'" '.selected($type_id,$group_id,false).'>'.$type_name.'</option>';
							}
							$output .= '</select>';
						}
						foreach($type as $array_key => $array_value){
							$type_disabled = !$group&&$group_id!==$array_key;
							$type_value = !$type_disabled&&is_array($item)&&isset($item[$array_key])?$item[$array_key]:'';
							$output .= self::generate_option($name.'['.$id.']['.$array_key.']',$array_value,$type_value,!$group?$array_key:null,$type_disabled);
						}
						$output .= '<br/>';
						$output .= '<button type="button">Remove</button>';
						$output .= '</div>';
					}
			}else{
				if(!$group){
					$output .= '<select>';
					$type_keys = array_keys($type);
					if(is_array($value)){
						$group_keys = array_keys($value);
						if(count($group_keys))
							$group_id = $group_keys[0];
						else
							$group_id = $type_keys[0];
					}else{
						$group_id = $type_keys[0];
					}
					foreach($type as $type_id => $type_item){
						$type_name = isset($type_item['title']) ? $type_item['title'] : $type_id;
						$output .= '<option value="'.$type_id.'" '.selected($type_id,$group_id,false).'>'.$type_name.'</option>';
					}
					$output .= '</select>';
				}
				foreach($type as $array_key => $array_value){
					$type_disabled = (!$group&&$group_id!==$array_key)||$disabled;
					$type_value = !$type_disabled&&is_array($value)&&isset($value[$array_key])?$value[$array_key]:'';
					$output .= self::generate_option($name.'['.$array_key.']',$array_value,$type_value,!$group?$array_key:null,$type_disabled);
				}
			}
		}

		if(is_string($type)){
			if($multiple){
				if(is_array($value)){
					$nr = count($value);
					for($i=0;$i<$nr;$i++){
						$output .= '<div class="tesla-option-container">';
						$output .= self::generate_input($type, $name.'['.$i.']', $value[$i], $placeholder, $label, $default, $disabled);
						$output .= '<br/>';
						$output .= '<button type="button">Remove</button>';
						$output .= '</div>';
					}
				}else{
					$nr = 0;
				}
			}else{
				if($levels){
					$output .= '<div class="tesla-option-container">';
					$output .= self::generate_input($type, $name.'[value]', $value, $placeholder, $label, $default, $disabled);
					$output .= '<br/><button type="button" data-levels="1">New</button>';
					$output .= '</div>';
				}
				else{
					$output .= '<div class="tesla-option-container">';
					$output .= self::generate_input($type, $name, $value, $placeholder, $label, $default, $disabled);
					$output .= '</div>';
				}
			}
		}

		if($multiple){
			$output .= '<input type="hidden" name="'.$name.'[_level]" '.($disabled?'disabled="disabled"':'').' />';
			$output .= self::generate_option_template($name,$args);
		}

		if($levels){
			$output .= '<div class="tesla-option-levels">';
			$output .= self::generate_option_level($name,$args);
			$output .= '</div>';
		}

		if(!empty($description))
			$output .= '<p>'.$description.'</p>';

		$output .= '</fieldset>';

		return $output;

	}

	private static function generate_option_level($name, $args){

		if(!isset($args['type']))
			return;

		if(isset($args['multiple']))
			$multiple = $args['multiple'];
		else
			$multiple = false;

		if(isset($args['levels']))
			$levels = $args['levels'];
		else
			$levels = false;

		$type = $args['type'];

		$placeholder = isset($args['placeholder'])?$args['placeholder']:'';
		$label = isset($args['label'])?$args['label']:'';
		$default = isset($args['default'])?$args['default']:'';

		$output = '';

		if($multiple){
			$output .= '<button type="button">Add</button>';
			$output .= '<div class="tesla-option-template">';
			$name .= '[]';
		}

		if(is_array($type)){

			if(isset($args['group']))
				$group = $args['group'];
			else
				$group = true;

			if(!$group){
				$output .= '<select>';
				$type_keys = array_keys($type);
				$group_id = $type_keys[0];
				foreach($type as $type_id => $type_item){
					$type_name = isset($type_item['title']) ? $type_item['title'] : $type_id;
					$output .= '<option value="'.$type_id.'" '.selected($type_id,$group_id,false).'>'.$type_name.'</option>';
				}
				$output .= '</select>';
			}

			foreach($type as $array_key => $array_value){

				$title = isset($array_value['title'])?$array_value['title']:'';
				$description = isset($array_value['description'])?$array_value['description']:'';

				$output .= '<fieldset class="tesla-option" data-option="'.$array_key.'" '.(!$group&&$array_key!==$group_id?'style="display:none;"':'').' >';
				if(!empty($title))
					$output .= '<legend>'.$title.'</legend>';

				if($multiple)
					$output .= '<input type="hidden" name="'.$name.'['.$array_key.'][_level]" disabled="disabled" />';

				$output .= self::generate_option_level($name.'['.$array_key.']',$array_value);

				if(!empty($description))
					$output .= '<p>'.$description.'</p>';
				$output .= '</fieldset>';

			}

		}

		if(is_string($type)){

			if(!$multiple)
				$output .= '<div class="tesla-option-container">';

			$output .= self::generate_input($type, $name, '', $placeholder, $label, $default, true);

			if($levels){
				$output .= '<br/><button type="button">Remove</button><br/><button type="button" data-levels="1">New</button>';
			}

			if(!$multiple)
				$output .= '</div>';

		}

		if($multiple){
			$output .= '<br/>';
			$output .= '<button type="button">Remove</button>';
			$output .= '</div>';
		}

		return $output;

	}

	private static function generate_option_template($name, $args){

		if(!isset($args['type']))
			return;

		if(isset($args['multiple']))
			$multiple = $args['multiple'];
		else
			$multiple = false;

		if(isset($args['levels']))
			$levels = $args['levels'];
		else
			$levels = false;

		$type = $args['type'];

		$placeholder = isset($args['placeholder'])?$args['placeholder']:'';
		$label = isset($args['label'])?$args['label']:'';
		$default = isset($args['default'])?$args['default']:'';

		$output = '';

		if($multiple){
			$output .= '<button type="button">Add</button>';
			$output .= '<div class="tesla-option-template">';
			$name .= '[]';
		}

		if(is_array($type)){

			if(isset($args['group']))
				$group = $args['group'];
			else
				$group = true;

			if(!$group){
				$output .= '<select>';
				$type_keys = array_keys($type);
				$group_id = $type_keys[0];
				foreach($type as $type_id => $type_item){
					$type_name = isset($type_item['title']) ? $type_item['title'] : $type_id;
					$output .= '<option value="'.$type_id.'" '.selected($type_id,$group_id,false).'>'.$type_name.'</option>';
				}
				$output .= '</select>';
			}

			foreach($type as $array_key => $array_value){

				$title = isset($array_value['title'])?$array_value['title']:'';
				$description = isset($array_value['description'])?$array_value['description']:'';

				$output .= '<fieldset class="tesla-option" data-option="'.$array_key.'" '.(!$group&&$array_key!==$group_id?'style="display:none;"':'').' >';
				if(!empty($title))
					$output .= '<legend>'.$title.'</legend>';

				if($multiple)
					$output .= '<input type="hidden" name="'.$name.'['.$array_key.'][_level]" disabled="disabled" />';

				$output .= self::generate_option_template($name.'['.$array_key.']',$array_value);

				if(!empty($description))
					$output .= '<p>'.$description.'</p>';
				$output .= '</fieldset>';

			}

		}

		if(is_string($type)){

			if(!$multiple)
				$output .= '<div class="tesla-option-container">';

			$output .= self::generate_input($type, $name, '', $placeholder, $label, $default, true);

			if(!$multiple)
				$output .= '</div>';

		}

		if($multiple){
			$output .= '<br/>';
			$output .= '<button type="button">Remove</button>';
			$output .= '</div>';
		}

		return $output;

	}

	private static function generate_input($type, $name, $value='', $placeholder='', $label='', $default='', $disabled=false){

		if($disabled)
			$disabled = 'disabled="disabled"';
		else
			$disabled = '';

		$output = '';

		switch($type){

			case 'line':
				$output .= '<input type="text" name="'.$name.'" value="'.esc_attr($value).'" placeholder="'.$placeholder.'" '.$disabled.' />';
				break;

			case 'text':
				$output .= '<textarea rows="1" cols="40" name="'.$name.'" placeholder="'.$placeholder.'" '.$disabled.'>'.esc_textarea($value).'</textarea>';
				break;

			case 'image':
				$output .= '<img src="'.(!empty($value)?(is_numeric($value)?wp_get_attachment_url($value):$value):$default).'" alt="'.$placeholder.'" />';
				$output .= '<input type="hidden" name="'.$name.'" value="'.$value.'" '.$disabled.' />';
				break;

			case 'checkbox':
				if(!empty($label)&&is_array($label)){
					$label_keys = array_keys($label);
					$i = count($label);
					$output .= '<input type="hidden" name="'.$name.'[_level]" '.$disabled.' />';
					foreach($label as $key => $item){
						$checked = $disabled ? (is_array($default)&&in_array($key, $default))||$default===$key : is_array($value)&&in_array($key, $value);
						$output .= '<label><input type="checkbox" name="'.$name.'[]" value="'.$key.'" '.checked($checked,true,false).' '.$disabled.' /> '.$item.'</label>'.( --$i ? '<br/>' : '' );
					}
				}
				break;

			case 'radio':
				if(!empty($label)&&is_array($label)){
					$label_keys = array_keys($label);
					if($disabled)
						if(in_array($default, $label_keys))
							$checked = $default;
						else
							$checked = $label_keys[0];
					else
						if(in_array($value, $label_keys))
							$checked = $value;
						else
							if(in_array($default, $label_keys))
								$checked = $default;
							else
								$checked = $label_keys[0];
					$i = count($label);
					foreach($label as $key => $item){
						$output .= '<label><input type="radio" name="'.$name.'" value="'.$key.'" '.$disabled.' '.checked($key,$checked,false).' /> '.$item.'</label>'.( --$i ? '<br/>' : '' );
					}
				}
				break;

			case 'select':
				if(!empty($label)&&is_array($label)){
					$output .= '<select name="'.$name.'" '.$disabled.'>';
					$label_keys = array_keys($label);
					if($disabled)
						if(in_array($default, $label_keys))
							$checked = $default;
						else
							$checked = $label_keys[0];
					else
						if(in_array($value, $label_keys))
							$checked = $value;
						else
							if(in_array($default, $label_keys))
								$checked = $default;
							else
								$checked = $label_keys[0];
					foreach($label as $key => $item){
						$output .= '<option value="'.$key.'" '.selected($key,$checked,false).' />'.$item.'</option>';
					}
					$output .= '</select>';
				}
				break;

			case 'color':
				$output .= '<input class="tesla-option-color" type="text" name="'.$name.'" value="'.(''===$value&&''!==$default?$default:$value).'" placeholder="'.$placeholder.'" '.$disabled.' '.( !empty($default) ? 'data-default-color="'.$default.'"' : '' ).' />';
				break;

			case 'date':
				$output .= '<input class="tesla-option-date" type="text" name="'.$name.'" value="'.(''===$value&&''!==$default?$default:$value).'" placeholder="'.$placeholder.'" '.$disabled.' />';
				break;

			default:
				break;

		}

		return $output;

	}

	private static function normalize_keys($arr,$options){

		if(!isset($options['multiple'])||$options['multiple']===false){
			if($options['type']==='checkbox'&&is_array($arr)&&isset($arr['_level']))
				unset($arr['_level']);
			if(is_array($options['type'])){
				foreach($options['type'] as $key => $value)
					if(isset($arr[$key]))
						$arr[$key] = self::normalize_keys($arr[$key],$value);
			}
			return $arr;
		}else{
			if(is_array($arr)){
				if(isset($arr['_level']))
					unset($arr['_level']);
				if($options['type']==='checkbox')
					foreach($arr as $id => $item)
						if(is_array($item)&&isset($item['_level']))
							unset($arr[$id]['_level']);
				if(is_array($options['type']))
					foreach($arr as $id => $item){
						foreach($options['type'] as $key => $value)
							if(isset($item[$key]))
								$arr[$id][$key] = self::normalize_keys($item[$key],$value);
					}
				return array_values($arr);
			}else
				return $arr;
		}

	}

	public static function metabox_save($post_id){

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
	        return;

	    if (!isset($_POST['slide_options_nonce']) || !wp_verify_nonce($_POST['slide_options_nonce']))
	        return;

	    if (!current_user_can('edit_post', $post_id))
	        return;

	    if (wp_is_post_revision($post_id) === false) {

	    	$post_type = get_post_type($post_id);

	    	$slider_options = self::$slider_config;

	    	$post_options = $slider_options[$post_type]['options'];

	    	$values_array = array();

	    	foreach($post_options as $field => $value){

    			$values_array[$field] = self::normalize_keys($_POST[$field],$value);

	    	}

	        add_post_meta($post_id, 'slide_options', $values_array, true) or
            	update_post_meta($post_id, 'slide_options', $values_array);

	    }

	}

	private static function process_options($values_array, $options_array){
		$result = array();
		foreach($options_array as $option_id => $option){
			if(is_array($option['type'])){
				if(!isset($option['group'])||$option['group']===true){
					if(empty($option['multiple'])){
						if(isset($values_array[$option_id])){
							if($values_array[$option_id]===array()){
								if(isset($option['default'])){
									$result[$option_id] = $option['default'];
								}else{
									$result[$option_id] = array();
								}
							}else{
								$result[$option_id] = self::process_options($values_array[$option_id], $option['type']);
							}
						}else{
							if(isset($option['default'])){
								$result[$option_id] = $option['default'];
							}else{
								$result[$option_id] = array();
							}
						}
					}else{
						$result[$option_id] = array();
						foreach ($values_array[$option_id] as $value) {
							if($value===array()){
								if(isset($option['default'])){
									array_push($result[$option_id], $option['default']);
								}else{
									array_push($result[$option_id], array());
								}
							}else{
								array_push($result[$option_id], self::process_options($value, $option['type']));
							}
						}
					}
				}else{
					if(empty($option['multiple'])){
						$key = key($values_array[$option_id]);
						$result[$option_id] = self::process_options( $values_array[$option_id], array( $key => $option['type'][$key] ));
					}else{
						$result[$option_id] = array();
						foreach ($values_array[$option_id] as $value) {
							$key = key($value);
							array_push($result[$option_id], self::process_options( $value, array( $key => $option['type'][$key] )));
						}
					}
				}
			}else{
				if(empty($option['multiple'])){
					if(isset($values_array[$option_id])){
						if($values_array[$option_id]===''){
							if(isset($option['default'])){
								$result[$option_id] = $option['default'];
							}else{
								$result[$option_id] = '';
							}
						}else{
							$result[$option_id] = self::process_options_filter($option['type'], $values_array[$option_id]);
						}
					}else{
						if(isset($option['default'])){
							$result[$option_id] = $option['default'];
						}else{
							$result[$option_id] = '';
						}
					}
				}else{
					$result[$option_id] = array();
					foreach((array)$values_array[$option_id] as $value){
						if($value===''){
							if(isset($option['default'])){
								array_push($result[$option_id], $option['default']);
							}else{
								array_push($result[$option_id], '');
							}
						}else{
							array_push($result[$option_id], self::process_options_filter($option['type'], $value));
						}
					}
				}
			}
		}
		return $result;
	}

	private static function process_options_filter($option_type, $value){

		switch ($option_type) {
			case 'image':
				$filtered = new Tesla_slider_image($value);
				break;
			case 'line':
				$filtered = do_shortcode($value);
				break;
			case 'text':
				$filtered = do_shortcode($value);
				break;
			case 'editor':
				$filtered = do_shortcode($value);
				break;
			default:
				$filtered = $value;
				break;
		}

		return $filtered;

	}

	public static function get_slider_html($slider_id, $category='', $output_id = null, $post_id = null, $shortcode_parameters = array(), $query = array(), $custom = array()){

		$args = func_get_args();

		if(count($args)===2&&is_array($args[1])){
			$args = $args[1];
			$args_defaults = array(
				'category' => '',
				'output_id' => null,
				'post_id' => null,
				'shortcode_parameters' => array(),
				'query' => array(),
				'custom' => array()
			);
			$args = shortcode_atts($args_defaults,$args);
			$category = $args['category'];
			$output_id = $args['output_id'];
			$post_id = $args['post_id'];
			$shortcode_parameters = $args['shortcode_parameters'];
			$query = $args['query'];
			$custom = $args['custom'];
		}

		$slider_options = self::$slider_config;

		if(!array_key_exists($slider_id, $slider_options))
			return false;
		else{
			if($output_id===null)
				if(isset($slider_options[$slider_id]['output_default'])&&isset($slider_options[$slider_id]['output'][$slider_options[$slider_id]['output_default']]))
					$output_id = $slider_options[$slider_id]['output_default'];
				else{
					$output_id = array_keys($slider_options[$slider_id]['output']);
					$output_id = reset($output_id);
				}
			$data = array();
			if($post_id === null){
				$query_options = array(
					'post_type' => $slider_id,
					'order' => isset($slider_options[$slider_id]['order'])?$slider_options[$slider_id]['order']:'DESC',
					'posts_per_page' => -1,
					'suppress_filters' => false
				);
				if(!empty($category)){
					$query_options['tax_query'] = array(
						array(
							'taxonomy' => $slider_id.'_tax',
							'field' => 'slug',
							'terms' => explode(' ',$category),
							'operator' => 'IN'
						)
					);
				}
				if(!empty($query))
					$query_options = array_merge($query_options,$query);
				$posts_array = get_posts($query_options);
			}else
				if(is_array($post_id))
					$posts_array = $post_id;
				else
					$posts_array = array( get_post($post_id) );
			$values_array = array();
			$all_categories_pool = array();
			foreach($posts_array as $post){
				$meta = apply_filters('tesla_slide_options', get_post_meta($post->ID, 'slide_options', true), $post->ID, 'get_slider_html');
				$meta_array = self::process_options($meta, $slider_options[$slider_id]['options']);
				$post_cats = get_the_terms($post->ID, $slider_id.'_tax');
				$post_cats_slugs = array();
				if(is_array($post_cats))
					foreach($post_cats as $cat)
						if($cat->slug!==$category&&$cat->name!==$category)
							$post_cats_slugs[$cat->slug] = $cat->name;
				$all_categories_pool = array_merge($all_categories_pool,$post_cats_slugs);
				$values_array[] = array(
					'post' => $post,
					'options' => $meta_array,
					'categories' => $post_cats_slugs, //array of slugs
					'related' => self::get_related($post->ID,$category)
				);
			}
			$all_categories = Tesla_slider::get_categories($slider_id, true);
			// foreach ($all_categories as $all_categories_key => $all_categories_value)
			// 	if($all_categories_key===$category||$all_categories_value===$category)
			// 		unset($all_categories[$all_categories_key]);
			foreach ($all_categories as $all_categories_key => $all_categories_value)
				if(''!==$category&&!array_key_exists($all_categories_key, $all_categories_pool))
					unset($all_categories[$all_categories_key]);
			$data['slides'] = $values_array;
			$data['all_categories'] = $all_categories;
			$shortcode_defaults = isset($slider_options[$slider_id]['output'][$output_id]['shortcode_defaults'])?$slider_options[$slider_id]['output'][$output_id]['shortcode_defaults']:array();
			$data['shortcode'] = shortcode_atts($shortcode_defaults,$shortcode_parameters);
			$data['slider_id'] = $slider_id;
			$data['output_id'] = $output_id;
			$data['custom'] = $custom;
			
			$view = $slider_options[$slider_id]['output'][$output_id]['view'];
			return self::$load->view($view,$data,true,true);
		}

	}

	public static function get_available_sliders_list(){

		$slider_list = array();

		$slider_options = self::$slider_config;
		
		foreach($slider_options as $slider_id => $slider)
			$slider_list[$slider_id] = $slider['name'];

		return $slider_list;

	}

	public static function get_categories($slider_id, $hide_empty = false, $raw = false){

		$tax_args = apply_filters('tesla_slide_categories_args', array('hide_empty'=>$hide_empty), $slider_id);

		$tax_array = get_terms($slider_id.'_tax',$tax_args);

		$tax_array = apply_filters('tesla_slide_categories_'.$slider_id.'_tax', $tax_array);

		if($raw){
			return $tax_array;
		}

		$tax_categories = array();

		foreach($tax_array as $tax){
			$tax_categories[$tax->slug] = $tax->name;
		}

		return $tax_categories;

	}

	public static function get_related($post_id,$category){

		$slider_options = self::$slider_config;

		$slider_id = get_post_type($post_id);

		$slugs_array = get_the_terms($post_id, $slider_id.'_tax');
		$slugs = array();
		if(is_array($slugs_array))
			foreach($slugs_array as $cat)
				$slugs[] = $cat->slug;

		$posts_query = array(
			'post_type' => $slider_id,
			'post__not_in' => array( $post_id ),
			'order' => isset($slider_options[$slider_id]['order'])?$slider_options[$slider_id]['order']:'DESC',
			'posts_per_page' => -1,
			'suppress_filters' => false
		);

		$tax_query = array();

		if(!empty($slugs))
			$tax_query[] = array(
				'taxonomy' => $slider_id.'_tax',
				'field' => 'slug',
				'terms' => $slugs,
				'operator' => 'IN'
			);

		if(!empty($category)){
			$tax_query[] = array(
				'taxonomy' => $slider_id.'_tax',
				'field' => 'slug',
				'terms' => explode(' ',$category),
				'operator' => 'IN'
			);
			$tax_query['relation'] = 'AND';
		}

		if(!empty($tax_query))
			$posts_query['tax_query'] = $tax_query;

		$related_array = get_posts($posts_query);

		$values_array = array();
		foreach($related_array as $post){
			$meta = apply_filters('tesla_slide_options', get_post_meta($post->ID, 'slide_options', true), $post->ID, 'get_related');
			$meta_array = self::process_options($meta, $slider_options[$slider_id]['options']);
			
			$post_cats = get_the_terms($post->ID, $slider_id.'_tax');
			$post_cats_slugs = array();
			if(is_array($post_cats))
				foreach($post_cats as $cat)
					if($cat->slug!==$category&&$cat->name!==$category)
						$post_cats_slugs[] = $cat->slug;
			$values_array[] = array(
				'post' => $post,
				'options' => $meta_array,
				'categories' => $post_cats_slugs //array of slugs
			);
		}

		return $values_array;

	}

}

class Tesla_slider_image{

	public $url;
	public $id;

	public function __construct($value) {

		if(is_numeric($value)){
			$this->id = $value;
			$this->url = (string) wp_get_attachment_url($this->id);
		}else{
			$this->id = null;
			$this->url = (string) $value;
		}

	}

	public function __toString(){

		return $this->url;

	}

}

class Tesla_slider_tax{

	public $taxonomy;

	public function __construct($taxonomy) {

		$this->taxonomy = $taxonomy;

	}

	public function add_order(){

		if(is_admin()){

			global $pagenow;

			add_action( $this->taxonomy.'_add_form_fields', array($this, 'tt_taxonomy_add_fields'), 10, 2 );

			add_action( $this->taxonomy.'_edit_form_fields', array($this, 'tt_taxonomy_edit_fields'), 10, 2 );

			add_action( 'edited_'.$this->taxonomy.'', array($this, 'tt_taxonomy_save_fields'), 10, 2 );
			add_action( 'create_'.$this->taxonomy.'', array($this, 'tt_taxonomy_save_fields'), 10, 2 );

			add_filter('manage_edit-'.$this->taxonomy.'_columns', array($this, 'tt_taxonomy_columns_head'));

			add_filter('manage_'.$this->taxonomy.'_custom_column', array($this, 'tt_taxonomy_columns_content'), 10, 3);

			if('edit-tags.php'===$pagenow)
		    	add_action('quick_edit_custom_box', array($this, 'tt_taxonomy_columns_quick_edit'), 10, 3);

			add_action('admin_enqueue_scripts', array($this, 'tt_taxonomy_columns_scripts'));

		}else{

			add_filter('tesla_slide_categories_'.$this->taxonomy, array($this, 'tt_terms_ordered'), 10, 2);

		}

	}

	function tt_taxonomy_add_fields() {
	    ?>
	    <div class="form-field">
	        <label for="tt_tax_order_input"><?php _ex( 'Order', 'portfolio category order', 'tesla' ); ?></label>
	        <input type="text" name="tt_tax_order_input" id="tt_tax_order_input" value="">
	        <p class="description"><?php _ex( 'Set the order in which the categories should be displayed.', 'portfolio category order', 'tesla' ); ?></p>
	    </div>
	    <?php
	}

	function tt_taxonomy_edit_fields($term) {
	 
	    $term_meta = $this->tt_taxonomy_order($term->term_taxonomy_id);

	    ?>
	    <tr class="form-field">
	    <th scope="row" valign="top"><label for="tt_tax_order_input"><?php _ex( 'Order', 'portfolio category order', 'tesla' ); ?></label></th>
	        <td>
	            <input type="text" name="tt_tax_order_input" id="tt_tax_order_input" value="<?php echo esc_attr( $term_meta ); ?>">
	            <p class="description"><?php _ex( 'Set the order in which the categories should be displayed.', 'portfolio category order', 'tesla' ); ?></p>
	        </td>
	    </tr>
	    <?php
	}

	function tt_taxonomy_save_fields( $term_id ) {
	    if ( isset( $_POST['tt_tax_order_input'] ) ) {
	    	$term = get_term($term_id, $this->taxonomy);
	        $term_meta_array = $this->tt_taxonomy_order();
	        $term_meta = $_POST['tt_tax_order_input'];
	        $term_meta_array[$term->term_taxonomy_id] = $term_meta;
	        update_option("tt_taxonomy_order", $term_meta_array);
	    }
	}

	function tt_taxonomy_columns_head($columns) {
	    $columns['order']  = _x( 'Order', 'portfolio category order column', 'tesla' );
	    return $columns;
	}

	function tt_taxonomy_order($id = null){

	    $term_meta_array = get_option("tt_taxonomy_order", array());

	    if(is_null($id)){

	        return $term_meta_array;

	    }else{

	        $term_meta = (int) ( array_key_exists($id, $term_meta_array) ? $term_meta_array[$id] : 0 );
	        return $term_meta;

	    }

	}

	function tt_terms_ordered_compare($a, $b){

	    $a_order = $this->tt_taxonomy_order($a->term_taxonomy_id);
	    $b_order = $this->tt_taxonomy_order($b->term_taxonomy_id);

	    if ($a_order === $b_order)
	        return strcmp($a->name, $b->name);
	    else
	        return ($a_order < $b_order) ? -1 : 1;

	}

	function tt_terms_ordered($tax_array){

	    usort($tax_array, array($this,'tt_terms_ordered_compare'));
	    return $tax_array;

	}

	function tt_taxonomy_columns_content($empty, $column_name, $term_id) {
	    if ($column_name == 'order') {
	    	$term = get_term($term_id, $this->taxonomy);
	        $term_meta = $this->tt_taxonomy_order($term->term_taxonomy_id);
	        echo $term_meta;
	    }
	}

	function tt_taxonomy_columns_quick_edit($column_name, $screen, $tax){

	    if($tax !== $this->taxonomy.'' || $column_name !== 'order' || 'edit-tags' !== $screen)
	        return false;

	    ?>
	    <fieldset>
	        <div id="my-custom-content" class="inline-edit-col">
	            <label>
	                <span class="title">Order</span>
	                <span class="input-text-wrap"><input type="text" name="tt_tax_order_input" class="ptitle" value=""></span>
	            </label>
	        </div>
	    </fieldset>
	    <?php
	}

	function tt_taxonomy_columns_scripts($hook_suffix) {
	    if ('edit-tags.php' === $hook_suffix && isset($_GET['taxonomy']) && $this->taxonomy.'' === $_GET['taxonomy'] && !isset($_GET['action'])){
	        wp_enqueue_script('tt-tax-quickedit', tesla_locate_uri('tesla_framework/static/js/tax-quickedit.js'),array('inline-edit-tax'),null,true);
	    }
	}

}