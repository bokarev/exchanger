<?php
if( !defined( 'ABSPATH')){ exit(); }
 
function get_account_wline($vd, $naps, $id, $place){ 
global $wpdb;	

	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);

	$temp = '';
	
	$vdid = $vd->id;
	if($id == 1){
		$show = $vd->show1;
		$show = apply_filters('form_bids_account1', $show, $naps, $vd);
		$txt = pn_strip_input(ctv_ml($vd->txt1));
		if(!$txt){ $txt = __('From account','pn'); }
		$helps = pn_strip_text(ctv_ml($vd->helps));
	} else {
		$show = $vd->show2;
		$show = apply_filters('form_bids_account2', $show, $naps, $vd);
		$txt = pn_strip_input(ctv_ml($vd->txt2));
		if(!$txt){ $txt = __('Into account','pn'); }
		$helps = pn_strip_text(ctv_ml($vd->helps2));
	}
		$check_purse = 0;
		if($naps->check_purse == $id or $naps->check_purse == 3){
			$check_purse = 1;
		} 
		$check_purse_text = pn_strip_input(ctv_ml($vd->check_text));
		if(!$check_purse_text){ $check_purse_text = __('e-wallet has valid status','pn'); }
		
		$notv = 'style="display: none"';
		if($show == 1){ $notv = ''; }
		
		$placeholder = apply_filters('placeholder_field_account', pn_strip_input($vd->firstzn), $vd, $naps);
														
		$h_class = '';
		$h_div = '';
		$has_help_cl = '';
		$help_span = '';		
		if($helps){
			$has_help_cl = 'has_help';
			$help_span = '<span class="help_tooltip_label"></span>';			
			$h_class = 'js_help';
			$h_div = '
				<div class="info_window js_window">
					<div class="info_window_ins">
						<div class="info_window_abs"></div>
						'. apply_filters('comment_text', $helps) .'
					</div>
				</div>															
			';
		}
														
		$purse = ''; 												
		if(!$purse){
			$purse = pn_strip_input(get_mycookie('cache_account'. $vdid));
		}
		
		if($place == 'widget'){
			$class = 'hexch';			
		} else {
			$class = 'xchange';			
		}
		
		$temp .= '
		<div class="'. $class .'_curs_line" '. $notv .'>
			<div class="'. $class .'_curs_line_ins">
				<div class="'. $class .'_curs_label">
					<div class="'. $class .'_curs_label_ins">
						<label for="account'. $id .'"><span class="'. $class .'_label">'. $txt .'<span class="req">*</span>: '. $help_span .'</span></label>
					</div>
				</div>	
			</div>
			<div class="'. $class .'_curs_input js_wrap_error js_wrap_error_br js_window_wrap">
			';
				$account_input = '<input type="text" name="account'. $id .'" cash-id="account'. $vdid .'" id="account'. $id .'" class="js_account'. $id .' '. $h_class .' cache_data check_cache" placeholder="'. $placeholder .'" value="'. $purse .'" />';
				$temp .= apply_filters('form_bids_account_input', $account_input, $id, $vdid, $purse, $placeholder, $h_class);
			$temp .= '
				<div class="js_error js_account'. $id .'_error"></div>
				'. $h_div .'
			</div>
				<div class="clear"></div>
			';
			
			if($check_purse > 0){
				$temp .= '
				<div class="check_purse_line">
					<div class="checkbox js_check_purse"><input type="checkbox" name="check_purse'. $id .'" value="1" /> '. $check_purse_text .'</div>
				</div>
				';
			}	
				
			$temp .= '
		</div>	
		';	
	
	return $temp;
}

function get_doppole_wline($vd, $naps, $id, $place){
global $wpdb;	

	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);

	$temp = '';
	
	$where = '';
	if($id == 1){
		$where .= " AND place_id IN('0','1')";
	} else {
		$where .= " AND place_id IN('0','2')";
	}
	
	$valut_id = $vd->id;
	$datas = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."custom_fields_valut WHERE valut_id='$valut_id' AND status='1' $where ORDER BY cf_order ASC");
	foreach($datas as $data){
		$data_id = $data->id;
		$title = pn_strip_input(ctv_ml($data->cf_name));
		$cf_req = $data->cf_req;
		$req = '';
		if($cf_req == 1){
			$req = '<span class="req">*</span>';
		}
		
		if($place == 'widget'){
			$class = 'hexch';			
		} else {
			$class = 'xchange';			
		}		
		
		$helps = pn_strip_text(ctv_ml($data->helps));
		$has_help_cl = '';
		$help_span = '';
		if($helps){
			$has_help_cl = 'has_help';
			$help_span = '<span class="help_tooltip_label"></span>';
		}
		
		$temp .= '
		<div class="'. $class .'_curs_line '. $has_help_cl .'">
			<div class="'. $class .'_curs_label">
				<div class="'. $class .'_curs_label_ins">
					<label for="cfc'. $data_id .'"><span class="'. $class .'_label">'. $title .''. $req .': '. $help_span .'</span></label>
				</div>	
			</div>
			<div class="'. $class .'_curs_input js_wrap_error js_wrap_error_br js_window_wrap">
			';
			
			$value = pn_strip_input(get_mycookie('cache_cfc'. $data_id)); 
			
			$vid = $data->vid;
			if($vid == 0){
				
				$h_class = '';
				$h_div = '';
				if($helps){
					$h_class = 'js_help';
					$h_div = '
						<div class="info_window js_window">
							<div class="info_window_ins">
								<div class="info_window_abs"></div>
								'. apply_filters('comment_text', $helps) .'
							</div>
						</div>															
					';
				}		

				$temp .= '
				<input type="text" name="cfc'. $data_id .'" id="cfc'. $data_id .'" cash-id="cfc'. $data_id .'" class="js_cfc'. $data_id .' cache_data check_cache '. $h_class .'" placeholder="'. pn_strip_input($data->firstzn) .'" value="'. $value .'" />
				'. $h_div .'								
				';				
				
			} else {
				
				$temp .= '
				<select name="cfc'. $data_id .'" cash-id="cfc'. $data_id .'" class="js_my_sel cache_data check_cache" id="cfc'. $data_id .'" autocomplete="off">';
					$datas = explode("\n",ctv_ml($data->datas));
					foreach($datas as $key => $da){
						$da = pn_strip_input($da);
						if($da){
							
							$temp .= '<option value="'. $key .'" '. selected($key, $value, false) .'>'. $da .'</option>';
							
						}
					}
				$temp .= '	
				</select>	
				';
				
			}
			
			$temp .= '
				<div class="js_error js_cfc'. $data_id .'_error"></div>
			</div>
				<div class="clear"></div>
		</div>	
		';
	
	}
	
	return $temp;
}

