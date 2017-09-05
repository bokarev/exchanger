<?php
if( !defined( 'ABSPATH')){ exit(); }
 
/* letter paymerchant error */
add_filter('admin_mailtemp','paymerchant_admin_mailtemp');
function paymerchant_admin_mailtemp($places_admin){
	
	$places_admin['paymerchant_error'] = __('Automatic payout error','pn');
	
	return $places_admin;
}

add_filter('mailtemp_tags_paymerchant_error','def_mailtemp_tags_paymerchant_error');
function def_mailtemp_tags_paymerchant_error($tags){
	
	$tags['bid_id'] = __('ID Order','pn');
	$tags['error_txt'] = __('Error','pn');
	
	return $tags;
}

function send_paymerchant_error($bid_id, $error_txt){

	$mailtemp = get_option('mailtemp');
	if(isset($mailtemp['paymerchant_error'])){
		$data = $mailtemp['paymerchant_error'];
		if($data['send'] == 1){
			$ot_mail = is_email($data['mail']);
			$ot_name = pn_strip_input($data['name']);
			$sitename = pn_strip_input(get_bloginfo('sitename'));			
			$subject = pn_strip_input(ctv_ml($data['title']));
						
			$html = pn_strip_text(ctv_ml($data['text']));
						
			if($data['tomail']){
						
				$to_mail = $data['tomail'];
						
				$subject = str_replace('[sitename]', $sitename ,$subject);
				$subject = str_replace('[bid_id]', $bid_id ,$subject);
				$subject = apply_filters('mail_paymerchant_error_subject',$subject);
							
				$html = str_replace('[sitename]', $sitename ,$html);
				$html = str_replace('[bid_id]', $bid_id ,$html);
				$html = str_replace('[error_txt]',$error_txt,$html);
				$html = apply_filters('mail_paymerchant_error_text',$html);
				$html = apply_filters('comment_text',$html);
														
				pn_mail($to_mail, $subject, $html, $ot_name, $ot_mail);	
						
			}
		}
	}
				
}				
/* end letter paymerchant error */

/* автооплата */
add_action('change_bidstatus_realpay','paymerch_change_bidstatus_realpay',1500,3);
function paymerch_change_bidstatus_realpay($obmen_id, $item, $place){
global $wpdb;	
	if($place == 'site'){
		$m_id = apply_filters('get_paymerchant_id',0, is_isset($item,'m_out'), $item);
		if($m_id){
			$naps_id = intval(is_isset($item, 'naps_id'));
			$naps_data = get_naps_meta($naps_id, 'paymerch_data');
			$m_out_realpay = intval(is_isset($naps_data, 'm_out_realpay'));
			if(is_paymerch_realpay($m_id) and $m_out_realpay == 0 or $m_out_realpay == 2){
				do_action('paymerchant_action_bid_'.$m_id, $item, 'site', $naps_data);
			}
		}
	}	
}

add_action('change_bidstatus_verify','paymerch_change_bidstatus_verify',1500,3);
function paymerch_change_bidstatus_verify($obmen_id, $item, $place){
global $wpdb;
	if($place == 'site'){
		$m_id = apply_filters('get_paymerchant_id',0, is_isset($item,'m_out'), $item);
		if($m_id){
			$naps_id = intval(is_isset($item, 'naps_id'));
			$naps_data = get_naps_meta($naps_id, 'paymerch_data');
			$m_out_verify = intval(is_isset($naps_data, 'm_out_verify'));
			if(is_paymerch_verify($m_id) and $m_out_verify == 0 or $m_out_verify == 2){
				do_action('paymerchant_action_bid_'.$m_id, $item, 'site', $naps_data);
			}
		}
	}	
}
/* end автооплата */

