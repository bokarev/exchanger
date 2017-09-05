<?php
/* 
https://www.livecoin.net/api/vouchers
*/

if(!class_exists('AP_LiveCoin')){
class AP_LiveCoin {
    private $key = "";
    private $secret="";
	private $test = 0;

    function __construct($key,$secret)
    {
        $this->key = trim($key);
        $this->secret = trim($secret);
    }	
	
	/*
	stdClass Object
	(
		[errorMessage] => Signature verification failed
		[errorCode] => 20
	)
	*/	
	
	public function make_voucher($amount, $currency, $description=''){
		
		/*
		USD EUR RUR BTC LTC EMC DASH DOGE MONA PPC NMC CURE ETH
		*/
		$amount = sprintf("%0.8F",$amount);
		$amount = rtrim($amount,'0');
		$amount = rtrim($amount,'.');
		
		$currency = trim((string)$currency);

		$description = (string)$description;
		
		$res = $this->request('payment/voucher/make', array('amount'=>$amount, 'currency' => $currency, 'description'=> $description));
		if(is_string($res) and strstr($res, 'LVC-')){ 
			return trim((string)$res);
		}
		/* или купон или пустота */
		
	}	
	
	public function amount_voucher($code){
		
		$res = $this->request('payment/voucher/amount', array('voucher_code'=>$code));
		if(is_string($res)){
			return trim((string)$res);
		}
		/* или сумма или пустота */
		
	}

	public function get_balans(){
		
		$request = $this->request('payment/balances', array(), 'get');
		$res = @json_decode($request);
		if(is_array($res)){
			$purses = array();
			foreach($res as $val){
				$type = trim(is_isset($val,'type'));
				$currency = trim(is_isset($val,'currency'));
				$value = trim(is_isset($val,'value'));
				if($type == 'available'){
					$purses[$currency] = $value;
				}
			}
			return $purses;
		}
		/* или массив или пустота */
		
	}

	public function get_transfer($method, $amount, $currency, $params=array()){		
		
		$currency = strtoupper(trim((string)$currency));
		if(!is_array($params)){ $params = array(); }
		
		$data_params = array(
			'amount' => $amount,
			'currency' => $currency,
		);
		foreach($params as $k => $v){
			$data_params[$k] = $v;
		}
		
		$request = $this->request('payment/out/'.$method, $data_params);
		$res = @json_decode($request);
		if(is_object($res)){
			return $res;
		}		
		
	}	
	
	public function request($url, $params = array(), $method='post'){
		$params = (array)$params;
		$url = trim((string)$url);
		
		ksort($params);
		$postFields = http_build_query($params, '', '&');
		$signature = strtoupper(hash_hmac('sha256', $postFields, $this->secret));
	 
		$headers = array(
			"Api-Key: ". $this->key,
			"Sign: $signature"
		);
		
		$c_options = array(
			CURLOPT_HTTPHEADER => $headers,
		);
		if($method == 'post'){
			$c_options[CURLOPT_POST] = true;
			$c_options[CURLOPT_POSTFIELDS] = $postFields;
		}	
		
						
		$c_result = get_curl_parser('https://api.livecoin.net/'.$url, $c_options, 'autopay', 'livecoin');
		$err  = $c_result['err'];
		$out = $c_result['output'];	
		if(!$err){
					
			if($this->test == 1){
				echo $out;
				exit;
			}					
					
			return $out;
					
		} elseif($this->test == 1){
			echo $err;
			exit;
		}		
		
	}
}    
}
