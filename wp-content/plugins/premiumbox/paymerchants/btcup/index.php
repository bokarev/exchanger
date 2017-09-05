<?php 
/*
title: [ru_RU:]BTC-UP[:ru_RU][en_US:]BTC-UP[:en_US]
description: [ru_RU:]авто выплаты BTC-UP[:ru_RU][en_US:]BTC-UP automatic payouts[:en_US]
version: 1.2
*/

if(!class_exists('paymerchant_btcup')){
	class paymerchant_btcup extends AutoPayut_Premiumbox{

		function __construct($file, $title)
		{
			$map = array(
				'BUTTON', 'KEY_COUPON', 'SECRET_COUPON',
				'KEY_INFO', 'SECRET_INFO', 'KEY_WITHDRAW', 'SECRET_WITHDRAW',
			);
			parent::__construct($file, $map, $title, 'BUTTON');
			
			add_action('get_paymerchant_admin_options_'.$this->name, array($this, 'get_paymerchant_admin_options'), 10, 2);
			add_filter('paymerchants_settingtext_'.$this->name, array($this, 'paymerchants_settingtext'));
			add_filter('user_mailtemp',array($this,'user_mailtemp'));
			add_filter('mailtemp_tags_btcup_paycoupon',array($this,'mailtemp_tags_paycoupon'));
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
					$noptions[] = array(
						'view' => 'warning',
						'default' => sprintf(__('Use only latin symbols in payment notes. Maximum: %s characters.','pn'), 100),
					);						
				}
			}

			if(isset($noptions['bottom_title'])){
				unset($noptions['bottom_title']);
			}
			if(isset($noptions['checkpay'])){
				unset($noptions['checkpay']);
			}				

			$noptions[] = array(
				'view' => 'select',
				'title' => __('Link coupon to users login','pn'),
				'options' => array('0' => __('No','pn'),'1' => __('Yes','pn')),
				'default' => intval(is_isset($data, 'bindlogin')),
				'name' => 'bindlogin',
				'work' => 'int',
			);
			
			$opt = array(
				'0' => __('Coupon','pn'),
				'1' => __('Trasnfer funds','pn'),
			);
			$noptions[] = array(
				'view' => 'select',
				'title' => __('Transaction type','pn'),
				'options' => $opt,
				'default' => intval(is_isset($data, 'variant')),
				'name' => 'variant',
				'work' => 'int',
			);
			$noptions[] = array(
				'view' => 'input',
				'title' => __('Coupon validity (in minutes)','pn'),
				'default' => intval(is_isset($data, 'lifetime')),
				'name' => 'lifetime',
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
			
			$text = '';
			return $text;
		}
		
		function user_mailtemp($places_admin){
			
			$places_admin['btcup_paycoupon'] = sprintf(__('%s automatic payout','pn'), 'BTC-UP');
			
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
				$this->name.'_2' => 'BTC',
				$this->name.'_3' => 'EMC',
				$this->name.'_4' => 'ETH',
				$this->name.'_5' => 'LTC',
			);
			
			foreach($purses as $k => $v){
				$v = trim($v);
				if($v){
					$list[$k] = 'BTC-UP '. $v;
				}
			}
			
			return $list;									
		}

		function update_valut_autoreserv($ind, $key, $valut_id){
			if($ind == 0){
				if(strstr($key, $this->name.'_')){				
					$purses = array(
						$this->name.'_1' => 'USD',
						$this->name.'_2' => 'BTC',
						$this->name.'_3' => 'EMC',
						$this->name.'_4' => 'ETH',
						$this->name.'_5' => 'LTC',
					);					
					$purse = trim(is_isset($purses, $key));
					if($purse){						
						try{					
							$oClass = new AP_BTCUP();
							$res = $oClass->get_balans('', is_deffin($this->m_data,'KEY_INFO'), is_deffin($this->m_data,'SECRET_INFO'));
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
						$this->name.'_2' => 'BTC',
						$this->name.'_3' => 'EMC',
						$this->name.'_4' => 'ETH',
						$this->name.'_5' => 'LTC',
					);
					$purse = trim(is_isset($purses, $key));
					if($purse){						
						try{
							$oClass = new AP_BTCUP();
							$res = $oClass->get_balans('', is_deffin($this->m_data,'KEY_INFO'), is_deffin($this->m_data,'SECRET_INFO'));
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
					$lifetime = intval(is_isset($paymerch_data, 'lifetime'));
					$bindlogin = intval(is_isset($paymerch_data, 'bindlogin'));
				
					$vtype = mb_strtoupper($item->vtype2);
					$vtype = str_replace('RUB','RUR',$vtype);
					
					$enable = array('USD', 'BTC', 'EMC', 'ETH', 'LTC');		
					if(!in_array($vtype, $enable)){
						$error[] = __('Wrong currency code','pn'); 
					}	
					
					if($bindlogin == 1){
						$receiver = $item->account2;
						$account = $item->user_email;
					} else {
						$receiver = '';
						$account = $item->account2;
					}					
					if (!is_email($account) and $variant == 0) {
						$error[] = __('Client wallet type does not match with currency code','pn');
					}				
					
					$sum = is_paymerch_sum($this->name, $item, $paymerch_data);
					
					$two = array('USD');
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
						
								$res = new AP_BTCUP();
								
								if($variant == 0){
									$res = $res->make_voucher($sum, $vtype, $lifetime, is_deffin($this->m_data,'KEY_COUPON'), is_deffin($this->m_data,'SECRET_COUPON'), $receiver);
									if($res['error'] == 1){
										$error[] = __('Payout error','pn');
										$pay_error = 1;
									} else {
										$coupon = $res['coupon'];
										$trans_id = $res['trans_id'];
									}									
								} else {
									$res = $res->get_transfer($sum, $vtype, $account, is_deffin($this->m_data,'KEY_WITHDRAW'), is_deffin($this->m_data,'SECRET_WITHDRAW'), $notice);
									if($res['error'] == 1){
										$error[] = __('Payout error','pn');
										$pay_error = 1;
									} else {
										$trans_id = $res['trans_id'];
									}
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
						
						if($variant == 0){
							
							$mailtemp = get_option('mailtemp');
							if(isset($mailtemp['btcup_paycoupon'])){
								$data = $mailtemp['btcup_paycoupon'];
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
							
							do_action('merchant_create_coupon', $coupon, $item, 'btcup', $place);
						
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

new paymerchant_btcup(__FILE__, 'BTCUP');