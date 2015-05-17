<ul class="tt_share_platform">
<?php if (!empty($input['platforms']))
  foreach ( $input['platforms'] as $platform ) : ?>
    <li>
      <input
        class="tt_social_<?php echo $platform?> <?php if (!empty($options[$input_id . '_' . $platform])) echo 'social_active'?>"
        type="text"
        value="<?php if (!empty($options[$input_id . '_' . $platform])) echo $options[$input_id . '_' . $platform]?>"
        title="<?php echo $platform?> Page"
        name="<?php echo THEME_OPTIONS?>[<?php echo $input_id . "_" . $platform?>]"
        placeholder="<?php echo ucfirst($platform)?>">
    </li>
  <?php endforeach;?>
</ul>