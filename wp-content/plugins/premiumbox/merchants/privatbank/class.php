<?php
if(!class_exists('PrivatBankApi')){
	class PrivatBankApi {
		private $merchant_id = "";
		private $password = "";
		private $test = 0;

		function __construct($merchant_id, $password)
		{
			$this->merchant_id = trim($merchant_id);
			$this->password = trim($password);
		}	
		
		public function get_history($card){
			$data = '<oper>cmt</oper><wait>0</wait><test>'. $this->test .'</test><payment><prop name="sd" value="'. date('d.m.Y',strtotime('-3 days')) .'" /><prop name="ed" value="'. date('d.m.Y',strtotime('+1 day')) .'" /><prop name="card" value="'. $card .'" /></payment>';
			
			$request = $this->request('rest_fiz', $data);
			$res = @simplexml_load_string($request);
			$data = array();
			if(is_object($res) and isset($res->data->info->statements->statement)){
				foreach($res->data->info->statements->statement as $val){
					$terminal = (string)$val['description']; //terminal
					if(preg_match('/\((.*?)\)/s', $terminal, $item)){
						$site_id = trim(preg_replace("/[^0-9]/", '', $item[1]));
						$amount = (string)$val['amount'];
						$data[$site_id]['amount'] = trim(preg_replace("/[^0-9.]/", '', $amount));
						$data[$site_id]['currency'] = trim(preg_replace("/[^A-Z]/", '', $amount));
					}			
				}
			}		
			
			return $data;		
		}	

		public function request($action, $data){

			$pass = $this->password;
			$sign=sha1(md5($data.$pass));
			
			$xml = '<?xml version="1.0" encoding="UTF-8"?><request version="1.0"><merchant><id>'. $this->merchant_id .'</id><signature>'. $sign .'</signature></merchant><data>'. $data .'</data></request>';	
			
			$c_options = array(
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => $xml,
				CURLOPT_HTTPHEADER => array( 'Content-Type: text/xml' )
			);			
			$result = get_curl_parser('https://api.privatbank.ua/p24api/'.$action, $c_options, 'merchant', 'privatbank');
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