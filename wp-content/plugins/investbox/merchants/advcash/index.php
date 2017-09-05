<?php 
if( !defined( 'ABSPATH')){ exit(); }

global $investbox;
$investbox->include_patch(__FILE__, 'dostup/index');
$investbox->include_patch(__FILE__, 'class');

if(!class_exists('InvestBox_advcash_Merchant')){
	class InvestBox_advcash_Merchant extends InvestBox_Merchant {

		function __construct()
		{
		
			$this->merch_name = 'advcash';
			parent::__construct();
			
		}	
		
		public function invest_systems($systems){
			
			$systems['advcash_usd'] = array('title'=>'AdvCash', 'valut'=>'USD');
			$systems['advcash_rub'] = array('title'=>'AdvCash', 'valut'=>'RUR');	
			
			return $systems;
		}		
		
		public function pay_form_deposit($temp, $data){
			if($data->gid == 'advcash_usd' or $data->gid == 'advcash_rub'){
				if(defined('THE_ADVCASH_ACCOUNT_EMAIL') and defined('THE_ADVCASH_SCI_NAME') and defined('THE_ADVCASH_SCI_SECRET')){
					 
					$amount = is_my_money($data->insumm,2);
					$text_pay = __('Payment of a request','inex') .' '. __('id','inex') .' '. $data->id;	
			
					if($data->gid == 'advcash_usd'){
						$currency = 'USD';
					} else {
						$currency = 'RUR';
					}
					$orderId = $data->id;
					$ac_account_email = THE_ADVCASH_ACCOUNT_EMAIL;
					$ac_sci_name = THE_ADVCASH_SCI_NAME;
					$sign = hash('sha256', $ac_account_email . ":" . $ac_sci_name . ":" . $amount . ":" . $currency . ":" . THE_ADVCASH_SCI_SECRET . ":" . $orderId);
									
					$temp = '
					<form name="MerchantPay" action="https://wallet.advcash.com/sci/" method="post" target="_blank">
						<input type="hidden" name="ac_account_email" value="'. $ac_account_email .'" /> 
						<input type="hidden" name="ac_sci_name" value="'. $ac_sci_name .'" />  
						<input type="hidden" name="ac_order_id" value="'. $orderId .'" /> 
						<input type="hidden" name="ac_sign" value="'. $sign .'" />			
								
						<input type="hidden" name="ac_amount" value="'. $amount .'" />
						<input type="hidden" name="ac_currency" value="'. $currency .'" />
						<input type="hidden" name="ac_comments" value="'. $text_pay .'" />
								
						<input type="submit" value="'. __('Go to payment section','inex') .'" />
					</form>												
					';					 

				}
			}
			return $temp;				
		}	
		
		public function merchant_status(){
			global $wpdb;
	
			$transactionId = is_param_req('ac_transfer');
			$paymentDate = is_param_req('ac_start_date');
			$sciName = is_param_req('ac_sci_name');
			$payer = is_param_req('ac_src_wallet');
			$destWallet = is_param_req('ac_dest_wallet');
			$orderId = is_param_req('ac_order_id');
			$amount = is_param_req('ac_amount');
			$currency = is_param_req('ac_merchant_currency');
			$hash = is_param_req('ac_hash'); 
			$pay_status = is_param_req('ac_transaction_status');
			
			if( $hash != strtolower( hash('sha256', $transactionId.':'.$paymentDate.':'.$sciName.':'.$payer.':'.$destWallet.':'.$orderId.':'.$amount.':'.$currency.':'. THE_ADVCASH_SCI_SECRET ) ) ){
				die( 'Неверная контрольная подпись' );	
			}	
			
			if($pay_status != 'COMPLETED'){
				die( 'Неверный статус' );	
			}
			
			$theid = intval($orderId);
			$this->payed_deposit($theid,$amount,$currency, 'Completed','Uncompleted');
		}		
		
	}    
}
new InvestBox_advcash_Merchant();