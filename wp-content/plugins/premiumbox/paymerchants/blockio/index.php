<?php
/*
title: [ru_RU:]BlockIo[:ru_RU][en_US:]BlockIo[:en_US]
description: [ru_RU:]авто выплаты BlockIo[:ru_RU][en_US:]BlockIo automatic payouts[:en_US]
version: 1.2
*/

if(!class_exists('paymerchant_blockio')){
	class paymerchant_blockio extends AutoPayut_Premiumbox{

		function __construct($file, $title)
		{
			$map = array(
				'AP_BLOCKIO_BUTTON', 'AP_BLOCKIO_SSL', 'AP_BLOCKIO_PIN', 
				'AP_BLOCKIO_BTC', 'AP_BLOCKIO_LTC', 'AP_BLOCKIO_DOGE', 
			);
			parent::__construct($file, $map, $title, 'AP_BLOCKIO_BUTTON');
			
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
			if(isset($options['bottom_title'])){
				unset($options['bottom_title']);
			}			

			$opt = array(
				'low' => 'low',
				'medium' => 'medium',
				'high' => 'high',
			);
			$priority = trim(is_isset($data, 'priority'));
			if(!$priority){ $priority = 'low'; }
			$options[] = array(
				'view' => 'select',
				'title' => __('Network payment priority','pn'),
				'options' => $opt,
				'default' => $priority,
				'name' => 'priority',
				'work' => 'input',
			);			

			$options['bottom_title'] = array(
				'view' => 'h3',
				'title' => '',
				'submit' => __('Save','pn'),
				'colspan' => 2,
			);								
			
			return $options;
		}				

		function paymerchants_settingtext(){
			$text = '| <span class="bred">'. __('Config file is not set up','pn') .'</span>';
			if(
				is_deffin($this->m_data,'AP_BLOCKIO_PIN')  
			){
				$text = '';
			}
			
			return $text;
		}

		function reserv_place_list($list){
			
			$purses = array(
				$this->name.'_1' => array(
					'title' => 'BTC',
					'key' => is_deffin($this->m_data,'AP_BLOCKIO_BTC'),
				),
				$this->name.'_2' => array(
					'title' => 'LTC',
					'key' => is_deffin($this->m_data,'AP_BLOCKIO_LTC'),
				),
				$this->name.'_3' => array(
					'title' => 'DOGE',
					'key' => is_deffin($this->m_data,'AP_BLOCKIO_DOGE'),
				),
			);
			
			foreach($purses as $k => $v){
				$key = trim($v['key']);
				if($key){
					$list[$k] = 'BlockIo '. $v['title'];
				}
			}
			
			return $list;						
		}

		function update_valut_autoreserv($ind, $key, $valut_id){
			
			if($ind == 0){
				if(strstr($key, $this->name.'_')){
				
					$purses = array(
						$this->name.'_1' => is_deffin($this->m_data,'AP_BLOCKIO_BTC'),
						$this->name.'_2' => is_deffin($this->m_data,'AP_BLOCKIO_LTC'),
						$this->name.'_3' => is_deffin($this->m_data,'AP_BLOCKIO_DOGE'),
					);
					
					$api = trim(is_isset($purses, $key));
					if($api){
						
						try{
					
							$block_io = new AP_BlockIo($api, is_deffin($this->m_data,'AP_BLOCKIO_PIN'), 2, is_deffin($this->m_data,'AP_BLOCKIO_SSL'));
							$res = $block_io->get_balance();	
							if(isset($res->status) and $res->status == 'success' and isset($res->data->available_balance)){
								$rezerv = (string)$res->data->available_balance;
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
				
					$purses = array(
						$this->name.'_1' => is_deffin($this->m_data,'AP_BLOCKIO_BTC'),
						$this->name.'_2' => is_deffin($this->m_data,'AP_BLOCKIO_LTC'),
						$this->name.'_3' => is_deffin($this->m_data,'AP_BLOCKIO_DOGE'),
					);
					
					$api = trim(is_isset($purses, $key));
					if($api){
						
						try{
					
							$block_io = new AP_BlockIo($api, is_deffin($this->m_data,'AP_BLOCKIO_PIN'), 2, is_deffin($this->m_data,'AP_BLOCKIO_SSL'));
							$res = $block_io->get_balance();	
							if(isset($res->status) and $res->status == 'success' and isset($res->data->available_balance)){
								$rezerv = (string)$res->data->available_balance;
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
					
					$enable = array('BTC','LTC','DOGE');		
					if(!in_array($vtype, $enable)){
						$error[] = __('Wrong currency code','pn'); 
					}					
					
					$account = $item->account2;
					if (!$account) {
						$error[] = __('Client wallet type does not match with currency code','pn');
					}				
					
					$sum = is_my_money(is_paymerch_sum($this->name, $item, $paymerch_data));
					$minsum = '0.00005';
					if($sum < $minsum){
						$error[] = sprintf(__('Minimum payment amount is %s','pn'), $minsum);
					}		
					
					$api = 0;
					if($vtype == 'BTC' and is_deffin($this->m_data,'AP_BLOCKIO_BTC')){
						$api = is_deffin($this->m_data,'AP_BLOCKIO_BTC');
					} elseif($vtype == 'LTC' and is_deffin($this->m_data,'AP_BLOCKIO_LTC')){
						$api = is_deffin($this->m_data,'AP_BLOCKIO_LTC');
					} elseif($vtype == 'DOGE' and is_deffin($this->m_data,'AP_BLOCKIO_DOGE')){
						$api = is_deffin($this->m_data,'AP_BLOCKIO_DOGE');
					}
					
					if(!$api){	
						$error[] = 'Error interfaice';
					}
					
					if(count($error) == 0){

						$result = update_bids_meta($item->id, 'ap_status', 1);
						update_bids_meta($item->id, 'ap_status_date', current_time('timestamp'));				
						if($result){				
					
							try{
							
								$block_io = new AP_BlockIo($api, is_deffin($this->m_data,'AP_BLOCKIO_PIN'), 2, is_deffin($this->m_data,'AP_BLOCKIO_SSL'));
								
								$priority = trim(is_isset($paymerch_data, 'priority'));
								$prio = array('low','medium','high');
								if(!in_array($priority, $prio)){
									$priority = 'low';
								}
								
								$res = $block_io->withdraw(array('amounts' => $sum, 'to_addresses' => $account, 'priority' => $priority));
								if(isset($res->data->txid)){
									$trans_id = $res->data->txid;
								}								
								if(!isset($res->status) or $res->status != 'success' or !isset($res->data->amount_sent)){
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

new paymerchant_blockio(__FILE__, 'BlockIo');