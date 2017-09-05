<?php
/*
title: [ru_RU:]Privat24[:ru_RU][en_US:]Privat24[:en_US]
description: [ru_RU:]авто выплаты Privat24[:ru_RU][en_US:]Privat24 automatic payouts[:en_US]
version: 1.2
*/

if(!class_exists('paymerchant_privatbank')){
	class paymerchant_privatbank extends AutoPayut_Premiumbox{
		function __construct($file, $title)
		{
			$map = array(
				'AP_PRIVAT24_BUTTON', 'AP_PRIVAT24_MERCHANT_ID_UAH', 'AP_PRIVAT24_MERCHANT_KEY_UAH', 
				'AP_PRIVAT24_MERCHANT_CARD_UAH', 'AP_PRIVAT24_MERCHANT_ID_USD', 'AP_PRIVAT24_MERCHANT_KEY_USD',
				'AP_PRIVAT24_MERCHANT_CARD_USD', 'AP_PRIVAT24_MERCHANT_ID_EUR', 'AP_PRIVAT24_MERCHANT_KEY_EUR',
				'AP_PRIVAT24_MERCHANT_CARD_EUR', 
			);
			parent::__construct($file, $map, $title, 'AP_PRIVAT24_BUTTON');
			
			add_action('get_paymerchant_admin_options_'.$this->name, array($this, 'get_paymerchant_admin_options'), 10, 2);
			add_filter('paymerchants_settingtext_'.$this->name, array($this, 'paymerchants_settingtext'));
			add_filter('reserv_place_list',array($this,'reserv_place_list'));
			add_filter('update_valut_autoreserv', array($this,'update_valut_autoreserv'), 10, 3);
			add_filter('update_naps_reserv', array($this,'update_naps_reserv'), 10, 4);
			add_action('paymerchant_action_bid_'.$this->name, array($this,'paymerchant_action_bid'),99,3);
			add_action('myaction_merchant_ap_'.$this->name.'_cron', array($this,'myaction_merchant_cron'));
		}

		function get_paymerchant_admin_options($options, $data){
			
			if(isset($options['bottom_title'])){
				unset($options['bottom_title']);
			}
			if(isset($options['checkpay'])){
				unset($options['checkpay']);
			}			

			$opt = array(
				'0' => __('Privat24','pn'),
				'1' => __('Privat24 Visa','pn'),
			);
			$options['variant'] = array(
				'view' => 'select',
				'title' => __('Transaction type','pn'),
				'options' => $opt,
				'default' => intval(is_isset($data, 'variant')),
				'name' => 'variant',
				'work' => 'int',
			);			
			
			$statused = apply_filters('bid_status_list',array());
			if(!is_array($statused)){ $statused = array(); }

			$error_status = trim(is_isset($data, 'error_status'));
			if(!$error_status){ $error_status = 'realpay'; }
			$options[] = array(
				'view' => 'select',
				'title' => __('API status error','pn'),
				'options' => $statused,
				'default' => $error_status,
				'name' => 'error_status',
				'work' => 'input',
			);

			$options['bottom_title'] = array(
				'view' => 'h3',
				'title' => '',
				'submit' => __('Save','pn'),
				'colspan' => 2,
			);				
			
			$text = '
			<strong>CRON:</strong> <a href="'. get_merchant_link('ap_'. $this->name .'_cron') .'" target="_blank">'. get_merchant_link('ap_'. $this->name .'_cron') .'</a>
			';
			$options[] = array(
				'view' => 'textfield',
				'title' => '',
				'default' => $text,
			);			
			
			return $options;
		}		
		
		function paymerchants_settingtext(){
			$text = '| <span class="bred">'. __('Config file is not set up','pn') .'</span>';
			if(
				is_deffin($this->m_data,'AP_PRIVAT24_MERCHANT_ID_UAH') and is_deffin($this->m_data,'AP_PRIVAT24_MERCHANT_KEY_UAH') and is_deffin($this->m_data,'AP_PRIVAT24_MERCHANT_CARD_UAH')
				or is_deffin($this->m_data,'AP_PRIVAT24_MERCHANT_ID_USD') and is_deffin($this->m_data,'AP_PRIVAT24_MERCHANT_KEY_USD') and is_deffin($this->m_data,'AP_PRIVAT24_MERCHANT_CARD_USD')
				or is_deffin($this->m_data,'AP_PRIVAT24_MERCHANT_ID_EUR') and is_deffin($this->m_data,'AP_PRIVAT24_MERCHANT_KEY_EUR') and is_deffin($this->m_data,'AP_PRIVAT24_MERCHANT_CARD_EUR')
			){
				$text = '';
			}
			
			return $text;
		}

		function reserv_place_list($list){
			
			$purses = array(
				$this->name.'_1' => is_deffin($this->m_data,'AP_PRIVAT24_MERCHANT_CARD_UAH'),
				$this->name.'_2' => is_deffin($this->m_data,'AP_PRIVAT24_MERCHANT_CARD_USD'),
				$this->name.'_3' => is_deffin($this->m_data,'AP_PRIVAT24_MERCHANT_CARD_EUR'),
			);
			
			foreach($purses as $k => $v){
				$v = trim($v);
				if($v){
					$list[$k] = 'PrivatBank '. $v;
				}
			}
			
			return $list;						
		}

		function update_valut_autoreserv($ind, $key, $valut_id){
			
			if($ind == 0){
				if(strstr($key, $this->name.'_')){
				
					if($key == $this->name.'_1'){
						$merchant_id = is_deffin($this->m_data,'AP_PRIVAT24_MERCHANT_ID_UAH');
						$merchant_pass = is_deffin($this->m_data,'AP_PRIVAT24_MERCHANT_KEY_UAH');
						$card = is_deffin($this->m_data,'AP_PRIVAT24_MERCHANT_CARD_UAH');
					} elseif($key == $this->name.'_2'){
						$merchant_id = is_deffin($this->m_data,'AP_PRIVAT24_MERCHANT_ID_USD');
						$merchant_pass = is_deffin($this->m_data,'AP_PRIVAT24_MERCHANT_KEY_USD');
						$card = is_deffin($this->m_data,'AP_PRIVAT24_MERCHANT_CARD_USD');				
					} elseif($key == $this->name.'_3'){	
						$merchant_id = is_deffin($this->m_data,'AP_PRIVAT24_MERCHANT_ID_EUR');
						$merchant_pass = is_deffin($this->m_data,'AP_PRIVAT24_MERCHANT_KEY_EUR');
						$card = is_deffin($this->m_data,'AP_PRIVAT24_MERCHANT_CARD_EUR');				
					}

					if($merchant_id and $merchant_pass and $card){
						
						try{
					
							$oClass = new AP_PrivatBank($merchant_id,$merchant_pass);
							$res = $oClass->get_balans($card);
							if(is_array($res)){
								
								$rezerv = '-1';
								
								foreach($res as $pursename => $amount){
									if( $pursename == $card ){
										$rezerv = trim((string)$amount);
										break;
									}
								}
								
								if($rezerv != '-1'){
									pm_update_vr($valut_id, $rezerv);
								}						
								
							} 
						
						}
						catch (Exception $e)
						{
							
						} 				
						
						return 1;
					}
				
				}
			}
			
			return $ind;			
		}

		function update_naps_reserv($ind, $key, $naps_id, $naps){
			
			if($ind == 0){
				if(strstr($key, $this->name.'_')){
				
					if($key == $this->name.'_1'){
						$merchant_id = is_deffin($this->m_data,'AP_PRIVAT24_MERCHANT_ID_UAH');
						$merchant_pass = is_deffin($this->m_data,'AP_PRIVAT24_MERCHANT_KEY_UAH');
						$card = is_deffin($this->m_data,'AP_PRIVAT24_MERCHANT_CARD_UAH');
					} elseif($key == $this->name.'_2'){
						$merchant_id = is_deffin($this->m_data,'AP_PRIVAT24_MERCHANT_ID_USD');
						$merchant_pass = is_deffin($this->m_data,'AP_PRIVAT24_MERCHANT_KEY_USD');
						$card = is_deffin($this->m_data,'AP_PRIVAT24_MERCHANT_CARD_USD');				
					} elseif($key == $this->name.'_3'){	
						$merchant_id = is_deffin($this->m_data,'AP_PRIVAT24_MERCHANT_ID_EUR');
						$merchant_pass = is_deffin($this->m_data,'AP_PRIVAT24_MERCHANT_KEY_EUR');
						$card = is_deffin($this->m_data,'AP_PRIVAT24_MERCHANT_CARD_EUR');				
					}

					if($merchant_id and $merchant_pass and $card){
						
						try{
					
							$oClass = new AP_PrivatBank($merchant_id,$merchant_pass);
							$res = $oClass->get_balans($card);
							if(is_array($res)){
								
								$rezerv = '-1';
								
								foreach($res as $pursename => $amount){
									if( $pursename == $card ){
										$rezerv = trim((string)$amount);
										break;
									}
								}
								
								if($rezerv != '-1'){
									pm_update_nr($naps_id, $rezerv);
								}						
								
							} 
						
						}
						catch (Exception $e)
						{
							
						} 				
						
						return 1;
					}
				
				}
			}
			
			return $ind;			
		}		

		function paymerchant_action_bid($item, $place, $naps_data){
			global $wpdb;
			
			$item_id = is_isset($item,'id');
			if($item_id){

				$paymerch_data = get_paymerch_data($this->name);
				
				$au_filter = array(
					'error' => array(),
					'pay_error' => 0,
					'enable' => 1,
				);
				$au_filter = apply_filters('autopayment_filter', $au_filter, $this->name, $item, $place, $naps_data, $paymerch_data);
				
				$error = (array)$au_filter['error'];
				$pay_error = intval($au_filter['pay_error']);
				$trans_id = 0;
				
				if($au_filter['enable'] == 1){			
			
					$vtype = mb_strtoupper($item->vtype2);

					$enable = array('UAH','USD','EUR');
					if(!in_array($vtype, $enable)){
						$error[] = __('Wrong currency code','pn'); 
					}		
					
					$account = $item->account2;
					$account = mb_strtoupper($account);
					if (!preg_match("/^[0-9]{7,25}$/", $account, $matches )) {
						$error[] = __('Client wallet type does not match with currency code','pn');
					}		
					
					$sum = is_my_money(is_paymerch_sum($this->name, $item, $paymerch_data), 2);			
					
					$merchant_id = is_deffin($this->m_data,'AP_PRIVAT24_MERCHANT_ID_'.$vtype);
					$merchant_pass = is_deffin($this->m_data,'AP_PRIVAT24_MERCHANT_KEY_'.$vtype);
					$merchant_card = is_deffin($this->m_data,'AP_PRIVAT24_MERCHANT_CARD_'.$vtype);
					
					if(!$merchant_id or !$merchant_pass){
						$error[] = 'Error interfaice';
					}
					
					$variant = intval(is_isset($paymerch_data, 'variant'));
					if($variant == 1){
						$fio = array($item->last_name, $item->first_name, $item->second_name);
						$fio = array_unique($fio);
						$fio_str = trim(join(' ',$fio));
						if(!$fio_str){
							$error[] = 'Error FIO';
						}						
					}
					
					if(count($error) == 0){

						$result = update_bids_meta($item->id, 'ap_status', 1);
						update_bids_meta($item->id, 'ap_status_date', current_time('timestamp'));				
						if($result){				
					
							$notice = get_text_paymerch($this->name, $item);
							if(!$notice){ $notice = sprintf(__('ID order %s','pn'), $item->id); }
							$notice = trim(pn_maxf($notice,150));
						
							try {
						
								$oClass = new AP_PrivatBank($merchant_id,$merchant_pass);
								if($variant == 0){
									$res = $oClass->make_order($item_id, $account, $sum, $vtype, $notice);
								} else {
									$res = $oClass->make_order_visa($item_id, $account, $sum, $vtype, $notice, $fio_str);
								}
								if($res['error'] == 1){
									$error[] = __('Payment error','pn');
									$pay_error = 1;
								} else {
									$trans_id = $res['id'];
								}
							
							}
							catch (Exception $e)
							{
								$error[] = $e;
								$pay_error = 1;
							}

						} else {
							$error[] = 'Database error';
						}						
									
					}
					
					if(count($error) > 0){
						
						if($pay_error == 1){
							update_bids_meta($item->id, 'ap_status', 0);
							update_bids_meta($item->id, 'ap_status_date', current_time('timestamp'));
						}					
						
						$error_text = join('<br />',$error);
						
						do_action('paymerchant_error', $this->name, $error, $item_id, $place);
						
						if($place == 'admin'){
							pn_display_mess(__('Error!','pn') . $error_text);
						} else {
							send_paymerchant_error($item_id, $error_text);
						}
						
					} else {			
						
						$params = array(
							'soschet' => $merchant_card,
							'trans_out' => $trans_id,
						);
						the_merchant_bid_status('coldsuccess', $item_id, 'user', 1, $place, $params);						
						
						if($place == 'admin'){
							pn_display_mess(__('Payment is successfully created. Waiting for confirmation from Privat24.','pn'),__('Payment is successfully created. Waiting for confirmation from Privat24.','pn'),'true');
						} 
						
					}
					
				}	
			}			
			
		}

		function myaction_merchant_cron(){
		global $wpdb;
			
			$m_out = $this->name;
			
			$data = get_paymerch_data($this->name);
			$error_status = is_status_name(is_isset($data, 'error_status'));
			if(!$error_status){ $error_status = 'realpay'; }
			
			$en_currency = array('USD', 'EUR', 'UAH');
			$items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."bids WHERE status = 'coldsuccess' AND m_out='$m_out'");
			foreach($items as $item){
				
				$currency = mb_strtoupper($item->vtype2);
				if(in_array($currency, $en_currency)){
				
					$merchant_id = is_deffin($this->m_data,'AP_PRIVAT24_MERCHANT_ID_'.$currency);
					$merchant_key = is_deffin($this->m_data,'AP_PRIVAT24_MERCHANT_KEY_'.$currency);
				
					if($merchant_id and $merchant_key){
				
						try {
						
							$oClass = new AP_PrivatBank($merchant_id,$merchant_key);
							$res = $oClass->check_order($item->id);
							if(isset($res['status'])){
								if($res['status'] == 'ok'){
									$params = array(
										'soschet' => '',
										'trans_out' => '',
									);
									the_merchant_bid_status('success', $item->id, 'system', 1, 'site', $params);														
								} elseif($res['status'] != 'snd') {
									send_paymerchant_error($item->id, __('Your payment is declined','pn'));
									update_bids_meta($item->id, 'ap_status', 0);
									update_bids_meta($item->id, 'ap_status_date', current_time('timestamp'));
									$arr = array(
										'status'=> $error_status,
										'editdate'=> current_time('mysql'),
									);									
									$wpdb->update($wpdb->prefix.'bids', $arr, array('id'=>$item->id));
								}
							}
						
						}
						catch( Exception $e ) {
									
						}
					
					}
				
				}
				
			}
			
		}		
		
	}
}

new paymerchant_privatbank(__FILE__, 'Privat24');