/* кнопка автовыплаты */
add_filter('onebid_actions','onebid_actions_paymerch',99,3);
function onebid_actions_paymerch($actions, $item, $data_fs){
global $wpdb;
	
	$status = $item->status;
	$st = array('realpay','verify','payed');
	$st = apply_filters('status_for_autopay_admin',$st);
	$st = (array)$st;
	if(in_array($status, $st)){
		if(current_user_can('administrator') or current_user_can('pn_bids_payouts')){
			$m_id = apply_filters('get_paymerchant_id',0, is_isset($item,'m_out'), $item);
			if($m_id){
				$enable_autopay = apply_filters('paymerchant_enable_autopay',0 , $m_id);
				if($enable_autopay == 1){
					if(is_paymerch_button($m_id)){
						$actions['pay_merch'] = array(
							'type' => 'link',
							'title' => __('Transfer','pn'),
							'label' => __('Transfer','pn'),
							'link' => pn_link_post('paymerchant_bid_action') .'&id=[id]',
							'link_target' => '_blank',
							'link_class' => 'pay_merch',
						);					
					}
				}
			}
		}
	}
	
	return $actions;
}

add_action('premium_action_paymerchant_bid_action','def_paymerchant_bid_action');
function def_paymerchant_bid_action(){
global $wpdb;

	if(current_user_can('administrator') or current_user_can('pn_bids_payouts')){
		admin_pass_protected(__('Enter security code','pn'), __('Enter','pn'));	
		
		$bid_id = intval(is_param_get('id'));
		$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."bids WHERE id='$bid_id'");
		if(isset($item->id)){
			$status = $item->status;
			$st = array('realpay','verify','payed');
			$st = apply_filters('status_for_autopay_admin',$st);
			$st = (array)$st;
			if(in_array($status, $st)){
				$m_id = apply_filters('get_paymerchant_id',0, is_isset($item,'m_out'), $item);
				if($m_id){
					$enable_autopay = apply_filters('paymerchant_enable_autopay',0 , $m_id);
					if($enable_autopay == 1){
						if(is_paymerch_button($m_id)){
							$naps_id = intval(is_isset($item, 'naps_id'));
							$naps_data = get_naps_meta($naps_id, 'paymerch_data');
							do_action('paymerchant_action_bid_'.$m_id, $item, 'admin', $naps_data);
						} else {
							pn_display_mess(__('Error! Automatic payout is disabled','pn'));
						}
					} else {
						pn_display_mess(__('Error! Automatic payout is disabled','pn'));
					}
				} else {
					pn_display_mess(__('Error! Automatic payout is disabled','pn'));
				}
			} else {
				pn_display_mess(__('Error! Incorrect status of the order','pn'));
			}
		} else {
			pn_display_mess(__('Error! Order do not exist','pn'));
		}
	} else {
		pn_display_mess(__('Error! insufficient privileges!','pn'));
	}
}
/* кнопка автовыплаты */

function get_text_paymerch($m_id, $item){
	if($m_id and isset($item->id)){
		$merch_data = get_option('paymerch_data');
		$data = is_isset($merch_data,$m_id);
		$text = trim(ctv_ml(is_isset($data,'note')));
		
		$fio_arr = array($item->last_name, $item->first_name, $item->second_name);
		$fio_arr = array_unique($fio_arr);
		$fio = pn_strip_input(join(' ',$fio_arr));
		
		$text = apply_filters('get_text_paymerch', $text, $m_id, $item);
		$text = str_replace('[id]',$item->id,$text);
		$text = str_replace('[sum1]', pn_strip_input($item->summ1_dc),$text);
		$text = str_replace('[valut1]', pn_strip_input(ctv_ml($item->valut1)) .' '. pn_strip_input($item->vtype1),$text);	
		$text = str_replace('[sum2]', pn_strip_input($item->summ2c),$text);
		$text = str_replace('[valut2]', pn_strip_input(ctv_ml($item->valut2)) .' '. pn_strip_input($item->vtype2),$text);
		$text = str_replace('[account]', pn_strip_input($item->account2),$text);
		$text = str_replace('[account_give]', pn_strip_input($item->account1),$text);
		$text = str_replace('[account_get]', pn_strip_input($item->account2),$text);
		$text = str_replace('[fio]',$fio,$text);
		$text = str_replace('[last_name]',pn_strip_input($item->last_name),$text);
		$text = str_replace('[first_name]',pn_strip_input($item->first_name),$text);
		$text = str_replace('[second_name]',pn_strip_input($item->second_name),$text);			
		$text = str_replace('[ip]', pn_strip_input($item->user_ip),$text);
		$text = str_replace('[skype]', pn_strip_input($item->user_skype),$text);
		$text = str_replace('[phone]', pn_strip_input($item->user_phone),$text);
		$text = str_replace('[email]', is_email($item->user_email),$text);	
		$text = str_replace('[passport]', pn_strip_input($item->user_passport),$text);
		
		return esc_attr($text);
	}
}