function get_napspole_wline($naps, $place){
global $wpdb;	

	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);	

	$temp = '';
	
	$naps_id = $naps->id;
	$datas = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."custom_fields LEFT OUTER JOIN ". $wpdb->prefix ."cf_naps ON(".$wpdb->prefix."custom_fields.id = ". $wpdb->prefix ."cf_naps.cf_id) WHERE status='1' AND ". $wpdb->prefix ."cf_naps.naps_id = '$naps_id' ORDER BY cf_order ASC");
	
	if($place == 'widget'){
		$class = 'hexch';
	} else {
		$class = 'xchange';
	}	
	
	if(count($datas) > 0){
		
		$temp .= '
		<div class="'. $class .'_pers">
			<div class="'. $class .'_pers_ins">
													
				<div class="'. $class .'_pers_title">
					<div class="'. $class .'_pers_title_ins">
						<span>'. apply_filters('exchange_personaldata_title',__('Personal data','pn')) .'</span>
					</div>
				</div>
				<div class="'. $class .'_pers_div">
					<div class="'. $class .'_pers_div_ins">';

					foreach($datas as $data){
	
						$title = pn_strip_input(ctv_ml($data->cf_name));
						$data_id = $data->cf_id;
						$cf_req = $data->cf_req;
						$req = '';
						if($cf_req == 1){
							$req = '<span class="req">*</span>';
						}	
					
						$helps = pn_strip_text(ctv_ml($data->helps));
						$has_help_cl = '';
						$help_span = '';
						if($helps){
							$has_help_cl = 'has_help';
							$help_span = '<span class="help_tooltip_label"></span>';
						}					
					
						$temp .= '
						<div class="'. $class .'_pers_line '. $has_help_cl .'">
							<div class="'. $class .'_pers_label">
								<div class="'. $class .'_pers_label_ins">
									<label for="cf'. $data_id .'"><span class="'. $class .'_label">'. $title .''. $req .': '. $help_span .'</span></label>
								</div>	
							</div>
							<div class="'. $class .'_pers_input">
								<div class="js_wrap_error js_wrap_error_br js_window_wrap">';
									
								$vid = $data->vid;
								
								$value = '';
								$cf_auto = $data->cf_auto;
								if($user_id){
									$value = apply_filters('cf_auto_user_value','',$cf_auto, $ui);
								}
									
								if(!$value){
									$value = pn_strip_input(get_mycookie('cache_cf'. $data_id));
								}				
								
								if($vid == 0){
									
									$h_class = '';
									$h_div = '';
									if($helps){
										$h_class = 'js_help';
										$h_div = '
											<div class="info_window js_window">
												<div class="info_window_ins">
													<div class="info_window_abs"></div>
													'. apply_filters('comment_text', $helps) .'
												</div>
											</div>															
										';
									}		

									$temp .= '
									<input type="text" name="cf'. $data_id .'" cash-id="cf'. $data_id .'" id="cf'. $data_id .'" class="cache_data check_cache js_cf'. $data_id .' '. $h_class .'" placeholder="'. pn_strip_input($data->firstzn) .'" value="'. $value .'" />
									'. $h_div .'								
									';				
									
								} else {
									
									$temp .= '
									<select name="cf'. $data_id .'" cash-id="cf'. $data_id .'" class="js_my_sel cache_data check_cache" id="cf'. $data_id .'" autocomplete="off">';
										$datas = explode("\n",ctv_ml($data->datas));
										foreach($datas as $key => $da){
											$da = pn_strip_input($da);
											if($da){
												
												$temp .= '<option value="'. $key .'" '. selected($key, $value, false) .'>'. $da .'</option>';
												
											}
										}
									$temp .= '	
									</select>	
									';
									
								}				
									
									$temp .= '
									<div class="js_error js_cf'. $data_id .'_error"></div>
								</div>
							</div>
								<div class="clear"></div>
						</div>';
	
					}
	
					$temp .= '
					</div>
				</div>											
			</div>
		</div>';	
	
	}
	
	return $temp;	
}  