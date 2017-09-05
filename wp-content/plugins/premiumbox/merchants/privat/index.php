<?php
/*
title: [ru_RU:]Privat24[:ru_RU][en_US:]Privat24[:en_US]
description: [ru_RU:]мерчант Privat24[:ru_RU][en_US:]Privat24 merchant[:en_US]
version: 1.2
*/

if(!class_exists('merchant_privat')){
	class merchant_privat extends Merchant_Premiumbox{

		function __construct($file, $title)
		{
			$map = array(
				'PRIVAT24_MERCHANT_ID_UAH', 'PRIVAT24_MERCHANT_KEY_UAH', 
				'PRIVAT24_MERCHANT_ID_USD', 'PRIVAT24_MERCHANT_KEY_USD', 
				'PRIVAT24_MERCHANT_ID_EUR', 'PRIVAT24_MERCHANT_KEY_EUR', 
			);
			parent::__construct($file, $map, $title);
			
			add_action('get_merchant_admin_options_'. $this->name, array($this, 'get_merchant_admin_options'), 10, 2);
			add_filter('merchants_settingtext_'.$this->name, array($this, 'merchants_settingtext'));
			add_filter('merchant_formstep_autocheck',array($this, 'merchant_formstep_autocheck'),1,2);
			add_filter('merchants_action_bid_'.$this->name, array($this,'merchants_action_bid'),99,4);
			add_action('myaction_merchant_'. $this->name .'_return', array($this,'myaction_merchant_return'));
			add_action('myaction_merchant_'. $this->name .'_status' . get_hash_result_url($this->name), array($this,'myaction_merchant_status'));
		}

		function get_merchant_admin_options($options, $data){ 
			
			$text = '
			<strong>CRON:</strong> <a href="'. get_merchant_link($this->name.'_status' . get_hash_result_url($this->name)) .'" target="_blank">'. get_merchant_link($this->name.'_status' . get_hash_result_url($this->name)) .'</a>			
			';

			if(isset($options['check'])){
				unset($options['check']);
			}
			if(isset($options['check_api'])){
				unset($options['check_api']);
			}
			if(isset($options['check_payapi'])){
				unset($options['check_payapi']);
			}			
			
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
				is_deffin($this->m_data,'PRIVAT24_MERCHANT_ID_UAH') and is_deffin($this->m_data,'PRIVAT24_MERCHANT_KEY_UAH') 
				or is_deffin($this->m_data,'PRIVAT24_MERCHANT_ID_USD') and is_deffin($this->m_data,'PRIVAT24_MERCHANT_KEY_USD') 
				or is_deffin($this->m_data,'PRIVAT24_MERCHANT_ID_EUR') and is_deffin($this->m_data,'PRIVAT24_MERCHANT_KEY_EUR') 
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
				
			$merchant = 0;
			if($vtype == 'UAH'){
				$merchant = is_deffin($this->m_data,'PRIVAT24_MERCHANT_ID_UAH');
			} elseif($vtype == 'USD'){
				$merchant = is_deffin($this->m_data,'PRIVAT24_MERCHANT_ID_USD');
			} elseif($vtype == 'EUR'){
				$merchant = is_deffin($this->m_data,'PRIVAT24_MERCHANT_ID_EUR');
			}		
					
	
			$pay_sum = is_my_money($pay_sum,2);		
			$text_pay = get_text_pay($this->name, $item, $pay_sum);
					
			$params = array(
				'pay_purse' => '',
				'sum' => 0,
				'naschet' => '',
				'trans_in' => '',
			);
			the_merchant_bid_status('techpay', $item->id, 'user', 0, '', $params);								
					 
			$temp = '
			<form name="pay" action="https://api.privatbank.ua/p24api/ishop" method="post" target="_blank">
											
				<input type="hidden" name="merchant" value="'. $merchant .'" />
				<input type="hidden" name="pay_way" value="privat24" />
				<input type="hidden" name="server_url" value="'. get_merchant_link($this->name.'_status' . get_hash_result_url($this->name)) .'" />
				<input type="hidden" name="return_url" value="'. get_merchant_link($this->name.'_return') .'" />
				<input name="order" type="hidden" value="'. $item->id .'" />
				<input name="amt" type="hidden" value="'. $pay_sum .'" />
				<input name="ccy" type="hidden" value="'. $vtype .'" />
				<input name="details" type="hidden" value="'. $text_pay .'" />
				<input name="ext_details" type="hidden" value="'. is_email($item->user_email) .'" />

				<input type="submit" value="'. __('Make a payment','pn') .'" />
			</form>												
			';					
		
			return $temp;					
		}

		function myaction_merchant_return(){
	
			$payment = urldecode(is_param_post('payment'));
			parse_str($payment,$arr);
			
			$order_id = intval(is_isset($arr,'order'));
			$state = is_isset($arr,'state');

			if($state == 'ok'){
				the_merchant_bid_success($order_id);
			} else {	
				the_merchant_bid_delete($order_id);
			}			
	
		}

		function myaction_merchant_status(){
			global $wpdb;
			
			$m_in = $this->name;
			
			$data = get_merch_data($this->name);
			$show_error = intval(is_isset($data, 'show_error'));
			
			$en_currency = array('USD', 'EUR', 'UAH');
			$items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."bids WHERE status IN('coldpay','techpay') AND m_in='$m_in'");
			foreach($items as $item){
				
				$currency = mb_strtoupper($item->vtype1);
				if(in_array($currency, $en_currency)){
				
					$merchant_id = is_deffin($this->m_data,'PRIVAT24_MERCHANT_ID_'.$currency);
					$merchant_key = is_deffin($this->m_data,'PRIVAT24_MERCHANT_KEY_'.$currency);
				
					if($merchant_id and $merchant_key){
						try {
							$oClass = new PrivatBank($merchant_id,$merchant_key);
							$res = $oClass->get_order($item->id);
							if(isset($res['state']) and $res['state'] == 'ok'){
								$id = $res['order'];
								$data = get_data_merchant_for_id($id, $item);
								$in_summ = $res['amt'];
								$in_summ = is_my_money($in_summ,2);
								$err = $data['err'];
								$m_id = $data['m_id'];
								$pay_purse = is_pay_purse('', $data, $m_id);
								$vtype = $data['vtype'];	
								$bid_sum = is_my_money($data['pay_sum'],2);
								$bid_sum = apply_filters('merchant_bid_sum', $bid_sum, $m_in);
								if($err == 0 and $m_id and $m_id == $m_in and $vtype == $res['ccy']){
									if($in_summ >= $bid_sum){	
										$params = array(
											'pay_purse' => $pay_purse,
											'sum' => $in_summ,
											'naschet' => $merchant_id,
											'trans_in' => is_isset($res,'payment_id'),
										);
										the_merchant_bid_status('realpay', $id, 'user', 0, '', $params);											
									}				 
								}	
							}
						}
						catch( Exception $e ) {
							if($show_error){
								echo $e;
							}
						}
					} 
				
				}
				
			}			
	
		}
		
	}
}

new merchant_privat(__FILE__, 'Privat24');