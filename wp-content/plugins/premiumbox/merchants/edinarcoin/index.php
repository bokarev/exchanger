<?php
/*
title: [ru_RU:]E-dinarcoin[:ru_RU][en_US:]E-dinarcoin[:en_US]
description: [ru_RU:]мерчант E-dinarcoin[:ru_RU][en_US:]E-dinarcoin merchant[:en_US]
version: 1.2
*/

if(!class_exists('merchant_edinar')){
	class merchant_edinar extends Merchant_Premiumbox {

		function __construct($file, $title)
		{
			$map = array(
				'TOKEN', 'ACCOUNT',
			);
			parent::__construct($file, $map, $title);
			
			add_filter('merchants_settingtext_'.$this->name, array($this, 'merchants_settingtext'));
			add_filter('merchants_action_bid_'.$this->name, array($this,'merchants_action_bid'),99,4);
			add_filter('get_merchant_admin_options_'.$this->name,array($this, 'get_merchant_admin_options'),1,2);
			add_action('myaction_merchant_'. $this->name .'_cron'. get_hash_result_url($this->name), array($this,'myaction_merchant_cron'));
			
			/* check address */
			//add_filter('merchant_formstep_after', array($this,'merchant_formstep_after'),99,4);
			//add_action('myaction_merchant_'. $this->name .'_checkorder', array($this,'myaction_merchant_checkorder'));
			add_filter('merchant_formstep_autocheck',array($this, 'merchant_formstep_autocheck'),1,2);
			/* end check address */
			
			add_filter('user_mailtemp',array($this,'user_mailtemp'));
			add_filter('admin_mailtemp',array($this,'admin_mailtemp'));
			add_filter('mailtemp_tags_generate_address1_edinar',array($this,'mailtemp_tags_generate_address'));
			add_filter('mailtemp_tags_generate_address2_edinar',array($this,'mailtemp_tags_generate_address'));
		}
		
		function user_mailtemp($places_admin){
			
			$places_admin['generate_address1_edinar'] = sprintf(__('Address generation for %s','pn'), 'Edinar');
			
			return $places_admin;
		}

		function admin_mailtemp($places_admin){
			
			$places_admin['generate_address2_edinar'] = sprintf(__('Address generation for %s','pn'), 'Edinar');
			
			return $places_admin;
		}

		function mailtemp_tags_generate_address($tags){
			
			$tags['bid_id'] = __('ID Order','pn');
			$tags['address'] = __('Address','pn');
			$tags['sum'] = __('Amount','pn');
			
			return $tags;
		}				

		function merchant_formstep_autocheck($autocheck, $m_id){
			
			if($m_id and $m_id == $this->name){
				$autocheck = 1;
			}
			
			return $autocheck;
		}		
		
		function get_merchant_admin_options($options, $data){
			
			if(isset($options['check'])){
				unset($options['check']);
			}
			if(isset($options['check_api'])){
				unset($options['check_api']);
			}
			if(isset($options['check_payapi'])){
				unset($options['check_payapi']);
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
			if(isset($options['enableip'])){
				unset($options['enableip']);
			}			

			$text = '
			<strong>CRON URL:</strong> <a href="'. get_merchant_link($this->name.'_cron'. get_hash_result_url($this->name)) .'" target="_blank">'. get_merchant_link($this->name.'_cron'. get_hash_result_url($this->name)) .'</a><br />
			';

			$options[] = array(
				'view' => 'textfield',
				'title' => '',
				'default' => $text,
			);			
			
			return $options;
		}			
		
		function merchants_settingtext(){
			$text = '| <span class="bred">'. __('Config file is not set up','pn') .'</span>';
			if(
				is_deffin($this->m_data,'TOKEN') and is_deffin($this->m_data,'ACCOUNT')
			){
				$text = '';
			}
			
			return $text;
		}		

 		function merchants_action_bid($temp, $pay_sum, $item, $naps){
			global $wpdb;
			
			$item_id = $item->id;
			$sum = pn_strip_input($item->summ1_dc);
			$vtype = pn_strip_input($item->vtype1);	
			
			$enable_currency = array('EDC');
			if(in_array($vtype, $enable_currency)){
				
				$data = get_merch_data($this->name);
				$show_error = intval(is_isset($data, 'show_error'));
				
				$params = array();
				the_merchant_bid_status('techpay', $item_id, 'user', 0, '', $params);				
				
				$naschet = pn_strip_input($item->naschet);
				if(!$naschet){
					try{
						$token = is_deffin($this->m_data,'TOKEN');
						$account = is_deffin($this->m_data,'ACCOUNT');
						$class = new Edinar($token);
						$result_url = get_merchant_link($this->name.'_cron' . get_hash_result_url($this->name)).'?hook_id='.$item_id;
						$naschet = $class->add_adress($account, $result_url);
						if($naschet){
							update_bids_naschet($item_id, $naschet);
							
							$mailtemp = get_option('mailtemp');
							if(isset($mailtemp['generate_address2_edinar'])){
								$data = $mailtemp['generate_address2_edinar'];
								if($data['send'] == 1){
									$ot_mail = is_email($data['mail']);
									$ot_name = pn_strip_input($data['name']);
									$sitename = pn_strip_input(get_bloginfo('sitename'));			
									$subject = pn_strip_input(ctv_ml($data['title']));
												
									$html = pn_strip_text(ctv_ml($data['text']));
												
									if($data['tomail']){
												
										$to_mail = $data['tomail'];
														
										$sarray = array(
											'[sitename]' => $sitename,
											'[bid_id]' => $item_id,
											'[address]' => $naschet,
											'[sum]' => $sum,
										);							
										$subject = get_replace_arrays($sarray, $subject);										
													
										$html = get_replace_arrays($sarray, $html);
										$html = apply_filters('comment_text',$html);
																
										pn_mail($to_mail, $subject, $html, $ot_name, $ot_mail);	
												
									}
								}
							}

							if(isset($mailtemp['generate_address1_edinar'])){
								$data = $mailtemp['generate_address1_edinar'];
								if($data['send'] == 1){
									$ot_mail = is_email($data['mail']);
									$ot_name = pn_strip_input($data['name']);
								
									$subject = pn_strip_input(ctv_ml($data['title']));
									$sitename = pn_strip_input(get_bloginfo('sitename'));
									$html = pn_strip_text(ctv_ml($data['text']));
								
									$to_mail = is_email($item->user_email);
									if($to_mail){
								
										$sarray = array(
											'[sitename]' => $sitename,
											'[bid_id]' => $item_id,
											'[address]' => $naschet,
											'[sum]' => $sum,
										);							
										$subject = get_replace_arrays($sarray, $subject);								
																
										$html = get_replace_arrays($sarray, $html);											
										$html = apply_filters('comment_text',$html);
									
										pn_mail($to_mail, $subject, $html, $ot_name, $ot_mail);	 
									}
								}
							}								
						}
					}
					catch (Exception $e)
					{
						if($show_error){
							echo $e;
						}
					}				
				}
				
				if($naschet){
					?>
						<div class="zone">
							<p><?php printf(__('In order to pay an ID <b>%1$s</b> order send amount <b>%2$s</b> %4$s on address <b>%3$s</b>','pn'),$item_id, $sum, $naschet, $vtype); ?></p>
						</div>				
					<?php
				} else {
					?>
						<div class="error_div"><?php _e('Error','pn'); ?></div>
					<?php				
				}				
			} else {
				?>
				<div class="error_div"><?php _e('Error','pn'); ?></div>
				<?php			
			} 
		} 
 
		function myaction_merchant_cron(){
	
			$order_id = trim(is_param_get('hook_id'));
			$this->edinar_check_orders($order_id, 0);
	
		}		

		function edinar_check_orders($order_id=0, $return=0){
			global $wpdb;

			$m_id = $this->name;
			$order_id = intval($order_id);
			$check_wallet = is_check_wallet($m_id);
			$return_url = '';

			$where = '';
			if($order_id){
				$where = " AND id = '$order_id'";
			}
			
			$data = get_merch_data($this->name);
			$show_error = intval(is_isset($data, 'show_error'));
			
			$items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."bids WHERE status IN ('techpay') AND vtype1 IN('EDC') AND naschet != '' AND m_in='$m_id' $where");
			foreach($items as $item){
				$item_id = $item->id;
							
				$address = $item->naschet;
				$pay_purse = apply_filters('pay_purse_merchant', '', $check_wallet, $m_id);
				$sum = $item->summ1_dc;
				$sum = apply_filters('merchant_bid_sum', $sum, $m_id);
					
				if($return){
					$return_url = get_bids_url($item->hashed);
				}
					
				try {
					$token = is_deffin($this->m_data,'TOKEN');
					$account = is_deffin($this->m_data,'ACCOUNT');
					$class = new Edinar($token);						
					$datas = $class->get_history_address($address);
					foreach($datas as $trans_id => $data){
						$amount = is_my_money($data['amount']);	
						if($amount >= $sum){
							$trans_id = pn_strip_input($trans_id);
								
							$params = array(
								'pay_purse' => $pay_purse,
								'sum' => $amount,
								'naschet' => '',
								'trans_in' => $trans_id,
							);
							the_merchant_bid_status('realpay', $item_id, 'user', 0, '', $params);								
								
							break;
						}
					}
				}
				catch (Exception $e)
				{
					if($show_error){
						echo $e;
					}
				}					
			} 
			
			if($return_url){
				wp_redirect($return_url);
				exit;
			}			
			
		}
		
		function merchant_formstep_after($content, $m_id, $item, $naps){
			if($m_id and $m_id == $this->name){
				$temp = '
				<div class="block_warning_merch">
					<div class="block_warning_merch_ins">		
						<p>'. __('Attention! Click "Check payment", if you have aready paid the order.','pn') .'</p>		
					</div>
				</div>
							
				<div class="block_paybutton_merch">
					<div class="block_paybutton_merch_ins">				
						<a href="'. get_merchant_link($this->name.'_checkorder') .'?order_id='. $item->id .'" class="merch_paybutton">'. __('Check payment','pn') .'</a>				
					</div>
				</div>							
				';	

				return $temp;
			}
			return $content;
		}

		function myaction_merchant_checkorder(){
			
			$order_id = intval(is_param_get('order_id'));
			$this->edinar_check_orders($order_id, 1);	
	
		}
		
	}
}

new merchant_edinar(__FILE__, 'E-dinarcoin');