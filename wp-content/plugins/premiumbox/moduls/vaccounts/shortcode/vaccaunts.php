<?php
if( !defined( 'ABSPATH')){ exit(); }
 
add_action('pn_adminpage_quicktags_pn_add_naps','adminpage_quicktags_page_vaccounts');
add_action('pn_adminpage_quicktags_pn_naps_temp','adminpage_quicktags_page_vaccounts');
function adminpage_quicktags_page_vaccounts(){
?>
edButtons[edButtons.length] = 
new edButton('premium_vaccounts', '<?php _e('Account','pn'); ?>','[num_schet valuts="" vid="" hide="0"]');
<?php	
}
   
add_shortcode('num_schet', 'num_schet_shortcode');
function num_schet_shortcode($atts, $content){
global $wpdb, $bids_data;

	if(isset($bids_data->id)){
		
		$n_atts = array();
		if(is_array($atts)){
			foreach($atts as $k => $v){
				$n_atts[$k] = str_replace(array('&quot;','&#039;'),'',$v);
			}
		}		
		
		$hide = intval(is_isset($n_atts,'hide'));
		$valut_id = intval(is_isset($n_atts,'valuts'));
		if(!$valut_id){ $valut_id = $bids_data->valut1i; } 
		$vid = intval(is_isset($n_atts,'vid'));
		$idbid = intval(is_isset($bids_data,'id'));
		$naschet = pn_maxf_mb(pn_strip_input(is_isset($bids_data,'naschet')),500);
		$sum = is_my_money(is_isset($bids_data,'summ1_dc'));
	
		$theval = get_now_vaccount($valut_id, $vid, $idbid, $sum, $naschet, array());
		if(!$hide){
			return $theval;
		}
		
	} else {
		return '[error]';
	}
}