add_filter('list_paymerchants', 'def_list_paymerchants', 100);
function def_list_paymerchants($list){
	asort($list);
	return $list;
}

/* проверка активности авто-выплаты */
function is_enable_paymerchant($id){
	$merchants = get_option('paymerchants');
	if(!is_array($merchants)){ $merchants = array(); }
	
	return intval(is_isset($merchants,$id));
}

/* автоматический вывод инструкции мерчанта */
add_action('instruction_paymerchant','def_instruction_paymerchant',1,2);
function def_instruction_paymerchant($instruction,$m_id){
global $premiumbox;
	
	if($m_id){
		$merch_data = get_option('paymerch_data');
		$data = is_isset($merch_data,$m_id); 
		$text = trim(ctv_ml(is_isset($data,'text')));
		if($text){
			if($instruction){ $instruction .= '<br />'; }
			return $text;
		} else {
			$show = intval($premiumbox->get_option('exchange','mp_ins'));
			if($show == 0){
				return $text;
			} else {
				return $instruction;
			}
		}
	}
	
	return $instruction;
}

function get_paymerch_data($m_id){
global $pn_paymerch_data;
	if(!is_array($pn_paymerch_data)){
		$pn_paymerch_data = (array)get_option('paymerch_data');
	}
	return is_isset($pn_paymerch_data,$m_id);
}

/* стандартная проверка всех ав */
add_filter('autopayment_filter', 'def_autopayment_filter', 1, 6);
function def_autopayment_filter($au_filter, $m_id, $item, $place, $naps_data, $paymerch_data){
	
	if(is_substitution_account($item)){
		$au_filter['error'][] = __('Data from the order were compromised', 'pn');
	}	
	
	$autopay_status = intval(get_bids_meta($item->id, 'ap_status'));
	if($autopay_status == 1){
		$au_filter['error'][] = __('Automatic payout is done', 'pn');		
	}	
	
	$sum = is_my_money(is_paymerch_sum($m_id, $item, $paymerch_data), 2);
	
	if(is_paymerch_check_sum($m_id, $sum, $place, $paymerch_data, $naps_data) != 1){
		$au_filter['error'][] = __('The amount exceeds the limit for automatic payouts for order', 'pn');		
	}				
					
	if(is_paymerch_check_day_sum($m_id, $sum, $place, $paymerch_data, $naps_data) != 1){
		$au_filter['error'][] = __('The amount exceeds the daily limit for automatic payouts', 'pn');		
	}	
	
	return $au_filter;
}

function is_paymerch_sum($m_id, $item, $paymerch_data){
	
	$where_sum = intval(is_isset($paymerch_data, 'where_sum'));
	$sum = 0;
	if($where_sum == 0){
		$sum = $item->summ2c;
	} elseif($where_sum == 1){
		$sum = $item->summ2_dc;
	} elseif($where_sum == 2){
		$sum = $item->summ2cr;
	} elseif($where_sum == 3){
		$sum = $item->summ2;
	}
	return $sum;
}

function is_substitution_account($item){
	
	$hask_keys = bid_hashkey();
	
	$hashdata = @unserialize($item->hashdata);
	if(!is_array($hashdata)){ $hashdata = array(); }
	
	foreach($hask_keys as $key){
		$value = trim(is_isset($item, $key));
		if($value){
			$hash = trim(is_isset($hashdata, $key));
			if(!is_pn_crypt($hash, $value)){
				return 1;
				break;
			}	
		}
	}	

	return 0;
}

