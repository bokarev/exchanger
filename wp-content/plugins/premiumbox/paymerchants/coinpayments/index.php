<?php
/*
title: [ru_RU:]Coinpayments[:ru_RU][en_US:]Coinpayments[:en_US]
description: [ru_RU:]авто выплаты Coinpayments[:ru_RU][en_US:]Coinpayments automatic payouts[:en_US]
version: 1.2
*/

if(!class_exists('paymerchant_coinpayments')){
	class paymerchant_coinpayments extends AutoPayut_Premiumbox{

		function __construct($file, $title)
		{
			$map = array(
				'BUTTON', 'PUBLIC_KEY', 'PRIVAT_KEY',
				'BTC','LTC','XRP','DASH','DOGE','ETC','ETH','NMC','PPC','USDT','WAVES','XMR','ZEC',
			);
			parent::__construct($file, $map, $title, 'BUTTON');
			
			add_action('get_paymerchant_admin_options_'.$this->name, array($this, 'get_paymerchant_admin_options'), 10, 2);			
			add_filter('paymerchants_settingtext_'.$this->name, array($this, 'paymerchants_settingtext'));
			add_filter('reserv_place_list',array($this,'reserv_place_list'));
			add_filter('update_valut_autoreserv', array($this,'update_valut_autoreserv'), 10, 3);
			add_filter('update_naps_reserv', array($this,'update_naps_reserv'), 10, 4);
			add_action('paymerchant_action_bid_'.$this->name, array($this,'paymerchant_action_bid'),99,3);
		}	
		
		function get_paymerchant_admin_options($options, $data){
			
			if(isset($options['note'])){
				unset($options['note']);
			}			
			if(isset($options['checkpay'])){
				unset($options['checkpay']);
			}														
			
			return $options;
		}				

		function paymerchants_settingtext(){
			$text = '| <span class="bred">'. __('Config file is not set up','pn') .'</span>';
			if(
				is_deffin($this->m_data,'PUBLIC_KEY') and is_deffin($this->m_data,'PRIVAT_KEY')  
			){
				$text = '';
			}
			
			return $text;
		}

		function reserv_place_list($list){
			
			$keys = array('BTC','LTC','XRP','DASH','DOGE','ETC','ETH','NMC','PPC','USDT','WAVES','XMR','ZEC');
			
			$r = 0;
			foreach($keys as $key){ $r++;
				$key = trim($key);
				if($key){
					$list[$this->name.'_'.$r] = 'Coinpayments '. $key .':'. is_deffin($this->m_data, $key);
				}
			}
			
			return $list;						
		}

		function update_valut_autoreserv($ind, $key, $valut_id){
			
			if($ind == 0){
				if(strstr($key, $this->name.'_')){
				
					$keys = array('BTC','LTC','XRP','DASH','DOGE','ETC','ETH','NMC','PPC','USDT','WAVES','XMR','ZEC');
					$purses = array();
					$r = 0;
					foreach($keys as $keysv){ $r++;
						$keysv = trim($keysv);
						if($keysv){
							$purses[$this->name.'_'.$r] = $keysv;
						}
					}				
	
					$api = trim(is_isset($purses, $key));
					if($api){
						
						try {
							$PUBLIC_KEY = is_deffin($this->m_data,'PUBLIC_KEY');
							$PRIVAT_KEY = is_deffin($this->m_data,'PRIVAT_KEY');
							
							$class = new AP_CoinPaymentsAPI($PRIVAT_KEY, $PUBLIC_KEY);
							$result = $class->get_balans();
					
							$rezerv = '-1';
					
							if(isset($result['error']) and $result['error'] == 'ok'){
								$res = $result['result'];
								if(is_array($res)){
									foreach($res as $k => $v){
										if($api == $k){
											$rezerv = $res[$k]['balancef'];
										}
									}
								}
							}	
							
							if($rezerv != '-1'){
								pm_update_vr($valut_id, $rezerv);
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
				
					$keys = array('BTC','LTC','XRP','DASH','DOGE','ETC','ETH','NMC','PPC','USDT','WAVES','XMR','ZEC');
					$purses = array();
					$r = 0;
					foreach($keys as $keysv){ $r++;
						$keysv = trim($keysv);
						if($keysv){
							$purses[$this->name.'_'.$r] = $keysv;
						}
					}				
	
					$api = trim(is_isset($purses, $key));
					if($api){
						
						try {
							$PUBLIC_KEY = is_deffin($this->m_data,'PUBLIC_KEY');
							$PRIVAT_KEY = is_deffin($this->m_data,'PRIVAT_KEY');
							
							$class = new AP_CoinPaymentsAPI($PRIVAT_KEY, $PUBLIC_KEY);
							$result = $class->get_balans();
					
							$rezerv = '-1';
					
							if(isset($result['error']) and $result['error'] == 'ok'){
								$res = $result['result'];
								if(is_array($res)){
									foreach($res as $k => $v){
										if($api == $k){
											$rezerv = $res[$k]['balancef'];
										}
									}
								}
							}	
							
							if($rezerv != '-1'){
								pm_update_nr($naps_id, $rezerv);
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
					
					$enable = array('BTC','LTC','XRP','DASH','DOGE','ETC','ETH','NMC','PPC','USDT','WAVES','XMR','ZEC');		
					if(!in_array($vtype, $enable)){
						$error[] = __('Wrong currency code','pn'); 
					}					
					
					$account = $item->account2;
					if (!$account) {
						$error[] = __('Client wallet type does not match with currency code','pn');
					}				
					
					$sum = is_my_money(is_paymerch_sum($this->name, $item, $paymerch_data));
					$minsum = '0.0004';
					if($sum < $minsum){
						$error[] = sprintf(__('Minimum payment amount is %s','pn'), $minsum);
					}		
					
					$PUBLIC_KEY = is_deffin($this->m_data,'PUBLIC_KEY');
					$PRIVAT_KEY = is_deffin($this->m_data,'PRIVAT_KEY');
					
					if(count($error) == 0){

						$result = update_bids_meta($item->id, 'ap_status', 1);
						update_bids_meta($item->id, 'ap_status_date', current_time('timestamp'));				
						if($result){				
							try{
								$class = new AP_CoinPaymentsAPI($PRIVAT_KEY, $PUBLIC_KEY);
								$auto_confirm = 1;
								$result = $class->get_transfer($sum, $vtype, $account, $auto_confirm);
								if(isset($result['result']) and isset($result['result']['id'])){
									$trans_id = $result['result']['id'];
								} else {
									$error[] = $result['error'];
									$pay_error = 1;
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
							'trans_out' => $trans_id,
						);
						the_merchant_bid_status('success', $item_id, 'user', 1, $place, $params);					
						 
						if($place == 'admin'){
							pn_display_mess(__('Automatic payout is done','pn'),__('Automatic payout is done','pn'),'true');
						} 
					}
				
				}
			}			
		}				
	}
}
new paymerchant_coinpayments(__FILE__, 'Coinpayments');