<textarea
  id="<?php echo esc_attr($input_id)?>"
  name="<?php echo THEME_OPTIONS?>[<?php echo esc_attr($input_id);?>]<?php echo esc_attr($repeater) ?>"
  class=<?php if ( ! empty( $input[ 'class' ] ) ) echo esc_attr($input['class']) ; ?>
  rows="<?php if ( ! empty( $input[ 'rows' ] ) ) echo esc_attr($input['rows']) ;else echo '5' ;?>"
  cols="<?php if ( ! empty( $input[ 'cols' ] ) ) echo esc_attr($input['cols']) ;else echo '10' ;?>"
><?php if ( ! empty( $options[ $input_id ] ) )
        echo tt_get_value($input_nr,$input_id,$inputs_count);
     elseif (! empty( $input['default_value'] ) )
        echo ( $input['default_value'] );
?></textarea>