function is_paymerch_check_sum($m_id, $sum, $place, $paymerch_data, $naps_data=array()){
	if($place == 'admin' and get_paymerch_button_maximum($m_id) == 1){
		return 1;
	}	
	$max_sum = is_my_money(is_isset($naps_data,'m_out_max_sum'));
	if($max_sum <= 0){
		$max_sum = is_my_money(is_isset($paymerch_data,'max_sum'));
	}
	if($max_sum > 0){
		$sum = is_my_money($sum);
		if($sum > $max_sum){
			return 0;
		}
	}
	return 1;
}

function is_paymerch_check_day_sum($m_id, $sum, $place, $paymerch_data, $naps_data=array()){
	if($place == 'admin' and get_paymerch_button_maximum($m_id) == 1){
		return 1;
	}	
	if($sum > 0){
		$max_sum = is_my_money(is_isset($naps_data,'m_out_max'));
		if($max_sum <= 0){
			$max_sum = is_my_money(is_isset($paymerch_data,'max'));
		}		
		if($max_sum > 0){
			$time = current_time('timestamp');
			$date = date('Y-m-d 00:00:00',$time);			
			$day_sum = get_sum_for_autopay($m_id, $date);
			$plan_sum = $day_sum + $sum;
			if($plan_sum > $max_sum){
				return 0;
			}
		}
	}
		return 1;
}

function is_paymerch_realpay($m_id){
	
	$data = get_paymerch_data($m_id); 

	return intval(is_isset($data,'realpay'));
}

function is_paymerch_verify($m_id){
	
	$data = get_paymerch_data($m_id);  

	return intval(is_isset($data,'verify'));
}

function is_paymerch_checkpay($m_id){

	$data = get_paymerch_data($m_id);  
	
	return intval(is_isset($data,'checkpay'));
}

function is_paymerch_button($m_id){

	$data = get_paymerch_data($m_id);  
	
	return intval(is_isset($data,'button'));
}

function get_paymerch_button_maximum($m_id){

	$data = get_paymerch_data($m_id);  
	
	return intval(is_isset($data,'button_maximum'));
}

if(!class_exists('AutoPayut_Premiumbox')){
	class AutoPayut_Premiumbox{
		public $name = "";
		public $m_data = "";
		public $title = "";	
		public $autopay_button_arg = "";
		
		function __construct($file, $map, $title, $autopay_button_arg='BUTTON')
		{
			$path = get_extension_file($file);
			$name = get_extension_name($path);
			$numeric = get_extension_num($name);

			$data = set_extension_data($path . '/dostup/index', $map);
			
			global $premiumbox;
			$premiumbox->file_include($path . '/class');
			
			$this->name = trim($name);
			$this->m_data = $data;
			$this->title = $title.' '.$numeric;
			$this->autopay_button_arg = $autopay_button_arg;
			
			add_filter('list_paymerchants', array($this, 'list_paymerchants'));
			add_filter('get_paymerchant_id',array($this, 'get_paymerchant_id'),1,3);
			add_filter('paymerchant_enable_autopay',array($this, 'paymerchant_enable_autopay'),1,2);
			
		}	
		
		public function list_paymerchants($list_merchants){
			$list_merchants[] = array(
				'id' => $this->name,
				'title' => $this->title
			);
			return $list_merchants;
		}

		public function get_paymerchant_id($now, $m_id, $item){
			if($m_id and $m_id == $this->name){
				if(is_enable_paymerchant($m_id)){
					return $m_id;
				} 
			}
			return $now;
		}

		public function paymerchant_enable_autopay($now, $m_id){
			global $premiumbox;
			if($m_id and $m_id == $this->name and intval(is_deffin($this->m_data, $this->autopay_button_arg)) == 1 or $premiumbox->is_debug_mode()){	
				return 1;
			}
			return $now;
		}		
	}
}