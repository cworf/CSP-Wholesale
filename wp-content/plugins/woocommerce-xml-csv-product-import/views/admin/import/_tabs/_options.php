<div class="panel woocommerce_options_panel" id="add_on_options" style="display:none;">
	<div class="woo-add-on-free-edition-notice upgrade_template">
		<a href="http://www.wpallimport.com/upgrade-to-pro/?utm_source=free-plugin&utm_medium=in-plugin&utm_campaign=woocommerce" target="_blank" class="upgrade_woo_link"><?php _e('Upgrade to the professional edition of WP All Import and the WooCommerce add-on to import to Variable, Affiliate, and Grouped products.', 'pmxi_plugin');?></a>
	</div>	
	<div class="options_group">
		
		<p class="form-field"><?php _e('Import options','pmxi_plugin');?></p>
		
		<p class="form-field wpallimport-radio-field">
			<input type="hidden" name="missing_records_stock_status" value="0" />
			<input type="checkbox" id="missing_records_stock_status" name="missing_records_stock_status" value="1" <?php echo $post['missing_records_stock_status'] ? 'checked="checked"' : '' ?> />
			<label for="missing_records_stock_status"><?php _e('Set out of stock status for missing records', 'pmxi_plugin') ?></label>
			<a href="#help" class="wpallimport-help" title="<?php _e('Option to set the stock status to out of stock instead of deleting the product entirely. This option doesn\'t work when \'Delete missing records\' option is enabled.', 'pmxi_plugin') ?>" style="position:relative; top:-2px;">?</a>
		</p>
		<p class="form-field wpallimport-radio-field">
			<input type="hidden" name="disable_auto_sku_generation" value="0" />
			<input type="checkbox" id="disable_auto_sku_generation" name="disable_auto_sku_generation" value="1" <?php echo $post['disable_auto_sku_generation'] ? 'checked="checked"' : '' ?> />
			<label for="disable_auto_sku_generation"><?php _e('Disable auto SKU generation', 'pmxi_plugin') ?></label>
			<a href="#help" class="wpallimport-help" title="<?php _e('Plugin will NOT automaticaly generate the SKU for each product based on md5 algorithm, if SKU option is empty.', 'pmxi_plugin') ?>" style="position:relative; top:-2px;">?</a>
		</p>
		<p class="form-field wpallimport-radio-field">
			<input type="hidden" name="disable_sku_matching" value="0" />
			<input type="checkbox" id="disable_sku_matching" name="disable_sku_matching" value="1" <?php echo $post['disable_sku_matching'] ? 'checked="checked"' : '' ?> />
			<label for="disable_sku_matching"><?php _e('Don\'t check for duplicate SKUs', 'pmxi_plugin') ?></label>
			<a href="#help" class="wpallimport-help" title="<?php _e('Each product should have a unique SKU. If this box is checked, WP All Import won\'t check for duplicate SKUs, which speeds up the import process. Make sure the SKU for each of your products is unique. If this box is unchecked, WP All Import will import products with duplicate SKUs with a blank SKU.', 'pmxi_plugin') ?>" style="position:relative; top:-2px;">?</a>
		</p>							
		
	</div>	
</div>