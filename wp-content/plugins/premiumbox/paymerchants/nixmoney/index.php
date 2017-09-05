<?php
/*
title: [ru_RU:]NixMoney[:ru_RU][en_US:]NixMoney[:en_US]
description: [ru_RU:]авто выплаты NixMoney[:ru_RU][en_US:]NixMoney automatic payouts[:en_US]
version: 1.2
*/

if(!class_exists('paymerchant_nixmoney')){
	class paymerchant_nixmoney extends AutoPayut_Premiumbox{
		function __construct($file, $title)
		{
			$map = array(
				'AP_NIXMONEY_BUTTON', 'AP_NIXMONEY_ACCOUNT', 'AP_NIXMONEY_PASSWORD', 
				'AP_NIXMONEY_USD', 'AP_NIXMONEY_EUR', 'AP_NIXMONEY_BTC',
				'AP_NIXMONEY_LTC', 'AP_NIXMONEY_PPC', 'AP_NIXMONEY_FTC',
				'AP_NIXMONEY_CRT', 'AP_NIXMONEY_GBC', 'AP_NIXMONEY_DOGE',
			);
			parent::__construct($file, $map, $title, 'AP_NIXMONEY_BUTTON');	
			
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
						'default' => sprintf(__('Use only latin symbols in payment notes. Maximum: %s characters.','pn'), 100),
					);						
				}
			}		
			
			return $noptions;
		}	
		
		function paymerchants_settingtext(){
			$text = '| <span class="bred">'. __('Config file is not set up','pn') .'</span>';
			if(
				is_deffin($this->m_data,'AP_NIXMONEY_ACCOUNT') 
				and is_deffin($this->m_data,'AP_NIXMONEY_PASSWORD')  
			){
				$text = '';
			}
			
			return $text;
		}

		function reserv_place_list($list){
			
			$purses = array(
				$this->name.'_1' => is_deffin($this->m_data,'AP_NIXMONEY_USD'),
				$this->name.'_2' => is_deffin($this->m_data,'AP_NIXMONEY_EUR'),
				$this->name.'_3' => is_deffin($this->m_data,'AP_NIXMONEY_BTC'),
				$this->name.'_4' => is_deffin($this->m_data,'AP_NIXMONEY_LTC'),
				$this->name.'_5' => is_deffin($this->m_data,'AP_NIXMONEY_PPC'),
				$this->name.'_6' => is_deffin($this->m_data,'AP_NIXMONEY_FTC'),
				$this->name.'_7' => is_deffin($this->m_data,'AP_NIXMONEY_CRT'),
				$this->name.'_8' => is_deffin($this->m_data,'AP_NIXMONEY_GBC'),
				$this->name.'_9' => is_deffin($this->m_data,'AP_NIXMONEY_DOGE'),
			);
			
			foreach($purses as $k => $v){
				$v = trim($v);
				if($v){
					$list[$k] = 'NixMoney '. $v;
				}
			}
			
			return $list;			
		}

		function update_valut_autoreserv($ind, $key, $valut_id){
			
			if($ind == 0){
				if(strstr($key, $this->name.'_')){
				
					$purses = array(
						$this->name.'_1' => is_deffin($this->m_data,'AP_NIXMONEY_USD'),
						$this->name.'_2' => is_deffin($this->m_data,'AP_NIXMONEY_EUR'),
						$this->name.'_3' => is_deffin($this->m_data,'AP_NIXMONEY_BTC'),
						$this->name.'_4' => is_deffin($this->m_data,'AP_NIXMONEY_LTC'),
						$this->name.'_5' => is_deffin($this->m_data,'AP_NIXMONEY_PPC'),
						$this->name.'_6' => is_deffin($this->m_data,'AP_NIXMONEY_FTC'),
						$this->name.'_7' => is_deffin($this->m_data,'AP_NIXMONEY_CRT'),
						$this->name.'_8' => is_deffin($this->m_data,'AP_NIXMONEY_GBC'),
						$this->name.'_9' => is_deffin($this->m_data,'AP_NIXMONEY_DOGE'),
					);
					
					$purse = trim(is_isset($purses, $key));
					if($purse){
						
						try{
					
							$oClass = new AP_NixMoney( is_deffin($this->m_data,'AP_NIXMONEY_ACCOUNT'), is_deffin($this->m_data,'AP_NIXMONEY_PASSWORD') );
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
						$this->name.'_1' => is_deffin($this->m_data,'AP_NIXMONEY_USD'),
						$this->name.'_2' => is_deffin($this->m_data,'AP_NIXMONEY_EUR'),
						$this->name.'_3' => is_deffin($this->m_data,'AP_NIXMONEY_BTC'),
						$this->name.'_4' => is_deffin($this->m_data,'AP_NIXMONEY_LTC'),
						$this->name.'_5' => is_deffin($this->m_data,'AP_NIXMONEY_PPC'),
						$this->name.'_6' => is_deffin($this->m_data,'AP_NIXMONEY_FTC'),
						$this->name.'_7' => is_deffin($this->m_data,'AP_NIXMONEY_CRT'),
						$this->name.'_8' => is_deffin($this->m_data,'AP_NIXMONEY_GBC'),
						$this->name.'_9' => is_deffin($this->m_data,'AP_NIXMONEY_DOGE'),
					);
					
					$purse = trim(is_isset($purses, $key));
					if($purse){
						
						try{
					
							$oClass = new AP_NixMoney( is_deffin($this->m_data,'AP_NIXMONEY_ACCOUNT'), is_deffin($this->m_data,'AP_NIXMONEY_PASSWORD') );
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
					
					$enable = array('USD','EUR','BTC','LTC','PPC','FTC','CRT','GBC','DOGE');
					if(!in_array($vtype, $enable)){
						$error[] = __('Wrong currency code','pn'); 
					}						
						
					$account = $item->account2;
					$account = mb_strtoupper($account);				
					
					$site_purse = '';
					if($vtype == 'USD'){
						$site_purse = is_deffin($this->m_data,'AP_NIXMONEY_USD');
					} elseif($vtype == 'EUR'){
						$site_purse = is_deffin($this->m_data,'AP_NIXMONEY_EUR');
					} elseif($vtype == 'BTC'){
						$site_purse = is_deffin($this->m_data,'AP_NIXMONEY_BTC');
					} elseif($vtype == 'LTC'){
						$site_purse = is_deffin($this->m_data,'AP_NIXMONEY_LTC');
					} elseif($vtype == 'PPC'){
						$site_purse = is_deffin($this->m_data,'AP_NIXMONEY_PPC');
					} elseif($vtype == 'FTC'){
						$site_purse = is_deffin($this->m_data,'AP_NIXMONEY_FTC');
					} elseif($vtype == 'CRT'){
						$site_purse = is_deffin($this->m_data,'AP_NIXMONEY_CRT');
					} elseif($vtype == 'GBC'){
						$site_purse = is_deffin($this->m_data,'AP_NIXMONEY_GBC');
					} elseif($vtype == 'DOGE'){
						$site_purse = is_deffin($this->m_data,'AP_NIXMONEY_DOGE');					
					} 
					
					$site_purse = mb_strtoupper($site_purse);
					if (!$site_purse) {
						$error[] = __('Your account set on website does not match with currency code','pn');
					}			

					$sum = is_paymerch_sum($this->name, $item, $paymerch_data);
					
					$two = array('USD','EUR');
					if(in_array($vtype, $two)){
						$sum = is_my_money($sum, 2);
					} else {
						$sum = is_my_money($sum);
					}
					
					$check_history = intval(is_isset($paymerch_data, 'checkpay'));
					if($check_history == 1){
					
						try {
							$class = new AP_NixMoney( is_deffin($this->m_data,'AP_NIXMONEY_ACCOUNT'), is_deffin($this->m_data,'AP_NIXMONEY_PASSWORD') );
							$hres = $class->getHistory( date( 'd.m.Y', strtotime( '-2 day' ) ), date( 'd.m.Y', strtotime( '+2 day' ) ), 'paymentid', 'rashod' );
							if($hres['error'] == 0){
								$histories = $hres['responce'];
								if(isset($histories[$item_id])){
									$error[] = sprintf(__('Payment ID %s has already been paid','pn'), $item_id);	
								} 
							} else {
								$error[] = __('Failed to retrieve payment history','pn');
							}
						}
						catch( Exception $e ) {
							$error[] = $e->getMessage();
						}		
					
					}					
					
					if(count($error) == 0){

						$result = update_bids_meta($item->id, 'ap_status', 1);
						update_bids_meta($item->id, 'ap_status_date', current_time('timestamp'));		
						if($result){
					
							$notice = get_text_paymerch($this->name, $item);
							if(!$notice){ $notice = sprintf(__('ID order %s','pn'), $item->id); }
							$notice = trim(pn_maxf($notice,100));
						
							try{
						
								$oClass = new AP_NixMoney( is_deffin($this->m_data,'AP_NIXMONEY_ACCOUNT'), is_deffin($this->m_data,'AP_NIXMONEY_PASSWORD') );
								$res = $oClass->SendMoney($site_purse, $account, $sum, $item_id, $notice);
								if($res['error'] == 1){
									$error[] = __('Payout error','pn');
									$pay_error = 1;
								} else {
									$trans_id = $res['trans_id'];
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
							'soschet' => $site_purse,
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

new paymerchant_nixmoney(__FILE__, 'NixMoney');