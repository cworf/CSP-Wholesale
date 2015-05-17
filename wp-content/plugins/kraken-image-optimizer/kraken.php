<?php
/*
	Copyright 2014  Karim Salman  (email : ksalman@kraken.io)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
 * Plugin Name: Kraken Image Optimizer
 * Plugin URI: http://wordpress.org/plugins/kraken-image-optimizer/
 * Description: This plugin allows you to optimize your WordPress images through the Kraken API, the world's most advanced image optimization solution.
 * Author: Karim Salman
 * Version: 1.0.5.3
 * Stable Tag: 1.0.5.3
 * Author URI: https://kraken.io
 * License GPL2
 */


if ( !class_exists( 'Wp_Kraken' ) ) {

	class Wp_Kraken {

		private $id;

		private $kraken_settings = array();

		private $thumbs_data = array();

		private $optimization_type = 'lossy';

		function __construct() {
			$plugin_dir_path = dirname( __FILE__ );
			require_once( $plugin_dir_path . '/lib/Kraken.php' );
			$this->kraken_settings = get_option( '_kraken_options' );
			$this->optimization_type = $this->kraken_settings['api_lossy'];
			add_action( 'admin_init', array( &$this, 'admin_init' ) );
			add_action( 'admin_enqueue_scripts', array( &$this, 'my_enqueue' ) );
			add_action( 'wp_ajax_kraken_request', array( &$this, 'kraken_media_library_ajax_callback' ) );
			add_action( 'manage_media_custom_column', array( &$this, 'fill_media_columns' ), 10, 2 );
			add_filter( 'manage_media_columns', array( &$this, 'add_media_columns') );
			add_filter( 'wp_generate_attachment_metadata', array( &$this, 'optimize_thumbnails') );
			add_action( 'add_attachment', array( &$this, 'kraken_media_uploader_callback' ) );
		}

		/*
		 *  Adds kraken fields and settings to Settings->Media settings page
		 */
		function admin_init() {

			add_settings_section( 'kraken_image_optimizer', 'Kraken Image Optimizer', array( &$this, 'show_kraken_image_optimizer' ), 'media' );

			register_setting(
				'media',
				'_kraken_options',
				array( &$this, 'validate_options' )
			);

			add_settings_field(
				'kraken_api_key',
				'API Key:',
				array( &$this, 'show_api_key' ),
				'media',
				'kraken_image_optimizer'
			);

			add_settings_field(
				'kraken_api_secret',
				'API Secret:',
				array( &$this, 'show_api_secret' ),
				'media',
				'kraken_image_optimizer'
			);

			add_settings_field(
				'kraken_lossy',
				'Optimization Type:',
				array( &$this, 'show_lossy' ),
				'media',
				'kraken_image_optimizer'
			);

			add_settings_field(
				'credentials_valid',
				'API status:',
				array( &$this, 'show_credentials_validity' ),
				'media',
				'kraken_image_optimizer'
			);

		}

		function my_enqueue( $hook ) {
			if ( $hook == 'options-media.php' || $hook == 'upload.php') {
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'tipsy-js', plugins_url( '/js/jquery.tipsy.js', __FILE__ ), array( 'jquery' ) );
				wp_enqueue_script( 'async-js', plugins_url( '/js/async.js', __FILE__ ) );
				wp_enqueue_script( 'ajax-script', plugins_url( '/js/ajax.js', __FILE__ ), array( 'jquery' ) );
				wp_enqueue_style( 'kraken_admin_style', plugins_url( 'css/admin.css', __FILE__ ) );
				wp_enqueue_style( 'tipsy-style', plugins_url( 'css/tipsy.css', __FILE__ ) );
				wp_enqueue_style( 'modal-style', plugins_url( 'css/jquery.modal.css', __FILE__ ) );
				wp_localize_script( 'ajax-script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
				wp_enqueue_script( 'modal-js', plugins_url( '/js/jquery.modal.min.js', __FILE__ ), array( 'jquery' ) );				
			}
		}

		function get_api_status( $api_key, $api_secret ) {

			/*  Possible API Status Errors:
			 *
			 * 'Incoming request body does not contain a valid JSON object'
			 * 'Incoming request body does not contain a valid auth.api_key or auth.api_secret'
			 * 'Kraken has encountered an unexpected error and cannot fulfill your request'
			 * 'User not found'
			 * 'API Key and API Secret mismatch'
			 */

			if ( !empty( $api_key ) && !empty( $api_secret ) ) {
				$kraken = new Kraken( $api_key, $api_secret );
				$status = $kraken->status();
				return $status;
			}
			return false;
		}

		/**
		 *  Handles optimizing already-uploaded images in the  Media Library
		 */
		function kraken_media_library_ajax_callback() {

			$image_id = (int) $_POST['id'];
			$type = false;
			if ( isset( $_POST['type'] ) ) {
				$type = $_POST['type'];
			}

			$this->id = $image_id;

			if ( wp_attachment_is_image( $image_id ) ) {

				$image_path = get_attached_file( $image_id );
				$settings = $this->kraken_settings;
				$api_key = isset( $settings['api_key'] ) ? $settings['api_key'] : '';
				$api_secret = isset( $settings['api_secret'] ) ? $settings['api_secret'] : '';

				$status = $this->get_api_status( $api_key, $api_secret );

				if ( $status === false ) {
					$kv['error'] = 'There is a problem with your credentials. Please check them in the Kraken.io settings section of Media Settings, and try again.';
					update_post_meta( $image_id, '_kraken_size', $kv );
					echo json_encode( array( 'error' => $kv['error'] ) );
					exit;
				}

				if ( isset( $status['active'] ) && $status['active'] === true ) {

				} else {
					echo json_encode( array( 'error' => 'Your API is inactive. Please visit your account settings' ) );
					die();
				}

				$result = $this->optimize_image( $image_path, $type );

				$kv = array();

				if ( $result['success'] == true && !isset( $result['error'] ) ) {

					$kraked_url = $result['kraked_url'];
					$savings_percentage = (int) $result['saved_bytes'] / (int) $result['original_size'] * 100;
					$kv['original_size'] = self::pretty_kb( $result['original_size'] );
					$kv['kraked_size'] = self::pretty_kb( $result['kraked_size'] );
					$kv['saved_bytes'] = self::pretty_kb( $result['saved_bytes'] );
					$kv['savings_percent'] = round( $savings_percentage, 2 ) . '%';
					$kv['type'] = $result['type'];
					$kv['success'] = true;
					$kv['meta'] = wp_get_attachment_metadata( $image_id );

					if ( $this->replace_image( $image_path, $kraked_url ) ) {

						// get metadata for thumbnails
						$image_data = wp_get_attachment_metadata( $image_id );
						$this->optimize_thumbnails( $image_data );

						// store kraked info to DB
						update_post_meta( $image_id, '_kraken_size', $kv );

						// krak thumbnails, store that data too
						$kraked_thumbs_data = get_post_meta( $image_id, '_kraked_thumbs', true );
						if ( !empty( $kraked_thumbs_data ) ) {
							$kv['thumbs_data'] = $kraked_thumbs_data;
						}

						echo json_encode( $kv );
					} else {
						echo json_encode( array( 'error' => 'Could not overwrite original file. Please ensure that your files are writable by plugins.' ) );
						exit;
					}
				} else {

					// error or no optimization
					if ( file_exists( $image_path ) ) {

						$kv['original_size'] = self::pretty_kb( filesize( $image_path ) );
						$kv['error'] = $result['error'];
						$kv['type'] = $result['type'];

						if ( $kv['error'] == 'This image can not be optimized any further' ) {
							$kv['kraked_size'] = 'No savings found';
							$kv['no_savings'] = true;
						}

						update_post_meta( $image_id, '_kraken_size', $kv );

					} else {
						// file not found
					}
					echo json_encode($result);
				}
			}
			die();
		}

		/**
		 *  Handles optimizing images uploaded through any of the media uploaders.
		 */
		function kraken_media_uploader_callback( $image_id ) {
			$this->id = $image_id;

			if ( wp_attachment_is_image( $image_id ) ) {

				$settings = $this->kraken_settings;
				$type = $settings['api_lossy'];
				$image_path = get_attached_file( $image_id );
				$result = $this->optimize_image( $image_path, $type );

				if ( $result['success'] == true && !isset( $result['error'] ) ) {

					$kraked_url = $result['kraked_url'];
					$savings_percentage = (int) $result['saved_bytes'] / (int) $result['original_size'] * 100;
					$kv['original_size'] = self::pretty_kb( $result['original_size'] );
					$kv['kraked_size'] = self::pretty_kb( $result['kraked_size'] );
					$kv['saved_bytes'] = self::pretty_kb( $result['saved_bytes'] );
					$kv['savings_percent'] = round( $savings_percentage, 2 ) . '%';
					$kv['type'] = $result['type'];
					$kv['success'] = true;
					$kv['meta'] = wp_get_attachment_metadata( $image_id );

					if ( $this->replace_image( $image_path, $kraked_url ) ) {
						update_post_meta( $image_id, '_kraken_size', $kv );
					} else {
						// writing image failed
					}
				} else {

					// error or no optimization
					if ( file_exists( $image_path ) ) {

						$kv['original_size'] = self::pretty_kb( filesize( $image_path ) );
						$kv['error'] = $result['error'];
						$kv['type'] = $result['type'];

						if ( $kv['error'] == 'This image can not be optimized any further' ) {
							$kv['kraked_size'] = 'No savings found';
							$kv['no_savings'] = true;
						}

						update_post_meta( $image_id, '_kraken_size', $kv );

					} else {
						// file not found
					}
				}
			}
		}


		function show_credentials_validity() {

			$settings = $this->kraken_settings;
			$api_key = isset( $settings['api_key'] ) ? $settings['api_key'] : '';
			$api_secret = isset( $settings['api_secret'] ) ? $settings['api_secret'] : '';

			$status = $this->get_api_status( $api_key, $api_secret );
			$url = admin_url() . 'images/';

			if ( $status !== false && isset( $status['active'] ) && $status['active'] === true ) {
				$url .= 'yes.png';
				echo '<p class="apiStatus">Your credentials are valid <span class="apiValid" style="background:url(' . "'$url') no-repeat 0 0" . '"></span></p>';
			} else {
				$url .= 'no.png';
				echo '<p class="apiStatus">There is a problem with your credentials <span class="apiInvalid" style="background:url(' . "'$url') no-repeat 0 0" . '"></span></p>';
			}
		}

		function show_kraken_image_optimizer() {
			echo '<a href="http://kraken.io" title="Visit Kraken.io Homepage">Kraken.io</a> API settings';
		}

		function validate_options( $input ) {
			$valid = array();
			$error = '';
			$valid['api_lossy'] = $input['api_lossy'];

			if ( !function_exists( 'curl_exec' ) ) {
				$error = 'cURL not available. Kraken Image Optimizer requires cURL in order to communicate with Kraken.io servers. <br /> Please ask your system administrator or host to install PHP cURL, or contact support@kraken.io for advice';
			} else {
				$status = $this->get_api_status( $input['api_key'], $input['api_secret'] );

				if ( $status !== false ) {

					if ( isset($status['active']) && $status['active'] === true ) {
						if ( $status['plan_name'] === 'Developers' ) {
							$error = 'Developer API credentials cannot be used with this plugin.';
						} else {
							$valid['api_key'] = $input['api_key'];
							$valid['api_secret'] = $input['api_secret'];
						}
					} else {
						$error = 'There is a problem with your credentials. Please check them from your Kraken.io account.';
					}

				} else {
					$error = 'Please enter a valid Kraken.io API key and secret';
				}				
			}

			if ( !empty( $error)  ) {
				add_settings_error(
					'media',
					'api_key_error',
					$error,
					'error'
				);
			}
			return $valid;
		}

		function show_api_key() {
			$settings = $this->kraken_settings;
			$value = isset( $settings['api_key'] ) ? $settings['api_key'] : '';
			?>
				<input id='kraken_api_key' name='_kraken_options[api_key]'
				 type='text' value='<?php echo esc_attr( $value ); ?>' size="50"/>
			<?php
		}

		function show_api_secret() {
			$settings = $this->kraken_settings;
			$value = isset( $settings['api_secret'] ) ? $settings['api_secret'] : '';
			?>
				<input id='kraken_api_secret' name='_kraken_options[api_secret]'
				 type='text' value='<?php echo esc_attr( $value ); ?>' size="50"/>
			<?php
		}

		function show_lossy() {
			$options = get_option( '_kraken_options' );
			$value = isset( $options['api_lossy'] ) ? $options['api_lossy'] : 'lossy';

			$html = '<input type="radio" id="kraken_lossy" name="_kraken_options[api_lossy]" value="lossy"' . checked( 'lossy', $value, false ) . '/>';
			$html .= '<label for="kraken_lossy">Lossy</label>';

			$html .= '<input style="margin-left:10px;" type="radio" id="kraken_lossless" name="_kraken_options[api_lossy]" value="lossless"' . checked( 'lossless', $value, false ) . '/>';
			$html .= '<label for="kraken_lossless">Lossless</label>';

			echo $html;
		}

		function add_media_columns( $columns ) {
			$columns['original_size'] = 'Original Size';
			$columns['kraked_size'] = 'Kraked Size';
			return $columns;
		}

		function fill_media_columns( $column_name, $id ) {

			$original_size = filesize( get_attached_file( $id ) );
			$original_size = self::pretty_kb( $original_size );

			$options = get_option( '_kraken_options' );
			$type = isset( $options['api_lossy'] ) ? $options['api_lossy'] : 'lossy';


			if ( strcmp( $column_name, 'original_size' ) === 0 ) {
				if ( wp_attachment_is_image( $id ) ) {

					$meta = get_post_meta( $id, '_kraken_size', true );

					if ( isset( $meta['original_size'] ) ) {
						echo $meta['original_size'];
					} else {
						echo $original_size;
					}
				} else {
					echo $original_size;
				}
			} else if ( strcmp( $column_name, 'kraked_size' ) === 0 ) {

				if ( wp_attachment_is_image( $id ) ) {

					$meta = get_post_meta($id, '_kraken_size', true);

					// Is it optimized? Show some stats
					if ( isset( $meta['kraked_size'] ) && empty( $meta['no_savings'] ) ) {
						$kraked_size = $meta['kraked_size'];
						$type = $meta['type'];
						$savings_percentage = $meta['savings_percent'];
						echo '<strong>' . $kraked_size .'</strong><br /><small>Type:&nbsp;' . $type . '</small><br /><small>Savings:&nbsp;' . $savings_percentage . '</small>';

						$thumbs_data = get_post_meta( $id, '_kraked_thumbs', true );
						$thumbs_count = count( $thumbs_data );

						if ( !empty( $thumbs_data ) ) {
							echo '<br /><small>' . $thumbs_count . ' thumbs optimized' . '</small>';
						}

					// Were there no savings, or was there an error?
					} else {
						$image_url = wp_get_attachment_url( $id );
						$filename = basename( $image_url );
						echo '<div class="buttonWrap"><button data-setting="' . $type . '" type="button" class="kraken_req" data-id="' . $id . '" id="krakenid-' . $id .'" data-filename="' . $filename . '" data-url="' . $image_url . '">Optimize This Image</button><span class="krakenSpinner"></span></div>';
						if ( !empty( $meta['no_savings'] ) ) {
							echo '<div class="noSavings"><strong>No savings found</strong><br /><small>Type:&nbsp;' . $meta['type'] . '</small></div>';
						} else if ( isset( $meta['error'] ) ) {
							$error = $meta['error'];
							echo '<div class="krakenErrorWrap"><a class="krakenError" title="' . $error . '">Failed! Hover here</a></div>';
						}
					}
				} else {
					echo 'n/a';
				}
			}
		}

		function replace_image( $image_path, $kraked_url ) {
			$rv = false;
			$ch =  curl_init( $kraked_url );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
	        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );   
			$result = curl_exec( $ch );
			$rv = file_put_contents( $image_path, $result );
			return $rv !== false;
		}

		function optimize_image( $image_path, $type ) {
			$settings = $this->kraken_settings;
			$kraken = new Kraken( $settings['api_key'], $settings['api_secret'] );

			if ( !empty( $type ) ) {
				$lossy = $type === 'lossy';
			} else {
				$lossy = $settings['api_lossy'] === "lossy";
			}

			$params = array(
				"file" => $image_path,
				"wait" => true,
				"lossy" => $lossy,
				"origin" => "wp"
			);

			$data = $kraken->upload( $params );

			$data['type'] = !empty( $type ) ? $type : $settings['api_lossy'];

			return $data;
		}

		function optimize_thumbnails( $image_data ) {

			$image_id = $this->id;
			if ( empty( $image_id ) ) {
				global $wpdb;
				$post = $wpdb->get_row( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_value = %s LIMIT 1", $image_data['file'] ) );
				$image_id = $post->post_id;
			}

			$path_parts = pathinfo( $image_data['file'] );

			// e.g. 04/02, for use in getting correct path or URL
			$upload_subdir = $path_parts['dirname'];

			$upload_dir = wp_upload_dir();

			// all the way up to /uploads
			$upload_base_path = $upload_dir['basedir'];
			$upload_full_path = $upload_base_path . '/' . $upload_subdir;

			$sizes = array();

			if ( isset( $image_data['sizes'] ) ) {
				$sizes = $image_data['sizes'];
			}

			if ( !empty( $sizes ) ) {

				$thumb_path = '';

				$thumbs_optimized_store = array();
				$this_thumb = array();

				foreach ( $sizes as $key => $size ) {

					$thumb_path = $upload_full_path . '/' . $size['file'];

					if ( file_exists( $thumb_path ) !== false ) {

						$result = $this->optimize_image( $thumb_path, $this->optimization_type );

						if ( !empty($result) && isset($result['success']) && isset( $result['kraked_url'] ) ) {
							$kraked_url = $result["kraked_url"];
							if ( $this->replace_image( $thumb_path, $kraked_url ) ) {
								$this_thumb = array( 'thumb' => $key, 'file' => $size['file'], 'original_size' => $result['original_size'], 'kraked_size' => $result['kraked_size'], 'type' => $this->optimization_type );
								$thumbs_optimized_store [] = $this_thumb;
							}
						}
					}
				}
			}
			if ( !empty( $thumbs_optimized_store ) ) {
				update_post_meta( $image_id, '_kraked_thumbs', $thumbs_optimized_store, false );
			}
			return $image_data;
		}

		static function pretty_kb( $bytes ) {
			return round( ( $bytes / 1024 ), 2 ) . ' kB';
		}
	}
}

new Wp_Kraken();
