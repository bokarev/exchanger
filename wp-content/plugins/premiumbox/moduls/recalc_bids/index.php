<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Пересчет суммы обмена[:ru_RU][en_US:]Recalculation of exchange amount[:en_US]
description: [ru_RU:]Пересчет суммы обмена[:ru_RU][en_US:]Recalculation of exchange amount[:en_US]
version: 0.1
category: [ru_RU:]Направления обменов[:ru_RU][en_US:]Exchange directions[:en_US]
cat: naps
*/
$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* BD */
add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_recalcbids');
function bd_pn_moduls_active_recalcbids(){
global $wpdb;	
	
	$table_name = $wpdb->prefix ."recalc_bids";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT ,
		`naps_id` bigint(20) NOT NULL default '0',
		`enable_recalc` int(1) NOT NULL default '0',
		`cou_hour` varchar(20) NOT NULL default '0',
		`cou_minute` varchar(20) NOT NULL default '0',
		`statused` longtext NOT NULL,
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);	
	
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."bids LIKE 'recalcdate'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."bids ADD `recalcdate` datetime NOT NULL");
    }	
	
}
/* end BD */

add_filter('list_tabs_naps','list_tabs_naps_recalcbids');
function list_tabs_naps_recalcbids($list_tabs_naps){
	
	$list_tabs_naps['recalcbids'] = __('Recalculation of exchange amount','pn');
	
	return $list_tabs_naps;
}

add_action('tab_naps_recalcbids','naps_tab_recalcbids',10,2);
function naps_tab_recalcbids($data, $data_id){	
global $wpdb, $premiumbox;
 	if(isset($data->id)){ 
		$data_id = $data->id;
		$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."recalc_bids WHERE naps_id='$data_id'"); 
		$cou_hour = intval(is_isset($item, 'cou_hour'));
		$enable_recalc = intval(is_isset($item, 'enable_recalc'));
		$lists = array(
			'new' => __('new order','pn'),
			'techpay' => __('when user entered payment section','pn'),
			'payed' => __('user marked order as paid','pn'),
			'coldpay' => __('waiting for merchant confirmation','pn'),
		);		
	?>
		<tr>
			<th><?php _e('Enable recalculation of exchange amount','pn'); ?></th> 
			<td colspan="2">
				<div class="premium_wrap_standart">												
					<select name="enable_recalc" autocomplete="off"> 
						<option value="0" <?php selected($enable_recalc,0); ?>><?php _e('No','pn'); ?></option>
						<option value="1" <?php selected($enable_recalc,1); ?>><?php _e('Yes','pn'); ?></option>					
						<option value="2" <?php selected($enable_recalc,2); ?>><?php _e('Yes, if rate increased','pn'); ?></option>	
						<option value="3" <?php selected($enable_recalc,3); ?>><?php _e('Yes, if rate decreased','pn'); ?></option>
					</select>
				</div>
			</td>
		</tr>	
		<tr>
			<th><?php _e('Order status for recalculation','pn'); ?></th>
			<td colspan="2">
				<div class="premium_wrap_standart">				
					<div class="cf_div">
						<div style="font-weight: 500;"><label><input type="checkbox" class="check_all" name="" value="1" /> <?php _e('Check all/Uncheck all','pn'); ?></label></div>
						<?php
						$string = trim(is_isset($item,'statused'));
						$def = array();
						if(preg_match_all('/\[d](.*?)\[\/d]/s',$string, $match, PREG_PATTERN_ORDER)){
							$def = $match[1];
						}
						foreach($lists as $key => $title){ 
						?>	
							<div><label><input type="checkbox" name="recalcbids_statused[]" <?php if(in_array($key,$def)){ ?>checked="checked"<?php } ?> value="<?php echo $key; ?>" /> <?php echo $title;?></label></div>	
						<?php
						}	
						?>
					</div>				
				</div>
			</td>
		</tr>	
		<tr>
			<th><?php _e('Perform recalculation through','pn'); ?> (<?php _e('hours', 'pn'); ?>)</th>
			<td colspan="2">
				<div class="premium_wrap_standart">
					<input type="text" name="recalcbids_cou_hour" style="width: 50px;" value="<?php echo $cou_hour; ?>" />
				</div>			
			</td>
		</tr>			
	<?php 
	} 
} 	 

