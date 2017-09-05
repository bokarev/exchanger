<?php
/*
title: [ru_RU:]BlockIo[:ru_RU][en_US:]BlockIo[:en_US]
description: [ru_RU:]мерчант Block.io[:ru_RU][en_US:]Block.io merchant[:en_US]
version: 1.2
*/

/* 
if (!extension_loaded('gmp')) {
    return;
}

if (!extension_loaded('mcrypt')) {
    return;
}

if (!extension_loaded('curl')) {
    return;
}
*/

if(!class_exists('merchant_blockio')){
	class merchant_blockio extends Merchant_Premiumbox {

		function __construct($file, $title)
		{
			$map = array(
				'BLOCKIO_SSL', 'BLOCKIO_CV', 'BLOCKIO_PIN',
				'BLOCKIO_BTC', 'BLOCKIO_LTC', 'BLOCKIO_DOGE', 
			);
			parent::__construct($file, $map, $title);
			
			add_filter('merchants_settingtext_'.$this->name, array($this, 'merchants_settingtext'));
			add_filter('merchants_action_bid_'.$this->name, array($this,'merchants_action_bid'),99,4);
			add_filter('get_merchant_admin_options_'.$this->name,array($this, 'get_merchant_admin_options'),1,2);
			add_action('myaction_merchant_'. $this->name .'_cron'. get_hash_result_url($this->name), array($this,'myaction_merchant_cron'));
			add_action('myaction_merchant_'. $this->name .'_archive_cron'. get_hash_result_url($this->name), array($this,'myaction_merchant_archive_cron'));
			add_filter('merchant_formstep_after', array($this,'merchant_formstep_after'),99,4);
			add_action('myaction_merchant_'. $this->name .'_checkorder', array($this,'myaction_merchant_checkorder'));
			add_filter('user_mailtemp',array($this,'user_mailtemp'));
			add_filter('admin_mailtemp',array($this,'admin_mailtemp'));
			add_filter('mailtemp_tags_generate_Address1_blockio',array($this,'mailtemp_tags_generate_Address'));
			add_filter('mailtemp_tags_generate_Address2_blockio',array($this,'mailtemp_tags_generate_Address'));
			add_filter('merchant_formstep_autocheck',array($this, 'merchant_formstep_autocheck'),1,2);
		}
		
		function user_mailtemp($places_admin){
			
			$places_admin['generate_Address1_blockio'] = sprintf(__('Address generation for %s','pn'), 'BlockIo');
			
			return $places_admin;
		}

		function admin_mailtemp($places_admin){
			
			$places_admin['generate_Address2_blockio'] = sprintf(__('Address generation for %s','pn'), 'BlockIo');
			
			return $places_admin;
		}

		function mailtemp_tags_generate_Address($tags){
			
			$tags['bid_id'] = __('ID Order','pn');
			$tags['Address'] = __('Address','pn');
			$tags['sum'] = __('Amount','pn');
			$tags['count'] = __('Confirmations','pn');
			
			return $tags;
		}				

		function get_merchant_admin_options($options, $data){
			
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
			if(isset($options['check_api'])){
				unset($options['check_api']);
			}
			if(isset($options['check_payapi'])){
				unset($options['check_payapi']);
			}			

			$text = '
			<strong>CRON URL:</strong> <a href="'. get_merchant_link($this->name.'_cron'. get_hash_result_url($this->name)) .'" target="_blank">'. get_merchant_link($this->name.'_cron'. get_hash_result_url($this->name)) .'</a><br />
			<strong>CRON ARCHIVE URL:</strong> <a href="'. get_merchant_link($this->name.'_archive_cron'. get_hash_result_url($this->name)) .'" target="_blank">'. get_merchant_link($this->name.'_archive_cron'. get_hash_result_url($this->name)) .'</a>			
			';

			$options[] = array(
				'view' => 'textfield',
				'title' => '',
				'default' => $text,
			);			
			
			return $options;
		}			
		
		function merchant_formstep_autocheck($autocheck, $m_id){
			
			if($m_id and $m_id == $this->name){
				$autocheck = 0;
			}
			
			return $autocheck;
		}		
		
		function merchants_settingtext(){
			$text = '| <span class="bred">'. __('Config file is not set up','pn') .'</span>';
			if(
				is_deffin($this->m_data,'BLOCKIO_PIN') 
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
			
			$enable_currency = array('BTC','LTC','DOGE');
			
			if(in_array($vtype, $enable_currency)){
				
				$data = get_merch_data($this->name);
				$show_error = intval(is_isset($data, 'show_error'));
				
				$api = 0;
				if($vtype == 'BTC'){
					$api = is_deffin($this->m_data,'BLOCKIO_BTC');
				} elseif($vtype == 'LTC'){
					$api = is_deffin($this->m_data,'BLOCKIO_LTC');
				} elseif($vtype == 'DOGE'){
					$api = is_deffin($this->m_data,'BLOCKIO_DOGE');
				}

				$naschet = pn_strip_input($item->naschet);
				if(!$naschet){
					try{
				
						$block_io = new BlockIo($api, is_deffin($this->m_data,'BLOCKIO_PIN'),2,is_deffin($this->m_data,'BLOCKIO_SSL'));
						$res = $block_io->get_new_address();	
						if(isset($res->status) and $res->status == 'success' and isset($res->data->address)){
							$naschet = pn_strip_input($res->data->address);
							update_bids_naschet($item_id, $naschet);
							
							$mailtemp = get_option('mailtemp');
							if(isset($mailtemp['generate_Address2_blockio'])){
								$data = $mailtemp['generate_Address2_blockio'];
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
											'[Address]' => $naschet,
											'[sum]' => $sum,
											'[count]' => intval(is_deffin($this->m_data,'BLOCKIO_CV')),
										);							
										$subject = get_replace_arrays($sarray, $subject);										
													
										$html = get_replace_arrays($sarray, $html);
										$html = apply_filters('comment_text',$html);
																									
										pn_mail($to_mail, $subject, $html, $ot_name, $ot_mail);		
									}
								}
							}

							if(isset($mailtemp['generate_Address1_blockio'])){
								$data = $mailtemp['generate_Address1_blockio'];
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
											'[Address]' => $naschet,
											'[sum]' => $sum,
											'[count]' => intval(is_deffin($this->m_data,'BLOCKIO_CV')),
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
								<p><?php printf(__('The order status changes to "Paid" when we get <b>%1$s</b> confirmations','pn'), is_deffin($this->m_data,'BLOCKIO_CV')); ?></p>
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
	
			global $wpdb;

			$m_id = $this->name;
			$check_wallet = is_check_wallet($m_id);
			$currencies = array('BTC','LTC','DOGE');
			foreach($currencies as $curr){
				$api = is_deffin($this->m_data,'BLOCKIO_'.$curr);
				if($api){
				
					$data = get_merch_data($this->name);
					$show_error = intval(is_isset($data, 'show_error'));
				
					try{
						
						$block_io = new BlockIo($api, is_deffin($this->m_data,'BLOCKIO_PIN'), 2, is_deffin($this->m_data,'BLOCKIO_SSL'));
						$res = $block_io->get_transactions(array('type' => 'received'));
						if(isset($res->status) and $res->status == 'success' and isset($res->data->network) and isset($res->data->txs)){
							if($curr == $res->data->network){			
						
								$n_conf = intval(is_deffin($this->m_data,'BLOCKIO_CV'));
						
								foreach($res->data->txs as $data){
									$confirmations = $data->confirmations;
										
									$sender = '';
									if(isset($data->senders[0])){
										$sender = $data->senders[0];
									}
										
									$amount = '0';
									if(isset($data->amounts_received[0]->amount)){
										$amount = is_my_money($data->amounts_received[0]->amount);
									}

									$Address = '';
									if(isset($data->amounts_received[0]->recipient)){
										$Address = $data->amounts_received[0]->recipient;
									}	
									
									$trans_id = 0;
									if(isset($data->txid)){
										$trans_id = $data->txid;
									}									

									if($amount > 0 and $Address){
										
										$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."bids WHERE status IN ('new','coldpay') AND vtype1='$curr' AND naschet='$Address' AND m_in='$m_id'");
										if(isset($item->id)){
											
											$status = $item->status;
												
											$pay_purse = apply_filters('pay_purse_merchant', $sender, $check_wallet, $m_id);
											$sum = $item->summ1_dc;
											$sum = apply_filters('merchant_bid_sum', $sum, $m_id);
											if($amount >= $sum){
													
												do_action('merchant_confirm_count', $item->id, $confirmations, $item, '', $n_conf);	

												if($confirmations >= $n_conf){
													if($status == 'new' or $status == 'coldpay'){
														$params = array(
															'pay_purse' => $pay_purse,
															'sum' => $amount,
															'naschet' => '',
															'trans_in' => $trans_id,
														);
														the_merchant_bid_status('realpay', $item->id, 'user', 0, '', $params);														
													}  
												} else {
													if($status == 'new'){
														$params = array(
															'pay_purse' => $pay_purse,
															'sum' => $amount,
															'naschet' => '',
															'trans_in' => $trans_id,
														);
														the_merchant_bid_status('coldpay', $item->id, 'user', 0, '', $params);														
													}	
												}	
											}
												
										}
											
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
			}
	
		}
		
		function myaction_merchant_archive_cron(){
			global $wpdb;

			$m_id = $this->name;
			$apis = array();
			$items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."bids WHERE status = 'success' AND naschet != '' AND m_in='$m_id' ORDER BY id DESC LIMIT 50");
			foreach($items as $item){
				$currency = trim(mb_strtoupper($item->vtype1));
				$naschet = pn_strip_input($item->naschet);
				if($naschet){
					$apis[$currency][] = $naschet;
				}
			}
			
			foreach($apis as $curr => $datas){
				$api_key = trim(is_deffin($this->m_data,'BLOCKIO_'.$curr));
				if($api_key and is_array($datas) and count($datas) > 0){
					$addresses = join(',', $datas);
					$this->archive_request($api_key, $addresses);
				}
			}
			
			_e('Done', 'pn');
			
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
						<a href="'. get_merchant_link($this->name.'_checkorder') .'?order='. $item->id .'" class="merch_paybutton">'. __('Check payment','pn') .'</a>				
					</div>
				</div>							
				';	

				return $temp;
			}
			return $content;
		}

		function myaction_merchant_checkorder(){
			global $wpdb;

			$item_id = intval(is_param_get('order'));
			if($item_id > 0){
			
				$m_id = $this->name;
				$check_wallet = is_check_wallet($m_id);
				$currencies = array('BTC','LTC','DOGE');
				
				$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."bids WHERE id='$item_id' AND m_in='$m_id' AND status != 'auto'");
				if(isset($item->id)){
					$vtype = $item->vtype1;
					if(in_array($vtype,$currencies)){
						$naschet = $item->naschet;
						if($naschet){
							
							$data = get_merch_data($this->name);
							$show_error = intval(is_isset($data, 'show_error'));							
							
							$naps_id = intval($item->naps_id);
							$naps = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."naps WHERE id='$naps_id'");
							if(isset($naps->m_in)){
								if($item->status == 'new' or $item->status == 'coldpay'){
									
									$api = 0;
									if($vtype == 'BTC'){
										$api = is_deffin($this->m_data,'BLOCKIO_'.$vtype);
									} elseif($vtype == 'LTC'){
										$api = is_deffin($this->m_data,'BLOCKIO_'.$vtype);
									} elseif($vtype == 'DOGE'){
										$api = is_deffin($this->m_data,'BLOCKIO_'.$vtype);
									}
									
									if($api){
										try{	
											$block_io = new BlockIo($api, is_deffin($this->m_data,'BLOCKIO_PIN'), 2, is_deffin($this->m_data,'BLOCKIO_SSL'));
											$res = $block_io->get_transactions(array('type' => 'received', 'addresses' => $naschet));
											if(isset($res->status) and $res->status == 'success' and isset($res->data->network) and isset($res->data->txs)){
												if($vtype == $res->data->network){			
													
													$n_conf = intval(is_deffin($this->m_data,'BLOCKIO_CV'));
												
													foreach($res->data->txs as $data){
														$confirmations = $data->confirmations;
	
														$sender = '';
														if(isset($data->senders[0])){
															$sender = $data->senders[0];
														}
																
														$amount = '0';
														if(is_object($data) and isset($data->amounts_received[0]->amount)){
															$amount = is_my_money($data->amounts_received[0]->amount,8);
														}

														$Address = '';
														if(is_object($data) and isset($data->amounts_received[0]->recipient)){
															$Address = $data->amounts_received[0]->recipient;
														}	

														$trans_id = 0;
														if(isset($data->txid)){
															$trans_id = $data->txid;
														}	
														
														if($amount > 0 and $Address){
															
															if($naschet == $Address){
																		
																$status = $item->status;
												
																$pay_purse = apply_filters('pay_purse_merchant', $sender, $check_wallet, $m_id);
																$sum = $item->summ1_dc;
																$sum = apply_filters('merchant_bid_sum', $sum, $m_id);
																if($amount >= $sum){
																		
																	do_action('merchant_confirm_count', $item->id, $confirmations, $item, $naps, $n_conf);	
																		
																	if($confirmations >= $n_conf){
																		if($status == 'new' or $status == 'coldpay'){
																			$params = array(
																				'pay_purse' => $pay_purse,
																				'sum' => $amount,
																				'naschet' => '',
																				'trans_in' => $trans_id,
																			);
																			the_merchant_bid_status('realpay', $item->id, 'user', 0, '', $params);																			
																		} 
																	} else {
																		if($status == 'new'){
																			$params = array(
																				'pay_purse' => $pay_purse,
																				'sum' => $amount,
																				'naschet' => '',
																				'trans_in' => $trans_id,
																			);
																			the_merchant_bid_status('coldpay', $item->id, 'user', 0, '', $params);																			
																		}	
																	}	
																}																		
																		
															}
																	
														}
													}
												
												}
											} 
										}
										catch (Exception $e)
										{
												
										}										
									}	
								}	
									
									$url = get_bids_url($item->hashed);
									wp_redirect($url);
									exit;								
							} else {
								pn_display_mess(__('Error!','pn'));
							}
						} else {
							$url = get_ajax_link('payedmerchant') .'&hash='. is_bid_hash($item->hashed);
							wp_redirect($url);
							exit;
						}
					} else {
						pn_display_mess(sprintf(__('Error currency code! %s only!','pn'),'BTC, LTC or DOGE'));
					}
				} else {
					pn_display_mess(__('Error!','pn'));
				}
			} else {
				pn_display_mess(__('Error!','pn'));
			}			
	
		}
		
		function archive_request($api_key, $addresses){
			get_curl_parser('https://block.io/api/v2/archive_addresses/?api_key='. $api_key .'&addresses='.$addresses, array(), 'merchant', 'blockio');			
		}
		
	}
}

new merchant_blockio(__FILE__, 'BlockIo');