<?php
if(!class_exists('PerfectMoney')){
class PerfectMoney {
    private $iAccountID, $sPassPhrase;
    
    # Конструктор, принимает id аккаунта и пароль.
    public function __construct( $iAccountID, $sPassPhrase ) {
        $this->iAccountID = intval( $iAccountID );
        $this->sPassPhrase = trim( $sPassPhrase );
    }
    
    public function getHistory( $sStartDate, $sEndDate, $key = 'batchid', $what='prihod') {
                 		 
		$date1 = explode('.', $sStartDate);
		$date2 = explode('.', $sEndDate);
        
		$sdata =  array( 
            'AccountID' => $this->iAccountID,
            'PassPhrase' => $this->sPassPhrase,
            'startday' => $date1[0] - 0,
            'startmonth' => $date1[1] - 0,
            'startyear' => $date1[2] - 0,
            'endday' => $date2[0] - 0,
            'endmonth' => $date2[1] - 0,
            'endyear' => $date2[2] - 0,
            // 'batchfilter' => $batch_id,
            // 'payment_id' => $payment_id			
        );
		if($what == 'prihod'){
			$sdata['paymentsreceived'] = true;
		} else {
			$sdata['paymentsmade'] = true;
		}
		
        $outs = explode("\n", $this->request('https://perfectmoney.is/acct/historycsv.asp',$sdata));
		
		$data = array();
		$data['error'] = 1;
        if(trim($outs[0]) == 'Time,Type,Batch,Currency,Amount,Fee,Payer Account,Payee Account,Payment ID,Memo'){
			$data['error'] = 0;
			foreach($outs as $res){
				$arr_data = explode(',',$res);
				if(count($arr_data) >= 9){
					if($key == 'batchid'){
						$now_key = $arr_data[2];
					} else {
						$now_key = $arr_data[8];
					}	
					$data['responce'][$now_key] = array(
						'date' => $arr_data[0],
						'type' => $arr_data[1],
						'batch' => $arr_data[2],
						'currency' => $arr_data[3],
						'amount' => $arr_data[4],
						'fee' => $arr_data[5],
						'receiver' => $arr_data[6],
						'sender' => $arr_data[7],
						'payment_id' => $arr_data[8],
					);
				}
			}
		} elseif(trim($outs[0]) == 'No Records Found.') {
			$data['error'] = 0;
			$data['responce'] = array();			
		} 	
		
		return $data;
    }
    
    # Метод отправки запроса и получения ответа.
    private static function request( $url, array $data = array() ) {
        
		$c_options = array(
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => http_build_query($data)
		);
		$result = get_curl_parser($url, $c_options, 'merchant', 'perfectmoney');
		$err  = $result['err'];
		$out = $result['output'];
		if(!$err){		
			return $out;		
		}		
		
    }
}
}