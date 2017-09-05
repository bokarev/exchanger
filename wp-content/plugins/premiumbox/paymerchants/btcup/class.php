<?php
/* 
https://btc-up.com/#/api/general
USD, BTC, EMC, ETH, LTC 
*/

if(!class_exists('AP_BTCUP')){
class AP_BTCUP
{
	private $test = 0;

    function __construct()
    {
		
    }	
	
 	public function make_voucher($amount, $currency, $expiryPeriod='', $key_coupon, $secret_coupon, $receiver=''){
		$data = array();
		$data['error'] = 1;
		$data['trans_id'] = 0;
		$data['coupon'] = 0;		
		
		$amount = sprintf("%0.8F",$amount);
		$amount = rtrim($amount,'0');
		$amount = rtrim($amount,'.');
		
		$currency = strtoupper(trim((string)$currency));
		$expiryPeriod = trim((string)$expiryPeriod);
		$receiver = trim((string)$receiver);
		
		$key_coupon = trim((string)$key_coupon);
		$secret_coupon = trim((string)$secret_coupon);
		
		$params = array();
		$params['currency'] = $currency;
		$params['amount'] = $amount;
		if($expiryPeriod > 0){
			$params['expiryPeriod'] = $expiryPeriod;
		}
		if($receiver){
			$params['receiver'] = $receiver;
		}		
		
		$res = $this->request('CreateCoupon', $params, $key_coupon, $secret_coupon);
		if(is_object($res) and isset($res->success, $res->result) and $res->success == 1){
			$data['error'] = 0;
			$data['trans_id'] = trim((string)$res->result->transID);
			$data['coupon'] = trim((string)$res->result->coupon);
		}		
		return $data;
	} 	
	
 	public function get_balans($currency, $key_info, $secret_info){
		
		$key_info = trim((string)$key_info);
		$secret_info = trim((string)$secret_info);
		
		$res = $this->request('balance', array(), $key_info, $secret_info);
			
		if(is_object($res) and isset($res->success, $res->result) and $res->success == 1){
				
			$purses = array();
				
			foreach($res->result as $key => $val){
				$currency = trim($key);
				$value = trim(is_isset($val,'available'));
				$purses[$currency] = $value;
			}
			
			return $purses;
		}
		
	}

 	public function get_transfer($amount, $currency, $receiver, $key_withdraw, $secret_withdraw, $description=''){
		$data = array();
		$data['error'] = 1;
		$data['trans_id'] = 0;
		
		$amount = sprintf("%0.8F",$amount);
		$amount = rtrim($amount,'0');
		$amount = rtrim($amount,'.');
		
		$currency = strtoupper(trim((string)$currency));
		
		$description = trim((string)$description);
		$receiver = trim((string)$receiver);
		
		$key_withdraw = trim((string)$key_withdraw);
		$secret_withdraw = trim((string)$secret_withdraw);		
		
		$params = array();
		$params['currency'] = $currency;
		$params['amount'] = $amount;
		$params['description'] = $description;
		$params['receiver'] = $receiver;
		
		$res = $this->request('transfer', $params, $key_withdraw, $secret_withdraw);

		if(is_object($res) and isset($res->success, $res->result) and $res->success == 1){
				
			$data['error'] = 0;
			$data['trans_id'] = intval($res->result->transactionId);
	
		}
			
		//stdClass Object ( [success] => 1 [result] => stdClass Object ( [transactionId] => 170 [amountSent] => 1.01 [funds] => stdClass Object ( [available] => 8.99 [blocked] => 0 ) ) )	
		
		return $data;
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