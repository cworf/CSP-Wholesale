<?php

	/* Load plugin text domain (localization) */
	add_action('init', 'upme_load_textdomain');
	function upme_load_textdomain() {
		load_plugin_textdomain( 'upme', false,'/upme/l10n');
	}
	
	//allow redirection, even if my theme starts to send output to the browser
	add_action('init', 'upme_output_buffer');
	function upme_output_buffer() {
		ob_start();
	}