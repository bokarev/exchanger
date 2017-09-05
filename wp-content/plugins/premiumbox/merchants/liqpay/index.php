<?php
/*
title: [ru_RU:]LiqPay[:ru_RU][en_US:]LiqPay[:en_US]
description: [ru_RU:]мерчант LiqPay[:ru_RU][en_US:]LiqPay merchant[:en_US]
version: 1.2
*/

if(!class_exists('merchant_liqpay')){
	class merchant_liqpay extends Merchant_Premiumbox {
		function __construct($file, $title)
		{
			$map = array(
				'LIQPAY_PUBLIC_KEY', 'LIQPAY_PRIVATE_KEY', 
			);			
			parent::__construct($file, $map, $title);
			
			add_action('get_merchant_admin_options_'. $this->name, array($this, 'get_merchant_admin_options'), 10, 2);
			add_filter('merchants_settingtext_'.$this->name, array($this, 'merchants_settingtext'));
			add_filter('merchant_formstep_autocheck',array($this, 'merchant_formstep_autocheck'),1,2);
			add_filter('merchants_action_bid_'.$this->name, array($this,'merchants_action_bid'),99,4);
			add_action('myaction_merchant_'. $this->name .'_fail', array($this,'myaction_merchant_fail'));
			add_action('myaction_merchant_'. $this->name .'_success', array($this,'myaction_merchant_success'));
			add_action('myaction_merchant_'. $this->name .'_status' . get_hash_result_url($this->name), array($this,'myaction_merchant_status'));
			add_action('myaction_merchant_'. $this->name .'_cron' . get_hash_result_url($this->name), array($this,'myaction_merchant_cron'));
		}

		function get_merchant_admin_options($options, $data){ 
			
			if(isset($options['bottom_title'])){
				unset($options['bottom_title']);
			}
			
			if(isset($options['check'])){
				unset($options['check']);
			}
			if(isset($options['check_payapi'])){
				unset($options['check_payapi']);
			}			
			
			$options['check_api'] = array(
				'view' => 'select',
				'title' => __('Check payment history by API','pn'),
				'options' => array('0'=>__('No','pn'), '1'=>__('Yes','pn')),
				'default' => is_isset($data, 'check_api'),
				'name' => 'check_api',
				'work' => 'int',
			);

			$opt = array(
				'0' => __('shop settings','pn'),
				'1' => __('card payment','pn'),
				'2' => __('liqpay account','pn'),
				'3' => __('privat24 account','pn'),
				'4' => __('masterpass account','pn'),
				'5' => __('installments','pn'),
				'6' => __('cash','pn'),
				'7' => __('invoice to e-mail','pn'),
				'8' => __('qr code scanning','pn'),
			);
			$paytype = intval(is_isset($data, 'paytype'));
			$options[] = array(
				'view' => 'select',
				'title' => __('Payment method','pn'),
				'options' => $opt,
				'default' => $paytype,
				'name' => 'paytype',
				'work' => 'int',
			);			
			
			$text = '
			<strong>RETURN URL:</strong> <a href="'. get_merchant_link($this->name.'_status' . get_hash_result_url($this->name)) .'" target="_blank">'. get_merchant_link($this->name.'_status' . get_hash_result_url($this->name)) .'</a><br />
			<strong>SUCCESS URL:</strong> <a href="'. get_merchant_link($this->name.'_success') .'" target="_blank">'. get_merchant_link($this->name.'_success') .'</a><br />
			<strong>FAIL URL:</strong> <a href="'. get_merchant_link($this->name.'_fail') .'" target="_blank">'. get_merchant_link($this->name.'_fail') .'</a><br />	
			<strong>CRON:</strong> <a href="'. get_merchant_link($this->name.'_cron' . get_hash_result_url($this->name)) .'" target="_blank">'. get_merchant_link($this->name.'_cron' . get_hash_result_url($this->name)) .'</a>				
			';

			$options[] = array(
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
				is_deffin($this->m_data,'LIQPAY_PUBLIC_KEY') 
				and is_deffin($this->m_data,'LIQPAY_PRIVATE_KEY') 
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

			$params = array(
				'pay_purse' => '',
				'sum' => '',
				'naschet' => '',
				'trans_in' => '',
			);
			the_merchant_bid_status('techpay', $item->id, 'user', 0, '', $params);		
		 
			$vtype = pn_strip_input($item->vtype1);
					
			$locale = get_locale();
			if($locale == 'ru_RU'){
				$lang = 'ru';
			} else {
				$lang = 'en';
			}			
						
			$pay_sum = is_my_money($pay_sum,2);		
			$text_pay = get_text_pay($this->name, $item, $pay_sum);
						
			$LIQPAY_RESULT_URL = get_merchant_link($this->name.'_success');
			$LIQPAY_SERVER_URL = get_merchant_link($this->name.'_status' . get_hash_result_url($this->name));
				
			$data = get_merch_data($this->name);
			$show_error = intval(is_isset($data, 'show_error'));	
					
			try {
				
				$liqpay = new LiqPay(is_deffin($this->m_data,'LIQPAY_PUBLIC_KEY'), is_deffin($this->m_data,'LIQPAY_PRIVATE_KEY'));
				$cnb_form = array(
					'version'        => 3,
					'action'         => 'pay',
					'amount'         => $pay_sum,
					'currency'       => $vtype,
					'description'    => $text_pay,
					'order_id'       => $item->id,
					'language'       => $lang,
					'result_url'       => $LIQPAY_RESULT_URL,
					'server_url'       => $LIQPAY_SERVER_URL,
					'public_key' => is_deffin($this->m_data,'LIQPAY_PUBLIC_KEY'),
				);
				$data = get_merch_data($this->name);
				$paytype = intval(is_isset($data, 'paytype'));			
				$opts = array(
					'1' => 'card',
					'2' => 'liqpay',
					'3' => 'privat24',
					'4' => 'masterpass',
					'5' => 'part',
					'6' => 'cash',
					'7' => 'invoice',
					'8' => 'qr',
				);
				$pt = trim(is_isset($opts, $paytype));
				if($pt){
					$cnb_form['paytypes'] = $pt;
				}
				
				$temp = $liqpay->cnb_form($cnb_form);				
			}
			catch( Exception $e ) {
				if($show_error){
					$temp = $e->getMessage();
				}
			}
					
			return $temp;				
		}

		function myaction_merchant_fail(){
	
			$id = get_payment_id('order_id');
			the_merchant_bid_delete($id);
	
		}

		function myaction_merchant_success(){
	
			$id = get_payment_id('order_id');
			the_merchant_bid_success($id);
	
		}

		function myaction_merchant_status(){
	
			do_action('merchant_logs', $this->name);
	
			$def_signature = is_param_req('signature');
			$def_data = is_param_req('data');
	
			if(!$def_signature){
				die( 'bad signature' );
			}
			
			$data = base64_decode($def_data);
			$datap = @json_decode($data, true);
	
			$public_key = is_deffin($this->m_data,'LIQPAY_PUBLIC_KEY');
			$private_key = is_deffin($this->m_data,'LIQPAY_PRIVATE_KEY');

			$signature = base64_encode( sha1( $private_key . $def_data . $private_key, 1 ) );
			if($signature != $def_signature){
				die( 'bad sign in request' );
			}
			
			$order_id = $datap['order_id'];
			$type = $datap['type'];/* buy */
			$action = $datap['action'];/* pay */
			$status = $datap['status'];
			$amount = $datap['amount'];
			$currency = $datap['currency'];
			$transaction_id = $datap['transaction_id'];
			
			$data = get_merch_data($this->name);
			$check_history = intval(is_isset($data, 'check_api'));
			$show_error = intval(is_isset($data, 'show_error'));
			if($check_history == 1){
			
				try {
					$liqpay = new LiqPay($public_key, $private_key);
					$res = $liqpay->api("request", array(
						'action' => 'status',
						'version' => '3',
						'order_id' => $order_id
					));	
					if(isset($res->result) and $res->result == 'ok'){
						$type = $res->type;/* buy */
						$action = $res->action;/* pay */
						$status = $res->status;
						$amount = $res->amount;
						$currency = $res->currency;
						$transaction_id = $res->transaction_id;						
					} else {
						die( 'bad request' );
					}
				}
				catch( Exception $e ) {
					if($show_error){
						die('Фатальная ошибка: '.$e->getMessage());
					} else {
						die('Фатальная ошибка');
					}
				}		
			
			}	

			if($type != 'buy' or $action != 'pay'){
				die( 'bad data' );
			}
			
			$id = $order_id;
			$data = get_data_merchant_for_id($id);
			$in_summ = $amount;	
			$in_summ = is_my_money($in_summ,2);
			$err = $data['err'];
			$status = $data['status'];
			$m_id = $data['m_id'];
			$pay_purse = is_pay_purse('', $data, $m_id);
				
			$vtype = $data['vtype'];
			$vtype = str_replace(array('GLD'),'OAU',$vtype);
			
			$bid_sum = is_my_money($data['pay_sum'],2);
			$bid_sum = apply_filters('merchant_bid_sum', $bid_sum, $m_id);
			
			if($status == 'new' or $status == 'coldpay'or $status == 'techpay'){ 
				if($err == 0){
					if($m_id and $m_id == $this->name){
						if($vtype == $currency){
							if($in_summ >= $bid_sum){		
						
								if($status == 'success'){
									$params = array(
										'pay_purse' => $pay_purse,
										'sum' => $in_summ,
										'naschet' => $public_key,
										'trans_in' => $transaction_id,
									);
									the_merchant_bid_status('realpay', $id, 'user', 0, '', $params);																		
								} elseif($status == 'failure' or $status == 'error' or $status == 'reversed') {
									$params = array(
										'pay_purse' => $pay_purse,
										'sum' => $in_summ,
										'naschet' => $public_key,
										'trans_in' => $transaction_id,
									);
									the_merchant_bid_status('error', $id, 'user', 0, '', $params);																		
								} else {	
									$params = array(
										'pay_purse' => $pay_purse,
										'sum' => $in_summ,
										'naschet' => $public_key,
										'trans_in' => $transaction_id,
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
		
		function myaction_merchant_cron(){
		global $wpdb;	
			
			$data = get_merch_data($this->name);
			$show_error = intval(is_isset($data, 'show_error'));
			
			$m_in = $this->name;
			$public_key = is_deffin($this->m_data,'LIQPAY_PUBLIC_KEY');
			$private_key = is_deffin($this->m_data,'LIQPAY_PRIVATE_KEY');
			$items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."bids WHERE status IN('coldpay','techpay') AND m_in='$m_in'");
			foreach($items as $item){
				$order_id = $item->id;
				try {
					$liqpay = new LiqPay($public_key, $private_key);
					$res = $liqpay->api("request", array(
						'action' => 'status',
						'version' => '3',
						'order_id' => $order_id
					));	
					if(isset($res->result, $res->status) and $res->result == 'ok'){ 
						$type = $res->type;
						$action = $res->action;
						$amount = $res->amount;
						$currency = $res->currency;
						$transaction_id = $res->transaction_id;
						$status = $res->status;

						$id = $order_id;
						$data = get_data_merchant_for_id($id, $item);
						$in_summ = $amount;
						$in_summ = is_my_money($in_summ,2);
						$err = $data['err'];
						$m_id = $data['m_id'];
						$pay_purse = is_pay_purse('', $data, $m_id);
						$vtype = $data['vtype'];
									
						$bid_sum = is_my_money($data['pay_sum'],2);
						$bid_sum = apply_filters('merchant_bid_sum', $bid_sum, $m_in);
						
						if($err == 0 and $vtype == $currency and $type == 'buy' and $action == 'pay'){
							if($in_summ >= $bid_sum){	
							
								if($status == 'success'){
									$params = array(
										'pay_purse' => $pay_purse,
										'sum' => $in_summ,
										'naschet' => $public_key,
										'trans_in' => $transaction_id,
									);
									the_merchant_bid_status('realpay', $id, 'system', 0, '', $params);										
								} elseif($status == 'failure' or $status == 'error' or $status == 'reversed') {
									$params = array(
										'pay_purse' => $pay_purse,
										'sum' => $in_summ,
										'naschet' => $public_key,
										'trans_in' => $transaction_id,
									);
									the_merchant_bid_status('error', $id, 'system', 0, '', $params);									
								} else {
									$params = array(
										'pay_purse' => $pay_purse,
										'sum' => $in_summ,
										'naschet' => $public_key,
										'trans_in' => $transaction_id,
									);
									the_merchant_bid_status('coldpay', $id, 'system', 0, '', $params);																
								}							
			
							}				 
						}	

					} 	
				}
				catch( Exception $e ) {
					if($show_error){
						die($e);
					}	
				}
			}	

			_e('Done','pn');
			exit;
			
		}	
		
	}
}

new merchant_liqpay(__FILE__, 'LiqPay');