<?php
if(!class_exists('AP_YaMoney')){
class AP_YaMoney
{
    private $token, $name, $app_id, $app_key;
	
    function __construct($app_id, $app_key, $name)
    {
				
		$this->app_id = trim($app_id);
		$this->app_key = trim($app_key);
		$this->name = $name = trim($name);		
				
		$this->token = $this->get_token($name);		
    }		
	
	public function get_token($name){
		
		$file = PN_PLUGIN_DIR . 'paymerchants/'. $name .'/dostup/access_token.php';
		if(!file_exists($file)){
			file_put_contents($file, ' ');
		}
		$token = '';
		if(file_exists($file)){
			$token = file_get_contents( $file );
		}
		return trim($token);
	}
	
	public function update_token($token){
		
		$token = trim(esc_html(strip_tags($token)));
		@file_put_contents( PN_PLUGIN_DIR.'paymerchants/'. $this->name .'/dostup/access_token.php', $token );
		
	}	
	
	public function accountInfo($token=''){
		return $this->request('https://money.yandex.ru/api/account-info', array(), $token);
	}	
	
	public function addPay($purse, $sum, $comment, $label) {
		
		$res = $this->request( 'https://money.yandex.ru/api/request-payment', array(
			'pattern_id' => 'p2p',
			'to' => $purse,
			'amount_due' => $sum,
			'comment' => $comment,
			'message' => $comment,
			'label' => $label,
		));
		
		if(isset($res['request_id'])){
			return $res['request_id'];
		}
	}

	public function processPay($request_id) {
		
		$data = array();
		$data['error'] = 1;	
		
		$res = $this->request( 'https://money.yandex.ru/api/process-payment', array(
			'request_id' => $request_id,
		));
		
		if(isset($res['status']) and $res['status'] == 'success'){
			$data['error'] = 0;
		}
		
		return $data;
	}	
	
	public function auth() {
		$code = $_GET['code'];
	
		$res = $this->request( 'https://sp-money.yandex.ru/oauth/token', array(
			'code' => $code,
			'client_id' => $this->app_id,
			'grant_type' => 'authorization_code',
			'redirect_uri' => get_merchant_link('ap_'. $this->name .'_verify'),
			'client_secret' => $this->app_key
		));
		if( isset($res['access_token']) ){
			return $res['access_token'];
		}
	}	
	
	public function request($url, $data, $now_token=''){
		
		$data = (array)$data;
		
		$token = '';
		if($now_token){
			$token = $now_token;
		} elseif($this->token){
			$token = $this->token;
		}		
		
		$c_options = array(
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => http_build_query($data),
		);	
				
		if($token){	
			$c_options[CURLOPT_HTTPHEADER] = array( 'Authorization: Bearer '. $token );
		}			
			
		$c_result = get_curl_parser($url, $c_options, 'autopay', 'yamoney');
		$err  = $c_result['err'];
		$out = $c_result['output'];		
		if(!$err and $out != ''){
			if($res = @json_decode( $out, true )){
				return $res;
			} 
		}	
		
	}
	
}
}