<div id="<?php echo $input_id;?>" class="tt_map_container">
  <input
    class="map_search"
    type="text">
  <div
    class="tt_map map-canvas<?php if ( ! empty( $input['class' ] ) ) echo $input['class'] ; ?>"
    name="<?php echo THEME_OPTIONS?>[<?php echo $input_id;?>]"
  ></div>
  <input
    class="map-coords"
    type="hidden"
    name="<?php echo THEME_OPTIONS?>[<?php echo $input_id;?>_coords]"
    value="<?php if ( ! empty( $options[ $input_id . "_coords" ] ) ) echo $options[ $input_id . "_coords" ] ;?>">
  <input
    class="marker-coords"
    type="hidden"
    name="<?php echo THEME_OPTIONS?>[<?php echo $input_id;?>_marker_coords]"
    value="<?php if ( ! empty( $options[ $input_id . "_marker_coords" ] ) ) echo $options[ $input_id . "_marker_coords" ] ;?>">
  <input
    class="map-zoom"
    type="hidden"
    name="<?php echo THEME_OPTIONS?>[<?php echo $input_id;?>_zoom]"
    value="<?php if ( ! empty( $options[ $input_id . "_zoom" ] ) ) echo $options[ $input_id . "_zoom" ] ;?>">
  <?php if (!empty($input['icons']))
    foreach ($input['icons'] as $icon) : ?>
      <label class="map-icon">
        <input
          type="radio"
          name="<?php echo THEME_OPTIONS?>[<?php echo $input_id;?>_icon]"
          value="<?php echo TT_FW . '/static/images/mapicons/' . $icon?>"
          <?php if(!empty($options[ $input_id . '_icon']))checked( TT_FW . '/static/images/mapicons/' . $icon , $options[ $input_id . '_icon']); ?>
          ><img src="<?php echo TT_FW . '/static/images/mapicons/' . $icon?>" alt="map icon" /></label>
    <?php endforeach;?>
</div>