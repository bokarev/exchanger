<?php
if(!class_exists('ExmoApi')){
class ExmoApi{
	
    private $key = "";
    private $secret="";
	private $test = 0;

    function __construct($key,$secret)
    {
        $this->key = trim($key);
        $this->secret = trim($secret);
    }	
	
	public function redeem_voucher($code){
		
		$request = $this->request('excode_load', array('code'=>$code));
		$res = @json_decode($request);
		if(is_object($res) and $res->result == 1){
			return $res;
		}
		/* или данные или пустота */
		
	}	
	
	public function request($api_name, $req = array()){ 
		
		$mt = explode(' ', microtime());
		$NONCE = $mt[1] . substr($mt[0], 2, 6);

		$url = "http://api.exmo.com/v1/$api_name";

		$req['nonce'] = $NONCE;

		$post_data = http_build_query($req, '', '&');

		$sign = hash_hmac('sha512', $post_data, $this->secret);

		$headers = array(
			'Sign: ' . $sign,
			'Key: ' . $this->key,
		);

		static $ch = null;
		
		$c_options = array(
			CURLOPT_POSTFIELDS => $post_data,
			CURLOPT_HTTPHEADER => $headers,
		);		
		$result = get_curl_parser($url, $c_options, 'merchant', 'exmo');
		
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