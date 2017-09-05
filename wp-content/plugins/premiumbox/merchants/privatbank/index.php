<?php
/*
title: [ru_RU:]Privat24 History (выписка)[:ru_RU][en_US:]Privat24 History (statement)[:en_US]
description: [ru_RU:]проверка истории платежей по выписке из мерчанта Privat24 [:ru_RU][en_US:]checking out payments history according to the merchant Private24 list[:en_US]
version: 1.2
*/

if(!class_exists('merchant_privatbank')){
	class merchant_privatbank extends Merchant_Premiumbox {

		function __construct($file, $title)
		{
			$map = array(
				'MERCHANT_ID', 'MERCHANT_KEY', 'CARD_NUM'
			);
			parent::__construct($file, $map, $title);
			
			add_filter('get_text_pay', array($this,'get_text_pay'), 99, 3);
			add_action('get_merchant_admin_options_'. $this->name, array($this, 'get_merchant_admin_options'), 10, 2);
			add_filter('merchants_settingtext_'.$this->name, array($this, 'merchants_settingtext'));
			add_filter('merchant_pay_button_'.$this->name, array($this,'merchant_pay_button'),99,4);
			add_action('myaction_merchant_'. $this->name .'_status' . get_hash_result_url($this->name), array($this,'myaction_merchant_status'));
			add_action('myaction_merchant_'. $this->name .'_paystatus', array($this,'myaction_merchant_paystatus'));
		}

		function get_merchant_admin_options($options, $data){ 
			
			if(isset($options['bottom_title'])){
				unset($options['bottom_title']);
			}

			$text = '
			<strong>CRON:</strong> <a href="'. get_merchant_link($this->name.'_status' . get_hash_result_url($this->name)) .'" target="_blank">'. get_merchant_link($this->name.'_status' . get_hash_result_url($this->name)) .'</a>			
			';

			if(isset($options['note'])){
				unset($options['note']);
			}			
			if(isset($options['check'])){
				unset($options['check']);
			}
			if(isset($options['check_api'])){
				unset($options['check_api']);
			}
			if(isset($options['check_payapi'])){
				unset($options['check_payapi']);
			}			
			if(isset($options['type'])){
				unset($options['type']);
			}
			if(isset($options['help_type'])){
				unset($options['help_type']);
			}
			if(isset($options['enableip'])){
				unset($options['enableip']);
			}			
			
			$options['text'] = array(
				'view' => 'textfield',
				'title' => '',
				'default' => $text,
			);	

			$options['bottom_title'] = array(
				'view' => 'h3',
				'title' => '',
				'submit' => __('Save','pn'),
				'colspan' => 2,
			);				
			
			return $options;	
		}				
		
		function get_text_pay($text, $m_id, $item){
			if($m_id and $m_id == $this->name){
				$text = str_replace('[id]','('.$item->id.')',$text);
			}
			return $text;
		}		
		
		function merchants_settingtext(){
			$text = '| <span class="bred">'. __('Config file is not set up','pn') .'</span>';
			if(
				is_deffin($this->m_data,'MERCHANT_ID') and is_deffin($this->m_data,'MERCHANT_KEY') and is_deffin($this->m_data,'CARD_NUM') 
			){
				$text = '';
			}
			
			return $text;
		}	

		function merchant_pay_button_visible($ind, $m_id, $item, $naps){
			
			if($m_id and $m_id == $this->name){
				$ind = 0;
			}			
			
			return $ind;			
		}			
		
		function merchant_pay_button($merchant_pay_button, $summ_to_pay, $item, $naps){
			
			$merchant_pay_button = '
			<a href="'. get_merchant_link($this->name . '_paystatus') .'?hash='. is_bid_hash($item->hashed) .'" class="success_paybutton iam_pay_bids">'. __('Paid','pn') .'</a>
			';
			
			return $merchant_pay_button;
		}

		function myaction_merchant_paystatus(){
		global $wpdb;	
	
			$hashed = is_bid_hash(is_param_get('hash'));
			if($hashed){
				$obmen = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."bids WHERE hashed='$hashed'");
				if(isset($obmen->id)){
					if($obmen->status == 'new'){
						if(is_true_userhash($obmen)){					
							$naps_id = intval($obmen->naps_id);
							$naps = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."naps WHERE naps_status='1' AND autostatus='1' AND id='$naps_id'");
							$m_id = apply_filters('get_merchant_id','', is_isset($naps,'m_in'), $obmen);
							if($m_id and $m_id == $this->name){
								$result = $wpdb->update($wpdb->prefix.'bids', array('status'=>'payed','editdate'=>current_time('mysql')), array('id'=>$obmen->id));
								if($result == 1){
									do_action('change_bidstatus_all', 'payed', $obmen->id, $obmen, 'site', 'user');
									do_action('change_bidstatus_payed', $obmen->id, $obmen, 'site', 'user');
								}
							}
						}
					} 
				}
			} 
				$url = get_bids_url($hashed);
				wp_redirect($url);
				exit;	
		}		
		
		function myaction_merchant_status(){
			global $wpdb;			
			$m_in = $this->name;
			
			$data = get_merch_data($this->name);
			$show_error = intval(is_isset($data, 'show_error'));
			
			try {
				$oClass = new PrivatBankApi(is_deffin($this->m_data,'MERCHANT_ID'),is_deffin($this->m_data,'MERCHANT_KEY'));
				$card = is_deffin($this->m_data,'CARD_NUM');
				$res = $oClass->get_history($card);
				if(is_array($res)){
					foreach($res as $bid_id => $bid_data){
						$bid_id = intval($bid_id);
						$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."bids WHERE status IN('coldpay','techpay','payed') AND m_in='$m_in' AND id='$bid_id'");
						if(isset($item->id)){
							$currency = mb_strtoupper(is_isset($bid_data,'currency'));
					
							$id = $bid_id;
							$data = get_data_merchant_for_id($id, $item);
							$in_summ = is_isset($bid_data,'amount');
							$in_summ = is_my_money($in_summ,2);
							$err = $data['err'];
							$status = $data['status'];
							$m_id = $data['m_id'];
							$pay_purse = is_pay_purse('', $data, $m_id);
							$vtype = $data['vtype'];
												
							$bid_sum = is_my_money($data['pay_sum'],2);	
							$bid_sum = apply_filters('merchant_bid_sum', $bid_sum, $m_in);
							if($err == 0 and $vtype == $currency){
								if($in_summ >= $bid_sum){
									$params = array(
										'pay_purse' => $pay_purse,
										'sum' => $in_summ,
										'naschet' => is_deffin($this->m_data,'MERCHANT_ID'),
									);
									the_merchant_bid_status('realpay', $id, 'user', 0, '', $params);													
								}				 	
							}				
				
						}
					}
				}
			}	
			catch( Exception $e ) {
				if($show_error){
					echo $e;
				}	
			}			
		}
		
	}
}

new merchant_privatbank(__FILE__, 'Privat24 History');