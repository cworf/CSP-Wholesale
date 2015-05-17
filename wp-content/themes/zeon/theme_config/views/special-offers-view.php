<?php if(!empty($shortcode['boxed'])) : ?>
	</div>
	<div class="box color-2">
        <div class="container">
<?php endif; ?>

<?php if(!empty($shortcode['title'])) : ?>
	<div class="info-details">
    	<h1><?php echo $shortcode['title'] ?></h1>
    	<?php if(!empty($shortcode['description'])) echo "<p>".$shortcode['description']."</p>"?>
    </div>
<?php endif; ?>
<div class="row">
	<?php foreach ($slides as $slide_nr => $slide) : 
		if($slide_nr && !($slide_nr%3)):?>
            </div><div class="row">
        <?php endif;?>
	    <div class="col-md-4 col-xs-4">
	        <div class="shop-links">
	            <div class="shop-links-cover">
	                <?php echo get_the_post_thumbnail($slide['post']->ID); ?>
	            </div>
	            <div class="shop-links-box"<?php if(!empty($slide['options']['color'])) echo " style='background-color:{$slide['options']['color']}'" ?>>
	                <h2><?php echo get_the_title( $slide['post']->ID ); ?><span><?php echo $slide['post']->post_content?></span></h2>
	                <a href="<?php if(!empty($slide['options']['link'])) echo $slide['options']['link']?>"<?php if(!empty($slide['options']['color'])) echo " style='color:{$slide['options']['color']}'" ?>><?php _e('Shop now','zeon') ?></a>
	            </div>
	        </div>
	    </div>
	<?php endforeach; ?>
</div>
<?php if(!empty($shortcode['boxed'])) : ?>
		</div>
	</div>
	<div class="container">
<?php endif; ?>