<?php if (empty($options[ $input_id ]))
  $options[ $input_id ] = NULL;
  $value = tt_get_value($input_nr,$input_id,$inputs_count);
 ?>
  <select
    id="<?php echo $input_id?>"
    name="<?php echo THEME_OPTIONS?>[<?php echo $input_id?>]<?php echo $repeater ?><?php if (!empty($input['multiple']))echo '[]';?>"
    class="<?php if (!empty($input['class']))echo $input['class']; if (!empty($input['multiple'])) echo ' multiple_select';?>"
    <?php if (!empty($input['multiple']))echo ' multiple';?>
    >
    <?php 
      if($input['mailchimp'] ){
        if(_go('mailchimp_api_key')){
          if(!_go('mailchimp_lists'))
            $mailchimp_lists = TT_Subscription::get_mailchimp_lists();
          else
            $mailchimp_lists = $input['options'];
          if(!empty($mailchimp_lists)){
            $input['options'] = array();
            foreach ($mailchimp_lists as $key => $list) {
              $input['options'][$list->name] = $list->id;
            }
          }
        }
      }
     ?>
    <?php if (!empty($input['range']) && $input['range_type'] == 'digit' ) : ?>
        <?php for ( $i = $input['range'][0]; $i <= $input['range'][1]; $i ++  ) : ?>
            <option
                value="<?php echo $i ?>"<?php if (!empty($input['multiple']))
                                                foreach($options[ $input_id ] as $val)
                                                  selected($i,$val);
                                              else
                                                selected($i,$value);?>
             ><?php echo $i ?></option>
        <?php endfor; ?>
    <?php else:?>
        <?php foreach ( $input['options'] as $name=>$val ) : ?>
            <option
                value="<?php echo $val ?>"<?php if (!empty($input['multiple'])){
                                                    if (!empty($options[ $input_id ]))
                                                      foreach($options[ $input_id ] as $option)
                                                        if ( $val == $option )
                                                          echo ' selected="selected"';
                                                  }elseif ( $val == $value )
                                                    echo ' selected="selected"' ?>
            ><?php echo $name ?></option>
        <?php endforeach; ?>
    <?php endif;?>
  </select>