<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_pn_migrate', 'pn_adminpage_title_pn_migrate');
function pn_adminpage_title_pn_migrate($page){
	_e('Migration','pn');
} 

/* настройки */
add_action('pn_adminpage_content_pn_migrate','def_pn_adminpage_content_pn_migrate');
function def_pn_adminpage_content_pn_migrate(){
?>
<div class="premium_body">
	<table class="premium_standart_table">
		<?php
		pn_h3(sprintf(__('Migration (if version is lesser than %s)','pn'),'1.0'), '');
		
		$r=0;
		while($r++<18){
		?>		
		<tr>
			<td>		
				<input name="submit" type="submit" class="button pn_prbar" data-count-url="<?php pn_the_link_ajax('migrate_step_count'); ?>&step=1_<?php echo $r; ?>" data-title="<?php printf(__('Step %s','pn'),$r); ?>" value="<?php printf(__('Step %s','pn'),$r); ?>" />	
				<input name="submit" type="submit" class="button pn_prbar" data-count-url="<?php pn_the_link_ajax('migrate_step_count'); ?>&step=1_<?php echo $r; ?>&tech=1" data-title="<?php printf(__('Step %s','pn'),$r); ?>" value="<?php printf(__('Technical step %s','pn'),$r); ?>" />		
			</td>
		</tr>
		<?php 
		} 
		?>
	</table>
</div>

<div class="premium_body">
	<table class="premium_standart_table">
		<?php
		pn_h3(sprintf(__('Migration (if version is lesser than %s)','pn'),'1.2'), '');
		
		$r=0;
		while($r++<7){
		?>		
		<tr>
			<td>		
				<input name="submit" type="submit" class="button pn_prbar" data-count-url="<?php pn_the_link_ajax('migrate_step_count'); ?>&step=2_<?php echo $r; ?>" data-title="<?php printf(__('Step %s','pn'),$r); ?>" value="<?php printf(__('Step %s','pn'),$r); ?>" />	
				<input name="submit" type="submit" class="button pn_prbar" data-count-url="<?php pn_the_link_ajax('migrate_step_count'); ?>&step=2_<?php echo $r; ?>&tech=1" data-title="<?php printf(__('Step %s','pn'),$r); ?>" value="<?php printf(__('Technical step %s','pn'),$r); ?>" />		
			</td>
		</tr>
		<?php 
		} 
		?>
	</table>
</div>
	
<div class="premium_shadow js_techwindow"></div>
<div class="prbar_wrap js_techwindow">
	<div class="prbar_wrap_ins">
		<div class="prbar_close"></div>
		<div class="prbar_title"></div>
		<div class="prbar_content">
		
			<div class="prbar_num">
				<?php printf(__('Found: %1s %2s %3s requests','pn'), '<input type="text" name="" class="prbar_num_count" value="','0','" />'); ?>
			</div>
			<div class="prbar_control">
				<div class="prbar_input">
					<?php _e('Perform','pn'); ?>: <input type="text" name="" class="prbar_count" value="100" />
				</div>
				<div class="prbar_submit"><?php _e('Run','pn'); ?></div>
					<div class="premium_clear"></div>
			</div>
			
			<div class="prbar_ind"><div class="prbar_ind_abs"></div><div class="prbar_ind_text">0%</div></div>
			<div class="prbar_log_wrap">
				<div class="prbar_log"></div>			
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
jQuery(function($){
	$(document).PrBar({ 
		trigger: '.pn_prbar',
		start_title: '<?php _e('determining the number of requests','pn'); ?>...',
		end_title: '<?php _e('number of requests defined','pn'); ?>',
		line_text: '<?php _e('%now% of %max% steps completed','pn'); ?>',
		line_success: '<?php _e('step %now% is successful','pn'); ?>',
		end_progress: '<?php _e('action is completed','pn'); ?>',
		success: function(res){
			res.prop('disabled', true);
		}
	});
});
</script>
<?php
}

