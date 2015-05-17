<div class="subscribers_container">
  <?php
  $subscriptions = get_subscriptions();
  if ( count($subscriptions) > 1 ) :
    foreach ( $subscriptions as $key => $subscription ) : if($key == 0) continue; if($key > 20) break;//show first 20 subscribers?>
      <div class="subscriber">
          <p><i><?php echo $subscription ?></i></p>
      </div>
    <?php endforeach;?>
      <p>Total : <?php echo count($subscriptions) - 2; //ignoring first and last rows in file (column naimes, empty row)?></p>
    <div>
      <a href="#" class="tt_btn clear"><span class="erase">Clear Subscription List</span></a>
    </div>
    <div class="tt_option_title"><span>Export subscription list</span></div>
    <a href="<?php echo TT_THEME_URI . '/subscriptions.txt'?>" class="tt_btn" download="subscriptions.txt" target="_blank"><span class="tab_delimited">Tab Delimited TXT</span></a> 
    <a href="<?php echo TT_THEME_URI . '/subscriptions.csv'?>" class="tt_btn" download="subscriptions.csv" target="_blank"><span class="csv">CSV</span></a>
  <?php else: ?>
    <p>No subscribers yet...</p>
  <?php endif; ?>
</div>