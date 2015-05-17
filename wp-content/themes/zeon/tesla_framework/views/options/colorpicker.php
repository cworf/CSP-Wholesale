<input
    id="<?php echo esc_attr($input_id)?>"
    type="text" 
    value="<?php echo tt_get_value($input_nr,$input_id,$inputs_count); ?>"
    class="my-color-field<?php if (!empty($input['class']))echo " " . $input['class'];?>"
    data-default-color="<?php if (!empty($input['default'])) echo esc_attr($input['default']);?>" 
    name="<?php echo THEME_OPTIONS?>[<?php echo esc_attr($input_id);?>]<?php echo esc_attr($repeater) ?>"
/>