<?php
/*
title: [ru_RU:]Внутренний счет[:ru_RU][en_US:]Internal account[:en_US]
description: [ru_RU:]авто выплаты для внутреннего счета[:ru_RU][en_US:]auto payouts for internal account[:en_US]
version: 1.2
*/

if(!class_exists('paymerchant_domacc')){
	class paymerchant_domacc extends AutoPayut_Premiumbox{
		function __construct($file, $title)
		{
			$map = array();
			parent::__construct($file, $map, $title, 'BUTTON');	
			
			add_action('get_paymerchant_admin_options_'.$this->name, array($this, 'get_paymerchant_admin_options'), 10, 2);
			add_filter('paymerchant_enable_autopay',array($this, 'paymerchant_enable_autopay'),1,2);
			add_action('paymerchant_action_bid_'.$this->name, array($this,'paymerchant_action_bid'),99,3);
		}
		
		function get_paymerchant_admin_options($options, $data){
			
			if(isset($options['note'])){
				unset($options['note']);
			}
			if(isset($options['max'])){
				unset($options['max']);
			}					
			if(isset($options['max_sum'])){
				unset($options['max_sum']);
			}
			if(isset($options['checkpay'])){
				unset($options['checkpay']);
			}			
			if(isset($options['where_sum'])){
				unset($options['where_sum']);
			}		
			
			return $options;
		}		

		function paymerchant_enable_autopay($now, $m_id){
			
			if($m_id and $m_id == $this->name){	
				return 1;
			}
			
			return $now;
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
					
					if(count($error) == 0){

						$result = update_bids_meta($item_id, 'ap_status', 1);
						update_bids_meta($item_id, 'ap_status_date', current_time('timestamp'));
						
						if($result){
							$wpdb->update($wpdb->prefix.'bids', array('domacc2'=>'1'), array('id'=>$item_id));
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

new paymerchant_domacc(__FILE__, 'Internal account');