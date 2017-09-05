<?php
/*
title: [ru_RU:]Paxum[:ru_RU][en_US:]Paxum[:en_US]
description: [ru_RU:]мерчант Paxum[:ru_RU][en_US:]Paxum merchant[:en_US]
version: 1.2
*/

if(!class_exists('merchant_paxum')){
	class merchant_paxum extends Merchant_Premiumbox {

		function __construct($file, $title)
		{
			
			$map = array(
				'PAXUM_EMAIL', 'PAXUM_SECRET', 
			);
			parent::__construct($file, $map, $title);
			
			add_action('get_merchant_admin_options_'. $this->name, array($this, 'get_merchant_admin_options'), 10, 2);
			add_filter('merchants_settingtext_'.$this->name, array($this, 'merchants_settingtext'));
			add_filter('merchant_formstep_autocheck',array($this, 'merchant_formstep_autocheck'),1,2);
			add_filter('merchants_action_bid_'.$this->name, array($this,'merchants_action_bid'),99,4);
			add_action('myaction_merchant_'. $this->name .'_fail', array($this,'myaction_merchant_fail'));
			add_action('myaction_merchant_'. $this->name .'_success', array($this,'myaction_merchant_success'));
			add_action('myaction_merchant_'. $this->name .'_status', array($this,'myaction_merchant_status'));
		}

		function get_merchant_admin_options($options, $data){ 
			
			$text = '
			<strong>RESULT URL:</strong> <a href="'. get_merchant_link($this->name.'_status') .'" target="_blank">'. get_merchant_link($this->name.'_status' . get_hash_result_url($this->name)) .'</a>			
			';
			$options[] = array(
				'view' => 'textfield',
				'title' => '',
				'default' => $text,
			);
			if(isset($options['check'])){
				unset($options['check']);
			}
			if(isset($options['check_api'])){
				unset($options['check_api']);
			}
			if(isset($options['check_payapi'])){
				unset($options['check_payapi']);
			}
			if(isset($options['resulturl'])){
				unset($options['resulturl']);
			}			
			
			return $options;	
		}			
		
		function merchants_settingtext(){
			$text = '| <span class="bred">'. __('Config file is not set up','pn') .'</span>';
			if(
				is_deffin($this->m_data,'PAXUM_SECRET') 
				and is_deffin($this->m_data,'PAXUM_EMAIL') 
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
					
				$temp = '
				<form name="changer_form" action="https://www.paxum.com/payment/phrame.php?action=displayProcessPaymentLogin" target="_blank" method="post">
					<input type="hidden" name="business_email" value="'. is_deffin($this->m_data,'PAXUM_EMAIL') .'" />
					<input type="hidden" name="button_type_id" value="1" />
					<input type="hidden" name="item_id" value="'. $item->id .'" />
					<input type="hidden" name="item_name" value="'. $text_pay .'" />
					<input type="hidden" name="amount" value="'. $pay_sum .'" />
					<input type="hidden" name="currency" value="'. $vtype .'" />
					<input type="hidden" name="ask_shipping" value="1" />
					<input type="hidden" name="cancel_url" value="'. get_merchant_link($this->name.'_fail') .'" />
					<input type="hidden" name="finish_url" value="'. get_merchant_link($this->name.'_success') .'" />
					<input type="hidden" name="variables" value="notify_url='. get_merchant_link($this->name.'_status') .'" />
					<input type="submit" value="'. __('Make a payment','pn') .'" />
				</form>													
				';				

			return $temp;		
			
		}

		function myaction_merchant_fail(){
	
			$id = get_payment_id('transaction_item_id');
			the_merchant_bid_delete($id);
	
		}

		function myaction_merchant_success(){
	
			$id = get_payment_id('transaction_item_id');
			the_merchant_bid_success($id);
	
		}

		function myaction_merchant_status(){
	
			do_action('merchant_logs', $this->name);
	
			if(!isset($_POST['transaction_item_id']) or !isset($_POST['key'])){
				die( 'No id' );
			}		
			
			$rawPostedData = file_get_contents('php://input');

			$i = strpos($rawPostedData, "&key=");
			$fieldValuePairsData = substr($rawPostedData, 0, $i);

			$calculatedKey = md5($fieldValuePairsData . is_deffin($this->m_data,'PAXUM_SECRET'));

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

			$id = is_param_post('transaction_item_id');
			$data = get_data_merchant_for_id($id);
			$in_summ = is_param_post('transaction_amount');
			$in_summ = is_my_money($in_summ,2);
			$transaction_status = is_param_post('transaction_status');
			$transaction_currency = is_param_post('transaction_currency');
			
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
						if($vtype == $transaction_currency and $transaction_status == 'done'){
							if($in_summ >= $bid_sum){		
					
								$params = array(
									'pay_purse' => $pay_purse,
									'sum' => $in_summ,
									'naschet' => '',
									'trans_in' => '',
								);
								the_merchant_bid_status('realpay', $id, 'user', 0, '', $params);					
											
								die( 'Completed' );
								 
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

new merchant_paxum(__FILE__, 'Paxum');