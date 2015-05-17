<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Updater Class
 */
class IgniteWoo_Updater {
	public $updater;
	public $admin;
	private $token = 'ignitewoo-updater';
	private $plugin_url;
	private $plugin_path;
	public $version;
	private $file;
	private $products;

	/**
	 * Constructor.
	 * @param string $file The base file of the plugin.
	 * @since  1.0.0
	 * @return  void
	 */
	public function __construct ( $file ) {
		$this->file = $file;
		$this->plugin_url = trailingslashit( plugins_url( '', $plugin = $file ) );
		$this->plugin_path = trailingslashit( dirname( $file ) );

		$this->products = array();

		$this->load_plugin_textdomain();
		add_action( 'init', array( &$this, 'load_localisation' ), 0 );

		// Run this on activation.
		register_activation_hook( $this->file, array( &$this, 'activation' ) );

		if ( is_admin() ) {
			// Load the self-updater.
			require_once( 'class-ignitewoo-updater-self-updater.php' );
			$this->updater = new IgniteWoo_Updater_Self_Updater( $file );

			// Load the admin.
			require_once( 'class-ignitewoo-updater-admin.php' );
			$this->admin = new IgniteWoo_Updater_Admin( $file );

			// Get queued plugin updates
			add_action( 'plugins_loaded', array( &$this, 'load_queued_updates' ) );

			add_action( 'admin_head', array( &$this, 'admin_head' ) );

			add_action( 'admin_notices', array( &$this, 'admin_notices' ) );

			add_action( 'wp_ajax_ignitewoo_dismiss_notice', array( &$this, 'dismiss_notice' ) );
			
			add_action( 'init', array( &$this, 'setup_upgrade_notice_hooks' ) );
			
		}
		
	}

	
	function setup_upgrade_notice_hooks() { 
	
		$notice_files = get_transient( 'ignitewoo_upgrade_notice_files' );
		
		$notice_files = maybe_unserialize( $notice_files );
		
		if ( empty( $notice_files ) )
			return;
			
		foreach( $notice_files as $nf ) { 
		
			add_action( 'in_plugin_update_message-' . $nf, array( $this, 'in_plugin_update_message' ), 1 , 2 );

		}
	
	}
	
	
	function in_plugin_update_message( $plugin_data = null, $r = null ) { 

		if ( empty( $r->upgrade_notice ) )
			return;
			
		$upgrade_notice = '<div class="ign_plugin_upgrade_notice">';

		$notice = html_entity_decode( $r->upgrade_notice );
		
		//$notice = wp_kses_post( preg_replace( '~\[([^\]]*)\]\(([^\)]*)\)~', '<a href="${2}">${1}</a>', $r->upgrade_notice ) );

		$upgrade_notice .= str_replace( "\n", "<br/>", $notice );
		
		$upgrade_notice .= '</div>';
		
		echo $upgrade_notice;
	
	}
	
