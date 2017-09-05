<?php
if( !defined( 'ABSPATH')){ exit(); }

add_filter('list_merchants', 'def_list_merchants', 100);
function def_list_merchants($list){
	asort($list);
	return $list;
}

function is_enable_merchant($id){
	$merchants = get_option('merchants');
	if(!is_array($merchants)){ $merchants = array(); }
	
	return intval(is_isset($merchants,$id));
}

/* конструктор примечания */
function get_text_pay($m_id, $item, $pay_sum){
	if($m_id and isset($item->id)){
		$merch_data = get_option('merch_data');
		$data = is_isset($merch_data,$m_id);
		$text = trim(ctv_ml(is_isset($data,'note')));
		
		$fio_arr = array($item->last_name, $item->first_name, $item->second_name);
		$fio_arr = array_unique($fio_arr);
		$fio = pn_strip_input(join(' ',$fio_arr));
		
		$text = apply_filters('get_text_pay', $text, $m_id, $item);
		$text = str_replace('[id]',$item->id, $text);
		$text = str_replace('[paysum]', $pay_sum, $text);
		$text = str_replace('[sum1]', is_my_money($item->summ1_dc), $text); 
		$text = str_replace('[valut1]', pn_strip_input(ctv_ml($item->valut1)) .' '. pn_strip_input($item->vtype1),$text);	
		$text = str_replace('[sum2]', is_my_money($item->summ2c),$text);
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
		$text = str_replace('[phone]', is_phone($item->user_phone),$text);
		$text = str_replace('[email]', is_email($item->user_email),$text);	
		$text = str_replace('[passport]', pn_strip_input($item->user_passport),$text);		
		
		return esc_attr(trim($text));
	}
}

/* автоматический вывод инструкции мерчанта */
add_action('instruction_merchant','def_instruction_merchant',1,2);
function def_instruction_merchant($instruction,$m_id){
global $premiumbox;
	
	if($m_id){	
		$merch_data = get_option('merch_data');
		$data = is_isset($merch_data,$m_id); 
		$text = trim(ctv_ml(is_isset($data,'text')));
		if($text){
			if($instruction){ $instruction .= '<br />'; }
			return $text;
		} else {
			$show = intval($premiumbox->get_option('exchange','m_ins'));
			if($show == 0){
				return $text;
			} else {
				return $instruction;
			}
		}
	}
	
	return $instruction;
} 

/* проверка по IP */
add_action('merchant_logs','enableip_merchant_logs',1);
function enableip_merchant_logs($m_id){
global $premiumbox;
	
	if($m_id){	
		$merch_data = get_option('merch_data');
		$data = is_isset($merch_data,$m_id); 
		$enable_ip = explode("\n", is_isset($data, 'enableip'));
		$yes_ip = '';
		foreach($enable_ip as $v){
			$v = pn_strip_input($v);
			if($v){
				$yes_ip .= '[d]'. $v .'[/d]';
			}
		}
		$user_ip = pn_real_ip();
		if($yes_ip and enable_to_ip($user_ip, $yes_ip) == 1){
			die(sprintf(__('IP adress (%s) is blocked','pn'), $user_ip));
			exit;
		}
	}
} 

function get_merch_data($m_id){
global $pn_merch_data;
	if(!is_array($pn_merch_data)){
		$pn_merch_data = (array)get_option('merch_data');
	}
	return is_isset($pn_merch_data, $m_id);
}

function is_check_wallet($m_id){
	$data = get_merch_data($m_id); 
	return intval(is_isset($data,'check'));
}

function is_check_payapi($m_id){
	$data = get_merch_data($m_id); 
	return intval(is_isset($data,'check_payapi'));
}

function get_hash_result_url($m_id){
	$data = get_merch_data($m_id); 
	$hash = trim(is_isset($data,'resulturl'));
	if($hash){ $hash = '_' . $hash; }
	return $hash;
}

function get_corr_sum($m_id){
	$data = get_merch_data($m_id); 
	return is_my_money(is_isset($data,'corr'));
}

add_filter('merchant_bid_sum', 'def_merchant_bid_sum', 10, 2);
function def_merchant_bid_sum($sum, $m_id){
	
	$corr = get_corr_sum($m_id);
	if($corr != 0){
		$sum = $sum - $corr;
	}
	
	return $sum;
}

function is_type_merchant($m_id){
	$data = get_merch_data($m_id); 
	return intval(is_isset($data,'type'));
} 

function is_pay_purse($payer, $data, $m_id){
	return apply_filters('pay_purse_merchant', $payer, is_isset($data,'check_purse'), $m_id);
}

add_filter('pay_purse_merchant', 'def_pay_purse_merchant',10,3);
function def_pay_purse_merchant($purse, $check, $m_id){
	$purse = str_replace(array('+',' '),'',$purse);
	if($check == 0){
		$purse = '';
	}	
	return $purse;
}

