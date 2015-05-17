<?php foreach($input['values'] as $key => $value) : ?>
<label class="tt_checkbox tt_checkbox_<?php if(!empty($input['list'])) echo 'list'; else echo 'grid'?> <?php if (!empty($input['class']))echo esc_attr($input['class'])?>">
  <input
      class="<?php if (!empty($input['class']))echo esc_attr($input['class']);if (!empty($input['action']))echo " tt_interact";?>"
      type="<?php echo esc_attr($input['type']);?>"
      name="<?php echo THEME_OPTIONS?>[<?php echo esc_attr($input_id);?>]"
      <?php if( !empty( $options[ $input_id ] ) ) 
            checked( $value , $options[ $input_id ]);?>
      value ="<?php echo esc_attr($value);?>"
      <?php if (!empty($input['action'])) :?>
        data-tt-interact-objs='<?php echo json_encode($input['action'][1]) ?>'
        data-tt-interact-action="<?php echo esc_attr($input['action'][0]) ?>"
      <?php endif;?>
  ><span><?php print $value;?></span>
</label>
<?php endforeach;?>