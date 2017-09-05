<?php 
if( !defined( 'ABSPATH')){ exit(); }

global $investbox;
$investbox->include_patch(__FILE__, 'dostup/index');
$investbox->include_patch(__FILE__, 'class');

if(!class_exists('InvestBox_payeer_Merchant')){
	class InvestBox_payeer_Merchant extends InvestBox_Merchant {

		function __construct()
		{
		
			$this->merch_name = 'payeer';
			parent::__construct();
			
		}	
		
		public function invest_systems($systems){
			
			$systems['payeer_usd'] = array('title'=>'Payeer', 'valut'=>'USD');
			$systems['payeer_rub'] = array('title'=>'Payeer', 'valut'=>'RUB');			
			
			return $systems;
		}		
		
		public function pay_form_deposit($temp, $data){
			global $investbox;
			if($data->gid == 'payeer_usd' or $data->gid == 'payeer_rub'){
				if(defined('THE_PAYEER_SEKRET_KEY') and defined('THE_PAYEER_SHOP_ID')){
					 
					$temp = '';
					$textpay = __('Payment of a request','inex') .' '. __('id','inex') .' '. $data->id .''; 
							
					$paysumm = $investbox->alter_summ($data->insumm, '0.95');		
					
					$vtype = $data->gvalut;
					
					$m_desc = base64_encode($textpay);
					$m_amount = round($paysumm,2);
					$m_amount = number_format($m_amount, 2, '.', '');
					$arHash = array(
						THE_PAYEER_SHOP_ID,
						$data->id,
						$m_amount,
						$vtype,
						$m_desc,
						THE_PAYEER_SEKRET_KEY
					);
					$sign = strtoupper(hash('sha256', implode(":", $arHash)));
						
					$temp .= '
					<form method="GET" action="//payeer.com/api/merchant/m.php" target="_blank">
						<input type="hidden" name="m_shop" value="'. THE_PAYEER_SHOP_ID .'">
						<input type="hidden" name="m_orderid" value="'. $data->id .'">
						<input type="hidden" name="m_amount" value="'. round($paysumm,2) .'">
						<input type="hidden" name="m_curr" value="'. $vtype .'">
						<input type="hidden" name="m_desc" value="'. $m_desc .'">
						<input type="hidden" name="m_sign" value="'. $sign .'">
						<input type="submit" value="'. __('Go to payment section','inex') .'" /> 
					</form>						
					';
			
				}
			}
			
			return $temp;		
		}	
		
		public function merchant_status(){
			global $user_ID, $wpdb;
	
			if (isset($_POST["m_operation_id"]) && isset($_POST["m_sign"])){
				
				$m_key = THE_PAYEER_SEKRET_KEY;
				$arHash = array($_POST['m_operation_id'],
						$_POST['m_operation_ps'],
						$_POST['m_operation_date'],
						$_POST['m_operation_pay_date'],
						$_POST['m_shop'],
						$_POST['m_orderid'],
						$_POST['m_amount'],
						$_POST['m_curr'],
						$_POST['m_desc'],
						$_POST['m_status'],
						$m_key);
						
				$sign_hash = strtoupper(hash('sha256', implode(":", $arHash)));
				if ($_POST["m_sign"] == $sign_hash && $_POST['m_status'] == "success"){
			
					$theid = intval($_POST['m_orderid']);
					$dPaymentAmount = esc_sql($_POST['m_amount']);
					$sCurrency = $_POST['m_curr'];
					$this->payed_deposit($theid,$dPaymentAmount,$sCurrency, $theid."|success" , $theid."|error");	 	

				} 
				
				echo $_POST['m_orderid']."|error";
			}
			
		}		
		
	}    
}
new InvestBox_payeer_Merchant();