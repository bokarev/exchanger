<?php
if(!class_exists('AP_NixMoney')){
class AP_NixMoney {
    private $accountid, $passphrase;
    
    # Конструктор, принимает id аккаунта и пароль.
    public function __construct( $accountid, $passphrase ) {
        $this->accountid = trim( $accountid );
        $this->passphrase = trim( $passphrase );
    }
    
	public function getBalans() {
		
        $out = $this->request( 
            'https://www.nixmoney.com/balance', 
            array( 
                'ACCOUNTID' => $this->accountid,
                'PASSPHRASE' => $this->passphrase,
            ) 
        );
		
		$data = array();
		
		if(preg_match_all("/<input name='(.*)' type='hidden' value='(.*)'>/", $out, $result, PREG_SET_ORDER)){
			
			foreach($result as $val){
				$data[$val[1]] = $val[2];
			}
			
		}	
		
		return $data;	
	
	}
	
    public function getHistory( $sStartDate, $sEndDate, $key = 'batchid', $what='prihod') {
        
        $date1 = explode('.', $sStartDate);
		$date2 = explode('.', $sEndDate);
        
 		$sdata =  array( 
            'ACCOUNTID' => $this->accountid,
            'PASSPHRASE' => $this->passphrase,
            'STARTDAY' => $date1[0] - 0,
            'STARTMONTH' => $date1[1] - 0,
            'STARTYEAR' => $date1[2] - 0,
            'ENDDAY' => $date2[0] - 0,
            'ENDMONTH' => $date2[1] - 0,
            'ENDYEAR' => $date2[2] - 0,
            // 'BATCHFILTER' => $batch_id,
            // 'PAYMENT_ID' => $payment_id			
        );
		if($what == 'prihod'){
			$sdata['PAYMENTSRECEIVED'] = true;
		} else {
			$sdata['PAYMENTSMADE'] = true;
		}
		
        $outs = explode("\n", $this->request('https://www.nixmoney.com/history',$sdata));
		
		$data = array();
		$data['error'] = 1;
        if(trim($outs[0]) == 'Time,Type,Batch,Currency,Amount,Fee,Payer Account,Payee Account,Payment ID,Memo'){
			$data['error'] = 0;
			foreach($outs as $res){
				$res = trim($res);
				$arr_data = explode(',',$res);
				if(count($arr_data) >= 9){
					if($key == 'batchid'){
						$now_key = (int)$arr_data[0];
					} else {
						$now_key = (int)$arr_data[8];
					}	
					$data['responce'][$now_key] = array(
						'date' => $arr_data[1],
						'type' => $arr_data[2],
						'batch' => $arr_data[0],
						'currency' => $arr_data[3],
						'amount' => $arr_data[4],
						'fee' => $arr_data[5],
						'receiver' => $arr_data[7],
						'sender' => $arr_data[6],
						'payment_id' => intval($arr_data[8]),
					);
				}
			}
		} elseif(trim($outs[0]) == 'No Records Found.') {
			$data['error'] = 0;
			$data['responce'] = array();
		}
		
		return $data;
    }	
	
	public function SendMoney($sender, $receiver, $amount, $item_id, $comment) {
		$data = array();
		$data['error'] = 1;	
		$data['trans_id'] = 0;	
		
        $out = $this->request( 
            'https://www.nixmoney.com/send', 
            array( 
                'PASSPHRASE' => $this->passphrase,
				'PAYER_ACCOUNT' => $sender,
				'PAYEE_ACCOUNT' => $receiver,
				'AMOUNT' => $amount,
				'PAYMENT_ID' => $item_id,
				'MEMO' => $comment,		
            ) 
        );	
		
		if(preg_match_all("/<input name='(.*)' type='hidden' value='(.*)'>/", $out, $result, PREG_SET_ORDER)){
			$ndata = array();
			foreach($result as $val){
				$ndata[$val[1]] = $val[2];
			}		
			if(isset($ndata['PAYMENT_AMOUNT'], $ndata['PAYMENT_BATCH_NUM'])){
				$data['error'] = 0;
				$data['trans_id'] = $ndata['PAYMENT_BATCH_NUM'];
			}
		}
		
		return $data;
	}	
    
    # Метод отправки запроса и получения ответа.
    private static function request( $url, array $data = array() ) {
        
		$c_options = array(
			CURLOPT_POST => true,					
			CURLOPT_POSTFIELDS => http_build_query($data),					
		);				
		$c_result = get_curl_parser($url, $c_options, 'autopay', 'nixmoney');
		$err  = $c_result['err'];
		$out = $c_result['output'];
		if(!$err){	
			return $out;
		}		
		
    }
}
}
