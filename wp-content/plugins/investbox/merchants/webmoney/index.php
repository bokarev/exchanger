<?php 
if( !defined( 'ABSPATH')){ exit(); }

global $investbox;
$investbox->include_patch(__FILE__, 'dostup/index');
$investbox->include_patch(__FILE__, 'class');

if(!class_exists('InvestBox_webmoney_Merchant')){
	class InvestBox_webmoney_Merchant extends InvestBox_Merchant {

		function __construct()
		{
		
			$this->merch_name = 'webmoney';
			parent::__construct();
			
		}	
		
		public function invest_systems($systems){ 
			
			$systems['webmoney_usd'] = array('title'=>'Webmoney', 'valut'=>'USD');
			$systems['webmoney_rub'] = array('title'=>'Webmoney', 'valut'=>'RUB');			
			
			return $systems;
		}		
		
		public function pay_form_deposit($temp, $data){
			if($data->gid == 'webmoney_usd' and defined('THE_WEBMONEY_WMZ_PURSE') and defined('THE_WEBMONEY_WMZ_KEY') or $data->gid == 'webmoney_rub' and defined('THE_WEBMONEY_WMR_PURSE') and defined('THE_WEBMONEY_WMR_KEY')){
 
				if($data->gid == 'webmoney_usd'){
					$LMI_PAYEE_PURSE = THE_WEBMONEY_WMZ_PURSE;
				} else {
					$LMI_PAYEE_PURSE = THE_WEBMONEY_WMR_PURSE;
				}

				$temp = '';
				$textpay = __('Payment of a request','inex') .' '. __('id','inex') .' '. $data->id .''; 
						
				$temp .= '
					<form name="MerchantPay" action="https://merchant.webmoney.ru/lmi/payment.asp" method="post" accept-charset="windows-1251">
						<input type="hidden" name="LMI_RESULT_URL" value="'. get_merchant_link('invest_'. $this->merch_name .'_status') .'" />
						<input type="hidden" name="LMI_SUCCESS_URL" value="'. get_merchant_link('invest_'. $this->merch_name .'_success') .'" />
						<input type="hidden" name="LMI_SUCCESS_METHOD" value="POST" />
						<input type="hidden" name="LMI_FAIL_URL" value="'. get_merchant_link('invest_'. $this->merch_name .'_fail') .'" />
						<input type="hidden" name="LMI_FAIL_METHOD" value="POST" />			    
						<input name="LMI_PAYMENT_NO" type="hidden" value="'. $data->id .'" />
						<input name="LMI_PAYMENT_AMOUNT" type="hidden" value="'. pn_strip_text(round($data->insumm,2)) .'" />
						<input name="LMI_PAYEE_PURSE" type="hidden" value="'. $LMI_PAYEE_PURSE .'" />
						<input name="LMI_PAYMENT_DESC" type="hidden" value="'. $textpay .'" />
						<input name="sEmail" type="hidden" value="'. is_email($data->user_email) .'" />				

						<input type="submit" value="'. __('Go to payment section','inex') .'" /> 
					</form>							
				';

			}
			
			return $temp;		
		}	
		
		public function merchant_status(){
			global $wpdb;
	
			$dPaymentAmount = trim(is_param_post('LMI_PAYMENT_AMOUNT'));
			$iPaymentID = trim(is_param_post('LMI_PAYMENT_NO'));
			$bPaymentMode = trim(is_param_post('LMI_MODE'));
			$iPayerWMID = trim(is_param_post('LMI_PAYER_WM'));
			$sPayerPurse = trim(is_param_post('LMI_PAYER_PURSE'));
			$sEmail = trim(is_param_post('sEmail'));

			if( $bPaymentMode != 0 ) {
				die( 'NO TEST MODE' );
			}

			if( isset( $_POST['LMI_PREREQUEST'] ) ){
				die( 'YES' );
			}

			$iSysInvsID = trim(is_param_post('LMI_SYS_INVS_NO'));
			$iSysTransID = trim(is_param_post('LMI_SYS_TRANS_NO'));
			$sSignature = trim(is_param_post('LMI_HASH'));
			$sSysTransDate = trim(is_param_post('LMI_SYS_TRANS_DATE'));

			if( !defined( 'THE_WEBMONEY_WM'.substr( $sPayerPurse, 0, 1 ).'_PURSE' ) or !defined( 'THE_WEBMONEY_WM'.substr( $sPayerPurse, 0, 1 ).'_KEY' )){
				die( 'Error defined' );
			}			
			
			if( $sSignature != strtoupper( hash( 'sha256', implode(  '', array( constant( 'THE_WEBMONEY_WM'.substr( $sPayerPurse, 0, 1 ).'_PURSE' ), $dPaymentAmount, $iPaymentID, $bPaymentMode, $iSysInvsID, $iSysTransID, $sSysTransDate, constant( 'THE_WEBMONEY_WM'.substr( $sPayerPurse, 0, 1 ).'_KEY' ), $sPayerPurse, $iPayerWMID ) ) ) ) ) {
				die( 'Error control sign' );
			}

			$theid = intval($iPaymentID);
			$wmw = substr( $sPayerPurse, 0, 1 );
			if($wmw == 'Z'){
				$sPaymentUnits = 'USD';
			} elseif($wmw == 'R'){
				$sPaymentUnits = 'RUB';
			}

			$this->payed_deposit($theid,$dPaymentAmount,$sPaymentUnits,'OK','Bad');			
		}		
		
	}    
}
new InvestBox_webmoney_Merchant();