<?php
/*
title: [ru_RU:]AdvCash[:ru_RU][en_US:]AdvCash[:en_US]
description: [ru_RU:]мерчант AdvCash[:ru_RU][en_US:]AdvCash merchant[:en_US]
version: 1.2
*/

if(!class_exists('merchant_advcash')){
	class merchant_advcash extends Merchant_Premiumbox {
		function __construct($file, $title)
		{
			
			$map = array(
				'ACCOUNT_EMAIL', 'SCI_NAME', 'SCI_SECRET',
				'API_NAME','API_PASSWORD',
			);
			parent::__construct($file, $map, $title);
			
			add_filter('merchants_settingtext_'. $this->name, array($this, 'merchants_settingtext'));
			add_filter('merchant_formstep_autocheck',array($this, 'merchant_formstep_autocheck'),1,2);
			add_action('get_merchant_admin_options_'. $this->name, array($this, 'get_merchant_admin_options'), 10, 2);
			add_filter('merchants_action_bid_'.$this->name, array($this,'merchants_action_bid'),99,4);
			add_action('myaction_merchant_'. $this->name .'_fail', array($this,'myaction_merchant_fail'));
			add_action('myaction_merchant_'. $this->name .'_success', array($this,'myaction_merchant_success'));
			add_action('myaction_merchant_'. $this->name .'_status' . get_hash_result_url($this->name), array($this,'myaction_merchant_status'));
		}

		function merchants_settingtext(){
			$text = '| <span class="bred">'. __('Config file is not set up','pn') .'</span>';
			if(
				is_deffin($this->m_data,'ACCOUNT_EMAIL') 
				and is_deffin($this->m_data,'SCI_NAME') 
				and is_deffin($this->m_data,'SCI_SECRET') 
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

		function get_merchant_admin_options($options, $data){ 
			
			if(isset($options['bottom_title'])){
				unset($options['bottom_title']);
			}
			if(isset($options['check_payapi'])){
				unset($options['check_payapi']);
			}			
			
			$text = '
			<strong>Status URL:</strong> <a href="'. get_merchant_link($this->name.'_status' . get_hash_result_url($this->name)) .'" target="_blank">'. get_merchant_link($this->name.'_status' . get_hash_result_url($this->name)) .'</a><br />
			<strong>Success URL:</strong> <a href="'. get_merchant_link($this->name.'_success') .'" target="_blank">'. get_merchant_link($this->name.'_success') .'</a><br />
			<strong>Fail URL:</strong> <a href="'. get_merchant_link($this->name.'_fail') .'" target="_blank">'. get_merchant_link($this->name.'_fail') .'</a>			
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

		function merchants_action_bid($temp, $pay_sum, $item, $naps){

			$amount = is_my_money($pay_sum,2);
			$text_pay = get_text_pay($this->name, $item, $amount);		
			
			$currency = pn_strip_input(str_replace('RUB','RUR',$item->vtype1));
			$orderId = $item->id;
			$ac_account_email = is_deffin($this->m_data,'ACCOUNT_EMAIL');
			$ac_sci_name = is_deffin($this->m_data,'SCI_NAME');
			$sign = hash('sha256', $ac_account_email . ":" . $ac_sci_name . ":" . $amount . ":" . $currency . ":" . is_deffin($this->m_data,'SCI_SECRET') . ":" . $orderId);
									
			$temp = '
			<form name="MerchantPay" action="https://wallet.advcash.com/sci/" method="post">
				<input type="hidden" name="ac_account_email" value="'. $ac_account_email .'" /> 
				<input type="hidden" name="ac_sci_name" value="'. $ac_sci_name .'" />  
				<input type="hidden" name="ac_order_id" value="'. $orderId .'" /> 
				<input type="hidden" name="ac_sign" value="'. $sign .'" />			
						
				<input type="hidden" name="ac_amount" value="'. $amount .'" />
				<input type="hidden" name="ac_currency" value="'. $currency .'" />
				<input type="hidden" name="ac_comments" value="'. $text_pay .'" />
						
				<input type="submit" value="'. __('Make a payment','pn') .'" />
			</form>												
			';				
			
			return $temp;
		}

		function myaction_merchant_fail(){
			$id = get_payment_id('ac_order_id');
			the_merchant_bid_delete($id);
		}

		function myaction_merchant_success(){
			$id = get_payment_id('ac_order_id');
			the_merchant_bid_success($id);
		}
	
		function myaction_merchant_status(){
	
			do_action('merchant_logs', $this->name);
	
			/* Получение внешних данных */
			$transactionId = is_param_req('ac_transfer');
			$paymentDate = is_param_req('ac_start_date');
			$sciName = is_param_req('ac_sci_name');
			$payer = is_param_req('ac_src_wallet');
			$destWallet = is_param_req('ac_dest_wallet');
			$orderId = is_param_req('ac_order_id');
			$amount = is_param_req('ac_amount');
			$currency = is_param_req('ac_merchant_currency');
			$hash = is_param_req('ac_hash'); 
			$pay_status = is_param_req('ac_transaction_status');
			
			if( $hash != strtolower( hash('sha256', $transactionId.':'.$paymentDate.':'.$sciName.':'.$payer.':'.$destWallet.':'.$orderId.':'.$amount.':'.$currency.':'. is_deffin($this->m_data,'SCI_SECRET') ) ) ){
				die( 'Неверная контрольная подпись' );	
			}	
			
			$next = 1;
			
			$data = get_merch_data($this->name);
			$check_history = intval(is_isset($data, 'check_api'));
			$show_error = intval(is_isset($data, 'show_error'));
			if($check_history == 1){
			
				$next = 0;
			
				try {
					$merchantWebService = new MerchantWebService();
					$arg0 = new authDTO();
					$arg0->apiName = is_deffin($this->m_data,'API_NAME');
					$arg0->accountEmail = is_deffin($this->m_data,'ACCOUNT_EMAIL');
					$arg0->authenticationToken = $merchantWebService->getAuthenticationToken(is_deffin($this->m_data,'API_PASSWORD'));

					$arg1 = $transactionId;

					$findTransaction = new findTransaction();
					$findTransaction->arg0 = $arg0;
					$findTransaction->arg1 = $arg1;
					
					$findTransactionResponse = $merchantWebService->findTransaction($findTransaction);
					if(isset($findTransactionResponse->return)){
						$result = $findTransactionResponse->return;
						
						$next = 1;
						
						$payer = is_isset($result, 'walletSrcId');
						$destWallet = is_isset($result, 'walletDestId');
						$orderId = is_isset($result, 'orderId');
						$amount = is_isset($result, 'amount');
						$currency = is_isset($result, 'currency');
						$pay_status = is_isset($result,'status');						
					}
				}
				catch( Exception $e ) {
					if($show_error){
						die($e->getMessage());
					}
				}		
			
			}			
			
			if($next != 1){
				die( 'Ошибка проверки по истории!' );
			}
			
			$id = $orderId;
			$data = get_data_merchant_for_id($id);
			$in_summ = $amount;
			$in_summ = is_my_money($in_summ,2);
			$err = $data['err'];
			$status = $data['status'];
			$m_id = $data['m_id'];
			$pay_purse = is_pay_purse($payer, $data, $m_id);
			
			$vtype = $data['vtype'];
			$vtype = str_replace('RUB','RUR',$vtype);
			$bid_sum = is_my_money($data['pay_sum'],2);
			$bid_sum = apply_filters('merchant_bid_sum', $bid_sum, $m_id);
			
			/*
			PENDING, PROCESS, CONFIRMED, COMPLETED, CANCELED
			*/
			$en_status = array('new','techpay','coldpay');
			if(in_array($status, $en_status)){ 
				if($err == 0){
					if($m_id and $m_id == $this->name){
						if($vtype == $currency){
							if($in_summ >= $bid_sum){		
					
								$now_status = '';
								if($pay_status == 'PENDING'){
									$now_status = 'coldpay';
								} elseif($pay_status == 'PROCESS'){
									$now_status = 'coldpay';
								} elseif($pay_status == 'COMPLETED'){
									$now_status = 'realpay';
								}
								if($now_status){	
								
									$params = array(
										'pay_purse' => $pay_purse,
										'sum' => $in_summ,
										'soschet' => '',
										'naschet' => $destWallet,
										'trans_in' => $transactionId,
										'trans_out' => '',
									);
									the_merchant_bid_status($now_status, $id, 'user', 0, '', $params);
								}
								
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

new merchant_advcash(__FILE__, 'AdvCash');		