<input
    class="datepicker"
    type="text"
    name="<?php echo THEME_OPTIONS?>[<?php echo $input_id;?>]<?php echo $repeater ?>"
    value="<?php echo tt_get_value($input_nr,$input_id,$inputs_count); ?>"
>
<?php if (!empty($input['date_range'])) : //---Date Range-------------------?> 
    <input
        class="datepicker"
        type="text"
        name="<?php echo THEME_OPTIONS?>[<?php echo $input_id . '-to';?>]<?php echo $repeater ?>"
        value="<?php echo tt_get_value($input_nr,$input_id . '-to',$inputs_count); ?>"
    />
<?php endif?>