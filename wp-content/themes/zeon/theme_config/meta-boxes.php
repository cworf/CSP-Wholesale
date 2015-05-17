<?php 
return array(
		'metaboxes'=>array(
			array(
			    'id'             => 'slider',            // meta box id, unique per meta box
			    'title'          => 'Tesla/Revolution Slider Options',   // meta box title
			    'post_type'      => array('page'),		// post types, accept custom post types as well, default is array('post'); optional
			    'taxonomy'       => array(),    // taxonomy name, accept categories, post_tag and custom taxonomies
			    'context'		 => 'normal',						// where the meta box appear: normal (default), advanced, side; optional
			    'priority'		 => 'low',						// order of meta box: high (default), low; optional
			    'input_fields'   => array(            			// list of meta fields 
			    	'slider_categ'=>array(
			    		'name'=>'Slides Category Slug / Revolution Slider alias',
			    		'type'=>'text'
			    		)
		    	),
			
			),
			array(
			    'id'             => 'video_featured',          // meta box id, unique per meta box
			    'title'          => 'Featured Video Embed',         // meta box title
			    'post_type'      => array('post'),
			    'priority'		 => 'low',
			    'context'		=> 'side',
			    'input_fields'   => array(            // list of meta fields (can be added by field arrays)
			    	'video_embed'=>array(
			    		'name'=>"Insert your embed code",
			    		'type'=>"textarea",
			    		)
			    	)
				),
		)
	);