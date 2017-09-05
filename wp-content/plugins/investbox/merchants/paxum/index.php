<?php 
if( !defined( 'ABSPATH')){ exit(); }

global $investbox;
$investbox->include_patch(__FILE__, 'dostup/index');
$investbox->include_patch(__FILE__, 'class');

if(!class_exists('InvestBox_paxum_Merchant')){
	class InvestBox_paxum_Merchant extends InvestBox_Merchant {

		function __construct()
		{
			$this->merch_name = 'paxum';
			parent::__construct();
		}	
		
		public function invest_systems($systems){
			
			$systems['paxum_usd'] = array('title'=>'Paxum', 'valut'=>'USD');
			$systems['paxum_eur'] = array('title'=>'Paxum', 'valut'=>'EUR');	
			
			return $systems;
		}		
		
		public function pay_form_deposit($temp, $data){
			if($data->gid == 'paxum_usd' or $data->gid == 'paxum_eur'){
				if(defined('THE_PAXUM_EMAIL') and defined('THE_PAXUM_SECRET')){
					 
					if($data->gid == 'paxum_usd'){
						$PAYMENT_UNITS = 'USD';
						// $PAYEE_ACCOUNT = THE_PM_U_ACCOUNT;
					} else {
						$PAYMENT_UNITS = 'EUR';
						// $PAYEE_ACCOUNT = THE_PM_E_ACCOUNT;
					}

					$pay_sum = is_my_money($data->insumm,2);							
					$text_pay = __('Payment of a request','inex') .' '. __('id','inex') .' '. $data->id; 
					
					$temp = '
					<form name="changer_form" action="https://www.paxum.com/payment/phrame.php?action=displayProcessPaymentLogin" target="_blank" method="post">
						<input type="hidden" name="business_email" value="'. THE_PAXUM_EMAIL .'" />
						<input type="hidden" name="button_type_id" value="1" />
						<input type="hidden" name="item_id" value="'. $data->id .'" />
						<input type="hidden" name="item_name" value="'. $text_pay .'" />
						<input type="hidden" name="amount" value="'. $pay_sum .'" />
						<input type="hidden" name="currency" value="'. $PAYMENT_UNITS .'" />
						<input type="hidden" name="ask_shipping" value="1" />
						<input type="hidden" name="cancel_url" value="'. get_merchant_link('invest_'. $this->merch_name .'_fail') .'" />
						<input type="hidden" name="finish_url" value="'. get_merchant_link('invest_'. $this->merch_name .'_success') .'" />
						<input type="hidden" name="variables" value="notify_url='. get_merchant_link('invest_'. $this->merch_name .'_status') .'" />
						<input type="submit" value="'. __('Go to payment section','inex') .'" />
					</form>													
					';					

				}
			}
			return $temp;				
		}	
		
		public function merchant_status(){
			
			if(!isset($_POST['transaction_item_id']) or !isset($_POST['key'])){
				die( 'No id' );
			}		
			
			$rawPostedData = file_get_contents('php://input');

			$i = strpos($rawPostedData, "&key=");
			$fieldValuePairsData = substr($rawPostedData, 0, $i);

			$calculatedKey = md5($fieldValuePairsData . THE_PAXUM_SECRET);

			$isValid = $_POST["key"] == $calculatedKey ? true : false;

			if(!$isValid)
			{
				die("This is not a valid notification message");
			}

			/*
			TODO: Process notification here
			$_POST['transaction_item_id'] - номер заказа который прописывался в $OrderID в index.php
			$_POST['transaction_amount'] - сумма прихода 
			$_POST['transaction_currency'] - валюта прихода (USD,EUR..)
			$_POST['transaction_status'] - если все ок то вернет done.
			*/			
	
			$theid = intval($_POST['transaction_item_id']);
			$this->payed_deposit($theid, $_POST['transaction_amount'], $_POST['transaction_currency'], 'Completed',__('Uncompleted','inex'));
		}		
		
	}    
}
new InvestBox_paxum_Merchant();