<?php

return array(
        'favico' => array(
                'dir' => '/theme_config/icons/favicon_zeon_white.ico'
        ),
        'option_saved_text' => 'Options successfully saved',
        'tabs' => array(
                array(
                        'title'=>'General Options',
                        'icon'=>1,
                        'boxes' => array(
                                'Logo Customization' => array(
                                        'icon'=>'customization',
                                        'size'=>'2_3',
                                        'columns'=>true,
                                        'description'=>'Here you upload a image as logo or you can write it as text and select the logo color, size, font.',
                                        'input_fields' => array(
                                                'Logo As Image'=>array(
                                                        'size'=>'half',
                                                        'id'=>'logo_image',
                                                        'type'=>'image_upload',
                                                        'note'=>'Here you can insert your link to a image logo or upload a new logo image.'
                                                ),
                                                'Logo As Text'=>array(
                                                        'size'=>'half_last',
                                                        'id'=>'logo_text',
                                                        'type'=>'text',
                                                        'note' => "Type the logo text here, then select a color, set a size and font",
                                                        'color_changer'=>true,
                                                        'font_changer'=>true,
                                                        'font_size_changer'=>array(8,50, 'px'),
                                                        'font_preview'=>array(true, true)
                                                ),
                                                'Logo/Menu Wrapper Size' => array(
                                                        'id'    => 'logo_wrapper_size',
                                                        'type'  => 'radio',
                                                        'size' => '1',
                                                        'values' => array('2','3','4','5','6','7','8'),
                                                        'note' => 'Change this size if your logo is being shrinked (smaller than it should be) or your meta menu needs more space. Note : This will decrease/increase the size for the meta menu (the bigger this options, the smaller the meta menu and vice versa). Default is 4.'
                                                )
                                        )
                                ),
                                'Favicon' => array(
                                        'icon'=>'customization',
                                        'size'=>'1_3_last',
                                        'input_fields' => array(
                                                array(
                                                        'id'=>'favicon',
                                                        'type'=>'image_upload',
                                                        'note'=>'Here you can upload the favicon icon.'
                                                )
                                        )
                                ),
                                'Custom CSS' => array(
                                        'icon'=>'css',
                                        'size'=>'1',
                                        'description'=>'Here you can write your personal CSS for customizing the classes you want. Or use our <b>Custom Styler</b>, from the Site Colors tab, for an easier custom css color picking.',
                                        'input_fields' => array(
                                                array(
                                                        'id'=>'custom_css',
                                                        'type'=>'textarea'
                                                )
                                        )
                                ),
                                'Custom JS' => array(
                                        'icon'=>'js',
                                        'size'=>'1',
                                        'description'=>'Here you can write your personal JS that will be appended to footer.',
                                        'input_fields' => array(
                                                array(
                                                        'id'=>'custom_js',
                                                        'type'=>'textarea'
                                                )
                                        )
                                )
                        )
                ),
                array(
                        'title' => 'Site Colors',
                        'icon'=>4,
                        'boxes' => array(
                                'Background Customization'=>array(
                                        'icon'=>'customization',
                                        'columns'=>true,
                                        'size' => '2_3',
                                        'input_fields' => array(
                                                'Background Color'=>array(
                                                        'size'=>'half',
                                                        'id'=>'bg_color',
                                                        'type'=>'colorpicker'
                                                ),
                                                'Background Image' => array(
                                                        'size'=>'half_last',
                                                        'id'=>'bg_image',
                                                        'type'=>'image_upload'
                                                )
                                        )
                                ),
                                'Site Colors' => array(
                                        'icon'=>'background',
                                        'columns'=>true,
                                        'size' => '2_3',
                                        'input_fields' => array(
                                                'Primary Site Color'=>array(
                                                        'size'=>'half',
                                                        'id'=>'site_color',
                                                        'type'=>'colorpicker',
                                                        'note'=>'Choose primary color for your website. This will affect only specific elements.<br>To return to default color , open colorpicker and click the Clear button.'
                                                ),
                                                'Secondary Site Color'=>array(
                                                        'size'=>'half_last',
                                                        'id'=>'site_color_2',
                                                        'type'=>'colorpicker',
                                                        'note'=>'Choose secondary color for your website. This will affect only specific elements.<br>To return to default color , open colorpicker and click the Clear button.'
                                                ),
                                        )
                                ),
                                'Custom Styler'=>array(
                                    'icon' => 'customization',
                                    'description'=>"Add new custom CSS rules with ease. <a target='_blank' href='http://teslathemes.com/doc/zeon/#fw-custom-styler'>How to use ?</a>",
                                    'size'=>'half',
                                    'repeater' => 'Add new rule/style',
                                    'input_fields' =>array(
                                            'CSS Selector'=>array(
                                                    'size'=>'1_3',
                                                    'id'=>'custom_selector',
                                                    'type'=>'text',
                                                    'placeholder' => '.class',
                                                    'note' => "Insert CSS selector that will be used when applying the custom colors.",
                                                    ),
                                            'Color'=>array(
                                                    'type'=>'colorpicker',
                                                    'id'=>'custom_color',
                                                    'note'=>'Custom color applied to the elemnts matching the above css selector.'
                                                    ),
                                            'Background Color'=>array(
                                                    'type'=>'colorpicker',
                                                    'id'=>'custom_bg_color',
                                                    'note'=>'Custom background color applied to the elemnts matching the above css selector.'
                                                    ),
                                            'Important' => array(
                                                    'id'    => 'important',
                                                    'type'  => 'checkbox',
                                                    'label' => 'If the colors are not applied you can try selecting this checkbox to make them important.',
                                            ),
                                    )
                                ),
                        )
                ),
                array(
                        'title' => 'SEO and Socials',
                        'icon'=>2,
                        'boxes'=>array(
                                'ShareThis feature'=>array(
                                        'icon'=>'social',
                                        'description'=>"To use this service please select your favorite social networks",
                                        'size'=>'3',
                                        'input_fields'=>array(
                                                array(
                                                        'type'  => 'select',
                                                        'id'    => 'share_this',
                                                        'label' => 'Facebook',
                                                        'class'  => 'social_search',
                                                        'multiple' => true,
                                                        'options'=>array('Google'=>'googleplus','Facebook'=>'facebook','Twitter'=>'twitter','Pinterest'=>'pinterest',"Linkedin"=>'linkedin')
                                                )
                                        )
                                ),
                                'Social Platforms'=>array(
                                        'icon'=>'social',
                                        'description'=>"Insert the link to the social share page.",
                                        'size'=>'3',
                                        'columns'=>true,
                                        'input_fields'=>array(
                                                array(
                                                        'id'=>'social_platforms',
                                                        'size'=>'half',
                                                        'type'=>'social_platforms',
                                                        'platforms'=>array('facebook','twitter','vimeo','pinterest','google')
                                                )
                                        )
                                ),
                                'Tracking Code' => array(
                                        'icon'=>'track',
                                        'size'=>'3_last',
                                        'input_fields'=>array(
                                                array(
                                                        'type'=>'textarea',
                                                        'id'=>'tracking_code'
                                                )
                                        )
                                )
                        )
                ),
                array(
                        'title' => 'Additional Options',
                        'icon'  => 6,
                        'boxes' => array(
                                '404 error page settings'=>array(
                                        'icon' => 'customization',
                                        'description'=>"Setup your 404 error page",
                                        'size'=>'1',
                                        'columns'=>true,
                                        'input_fields' =>array(
                                            'Image' => array(
                                                    'id'    => 'error_image',
                                                    'type'  => 'image_upload',
                                                    'note' => 'Here you can insert your link to a image or upload a new 404 error image.',
                                                    'size' => 'half'
                                            ),
                                            'Page title' => array(
                                                    'id'    => 'error_title',
                                                    'type'  => 'text',
                                                    'note' => 'This is the title of the 404 page',
                                                    'size' => 'half_last'
                                            ),
                                            'Message' => array(
                                                    'id'    => 'error_message',
                                                    'type'  => 'textarea',
                                                    'note' => 'This message will appear on 404 page. Use html (&lt;h2&gt;,&lt;h3&gt;,&lt;p&gt;) to enhance it .',
                                                    'size' => 'half'
                                            )
                                        )
                                ),
                                'Page Settings'=>array(
                                        'icon' => 'customization',
                                        'description'=>"Other settings",
                                        'size'=>'1',
                                        'columns'=>true,
                                        'input_fields' =>array(
                                                'Header Text' => array(
                                                        'id'    => 'header_text',
                                                        'type'  => 'text',
                                                        'note' => 'Text that will appear in the left part of the header.',
                                                        'size'  =>      '3'
                                                ),
                                                'Related Posts' => array(
                                                        'id'    => 'show_related_posts',
                                                        'type'  => 'checkbox',
                                                        'label' => 'To show related posts, beneath single post content, this checkbox must be checked',
                                                        'size' => '3'
                                                ),
                                                'Copyright' => array(
                                                        'id'    => 'copyright_message',
                                                        'type'  => 'text',
                                                        'note' => 'Message that will appear in the footer.',
                                                        'size'  =>      '3_last'
                                                ),
                                                'Footer Text' => array(
                                                        'id'    => 'footer_text',
                                                        'type'  => 'text',
                                                        'note' => 'Text that will appear in the right part of the footer (you can use html to place images with link) .',
                                                        'size'  => 'half'
                                                ),
                                                'Breadcrumbs' => array(
                                                        'id'    => 'show_breadcrumbs',
                                                        'type'  => 'checkbox',
                                                        'label' => 'To show woocommerce\'s breadcrumbs, this checkbox must be checked',
                                                        'size' => 'half_last'
                                                ),
                                                
                                        )
                                )
                        )
                ),
                array(
                        'title' => 'Contact Info',
                        'icon'  => 5,
                        'boxes' => array(
                                'Contact info'=>array(
                                        'icon' => 'customization',
                                        'description'=>"Provide contact information. This information will appear in contact template. For more informations read documentation.",
                                        'size'=>'2_3',
                                        'columns'=>true,
                                        'input_fields' =>array(
                                                'Map' => array(
                                                        'id'    => 'contact_map',
                                                        'type'  => 'map',
                                                        'note' => 'Just navigate to the location you want to be displayed on the google map and if you want a pin over your location , 
                                                                    press the "Drop marker here" button. You can also choose another icon for it.' ,
                                                        'size' => 'half',
                                                        'icons' => array('google-marker.gif','home.png','home_1.png','home_2.png','administration.png','office-building.png')
                                                ),
                                                'Contact form' => array(
                                                        'id'    => 'contact_form',
                                                        'type'  => 'checkbox',
                                                        'label' => 'To use Contact Form , this checkbox must be checked',
                                                        'size' => 'half_last',
                                                        'action' => array('show',array('title_contact'))
                                                ),
                                                array(
                                                        'id'    => 'title_contact',
                                                        'type'  => 'text',
                                                        'note' => 'Contact form header',
                                                        'size' => 'half',
                                                        'placeholder' => 'Drop us a line'
                                                ),
                                                array(
                                                        'id'    => 'email_contact',
                                                        'type'  => 'text',
                                                        'note' => 'Provide an email, used to recive messages from Contact Form',
                                                        'size' => 'half_last',
                                                        'placeholder' => 'Contact Form Email'
                                                ),
                                                'Contact address' => array(
                                                        'id'    => 'contact_address',
                                                        'type'  => 'textarea',
                                                        'note' => 'Provide your address',
                                                        'placeholder' => 'Address',
                                                        'size' => 'half'
                                                ),
                                                array(
                                                        'id'    => 'contact_phone',
                                                        'type'  => 'text',
                                                        'note' => 'Provide your phone number',
                                                        'size' => 'half_last',
                                                        'placeholder' => 'Phone number'
                                                ),
                                                array(
                                                        'id'    => 'contact_fax',
                                                        'type'  => 'text',
                                                        'note' => 'Provide your fax number',
                                                        'size' => 'half',
                                                        'placeholder' => 'Fax number'
                                                ),
                                        )
                                )

                        )
                ),
                array(
                        'title' => 'Subscription',
                        'icon'  => 7,
                        'boxes' => array(
                                'Subscribers'=>array(
                                        'icon' => 'social',
                                        'description'=>'First 20 subscribers are listed here. To get the full list export files using buttons below:',
                                        'size'=>'full',
                                        'input_fields' => array(
                                                array(
                                                        'type'=>'subscription',
                                                        'id'=>'subscription_list'
                                                )
                                        )
                                )
                        )
                ),
                array(
                        'title' => 'Typography',
                        'icon'  => 1,
                        'boxes' => array(
                                'Font Changers'=>array(
                                        'icon' => 'customization',
                                        'description'=>'Change the fonts & colors for site\'s sections:',
                                        'size'=>'1',
                                        'columns'=>true,
                                        'input_fields' => array(
                                                'Main Content Font/Color'=>array(
                                                    'size'=>'1_3',
                                                    'id'=>'main_content_text',
                                                    'type'=>'text',
                                                    'note' => "Then select a color, set a size and choose a font",
                                                    'color_changer'=>true,
                                                    'font_changer'=>true,
                                                    'font_size_changer'=>array(8,50, 'px'),
                                                    'hide_input'=>true,
                                                    ),
                                                'Sidebar Font/Color'=>array(
                                                    'size'=>'1_3',
                                                    'id'=>'sidebar_text',
                                                    'type'=>'text',
                                                    'note' => "Then select a color, set a size and choose a font",
                                                    'color_changer'=>true,
                                                    'font_changer'=>true,
                                                    'font_size_changer'=>array(8,50, 'px'),
                                                    'hide_input'=>true,
                                                    ),
                                                'Menu Font/Color'=>array(
                                                    'size'=>'1_3_last',
                                                    'id'=>'menu_text',
                                                    'type'=>'text',
                                                    'note' => "Then select a color, set a size and choose a font",
                                                    'color_changer'=>true,
                                                    'font_changer'=>true,
                                                    'font_size_changer'=>array(8,50, 'px'),
                                                    'hide_input'=>true,
                                                    ),
                                                
                                        )
                                ),
                                
                        )
                )
        ),
        'styles' => array( array('wp-color-picker'),'style','select2' )
        ,
        'scripts' => array( array( 'jquery', 'jquery-ui-core','jquery-ui-datepicker','wp-color-picker' ), 'select2.min','jquery.cookie','tt_options', 'admin_js' )
);