<?php
function showPublishedportfolios_1($id)
{
 global $wpdb;
	$query=$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."huge_itportfolio_images where portfolio_id = '%d' order by ordering ASC",$id);
	$images=$wpdb->get_results($query);
	$query=$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."huge_itportfolio_portfolios where id = '%d' order by id ASC",$id);
	$portfolio=$wpdb->get_results($query);
        $paramssld = '';
	return front_end_portfolio($images, $paramssld, $portfolio);
}
?>






