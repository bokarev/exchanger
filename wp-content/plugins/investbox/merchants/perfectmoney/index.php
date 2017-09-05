<?php 
if( !defined( 'ABSPATH')){ exit(); }

global $investbox;
$investbox->include_patch(__FILE__, 'dostup/index');
$investbox->include_patch(__FILE__, 'class');

if(!class_exists('InvestBox_perfectmoney_Merchant')){
	class InvestBox_perfectmoney_Merchant extends InvestBox_Merchant {

		function __construct()
		{
		
			$this->merch_name = 'perfectmoney';
			parent::__construct();
			
		}	
		
		public function invest_systems($systems){
			
			$systems['perfectmoney_usd'] = array('title'=>'PerfectMoney', 'valut'=>'USD');
			$systems['perfectmoney_eur'] = array('title'=>'PerfectMoney', 'valut'=>'EUR');	
			
			return $systems;
		}		
		
		public function pay_form_deposit($temp, $data){
			if($data->gid == 'perfectmoney_usd' or $data->gid == 'perfectmoney_eur'){
				if(defined('THE_PM_U_ACCOUNT') and defined('THE_PM_E_ACCOUNT') and defined('THE_PM_PAYEE_NAME')){
					 
					if($data->gid == 'perfectmoney_usd'){
						$PAYMENT_UNITS = 'USD';
						$PAYEE_ACCOUNT = THE_PM_U_ACCOUNT;
					} else {
						$PAYMENT_UNITS = 'EUR';
						$PAYEE_ACCOUNT = THE_PM_E_ACCOUNT;
					}

					$temp = '';
					$textpay = __('Payment of a request','inex') .' '. __('id','inex') .' '. $data->id .''; 
							
					$temp .= '
					<form name="MerchantPay" action="https://perfectmoney.is/api/step1.asp" method="post" target="_blank">
						<input name="SUGGESTED_MEMO" type="hidden" value="'. $textpay .'" />
						<input name="sEmail" type="hidden" value="'. is_email($data->user_email) .'" />
						<input name="PAYMENT_AMOUNT" type="hidden" value="'. pn_strip_text(round($data->insumm,2)) .'" />
						<input name="PAYEE_ACCOUNT" type="hidden" value="'. $PAYEE_ACCOUNT .'" />											
						<input type="hidden" name="PAYEE_NAME" value="'. THE_PM_PAYEE_NAME .'" />
						<input type="hidden" name="PAYMENT_UNITS" value="'. $PAYMENT_UNITS .'" />
						<input type="hidden" name="PAYMENT_ID" value="'. $data->id .'" />
						<input type="hidden" name="STATUS_URL" value="'. get_merchant_link('invest_'. $this->merch_name .'_status') .'" />
						<input type="hidden" name="PAYMENT_URL" value="'. get_merchant_link('invest_'. $this->merch_name .'_success') .'" />
						<input type="hidden" name="PAYMENT_URL_METHOD" value="POST" />
						<input type="hidden" name="NOPAYMENT_URL" value="'. get_merchant_link('invest_'. $this->merch_name .'_fail') .'" />
						<input type="hidden" name="NOPAYMENT_URL_METHOD" value="POST" />
						<input type="hidden" name="SUGGESTED_MEMO_NOCHANGE" value="1" />
						<input type="hidden" name="BAGGAGE_FIELDS" value="sEmail" />
						<input type="submit" value="'. __('Go to payment section','inex') .'" /> 
					</form>					
					';

				}
			}
			return $temp;				
		}	
		
		public function merchant_status(){
			global $wpdb;
	
			$sPayeeAccount = isset( $_POST['PAYEE_ACCOUNT'] ) ? trim( $_POST['PAYEE_ACCOUNT'] ) : null;
			$iPaymentID = isset( $_POST['PAYMENT_ID'] ) ? $_POST['PAYMENT_ID'] : null;
			$dPaymentAmount = isset( $_POST['PAYMENT_AMOUNT'] ) ? trim( $_POST['PAYMENT_AMOUNT'] ) : null;
			$sPaymentUnits = isset( $_POST['PAYMENT_UNITS'] ) ? trim( $_POST['PAYMENT_UNITS'] ) : null;
			$iPaymentBatch = isset( $_POST['PAYMENT_BATCH_NUM'] ) ? trim( $_POST['PAYMENT_BATCH_NUM'] ) : null;
			$sPayerAccount = isset( $_POST['PAYER_ACCOUNT'] ) ? trim( $_POST['PAYER_ACCOUNT'] ) : null;
			$sTimeStampGMT = isset( $_POST['TIMESTAMPGMT'] ) ? trim( $_POST['TIMESTAMPGMT'] ) : null;
			$sV2Hash = isset( $_POST['V2_HASH'] ) ? trim( $_POST['V2_HASH'] ) : null;

			if( !in_array( $sPaymentUnits, array( 'USD', 'EUR' ) ) ){
				pn_display_mess('The wrong type of currency');
			}

			if( $sV2Hash != strtoupper( md5( $iPaymentID.':'.$sPayeeAccount.':'.$dPaymentAmount.':'.$sPaymentUnits.':'.$iPaymentBatch.':'.$sPayerAccount.':'.strtoupper( md5( THE_PM_ALTERNATE_PHRASE ) ).':'.$sTimeStampGMT ) ) ){
				pn_display_mess( 'Incorrect control signature' );
			}

			if( $sPayeeAccount != constant( 'THE_PM_'.substr( $sPayeeAccount, 0, 1 ).'_ACCOUNT' ) ){
				pn_display_mess( 'Invalid account Seller' );
			}

			$theid = intval($iPaymentID);
			$this->payed_deposit($theid,$dPaymentAmount,$sPaymentUnits, __('Archived','inex'),__('Uncompleted','inex'));
		}		
		
	}    
}
new InvestBox_perfectmoney_Merchant();