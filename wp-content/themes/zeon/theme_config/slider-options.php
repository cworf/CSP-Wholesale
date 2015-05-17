<?php

return array(
	'slider' => array(
		'name' => 'Main Slider',
		'term' => 'slide',
		'term_plural' => 'slides',
		'order' => 'ASC',
		'options' => array(
			'image' => array(
				'type' => 'image',
				'description' => 'Image of the slide',
				'title' => 'Image',
				'default' => 'holder.js/1920x700/auto'
			)
		),
		'output_default' => 'main',
		'output' => array(
			'main' => array(
				'shortcode' => 'tesla_slider',
				'view' => 'views/slider-view',
				'shortcode_defaults' => array(
					'nr'=>0,
					'title'=>'New Products',
				)
			)
		),
		'icon' => 'icons/favicon_zeon_white.ico',
	),
	'faq' => array(
		'name' => 'FAQ',
		'term' => 'Questions and answers',
		'term_plural' => 'Q & A',
		'order' => 'ASC',
		'has_single' => false,
		'post_options' => array('supports'=> array( 'title', 'editor')),
		'taxonomy_options' => array('show_ui' => false),
		'options' => array(),
		'icon' => 'icons/favicon_zeon_white.ico',
		'output' => array(
			'main' => array(
				'shortcode' => 'tesla_faq',
				'view' => 'views/faq-view',
				'shortcode_defaults' => array(
					
				)
			)
		)
	),
	'info_box' => array(
		'name' => 'Info Boxes',
		'term' => 'Info Box',
		'term_plural' => 'Info Boxes',
		'order' => 'ASC',
		'has_single' => false,
		'post_options' => array('supports'=> array( 'title', 'editor', 'thumbnail'), 'taxonomies' => array(),'has_archive'=>false),
		'taxonomy_options' => array('show_ui' => true),
		'options' => array(
			
		),
		'icon' => 'icons/favicon_zeon_white.ico',
		'output' => array(
			'main' => array(
				'shortcode' => 'tesla_info_box',
				'view' => 'views/info-box-view',
				'shortcode_defaults' => array(
				)
			)
		)
	),
	'services' => array(
		'name' => 'Services',
		'term' => 'service',
		'term_plural' => 'services',
		'order' => 'ASC',
		'has_single' => true,
		'post_options' => array('supports'=>array('title','editor')),
		'taxonomy_options' => array('show_ui'=>true),
		'options' => array(
			'icon'=>array(
				'title'=> 'Set Service Icon',
				'type'=>array(
					'def_icon' => array(
						'title' => 'Vector Icon',
			            'description' => 'Choose from one of the hundreds of vector icons and then apply a color for better performance on all devices.',
			            'type' => array(
							'icon_nr'	=>	array(
								'title' => 'Icon',
					            'description' => 'The nr of the vector icon. You can see all the numbers by hovering on icons on this page <a href="http://teslathemes.com/demo/wp/zeon/?p=498">Icons Set</a>.',
					            'type' => 'line',
					            'default' => '374'
							),
							'icon_color' => array(
								'type' => 'color',
								'description' => 'Choose icon color',
								'title' => 'Icon Color',
								'default' => '#191919'
							),
							'bg_color' => array(
								'type' => 'color',
								'description' => 'Choose icon\'s background color',
								'title' => 'Icon Background Color',
								'default' => ''
							)
						),
						'group' => true,
					),
					'custom_icon' => array(
						'title' => 'Custom Icon',
						'description' => 'Upload your own icon here.',
						'type' => 'image',
						'default' => 'holder.js/140x140/auto'
					)
				),
				'group'=>false
			),
			'title_color'	=> array(
				'type' => 'color',
				'description' => 'Choose service title color',
				'title' => 'Title Color',
				'default' => ''
			)
		),
		'icon' => 'icons/favicon_zeon_white.ico',
		'output_default' => 'main',
		'output' => array(
			'main' => array(
				'shortcode' => 'tesla_services',
				'view' => 'views/services-view',
				'shortcode_defaults' => array(
					'title' => 'SERVICES',
					'nr' => 0
				)
			),
			'second'=>array(
				'shortcode' => 'tesla_services_2',
				'view' => 'views/services-2-view',
				'shortcode_defaults' => array(
					'title' => 'SERVICES',
					'nr' => 0
				)
			)
		)
	),
	'testimonials' => array(
		'name' => 'Testimonials',
		'term' => 'testimonial',
		'term_plural' => 'testimonials',
		'order' => 'ASC',
		'has_single' => false,
		'post_options' => array('supports'=>array('title','editor','thumbnail')),
		'taxonomy_options' => array('show_ui'=>false),
		'options' => array(),
		'icon' => 'icons/favicon_zeon_white.ico',
		'output_default' => 'main',
		'output' => array(
			'main' => array(
				'shortcode' => 'tesla_testimonials',
				'view' => 'views/testimonials-view',
				'shortcode_defaults' => array(
					'title' => 'Testimonials'
				)
			)
		)
	),
	'stats' => array(
		'name' => 'Stats',
		'term' => 'stat',
		'term_plural' => 'stats',
		'order' => 'ASC',
		'has_single' => false,
		'post_options' => array('supports'=>array('title','editor')),
		'options' => array(
				
			),
		'icon' => 'icons/favicon_zeon_white.ico',
		'output_default' => 'main',
		'output' => array(
			'main' => array(
				'shortcode' => 'tesla_stats',
				'view' => 'views/stats-view',
				'shortcode_defaults' => array(
					'title' => 'Statistics'
				)
			)
		)
	),
	'clients' => array(
		'name' => 'Clients Slider',
		'term' => 'client',
		'term_plural' => 'clients',
		'order' => 'ASC',
		'has_single' => false,
		'post_options' => array('supports'=>array('title','thumbnail')),
		'taxonomy_options' => array('show_ui'=>false),
		'options' => array(
			'link' => array(
					'title' => 'Link to',
		            'description' => 'Insert the link to where should clicking on the image lead.',
		            'type' => 'line',
		            'default' => '#'
				)
			),
		'icon' => 'icons/favicon_zeon_white.ico',
		'output_default' => 'main',
		'output' => array(
			'main' => array(
				'shortcode' => 'tesla_clients_slider',
				'view' => 'views/clients-slider-view',
				'shortcode_defaults' => array(
					'title' => 'Our Brands'
				)
			)
		)
	),
	'toggle' => array(
		'name' => 'Toggle List',
		'term' => 'Toggle list item',
		'term_plural' => 'Toggle list items',
		'order' => 'ASC',
		'has_single' => false,
		'post_options' => array('supports'=>array('title','editor')),
		'taxonomy_options' => array('show_ui'=>false),
		'options' => array(),
		'icon' => 'icons/favicon_zeon_white.ico',
		'output_default' => 'main',
		'output' => array(
			'main' => array(
				'shortcode' => 'tesla_toggle_list',
				'view' => 'views/toggle-list-view',
				'shortcode_defaults' => array(
					'title' => 'Why Choose Us'
				)
			)
		)
	),
	'tabs' => array(
		'name' => 'Tabs',
		'term' => 'Tab',
		'term_plural' => 'Tabs',
		'order' => 'ASC',
		'has_single' => false,
		'post_options' => array('supports'=>array('title','editor')),
		'taxonomy_options' => array('show_ui'=>true),
		'options' => array(),
		'icon' => 'icons/favicon_zeon_white.ico',
		'output_default' => 'main',
		'output' => array(
			'main' => array(
				'shortcode' => 'tesla_tabs',
				'view' => 'views/tabs-view',
				'shortcode_defaults' => array(
					
				)
			)
		)
	),
	'team' => array(
		'name' => 'Team',
		'term' => 'team member',
		'term_plural' => 'team members',
		'order' => 'ASC',
		'has_single' => false,
		'post_options' => array('supports'=>array('title','editor','thumbnail')),
		'taxonomy_options' => array('show_ui'=>false),
		'options' => array(
			'position' => array(
				'title' => 'Position',
				'description' => 'Enter the position of this team member.',
				'type' => 'line'
			),
			'social' => array(
				'title' => 'Social Icons',
				'description' => 'Add social icons for current team member.',
				'type' => array(
					'facebook' => array(
						'title' => 'Facebook',
						'description' => 'Set the full URL to the Facebook page.',
						'type' => 'line'
					),
					'twitter' => array(
						'title' => 'Twitter',
						'description' => 'Set the full URL to the Twitter page.',
						'type' => 'line'
					),
					'google' => array(
						'title' => 'Google+',
						'description' => 'Set the full URL to the Google+ page.',
						'type' => 'line'
					),
					'pinterest' => array(
						'title' => 'Pinterest',
						'description' => 'Set the full URL to the Pinterest page.',
						'type' => 'line'
					),
					'vimeo' => array(
						'title' => 'Vimeo',
						'description' => 'Set the full URL to the Vimeo page.',
						'type' => 'line'
					),
					'custom' => array(
						'title' => 'Custom icon',
						'description' => 'Set up an icon for a custom social platform.',
						'type' => array(
							'icon' => array(
								'title' => 'Icon',
								'description' => 'Set the icon for the social platform.',
								'type' => 'image',
								'default' => 'holder.js/20x20/auto/#fff:#000'
							),
							'url' => array(
								'title' => 'URL',
								'description' => 'Set the full URL to the custom social platform.',
								'type' => 'line'
							)
						)
					)
				),
				'group' => false,
				'multiple' => true
			)
		),
		'icon' => 'icons/favicon_zeon_white.ico',
		'output_default' => 'main',
		'output' => array(
			'main' => array(
				'shortcode' => 'tesla_team',
				'view' => 'views/team-view',
				'shortcode_defaults' => array(
					'title' => 'our team',
				)
			)
		)
	),
	'price' => array(
		'name' => 'Pricing Tables',
		'term' => 'Pricing table',
		'term_plural' => 'Pricing tables',
		'order' => 'ASC',
		'has_single' => false,
		'options' => array(
			'price' => array(
				'title' => 'Price',
				'description' => 'Set the price for current table.',
				'type' => 'line'
			),
			'link' => array(
				'title' => 'Link',
				'description' => 'Set the full URL for the buy button.',
				'type' => 'line'
			),
			'link_text' => array(
				'title' => 'Link Text',
				'description' => 'Text of the button.',
				'type' => 'line'
			),
			'outlined' => array(
				'title' => 'Outline',
				'type' => 'checkbox',
				'label' => array('outlined'=>'Outline this table (make it stand out)')
			),
			'type'	=> array(
				'type'=>'radio',
				'description' => 'Select style type of the pricing table (1 - with header and footer or 2 - header only)',
				'title' => 'Box Style',
				'label' => array('1'=>'1', '2'=>'2'),
				'default' => '1'
			),
			'features' => array(
				'title' => 'Options',
				'description' => 'Add options for current table.',
				'type' => 'line',
				'multiple' => true
			)
		),
		'icon' => 'icons/favicon_zeon_white.ico',
		'output_default' => 'main',
		'output' => array(
			'main' => array(
				'shortcode' => 'tesla_pricing_tables',
				'view' => 'views/price-view',
				'shortcode_defaults' => array(
					'size' => 4
				)
			)
		)
	),
	'special_offers' => array(
		'name' => 'Special Offers',
		'term' => 'Special offer',
		'term_plural' => 'Special Offers',
		'order' => 'ASC',
		'has_single' => false,
		'post_options' => array('supports'=> array( 'title', 'editor','thumbnail')),
		'options' => array(
			'link'	=>array(
				'title' => 'Button Link',
				'description' => 'Set the link for the Call To Action button.',
				'type' => 'line'
			),
			'color'	=> array(
				'type'	=> 'color',
				'title'	=> 'Color',
				'default'	=> '',
				'description'	=> 'Choose color for current Special offer'
			)
		),
		'icon' => 'icons/favicon_zeon_white.ico',
		'output_default' => 'main',
		'output' => array(
			'main' => array(
				'shortcode' => 'tesla_special_offers',
				'view' => 'views/special-offers-view',
				'shortcode_defaults' => array(
					'title'=>'Best deals',
					'description'=>'',
					'boxed' => false
				)
			)
		)
	),

);