/* идентификатор */
function get_payment_id($arg){
	$id = intval(is_param_post($arg));
	if(!$id){ $id = intval(is_param_get($arg)); }
	return $id;
}

/* действие, при отказе от оплаты */
function the_merchant_bid_delete($id){
global $wpdb;
	$id = intval($id);	
	if($id){
		$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."bids WHERE id='$id' AND status != 'auto'");
		if(isset($item->id)){
			
			$hashed = is_bid_hash($item->hashed);
			$url = get_bids_url($hashed);
			wp_redirect($url);
			exit;		

		} else {
			pn_display_mess(__('You refused a payment','pn'));
		}
	} else {
		pn_display_mess(__('You refused a payment','pn'));
	}	
}

/* действие, при успешной оплате */
function the_merchant_bid_success($id){
global $wpdb;
	$id = intval($id);	
	if($id){
		$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."bids WHERE id='$id' AND status != 'auto'");
		if(isset($item->id)){
			
			$hashed = is_bid_hash($item->hashed);
			$url = get_bids_url($hashed);
			wp_redirect($url);
			exit;		

		} else {
			pn_display_mess(__('You have successfully paid','pn'),__('You have successfully paid','pn'),'true');
		}
	} else {
		pn_display_mess(__('You have successfully paid','pn'),__('You have successfully paid','pn'),'true');
	}	
}

/* помечаем заявку как оплаченную */
function the_merchant_bid_status($status, $id, $system='user', $ap=0, $place='', $params=array()){
global $wpdb;	

	$sum = is_isset($params, 'sum');
	$pay_purse = is_isset($params, 'pay_purse');
	$soschet = is_isset($params, 'soschet'); 
	$naschet = is_isset($params, 'naschet'); 
	$trans_in = is_isset($params, 'trans_in'); 
	$trans_out = is_isset($params, 'trans_out');

	$id = intval($id);
	$system = trim($system);
	if($system != 'system'){ $system = 'user'; }
	
	$status = is_status_name($status);
	if($id and $status){
		$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."bids WHERE id='$id' AND status != 'auto'");
		if(isset($item->id)){
			$bid_status = $item->status;
			if($bid_status != $status){

				$pay_purse = pn_maxf_mb(pn_strip_input($pay_purse),500);
				$sum = is_my_money($sum);
				$soschet = pn_maxf_mb(pn_strip_input($soschet),500);
				$naschet = pn_maxf_mb(pn_strip_input($naschet),500);
				$trans_in = pn_maxf_mb(pn_strip_input($trans_in),500);	
				$trans_out = pn_maxf_mb(pn_strip_input($trans_out),500);
				$account = str_replace(' ','',$item->account1);
				$ap = intval($ap);
					
				$arr = array(
					'editdate'=> current_time('mysql') 
				);	
					
				$tables = array();
					
				if($soschet){
					$arr['soschet'] = $soschet;
					$tables[] = 'soschet';
				}	
				if($naschet){
					$arr['naschet'] = $naschet;
					$tables[] = 'naschet';
				}					
				if($trans_in){
					$arr['trans_in'] = $trans_in;
					$tables[] = 'trans_in';				
				}
				if($trans_out){
					$arr['trans_out'] = $trans_out;
					$tables[] = 'trans_out';				
				}					
					
				$def_sum = is_my_money($item->summ1_dc);
				if($sum > $def_sum){
					$arr['exceed_pay'] = 1;
				}			

				$arr['status'] = $status;
					
				if($arr['status'] == 'realpay'){
					if($pay_purse and $account and $pay_purse != $account){
						$arr['status'] = 'verify';
					}	
				}
					
				$result = $wpdb->update($wpdb->prefix.'bids', $arr, array('id'=>$item->id));
				if($result == 1){
					do_action('change_bidstatus_all', $arr['status'], $item->id, $item, 'site', $system);
					do_action('change_bidstatus_' . $arr['status'], $item->id, $item, 'site', $system);	
					bid_hashdata($id, '', $tables);
					if($ap == 1 and $arr['status'] == 'success'){
						do_action('set_autopayouts', $item, $place);
					}
				}					
					
				if($sum > 0){
					update_bids_meta($item->id, 'pay_sum', $sum);
				}
				if($pay_purse){
					update_bids_meta($item->id, 'pay_ac', $pay_purse);
				}
				
			}
		}	
	}
}	

