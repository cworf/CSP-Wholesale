<?php
class TT_Security extends TeslaFramework {

	public $username;
	public $license = NULL;
	public $state = 'active';
	public $update_key = 'teslathemes_encription_key';
	public $custom_error_message = NULL;

	function __construct(){
		$this->username = ($this->get_license())?base64_decode(file_get_contents($this->get_license())):NULL;
	}

	function get_license(){
		$location = TT_THEME_DIR . "/theme_config/tt_license.txt";
		if (file_exists($location)){
			$this->license = $location;			
			return $location;
		}else{
			$this->state = 'corrupt';
			return NULL;
		}
	}

	public function check_state(){
		$this->check_username();
		return $this->throw_errors();
	}

	public function throw_errors(){
		switch ($this->state) {
			case 'active':
				break;
			case 'warning' :
				$this->error_message(true);
				break;
			case 'no data':
				$this->error_message(true,"<span>Error :</span> '<em>" . $this->custom_error_message . "'</em><br><hr>");
				break;
			case 'blocked':
				$this->error_message();
				return FALSE;
			case 'corrupt':
				$this->error_message( false, "<span>Note :</span> Don't change the code or license file contents please.");
				return FALSE;
		}
		return TRUE;
	}

	public function change_state($new_state){
		$this->state = $new_state;
		return;
	}

	public function check_username(){
		if ($this->username){
			if( in_array( $this->username, array( 'tt_general_user','tt_other_marketplaces_user' ) ) )
				return;
			$result = get_transient( 'security_api_result' );
			if(!$result){
				$api = wp_remote_get( 'http://teslathemes.com/amember/api/check-access/by-login?_key=n7RYtMjOm9qjzmiWQlta&login=' . $this->username , array('timeout' => 15) );
				if(!empty($api) && !is_wp_error( $api )){
					$result = json_decode(wp_remote_retrieve_body($api));
					set_transient( 'security_api_result', $result, 120 * MINUTE_IN_SECONDS );
				}else{
					if(is_wp_error( $api )){
						$this->custom_error_message = $api->get_error_message();
					}
					$this->state = 'no data' ;
					return;
				}
			}
			
			if ( $result->ok ){
				if (!empty($result->subscriptions)){
					if ( !empty($result->subscriptions->{28}) ){
						$this->state = 'blocked' ;
					}elseif(!empty($result->subscriptions->{34})){
						$this->state = 'warning' ;
					}
				}
			}else
				$this->state = 'corrupt';
		}else
			$this->state = 'corrupt';
		return;
	}

	private function error_message($just_warning=NULL,$custom=NULL){
		echo "<div id='result_content'><div id='tt_import_alert'>";
		if($custom)
			echo $custom;
		if ( $just_warning )
			echo '<span>WARNING :</span> We noticed some fraudulent activity with our theme or couldn\'t connect to our servers for some reasons. Please contact us in 5 days to fix this or '.THEME_PRETTY_NAME.' framework page will be blocked.<br> <span>State : ' . $this->state . '</span>';
		else{
			echo 'The '.THEME_PRETTY_NAME.' page is <span>blocked</span> by TeslaThemes due to some <span>fraudulent action</span>.<br> Please contact us at support@teslathemes.com or click the link below to correct your license if you think that this is a mistake. <br><span>State : ' . $this->state . '</span>';
		}
		$mail_body = rawurlencode("Insert Your Credentials Below \n \n ================== \n WP INSTALLATION URL: \n WP USERNAME: \n WP PASSWORD: \n \n FTP HOST: \n FTP USERNAME: \n FTP PASSWORD: \n ======================= \n");
		echo "</div><a target='_blank' href='mailto:support@teslathemes.com?subject=[".THEME_NAME."]%20Security&body=$mail_body' class='btn'>Contact TeslaThemes</a></div>";
		return;
	}

}