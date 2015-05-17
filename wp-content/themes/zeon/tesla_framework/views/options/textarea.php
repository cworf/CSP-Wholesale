<textarea
  id="<?php echo $input_id?>"
  name="<?php echo THEME_OPTIONS?>[<?php echo $input_id;?>]<?php echo $repeater ?>"
  class=<?php if ( ! empty( $input[ 'class' ] ) ) echo $input['class'] ; ?>
  rows="<?php if ( ! empty( $input[ 'rows' ] ) ) echo $input['rows'] ;else echo '5' ;?>"
  cols="<?php if ( ! empty( $input[ 'cols' ] ) ) echo $input['cols'] ;else echo '10' ;?>"
><?php if ( ! empty( $options[ $input_id ] ) )
        echo tt_get_value($input_nr,$input_id,$inputs_count);
     elseif (! empty( $input['default_value'] ) )
        echo ( $input['default_value'] );
?></textarea>