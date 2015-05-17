<?php 
	if(empty($_GET['file']) || empty($_GET['nonce']) )
		die();
	if($_GET['file'] == 'theme_options'){
		$filename = 'theme_options.txt';
		$path = $_GET['TT_FW'] . '/../theme_config/theme_options.txt';
	}
	header("Content-Type: application/octet-stream");
	header("Content-Disposition: attachment; filename=".$filename);
	readfile($path);
?>