add_action('premium_action_migrate_step_count','def_premium_action_migrate_step_count');
function def_premium_action_migrate_step_count(){
global $wpdb;	

	only_post();

	$log = array();
	$log['status'] = '';
	$log['status_code'] = 0; 
	$log['status_text'] = '';
	$log['count'] = 0;
	$log['link'] = '';
	
	$step = is_param_get('step');
	$tech = intval(is_param_get('tech'));
	if(current_user_can('administrator')){
		$count = 0;
		
		if(!$tech){
			
			if($step == '1_1'){
				$count = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."naps");				
			}
			
			if($step == '1_2'){
				$count = 1;				
			}
			
			if($step == '1_3'){
				$count = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."valuts");				
			}	
			
			if($step == '1_4'){
				$count = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."valuts");				
			}		
			
			if($step == '1_5'){
				$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'naps_lang'");
				if ($query == 1){
					$count = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."naps WHERE autostatus='1'");
				}
			}

			if($step == '1_6'){
				$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'not_country'");
				if ($query == 1){
					$count = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."naps WHERE autostatus='1'");
				}
			}

			if($step == '1_7'){
				$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'not_ip'");
				if ($query == 1){
					$count = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."naps WHERE autostatus='1'");
				}
			}

			if($step == '1_8'){			
				$query = $wpdb->query("SHOW COLUMNS FROM ". $wpdb->prefix ."bids LIKE 'new_user'");
				if ($query == 1){		
					$count = $wpdb->query("SELECT ".$wpdb->prefix."bids.id FROM ".$wpdb->prefix."bids LEFT OUTER JOIN ". $wpdb->prefix ."bids_meta ON(".$wpdb->prefix."bids.id = ". $wpdb->prefix ."bids_meta.item_id) WHERE ". $wpdb->prefix ."bids_meta.meta_key='new' AND ". $wpdb->prefix ."bids_meta.meta_value='1'");
				}			
			}	
			
			if($step == '1_9'){	
				$tables = array(
					'_login_check','_warning_mess','_partners','_reviews','_reviews_meta','_user_discounts','_blacklist','_geoip_blackip','_geoip_whiteip','_geoip_template',
					'_geoip_iplist','_geoip_country','_psys','_archive_data','_vtypes','_valuts','_valuts_meta','_operator_schedules','_user_accounts','_partner_pers','_plinks',
					'_payoutuser','_user_fav','_uv_field','_uv_field_user','_userverify','_uv_accounts','_custom_fields_valut','_custom_fields','_cf_naps','_naps',
					'_naps_meta','_masschange','_bidstatus','_trans_reserv','_valuts_account','_bids','_bids_meta','_admin_captcha','_bid_logs','_naps_sumcurs','_standart_captcha',
					'_inex_system','_inex_tars','_inex_deposit','_inex_change'
				);
				$count = count($tables);
			}
			
			if($step == '1_10'){
				$count = 1;				
			}
			
			if($step == '1_11'){
				$count = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."valuts");				
			}
			
			if($step == '1_12'){
				$count = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."custom_fields_valut");				
			}
			
			if($step == '1_13'){
				$count = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."custom_fields");				
			}	

			if($step == '1_14'){
				$count = 1;				
			}
			
			if($step == '1_15'){
				$count = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."naps");				
			}

			if($step == '1_16'){
				$query = $wpdb->query("CHECK TABLE ". $wpdb->prefix ."maintrance");
				if($query == 1){
					$count = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."maintrance");	
				}	
			}

			if($step == '1_17'){
				$arr = array(
					array(
						'tbl' => 'users',
						'row' => 'user_discount',
					),
					array(
						'tbl' => 'vtypes',
						'row' => 'vncurs',					
					),
					array(
						'tbl' => 'vtypes',
						'row' => 'nums',					
					),
					array(
						'tbl' => 'valuts',
						'row' => 'valut_reserv',					
					),
					array(
						'tbl' => 'trans_reserv',
						'row' => 'trans_summ',					
					),
					array(
						'tbl' => 'naps',
						'row' => 'curs1',					
					),
					array(
						'tbl' => 'naps',
						'row' => 'curs2',					
					),
					array(
						'tbl' => 'naps',
						'row' => 'profit_summ1',					
					),
					array(
						'tbl' => 'naps',
						'row' => 'profit_summ2',					
					),
					array(
						'tbl' => 'naps',
						'row' => 'com_summ1',					
					),
					array(
						'tbl' => 'naps',
						'row' => 'com_summ2',					
					),
					array(
						'tbl' => 'naps',
						'row' => 'com_summ1_check',					
					),
					array(
						'tbl' => 'naps',
						'row' => 'com_summ2_check',					
					),
					array(
						'tbl' => 'bids',
						'row' => 'curs1',					
					),
					array(
						'tbl' => 'bids',
						'row' => 'curs2',					
					),
					array(
						'tbl' => 'bids',
						'row' => 'exsum',					
					),
					array(
						'tbl' => 'bids',
						'row' => 'user_sksumm',					
					),
					array(
						'tbl' => 'bids',
						'row' => 'summ1',					
					),
					array(
						'tbl' => 'bids',
						'row' => 'dop_com1',					
					),
					array(
						'tbl' => 'bids',
						'row' => 'summ1_dc',					
					),
					array(
						'tbl' => 'bids',
						'row' => 'com_ps1',					
					),
					array(
						'tbl' => 'bids',
						'row' => 'summ1c',					
					),
					array(
						'tbl' => 'bids',
						'row' => 'summ1cr',					
					),
					array(
						'tbl' => 'bids',
						'row' => 'summ2t',					
					),
					array(
						'tbl' => 'bids',
						'row' => 'summ2',					
					),
					array(
						'tbl' => 'bids',
						'row' => 'dop_com2',					
					),
					array(
						'tbl' => 'bids',
						'row' => 'com_ps2',					
					),
					array(
						'tbl' => 'bids',
						'row' => 'summ2_dc',					
					),
					array(
						'tbl' => 'bids',
						'row' => 'summ2c',					
					),
					array(
						'tbl' => 'bids',
						'row' => 'summ2cr',					
					),
					array(
						'tbl' => 'bids',
						'row' => 'profit',					
					),
					array(
						'tbl' => 'valuts',
						'row' => 'inday1',					
					),	
					array(
						'tbl' => 'valuts',
						'row' => 'inday2',					
					),
					array(
						'tbl' => 'valuts',
						'row' => 'inmon1',					
					),
					array(
						'tbl' => 'valuts',
						'row' => 'inmon2',					
					),
					array(
						'tbl' => 'user_discounts',
						'row' => 'sumec',					
					),
					array(
						'tbl' => 'user_discounts',
						'row' => 'discount',					
					),	
					array(
						'tbl' => 'naps',
						'row' => 'mnums1',				
					),	
					array(
						'tbl' => 'naps',
						'row' => 'mnums2',					
					),	
					array(
						'tbl' => 'masschange',
						'row' => 'curs1',					
					),
					array(
						'tbl' => 'masschange',
						'row' => 'curs2',					
					),
					array(
						'tbl' => 'naps',
						'row' => 'nums1',					
					),	
					array(
						'tbl' => 'naps',
						'row' => 'nums2',					
					),
					array(
						'tbl' => 'users',
						'row' => 'partner_pers',					
					),
					array(
						'tbl' => 'bids',
						'row' => 'summp',					
					),
					array(
						'tbl' => 'bids',
						'row' => 'partpr',					
					),
					array(
						'tbl' => 'partner_pers',
						'row' => 'sumec',					
					),
					array(
						'tbl' => 'partner_pers',
						'row' => 'pers',
					),	
					array(
						'tbl' => 'naps_sumcurs',
						'row' => 'sum_val',
					),
					array(
						'tbl' => 'naps_sumcurs',
						'row' => 'curs1',
					),
					array(
						'tbl' => 'naps_sumcurs',
						'row' => 'curs2',
					),				
				);
				
				$count = count($arr);			
			}

			if($step == '1_18'){	
				$count = $wpdb->query("SELECT * FROM ". $wpdb->prefix ."bids WHERE status = 'my'");
			}

			if($step == '2_1'){	
				$count = 1;
			}

			if($step == '2_2'){
				$query = $wpdb->query("CHECK TABLE ". $wpdb->prefix ."trans_reserv_logs");
				if($query == 1){
					$count = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."trans_reserv_logs");	
				}	
			}
			
			if($step == '2_3'){
				$count = 1;
			}
			
			if($step == '2_4'){	
				$count = $wpdb->query("SELECT * FROM ". $wpdb->prefix ."bids WHERE status != 'auto'");
			}	

			if($step == '2_5'){
				$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."bids LIKE 'domacc'");
				if ($query){
					$count = $wpdb->query("SELECT * FROM ". $wpdb->prefix ."bids WHERE domacc != '0'");
				}			
			}
			
			if($step == '2_6'){
				$count = 1;
			}

			if($step == '2_7'){
				$query = $wpdb->query("CHECK TABLE ". $wpdb->prefix ."archive_bids");
				if($query == 1){
					$count = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."archive_bids");	
				}	
			}			
		}
		
		$log['status'] = 'success';
		$log['count'] = $count;
		$log['link'] = pn_link_ajax('migrate_step_request').'&step='.$step;
		$log['status_text'] = __('Ok!','pn');

	} else {
		$log['status'] = 'error';
		$log['status_code'] = 1; 
		$log['status_text'] = __('Error! insufficient privileges!','pn');
	}
	
	echo json_encode($log);
	exit;	
}

