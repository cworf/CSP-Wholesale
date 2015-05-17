<div class="wrap">
    <h2><?php _e('UPME - Update User Search Cache','upme');?></h2>
    
    <h3><?php _e('Update User Search Cache','upme'); ?></h3>

    <p><?php _e('In order to keep your search working smoothly you can update user cache from here. However it will be updated once user profile is updated.','upme'); ?></p>
    <?php 
        $users = array();
        $users = get_users(array('fields'=>'ID'));
    ?>
    
    <p>
    <?php 
        echo sprintf(__('You have total <span id="upme-total-user" style="font-weight: bold;">%s</span> users in your website.', 'upme'), count($users));
    ?>
    </p>
    
    <p>
    <?php 
        _e('<p id="upme-processing-tag" style="display:none;">Processing.... <span id="upme-completed-users" style="display:none;"> users Completed</span> </p>','upme');
    ?>
    </p>
    <p id="upme-upgrade-success" style="display:none;">
    <span style="color: green; font-weight: bold;"><?php _e('User Search Cache Updated.')?></span>
    </p>

    <?php 
        echo UPME_Html::button('button', 
                    array(
                        'name' => 'reset-options-fields',
                        'id' => 'upme-update-user-cache',
                        'value' => __('Update User Cache', 'upme'),
                        'class' => 'button button-primary'
                    )
                );
    ?>
    
</div>
