<?php
/*
title: [ru_RU:]Qiwi[:ru_RU][en_US:]Qiwi[:en_US]
description: [ru_RU:]авто выплаты Qiwi[:ru_RU][en_US:]Qiwi automatic payouts[:en_US]
version: 1.2
*/

if(!class_exists('paymerchant_qiwi')){
	class paymerchant_qiwi extends AutoPayut_Premiumbox{

		function __construct($file, $title)
		{
			$map = array();
			parent::__construct($file, $map, $title, 'BUTTON');
			
			add_action('get_paymerchant_admin_options_'.$this->name, array($this, 'get_paymerchant_admin_options'), 10, 2);
			add_filter('onebid_actions', array($this, 'onebid_actions'), 10,3);
		}

		function get_paymerchant_admin_options($options, $data){
			
			if(isset($options['realpay'])){
				unset($options['realpay']);
			}
			if(isset($options['verify'])){
				unset($options['verify']);
			}
			if(isset($options['checkpay'])){
				unset($options['checkpay']);
			}			
			if(isset($options['button'])){
				unset($options['button']);
			}
			if(isset($options['button_maximum'])){
				unset($options['button_maximum']);
			}			
			if(isset($options['max'])){
				unset($options['max']);
			}
			if(isset($options['max_sum'])){
				unset($options['max_sum']);
			}

			if(isset($options['timeout'])){
				unset($options['timeout']);
			}
			if(isset($options['timeout_user'])){
				unset($options['timeout_user']);
			}
			if(isset($options['line_timeout'])){
				unset($options['line_timeout']);
			}			
			
			return $options;
		}			
		
		function onebid_actions($actions, $item, $data_fs){
			$m_out = is_isset($item,'m_out');
			if($m_out and $m_out == $this->name){
				
				$status = $item->status;
				$st = array('realpay','verify','payed');
				$st = apply_filters('status_for_autopay_admin',$st);
				$st = (array)$st;
				if(in_array($status, $st)){				
				
					$paymerch_data = get_paymerch_data($this->name);
					$pay_sum = is_my_money(is_paymerch_sum($this->name, $item, $paymerch_data), 2); 
					$text_pay = get_text_paymerch($this->name, $item);									
					$qiwi_account = trim(str_replace('+','',$item->account2));	
		
					$pay_sum = sprintf("%0.2F",$pay_sum);
					$sum = explode('.',$pay_sum);
					
					$vtype = mb_strtoupper($item->vtype2);
					
					$url = "https://qiwi.com/transfer/form.action?extra['account']=". $qiwi_account ."&source=qiwi_". $vtype ."&amountInteger=". $sum[0] ."&amountFraction=". $sum[1] ."&currency=". $vtype ."&extra['comment']=".urldecode($text_pay);					

					$actions['qiwi_ap'] = array(
						'type' => 'link',
						'title' => __('Transfer','pn'),
						'label' => __('Transfer','pn'),
						'link' => $url,
						'link_target' => '_blank',
						'link_class' => 'pay_merch',
					);					
			
				}
			}
			
			return $actions;
		}		
		
	}
}

new paymerchant_qiwi(__FILE__, 'Qiwi');