add_action('premium_action_migrate_step_request','def_premium_action_migrate_step_request');
function def_premium_action_migrate_step_request(){
global $wpdb, $premiumbox;	

	only_post();

	$log = array();
	$log['status'] = '';
	$log['status_code'] = 0; 
	$log['status_text'] = '';
	$log['count'] = 0;
	$log['link'] = '';
	
	$step = is_param_get('step');
	$idspage = intval(is_param_post('idspage'));
	$limit = intval(is_param_post('limit')); if($limit < 1){ $limit = 1; }
	$offset = ($idspage - 1) * $limit;
	if(current_user_can('administrator')){
		
		if($step == '1_1'){	 /*****************/

 			$old_key = list_old_key_merchant();
		
			$datas = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."naps LIMIT {$offset},{$limit}");
			foreach($datas as $data){
				$naps_id = $data->id;
				$m_in = is_extension_name($data->m_in);
				$m_out = is_extension_name($data->m_out);
				$new_m_in = intval($m_in);
				$new_m_out = intval($m_out);
						
				$arr = array();
				if(is_my_money($data->com_summ1_check) == 0){
					$arr['com_summ1_check'] = is_my_money($data->com_summ1);
				}
				if(is_my_money($data->com_summ2_check) == 0){
					$arr['com_summ2_check'] = is_my_money($data->com_summ2);
				}
				if(is_my_money($data->com_pers1_check) == 0){
					$arr['com_pers1_check'] = is_my_money($data->com_pers1);
				}
				if(is_my_money($data->com_pers2_check) == 0){
					$arr['com_pers2_check'] = is_my_money($data->com_pers2);
				}
				if($new_m_in > 0){
					$arr['m_in'] = $m_in = is_extension_name(is_isset($old_key,$m_in));
				}
				$new_m_out = intval($m_out);
				if($new_m_out > 0){
					$arr['m_out'] = $m_out = is_extension_name(is_isset($old_key,$m_out));
				}				
				if(count($arr) > 0){
					$wpdb->update($wpdb->prefix.'naps', $arr, array('id'=>$naps_id));
				}
				$wpdb->query("UPDATE ".$wpdb->prefix."bids SET m_in = '$m_in', m_out = '$m_out' WHERE naps_id = '$naps_id'");
			} 			
			
		}	

		if($step == '1_2'){	 /*****************/

			$val = trim($premiumbox->get_option('exchange','techregtext'));
			if($val){
				$premiumbox->update_option('tech','text',$val);
				$premiumbox->update_option('exchange','techregtext','');
			}
			$val = trim($premiumbox->get_option('exchange','techreg'));
			if($val){
				$premiumbox->update_option('tech','manualy',$val);
				$premiumbox->update_option('exchange','techreg','');
			}

			$old_key = list_old_key_merchant();		
			$new_merch_data = $merch_data = $premiumbox->get_option('merch_data');
			if(is_array($merch_data)){
				foreach($merch_data as $key => $val){
					$key = is_extension_name(is_isset($old_key,$key));
					if($key){
						$new_merch_data[$key] = $val;
					} 
				}
				$premiumbox->update_option('merch_data', $new_merch_data);
			}
			$new_merch_data = $merch_data = $premiumbox->get_option('paymerch_data');
			if(is_array($merch_data)){
				foreach($merch_data as $key => $val){
					$key = is_extension_name(is_isset($old_key,$key));
					if($key){
						$new_merch_data[$key] = $val;
					} 
				}
				$premiumbox->update_option('paymerch_data', $new_merch_data);
			}			
		
		}
		
		if($step == '1_3'){	 /*****************/
		
			$datas = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."valuts LIMIT {$offset},{$limit}");
			foreach($datas as $data){
				$id = $data->id;
				$reserv_place = $data->reserv_place;
				if($reserv_place){					
					$reserv_place = str_replace('m1_','perfectmoney_',$reserv_place);
					$reserv_place = str_replace('m12_','nixmoney_',$reserv_place);
					$reserv_place = str_replace('m3_','webmoney_',$reserv_place);
					$reserv_place = str_replace('m8_','okpay_',$reserv_place);
					$reserv_place = str_replace('m29_','blockio_',$reserv_place);
					$reserv_place = str_replace('m23_','btce_',$reserv_place);
					$reserv_place = str_replace('m28_','livecoin_',$reserv_place);
					$reserv_place = str_replace('m5_','privat_',$reserv_place);
					$reserv_place = str_replace('m4_','yamoney_',$reserv_place);					
					$wpdb->query("UPDATE ".$wpdb->prefix."valuts SET reserv_place = '$reserv_place' WHERE id = '$id'");
				}
			}
			
		}
		
		if($step == '1_4'){	 /*****************/
		
			$valuts = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."valuts LIMIT {$offset},{$limit}");
			$naps = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."naps");
			foreach($valuts as $val){
				$val_id = $val->id;
				foreach($naps as $nap){
					$nap_id = $nap->id;
						$cc = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."naps_order WHERE naps_id='$nap_id' AND v_id='$val_id'");
						if($cc == 0){
							$arr = array(
								'naps_id' => $nap_id,
								'v_id' => $val_id,
							);
							$wpdb->insert($wpdb->prefix.'naps_order', $arr);
						}
				}			
				
			}		
		
		}
		
		if($step == '1_5'){	 /*****************/
		
			$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'naps_lang'");
			if ($query == 1){
				$all = '';
				$langs = get_langs_ml();
				foreach($langs as $lang){
					$all .= '[d]'. $lang .'[/d]';
				}				
				$datas = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."naps WHERE autostatus='1' LIMIT {$offset},{$limit}");
				foreach($datas as $data){
					$id = $data->id;
					$naps_lang = pn_strip_input($data->naps_lang);
					$naps_lang = str_replace('0','',$naps_lang);
					if(!strstr($naps_lang, '[d]')){
						if($naps_lang){
							$naps_lang = '[d]'.$naps_lang.'[/d]';
						} else {
							$naps_lang = $all;
						}
						$arr = array();
						$arr['naps_lang'] = $naps_lang;
						$wpdb->update($wpdb->prefix.'naps', $arr, array('id'=>$id));					
					}
				}
			}	
			
		}

		if($step == '1_6'){	 /*****************/		
			$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'not_country'");
			if ($query == 1){
				$datas = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."naps WHERE autostatus='1' LIMIT {$offset},{$limit}");
				foreach($datas as $data){
					$id = $data->id;					
					$not_country = @unserialize($data->not_country);
					if(is_array($not_country)){
						if(count($not_country) > 0){							
							$country = '';
							foreach($not_country as $cou){
								$country .= '[d]'. $cou .'[/d]';
							}							
							$arr = array();
							$arr['not_country'] = $country;
							$wpdb->update($wpdb->prefix.'naps', $arr, array('id'=>$id));														
						}					
					}
				}
			}		
		}
		
		if($step == '1_7'){	 /*****************/

			$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'not_ip'");
			if ($query == 1){
				$datas = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."naps WHERE autostatus='1' LIMIT {$offset},{$limit}");
				foreach($datas as $data){
					$id = $data->id;
					$not_ip = $data->not_ip;
					if(!strstr($not_ip, '[d]') and $not_ip){
						$not_ip = explode("\n",$not_ip);
						if(count($not_ip) > 0){
							$item = '';
							foreach($not_ip as $v){
								$v = trim($v);
								if($v){
									$item .= '[d]'. $v .'[/d]';
								}
							}	
							$arr = array();
							$arr['not_ip'] = $item;
							$wpdb->update($wpdb->prefix.'naps', $arr, array('id'=>$id));							
						}
					}
				}
			}		

		}
		
		if($step == '1_8'){	 /*****************/

			$query = $wpdb->query("SHOW COLUMNS FROM ". $wpdb->prefix ."bids LIKE 'new_user'");
			if ($query == 1){		
				
				$items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."bids LEFT OUTER JOIN ". $wpdb->prefix ."bids_meta ON(".$wpdb->prefix."bids.id = ". $wpdb->prefix ."bids_meta.item_id) WHERE ". $wpdb->prefix ."bids_meta.meta_key='new' AND ". $wpdb->prefix ."bids_meta.meta_value='1' LIMIT {$offset},{$limit}");
				foreach($items as $item){
					
					$arr = array(
						'new_user' => 1,
					);
					$wpdb->update($wpdb->prefix.'bids', $arr, array('id'=>$item->item_id));		
					
				}
			}		

		}

		if($step == '1_9'){	 /*****************/

			$tables = array(
				'_login_check','_warning_mess','_partners','_reviews','_reviews_meta','_user_discounts','_blacklist','_geoip_blackip','_geoip_whiteip','_geoip_template',
				'_geoip_iplist','_geoip_country','_psys','_archive_data','_vtypes','_valuts','_valuts_meta','_operator_schedules','_user_accounts','_partner_pers','_plinks',
				'_payoutuser','_user_fav','_uv_field','_uv_field_user','_userverify','_uv_accounts','_custom_fields_valut','_custom_fields','_cf_naps','_naps',
				'_naps_meta','_masschange','_bidstatus','_trans_reserv','_valuts_account','_bids','_bids_meta','_admin_captcha','_bid_logs','_naps_sumcurs','_standart_captcha',
				'_inex_system','_inex_tars','_inex_deposit','_inex_change'
			);	
			$array = array_slice($tables, $offset, $limit);
			foreach($array as $tb){
				$tb = ltrim($tb,'_');
				$table = $wpdb->prefix . $tb;
				$query = $wpdb->query("CHECK TABLE {$table}");
				if($query == 1){
					$wpdb->query("ALTER TABLE {$table} ENGINE=InnoDB");
				}
			}

		}
		
		if($step == '1_10'){	 /*****************/		
		
			$globalajax = trim($premiumbox->get_option('globalajax'));
			if($globalajax){
				$premiumbox->update_option('ga','ga_admin', 1);
				$premiumbox->update_option('ga','ga_site', 1);
				$premiumbox->delete_option('globalajax');
			}		
			
			$adminpass = trim($premiumbox->get_option('adminpass'));
			if(!is_numeric($adminpass)){
				$premiumbox->update_option('adminpass','', 1);
			}

			$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."valuts LIKE 'psys_title'");
			if ($query) { 
				$wpdb->query("ALTER TABLE ".$wpdb->prefix ."valuts CHANGE `psys_title` `psys_title` longtext NOT NULL");
			}
			$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."valuts LIKE 'vtype_title'");
			if ($query) { 
				$wpdb->query("ALTER TABLE ".$wpdb->prefix ."valuts CHANGE `vtype_title` `vtype_title` longtext NOT NULL");
			}				
		
		}	

		if($step == '1_11'){	 /*****************/
		
			$datas = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."valuts LIMIT {$offset},{$limit}");
			foreach($datas as $data){
				$helps2 = trim($data->helps2);
				if(!$helps2){					
					$arr = array(
						'helps2' => $data->helps,
					);
					$wpdb->update($wpdb->prefix.'valuts', $arr, array('id'=>$data->id));						
				}
			}
			
		}		

		if($step == '1_12'){	 /*****************/
		
			$datas = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."custom_fields_valut LIMIT {$offset},{$limit}");
			foreach($datas as $data){
				$tech_name = pn_strip_input($data->tech_name);
				if(!$tech_name){					
					$arr = array(
						'tech_name' => pn_strip_input($data->cf_name),
					);
					$wpdb->update($wpdb->prefix.'custom_fields_valut', $arr, array('id'=>$data->id));						
				}
			}
			
		}

		if($step == '1_13'){	 /*****************/
		
			$datas = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."custom_fields LIMIT {$offset},{$limit}");
			foreach($datas as $data){
				$tech_name = pn_strip_input($data->tech_name);
				if(!$tech_name){					
					$arr = array(
						'tech_name' => pn_strip_input($data->cf_name),
					);
					$wpdb->update($wpdb->prefix.'custom_fields', $arr, array('id'=>$data->id));						
				}
			}
			
		}

		if($step == '1_14'){	 /*****************/

			$query = $wpdb->query("CHECK TABLE ". $wpdb->prefix ."term_meta");
			if($query == 1){		
				$items = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."term_meta");
				foreach($items as $item){
					$id = $item->id;
					$arr = array(
						'term_id' => $item->item_id,
						'meta_key' => $item->meta_key,
						'meta_value' => $item->meta_value,
					);
					$wpdb->insert($wpdb->prefix.'termmeta', $arr);
					$wpdb->query("DELETE FROM ". $wpdb->prefix ."term_meta WHERE id = '$id'");
				}
			}				

		}		
		
		if($step == '1_15'){	 /*****************/
		
			$datas = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."naps LIMIT {$offset},{$limit}");
			foreach($datas as $data){
				$tech_name = pn_strip_input($data->tech_name);
				if(!$tech_name){					
					$arr = array(
						'tech_name' => get_vtitle($data->valut_id1) .' &rarr; '. get_vtitle($data->valut_id2),
					);
					$wpdb->update($wpdb->prefix.'naps', $arr, array('id'=>$data->id));						
				}
			} 					
		
		}
		
		if($step == '1_16'){	 /*****************/
			$query = $wpdb->query("CHECK TABLE ". $wpdb->prefix ."maintrance");
			if($query == 1){
				$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."maintrance LIKE 'page_files'");
				if ($query) { 
				
					$datas = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."maintrance LIMIT {$offset},{$limit}");
					foreach($datas as $data){
						$pages_law = array();
						$pages_law['files'] = $data->page_files;
						$pages_law['smxml'] = $data->page_xml;
						$pages_law['sm'] = $data->page_sitemap;
						$pages_law['tar'] = $data->page_tarifs;
						$pages_law['home'] = $data->page_home;
						$pages_law['exchange'] = $data->page_exchange;
						$pages_law = serialize($pages_law);
						$wpdb->update($wpdb->prefix.'maintrance', array('pages_law'=>$pages_law), array('id'=>$data->id));						
					}				

					$wpdb->query("ALTER TABLE ".$wpdb->prefix ."maintrance DROP `page_files`");
					$wpdb->query("ALTER TABLE ".$wpdb->prefix ."maintrance DROP `page_xml`");
					$wpdb->query("ALTER TABLE ".$wpdb->prefix ."maintrance DROP `page_sitemap`");
					$wpdb->query("ALTER TABLE ".$wpdb->prefix ."maintrance DROP `page_tarifs`");
					$wpdb->query("ALTER TABLE ".$wpdb->prefix ."maintrance DROP `page_home`");
					$wpdb->query("ALTER TABLE ".$wpdb->prefix ."maintrance DROP `page_exchange`");
				} 					
			}
		}	

		if($step == '1_17'){	 /*****************/

			$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."valuts LIKE 'valut_decimal'");
			if ($query) { 
				$wpdb->query("ALTER TABLE ".$wpdb->prefix ."valuts CHANGE `valut_decimal` `valut_decimal` int(2) NOT NULL default '2'");
			}			
			
			$arr = array(
				array(
					'tbl' => 'users',
					'row' => 'user_discount',
				),
				array(
					'tbl' => 'vtypes',
					'row' => 'vncurs',					
				),
				array(
					'tbl' => 'vtypes',
					'row' => 'nums',					
				),
				array(
					'tbl' => 'valuts',
					'row' => 'valut_reserv',					
				),
				array(
					'tbl' => 'trans_reserv',
					'row' => 'trans_summ',					
				),
				array(
					'tbl' => 'naps',
					'row' => 'curs1',					
				),
				array(
					'tbl' => 'naps',
					'row' => 'curs2',					
				),
				array(
					'tbl' => 'naps',
					'row' => 'profit_summ1',					
				),
				array(
					'tbl' => 'naps',
					'row' => 'profit_summ2',					
				),
				array(
					'tbl' => 'naps',
					'row' => 'com_summ1',					
				),
				array(
					'tbl' => 'naps',
					'row' => 'com_summ2',					
				),
				array(
					'tbl' => 'naps',
					'row' => 'com_summ1_check',					
				),
				array(
					'tbl' => 'naps',
					'row' => 'com_summ2_check',					
				),
				array(
					'tbl' => 'bids',
					'row' => 'curs1',					
				),
				array(
					'tbl' => 'bids',
					'row' => 'curs2',					
				),
				array(
					'tbl' => 'bids',
					'row' => 'exsum',					
				),
				array(
					'tbl' => 'bids',
					'row' => 'user_sksumm',					
				),
				array(
					'tbl' => 'bids',
					'row' => 'summ1',					
				),
				array(
					'tbl' => 'bids',
					'row' => 'dop_com1',					
				),
				array(
					'tbl' => 'bids',
					'row' => 'summ1_dc',					
				),
				array(
					'tbl' => 'bids',
					'row' => 'com_ps1',					
				),
				array(
					'tbl' => 'bids',
					'row' => 'summ1c',					
				),
				array(
					'tbl' => 'bids',
					'row' => 'summ1cr',					
				),
				array(
					'tbl' => 'bids',
					'row' => 'summ2t',					
				),
				array(
					'tbl' => 'bids',
					'row' => 'summ2',					
				),
				array(
					'tbl' => 'bids',
					'row' => 'dop_com2',					
				),
				array(
					'tbl' => 'bids',
					'row' => 'com_ps2',					
				),
				array(
					'tbl' => 'bids',
					'row' => 'summ2_dc',					
				),
				array(
					'tbl' => 'bids',
					'row' => 'summ2c',					
				),
				array(
					'tbl' => 'bids',
					'row' => 'summ2cr',					
				),
				array(
					'tbl' => 'bids',
					'row' => 'profit',					
				),
				array(
					'tbl' => 'valuts',
					'row' => 'inday1',					
				),	
				array(
					'tbl' => 'valuts',
					'row' => 'inday2',					
				),
				array(
					'tbl' => 'valuts',
					'row' => 'inmon1',					
				),
				array(
					'tbl' => 'valuts',
					'row' => 'inmon2',					
				),
				array(
					'tbl' => 'user_discounts',
					'row' => 'sumec',					
				),
				array(
					'tbl' => 'user_discounts',
					'row' => 'discount',					
				),	
				array(
					'tbl' => 'naps',
					'row' => 'mnums1',				
				),	
				array(
					'tbl' => 'naps',
					'row' => 'mnums2',					
				),	
				array(
					'tbl' => 'masschange',
					'row' => 'curs1',					
				),
				array(
					'tbl' => 'masschange',
					'row' => 'curs2',					
				),
				array(
					'tbl' => 'naps',
					'row' => 'nums1',					
				),	
				array(
					'tbl' => 'naps',
					'row' => 'nums2',					
				),
				array(
					'tbl' => 'users',
					'row' => 'partner_pers',					
				),
				array(
					'tbl' => 'bids',
					'row' => 'summp',					
				),
				array(
					'tbl' => 'bids',
					'row' => 'partpr',					
				),
				array(
					'tbl' => 'partner_pers',
					'row' => 'sumec',					
				),
				array(
					'tbl' => 'partner_pers',
					'row' => 'pers',
				),	
				array(
					'tbl' => 'naps_sumcurs',
					'row' => 'sum_val',
				),
				array(
					'tbl' => 'naps_sumcurs',
					'row' => 'curs1',
				),
				array(
					'tbl' => 'naps_sumcurs',
					'row' => 'curs2',
				),				
			);
			$arr = array_slice($arr, $offset, $limit);
			
			foreach($arr as $data){
				$table = $wpdb->prefix. $data['tbl'];
				$query = $wpdb->query("CHECK TABLE {$table}");
				if($query == 1){
					$row = $data['row'];
					$que = $wpdb->query("SHOW COLUMNS FROM {$table} LIKE '{$row}'");
					if ($que) {
						$wpdb->query("ALTER TABLE {$table} CHANGE `{$row}` `{$row}` varchar(50) NOT NULL default '0'");
					}	
				}
			}			

		}		
		
		if($step == '1_18'){	 /*****************/
		
			$datas = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."bids WHERE status = 'my' LIMIT {$offset},{$limit}");
			foreach($datas as $data){

				$arr = array();
				$arr['status'] = 'my'. is_isset($data,'mystatus');
				$wpdb->update($wpdb->prefix.'bids', $arr, array('id'=>$data->id));
								
			}		
		
		}	

		if($step == '2_1'){	 /*****************/
		
			$sn = trim($premiumbox->get_option('second_name'));
			if(!is_numeric($sn)){
				$sn = 1;
			}	
			$ch_mail = trim($premiumbox->get_option('change_email'));
			if(!is_numeric($ch_mail)){
				$ch_mail = 1;
			}
			
			$fields = array(
				'login' => 1,
				'last_name' => 1,
				'first_name' => 1,
				'second_name' => $sn,
				'user_phone' => 1,
				'user_skype' => 1,
				'website' => 1,
				'user_passport' => 1,
			);	
			$premiumbox->update_option('user_fields','',$fields);
			
			$fields = array(
				'user_email' => $ch_mail,
				'last_name' => 1,
				'first_name' => 1,
				'second_name' => 1,
				'user_phone' => 1,
				'user_skype' => 1,
				'website' => 1,
				'user_passport' => 1,
			);			
			$premiumbox->update_option('user_fields_change','',$fields);
			
			$premiumbox->delete_option('second_name');
			$premiumbox->delete_option('change_email');
		
		}

		if($step == '2_2'){	 /*****************/
			$query = $wpdb->query("CHECK TABLE ". $wpdb->prefix ."trans_reserv_logs");
			if($query == 1){
				$query = $wpdb->query("CHECK TABLE ". $wpdb->prefix ."db_admin_logs");
				if($query == 1){
					$datas = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."trans_reserv_logs LIMIT {$offset},{$limit}");
					foreach($datas as $data){
						$id = $data->id;
						$last_data = array(
							'trans_summ' => $data->old_sum,
						);
						$last_data = @serialize($last_data);
						
						$new_data = array(
							'trans_summ' => $data->new_sum,
						);		
						$new_data = @serialize($new_data);					

						$arr = array();
						$arr['tbl_name'] = 'reserv';
						$arr['item_id'] = $data->trans_id;
						$arr['trans_type'] = $data->trans_type;
						$arr['trans_date'] = $data->trans_date;
						$arr['old_data'] = $last_data;
						$arr['new_data'] = $new_data;
						$arr['user_id'] = $data->user_id;
						$arr['user_login'] = $data->user_login;
						$result = $wpdb->insert($wpdb->prefix.'db_admin_logs', $arr);	
						if($result){
							$wpdb->query("DELETE FROM ". $wpdb->prefix ."trans_reserv_logs WHERE id = '$id'");
						}
					}	
				}
			}
		}		

		if($step == '2_3'){	 /*****************/
			$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."bids LIKE 'user_ip'");
			if ($query == 1) { 
				$wpdb->query("ALTER TABLE ".$wpdb->prefix ."bids CHANGE `user_ip` `user_ip` varchar(150) NOT NULL");
			}
		}	

		if($step == '2_4'){	 /*****************/
			$datas = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."bids WHERE status != 'auto' LIMIT {$offset},{$limit}");
			foreach($datas as $data){
				$id = $data->id;
				
				bid_hashdata($id, $data, '');				
			} 
		}

		if($step == '2_5'){	 /*****************/
		
			$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."bids LIKE 'domacc'");
			if ($query){
				$datas = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."bids WHERE domacc != '0' LIMIT {$offset},{$limit}");
				foreach($datas as $data){
					$id = $data->id;
					$domacc = $data->domacc;
					$arr = array();
					if($domacc == 1){
						$arr['domacc1'] = '1';
					} else {
						$arr['domacc2'] = '1';
					}
					$wpdb->update($wpdb->prefix . 'bids', $arr, array('id'=>$id));
				}
			}				

		}

		if($step == '2_6'){	 /*****************/
		
			$arr = array(
				array(
					'tbl' => 'bcbroker_naps',
					'row' => 'step',
				),
				array(
					'tbl' => 'bcbroker_naps',
					'row' => 'cours1',
				),
				array(
					'tbl' => 'bcbroker_naps',
					'row' => 'cours2',
				),
				array(
					'tbl' => 'bcbroker_naps',
					'row' => 'min_sum',
				),
				array(
					'tbl' => 'bcbroker_naps',
					'row' => 'max_sum',
				),
				array(
					'tbl' => 'bcbroker_naps',
					'row' => 'min_res',
				),				
			);
			foreach($arr as $data){
				$table = $wpdb->prefix. $data['tbl'];
				$query = $wpdb->query("CHECK TABLE {$table}");
				if($query == 1){
					$row = $data['row'];
					$que = $wpdb->query("SHOW COLUMNS FROM {$table} LIKE '{$row}'");
					if ($que) {
						$wpdb->query("ALTER TABLE {$table} CHANGE `{$row}` `{$row}` varchar(250) NOT NULL default '0'");
					}	
				}
			}

		}	
		

		if($step == '2_7'){	 /*****************/ 
		
			$query = $wpdb->query("CHECK TABLE ". $wpdb->prefix ."archive_bids");
			if($query == 1){
				
				if($offset == 0){
					$wpdb->query("DELETE FROM ". $wpdb->prefix ."archive_data WHERE meta_key != 'plinks'"); 
				}
				
				$datas = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."archive_bids ORDER BY id ASC LIMIT {$offset},{$limit}");
				foreach($datas as $data){
					$id = $data->bid_id;	
					$status = $data->status;
					$user_id = $data->user_id;
					$arch = @unserialize($data->archive_content);
					$vtype1i = $arch['vtype1i'];
					$vtype2i = $arch['vtype2i'];
					$vtype1 = $arch['vtype1'];
					$vtype2 = $arch['vtype2'];
					$valut1i = $arch['valut1i'];
					$valut2i = $arch['valut2i'];
					$pcalc = intval(is_isset($arch, 'pcalc'));
					$domacc = intval(is_isset($arch, 'domacc'));
					$domacc1 = intval(is_isset($arch, 'domacc1'));
					$domacc2 = intval(is_isset($arch, 'domacc2'));
			
					if($status == 'success'){
						if($user_id > 0){
							set_archive_data($user_id, 'user_exsum', '', '', $arch['exsum']);	
							set_archive_data($user_id, 'user_bids_success', '', '', 1);
						}
						if($pcalc == 1){
							set_archive_data($arch['ref_id'], 'pbids', '', '', 1);
							set_archive_data($arch['ref_id'], 'pbids_sum', '', '', $arch['summp']);
							set_archive_data($arch['ref_id'], 'pbids_exsum', '', '', $arch['exsum']);
						}
					}
					
					set_archive_data($vtype1i, 'vtype_give', $status, '', $arch['summ1cr']);
					set_archive_data($vtype2i, 'vtype_get', $status, '', $arch['summ2cr']);
					set_archive_data($valut1i, 'valut_give', $status, '', $arch['summ1cr']);
					set_archive_data($valut2i, 'valut_get', $status, '', $arch['summ2cr']);
					set_archive_data($arch['naps_id'], 'naps_give', $status, '', $arch['summ1cr']);
					set_archive_data($arch['naps_id'], 'naps_get', $status, '', $arch['summ2cr']);
					
					if($user_id > 0){
						if($domacc == 1){
							set_archive_data($user_id, 'domacc1_vtype', $status, $vtype1i, $arch['summ1c']);
						}
						if($domacc == 2){
							set_archive_data($user_id, 'domacc2_vtype', $status, $vtype2i, $arch['summ2c']);
						}
						if($domacc1 == 1){
							set_archive_data($user_id, 'domacc1_vtype', $status, $vtype1i, $arch['summ1c']);
						}
						if($domacc2 == 1){
							set_archive_data($user_id, 'domacc2_vtype', $status, $vtype2i, $arch['summ2c']);
						}				
					}
				}
			}				
		}			
		
		$log['status'] = 'success';	
		$log['status_text'] = __('Ok!','pn');		
		
	} else {
		$log['status'] = 'error';
		$log['status_code'] = 1; 
		$log['status_text'] = __('Error! insufficient privileges!','pn');
	}
	
	echo json_encode($log);
	exit;	
}