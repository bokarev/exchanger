<?php
if( !defined( 'ABSPATH')){ exit(); }
	
add_action('siteplace_js','siteplace_js_exchange_action');
function siteplace_js_exchange_action(){
?>	
/* exchange action */
jQuery(function($){
	
	function cache_exchange_data(thet){
		var ind = 0;
		if(thet.hasClass('check_cache')){
			if($('#check_data').length > 0){
				if($('#check_data').prop('checked')){
					ind = 1;
				}
			}	
		} else {
			ind = 1;
		}
		if(ind == 1){
			var id = thet.attr('cash-id');
			var v = thet.val();
			Cookies.set("cache_"+id, v, { expires: 7, path: '/' });			
		}
	}
	
	$(document).on('change', '.cache_data', function(){
		cache_exchange_data($(this));
	});
	$(document).on('keyup', '.cache_data', function(){
		cache_exchange_data($(this));
	});	
	
	$(document).on('change', '#check_data', function(){
		if($(this).prop('checked')){
			Cookies.set("check_data", 1, { expires: 7, path: '/' });
			$('.check_cache').each(function(){
				var id = $(this).attr('name');
				var v = $(this).val();
				Cookies.set("cache_"+id, v, { expires: 7, path: '/' });	
			});
		} else {
			Cookies.set("check_data", 0, { expires: 7, path: '/' });
			$('.check_cache').each(function(){
				var id = $(this).attr('cash-id');
				Cookies.remove("cache_"+id);	
			});			
		}
	});
	
	function add_cf_error(id, text){
		$('.js_cf'+id).parents('.js_wrap_error').addClass('error');
		if(text.length > 0){
			$('.js_cf'+ id +'_error').html(text);
		}
	}
	
	function add_cfc_error(id, text){
		$('.js_cfc'+id).parents('.js_wrap_error').addClass('error');
		if(text.length > 0){
			$('.js_cfc'+ id +'_error').html(text);
		}
	}	
	
    $('.ajax_post_bids').ajaxForm({
        dataType:  'json',
		beforeSubmit: function(a,f,o) {
			f.addClass('thisactive');
			$('.thisactive input[type=submit], .thisactive input[type=button]').attr('disabled',true);
			$('.ajax_post_bids_res').html('<div class="resulttrue"><?php echo esc_attr(__('Processing. Please wait','pn')); ?></div>');
        },
		error: function(res, res2, res3) {
			<?php do_action('pn_js_error_response', 'form'); ?>
		},		
        success: function(res) { 
		
				if(res['summ1_error'] == 1){
					$('.js_summ1').parents('.js_wrap_error').addClass('error');
					$('.js_summ1_error').html(res['summ1_error_text']);
				} 
				if(res['summ2_error'] == 1){
					$('.js_summ2').parents('.js_wrap_error').addClass('error');
					$('.js_summ2_error').html(res['summ2_error_text']);
				} 
				if(res['summ1c_error'] == 1){
					$('.js_summ1c').parents('.js_wrap_error').addClass('error');
					$('.js_summ1c_error').html(res['summ1c_error_text']);
				} 
				if(res['summ2c_error'] == 1){
					$('.js_summ2c').parents('.js_wrap_error').addClass('error');
					$('.js_summ2c_error').html(res['summ2c_error_text']);
				} 
				if(res['account1_error'] == 1){
					$('.js_account1').parents('.js_wrap_error').addClass('error');
					$('.js_account1_error').html(res['account1_error_text']);
				} 				
				if(res['account2_error'] == 1){
					$('.js_account2').parents('.js_wrap_error').addClass('error');
					$('.js_account2_error').html(res['account2_error_text']);
				} 
				
				if(res['cf']){
					var cf = res['cf'];
					var cf_er = res['cf_er'];
					for (var i = 0; i < cf.length; i++) {
						var cfid = cf[i];
						var cftext = cf_er[i];
						add_cf_error(cfid, cftext);
					}
				}

				if(res['cfc']){
					var cfc = res['cfc'];
					var cfc_er = res['cfc_er'];
					for (var i = 0; i < cfc.length; i++) {
						var cfid = cfc[i];
						var cftext = cfc_er[i];
						add_cfc_error(cfid, cftext);
					}	
				}

			if(res['status'] == 'error'){
				$('.ajax_post_bids_res').html('<div class="resultfalse"><div class="resultclose"></div>'+res['status_text']+'</div>');
				if($('.js_wrap_error.error').length > 0){
					var ftop = $('.js_wrap_error.error:first').offset().top - 100;
					$('body,html').animate({scrollTop: ftop},500);
				}
			}
			if(res['status'] == 'success'){
				$('.ajax_post_bids_res').html('<div class="resulttrue"><div class="resultclose"></div>'+res['status_text']+'</div>');
			}				
		
			if(res['url']){
				window.location.href = res['url']; 
			}
			
			<?php do_action('ajax_post_form_jsresult'); ?>
		
		    $('.thisactive input[type=submit], .thisactive input[type=button]').attr('disabled',false);
			$('.thisactive').removeClass('thisactive');
			
        }
    });	
	
});
/* end exchange action */	
<?php	
}	
/* end добавляем JS */