function get_now_vaccount($valut_id, $vid, $bidid, $sum, $naschet,$not){
global $wpdb;
	
	if(!is_array($not)){ $not = array(); }
	$where = '';
	if(count($not) > 0){
		$notted = join(',',$not);
		$where = " AND id NOT IN($notted)";
	}
	
	$time = current_time('timestamp');
	$date1 = date('Y-m-d 00:00:00',$time);
	$date2 = date('Y-m-01 00:00:00',$time);
	
	if($vid == 0){ /* показывать случайно один раз */
	
		$val = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."valuts_account WHERE count_visit='0' AND valut_id='$valut_id' AND status='1' $where ORDER BY RAND()"); 
		if(isset($val->id)){
			$val_id = $val->id;
			$accountnum = pn_strip_input(get_vaccs_txtmeta($val_id, 'accountnum'));
			$max_visit = intval($val->max_visit);
			$max_day = pn_strip_input($val->inday);
			$max_month = pn_strip_input($val->inmonth);
			if($max_day > 0){
				$now_day = get_vaccount_sum($accountnum, 'in', $date1);
				$now_day = $now_day + $sum;
				if($now_day > $max_day){
					$not[] = "'{$val_id}'";
					
					return get_now_vaccount($valut_id, $vid, $bidid, $sum,$naschet, $not);
				}
			}
			if($max_month > 0){
				$now_month = get_vaccount_sum($accountnum, 'in', $date2);
				$now_month = $now_month + $sum;
				if($now_month > $max_month){
					$not[] = "'{$val_id}'";
					
					return get_now_vaccount($valut_id, $vid, $bidid, $sum,$naschet, $not);
				}
			}			
			
			$array = array();
			$array['count_visit'] = $val->count_visit+1;
			$wpdb->update($wpdb->prefix."valuts_account",$array,array('id'=>$val->id));	

			update_bids_naschet($bidid, $accountnum);		
			
			return $accountnum;
		} else {
			return apply_filters('not_vaccaunt_now', '<span class="not_vaccaunt_now">'.__('Please contact us to provide your account number','pn').'</span>');
		}
		
	} elseif($vid == 1){ /* показывать случайно */
	
		$val = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."valuts_account WHERE valut_id='$valut_id' AND status='1' $where ORDER BY RAND()"); 
		if(isset($val->id)){
			$val_id = $val->id;
			$accountnum = pn_strip_input(get_vaccs_txtmeta($val_id, 'accountnum'));
			$max_visit = intval($val->max_visit);
			if($val->count_visit >= $max_visit and $max_visit > 0){
				$not[] = "'{$val_id}'";
					
				return get_now_vaccount($valut_id, $vid, $bidid, $sum,$naschet, $not);				
			}
			$max_day = pn_strip_input($val->inday);
			$max_month = pn_strip_input($val->inmonth);
			if($max_day > 0){
				$now_day = get_vaccount_sum($accountnum, 'in', $date1);
				$now_day = $now_day + $sum;
				if($now_day > $max_day){
					$not[] = "'{$val_id}'";
					
					return get_now_vaccount($valut_id, $vid, $bidid, $sum,$naschet, $not);
				}
			}
			if($max_month > 0){
				$now_month = get_vaccount_sum($accountnum, 'in', $date2);
				$now_month = $now_month + $sum;
				if($now_month > $max_month){
					$not[] = "'{$val_id}'";
					
					return get_now_vaccount($valut_id, $vid, $bidid, $sum,$naschet, $not);
				}
			}			
			
			$array = array();
			$array['count_visit'] = $val->count_visit+1;
			$wpdb->update($wpdb->prefix."valuts_account",$array,array('id'=>$val->id));	

			update_bids_naschet($bidid, $accountnum);		
			
			return $accountnum;
		} else {
			return apply_filters('not_vaccaunt_now', '<span class="not_vaccaunt_now">'.__('Please contact us to provide your account number','pn').'</span>');
		}		
		
	} elseif($vid == 2){ /* отображать счет постоянно в рамках одной заявки */
	
		$accountnum = pn_maxf_mb(pn_strip_input($naschet),500);
		if($accountnum){
			
			return $accountnum;
			
		} else {

			$val = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."valuts_account WHERE valut_id='$valut_id' AND status='1' $where ORDER BY RAND()"); 
			if(isset($val->id)){
				$val_id = $val->id;
				$accountnum = pn_strip_input(get_vaccs_txtmeta($val_id, 'accountnum'));
				$max_visit = intval($val->max_visit);
				if($val->count_visit >= $max_visit and $max_visit > 0){
					$not[] = "'{$val_id}'";
						
					return get_now_vaccount($valut_id, $vid, $bidid, $sum,$naschet, $not);				
				}
				$max_day = pn_strip_input($val->inday);
				$max_month = pn_strip_input($val->inmonth);
				if($max_day > 0){
					$now_day = get_vaccount_sum($accountnum, 'in', $date1);
					$now_day = $now_day + $sum;
					if($now_day > $max_day){
						$not[] = "'{$val_id}'";
						
						return get_now_vaccount($valut_id, $vid, $bidid, $sum,$naschet, $not);
					}
				}
				if($max_month > 0){
					$now_month = get_vaccount_sum($accountnum, 'in', $date2);
					$now_month = $now_month + $sum;
					if($now_month > $max_month){
						$not[] = "'{$val_id}'";
						
						return get_now_vaccount($valut_id, $vid, $bidid, $sum,$naschet, $not);
					}
				}			
				
				$array = array();
				$array['count_visit'] = $val->count_visit+1;
				$wpdb->update($wpdb->prefix."valuts_account",$array,array('id'=>$val->id));	

				update_bids_naschet($bidid, $accountnum);		
				
				return $accountnum;
			} else {
				return apply_filters('not_vaccaunt_now', '<span class="not_vaccaunt_now">'.__('Please contact us to provide your account number','pn').'</span>');
			}
			
		}
		
	} 
}