<?php $max = floor(12/(int)$shortcode['size']); ?>
<div class="site-title"><div class="site-inside"><span>Pricing tables</span></div></div> 
<div class="row">
	<?php foreach($slides as $i => $slide): ?>
  	<?php if($i&&!($i%$max)): ?>
          </div>
          <div class="row">
  	<?php endif; ?>
    <div class="col-md-<?php echo $shortcode['size']; ?> col-xs-6">
      <?php if($slide['options']['type'] == '1') : ?>
        <div class="pricing-table-1<?php if(in_array('outlined', $slide['options']['outlined'])) echo ' pricing-table-favorite'; ?>">
          <ul>
            <li class="pricing-table-name"><?php echo get_the_title($slide['post']->ID); ?></li>
            <?php foreach($slide['options']['features'] as $feature): ?>
              <li class="pricing-table-list"><i class="icon-458" title="458"></i><?php echo do_shortcode($feature); ?></li>
            <?php endforeach; ?>
            <li class="pricing-table-price"><?php echo $slide['options']['price'] ?> <a href="<?php echo $slide['options']['link'] ?>"><?php echo $slide['options']['link_text'] ? $slide['options']['link_text'] : __('Buy now','zeon')?></a></li>
          </ul>
        </div>
        
      <?php else: ?>
        <div class="pricing-table-2<?php if(in_array('outlined', $slide['options']['outlined'])) echo ' pricing-table-favorite'; ?>">
            <div class="pricing-table-name"><?php echo get_the_title($slide['post']->ID); ?></div>
            <div class="pricing-table-price"><?php echo $slide['options']['price']; ?> <a href="<?php echo $slide['options']['link'] ?>"><?php echo $slide['options']['link_text'] ? $slide['options']['link_text'] : __('buy now','zeon')?></a></div>
            <ul>
              <?php foreach($slide['options']['features'] as $feature): ?>
                <li><?php echo do_shortcode($feature); ?></li>
              <?php endforeach; ?>
            </ul>
        </div>
      <?php endif; ?>
    </div>
	<?php endforeach; ?>
</div>