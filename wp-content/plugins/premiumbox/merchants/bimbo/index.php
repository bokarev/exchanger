<?php
/*
title: [ru_RU:]BimBo[:ru_RU][en_US:]BimBo[:en_US]
description: [ru_RU:]мерчант BimBo[:ru_RU][en_US:]BimBo merchant[:en_US]
version: 1.2
*/

if(!class_exists('merchant_bimbo')){
	class merchant_bimbo extends Merchant_Premiumbox {

		function __construct($file, $title)
		{
			$map = array();
			parent::__construct($file, $map, $title);
			
			add_action('get_merchant_admin_options_'. $this->name, array($this, 'get_merchant_admin_options'), 10, 2);
			add_filter('merchant_pay_button_'.$this->name, array($this,'merchant_pay_button'),99,4);
			add_filter('merchant_formstep_after', array($this,'merchant_formstep_after'),99,4);
			add_action('myaction_merchant_'. $this->name .'_gostatus', array($this,'myaction_merchant_gostatus'));
			add_action('myaction_merchant_'. $this->name .'_paystatus', array($this,'myaction_merchant_paystatus'));
		}

		function get_merchant_admin_options($options, $data){ 
		
			if(isset($options['bottom_title'])){
				unset($options['bottom_title']);
			}
			if(isset($options['check'])){
				unset($options['check']);
			}			
			if(isset($options['note'])){
				unset($options['note']);
			}
			if(isset($options['type'])){
				unset($options['type']);
			}
			if(isset($options['help_type'])){
				unset($options['help_type']);
			}
			if(isset($options['corr'])){
				unset($options['corr']);
			}
			if(isset($options['check_api'])){
				unset($options['check_api']);
			}
			if(isset($options['check_payapi'])){
				unset($options['check_payapi']);
			}
			if(isset($options['enableip'])){
				unset($options['enableip']);
			}
			if(isset($options['resulturl'])){
				unset($options['resulturl']);
			}
			if(isset($options['show_error'])){
				unset($options['show_error']);
			}			
			
			$options[] = array(
				'view' => 'inputbig',
				'title' => __('Link','pn'),
				'default' => is_isset($data, 'link'),
				'name' => 'link',
				'ml' => 1,
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

		function merchant_formstep_after($content, $m_id, $item, $naps){
			if($m_id and $m_id == $this->name){
				
				$temp = '
				<div class="block_warning_merch">
					<div class="block_warning_merch_ins">		
						<p>'. __('Attention! Click "Paid", if you have already paid the request.','pn') .'</p>		
					</div>
				</div>
							
				<div class="block_paybutton_merch">
					<div class="block_paybutton_merch_ins">				
						<a href="'. get_merchant_link($this->name . '_paystatus') .'?hash='. is_bid_hash($item->hashed) .'" class="merch_paybutton iam_pay_bids">'. __('Paid','pn') .'</a>				
					</div>
				</div>							
				';	

				return $temp;
			}
			return $content;
		}	

		function myaction_merchant_paystatus(){
		global $wpdb;	
	
			$hashed = is_bid_hash(is_param_get('hash'));
			if($hashed){
				$obmen = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."bids WHERE hashed='$hashed'");
				if(isset($obmen->id)){
					$en_status = array('new','techpay','coldpay');
					if(in_array($obmen->status, $en_status)){
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
		
		function merchant_pay_button($merchant_pay_button, $summ_to_pay, $item, $naps){
			
			$merchant_pay_button = '
			<a href="'. get_merchant_link($this->name . '_gostatus') .'" target="_blank" class="success_paybutton">'. __('Make a payment','pn') .'</a>
			';
			
			return $merchant_pay_button;
		}

		function myaction_merchant_gostatus(){
		global $wpdb;	
	
			$data = get_merch_data($this->name);
			$url = trim(ctv_ml(is_isset($data, 'link')));
	
			wp_redirect($url);
			exit;	
		}		
		
	}
}

new merchant_bimbo(__FILE__, 'BimBo');