/* стандартные данные для мерчантов */
add_filter('summ_to_pay','def_summ_to_pay',1,4);
function def_summ_to_pay($sum, $m_id ,$item, $naps){
	
	if($m_id){
		if(isset($naps->id) and isset($item->id)){
			
			$vid = is_type_merchant($m_id);
			if($naps->pay_com1 == 0 and $vid == 1){ /* если оплачивает юзер */
			
				$pers = $naps->com_pers1;
				$sumc = $naps->com_summ1;
				if(isset($item->check_purse1) and $item->check_purse1 == 1){
					$pers = $naps->com_pers1_check;
					$sumc = $naps->com_summ1_check;
				}	
				$comis = array(
					'pers' => $pers,
					'sum' => $sumc,
				);
				$comis = apply_filters('merchant_comission_work', $comis, $item, $naps);
			
				$pers = $comis['pers'];
				$sumc = $comis['sum'];
			
				$sc = $naps->nscom1;
				$new_sum = 0;
				if($pers > 0){
					if($sc == 0){
						$new_sum = is_my_money($item->summ1_dc + ($item->summ1_dc / 100 * $pers));						
					} else { /* нестандартная комиссия */
						$new_sum = is_my_money(pers_alter_summ($item->summ1_dc, $pers));
					}
				}
				
				$comis = $new_sum - $sum + $sumc;
				$min = $naps->minsumm1com;
				$max = $naps->maxsumm1com;
				if($comis < $min){ $comis = $min; }
				if($max > 0 and $comis > $max){ $comis = $max; }
				return $sum + $comis;
				
			} 
			
		} 
	}	
	
	return $sum;
}

function get_data_merchant_for_id($id, $item=''){
global $wpdb;	

    $id = intval($id);
	$array = array();
	$array['err'] = 0;
	$array['status'] = $array['vtype'] = $array['hashed'] = $array['m_id'] = '';
	$array['sum'] = $array['pay_com'] = $array['com_pers'] = $array['com_sum'] = $array['nscom'] = $array['check_purse'] = $array['pay_sum'] = 0;
	$array['bids_data'] = $array['naps_data'] = array();
	
	if($id){
		if(!is_object($item)){
			$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."bids WHERE id='$id'");
		}
		if(isset($item->id)){
			
			$array['err'] = 0;
			$array['status'] = is_status_name($item->status);
			$array['sum'] = is_my_money($item->summ1_dc);
			$array['vtype'] = is_site_value($item->vtype1);
			$array['hashed'] = is_bid_hash($item->hashed);
			$array['pay_sum'] = is_my_money($item->summ1_dc);
			$array['bids_data'] = (array)$item;
			
			$naps_id = intval($item->naps_id);
			$naps = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."naps WHERE autostatus='1' AND id='$naps_id'");
			if(isset($naps->m_in)){ 
			
				$array['naps_data'] = (array)$naps;
			
				$m_id = apply_filters('get_merchant_id','',$naps->m_in, $item);
				$array['m_id'] = $m_id;
				
				$array['check_purse'] = is_check_wallet($m_id);
				$array['pay_com'] = is_my_money($naps->pay_com1);
				$array['nscom'] = pn_strip_input($naps->nscom1);
				
				$com_pers1 = is_my_money($naps->com_pers1);
				$com_sum1 = is_my_money($naps->com_summ1);
				if(isset($item->check_purse1) and $item->check_purse1 == 1){
					$com_pers1 = is_my_money($naps->com_pers1_check);
					$com_sum1 = is_my_money($naps->com_summ1_check);
				}
				
				$comis = array(
					'pers' => $com_pers1,
					'sum' => $com_sum1,
				);
				$comis = apply_filters('merchant_comission_work', $comis, $item, $naps);
			
				$com_pers1 = $comis['pers'];
				$com_sum1 = $comis['sum'];				
				
				$array['com_pers'] = $com_pers1;
				$array['com_sum'] = $com_sum1;
				
				if($naps->pay_com1 == 1){
						
					$bid_sum = is_my_money($item->summ1_dc);
					$onepers = $bid_sum / 100 * $com_pers1;
					$pay_sum = $bid_sum - $onepers - $com_sum1;
					$array['pay_sum'] = pn_strip_input($pay_sum);
						 
				} 				
				
			}
	
		} else {
			$array['err'] = 2;	
		}
	} else {
		$array['err'] = 1;
	}
	
	return $array;
}

if(!class_exists('Merchant_Premiumbox')){
	class Merchant_Premiumbox{
		public $name = "";
		public $m_data = "";
		public $title = "";		
		
		function __construct($file, $map, $title)
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
			
			add_filter('list_merchants', array($this, 'list_merchants'));
			add_filter('get_merchant_id',array($this, 'get_merchant_id'),1,3);
			
		}	
		
		public function list_merchants($list_merchants){
			$list_merchants[] = array(
				'id' => $this->name,
				'title' => $this->title
			);
			return $list_merchants;
		}

		public function get_merchant_id($now, $m_id, $item){
			if($m_id and $m_id == $this->name){
				if(is_enable_merchant($m_id)){
					return $m_id;
				} 
			}
			return $now;
		}		
		
	}
}