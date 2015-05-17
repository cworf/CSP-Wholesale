<ul class="tt_share_platform">
<?php if (!empty($input['platforms']))
  foreach ( $input['platforms'] as $platform ) : ?>
    <li>
      <input
        class="tt_social_<?php echo esc_attr($platform)?> <?php if (!empty($options[$input_id . '_' . $platform])) echo 'social_active'?>"
        type="text"
        value="<?php if (!empty($options[$input_id . '_' . $platform])) echo esc_attr($options[$input_id . '_' . $platform])?>"
        title="<?php echo esc_attr($platform)?> Page"
        name="<?php echo THEME_OPTIONS?>[<?php echo esc_attr($input_id . "_" . $platform)?>]"
        placeholder="<?php echo ucfirst($platform)?>">
    </li>
  <?php endforeach;?>
</ul>