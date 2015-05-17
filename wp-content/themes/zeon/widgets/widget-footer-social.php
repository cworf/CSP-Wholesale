<?php
class Tesla_socials_footer_widget extends WP_Widget {

    function __construct() {
        parent::__construct(
                'tesla_socials_footer_widget',
                '['.THEME_PRETTY_NAME.'] Social',
                array(
            'description' => __('Adds social icons with text in footer widgetized area.', 'zeon'),
            'classname' => 'tesla_socials_footer_widget',
                )
        );
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        $facebook = isset($instance['facebook']) ? esc_attr($instance['facebook']) : '';
        $twitter = isset($instance['twitter']) ? esc_attr($instance['twitter']) : '';
        $youtube = isset($instance['youtube']) ? esc_attr($instance['youtube']) : '';
        $google = isset($instance['google']) ? esc_attr($instance['google']) : '';

        echo $before_widget;
        if (!empty($title))
            echo $before_title . $title . $after_title;?>

            <ul class="socials">
                <?php if($facebook) : ?>
                    <li><a href="<?php echo $facebook ?>"><img src="<?php echo IMAGES ?>/elements/socials/facebook.png" alt="facebook"/><?php _ex('Facebook','widget','zeon') ?></a></li>
                <?php endif; ?>
                <?php if($twitter) : ?>
                    <li><a href="<?php echo $twitter ?>"><img src="<?php echo IMAGES ?>/elements/socials/twitter.png" alt="twitter"/><?php _ex('Twitter','widget','zeon') ?></a></li>
                <?php endif; ?>
                <?php if($youtube) : ?>
                    <li><a href="<?php echo $youtube ?>"><img src="<?php echo IMAGES ?>/elements/socials/youtube.png" alt="youtube"/><?php _ex('Youtube','widget','zeon') ?></a></li>
                <?php endif; ?>
                <?php if($google) : ?>
                    <li><a href="<?php echo $google ?>"><img src="<?php echo IMAGES ?>/elements/socials/googleplus.png" alt="google"/><?php _ex('Google+','widget','zeon') ?></a></li>
                <?php endif; ?>
            </ul>

        <?php echo $after_widget;
    }

    function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['facebook'] = strip_tags($new_instance['facebook']);
        $instance['twitter'] = strip_tags($new_instance['twitter']);
        $instance['youtube'] = strip_tags($new_instance['youtube']);
        $instance['google'] = strip_tags($new_instance['google']);

        return $instance;
    }

    function form($instance) {
        $instance = wp_parse_args((array) $instance, array('title' => ''));
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        $facebook = isset($instance['facebook']) ? esc_attr($instance['facebook']) : '';
        $twitter = isset($instance['twitter']) ? esc_attr($instance['twitter']) : '';
        $youtube = isset($instance['youtube']) ? esc_attr($instance['youtube']) : '';
        $google = isset($instance['google']) ? esc_attr($instance['google']) : '';
        ?>
        <p>
            <label><?php _ex('Title:','dashboard','zeon'); ?><input class="widefat" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label> 
            <label><?php _ex('Facebook:','dashboard','zeon'); ?><input class="widefat" name="<?php echo $this->get_field_name('facebook'); ?>" type="text" value="<?php echo esc_attr($facebook); ?>" /></label> 
            <label><?php _ex('Twitter:','dashboard','zeon'); ?><input class="widefat" name="<?php echo $this->get_field_name('twitter'); ?>" type="text" value="<?php echo esc_attr($twitter); ?>" /></label> 
            <label><?php _ex('Youtube:','dashboard','zeon'); ?><input class="widefat" name="<?php echo $this->get_field_name('youtube'); ?>" type="text" value="<?php echo esc_attr($youtube); ?>" /></label> 
            <label><?php _ex('Google Plus:','dashboard','zeon'); ?><input class="widefat" name="<?php echo $this->get_field_name('google'); ?>" type="text" value="<?php echo esc_attr($google); ?>" /></label> 
        </p>
        <?php
    }
}

add_action('widgets_init', create_function('', 'return register_widget("Tesla_socials_footer_widget");'));