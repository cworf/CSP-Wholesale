<?php
class Tesla_subscription_widget extends WP_Widget {

    function __construct() {
        parent::__construct(
                'tesla_subscription_widget',
                '['.THEME_PRETTY_NAME.'] Subscription',
                array(
            'description' => __('Adds subscription form in footer widgetized area.', 'zeon'),
            'classname' => 'tesla_subscription_widget',
                )
        );
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        $placeholder_name = isset($instance['placeholder_name']) ? esc_attr($instance['placeholder_name']) : '';
        $placeholder_email = isset($instance['placeholder_email']) ? esc_attr($instance['placeholder_email']) : '';
        $button = isset($instance['button']) ? esc_attr($instance['button']) : '';
        echo $before_widget;?>
        <div class="subscription">
            <?php if (!empty($title)): ?>
                <div class="subscription-title"><?php echo $title ?></div>
            <?php endif; ?>
            <form class="subscription" id="newsletter" method="post" data-tt-subscription>
                <input type="text" name="newsletter-name" placeholder="<?php echo $placeholder_name ?>" class="subscription-line" data-tt-subscription-type="name">
                <input type="text" name="newsletter-email" placeholder="<?php echo $placeholder_email ?>" class="subscription-line" data-tt-subscription-required data-tt-subscription-type="email">
                <input type="submit" value="<?php echo $button ?>" class="button-5">
            </form>
            <p id="result_container"></p>
        </div>
        <?php echo $after_widget;
    }

    function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['placeholder_name'] = strip_tags($new_instance['placeholder_name']);
        $instance['placeholder_email'] = strip_tags($new_instance['placeholder_email']);
        $instance['button'] = strip_tags($new_instance['button']);

        return $instance;
    }

    function form($instance) {
        $instance = wp_parse_args((array) $instance, array('title' => ''));
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        $placeholder_name = isset($instance['placeholder_name']) ? esc_attr($instance['placeholder_name']) : '';
        $placeholder_email = isset($instance['placeholder_email']) ? esc_attr($instance['placeholder_email']) : '';
        $button = isset($instance['button']) ? esc_attr($instance['button']) : '';
        ?>
        <p>
            <label><?php _ex('Title:','dashboard','zeon'); ?><input class="widefat" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label> 
            <label><?php _ex('Placeholder name:','dashboard','zeon'); ?><input class="widefat" name="<?php echo $this->get_field_name('placeholder_name'); ?>" type="text" value="<?php echo esc_attr($placeholder_name); ?>" /></label> 
            <label><?php _ex('Placeholder Email:','dashboard','zeon'); ?><input class="widefat" name="<?php echo $this->get_field_name('placeholder_email'); ?>" type="text" value="<?php echo esc_attr($placeholder_email); ?>" /></label> 
            <label><?php _ex('Button Text:','dashboard','zeon'); ?><input class="widefat" name="<?php echo $this->get_field_name('button'); ?>" type="text" value="<?php echo esc_attr($button); ?>" /></label> 
        </p>
        <?php
    }
}

add_action('widgets_init', create_function('', 'return register_widget("Tesla_subscription_widget");'));