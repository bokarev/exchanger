<?php
/*
title: [ru_RU:]Perfect Money[:ru_RU][en_US:]Perfect Money[:en_US]
description: [ru_RU:]мерчант Perfect Money[:ru_RU][en_US:]Perfect Money merchant[:en_US]
version: 1.2
*/

if(!class_exists('merchant_perfectmoney')){
	class merchant_perfectmoney extends Merchant_Premiumbox {

		function __construct($file, $title)
		{
			$map = array(
				'PM_ACCOUNT_ID', 'PM_PHRASE', 'PM_U_ACCOUNT', 
				'PM_E_ACCOUNT', 'PM_G_ACCOUNT', 'PM_PAYEE_NAME',
				'PM_ALTERNATE_PHRASE',
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
			
			if(isset($options['bottom_title'])){
				unset($options['bottom_title']);
			}
			if(isset($options['check_payapi'])){
				unset($options['check_payapi']);
			}				

			$options['paymethod'] = array(
				'view' => 'select',
				'title' => __('Payment method','pn'),
				'options' => array('0'=>__('All','pn'), '1'=>__('Account','pn'), '2'=>__('E-Voucher','pn'), '3'=>__('SMS','pn'), '4'=>__('Wire','pn')),
				'default' => is_isset($data, 'paymethod'),
				'name' => 'paymethod',
				'work' => 'int',
			);			
			
			$text = '
			<strong>RETURN URL:</strong> <a href="'. get_merchant_link($this->name.'_status' . get_hash_result_url($this->name)) .'" target="_blank">'. get_merchant_link($this->name.'_status' . get_hash_result_url($this->name)) .'</a><br />
			<strong>SUCCESS URL:</strong> <a href="'. get_merchant_link($this->name.'_success') .'" target="_blank">'. get_merchant_link($this->name.'_success') .'</a><br />
			<strong>FAIL URL:</strong> <a href="'. get_merchant_link($this->name.'_fail') .'" target="_blank">'. get_merchant_link($this->name.'_fail') .'</a>				
			';

			$options['text'] = array(
				'view' => 'textfield',
				'title' => '',
				'default' => $text,
			);
			$options['bottom_title'] = array(
				'view' => 'h3',
				'title' => '',
				'submit' => __('Save','pn'),
				'colspan' => 2,
			);			
			
			return $options;	
		}							
		
		function merchants_settingtext(){
			$text = '| <span class="bred">'. __('Config file is not set up','pn') .'</span>';
			if(
				is_deffin($this->m_data,'PM_U_ACCOUNT') 
				or is_deffin($this->m_data,'PM_E_ACCOUNT') 
				or is_deffin($this->m_data,'PM_G_ACCOUNT') 
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
			$vtype = str_replace('GLD','OAU',$vtype);
				
			$PAYEE_ACCOUNT = 0;
				
			if($vtype == 'USD'){
				$PAYMENT_UNITS = 'USD';
				$PAYEE_ACCOUNT = is_deffin($this->m_data,'PM_U_ACCOUNT');
			} elseif($vtype == 'EUR'){
				$PAYMENT_UNITS = 'EUR';
				$PAYEE_ACCOUNT = is_deffin($this->m_data,'PM_E_ACCOUNT');
			} elseif($vtype == 'OAU'){
				$PAYMENT_UNITS = 'OAU';
				$PAYEE_ACCOUNT = is_deffin($this->m_data,'PM_G_ACCOUNT');			
			}		

			$pay_sum = is_my_money($pay_sum,2);				
			$text_pay = get_text_pay($this->name, $item, $pay_sum);
			
			$data = get_merch_data($this->name);
			$paymethod = intval(is_isset($data, 'paymethod'));
			$AVAILABLE_PAYMENT_METHODS = 'all';
			if($paymethod == 1){
				$AVAILABLE_PAYMENT_METHODS = 'account';
			} elseif($paymethod == 2){
				$AVAILABLE_PAYMENT_METHODS = 'voucher';
			} elseif($paymethod == 3){
				$AVAILABLE_PAYMENT_METHODS = 'sms';
			} elseif($paymethod == 4){			
				$AVAILABLE_PAYMENT_METHODS = 'wire';
			}
					
			$temp = '
			<form name="MerchantPay" action="https://perfectmoney.is/api/step1.asp" method="post">
				<input name="SUGGESTED_MEMO" type="hidden" value="'. $text_pay .'" />
				<input name="sEmail" type="hidden" value="'. is_email($item->user_email) .'" />
				<input name="PAYMENT_AMOUNT" type="hidden" value="'. $pay_sum .'" />
				<input name="PAYEE_ACCOUNT" type="hidden" value="'. $PAYEE_ACCOUNT .'" />								
									
				<input type="hidden" name="AVAILABLE_PAYMENT_METHODS" value="'. $AVAILABLE_PAYMENT_METHODS .'" />					
				<input type="hidden" name="PAYEE_NAME" value="'. is_deffin($this->m_data,'PM_PAYEE_NAME') .'" />
				<input type="hidden" name="PAYMENT_UNITS" value="'. $PAYMENT_UNITS .'" />
				<input type="hidden" name="PAYMENT_ID" value="'. $item->id .'" />
				<input type="hidden" name="STATUS_URL" value="'. get_merchant_link($this->name.'_status' . get_hash_result_url($this->name)) .'" />
				<input type="hidden" name="PAYMENT_URL" value="'. get_merchant_link($this->name.'_success') .'" />
				<input type="hidden" name="PAYMENT_URL_METHOD" value="POST" />
				<input type="hidden" name="NOPAYMENT_URL" value="'. get_merchant_link($this->name.'_fail') .'" />
				<input type="hidden" name="NOPAYMENT_URL_METHOD" value="POST" />
				<input type="hidden" name="SUGGESTED_MEMO_NOCHANGE" value="1" />
				<input type="hidden" name="BAGGAGE_FIELDS" value="sEmail" />

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
	
			$sPayeeAccount = isset( $_POST['PAYEE_ACCOUNT'] ) ? trim( $_POST['PAYEE_ACCOUNT'] ) : null;
			$iPaymentID = isset( $_POST['PAYMENT_ID'] ) ? $_POST['PAYMENT_ID'] : null;
			$dPaymentAmount = isset( $_POST['PAYMENT_AMOUNT'] ) ? trim( $_POST['PAYMENT_AMOUNT'] ) : null;
			$sPaymentUnits = isset( $_POST['PAYMENT_UNITS'] ) ? trim( $_POST['PAYMENT_UNITS'] ) : null;
			$iPaymentBatch = isset( $_POST['PAYMENT_BATCH_NUM'] ) ? trim( $_POST['PAYMENT_BATCH_NUM'] ) : null;
			$sPayerAccount = isset( $_POST['PAYER_ACCOUNT'] ) ? trim( $_POST['PAYER_ACCOUNT'] ) : null;
			$sTimeStampGMT = isset( $_POST['TIMESTAMPGMT'] ) ? trim( $_POST['TIMESTAMPGMT'] ) : null;
			$sV2Hash = isset( $_POST['V2_HASH'] ) ? trim( $_POST['V2_HASH'] ) : null;
			
			if( !in_array( $sPaymentUnits, array( 'USD', 'EUR', 'OAU' ) ) ){
				die( 'Invalid currency of payment' );
			}

			if( $sV2Hash != strtoupper( md5( $iPaymentID.':'.$sPayeeAccount.':'.$dPaymentAmount.':'.$sPaymentUnits.':'.$iPaymentBatch.':'.$sPayerAccount.':'.strtoupper( md5( is_deffin($this->m_data,'PM_ALTERNATE_PHRASE') ) ).':'.$sTimeStampGMT ) ) ){
				die( 'Invalid control signature' );
			}

			$constant = is_deffin($this->m_data,'PM_'.substr( $sPayeeAccount, 0, 1 ).'_ACCOUNT');
			if( $sPayeeAccount != $constant ){
				die( 'Invalid the seller s account' );
			}
			
			$data = get_merch_data($this->name);
			$check_history = intval(is_isset($data, 'check_api'));
			$show_error = intval(is_isset($data, 'show_error'));
			if($check_history == 1){
			
				try {
					$class = new PerfectMoney( is_deffin($this->m_data,'PM_ACCOUNT_ID'), is_deffin($this->m_data,'PM_PHRASE') );
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
				$vtype = str_replace(array('GLD'),'OAU',$vtype);
			
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
			
									die('Completed');
									
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

new merchant_perfectmoney(__FILE__, 'Perfect Money');