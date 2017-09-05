<?php 
/*
title: [ru_RU:]ExMo[:ru_RU][en_US:]ExMo[:en_US]
description: [ru_RU:]авто выплаты ExMo[:ru_RU][en_US:]ExMo automatic payouts[:en_US]
version: 1.2
*/

if(!class_exists('paymerchant_exmo')){
	class paymerchant_exmo extends AutoPayut_Premiumbox{

		function __construct($file, $title)
		{
			$map = array(
				'BUTTON', 'KEY', 'SECRET', 
			);
			parent::__construct($file, $map, $title, 'BUTTON');
			
			add_action('get_paymerchant_admin_options_'.$this->name, array($this, 'get_paymerchant_admin_options'), 10, 2);
			add_filter('paymerchants_settingtext_'.$this->name, array($this, 'paymerchants_settingtext'));
			add_filter('user_mailtemp',array($this,'user_mailtemp'));
			add_filter('mailtemp_tags_exmo_paycoupon',array($this,'mailtemp_tags_paycoupon'));
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
				is_deffin($this->m_data,'KEY') 
				and is_deffin($this->m_data,'SECRET')  
			){
				$text = '';
			}
			
			return $text;
		}
		
		function user_mailtemp($places_admin){
			
			$places_admin['exmo_paycoupon'] = sprintf(__('%s automatic payout','pn'), 'ExMo');
			
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
				$this->name.'_3' => 'RUB',
				$this->name.'_4' => 'BTC',
				$this->name.'_5' => 'DOGE',
				$this->name.'_6' => 'DASH',
				$this->name.'_7' => 'ETH',				
				$this->name.'_8' => 'LTC',
			);
			
			foreach($purses as $k => $v){
				$v = trim($v);
				if($v){
					$list[$k] = 'ExMo '. $v;
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
						$this->name.'_3' => 'RUB',
						$this->name.'_4' => 'BTC',
						$this->name.'_5' => 'DOGE',
						$this->name.'_6' => 'DASH',
						$this->name.'_7' => 'ETH',				
						$this->name.'_8' => 'LTC',
					);
					$purse = trim(is_isset($purses, $key));
					if($purse){						
						try{					
							$oClass = new AP_ExmoApi(is_deffin($this->m_data,'KEY'),is_deffin($this->m_data,'SECRET'));
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
						$this->name.'_3' => 'RUB',
						$this->name.'_4' => 'BTC',
						$this->name.'_5' => 'DOGE',
						$this->name.'_6' => 'DASH',
						$this->name.'_7' => 'ETH',				
						$this->name.'_8' => 'LTC',						
					);
					$purse = trim(is_isset($purses, $key));
					if($purse){						
						try{	
							$oClass = new AP_ExmoApi(is_deffin($this->m_data,'KEY'),is_deffin($this->m_data,'SECRET'));
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
			
					$vtype = mb_strtoupper($item->vtype2);
					$vtype = str_replace('RUR','RUB',$vtype);
					
					$enable = array('USD','EUR','RUB','BTC','DOGE','DASH','ETH','LTC');		
					if(!in_array($vtype, $enable)){
						$error[] = __('Wrong currency code','pn'); 
					}						
						
					$account = $item->account2;
					if (!is_email($account)) {
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
					
							try{
								$res = new AP_ExmoApi(is_deffin($this->m_data,'KEY'),is_deffin($this->m_data,'SECRET'));
								$res = $res->make_voucher($sum, $vtype);
								if($res['error'] == 1){
									$error[] = __('Payout error','pn');
									$pay_error = 1;
								} else {
									$coupon = $res['coupon'];
									$trans_id = $res['trans_id'];
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
						
						$mailtemp = get_option('mailtemp');
						if(isset($mailtemp['exmo_paycoupon'])){
							$data = $mailtemp['exmo_paycoupon'];
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
						
						do_action('merchant_create_coupon', $coupon, $item, 'exmo', $place);
						
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

new paymerchant_exmo(__FILE__, 'ExMo');