<?php

	/* admin icons for the plugin */
	add_action('admin_head',  'upme_admin_icon');
	function upme_admin_icon(){
		$screen = get_current_screen();
		if( !strstr($screen->id, 'upme') )
			return;

		$image_url = upme_url.'admin/images/upme-icon-32.png';
		echo "<style>
		#upme-icon-wp-upme {
			background: transparent url( '{$image_url }' ) no-repeat;
		}
		</style>";
	}