<?php
if(!class_exists('AP_BASEL')){
class AP_BASEL {
    private $username, $secret;
	private $test = 0;
    
    # Конструктор, принимает id аккаунта и пароль.
    public function __construct( $username, $secret ) {
        $this->username = trim( $username );
        $this->secret = trim( $secret );
    }
    
	public function get_balans() {
		
        $result = $this->request('accounts');
		$data = array();
		
		if(isset($result['data']) and is_array($result['data'])){
			foreach($result['data'] as $info){
				if(isset($info['id'])){
					$data[$info['id']] = $info['balance'];
				}
			}
		}	
		
		return $data;	
	
	}
	
	public function send_money($sender, $receiver, $amount, $currency) {
		
		$sender = trim($sender);
		$receiver = trim($receiver);
		$amount = trim($amount);
		$currency = trim($currency);
		
		$data = array();
		$data['error'] = 1;
		$data['trans_id'] = 0;
		
		$jsonData = array();
		$jsonData['amount'] = $amount;
		$jsonData['currency'] = $currency;
		$jsonData['fromId'] = $sender;
		$jsonData['toId'] = $receiver;
        $result = $this->request('transaction', $jsonData);			
		
		if(isset($result['data']) and isset($result['data']['transactionResult']) and $result['data']['transactionResult'] == 'OK'){		
			if(isset($result['data']['transactionId'])){
				$data['error'] = 0;
				$data['trans_id'] = $result['data']['transactionId'];
			}
		}
		
		return $data;
	}	
    
    # Метод отправки запроса и получения ответа.
    public function request( $method, array $data = array() ) {
        
		$jsonCommand['commandType'] = $method;
		$jsonData['userName'] = $this->username;
		if(is_array($data)){
			foreach($data as $k => $v){
				$jsonData[$k] = $v;
			}
		}
		$jsonCommand['signature']  ='';
		$jsonCommand['data'] = $jsonData;
		$jsonCommand['signature'] =  $this->generateSignJSON(json_encode($jsonCommand), $this->secret);
		$data_string = @json_encode($jsonCommand);

		$c_options = array(
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => $data_string,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'Content-Length: ' . strlen($data_string)
			)		
		);
						
		$c_result = get_curl_parser('https://basel3.is/rest/api_json', $c_options, 'autopay', 'basel');
		$err  = $c_result['err'];
		$out = $c_result['output'];
		if(!$err){		
			$jsonReply = @json_decode($out, true);
			return $jsonReply;		
		} elseif($this->test == 1){
			echo $err;
			exit;
		}
		
    }
	
	public function generateSignJSON($jsonStr, $secret) {
		$md5 = md5($this->extractDataFromAllJson($jsonStr) . $secret);
		$sign = sha1($md5);
		return $sign;
	}

	public function extractDataFromAllJson($json) {

		$dataPosition = strpos($json, "\"data\"");
		$jsonWithoutData = substr($json, $dataPosition + strlen("\"data\""));

		$s1Position = strpos($jsonWithoutData,'[');
		if ($s1Position !== false) {
			$jsonWithoutS1 = substr($jsonWithoutData, $s1Position + 2);
			$s2Position = strrchr($jsonWithoutS1, '}');
			$jsonWithoutS2 = substr($jsonWithoutS1, 0, $s2Position-1);
			$s3Position = strrchr($jsonWithoutS2, ']');
			$jsonWithoutS3 = substr($jsonWithoutS2,0, $s3Position-1);
			return $jsonWithoutS3;
		} else {
			$s1Position_ = strpos($jsonWithoutData, '{');
			$s3Position_ = strrchr($jsonWithoutData,'}');
			$ret = substr( $jsonWithoutData, $s1Position_ + 1, $s3Position_ - 2);
			return $ret;
		}

	} 
}
}