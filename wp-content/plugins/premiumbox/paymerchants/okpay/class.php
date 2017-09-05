<?php
if(!class_exists('AP_OKPay')){
class AP_OKPay
{
    private $sAccount, $oSoapClient, $sSecurityToken;
	
    function __construct($sAccount, $sAPIKey)
    {
		
		try{
			
			$this->sAccount = trim( $sAccount );
			$this->oSoapClient = new SoapClient( 'https://api.okpay.com/OkPayAPI?wsdl' );
			$aExplode = explode( ':', str_replace( array( '-', ' ' ), array( '', ':' ), $this->oSoapClient->Get_Date_Time()->Get_Date_TimeResult ) );
			$this->sSecurityToken = strtoupper( hash('sha256', $sAPIKey.':'.$aExplode[0].':'.$aExplode[1] ) );			
			
		}
		catch (Exception $e)
		{
			
		}		
		
    }		
	
	public function getBalans() {
	
		/* EUR,USD,RUB */
	
		if($this->sSecurityToken){
			try{	
				$obj = new stdClass();
				$obj->WalletID = $this->sAccount;
				$obj->SecurityToken = $this->sSecurityToken;
				$webService1 = $this->oSoapClient->Wallet_Get_Balance($obj);
				$wsResult1 = $webService1->Wallet_Get_BalanceResult;
				if(is_object($wsResult1)){
					$purses = array();
					foreach($wsResult1->Balance as $val){
						$currency = trim(is_isset($val,'Currency'));
						$value = trim(is_isset($val,'Amount'));
						$purses[$currency] = $value;
					}
					return $purses;
				}
			}
			catch (Exception $e)
			{
					
			}
		}	
	   
	}
	
	public function SendMoney($currency, $receiver, $amount, $comment) {
		$data = array();
		$data['error'] = 1;		
		
		if($this->sSecurityToken){
			try{
				
				$obj = new stdClass();
				$obj->WalletID = $this->sAccount;
				$obj->SecurityToken = $this->sSecurityToken;
				$obj->Currency = $currency;
				$obj->Receiver = $receiver;
				$obj->Amount = $amount;
				$obj->Comment = $comment;
				$obj->IsReceiverPaysFees = FALSE; /* false - комиссию платите вы, true - клиент */
				$webService1 = $this->oSoapClient->Send_Money($obj);
				$wsResult1 = $webService1->Send_MoneyResult;
				if($wsResult1->Status == 'Completed'){
					$data['error'] = 0;
				}
				
			}
			catch (Exception $e)
			{
					
			}
		}	
		
		return $data;
	}	
	
}
}