add_action('pn_naps_edit_before', 'pn_naps_edit_recalcbids', 10, 2);
add_action('pn_naps_add', 'pn_naps_edit_recalcbids', 10, 2);
function pn_naps_edit_recalcbids($data_id, $array){
global $wpdb;	

	if($data_id){
		$enable_recalc = intval(is_param_post('enable_recalc'));		
		if($enable_recalc > 0){
			$arr = array();
			$arr['enable_recalc'] = $enable_recalc;
			$arr['naps_id'] = $data_id;
			$arr['cou_hour'] = intval(is_param_post('recalcbids_cou_hour'));
			$autodelbids_statused = is_param_post('recalcbids_statused');
			$statused = '';
			if(is_array($autodelbids_statused)){
				foreach($autodelbids_statused as $st){
					$st = is_status_name($st);
					if($st){
						$statused .= '[d]'. $st .'[/d]';
					}
				}
			}
			$arr['statused'] = $statused;			
			$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."recalc_bids WHERE naps_id='$data_id'"); 
			if(isset($item->id)){
				$wpdb->update($wpdb->prefix."recalc_bids", $arr, array('id'=>$item->id));
			} else {
				$wpdb->insert($wpdb->prefix."recalc_bids", $arr);
			}
		} else {
			$wpdb->query("DELETE FROM ".$wpdb->prefix."recalc_bids WHERE naps_id = '$data_id'");
		}
	}
}

add_action('pn_naps_copy', 'pn_naps_copy_recalcbids', 10, 2);
function pn_naps_copy_recalcbids($last_id, $data_id){
global $wpdb;	
	$data = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."recalc_bids WHERE naps_id='$last_id'");
	foreach($data as $item){
		$arr = array();
		foreach($item as $k => $v){
			$arr[$k] = $v;
		}
		$arr['naps_id'] = $data_id;
		$wpdb->insert($wpdb->prefix.'recalc_bids', $arr);
	}	
}

add_action('pn_adminpage_quicktags_pn_add_naps','recalcbids_adminpage_quicktags_page_naps');
add_action('pn_adminpage_quicktags_pn_naps_temp','recalcbids_adminpage_quicktags_page_naps');
function recalcbids_adminpage_quicktags_page_naps(){
?>
edButtons[edButtons.length] = 
new edButton('premium_recalcbids', '<?php _e('Recalculate time','pn'); ?>','[bid_recalc]');
<?php	
} 

add_filter('bid_instruction_tags','recalcbids_bid_instruction_tags', 1000, 2);
function recalcbids_bid_instruction_tags($instruction, $item){
global $wpdb, $premiumbox;
	if(strstr($instruction,'[bid_recalc]') and isset($item->status)){
		$bid_recalc = __('undefined','pn');
		$naps_id = $item->naps_id;
		$data = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."recalc_bids WHERE naps_id='$naps_id'");
		$cou_hour = intval(is_isset($data,'cou_hour'));
		if($cou_hour > 0){
			$bid_recalc = sprintf(__('Order will be recalculate every %s hour(s)','pn'), $cou_hour);
		}
		$instruction = str_replace('[bid_recalc]', $bid_recalc, $instruction); 
	}	
	
	return $instruction;
}

