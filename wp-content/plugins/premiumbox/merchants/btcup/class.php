<?php
/* 
https://btc-up.com/#/api/general
USD, BTC, EMC, ETH, LTC 
*/

if(!class_exists('BTCUP')){
class BTCUP
{
	private $test = 0;

    function __construct()
    {

    }	
	
 	public function redeem_voucher($coupon, $key_coupon, $secret_coupon){
		$coupon = trim((string)$coupon);
		$key_coupon = trim((string)$key_coupon);
		$secret_coupon = trim((string)$secret_coupon);
		
		$params = array();
		$params['coupon'] = $coupon;	
		
		$res = $this->request('RedeemCoupon', $params, $key_coupon, $secret_coupon);
		
		if(is_object($res) and isset($res->success, $res->result) and $res->success == 1){
			return $res->result;
			//tdClass Object ( [couponAmount] => 1.01 [couponCurrency] => USD [transID] => 168 [funds] => stdClass Object ( [available] => 8.9 [blocked] => 0 ) ) 
		}		
	}

	public function request($method, $params = array(), $api_key='', $secret=''){
		$params = (array)$params;
		$params['method'] = trim((string)$method);
		$params['nonce'] = $nonce = time();
		
		$post_data = http_build_query($params, '', '&');
		
		$api_key = trim((string)$api_key);
		$secret = trim((string)$secret);		
		$sign = hash_hmac("sha512", $post_data, $secret);
		
        $headers = array(
            'Sign: '. $sign,
            'Key: '. $api_key,
        );
		
		if($ch = curl_init()){
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_URL, 'https://tapi.btc-up.com');
			curl_setopt($ch, CURLOPT_POST, 'POST');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);			
			$err  = curl_errno($ch);
			$res = curl_exec($ch);
			curl_close($ch);
			if(!$err){
						
				if($this->test == 1){
					echo $res;
					exit;
				}
				
				$result = @json_decode($res);	
				return $result;
					
			} elseif($this->test == 1){
				echo $err;
				exit;
			}
		}		
		
	}	
}
}