/* bids add */
add_action('myaction_site_bidsform', 'def_myaction_ajax_bidsform');
function def_myaction_ajax_bidsform(){
global $wpdb, $premiumbox;	
	
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);
	
	$log = array();
	$log['status'] = '';
	$log['response'] = '';
	$log['status_code'] = 0; 
	$log['status_text'] = __('Error','pn');	

	$premiumbox->up_mode();

	$naps_id = intval(is_param_post('naps_id'));
	
	$log = apply_filters('before_ajax_form_field', $log, 'exchangeform');
	$log = apply_filters('before_ajax_bidsform', $log, $naps_id);
	
	$error = 0;
	$error_text = array();
	$summ1_error = $summ2_error = $summ1c_error = $summ2c_error = $account1_error = $account2_error = 0;
	$summ1_error_text = $summ2_error_text = $summ1c_error_text = $summ2c_error_text = $account1_error_text = $account2_error_text = '';
	$cf = $cf_er = $cfc = $cfc_er = array();
	
	if(!$naps_id){
		$log['status'] = 'error';
		$log['status_code'] = 1; 
		$log['status_text'] = __('Error! The direction do not exist','pn');
		echo json_encode($log);
		exit;		
	}
	
	$naps_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."naps WHERE naps_status='1' AND autostatus='1' AND id='$naps_id'");
	if(!isset($naps_data->id)){
		$log['status'] = 'error';
		$log['status_code'] = 1; 
		$log['status_text'] = __('Error! The direction do not exist','pn');
		echo json_encode($log);
		exit;		
	}
	
	$naps = array();
	foreach($naps_data as $naps_key => $naps_val){
		$naps[$naps_key] = $naps_val;
	}
	$naps_meta = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."naps_meta WHERE item_id='$naps_id'");
	foreach($naps_meta as $naps_item){
		$naps[$naps_item->meta_key] = $naps_item->meta_value;
	}	
	$naps = (object)$naps; /* вся информация о направлении */
	
	$valut1_id = intval($naps->valut_id1);
	$valut2_id = intval($naps->valut_id2);
	
	$vd1 = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."valuts WHERE id='$valut1_id'");
	$vd2 = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."valuts WHERE id='$valut2_id'");

	if(!isset($vd1->id) or !isset($vd2->id)){
		$log['status'] = 'error';
		$log['status_code'] = 1;
		$log['status_text'] = __('Error! The direction do not exist','pn');
		echo json_encode($log);
		exit;		
	}	
	
	$check_purse1 = $check_purse2 = 0;
	
	/* счета валют */
	$account1 = $account2 = '';
	
	$show = apply_filters('form_bids_account1', $vd1->show1, $naps, $vd1);
	if($show == 1){
		$account1 = pn_strip_input(is_param_post('account1'));
		$account1 = get_purse($account1, $vd1);
		if(!$account1){
			$error = $error+1;
			$account1_error = 1;
			$account1_error_text = __('invalid account number','pn');
		}
	}
	
	$show = apply_filters('form_bids_account2', $vd2->show2, $naps, $vd2);
	if($show == 1){
		$account2 = pn_strip_input(is_param_post('account2'));
		$account2 = get_purse($account2, $vd2);
		if(!$account2){
			$error = $error+1;
			$account2_error = 1;
			$account2_error_text = __('invalid account number','pn');
		}
	}
	/* end счета валют */
	
	/* чекер аккаунтов */
	$check_enable = $naps->check_purse;
	if($account1){
		if($check_enable == 1 or $check_enable == 3){
			$check_purse1 = apply_filters('set_check_account1', 0, $account1, $vd1->check_purse);
		}
	}
	if($account2){
		if($check_enable == 2 or $check_enable == 3){
			$check_purse2 = apply_filters('set_check_account2', 0, $account2, $vd2->check_purse);
		}
	}	
	
	$req_check_purse = $naps->req_check_purse;
	if($req_check_purse == 1 or $req_check_purse == 3){
		if($check_purse1 != 1){
			$error = $error+1;
			$account1_error = 1;
			$account1_error_text = apply_filters('check_purse1_text', __('account has invalid status','pn'), $vd1->check_purse);			
		}
	}
	if($req_check_purse == 2 or $req_check_purse == 3){
		if($check_purse2 != 1){
			$error = $error+1;
			$account2_error = 1;
			$account2_error_text = apply_filters('check_purse2_text', __('account has invalid status','pn'), $vd2->check_purse);			
		}
	}	
	/* end чекер аккаунтов */
	
	$post_sum = is_my_money(is_param_post('sum1'));

	$cdata = get_calc_data($vd1, $vd2, $naps, $user_id, $post_sum, $check_purse1, $check_purse2);
	
	$decimal1 = $cdata['decimal1'];
	$decimal2 = $cdata['decimal2'];
	$vtype1 = $cdata['vtype1'];
	$vtype2 = $cdata['vtype2'];
	$psys1 = $cdata['psys1'];
	$psys2 = $cdata['psys2'];	
	
	$curs1 = $cdata['curs1'];
	$curs2 = $cdata['curs2'];	
	
	$summ1 = $cdata['summ1'];
	$summ1c = $cdata['summ1c'];
	$summ2 = $cdata['summ2'];
	$summ2c = $cdata['summ2c'];	

	$profit = $cdata['profit'];
	$user_sk = $cdata['user_discount'];
	$user_sksumm = $cdata['user_sksumm'];
	$dop_com1 = $cdata['dop_com1'];
	$summ1_dc = $cdata['summ1_dc'];
	$com_ps1 = $cdata['com_ps1'];
	$summ1cr = $cdata['summ1cr'];
	$summ2t = $cdata['summ2t'];
	$dop_com2 = $cdata['dop_com2'];
	$com_ps2 = $cdata['com_ps2'];
	$summ2_dc = $cdata['summ2_dc'];
	$summ2cr = $cdata['summ2cr'];
	$exsum = $cdata['exsum'];
	
	/* максимум и минимум */
		$min1 = get_min_sum_to_naps_give($naps, $vd1);
		$max1 = get_max_sum_to_naps_give($naps, $vd1);
		/* if($min1 > $max1 and is_numeric($max1)){ $min1 = $max1; } */

		$min2 = get_min_sum_to_naps_get($naps, $vd2); 
		$max2 = get_max_sum_to_naps_get($naps, $vd2);
		/* if($min2 > $max2 and is_numeric($max2)){ $min2 = $max2; } */		
		
		if($summ1 < $min1){
			$error = $error+1;
			$summ1_error = 1;
			$summ1_error_text = __('min','pn').'.: '. $min1 .' '.$vtype1;													
		}						
		if($summ1 > $max1 and is_numeric($max1)){
			$error = $error+1;
			$summ1_error = 1;
			$summ1_error_text = __('max','pn').'.: '. $max1 .' '.$vtype1;													
		}						
		if($summ2 < $min2){
			$error = $error+1;
			$summ2_error = 1;
			$summ2_error_text = __('min','pn').'.: '. $min2 .' '.$vtype2;													
		}							
		if($summ2 > $max2 and is_numeric($max2)){
			$error = $error+1;
			$summ2_error = 1;
			$summ2_error_text = __('max','pn').'.: '. $max2 .' '.$vtype2;													
		}								
		if($summ1 <= 0){
			$error = $error+1;
			$summ1_error = 1;
		}							
		if($summ2 <= 0){
			$error = $error+1;
			$summ2_error = 1;
		}						
		if($summ1c <= 0){
			$error = $error+1;
			$summ1c_error = 1;
		}							
		if($summ2c <= 0){
			$error = $error+1;
			$summ2c_error = 1;
		}		
	/* end максимум и минимум */
	
	/* данные по валютам */
	$valut1 = pn_strip_input($vd1->psys_title);
	$valut2 = pn_strip_input($vd2->psys_title);
	$valut1i = $vd1->id;
	$valut2i = $vd2->id;
	$vtype1 = is_site_value($vd1->vtype_title);
	$vtype2 = is_site_value($vd2->vtype_title);
	$vtype1i = $vd1->vtype_id;
	$vtype2i = $vd2->vtype_id;	
	$psys1i = $vd1->psys_id;
	$psys2i = $vd2->psys_id;	
	/* end данные по валютам */
	
	$unmetas = array();
	$auto_data = array();
	
	/* основные поля */
	$first_name = '';
	$last_name = '';
	$second_name = '';
	$user_phone = '';
    $user_skype = '';
	$user_email = '';
	$user_passport = '';
	
	$metas = array();
	$dmetas = array();
	
	$osnpoles = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."custom_fields LEFT OUTER JOIN ". $wpdb->prefix ."cf_naps ON(".$wpdb->prefix."custom_fields.id = ". $wpdb->prefix ."cf_naps.cf_id) WHERE status='1' AND ". $wpdb->prefix ."cf_naps.naps_id = '$naps_id' ORDER BY cf_order ASC");
	foreach($osnpoles as $op_item){
		$op_id = $op_item->cf_id;
		$op_vid = $op_item->vid;
		$op_name = pn_strip_input($op_item->cf_name);
		$op_req = $op_item->cf_req;
		$op_hidden = $op_item->cf_hidden;
		$op_value = pn_strip_input(is_param_post('cf'.$op_id));
		$op_uniq = '';
		if($op_vid == 0){
			$op_value = $op_uniq = pn_maxf_mb(get_cf_field($op_value,$op_item),500);
		} else {
			$op_value = intval($op_value);
		}
		
		$op_auto = $op_item->cf_auto;
		if(!$op_auto){ /* если не авто поле */
			if($op_vid == 0){
				
				$op_value = $op_value;
				
				$metas[] = array(
					'title' => $op_name,
					'data' => $op_value,
					'hidden' => $op_hidden,
				);
				
				if(!$op_value and $op_req == 1){
					$error = $error+1;
					$cf[] = $op_id;
					$cf_er[] = __('field is not filled','pn');
				}
				
			} else { /* select */
			
				$op_datas = explode("\n",ctv_ml($op_item->datas));
				foreach($op_datas as $key => $da){
					$da = pn_strip_input($da);
					if($da){
						if($key == $op_value){
							$op_uniq = $op_name;
							$metas[] = array(
								'title' => $op_name,
								'data' => $da,
								'hidden' => $op_hidden,
							);
						}		
					}
				}
			}
		} else {
			
			$op_value = $op_uniq = apply_filters('cf_strip_auto_value',$op_value, $op_auto, $op_item ,$naps, $cdata);
			
			if(!$op_value and $op_req == 1){
				$error = $error+1;
				$cf[] = $op_id;
				$cf_er[] = __('field is not filled','pn');	
			} 
			
			$cauv = array(
				'error' => 0,
				'error_text' => ''
			);
			$cauv = apply_filters('cf_auto_form_value',$cauv,$op_value,$op_item,$naps, $cdata);
			
			if($cauv['error'] == 1){
				$error = $error+1;
				$cf[] = $op_id;
				$cf_er[] = $cauv['error_text'];				
			}
			
			if($op_auto == 'first_name'){
				$first_name = $op_value;
			} elseif($op_auto == 'last_name'){
				$last_name = $op_value;
			} elseif($op_auto == 'second_name'){
				$second_name = $op_value;
			} elseif($op_auto == 'user_phone'){
				$user_phone = $op_value;
			} elseif($op_auto == 'user_skype'){
				$user_skype = $op_value;
			} elseif($op_auto == 'user_email'){
				$user_email = $op_value;
			} elseif($op_auto == 'user_passport'){	
				$user_passport = $op_value;
			}
			
			$metas[] = array(
				'title' => $op_name,
				'data' => $op_value,
				'hidden' => $op_hidden,
				'auto' => $op_auto,
			);				

			$auto_data[$op_auto] = $op_value;
			
		}
		
		$uniqueid = pn_strip_input($op_item->uniqueid);
		if($uniqueid){
			$unmetas[$uniqueid] = $op_uniq;
		}		
	}
	/* end основные поля */		
	
	/* дополнительные поля */
	$dmetas[1] = $dmetas[2] = array();	
	
	$doppoles = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."custom_fields_valut WHERE valut_id='$valut1i' AND status='1' AND place_id IN('0','1') OR valut_id='$valut2i' AND status='1' AND place_id IN('0','2') ORDER BY cf_order ASC");
	foreach($doppoles as $dp_item){
		$dp_id = $dp_item->id;
		$dp_vid = $dp_item->vid;
		$dp_name = pn_strip_input($dp_item->cf_name);
		$dp_req = $dp_item->cf_req;
		$dp_hidden = $dp_item->cf_hidden;
		$dp_value = pn_maxf_mb(pn_strip_input(is_param_post('cfc'.$dp_id)),500);
		$dp_uniq = '';
		if($dp_vid == 0){
			$dp_value = $dp_uniq = get_cf_field($dp_value,$dp_item);
		} else {
			$dp_value = intval($dp_value);
		}		
		
		$place_id = 1;
		if($dp_item->valut_id == $valut2i){
			$place_id = 2;
		}
		
		if($dp_vid == 0){
				
			$dmetas[$place_id][] = array(
				'title' => $dp_name,
				'data' => $dp_value,
				'hidden' => $dp_hidden,
			);
				
			if(!$dp_value and $dp_req == 1){
				$error = $error+1;
				$cfc[] = $dp_id;
				$cfc_er[] = __('field is not filled','pn');
			}
				
		} else { /* select */
		
			$dp_datas = explode("\n",ctv_ml($dp_item->datas));
			foreach($dp_datas as $key => $da){
				$da = pn_strip_input($da);
				if($da){
					if($key == $dp_value){
						$dp_uniq = $dp_name;
						$dmetas[$place_id][] = array(
							'title' => $dp_name,
							'data' => $da,
							'hidden' => $dp_hidden,
						);
					}		
				}
			}
				
		}
		
		$uniqueid = pn_strip_input($dp_item->uniqueid);
		if($uniqueid){
			$unmetas[$uniqueid] = $dp_uniq;
		}		
	}	
	/* end доп.поля */	
	
	/* фильтры счета валют */
	if($account1){
		$account1_bids = array(
			'error' => 0,
			'error_text' => ''
		);
		$account1_bids = apply_filters('account1_bids', $account1_bids, $account1, $naps, $vd1, $auto_data, $cdata);
		if($account1_bids['error'] == 1){
			$error = $error + 1;
			$account1_error = 1;
			$account1_error_text = $account1_bids['error_text'];
		}
	}

	if($account2){
		$account2_bids = array(
			'error' => 0,
			'error_text' => ''
		);
		$account2_bids = apply_filters('account2_bids', $account2_bids, $account2, $naps, $vd2, $auto_data, $cdata);
		if($account2_bids['error'] == 1){
			$error = $error + 1;
			$account2_error = 1;
			$account2_error_text = $account2_bids['error_text'];
		}		
	}	
	/* end фильтры счета валют */	
	
	$user_ip = pn_real_ip();
	/* проверки на обмен */
	$error_bids = array(
		'error' => 0,
		'error_text' => $error_text,
		'account1_error' => $account1_error,
		'account1_error_text' => $account1_error_text,
		'account2_error' => $account2_error,
		'account2_error_text' => $account2_error_text,
	);
	$error_bids = apply_filters('error_bids', $error_bids, $account1, $account2, $naps, $vd1, $vd2, $auto_data, $unmetas, $cdata);
	$error = $error + $error_bids['error'];
	$error_text = $error_bids['error_text'];
		
	$account1_error = is_isset($error_bids,'account1_error');
	$account1_error_text = is_isset($error_bids,'account1_error_text');	
	$account2_error = is_isset($error_bids,'account2_error');
	$account2_error_text = is_isset($error_bids,'account2_error_text');			
	/* end проверки */
	
	if($error > 0){
		
		$log['status'] = 'error';
		$log['status_code'] = 1;
		if(is_array($error_text) and count($error_text) > 0){ 
			$error_text = join('<br />',$error_text);
		} elseif(!$error_text){
			$error_text = __('Error!','pn'); 
		}
		$log['status_text'] = $error_text;
		
	} else {
		
		$datetime = current_time('mysql');
		$hashed = unique_bid_hashed();
		
		$array = array();
		$array['createdate'] = $datetime;
		$array['editdate'] = $datetime;
		$array['hashed'] = $hashed;
		$array['status'] = 'auto';
		$array['bid_locale'] = get_locale();
		$array['naps_id'] = $naps_id;
		$array['m_in'] = is_extension_name($naps->m_in); 
		$array['m_out'] = is_extension_name($naps->m_out);
		$array['curs1'] = $curs1;
		$array['curs2'] = $curs2;
		$array['valut1'] = $valut1;
		$array['valut2'] = $valut2;
		$array['valut1i'] = $valut1i;
		$array['valut2i'] = $valut2i;
		$array['vtype1'] = $vtype1;
		$array['vtype2'] = $vtype2;
		$array['vtype1i'] = $vtype1i;
		$array['vtype2i'] = $vtype2i;
		$array['psys1i'] = $psys1i;
		$array['psys2i'] = $psys2i;
		$array['user_id'] = $user_id;
		$array['user_sk'] = $user_sk;
		$array['user_sksumm'] = $user_sksumm;
		$array['user_ip'] = $user_ip;
		$array['first_name'] = $first_name;
		$array['last_name'] = $last_name;
		$array['second_name'] = $second_name;
		$array['user_phone'] = $user_phone;
		$array['user_skype'] = $user_skype;
		$array['user_email'] = $user_email;
		$array['user_passport'] = $user_passport;
		$array['account1'] = $account1;
		$array['account2'] = $account2;
		$array['metas'] = serialize($metas);	
		$array['dmetas'] = serialize($dmetas);
		$array['unmetas'] = serialize($unmetas);
		$array['exsum'] = $exsum;
		$array['summ1'] = $summ1;
		$array['dop_com1'] = $dop_com1;
		$array['summ1_dc'] = $summ1_dc;
		$array['com_ps1'] = $com_ps1;
		$array['summ1c'] = $summ1c;
		$array['summ1cr'] = $summ1cr;
		$array['summ2t'] = $summ2t;
		$array['summ2'] = $summ2;
		$array['dop_com2'] = $dop_com2;
		$array['com_ps2'] = $com_ps2;
		$array['summ2_dc'] = $summ2_dc;
		$array['summ2c'] = $summ2c;
		$array['summ2cr'] = $summ2cr;	
		$array['profit'] = $profit;
		$array['check_purse1'] = $check_purse1;
		$array['check_purse2'] = $check_purse2;
		$array = apply_filters('array_data_create_bids',$array, $naps, $vd1, $vd2, $cdata);
		
		$wpdb->insert($wpdb->prefix.'bids', $array);
		$obmen_id = $wpdb->insert_id;
		if($obmen_id > 0){
			$obmen = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."bids WHERE id='$obmen_id' AND status='auto'");
			if(isset($obmen->id)){
				do_action('change_bidstatus_all', 'auto',  $obmen->id, $obmen, 'site', 'user');
				do_action('change_bidstatus_auto', $obmen->id, $obmen, 'site', 'user'); 
			
				$log['url'] = get_bids_url($hashed);
				$log['status'] = 'success';
				$log['status_text'] = __('Your order successfully created','pn');	
			} else {
				$log['status'] = 1;
				$log['status_text'] = __('Error! System error!','pn');				
			}
		} else {
			$log['status'] = 1;
			$log['status_text'] = __('Error! Database error','pn');
		}
		
	}
	
	$log['account1_error'] = $account1_error;
	$log['account1_error_text'] = $account1_error_text;	
	$log['account2_error'] = $account2_error;
	$log['account2_error_text'] = $account2_error_text;	
	$log['summ1_error'] = $summ1_error;
	$log['summ1_error_text'] = $summ1_error_text;
	$log['summ2_error'] = $summ2_error;
	$log['summ2_error_text'] = $summ2_error_text;
	$log['summ1c_error'] = $summ1c_error;
	$log['summ1c_error_text'] = $summ1c_error_text;
	$log['summ2c_error'] = $summ2c_error;
	$log['summ2c_error_text'] = $summ2c_error_text;
	$log['cf'] = $cf;
	$log['cf_er'] = $cf_er;	
	$log['cfc'] = $cfc;
	$log['cfc_er'] = $cfc_er;	
	echo json_encode($log);
	exit;
}

