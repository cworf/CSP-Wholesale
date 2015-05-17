<button
    id="<?php echo $input_id;?>"
    class="tt_button <?php if ( ! empty( $input['class' ] ) ) echo $input['class'] ; ?>"
    name="<?php echo THEME_OPTIONS?>[<?php echo $input_id;?>]"
><?php if ( ! empty(  $input [ 'value' ] ) ) printf(__( '%s', THEME_NAME ),$input['value']); ; ?></button>