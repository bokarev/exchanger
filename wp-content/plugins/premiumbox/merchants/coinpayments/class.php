<?php
if(!class_exists('CoinPaymentsAPI')){
	class CoinPaymentsAPI {
		private $private_key = '';
		private $public_key = '';
		private $ch = null;
	
		function __construct($private_key, $public_key){
			$this->private_key = $private_key;
			$this->public_key = $public_key;
		}	
	
		public function create_adress($currency = '', $ipn_url='') {		
			return $this->api_call('get_callback_address', array('currency' => $currency, 'ipn_url' => $ipn_url));
		}		
	
		private function api_call($cmd, $req = array()) {
			
			$req['version'] = 1;
			$req['cmd'] = $cmd;
			$req['key'] = $this->public_key;
			$req['format'] = 'json'; 
			
			$post_data = http_build_query($req, '', '&');
			
			$hmac = hash_hmac('sha512', $post_data, $this->private_key);
			
			if ($this->ch === null) {
				$this->ch = curl_init('https://www.coinpayments.net/api.php');
				curl_setopt($this->ch, CURLOPT_FAILONERROR, TRUE);
				curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
			}
			curl_setopt($this->ch, CURLOPT_HTTPHEADER, array('HMAC: '.$hmac));
			curl_setopt($this->ch, CURLOPT_POSTFIELDS, $post_data);
			
			$data = curl_exec($this->ch);                
			if ($data !== FALSE) {
				if (PHP_INT_SIZE < 8 && version_compare(PHP_VERSION, '5.4.0') >= 0) {
					$dec = json_decode($data, TRUE, 512, JSON_BIGINT_AS_STRING);
				} else {
					$dec = json_decode($data, TRUE);
				}
				if ($dec !== NULL && count($dec)) {
					return $dec;
				} else {
					return array('error' => 'Unable to parse JSON result ('. json_last_error() .')');
				}
			} else {
				return array('error' => 'cURL error: '.curl_error($this->ch));
			}
		}
	}
}