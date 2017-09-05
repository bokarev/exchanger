<?php

if(!class_exists('OKPay')){
class OKPay
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
	
	public function searchTransfer( $iTransferID ) {
			
		if($this->sSecurityToken){	
			
			try{
				
				$obj = new stdClass();
				$obj->WalletID = $this->sAccount;
				$obj->SecurityToken = $this->sSecurityToken;
				$obj->TxnID = $iTransferID;
				$webService3 = $this->oSoapClient->Transaction_Get($obj)->Transaction_GetResult;
				return json_decode( json_encode( $webService3 ), true );
				
			}
			catch (Exception $e)
			{
					
			}
		} 
			
	}
	
}
}