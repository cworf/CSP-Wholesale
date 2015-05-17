<?php 
  $value = tt_get_value($input_nr,$input_id,$inputs_count);
 ?>
<label class="tt_checkbox tt_checkbox_<?php if(!empty($input['list'])) echo 'list'; else echo 'grid'?> <?php if (!empty($input['class']))echo esc_attr($input['class'])?>">
    <input
        class="<?php if (!empty($input['action']))echo "tt_interact";?>"
        id="<?php echo esc_attr($input_id)?>"
        type="checkbox"
        name="<?php echo THEME_OPTIONS?>[<?php echo esc_attr($input_id);?>]<?php echo esc_attr($repeater) ?>"
        <?php if( !empty( $value ) ) 
              checked( $input_id, $value );?>
        value="<?php echo esc_attr($input_id); ?>"
        <?php if (!empty($input['action'])) :?>
          data-tt-interact-objs='<?php echo json_encode($input['action'][1]) ?>'
          data-tt-interact-action="<?php echo esc_attr($input['action'][0]) ?>"
        <?php endif;?>
    >
    <span><?php if (!empty($input['label'])) print $input['label'];?></span>
</label>