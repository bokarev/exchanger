<?php 
if( !defined( 'ABSPATH')){ exit(); }

global $investbox;
$investbox->include_patch(__FILE__, 'dostup/index');
$investbox->include_patch(__FILE__, 'class');

if(!class_exists('InvestBox_nixmoney_Merchant')){
	class InvestBox_nixmoney_Merchant extends InvestBox_Merchant {

		function __construct()
		{
		
			$this->merch_name = 'nixmoney';
			parent::__construct();
			
		}	
		
		public function invest_systems($systems){
			
			$systems['nixmoney_usd'] = array('title'=>'Nixmoney', 'valut'=>'USD');
			$systems['nixmoney_eur'] = array('title'=>'Nixmoney', 'valut'=>'EUR');	
			
			return $systems;
		}		
		
		public function pay_form_deposit($temp, $data){
			if($data->gid == 'nixmoney_usd' or $data->gid == 'nixmoney_eur'){
				if(defined('THE_NIXMONEY_PASSWORD') and defined('THE_NIXMONEY_ACCOUNT')){
					 
					if($data->gid == 'nixmoney_usd'){
						$PAYMENT_UNITS = 'USD';
						$PAYEE_ACCOUNT = THE_NIXMONEY_USD;
					} else {
						$PAYMENT_UNITS = 'EUR';
						$PAYEE_ACCOUNT = THE_NIXMONEY_EUR;
					}					 
					 
					$pay_sum = is_my_money($data->insumm,2);				
					$text_pay = __('Payment of a request','inex') .' '. __('id','inex') .' '. $data->id;
						
					$temp = '
					<form action="https://nixmoney.com/merchant.jsp" method="post" target="_blank">
						<input type="hidden" name="PAYEE_ACCOUNT" value="'. $PAYEE_ACCOUNT .'" />
						<input type="hidden" name="PAYEE_NAME" value="'. $text_pay .'" />
						<input type="hidden" name="PAYMENT_AMOUNT" value="'. $pay_sum .'" />
						<input type="hidden" name="PAYMENT_URL" value="'. get_merchant_link('invest_'. $this->merch_name .'_success') .'" />
						<input type="hidden" name="NOPAYMENT_URL" value="'. get_merchant_link('invest_'. $this->merch_name .'_fail') .'" />
						<input type="hidden" name="BAGGAGE_FIELDS" value="PAYMENT_ID" />
						<input type="hidden" name="PAYMENT_ID" value="'. $data->id .'" />
						<input type="hidden" name="STATUS_URL" value="'. get_merchant_link('invest_'. $this->merch_name .'_status') .'" />
						<input type="submit" value="'. __('Go to payment section','inex') .'" />
					</form>													
					';					 
					
				}
			}
			return $temp;				
		}	
		
		public function merchant_status(){
			global $wpdb;
	
			if(!isset($_POST['PAYMENT_ID'])){
				die('no id');
			}
			if(!isset($_POST['V2_HASH'])){
				die('no hash');
			}

			$string = $_POST['PAYMENT_ID'].':'.$_POST['PAYEE_ACCOUNT'].':'.$_POST['PAYMENT_AMOUNT'].':'.$_POST['PAYMENT_UNITS'].':'.$_POST['PAYMENT_BATCH_NUM'].':'.$_POST['PAYER_ACCOUNT'].':'.strtoupper(md5(THE_NIXMONEY_PASSWORD)).':'.$_POST['TIMESTAMPGMT'];
	
			$v2key = $_POST['V2_HASH'];
			$hash = strtoupper(md5($string));

			$sPayeeAccount = isset( $_POST['PAYEE_ACCOUNT'] ) ? trim( $_POST['PAYEE_ACCOUNT'] ) : null;
			$iPaymentID = isset( $_POST['PAYMENT_ID'] ) ? $_POST['PAYMENT_ID'] : null;
			$dPaymentAmount = isset( $_POST['PAYMENT_AMOUNT'] ) ? trim( $_POST['PAYMENT_AMOUNT'] ) : null;
			$sPaymentUnits = isset( $_POST['PAYMENT_UNITS'] ) ? trim( $_POST['PAYMENT_UNITS'] ) : null;
			$iPaymentBatch = isset( $_POST['PAYMENT_BATCH_NUM'] ) ? trim( $_POST['PAYMENT_BATCH_NUM'] ) : null;
			$sPayerAccount = isset( $_POST['PAYER_ACCOUNT'] ) ? trim( $_POST['PAYER_ACCOUNT'] ) : null;

			if( !in_array( $sPaymentUnits, array( 'USD', 'EUR' ) ) ){
				pn_display_mess('The wrong type of currency');
			}

			if($hash != $v2key){
				pn_display_mess( 'Incorrect control signature' );
			}

			if( $sPayeeAccount != constant( 'THE_NIXMONEY_'.substr( $sPayeeAccount, 0, 1 ) ) ){
				pn_display_mess( 'Invalid account Seller' );
			}

			$theid = intval($iPaymentID);
			$this->payed_deposit($theid,$dPaymentAmount,$sPaymentUnits, 'ok',__('Uncompleted','inex'));
		}		
		
	}    
}
new InvestBox_nixmoney_Merchant();