/* bids add */
add_action('myaction_site_createbids', 'def_myaction_ajax_createbids');
function def_myaction_ajax_createbids(){
global $wpdb, $premiumbox;	
	
	$log = array();
	$log['status'] = '';
	$log['response'] = '';
	$log['status_code'] = 0; 
	$log['status_text'] = __('Error','pn');		

	$premiumbox->up_mode();
	
	$log = apply_filters('before_ajax_form_field', $log, 'createbids');
	$log = apply_filters('before_ajax_createbids', $log);
	
	$hashed = is_bid_hash(is_param_post('hash'));
	
	if(!$hashed){
		$log['status'] = 'error';
		$log['status_code'] = 1;
		$log['status_text'] = __('Error! System error','pn');
		echo json_encode($log);
		exit;		
	}
	
	$obmen = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."bids WHERE hashed='$hashed' AND status='auto'");
	if(!isset($obmen->id)){
		$log['status'] = 'error';
		$log['status_code'] = 1;
		$log['status_text'] = __('Error! System error','pn');
		echo json_encode($log);
		exit;		
	}
	
	$valut1i = intval($obmen->valut1i);
	$valut2i = intval($obmen->valut2i);
	
	$vd1 = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."valuts WHERE id='$valut1i'");
	$vd2 = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."valuts WHERE id='$valut2i'");

	if(!isset($vd1->id) or !isset($vd2->id)){
		$log['status'] = 'error';
		$log['status_code'] = 1;
		$log['status_text'] = __('Error! System error','pn');
		echo json_encode($log);
		exit;		
	}

	$naps_id = intval($obmen->naps_id);
	
	$naps = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."naps WHERE naps_status='1' AND autostatus='1' AND id='$naps_id'");
	if(!isset($naps->id)){
		$log['status'] = 'error';
		$log['status_code'] = 1;
		$log['status_text'] = __('Error! The direction do not exist','pn');
		echo json_encode($log);
		exit;			
	}	
	
	/* максимум и минимум */
	$max1 = get_max_sum_to_naps_give($naps, $vd1);
	$max2 = get_max_sum_to_naps_get($naps, $vd2);		
	$summ1 = pn_strip_input($obmen->summ1);
	$summ2 = pn_strip_input($obmen->summ2);
							
	if($summ1 > $max1 and is_numeric($max1) or $summ2 > $max2 and is_numeric($max2)){
		$log['status'] = 'error';
		$log['status_code'] = 1;
		$log['status_text'] = __('Error! Not enough reserve for the exchange','pn');
		echo json_encode($log);
		exit;													
	}							
	/* end максимум и минимум */
	
	add_mycookie('cache_sum1', 0);
	
	/* secure */
	$obmen_data = array();
	foreach($obmen as $key => $value){
		$obmen_data[$key] = $value;
	}
	
	$my_dir = wp_upload_dir();
	$dir = $my_dir['basedir'].'/bids/';
	if(!is_dir($dir)){
		@mkdir($dir, 0777);
	}
				
	$htaccess = $dir.'.htaccess';
	if(!is_file($htaccess)){
		$nhtaccess = "Order allow,deny \n Deny from all";
		$file_open = @fopen($htaccess, 'w');
		@fwrite($file_open, $nhtaccess);
		@fclose($file_open);		
	}
				 
	$file = $dir . $obmen->id .'.txt';
	$file_data = @serialize($obmen_data);
	$file_open = @fopen($file, 'w');
	@fwrite($file_open, $file_data);
	@fclose($file_open);
	/* end secure */			
	
	bid_hashdata($obmen->id, $obmen, '');
	
	$datetime = current_time('mysql');
	$array = array();
	$array['createdate'] = $datetime;
	$array['editdate'] = $datetime;
	$array['status'] = 'new';
	$array['user_hash'] = get_user_hash();
	
	$array = apply_filters('array_data_bids_new', $array, $obmen);
	
	$wpdb->update($wpdb->prefix.'bids', $array, array('id'=>$obmen->id));
	
	do_action('change_bidstatus_all', 'new',  $obmen->id, $obmen, 'site', 'user');	
	do_action('change_bidstatus_new', $obmen->id, $obmen, 'site', 'user'); 
	
	$log['url'] = get_bids_url($obmen->hashed);
	$log['status'] = 'success';
	$log['status_text'] = __('Your order successfully created','pn');		
	
	echo json_encode($log);
	exit;
}

