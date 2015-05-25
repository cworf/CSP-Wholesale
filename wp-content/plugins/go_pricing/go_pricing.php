<?php
/*
Plugin Name: Go - Responsive Pricing & Compare Tables
Plugin URI: http://codecanyon.net/item/go-responsive-pricing-compare-tables-for-wp/3725820
Description: The New Generation Pricing Tables. If you like traditional Pricing Tables, but you would like get much more out of it, then this rodded product is a useful tool for you.
Author: Granth
Version: 2.4.5
Author URI: http://themeforest.net/user/Granth
*/

//+----------------------------------------------------------------+
//| Define constants
//+----------------------------------------------------------------+

define( 'GW_GO_VER', '2.4.5' );
define( 'GW_GO_PREFIX', 'go_pricing_' );
define( 'GW_GO_TEXTDOMAN', 'go_pricing' ); 

//+----------------------------------------------------------------+
//| GoPricing class
//+----------------------------------------------------------------+

require_once(  plugin_dir_path( __FILE__ ) . trailingslashit( 'includes/vc' ) . 'class_vc_extend.php' );

if ( !class_exists( 'GW_GoPricing' ) ) {
	class GW_GoPricing {
		
		protected $screen_hooks = null;
		
		/* construct */
		public function __construct() {
			if ( is_admin() ) {
				if ( ( isset( $_GET['page'] ) && preg_match("/go-pricing/", $_GET['page'] ) == 1 ) ) {
					add_action( 'admin_init', array( $this, 'go_pricing_register_admin_js' ) );
					add_action( 'admin_init', array( $this, 'go_pricing_load_admin_css' ) );
					add_action( 'plugins_loaded', array( $this, 'go_pricing_load_textdomain' ) );
					
				}				
				add_action( 'admin_notices', array( $this, 'print_admin_notices' ) );								
				add_action( 'admin_menu', array( $this, 'go_pricing_register_menu_page' ) );
				add_action( 'admin_menu', array( $this, 'go_pricing_register_submenu_page' ) );				
			} 
			add_shortcode( 'go_pricing', array( $this, 'go_pricing_shortcode' ) );
			add_shortcode( 'go_pricing_youtube', array( $this, 'go_pricing_youtube_shortcode' ) );
			add_shortcode( 'go_pricing_vimeo', array( $this, 'go_pricing_vimeo_shortcode' ) );
			add_shortcode( 'go_pricing_screenr', array( $this, 'go_pricing_screenr_shortcode' ) );
			add_shortcode( 'go_pricing_dailymotion', array( $this, 'go_pricing_dailymotion_shortcode' ) );
			add_shortcode( 'go_pricing_metacafe', array( $this, 'go_pricing_metacafe_shortcode' ) );
			add_shortcode( 'go_pricing_html5_video', array( $this, 'go_pricing_html5_video_shortcode' ) );
			add_shortcode( 'go_pricing_soundcloud', array( $this, 'go_pricing_soundcloud_shortcode' ) );
			add_shortcode( 'go_pricing_mixcloud', array( $this, 'go_pricing_mixcloud_shortcode' ) );
			add_shortcode( 'go_pricing_beatport', array( $this, 'go_pricing_beatport_shortcode' ) );						
			add_shortcode( 'go_pricing_audio', array( $this, 'go_pricing_audio_shortcode' ) );
			add_shortcode( 'go_pricing_map', array( $this, 'go_pricing_map_shortcode' ) );
			add_shortcode( 'go_pricing_custom_iframe', array( $this, 'go_pricing_custom_iframe_shortcode' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'go_pricing_load_css' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'go_pricing_register_js' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'go_pricing_enqeue_js') );
			register_activation_hook( __FILE__, array( __CLASS__, 'go_pricing_activation' ) );
			register_uninstall_hook( __FILE__, array( __CLASS__, 'go_pricing_uninstall' ) );	
		}
		
		/* allow WebM mime type */
		public function go_pricing_add_webm_mime( $mimes ){
			$mimes = array_merge( $mimes, array( 'webm' => 'video/webm' ) );
			return $mimes;
		}		
		
		/* activation */
		public static function go_pricing_activation() {			
			$table_settings = get_option( GW_GO_PREFIX . 'table_settings' );
			self::generate_styles ( $table_settings );
		}

		/* uninstall -> delete db data */
		public static function go_pricing_uninstall() {			
			delete_option( GW_GO_PREFIX . 'tables' );
			delete_option( GW_GO_PREFIX . 'table_settings' );
			delete_option( GW_GO_PREFIX . 'notices' );			
		}

		/* load textdomain */
		public function go_pricing_load_textdomain() {			
			load_plugin_textdomain( GW_GO_TEXTDOMAN, false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
		}

		/* create custom menu page */
		public function go_pricing_register_menu_page() {
			$table_settings = get_option( GW_GO_PREFIX . 'table_settings' );
			$capability = isset( $table_settings['capability'] ) ? $table_settings['capability'] : 'manage_options';
			add_menu_page('Go - Pricing & Compare Tables', 'Go Pricing', $capability, 'go-pricing', array( $this, 'go_pricing_menu_page_callback' ) , plugin_dir_url( __FILE__ ) . '/admin/images/icon_wp_nav.png', 90);
			add_action( 'admin_enqueue_scripts', array( $this, 'go_pricing_enqeue_admin_js') );
		}
		/* create custom submenu page for settings menu item */
		public function go_pricing_register_submenu_page() {
			$table_settings = get_option( GW_GO_PREFIX . 'table_settings' );
			$capability = isset( $table_settings['capability'] ) ? $table_settings['capability'] : 'manage_options';			
			add_submenu_page( 'go-pricing', 'General Settings', 'General Settings', $capability, 'go-pricing-settings', array( $this, 'go_pricing_submenu_page_settings_callback' ) );

			/* Submenu page - Import & Export */
			$this->screen_hooks[] = add_submenu_page(
				'go-pricing',
				__( 'Import & Export', GW_GO_TEXTDOMAN ) . ' | ' . __( 'Go - Pricing', GW_GO_TEXTDOMAN ),
				__( 'Import & Export', GW_GO_TEXTDOMAN ),
				$capability,
				'go-pricing-import-export',
				array( $this, 'plugin_submenu_page_import_export' )
			);	

		}
		
		/* register js files for admin */
		public function go_pricing_register_admin_js() {
			wp_register_script( GW_GO_PREFIX . 'admin', plugin_dir_url( __FILE__ ) . 'admin/js/go_pricing_admin_scripts.js', 'jquery', GW_GO_VER );			
		}

		/* register js files */
		public function go_pricing_register_js() {
			wp_register_script( GW_GO_PREFIX . 'scripts', plugin_dir_url( __FILE__ ) . 'assets/js/go_pricing_scripts.js', 'jquery', GW_GO_VER, true );
			wp_register_script( GW_GO_PREFIX . 'googlemap', 'http://maps.google.com/maps/api/js?sensor=false', false, GW_GO_VER, true );
			wp_register_script( GW_GO_PREFIX . 'jqplugin-gomap', plugin_dir_url( __FILE__ ). 'assets/plugins/js/jquery.gomap-1.3.2.min.js', 'jquery, ' . GW_GO_PREFIX . 'googlemap', GW_GO_VER, true );
			global $wp_version;
			if ( version_compare( $wp_version, 3.6, "<" ) ) {	
				wp_register_script( GW_GO_PREFIX . 'jqplugin-mediaelementjs', plugin_dir_url( __FILE__ ) . 'assets/plugins/js/mediaelementjs/mediaelement-and-player.min.js', 'jquery', GW_GO_VER, true ); 		
			}
		}
		
		/* enqeue js files for admin */
		public function go_pricing_enqeue_admin_js() {
			wp_enqueue_script( GW_GO_PREFIX . 'admin' );		
			wp_enqueue_script( 'farbtastic' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'media-upload' );
			wp_enqueue_script( 'thickbox' );
		}			
		
		/* enqeue js files */
		public function go_pricing_enqeue_js() {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( GW_GO_PREFIX . 'scripts' );
			global $wp_version;
			if ( version_compare( $wp_version, 3.6, "<" ) ) {
				wp_enqueue_script( GW_GO_PREFIX . 'jqplugin-mediaelementjs' );
			} else {
				wp_enqueue_script( 'wp-mediaelement' );
			}			
		}		
		
		/* load css files ( admin & frontend ) */
		public function go_pricing_load_admin_css() {
			wp_register_style( GW_GO_PREFIX . 'admin', plugin_dir_url( __FILE__ ) . 'admin/css/go_pricing_admin_styles.css', 'jquery', GW_GO_VER );
			wp_enqueue_style( GW_GO_PREFIX . 'admin' );
			wp_enqueue_style( 'farbtastic' );
			wp_enqueue_style( 'thickbox' );
		}
		
		/* load css files ( admin & frontend ) */
		public function go_pricing_load_css() {			
			wp_register_style( GW_GO_PREFIX . 'styles', plugin_dir_url( __FILE__ ) . 'assets/css/go_pricing_styles.css', false, GW_GO_VER );
			wp_enqueue_style( GW_GO_PREFIX . 'styles' );
			wp_register_style( GW_GO_PREFIX . 'jqplugin-mediaelementjs', plugin_dir_url( __FILE__ ) . 'assets/plugins/js/mediaelementjs/mediaelementplayer.min.css', false, GW_GO_VER );
			wp_register_style( GW_GO_PREFIX . 'jqplugin-mediaelementjs-skin', plugin_dir_url( __FILE__ ) . 'assets/plugins/js/mediaelementjs/skin/mediaelementplayer.css', false, GW_GO_VER );			
			wp_enqueue_style( GW_GO_PREFIX . 'jqplugin-mediaelementjs' );
			wp_enqueue_style( GW_GO_PREFIX . 'jqplugin-mediaelementjs-skin' );							
		}				

		/* print admin notices */
		public function print_admin_notices() {
			$new_current_notices = $current_notices = get_option( GW_GO_PREFIX . 'notices', array() ); 
			if ( $current_notices && !empty ( $current_notices ) ) {
				foreach ( $current_notices as $nkey => $current_notice ) {
					$output='<div class="' . ( isset( $current_notice['success'] ) && $current_notice['success'] == true ? 'updated' : 'error' ) . '">';
					$output.='<p><strong>' . ( isset( $current_notice['message'] ) ? $current_notice['message'] : '' ) . '</strong></p>';
					$output.='</div>';
					echo $output;
					if ( isset( $current_notice['permanent'] ) && $current_notice['permanent'] == false ) {
						unset( $new_current_notices[$nkey] );
					}
				}	
			}
			
			if ( $new_current_notices != $current_notices ) {
				update_option ( GW_GO_PREFIX . 'notices', $new_current_notices );  
			}
		}	
	
		/* update admin notices */ 
		public static function update_admin_notices( $notices = array() ) {
			
			if ( $notices && is_array( $notices ) && !empty( $notices ) ) {
				$current_notices = get_option( GW_GO_PREFIX . 'notices', array() ); 
				$new_current_notices = array_merge( $notices, $current_notices );
				if ( $new_current_notices != $current_notices ) {
					update_option ( GW_GO_PREFIX . 'notices', $new_current_notices );  
				}
			}
		}

		/* generate_styles */ 
		public static function generate_styles ( $data ) {
			if ( !empty( $data ) ) { $table_settings = $data; }
			ob_start();
			require_once( plugin_dir_path( __FILE__ ) . 'assets/css/go_pricing_dinamic_styles.php' );
			$css_data = ob_get_clean();
			$write_success = @file_put_contents( plugin_dir_path( __FILE__ ) . 'assets/css/go_pricing_styles.css', $css_data );
			if ( $write_success === false ) {
				$notices[] = array ( 
					'success' => false,
					'permanent' => false,
					'message' => __( 'The "go_pricing_styles.css" file couldn\'t be created in "assets/css" folder lack of write permission. <strong>Please set this folder\'s chmod to 777 and activate the plugin again.</strong>', GW_GO_TEXTDOMAN )
				);
			}
			if ( isset( $notices ) ) { self::update_admin_notices ( $notices ); }			
			
		}

		/* SHORTCODES */
		
		/* Google map shortcode [go_pricing_map] */
		public function go_pricing_map_shortcode( $atts, $content = null ) {
			extract( shortcode_atts( array( 
					'address' => '',
					'title' => '',
					'icon' =>'',
					'content' => null,
					'popup' => 'no',
					'zoom' => 14,
					'maptype' => 'ROADMAP',
					'width' => '100%',
					'height' => '300',
					'class' => ''
				 ), $atts ) );
			$height = preg_match( '{^[0-9]*$}', $height ) ? $height : '300';
			$class = $class != '' ? ' ' . esc_attr( trim( preg_replace( '/\s\s+/', ' ', $class ) ) ) : '';
			$popup = $popup == 'yes' ? true : false;
			
			$mapdata['markers'][] = array ( 
				'address' => $address,
				'title' => $title,
				'icon' => !empty( $icon ) ? array( 'image' => $icon ) : null,
				'html' => isset( $content ) ? array( 
					'content' => $content,
					'popup' => $popup
				 ) : null,
			 );
			$mapdata['zoom'] = intval($zoom);
			$mapdata['maptype'] = $maptype;
			$mapdata['mapTypeControl'] = false;
		
			wp_enqueue_script( GW_GO_PREFIX . 'googlemap' );
			wp_enqueue_script( GW_GO_PREFIX . 'jqplugin-gomap' );	
		
			return '<div class="gw-go-gmap' . $class . '" style="width:100%; height:' . $height . 'px;" data-map="' . esc_attr( json_encode( $mapdata ) ) . '"></div>';
		}

		/* html5 video shortcode [go_pricing_html5_video] */
		public function go_pricing_html5_video_shortcode( $atts, $content = null ) {
			extract( shortcode_atts( array( 
				'mp4_src' => '',
				'webm_src' => '',
				'ogg_src' => '',
				'poster_src' => '',
				'autoplay' => 'no',
				'loop' => 'no',	
			 ), $atts ) );	
		
			$autoplay = $autoplay == 'yes' ? ' autoplay="true"' : '';
			$loop = $loop == 'yes' ? ' loop="true"' : '';
			$mp4_src = $mp4_src != '' ? '<source src="' . $mp4_src . '" type="video/mp4">' : '';
			$webm_src = $webm_src != '' ? '<source src="' . $webm_src . '" type="video/webm">' : '';
			$ogg_src = $ogg_src != '' ? '<source src="' . $ogg_src . '" type="video/ogg">' : '';
			$poster_src = $poster_src != '' ? $poster_src : plugin_dir_url( __FILE__ ) . 'assets/images/blank.png';

			return '<video controls="controls"' . ( $autoplay != '' ? $autoplay : '' ) . ( $loop != '' ? $loop : '' ) . ( $poster_src != '' ? ' poster="' . $poster_src . '"' : '' ) .'>' . $mp4_src . $webm_src . $ogg_src . '<object type="application/x-shockwave-flash" data="' . plugin_dir_url( __FILE__ ) . 'assets/plugins/js/mediaelementjs/flashmediaelement.swf">
              <param name="movie" value="' . plugin_dir_url( __FILE__ ) . 'assets/plugins/js/mediaelementjs/flashmediaelement.swf" />
              <param name="flashvars" value="controls=true&poster=' . $poster_src . '&file=' . $mp4_src . '" />
              <img src="' . $poster_src . '" title="No video playback capabilities" />
          </object></video>';
		}
		
		/* html5 audio shortcode [go_pricing_audio] */
		public function go_pricing_audio_shortcode( $atts, $content = null ) {
			extract( shortcode_atts( array( 
				'mp3_src' => '',
				'ogg_src' => '',
				'wav_src' => '',
				'autoplay' => 'no',
				'loop' => 'no',	
			 ), $atts ) );	
		
			$autoplay = $autoplay == 'yes' ? ' autoplay="true"' : '';
			$loop = $loop == 'yes' ? ' loop="true"' : '';
			$mp3_src = $mp3_src != '' ? '<source src="' . $mp3_src . '" type="audio/mpeg">' : '';
			$ogg_src = $ogg_src != '' ? '<source src="' . $ogg_src . '" type="audio/ogg">' : '';
			$wav_src = $wav_src != '' ? '<source src="' . $wav_src . '" type="audio/wav">' : '';						
	
			return '<audio controls="controls"' . ( $autoplay != '' ? $autoplay : '' ) . ( $loop != '' ? $loop : '' ) . '>' . $mp3_src . $ogg_src . $wav_src . '</audio>';
		}		
		
		/* youtube shortcode [go_pricing_youtube] */
		public function go_pricing_youtube_shortcode( $atts, $content = null ) {
			extract( shortcode_atts( array( 
				'autoplay' => 'no',
				'https' => 'no',	
				'video_id' => '',
				'height' => 'auto',
			 ), $atts ) );	
		
			$autoplay = $autoplay == 'yes' ? '1' : '';
			$https = $https == 'yes' ? 's' : '';
			$width = '1000';
			$style = $height != 'auto' ? 'height:'.$height.'px !important; padding:0 !important;' : '';
			return '<div class="gw-go-video-wrapper"' . ( $style != '' ? ' style="' . $style . '"' : '' ) .'><iframe src="http' . $https . '://www.youtube.com/embed/' . esc_attr( $video_id ) . '?wmode=opaque&amp;controls=1&amp;showinfo=1&amp;autohide=1&amp;rel=0&amp;autoplay=' . $autoplay . '" width="' . $width . '" height="' . $height . '" marginheight="0" marginwidth="0" frameborder="0"></iframe></div>';
		}

		/* vimeo shortcode [go_pricing_vimeo] */
		public function go_pricing_vimeo_shortcode( $atts, $content = null ) {
			extract( shortcode_atts( array( 
				'autoplay' => 'no',
				'color' => '',
				'https' => 'no',	
				'video_id' => '',
				'height' => 'auto',
			 ), $atts ) );	
		
			$autoplay = $autoplay == 'yes' ? '1' : '';
			$width = '1000';
			$style = $height != 'auto' ? 'height:'.$height.'px !important; padding:0 !important;' : '';
			return '<div class="gw-go-video-wrapper"' . ( $style != '' ? ' style="' . $style . '"' : '' ) .'><iframe src="http://player.vimeo.com/video/' . esc_attr( $video_id ) . '?title=0&amp;byline=0&amp;portrait=0&amp;autohide=1&amp;color=' . $color . '&amp;autoplay=' . $autoplay . '" width="' . $width . '" height="' . $height . '" marginheight="0" marginwidth="0" frameborder="0"></iframe></div>';
		}

		/* screenr shortcode [go_pricing_screenr] */
		public function go_pricing_screenr_shortcode( $atts, $content = null ) {
			extract( shortcode_atts( array( 
				'video_id' => '',
				'height' => 'auto',
			 ), $atts ) );	

			$width = '1000';
			$style = $height != 'auto' ? 'height:'.$height.'px !important; padding:0 !important;' : '';
			return '<div class="gw-go-video-wrapper"' . ( $style != '' ? ' style="' . $style . '"' : '' ) .'><iframe src="http://www.screenr.com/embed/' . esc_attr( $video_id ) . '" width="' . $width . '" height="' . $height . '" marginheight="0" marginwidth="0" frameborder="0"></iframe></div>';
		}
		
		/* screenr shortcode [go_pricing_dailymotion] */
		public function go_pricing_dailymotion_shortcode( $atts, $content = null ) {
			extract( shortcode_atts( array( 
				'video_id' => '',
				'height' => 'auto',
				'autoplay' => 'no',
			 ), $atts ) );	

			$autoplay = $autoplay == 'yes' ? '1' : '0';
			$style = $height != 'auto' ? 'height:' . $height . 'px !important; padding:0 !important;' : '';
			return '<div class="gw-go-video-wrapper"' . ( $style != '' ? ' style="' . $style . '"' : '' ) .'><iframe src="//www.dailymotion.com/embed/video/' . esc_attr( $video_id ) . '?wmode=opaque&amp;autoPlay=' . $autoplay . '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>';
		}
		
		/* screenr shortcode [go_pricing_metacafe] */
		public function go_pricing_metacafe_shortcode( $atts, $content = null ) {
			extract( shortcode_atts( array( 
				'video_id' => '',
				'height' => 'auto',
				'autoplay' => 'no',
			 ), $atts ) );	

			$autoplay = $autoplay == 'yes' ? '1' : '0';
			$style = $height != 'auto' ? 'height:' . $height . 'px !important; padding:0 !important;' : '';
			return '<div class="gw-go-video-wrapper"' . ( $style != '' ? ' style="' . $style . '"' : '' ) .'><iframe src="http://www.metacafe.com/embed/' . esc_attr( $video_id ) . '?wmode=opaque&amp;ap=' . $autoplay . '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>';
		}			

		/* soundcloud shortcode [go_pricing_soundcloud] */
		public function go_pricing_soundcloud_shortcode( $atts, $content = null ) {
			extract( shortcode_atts( array( 
				'track_id' => '',
				'height' => 'auto',
				'autoplay' => 'no',
			 ), $atts ) );	

			$autoplay = $autoplay == 'yes' ? 'true' : 'false';
			$style = $height != 'auto' ? 'height:' . $height . 'px !important; padding:0 !important;' : '';
			return '<div class="gw-go-video-wrapper"' . ( $style != '' ? ' style="' . $style . '"' : '' ) .'><iframe src="//w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Ftracks%2F' . esc_attr( $track_id ) . '?wmode=opaque&amp;auto_play=' . $autoplay . '&amp;show_artwork=true" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>';
		}
		
		/* mixcloud shortcode [go_pricing_mixcloud] */
		public function go_pricing_mixcloud_shortcode( $atts, $content = null ) {
			extract( shortcode_atts( array( 
				'track_url' => '',
				'height' => 'auto'
			 ), $atts ) );	

			$style = $height != 'auto' ? 'height:' . $height . 'px !important; padding:0 !important;' : '';
			return '<div class="gw-go-video-wrapper"' . ( $style != '' ? ' style="' . $style . '"' : '' ) .'><iframe src="//www.mixcloud.com/widget/iframe/?feed=' . esc_attr( urlencode( trim( $track_id, '/' ) ) ) . '%2F&amp;show_tracklist=&amp;wmode=opaque" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>';
		}		
		
		/* beatport shortcode [go_pricing_beatport] */
		public function go_pricing_beatport_shortcode( $atts, $content = null ) {
			extract( shortcode_atts( array( 
				'track_id' => '',
				'height' => 'auto',
				'autoplay' => 'no',
			 ), $atts ) );	

			$autoplay = $autoplay == 'yes' ? '&amp;auto=' . $autoplay : '';
			$style = $height != 'auto' ? 'height:' . $height . 'px !important; padding:0 !important;' : '';
			return '<div class="gw-go-video-wrapper"' . ( $style != '' ? ' style="' . $style . '"' : '' ) .'><iframe src="http://embed.beatport.com/player?id=' . esc_attr( $track_id ) . '?wmode=opaque&amp;type=track' . $autoplay . '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>';
		}		
		
		/* custom iframe audio shortcode [go_pricing_custom_iframe] */
		public function go_pricing_custom_iframe_shortcode( $atts, $content = null ) {
			extract( shortcode_atts( array( 
				'url' => '',
				'height' => 'auto'
			 ), $atts ) );	

			$style = $height != 'auto' ? 'height:' . $height . 'px !important; padding:0 !important;' : '';
			return '<div class="gw-go-video-wrapper"' . ( $style != '' ? ' style="' . $style . '"' : '' ) .'><iframe src="' . esc_attr( $url ) . '?wmode=opaque" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>';
		}		
		
		/* pricing table shortcode [go_pricing] */
		public function go_pricing_shortcode( $atts, $content = null ) {
			extract( shortcode_atts( array( 
				'id' 	=> '',
				'margin_bottom' => '20px'
			 ), $atts ) );

			if ( !empty( $id ) ) { 
				$pricing_tables = get_option( GW_GO_PREFIX . 'tables' ); 
				$custom_styles = get_option( GW_GO_PREFIX . 'custom_styles' );						
				if ( !empty( $pricing_tables ) ) {
					/* get data of table if found */
					foreach ( $pricing_tables as $pricing_table ) {
						if 	( isset ( $pricing_table['table-id'] ) && $pricing_table['table-id'] == $id ) {
							$generated_table_classes[]='gw-go';
							$generated_table_classes[]='gw-go-clearfix';
							if ( isset( $pricing_table['enlarge-current'] ) && $pricing_table['enlarge-current'] == '1' ) { $generated_table_classes[]='gw-go-enlarge-current'; }						
							if ( isset( $pricing_table['colspace'] ) && $pricing_table['colspace'] != '' ) { $generated_table_classes[]=$pricing_table['colspace']; }
							if ( isset( $pricing_table['hide-footer'] ) && !empty( $pricing_table['hide-footer'] ) ) { $generated_table_classes[]='gw-go-no-footer'; }
							$colnum = count( $pricing_table['col-style'] );
							$rownum = isset( $pricing_table['col-detail'] ) ? count( $pricing_table['col-detail'] )/$colnum : 0;	
							switch ($colnum) {
								case 1: $generated_table_classes[]='gw-go-1col'; break;
								case 2: $generated_table_classes[]='gw-go-2cols'; break;
								case 3: $generated_table_classes[]='gw-go-3cols'; break;
								case 4: $generated_table_classes[]='gw-go-4cols'; break;
								case 5: $generated_table_classes[]='gw-go-5cols'; break;
								case 6: $generated_table_classes[]='gw-go-6cols'; break;
								case 7: $generated_table_classes[]='gw-go-7cols'; break;								
							}
							$generated_table='<div id="go-pricing-table-' . esc_attr( $pricing_table['table-id'] ) . '"' . ( $margin_bottom != '' ? ' style="margin-bottom:' . $margin_bottom . '"' : '' ) .'><div class="' . ( implode( $generated_table_classes, ' ' ) ) . '"  data-colnum="' . esc_attr( $colnum ) . '" data-rownum="' . esc_attr( $rownum ) . '" data-equalize="' . esc_attr( isset( $pricing_table['equal-height'] ) && $pricing_table['equal-height'] == '1' ? 'true' : 'false' ) . '">';
							for ( $x = 0; $x < $colnum ; $x++ ) {
								$col_data = explode( '_', $pricing_table['col-style'][$x] );
								$col_style = $col_data[0];
								if ( preg_match( "/blue/", $col_style ) == 1 ) { wp_enqueue_style( GW_GO_PREFIX . 'skin_blue', plugin_dir_url( __FILE__ ) . 'assets/css/go_pricing_skin_blue.css', false, GW_GO_VER ); 
								} elseif ( preg_match( "/green/", $col_style ) == 1 ) { wp_enqueue_style( GW_GO_PREFIX . 'skin_green', plugin_dir_url( __FILE__ ) . 'assets/css/go_pricing_skin_green.css', false, GW_GO_VER ); 
								} elseif ( preg_match( "/earth/", $col_style ) == 1 ) { wp_enqueue_style( GW_GO_PREFIX . 'skin_earth', plugin_dir_url( __FILE__ ) . 'assets/css/go_pricing_skin_earth.css', false, GW_GO_VER );
								} elseif ( preg_match( "/red/", $col_style ) == 1 ) { wp_enqueue_style( GW_GO_PREFIX . 'skin_red', plugin_dir_url( __FILE__ ) . 'assets/css/go_pricing_skin_red.css', false, GW_GO_VER );
								} elseif ( preg_match( "/yellow/", $col_style ) == 1 ) { wp_enqueue_style( GW_GO_PREFIX . 'skin_yellow', plugin_dir_url( __FILE__ ) . 'assets/css/go_pricing_skin_yellow.css', false, GW_GO_VER );
								} elseif ( preg_match( "/purple/", $col_style ) == 1 ) { wp_enqueue_style( GW_GO_PREFIX . 'skin_purple', plugin_dir_url( __FILE__ ) . 'assets/css/go_pricing_skin_purple.css', false, GW_GO_VER );
								} else {
									if ( isset( $custom_styles ) && !empty( $custom_styles ) ) :
										foreach ( $custom_styles as $custom_style_id => $custom_style ) : 
											if ( preg_match( "/" . $custom_style_id . "/", $col_style ) == 1  && isset ( $custom_style_id ) && !empty ( $custom_style_id ) && isset ( $custom_style['css'] ) && !empty ( $custom_style['css'] ) ) {
												wp_enqueue_style( GW_GO_PREFIX . 'skin_' . $custom_style_id, $custom_style['css'], false, GW_GO_VER );
											}
										endforeach;
									endif;									
								}
								
								$col_type = isset( $col_data[1] ) ? $col_data[1] : 'pricing';
								$generated_table .= '<div class="gw-go-col-wrap' . ( $pricing_table['col-highlight'][$x] == '1' ? ' gw-go-current gw-go-hover' : '' ) . ( isset( $pricing_table['col-disable-enlarge'][$x] ) && $pricing_table['col-disable-enlarge'][$x] == '1' ? ' gw-go-disable-enlarge' : '' ) . ( isset( $pricing_table['col-disable-hover'][$x] ) && $pricing_table['col-disable-hover'][$x] == '1' ? ' gw-go-disable-hover' : '' ) . ' gw-go-col-wrap-'. $x . '">';
								$generated_table .= '<div class="gw-go-col' . ( $col_style != '' ? ' ' . 'gw-go-' . $col_style : '' ) . ( $pricing_table['col-shadow'][$x] != '' ? ' ' . 'gw-go-' . $pricing_table['col-shadow'][$x] : '' ) . '">';
								
								/* ribbon */
								$ribbon_classes = array();
								$ribbon_styles = array();
								if ( isset( $pricing_table['col-ribbon'][$x] ) && $pricing_table['col-ribbon'][$x] == 'custom' ) {
									$ribbon_classes[]=isset( $pricing_table['col-custom-ribbon-align'][$x] ) && $pricing_table['col-custom-ribbon-align'][$x] != 'right' ? 'gw-go-ribbon-left' : 'gw-go-ribbon-right';
									$ribbon_styles[]=isset( $pricing_table['col-custom-ribbon'][$x] ) ? 'background: url(' . $pricing_table['col-custom-ribbon'][$x] . ') 0 0 no-repeat' : '';
								} elseif ( isset( $pricing_table['col-ribbon'][$x] ) ) {
									$ribbon_classes[]=preg_match("/left/", $pricing_table['col-ribbon'][$x] ) == 1 ? 'gw-go-ribbon-left' : 'gw-go-ribbon-right';
									$ribbon_classes[]='gw-go-ribbon-' . $pricing_table['col-ribbon'][$x];
								}
								if ( isset( $pricing_table['col-ribbon'][$x] ) && !empty( $pricing_table['col-ribbon'][$x] ) ) {
									$generated_table .= '<div' . ( !empty( $ribbon_classes ) ? ' class="' . implode( ' ', $ribbon_classes ) . '"' : '' ) . ( !empty( $ribbon_styles ) ? ' style="' .  implode( '; ', $ribbon_styles) . '"' : '' ) . '></div>';
								}
								
								/* header */
								if ( $pricing_table['col-replace'][$x]=='1' && $col_type != 'html' ) {
									if ( isset( $pricing_table['col-html'][$x]) && !empty( $pricing_table['col-html'][$x] ) ) {	$pricing_table['col-html'][$x] = preg_replace( '/\r\n+|\r+|\n+|\t+/i', '', $pricing_table['col-html'][$x] ); }
									if ( isset( $pricing_table['col-css'][$x]) && !empty( $pricing_table['col-css'][$x] ) ) {	$pricing_table['col-css'][$x] = preg_replace( '/\r\n+|\r+|\n+|\t+/i', '', $pricing_table['col-css'][$x] ); }									
									$generated_table .= '<div class="gw-go-header" style="'.$pricing_table['col-css'][$x].'">' . do_shortcode( $pricing_table['col-html'][$x] ) .'</div>';
								} else {
									/* pricing type header */
									if ( $col_type == 'pricing' ) {
										$generated_table .= '<div class="gw-go-header">';
										$generated_table .= '<div class="gw-go-header-top">';
										$generated_table .= '<h3>' . $pricing_table['col-title'][$x] . '</h3>' .
											'<div class="gw-go-coin-wrap">'.
											'<div class="gw-go-coinf"><div>' . $pricing_table['col-price'][$x] . '</div></div>' .
											'<div class="gw-go-coinb"><div>' . $pricing_table['col-price'][$x] . '</div></div>' .
											'</div></div><div class="gw-go-header-bottom"></div>' . 
											'</div>';
									}
									/* pricing2 type header */ 
									if ( $col_type == 'pricing2' ) {
										$generated_table .= '<div class="gw-go-header">';
										$generated_table .= '<div class="gw-go-header-top">';
										$generated_table .= '<h3>' . $pricing_table['col-title'][$x] . '</h3>' .
											'<div class="gw-go-coin-wrap">'.
											'<div class="gw-go-coinf"><div>' . $pricing_table['col-price'][$x] . '</div></div>' .
											'<div class="gw-go-coinb"><div>' . $pricing_table['col-price'][$x] . '</div></div>' .
											'</div></div><div class="gw-go-header-bottom" style="'.$pricing_table['col-pricing-css'][$x].'">' . do_shortcode( $pricing_table['col-pricing-html'][$x] ) .'</div>' . 
											'</div>';						
									}
									/* pricing3 type header */ 
									if ( $col_type == 'pricing3' ) {
										$generated_table .= '<div class="gw-go-header">';
										$generated_table .= '<h3>' . $pricing_table['col-title'][$x] . '</h3>' .
											'<div class="gw-go-coin-wrap">'.
											'<div class="gw-go-coinf"><div>' . $pricing_table['col-price'][$x] . '</div></div>' .
											'<div class="gw-go-coinb"><div>' . $pricing_table['col-price'][$x] . '</div></div>' .
											'</div><div class="gw-go-header-bottom" style="'.$pricing_table['col-pricing-css'][$x].'"><img src="' . $pricing_table['col-pricing-img'][$x] .'" class="gw-go-responsive-img" /></div>' . 
											'</div>';						
									}
									/* pricing3 type header */ 
									if ( $col_type == 'team' ) {
										$generated_table .= '<div class="gw-go-header">';
										$generated_table .= '<h3>' . $pricing_table['col-title'][$x] . '</h3>' .
											'<div class="gw-go-header-bottom" style="'.$pricing_table['col-pricing-css'][$x].'"><img src="' . $pricing_table['col-pricing-img'][$x] .'" class="gw-go-responsive-img" /></div>' . 
											'</div>';						
									}
									/* pricing3 type header */ 
									if ( $col_type == 'product' ) {
										$generated_table .= '<div class="gw-go-header">';
										$generated_table .= '<h3>' . $pricing_table['col-title'][$x] . '</h3>' .
											'<div class="gw-go-header-bottom" style="'.$pricing_table['col-pricing-css'][$x].'"><img src="' . $pricing_table['col-pricing-img'][$x] .'" class="gw-go-responsive-img" /></div>' . 
											$pricing_table['col-price'][$x] . 
											'</div>';						
									}		
								}
								
								/* html pricing */
								if ( $col_type == 'html' ) {
									$generated_table .= '<div class="gw-go-header" style="'.$pricing_table['col-css'][$x].'">' . do_shortcode( $pricing_table['col-html'][$x] ) .'</div>';
								}
								
								/* body */
								$row_count=0;
								$generated_table .=  '<ul class="gw-go-body">';
								for ( $y = $x*$rownum; $y < ( $x+1 )*$rownum ; $y++ ) {
									$styles = array();
									if ( isset( $pricing_table['tooltip-width'] ) ) { $styles[]='width:' . $pricing_table['tooltip-width']; }
									if ( isset( $pricing_table['tooltip-bg-color'] ) ) { 
										$styles[]='background:' . $pricing_table['tooltip-bg-color'];
										$styles[]='border-color:' . $pricing_table['tooltip-bg-color'];
									}
									if ( isset( $pricing_table['tooltip-text-color'] ) ) { $styles[]='color:' . $pricing_table['tooltip-text-color']; }
									$style = implode('; ', $styles);
									$classes = array();
									if ( $row_count % 2 == 1) { $classes[]='gw-go-even'; }
									$classes[] = 'gw-go-row-'. ( $y - ( $colnum*$x) );
									if ( isset( $pricing_table['col-detail-tip'][$y] ) && !empty( $pricing_table['col-detail-tip'][$y] ) ) { $classes[]='gw-go-has-tooltip'; }
									$generated_table .= '<li' . ( !empty( $classes ) ? ' class="' . implode( ' ', $classes ) . '"' : '' ) . ( $pricing_table['col-align'][$y]!='' ? ' style="text-align:' . ( $pricing_table['col-align'][$y] ) . ';"' : '' ) .' data-col="'. $x .'" data-row="' . ($y - ( $colnum*$x ) ) . '"><div class="gw-go-body-cell">' . do_shortcode( $pricing_table['col-detail'][$y] ) . '<span class="gw-go-tooltip"' . ( !empty( $styles ) ? ' style="'. $style . '" ' : '' ) . '>' . ( isset( $pricing_table['col-detail-tip'][$y] ) && !empty( $pricing_table['col-detail-tip'][$y] ) ? do_shortcode( $pricing_table['col-detail-tip'][$y] ) : '' ) . '</span></div></li>';
									$row_count++;
								}
								$generated_table .=  '</ul>';				
								
								/* footer */
								
								/* buttons */
								if ( !isset( $pricing_table['hide-footer'] ) || ( isset( $pricing_table['hide-footer'] ) && $pricing_table['hide-footer']==0 ) ) {
									$generated_table.='<div class="gw-go-footer"><div class="gw-go-btn-wrap"><div class="gw-go-btn-wrap-inner">';
									if ( isset( $pricing_table['col-button-text'][$x] ) && ($pricing_table['col-button-text'][$x] != '' || $pricing_table['col-button-type'][$x] == 'custom' ) ) {
										if ( isset( $pricing_table['col-button-type'][$x] ) && $pricing_table['col-button-type'][$x] == 'button' ) {
											$generated_table.='<a href="' . esc_attr( $pricing_table['col-button-link'][$x] ) . '" class="gw-go-btn gw-go-btn-' . ( $pricing_table['col-button-size'][$x] ) . '"' . ( isset( $pricing_table['col-button-target'][$x] ) && $pricing_table['col-button-target'][$x] == '1' ? ' target="_blank"' : '') . ( isset( $pricing_table['col-button-nofollow'][$x] ) && $pricing_table['col-button-nofollow'][$x] == '1' ? ' rel="nofollow"' : '') . '>' . do_shortcode( $pricing_table['col-button-text'][$x] ) . '</a>';
										} elseif ( isset( $pricing_table['col-button-type'][$x] ) && $pricing_table['col-button-type'][$x] == 'submit' ) {
											$generated_table.='<span class="gw-go-btn gw-go-btn-' . ( $pricing_table['col-button-size'][$x] ) . '">' . do_shortcode( $pricing_table['col-button-text'][$x] ) . do_shortcode( $pricing_table['col-button-link'][$x] ) . '</span>';
										} elseif ( isset( $pricing_table['col-button-type'][$x] ) && $pricing_table['col-button-type'][$x] == 'custom' ) {
											$generated_table.= do_shortcode( $pricing_table['col-button-link'][$x] );
										}
									}
									$generated_table.='</div></div></div>';
								}
								$generated_table.='</div>';
								$generated_table.='</div>';
							}
						$generated_table.='</div>';
						$generated_table.='</div>';
						$generated_table = do_shortcode( $generated_table );
						return $generated_table;
						}
					}
				}
			} else {
				/* if id is missing */
				return '<p>' . sprintf( __( 'You must specify a pricing table id.', GW_GO_TEXTDOMAN ), $id ) . '</p>';
			}
			
			/* if id is incorrect */
			if ( !isset( $generated_table ) ) { return '<p>' . sprintf( __( 'Pricing table with id of "%s" is not defined.', GW_GO_TEXTDOMAN ), $id ) . '</p>'; }
		}
		
		/* page for admin */
		public function go_pricing_menu_page_callback( $response=array() ) {
			$this->go_pricing_load_textdomain();
			?>
            <script type="text/javascript">
			/* translateable js messages object*/
			var	goPricingText = {
				'ajaxError' : '<?php _e( 'Oops, AJAX error!', GW_GO_TEXTDOMAN ); ?>',
				'clone' : '<?php _e( 'Clone', GW_GO_TEXTDOMAN ); ?>',
				'del' : '<?php _e( 'Delete', GW_GO_TEXTDOMAN ); ?>',
				'row' : '<?php _e( 'row', GW_GO_TEXTDOMAN ); ?>',	
				'col' : '<?php _e( 'column', GW_GO_TEXTDOMAN ); ?>',					
				'description' : '<?php _e( 'Description', GW_GO_TEXTDOMAN ); ?>',
				'tooltip' : '<?php _e( 'Tooltip', GW_GO_TEXTDOMAN ); ?>',
				'blue' : '<?php _e( 'Blue', GW_GO_TEXTDOMAN ); ?>',
				'earth' : '<?php _e( 'Earth', GW_GO_TEXTDOMAN ); ?>',
				'yellow' : '<?php _e( 'Yellow', GW_GO_TEXTDOMAN ); ?>',
				'purple' : '<?php _e( 'Purple', GW_GO_TEXTDOMAN ); ?>',
				'red' : '<?php _e( 'Red', GW_GO_TEXTDOMAN ); ?>',
				'green' : '<?php _e( 'Green', GW_GO_TEXTDOMAN ); ?>',
				'generalOptions' : '<?php _e( 'General options', GW_GO_TEXTDOMAN ); ?>',
				'setColStyle' : '<?php _e( 'Set global column style', GW_GO_TEXTDOMAN ); ?>',
				'highlightCol' : '<?php _e( 'Highlight column?', GW_GO_TEXTDOMAN ); ?>',
				'disableEnlargeCol' : '<?php _e( 'Disable enlarge?', GW_GO_TEXTDOMAN ); ?>',
				'disableHoverCol' : '<?php _e( 'Disable hover state?', GW_GO_TEXTDOMAN ); ?>',
				'yes' : '<?php _e( 'Yes', GW_GO_TEXTDOMAN ); ?>',
				'Style' : '<?php _e( 'Style', GW_GO_TEXTDOMAN ); ?>',
				'style' : '<?php _e( 'style', GW_GO_TEXTDOMAN ); ?>',
				'styles' : '<?php _e( 'styles', GW_GO_TEXTDOMAN ); ?>',
				'shadowStyle' : '<?php _e( 'Shadow style', GW_GO_TEXTDOMAN ); ?>',
				'shadow' : '<?php _e( 'shadow', GW_GO_TEXTDOMAN ); ?>',	
				'noShadow' : '<?php _e( 'No shadow', GW_GO_TEXTDOMAN ); ?>',
				'ribbon' : '<?php _e( 'Ribbon', GW_GO_TEXTDOMAN ); ?>',
				'ribbons' : '<?php _e( 'ribbons', GW_GO_TEXTDOMAN ); ?>',
				'noRibbon' : '<?php _e( 'No ribbon', GW_GO_TEXTDOMAN ); ?>',	
				'customRibbon' : '<?php _e( 'Custom ribbon (+add)', GW_GO_TEXTDOMAN ); ?>',
				'leftSide' : '<?php _e( 'left side', GW_GO_TEXTDOMAN ); ?>',
				'rightSide' : '<?php _e( 'right side', GW_GO_TEXTDOMAN ); ?>',					
				'ribbonAlign' : '<?php _e( 'Ribbon Align', GW_GO_TEXTDOMAN ); ?>',
				'alignLeft' : '<?php _e( 'Align left', GW_GO_TEXTDOMAN ); ?>',
				'alignRight' : '<?php _e( 'Align right', GW_GO_TEXTDOMAN ); ?>',	
				'expandAll' : '<?php _e( 'Expand all', GW_GO_TEXTDOMAN ); ?>',	
				'collapseAll' : '<?php _e( 'Collapse all', GW_GO_TEXTDOMAN ); ?>',	
				'buttonOptions' : '<?php _e( 'Button options', GW_GO_TEXTDOMAN ); ?>',
				'buttonSet' : '<?php _e( 'Set column button function', GW_GO_TEXTDOMAN ); ?>',
				'buttonType' : '<?php _e( 'Button type', GW_GO_TEXTDOMAN ); ?>',
				'buttonText' : '<?php _e( 'Button content', GW_GO_TEXTDOMAN ); ?>',	
				'buttonLink' : '<?php _e( 'Button link / Paypal button code or shortcode', GW_GO_TEXTDOMAN ); ?>',
				'buttonSize' : '<?php _e( 'Button size', GW_GO_TEXTDOMAN ); ?>',
				'buttonOpen' : '<?php _e( 'Open in new window?', GW_GO_TEXTDOMAN ); ?>',
				'buttonNofollow' : '<?php _e( 'Nofollow link?', GW_GO_TEXTDOMAN ); ?>',						
				'pricingHeader' : '<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>',
				'htmlHeader' : '<?php _e( 'html header', GW_GO_TEXTDOMAN ); ?>',
				'pricingHtmlHeader' : '<?php _e( 'pricing with html header', GW_GO_TEXTDOMAN ); ?>',
				'pricingImgHeader' : '<?php _e( 'pricing with full image header', GW_GO_TEXTDOMAN ); ?>',
				'productHeader' : '<?php _e( 'product pricing header', GW_GO_TEXTDOMAN ); ?>',
				'teamHeader' : '<?php _e( 'team header', GW_GO_TEXTDOMAN ); ?>',
				'bodyOptions' : '<?php _e( 'Body options', GW_GO_TEXTDOMAN ); ?>',
				'bodySet' : '<?php _e( 'Set column main content', GW_GO_TEXTDOMAN ); ?>',
				'addDetail' : '<?php _e( 'Add row', GW_GO_TEXTDOMAN ); ?>',
				'headerOptions' : '<?php _e( 'Header options', GW_GO_TEXTDOMAN ); ?>',
				'headerSet' : '<?php _e( 'Set column header content', GW_GO_TEXTDOMAN ); ?>',
				'colTitle' : '<?php _e( 'Column title', GW_GO_TEXTDOMAN ); ?>',					
				'price' : '<?php _e( 'Price', GW_GO_TEXTDOMAN ); ?>',
				'addImg' : '<?php _e( 'Add image', GW_GO_TEXTDOMAN ); ?>',	
				'selectImg' : '<?php _e( 'Select an image', GW_GO_TEXTDOMAN ); ?>',	
				'htmlContent' : '<?php _e( 'HTML content', GW_GO_TEXTDOMAN ); ?>',	
				'cssExtension' : '<?php _e( 'CSS extension', GW_GO_TEXTDOMAN ); ?>',	
				'replaceDefault' : '<?php _e( 'Replace default header type?', GW_GO_TEXTDOMAN ); ?>',
				'medium' : '<?php _e( 'Medium', GW_GO_TEXTDOMAN ); ?>',
				'large' : '<?php _e( 'Large', GW_GO_TEXTDOMAN ); ?>',
				'small' : '<?php _e( 'Small', GW_GO_TEXTDOMAN ); ?>',
				'regButton' : '<?php _e( 'Regular button', GW_GO_TEXTDOMAN ); ?>',
				'submitButton' : '<?php _e( 'Form submit button (e.g. Paypal)', GW_GO_TEXTDOMAN ); ?>',
				'customButton' : '<?php _e( 'Custom button', GW_GO_TEXTDOMAN ); ?>',
				'scEditor' : '<?php _e( 'Shortcode editor', GW_GO_TEXTDOMAN ); ?>',
				'addSc' : '<?php _e( 'Add shortcode', GW_GO_TEXTDOMAN ); ?>',
				'maxCol' : '<?php _e( 'You have reached the maximum number of columns!', GW_GO_TEXTDOMAN ); ?>',
				'insert' : '<?php _e( 'Insert', GW_GO_TEXTDOMAN ); ?>'
			};
			</script>		
            <?php $plugin_dir_url = plugin_dir_url( __FILE__ ); ?>
			<div id="go-pricing-admin-wrap" class="wrap" data-id="<?php echo $_SERVER['REQUEST_URI']; ?>" data-plugin-url="<?php echo $plugin_dir_url; ?>" data-admin-url="<?php echo admin_url(); ?>">
			<div id="go-pricing-admin-icon" class="icon32"></div>
		    <h2><?php _e( 'Go - Responsive Pricing & Compare Tables', GW_GO_TEXTDOMAN ); ?></h2>
		    <p></p>
			<?php if ( !empty( $response ) ) : ?>
            <div id="result" class="<?php echo $response['result'] == 'error' ? 'error' : 'updated'; ?>">
            <?php foreach ( $response['message'] as $error_msg ) : ?>
                <p><strong><?php echo $error_msg; ?></strong></p>
            <?php endforeach;  $response = array(); ?>
            </div>
            <?php
            endif;
			
			$pricing_tables = get_option( GW_GO_PREFIX . 'tables' );

			/* load default page -> pricing table selector */
			if ( !$_POST ) : ?>
            <form id="go-pricing-form" name="form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" data-ajaxerrormsg="<?php _e( 'Oops, AJAX error!', GW_GO_TEXTDOMAN ); ?>">
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( GW_GO_PREFIX . basename( __FILE__ ) ); ?>" />
                <input type="hidden" id="action-type" name="action-type" value="select" />
                <div id="go-pricing-settings-wrapper" class="postbox">
                    <h3 class="hndle"><?php _e( 'Table creator', GW_GO_TEXTDOMAN ); ?><span></span></h3>
                    <div class="inside go-pricing-thumbs-wrapper">
                    	<div class="postbox inside go-pricing-thumbs">
                        <input type="hidden" id="go-pricing-select" name="select" />
						<?php 
						if ( !empty( $pricing_tables ) ) : 
                        foreach( $pricing_tables as $key=>$value ) : 
						$filename = explode( 'demo-', $value['table-id'] );
						if ( isset( $filename[1] ) && file_exists( plugin_dir_path( __FILE__ ) . 'assets/images/thumbnails/' . $filename[1] . '.jpg' ) ) {
							$imgsrc = $plugin_dir_url . 'assets/images/thumbnails/' . $filename[1] . '.jpg';
						} else {
							$imgsrc = apply_filters( 'go_pricing_custom_thumbs_filter', $plugin_dir_url . 'assets/images/thumbnails/custom.jpg', $value['table-id'] );
						}
		                ?>
                        <a href="#" class="go-pricing-thumb" data-id="<?php echo esc_attr( $key ); ?>"><img src="<?php echo esc_attr( $imgsrc ); ?>" width="200" height="97" class="go-pricing-img-thumb" /><span><?php echo esc_attr( $value['table-name'] ); ?></span></a>
                        <?php endforeach; endif; ?>						
                        <a href="#" class="go-pricing-thumb-create"><img src="<?php echo esc_attr( $plugin_dir_url . 'admin/images/add_table.png' ); ?>" width="200" height="97" class="go-pricing-img-thumb" /><span><?php esc_attr_e( 'Create a new table', GW_GO_TEXTDOMAN ); ?></span></a>
                        <div style="clear:both;"></div>
                        </div>
                	</div>
                </div>                
                <p class="submit">
                    <input name="edit" type="button" class="button-primary go-pricing-edit" data-edit="<?php esc_attr_e( 'Edit', GW_GO_TEXTDOMAN ); ?>" data-create="<?php esc_attr_e( 'Create a new table', GW_GO_TEXTDOMAN ); ?>" value="<?php esc_attr_e( 'Create a new table', GW_GO_TEXTDOMAN ); ?>" />
					<input name="copy" type="button" class="button-secondary go-pricing-copy" value="<?php esc_attr_e( 'Clone', GW_GO_TEXTDOMAN ); ?>" />                    
                    <input name="save" type="submit" class="button-secondary go-pricing-delete" value="<?php esc_attr_e( 'Delete', GW_GO_TEXTDOMAN ); ?>" />
                    <img src="<?php echo admin_url(); ?>/images/wpspin_light.gif" class="ajax-loading" alt="" />
                </p>
            </form>
        	<?php 
			endif;
			
            /* add new or edit pricing table */
            if ( $_POST && ( $_POST['action-type'] == "select" || $_POST['action-type'] == "edit" ) ) : 

		   	/* create or get unique_id */
		   	$uniqid = isset( $_POST['select'] ) ? ( empty( $_POST['select'] ) ? uniqid() : $_POST['select'] ) : $_POST['uniqid'];
			
			/* get field data or set default values */
			$table_name = isset( $pricing_tables[$uniqid] ) ? $pricing_tables[$uniqid]['table-name'] : '';
			$table_id = isset( $pricing_tables[$uniqid] ) ? $pricing_tables[$uniqid]['table-id'] : '';
			$table_enlarge_current = isset( $pricing_tables[$uniqid] ) ? $pricing_tables[$uniqid]['enlarge-current'] : '1';
			$table_equal_height = isset( $pricing_tables[$uniqid] ) ? $pricing_tables[$uniqid]['equal-height'] : '1';
			$table_colspace = isset( $pricing_tables[$uniqid] ) ? $pricing_tables[$uniqid]['colspace'] : '';
			$tooltip_width = isset( $pricing_tables[$uniqid] ) ? $pricing_tables[$uniqid]['tooltip-width'] : '130px';			
			$tooltip_bg_color = isset( $pricing_tables[$uniqid] ) ? $pricing_tables[$uniqid]['tooltip-bg-color'] : '#9D9D9D';
			$tooltip_text_color = isset( $pricing_tables[$uniqid] ) ? $pricing_tables[$uniqid]['tooltip-text-color'] : '#333333';
			$table_hide_footer = isset( $pricing_tables[$uniqid] ) ? $pricing_tables[$uniqid]['hide-footer'] : '0';
			?>
            <form id="go-pricing-form" name="form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( GW_GO_PREFIX . basename( __FILE__ ) ); ?>" />
                <input type="hidden" id="action-type" name="action-type" value="edit" />
                <input type="hidden" name="uniqid" value="<?php echo $uniqid; ?>" />
                <div id="go-pricing-settings-wrapper" class="postbox">
                    <h3 class="hndle"><?php _e( 'Global table settings', GW_GO_TEXTDOMAN ); ?><span></span></h3>
                    <div class="inside">
                        <table class="form-table">
                            <tr>
                                <th class="w150"><label for="go-pricing-table-name"><?php _e( 'Pricing table name', GW_GO_TEXTDOMAN ); ?></label></th>
                                <td class="w300"><input type="text" name="table-name" id="go-pricing-table-name" value="<?php echo esc_attr( $table_name ); ?>" class="w255" /></td>
                                <td><p class="description"><?php _e( 'Name for pricing table, only used in admin area.', GW_GO_TEXTDOMAN ); ?></p></td>
                            </tr>
                            <tr>
                                <th class="w150"><label for="go-pricing-table-id"><?php _e( 'Pricing table id' , GW_GO_TEXTDOMAN ); ?></label></th>
                                <td class="w300"><input type="text" name="table-id" id="go-pricing-table-id" value="<?php echo esc_attr( $table_id ); ?>" class="w255" /></td>
                                <td><p class="description"><?php _e( 'Unique id, used in shortcodes (e.g. if table id is: "my_table" the shortcode will be: [go_pricing id="my_table"]).', GW_GO_TEXTDOMAN ); ?></p></td>
                            </tr>
						</table>
                        <div class="go-pricing-separator"></div>                        
                        <table class="form-table">    
                            <tr>
                                <th class="w150"><label for="go-pricing-enlarge-current"><?php _e( 'Enlarge current column?', GW_GO_TEXTDOMAN ); ?></label></th>
                                <td class="w300"><input id="go-pricing-enlarge-current" type="checkbox" name="enlarge-current-chk"<?php echo $table_enlarge_current == '1' ? ' checked="checked"' : ''; ?> /><input type="hidden" name="enlarge-current" value="<?php echo $table_enlarge_current == '1' ? '1' : '0'; ?>" />&nbsp;<?php _e( 'Yes', GW_GO_TEXTDOMAN ); ?></td>
                                <td><p class="description"><?php _e( 'Whether to increase the size of the current column.', GW_GO_TEXTDOMAN ); ?></p></td>
                            </tr>                           
                            <tr>
                                <th class="w150"><label for="go-pricing-colspace"><?php _e( 'Space between columns', GW_GO_TEXTDOMAN ); ?></label></th>
                                <td class="w300">
                                	<select id="go-pricing-colspace" name="colspace" class="w255 widefat">
                                    	<option value=""<?php echo $table_colspace == '' ? ' selected="selected"' : ''; ?>><?php _e( 'No space', GW_GO_TEXTDOMAN ); ?></option>
                                    	<option value="gw-go-space-1p"<?php echo $table_colspace == 'gw-go-space-1p' ? ' selected="selected"' : ''; ?>>1% <?php _e( 'space', GW_GO_TEXTDOMAN ); ?></option>
                                    	<option value="gw-go-space-2p"<?php echo $table_colspace == 'gw-go-space-2p' ? ' selected="selected"' : ''; ?>>2% <?php _e( 'space', GW_GO_TEXTDOMAN ); ?></option>
                                    	<option value="gw-go-space-3p"<?php echo $table_colspace == 'gw-go-space-3p' ? ' selected="selected"' : ''; ?>>3% <?php _e( 'space', GW_GO_TEXTDOMAN ); ?></option>
                                    	<option value="gw-go-space-4p"<?php echo $table_colspace == 'gw-go-space-4p' ? ' selected="selected"' : ''; ?>>4% <?php _e( 'space', GW_GO_TEXTDOMAN ); ?></option>
                                    	<option value="gw-go-space-5p"<?php echo $table_colspace == 'gw-go-space-5p' ? ' selected="selected"' : ''; ?>>5% <?php _e( 'space', GW_GO_TEXTDOMAN ); ?></option>                                                                                                                                                                
                                	</select>
                                <td><p class="description"><?php _e( 'Space between two columns.', GW_GO_TEXTDOMAN ); ?></p></td>
                            </tr>
                            <tr>
                                <th class="w150"><label for="go-pricing-equal-height"><?php _e( 'Equalize row height?', GW_GO_TEXTDOMAN ); ?></label></th>
                                <td class="w300"><input id="go-pricing-equal-height" type="checkbox" name="equal-height-chk"<?php echo $table_equal_height == '1' ? ' checked="checked"' : ''; ?> /><input type="hidden" name="equal-height" value="<?php echo $table_equal_height == '1' ? '1' : '0'; ?>" />&nbsp;<?php _e( 'Yes', GW_GO_TEXTDOMAN ); ?></td>
                                <td><p class="description"><?php _e( 'Whether to equalize the description rows height.', GW_GO_TEXTDOMAN ); ?></p></td>
                            </tr>							
						</table>
                        <div class="go-pricing-separator"></div>
                        <table class="form-table">                                                                                                                             
                            <tr>
                                <th class="w150"><label for="go-pricing-tooltip-width"><?php _e( 'Tooltip width', GW_GO_TEXTDOMAN ); ?></label></th>
                                <td class="w300"><input type="text" name="tooltip-width" id="go-pricing-tooltip-width" value="<?php echo esc_attr( $tooltip_width ); ?>" class="w255" /></td>
                                <td><p class="description"><?php _e( 'Width of the tooltip (in pixels).', GW_GO_TEXTDOMAN ); ?></strong></p></td>
                            </tr>
                            <tr>
                                <th class="w150"><label for="go-pricing-tooltip-bg-color"><?php _e( 'Tooltip background color', GW_GO_TEXTDOMAN ); ?></label></th>
                                <td class="w300"><input type="text" name="tooltip-bg-color" id="go-pricing-tooltip-bg-color" value="<?php echo esc_attr( $tooltip_bg_color ); ?>" class="w255" style="background-color:<?php echo esc_attr( $tooltip_bg_color ); ?>;" /></td>
                                <td><p class="description"><?php _e( 'Background color of the tooltip (you can use colorpicker).', GW_GO_TEXTDOMAN ); ?></strong></p></td>
                            </tr>
                            <tr style="display:none;"><th class="w150"></th><td colspan="2"><div class="go-pricing-colorpicker"></div></tr>
                            <tr>
                                <th class="w150"><label for="go-pricing-tooltip-text-color"><?php _e( 'Tooltip text color', GW_GO_TEXTDOMAN ); ?></label></th>
                                <td class="w300"><input type="text" name="tooltip-text-color" id="go-pricing-tooltip-text-color" value="<?php echo esc_attr( $tooltip_text_color ); ?>" class="w255" style="background-color:<?php echo esc_attr( $tooltip_text_color ); ?>;" /></td>
                                <td><p class="description"><?php _e( 'Text color of the tooltip (you can use colorpicker).', GW_GO_TEXTDOMAN ); ?></strong></p></td>
                            </tr>
                            <tr style="display:none;"><th class="w150"></th><td colspan="2"><div class="go-pricing-colorpicker"></div></tr>                       
                        </table>
						<div class="go-pricing-separator"></div>
						<table class="form-table">
                            <tr>
                                <th class="w150"><label for="go-pricing-hide-footer"><?php _e( 'Hide footer?', GW_GO_TEXTDOMAN ); ?></label></th>
                                <td class="w300"><input id="go-pricing-hide-footer" type="checkbox" name="hide-footer-chk"<?php echo $table_hide_footer == '1' ? ' checked="checked"' : ''; ?> /><input type="hidden" name="hide-footer" value="<?php echo $table_hide_footer == '1' ? '1' : '0'; ?>" />&nbsp;<?php _e( 'Yes', GW_GO_TEXTDOMAN ); ?></td>
                                <td><p class="description"><?php _e( 'Whether to hide the table footer', GW_GO_TEXTDOMAN ); ?></p></td>
                            </tr>						
						</table>
                    </div>
                </div>
                <p class="submit">
                    <input name="cancel" type="button" class="button-secondary go-pricing-cancel" value="<?php esc_attr_e( 'Back', GW_GO_TEXTDOMAN ); ?>" />
                    <input name="save" type="submit" class="button-primary go-pricing-save" value="<?php esc_attr_e( 'Save', GW_GO_TEXTDOMAN ); ?>" />
                    <img src="<?php echo admin_url(); ?>/images/wpspin_light.gif" class="ajax-loading" alt="" />
                </p>				
                <div class="go-pricing-space"></div>
                <div id="go-pricing-column-wrapper" class="postbox">
                    <h3 class="hndle"><?php _e( 'Column settings', GW_GO_TEXTDOMAN ); ?><span></span></h3>
                    <div class="inside">
                        <ul class="go-pricing-columns">
						<?php 
						$custom_styles = get_option( GW_GO_PREFIX . 'custom_styles' );					
						if ( isset( $custom_styles ) && !empty( $custom_styles ) ) :
						?>
						<script type="text/javascript">
						goPricingCustomStyles = <?php echo json_encode ( $custom_styles ); ?> || []
						</script>
						<?php				
						endif;						
                        if ( isset( $pricing_tables[$uniqid]['col-style'] ) ) :
                        $colnum = count( $pricing_tables[$uniqid]['col-style'] );
                        $rownum = isset( $pricing_tables[$uniqid]['col-detail'] ) ? count( $pricing_tables[$uniqid]['col-detail'] )/$colnum : 0;
                        for ( $x = 0; $x < $colnum; $x++ ) :
                        ?>                        
                        <li class="go-pricing-column">
                        	<div class="postbox">
                            <h3 class="hndle"><div class="go-pricing-handle-icon-general-options"><?php _e( 'General options', GW_GO_TEXTDOMAN ); ?><small><?php _e( 'Set global column style', GW_GO_TEXTDOMAN ); ?></small></div><span class="go-pricing-closed"></span></h3>
                            <div class="inside">
                                <table class="form-table">
                                    <tr>
                                        <th class="w100"><label><?php _e( 'Highlight column?', GW_GO_TEXTDOMAN ); ?></label></th>                                          
                                        <td><input type="checkbox" name="col-highlight-chk[]"<?php echo isset( $pricing_tables[$uniqid]['col-highlight'][$x] ) && $pricing_tables[$uniqid]['col-highlight'][$x]=='1' ? ' checked="checked"' : ''; ?> /><input type="hidden" name="col-highlight[]" value="<?php echo isset( $pricing_tables[$uniqid]['col-highlight'][$x] ) && $pricing_tables[$uniqid]['col-highlight'][$x]=='1' ? '1' : '0'; ?>" />&nbsp;<?php _e( 'Yes', GW_GO_TEXTDOMAN ); ?></td>                                        
                                    </tr>
                                    <tr>
                                        <th class="w100"><label><?php _e( 'Disable enlarge?', GW_GO_TEXTDOMAN ); ?></label></th>                                          
                                        <td><input type="checkbox" name="col-disable-enlarge-chk[]"<?php echo isset( $pricing_tables[$uniqid]['col-disable-enlarge'][$x] ) && $pricing_tables[$uniqid]['col-disable-enlarge'][$x]=='1' ? ' checked="checked"' : ''; ?> /><input type="hidden" name="col-disable-enlarge[]" value="<?php echo isset( $pricing_tables[$uniqid]['col-disable-enlarge'][$x] ) && $pricing_tables[$uniqid]['col-disable-enlarge'][$x]=='1' ? '1' : '0'; ?>" />&nbsp;<?php _e( 'Yes', GW_GO_TEXTDOMAN ); ?></td>                                        
                                    </tr>
                                    <tr>
                                        <th class="w100"><label><?php _e( 'Disable hover state?', GW_GO_TEXTDOMAN ); ?></label></th>                                          
                                        <td><input type="checkbox" name="col-disable-hover-chk[]"<?php echo isset( $pricing_tables[$uniqid]['col-disable-hover'][$x] ) && $pricing_tables[$uniqid]['col-disable-hover'][$x]=='1' ? ' checked="checked"' : ''; ?> /><input type="hidden" name="col-disable-hover[]" value="<?php echo isset( $pricing_tables[$uniqid]['col-disable-hover'][$x] ) && $pricing_tables[$uniqid]['col-disable-hover'][$x]=='1' ? '1' : '0'; ?>" />&nbsp;<?php _e( 'Yes', GW_GO_TEXTDOMAN ); ?></td>                                        
                                    </tr>																		
                                    <tr>
                                        <th class="w100"><label><?php _e( 'Style', GW_GO_TEXTDOMAN ); ?></label></th>
                                        <td>
                                            <select name="col-style[]" class="w255 widefat">
												<?php																							
												if ( isset( $custom_styles ) && !empty( $custom_styles ) ) :
												foreach ( $custom_styles as $custom_style ) : 
												if ( isset( $custom_style['name'] ) && !empty( $custom_style['name'] ) ) :
												?>
												<optgroup label="<?php echo esc_attr ( $custom_style['name'] ); ?> <?php _e( 'styles', GW_GO_TEXTDOMAN ); ?>"></optgroup>
												<?php
												if ( isset( $custom_style['styles'] ) && !empty( $custom_style['styles'] ) ) :
												foreach ( $custom_style['styles'] as $current_custom_style ) : 
												?>
												<option value="<?php echo esc_attr( $current_custom_style['id'] ); ?>"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]==$current_custom_style['id'] ? ' selected="selected"' : ''; ?>><?php echo $current_custom_style['name']; ?> (<?php echo $current_custom_style['type']; ?>)</option>
												<?php
												endforeach;
												endif;
												endif;
												endforeach;
												endif;
												?>
                                                <optgroup label="<?php _e( 'Blue', GW_GO_TEXTDOMAN ); ?> <?php _e( 'styles', GW_GO_TEXTDOMAN ); ?>"></optgroup>
                                                <option value="blue1_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='blue1_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Blue', GW_GO_TEXTDOMAN ); ?>1 (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="blue2_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='blue2_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Blue', GW_GO_TEXTDOMAN ); ?>2 (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="blue3a_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='blue3a_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Blue', GW_GO_TEXTDOMAN ); ?>3a (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="blue3b_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='blue3b_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Blue', GW_GO_TEXTDOMAN ); ?>3b (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="blue3c_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='blue3c_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Blue', GW_GO_TEXTDOMAN ); ?>3c (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="blue3d_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='blue3d_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Blue', GW_GO_TEXTDOMAN ); ?>3d (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="blue4a_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='blue4a_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Blue', GW_GO_TEXTDOMAN ); ?>4a (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="blue4b_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='blue4b_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Blue', GW_GO_TEXTDOMAN ); ?>4b (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="blue4c_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='blue4c_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Blue', GW_GO_TEXTDOMAN ); ?>4c (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="blue4d_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='blue4d_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Blue', GW_GO_TEXTDOMAN ); ?>4d (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="blue5_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='blue5_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Blue', GW_GO_TEXTDOMAN ); ?>5 (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="blue6_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='blue6_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Blue', GW_GO_TEXTDOMAN ); ?>6 (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="blue7_html"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='blue7_html' ? ' selected="selected"' : ''; ?>><?php _e( 'Blue', GW_GO_TEXTDOMAN ); ?>7 (<?php _e( 'html header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="blue8_html"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='blue8_html' ? ' selected="selected"' : ''; ?>><?php _e( 'Blue', GW_GO_TEXTDOMAN ); ?>8 (<?php _e( 'html header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="blue9_pricing2"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='blue9_pricing2' ? ' selected="selected"' : ''; ?>><?php _e( 'Blue', GW_GO_TEXTDOMAN ); ?>9 (<?php _e( 'pricing with html header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="blue10_pricing3"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='blue10_pricing3' ? ' selected="selected"' : ''; ?>><?php _e( 'Blue', GW_GO_TEXTDOMAN ); ?>10 (<?php _e( 'pricing with full image header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="blue11a_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='blue11a_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Blue', GW_GO_TEXTDOMAN ); ?>11a (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="blue11b_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='blue11b_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Blue', GW_GO_TEXTDOMAN ); ?>11b (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="blue11c_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='blue11c_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Blue', GW_GO_TEXTDOMAN ); ?>11c (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
												<option value="blue11d_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='blue11d_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Blue', GW_GO_TEXTDOMAN ); ?>11d (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>                                                
                                                <option value="blue12_team"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='blue12_team' ? ' selected="selected"' : ''; ?>><?php _e( 'Blue', GW_GO_TEXTDOMAN ); ?>12 (<?php _e( 'team header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="blue13_product"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='blue13_product' ? ' selected="selected"' : ''; ?>><?php _e( 'Blue', GW_GO_TEXTDOMAN ); ?>13 (<?php _e( 'product pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="blue14_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='blue14_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Blue', GW_GO_TEXTDOMAN ); ?>14 (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="blue15_html"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='blue15_html' ? ' selected="selected"' : ''; ?>><?php _e( 'Blue', GW_GO_TEXTDOMAN ); ?>15 (<?php _e( 'html header', GW_GO_TEXTDOMAN ); ?>)</option>                                                                                                                                                                                                                                                                                                                      
                                                <optgroup label="<?php _e( 'Green', GW_GO_TEXTDOMAN ); ?> <?php _e( 'styles', GW_GO_TEXTDOMAN ); ?>"></optgroup>
                                                <option value="green1_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='green1_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Green', GW_GO_TEXTDOMAN ); ?>1 (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="green2_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='green2_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Green', GW_GO_TEXTDOMAN ); ?>2 (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="green3a_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='green3a_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Green', GW_GO_TEXTDOMAN ); ?>3a (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="green3b_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='green3b_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Green', GW_GO_TEXTDOMAN ); ?>3b (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="green3c_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='green3c_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Green', GW_GO_TEXTDOMAN ); ?>3c (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="green3d_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='green3d_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Green', GW_GO_TEXTDOMAN ); ?>3d (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="green4a_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='green4a_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Green', GW_GO_TEXTDOMAN ); ?>4a (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="green4b_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='green4b_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Green', GW_GO_TEXTDOMAN ); ?>4b (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="green4c_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='green4c_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Green', GW_GO_TEXTDOMAN ); ?>4c (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="green4d_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='green4d_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Green', GW_GO_TEXTDOMAN ); ?>4d (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="green5_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='green5_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Green', GW_GO_TEXTDOMAN ); ?>5 (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="green6_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='green6_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Green', GW_GO_TEXTDOMAN ); ?>6 (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="green7_html"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='green7_html' ? ' selected="selected"' : ''; ?>><?php _e( 'Green', GW_GO_TEXTDOMAN ); ?>7 (<?php _e( 'html header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="green8_html"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='green8_html' ? ' selected="selected"' : ''; ?>><?php _e( 'Green', GW_GO_TEXTDOMAN ); ?>8 (<?php _e( 'html header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="green9_pricing2"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='green9_pricing2' ? ' selected="selected"' : ''; ?>><?php _e( 'Green', GW_GO_TEXTDOMAN ); ?>9 (<?php _e( 'pricing with html header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="green10_pricing3"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='green10_pricing3' ? ' selected="selected"' : ''; ?>><?php _e( 'Green', GW_GO_TEXTDOMAN ); ?>10 (<?php _e( 'pricing with full image header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="green11a_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='green11a_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Green', GW_GO_TEXTDOMAN ); ?>11a (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="green11b_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='green11b_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Green', GW_GO_TEXTDOMAN ); ?>11b (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="green11c_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='green11c_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Green', GW_GO_TEXTDOMAN ); ?>11c (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
												<option value="green11d_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='green11d_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Green', GW_GO_TEXTDOMAN ); ?>11d (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>                                                
                                                <option value="green12_team"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='green12_team' ? ' selected="selected"' : ''; ?>><?php _e( 'Green', GW_GO_TEXTDOMAN ); ?>12 (<?php _e( 'team header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="green13_product"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='green13_product' ? ' selected="selected"' : ''; ?>><?php _e( 'Green', GW_GO_TEXTDOMAN ); ?>13 (<?php _e( 'product pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="green14_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='green14_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Green', GW_GO_TEXTDOMAN ); ?>14 (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="green15_html"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='green15_html' ? ' selected="selected"' : ''; ?>><?php _e( 'Green', GW_GO_TEXTDOMAN ); ?>15 (<?php _e( 'html header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <optgroup label="<?php _e( 'Red', GW_GO_TEXTDOMAN ); ?> <?php _e( 'styles', GW_GO_TEXTDOMAN ); ?>"></optgroup>
                                                <option value="red1_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='red1_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Red', GW_GO_TEXTDOMAN ); ?>1 (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="red2_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='red2_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Red', GW_GO_TEXTDOMAN ); ?>2 (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="red3a_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='red3a_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Red', GW_GO_TEXTDOMAN ); ?>3a (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="red3b_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='red3b_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Red', GW_GO_TEXTDOMAN ); ?>3b (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="red3c_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='red3c_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Red', GW_GO_TEXTDOMAN ); ?>3c (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="red3d_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='red3d_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Red', GW_GO_TEXTDOMAN ); ?>3d (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="red4a_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='red4a_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Red', GW_GO_TEXTDOMAN ); ?>4a (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="red4b_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='red4b_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Red', GW_GO_TEXTDOMAN ); ?>4b (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="red4c_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='red4c_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Red', GW_GO_TEXTDOMAN ); ?>4c (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="red4d_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='red4d_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Red', GW_GO_TEXTDOMAN ); ?>4d (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="red5_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='red5_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Red', GW_GO_TEXTDOMAN ); ?>5 (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="red6_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='red6_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Red', GW_GO_TEXTDOMAN ); ?>6 (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="red7_html"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='red7_html' ? ' selected="selected"' : ''; ?>><?php _e( 'Red', GW_GO_TEXTDOMAN ); ?>7 (<?php _e( 'html header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="red8_html"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='red8_html' ? ' selected="selected"' : ''; ?>><?php _e( 'Red', GW_GO_TEXTDOMAN ); ?>8 (<?php _e( 'html header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="red9_pricing2"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='red9_pricing2' ? ' selected="selected"' : ''; ?>><?php _e( 'Red', GW_GO_TEXTDOMAN ); ?>9 (<?php _e( 'pricing with html header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="red10_pricing3"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='red10_pricing3' ? ' selected="selected"' : ''; ?>><?php _e( 'Red', GW_GO_TEXTDOMAN ); ?>10 (<?php _e( 'pricing with full image header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="red11a_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='red11a_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Red', GW_GO_TEXTDOMAN ); ?>11a (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="red11b_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='red11b_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Red', GW_GO_TEXTDOMAN ); ?>11b (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="red11c_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='red11c_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Red', GW_GO_TEXTDOMAN ); ?>11c (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
												<option value="red11d_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='red11d_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Red', GW_GO_TEXTDOMAN ); ?>11d (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>                                                
                                                <option value="red12_team"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='red12_team' ? ' selected="selected"' : ''; ?>><?php _e( 'Red', GW_GO_TEXTDOMAN ); ?>12 (<?php _e( 'team header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="red13_product"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='red13_product' ? ' selected="selected"' : ''; ?>><?php _e( 'Red', GW_GO_TEXTDOMAN ); ?>13 (<?php _e( 'product pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="red14_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='red14_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Red', GW_GO_TEXTDOMAN ); ?>14 (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="red15_html"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='red15_html' ? ' selected="selected"' : ''; ?>><?php _e( 'Red', GW_GO_TEXTDOMAN ); ?>15 (<?php _e( 'html header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <optgroup label="<?php _e( 'Purple', GW_GO_TEXTDOMAN ); ?> <?php _e( 'styles', GW_GO_TEXTDOMAN ); ?>"></optgroup>
                                                <option value="purple1_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='purple1_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Purple', GW_GO_TEXTDOMAN ); ?>1 (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="purple2_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='purple2_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Purple', GW_GO_TEXTDOMAN ); ?>2 (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="purple3a_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='purple3a_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Purple', GW_GO_TEXTDOMAN ); ?>3a (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="purple3b_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='purple3b_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Purple', GW_GO_TEXTDOMAN ); ?>3b (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="purple3c_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='purple3c_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Purple', GW_GO_TEXTDOMAN ); ?>3c (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="purple3d_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='purple3d_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Purple', GW_GO_TEXTDOMAN ); ?>3d (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="purple4a_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='purple4a_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Purple', GW_GO_TEXTDOMAN ); ?>4a (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="purple4b_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='purple4b_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Purple', GW_GO_TEXTDOMAN ); ?>4b (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="purple4c_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='purple4c_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Purple', GW_GO_TEXTDOMAN ); ?>4c (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="purple4d_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='purple4d_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Purple', GW_GO_TEXTDOMAN ); ?>4d (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="purple5_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='purple5_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Purple', GW_GO_TEXTDOMAN ); ?>5 (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="purple6_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='purple6_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Purple', GW_GO_TEXTDOMAN ); ?>6 (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="purple7_html"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='purple7_html' ? ' selected="selected"' : ''; ?>><?php _e( 'Purple', GW_GO_TEXTDOMAN ); ?>7 (<?php _e( 'html header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="purple8_html"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='purple8_html' ? ' selected="selected"' : ''; ?>><?php _e( 'Purple', GW_GO_TEXTDOMAN ); ?>8 (<?php _e( 'html header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="purple9_pricing2"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='purple9_pricing2' ? ' selected="selected"' : ''; ?>><?php _e( 'Purple', GW_GO_TEXTDOMAN ); ?>9 (<?php _e( 'pricing with html header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="purple10_pricing3"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='purple10_pricing3' ? ' selected="selected"' : ''; ?>><?php _e( 'Purple', GW_GO_TEXTDOMAN ); ?>10 (<?php _e( 'pricing with full image header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="purple11a_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='purple11a_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Purple', GW_GO_TEXTDOMAN ); ?>11a (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="purple11b_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='purple11b_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Purple', GW_GO_TEXTDOMAN ); ?>11b (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="purple11c_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='purple11c_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Purple', GW_GO_TEXTDOMAN ); ?>11c (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
												<option value="purple11d_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='purple11d_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Purple', GW_GO_TEXTDOMAN ); ?>11d (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>                                                
                                                <option value="purple12_team"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='purple12_team' ? ' selected="selected"' : ''; ?>><?php _e( 'Purple', GW_GO_TEXTDOMAN ); ?>12 (<?php _e( 'team header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="purple13_product"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='purple13_product' ? ' selected="selected"' : ''; ?>><?php _e( 'Purple', GW_GO_TEXTDOMAN ); ?>13 (<?php _e( 'product pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="purple14_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='purple14_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Purple', GW_GO_TEXTDOMAN ); ?>14 (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="purple15_html"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='purple15_html' ? ' selected="selected"' : ''; ?>><?php _e( 'Purple', GW_GO_TEXTDOMAN ); ?>15 (<?php _e( 'html header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <optgroup label="<?php _e( 'Yellow', GW_GO_TEXTDOMAN ); ?> <?php _e( 'styles', GW_GO_TEXTDOMAN ); ?>"></optgroup>
                                                <option value="yellow1_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='yellow1_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Yellow', GW_GO_TEXTDOMAN ); ?>1 (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="yellow2_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='yellow2_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Yellow', GW_GO_TEXTDOMAN ); ?>2 (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="yellow3a_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='yellow3a_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Yellow', GW_GO_TEXTDOMAN ); ?>3a (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="yellow3b_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='yellow3b_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Yellow', GW_GO_TEXTDOMAN ); ?>3b (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="yellow3c_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='yellow3c_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Yellow', GW_GO_TEXTDOMAN ); ?>3c (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="yellow3d_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='yellow3d_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Yellow', GW_GO_TEXTDOMAN ); ?>3d (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="yellow4a_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='yellow4a_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Yellow', GW_GO_TEXTDOMAN ); ?>4a (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="yellow4b_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='yellow4b_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Yellow', GW_GO_TEXTDOMAN ); ?>4b (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="yellow4c_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='yellow4c_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Yellow', GW_GO_TEXTDOMAN ); ?>4c (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="yellow4d_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='yellow4d_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Yellow', GW_GO_TEXTDOMAN ); ?>4d (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="yellow5_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='yellow5_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Yellow', GW_GO_TEXTDOMAN ); ?>5 (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="yellow6_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='yellow6_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Yellow', GW_GO_TEXTDOMAN ); ?>6 (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="yellow7_html"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='yellow7_html' ? ' selected="selected"' : ''; ?>><?php _e( 'Yellow', GW_GO_TEXTDOMAN ); ?>7 (<?php _e( 'html header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="yellow8_html"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='yellow8_html' ? ' selected="selected"' : ''; ?>><?php _e( 'Yellow', GW_GO_TEXTDOMAN ); ?>8 (<?php _e( 'html header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="yellow9_pricing2"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='yellow9_pricing2' ? ' selected="selected"' : ''; ?>><?php _e( 'Yellow', GW_GO_TEXTDOMAN ); ?>9 (<?php _e( 'pricing with html header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="yellow10_pricing3"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='yellow10_pricing3' ? ' selected="selected"' : ''; ?>><?php _e( 'Yellow', GW_GO_TEXTDOMAN ); ?>10 (<?php _e( 'pricing with full image header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="yellow11a_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='yellow11a_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Yellow', GW_GO_TEXTDOMAN ); ?>11a (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="yellow11b_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='yellow11b_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Yellow', GW_GO_TEXTDOMAN ); ?>11b (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="yellow11c_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='yellow11c_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Yellow', GW_GO_TEXTDOMAN ); ?>11c (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
												<option value="yellow11d_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='yellow11d_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Yellow', GW_GO_TEXTDOMAN ); ?>11d (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>                                                
                                                <option value="yellow12_team"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='yellow12_team' ? ' selected="selected"' : ''; ?>><?php _e( 'Yellow', GW_GO_TEXTDOMAN ); ?>12 (<?php _e( 'team header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="yellow13_product"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='yellow13_product' ? ' selected="selected"' : ''; ?>><?php _e( 'Yellow', GW_GO_TEXTDOMAN ); ?>13 (<?php _e( 'product pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="yellow14_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='yellow14_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Yellow', GW_GO_TEXTDOMAN ); ?>14 (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="yellow15_html"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='yellow15_html' ? ' selected="selected"' : ''; ?>><?php _e( 'Yellow', GW_GO_TEXTDOMAN ); ?>15 (<?php _e( 'html header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <optgroup label="<?php _e( 'Earth', GW_GO_TEXTDOMAN ); ?> <?php _e( 'styles', GW_GO_TEXTDOMAN ); ?>"></optgroup>
                                                <option value="earth1_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='earth1_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Earth', GW_GO_TEXTDOMAN ); ?>1 (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="earth2_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='earth2_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Earth', GW_GO_TEXTDOMAN ); ?>2 (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="earth3a_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='earth3a_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Earth', GW_GO_TEXTDOMAN ); ?>3a (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="earth3b_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='earth3b_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Earth', GW_GO_TEXTDOMAN ); ?>3b (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="earth3c_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='earth3c_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Earth', GW_GO_TEXTDOMAN ); ?>3c (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="earth3d_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='earth3d_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Earth', GW_GO_TEXTDOMAN ); ?>3d (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="earth4a_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='earth4a_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Earth', GW_GO_TEXTDOMAN ); ?>4a (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="earth4b_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='earth4b_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Earth', GW_GO_TEXTDOMAN ); ?>4b (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="earth4c_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='earth4c_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Earth', GW_GO_TEXTDOMAN ); ?>4c (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="earth4d_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='earth4d_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Earth', GW_GO_TEXTDOMAN ); ?>4d (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="earth5_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='earth5_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Earth', GW_GO_TEXTDOMAN ); ?>5 (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="earth6_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='earth6_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Earth', GW_GO_TEXTDOMAN ); ?>6 (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="earth7_html"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='earth7_html' ? ' selected="selected"' : ''; ?>><?php _e( 'Earth', GW_GO_TEXTDOMAN ); ?>7 (<?php _e( 'html header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="earth8_html"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='earth8_html' ? ' selected="selected"' : ''; ?>><?php _e( 'Earth', GW_GO_TEXTDOMAN ); ?>8 (<?php _e( 'html header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="earth9_pricing2"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='earth9_pricing2' ? ' selected="selected"' : ''; ?>><?php _e( 'Earth', GW_GO_TEXTDOMAN ); ?>9 (<?php _e( 'pricing with html header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="earth10_pricing3"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='earth10_pricing3' ? ' selected="selected"' : ''; ?>><?php _e( 'Earth', GW_GO_TEXTDOMAN ); ?>10 (<?php _e( 'pricing with full image header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="earth11a_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='earth11a_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Earth', GW_GO_TEXTDOMAN ); ?>11a (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="earth11b_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='earth11b_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Earth', GW_GO_TEXTDOMAN ); ?>11b (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="earth11c_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='earth11c_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Earth', GW_GO_TEXTDOMAN ); ?>11c (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
												<option value="earth11d_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='earth11d_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Earth', GW_GO_TEXTDOMAN ); ?>11d (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>                                                
                                                <option value="earth12_team"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='earth12_team' ? ' selected="selected"' : ''; ?>><?php _e( 'Earth', GW_GO_TEXTDOMAN ); ?>12 (<?php _e( 'team header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="earth13_product"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='earth13_product' ? ' selected="selected"' : ''; ?>><?php _e( 'Earth', GW_GO_TEXTDOMAN ); ?>13 (<?php _e( 'product pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="earth14_pricing"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='earth14_pricing' ? ' selected="selected"' : ''; ?>><?php _e( 'Earth', GW_GO_TEXTDOMAN ); ?>14 (<?php _e( 'pricing header', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="earth15_html"<?php echo isset( $pricing_tables[$uniqid]['col-style'][$x] ) && $pricing_tables[$uniqid]['col-style'][$x]=='earth15_html' ? ' selected="selected"' : ''; ?>><?php _e( 'Earth', GW_GO_TEXTDOMAN ); ?>15 (<?php _e( 'html header', GW_GO_TEXTDOMAN ); ?>)</option>                                                                                                                                                
                                            </select>
                                        </td>
                                        </tr>
									<tr>
                                        <th class="w100"><label><?php _e( 'Shadow style', GW_GO_TEXTDOMAN ); ?></label></th>
                                        <td>
                                            <select name="col-shadow[]" class="w255 widefat go-pricing-shadow-selector">
                                                <option value=""<?php echo $pricing_tables[$uniqid]['col-shadow'][$x]=='' ? ' selected="selected"' : ''; ?>><?php _e( 'No shadow', GW_GO_TEXTDOMAN ); ?></option>
                                                <option value="shadow1"<?php echo isset( $pricing_tables[$uniqid]['col-shadow'][$x] ) && $pricing_tables[$uniqid]['col-shadow'][$x]=='shadow1' ? ' selected="selected"' : ''; ?>><?php _e( 'shadow', GW_GO_TEXTDOMAN ); ?> <?php _e( 'style', GW_GO_TEXTDOMAN ); ?>1</option>
                                                <option value="shadow2"<?php echo isset( $pricing_tables[$uniqid]['col-shadow'][$x] ) && $pricing_tables[$uniqid]['col-shadow'][$x]=='shadow2' ? ' selected="selected"' : ''; ?>><?php _e( 'shadow', GW_GO_TEXTDOMAN ); ?> <?php _e( 'style', GW_GO_TEXTDOMAN ); ?>2</option>
                                                <option value="shadow3"<?php echo isset( $pricing_tables[$uniqid]['col-shadow'][$x] ) && $pricing_tables[$uniqid]['col-shadow'][$x]=='shadow3' ? ' selected="selected"' : ''; ?>><?php _e( 'shadow', GW_GO_TEXTDOMAN ); ?> <?php _e( 'style', GW_GO_TEXTDOMAN ); ?>3</option>
                                                <option value="shadow4"<?php echo isset( $pricing_tables[$uniqid]['col-shadow'][$x] ) && $pricing_tables[$uniqid]['col-shadow'][$x]=='shadow4' ? ' selected="selected"' : ''; ?>><?php _e( 'shadow', GW_GO_TEXTDOMAN ); ?> <?php _e( 'style', GW_GO_TEXTDOMAN ); ?>4</option>
                                                <option value="shadow5"<?php echo isset( $pricing_tables[$uniqid]['col-shadow'][$x] ) && $pricing_tables[$uniqid]['col-shadow'][$x]=='shadow5' ? ' selected="selected"' : ''; ?>><?php _e( 'shadow', GW_GO_TEXTDOMAN ); ?> <?php _e( 'style', GW_GO_TEXTDOMAN ); ?>5</option>                                                                                                                                                                                                                                                                    
                                            </select>
                                        </td>                                           
                                    </tr>
									<tr class="go-pricing-img-shadow-wrapper">
                                        <th class="w100"></th>
                                        <td><img src="<?php echo esc_attr( $plugin_dir_url ); ?>admin/images/shadow_6.png" class="go-pricing-img-shadow" /></td>                                           
                                    </tr>                                                                                                                                                                    
									<tr>
                                        <th class="w100"><label><?php _e( 'Ribbon', GW_GO_TEXTDOMAN ); ?></label></th>
                                        <td>
                                            <select name="col-ribbon[]" class="w255 widefat go-pricing-ribbon-selector">
                                                <option value=""><?php _e( 'No ribbon', GW_GO_TEXTDOMAN ); ?></option>
                                                <option value="custom"<?php echo $pricing_tables[$uniqid]['col-ribbon'][$x]=='custom' ? ' selected="selected"' : ''; ?>><?php _e( 'Custom ribbon (+add)', GW_GO_TEXTDOMAN ); ?></option>                                                
                                                <optgroup label="<?php _e( 'Blue', GW_GO_TEXTDOMAN ); ?> <?php _e( 'ribbons', GW_GO_TEXTDOMAN ); ?>"></optgroup>
                                                <option value="left-blue-50percent"<?php echo $pricing_tables[$uniqid]['col-ribbon'][$x]=='left-blue-50percent' ? ' selected="selected"' : ''; ?>>50% (<?php _e( 'left side', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="right-blue-50percent"<?php echo $pricing_tables[$uniqid]['col-ribbon'][$x]=='right-blue-50percent' ? ' selected="selected"' : ''; ?>>50% (<?php _e( 'right side', GW_GO_TEXTDOMAN ); ?>)</option>                                                
                                                <option value="left-blue-new"<?php echo $pricing_tables[$uniqid]['col-ribbon'][$x]=='left-blue-new' ? ' selected="selected"' : ''; ?>>New (<?php _e( 'left side', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="right-blue-new"<?php echo $pricing_tables[$uniqid]['col-ribbon'][$x]=='right-blue-new' ? ' selected="selected"' : ''; ?>>New (<?php _e( 'right side', GW_GO_TEXTDOMAN ); ?>)</option>                                                
                                                <option value="left-blue-top"<?php echo $pricing_tables[$uniqid]['col-ribbon'][$x]=='left-blue-top' ? ' selected="selected"' : ''; ?>>Top (<?php _e( 'left side', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="right-blue-top"<?php echo $pricing_tables[$uniqid]['col-ribbon'][$x]=='right-blue-top' ? ' selected="selected"' : ''; ?>>Top (<?php _e( 'right side', GW_GO_TEXTDOMAN ); ?>)</option>                                                
                                                <option value="left-blue-save"<?php echo $pricing_tables[$uniqid]['col-ribbon'][$x]=='left-blue-save' ? ' selected="selected"' : ''; ?>>Save (<?php _e( 'left side', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="right-blue-save"<?php echo $pricing_tables[$uniqid]['col-ribbon'][$x]=='right-blue-save' ? ' selected="selected"' : ''; ?>>Save (<?php _e( 'right side', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <optgroup label="<?php _e( 'Green', GW_GO_TEXTDOMAN ); ?> <?php _e( 'ribbons', GW_GO_TEXTDOMAN ); ?>"></optgroup>
                                                <option value="left-green-50percent"<?php echo $pricing_tables[$uniqid]['col-ribbon'][$x]=='left-green-50percent' ? ' selected="selected"' : ''; ?>>50% (<?php _e( 'left side', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="right-green-50percent"<?php echo $pricing_tables[$uniqid]['col-ribbon'][$x]=='right-green-50percent' ? ' selected="selected"' : ''; ?>>50% (<?php _e( 'right side', GW_GO_TEXTDOMAN ); ?>)</option>                                                
                                                <option value="left-green-new"<?php echo $pricing_tables[$uniqid]['col-ribbon'][$x]=='left-green-new' ? ' selected="selected"' : ''; ?>>New (<?php _e( 'left side', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="right-green-new"<?php echo $pricing_tables[$uniqid]['col-ribbon'][$x]=='right-green-new' ? ' selected="selected"' : ''; ?>>New (<?php _e( 'right side', GW_GO_TEXTDOMAN ); ?>)</option>                                                
                                                <option value="left-green-top"<?php echo $pricing_tables[$uniqid]['col-ribbon'][$x]=='left-green-top' ? ' selected="selected"' : ''; ?>>Top (<?php _e( 'left side', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="right-green-top"<?php echo $pricing_tables[$uniqid]['col-ribbon'][$x]=='right-green-top' ? ' selected="selected"' : ''; ?>>Top (<?php _e( 'right side', GW_GO_TEXTDOMAN ); ?>)</option>                                                
                                                <option value="left-green-save"<?php echo $pricing_tables[$uniqid]['col-ribbon'][$x]=='left-green-save' ? ' selected="selected"' : ''; ?>>Save (<?php _e( 'left side', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="right-green-save"<?php echo $pricing_tables[$uniqid]['col-ribbon'][$x]=='right-green-save' ? ' selected="selected"' : ''; ?>>Save (<?php _e( 'right side', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <optgroup label="<?php _e( 'Red', GW_GO_TEXTDOMAN ); ?> <?php _e( 'ribbons', GW_GO_TEXTDOMAN ); ?>"></optgroup>
                                                <option value="left-red-50percent"<?php echo $pricing_tables[$uniqid]['col-ribbon'][$x]=='left-red-50percent' ? ' selected="selected"' : ''; ?>>50% (<?php _e( 'left side', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="right-red-50percent"<?php echo $pricing_tables[$uniqid]['col-ribbon'][$x]=='right-red-50percent' ? ' selected="selected"' : ''; ?>>50% (<?php _e( 'right side', GW_GO_TEXTDOMAN ); ?>)</option>                                                
                                                <option value="left-red-new"<?php echo $pricing_tables[$uniqid]['col-ribbon'][$x]=='left-red-new' ? ' selected="selected"' : ''; ?>>New (<?php _e( 'left side', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="right-red-new"<?php echo $pricing_tables[$uniqid]['col-ribbon'][$x]=='right-red-new' ? ' selected="selected"' : ''; ?>>New (<?php _e( 'right side', GW_GO_TEXTDOMAN ); ?>)</option>                                                
                                                <option value="left-red-top"<?php echo $pricing_tables[$uniqid]['col-ribbon'][$x]=='left-red-top' ? ' selected="selected"' : ''; ?>>Top (<?php _e( 'left side', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="right-red-top"<?php echo $pricing_tables[$uniqid]['col-ribbon'][$x]=='right-red-top' ? ' selected="selected"' : ''; ?>>Top (<?php _e( 'right side', GW_GO_TEXTDOMAN ); ?>)</option>                                                
                                                <option value="left-red-save"<?php echo $pricing_tables[$uniqid]['col-ribbon'][$x]=='left-red-save' ? ' selected="selected"' : ''; ?>>Save (<?php _e( 'left side', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="right-red-save"<?php echo $pricing_tables[$uniqid]['col-ribbon'][$x]=='right-red-save' ? ' selected="selected"' : ''; ?>>Save (<?php _e( 'right side', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <optgroup label="<?php _e( 'Yellow', GW_GO_TEXTDOMAN ); ?> <?php _e( 'ribbons', GW_GO_TEXTDOMAN ); ?>"></optgroup>
                                                <option value="left-yellow-50percent"<?php echo $pricing_tables[$uniqid]['col-ribbon'][$x]=='left-yellow-50percent' ? ' selected="selected"' : ''; ?>>50% (<?php _e( 'left side', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="right-yellow-50percent"<?php echo $pricing_tables[$uniqid]['col-ribbon'][$x]=='right-yellow-50percent' ? ' selected="selected"' : ''; ?>>50% (<?php _e( 'right side', GW_GO_TEXTDOMAN ); ?>)</option>                                                
                                                <option value="left-yellow-new"<?php echo $pricing_tables[$uniqid]['col-ribbon'][$x]=='left-yellow-new' ? ' selected="selected"' : ''; ?>>New (<?php _e( 'left side', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="right-yellow-new"<?php echo $pricing_tables[$uniqid]['col-ribbon'][$x]=='right-yellow-new' ? ' selected="selected"' : ''; ?>>New (<?php _e( 'right side', GW_GO_TEXTDOMAN ); ?>)</option>                                                
                                                <option value="left-yellow-top"<?php echo $pricing_tables[$uniqid]['col-ribbon'][$x]=='left-yellow-top' ? ' selected="selected"' : ''; ?>>Top (<?php _e( 'left side', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="right-yellow-top"<?php echo $pricing_tables[$uniqid]['col-ribbon'][$x]=='right-yellow-top' ? ' selected="selected"' : ''; ?>>Top (<?php _e( 'right side', GW_GO_TEXTDOMAN ); ?>)</option>                                                
                                                <option value="left-yellow-save"<?php echo $pricing_tables[$uniqid]['col-ribbon'][$x]=='left-yellow-save' ? ' selected="selected"' : ''; ?>>Save (<?php _e( 'left side', GW_GO_TEXTDOMAN ); ?>)</option>
                                                <option value="right-yellow-save"<?php echo $pricing_tables[$uniqid]['col-ribbon'][$x]=='right-yellow-save' ? ' selected="selected"' : ''; ?>>Save (<?php _e( 'right side', GW_GO_TEXTDOMAN ); ?>)</option>
                                            </select>
                                        </td>                                            
                                    </tr>
                                    <tr class="go-pricing-img-custom-ribbon-wrapper">
                                    	<th class="w100"><label><?php _e( 'Ribbon align', GW_GO_TEXTDOMAN ); ?></label></th>
										<td>
                                        	<select name="col-custom-ribbon-align[]" class="w255 widefat">
                                            	<option value="left"<?php echo isset( $pricing_tables[$uniqid]['col-custom-ribbon-align'][$x] ) && $pricing_tables[$uniqid]['col-custom-ribbon-align'][$x]=='left' ? ' selected="selected"' : ''; ?>><?php _e( 'Align left', GW_GO_TEXTDOMAN ); ?></option>
												<option value="right"<?php echo isset ( $pricing_tables[$uniqid]['col-custom-ribbon-align'][$x] ) && $pricing_tables[$uniqid]['col-custom-ribbon-align'][$x]=='right' ? ' selected="selected"' : ''; ?>><?php _e( 'Align right', GW_GO_TEXTDOMAN ); ?></option>                                            
                                            </select>
                                        </td>
                                    </tr>                                    
									<tr class="go-pricing-img-ribbon-wrapper">
                                        <th class="w100"></th>
                                        <td><img src="<?php echo esc_attr( $plugin_dir_url ); ?>admin/images/blank.png" class="go-pricing-img-ribbon" /><input type="hidden" name="col-custom-ribbon[]" value="<?php echo isset( $pricing_tables[$uniqid]['col-custom-ribbon'][$x] ) ? esc_attr( $pricing_tables[$uniqid]['col-custom-ribbon'][$x] ) : ''; ?>" /></td>                                           
                                    </tr>
                                    <tr class="go-pricing-img-custom-ribbon-wrapper">
                                        <th class="w100"></th>
										<td><a href="#" data-img-class="go-pricing-img-ribbon" class="go-pricing-add-image button-secondary" style="margin:0 3px;"><span class="go-pricing-button-icon-add"></span><?php _e( 'Add image', GW_GO_TEXTDOMAN ); ?></a></td>                                       
                                    </tr>                   
                                </table>                        
                            </div>
                            </div>
                            <div class="postbox">
                            <h3 class="hndle"><div class="go-pricing-handle-icon-header-options"><?php _e( 'Header options', GW_GO_TEXTDOMAN ); ?><small><?php _e( 'Set column header content',GW_GO_TEXTDOMAN ); ?></small></div><span class="go-pricing-closed"></span></h3>
                            <div class="inside">
                                
                                <table class="form-table go-pricing-header-types go-pricing-header-type-pricing go-pricing-header-type-pricing2 go-pricing-header-type-pricing3 go-pricing-header-type-team go-pricing-header-type-product">
                                    <tr>
                                        <th class="w100"><label><?php _e( 'Column title', GW_GO_TEXTDOMAN ); ?></label></th>
                                        <td><input type="text" name="col-title[]" value="<?php echo isset( $pricing_tables[$uniqid]['col-title'][$x] ) ? esc_attr( $pricing_tables[$uniqid]['col-title'][$x] ): ''; ?>" class="w255" /></td>
                                    </tr>
                                </table>
                                <table class="form-table go-pricing-header-types go-pricing-header-type-pricing  go-pricing-header-type-pricing2 go-pricing-header-type-pricing3 go-pricing-header-type-product">
                                    <tr>
                                        <th class="w100"><label><?php _e( 'Price', GW_GO_TEXTDOMAN ); ?></label></th>
                                        <td><input type="text" name="col-price[]" value="<?php echo isset( $pricing_tables[$uniqid]['col-price'][$x] ) ? esc_attr( $pricing_tables[$uniqid]['col-price'][$x] ) : ''; ?>" class="w255" /></td>
									</tr>
                                </table>
                                <table class="form-table go-pricing-header-types go-pricing-header-type-pricing2">
                                    <tr>
                                        <th class="w100"></th>
										<td><a href="#" class="go-pricing-add-sc" data-popup-title="<?php esc_attr_e( 'Shortcode editor', GW_GO_TEXTDOMAN ); ?>" data-popup-width="670" data-popup-height="500" data-popup-action="go_pricing_sc_popup_header" data-target-class="go-pricing-popup-target"><?php esc_attr_e( 'Add shortcode', GW_GO_TEXTDOMAN ); ?></a></td>
                                    </tr>                                
                                    <tr>
                                        <th class="w100"><label><?php _e( 'HTML content', GW_GO_TEXTDOMAN ); ?></label></th>
                                        <td><textarea name="col-pricing-html[]" class="go-pricing-popup-target w255"><?php echo isset( $pricing_tables[$uniqid]['col-pricing-html'][$x] ) ? esc_textarea( $pricing_tables[$uniqid]['col-pricing-html'][$x] ) : ''; ?></textarea></td>
                                    </tr>
                              	</table>
                                <table class="form-table go-pricing-header-types go-pricing-header-type-pricing3 go-pricing-header-type-product go-pricing-header-type-team">
                                	<tr class="go-pricing-img-wrapper">
                                        <th class="w100"><label><?php _e( 'Select an image', GW_GO_TEXTDOMAN ); ?></label></th>
										<td><img src="<?php echo isset( $pricing_tables[$uniqid]['col-pricing-img'][$x] ) ? esc_attr( $pricing_tables[$uniqid]['col-pricing-img'][$x] ) : esc_attr( $plugin_dir_url ) . 'admin/images/blank.png'; ?>" class="go-pricing-img" /><input type="hidden" name="col-pricing-img[]" value="<?php echo isset( $pricing_tables[$uniqid]['col-pricing-img'][$x] ) ? esc_attr( $pricing_tables[$uniqid]['col-pricing-img'][$x] ) : ''; ?>" /></td>                                        
                                    </tr>
                                	<tr>
                                        <th class="w100"></th>
										<td><a href="#" data-img-class="go-pricing-img" class="go-pricing-add-image button-secondary" style="margin:0 3px;"><span class="go-pricing-button-icon-add"></span><?php _e( 'Add image', GW_GO_TEXTDOMAN ); ?></a></td>                                       
                                    </tr>                                    
                                </table>
                                <table class="form-table go-pricing-header-types go-pricing-header-type-pricing2 go-pricing-header-type-pricing3 go-pricing-header-type-team">
                                    <tr>
                                        <th class="w100"><label><?php _e( 'CSS extension', GW_GO_TEXTDOMAN ); ?></label></th>
                                        <td><textarea name="col-pricing-css[]" class="w255"><?php echo isset( $pricing_tables[$uniqid]['col-pricing-css'][$x] ) ? esc_textarea( $pricing_tables[$uniqid]['col-pricing-css'][$x] ) : ''; ?></textarea></td>
                                    </tr>                                    
                                </table>                                
                                <table class="form-table go-pricing-header-types go-pricing-header-type-pricing go-pricing-header-type-pricing2 go-pricing-header-type-pricing3 go-pricing-header-type-team go-pricing-header-type-product">
                                	<tr>
                                        <th class="w100"><label><?php _e( 'Replace default header type?', GW_GO_TEXTDOMAN ); ?></label></th>
										<td><input type="checkbox" name="col-replace-chk[]"<?php echo isset( $pricing_tables[$uniqid]['col-replace'][$x] ) && $pricing_tables[$uniqid]['col-replace'][$x]=='1' ? ' checked="checked"' : ''; ?> /><input type="hidden" name="col-replace[]" value="<?php echo isset( $pricing_tables[$uniqid]['col-replace'][$x] ) && $pricing_tables[$uniqid]['col-replace'][$x]=='1' ? '1' : '0'; ?>" />&nbsp;<?php _e( 'Yes', GW_GO_TEXTDOMAN ); ?></td>                                        
                                    </tr>
                                </table>
								<table class="form-table">
                                    <tr>
                                        <th class="w100"></th>
                                        <td><a href="#" class="go-pricing-add-sc" data-popup-title="<?php esc_attr_e( 'Shortcode editor', GW_GO_TEXTDOMAN ); ?>" data-popup-width="670" data-popup-height="500" data-popup-action="go_pricing_sc_popup_header" data-target-class="go-pricing-popup-target"><?php esc_attr_e( 'Add shortcode', GW_GO_TEXTDOMAN ); ?></a></td>
                                    </tr>
                                    <tr>
                                        <th class="w100"><label><?php _e( 'HTML content', GW_GO_TEXTDOMAN ); ?></label></th>
                                        <td><textarea name="col-html[]" class="go-pricing-popup-target w255"><?php echo isset ( $pricing_tables[$uniqid]['col-html'][$x] ) ? esc_textarea( $pricing_tables[$uniqid]['col-html'][$x] ) : ''; ?></textarea></td>
                                    </tr>
                                    <tr>
                                        <th class="w100"><label><?php _e( 'CSS extension', GW_GO_TEXTDOMAN ); ?></label></th>
                                        <td><textarea name="col-css[]" class="w255"><?php echo isset ( $pricing_tables[$uniqid]['col-css'][$x] ) ? esc_textarea( $pricing_tables[$uniqid]['col-css'][$x] ) : ''; ?></textarea></td>
                                    </tr>                                                                    
                                </table>                                
                            </div>
                            </div>
                            <div class="postbox">
                            <h3 class="hndle"><div class="go-pricing-handle-icon-body-options"><?php _e( 'Body options', GW_GO_TEXTDOMAN ); ?><small><?php _e( 'Set column main content',GW_GO_TEXTDOMAN ); ?></small></div><span class="go-pricing-closed"></span></h3>
                            <div class="inside">
                            	<div class="go-pricing-sortable-rows">
								<?php 
                                if ( isset( $pricing_tables[$uniqid]['col-detail'] ) ) :
                                for ( $y = $x*$rownum; $y < ($x+1)*$rownum; $y++ ) : 
                                ?>                                
                                <table class="form-table go-pricing-sortable-row">
                                    <tr>
	                                    <th class="w100">
                                        <input type="hidden" name="col-align[]" class="go-pricing-col-align" value="<?php echo isset( $pricing_tables[$uniqid]['col-align'][$y] ) ? $pricing_tables[$uniqid]['col-align'][$y] : ''; ?>" />
                                        	<a href="#" class="go-pricing-align-icon-left<?php echo isset( $pricing_tables[$uniqid]['col-align'][$y] ) && $pricing_tables[$uniqid]['col-align'][$y]=='left' ? ' go-pricing-current' : ''; ?>" data-id="left"></a>
                                        	<a href="#" class="go-pricing-align-icon-center<?php echo isset( $pricing_tables[$uniqid]['col-align'][$y] ) && $pricing_tables[$uniqid]['col-align'][$y]=='' ? ' go-pricing-current' : ''; ?>" data-id=""></a>
                                        	<a href="#" class="go-pricing-align-icon-right<?php echo isset( $pricing_tables[$uniqid]['col-align'][$y] ) && $pricing_tables[$uniqid]['col-align'][$y]=='right' ? ' go-pricing-current' : ''; ?>" data-id="right"></a>                                                                                       
                                        </th>
	                                    <td><a href="#" class="go-pricing-add-sc" data-popup-title="<?php esc_attr_e( 'Shortcode editor', GW_GO_TEXTDOMAN ); ?>" data-popup-width="670" data-popup-height="500" data-popup-action="go_pricing_sc_popup_body" data-target-class="go-pricing-popup-target"><?php esc_attr_e( 'Add shortcode', GW_GO_TEXTDOMAN ); ?></a></td>              
                                     <tr>
                                        <th class="w60"><label><?php _e( 'Description', GW_GO_TEXTDOMAN ); ?></label></th>
                                        <td><textarea name="col-detail[]" class="go-pricing-popup-target w255"><?php echo isset( $pricing_tables[$uniqid]['col-detail'][$y] ) ? esc_textarea( $pricing_tables[$uniqid]['col-detail'][$y] ) : ''; ?></textarea></td>
                                     <tr>
                                     </tr>
                                        <th class="w60"><label><?php _e( 'Tooltip', GW_GO_TEXTDOMAN ); ?></label></th>
                                        <td><textarea name="col-detail-tip[]" class="w255"><?php echo isset( $pricing_tables[$uniqid]['col-detail-tip'][$y] ) ? esc_textarea( $pricing_tables[$uniqid]['col-detail-tip'][$y] ) : ''; ?></textarea></td>
                                    </tr>
                                    <tr>
	                                    <th class="w60"></th>
                                        <td colspan="2" style="padding-bottom:22px !important;"><a href="#" class="go-pricing-remove-detail button-secondary" style="margin:0 3px;"><span class="go-pricing-button-icon-remove"></span><?php _e( 'Delete row', GW_GO_TEXTDOMAN ); ?></a><a href="#" class="go-pricing-clone-detail button-secondary" style="margin:0 3px;"><span class="go-pricing-button-icon-clone"></span><?php _e( 'Clone row', GW_GO_TEXTDOMAN ); ?></a></td>
                                    </tr>
								</table>
                                <?php endfor; endif; ?>

                                </div>
                                
                                <table class="form-table">
                                    <tr class="go-pricing-add-detail-row">
	                                    <th class="w100"></th>
                                        <td><a href="#" class="go-pricing-add-detail button-secondary" style="margin:0 3px;"><span class="go-pricing-button-icon-add"></span><?php _e( 'Add row', GW_GO_TEXTDOMAN ); ?></a></td>
                                    </tr>                                                                                                
                                </table>                                                                
                            </div>
                            </div>
                            <div class="postbox">
                            <h3 class="hndle"><div class="go-pricing-handle-icon-button-options"><?php _e( 'Button options', GW_GO_TEXTDOMAN ); ?><small><?php _e( 'Set column button function',GW_GO_TEXTDOMAN ); ?></small></div><span class="go-pricing-closed"></span></h3>
                            <div class="inside">
                                <table class="form-table">
                                    <tr>
                                        <th class="w100"><label><?php _e( 'Button size', GW_GO_TEXTDOMAN ); ?></label></th>
                                        <td>
                                            <select name="col-button-size[]" class="w255">
                                                <option value="small"<?php echo isset ( $pricing_tables[$uniqid]['col-button-size'][$x] ) && $pricing_tables[$uniqid]['col-button-size'][$x]=='small' ? ' selected="selected"' : ''; ?>><?php _e( 'Small', GW_GO_TEXTDOMAN ); ?></option>
                                                <option value="medium"<?php echo isset ( $pricing_tables[$uniqid]['col-button-size'][$x] ) && $pricing_tables[$uniqid]['col-button-size'][$x]=='medium' ? ' selected="selected"' : ''; ?>><?php _e( 'Medium', GW_GO_TEXTDOMAN ); ?></option>
                                                <option value="large"<?php echo isset ( $pricing_tables[$uniqid]['col-button-size'][$x] ) && $pricing_tables[$uniqid]['col-button-size'][$x]=='large' ? ' selected="selected"' : ''; ?>><?php _e( 'Large', GW_GO_TEXTDOMAN ); ?></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="w100"><label><?php _e( 'Button type', GW_GO_TEXTDOMAN ); ?></label></th>
                                        <td>
                                            <select name="col-button-type[]" class="w255">
                                            	<option value="button"<?php echo isset ( $pricing_tables[$uniqid]['col-button-type'][$x] ) && $pricing_tables[$uniqid]['col-button-type'][$x]=='button' ? ' selected="selected"' : ''; ?>><?php _e( 'Regular button', GW_GO_TEXTDOMAN ); ?></option>
                                            	<option value="submit"<?php echo isset ( $pricing_tables[$uniqid]['col-button-type'][$x] ) && $pricing_tables[$uniqid]['col-button-type'][$x]=='submit' ? ' selected="selected"' : ''; ?>><?php _e( 'Form submit button (e.g. Paypal)', GW_GO_TEXTDOMAN ); ?></option>                                                
												<option value="custom"<?php echo isset ( $pricing_tables[$uniqid]['col-button-type'][$x] ) && $pricing_tables[$uniqid]['col-button-type'][$x]=='custom' ? ' selected="selected"' : ''; ?>><?php _e( 'Custom button', GW_GO_TEXTDOMAN ); ?></option>                                                
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="w100"></th>
                                        <td><a href="#" class="go-pricing-add-sc" data-popup-title="<?php esc_attr_e( 'Shortcode editor', GW_GO_TEXTDOMAN ); ?>" data-popup-width="670" data-popup-height="500" data-popup-action="go_pricing_sc_popup_button" data-target-class="go-pricing-popup-target"><?php esc_attr_e( 'Add shortcode', GW_GO_TEXTDOMAN ); ?></a></td>
                                    </tr>                                                                       
                                    <tr>
                                        <th class="w100"><label><?php _e( 'Button content', GW_GO_TEXTDOMAN ); ?></label></th>
                                        <td><textarea name="col-button-text[]" class="go-pricing-popup-target w255"><?php echo isset( $pricing_tables[$uniqid]['col-button-text'][$x] ) ? esc_textarea( $pricing_tables[$uniqid]['col-button-text'][$x] ) : '' ; ?></textarea></td>
                                    </tr>
                                    <tr>
                                        <th class="w100"><label><?php _e( 'Button link / Paypal button code or shortcode', GW_GO_TEXTDOMAN ); ?></label></th>
                                        <td><textarea name="col-button-link[]" rows="5" class="w255"><?php echo isset( $pricing_tables[$uniqid]['col-button-link'][$x] ) ? esc_attr( $pricing_tables[$uniqid]['col-button-link'][$x] ) : '' ; ?></textarea></td>
                                    </tr>
                                    <tr>
                                        <th class="w100"><label><?php _e( 'Open in new window?', GW_GO_TEXTDOMAN ); ?></label></th>
										<td><input type="checkbox" name="col-button-target-chk[]"<?php echo isset( $pricing_tables[$uniqid]['col-button-target'][$x] ) && $pricing_tables[$uniqid]['col-button-target'][$x]=='1' ? ' checked="checked"' : ''; ?> /><input type="hidden" name="col-button-target[]" value="<?php echo isset( $pricing_tables[$uniqid]['col-button-target'][$x] ) && $pricing_tables[$uniqid]['col-button-target'][$x]=='1' ? '1' : '0'; ?>" />&nbsp;<?php _e( 'Yes', GW_GO_TEXTDOMAN ); ?></td>                                                                                
                                    </tr>
                                    <tr>
                                        <th class="w100"><label><?php _e( 'Nofollow link?', GW_GO_TEXTDOMAN ); ?></label></th>
										<td><input type="checkbox" name="col-button-nofollow-chk[]"<?php echo isset( $pricing_tables[$uniqid]['col-button-nofollow'][$x] ) && $pricing_tables[$uniqid]['col-button-nofollow'][$x]=='1' ? ' checked="checked"' : ''; ?> /><input type="hidden" name="col-button-nofollow[]" value="<?php echo isset( $pricing_tables[$uniqid]['col-button-nofollow'][$x] ) && $pricing_tables[$uniqid]['col-button-nofollow'][$x]=='1' ? '1' : '0'; ?>" />&nbsp;<?php _e( 'Yes', GW_GO_TEXTDOMAN ); ?></td>                                                                                
                                    </tr> 									                                                                                                                                      
                                </table>
                            </div>
                            </div>
                            <div class="inside postbox go-pricing-column-controls"><a href="#" class="go-pricing-remove-column button-secondary"><span class="go-pricing-button-icon-remove"></span><?php _e( 'Delete', GW_GO_TEXTDOMAN ); ?></a><a href="#" class="go-pricing-clone-column button-secondary" ><span class="go-pricing-button-icon-clone"></span><?php _e( 'Clone', GW_GO_TEXTDOMAN ); ?></a><a href="#" class="go-pricing-collapse-column button-secondary" data-closed="<?php _e( 'Expand all', GW_GO_TEXTDOMAN ); ?>" data-open="<?php _e( 'Collapse all', GW_GO_TEXTDOMAN ); ?>"><span class="go-pricing-button-icon-collapse"></span><?php _e( 'Expand all', GW_GO_TEXTDOMAN ); ?></a></div>                        
                        </li>
                            <?php
							endfor;
							endif;
							?>                                
                            <li class="go-pricing-add-column"><a href="#" title="<?php esc_attr_e( 'Add column', GW_GO_TEXTDOMAN ); ?>"><span></span></a></li>
                            <li class="clearfix"></li>
                        </ul>
                    </div>
                </div>
                <p class="submit">
                    <input name="cancel" type="button" class="button-secondary go-pricing-cancel" value="<?php esc_attr_e( 'Back', GW_GO_TEXTDOMAN ); ?>" />
                    <input name="save" type="submit" class="button-primary go-pricing-save" value="<?php esc_attr_e( 'Save', GW_GO_TEXTDOMAN ); ?>" />
                    <img src="<?php echo admin_url(); ?>/images/wpspin_light.gif" class="ajax-loading" alt="" />
                </p>               
            </form>
    		<?php endif; ?>
            </div>
            <?php
		}		

		/* handle submit */ 
		public function go_pricing_ajax_submit() {
			$response=array();
			
			/* verify nonce */
			if ( !wp_verify_nonce( $_POST['nonce'], GW_GO_PREFIX . basename( __FILE__ ) ) ) { die ( __( 'Oops, something went wrong!', GW_GO_TEXTDOMAN ) ); }

			/* action types select, delete & null ( back ) */
			/* action type: select -> select a pricing table to edit or create a new one */
			if ( isset( $_POST['action-type'] ) && $_POST['action-type']=='select' ) {
				 $this->go_pricing_menu_page_callback();
				 exit; 
			/* action type: copy -> copy selected pricing table */	 
			} elseif ( isset( $_POST['action-type'] ) && $_POST['action-type']=='copy' ) {
				$pricing_tables = $new_pricing_tables = get_option( GW_GO_PREFIX . 'tables' );
				$new_uniqid=uniqid();
				if ( $pricing_tables[$_POST['select']] ) { 
					$new_pricing_tables[$new_uniqid]=$pricing_tables[$_POST['select']];
					$new_pricing_tables[$new_uniqid]['uniqid']=$new_uniqid;
					$new_pricing_tables[$new_uniqid]['table-name'].=' copy_'.date("jnY");
					$new_pricing_tables[$new_uniqid]['table-id'].='_copy_'.date("jnY");
				}
				if ( $pricing_tables != $new_pricing_tables ) { update_option ( GW_GO_PREFIX . 'tables', $new_pricing_tables );  }
				$response['result'] = 'success';
				$response['message'][] = __( 'Pricing tables has been successfully updated.', GW_GO_TEXTDOMAN );
				$_POST = array();
				$this->go_pricing_menu_page_callback( $response );
				exit;				
			/* action type: delete -> delete selected pricing table */	 
			} elseif ( isset( $_POST['action-type'] ) && $_POST['action-type']=='delete' ) {
				$pricing_tables = $new_pricing_tables = get_option( GW_GO_PREFIX . 'tables' );
				if ( $new_pricing_tables[$_POST['select']] ) { unset( $new_pricing_tables[$_POST['select']] ); }
				if ( $pricing_tables != $new_pricing_tables ) { update_option ( GW_GO_PREFIX . 'tables', $new_pricing_tables ); 
				}
				$response['result'] = 'success';
				$response['message'][] = __( 'Pricing tables has been successfully updated.', GW_GO_TEXTDOMAN );
				$_POST = array();
				$this->go_pricing_menu_page_callback( $response );
				exit;
			/* action type: not set -> load selector page */	
			} elseif ( !isset( $_POST['action-type'] ) ) {
				$_POST = array();
				$this->go_pricing_menu_page_callback();
				exit; 
			}
			
			/* prepare & save data to db */

			/* clean post fields */
			foreach( $_POST as $key=>$value ) {
				if ( is_array( $_POST[$key] ) ) {
					foreach( $_POST[$key] as $skey=>$svalue ) {
						if ( strlen( $_POST[$key][$skey] ) ) { 
							$_POST[$key][$skey] = stripslashes( $_POST[$key][$skey] );
							}
						$_POST[$key][$skey] = trim( $_POST[$key][$skey] );
					}
				} else {
					if ( strlen( $_POST[$key] ) ) { $_POST[$key] = stripslashes( $_POST[$key] ); }
					$_POST[$key] = strip_tags( trim( $_POST[$key] ) );
				}
			}
			
			/* validate pricing table name & id & colum */
			if ( isset ( $_POST['table-name'] ) && !strlen( $_POST['table-name'] ) ) {
				$response['result'] = 'error';
				$response['message'][] = __( 'Pricing table name is empty.', GW_GO_TEXTDOMAN );				
			}
			if ( isset ( $_POST['table-id'] ) && !strlen( $_POST['table-id'] ) ) {
				$response['result'] = 'error';
				$response['message'][] = __( 'Pricing table id is empty.', GW_GO_TEXTDOMAN );
			}	
			
			/* sanitize pricing table id */
			if ( isset ( $_POST['table-id'] ) ) { $_POST['table-id'] = sanitize_key( $_POST['table-id'] ); }

			/* get pricing tables & check if id is unique */
			$pricing_tables = get_option( GW_GO_PREFIX . 'tables' );
			
			if ( !isset( $response['result'] ) || $response['result'] != 'error' ) {
				if ( !empty( $pricing_tables ) ) {
					foreach ( $pricing_tables as $key=>$value ) {
						if ( $value['table-id'] == $_POST['table-id'] && $_POST['uniqid'] != $key ) {
							$response['result'] = 'error';
							$response['message'][] = __( 'This table id is already used.', GW_GO_TEXTDOMAN );					
						}
					}	
				}
			}

			/* if no error found */		
			foreach( $_POST as $key=>$value ) { if ( $key != 'action' && $key != 'nonce' &&  $key != 'action-type' ) { $new_table[$_POST['uniqid']][$key]=$value; } }	
			$new_pricing_tables = is_array( $pricing_tables ) ? array_merge( $pricing_tables, $new_table ) : $new_table;
			/* save pricing table to db */
			if ( !isset( $response['result'] ) || $response['result'] != 'error' ) {
				if ( $pricing_tables != $new_pricing_tables  ) { update_option ( GW_GO_PREFIX . 'tables', $new_pricing_tables ); }
				$response['result'] = 'success';
				$response['message'][] = __( 'Pricing tables has been successfully updated.', GW_GO_TEXTDOMAN );
			}	
			$this->go_pricing_menu_page_callback( $response );
			exit;	
		}

		/* General settings page */
		public function go_pricing_submenu_page_settings_callback( $response=array() ) {
			$this->go_pricing_load_textdomain();
			?>
			<div id="go-pricing-admin-wrap" class="wrap" data-id="<?php echo $_SERVER['REQUEST_URI']; ?>">
			<div id="go-pricing-admin-icon" class="icon32"></div>
		    <h2><?php _e( 'Go - Responsive Pricing & Compare Tables', GW_GO_TEXTDOMAN ); ?></h2>
		    <p></p>
			<?php if ( !empty( $response ) ) : ?>
            <div id="result" class="<?php echo $response['result'] == 'error' ? 'error' : 'updated'; ?>">
            <?php foreach ( $response['message'] as $error_msg ) : ?>
                <p><strong><?php echo $error_msg; ?></strong></p>
            <?php endforeach;  $response = array(); ?>
            </div>
            <?php 
			exit;
            endif;
			
			$table_settings = get_option( GW_GO_PREFIX . 'table_settings' );
			if ( empty( $table_settings ) ) {
				/* setting default values */
				$table_settings['responsivity']=1;
				$table_settings['transitions']=1;
				$table_settings['colw-min']='130px';
				$table_settings['colw-max']='';
				$table_settings['size1-min']='768px';
				$table_settings['size1-max']='959px';
				$table_settings['size2-min']='480px';
				$table_settings['size2-max']='767px';
				$table_settings['size3-min']='';
				$table_settings['size3-max']='479px';
				$table_settings['primary-font']='Arial, Helvetica, sans-serif;';	
				$table_settings['secondary-font']='Verdana, Geneva, sans-serif;';
				$table_settings['capability']='';
				$table_settings['custom-css']='';														
			}
			?>
            <form id="go-pricing-settings-form" name="settings-form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" data-ajaxerrormsg="<?php _e( 'Oops, AJAX error!', GW_GO_TEXTDOMAN ); ?>">
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( GW_GO_PREFIX . basename( __FILE__ ) ); ?>" />
                <input type="hidden" id="action-type" name="action-type" value="edit" />
                <div id="go-pricing-admin-wrap-wrapper" class="postbox">
                    <h3 class="hndle"><?php _e( 'General Settings', GW_GO_TEXTDOMAN ); ?><span></span></h3>
                    <div class="inside">
                    	<table class="form-table">
                            <tr>
                                <th class="w150"><label for="go-pricing-primary-font"><strong><?php _e( 'Primary font', GW_GO_TEXTDOMAN ); ?></strong></label></th>
                                <td class="w200"><input type="text" name="primary-font" id="go-pricing-primary-font" value="<?php echo isset( $table_settings['primary-font'] ) ? esc_attr( $table_settings['primary-font'] ) : ''; ?>" class="w200" /></td>
                                <td colspan="3"><p class="description"><?php _e( 'Primary font is used in header (title, price and buttons)', GW_GO_TEXTDOMAN ); ?></p></td>
                            </tr>                        
                            <tr>
                                <th class="w150"><label for="go-pricing-primary-font-css"><strong><?php _e( 'Primary font css', GW_GO_TEXTDOMAN ); ?></strong></label></th>
                                <td class="w200"><input type="text" name="primary-font-css" id="go-pricing-primary-font-css" value="<?php echo isset( $table_settings['primary-font-css'] ) ? ( $table_settings['primary-font-css'] ) : ''; ?>" class="w200" /></td>
                                <td colspan="3"><p class="description"><?php _e( 'Primary font external css file for Google (or other) fonts', GW_GO_TEXTDOMAN ); ?></p></td>
                            </tr>                        
                            <tr>
                                <th class="w150"><label for="go-pricing-secondary-font"><strong><?php _e( 'Secondary font', GW_GO_TEXTDOMAN ); ?></strong></label></th>
                                <td class="w200"><input type="text" name="secondary-font" id="go-pricing-secondary-font" value="<?php echo isset( $table_settings['secondary-font'] ) ? esc_attr( $table_settings['secondary-font'] ) : ''; ?>" class="w200" /></td>
                                <td colspan="3"><p class="description"><?php _e( 'Secondary font is used in the body part of the table (descriptions, tooltips)', GW_GO_TEXTDOMAN ); ?></p></td>
                            </tr>                        
                            <tr>
                                <th class="w150"><label for="go-pricing-secondary-font-css"><strong><?php _e( 'Secondary font css', GW_GO_TEXTDOMAN ); ?></strong></label></th>
                                <td class="w200"><input type="text" name="secondary-font-css" id="go-pricing-secondary-font-css" value="<?php echo isset( $table_settings['secondary-font-css'] ) ? ( $table_settings['secondary-font-css'] ) : ''; ?>" class="w200" /></td>
                                <td colspan="3"><p class="description"><?php _e( 'Secondary font external css file for Google (or other) fonts', GW_GO_TEXTDOMAN ); ?></p></td>
                            </tr>                        
                        </table>
                        <div class="go-pricing-separator"></div>   
                        <table class="form-table">
                            <tr>
                                <th class="w150"><strong><?php _e( 'Column widths', GW_GO_TEXTDOMAN ); ?></strong></th>
                                <td class="w100"><label for="go-pricing-colw-min"><?php _e( 'Minimum width', GW_GO_TEXTDOMAN ); ?></label></th>                                <td class="w100"><input type="text" name="colw-min" id="go-pricing-colw-min" value="<?php echo isset( $table_settings['colw-min'] ) ? ( $table_settings['colw-min'] ) : ''; ?>" class="w80" /></td>
                                <td class="w100"><label for="go-pricing-colw-max"><?php _e( 'Maximum width', GW_GO_TEXTDOMAN ); ?></label></td>
                                <td colspan="2"><input type="text" name="colw-max" id="go-pricing-colw-max" value="<?php echo isset( $table_settings['colw-max'] ) ? ( $table_settings['colw-max'] ) : ''; ?>" class="w80" /></td>
                            </tr>
                            <tr>
                                <th class="w150"><strong><?php _e( 'Enable CSS transitions', GW_GO_TEXTDOMAN ); ?></strong></th>
                                <td colspan="4"><input type="checkbox" name="transitions-chk"<?php echo isset( $table_settings['transitions'] ) & $table_settings['transitions']=='1' ? ' checked="checked"' : ''; ?> /><input type="hidden" name="transitions" value="<?php echo isset( $table_settings['transitions'] ) & $table_settings['transitions']=='1' ? '1' : '0'; ?>" />&nbsp;<?php _e( 'Yes', GW_GO_TEXTDOMAN ); ?></td>
                            </tr>                             
                       </table>
                       <div class="go-pricing-separator"></div> 
                       <table class="form-table">     
                            <tr>
                                <th class="w150"><strong><?php _e( 'Enable responsivity', GW_GO_TEXTDOMAN ); ?></strong></th>
                                <td colspan="4"><input type="checkbox" name="responsivity-chk"<?php echo isset( $table_settings['responsivity'] ) & $table_settings['responsivity']=='1' ? ' checked="checked"' : ''; ?> /><input type="hidden" name="responsivity" value="<?php echo isset( $table_settings['responsivity'] ) & $table_settings['responsivity']=='1' ? '1' : '0'; ?>" />&nbsp;<?php _e( 'Yes', GW_GO_TEXTDOMAN ); ?></td>
                            </tr>                        
                            <tr>
                                <th class="w150"><strong><?php _e( 'Tablet (portrait)', GW_GO_TEXTDOMAN ); ?></strong></th>
                                <td class="w100"><label for="go-pricing-size1-min"><?php _e( 'Minimum width', GW_GO_TEXTDOMAN ); ?></label></th>
                                <td class="w100"><input type="text" name="size1-min" id="go-pricing-size1-min" value="<?php echo isset( $table_settings['size1-min'] ) ? ( $table_settings['size1-min'] ) : ''; ?>" class="w80" /></td>
                                <td class="w100"><label for="go-pricing-size1-max"><?php _e( 'Maximum width', GW_GO_TEXTDOMAN ); ?></label></td>
                                <td colspan="2"><input type="text" name="size1-max" id="go-pricing-size1-max" value="<?php echo isset( $table_settings['size1-max'] ) ? ( $table_settings['size1-max'] ) : ''; ?>" class="w80" /></td>
                            </tr>
                            <tr>
                                <th class="w100"><strong><?php _e( 'Mobile (portrait)', GW_GO_TEXTDOMAN ); ?></strong></th>
                                <td class="w100"><label for="go-pricing-size2-min"><?php _e( 'Minimum width', GW_GO_TEXTDOMAN ); ?></label></th>
                                <td class="w100"><input type="text" name="size2-min" id="go-pricing-size2-min" value="<?php echo isset( $table_settings['size2-min'] ) ? ( $table_settings['size2-min'] ) : ''; ?>" class="w80" /></td>
                                <td class="w100"><label for="go-pricing-size2-max"><?php _e( 'Maximum width', GW_GO_TEXTDOMAN ); ?></label></td>
                                <td colspan="2"><input type="text" name="size2-max" id="go-pricing-size2-max" value="<?php echo isset( $table_settings['size2-max'] ) ? ( $table_settings['size2-max'] ) : ''; ?>" class="w80" /></td>
                            </tr>
                            <tr>
                                <th class="w100"><strong><?php _e( 'Mobile (landscape)', GW_GO_TEXTDOMAN ); ?></strong></th>
                                <td class="w100"><label for="go-pricing-size3-min"><?php _e( 'Minimum width', GW_GO_TEXTDOMAN ); ?></label></th>
                                <td class="w100"><input type="text" name="size3-min" id="go-pricing-size3-min" value="<?php echo isset( $table_settings['size3-min'] ) ? ( $table_settings['size3-min'] ) : ''; ?>" class="w80" /></td>
                                <td class="w100"><label for="go-pricing-size3-max"><?php _e( 'Maximum width', GW_GO_TEXTDOMAN ); ?></label></td>
                                <td colspan="2"><input type="text" name="size3-max" id="go-pricing-size3-max" value="<?php echo isset( $table_settings['size3-max'] ) ? ( $table_settings['size3-max'] ) : ''; ?>" class="w80" /></td>
                            </tr>
                       </table>
                       <?php if ( current_user_can( 'manage_options' ) ) : ?>
					   <div class="go-pricing-separator"></div> 
                       <table class="form-table"> 
                            <tr>
                                <th class="w150"><label for="go-pricing-secondary-font"><strong><?php _e( 'Set role', GW_GO_TEXTDOMAN ); ?></strong></label></th>
                                <td class="w200">
									<select name="capability" class="go-pricing-icon-align wfull">
										<option value="manage_options" <?php echo isset( $table_settings['capability'] ) && $table_settings['capability'] == 'manage_options' ? 'selected="selected"' : ''; ?>><?php _e( 'Administrator', GW_GO_TEXTDOMAN ); ?></option>
										<option value="edit_private_posts" <?php echo isset( $table_settings['capability'] ) && $table_settings['capability'] == 'edit_private_posts' ? 'selected="selected"' : ''; ?>><?php _e( 'Editor', GW_GO_TEXTDOMAN ); ?></option>
										<option value="publish_posts" <?php echo isset( $table_settings['capability'] ) && $table_settings['capability'] == 'publish_posts' ? 'selected="selected"' : ''; ?>><?php _e( 'Author', GW_GO_TEXTDOMAN ); ?></option>
										<option value="edit_posts" <?php echo isset( $table_settings['capability'] ) && $table_settings['capability'] == 'edit_posts' ? 'selected="selected"' : ''; ?>><?php _e( 'Contributor', GW_GO_TEXTDOMAN ); ?></option>
									</select>								
								</td>
                                <td colspan="3"><p class="description"><?php _e( 'Set user access to the plugin', GW_GO_TEXTDOMAN ); ?></p></td>
                            </tr>				    							                                                       
                        </table>
						<?php endif ?>
                    </div>
                </div>            
                <p class="submit">
                    <input name="save" type="button" class="button-primary go-pricing-save" value="<?php esc_attr_e( 'Save', GW_GO_TEXTDOMAN ); ?>" />
                    <img src="<?php echo admin_url(); ?>/images/wpspin_light.gif" class="ajax-loading" alt="" />
                </p>
				<div class="go-pricing-space"></div>
                <div id="go-pricing-admin-wrap-wrapper" class="postbox">
                    <h3 class="hndle"><?php _e( 'Custom CSS code', GW_GO_TEXTDOMAN ); ?><span></span></h3>
                    <div class="inside">
                        <table class="form-table">
                            <tr>
                            	<th>
                            	 <strong><?php _e( 'You can add custom CSS code to plugin main frontend stylesheet file.', GW_GO_TEXTDOMAN ); ?></strong>
									<?php _e( 'After clicking to the "Save" button your changes will appear in css file.', GW_GO_TEXTDOMAN ); ?>
                                </th>
                            </tr>
                            <tr>
                            	<th><textarea id="go-pricing-custom-css" name="custom-css" style="width:100%" rows="10"><?php echo isset( $table_settings['custom-css'] ) ? ( $table_settings['custom-css'] ) : ''; ?></textarea></th>
                           </tr>
                        </table>
						</table>
					</div>
				</div>
                <p class="submit">
                    <input name="save" type="button" class="button-primary go-pricing-save" value="<?php esc_attr_e( 'Save', GW_GO_TEXTDOMAN ); ?>" />
                    <img src="<?php echo admin_url(); ?>/images/wpspin_light.gif" class="ajax-loading" alt="" />
                </p>								
            </form>
        	<?php 
        }

		/* General settings submit */
		public function go_pricing_settings_ajax_submit() {
			$response=array();
			
			/* verify nonce */
			if ( !wp_verify_nonce( $_POST['nonce'], GW_GO_PREFIX . basename( __FILE__ ) ) ) { die ( __( 'Oops, something went wrong!', GW_GO_TEXTDOMAN ) ); }
				
			/* clean post fields */
			foreach( $_POST as $key=>$value ) {
				if ( is_array( $_POST[$key] ) ) {
					foreach( $_POST[$key] as $skey=>$svalue ) {
						if ( strlen( $_POST[$key][$skey] ) ) { 
							$_POST[$key][$skey] = stripslashes( $_POST[$key][$skey] );
							}
						$_POST[$key][$skey] = trim( $_POST[$key][$skey] );
					}
				} else {
					if ( strlen( $_POST[$key] ) ) { $_POST[$key] = stripslashes( $_POST[$key] ); }
					$_POST[$key] = strip_tags( trim( $_POST[$key] ) );
				}
			}
		
			/* get pricing tables & check if id is unique */
			$table_settings = $new_table_settings = get_option( GW_GO_PREFIX . 'table_settings' );
			$new_table_settings = $_POST;
			
			/* save pricing table data to db */
			if ( !isset( $response['result'] ) || $response['result'] != 'error' ) {
				if ( $table_settings != $new_table_settings  ) { update_option ( GW_GO_PREFIX . 'table_settings', $new_table_settings ); }
				$response['result'] = 'success';
				$response['message'][] = __( 'General settings has been successfully updated.', GW_GO_TEXTDOMAN );
				self::generate_styles( $new_table_settings );
			}	
			$this->go_pricing_submenu_page_settings_callback( $response );
			exit;	
		}
		
		/**
		 * Submenu page for Import & Export
		 */
		
		public function plugin_submenu_page_import_export() {
			include_once(  plugin_dir_path( __FILE__ ) . trailingslashit( 'includes' ) . 'submenu_page_import_export.php' );
		}

		/* ajax popup for header */
		public function go_pricing_sc_popup_header() {
			$this->go_pricing_load_textdomain();
			?>
            <div class="postbox go-pricing-popup-sc-selector-wrapper">
                <table class="form-table">
                    <tr>
                        <th class="w150"><label><?php _e( 'Create shortcode', GW_GO_TEXTDOMAN ); ?></label></th>
                        <td class="wfull">
                            <select class="go-pricing-popup-sc-selector wfull">
                                <option value=""><?php _e( '-- Select shortcode --', GW_GO_TEXTDOMAN ); ?></option>                                 
                                <option value="go_pricing_image"><?php _e( 'Image', GW_GO_TEXTDOMAN ); ?></option>
                                <optgroup label="<?php esc_attr_e( 'Video', GW_GO_TEXTDOMAN ); ?>"></optgroup>
								<option value="go_pricing_youtube"><?php _e( 'Youtube video', GW_GO_TEXTDOMAN ); ?></option>
                                <option value="go_pricing_vimeo"><?php _e( 'Vimeo video', GW_GO_TEXTDOMAN ); ?></option>
                                <option value="go_pricing_screenr"><?php _e( 'Screenr video', GW_GO_TEXTDOMAN ); ?></option>
								<option value="go_pricing_dailymotion"><?php _e( 'Dailymotion video', GW_GO_TEXTDOMAN ); ?></option>
								<option value="go_pricing_metacafe"><?php _e( 'Metacafe video', GW_GO_TEXTDOMAN ); ?></option>
                                <option value="go_pricing_html5_video"><?php _e( 'HTML5 video', GW_GO_TEXTDOMAN ); ?></option>
                                <optgroup label="<?php esc_attr_e( 'Audio', GW_GO_TEXTDOMAN ); ?>"></optgroup>
								<option value="go_pricing_soundcloud"><?php _e( 'Soundcloud audio', GW_GO_TEXTDOMAN ); ?></option>
								<option value="go_pricing_mixcloud"><?php _e( 'Mixcloud audio', GW_GO_TEXTDOMAN ); ?></option>
								<option value="go_pricing_beatport"><?php _e( 'Beatport audio', GW_GO_TEXTDOMAN ); ?></option>
								<option value="go_pricing_audio"><?php _e( 'HTML5 audio', GW_GO_TEXTDOMAN ); ?></option>
								<optgroup label="<?php esc_attr_e( 'Other', GW_GO_TEXTDOMAN ); ?>"></optgroup>
                                <option value="go_pricing_map"><?php _e( 'Google map', GW_GO_TEXTDOMAN ); ?></option>
								<option value="go_pricing_custom_iframe"><?php _e( 'Custom iframe', GW_GO_TEXTDOMAN ); ?></option>
                            </select>
                        </td>
                        <td class="w100"></td>
                    </tr>
                </table>
            </div>
            <div class="inside">
                <table class="form-table">
                    <tr class="go-pricing-popup-sc">
                        <th class="w150"></th>
                        <td class="wfull" style="padding-top:0 !important;"><p class="description"><?php _e( 'You can select & insert shortcodes into the content using the selector.', GW_GO_TEXTDOMAN ); ?></p></td>
                        <td class="w100"></td>
                    </tr>                
                    <tr class="go-pricing-popup-sc go_pricing_image">
                        <th class="w150"><label><?php _e( 'Image source', GW_GO_TEXTDOMAN ); ?></label></th>
                        <td class="wfull"><input type="text" name="src" class="wfull"><p class="description"><?php _e( 'Image source file. (required)', GW_GO_TEXTDOMAN ); ?></p></td>
                        <td class="w100 vtop"><a href="#" class="go-pricing-add-img-input go-pricing-popup-tb button-secondary" style="margin:1px 3px; padding: 4px 8px !important;"><span class="go-pricing-button-icon-add"></span><?php _e( 'Add', GW_GO_TEXTDOMAN ); ?></a></td>
                    </tr>
                    <tr class="go-pricing-popup-sc go_pricing_image">
                        <th class="w150"><label><?php _e( 'Width', GW_GO_TEXTDOMAN ); ?></label></th>
                        <td class="wfull"><input type="text" name="width" class="wfull"><p class="description"><?php _e( 'Width of the image in pixels e.g. "200". (optional)', GW_GO_TEXTDOMAN ); ?></p></td>
                    </tr>
                    <tr class="go-pricing-popup-sc go_pricing_image">
                        <th class="w150"><label><?php _e( 'Height', GW_GO_TEXTDOMAN ); ?></label></th>
                        <td class="wfull"><input type="text" name="height" class="wfull"><p class="description"><?php _e( 'Height of the image in pixels e.g. "150". (optional)', GW_GO_TEXTDOMAN ); ?></p></td>
                    </tr>
                    <tr class="go-pricing-popup-sc go_pricing_image">
                        <th class="w150"><label><?php _e( 'Classes', GW_GO_TEXTDOMAN ); ?></label></th>
                        <td class="wfull"><input type="text" name="class" class="wfull"><p class="description"><?php _e( 'Image classes. (optional)', GW_GO_TEXTDOMAN ); ?></p></td>                    </tr>                           
                    <tr class="go-pricing-popup-sc go_pricing_image">
                        <th class="w150"><label><?php _e( 'Responsive?', GW_GO_TEXTDOMAN ); ?></label></th>
                        <td class="wfull"><input type="checkbox" name="class" value="gw-go-responsive-img"> <?php _e( 'Yes', GW_GO_TEXTDOMAN ); ?><p class="description"><?php _e( 'Whether to make the image fluid/responsive (optional)', GW_GO_TEXTDOMAN ); ?></p></td>
                        <td class="w100"></td>
                    </tr>                    
                    <tr class="go-pricing-popup-sc go_pricing_youtube go_pricing_vimeo go_pricing_screenr go_pricing_dailymotion go_pricing_metacafe">
                        <th class="w150"><label><?php _e( 'Video id', GW_GO_TEXTDOMAN ); ?></label></th>
                        <td class="wfull"><input type="text" name="video_id" class="wfull"><p class="description"><?php _e( 'Video id. (required)', GW_GO_TEXTDOMAN ); ?></p></td>
                        <td class="w100"></td>
                    </tr>					
                    <tr class="go-pricing-popup-sc go_pricing_youtube go_pricing_vimeo go_pricing_screenr go_pricing_dailymotion go_pricing_metacafe">
                        <th class="w150"><label><?php _e( 'Video height', GW_GO_TEXTDOMAN ); ?></label></th>
                        <td class="wfull"><input type="text" name="height" class="wfull"><p class="description"><?php _e( 'Video height in pixels e.g. "300". (optional)', GW_GO_TEXTDOMAN ); ?></p></td>
                        <td class="w100"></td>
                    </tr>
                    <tr class="go-pricing-popup-sc go_pricing_html5_video">
                        <th class="w150"><label>MP4 <?php _e( 'source', GW_GO_TEXTDOMAN ); ?></label></th>
                        <td class="wfull"><input type="text" name="mp4_src" class="wfull"><p class="description"><?php _e( 'MP4 file for Safari, IE9, iPhone, iPad, Android, and WP7. (required)', GW_GO_TEXTDOMAN ); ?></p></td>
                        <td class="w100 vtop"><a href="#" class="go-pricing-add-file-input go-pricing-popup-tb button-secondary" style="margin:1px 3px; padding: 4px 8px !important;"><span class="go-pricing-button-icon-add"></span><?php _e( 'Add', GW_GO_TEXTDOMAN ); ?></a></td>
                    </tr>
                    <tr class="go-pricing-popup-sc go_pricing_html5_video">
                        <th class="w150"><label>WebM <?php _e( 'source', GW_GO_TEXTDOMAN ); ?></label></th>
                        <td class="wfull"><input type="text" name="webm_src" class="wfull"><p class="description"><?php _e( 'WebM/VP8 file for Firefox4, Opera, and Chrome.(optional)', GW_GO_TEXTDOMAN ); ?></p></td>
                        <td class="w100 vtop"><a href="#" class="go-pricing-add-file-input go-pricing-popup-tb button-secondary" style="margin:1px 3px; padding: 4px 8px !important;"><span class="go-pricing-button-icon-add"></span><?php _e( 'Add', GW_GO_TEXTDOMAN ); ?></a></td>
                    </tr>
                    <tr class="go-pricing-popup-sc go_pricing_html5_video">
                        <th class="w150"><label>Ogg <?php _e( 'source', GW_GO_TEXTDOMAN ); ?></label></th>
                        <td class="wfull"><input type="text" name="ogg_src" class="wfull"><p class="description"><?php _e( 'Ogg/Vorbis for older Firefox and Opera versions.(optional)', GW_GO_TEXTDOMAN ); ?></p></td>
                        <td class="w100 vtop"><a href="#" class="go-pricing-add-file-input go-pricing-popup-tb button-secondary" style="margin:1px 3px; padding: 4px 8px !important;"><span class="go-pricing-button-icon-add"></span><?php _e( 'Add', GW_GO_TEXTDOMAN ); ?></a></td>
                    </tr>
                    <tr class="go-pricing-popup-sc go_pricing_html5_video">
                        <th class="w150"><label><?php _e( 'Poster image source', GW_GO_TEXTDOMAN ); ?></label></th>
                        <td class="wfull"><input type="text" name="poster_src" class="wfull"><p class="description"><?php _e( 'Image to be shown while the video is downloading. (required)', GW_GO_TEXTDOMAN ); ?></p></td>
                        <td class="w100 vtop"><a href="#" class="go-pricing-add-file-input go-pricing-popup-tb button-secondary" style="margin:1px 3px; padding: 4px 8px !important;"><span class="go-pricing-button-icon-add"></span><?php _e( 'Add', GW_GO_TEXTDOMAN ); ?></a></td>
                    </tr>
                    <tr class="go-pricing-popup-sc go_pricing_soundcloud">
                        <th class="w150"><label><?php _e( 'Track ID', GW_GO_TEXTDOMAN ); ?></label></th>
                        <td class="wfull"><input type="text" name="track_id" class="wfull"><p class="description"><?php _e( 'Soundcloud track ID. (required)', GW_GO_TEXTDOMAN ); ?></p></td>
                        <td class="w100"></td>
                    </tr>
                    <tr class="go-pricing-popup-sc go_pricing_mixcloud">
                        <th class="w150"><label><?php _e( 'Track URL', GW_GO_TEXTDOMAN ); ?></label></th>
                        <td class="wfull"><input type="text" name="track_url" class="wfull"><p class="description"><?php _e( 'Mixcloud track URL. (required)', GW_GO_TEXTDOMAN ); ?></p></td>
                        <td class="w100"></td>
                    </tr>
                    <tr class="go-pricing-popup-sc go_pricing_beatport">
                        <th class="w150"><label><?php _e( 'Track ID', GW_GO_TEXTDOMAN ); ?></label></th>
                        <td class="wfull"><input type="text" name="track_id" class="wfull"><p class="description"><?php _e( 'Beatport track ID. (required)', GW_GO_TEXTDOMAN ); ?></p></td>
                        <td class="w100"></td>
                    </tr>
                    <tr class="go-pricing-popup-sc go_pricing_custom_iframe">
                        <th class="w150"><label><?php _e( 'URL', GW_GO_TEXTDOMAN ); ?></label></th>
                        <td class="wfull"><input type="text" name="url" class="wfull"><p class="description"><?php _e( 'Iframe URL. (required)', GW_GO_TEXTDOMAN ); ?></p></td>
                        <td class="w100"></td>
                    </tr>
                    <tr class="go-pricing-popup-sc go_pricing_beatport go_pricing_soundcloud go_pricing_mixcloud go_pricing_custom_iframe">
                        <th class="w150"><label><?php _e( 'Height', GW_GO_TEXTDOMAN ); ?></label></th>
                        <td class="wfull"><input type="text" name="height" class="wfull"><p class="description"><?php _e( 'Iframe height in pixels e.g. "300". (optional)', GW_GO_TEXTDOMAN ); ?></p></td>
                        <td class="w100"></td>
                    </tr>																										                                                                            
                    <tr class="go-pricing-popup-sc go_pricing_audio">
                        <th class="w150"><label>MP3 <?php _e( 'source', GW_GO_TEXTDOMAN ); ?></label></th>
                        <td class="wfull"><input type="text" name="mp3_src" class="wfull"><p class="description"><?php _e( 'MP3 audio file. (optional', GW_GO_TEXTDOMAN ); ?>)</p></td>
                        <td class="w100 vtop"><a href="#" class="go-pricing-add-file-input go-pricing-popup-tb button-secondary" style="margin:1px 3px; padding: 4px 8px !important;"><span class="go-pricing-button-icon-add"></span><?php _e( 'Add', GW_GO_TEXTDOMAN ); ?></a></td>
                    </tr>
                    <tr class="go-pricing-popup-sc go_pricing_audio">
                        <th class="w150"><label>Ogg <?php _e( 'source', GW_GO_TEXTDOMAN ); ?></label></th>
                        <td class="wfull"><input type="text" name="ogg_src" class="wfull"><p class="description"><?php _e( 'Ogg audio file. (optional)', GW_GO_TEXTDOMAN ); ?></p></td>
                        <td class="w100 vtop"><a href="#" class="go-pricing-add-file-input go-pricing-popup-tb button-secondary" style="margin:1px 3px; padding: 4px 8px !important;"><span class="go-pricing-button-icon-add"></span><?php _e( 'Add', GW_GO_TEXTDOMAN ); ?></a></td>
                    </tr>
                    <tr class="go-pricing-popup-sc go_pricing_audio">
                        <th class="w150"><label>Wav <?php _e( 'source', GW_GO_TEXTDOMAN ); ?></label></th>
                        <td class="wfull"><input type="text" name="wav_src" class="wfull"><p class="description"><?php _e( 'Wav audio file. (optional)', GW_GO_TEXTDOMAN ); ?></p></td>
                        <td class="w100 vtop"><a href="#" class="go-pricing-add-file-input go-pricing-popup-tb button-secondary" style="margin:1px 3px; padding: 4px 8px !important;"><span class="go-pricing-button-icon-add"></span><?php _e( 'Add', GW_GO_TEXTDOMAN ); ?></a></td>
                    </tr>
                    <tr class="go-pricing-popup-sc go_pricing_html5_video go_pricing_audio go_pricing_youtube go_pricing_vimeo go_pricing_dailymotion go_pricing_metacafe go_pricing_soundcloud go_pricing_beatport">
                        <th class="w150"><label><?php _e( 'Autoplay?', GW_GO_TEXTDOMAN ); ?></label></th>
                        <td class="wfull"><input type="checkbox" name="autoplay" value="yes"> <?php _e( 'Yes', GW_GO_TEXTDOMAN ); ?><p class="description"><?php _e( 'Whether to autoplay audio/video. (optional)', GW_GO_TEXTDOMAN ); ?></p></td>
                        <td class="w100"></td>
                    </tr>
                    <tr class="go-pricing-popup-sc go_pricing_html5_video go_pricing_audio">
                        <th class="w150"><label><?php _e( 'Loop?', GW_GO_TEXTDOMAN ); ?></label></th>
                        <td class="wfull"><input type="checkbox" name="loop" value="yes"> <?php _e( 'Yes', GW_GO_TEXTDOMAN ); ?><p class="description"><?php _e( 'Whether to play audio/video continuously. (optional)', GW_GO_TEXTDOMAN ); ?></p></td>
                        <td class="w100"></td>
                    </tr>
                    <tr class="go-pricing-popup-sc go_pricing_map">
                        <th class="w150"><label><?php _e( 'Address', GW_GO_TEXTDOMAN ); ?></label></th>
                        <td class="wfull"><input type="text" name="address" class="wfull"><p class="description"><?php _e( 'Address to be shown on map e.g. "New York, USA".(required)', GW_GO_TEXTDOMAN ); ?></p></td>
                        <td class="w100"></td>
                    </tr>
                    <tr class="go-pricing-popup-sc go_pricing_map">
                        <th class="w150"><label><?php _e( 'Map height', GW_GO_TEXTDOMAN ); ?></label></th>
                        <td class="wfull"><input type="text" name="height" class="wfull" value="400"><p class="description"><?php _e( 'Height of map in pixels e.g. "300". (required)', GW_GO_TEXTDOMAN ); ?></p></td>
                        <td class="w100"></td>
                    </tr>                    
                    <tr class="go-pricing-popup-sc go_pricing_map">
                        <th class="w150"><label><?php _e( 'Zoom level', GW_GO_TEXTDOMAN ); ?></label></th>
                        <td class="wfull">
                                <select name="zoom" class="cucc wfull">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                    <option value="13">13</option>
                                    <option value="14" selected="selected">14</option>
                                    <option value="15">15</option>
                                    <option value="16">16</option>
                                    <option value="17">17</option>
                                    <option value="18">18</option>
                                    <option value="19">19</option>
                                    <option value="20">20</option>
                                </select>
                                <p class="description"><?php _e( 'Zoom level for map. (required)', GW_GO_TEXTDOMAN ); ?></p></td>
                        <td class="w100"></td>
                    </tr>
                    <tr class="go-pricing-popup-sc go_pricing_map">
                        <th class="w150"></th>
                        <td class="wfull">
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/pins/pin_1.png'; ?>" class="go-pricing-pin" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/pins/pin_2.png'; ?>" class="go-pricing-pin" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/pins/pin_3.png'; ?>" class="go-pricing-pin" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/pins/pin_4.png'; ?>" class="go-pricing-pin" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/pins/pin_5.png'; ?>" class="go-pricing-pin" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/pins/pin_6.png'; ?>" class="go-pricing-pin" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/pins/pin_7.png'; ?>" class="go-pricing-pin" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/pins/pin_8.png'; ?>" class="go-pricing-pin" />                            
                        </td>
                        <td class="w100"></td>
                    </tr>
                    <tr class="go-pricing-popup-sc go_pricing_map">
                        <th class="w150"><label><?php _e( 'Marker image source', GW_GO_TEXTDOMAN ); ?></label></th>
                        <td class="wfull"><input type="text" name="icon" class="wfull"><p class="description"><?php _e( 'Custom address marker on map. (optional)', GW_GO_TEXTDOMAN ); ?><br />
                <strong><?php _e( 'Leave blank to use the default marker.', GW_GO_TEXTDOMAN ); ?></strong></p></td>
                        <td class="w100 vtop"><a href="#" class="go-pricing-add-file-input go-pricing-popup-tb button-secondary" style="margin:1px 3px; padding: 4px 8px !important;"><span class="go-pricing-button-icon-add"></span>Add</a></td>
                    </tr>                    
                    <tr class="go-pricing-popup-sc go_pricing_map">
                        <th class="w150"><label><?php _e( 'Marker title', GW_GO_TEXTDOMAN ); ?></label></th>
                        <td class="wfull"><input type="text" name="title" class="wfull"><p class="description"><?php _e( 'Custom title attribute for marker on map. (optional)', GW_GO_TEXTDOMAN ); ?></p></td>
                        <td class="w100"></td>
                    </tr>                    
                    <tr class="go-pricing-popup-sc go_pricing_map">
                        <th class="w150"><label><?php _e( 'Map info window content', GW_GO_TEXTDOMAN ); ?></label></th>
                        <td class="wfull"><textarea name="content" class="wfull"></textarea><p class="description"><?php _e( 'Info window (popup) for marker on map. (optional)', GW_GO_TEXTDOMAN ); ?></p></td>
                        <td class="w100"></td>
                    </tr>                    
                    <tr class="go-pricing-popup-sc go_pricing_map">
                        <th class="w150"><label><?php _e( 'Info window by default?', GW_GO_TEXTDOMAN ); ?></label></th>
                        <td class="wfull"><input type="checkbox" name="popup" value="yes"> <?php _e( 'Yes', GW_GO_TEXTDOMAN ); ?><p class="description"><?php _e( 'Whether to show info window by default. (optional)', GW_GO_TEXTDOMAN ); ?></p></td>
                        <td class="w100"></td>
                    </tr>
                    <tr class="go-pricing-popup-submit">
                        <th class="w150"></th>
                        <td class="wfull" style="padding:20px 0 30px !important;"><a href="#" class="go-pricing-popup-insert-sc button-primary" style="margin:1px 3px; padding: 4px 8px !important;"><?php _e( 'Insert shortcode', GW_GO_TEXTDOMAN );?></a></td>
                        <td class="w100"></td>
                    </tr>
				</table>            
            <?php 
			exit;
        }
		
		/* ajax popup for body */
		public function go_pricing_sc_popup_body() {
			$this->go_pricing_load_textdomain();
			?>
            <div class="inside">
                <table class="form-table">
                <input type="hidden" class="go-pricing-popup-sc-selector" value="go_pricing_span" />
                    <tr class="go-pricing-popup-sc go_pricing_span">
                        <th class="w150"><?php _e( 'Select icon', GW_GO_TEXTDOMAN ); ?></th>
                        <td class="wfull">
                            <p style="margin-top:-2px; margin-bottom:6px;">
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_light_arrow.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-light-arrow" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_light_arrow2.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-light-arrow2" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_light_circle.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-light-circle" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_light_cross.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-light-cross" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_light_dot.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-light-dot" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_light_minus.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-light-minus" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_light_ok.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-light-ok" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_light_plus.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-light-plus" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_light_star.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-light-star" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_team_light_email.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-light-email" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_team_light_facebook.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-light-facebook" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_team_light_skype.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-light-skype" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_team_light_twitter.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-light-twitter" />                            
                            &nbsp;</p>
                            <p style="margin-top:-2px; margin-bottom:6px;">
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_dark_arrow.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-dark-arrow" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_dark_arrow2.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-dark-arrow2" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_dark_circle.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-dark-circle" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_dark_cross.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-dark-cross" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_dark_dot.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-dark-dot" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_dark_minus.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-dark-minus" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_dark_ok.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-dark-ok" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_dark_plus.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-dark-plus" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_dark_star.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-dark-star" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_team_dark_email.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-dark-email" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_team_dark_facebook.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-dark-facebook" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_team_dark_skype.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-dark-skype" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_team_dark_twitter.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-dark-twitter" />                            
                            &nbsp;</p>
                            <p style="margin-top:-2px; margin-bottom:6px;"> 
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_red_arrow.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-red-arrow" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_red_arrow2.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-red-arrow2" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_red_circle.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-red-circle" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_red_cross.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-red-cross" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_red_dot.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-red-dot" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_red_minus.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-red-minus" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_red_ok.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-red-ok" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_red_plus.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-red-plus" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_red_star.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-red-star" />
                            &nbsp;</p>
                            <p style="margin-top:-2px; margin-bottom:6px;"> 
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_green_arrow.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-green-arrow" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_green_arrow2.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-green-arrow2" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_green_circle.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-green-circle" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_green_cross.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-green-cross" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_green_dot.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-green-dot" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_green_minus.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-green-minus" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_green_ok.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-green-ok" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_green_plus.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-green-plus" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_green_star.png'; ?>" class="go-pricing-icon" data-attr="gw-go-icon-green-star" />
                            &nbsp;</p>
                            <input type="hidden" name="class">                                                  
                        </td>
                        <td class="w100"></td>
                    </tr>
                    <tr class="go-pricing-popup-sc go_pricing_span">
                        <th class="w150"><label><?php _e( 'Custom icon source', GW_GO_TEXTDOMAN ); ?></label></th>
                        <td class="wfull"><input type="text" name="custom-icon" class="wfull"><p class="description"><?php _e( 'Transparent image (png/gif), 20x20 pixels. (optional)', GW_GO_TEXTDOMAN ); ?><br />
                <strong><?php _e( 'Leave blank to use the default icons.', GW_GO_TEXTDOMAN ); ?></strong></p></td>
                        <td class="w100 vtop"><a href="#" class="go-pricing-add-file-input go-pricing-popup-tb button-secondary" style="margin:1px 3px; padding: 4px 8px !important;"><span class="go-pricing-button-icon-add"></span>Add</a></td>
                    </tr>                    
                    <tr class="go-pricing-popup-sc go_pricing_span">
                        <th class="w150"><label><?php _e( 'Icon alignment', GW_GO_TEXTDOMAN ); ?></label></th>
                        <td class="wfull">
                        	<select class="go-pricing-icon-align wfull">
                            	<option value=""><?php _e( 'Align center', GW_GO_TEXTDOMAN ); ?></option>
                            	<option value="gw-go-icon-left"><?php _e( 'Align left', GW_GO_TEXTDOMAN ); ?></option>
                                <option value="gw-go-icon-right"><?php _e( 'Align right', GW_GO_TEXTDOMAN ); ?></option>
                            </select>
                            <p class="description"><?php _e( 'Icon alignment. Default value "Align center". (optional)', GW_GO_TEXTDOMAN ); ?></p></td>
                        <td class="w100"></td>
                    </tr>
                    <tr class="go-pricing-popup-submit">
                        <th class="w150"></th>
                        <td class="wfull" style="padding:20px 0 30px !important;"><a href="#" class="go-pricing-popup-insert-sc button-primary" style="margin:1px 3px; padding: 4px 8px !important;"><?php _e( 'Insert shortcode', GW_GO_TEXTDOMAN );?></a></td>
                        <td class="w100"></td>
                    </tr>                                                                                                                                                                   
                </table>            
            <?php 
			exit;
        }
		
		/* ajax popup for buttons */
		public function go_pricing_sc_popup_button() {
			$this->go_pricing_load_textdomain();
			?>
            <div class="inside">
                <table class="form-table">
                <input type="hidden" class="go-pricing-popup-sc-selector" value="go_pricing_span" />
                    <tr class="go-pricing-popup-sc go_pricing_span">
                        <th class="w150"><?php _e( 'Select icon', GW_GO_TEXTDOMAN ); ?></th>
                        <td class="wfull">
                            <p style="margin-top:-2px; margin-bottom:6px;">
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_white_basket_medium.png'; ?>" class="go-pricing-icon go-pricing-icon-dark" data-attr="gw-go-btn-icon-medium-white-basket" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_white_download_medium.png'; ?>" class="go-pricing-icon go-pricing-icon-dark" data-attr="gw-go-btn-icon-medium-white-download" />
                            <img src="<?php echo plugin_dir_url( __FILE__ ) . 'assets/images/signs/icon_white_basket_large.png'; ?>" class="go-pricing-icon go-pricing-icon-dark" data-attr="gw-go-btn-icon-large-white-basket" />
                            &nbsp;</p>
                            <input type="hidden" name="class">                                                  
                        </td>
                        <td class="w100"></td>
                    </tr>
                    <tr class="go-pricing-popup-sc go_pricing_span">
                        <th class="w150"><label><?php _e( 'Custom icon source', GW_GO_TEXTDOMAN ); ?></label></th>
                        <td class="wfull"><input type="text" name="custom-icon" class="wfull"><p class="description"><?php _e( 'Transparent image (png/gif). 20x20 pixels for normal, 24x24 pixels for large buttons. (optional)', GW_GO_TEXTDOMAN ); ?><br />
                <strong><?php _e( 'Leave blank to use the default icons.', GW_GO_TEXTDOMAN ); ?></strong></p></td>
                        <td class="w100 vtop"><a href="#" class="go-pricing-add-file-input go-pricing-popup-tb button-secondary" style="margin:1px 3px; padding: 4px 8px !important;"><span class="go-pricing-button-icon-add"></span>Add</a></td>
                    </tr>                    
                    <tr class="go-pricing-popup-sc go_pricing_span">
                        <th class="w150"><label><?php _e( 'Icon alignment', GW_GO_TEXTDOMAN ); ?></label></th>
                        <td class="wfull">
                        	<select class="go-pricing-icon-align wfull">
                            	<option value=""><?php _e( 'Align center', GW_GO_TEXTDOMAN ); ?></option>
                            	<option value="gw-go-icon-left"><?php _e( 'Align left', GW_GO_TEXTDOMAN ); ?></option>
                                <option value="gw-go-icon-right"><?php _e( 'Align right', GW_GO_TEXTDOMAN ); ?></option>
                            </select>
                            <p class="description"><?php _e( 'Icon alignment. Default value "Align center". (optional)', GW_GO_TEXTDOMAN ); ?></p></td>
                        <td class="w100"></td>
                    </tr>
                    <tr class="go-pricing-popup-sc go_pricing_span">
                        <th class="w150"><label><?php _e( 'Icon size', GW_GO_TEXTDOMAN ); ?></label></th>
                        <td class="wfull">
                        	<select class="go-pricing-icon-size wfull">
                            	<option value=""><?php _e( 'Medium', GW_GO_TEXTDOMAN ); ?></option>
                            	<option value="gw-go-btn-icon-large"><?php _e( 'Large', GW_GO_TEXTDOMAN ); ?></option>
                            </select>
                            <p class="description"><?php _e( 'Icon size. Default value "Medium". (optional)', GW_GO_TEXTDOMAN ); ?></p></td>
                        <td class="w100"></td>
                    </tr>                    
                    <tr class="go-pricing-popup-submit">
                        <th class="w150"></th>
                        <td class="wfull" style="padding:20px 0 30px !important;"><a href="#" class="go-pricing-popup-insert-sc button-primary" style="margin:1px 3px; padding: 4px 8px !important;"><?php _e( 'Insert shortcode', GW_GO_TEXTDOMAN );?></a></td>
                        <td class="w100"></td>
                    </tr>                                                                                                                                                                   
                </table>            
            <?php 
			exit;
        }				

		/* handle die for not-logged-in visitors */ 
		public function go_pricing_nopriv_ajax_submit() {
			die ( __( 'Oops, authorized persons only!', GW_GO_TEXTDOMAN ) );
		}
	}

//+----------------------------------------------------------------+
//| Initiate class
//+----------------------------------------------------------------+
	
	$GW_GoPricing = new GW_GoPricing();
	add_filter('upload_mimes', array( $GW_GoPricing, 'go_pricing_add_webm_mime'), 1, 1);	

//+----------------------------------------------------------------+
//| AJAX actions
//+----------------------------------------------------------------+

	/* ajax popup header */
	add_action( 'wp_ajax_nopriv_go_pricing_sc_popup_header', array( $GW_GoPricing, 'go_pricing_nopriv_ajax_submit' ) );
	add_action( 'wp_ajax_go_pricing_sc_popup_header', array( $GW_GoPricing, 'go_pricing_sc_popup_header' ) );

	/* ajax popup body */
	add_action( 'wp_ajax_nopriv_go_pricing_sc_popup_body', array( $GW_GoPricing, 'go_pricing_nopriv_ajax_submit' ) );
	add_action( 'wp_ajax_go_pricing_sc_popup_body', array( $GW_GoPricing, 'go_pricing_sc_popup_body' ) );
	
	/* ajax popup button */
	add_action( 'wp_ajax_nopriv_go_pricing_sc_popup_button', array( $GW_GoPricing, 'go_pricing_nopriv_ajax_submit' ) );
	add_action( 'wp_ajax_go_pricing_sc_popup_button', array( $GW_GoPricing, 'go_pricing_sc_popup_button' ) );	

	/* settings page ajax events */
	add_action( 'wp_ajax_nopriv_go_pricing_settings_ajax_submit', array( $GW_GoPricing, 'go_pricing_nopriv_ajax_submit' ) );
	add_action( 'wp_ajax_go_pricing_settings_ajax_submit', array( $GW_GoPricing, 'go_pricing_settings_ajax_submit' ) );
	
	/* settings page ajax events */
	add_action( 'wp_ajax_nopriv_go_pricing_import_export_ajax_submit', array( $GW_GoPricing, 'go_pricing_nopriv_ajax_submit' ) );
	add_action( 'wp_ajax_go_pricing_import_export_ajax_submit', array( $GW_GoPricing, 'go_pricing_import_export_ajax_submit' ) );	
	
	/* pricing table creator page */
	add_action( 'wp_ajax_nopriv_go_pricing_ajax_submit', array( $GW_GoPricing, 'go_pricing_nopriv_ajax_submit' ) );
	add_action( 'wp_ajax_go_pricing_ajax_submit', array( $GW_GoPricing, 'go_pricing_ajax_submit' ) );
	
} else {
	
	die ( __( 'GW_GoPricing class has been already declared!', GW_GO_TEXTDOMAN ) );

}