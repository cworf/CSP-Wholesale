<?php 
  $value = tt_get_value($input_nr,$input_id,$inputs_count);
 ?>
<input 
    type="text" 
    name="<?php echo THEME_OPTIONS?>[<?php echo $input_id;?>]<?php echo $repeater ?>" 
    id="<?php echo $input_id;?>"
    value="<?php if ( $value ) echo $value ; ?>"
/>
<button class="upload_image_button tt_button">
<?php
    if ( !empty( $input ['title'] ) )
      printf( __( '%s', THEME_NAME ), $input ['title'] );
    else
      _e('Upload Image', THEME_NAME);?>
</button>
<button class='tt_button remove_img'><?php _e('Remove Image',THEME_NAME)?></button>
<?php if(empty($input['no_preview'])) : ?>
  <div class="tt_option_title"><span><?php _e("Preview",THEME_NAME)?></span></div> 
  <div class="tt_show_logo">
    <img
        class="img_preview"
        src="<?php if ( $value )
                     echo  $value ;
                   else
                     echo TT_FW . "/static/images/tesla_logo.png" ?>"
    />
  </div>
<?php endif; ?>
<?php if (!empty($input['custom_width'])) : ?>
        <!-- IMAGE WIDTH RESIZE CODE HERE -->
<?php endif;?>