/* bids cancel */
add_action('myaction_site_canceledbids', 'def_myaction_ajax_canceledbids');
function def_myaction_ajax_canceledbids(){
global $wpdb, $premiumbox;	
	
	$premiumbox->up_mode();
	
	$hashed = is_bid_hash(is_param_get('hash'));
	if($hashed){
		$obmen = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."bids WHERE hashed='$hashed'");
		if(isset($obmen->id)){
			
			do_action('before_bidaction', 'canceledbids', $obmen);
			do_action('before_bidaction_canceledbids', $obmen);
			
			if($obmen->status == 'new'){
				if(is_true_userhash($obmen)){
					$result = $wpdb->update($wpdb->prefix.'bids', array('status'=>'cancel','editdate'=>current_time('mysql')), array('id'=>$obmen->id));
					if($result == 1){
						do_action('change_bidstatus_all', 'cancel', $obmen->id, $obmen, 'site','user');
						do_action('change_bidstatus_cancel', $obmen->id, $obmen, 'site','user');
					}
				}
			}
		}
	} 
		$url = get_bids_url($hashed);
		wp_redirect($url);
		exit;
}

/* bids payed */
add_action('myaction_site_payedbids', 'def_myaction_ajax_payedbids');
function def_myaction_ajax_payedbids(){
global $wpdb, $premiumbox;
	
	$premiumbox->up_mode();
	
	$hashed = is_bid_hash(is_param_get('hash'));
	if($hashed){
		$obmen = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."bids WHERE hashed='$hashed'");
		if(isset($obmen->id)){
			
			do_action('before_bidaction', 'payedbids', $obmen);
			do_action('before_bidaction_payedbids', $obmen);
			
			if($obmen->status == 'new'){
				if(is_true_userhash($obmen)){					
					$naps_id = intval($obmen->naps_id);
					$naps = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."naps WHERE naps_status='1' AND autostatus='1' AND id='$naps_id'");
					$m_id = apply_filters('get_merchant_id','', is_isset($naps,'m_in'), $obmen);
					if(!$m_id){
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

/* merchant payed */
add_action('myaction_site_payedmerchant', 'def_myaction_ajax_payedmerchant'); 
function def_myaction_ajax_payedmerchant(){
global $wpdb, $premiumbox;	
	
	$premiumbox->up_mode();
	
	$error = 1;
	$hashed = is_bid_hash(is_param_get('hash'));
	if($hashed){
		
		$obmen = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."bids WHERE hashed='$hashed' AND status IN('new','techpay','coldpay')");
		if(isset($obmen->id)){
			
			do_action('before_bidaction', 'payedmerchant', $obmen);
			do_action('before_bidaction_payedmerchant', $obmen);
			
			$naps_id = intval($obmen->naps_id);
			$naps = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."naps WHERE autostatus='1' AND id='$naps_id'");
			if(isset($naps->m_in)){
				$m_id = apply_filters('get_merchant_id','',$naps->m_in, $obmen);
				if($m_id){
						
					$error = 0;
						
					$summ_to_pay = apply_filters('summ_to_pay', $obmen->summ1_dc , $m_id ,$obmen, $naps);

					echo apply_filters('merchant_header', '', $obmen, $naps);
							
						echo '<div id="goedform" style="display: none;">';
						echo apply_filters('merchants_action_bid', '', $m_id, $summ_to_pay, $obmen, $naps);
						echo apply_filters('merchants_action_bid_'. $m_id, '', $summ_to_pay, $obmen, $naps);
						echo '</div>';
						echo '<div id="redirect_text" class="success_div" style="display: none;">'. __('Redirecting. Please wait','pn') .'</div>';
						?>
						<script type="text/javascript">
						jQuery(function($){
							var cou_form = $('#goedform form').length;
							if(cou_form > 0){
								document.oncontextmenu=function(e){return false};
								window.history.replaceState(null, null, '<?php echo get_bids_url($hashed); ?>');
								$('#redirect_text').show();
								$('#goedform form').attr('target','_self').submit();
							} else {
								$('#goedform').show();
							} 						
						});
						</script>				
						<?php 
					
					echo apply_filters('merchant_footer', '', $obmen, $naps);

				} 
			} 
			
		} 
	}  
	
	if($error == 1){
		$url = get_bids_url($hashed);
		wp_redirect($url);
		exit;	
	}
}  