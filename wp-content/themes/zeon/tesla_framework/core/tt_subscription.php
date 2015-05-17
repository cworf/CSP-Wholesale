<?php
//===============Subscription class================================ 
class TT_Subscription extends TeslaFramework{

	private static $config;

	public function __construct(){}

	public static function subscription_init() {
		if (file_exists( TT_THEME_DIR . '/theme_config/subscription.php' )){
			//get config files
			self::$config = include TT_THEME_DIR . '/theme_config/subscription.php';
			//registering axaj hooks
			self::subscriptions_ajax_hooks();
			add_action('wp_enqueue_scripts', array('TT_Subscription','enqueue_scripts'));
		}
	}

	private static function subscriptions_ajax_hooks(){
		add_action('wp_ajax_insert_subscription', array('TT_Subscription','insert_subscription_ajax'));
		add_action('wp_ajax_nopriv_insert_subscription', array('TT_Subscription','insert_subscription_ajax'));

		add_action('wp_ajax_get_subscription_configs', array('TT_Subscription','get_subscription_configs_ajax'));
		add_action('wp_ajax_nopriv_get_subscription_configs', array('TT_Subscription','get_subscription_configs_ajax'));

		add_action('wp_ajax_get_mailchimp_lists', array('TT_Subscription','get_mailchimp_lists_ajax'));
	}

	public static function enqueue_scripts(){
		wp_enqueue_script('subscription', TT_FW . '/static/js/subscription.js', '', false, true);
	}
	public static function get_subscription_configs_ajax(){
		if (!empty(self::$config))
			die(json_encode(self::$config));
		else
			die(false);
	}

	public static function insert_subscription_ajax(){
		$form = $_POST;
		$myFile = TT_THEME_DIR . '/subscriptions.txt';
		$myCSV = TT_THEME_DIR . '/subscriptions.csv';
		$format = (!empty(self::$config['date_format']))?self::$config['date_format'] : "F j, Y, g:i a" ;
		$date = date( $format );

		foreach($form as $name=>$input){
			if($name == 'action')
				continue;
			$data[$name] = $input;
			$headlines[$name] = ucfirst($name);
		}
		if(!empty($data)){
			$headlines[] = (!empty(self::$config['date_headline'])) ? self::$config['date_headline'] : 'Date' ;
			$data ['date'] = $date;

			$txt_first = false;
			$csv_first = false;

			if ( !file_exists ($myFile) )
				$txt_first = true;
			if ( !file_exists ($myCSV) )
				$csv_first = true;

			$fh = fopen( $myFile, 'a' ) or die($result = (!empty(self::$config['error_open_create_files_msg']))?self::$config['error_open_create_files_msg'] : $php_errormsg) ; //open/create txt file
			$fp = fopen( $myCSV, 'a' ) or die($result = (!empty(self::$config['error_open_create_files_msg']))?self::$config['error_open_create_files_msg'] : $php_errormsg) ;   //open/create CSV FILE
			
			if($txt_first)
				fputcsv( $fh, $headlines ,"\t");
			if($csv_first)
				fputcsv( $fp, $headlines );

			if ( fputcsv( $fh, $data ,"\t") &&                  //write txt file
				 fputcsv($fp, $data) )                          //write csv file
				$result = (!empty(self::$config['success_msg'])) ? self::$config['success_msg'] : __('Subscribed','TeslaFramework');
			else
			  $result = (!empty(self::$config['error_writing_msg']))?self::$config['error_writing_msg'] : __('Error','TeslaFramework');
											
			if(_go('mailchimp')){       //send api call to mailchimp if so selected in FW
				$mailchimp_msg = ( !empty(self::$config['error_mailchimp'] ) ) ? self::mailchimp_call($data) : self::mailchimp_call($data,true);
				$result = $mailchimp_msg === true ? $result : (!empty($mailchimp_msg) ? $mailchimp_msg : (!empty(self::$config['error_mailchimp']) ? self::$config['error_mailchimp'] : __('MailChimp Error','TeslaFramework')));
			}

			fclose( $fh );
			fclose( $fp );
			
		}else
			$result = ( !empty( self::$config['no_data_posted_msg'] ) ) ? self::$config['no_data_posted_msg'] : __('No data received','TeslaFramework');
		
		die(json_encode($result));
	}

	private static function mailchimp_call($data,$grab_error = false){
		if(_go('mailchimp_api_key') && _go('mailchimp_list_id')){
			$apikey = _go('mailchimp_api_key');
			$lsit_id = _go('mailchimp_list_id');
			if(preg_match('@-(.*)@is',$apikey,$dc_matches)){
				$dc = $dc_matches[1];
				$url = "https://$dc.api.mailchimp.com/2.0/lists/subscribe"; //subscribes user to list with id
				$email = $data['email'];
				unset($data['email']);
				$post_data = array(
					'apikey'=>$apikey,
					'id'=>$lsit_id,
					'email'=>array(
							'email'=>$email
						),
					'merge_vars'=>$data
					);
				if($grab_error)
					$result = curl_mailchimp($url,$post_data,true);
				else
					$result = curl_mailchimp($url,$post_data);
			}
		}else
			$result = ($grab_error) ? __('No API key for mailchimp','TeslaFramework') : FALSE;
		return $result;
	}

	public static function get_mailchimp_lists($custom_api_key = NULL){
		$apikey = ($custom_api_key) ? $custom_api_key : _go('mailchimp_api_key');
		if(preg_match('@-(.*)@is',$apikey,$dc_matches)){
			$dc = $dc_matches[1];
			$url = "https://$dc.api.mailchimp.com/2.0/lists/list";
			$post_data = array(
						'apikey'=>$apikey
						);
			$result = curl_mailchimp($url,$post_data,true,true);
		}else
			$result = __('Not a valid mailchimp api key.');
		return $result;
	}

	static function get_mailchimp_lists_ajax(){
		$api_key = (!empty($_POST['api_key'])) ? $_POST['api_key'] : NULL;
		die(json_encode(self::get_mailchimp_lists($api_key)));
	}


}
//=================END SUBSCRIPTION class=============================