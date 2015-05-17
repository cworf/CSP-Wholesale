<?php
if (function_exists('current_user_can'))
    if (!current_user_can('manage_options')) {
        die('Access Denied');
    }
if (!function_exists('current_user_can')) {
    die('Access Denied');
}

function      html_showStyles($param_values, $op_type)
{
    ?>	
<script>

jQuery(document).ready(function () {
	var strliID=$(location).attr('hash');
	//alert(strliID);
	jQuery('#portfolio-view-tabs li').removeClass('active');
	if(jQuery('#portfolio-view-tabs li a[href="'+strliID+'"]').length>0){
		jQuery('#portfolio-view-tabs li a[href="'+strliID+'"]').parent().addClass('active');
	}else {
		jQuery('#portfolio-view-tabs li a[href="#portfolio-view-options-0"]').parent().addClass('active');
	}
	jQuery('#portfolio-view-tabs-contents li').removeClass('active');
	strliID=strliID;
	//alert(strliID);
	if(jQuery(strliID).length>0){
		jQuery(strliID).addClass('active');
	}else {
		jQuery('#portfolio-view-options-0').addClass('active');
	}
	$('input[data-slider="true"]').bind("slider:changed", function (event, data) {
		 $(this).parent().find('span').html(parseInt(data.value)+"%");
		 $(this).val(parseInt(data.value));
	});	
});
</script>
<div class="wrap">

<?php $path_site2 = plugins_url("../images", __FILE__); ?>
		<?php $path_site = plugins_url("Front_images", __FILE__); ?>
			<div class="slider-options-head">
		<div style="float: left;">
			<div><a href="http://huge-it.com/wordpress-plugins-portfolio-gallery-user-manual/" target="_blank">User Manual</a></div>
			<div>This section allows you to configure the Portfolio/Gallery options. <a href="http://huge-it.com/wordpress-plugins-portfolio-gallery-user-manual/" target="_blank">More...</a></div>
			<div>This options are disabled in free version. Get full version to customize them. <a href="http://huge-it.com/wordpress-plugins-portfolio-gallery-user-manual/" target="_blank">Get full Version</a></div>
		</div>
		<div style="float: right;">
			<a class="header-logo-text" href="http://huge-it.com/portfolio-gallery/" target="_blank">
				<div><img width="250px" src="<?php echo $path_site2; ?>/huge-it1.png" /></div>
				<div>Get the full version</div>
			</a>
		</div>
	</div>
	<div style="clear:both;"></div>
<div id="poststuff">
		
		<div id="post-body-content" class="portfolio-options">
			<div id="post-body-heading">
				<h3>General Options</h3>
				
				<a class="save-portfolio-options button-primary">Save</a>
		
			</div>
		<form action="admin.php?page=Options_portfolio_styles" method="post" id="adminForm" name="adminForm">
		<div id="portfolio-options-list">
			
			<ul id="portfolio-view-tabs">
				<li class="active"><a href="#portfolio-view-options-0">Blocks Toggle Up/Down</a></li>
				<li><a href="#portfolio-view-options-1">Full-Height Blocks</a></li>
				<li><a href="#portfolio-view-options-2">Gallery/Content-Popup</a></li>
				<li><a href="#portfolio-view-options-3">Full-Width Blocks</a></li>
				<li><a href="#portfolio-view-options-4">FAQ Toggle Up/Down</a></li>
				<li><a href="#portfolio-view-options-5">Content Slider</a></li>
				<li><a href="#portfolio-view-options-6">Lightbox-Gallery</a></li>
			</ul>
			
			<ul class="options-block" id="portfolio-view-tabs-contents">

				<li id="portfolio-view-options-0" class="active">
					<div>
						<h3>Element Styles</h3>
						<div class="has-background">
							<label for="ht_view0_element_background_color">Element Background Color</label>
							<input name="params[ht_view0_element_background_color]" type="text" class="color" id="ht_view0_element_background_color" value="#f7f7f7" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(247, 247, 247);">
						</div>
						<div>
							<label for="ht_view0_element_border_width">Element Border Width</label>
							<input type="text" name="params[ht_view0_element_border_width]" id="ht_view0_element_border_width" value="1" class="text">
							<span>px</span>
						</div>
						<div class="has-background">
							<label for="ht_view0_element_border_color">Element Border Color</label>
							<input name="params[ht_view0_element_border_color]" type="text" class="color" id="ht_view0_element_border_color" value="#D0D0D0" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(208, 208, 208);">
						</div>
						<div>
							<label for="ht_view0_togglebutton_style">Toggle Button Style</label>
							<select id="ht_view0_togglebutton_style" name="params[ht_view0_togglebutton_style]">	
							  <option value="light">Light</option>
							  <option selected="selected" value="dark">Dark</option>
							</select>
						</div>
						<div class="has-background">
							<label for="ht_view0_show_separator_lines">Show Separator Lines</label>
							<input type="hidden" value="off" name="params[ht_view0_show_separator_lines]">
							<input type="checkbox" id="ht_view0_show_separator_lines" checked="checked" name="params[ht_view0_show_separator_lines]" value="on">
						</div>
					</div>
					<div>
						<h3>Main Image</h3>
						<div class="has-background">
							<label for="ht_view0_block_width">Main Image Width</label>
							<input type="text" name="params[ht_view0_block_width]" id="ht_view0_block_width" value="275" class="text">
							<span>px</span>
						</div>
						<div>
							<label for="ht_view0_block_height">Main Image Height</label>
							<input type="text" name="params[ht_view0_block_height]" id="ht_view0_block_height" value="160" class="text">
							<span>px</span>
						</div>
					</div>
					<div style="margin-top: 14px;">
						<h3>Title</h3>
						<div class="has-background">
							<label for="ht_view0_title_font_size">Title Font Size</label>
							<input type="text" name="params[ht_view0_title_font_size]" id="ht_view0_title_font_size" value="15" class="text">
							<span>px</span>
						</div>
						<div>
							<label for="ht_view0_title_font_color">Title Font Color</label>
							<input name="params[ht_view0_title_font_color]" type="text" class="color" id="ht_view0_title_font_color" value="#555555" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(85, 85, 85);">
						</div>
					</div>
                                        
                                        <div style="margin-top:-40px;">
                                            <h3>Sorting styles</h3>
                                            <div class="has-background" style="display: none;">
                                                    <label for="ht_view0_show_sorting">Show Sorting</label>
                                                    <input type="hidden" value="off" name="params[ht_view0_show_sorting]">
                                                    <input type="checkbox" id="ht_view0_show_sorting" checked="checked" name="params[ht_view0_show_sorting]" value="on">
                                            </div>
                                            <div class="has-background">
                                                    <label for="ht_view0_sortbutton_font_size">Sort Button Font Size</label>
                                                    <input type="text" name="params[ht_view0_sortbutton_font_size]" id="ht_view0_sortbutton_font_size" value="14" class="text">
                                                    <span>px</span>
                                            </div>
                                            <div class="">
                                                    <label for="ht_view0_sortbutton_font_color">Sort Button Font Color</label>
                                                    <input name="params[ht_view0_sortbutton_font_color]" type="text" class="color" id="ht_view0_sortbutton_font_color" value="#555555" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(85, 85, 85);">
                                            </div>
                                            <div class="has-background">
                                                    <label for="ht_view0_sortbutton_hover_font_color">Sort Button Font Hover Color</label>
                                                    <input name="params[ht_view0_sortbutton_hover_font_color]" type="text" class="color" id="ht_view0_sortbutton_hover_font_color" value="#ffffff" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
                                            </div>
                                            <div class="">
                                                    <label for="ht_view0_sortbutton_background_color">Sort Button Background Color</label>
                                                    <input name="params[ht_view0_sortbutton_background_color]" type="text" class="color" id="ht_view0_sortbutton_background_color" value="#F7F7F7" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(247, 247, 247);">
                                            </div>
                                            <div class="has-background">
                                                    <label for="ht_view0_sortbutton_hover_background_color">Sort Button Background Hover Color</label>
                                                    <input name="params[ht_view0_sortbutton_hover_background_color]" type="text" class="color" id="ht_view0_sortbutton_hover_background_color" value="#FF3845" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(255, 56, 69);">
                                            </div>
                                            <div class="" style="display: none;">
                                                    <label for="ht_view0_sortbutton_border_width">Sort Button Border Width</label>
                                                    <input type="text" name="params[ht_view0_sortbutton_border_width]" id="ht_view0_sortbutton_border_width" value="" class="text">
                                                    <span>px</span>
                                            </div>
                                            <div style="display: none;">
                                                    <input name="params[ht_view0_sortbutton_border_color]" type="text" class="color" id="ht_view0_sortbutton_border_color" value="#" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
                                                    <label for="ht_view0_sortbutton_border_color">Sort Button Border Color</label>
                                            </div>
                                                <div>
                                                    <label for="ht_view0_sortbutton_border_radius">Sort Button Border Radius</label>
                                                    <input type="text" name="params[ht_view0_sortbutton_border_radius]" id="ht_view0_sortbutton_border_radius" value="0" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div class="has-background">
                                                    <label for="ht_view0_sortbutton_border_padding">Sort Button Padding</label>
                                                    <input type="text" name="params[ht_view0_sortbutton_border_padding]" id="ht_view0_sortbutton_border_padding" value="3" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div style="display: none;">
                                                    <label for="ht_view0_sortbutton_margin">Sort Button Margins</label>
                                                    <input type="text" name="params[ht_view0_sortbutton_margin]" id="ht_view0_sortbutton_margin" value="" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div>
                                                    <label for="ht_view0_sorting_float">Sort block Position</label>
                                                    <select id="ht_view0_sorting_float" name="params[ht_view0_sorting_float]">	
                                                      <option value="left">Left</option>
                                                      <option value="right">Right</option>
                                                      <option selected="selected" value="top">Top</option>
                                                    </select>
						</div>
                                                <div class="has-background">
							<label for="ht_view0_sorting_name_by_default">Sort By Default Button Name</label>
							<input name="params[ht_view0_sorting_name_by_default]" type="text" id="ht_view0_sorting_name_by_default" value="Default" size="10" class="text">
						</div>
						<div class="">
							<label for="ht_view0_sorting_name_by_id">Sorting By ID Button Name</label>
							<input name="params[ht_view0_sorting_name_by_id]" type="text" id="ht_view0_sorting_name_by_id" value="Date" size="10">
						</div>
                                                <div class="has-background">
							<label for="ht_view0_sorting_name_by_name">Sorting By ID Button Name</label>
							<input name="params[ht_view0_sorting_name_by_name]" type="text" id="ht_view0_sorting_name_by_name" value="Title" size="10">
						</div>
						<div class="">
							<label for="ht_view0_sorting_name_by_random">Random Sorting Button Name</label>
							<input name="params[ht_view0_sorting_name_by_random]" type="text" id="ht_view0_sorting_name_by_random" value="Random" size="10">
						</div>
                                                <div class="has-background">
							<label for="ht_view0_sorting_name_by_asc">Ascedding Sorting Button Name</label>
							<input name="params[ht_view0_sorting_name_by_asc]" type="text" id="ht_view0_sorting_name_by_asc" value="Asceding" size="10">
						</div>
						<div class="">
							<label for="ht_view0_sorting_name_by_desc">Descedding Sorting Button Name</label>
							<input name="params[ht_view0_sorting_name_by_desc]" type="text" id="ht_view0_sorting_name_by_desc" value="Desceding" size="10">
						</div>
                                            </div>
                                                                                    
					<div style="margin-top:14px;">
						<h3>Thumbnails</h3>
						<div class="has-background">
							<label for="ht_view0_show_thumbs">Show Thumbnails</label>
							<input type="hidden" value="off" name="params[ht_view0_show_thumbs]">
							<input type="checkbox" id="ht_view0_show_thumbs" checked="checked" name="params[ht_view0_show_thumbs]" value="on">
						</div>
						<div>
							<label for="ht_view0_thumbs_position">Thumbnails Position</label>
							<select id="ht_view0_thumbs_position" name="params[ht_view0_thumbs_position]">	
							  <option selected="selected" value="before">Before Description</option>
							  <option value="after">After Description</option>
							</select>
						</div>
						<div class="has-background">
							<label for="ht_view0_thumbs_width">Thumbnails Width</label>
							<input type="text" name="params[ht_view0_thumbs_width]" id="ht_view0_thumbs_width" value="75" class="text">
							<span>px</span>
						</div>
					</div>
                                        
                                        
					<div>
						<h3>Description</h3>
						<div class="has-background">
							<label for="ht_view0_show_description">Show Description</label>
							<input type="hidden" value="off" name="params[ht_view0_show_description]">
							<input type="checkbox" id="ht_view0_show_description" checked="checked" name="params[ht_view0_show_description]" value="on">
						</div>
						<div>
							<label for="ht_view0_description_font_size">Description Font Size</label>
							<input type="text" name="params[ht_view0_description_font_size]" id="ht_view0_description_font_size" value="14" class="text">
							<span>px</span>
						</div>
						<div class="has-background">
							<label for="ht_view0_description_color">Description Font Color</label>
							<input name="params[ht_view0_description_color]" type="text" class="color" id="ht_view0_description_color" value="#5b5b5b" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(91, 91, 91);">
						</div>
					</div>
                                    
                                        <div style="margin-top: 14px;">
                                            <h3>Category styles</h3>
                                            
                                                <div style="display: none;">
                                                    <label for="ht_view0_show_filtering">Show Filtering</label>
                                                    <input type="hidden" value="off" name="params[ht_view0_show_filtering]">
                                                    <input type="checkbox" id="ht_view0_show_filtering" checked="checked" name="params[ht_view0_show_filtering]" value="on">
                                                </div>

                                                <div class="">
                                                    <label for="ht_view0_cat_all">Show All Category Button Name</label>
                                                    <input type="text" name="params[ht_view0_cat_all]" id="ht_view0_cat_all" value="All" class="text" />
                                                </div>
                                                <div class="has-background">
                                                    <label for="ht_view0_filterbutton_font_size">Filter Button Font Size</label>
                                                    <input type="text" name="params[ht_view0_filterbutton_font_size]" id="ht_view0_filterbutton_font_size" value="14" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div class="">
                                                    <label for="ht_view0_filterbutton_font_color">Filter Button Font Color</label>
                                                    <input name="params[ht_view0_filterbutton_font_color]" type="text" class="color" id="ht_view0_filterbutton_font_color" value="#555555" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(85, 85, 85);">
                                                </div>
                                                <div class="has-background">
                                                    <label for="ht_view0_filterbutton_hover_font_color">Filter Button Font Hover Color</label>
                                                    <input name="params[ht_view0_filterbutton_hover_font_color]" type="text" class="color" id="ht_view0_filterbutton_hover_font_color" value="#ffffff" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
                                                </div>
                                                <div class="">
                                                    <label for="ht_view0_filterbutton_background_color">Filter Button Background Color</label>
                                                    <input name="params[ht_view0_filterbutton_background_color]" type="text" class="color" id="ht_view0_filterbutton_background_color" value="#F7F7F7" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(247, 247, 247);">
                                                </div>
                                                <div class="has-background">
                                                    <label for="ht_view0_filterbutton_hover_background_color">Filter Button Background Hover Color</label>
                                                    <input name="params[ht_view0_filterbutton_hover_background_color]" type="text" class="color" id="ht_view0_filterbutton_hover_background_color" value="#FF3845" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(255, 56, 69);">
                                                </div>

                                                <div class="" style="display: none;">
                                                    <label for="ht_view0_filterbutton_border_width">Filter Button Border Width</label>
                                                    <input type="text" name="params[ht_view0_filterbutton_border_width]" id="ht_view0_filterbutton_border_width" value="" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div style="display: none;">
                                                    <input name="params[ht_view0_filterbutton_border_color]" type="text" class="color" id="ht_view0_filterbutton_border_color" value="#" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
                                                    <label for="ht_view0_filterbutton_border_color">Filter Button Border Color</label>
                                                </div>
                                                <div class="">
                                                    <label for="ht_view0_filterbutton_border_radius">Filter Button Border Radius</label>
                                                    <input type="text" name="params[ht_view0_filterbutton_border_radius]" id="ht_view0_filterbutton_border_radius" value="0" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div class="has-background">
                                                    <label for="ht_view0_filterbutton_border_padding">Filter Button Padding</label>
                                                    <input type="text" name="params[ht_view0_filterbutton_border_padding]" id="ht_view0_filterbutton_border_padding" value="3" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div style="display: none;">
                                                    <label for="ht_view0_filterbutton_margin">Filter Button Margins</label>
                                                    <input type="text" name="params[ht_view0_filterbutton_margin]" id="ht_view0_filterbutton_margin" value="" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div class="">
                                                    <label for="ht_view0_filtering_float">Filter block Position</label>
                                                    <select id="ht_view0_filtering_float" name="params[ht_view0_filtering_float]">	
                                                      <option selected="selected" value="left">Left</option>
                                                      <option value="right">Right</option>
                                                      <option value="top">Top</option>
                                                    </select>
                                                </div>
                                            </div>
                                    
					<div style="margin-top: -264px;">
						<h3>Link Button</h3>
						<div class="has-background">
							<label for="ht_view0_show_linkbutton">Show Link Button</label>
							<input type="hidden" value="off" name="params[ht_view0_show_linkbutton]">
							<input type="checkbox" id="ht_view0_show_linkbutton" checked="checked" name="params[ht_view0_show_linkbutton]" value="on">
						</div>
						<div>
							<label for="ht_view0_linkbutton_text">Link Button Text</label>
							<input type="text" name="params[ht_view0_linkbutton_text]" id="ht_view0_linkbutton_text" value="View More" class="text">
						</div>
						<div class="has-background">
							<label for="ht_view0_linkbutton_font_size">Link Button Font Size</label>
							<input type="text" name="params[ht_view0_linkbutton_font_size]" id="ht_view0_linkbutton_font_size" value="14" class="text">
							<span>px</span>
						</div>
						<div>
							<label for="ht_view0_linkbutton_color">Link Button Font Color</label>
							<input name="params[ht_view0_linkbutton_color]" type="text" class="color" id="ht_view0_linkbutton_color" value="#ffffff" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
						</div>
						<div class="has-background">
							<label for="ht_view0_linkbutton_font_hover_color">Link Button Font Hover Color</label>
							<input name="params[ht_view0_linkbutton_font_hover_color]" type="text" class="color" id="ht_view0_linkbutton_font_hover_color" value="#ffffff" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
						</div>
						<div>
							<label for="ht_view0_linkbutton_background_color">Link Button Background Color</label>
							<input name="params[ht_view0_linkbutton_background_color]" type="text" class="color" id="ht_view0_linkbutton_background_color" value="#e74c3c" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(231, 76, 60);">
						</div>
						<div class="has-background">
							<label for="ht_view0_linkbutton_background_hover_color">Link Button Background Hover Color</label>
							<input name="params[ht_view0_linkbutton_background_hover_color]" type="text" class="color" id="ht_view0_linkbutton_background_hover_color" value="#df2e1b" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(223, 46, 27);">
						</div>
                                                
					</div>
				</li>
				

				<!-- VIEW 1 -->
				<li id="portfolio-view-options-1">
					<div>
						<h3>Element Styles</h3>
						<div class="has-background">
							<label for="ht_view1_block_width">Block Width</label>
							<input type="text" name="params[ht_view1_block_width]" id="ht_view1_block_width" value="275" class="text">
							<span>px</span>
						</div>
						<div>
							<label for="ht_view1_element_background_color">Element Background Color</label>
							<input name="params[ht_view1_element_background_color]" type="text" class="color" id="ht_view1_element_background_color" value="#f7f7f7" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(247, 247, 247);">
						</div>
						<div class="has-background">
							<label for="ht_view1_element_border_width">Element Border Width</label>
							<input type="text" name="params[ht_view1_element_border_width]" id="ht_view1_element_border_width" value="1" class="text">
							<span>px</span>
						</div>
						<div>
							<label for="ht_view1_element_border_color">Element Border Color</label>
							<input name="params[ht_view1_element_border_color]" type="text" class="color" id="ht_view1_element_border_color" value="#D0D0D0" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(208, 208, 208);">
						</div>
						<div class="has-background">
							<label for="ht_view1_show_separator_lines">Show Separator Lines</label>
							<input type="hidden" value="off" name="params[ht_view1_show_separator_lines]">
							<input type="checkbox" id="ht_view1_show_separator_lines" checked="checked" name="params[ht_view1_show_separator_lines]" value="on">
						</div>
					</div>
					<div>
						<h3>Title</h3>
						<div class="has-background">
							<label for="ht_view1_title_font_size">Title Font Size</label>
							<input type="text" name="params[ht_view1_title_font_size]" id="ht_view1_title_font_size" value="15" class="text">
							<span>px</span>
						</div>
						<div>
							<label for="ht_view1_title_font_color">Title Font Color</label>
							<input name="params[ht_view1_title_font_color]" type="text" class="color" id="ht_view1_title_font_color" value="#555555" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(85, 85, 85);">
						</div>
					</div>
					<div style="margin-top: 14px;">
						<h3>Thumbnails</h3>
						<div class="has-background">
							<label for="ht_view1_show_thumbs">Show Thumbnails</label>
							<input type="hidden" value="off" name="params[ht_view1_show_thumbs]">
							<input type="checkbox" id="ht_view1_show_thumbs" checked="checked" name="params[ht_view1_show_thumbs]" value="on">
						</div>
						<div>
							<label for="ht_view1_thumbs_position">Thumbnails Position</label>
							<select id="ht_view1_thumbs_position" name="params[ht_view1_thumbs_position]">	
							  <option selected="selected" value="before">Before Description</option>
							  <option value="after">After Description</option>
							</select>
						</div>
						<div class="has-background">
							<label for="ht_view1_thumbs_width">Thumbnails Width</label>
							<input type="text" name="params[ht_view1_thumbs_width]" id="ht_view1_thumbs_width" value="75" class="text">
							<span>px</span>
						</div>
					</div>
                                       
					<div style="margin-top:-80px;">
						<h3>Link Button</h3>
						<div class="has-background">
							<label for="ht_view1_show_linkbutton">Show Link Button</label>
							<input type="hidden" value="off" name="params[ht_view1_show_linkbutton]">
							<input type="checkbox" id="ht_view1_show_linkbutton" checked="checked" name="params[ht_view1_show_linkbutton]" value="on">
						</div>
						<div>
							<label for="ht_view1_linkbutton_text">Link Button Text</label>
							<input type="text" name="params[ht_view1_linkbutton_text]" id="ht_view1_linkbutton_text" value="View More" class="text">
						</div>
						<div class="has-background">
							<label for="ht_view1_linkbutton_font_size">Link Button Font Size</label>
							<input type="text" name="params[ht_view1_linkbutton_font_size]" id="ht_view1_linkbutton_font_size" value="14" class="text">
							<span>px</span>
						</div>
						<div>
							<label for="ht_view1_linkbutton_color">Link Button Font Color</label>
							<input name="params[ht_view1_linkbutton_color]" type="text" class="color" id="ht_view1_linkbutton_color" value="#ffffff" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
						</div>
						<div class="has-background">
							<label for="ht_view1_linkbutton_font_hover_color">Link Button Font Hover Color</label>
							<input name="params[ht_view1_linkbutton_font_hover_color]" type="text" class="color" id="ht_view1_linkbutton_font_hover_color" value="#ffffff" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
						</div>
						<div>
							<label for="ht_view1_linkbutton_background_color">Link Button Background Color</label>
							<input name="params[ht_view1_linkbutton_background_color]" type="text" class="color" id="ht_view1_linkbutton_background_color" value="#e74c3c" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(231, 76, 60);">
						</div>
						<div class="has-background">
							<label for="ht_view1_linkbutton_background_hover_color">Link Button Background Hover Color</label>
							<input name="params[ht_view1_linkbutton_background_hover_color]" type="text" class="color" id="ht_view1_linkbutton_background_hover_color" value="#df2e1b" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(223, 46, 27);">
						</div>
					</div>
                                    
                                        
                                        
                                        <div style="margin-top: 14px;">
						<h3>Description</h3>
						<div class="has-background">
							<label for="ht_view1_show_description">Show Description</label>
							<input type="hidden" value="off" name="params[ht_view1_show_description]">
							<input type="checkbox" id="ht_view1_show_description" checked="checked" name="params[ht_view1_show_description]" value="on">
						</div>
						<div>
							<label for="ht_view1_description_font_size">Description Font Size</label>
							<input type="text" name="params[ht_view1_description_font_size]" id="ht_view1_description_font_size" value="14" class="text">
							<span>px</span>
						</div>
						<div class="has-background">
							<label for="ht_view1_description_color">Description Font Color</label>
							<input name="params[ht_view1_description_color]" type="text" class="color" id="ht_view1_description_color" value="#5b5b5b" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(91, 91, 91);">
						</div>
					</div>
                                        
                                        <div style="margin-top:14px;">
                                            <h3>Category styles</h3>
                                                <div style="display: none;">
                                                    <label for="ht_view1_show_filtering" style="display: none;">Show Filtering</label>
                                                    <input type="hidden" value="off" name="params[ht_view1_show_filtering]">
                                                    <input type="checkbox" id="ht_view1_show_filtering" checked="checked" name="params[ht_view1_show_filtering]" value="on">
                                                </div>
                                                <div class="">
                                                    <label for="ht_view1_cat_all">Show All Category Button Name</label>
                                                    <input type="text" name="params[ht_view1_cat_all]" id="ht_view1_cat_all" value="All" class="text" />
                                                </div>
                                                <div class="has-background">
                                                    <label for="ht_view1_filterbutton_font_size">Filter Button Font Size</label>
                                                    <input type="text" name="params[ht_view1_filterbutton_font_size]" id="ht_view1_filterbutton_font_size" value="14" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div class="">
                                                    <label for="ht_view1_filterbutton_font_color">Filter Button Font Color</label>
                                                    <input name="params[ht_view1_filterbutton_font_color]" type="text" class="color" id="ht_view1_filterbutton_font_color" value="#555555" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(85, 85, 85);">
                                                </div>
                                                <div class="has-background">
                                                    <label for="ht_view1_filterbutton_hover_font_color">Filter Button Font Hover Color</label>
                                                    <input name="params[ht_view1_filterbutton_hover_font_color]" type="text" class="color" id="ht_view1_filterbutton_hover_font_color" value="#ffffff" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
                                                </div>
                                                <div class="">
                                                    <label for="ht_view1_filterbutton_background_color">Filter Button Background Color</label>
                                                    <input name="params[ht_view1_filterbutton_background_color]" type="text" class="color" id="ht_view1_filterbutton_background_color" value="#F7F7F7" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(247, 247, 247);">
                                                </div>
                                                <div class="has-background">
                                                        <label for="ht_view1_filterbutton_hover_background_color">Filter Button Background Hover Color</label>
                                                        <input name="params[ht_view1_filterbutton_hover_background_color]" type="text" class="color" id="ht_view1_filterbutton_hover_background_color" value="#FF3845" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(255, 56, 69);">
                                                </div>
                                                <div class="" style="display: none;">
                                                    <label for="ht_view1_filterbutton_border_width">Filter Button Border Width</label>
                                                    <input type="text" name="params[ht_view1_filterbutton_border_width]" id="ht_view1_filterbutton_border_width" value="" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div style="display: none;">
                                                    <input name="params[ht_view1_filterbutton_border_color]" type="text" class="color" id="ht_view1_filterbutton_border_color" value="#" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
                                                    <label for="ht_view1_filterbutton_border_color">Filter Button Border Color</label>
                                                </div>
                                                <div class="">
                                                    <label for="ht_view1_filterbutton_border_radius">Filter Button Border Radius</label>
                                                    <input type="text" name="params[ht_view1_filterbutton_border_radius]" id="ht_view1_filterbutton_border_radius" value="0" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div class="has-background">
                                                    <label for="ht_view1_filterbutton_border_padding">Filter Button Padding</label>
                                                    <input type="text" name="params[ht_view1_filterbutton_border_padding]" id="ht_view1_filterbutton_border_padding" value="3" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div style="display: none;">
                                                    <label for="ht_view1_filterbutton_margin">Filter Button Margins</label>
                                                    <input type="text" name="params[ht_view1_filterbutton_margin]" id="ht_view1_filterbutton_margin" value="" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div class="">
                                                    <label for="ht_view1_filtering_float">Filter block Position</label>
                                                    <select id="ht_view1_filtering_float" name="params[ht_view1_filtering_float]">	
                                                      <option selected="selected" value="left">Left</option>
                                                      <option value="right">Right</option>
                                                      <option value="top">Top</option>
                                                    </select>
                                                </div>
                                                <div class="has-background">
							<label for="ht_view1_sorting_name_by_default">Sort By Default Button Name</label>
							<input name="params[ht_view1_sorting_name_by_default]" type="text" id="ht_view1_sorting_name_by_default" value="Default" size="10" class="text">
						</div>
						<div class="">
							<label for="ht_view1_sorting_name_by_id">Sorting By ID Button Name</label>
							<input name="params[ht_view1_sorting_name_by_id]" type="text" id="ht_view1_sorting_name_by_id" value="Date" size="10">
						</div>
                                                <div class="has-background">
							<label for="ht_view1_sorting_name_by_name">Sorting By ID Button Name</label>
							<input name="params[ht_view1_sorting_name_by_name]" type="text" id="ht_view1_sorting_name_by_name" value="Title" size="10">
						</div>
						<div class="">
							<label for="ht_view1_sorting_name_by_random">Random Sorting Button Name</label>
							<input name="params[ht_view1_sorting_name_by_random]" type="text" id="ht_view1_sorting_name_by_random" value="Random" size="10">
						</div>
                                                <div class="has-background">
							<label for="ht_view1_sorting_name_by_asc">Ascedding Sorting Button Name</label>
							<input name="params[ht_view1_sorting_name_by_asc]" type="text" id="ht_view1_sorting_name_by_asc" value="Asceding" size="10">
						</div>
						<div class="">
							<label for="ht_view1_sorting_name_by_desc">Descedding Sorting Button Name</label>
							<input name="params[ht_view1_sorting_name_by_desc]" type="text" id="ht_view1_sorting_name_by_desc" value="Desceding" size="10">
						</div>
                                        </div>
                                        
                                        <div style="margin-top: -574px;">
                                            <h3>Sorting styles</h3>
                                            <div class="" style="display: none;">
                                                    <label for="ht_view1_show_sorting" style="display: none;">Show Sorting</label>
                                                    <input type="hidden" value="off" name="params[ht_view1_show_sorting]">
                                                    <input type="checkbox" id="ht_view1_show_sorting" checked="checked" name="params[ht_view1_show_sorting]" value="on">
                                            </div>
                                            <div class="has-background">
                                                    <label for="ht_view1_sortbutton_font_size">Sort Button Font Size</label>
                                                    <input type="text" name="params[ht_view1_sortbutton_font_size]" id="ht_view1_sortbutton_font_size" value="14" class="text">
                                                    <span>px</span>
                                            </div>
                                            <div class="">
                                                    <label for="ht_view1_sortbutton_font_color">Sort Button Font Color</label>
                                                    <input name="params[ht_view1_sortbutton_font_color]" type="text" class="color" id="ht_view1_sortbutton_font_color" value="#555555" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(85, 85, 85);">
                                            </div>
                                            <div class="has-background">
                                                    <label for="ht_view1_sortbutton_hover_font_color">Sort Button Font Hover Color</label>
                                                    <input name="params[ht_view1_sortbutton_hover_font_color]" type="text" class="color" id="ht_view1_sortbutton_hover_font_color" value="#ffffff" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
                                            </div>
                                            <div class="">
                                                    <label for="ht_view1_sortbutton_background_color">Sort Button Background Color</label>
                                                    <input name="params[ht_view1_sortbutton_background_color]" type="text" class="color" id="ht_view1_sortbutton_background_color" value="#F7F7F7" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(247, 247, 247);">
                                            </div>
                                            <div class="has-background">
                                                    <label for="ht_view1_sortbutton_hover_background_color">Sort Button Background Hover Color</label>
                                                    <input name="params[ht_view1_sortbutton_hover_background_color]" type="text" class="color" id="ht_view1_sortbutton_hover_background_color" value="#FF3845" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(255, 56, 69);">
                                            </div>
                                            <div class="has-background" style="display: none;">
                                                    <label for="ht_view1_sortbutton_border_width">Sort Button Border Width</label>
                                                    <input type="text" name="params[ht_view1_sortbutton_border_width]" id="ht_view1_sortbutton_border_width" value="" class="text">
                                                    <span>px</span>
                                            </div>
                                            <div style="display: none;">
                                                    <input name="params[ht_view1_sortbutton_border_color]" type="text" class="color" id="ht_view1_sortbutton_border_color" value="#" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
                                                    <label for="ht_view1_sortbutton_border_color">Sort Button Border Color</label>
                                            </div>
                                                <div class="">
                                                    <label for="ht_view1_sortbutton_border_radius">Sort Button Border Radius</label>
                                                    <input type="text" name="params[ht_view1_sortbutton_border_radius]" id="ht_view1_sortbutton_border_radius" value="0" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div class="has-background">
                                                    <label for="ht_view1_sortbutton_border_padding">Sort Button Padding</label>
                                                    <input type="text" name="params[ht_view1_sortbutton_border_padding]" id="ht_view1_sortbutton_border_padding" value="3" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div style="display: none;">
                                                    <label for="ht_view1_sortbutton_margin">Sort Button Margins</label>
                                                    <input type="text" name="params[ht_view1_sortbutton_margin]" id="ht_view1_sortbutton_margin" value="" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div class="">
                                                    <label for="ht_view1_sorting_float">Sort block Position</label>
                                                    <select id="ht_view1_sorting_float" name="params[ht_view1_sorting_float]">	
                                                      <option value="left">Left</option>
                                                      <option value="right">Right</option>
                                                      <option selected="selected" value="top">Top</option>
                                                    </select>
						</div>
                                        </div>
				</li>

				<!-- VIEW 2 POPUP -->
				<li id="portfolio-view-options-2">
					<div>
						<h3>Element Styles</h3>
						<div class="has-background">
							<label for="ht_view2_element_width">Element Width</label>
							<input type="text" name="params[ht_view2_element_width]" id="ht_view2_element_width" value="275" class="text">
							<span>px</span>
						</div>
						<div>
							<label for="ht_view2_element_height">Element Height</label>
							<input type="text" name="params[ht_view2_element_height]" id="ht_view2_element_height" value="160" class="text">
							<span>px</span>
						</div>
						<div class="has-background">
							<label for="ht_view2_element_background_color">Element Background Color</label>
							<input name="params[ht_view2_element_background_color]" type="text" class="color" id="ht_view2_element_background_color" value="#f9f9f9" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(249, 249, 249);">
						</div>
						<div>
							<label for="ht_view2_element_border_width">Element Border Width</label>
							<input type="text" name="params[ht_view2_element_border_width]" id="ht_view2_element_border_width" value="1" class="text">
							<span>px</span>
						</div>
						<div class="has-background">
							<label for="ht_view2_element_border_color">Element Border Color</label>
							<input name="params[ht_view2_element_border_color]" type="text" class="color" id="ht_view2_element_border_color" value="#dedede" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(222, 222, 222);">
						</div>
						<div>
							<label for="ht_view2_element_overlay_color">Element's Image Overlay Color</label>
							<input name="params[ht_view2_element_overlay_color]" type="text" class="color" id="ht_view2_element_overlay_color" value="#FFFFFF" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
						</div>
						<div class="has-background">
							<label for="ht_view2_zoombutton_style">Element's Image Overlay Transparency</label>
							<div class="slider-container">
								<div class="slider" id="ht_view2_element_overlay_transparency-slider" style="position: relative; -webkit-user-select: none; box-sizing: border-box; min-height: 14px; margin-left: 7px; margin-right: 7px;"><div class="track" style="position: absolute; top: 50%; -webkit-user-select: none; cursor: pointer; width: 100%; margin-top: -3px;"></div><div class="highlight-track" style="position: absolute; top: 50%; -webkit-user-select: none; cursor: pointer; width: 66.5px; margin-top: -3px;"></div><div class="dragger" style="position: absolute; top: 50%; -webkit-user-select: none; cursor: pointer; margin-top: -7px; margin-left: -7px; left: 66.5px;"></div></div><input name="params[ht_view2_element_overlay_transparency]" id="ht_view2_element_overlay_transparency" data-slider-highlight="true" data-slider-values="0,10,20,30,40,50,60,70,80,90,100" type="text" data-slider="true" value="70" style="display: none;">
								<span>70%</span>
							</div>
						</div>
						<div>
							<label for="ht_view2_zoombutton_style">Zoom Image Style</label>
							<select id="ht_view2_zoombutton_style" name="params[ht_view2_zoombutton_style]">	
							  <option selected="selected" value="light">Light</option>
							  <option value="dark">Dark</option>
							</select>
						</div>
					</div>
					<div>					
						<h3>Element Title</h3>
						<div class="has-background">
							<label for="ht_view2_element_title_font_size">Element Title Font Size</label>
							<input type="text" name="params[ht_view2_element_title_font_size]" id="ht_view2_element_title_font_size" value="18" class="text">
							<span>px</span>
						</div>
						<div>
							<label for="ht_view2_element_title_font_color">Element Title Font Color</label>
							<input name="params[ht_view2_element_title_font_color]" type="text" class="color" id="ht_view2_element_title_font_color" value="#222222" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(34, 34, 34);">
						</div>
					</div>
					<div>					
						<h3>Element Link Button</h3>
						<div class="has-background">
							<label for="ht_view2_element_show_linkbutton">Show Link Button On Element</label>
							<input type="hidden" value="off" name="params[ht_view2_element_show_linkbutton]">
							<input type="checkbox" id="ht_view2_element_show_linkbutton" checked="checked" name="params[ht_view2_element_show_linkbutton]" value="on">
						</div>
						<div>
							<label for="ht_view2_element_linkbutton_text">Link Button Text</label>
							<input type="text" name="params[ht_view2_element_linkbutton_text]" id="ht_view2_element_linkbutton_text" value="View More" class="text">
						</div>
						<div class="has-background">
							<label for="ht_view2_element_linkbutton_font_size">Link Button Font Size</label>
							<input type="text" name="params[ht_view2_element_linkbutton_font_size]" id="ht_view2_element_linkbutton_font_size" value="14" class="text">
							<span>px</span>
						</div>
						<div>
							<label for="ht_view2_element_linkbutton_color">Link Button Font Color</label>
							<input name="params[ht_view2_element_linkbutton_color]" type="text" class="color" id="ht_view2_element_linkbutton_color" value="#ffffff" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
						</div>
						<div class="has-background">
							<label for="ht_view2_element_linkbutton_background_color">Link Button Background Color</label>
							<input name="params[ht_view2_element_linkbutton_background_color]" type="text" class="color" id="ht_view2_element_linkbutton_background_color" value="#2ea2cd" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(46, 162, 205);">
						</div>
					</div>
					
                                        <div style="margin-top: -36px;">
                                            <h3>Sorting styles</h3>
                                            <div class="" style="display: none;">
                                                    <label for="ht_view2_show_sorting" style="display: none;">Show Sorting</label>
                                                    <input type="hidden" value="off" name="params[ht_view2_show_sorting]">
                                                    <input type="checkbox" id="ht_view2_show_sorting" checked="checked" name="params[ht_view2_show_sorting]" value="on">
                                            </div>

                                            <div class="has-background">
                                                    <label for="ht_view2_sortbutton_font_size">Sort Button Font Size</label>
                                                    <input type="text" name="params[ht_view2_sortbutton_font_size]" id="ht_view2_sortbutton_font_size" value="14" class="text">
                                                    <span>px</span>
                                            </div>
                                            <div class="">
                                                    <label for="ht_view2_sortbutton_font_color">Sort Button Font Color</label>
                                                    <input name="params[ht_view2_sortbutton_font_color]" type="text" class="color" id="ht_view2_sortbutton_font_color" value="#555555" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(85, 85, 85);">
                                            </div>
                                            <div class="has-background">
                                                    <label for="ht_view2_sortbutton_hover_font_color">Sort Button Font Hover Color</label>
                                                    <input name="params[ht_view2_sortbutton_hover_font_color]" type="text" class="color" id="ht_view2_sortbutton_hover_font_color" value="#ffffff" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
                                            </div>
                                            <div class="">
                                                    <label for="ht_view2_sortbutton_background_color">Sort Button Background Color</label>
                                                    <input name="params[ht_view2_sortbutton_background_color]" type="text" class="color" id="ht_view2_sortbutton_background_color" value="#F7F7F7" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(247, 247, 247);">
                                            </div>
                                            <div class="has-background">
                                                    <label for="ht_view2_sortbutton_hover_background_color">Sort Button Background Hover Color</label>
                                                    <input name="params[ht_view2_sortbutton_hover_background_color]" type="text" class="color" id="ht_view2_sortbutton_hover_background_color" value="#FF3845" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(255, 56, 69);">
                                            </div>
                                            <div class="" style="display: none;">
                                                    <label for="ht_view2_sortbutton_border_width">Sort Button Border Width</label>
                                                    <input type="text" name="params[ht_view2_sortbutton_border_width]" id="ht_view2_sortbutton_border_width" value="" class="text">
                                                    <span>px</span>
                                            </div>
                                            <div style="display: none;">
                                                    <input name="params[ht_view2_sortbutton_border_color]" type="text" class="color" id="ht_view2_sortbutton_border_color" value="#" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
                                                    <label for="ht_view2_sortbutton_border_color">Sort Button Border Color</label>
                                            </div>
                                                <div class="">
                                                    <label for="ht_view2_sortbutton_border_radius">Sort Button Border Radius</label>
                                                    <input type="text" name="params[ht_view2_sortbutton_border_radius]" id="ht_view2_sortbutton_border_radius" value="0" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div class="has-background">
                                                    <label for="ht_view2_sortbutton_border_padding">Sort Button Padding</label>
                                                    <input type="text" name="params[ht_view2_sortbutton_border_padding]" id="ht_view2_sortbutton_border_padding" value="3" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div class="" style="display: none;">
                                                    <label for="ht_view2_sortbutton_margin">Sort Button Margins</label>
                                                    <input type="text" name="params[ht_view2_sortbutton_margin]" id="ht_view2_sortbutton_margin" value="" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div class="">
                                                    <label for="ht_view2_sorting_float">Sort block Position</label>
                                                    <select id="ht_view2_sorting_float" name="params[ht_view2_sorting_float]">	
                                                      <option value="left">Left</option>
                                                      <option value="right">Right</option>
                                                      <option selected="selected" value="top">Top</option>
                                                    </select>
						</div>
                                                <div class="has-background">
							<label for="ht_view2_sorting_name_by_default">Sort By Default Button Name</label>
							<input name="params[ht_view2_sorting_name_by_default]" type="text" id="ht_view2_sorting_name_by_default" value="Default" size="10" class="text">
						</div>
						<div class="">
							<label for="ht_view2_sorting_name_by_id">Sorting By ID Button Name</label>
							<input name="params[ht_view2_sorting_name_by_id]" type="text" id="ht_view2_sorting_name_by_id" value="Date" size="10">
						</div>
                                                <div class="has-background">
							<label for="ht_view2_sorting_name_by_name">Sorting By ID Button Name</label>
							<input name="params[ht_view2_sorting_name_by_name]" type="text" id="ht_view2_sorting_name_by_name" value="Title" size="10">
						</div>
						<div class="">
							<label for="ht_view2_sorting_name_by_random">Random Sorting Button Name</label>
							<input name="params[ht_view2_sorting_name_by_random]" type="text" id="ht_view2_sorting_name_by_random" value="Random" size="10">
						</div>
                                                <div class="has-background">
							<label for="ht_view2_sorting_name_by_asc">Ascedding Sorting Button Name</label>
							<input name="params[ht_view2_sorting_name_by_asc]" type="text" id="ht_view2_sorting_name_by_asc" value="Asceding" size="10">
						</div>
						<div class="">
							<label for="ht_view2_sorting_name_by_desc">Descedding Sorting Button Name</label>
							<input name="params[ht_view2_sorting_name_by_desc]" type="text" id="ht_view2_sorting_name_by_desc" value="Desceding" size="10">
						</div>
                                        </div>
                                        
                                        <div style="margin-top: 14px;">
                                            <h3>Category styles</h3>
                                                <div class="">
                                                    <label for="ht_view2_cat_all">Show All Category Button Name</label>
                                                    <input type="text" name="params[ht_view2_cat_all]" id="ht_view0_cat_all" value="All" class="text" />
                                                </div>                                            
                                            <div style="display: none;">
                                                    <label for="ht_view2_show_filtering" style="display: none;">Show Filtering</label>
                                                    <input type="hidden" value="off" name="params[ht_view2_show_filtering]">
                                                    <input type="checkbox" id="ht_view2_show_filtering" checked="checked" name="params[ht_view2_show_filtering]" value="on">
                                            </div>

                                            <div class="has-background">
                                                    <label for="ht_view2_filterbutton_font_size">Filter Button Font Size</label>
                                                    <input type="text" name="params[ht_view2_filterbutton_font_size]" id="ht_view2_filterbutton_font_size" value="14" class="text">
                                                    <span>px</span>
                                            </div>
                                            <div class="">
                                                    <label for="ht_view2_filterbutton_font_color">Filter Button Font Color</label>
                                                    <input name="params[ht_view2_filterbutton_font_color]" type="text" class="color" id="ht_view2_filterbutton_font_color" value="#555555" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(85, 85, 85);">
                                            </div>
                                            <div class="has-background">
                                                    <label for="ht_view2_filterbutton_hover_font_color">Filter Button Font Hover Color</label>
                                                    <input name="params[ht_view2_filterbutton_hover_font_color]" type="text" class="color" id="ht_view2_filterbutton_hover_font_color" value="#ffffff" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
                                            </div>
                                            <div class="">
                                                    <label for="ht_view2_filterbutton_background_color">Filter Button Background Color</label>
                                                    <input name="params[ht_view2_filterbutton_background_color]" type="text" class="color" id="ht_view2_filterbutton_background_color" value="#F7F7F7" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(247, 247, 247);">
                                            </div>
                                            <div class="has-background">
                                                    <label for="ht_view2_filterbutton_hover_background_color">Filter Button Background Hover Color</label>
                                                    <input name="params[ht_view2_filterbutton_hover_background_color]" type="text" class="color" id="ht_view2_filterbutton_hover_background_color" value="#FF3845" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(255, 56, 69);">
                                            </div>

                                            <div class="" style="display: none;">
                                                    <label for="ht_view2_filterbutton_border_width">Filter Button Border Width</label>
                                                    <input type="text" name="params[ht_view2_filterbutton_border_width]" id="ht_view2_filterbutton_border_width" value="" class="text">
                                                    <span>px</span>
                                            </div>
                                            <div style="display: none;">
                                                    <input name="params[ht_view2_filterbutton_border_color]" type="text" class="color" id="ht_view2_filterbutton_border_color" value="#" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
                                                    <label for="ht_view2_filterbutton_border_color">Filter Button Border Color</label>
                                            </div>
                                                <div class="">
                                                    <label for="ht_view2_filterbutton_border_radius">Filter Button Border Radius</label>
                                                    <input type="text" name="params[ht_view2_filterbutton_border_radius]" id="ht_view2_filterbutton_border_radius" value="0" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div class="has-background">
                                                    <label for="ht_view2_filterbutton_border_padding">Filter Button Padding</label>
                                                    <input type="text" name="params[ht_view2_filterbutton_border_padding]" id="ht_view2_filterbutton_border_padding" value="3" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div style="display: none;">
                                                    <label for="ht_view2_filterbutton_margin">Filter Button Margins</label>
                                                    <input type="text" name="params[ht_view2_filterbutton_margin]" id="ht_view2_filterbutton_margin" value="" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div class="">
                                                    <label for="ht_view2_filtering_float">Filter block Position</label>
                                                    <select id="ht_view2_filtering_float" name="params[ht_view2_filtering_float]">	
                                                      <option selected="selected" value="left">Left</option>
                                                      <option value="right">Right</option>
                                                      <option value="top">Top</option>
                                                    </select>
                                                </div>
                                        </div>
                                        
                                        
                                    <div style="margin-top: 14px;">	
						<h3>Popup Title</h3>
						<div class="has-background">
							<label for="ht_view2_popup_title_font_size">Popup Title Font Size</label>
							<input type="text" name="params[ht_view2_popup_title_font_size]" id="ht_view2_element_title_font_size" value="18" class="text">
							<span>px</span>
						</div>
						<div>
							<label for="ht_view2_popup_title_font_color">Popup Title Font Color</label>
							<input name="params[ht_view2_popup_title_font_color]" type="text" class="color" id="ht_view2_element_title_font_color" value="#222222" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(34, 34, 34);">
						</div>
					</div>
					<div style="margin-top: 14px;">
						<h3>Popup Thumbnails</h3>
						<div class="has-background">
							<label for="ht_view2_show_thumbs">Show Thumbnails</label>
							<input type="hidden" value="off" name="params[ht_view2_show_thumbs]">
							<input type="checkbox" id="ht_view2_show_thumbs" checked="checked" name="params[ht_view2_show_thumbs]" value="on">
						</div>
						<div>
							<label for="ht_view2_thumbs_position">Thumbnails Position</label>
							<select id="ht_view2_thumbs_position" name="params[ht_view2_thumbs_position]">	
							  <option selected="selected" value="before">Before Description</option>
							  <option value="after">After Description</option>
							</select>
						</div>
						<div class="has-background">
							<label for="ht_view2_thumbs_width">Thumbnails Width</label>
							<input type="text" name="params[ht_view2_thumbs_width]" id="ht_view2_thumbs_width" value="75" class="text">
							<span>px</span>
						</div>
						<div>
							<label for="ht_view2_thumbs_height">Thumbnails Width</label>
							<input type="text" name="params[ht_view2_thumbs_height]" id="ht_view2_thumbs_height" value="75" class="text">
							<span>px</span>
						</div>
					</div>
                                        <div style="margin-top: -224px;">
						<h3>Popup Description</h3>
						<div class="has-background">
							<label for="ht_view2_show_description">Show Description</label>
							<input type="hidden" value="off" name="params[ht_view2_show_description]">
							<input type="checkbox" id="ht_view2_show_description" checked="checked" name="params[ht_view2_show_description]" value="on">
						</div>
						<div>
							<label for="ht_view2_description_font_size">Description Font Size</label>
							<input type="text" name="params[ht_view2_description_font_size]" id="ht_view2_description_font_size" value="14" class="text">
							<span>px</span>
						</div>
						<div class="has-background">
							<label for="ht_view2_description_color">Description Font Color</label>
							<input name="params[ht_view2_description_color]" type="text" class="color" id="ht_view2_description_color" value="#222222" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(34, 34, 34);">
						</div>
					</div>
					<div style="margin-top: -10px;">
						<h3>Popup Link Button</h3>
						<div class="has-background">
							<label for="ht_view2_show_popup_linkbutton">Show Link Button</label>
							<input type="hidden" value="off" name="params[ht_view2_show_popup_linkbutton]">
							<input type="checkbox" id="ht_view2_show_popup_linkbutton" checked="checked" name="params[ht_view2_show_popup_linkbutton]" value="on">
						</div>
						<div>
							<label for="ht_view2_popup_linkbutton_text">Link Button Text</label>
							<input type="text" name="params[ht_view2_popup_linkbutton_text]" id="ht_view2_popup_linkbutton_text" value="View More" class="text">
						</div>
						<div class="has-background">
							<label for="ht_view2_popup_linkbutton_font_size">Link Button Font Size</label>
							<input type="text" name="params[ht_view2_popup_linkbutton_font_size]" id="ht_view2_popup_linkbutton_font_size" value="14" class="text">
							<span>px</span>
						</div>
						<div>
							<label for="ht_view2_popup_linkbutton_color">Link Button Font Color</label>
							<input name="params[ht_view2_popup_linkbutton_color]" type="text" class="color" id="ht_view2_popup_linkbutton_color" value="#ffffff" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
						</div>
						<div class="has-background">
							<label for="ht_view2_popup_linkbutton_font_hover_color">Link Button Font Hover Color</label>
							<input name="params[ht_view2_popup_linkbutton_font_hover_color]" type="text" class="color" id="ht_view2_popup_linkbutton_font_hover_color" value="#ffffff" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
						</div>
						<div>
							<label for="ht_view2_popup_linkbutton_background_color">Link Button Background Color</label>
							<input name="params[ht_view2_popup_linkbutton_background_color]" type="text" class="color" id="ht_view2_popup_linkbutton_background_color" value="#2ea2cd" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(46, 162, 205);">
						</div>
						<div class="has-background">
							<label for="ht_view2_popup_linkbutton_background_hover_color">Link Button Background Hover Color</label>
							<input name="params[ht_view2_popup_linkbutton_background_hover_color]" type="text" class="color" id="ht_view2_popup_linkbutton_background_hover_color" value="#0074a2" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(0, 116, 162);">
						</div>
					</div>
                                    
                                        <div style="margin-top: 14px;">
						<h3>Popup Styles</h3>
                                                <div class="has-background">
							<label for="ht_view2_popup_full_width">Popup Image Full Width</label>
							<input type="hidden" value="off" name="params[ht_view2_popup_full_width]">
							<input type="checkbox" id="ht_view2_popup_full_width" checked="checked" name="params[ht_view2_popup_full_width]" value="on">
						</div>
						<div class="">
							<label for="ht_view2_popup_background_color">Popup Background Color</label>
							<input name="params[ht_view2_popup_background_color]" type="text" class="color" id="ht_view2_popup_background_color" value="#FFFFFF" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
						</div>
						<div class="has-background">
							<label for="ht_view2_popup_overlay_color">Popup Overlay Color</label>
							<input name="params[ht_view2_popup_overlay_color]" type="text" class="color" id="ht_view2_popup_overlay_color" value="#000000" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">
						</div>
						<div class="">
							<label for="ht_view2_popup_overlay_transparency_color">Popup Overlay Transparency</label>
							<div class="slider-container">
								<div class="slider" id="ht_view2_popup_overlay_transparency_color-slider" style="position: relative; -webkit-user-select: none; box-sizing: border-box; min-height: 14px; margin-left: 7px; margin-right: 7px;"><div class="track" style="position: absolute; top: 50%; -webkit-user-select: none; cursor: pointer; width: 100%; margin-top: -3px;"></div><div class="highlight-track" style="position: absolute; top: 50%; -webkit-user-select: none; cursor: pointer; width: 66.5px; margin-top: -3px;"></div><div class="dragger" style="position: absolute; top: 50%; -webkit-user-select: none; cursor: pointer; margin-top: -7px; margin-left: -7px; left: 66.5px;"></div></div><input name="params[ht_view2_popup_overlay_transparency_color]" id="ht_view2_popup_overlay_transparency_color" data-slider-highlight="true" data-slider-values="0,10,20,30,40,50,60,70,80,90,100" type="text" data-slider="true" value="70" style="display: none;">
								<span>70%</span>
							</div>
						</div>
						<div class="has-background">
							<label for="ht_view2_popup_closebutton_style">Popup Close Button Style</label>
							<select id="ht_view2_popup_closebutton_style" name="params[ht_view2_popup_closebutton_style]">	
							  <option value="light">Light</option>
							  <option selected="selected" value="dark">Dark</option>
							</select>
						</div>
						<div class="">
							<label for="ht_view2_show_separator_lines">Show Separator Lines</label>
							<input type="hidden" value="off" name="params[ht_view2_show_separator_lines]">
							<input type="checkbox" id="ht_view2_show_separator_lines" checked="checked" name="params[ht_view2_show_separator_lines]" value="on">
						</div>
                                                
					</div>
                                        
                                    
				</li>	
				<!-- VIEW 3 Fullwidth -->
				<li id="portfolio-view-options-3">
					<div>
						<h3>Elements Styles</h3>
						<div class="has-background">
							<label for="ht_view3_mainimage_width">Main Image Width</label>
							<input type="text" name="params[ht_view3_mainimage_width]" id="ht_view3_mainimage_width" value="240" class="text">
							<span>px</span>
						</div>
						<div>
							<label for="ht_view3_element_background_color">Element Background Color</label>
							<input name="params[ht_view3_element_background_color]" type="text" class="color" id="ht_view3_element_background_color" value="#f9f9f9" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(249, 249, 249);">
						</div>
						<div class="has-background">
							<label for="ht_view3_element_border_width">Element Border Width</label>
							<input type="text" name="params[ht_view3_element_border_width]" id="ht_view3_element_border_width" value="1" class="text">
							<span>px</span>
						</div>
						<div>
							<label for="ht_view3_element_border_color">Element Border Color</label>
							<input name="params[ht_view3_element_border_color]" type="text" class="color" id="ht_view3_element_border_color" value="#dedede" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(222, 222, 222);">
						</div>
						<div class="has-background">
							<label for="ht_view3_show_separator_lines">Show Separator Lines</label>
							<input type="hidden" value="off" name="params[ht_view3_show_separator_lines]">
							<input type="checkbox" id="ht_view3_show_separator_lines" checked="checked" name="params[ht_view3_show_separator_lines]" value="on">
						</div>
					</div>
					<div>
						<h3>Title</h3>
						<div class="has-background">
							<label for="ht_view3_title_font_size">Title Font Size</label>
							<input type="text" name="params[ht_view3_title_font_size]" id="ht_view3_title_font_size" value="18" class="text">
							<span>px</span>
						</div>
						<div>
							<label for="ht_view3_title_font_color">Title Font Color</label>
							<input name="params[ht_view3_title_font_color]" type="text" class="color" id="ht_view3_title_font_color" value="#0074a2" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(0, 116, 162);">
						</div>
					</div>
					<div>
						<h3>Thumbnails</h3>
						<div class="has-background">
							<label for="ht_view3_show_thumbs">Show Thumbnails</label>
							<input type="hidden" value="off" name="params[ht_view3_show_thumbs]">
							<input type="checkbox" id="ht_view3_show_thumbs" checked="checked" name="params[ht_view3_show_thumbs]" value="on">
						</div>
						<div>
							<label for="ht_view3_thumbs_width">Thumbnails Width</label>
							<input type="text" name="params[ht_view3_thumbs_width]" id="ht_view3_thumbs_width" value="75" class="text">
							<span>px</span>
						</div>
						<div class="has-background">
							<label for="ht_view3_thumbs_height">Thumbnails Width</label>
							<input type="text" name="params[ht_view3_thumbs_height]" id="ht_view3_thumbs_height" value="75" class="text">
							<span>px</span>
						</div>
					</div>
                                        
                                        <div style="margin-top:-80px;">
                                            <h3>Sorting styles</h3>
                                            <div class="" style="display: none;">
                                                    <label for="ht_view3_show_sorting" style="display: none;">Show Sorting</label>
                                                    <input type="hidden" value="off" name="params[ht_view3_show_sorting]">
                                                    <input type="checkbox" id="ht_view3_show_sorting" checked="checked" name="params[ht_view3_show_sorting]" value="on">
                                            </div>
                                            <div class="has-background">
                                                    <label for="ht_view3_sortbutton_font_size">Sort Button Font Size</label>
                                                    <input type="text" name="params[ht_view3_sortbutton_font_size]" id="ht_view3_sortbutton_font_size" value="14" class="text">
                                                    <span>px</span>
                                            </div>
                                            <div class="">
                                                    <label for="ht_view3_sortbutton_font_color">Sort Button Font Color</label>
                                                    <input name="params[ht_view3_sortbutton_font_color]" type="text" class="color" id="ht_view3_sortbutton_font_color" value="#555555" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(85, 85, 85);">
                                            </div>
                                            <div class="has-background">
                                                    <label for="ht_view3_sortbutton_hover_font_color">Sort Button Font Hover Color</label>
                                                    <input name="params[ht_view3_sortbutton_hover_font_color]" type="text" class="color" id="ht_view3_sortbutton_hover_font_color" value="#ffffff" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
                                            </div>
                                            <div class="">
                                                    <label for="ht_view3_sortbutton_background_color">Sort Button Background Color</label>
                                                    <input name="params[ht_view3_sortbutton_background_color]" type="text" class="color" id="ht_view3_sortbutton_background_color" value="#F7F7F7" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(247, 247, 247);">
                                            </div>
                                            <div class="has-background">
                                                    <label for="ht_view3_sortbutton_hover_background_color">Sort Button Background Hover Color</label>
                                                    <input name="params[ht_view3_sortbutton_hover_background_color]" type="text" class="color" id="ht_view3_sortbutton_hover_background_color" value="#FF3845" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(255, 56, 69);">
                                            </div>
                                            <div class="" style="display: none;">
                                                    <label for="ht_view3_sortbutton_border_width">Sort Button Border Width</label>
                                                    <input type="text" name="params[ht_view3_sortbutton_border_width]" id="ht_view3_sortbutton_border_width" value="" class="text">
                                                    <span>px</span>
                                            </div>
                                            <div style="display: none;">
                                                    <input name="params[ht_view3_sortbutton_border_color]" type="text" class="color" id="ht_view3_sortbutton_border_color" value="#" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
                                                    <label for="ht_view3_sortbutton_border_color">Sort Button Border Color</label>
                                            </div>
                                                <div class="">
                                                    <label for="ht_view3_sortbutton_border_radius">Sort Button Border Radius</label>
                                                    <input type="text" name="params[ht_view3_sortbutton_border_radius]" id="ht_view3_sortbutton_border_radius" value="0" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div class="has-background">
                                                    <label for="ht_view3_sortbutton_border_padding">Sort Button Padding</label>
                                                    <input type="text" name="params[ht_view3_sortbutton_border_padding]" id="ht_view3_sortbutton_border_padding" value="3" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div style="display: none;">
                                                    <label for="ht_view3_sortbutton_margin">Sort Button Margins</label>
                                                    <input type="text" name="params[ht_view3_sortbutton_margin]" id="ht_view3_sortbutton_margin" value="" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div class="">
                                                    <label for="ht_view3_sorting_float">Sort block Position</label>
                                                    <select id="ht_view3_sorting_float" name="params[ht_view3_sorting_float]">	
                                                      <option value="left">Left</option>
                                                      <option value="right">Right</option>
                                                      <option selected="selected" value="top">Top</option>
                                                    </select>
						</div>
                                                <div class="has-background">
							<label for="ht_view3_sorting_name_by_default">Sort By Default Button Name</label>
							<input name="params[ht_view3_sorting_name_by_default]" type="text" id="ht_view3_sorting_name_by_default" value="Default" size="10" class="text">
						</div>
						<div class="">
							<label for="ht_view3_sorting_name_by_id">Sorting By ID Button Name</label>
							<input name="params[ht_view3_sorting_name_by_id]" type="text" id="ht_view3_sorting_name_by_id" value="Date" size="10">
						</div>
                                                <div class="has-background">
							<label for="ht_view3_sorting_name_by_name">Sorting By ID Button Name</label>
							<input name="params[ht_view3_sorting_name_by_name]" type="text" id="ht_view3_sorting_name_by_name" value="Title" size="10">
						</div>
						<div class="">
							<label for="ht_view3_sorting_name_by_random">Random Sorting Button Name</label>
							<input name="params[ht_view3_sorting_name_by_random]" type="text" id="ht_view3_sorting_name_by_random" value="Random" size="10">
						</div>
                                                <div class="has-background">
							<label for="ht_view3_sorting_name_by_asc">Ascedding Sorting Button Name</label>
							<input name="params[ht_view3_sorting_name_by_asc]" type="text" id="ht_view3_sorting_name_by_asc" value="Asceding" size="10">
						</div>
						<div class="">
							<label for="ht_view3_sorting_name_by_desc">Descedding Sorting Button Name</label>
							<input name="params[ht_view3_sorting_name_by_desc]" type="text" id="ht_view3_sorting_name_by_desc" value="Desceding" size="10">
						</div>
                                        </div>
                                        
                                        <div style="margin-top: 14px;">
                                            <h3>Category styles</h3>
                                                 <div class="">
                                                    <label for="ht_view3_cat_all">Show All Category Button Name</label>
                                                    <input type="text" name="params[ht_view3_cat_all]" id="ht_view3_cat_all" value="All" class="text" />
                                                </div>                                           
                                            <div style="display: none;">
                                                    <label for="ht_view3_show_filtering" style="display: none;">Show Filtering</label>
                                                    <input type="hidden" value="off" name="params[ht_view3_show_filtering]">
                                                    <input type="checkbox" id="ht_view3_show_filtering" checked="checked" name="params[ht_view3_show_filtering]" value="on">
                                            </div>

                                            <div class="has-background">
                                                    <label for="ht_view3_filterbutton_font_size">Filter Button Font Size</label>
                                                    <input type="text" name="params[ht_view3_filterbutton_font_size]" id="ht_view3_filterbutton_font_size" value="14" class="text">
                                                    <span>px</span>
                                            </div>
                                            <div>
                                                    <label for="ht_view3_filterbutton_font_color">Filter Button Font Color</label>
                                                    <input name="params[ht_view3_filterbutton_font_color]" type="text" class="color" id="ht_view3_filterbutton_font_color" value="#555555" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(85, 85, 85);">
                                            </div>
                                            <div class="has-background">
                                                    <label for="ht_view3_filterbutton_hover_font_color">Filter Button Font Hover Color</label>
                                                    <input name="params[ht_view3_filterbutton_hover_font_color]" type="text" class="color" id="ht_view3_filterbutton_hover_font_color" value="#ffffff" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
                                            </div>
                                            <div class="">
                                                    <label for="ht_view3_filterbutton_background_color">Filter Button Background Color</label>
                                                    <input name="params[ht_view3_filterbutton_background_color]" type="text" class="color" id="ht_view3_filterbutton_background_color" value="#F7F7F7" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(247, 247, 247);">
                                            </div>
                                            <div class="has-background">
                                                    <label for="ht_view3_filterbutton_hover_background_color">Filter Button Background Hover Color</label>
                                                    <input name="params[ht_view3_filterbutton_hover_background_color]" type="text" class="color" id="ht_view3_filterbutton_hover_background_color" value="#FF3845" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(255, 56, 69);">
                                            </div>
                                            <div class="has-background" style="display: none;">
                                                    <label for="ht_view3_filterbutton_border_width">Filter Button Border Width</label>
                                                    <input type="text" name="params[ht_view3_filterbutton_border_width]" id="ht_view3_filterbutton_border_width" value="" class="text">
                                                    <span>px</span>
                                            </div>
                                            <div style="display: none;">
                                                    <input name="params[ht_view3_filterbutton_border_color]" type="text" class="color" id="ht_view3_filterbutton_border_color" value="#" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
                                                    <label for="ht_view3_filterbutton_border_color">Filter Button Border Color</label>
                                            </div>
                                                <div class="">
                                                    <label for="ht_view3_filterbutton_border_radius">Filter Button Border Radius</label>
                                                    <input type="text" name="params[ht_view3_filterbutton_border_radius]" id="ht_view3_filterbutton_border_radius" value="0" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div class="has-background">
                                                    <label for="ht_view3_filterbutton_border_padding">Filter Button Padding</label>
                                                    <input type="text" name="params[ht_view3_filterbutton_border_padding]" id="ht_view3_filterbutton_border_padding" value="3" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div style="display: none;">
                                                    <label for="ht_view3_filterbutton_margin">Filter Button Margins</label>
                                                    <input type="text" name="params[ht_view3_filterbutton_margin]" id="ht_view3_filterbutton_margin" value="" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div class="">
                                                    <label for="ht_view3_filtering_float">Filter block Position</label>
                                                    <select id="ht_view3_filtering_float" name="params[ht_view3_filtering_float]">	
                                                      <option selected="selected" value="left">Left</option>
                                                      <option value="right">Right</option>
                                                      <option value="top">Top</option>
                                                    </select>
                                                </div>
                                        </div>
                                        
					<div>
						<h3>Description</h3>
						<div class="has-background">
							<label for="ht_view3_show_description">Show Description</label>
							<input type="hidden" value="off" name="params[ht_view3_show_description]">
							<input type="checkbox" id="ht_view3_show_description" checked="checked" name="params[ht_view3_show_description]" value="on">
						</div>
						<div>
							<label for="ht_view3_description_font_size">Description Font Size</label>
							<input type="text" name="params[ht_view3_description_font_size]" id="ht_view3_description_font_size" value="14" class="text">
							<span>px</span>
						</div>
						<div class="has-background">
							<label for="ht_view3_description_color">Description Font Color</label>
							<input name="params[ht_view3_description_color]" type="text" class="color" id="ht_view3_description_color" value="#555555" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(85, 85, 85);">
						</div>
					</div>
					<div style="margin-top: -50px;">
						<h3>Link Button</h3>
						<div class="has-background">
							<label for="ht_view3_show_linkbutton">Show Link Button</label>
							<input type="hidden" value="off" name="params[ht_view3_show_linkbutton]">
							<input type="checkbox" id="ht_view3_show_linkbutton" checked="checked" name="params[ht_view3_show_linkbutton]" value="on">
						</div>
						<div>
							<label for="ht_view3_linkbutton_text">Link Button Text</label>
							<input type="text" name="params[ht_view3_linkbutton_text]" id="ht_view3_linkbutton_text" value="View More" class="text">
						</div>
						<div class="has-background">
							<label for="ht_view3_linkbutton_font_size">Link Button Font Size</label>
							<input type="text" name="params[ht_view3_linkbutton_font_size]" id="ht_view3_linkbutton_font_size" value="14" class="text">
							<span>px</span>
						</div>
						<div>
							<label for="ht_view3_linkbutton_color">Link Button Font Color</label>
							<input name="params[ht_view3_linkbutton_color]" type="text" class="color" id="ht_view3_linkbutton_color" value="#ffffff" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
						</div>
						<div class="has-background">
							<label for="ht_view3_linkbutton_font_hover_color">Link Button Font Hover Color</label>
							<input name="params[ht_view3_linkbutton_font_hover_color]" type="text" class="color" id="ht_view3_linkbutton_font_hover_color" value="#ffffff" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
						</div>
						<div>
							<label for="ht_view3_linkbutton_background_color">Link Button Background Color</label>
							<input name="params[ht_view3_linkbutton_background_color]" type="text" class="color" id="ht_view3_linkbutton_background_color" value="#2ea2cd" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(46, 162, 205);">
						</div>
						<div class="has-background">
							<label for="ht_view3_linkbutton_background_hover_color">Link Button Background Hover Color</label>
							<input name="params[ht_view3_linkbutton_background_hover_color]" type="text" class="color" id="ht_view3_linkbutton_background_hover_color" value="#0074a2" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(0, 116, 162);">
						</div>
					</div>
				</li>
				
				<!-- VIEW 4 FAQ  -->
				<li id="portfolio-view-options-4">
					<div>
						<h3>First Shown Block</h3>
						<div class="has-background">
							<label for="ht_view4_block_width">Block Width</label>
							<input type="text" name="params[ht_view4_block_width]" id="ht_view4_block_width" value="275" class="text">
							<span>px</span>
						</div>
						<div>
							<label for="ht_view4_element_background_color">Block Background Color</label>
							<input name="params[ht_view4_element_background_color]" type="text" class="color" id="ht_view4_element_background_color" value="#f9f9f9" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(249, 249, 249);">
						</div>
						<div class="has-background">
							<label for="ht_view4_element_border_width">Block Border Width</label>
							<input type="text" name="params[ht_view4_element_border_width]" id="ht_view4_element_border_width" value="1" class="text">
							<span>px</span>
						</div>
						<div>
							<label for="ht_view4_element_border_color">Block Border Color</label>
							<input name="params[ht_view4_element_border_color]" type="text" class="color" id="ht_view4_element_border_color" value="#dedede" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(222, 222, 222);">
						</div>
					</div>
					<div>
						<h3>Title</h3>
						<div class="has-background">
							<label for="ht_view4_title_font_size">Title Font Size</label>
							<input type="text" name="params[ht_view4_title_font_size]" id="ht_view4_title_font_size" value="18" class="text">
							<span>px</span>
						</div>
						<div>
							<label for="ht_view4_title_font_color">Title Font Color</label>
							<input name="params[ht_view4_title_font_color]" type="text" class="color" id="ht_view4_title_font_color" value="#E74C3C" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(231, 76, 60);">
						</div>
						<div class="has-background">
							<label for="ht_view4_togglebutton_style">Toggle Button Style</label>
							<select id="ht_view4_togglebutton_style" name="params[ht_view4_togglebutton_style]">	
							  <option value="light">Light</option>
							  <option selected="selected" value="dark">Dark</option>
							</select>
						</div>
					</div>
					
                                        <div style="margin-top: 14px;">
                                            <h3>Sorting styles</h3>
                                            <div class="" style="display: none;">
                                                    <label for="ht_view4_show_sorting" style="display: none;">Show Sorting</label>
                                                    <input type="hidden" value="off" name="params[ht_view4_show_sorting]">
                                                    <input type="checkbox" id="ht_view4_show_sorting" checked="checked" name="params[ht_view4_show_sorting]" value="on">
                                            </div>

                                            <div class="has-background">
                                                    <label for="ht_view4_sortbutton_font_size">Sort Button Font Size</label>
                                                    <input type="text" name="params[ht_view4_sortbutton_font_size]" id="ht_view4_sortbutton_font_size" value="14" class="text">
                                                    <span>px</span>
                                            </div>
                                            <div class="">
                                                    <label for="ht_view4_sortbutton_font_color">Sort Button Font Color</label>
                                                    <input name="params[ht_view4_sortbutton_font_color]" type="text" class="color" id="ht_view4_sortbutton_font_color" value="#555555" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(85, 85, 85);">
                                            </div>
                                            <div class="has-background">
                                                    <label for="ht_view4_sortbutton_hover_font_color">Sort Button Font Hover Color</label>
                                                    <input name="params[ht_view4_sortbutton_hover_font_color]" type="text" class="color" id="ht_view4_sortbutton_hover_font_color" value="#ffffff" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
                                            </div>
                                            <div class="">
                                                    <label for="ht_view4_sortbutton_background_color">Sort Button Background Color</label>
                                                    <input name="params[ht_view4_sortbutton_background_color]" type="text" class="color" id="ht_view4_sortbutton_background_color" value="#F7F7F7" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(247, 247, 247);">
                                            </div>
                                            <div class="has-background">
                                                    <label for="ht_view4_sortbutton_hover_background_color">Sort Button Background Hover Color</label>
                                                    <input name="params[ht_view4_sortbutton_hover_background_color]" type="text" class="color" id="ht_view4_sortbutton_hover_background_color" value="#FF3845" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(255, 56, 69);">
                                            </div>
                                            <div class="has-background" style="display: none;">
                                                    <label for="ht_view4_sortbutton_border_width">Sort Button Border Width</label>
                                                    <input type="text" name="params[ht_view4_sortbutton_border_width]" id="ht_view4_sortbutton_border_width" value="" class="text">
                                                    <span>px</span>
                                            </div>
                                            <div style="display: none;">
                                                    <input name="params[ht_view4_sortbutton_border_color]" type="text" class="color" id="ht_view4_sortbutton_border_color" value="#" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
                                                    <label for="ht_view4_sortbutton_border_color">Sort Button Border Color</label>
                                            </div>
                                                <div class="">
                                                    <label for="ht_view4_sortbutton_border_radius">Sort Button Border Radius</label>
                                                    <input type="text" name="params[ht_view4_sortbutton_border_radius]" id="ht_view4_sortbutton_border_radius" value="0" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div class="has-background">
                                                    <label for="ht_view4_sortbutton_border_padding">Sort Button Padding</label>
                                                    <input type="text" name="params[ht_view4_sortbutton_border_padding]" id="ht_view4_sortbutton_border_padding" value="3" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div style="display: none;">
                                                    <label for="ht_view4_sortbutton_margin">Sort Button Margins</label>
                                                    <input type="text" name="params[ht_view4_sortbutton_margin]" id="ht_view4_sortbutton_margin" value="" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div class="">
                                                    <label for="ht_view4_sorting_float">Sort block Position</label>
                                                    <select id="ht_view4_sorting_float" name="params[ht_view4_sorting_float]">	
                                                      <option value="left">Left</option>
                                                      <option value="right">Right</option>
                                                      <option selected="selected" value="top">Top</option>
                                                    </select>
						</div>
                                                <div class="has-background">
                                                        <label for="ht_view4_sorting_name_by_default">Sort By Default Button Name</label>
                                                        <input name="params[ht_view4_sorting_name_by_default]" type="text" id="ht_view4_sorting_name_by_default" value="Default" size="10" class="text">
                                                </div>
                                                <div class="">
                                                        <label for="ht_view4_sorting_name_by_id">Sorting By ID Button Name</label>
                                                        <input name="params[ht_view4_sorting_name_by_id]" type="text" id="ht_view4_sorting_name_by_id" value="Date" size="10">
                                                </div>
                                                <div class="has-background">
                                                        <label for="ht_view4_sorting_name_by_name">Sorting By ID Button Name</label>
                                                        <input name="params[ht_view4_sorting_name_by_name]" type="text" id="ht_view4_sorting_name_by_name" value="Title" size="10">
                                                </div>
                                                <div class="">
                                                        <label for="ht_view4_sorting_name_by_random">Random Sorting Button Name</label>
                                                        <input name="params[ht_view4_sorting_name_by_random]" type="text" id="ht_view4_sorting_name_by_random" value="Random" size="10">
                                                </div>
                                                <div class="has-background">
                                                        <label for="ht_view4_sorting_name_by_asc">Ascedding Sorting Button Name</label>
                                                        <input name="params[ht_view4_sorting_name_by_asc]" type="text" id="ht_view4_sorting_name_by_asc" value="Asceding" size="10">
                                                </div>
                                                <div class="">
                                                        <label for="ht_view4_sorting_name_by_desc">Descedding Sorting Button Name</label>
                                                        <input name="params[ht_view4_sorting_name_by_desc]" type="text" id="ht_view4_sorting_name_by_desc" value="Desceding" size="10">
                                                </div>
                                            </div>
                                    
                                            <div style="margin-top: -600px;">
                                            <h3>Category styles</h3>
                                                <div class="" style="display: none;">
                                                    <label for="ht_view4_show_filtering" style="display: none;">Show Filtering</label>
                                                    <input type="hidden" value="off" name="params[ht_view4_show_filtering]">
                                                    <input type="checkbox" id="ht_view4_show_filtering" checked="checked" name="params[ht_view4_show_filtering]" value="on">
                                                </div>
                                                <div class="">
                                                    <label for="ht_view4_cat_all">Show All Category Button Name</label>
                                                    <input type="text" name="params[ht_view4_cat_all]" id="ht_view4_cat_all" value="All" class="text" />
                                                </div>												
                                                <div class="has-background">
                                                        <label for="ht_view4_filterbutton_font_size">Filter Button Font Size</label>
                                                        <input type="text" name="params[ht_view4_filterbutton_font_size]" id="ht_view4_filterbutton_font_size" value="14" class="text">
                                                        <span>px</span>
                                                </div>
                                                <div class="">
                                                        <label for="ht_view4_filterbutton_font_color">Filter Button Font Color</label>
                                                        <input name="params[ht_view4_filterbutton_font_color]" type="text" class="color" id="ht_view4_filterbutton_font_color" value="#555555" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(85, 85, 85);">
                                                </div>
                                                <div class="has-background">
                                                        <label for="ht_view4_filterbutton_hover_font_color">Filter Button Font Hover Color</label>
                                                        <input name="params[ht_view4_filterbutton_hover_font_color]" type="text" class="color" id="ht_view4_filterbutton_hover_font_color" value="#ffffff" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
                                                </div>
                                                <div class="">
                                                        <label for="ht_view4_filterbutton_background_color">Filter Button Background Color</label>
                                                        <input name="params[ht_view4_filterbutton_background_color]" type="text" class="color" id="ht_view4_filterbutton_background_color" value="#F7F7F7" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(247, 247, 247);">
                                                </div>
                                                <div class="has-background">
                                                        <label for="ht_view4_filterbutton_hover_background_color">Filter Button Background Hover Color</label>
                                                        <input name="params[ht_view4_filterbutton_hover_background_color]" type="text" class="color" id="ht_view4_filterbutton_hover_background_color" value="#FF3845" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(255, 56, 69);">
                                                </div>
                                                <div class="" style="display: none;">
                                                        <label for="ht_view4_filterbutton_border_width">Filter Button Border Width</label>
                                                        <input type="text" name="params[ht_view4_filterbutton_border_width]" id="ht_view4_filterbutton_border_width" value="" class="text">
                                                        <span>px</span>
                                                </div>
                                                <div style="display: none;">
                                                        <input name="params[ht_view4_filterbutton_border_color]" type="text" class="color" id="ht_view4_filterbutton_border_color" value="#" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
                                                        <label for="ht_view4_filterbutton_border_color">Filter Button Border Color</label>
                                                </div>
                                                <div class="">
                                                    <label for="ht_view4_filterbutton_border_radius">Filter Button Border Radius</label>
                                                    <input type="text" name="params[ht_view4_filterbutton_border_radius]" id="ht_view4_filterbutton_border_radius" value="0" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div class="has-background">
                                                    <label for="ht_view4_filterbutton_border_padding">Filter Button Padding</label>
                                                    <input type="text" name="params[ht_view4_filterbutton_border_padding]" id="ht_view4_filterbutton_border_padding" value="3" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div style="display: none;">
                                                    <label for="ht_view4_filterbutton_margin">Filter Button Margins</label>
                                                    <input type="text" name="params[ht_view4_filterbutton_margin]" id="ht_view4_filterbutton_margin" value="" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div class="">
                                                    <label for="ht_view4_filtering_float">Filter block Position</label>
                                                    <select id="ht_view4_filtering_float" name="params[ht_view4_filtering_float]">	
                                                      <option selected="selected" value="left">Left</option>
                                                      <option value="right">Right</option>
                                                      <option value="top">Top</option>
                                                    </select>
                                                </div>
                                            </div>
                                    
                                            <div style="margin-top: -186px;">
						<h3>Link Button</h3>
						<div class="has-background">
							<label for="ht_view4_show_linkbutton">Show Link Button</label>
							<input type="hidden" value="off" name="params[ht_view4_show_linkbutton]">
							<input type="checkbox" id="ht_view4_show_linkbutton" checked="checked" name="params[ht_view4_show_linkbutton]" value="on">
						</div>
						<div>
							<label for="ht_view4_linkbutton_text">Link Button Text</label>
							<input type="text" name="params[ht_view4_linkbutton_text]" id="ht_view4_linkbutton_text" value="View More" class="text">
						</div>
						<div class="has-background">
							<label for="ht_view4_linkbutton_font_size">Link Button Font Size</label>
							<input type="text" name="params[ht_view4_linkbutton_font_size]" id="ht_view4_linkbutton_font_size" value="14" class="text">
							<span>px</span>
						</div>
						<div>
							<label for="ht_view4_linkbutton_color">Link Button Font Color</label>
							<input name="params[ht_view4_linkbutton_color]" type="text" class="color" id="ht_view4_linkbutton_color" value="#ffffff" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
						</div>
						<div class="has-background">
							<label for="ht_view4_linkbutton_font_hover_color">Link Button Font Hover Color</label>
							<input name="params[ht_view4_linkbutton_font_hover_color]" type="text" class="color" id="ht_view4_linkbutton_font_hover_color" value="#ffffff" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
						</div>
						<div>
							<label for="ht_view4_linkbutton_background_color">Link Button Background Color</label>
							<input name="params[ht_view4_linkbutton_background_color]" type="text" class="color" id="ht_view4_linkbutton_background_color" value="#e74c3c" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(231, 76, 60);">
						</div>
						<div class="has-background">
							<label for="ht_view4_linkbutton_background_hover_color">Link Button Background Hover Color</label>
							<input name="params[ht_view4_linkbutton_background_hover_color]" type="text" class="color" id="ht_view4_linkbutton_background_hover_color" value="#df2e1b" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(223, 46, 27);">
						</div>
					</div>
                                    
                                        <div>
						<h3>Description</h3>
						<div class="has-background">
							<label for="ht_view4_show_description">Show Description</label>
							<input type="hidden" value="off" name="params[ht_view4_show_description]">
							<input type="checkbox" id="ht_view4_show_description" checked="checked" name="params[ht_view4_show_description]" value="on">
						</div>
						<div>
							<label for="ht_view4_description_font_size">Description Font Size</label>
							<input type="text" name="params[ht_view4_description_font_size]" id="ht_view4_description_font_size" value="14" class="text">
							<span>px</span>
						</div>
						<div class="has-background">
							<label for="ht_view4_description_color">Description Font Color</label>
							<input name="params[ht_view4_description_color]" type="text" class="color" id="ht_view4_description_color" value="#555555" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(85, 85, 85);">
						</div>
					</div>
					
				</li>
				<!-- View 5 Slider -->
				<li id="portfolio-view-options-5">
					<div>
						<h3>Slider</h3>					
						<div class="has-background">
							<label for="ht_view5_slider_background_color">Slider Background Color</label>
							<input name="params[ht_view5_slider_background_color]" type="text" class="color" id="ht_view5_slider_background_color" value="#f9f9f9" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(249, 249, 249);">
						</div>
						<div>
							<label for="ht_view5_icons_style">Icons Style</label>
							<select id="ht_view5_icons_style" name="params[ht_view5_icons_style]">	
							  <option value="light">Light</option>
							  <option selected="selected" value="dark">Dark</option>
							</select>
						</div>
						<div class="has-background">
							<label for="ht_view5_show_separator_lines">Show Separator Lines</label>
							<input type="hidden" value="off" name="params[ht_view5_show_separator_lines]">
							<input type="checkbox" id="ht_view5_show_separator_lines" checked="checked" name="params[ht_view5_show_separator_lines]" value="on">
						</div>
					</div>
					<div>
						<h3>Images</h3>
						<div class="has-background">
							<label for="ht_view5_main_image_width">Main Image Width</label>
							<input type="text" name="params[ht_view5_main_image_width]" id="ht_view5_main_image_width" value="275" class="text">
							<span>px</span>
						</div>
						<div>
							<label for="ht_view5_show_thumbs">Show Thumbs</label>
							<input type="hidden" value="off" name="params[ht_view5_show_thumbs]">
							<input type="checkbox" id="ht_view5_show_thumbs" checked="checked" name="params[ht_view5_show_thumbs]" value="on">
						</div>		
						<div class="has-background">
							<label for="ht_view5_thumbs_width">Thumbs Width</label>
							<input type="text" name="params[ht_view5_thumbs_width]" id="ht_view5_thumbs_width" value="75" class="text">
							<span>px</span>
						</div>
						<div>
							<label for="ht_view5_thumbs_height">Thumbs Height</label>
							<input type="text" name="params[ht_view5_thumbs_height]" id="ht_view5_thumbs_height" value="75" class="text">
							<span>px</span>
						</div>
					</div>
					<div style="margin-top:-30px;">
						<h3>Title</h3>
						<div class="has-background">
							<label for="ht_view5_title_font_size">Title Font Size</label>
							<input type="text" name="params[ht_view5_title_font_size]" id="ht_view5_title_font_size" value="16" class="text">
							<span>px</span>
						</div>
						<div>
							<label for="ht_view5_title_font_color">Title Font Color</label>
							<input name="params[ht_view5_title_font_color]" type="text" class="color" id="ht_view5_title_font_color" value="#0074a2" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(0, 116, 162);">
						</div>
					</div>
					<div>
						<h3>Description</h3>
						<div class="has-background">
							<label for="ht_view5_show_description">Show Description</label>
							<input type="hidden" value="off" name="params[ht_view5_show_description]">
							<input type="checkbox" id="ht_view5_show_description" checked="checked" name="params[ht_view5_show_description]" value="on">
						</div>
						<div>
							<label for="ht_view5_description_font_size">Description Font Size</label>
							<input type="text" name="params[ht_view5_description_font_size]" id="ht_view5_description_font_size" value="14" class="text">
							<span>px</span>
						</div>
						<div class="has-background">
							<label for="ht_view5_description_color">Description Font Color</label>
							<input name="params[ht_view5_description_color]" type="text" class="color" id="ht_view5_description_color" value="#555555" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(85, 85, 85);">
						</div>
					</div>
					<div style="margin-top:-65px;">
						<h3>Link Button</h3>
						<div class="has-background">
							<label for="ht_view5_show_linkbutton">Show Link Button</label>
							<input type="hidden" value="off" name="params[ht_view5_show_linkbutton]">
							<input type="checkbox" id="ht_view5_show_linkbutton" checked="checked" name="params[ht_view5_show_linkbutton]" value="on">
						</div>
						<div>
							<label for="ht_view5_linkbutton_text">Link Button Text</label>
							<input type="text" name="params[ht_view5_linkbutton_text]" id="ht_view5_linkbutton_text" value="View More" class="text">
						</div>
						<div class="has-background">
							<label for="ht_view5_linkbutton_font_size">Link Button Font Size</label>
							<input type="text" name="params[ht_view5_linkbutton_font_size]" id="ht_view5_linkbutton_font_size" value="14" class="text">
							<span>px</span>
						</div>
						<div>
							<label for="ht_view5_linkbutton_color">Link Button Font Color</label>
							<input name="params[ht_view5_linkbutton_color]" type="text" class="color" id="ht_view5_linkbutton_color" value="#ffffff" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
						</div>
						<div class="has-background">
							<label for="ht_view5_linkbutton_font_hover_color">Link Button Font Hover Color</label>
							<input name="params[ht_view5_linkbutton_font_hover_color]" type="text" class="color" id="ht_view5_linkbutton_font_hover_color" value="#ffffff" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
						</div>
						<div>
							<label for="ht_view5_linkbutton_background_color">Link Button Background Color</label>
							<input name="params[ht_view5_linkbutton_background_color]" type="text" class="color" id="ht_view5_linkbutton_background_color" value="#2ea2cd" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(46, 162, 205);">
						</div>
						<div class="has-background">
							<label for="ht_view5_linkbutton_background_hover_color">Link Button Background Hover Color</label>
							<input name="params[ht_view5_linkbutton_background_hover_color]" type="text" class="color" id="ht_view5_linkbutton_background_hover_color" value="#0074a2" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(0, 116, 162);">
						</div>
					</div>
				</li>
				<!-- VIEW 6 Gallery  -->
				<li id="portfolio-view-options-6">
                                        <div style="margin-top: 0px">
						<h3>Title</h3>
						<div class="has-background">
							<label for="ht_view6_title_font_size">Title Font Size</label>
							<input type="text" name="params[ht_view6_title_font_size]" id="ht_view6_title_font_size" value="16" class="text">
							<span>px</span>
						</div>
						<div>
							<label for="ht_view6_title_font_color">Title Font Color</label>
							<input name="params[ht_view6_title_font_color]" type="text" class="color" id="ht_view6_title_font_color" value="#0074A2" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(0, 116, 162);">
						</div>
						<div class="has-background">
							<label for="ht_view6_title_font_hover_color">Title Font Hover Color</label>
							<input name="params[ht_view6_title_font_hover_color]" type="text" class="color" id="ht_view6_title_font_hover_color" value="#2EA2CD" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(46, 162, 205);">
						</div>
						<div>
							<label for="ht_view6_title_background_color">Title Background Color</label>
							<input name="params[ht_view6_title_background_color]" type="text" class="color" id="ht_view6_title_background_color" value="#000000" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(0, 0, 0);">
						</div>
						<div class="has-background">
							<label for="ht_view6_title_background_transparency">Title Background Transparency</label>
							<div class="slider-container">
								<input name="params[ht_view6_title_background_transparency]" id="ht_view6_title_background_transparency" data-slider-highlight="true" data-slider-values="0,10,20,30,40,50,60,70,80,90,100" type="text" data-slider="true" value="80" style="display: none;">
								<span>80%</span>
							</div>
						</div>
					</div>
                                    
                                        <div style="margin-top: 0px;">
						<h3>Image</h3>
						<div class="has-background">
							<label for="ht_view6_width">Image Width</label>
							<input type="text" name="params[ht_view6_width]" id="ht_view6_width" value="275" class="text">
							<span>px</span>
						</div>
						<div>
							<label for="ht_view6_border_width">Image Border Width</label>
							<input type="text" name="params[ht_view6_border_width]" id="ht_view6_border_width" value="0" class="text">
							<span>px</span>
						</div>
						<div class="has-background">
							<label for="ht_view6_border_color">Image Border Color</label>
							<input name="params[ht_view6_border_color]" type="text" class="color" id="ht_view6_border_color" value="#eeeeee" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(238, 238, 238);">
						</div>
						<div>
							<label for="ht_view6_border_radius">Border Radius</label>
							<input type="text" name="params[ht_view6_border_radius]" id="ht_view6_border_radius" value="3" class="text">
							<span>px</span>
						</div>
					</div>
                                        
                                        <div>
                                            <h3>Sorting styles</h3>
                                            <div class="" style="display: none;">
                                                    <label for="ht_view6_show_sorting" style="display: none;">Show Sorting</label>
                                                    <input type="hidden" value="off" name="params[ht_view6_show_sorting]">
                                                    <input type="checkbox" id="ht_view6_show_sorting" checked="checked" name="params[ht_view6_show_sorting]" value="on">
                                            </div>

                                            <div class="has-background">
                                                    <label for="ht_view6_sortbutton_font_size">Sort Button Font Size</label>
                                                    <input type="text" name="params[ht_view6_sortbutton_font_size]" id="ht_view6_sortbutton_font_size" value="14" class="text">
                                                    <span>px</span>
                                            </div>
                                            <div class="">
                                                    <label for="ht_view6_sortbutton_font_color">Sort Button Font Color</label>
                                                    <input name="params[ht_view6_sortbutton_font_color]" type="text" class="color" id="ht_view6_sortbutton_font_color" value="#555555" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(85, 85, 85);">
                                            </div>
                                            <div class="has-background">
                                                    <label for="ht_view6_sortbutton_hover_font_color">Sort Button Font Hover Color</label>
                                                    <input name="params[ht_view6_sortbutton_hover_font_color]" type="text" class="color" id="ht_view6_sortbutton_hover_font_color" value="#ffffff" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
                                            </div>
                                            <div class="">
                                                    <label for="ht_view6_sortbutton_background_color">Sort Button Background Color</label>
                                                    <input name="params[ht_view6_sortbutton_background_color]" type="text" class="color" id="ht_view6_sortbutton_background_color" value="#F7F7F7" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(247, 247, 247);">
                                            </div>
                                            <div class="has-background">
                                                    <label for="ht_view6_sortbutton_hover_background_color">Sort Button Background Hover Color</label>
                                                    <input name="params[ht_view6_sortbutton_hover_background_color]" type="text" class="color" id="ht_view6_sortbutton_hover_background_color" value="#FF3845" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(255, 56, 69);">
                                            </div>
                                            <div class="" style="display: none;">
                                                    <label for="ht_view6_sortbutton_border_width">Sort Button Border Width</label>
                                                    <input type="text" name="params[ht_view6_sortbutton_border_width]" id="ht_view6_sortbutton_border_width" value="" class="text">
                                                    <span>px</span>
                                            </div>
                                            <div style="display: none;">
                                                    <input name="params[ht_view6_sortbutton_border_color]" type="text" class="color" id="ht_view6_sortbutton_border_color" value="#" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
                                                    <label for="ht_view6_sortbutton_border_color">Sort Button Border Color</label>
                                            </div>
                                                <div class="">
                                                    <label for="ht_view6_sortbutton_border_radius">Sort Button Border Radius</label>
                                                    <input type="text" name="params[ht_view6_sortbutton_border_radius]" id="ht_view6_sortbutton_border_radius" value="0" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div class="has-background">
                                                    <label for="ht_view6_sortbutton_border_padding">Sort Button Padding</label>
                                                    <input type="text" name="params[ht_view6_sortbutton_border_padding]" id="ht_view6_sortbutton_border_padding" value="3" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div style="display: none;">
                                                    <label for="ht_view6_sortbutton_margin">Sort Button Margins</label>
                                                    <input type="text" name="params[ht_view6_sortbutton_margin]" id="ht_view6_sortbutton_margin" value="" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div class="">
                                                    <label for="ht_view6_sorting_float">Sort block Position</label>
                                                    <select id="ht_view6_sorting_float" name="params[ht_view6_sorting_float]">	
                                                      <option value="left">Left</option>
                                                      <option value="right">Right</option>
                                                      <option selected="selected" value="top">Top</option>
                                                    </select>
						</div>
                                                <div class="has-background">
							<label for="ht_view6_sorting_name_by_default">Sort By Default Button Name</label>
							<input name="params[ht_view6_sorting_name_by_default]" type="text" id="ht_view6_sorting_name_by_default" value="Default" size="10" class="text">
						</div>
						<div class="">
							<label for="ht_view6_sorting_name_by_id">Sorting By ID Button Name</label>
							<input name="params[ht_view6_sorting_name_by_id]" type="text" id="ht_view6_sorting_name_by_id" value="Date" size="10">
						</div>
                                                <div class="has-background">
							<label for="ht_view6_sorting_name_by_name">Sorting By ID Button Name</label>
							<input name="params[ht_view6_sorting_name_by_name]" type="text" id="ht_view6_sorting_name_by_name" value="Title" size="10">
						</div>
						<div class="">
							<label for="ht_view6_sorting_name_by_random">Random Sorting Button Name</label>
							<input name="params[ht_view6_sorting_name_by_random]" type="text" id="ht_view6_sorting_name_by_random" value="Random" size="10">
						</div>
                                                <div class="has-background">
							<label for="ht_view6_sorting_name_by_asc">Ascedding Sorting Button Name</label>
							<input name="params[ht_view6_sorting_name_by_asc]" type="text" id="ht_view6_sorting_name_by_asc" value="Asceding" size="10">
						</div>
						<div class="">
							<label for="ht_view6_sorting_name_by_desc">Descedding Sorting Button Name</label>
							<input name="params[ht_view6_sorting_name_by_desc]" type="text" id="ht_view6_sorting_name_by_desc" value="Desceding" size="10">
						</div>
                                        </div>
                                    
                                        <div style="margin-top: -600px">
                                            <h3>Category styles</h3>
                                                <div style="display: none;">
                                                    <label for="ht_view6_show_filtering" style="display: none;">Show Filtering</label>
                                                    <input type="hidden" value="off" name="params[ht_view6_show_filtering]">
                                                    <input type="checkbox" id="ht_view6_show_filtering" checked="checked" name="params[ht_view6_show_filtering]" value="on">
                                                </div>
                                                <div class="">
                                                    <label for="ht_view6_cat_all">Show All Category Button Name</label>
                                                    <input type="text" name="params[ht_view6_cat_all]" id="ht_view6_cat_all" value="All" class="text" />
                                                </div>
                                                <div class="has-background">
                                                    <label for="ht_view6_filterbutton_font_size">Filter Button Font Size</label>
                                                    <input type="text" name="params[ht_view6_filterbutton_font_size]" id="ht_view6_filterbutton_font_size" value="14" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div class="">
                                                        <label for="ht_view6_filterbutton_font_color">Filter Button Font Color</label>
                                                        <input name="params[ht_view6_filterbutton_font_color]" type="text" class="color" id="ht_view6_filterbutton_font_color" value="#555555" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(85, 85, 85);">
                                                </div>
                                                <div class="has-background">
                                                        <label for="ht_view6_filterbutton_hover_font_color">Filter Button Font Hover Color</label>
                                                        <input name="params[ht_view6_filterbutton_hover_font_color]" type="text" class="color" id="ht_view6_filterbutton_hover_font_color" value="#ffffff" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
                                                </div>
                                                <div class="">
                                                    <label for="ht_view6_filterbutton_background_color">Filter Button Background Color</label>
                                                    <input name="params[ht_view6_filterbutton_background_color]" type="text" class="color" id="ht_view6_filterbutton_background_color" value="#F7F7F7" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(247, 247, 247);">
                                                </div>
                                                <div class="has-background">
                                                    <label for="ht_view6_filterbutton_hover_background_color">Filter Button Background Hover Color</label>
                                                    <input name="params[ht_view6_filterbutton_hover_background_color]" type="text" class="color" id="ht_view6_filterbutton_hover_background_color" value="#FF3845" size="10" autocomplete="off" style="color: rgb(255, 255, 255); background-color: rgb(255, 56, 69);">
                                                </div>

                                                <div class="" style="display: none;">
                                                        <label for="ht_view6_filterbutton_border_width">Filter Button Border Width</label>
                                                        <input type="text" name="params[ht_view6_filterbutton_border_width]" id="ht_view6_filterbutton_border_width" value="" class="text">
                                                        <span>px</span>
                                                </div>
                                                <div style="display: none;">
                                                        <input name="params[ht_view6_filterbutton_border_color]" type="text" class="color" id="ht_view6_filterbutton_border_color" value="#" size="10" autocomplete="off" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">
                                                        <label for="ht_view6_filterbutton_border_color">Filter Button Border Color</label>
                                                </div>
                                                <div class="">
                                                    <label for="ht_view6_filterbutton_border_radius">Filter Button Border Radius</label>
                                                    <input type="text" name="params[ht_view6_filterbutton_border_radius]" id="ht_view6_filterbutton_border_radius" value="0" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div class="has-background">
                                                    <label for="ht_view6_filterbutton_border_padding">Filter Button Padding</label>
                                                    <input type="text" name="params[ht_view6_filterbutton_border_padding]" id="ht_view6_filterbutton_border_padding" value="3" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div style="display: none;">
                                                    <label for="ht_view6_filterbutton_margin">Filter Button Margins</label>
                                                    <input type="text" name="params[ht_view6_filterbutton_margin]" id="ht_view6_filterbutton_margin" value="" class="text">
                                                    <span>px</span>
                                                </div>
                                                <div class="">
                                                    <label for="ht_view6_filtering_float">Filter block Position</label>
                                                    <select id="ht_view6_filtering_float" name="params[ht_view6_filtering_float]">	
                                                      <option selected="selected" value="left">Left</option>
                                                      <option value="right">Right</option>
                                                      <option value="top">Top</option>
                                                    </select>
                                                </div>
                                        </div>                                        
				</li>
			</ul>

		<div id="post-body-footer">
			<a class="save-portfolio-options button-primary">Save</a>
			<div class="clear"></div>
		</div>
		
		</div></form>
	</div>
</div>
</div>
<input type="hidden" name="option" value=""/>
<input type="hidden" name="task" value=""/>
<input type="hidden" name="controller" value="options"/>
<input type="hidden" name="op_type" value="styles"/>
<input type="hidden" name="boxchecked" value="0"/>

<?php
}