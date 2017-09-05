<?php
/*
title: [ru_RU:]Basel[:ru_RU][en_US:]Basel[:en_US]
description: [ru_RU:]мерчант Basel[:ru_RU][en_US:]Basel merchant[:en_US]
version: 1.2
*/

if(!class_exists('merchant_basel')){
	class merchant_basel extends Merchant_Premiumbox{
		
		function __construct($file, $title)
		{
			$map = array(
				'E_PURSE', 'R_PURSE', 'S_PURSE', 'SECRET'
			);
			parent::__construct($file, $map, $title);
			
			add_action('get_merchant_admin_options_'. $this->name, array($this, 'get_merchant_admin_options'), 10, 2);
			add_filter('merchants_settingtext_'.$this->name, array($this, 'merchants_settingtext'));
			add_filter('merchant_formstep_autocheck',array($this, 'merchant_formstep_autocheck'),1,2);
			add_filter('merchants_action_bid_'.$this->name, array($this,'merchants_action_bid'),99,4);
			add_action('myaction_merchant_'. $this->name .'_status' . get_hash_result_url($this->name), array($this,'myaction_merchant_status'));
		}

		function get_merchant_admin_options($options, $data){ 
			
			if(isset($options['check'])){
				unset($options['check']);
			}
			if(isset($options['check_api'])){
				unset($options['check_api']);
			}
			if(isset($options['check_payapi'])){
				unset($options['check_payapi']);
			}			
			if(isset($options['note'])){
				unset($options['note']);
			}			
			
			$text = '
			<strong>Result URL:</strong> <a href="'. get_merchant_link($this->name.'_status' . get_hash_result_url($this->name)) .'" target="_blank">'. get_merchant_link($this->name.'_status' . get_hash_result_url($this->name)) .'</a><br />			
			';

			$options['text'] = array(
				'view' => 'textfield',
				'title' => '',
				'default' => $text,
			);									
			
			return $options;	
		}		
		
		function merchants_settingtext(){
			$text = '| <span class="bred">'. __('Config file is not set up','pn') .'</span>';
			if(
				is_deffin($this->m_data,'E_PURSE') and  is_deffin($this->m_data,'SECRET')
				or is_deffin($this->m_data,'R_PURSE') and  is_deffin($this->m_data,'SECRET')
				or is_deffin($this->m_data,'S_PURSE') and  is_deffin($this->m_data,'SECRET')
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
			$shopAccount = 0;

			if($vtype == 'USD'){
				$shopAccount = is_deffin($this->m_data,'S_PURSE');
			} elseif($vtype == 'RUB'){
				$shopAccount = is_deffin($this->m_data,'R_PURSE');
			} elseif($vtype == 'EUR'){
				$shopAccount = is_deffin($this->m_data,'E_PURSE');
			} 	


			$pay_sum = is_my_money($pay_sum,2);		
						
			$temp = '
			<form action="https://basel3.is/invoice" method="POST">
				<input type="hidden" name="shopAccount" value="'. $shopAccount .'">
				<input type="hidden" name="shopInvoice" value="'. $item->id .'">
				<input type="hidden" name="amount" value="'. $pay_sum .'">
				<input type="hidden" name="currency" value="'. $vtype .'">
				<input type="hidden" name="backUrl" value="'. get_bids_url($item->hashed) .'">
				<input type="submit" value="Ok">
			</form>					
			';				
			
			return $temp;
		}

		function myaction_merchant_status(){
	
			do_action('merchant_logs', $this->name);
	
			$invoice = is_param_req('invoice');
			$accountId = is_param_req('accountId');
			$transactionId = is_param_req('transactionId');
			$amount = is_param_req('amount');
			$currency = is_param_req('currency');
			$status = is_param_req('status');
			$sign = is_param_req('sign');
	
			if( $status != 'PAID' ) {
				die( 'Payments status no paid' );
			}

			if( $sign != sha1(md5($invoice.'_'. $transactionId .'_'. $accountId .'_'. $amount .'_'.is_deffin($this->m_data,'SECRET'))) ){
				die( 'Invalid control signature' );
			}

			$id = $invoice;
			$data = get_data_merchant_for_id($id);
			
			$in_summ = $amount;	
			$in_summ = is_my_money($in_summ,2);
			$err = $data['err'];
			$status = $data['status'];
			$m_id = $data['m_id'];
			$pay_purse = is_pay_purse('', $data, $m_id);
			
			$vtype = $data['vtype'];	
	
			$bid_sum = is_my_money($data['pay_sum'],2);
			$bid_sum = apply_filters('merchant_bid_sum', $bid_sum, $m_id);
	
			if($status == 'new'){ 
				if($err == 0){
					if($m_id and $m_id == $this->name){
						if($vtype == $currency){
							if($in_summ >= $bid_sum){		
					
								$params = array(
									'pay_purse' => $pay_purse,
									'sum' => $in_summ,
									'naschet' => $accountId,
									'trans_in' => $transactionId,
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

new merchant_basel(__FILE__, 'Basel');