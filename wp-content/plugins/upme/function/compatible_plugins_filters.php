<?php

add_filter( 'wpseo_canonical','upme_wpseo_canonical');

function upme_wpseo_canonical($canonical){
	return $_SERVER['REQUEST_URI'];
}