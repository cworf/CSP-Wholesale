<?php

class TeslaFramework {
	
	protected $load;
	
	function __construct(){
		$this->load = new TT_Load;
		if(class_exists('TT_Security'))
			$this->tesla_security = new TT_Security();
		$this->load->helper('general');
		$this->load->helper('twitter');
		$this->metaboxes();
		$this->load_scripts();
		$this->theme_domain();
	}

	//Loading metaboxes class
	function metaboxes(){
		/*
		Plugin Name: Demo MetaBox
		Plugin URI: http://en.bainternet.info
		Description: My Meta Box Class usage + Tax meta class
		Version: 3.1.0
		Author: Bainternet, Ohad Raz
		Author URI: http://en.bainternet.info
		*/
		$meta_options = include TT_THEME_DIR . '/theme_config/meta-boxes.php';

		
		if (!empty($meta_options)){
			//include the main classes files
			require_once(TT_FW_DIR . "/extensions/meta-box-class/my-meta-box-class.php");
			require_once(TT_FW_DIR . "/extensions/Tax-meta-class/Tax-meta-class.php");
			$prefix = THEME_NAME . "_";
			if(!empty($meta_options['metaboxes'])){
				foreach ($meta_options['metaboxes'] as $meta_id => $meta) {
					$config = array(
						'id'             => '',          // meta box id, unique per meta box
						'title'          => __('Simple Meta Box fields','TeslaFramework'),          // meta box title
						'pages'          => array(),      // post types, accept custom post types as well, default is array('post'); optional
						'context'        => 'normal',            // where the meta box appear: normal (default), advanced, side; optional
						'priority'       => 'high',            // order of meta box: high (default), low; optional
						'fields'         => array(),            // list of meta fields (can be added by field arrays)
						'local_images'   => false,          // Use local or hosted images (meta box images for add/remove)
						'use_with_theme' => true          //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
					  );
					if(!empty($meta['post_type'])){
						$meta['pages'] = $meta['post_type'];
						unset($meta['post_type']);
					}
					$config = array_merge($config,$meta);
					$my_meta_{$meta_id} =  new AT_Meta_Box($config);
					if ( !empty($meta['taxonomy'])){
						$config_tax = $config;
						$config_tax['pages'] = $config_tax['taxonomy'];
						unset($config_tax['taxonomy']);
						$my_meta_tax_{$meta_id} =  new Tax_Meta_Class($config_tax);
					}
					if ( !empty($meta['input_fields'])){
						foreach ($meta['input_fields'] as $input_id => $input) {
							$input_type = "add" . ucfirst($input['type']);
							if ($input['type'] == 'taxonomy'){
								$my_meta_{$meta_id}->$input_type($prefix.$input_id,array('taxonomy'=>$input['taxonomy']),$input);
								if(isset($my_meta_tax_{$meta_id}))
									$my_meta_tax_{$meta_id}->$input_type($prefix.$input_id,array('taxonomy'=>$input['taxonomy']),$input);
							}
							else if ($input['type'] == 'select' || $input['type'] == 'radio' ){
								$my_meta_{$meta_id}->$input_type($prefix.$input_id,$input['values'],$input);
								if(isset($my_meta_tax_{$meta_id}))
									$my_meta_tax_{$meta_id}->$input_type($prefix.$input_id,$input['values'],$input);
							}
							else{
								$my_meta_{$meta_id}->$input_type($prefix.$input_id,$input);
								if(isset($my_meta_tax_{$meta_id})){
									$my_meta_tax_{$meta_id}->$input_type($prefix.$input_id,$input);
								}

							}
						}
					}
					$my_meta_{$meta_id}->Finish();
					if(isset($my_meta_tax_{$meta_id}))
						$my_meta_tax_{$meta_id}->Finish();
				}
			}
		}
	}

	function load_scripts(){
		add_action('wp_enqueue_scripts', array($this,'enqueue_default_scripts'));
		add_action('wp_head',array($this,'ajaxurl'));
	}

	function enqueue_default_scripts(){
		wp_enqueue_script('google_map', 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places', '', false, true);
		
	}

	//inserting ajaxurl variable in the head for frontend usage
	function ajaxurl()
	{
		echo '<script type="text/javascript">var ajaxurl = \''.admin_url('admin-ajax.php').'\';</script>';
	}

	//Load Textdomain
	function theme_domain(){
		add_action('after_setup_theme', array($this,'theme_textdomain_setup'));
	}
		
	function theme_textdomain_setup(){
		load_theme_textdomain(THEME_NAME, TT_THEME_DIR . '/languages');
	}

	function function_checks(){
		if (!method_exists('TT_Security','check_username'))
			return FALSE;
		if (file_exists(TT_FW_DIR . '/core/tt_security.php')){
			if (strlen(trim(preg_replace('/\s\s+/', ' ', file_get_contents(TT_FW_DIR . '/core/tt_security.php')))) !== 3294 )
				return FALSE;
		}else
			return FALSE;
		return TRUE;
	}

}