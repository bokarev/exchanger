<?php 
if( !defined( 'ABSPATH')){ exit(); }

global $investbox;
$investbox->include_patch(__FILE__, 'dostup/index');
$investbox->include_patch(__FILE__, 'class');

if(!class_exists('InvestBox_okpay_Merchant')){
	class InvestBox_okpay_Merchant extends InvestBox_Merchant {

		function __construct()
		{
		
			$this->merch_name = 'okpay';
			parent::__construct();
			
		}	
		
		public function invest_systems($systems){
			
			$systems['okpay_usd'] = array('title'=>'Okpay', 'valut'=>'USD');
			$systems['okpay_rub'] = array('title'=>'Okpay', 'valut'=>'RUB');	
			
			return $systems;
		}		
		
		public function pay_form_deposit($temp, $data){
			if($data->gid == 'okpay_usd' or $data->gid == 'okpay_rub'){
				if(defined('THE_OKPAY_API_KEY') and defined('THE_OKPAY_ACCOUNT')){
					 
					$temp = '';
					$textpay = __('Payment of a request','inex') .' '. __('id','inex') .' '. $data->id .''; 
						
					$account = THE_OKPAY_ACCOUNT;
						
					if($data->gid == 'okpay_usd'){
						$vtype = 'USD';
					} else {
						$vtype = 'RUB';
					}
							
					$temp .= '
					<form action="https://www.okpay.com/process.html" method="post">
						<input name="ok_receiver" type="hidden" value="'. $account .'" />
						<input name="ok_fees" type="hidden" value="1" />
						<input name="ok_return_success" type="hidden" value="'. get_merchant_link('invest_'. $this->merch_name .'_success') .'" />
						<input name="ok_return_fail" type="hidden" value="'. get_merchant_link('invest_'. $this->merch_name .'_fail') .'" />
						<input name="ok_ipn" type="hidden" value="'. get_merchant_link('invest_'. $this->merch_name .'_status') .'" />								
						<input name="ok_invoice" type="hidden" value="'. $data->id .'" />
						<input name="ok_item_1_price" type="hidden" value="'. pn_strip_text(round($data->insumm,2)) .'" />
						<input name="ok_currency" type="hidden" value="'. $vtype .'" />
						<input name="ok_item_1_name" type="hidden" value="'. $textpay .'" />
						<input name="ok_payer_email" type="hidden" value="'. is_email($data->user_email) .'" />
						<input type="submit" value="'. __('Go to payment section','inex') .'" /> 
					</form>								
					';

				}
			}
			return $temp;		
		}	
		
		public function merchant_status(){
			global $wpdb;
	
			$iTransferID = intval( is_param_req( 'ok_txn_id' ) );
			if(!$iTransferID){
				die('Not id');
			}

			$oClass = new OKPay( THE_OKPAY_ACCOUNT, THE_OKPAY_API_KEY );
			$aTransfer = $oClass->searchTransfer( $iTransferID );

			if( !isset($aTransfer['Status']) or $aTransfer['Status'] != 'Completed'){
				die('Неверный статус платежа');
			}

			$iOrderID = $aTransfer['Invoice'] - 0;
			$dAmount = $aTransfer['Amount'] - 0;
			$sCurrency = $aTransfer['Currency'];

			if( $aTransfer['Receiver']['WalletID'] != THE_OKPAY_ACCOUNT ){
				die( 'Неверный счет получателя' );
			}

			$theid = intval($iOrderID);
			$dPaymentAmount = $dAmount;

			$this->payed_deposit($theid,$dPaymentAmount,$sCurrency,__('Archived','inex'),__('Uncompleted','inex'));
		}		
		
	}    
}
new InvestBox_okpay_Merchant();