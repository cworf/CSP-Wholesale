<?php
/*
    Template Name: Home - Wide Slider
*/
get_header();?>
<?php $slider_category = get_post_meta($post->ID,THEME_NAME . '_slider_categ',true);
if ( class_exists('RevSliderFront') ) {
    $rvslider = new RevSlider();
    $arrSliders = $rvslider->getArrSliders();
    if( !empty( $arrSliders ) ) {
    	foreach ($arrSliders as $revSlider) {
    		if($revSlider->getAlias() === $slider_category)
    			$revSliderAlias = $revSlider->getAlias();
    	}
    }
}
if(!empty($revSliderAlias)){
	echo '<div class="the-slider slider">';
		putRevSlider( $revSliderAlias );
	echo '</div>';
}else
	echo Tesla_slider::get_slider_html('slider',$slider_category);

if (have_posts()) : 
    while(have_posts()) : the_post(); ?>
		<div class="container">
    		<?php the_content();?>
    	</div>
    <?php endwhile;
endif;

get_footer();