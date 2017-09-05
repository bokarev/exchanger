<?php
/*
title: [ru_RU:]Nixmoney[:ru_RU][en_US:]Nixmoney[:en_US]
description: [ru_RU:]мерчант Nixmoney[:ru_RU][en_US:]Nixmoney merchant[:en_US]
version: 1.2
*/

if(!class_exists('merchant_nixmoney')){
	class merchant_nixmoney extends Merchant_Premiumbox{
		function __construct($file, $title)
		{
			$map = array(
				'NIXMONEY_PASSWORD', 'NIXMONEY_ACCOUNT',
				'NIXMONEY_USD', 'NIXMONEY_EUR', 
				'NIXMONEY_BTC', 'NIXMONEY_LTC', 'NIXMONEY_PPC', 
				'NIXMONEY_FTC','NIXMONEY_CRT', 'NIXMONEY_GBC','NIXMONEY_DOGE',
			);
			parent::__construct($file, $map, $title);
			
			add_action('get_merchant_admin_options_'. $this->name, array($this, 'get_merchant_admin_options'), 10, 2);
			add_filter('merchants_settingtext_'.$this->name, array($this, 'merchants_settingtext'));
			add_filter('merchant_formstep_autocheck',array($this, 'merchant_formstep_autocheck'),1,2);
			add_filter('merchants_action_bid_'.$this->name, array($this,'merchants_action_bid'),99,4);
			add_action('myaction_merchant_'. $this->name .'_fail', array($this,'myaction_merchant_fail'));
			add_action('myaction_merchant_'. $this->name .'_success', array($this,'myaction_merchant_success'));
			add_action('myaction_merchant_'. $this->name .'_status' . get_hash_result_url($this->name), array($this,'myaction_merchant_status'));
		}
		
		function get_merchant_admin_options($options, $data){ 
			
			if(isset($options['check_payapi'])){
				unset($options['check_payapi']);
			}			
			
			$text = '
			<strong>FAIL url:</strong> <a href="'. get_merchant_link($this->name.'_fail') .'" target="_blank">'. get_merchant_link($this->name.'_fail') .'</a><br />
			<strong>STATUS url:</strong> <a href="'. get_merchant_link($this->name.'_status' . get_hash_result_url($this->name)) .'" target="_blank">'. get_merchant_link($this->name.'_status' . get_hash_result_url($this->name)) .'</a><br />
			<strong>SUCCESS url:</strong> <a href="'. get_merchant_link($this->name.'_success') .'" target="_blank">'. get_merchant_link($this->name.'_success') .'</a>
			';

			$noptions = array();
			foreach($options as $key => $val){
				if($key == 'bottom_title'){
					$noptions['text'] = array(
						'view' => 'textfield',
						'title' => '',
						'default' => $text,
					);					
				}
				$noptions[$key] = $val;
			}				
			
			return $noptions;	
		}		

		function merchants_settingtext(){
			$text = '| <span class="bred">'. __('Config file is not set up','pn') .'</span>';
			if(
				is_deffin($this->m_data,'NIXMONEY_PASSWORD') 
			){
				$text = '';
			}
			
			return $text;
		}	

		function merchant_formstep_autocheck($autocheck, $m_id){
			
			if($m_id and $m_id == $this->name){
				$autocheck = 1;
			}
			
			return $autocheck;
		}		

		function merchants_action_bid($temp, $pay_sum, $item, $naps){

			$vtype = pn_strip_input($item->vtype1);
			
			$PAYEE_ACCOUNT = 0;
					
				if($vtype == 'USD'){
					$PAYEE_ACCOUNT = is_deffin($this->m_data,'NIXMONEY_USD');
				} elseif($vtype == 'EUR'){
					$PAYEE_ACCOUNT = is_deffin($this->m_data,'NIXMONEY_EUR');
				} elseif($vtype == 'BTC'){
					$PAYEE_ACCOUNT = is_deffin($this->m_data,'NIXMONEY_BTC');
				} elseif($vtype == 'LTC'){
					$PAYEE_ACCOUNT = is_deffin($this->m_data,'NIXMONEY_LTC');
				} elseif($vtype == 'PPC'){
					$PAYEE_ACCOUNT = is_deffin($this->m_data,'NIXMONEY_PPC');
				} elseif($vtype == 'FTC'){
					$PAYEE_ACCOUNT = is_deffin($this->m_data,'NIXMONEY_FTC');	
				} elseif($vtype == 'CRT'){
					$PAYEE_ACCOUNT = is_deffin($this->m_data,'NIXMONEY_CRT');	
				} elseif($vtype == 'GBC'){
					$PAYEE_ACCOUNT = is_deffin($this->m_data,'NIXMONEY_GBC');
				} elseif($vtype == 'DOGE'){
					$PAYEE_ACCOUNT = is_deffin($this->m_data,'NIXMONEY_DOGE');					
				}		
				
				$pay_sum = is_my_money($pay_sum,2);				
				$text_pay = get_text_pay($this->name, $item, $pay_sum);
						
				$temp = '
				<form action="https://nixmoney.com/merchant.jsp" method="post" target="_blank">
					<input type="hidden" name="PAYEE_ACCOUNT" value="'. $PAYEE_ACCOUNT .'" />
					<input type="hidden" name="PAYEE_NAME" value="'. $text_pay .'" />
					<input type="hidden" name="PAYMENT_AMOUNT" value="'. $pay_sum .'" />
					<input type="hidden" name="PAYMENT_URL" value="'. get_merchant_link($this->name.'_success') .'" />
					<input type="hidden" name="NOPAYMENT_URL" value="'. get_merchant_link($this->name.'_fail') .'" />
					<input type="hidden" name="BAGGAGE_FIELDS" value="PAYMENT_ID" />
					<input type="hidden" name="PAYMENT_ID" value="'. $item->id .'" />
					<input type="hidden" name="STATUS_URL" value="'. get_merchant_link($this->name.'_status' . get_hash_result_url($this->name)) .'" />
					<input type="submit" value="'. __('Make a payment','pn') .'" />
				</form>													
				';				
			
			return $temp;		
		}

		function myaction_merchant_fail(){
	
			$id = get_payment_id('PAYMENT_ID');
			the_merchant_bid_delete($id);
	
		}

		function myaction_merchant_success(){
	
			$id = get_payment_id('PAYMENT_ID');
			the_merchant_bid_success($id);
	
		}

		function myaction_merchant_status(){
	
			do_action('merchant_logs', $this->name);
	
			if(!isset($_POST['PAYMENT_ID'])){
				die('no id');
			}
			if(!isset($_POST['V2_HASH'])){
				die('no hash');
			}

			$string= $_POST['PAYMENT_ID'].':'.$_POST['PAYEE_ACCOUNT'].':'.$_POST['PAYMENT_AMOUNT'].':'.$_POST['PAYMENT_UNITS'].':'.$_POST['PAYMENT_BATCH_NUM'].':'.$_POST['PAYER_ACCOUNT'].':'.strtoupper(md5(is_deffin($this->m_data,'NIXMONEY_PASSWORD'))).':'.$_POST['TIMESTAMPGMT'];
			 
			$v2key = $_POST['V2_HASH'];
			$hash=strtoupper(md5($string));
		  
			if($hash == $v2key){
				
				$iPaymentBatch = $_POST['PAYMENT_BATCH_NUM'];
				$iPaymentID = $_POST['PAYMENT_ID'];
				$dPaymentAmount = $_POST['PAYMENT_AMOUNT'];
				$sPayerAccount = $_POST['PAYER_ACCOUNT'];
				$sPaymentUnits = strtoupper($_POST['PAYMENT_UNITS']);
				$sPayeeAccount = $_POST['PAYEE_ACCOUNT'];
				
				$data = get_merch_data($this->name);
				$check_history = intval(is_isset($data, 'check_api'));
				$show_error = intval(is_isset($data, 'show_error'));
				if($check_history == 1){
				
					try {
						$class = new NixMoney( is_deffin($this->m_data,'NIXMONEY_ACCOUNT'), is_deffin($this->m_data,'NIXMONEY_PASSWORD') );
						$hres = $class->getHistory( date( 'd.m.Y', strtotime( '-2 day' ) ), date( 'd.m.Y', strtotime( '+2 day' ) ), 'batchid', 'prihod' );
						if($hres['error'] == 0){
							$histories = $hres['responce'];
							if(isset($histories[$iPaymentBatch])){
								$h = $histories[$iPaymentBatch];
								$sPayerAccount = trim($h['sender']); //счет плательщика
								$sPayeeAccount = trim($h['receiver']); //счет получателя
								$dPaymentAmount = trim($h['amount']); //сумма платежа
								$sPaymentUnits = trim($h['currency']); //валюта платежа (USD/EUR/OAU)	
							} else {
								die( 'Wrong pay' );
							}
						} else {
							die( 'Error history' );
						}
					}
					catch( Exception $e ) {
						if($show_error){
							die( 'Фатальная ошибка: '.$e->getMessage() );
						} else {
							die( 'Фатальная ошибка');
						}
					}		
				
				}				
				
				$id = $iPaymentID;
				$data = get_data_merchant_for_id($id);
				$in_summ = $dPaymentAmount;
				$in_summ = is_my_money($in_summ,2);
				$err = $data['err'];
				$status = $data['status'];
				$m_id = $data['m_id'];
				$pay_purse = is_pay_purse($sPayerAccount, $data, $m_id);
				$vtype = $data['vtype'];	
			
				$bid_sum = is_my_money($data['pay_sum'],2);
				$bid_sum = apply_filters('merchant_bid_sum', $bid_sum, $m_id);
			
				if($status == 'new'){ 
					if($err == 0){
						if($m_id and $m_id == $this->name){
							if($vtype == $sPaymentUnits){
								if($in_summ >= $bid_sum){		
					
									$params = array(
										'pay_purse' => $pay_purse,
										'sum' => $in_summ,
										'naschet' => $sPayeeAccount,
										'trans_in' => $iPaymentBatch,
									);
									the_merchant_bid_status('realpay', $id, 'user', 0, '', $params);					
		
									die( 'ok' );
								
								} else {
									die('The payment amount is less than the provisions');
								}
							} else {
								die('Wrong type of currency');
							}
						} else {
							die('At the direction of off merchant');
						}
					} else {
						die( 'The application does not exist or the wrong ID' );
					}
				} else {
					die( 'In the application the wrong status' );
				}
				
			}						
	
		}
		
	}
}

new merchant_nixmoney(__FILE__, 'Nixmoney');