<?php
/*
title: [ru_RU:]Yandex money[:ru_RU][en_US:]Yandex money[:en_US]
description: [ru_RU:]авто выплаты Yandex money[:ru_RU][en_US:]Yandex money automatic payouts[:en_US]
version: 1.2
*/

if(!class_exists('paymerchant_yamoney')){
	class paymerchant_yamoney extends AutoPayut_Premiumbox{

		function __construct($file, $title)
		{
			$map = array(
				'AP_YANDEX_MONEY_BUTTON', 'AP_YANDEX_MONEY_ACCOUNT', 'AP_YANDEX_MONEY_APP_ID', 
				'AP_YANDEX_MONEY_APP_KEY', 
			);
			parent::__construct($file, $map, $title, 'AP_YANDEX_MONEY_BUTTON');
			
			add_action('get_paymerchant_admin_options_'.$this->name, array($this, 'get_paymerchant_admin_options'), 10, 2);
			add_filter('paymerchants_settingtext_'.$this->name, array($this, 'paymerchants_settingtext'));
			add_action('before_paymerchant_admin',array($this,'before_paymerchant_admin'));
			add_action('myaction_merchant_ap_'.$this->name.'_verify', array($this,'myaction_merchant_verify'));
			add_filter('reserv_place_list',array($this,'reserv_place_list'));
			add_filter('update_valut_autoreserv', array($this,'update_valut_autoreserv'), 10, 3);
			add_filter('update_naps_reserv', array($this,'update_naps_reserv'), 10, 4);
			add_action('paymerchant_action_bid_'.$this->name, array($this,'paymerchant_action_bid'),99,3);
		}

		function get_paymerchant_admin_options($options, $data){
			
			if(isset($options['checkpay'])){
				unset($options['checkpay']);
			}			
			
			$text = '
			<strong>'. __('Enter address to create new application','pn') .':</strong> <a href="https://sp-money.yandex.ru/myservices/new.xml" target="_blank">https://sp-money.yandex.ru/myservices/new.xml</a>.<br />
			<strong>Redirect URI:</strong> <a href="'. get_merchant_link('ap_'. $this->name .'_verify') .'" target="_blank">'. get_merchant_link('ap_'. $this->name .'_verify') .'</a>				
			';
			$options['text'] = array(
				'view' => 'textfield',
				'title' => '',
				'default' => $text,
			);			
			
			return $options;
		}			
		
		function paymerchants_settingtext(){
			$text = '| <span class="bred">'. __('Config file is not set up','pn') .'</span>';
			if(
				is_deffin($this->m_data,'AP_YANDEX_MONEY_ACCOUNT')
			){
				$text = '';
			}
			
			return $text;
		}
		
		function before_paymerchant_admin($m_id){
			if($m_id and $m_id == $this->name){
			
				echo '<div class="premium_reply theerror">'. sprintf(__('You have to pass <a href="%s" target="_blank">application authorization</a> in order to proceed.','pn'), get_merchant_link('ap_'. $this->name .'_verify')) .'</div>';
					
			}			
		}
		
		function myaction_merchant_verify(){
			
			if(current_user_can('administrator') or current_user_can('pn_merchants')){
				
				if(is_deffin($this->m_data,'AP_YANDEX_MONEY_APP_ID')){

					if( isset( $_GET['code'] ) ) {
						
						$oClass = new AP_YaMoney(is_deffin($this->m_data,'AP_YANDEX_MONEY_APP_ID'), is_deffin($this->m_data,'AP_YANDEX_MONEY_APP_KEY'), $this->name);
						$token = $oClass->auth();
						if($token){
							
							$res = $oClass->accountInfo($token);
							if( !isset( $res['account'] ) or $res['account'] != is_deffin($this->m_data,'AP_YANDEX_MONEY_ACCOUNT') ){
								
								pn_display_mess(sprintf(__('Authorization can me made from account %s','pn'), is_deffin($this->m_data,'AP_YANDEX_MONEY_ACCOUNT')));
								
							} else {
								
								$oClass->update_token($token);
								wp_redirect(admin_url('admin.php?page=pn_data_paymerchants&m_id='. $this->name .'&reply=true'));
								exit;
								
							}
							
						} else {
							
							pn_display_mess(__('Retry','pn'));
							
						}
						
					} else {
						
						$oClass = new AP_YaMoney(is_deffin($this->m_data,'AP_YANDEX_MONEY_APP_ID'), is_deffin($this->m_data,'AP_YANDEX_MONEY_APP_KEY'), $this->name);
						$res = $oClass->accountInfo();

						if( !isset( $res['account'] ) or $res['account'] != is_deffin($this->m_data,'AP_YANDEX_MONEY_ACCOUNT') ){
							
							header( 'Location: https://sp-money.yandex.ru/oauth/authorize?client_id='. is_deffin($this->m_data,'AP_YANDEX_MONEY_APP_ID') .'&response_type=code&redirect_uri='. urlencode( get_merchant_link('ap_'. $this->name .'_verify') ) .'&scope=account-info operation-history operation-details payment-p2p ');
							exit();
							
						} else {
							
							pn_display_mess(__('Payment system is configured','pn'), __('Payment system is configured','pn'),'true');
							
						}
						
					}
				}
				
			} else {
				pn_display_mess(__('Error! insufficient privileges!','pn'));	
			}
		}		

		function reserv_place_list($list){
			
			$list[$this->name.'_1'] = 'Yandex Money '. is_deffin($this->m_data,'AP_YANDEX_MONEY_ACCOUNT');
			
			return $list;									
		}

		function update_valut_autoreserv($ind, $key, $valut_id){
			
			if($ind == 0){
				if($key == $this->name.'_1'){	
					try{
					
						$oClass = new AP_YaMoney(is_deffin($this->m_data,'AP_YANDEX_MONEY_APP_ID'), is_deffin($this->m_data,'AP_YANDEX_MONEY_APP_KEY'), $this->name);
						$res = $oClass->accountInfo();
						if(is_array($res) and isset($res['balance'])){
								
							$rezerv = trim((string)$res['balance']);
							pm_update_vr($valut_id, $rezerv);						
								
						} 
						
					}
					catch (Exception $e)
					{
							
					} 				
						
					return 1;
				}
			}
			
			return $ind;			
		}

		function update_naps_reserv($ind, $key, $naps_id, $naps){
			
			if($ind == 0){
				if($key == $this->name.'_1'){	
					try{
					
						$oClass = new AP_YaMoney(is_deffin($this->m_data,'AP_YANDEX_MONEY_APP_ID'), is_deffin($this->m_data,'AP_YANDEX_MONEY_APP_KEY'), $this->name);
						$res = $oClass->accountInfo();
						if(is_array($res) and isset($res['balance'])){
								
							$rezerv = trim((string)$res['balance']);
							pm_update_nr($naps_id, $rezerv);						
								
						} 
						
					}
					catch (Exception $e)
					{
							
					} 				
						
					return 1;
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
					
					$enable = array('RUB');
					if(!in_array($vtype, $enable)){
						$error[] = __('Wrong currency code','pn'); 
					}						
						
					$account = $item->account2;
					$account = mb_strtoupper($account);
					if (!preg_match("/^[0-9]{5,20}$/", $account, $matches )) {
						$error[] = __('Client wallet type does not match with currency code','pn');
					}							

					$sum = is_my_money(is_paymerch_sum($this->name, $item, $paymerch_data), 2);
					
					if(count($error) == 0){

						$result = update_bids_meta($item->id, 'ap_status', 1);
						update_bids_meta($item->id, 'ap_status_date', current_time('timestamp'));				
						if($result){				
					
							$notice = get_text_paymerch($this->name, $item);
							if(!$notice){ $notice = sprintf(__('ID order %s','pn'), $item->id); }
							$notice = trim(pn_maxf($notice,150));
						
							try{
						
								$oClass = new AP_YaMoney(is_deffin($this->m_data,'AP_YANDEX_MONEY_APP_ID'), is_deffin($this->m_data,'AP_YANDEX_MONEY_APP_KEY'), $this->name);
								$reguest_id = $oClass->addPay($account, $sum, $notice, $item->id);
								if($reguest_id){
									$res = $oClass->processPay($reguest_id);
									if($res['error'] == 1){
										$error[] = __('Payout error','pn');
										$pay_error = 1;
									}	
									$trans_id = $reguest_id;
								} else {
									$error[] = 'Error interfaice';
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
						} 
						send_paymerchant_error($item_id, $error_text);
						
					} else {
						
						$params = array(
							'soschet' => is_deffin($this->m_data,'AP_YANDEX_MONEY_ACCOUNT'),
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

new paymerchant_yamoney(__FILE__, 'Yandex money');