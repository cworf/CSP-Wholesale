<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div id="ignitewoo-updater" class="wrap">
	<?php screen_icon( 'index' ); ?><div class="wrap"><h2><?php echo $this->name; ?></h2>
<div id="col-container" style="width:100% !important">
	<?php
	echo '<div class="updated fade">' . wpautop( __( "Below is a list of the IgniteWoo products active on this installation.
	Activate your plugin license keys for this site to receive support and plugin updates.
	Looking for your license keys? Visit your <a href='http://ignitewoo.com/my-account'>My Account page at IgniteWoo.com</a>.", 'ignitewoo-updater' ) ) . '</div>' . "\n";
	?>
		<div class="col-wrap">
			<form id="activate-products" method="post" action="" class="validate">
				<input type="hidden" name="action" value="activate-products" />
				<input type="hidden" name="page" value="<?php echo esc_attr( $this->page_slug ); ?>" />
				<?php
				require_once( $this->classes_path . 'class-ignitewoo-updater-licenses-table.php' );
				$this->list_table = new IgniteWoo_Updater_Licenses_Table();
				$this->list_table->data = $this->get_detected_products();
				$this->list_table->prepare_items();
				$this->list_table->display();
				submit_button( __( 'Activate Products', 'ignitewoo-updater' ), 'button-primary' );
				?>
			</form>
		</div><!--/.col-wrap-->
</div><!--/#col-container-->
</div><!--/#ignitewoo-updater-->