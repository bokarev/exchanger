<?php 
/*
title: [ru_RU:]LiveCoin[:ru_RU][en_US:]LiveCoin[:en_US]
description: [ru_RU:]авто выплаты LiveCoin[:ru_RU][en_US:]LiveCoin automatic payouts[:en_US]
version: 1.2
*/

if(!class_exists('paymerchant_livecoin')){
	class paymerchant_livecoin extends AutoPayut_Premiumbox{

		function __construct($file, $title)
		{
			$map = array(
				'AP_LIVECOIN_BUTTON', 'AP_LIVECOIN_KEY', 'AP_LIVECOIN_SECRET', 
			);
			parent::__construct($file, $map, $title, 'AP_LIVECOIN_BUTTON');
			
			add_action('get_paymerchant_admin_options_'.$this->name, array($this, 'get_paymerchant_admin_options'), 10, 2);
			add_filter('paymerchants_settingtext_'.$this->name, array($this, 'paymerchants_settingtext'));
			add_filter('user_mailtemp',array($this,'user_mailtemp'));
			add_filter('mailtemp_tags_livecoin_paycoupon',array($this,'mailtemp_tags_paycoupon'));
			add_filter('reserv_place_list',array($this,'reserv_place_list'));
			add_filter('update_valut_autoreserv', array($this,'update_valut_autoreserv'), 10, 3);
			add_filter('update_naps_reserv', array($this,'update_naps_reserv'), 10, 4);
			add_action('paymerchant_action_bid_'.$this->name, array($this,'paymerchant_action_bid'),99,3);
		}

		function get_paymerchant_admin_options($options, $data){
			
			if(isset($options['checkpay'])){
				unset($options['checkpay']);
			}
			if(isset($options['bottom_title'])){
				unset($options['bottom_title']);
			}			
			
			$noptions = array();
			foreach($options as $key => $val){
				$noptions[$key] = $val;
				if($key == 'note'){
					$noptions[] = array(
						'view' => 'warning',
						'default' => sprintf(__('Use only latin symbols in payment notes. Maximum: %s characters.','pn'), 100),
					);						
				}
			}

			$opt = array(
				'0' => __('to Coupon','pn'),
				'1' => __('to Crypto currency','pn'),
				'2' => __('to Payeer','pn'),
				'3' => __('to Capitalist','pn'),
				'4' => __('to Bank card','pn'),
				'5' => __('to Okpay','pn'),
				'6' => __('to Perfect Money','pn'),
			);
			$noptions[] = array(
				'view' => 'select',
				'title' => __('Transaction type','pn'),
				'options' => $opt,
				'default' => intval(is_isset($data, 'variant')),
				'name' => 'variant',
				'work' => 'int',
			);			
			
			$noptions['bottom_title'] = array(
				'view' => 'h3',
				'title' => '',
				'submit' => __('Save','pn'),
				'colspan' => 2,
			);			
			
			return $noptions;
		}		
		
		function paymerchants_settingtext(){
			$text = '| <span class="bred">'. __('Config file is not set up','pn') .'</span>';
			if(
				is_deffin($this->m_data,'AP_LIVECOIN_KEY') 
				and is_deffin($this->m_data,'AP_LIVECOIN_SECRET')  
			){
				$text = '';
			}
			
			return $text;
		}
		
		function user_mailtemp($places_admin){
			
			$places_admin['livecoin_paycoupon'] = sprintf(__('%s automatic payout','pn'), 'LiveCoin');
			
			return $places_admin;
		}

		function mailtemp_tags_paycoupon($tags){
			
			$tags['id'] = __('Coupon code','pn');
			$tags['bid_id'] = __('ID Order','pn');
			
			return $tags;
		}		

		function reserv_place_list($list){
			
			$purses = array(
				$this->name.'_1' => 'USD',
				$this->name.'_2' => 'EUR',
				$this->name.'_3' => 'RUR',
				$this->name.'_4' => 'BTC',
				$this->name.'_5' => 'LTC',
				$this->name.'_6' => 'EMC',
				$this->name.'_7' => 'DASH',
				$this->name.'_8' => 'DOGE',
				$this->name.'_9' => 'MONA',
				$this->name.'_10' => 'PPC',
				$this->name.'_11' => 'NMC',
				$this->name.'_12' => 'CURE',
				$this->name.'_13' => 'ETH',
			);
			
			foreach($purses as $k => $v){
				$v = trim($v);
				if($v){
					$list[$k] = 'LiveCoin '. $v;
				}
			}
			
			return $list;									
		}

		function update_valut_autoreserv($ind, $key, $valut_id){
			if($ind == 0){
				if(strstr($key, $this->name.'_')){				
					$purses = array(
						$this->name.'_1' => 'USD',
						$this->name.'_2' => 'EUR',
						$this->name.'_3' => 'RUR',
						$this->name.'_4' => 'BTC',
						$this->name.'_5' => 'LTC',
						$this->name.'_6' => 'EMC',
						$this->name.'_7' => 'DASH',
						$this->name.'_8' => 'DOGE',
						$this->name.'_9' => 'MONA',
						$this->name.'_10' => 'PPC',
						$this->name.'_11' => 'NMC',
						$this->name.'_12' => 'CURE',
						$this->name.'_13' => 'ETH',
					);					
					$purse = trim(is_isset($purses, $key));
					if($purse){						
						try{					
							$oClass = new AP_LiveCoin(is_deffin($this->m_data,'AP_LIVECOIN_KEY'),is_deffin($this->m_data,'AP_LIVECOIN_SECRET'));
							$res = $oClass->get_balans();
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
			$ind = intval($ind);
			if(!$ind){
				if(strstr($key, $this->name.'_')){
					$purses = array(
						$this->name.'_1' => 'USD',
						$this->name.'_2' => 'EUR',
						$this->name.'_3' => 'RUR',
						$this->name.'_4' => 'BTC',
						$this->name.'_5' => 'LTC',
						$this->name.'_6' => 'EMC',
						$this->name.'_7' => 'DASH',
						$this->name.'_8' => 'DOGE',
						$this->name.'_9' => 'MONA',
						$this->name.'_10' => 'PPC',
						$this->name.'_11' => 'NMC',
						$this->name.'_12' => 'CURE',
						$this->name.'_13' => 'ETH',					
					);
					$purse = trim(is_isset($purses, $key));
					if($purse){						
						try{
							$oClass = new AP_LiveCoin(is_deffin($this->m_data,'AP_LIVECOIN_KEY'),is_deffin($this->m_data,'AP_LIVECOIN_SECRET'));
							$res = $oClass->get_balans();
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
				$coupon = '';			
			
				if($au_filter['enable'] == 1){			
			
					$variant = intval(is_isset($paymerch_data, 'variant'));
				
					$vtype = mb_strtoupper($item->vtype2);
					$vtype = str_replace('RUB','RUR',$vtype);
					
					$enable = array('USD','EUR','RUR','BTC','LTC','EMC','DASH','DOGE','MONA','PPC','NMC','CURE','ETH');		
					if(!in_array($vtype, $enable)){
						$error[] = __('Wrong currency code','pn'); 
					}	
					
					$account = $item->account2;
					if (!is_email($account) and $variant == 0) {
						$error[] = __('Client wallet type does not match with currency code','pn');
					}				
					
					$sum = is_paymerch_sum($this->name, $item, $paymerch_data);
					
					$two = array('USD','EUR','RUR');
					if(in_array($vtype, $two)){
						$sum = is_my_money($sum, 2);
					} else {
						$sum = is_my_money($sum);
					}
					
					if(count($error) == 0){

						$result = update_bids_meta($item->id, 'ap_status', 1);
						update_bids_meta($item->id, 'ap_status_date', current_time('timestamp'));				
						if($result){				
					
							$notice = get_text_paymerch($this->name, $item);
							if(!$notice){ $notice = sprintf(__('ID order %s','pn'), $item->id); }
							$notice = trim(substr($notice,0,100));
						
							try{
						
								$class = new AP_LiveCoin(is_deffin($this->m_data,'AP_LIVECOIN_KEY'),is_deffin($this->m_data,'AP_LIVECOIN_SECRET'));
								if($variant == 0){
									$coupon = $class->make_voucher($sum, $vtype, $notice);
									if(!$coupon){
										$error[] = __('Payout error','pn');
										$pay_error = 1;
									} 	
								} else {
									if($variant == 1){ // '1' => __('Crypto currency','pn'),
										$data_params = array(
											'wallet' => $account,
										);
										$method = 'coin';
									} elseif($variant == 2){ // '2' => __('Payeer','pn'),
										$data_params = array(
											'wallet' => $account,
										);			
										$method = 'payeer';
									} elseif($variant == 3){ // '3' => __('Capitalist','pn'),
										$data_params = array(
											'wallet' => $account,
										);		
										$method = 'capitalist';
									} elseif($variant == 4){ // '4' => __('Bank card','pn'),
										$unmetas = @unserialize($item->unmetas);
										$data_params = array(
											'card_number' => is_isset($unmetas,'card_number'),
											'expiry_month' => is_isset($unmetas,'expiry_month'),
											'expiry_year' => is_isset($unmetas,'expiry_year'),
										);	
										$method = 'card';
									} elseif($variant == 5){ // '5' => __('Okpay','pn'),
										$data_params = array(
											'wallet' => $account,
										);	
										$method = 'okpay';
									} elseif($variant == 6){ // '6' => __('Perfectmoney','pn'),	
										$data_params = array(
											'wallet' => $account,
										);	
										$method = 'perfectmoney';
									}
									
									$res = $class->get_transfer($method, $sum, $vtype, $data_params);
									$trans_id = intval(is_isset($res,'id'));
									if(!$trans_id){
										$error[] = __('Payout error','pn');
										$pay_error = 1;
									} 									
									
								}
							}
							catch (Exception $e)
							{
								$error[] = $e->getMessage();
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
						
						if($variant == 0){
						
							$mailtemp = get_option('mailtemp');
							if(isset($mailtemp['livecoin_paycoupon'])){
								$data = $mailtemp['livecoin_paycoupon'];
								if($data['send'] == 1){
									
									$ot_mail = is_email($data['mail']);
									$ot_name = pn_strip_input($data['name']);
												
									$subject = pn_strip_input(ctv_ml($data['title']));
									$sitename = pn_strip_input(get_bloginfo('sitename'));			
									$html = pn_strip_text(ctv_ml($data['text']));
												
									if($account){
													
										$subject = str_replace('[sitename]', $sitename ,$subject);
										$subject = str_replace('[id]', $coupon ,$subject);
										$subject = str_replace('[bid_id]', $item_id ,$subject);
													
										$html = str_replace('[sitename]', $sitename ,$html);
										$html = str_replace('[id]', $coupon ,$html);
										$html = str_replace('[bid_id]', $item_id ,$html);
										$html = apply_filters('comment_text',$html);
												
										pn_mail($account, $subject, $html, $ot_name, $ot_mail);			
	
									}
								}
							}

							do_action('merchant_create_coupon', $coupon, $item, 'livecoin', $place);
						}						
						
						$params = array(
							'soschet' => '',
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

new paymerchant_livecoin(__FILE__, 'LiveCoin');