<?php
/*
title: [ru_RU:]AdvCash[:ru_RU][en_US:]AdvCash[:en_US]
description: [ru_RU:]авто выплаты AdvCash[:ru_RU][en_US:]AdvCash automatic payouts[:en_US]
version: 1.2
*/

if(!class_exists('paymerchant_advcash')){
	class paymerchant_advcash extends AutoPayut_Premiumbox{
		function __construct($file, $title)
		{
			
			$map = array(
				'BUTTON', 'API_NAME', 'ACCOUNT_EMAIL', 'API_PASSWORD',
				'U_WALLET','E_WALLET','R_WALLET','G_WALLET',
				'H_WALLET','T_WALLET','B_WALLET',
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
			
			if(isset($options['checkpay'])){
				unset($options['checkpay']);
			}
			
			$noptions = array();
			foreach($options as $key => $val){
				$noptions[$key] = $val;
				if($key == 'note'){
					$noptions[] = array(
						'view' => 'warning',
						'default' => sprintf(__('Use only latin symbols in payment notes. Maximum: %s characters.','pn'), 100),
					);		
					$opts = array(
						'0' => __('to Wallet','pn'),
						'1' => __('to E-mail','pn'),
						'2' => __('to Bitcoin','pn'),
						'3' => __('to Capitalist','pn'),
						'4' => __('to Ecoin','pn'),
						'5' => __('to Okpay','pn'),
						'6' => __('to Paxum','pn'),
						'7' => __('to Payeer','pn'),
						'8' => __('to Perfect Money','pn'),
						'9' => __('to Webmoney','pn'),
						'10' => __('to Qiwi','pn'),
						'11' => __('to Yandex Money','pn'),
						'12' => __('to Payza','pn'),
						'13' => __('to AdvCash Card Virtual','pn'),
						'14' => __('to AdvCash Card Plastic','pn'),
					);
					
					$noptions['methodpay'] = array(
						'view' => 'select',
						'title' => __('Transaction type','pn'),
						'options' => $opts,
						'default' => intval(is_isset($data, 'methodpay')),
						'name' => 'methodpay',
						'work' => 'int',
					);					
				}
			}		
			
			return $noptions;
		}	
		
		function paymerchants_settingtext(){
			$text = '| <span class="bred">'. __('Config file is not set up','pn') .'</span>';
			if(
				is_deffin($this->m_data,'API_NAME') 
				and is_deffin($this->m_data,'ACCOUNT_EMAIL')  
				and is_deffin($this->m_data,'API_PASSWORD') 
			){
				$text = '';
			}
			
			return $text;
		}

		function reserv_place_list($list){			
			$purses = array(
				$this->name.'_1' => is_deffin($this->m_data,'U_WALLET'),
				$this->name.'_2' => is_deffin($this->m_data,'E_WALLET'),
				$this->name.'_3' => is_deffin($this->m_data,'R_WALLET'),
				$this->name.'_4' => is_deffin($this->m_data,'G_WALLET'),
				$this->name.'_5' => is_deffin($this->m_data,'H_WALLET'),
				$this->name.'_6' => is_deffin($this->m_data,'T_WALLET'),
				$this->name.'_7' => is_deffin($this->m_data,'B_WALLET'),
			);		
			foreach($purses as $k => $v){
				$v = trim($v);
				if($v){
					$list[$k] = 'AdvCash - '. $v;
				}
			}
			return $list;
		}

		function update_valut_autoreserv($ind, $key, $valut_id){
			
			$ind = intval($ind);
			if(!$ind){
				if(strstr($key, $this->name.'_')){
					$purses = array(
						$this->name.'_1' => is_deffin($this->m_data,'U_WALLET'),
						$this->name.'_2' => is_deffin($this->m_data,'E_WALLET'),
						$this->name.'_3' => is_deffin($this->m_data,'R_WALLET'),
						$this->name.'_4' => is_deffin($this->m_data,'G_WALLET'),
						$this->name.'_5' => is_deffin($this->m_data,'H_WALLET'),
						$this->name.'_6' => is_deffin($this->m_data,'T_WALLET'),
						$this->name.'_7' => is_deffin($this->m_data,'B_WALLET'),
					);
					$purse = trim(is_isset($purses, $key));
					if($purse){
						
						try{
												
							$merchantWebService = new MerchantWebService();
							$arg0 = new authDTO();
							$arg0->apiName = is_deffin($this->m_data,'API_NAME');
							$arg0->accountEmail = is_deffin($this->m_data,'ACCOUNT_EMAIL');
							$arg0->authenticationToken = $merchantWebService->getAuthenticationToken(is_deffin($this->m_data,'API_PASSWORD'));

							$getBalances = new getBalances();
							$getBalances->arg0 = $arg0;					
					
							$getBalancesResponse = $merchantWebService->getBalances($getBalances);
							
							$balances = array();
							if(is_object($getBalancesResponse) and isset($getBalancesResponse->return) and is_array($getBalancesResponse->return)){
								foreach($getBalancesResponse->return as $item){
									$id = trim((string)$item->id);
									$amount = trim((string)$item->amount);
									$balances[$id] = $amount;
								}
							}					
					
							$rezerv = '-1';
								
							foreach($balances as $pursename => $amount){
								if( $pursename == $purse ){
									$rezerv = trim((string)$amount);
									break;
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
			
			$ind = intval($ind);
			if(!$ind){
				if(strstr($key, $this->name.'_')){
					$purses = array(
						$this->name.'_1' => is_deffin($this->m_data,'U_WALLET'),
						$this->name.'_2' => is_deffin($this->m_data,'E_WALLET'),
						$this->name.'_3' => is_deffin($this->m_data,'R_WALLET'),
						$this->name.'_4' => is_deffin($this->m_data,'G_WALLET'),
						$this->name.'_5' => is_deffin($this->m_data,'H_WALLET'),
						$this->name.'_6' => is_deffin($this->m_data,'T_WALLET'),
						$this->name.'_7' => is_deffin($this->m_data,'B_WALLET'),
					);
					$purse = trim(is_isset($purses, $key));
					if($purse){
						
						try{
												
							$merchantWebService = new MerchantWebService();
							$arg0 = new authDTO();
							$arg0->apiName = is_deffin($this->m_data,'API_NAME');
							$arg0->accountEmail = is_deffin($this->m_data,'ACCOUNT_EMAIL');
							$arg0->authenticationToken = $merchantWebService->getAuthenticationToken(is_deffin($this->m_data,'API_PASSWORD'));

							$getBalances = new getBalances();
							$getBalances->arg0 = $arg0;					
					
							$getBalancesResponse = $merchantWebService->getBalances($getBalances);
							
							$balances = array();
							if(is_object($getBalancesResponse) and isset($getBalancesResponse->return) and is_array($getBalancesResponse->return)){
								foreach($getBalancesResponse->return as $item){
									$id = trim((string)$item->id);
									$amount = trim((string)$item->amount);
									$balances[$id] = $amount;
								}
							}					
					
							$rezerv = '-1';
								
							foreach($balances as $pursename => $amount){
								if( $pursename == $purse ){
									$rezerv = trim((string)$amount);
									break;
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
					$vtype = str_replace(array('GBP'),'G',$vtype);
					$vtype = str_replace(array('USD'),'U',$vtype);
					$vtype = str_replace(array('EUR'),'E',$vtype);
					$vtype = str_replace(array('RUR','RUB'),'R',$vtype);
					$vtype = str_replace(array('UAH'),'H',$vtype);
					$vtype = str_replace(array('KZT'),'T',$vtype);
					$vtype = str_replace(array('BRL'),'B',$vtype);
					
				
					$send_type = mb_strtoupper($item->vtype2);
					$send_type = str_replace(array('RUR','RUB'),'RUR',$send_type);
					
					$enable = array('G','U','E','R','H','T','B');
					if(!in_array($vtype, $enable)){
						$error[] = __('Wrong currency code','pn'); 
					}	
					
					$account = $item->account2;
					
					$method_pay = intval(is_isset($paymerch_data, 'methodpay'));
					
					$em_checks = array(1,13,14);
					if(in_array($method_pay, $em_checks)){
						if(!is_email($account)){
							$error[] = __('Client wallet type does not match with currency code','pn');
						}							
					} elseif($method_pay == 0) {
						$account = mb_strtoupper($account);
						if(!preg_match("/^{$vtype}[0-9]{0,20}$/", $account, $matches )) {
							$error[] = __('Client wallet type does not match with currency code','pn');
						}	
					}
					
					$sum = is_my_money(is_paymerch_sum($this->name, $item, $paymerch_data), 2);		
				
					/* проверка по истории is_paymerch_checkpay($m_id) */
				
					if(count($error) == 0){

						$result = update_bids_meta($item_id, 'ap_status', 1);
						update_bids_meta($item_id, 'ap_status_date', current_time('timestamp'));					
					
						if($result){
					
							$notice = get_text_paymerch($this->name, $item);
							if(!$notice){ $notice = sprintf(__('ID order %s','pn'), $item->id); }
							$notice = trim(pn_maxf($notice,100));
						
							try {
								$merchantWebService = new MerchantWebService();
								$arg0 = new authDTO();
								$arg0->apiName = is_deffin($this->m_data,'API_NAME');
								$arg0->accountEmail = is_deffin($this->m_data,'ACCOUNT_EMAIL');
								$arg0->authenticationToken = $merchantWebService->getAuthenticationToken(is_deffin($this->m_data,'API_PASSWORD'));					
						
								if($method_pay == 0 or $method_pay == 1){
									$arg1 = new sendMoneyRequest();
									$arg1->amount = $sum;
									$arg1->currency = $send_type;
									if($method_pay){
										$arg1->email = $account;
									} else {
										$arg1->walletId = $account;
									}
									$arg1->note = $notice;
									$arg1->savePaymentTemplate = false;

									$validationSendMoney = new validationSendMoney();
									$validationSendMoney->arg0 = $arg0;
									$validationSendMoney->arg1 = $arg1;

									$sendMoney = new sendMoney();
									$sendMoney->arg0 = $arg0;
									$sendMoney->arg1 = $arg1;
									$merchantWebService->validationSendMoney($validationSendMoney);
									$Response = $merchantWebService->sendMoney($sendMoney);
								} elseif($method_pay == 13 or $method_pay == 14){
									
									$cardType = 'VIRTUAL';
									if($method_pay == 14){
										$cardType = 'PLASTIC';
									}
									
									$arg1 = new advcashCardTransferRequest();
									$arg1->amount = $sum;
									$arg1->currency = $send_type;
									$arg1->email = $account;
									$arg1->cardType = $cardType;
									$arg1->note = $notice;
									$arg1->savePaymentTemplate = false;

									$validationSendMoneyToAdvcashCard = new validationSendMoneyToAdvcashCard();
									$validationSendMoneyToAdvcashCard->arg0 = $arg0;
									$validationSendMoneyToAdvcashCard->arg1 = $arg1;

									$sendMoneyToAdvcashCard = new sendMoneyToAdvcashCard();
									$sendMoneyToAdvcashCard->arg0 = $arg0;
									$sendMoneyToAdvcashCard->arg1 = $arg1;
									
									$merchantWebService->validationSendMoneyToAdvcashCard($validationSendMoneyToAdvcashCard);
									$Response = $merchantWebService->sendMoneyToAdvcashCard($sendMoneyToAdvcashCard);
									
								} else {
									
									$ecurrencies = array(
										'2' => 'BITCOIN',
										'3' => 'CAPITALIST',
										'4' => 'ECOIN',
										'5' => 'OKPAY',
										'6' => 'PAXUM',
										'7' => 'PAYEER',
										'8' => 'PERFECT_MONEY',
										'9' => 'WEB_MONEY',
										'10' => 'QIWI',
										'11' => 'YANDEX_MONEY',	
										'12' => 'PAYZA',
									);
										
									$ecurrency = is_isset($ecurrencies, $method_pay);	
									
									$arg1 = new withdrawToEcurrencyRequest();
									$arg1->amount = $sum;
									//$arg1->btcAmount = 0.01;
									$arg1->currency = $send_type;
									$arg1->ecurrency = $ecurrency;
									$arg1->receiver = $account;
									$arg1->note = $notice;
									$arg1->savePaymentTemplate = true;

									$validationSendMoneyToEcurrency = new validationSendMoneyToEcurrency();
									$validationSendMoneyToEcurrency->arg0 = $arg0;
									$validationSendMoneyToEcurrency->arg1 = $arg1;

									$sendMoneyToEcurrency = new sendMoneyToEcurrency();
									$sendMoneyToEcurrency->arg0 = $arg0;
									$sendMoneyToEcurrency->arg1 = $arg1;
									$merchantWebService->validationSendMoneyToEcurrency($validationSendMoneyToEcurrency);
									$Response = $merchantWebService->sendMoneyToEcurrency($sendMoneyToEcurrency);
								}
						
								if(is_object($Response) and isset($Response->return)){
									$trans_id = trim((string)$Response->return);
								} else {
									$error[] = __('Payout error','pn');
									$pay_error = 1;								
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
							update_bids_meta($item_id, 'ap_status', 0);
							update_bids_meta($item_id, 'ap_status_date', current_time('timestamp'));
						}	
							
						$error_text = join('<br />',$error);
						
						do_action('paymerchant_error', $this->name, $error, $item_id, $place);
						
						if($place == 'admin'){
							pn_display_mess(__('Error!','pn') . $error_text);
						} else {
							send_paymerchant_error($item_id, $error_text);
						}
							
					} else { 
							
						$site_purse = '';
						
						$params = array(
							'pay_purse' => '',
							'sum' => 0,
							'soschet' => $site_purse,
							'naschet' => '',
							'trans_in' => '',
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

new paymerchant_advcash(__FILE__, 'AdvCash');