function recalculation_bids(){
global $wpdb, $premiumbox;

	$time = current_time('timestamp');
	$date = current_time('mysql');
	
	$naps = array();
	$data_naps = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."naps WHERE naps_status='1' AND autostatus='1'");
	foreach($data_naps as $nap){
		$naps[$nap->id] = $nap;
	}
	
	$v = get_valuts_data();
	
	$recalcs = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."recalc_bids");
	foreach($recalcs as $rec){
		$naps_id = $rec->naps_id;
		$hours = $rec->cou_hour;
		$enable_recalc = $rec->enable_recalc;
		$in_status = array();
		$string = trim($rec->statused);
		if(preg_match_all('/\[d](.*?)\[\/d]/s',$string, $match, PREG_PATTERN_ORDER)){
			if(is_array($match[1])){
				foreach($match[1] as $st){
					$st = is_status_name($st);
					if($st){
						$in_status[] = "'". $st ."'";
					}						
				}
			}
		}  
		if(isset($naps[$naps_id])){
			$nap = $naps[$naps_id];
			$now_time = $time - ($hours * 60 * 60);
			$now_date = date('Y-m-d H:i:s', $now_time); 
			if(count($in_status) > 0){
				$in_join = join(',',$in_status);
				$items = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."bids WHERE status IN($in_join) AND naps_id='$naps_id' AND editdate < '$now_date' AND recalcdate < '$now_date'");
				foreach($items as $item){
					$arr = array();
					$arr['recalcdate'] = $date;
					
					$lcurs1 = $item->curs1;
					$lcurs2 = $item->curs2;
					$curs1 = $nap->curs1;
					$curs2 = $nap->curs2;
					
					$dej = 0; /* 1- увеличили, 2 - уменьшили */
					$c_old = $c_now = 0;
					if($lcurs1 != $curs1){
						$c_old = $lcurs1;
						$c_now = $curs1;
					} elseif($lcurs2 != $curs2){
						$c_old = $lcurs2;
						$c_now = $curs2;
					}
					if($c_now > $c_old){
						$dej = 1;
					} elseif($c_old > $c_now){
						$dej = 2;
					}
					

					if(isset($v[$nap->valut_id1]) and isset($v[$nap->valut_id2])){
						$up = 0;
						if(
							$enable_recalc == 1 or 
							$enable_recalc == 2 and $dej == 1 or
							$enable_recalc == 3 and $dej == 2
						){
							$up = 1;
							$cdata = get_calc_data($v[$nap->valut_id1], $v[$nap->valut_id2], $nap, $item->user_id, $item->summ1, $item->check_purse1, $item->check_purse2, 1);
							$arr['curs1'] = $cdata['curs1'];
							$arr['curs2'] = $cdata['curs2'];							
							$arr['user_sk'] = $cdata['user_discount'];
							$arr['user_sksumm'] = $cdata['user_sksumm'];
							$arr['exsum'] = $cdata['exsum'];
							$arr['summ1'] = $cdata['summ1'];
							$arr['dop_com1'] = $cdata['dop_com1'];
							$arr['summ1_dc'] = $cdata['summ1_dc'];
							$arr['com_ps1'] = $cdata['com_ps1'];
							$arr['summ1c'] = $cdata['summ1c'];
							$arr['summ1cr'] = $cdata['summ1cr'];
							$arr['summ2t'] = $cdata['summ2t'];
							$arr['summ2'] = $cdata['summ2'];
							$arr['dop_com2'] = $cdata['dop_com2'];
							$arr['com_ps2'] = $cdata['com_ps2'];
							$arr['summ2_dc'] = $cdata['summ2_dc'];
							$arr['summ2c'] = $cdata['summ2c'];
							$arr['summ2cr'] = $cdata['summ2cr'];	
							$arr['profit'] = $cdata['profit'];

						}
						$wpdb->update($wpdb->prefix ."bids", $arr, array('id'=>$item->id));
						if($up == 1){
							bid_hashdata($item->id, '', '');
						}
					}
				}
			}	
		}
	}
}  

add_filter('mycron_now', 'mycron_now_recalcbids');
function mycron_now_recalcbids($filters){
	$filters['recalculation_bids'] = __('Recalculation of exchange amount','pn');
	return $filters;
}