<?php
/*
title: [ru_RU:]Okpay[:ru_RU][en_US:]Okpay[:en_US]
description: [ru_RU:]мерчант Okpay[:ru_RU][en_US:]Okpay merchant[:en_US]
version: 1.2
*/

if(!class_exists('merchant_okpay')){
	class merchant_okpay extends Merchant_Premiumbox {

		function __construct($file, $title)
		{
			$map = array(
				'OKPAY_ACCOUNT', 'OKPAY_API_KEY', 
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
			
			$text = '
			<strong>RETURN URL:</strong> <a href="'. get_merchant_link($this->name.'_status' . get_hash_result_url($this->name)) .'" target="_blank">'. get_merchant_link($this->name.'_status' . get_hash_result_url($this->name)) .'</a><br />
			<strong>SUCCESS URL:</strong> <a href="'. get_merchant_link($this->name.'_success') .'" target="_blank">'. get_merchant_link($this->name.'_success') .'</a><br />
			<strong>FAIL URL:</strong> <a href="'. get_merchant_link($this->name.'_fail') .'" target="_blank">'. get_merchant_link($this->name.'_fail') .'</a>	
			';
			if(isset($options['check_api'])){
				unset($options['check_api']);
			}
			if(isset($options['check_payapi'])){
				unset($options['check_payapi']);
			}			
			
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
				is_deffin($this->m_data,'OKPAY_ACCOUNT') 
				and is_deffin($this->m_data,'OKPAY_API_KEY') 
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
						
			$pay_sum = is_my_money($pay_sum,2);						
			$text_pay = get_text_pay($this->name, $item, $pay_sum);
			
			$user_email = is_email($item->user_email);
						
			$temp = '
			<form action="https://www.okpay.com/process.html" method="post">
				<input name="ok_receiver" type="hidden" value="'. is_deffin($this->m_data,'OKPAY_ACCOUNT') .'" />
				<input name="ok_fees" type="hidden" value="0" />
				<input name="ok_return_success" type="hidden" value="'. get_merchant_link($this->name.'_success') .'" />
				<input name="ok_return_fail" type="hidden" value="'. get_merchant_link($this->name.'_fail') .'" />
				<input name="ok_ipn" type="hidden" value="'. get_merchant_link($this->name.'_status' . get_hash_result_url($this->name)) .'" />								
				<input name="ok_invoice" type="hidden" value="'. $item->id .'" />
				<input name="ok_item_1_price" type="hidden" value="'. $pay_sum .'" />
				<input name="ok_currency" type="hidden" value="'. $vtype .'" />
				<input name="ok_item_1_name" type="hidden" value="'. $text_pay .'" />';
				
				if($user_email){
					$temp .= '<input name="ok_payer_email" type="hidden" value="'. $user_email .'" />';
				}
				
				$temp .= '
				<input type="submit" value="'. __('Make a payment','pn') .'" />
			 </form>												
			';				
			
			return $temp;		
		}

		function myaction_merchant_fail(){
	
			$id = get_payment_id('ok_invoice');
			the_merchant_bid_delete($id);
	
		}

		function myaction_merchant_success(){
	
			$id = get_payment_id('ok_invoice');
			the_merchant_bid_success($id);
	
		}

		function myaction_merchant_status(){
	
			do_action('merchant_logs', $this->name);
	
			$iTransferID = intval( is_param_req( 'ok_txn_id' ) );
			if(!$iTransferID){
				die('Not id');
			}

			$sComment = is_param_req( 'ok_item_1_name' );

			$oClass = new OKPay( is_deffin($this->m_data,'OKPAY_ACCOUNT'), is_deffin($this->m_data,'OKPAY_API_KEY') );
			$aTransfer = $oClass->searchTransfer( $iTransferID );
			
			$okpay_status = is_isset($aTransfer,'Status');
			
			// if( !isset($aTransfer['Status']) or $aTransfer['Status'] != 'Completed'){
				// die('Неверный статус платежа');
			// }	

			if( $aTransfer['Receiver']['WalletID'] != is_deffin($this->m_data,'OKPAY_ACCOUNT') ){
				die( 'Неверный счет получателя' );
			}

			$dAmount = $aTransfer['Amount'] - 0;
			$sCurrency = $aTransfer['Currency'];
			$iOrderID = $aTransfer['Invoice'] - 0;
			$sSenderWalletID = $aTransfer['Sender']['WalletID'];
			$sEmail = $aTransfer['Sender']['Email'];

			# $iOrderID - номер заказа
			# $dAmount - сумма платежа
			# $sCurrency - валюта платежа
			# $sComment - примечание к платежу
			# $sSenderWalletID - счет плательщика
			# $sEmail - E-mail адрес
			# $iTransferID - внутренний номер перевода

			$id = $iOrderID;
			$data = get_data_merchant_for_id($id);
			$in_summ = $dAmount;
			$in_summ = is_my_money($in_summ,2);
			
			$err = $data['err'];
			$status = $data['status'];
			$m_id = $data['m_id'];
			$pay_purse = is_pay_purse($sSenderWalletID, $data, $m_id);
			$vtype = $data['vtype'];

			$bid_sum = is_my_money($data['pay_sum'],2);
			$bid_sum = apply_filters('merchant_bid_sum', $bid_sum, $m_id);
			
			if($status == 'new' or $status == 'coldpay'){ 
				if($err == 0){
					if($m_id and $m_id == $this->name){
						if($vtype == $sCurrency){
							if($in_summ >= $bid_sum){		
					
								if($okpay_status == 'Completed'){
									$params = array(
										'pay_purse' => $pay_purse,
										'sum' => $in_summ,
										'naschet' => is_deffin($this->m_data,'OKPAY_ACCOUNT'),
										'trans_in' => $iTransferID,
									);
									the_merchant_bid_status('realpay', $id, 'user', 0, '', $params);									
								} else {
									$params = array(
										'pay_purse' => $pay_purse,
										'sum' => $in_summ,
										'naschet' => is_deffin($this->m_data,'OKPAY_ACCOUNT'),
										'trans_in' => $iTransferID,
									);
									the_merchant_bid_status('coldpay', $id, 'user', 0, '', $params);									
								}
								
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

new merchant_okpay(__FILE__, 'Okpay');