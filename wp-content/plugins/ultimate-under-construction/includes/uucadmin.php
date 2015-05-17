<?php 

function uuc_options_page() {

	global $uuc_options;
	global $wp_version;

	ob_start(); ?>
	<div class="wrap">
		<div id="icon-tools" class="icon32"></div><h2>Ultimate Under Construction Plugin Options</h2>
		
		<form method="post" action="options.php">

			<?php 
			//Current version of WP seems to fall over on unticked Checkboxes... This is to tidy it up and stop unwanted 'Notices'
			//Enable Checkbox Sanitization
			if ( ! isset( $uuc_options['enable'] ) || $uuc_options['enable'] != '1' )
			  $uuc_options['enable'] = 0;
			else
			  $uuc_options['enable'] = 1;

			//Countdown Checkbox Sanitization
			if ( ! isset( $uuc_options['cdenable'] ) || $uuc_options['cdenable'] != '1' )
			  $uuc_options['cdenable'] = 0;
			else
			  $uuc_options['cdenable'] = 1;

			settings_fields('uuc_settings_group'); ?>

			<h4 class="uuc-title"><?php _e('Enable', 'uuc_domain'); ?></h4>
			<p>				
				<input id="uuc_settings[enable]" name="uuc_settings[enable]" type="checkbox" value="1" <?php checked($uuc_options['enable'], '1'); ?>/>
				<label class="description" for="uuc_settings[enable]"><?php _e('Enable the Under Construction Page','uuc_domain'); ?></label>

			<h4 class="uuc-title"><?php _e('Holding Page Type', 'uuc_domain'); ?></h4>
			<p>
				<label><input onclick="checkPage()" type="radio" name="uuc_settings[holdingpage_type]" id="htmlblock" value="htmlblock"<?php if(!isset($uuc_options['holdingpage_type'])){ ?> checked <?php } else { checked( 'htmlblock' == $uuc_options['holdingpage_type'] ); } ?> /> HTML Block</label><br />
				<label><input onclick="checkPage()" type="radio" name="uuc_settings[holdingpage_type]" id="custom" value="custom"<?php checked( 'custom' == $uuc_options['holdingpage_type'] ); ?> /> Custom Build</label><br />
			</p>

			<div id="htmlblockbg" <?php if ($uuc_options['holdingpage_type'] == "custom"){ ?> style="visibiliy: hidden; display: none;"<?php }; ?>>
				<h4 class="uuc-title"><?php _e('HTML Block', 'uuc_domain'); ?></h4>
				<p>
					<textarea class="theEditor" name="uuc_settings[html_block]" id="uuc_settings[html_block]" rows="10" cols="75"><?php if (isset($uuc_options['html_block'])) echo $uuc_options['html_block']; ?></textarea>
					<label class="description" for="uuc_settings[html_block]"><?php _e('<br />Enter the HTML - Advised for advanced users only!<br />Will display exactly as entered.', 'uuc_domain'); ?></label>
				</p>
			</div>

			<div id="custombg" <?php if ($uuc_options['holdingpage_type'] == "htmlblock"){ ?> style="visibility: hidden; display: none;"<?php }; ?>>
				<h4 class="uuc-title"><?php _e('Website Title', 'uuc_domain'); ?></h4>
				<p>
					<input id="uuc_settings[website_name]" name="uuc_settings[website_name]" type="text" value="<?php echo $uuc_options['website_name']; ?>"/> 
					<label class="description" for="uuc_settings[website_name]"><?php _e('Enter the Title of your website', 'uuc_domain'); ?></label>
				</p>

				<h4 class="uuc-title"><?php _e('Holding Message', 'uuc_domain'); ?></h4>
				<p>
					<textarea id="uuc_settings[holding_message]" name="uuc_settings[holding_message]" rows="5" cols="50"><?php echo $uuc_options['holding_message'] ?></textarea>
					<label class="description" for="uuc_settings[holding_message]"><?php _e('Enter a message to appear below the Website Title', 'uuc_domain'); ?></label>
				</p>

				<h4 class="uuc-title"><?php _e('Countdown Timer', 'uuc_domain'); ?></h4>
				<p>
					<input id="uuc_settings[cdenable]" name="uuc_settings[cdenable]" type="checkbox" value="1" <?php checked($uuc_options['cdenable'], '1'); ?>/>
					<label class="description" for="uuc_settings[cdenable]"><?php _e('Enable the Countdown Timer?','uuc_domain'); ?></label>
					<br />
					<br />
					<label><input type="radio" name="uuc_settings[cd_style]" id="flipclock" value="flipclock"<?php if(!isset($uuc_options['cd_style'])){ ?> checked <?php } else { checked( 'flipclock' == $uuc_options['cd_style'] ); } ?> /> Flip Clock / </label> 
					<label><input type="radio" name="uuc_settings[cd_style]" id="textclock" value="textclock"<?php checked( 'textclock' == $uuc_options['cd_style'] ); ?> /> Text only.</label>
					<br />
					<br />
					<input id="uuc_settings[cdday]" name="uuc_settings[cdday]" type="text" value="<?php echo $uuc_options['cdday']; ?>"/>
					<label class="description" for="uuc_settings[cdday]"><?php _e('Enter the Date - e.g. 14', 'uuc_domain'); ?></label>
					<br />
					<input id="uuc_settings[cdmonth]" name="uuc_settings[cdmonth]" type="text" value="<?php echo $uuc_options['cdmonth']; ?>"/>
					<label class="description" for="uuc_settings[cdmonth]"><?php _e('Enter the Month - e.g. 2', 'uuc_domain'); ?></label>
					<br />
					<input id="uuc_settings[cdyear]" name="uuc_settings[cdyear]" type="text" value="<?php echo $uuc_options['cdyear']; ?>"/>
					<label class="description" for="uuc_settings[cdyear]"><?php _e('Enter the Year -  e.g. 2014', 'uuc_domain'); ?></label>
					<br />
					<input id="uuc_settings[cdtext]" name="uuc_settings[cdtext]" type="text" value="<?php echo $uuc_options['cdtext']; ?>"/>
					<label class="description" for="uuc_settings[cdtext]"><?php _e('Enter the Countdown text - e.g. Till the site goes live!', 'uuc_domain'); ?></label>
				</p>

				<h4 class="uuc-title"><?php _e('Background Style', 'uuc_domain'); ?></h4>
				<p>
					<label><input onclick="checkEm()" type="radio" name="uuc_settings[background_style]" id="solidcolor" value="solidcolor"<?php if(!isset($uuc_options['background_style'])){ ?> checked <?php } else { checked( 'solidcolor' == $uuc_options['background_style'] ); } ?> /> Solid Colour</label><br />
					<label><input onclick="checkEm()" type="radio" name="uuc_settings[background_style]" id="patterned" value="patterned"<?php checked( 'patterned' == $uuc_options['background_style'] ); ?> /> Patterned Background</label>
				</p>

				<?php if ( $wp_version >= 3.5 ){ ?>
				<div id="solidcolorbg" <?php if($uuc_options['background_style'] == "patterned"){ ?>style="visibility: hidden; display: none;"<?php }; ?>>
					<h4 class="uuc-title"><?php _e('Background Colour', 'uuc_domain'); ?></h4>
					<p>
						<input name="uuc_settings[background_color]" id="background-color" type="text" value="<?php if ( isset( $uuc_options['background_color'] ) ) echo $uuc_options['background_color']; ?>" />
						<label class="description" for="uuc_settings[background_color]"><?php _e('Select the Background Colour', 'uuc_domain'); ?></label>
					</p>
				</div>
				<?php } else { ?>
				<div id="solidcolorbg" <?php if($uuc_options['background_style'] == "patterned"){ ?>style="visibility: hidden; display: none;"<?php }; ?>>
					<h4 class="uuc-title"><?php _e('Background Colour', 'uuc_domain'); ?></h4>
					<p>
					<div class="color-picker" style="position: relative;">
				        <input type="text" name="uuc_settings[background_color]" id="color" value="<?php if ( isset( $uuc_options['background_color'] ) ) echo $uuc_options['background_color']; ?>" />
				        <div style="position: absolute;" id="colorpicker"></div>
				    </div>
					</p>
				</div>
				<?php } ?>

				<div id="patternedbg" <?php if($uuc_options['background_style'] == "solidcolor"){ ?>style="visibility: hidden; display: none;"<?php }; ?>>
					<h4 class="uuc-title"><?php _e('Background Choice', 'uuc_domain'); ?></h4>
					<label><input type="radio" name="uuc_settings[background_styling]" id="background_choice_one" value="squairylight"<?php checked( 'squairylight' == isset($uuc_options['background_styling']) ); ?> /> Squairy</label><br />	
					<label><input type="radio" id="background_choice_two" name="uuc_settings[background_styling]" value="lightbind" <?php if(!isset($uuc_options['background_styling'])){ ?> checked <?php } else { checked( 'lightbind' == $uuc_options['background_styling'] ); } ?> /> Light Binding</label><br />
					<label><input type="radio" id="background_choice_three" name="uuc_settings[background_styling]" value="darkbind"  <?php if(!isset($uuc_options['background_styling'])){ ?> checked <?php } else { checked( 'darkbind' == $uuc_options['background_styling'] ); } ?> /> Dark Binding</label> <br />
					<label><input type="radio" id="background_choice_four" name="uuc_settings[background_styling]" value="wavegrid" <?php if(!isset($uuc_options['background_styling'])){ ?> checked <?php } else { checked( 'wavegrid' == $uuc_options['background_styling'] ); } ?> /> Wavegrid</label> <br />
					<label><input type="radio" id="background_choice_five" name="uuc_settings[background_styling]" value="greywashwall" <?php if(!isset($uuc_options['background_styling'])){ ?> checked <?php } else { checked( 'greywashwall' == $uuc_options['background_styling'] ); } ?> /> Gray Wash Wall</label> <br />
					<label><input type="radio" id="background_choice_six" name="uuc_settings[background_styling]" value="flatcardboard" <?php if(!isset($uuc_options['background_styling'])){ ?> checked <?php } else { checked( 'flatcardboard' == $uuc_options['background_styling'] ); } ?> /> Cardboard Flat</label> <br />
					<label><input type="radio" id="background_choice_seven" name="uuc_settings[background_styling]" value="pooltable" <?php if(!isset($uuc_options['background_styling'])){ ?> checked <?php } else { checked( 'pooltable' == $uuc_options['background_styling'] ); } ?> /> Pool Table</label> <br />
					<label><input type="radio" id="background_choice_eight" name="uuc_settings[background_styling]" value="oldmaths" <?php if(!isset($uuc_options['background_styling'])){ ?> checked <?php } else { checked( 'oldmaths' == $uuc_options['background_styling'] ); } ?> /> Old Mathematics</label> <br />
				</div>
			</div>

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Options', 'uuc_domain'); ?>" />
			</p>

			<script type="text/javascript">
			function checkPage() {
				if (document.getElementById("custom").checked) {
					document.getElementById("custombg").style.visibility = "visible";
					document.getElementById("custombg").style.display = "block";
					document.getElementById("htmlblockbg").style.visibility = "hidden";
					document.getElementById("htmlblockbg").style.display = "none";
				};

				if (document.getElementById("htmlblock").checked) {
					document.getElementById("htmlblockbg").style.visibility = "visible";
					document.getElementById("htmlblockbg").style.display = "block";
					document.getElementById("custombg").style.visibility = "hidden";
					document.getElementById("custombg").style.display = "none";
				}

			};

			function checkEm() {
			    if (document.getElementById("solidcolor").checked) {
			  		document.getElementById("solidcolorbg").style.visibility = "visible";
			        document.getElementById("solidcolorbg").style.display = "block";
			        document.getElementById("patternedbg").style.visibility = "hidden";
			        document.getElementById("patternedbg").style.display = "none";
			    };

			    if (document.getElementById("patterned").checked) {
			        document.getElementById("patternedbg").style.visibility = "visible";
			        document.getElementById("patternedbg").style.display = "block";
					document.getElementById("solidcolorbg").style.visibility = "hidden";
			        document.getElementById("solidcolorbg").style.display = "none";
			    };
			};
    		</script>

		</form>
	</div>
</div>
	<?php echo ob_get_clean();
}

function admin_register_head() {
    $siteurl = get_option('siteurl');
    $url = $siteurl . '/wp-content/plugins/ultimate-under-construction/includes/css/plugin_styles.css';
    echo "<link rel='stylesheet' type='text/css' href='$url' />\n";
}
add_action('admin_head', 'admin_register_head');

function uuc_add_options_link() {
	add_options_page('Ultimate Under Construction Plugin Options', 'Ultimate Under Construction', 'manage_options', 'uuc-options', 'uuc_options_page');
}
add_action('admin_menu', 'uuc_add_options_link');

function uuc_register_settings() {
	register_setting('uuc_settings_group', 'uuc_settings');
}
add_action('admin_init', 'uuc_register_settings');