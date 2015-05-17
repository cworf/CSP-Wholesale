<?php
/*
 * Admin Panel markup Tesla Framework
*/
$options = get_option( THEME_OPTIONS );
if ( ! isset( $_REQUEST[ 'settings-updated' ] ) )
  $_REQUEST[ 'settings-updated' ] = false;
else if ($_REQUEST[ 'settings-updated' ] == true)
  echo "<script type='text/javascript'>var updated = true</script>";
if ( !class_exists( 'TT_Security' ) ){
  exit();
}
?>
<div class="tt_admin">
<form method="post" action="save_options">
  <?php wp_nonce_field( -1, 'tesla-options-nonce'); ?>
  <?php settings_fields( THEME_OPTIONS );?>
  <div class="tt_top_bar">
    <div class="tt_top_links">
      <span>
        <a class="tt_documentation" target="_blank" href="http://teslathemes.com/documentation/"><?php _e("Documentation","TeslaFramework")?></a>
      </span>
      <span>
        <a class="tt_support" target="_blank" href="http://teslathemes.com/forums/"><?php _e("Support","TeslaFramework")?></a>
      </span>
      <span>
        <a class="tt_news" target="_blank" href="http://teslathemes.com/blog/"><?php _e("Latest News","TeslaFramework")?></a>
      </span>
    </div>
    <div class="tt_theme_logo">
      <span><?php echo THEME_PRETTY_NAME ?></span><?php _e(" Version " . THEME_VERSION,"TeslaFramework");?> | 
      <span><?php _e("Framework ","TeslaFramework")?></span><?php _e("Version " . TT_FW_VERSION,"TeslaFramework")?>
      <a class="check_update" title="Manual check for theme updates" href="#check_updates">Check Updates</a>
      <span class="check_update_result"></span>
      <a class="changelog thickbox" title="View Theme Changelog" href="http://teslathemes.com/auto_update/?theme=<?php echo THEME_NAME ?>&changelog=true&TB_iframe=true&width=1024&height=800">View Changelog</a>
    </div>
  </div>
  <div class="tt_sidebar">
    <div class="tt_logo"><a href="http://www.teslathemes.com" target="_blank"></a></div>
    <ul class="tt_left_menu" id="myTab">
    <?php foreach ( $tabs as $key => $tab ) : ?>
      <li
        class="<?php if ( $key == 0 )
                      echo "first active";
                     elseif ($key == count($tabs)-1)
                      echo "last"; ?>"
      ><a
            class="<?php if (!empty($tab['icon'])) echo "menu_" . $tab['icon']?>"
            href="#<?php echo str_replace(' ','_',$tab[ 'title' ]) ;?>"
            data-toggle="tab"
          ><?php printf( __( '%s', THEME_NAME ), $tab[ 'title' ] ) ?></a>
      </li>
    <?php endforeach; ?>
    </ul>
  </div>
  <div class="tt_content">
    <?php $j = 0 ;?>
