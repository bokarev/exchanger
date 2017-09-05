<?php
if(!class_exists('LiveCoin')){
class LiveCoin
{
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
	
	public function redeem_voucher($code){
		
		$request = $this->request('payment/voucher/redeem', array('voucher_code'=>$code));
		$res = @json_decode($request);
		if(is_object($res)){
			return $res;
		}
		/* или данные или пустота */
		
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
			$c_options[CURLOPT_POST] = 'POST';
			$c_options[CURLOPT_POSTFIELDS] = $postFields;
		}		
		
		$result = get_curl_parser('https://api.livecoin.net/'.$url, $c_options, 'merchant', 'livecoin');
		
		$err  = $result['err'];
		$out = $result['output'];
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