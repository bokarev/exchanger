<?php
/*
https://api.privatbank.ua/
*/
if(!class_exists('AP_PrivatBank')){
class AP_PrivatBank{
	private $merchant_id = "";
	private $password = "";
	private $test = 0;

    function __construct($merchant_id, $password)
    {
        $this->merchant_id = trim($merchant_id);
		$this->password = trim($password);
    }	
	
	public function check_order($order_id=0, $privat_id=0){
		
		if($order_id){
		
			$data = '<oper>cmt</oper><wait>0</wait><test>'. $this->test .'</test><payment><prop name="id" value="'. $order_id .'" /></payment>';
		
		} elseif($privat_id){
		
			$data = '<oper>cmt</oper><wait>0</wait><test>'. $this->test .'</test><payment><prop name="ref" value="'. $privat_id .'" /></payment>';
		
		}
		
		$request = $this->request('check_pay', $data);
		$res = @simplexml_load_string($request);
		$arr = array();
		$arr['error'] = 1; 
		$arr['status'] = 'not';
		if(is_object($res) and isset($res->data->payment->attributes()->status[0])){
			$status = (string)$res->data->payment->attributes()->status[0];
			if($status){
				$arr['error'] = 0;
				$arr['status'] = $status;
			}
		} 
		return $arr;
		
	}	
	
	public function make_order_visa($order_id, $to_card, $amount, $currency, $description, $fio){
		
		$data = '<oper>cmt</oper><wait>0</wait><test>'. $this->test .'</test><payment id="'. $order_id .'"><prop name="b_card_or_acc" value="'. $to_card .'" /><prop name="amt" value="'. $amount .'" /><prop name="ccy" value="'. $currency .'" /><prop name="b_name" value="'. $fio .'" /><prop name="details" value="'. $description .'" /></payment>';
		
		$request = $this->request('pay_visa', $data);
		$res = @simplexml_load_string($request);
		$arr = array();
		$arr['error'] = 1; 
		$arr['id'] = 0;
		if(is_object($res) and isset($res->data->payment->attributes()->ref[0]) and isset($res->data->payment->attributes()->state[0])){
			$state = (string)$res->data->payment->attributes()->state[0];
			$ref = (string)$res->data->payment->attributes()->ref[0];
			if($state == 1){
				$arr['error'] = 0;
				$arr['id'] = $ref;
			}
		} 
		return $arr;
		
	}	
	
	public function make_order($order_id, $to_card, $amount, $currency, $description){
		
		$data = '<oper>cmt</oper><wait>0</wait><test>'. $this->test .'</test><payment id="'. $order_id .'"><prop name="b_card_or_acc" value="'. $to_card .'" /><prop name="amt" value="'. $amount .'" /><prop name="ccy" value="'. $currency .'" /><prop name="details" value="'. $description .'" /></payment>';
		
		$request = $this->request('pay_pb', $data);
		$res = @simplexml_load_string($request);
		$arr = array();
		$arr['error'] = 1; 
		$arr['id'] = 0;
		if(is_object($res) and isset($res->data->payment->attributes()->ref[0]) and isset($res->data->payment->attributes()->state[0])){
			$state = (string)$res->data->payment->attributes()->state[0];
			$ref = (string)$res->data->payment->attributes()->ref[0];
			if($state == 1){
				$arr['error'] = 0;
				$arr['id'] = $ref;
			}
		} 
		return $arr;
		
	}	
	
	public function get_balans($card){
		
		$data = '<oper>cmt</oper><wait>0</wait><test>'. $this->test .'</test><payment id="1"><prop name="cardnum" value="'. $card .'" /><prop name="country" value="UA" /></payment>';
		
		$request = $this->request('balance', $data);
		$res = @simplexml_load_string($request);
		if(is_object($res) and isset($res->data->info->cardbalance->av_balance)){
			$purses = array();
			$purses[$card] = $res->data->info->cardbalance->av_balance;
			return $purses;
		} 
		/* или массив или пустота */
		
	}

	public function request($action, $data){

		$pass = $this->password;
		$sign=sha1(md5($data.$pass));
		
		$xml = '<?xml version="1.0" encoding="UTF-8"?>
		<request version="1.0">
		  <merchant>
			<id>'. $this->merchant_id .'</id>
			<signature>'. $sign .'</signature>
		  </merchant>
		  <data>'. $data .'</data>
		</request>
		';	
		
		$c_options = array(
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $xml,
		);
						
		$c_result = get_curl_parser('https://api.privatbank.ua/p24api/'.$action, $c_options, 'autopay', 'privat');
		$err  = $c_result['err'];
		$out = $c_result['output'];
		if(!$err){	

			if($this->test == 1){
				echo $out;
				exit;
			}
			
			return $out; 
		}				
	}
	
}
}