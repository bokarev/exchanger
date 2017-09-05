<?php
/*
title: [ru_RU:]PerfectMoney[:ru_RU][en_US:]PerfectMoney[:en_US]
description: [ru_RU:]авто выплаты PerfectMoney[:ru_RU][en_US:]PerfectMoney automatic payouts[:en_US]
version: 1.2
*/

if(!class_exists('paymerchant_perfectmoney')){
	class paymerchant_perfectmoney extends AutoPayut_Premiumbox{
		function __construct($file, $title)
		{
			$map = array(
				'BUTTON', 'ACCOUNT_ID', 'PHRASE', 
				'U_ACCOUNT', 'E_ACCOUNT', 'G_ACCOUNT',
			);
			parent::__construct($file, $map, $title, 'BUTTON');	
			
			add_action('get_paymerchant_admin_options_'.$this->name, array($this, 'get_paymerchant_admin_options'), 10, 2);
			add_filter('paymerchants_settingtext_'.$this->name, array($this, 'paymerchants_settingtext'));
			add_filter('user_mailtemp',array($this,'user_mailtemp'));
			add_filter('mailtemp_tags_perfectmoney_paycoupon',array($this,'mailtemp_tags_paycoupon'));			
			add_filter('reserv_place_list',array($this,'reserv_place_list'));
			add_filter('update_valut_autoreserv', array($this,'update_valut_autoreserv'), 10, 3);
			add_filter('update_naps_reserv', array($this,'update_naps_reserv'), 10, 4);
			add_action('paymerchant_action_bid_'.$this->name, array($this,'paymerchant_action_bid'),99,3);
		}

		function user_mailtemp($places_admin){
			
			$places_admin['perfectmoney_paycoupon'] = sprintf(__('%s automatic payout','pn'), 'Perfectmoney E-Vouchers');
			
			return $places_admin;
		}

		function mailtemp_tags_paycoupon($tags){
			
			$tags['id'] = __('Coupon code','pn');
			$tags['num'] = __('Activation code','pn');
			$tags['bid_id'] = __('ID Order','pn');
			
			return $tags;
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
						
			$opt = array(
				'0' => __('Account','pn'),
				'1' => __('E-Vouchers','pn'),
			);
			$noptions['variant'] = array(
				'view' => 'select',
				'title' => __('Transaction type','pn'),
				'options' => $opt,
				'default' => intval(is_isset($data, 'variant')),
				'name' => 'variant',
				'work' => 'int',
			);	
			if(isset($noptions['bottom_title'])){
				unset($noptions['bottom_title']);
			}			
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
				is_deffin($this->m_data,'ACCOUNT_ID') 
				and is_deffin($this->m_data,'PHRASE')  
			){
				$text = '';
			}
			
			return $text;
		}

		function reserv_place_list($list){
			
			$purses = array(
				$this->name.'_1' => is_deffin($this->m_data,'U_ACCOUNT'),
				$this->name.'_2' => is_deffin($this->m_data,'E_ACCOUNT'),
				$this->name.'_3' => is_deffin($this->m_data,'G_ACCOUNT'),
			);
			
			foreach($purses as $k => $v){
				$v = trim($v);
				if($v){
					$list[$k] = 'PerfectMoney '. $v;
				}
			}
			
			return $list;
		}

		function update_valut_autoreserv($ind, $key, $valut_id){
			
			if($ind == 0){
				if(strstr($key, $this->name.'_')){
				
					$purses = array(
						$this->name.'_1' => is_deffin($this->m_data,'U_ACCOUNT'),
						$this->name.'_2' => is_deffin($this->m_data,'E_ACCOUNT'),
						$this->name.'_3' => is_deffin($this->m_data,'G_ACCOUNT'),
					);
					
					$purse = trim(is_isset($purses, $key));
					if($purse){
						
						try{
					
							$oClass = new AP_PerfectMoney( is_deffin($this->m_data,'ACCOUNT_ID'), is_deffin($this->m_data,'PHRASE') );
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
						$this->name.'_1' => is_deffin($this->m_data,'U_ACCOUNT'),
						$this->name.'_2' => is_deffin($this->m_data,'E_ACCOUNT'),
						$this->name.'_3' => is_deffin($this->m_data,'G_ACCOUNT'),
					);
					
					$purse = trim(is_isset($purses, $key));
					if($purse){
						
						try{
					
							$oClass = new AP_PerfectMoney( is_deffin($this->m_data,'ACCOUNT_ID'), is_deffin($this->m_data,'PHRASE') );
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
			
				$coupon = '';
				$coupon_num = '';
				$error = (array)$au_filter['error'];
				$pay_error = intval($au_filter['pay_error']);
				$trans_id = 0;				
			
				if($au_filter['enable'] == 1){			
			
					$variant = intval(is_isset($paymerch_data,'variant'));
			
					$vtype = mb_strtoupper($item->vtype2);
					$vtype = str_replace(array('GLD','OAU'),'G',$vtype);
					$vtype = str_replace(array('USD'),'U',$vtype);
					$vtype = str_replace(array('EUR'),'E',$vtype);
					
					$enable = array('G','U','E');
					if(!in_array($vtype, $enable)){
						$error[] = __('Wrong currency code','pn'); 
					}						
						
					$account = $item->account2;
					
					if($variant == 0){
						$account = mb_strtoupper($account);
						if (!preg_match("/^{$vtype}[0-9]{0,20}$/", $account, $matches )) {
							$error[] = __('Client wallet type does not match with currency code','pn');
						}
					} else {
						if (!is_email($account)) {
							$error[] = __('Client wallet type does not match with currency code','pn');
						}						
					}
					
					$site_purse = '';
					if($vtype == 'G'){
						$site_purse = is_deffin($this->m_data,'G_ACCOUNT');
					} elseif($vtype == 'U'){
						$site_purse = is_deffin($this->m_data,'U_ACCOUNT');
					} elseif($vtype == 'E'){
						$site_purse = is_deffin($this->m_data,'E_ACCOUNT');
					} 
					
					$site_purse = mb_strtoupper($site_purse);
					if (!preg_match("/^{$vtype}[0-9]{0,20}$/", $site_purse, $matches )) {
						$error[] = __('Your account set on website does not match with currency code','pn');
					}			

					$sum = is_my_money(is_paymerch_sum($this->name, $item, $paymerch_data), 2);
					
					$check_history = intval(is_isset($paymerch_data, 'checkpay'));
					if($check_history == 1){
					
						try {
							$class = new AP_PerfectMoney( is_deffin($this->m_data,'ACCOUNT_ID'), is_deffin($this->m_data,'PHRASE') );
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
						
								$oClass = new AP_PerfectMoney( is_deffin($this->m_data,'ACCOUNT_ID'), is_deffin($this->m_data,'PHRASE') );
								if($variant == 0){
									$res = $oClass->SendMoney($site_purse, $account, $sum, $item_id, $notice);
								} else {
									$res = $oClass->CreateVaucher($site_purse, $sum);
								}
								if($res['error'] == 1){
									$error[] = __('Payout error','pn');
									$pay_error = 1;
								} else {
									$trans_id = $res['trans_id'];
									if($variant == 1){
										$coupon = $res['code'];
										$coupon_num = $res['num'];
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
						
						if($variant == 1){
						
							$mailtemp = get_option('mailtemp');
							if(isset($mailtemp['perfectmoney_paycoupon'])){
								$data = $mailtemp['perfectmoney_paycoupon'];
								if($data['send'] == 1){
									
									$ot_mail = is_email($data['mail']);
									$ot_name = pn_strip_input($data['name']);
									$sitename = pn_strip_input(get_bloginfo('sitename'));			
									$subject = pn_strip_input(ctv_ml($data['title']));
												
									$html = pn_strip_text(ctv_ml($data['text']));
												
									if($account){
												
										$subject = str_replace('[sitename]', $sitename ,$subject);
										$subject = str_replace('[id]', $coupon ,$subject);
										$subject = str_replace('[num]', $coupon_num ,$subject);
										$subject = str_replace('[bid_id]', $item_id ,$subject);
													
										$html = str_replace('[sitename]', $sitename ,$html);
										$html = str_replace('[id]', $coupon ,$html);
										$html = str_replace('[num]', $coupon_num ,$html);
										$html = str_replace('[bid_id]', $item_id ,$html);
										$html = apply_filters('comment_text',$html);
													
										pn_mail($account, $subject, $html, $ot_name, $ot_mail);	
												
									}
								}
							}						
							
							do_action('merchant_create_coupon', $coupon, $item, 'perfectmoney', $place);						
							
						}	
						
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

new paymerchant_perfectmoney(__FILE__, 'PerfectMoney');