	/**
	 * Load the plugin's localisation file.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function load_localisation () {
		load_plugin_textdomain( 'ignitewoo-updater', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation()

	/**
	 * Load the plugin textdomain from the main WordPress "languages" folder.
	 * @since  1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain () {
	    $domain = 'ignitewoo-updater';
	    // The "plugin_locale" filter is also used in load_plugin_textdomain()
	    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

	    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_plugin_textdomain()

	/**
	 * Load CSS styles.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	function admin_head() {
	
		if ( false !== strpos( $_SERVER['REQUEST_URI'], 'plugins.php' ) ) { 
			?>
			<style>
			.ign_plugin_upgrade_notice:before {
				content:"\f348";
				display:inline-block;
				font:400 18px/1 dashicons;
				speak:none;
				margin:0 8px 0 -2px;
				-webkit-font-smoothing:antialiased;
				-moz-osx-font-smoothing:grayscale;
				vertical-align:top
			}
			.ign_plugin_upgrade_notice {
				background: none repeat scroll 0 0 #d54d21;
				color: #fff;
				font-weight: 400;
				margin: 9px 0;
				padding: 1em;
			}
			.ign_plugin_upgrade_notice a {
				color:#fff;
				text-decoration:underline;
			}
			</style>
			<?php
		}
	
		if ( empty( $_GET['action'] ) || ( 'do-plugin-upgrade' != $_GET['action'] && 'update-selected' != $_GET['action'] && 'upgrade-plugin' != $_GET['action'] ) )
			return;
		?>
		<style>.update-messages, iframe .update-messages,.wrap .code{display:none !important;}</style>
		<?php
	}
	
	/**
	 * Run on activation.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function activation () {
		$this->register_plugin_version();
	} // End activation()

	/**
	 * Register the plugin's version.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	private function register_plugin_version () {
		if ( $this->version != '' ) {
			update_option( 'ignitewoo-updater' . '-version', $this->version );
		}
	} // End register_plugin_version()

	/**
	 * load_queued_updates function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function load_queued_updates() {
		global $ignitewoo_queued_updates;

		if ( ! empty( $ignitewoo_queued_updates ) && is_array( $ignitewoo_queued_updates ) )
			foreach ( $ignitewoo_queued_updates as $plugin )
				if ( is_object( $plugin ) && ! empty( $plugin->file ) && ! empty( $plugin->file_id ) && ! empty( $plugin->product_id ) )
					$this->add_product( $plugin->file, $plugin->file_id, $plugin->product_id );

	} // END load_queued_updates()

	/**
	 * load plugin update notices
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function admin_notices() {
		global $user_ID;

		if ( !$user_ID )
			return;

		$updates = get_option( 'ignitewoo_sitewide_notice', false );

		if ( !$updates )
			return;

		$dismissed = get_user_meta( $user_ID, 'ignitewoo_dismissals', true );

		if ( is_array( $dismissed ) && in_array( $updates['id'], $dismissed ) )
			return;

		echo '<div class="updated ignitewoo_plugin_notices" id="message" style="border: 1px solid #3273AA;background-color:#E4F3FF"><p>' . $updates['msg'] . ' &mdash; <a href="#" class="ignitewoo_dismiss_notice" rel="'.$updates['id'].'">Dismiss</a></p></div>';

		?>
		<script>
			jQuery( document ).ready( function() {
				jQuery( ".ignitewoo_dismiss_notice" ).click( function() {
					var id = jQuery( ".ignitewoo_dismiss_notice" ).attr( "rel" );
					jQuery.post( ajaxurl, {action:"ignitewoo_dismiss_notice",ignid:id}, function( data ) {
						jQuery( ".ignitewoo_plugin_notices" ).fadeOut( "slow" );
					});
				});
			});
		</script>

		<?php
	
	}

	/**
	 * dismiss notices
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function dismiss_notice() {
		global $user_ID;

		if ( !$user_ID )
			die;

		if ( empty( $_POST['ignid'] ) || absint( $_POST['ignid'] ) <= 0 )
			die;

		$dismissed = get_user_meta( $user_ID, 'ignitewoo_dismissals', true );

		if ( !$dismissed )
			$dismissed = array();

		$dismissed[] = absint( $_POST['ignid'] ) ;

		update_user_meta( $user_ID, 'ignitewoo_dismissals', $dismissed );
	}
	
	/**
	 * Add a product to await a license key for activation.
	 *
	 * Add an product into the array, to be processed with the other products.
	 *
	 * @since  1.0.0
	 * @param string $file The base file of the product to be activated.
	 * @param string $file_id The unique file ID of the product to be activated.
	 * @return  void
	 */
	public function add_product ( $file, $file_id, $product_id ) {
		if ( $file != '' && ! isset( $this->products[$file] ) ) { $this->products[$file] = array( 'file_id' => $file_id, 'product_id' => $product_id ); }
	} // End add_product()

	/**
	 * Remove a product from the available array of products.
	 *
	 * @since     1.0.0
	 * @param     string $key The key to be removed.
	 * @return    boolean
	 */
	public function remove_product ( $file ) {
		$response = false;
		if ( $file != '' && in_array( $file, array_keys( $this->products ) ) ) { unset( $this->products[$file] ); $response = true; }
		return $response;
	} // End remove_product()

	/**
	 * Return an array of the available product keys.
	 * @since  1.0.0
	 * @return array Product keys.
	 */
	public function get_products () {
		return (array) $this->products;
	} // End get_products()
} // End Class
?>