<?php
/*
title: [ru_RU:]OkPay[:ru_RU][en_US:]OkPay[:en_US]
description: [ru_RU:]авто выплаты OkPay[:ru_RU][en_US:]OkPay automatic payouts[:en_US]
version: 1.2
*/

if(!class_exists('paymerchant_okpay')){
	class paymerchant_okpay extends AutoPayut_Premiumbox{
		function __construct($file, $title)
		{
			$map = array(
				'AP_OKPAY_BUTTON', 'AP_OKPAY_ACCOUNT', 'AP_OKPAY_API_KEY', 
			);
			parent::__construct($file, $map, $title, 'AP_OKPAY_BUTTON');	
			
			add_action('get_paymerchant_admin_options_'.$this->name, array($this, 'get_paymerchant_admin_options'), 10, 2);
			add_filter('paymerchants_settingtext_'.$this->name, array($this, 'paymerchants_settingtext'));
			add_filter('reserv_place_list',array($this,'reserv_place_list'));
			add_filter('update_valut_autoreserv', array($this,'update_valut_autoreserv'), 10, 3);
			add_filter('update_naps_reserv', array($this,'update_naps_reserv'), 10, 4);
			add_action('paymerchant_action_bid_'.$this->name, array($this,'paymerchant_action_bid'),99,3);
		}

		function get_paymerchant_admin_options($options, $data){
			
			$noptions = array();
			foreach($options as $key => $val){
				$noptions[$key] = $val;
				if($key == 'note'){
					$noptions['warning'] = array(
						'view' => 'warning',
						'default' => sprintf(__('Use only latin symbols in payment notes. Maximum: %s characters.','pn'), 50),
					);						
				}
			}		
			
			return $noptions;
		}	
		
		function paymerchants_settingtext(){
			$text = '| <span class="bred">'. __('Config file is not set up','pn') .'</span>';
			if(
				is_deffin($this->m_data,'AP_OKPAY_ACCOUNT') 
				and is_deffin($this->m_data,'AP_OKPAY_API_KEY')  
			){
				$text = '';
			}
			
			return $text;
		}

		function reserv_place_list($list){
			
			$purses = array(
				$this->name.'_1' => 'EUR',
				$this->name.'_2' => 'USD',
				$this->name.'_3' => 'RUB',
			);
			
			foreach($purses as $k => $v){
				$v = trim($v);
				if($v){
					$list[$k] = 'OkPay '. $v;
				}
			}
			
			return $list;						
		}

		function update_valut_autoreserv($ind, $key, $valut_id){
			
			if($ind == 0){
				if(strstr($key, $this->name.'_')){
				
					$purses = array(
						$this->name.'_1' => 'EUR',
						$this->name.'_2' => 'USD',
						$this->name.'_3' => 'RUB',
					);
					
					$purse = trim(is_isset($purses, $key));
					if($purse){
						
						try{
					
							$oClass = new AP_OKPay( is_deffin($this->m_data,'AP_OKPAY_ACCOUNT'), is_deffin($this->m_data,'AP_OKPAY_API_KEY') );
							$res = $oClass->getBalans();
							if(is_array($res)){
								
								$rezerv = '-1';
								
								foreach($res as $pursename => $amount){
									if( $pursename == $purse ){
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
				
					$purses = array(
						$this->name.'_1' => 'EUR',
						$this->name.'_2' => 'USD',
						$this->name.'_3' => 'RUB',
					);
					
					$purse = trim(is_isset($purses, $key));
					if($purse){
						
						try{
					
							$oClass = new AP_OKPay( is_deffin($this->m_data,'AP_OKPAY_ACCOUNT'), is_deffin($this->m_data,'AP_OKPAY_API_KEY') );
							$res = $oClass->getBalans();
							if(is_array($res)){
								
								$rezerv = '-1';
								
								foreach($res as $pursename => $amount){
									if( $pursename == $purse ){
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
					$vtype = str_replace('RUR','RUB',$vtype);
					
					$enable = array('EUR','USD','RUB');
					if(!in_array($vtype, $enable)){
						$error[] = __('Wrong currency code','pn'); 
					}	
						
					$account = $item->account2;
					$account = mb_strtoupper($account);
					if (!preg_match("/^OK[0-9]{0,20}$/", $account, $matches )) {
						$error[] = __('Client wallet type does not match with currency code','pn');
					}							

					$sum = is_my_money(is_paymerch_sum($this->name, $item, $paymerch_data), 2);		
					
					if(count($error) == 0){

						$result = update_bids_meta($item->id, 'ap_status', 1);
						update_bids_meta($item->id, 'ap_status_date', current_time('timestamp'));				
						if($result){				
					
							$notice = get_text_paymerch($this->name, $item);
							if(!$notice){ $notice = sprintf(__('ID order %s','pn'), $item->id); }
							$notice = trim(pn_maxf($notice,48));
						
							try{
						
								$oClass = new AP_OKPay( is_deffin($this->m_data,'AP_OKPAY_ACCOUNT'), is_deffin($this->m_data,'AP_OKPAY_API_KEY'));
								$res = $oClass->SendMoney($vtype, $account, $sum, $notice);
								if($res['error'] == 1){
									$error[] = __('Payout error','pn');
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
							'soschet' => is_deffin($this->m_data,'AP_OKPAY_ACCOUNT'),
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

new paymerchant_okpay(__FILE__, 'OkPay');