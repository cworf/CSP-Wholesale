<?php if(empty($input['hide_input'])) : ?>
<input
    class="<?php if (!empty($input['class']))echo esc_attr($input['class']);?>"
    id="<?php echo esc_attr($input_id)?>"
    type="<?php echo esc_attr($input['type']);?>"
    name="<?php echo THEME_OPTIONS?>[<?php echo esc_attr($input_id);?>]<?php echo esc_attr($repeater) ?>"
    placeholder="<?php if (!empty($input['placeholder']))echo esc_attr($input['placeholder']);?>"
    value="<?php echo esc_attr(tt_get_value($input_nr,$input_id,$inputs_count)); ?>"
>
<?php endif; ?>
<?php if (!empty($input['color_changer'])) : //---text color changer--------?> 
    <input
        type="text" 
        value="<?php if ( ! empty( $options[ $input_id . "_color"]  ) ) echo esc_attr($options[ $input_id . "_color"]); ?>"
        class="text_color"
        data-default-color="<?php if (!empty($input['default']))  $input['default'];?>" 
        name="<?php echo THEME_OPTIONS?>[<?php echo esc_attr($input_id);?>_color]"
    />
<?php endif?>
<?php if (!empty($input['font_changer'])) : //---text font changer----------?>
    <select class="font_changer font_search" id="<?php echo esc_attr($input_id)?>_font" name="<?php echo THEME_OPTIONS?>[<?php echo esc_attr($input_id)?>_font]">
    <option></option>
    <?php 
    $fonts = get_google_fonts();
    if ( !empty($fonts) ) :?>
        <?php foreach ( $fonts->items as $font ) : ?>
            <option value='<?php echo esc_attr($font->family) ?>'<?php if ( $font->family == $options[ $input_id . "_font" ]  ) echo ' selected="selected"'  ?>><?php echo esc_attr($font->family) ?></option>
        <?php endforeach; ?>
    <?php endif;?>
    </select>
<?php endif;?>
<?php if (!empty($input['font_size_changer'])) : //---text size changer-----?>
    <input
      type="number"
      min="<?php echo esc_attr($input['font_size_changer'][0]);?>"
      max="<?php echo esc_attr($input['font_size_changer'][1])?>"
      value="<?php if (!empty($options[ $input_id . "_size" ])) echo esc_attr($options[ $input_id . "_size" ])?>"
      class="font_size_changer"
      id="<?php echo esc_attr($input_id)?>_size"
      name="<?php echo THEME_OPTIONS?>[<?php echo esc_attr($input_id)?>_size]"
      data-size-unit ="<?php echo esc_attr($input['font_size_changer'][2]);?>"
      ><span class="units"><?php if (!empty($input['font_size_changer'][2])) print $input['font_size_changer'][2] ?></span>
<?php endif;?>
<?php if (!empty($input['font_preview'][0])) : //---Font Preview---------------?>
    <div class="tt_option_title mt30"><span><?php _e('Text Logo Preview',THEME_NAME)?></span></div>
    <div
        class='tt_show_logo font_preview <?php if (!empty($input['font_preview'][1]))echo 'change_font_size'?>'
        style="color:<?php if ( ! empty( $options[ $input_id . "_color"]  ) ) echo esc_attr($options[ $input_id . "_color"]) ; ?>;
                font-family:<?php if ( ! empty( $options[ $input_id . "_font"]  ) ) echo esc_attr($options[ $input_id . "_font"]); ?>;
                font-size:<?php if ( ! empty( $options[ $input_id . "_size"]  ) && !empty($input['font_preview'][1])) echo esc_attr($options[ $input_id . "_size"] . $input['font_size_changer'][2]); ?>"
    ><?php if ( ! empty( $options[ $input_id ] ) ) print $options[ $input_id ]; ?></div>
<?php endif;?>