<!-- ========================================= TABS START ========================================================== -->
    <?php foreach ( $tabs as $key => $tab ) : ?>
      <div
        class="tt_tab<?php if ( $key == 0 )
                            echo " active";?>"
        id="<?php echo str_replace(' ','_',$tab[ 'title' ]) ; ?>"
      >
      <?php if (!empty($tab['type']) && $tab['type'] == 'iframe') : //if tab type is iframe show iframe?>
          <iframe width="100%" height="700px" src="<?php echo $tab['link'] ?>"></iframe> 
        <?php  continue;
      endif; ?>
      <!-- <a class="tt_teslathemes" target="_blank" href="http://teslathemes.com/?spb"></a> -->
      <?php
        if ( ! empty( $tab[ 'boxes' ] ) ) :  //======================BOXES START========================================
          foreach ( $tab[ 'boxes' ] as $box_name => $box ) :?>
          
          <?php if (count($tab[ 'boxes' ]) > 1):?>
            <div class="tt_box<?php if (!empty($box['size'])) echo " tt_box_" . $box['size'];?>">
          <?php endif;?>
              <div class="tt_content_box<?php if (!empty($box['class'])) echo " " . $box['class'];?>">
                <div class="tt_content_box_title">
                  <span class="tt_bg_icon tt_<?php if(!empty($box['icon'])) echo $box['icon'];?>"><?php printf( __( '%s', THEME_NAME ), $box_name ); ?></span>
                </div>
                <div class="tt_content_box_content">
                  <?php if (!empty($box['description']))
                    echo "<p>{$box['description']}</p>";
                  $repeater = ( !empty($box['repeater']) ) ? "[]" : "";  //repeater variable for names that will have to repeat
                  $inputs_count = count($box['input_fields']);
                  if ( !empty($box['repeater']) ) { //-----------if repeater start-------------------------------------
                    
                      $repeated_inputs = $box['input_fields'];
                      $first_repeated_key = key($repeated_inputs);
                    if(!empty($options[$repeated_inputs[$first_repeated_key]['id']])){
                      $first_repeated_input = $options[$repeated_inputs[$first_repeated_key]['id']];
                      $repeated_times = count($first_repeated_input);
                      $box['input_fields'] = array();

                      foreach ($first_repeated_input as $repeat_nr => $value) {
                        foreach ($repeated_inputs as $repeated_key => $repeated_input) {
                          $box['input_fields'][$repeated_key . ' ' . $repeat_nr] = $repeated_input;
                        }
                      }
                    }

                  }//---------------------------------------------repeater end if ---------------------------------------
                  $input_nr = 0;
                  foreach($box['input_fields'] as $input_field_name => $input) : //======INPUT FIELDS START=============
                    if($input_nr % $inputs_count == 0 && !empty($box['repeater']))
                      echo "<section class='options_block'>";
                    $input_id = $input['id'];
                    if (count($box['input_fields']) > 1 && !empty($box['columns'] ) ):?>
                      <div class="tt_box tt_box_<?php if (isset($input['size'])) echo $input['size'];?>">
                    <?php endif;
                    if (!empty($input_field_name) && !ctype_digit(str_replace(' ','',$input_field_name))):?>
                        <div class="tt_option_title">
                          <span class="tt_the_title"><?php printf( __( '%s', THEME_NAME ), $input_field_name );?></span>
                          <?php if ( !empty($box['repeater']) ) : ?>
                            
                          <?php endif; ?>
                        </div>
                      <?php endif;
                      if (!empty($input['hidden'])) //adding class for hidden elements
                        $input['class'] = (!empty($input['class'])) ? $input['class'] . " hidden" : "hidden";
                      //==========================START INPUT TYPES=======================================
                      require(TT_FW_DIR . '/views/options/' . $input['type'] . '.php');
                      //==========================END INPUT TYPES=========================?>
                      <?php //===================================LABELS===================================
                      if (!empty($input['note'])) : ?>
                          <p class="tt_explain<?php if (!empty($input['hidden'])) echo " hidden"?>"><?php escape_htmle(($input['note'])); ?></p>
                      <?php endif;?>
                      <?php if (count($box['input_fields']) > 1 && !( empty($box['columns'] ) ) ):?>
                        </div>
                      <?php endif;
                    $input_nr ++;
                    if($input_nr % $inputs_count == 0 && !empty($box['repeater'])): ?>
                      <div class="clear"></div>
                      <div class="remove_container">
                        <a class="remove_option">&#10006;<b><?php _e('Remove','TeslaFramework') ?></b></a>
                      </div>
                    </section>
                    <?php endif;

                    endforeach;//==================================INPUT FIELDS END================================================?>

                    <?php if ( !empty($box['repeater']) ) : //Remove repeater button------?>
                      
                    <?php endif; ?>
                  <div class="clear"></div>
                </div>
                <div class="tt_content_box_bottom">
                  <?php if (!empty($box['repeater'])) : ?>
                    <div class='repeater'>
                      <a class="repeat_box">&#43;<b>&nbsp;<?php echo $box['repeater']?></b></a>
                    </div>
                  <?php endif; ?>
                  <input class="tt_submit" type="submit" value="<?php _e('Save Changes','TeslaFramework')?>">
                    <div class="tt_bottom_note">
                      <?php printf( __( '%s', THEME_NAME ), $option_saved_text ); ?>
                    </div>
                </div>
              </div>
            <?php if (count($tab[ 'boxes' ]) > 1):?>
              </div>
            <?php endif;?>
        <?php endforeach;?>
        <?php endif;//================================================BOXES END=========================================?>
      </div>
    <?php endforeach;?>
<!-- =========================================== TABS END ========================================================== -->
    </div>
  </form>
  <div class="mailchimp_modal">
    <form>
      <input type="button" class="tt_button close" value="Close">
      <input type="button" class="tt_button choose" value="Choose">
    </form>
  </div>
</div>