<?php
if( !defined( 'ABSPATH')){ exit(); }

/* добавляем JS */
add_action('siteplace_js','siteplace_js_mobile_exchange_table3');
function siteplace_js_mobile_exchange_table3(){
	if(get_mobile_type_table() == 3){
?>	
jQuery(function($){
	
	function get_table_exchange(id,id1,id2){
		
		$('#js_submit_button').addClass('active');		
		$('.js_loader').show();
			
		var dataString='id='+id+'&id1=' + id1 + '&id2=' + id2;

		$.ajax({
			type: "POST",
			url: "<?php echo get_ajax_link('mobile_table3_change_select');?>",
			dataType: 'json',
			data: dataString,
			error: function(res, res2, res3){
				<?php do_action('pn_js_error_response', 'ajax'); ?>
			},			
			success: function(res)
			{
					
				$('.js_loader').hide();

				$('#js_html').html(res['html']);	

				if($('#hexch_html').length > 0){
					$('#hexch_html').html('');
				}	
				
				<?php do_action('live_change_html'); ?>
					
			}
		});		
		
	}
	 
	$(document).on('change', '#js_left_sel', function(){
		var id1 = $('#js_left_sel').val();
		var id2 = $('#js_right_sel').val();
		get_table_exchange(1, id1, id2);
	});
	
	$(document).on('change', '#js_right_sel', function(){
		var id1 = $('#js_left_sel').val();
		var id2 = $('#js_right_sel').val();
		get_table_exchange(2, id1, id2);
	});	

	$(document).on('click', '#js_reload_table', function(){
		
		var id1 = $('#js_right_sel').val();
		var id2 = $('#js_left_sel').val();
		get_table_exchange(1, id1, id2);	
		
		return false;
	});
	
	$(document).on('click', '#js_submit_button', function(){
		if($(this).hasClass('active')){
			return false;
		}
	});
});	
<?php	
	}
} 	
/* end добавляем JS */

add_filter('exchange_mobile_table_type3','get_exchange_mobile_table3', 10, 3);
function get_exchange_mobile_table3($temp, $def_cur_from='', $def_cur_to=''){
global $wpdb;	

	$temp = '';
	
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);

	$cur_from = is_xml_value(is_param_get('cur_from'));
	if(!$cur_from){
		$cur_from = $def_cur_from;
	}
	$cur_from = is_xml_value($cur_from);
	
	$cur_to = is_xml_value(is_param_get('cur_to'));
	if(!$cur_to){
		$cur_to = $def_cur_to;
	}	
	$cur_to = is_xml_value($cur_to);	

	$from = $to = 0;
	if($cur_from and $cur_to){
		$vd1 = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."valuts WHERE xml_value='$cur_from'");
		$vd2 = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."valuts WHERE xml_value='$cur_to'");
		if(isset($vd1->id) and isset($vd2->id)){
			$from = $vd1->id;
			$to = $vd2->id;	
		}
	} else {
		$where = get_naps_where('home');
		$nap_items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."naps WHERE $where ORDER BY to3_1 ASC");
		foreach($nap_items as $nap){
			$output = apply_filters('get_naps_output', 1, $nap, 'home');
			if($output){
				$from = $nap->valut_id1;
				$to = $nap->valut_id2;
				break;
			}	
		}		
	}
	
	$temp .='
	<div class="xchange_type_list">
		<div class="xchange_type_list_ins">

			<div class="xtl_html_wrap">
				<div class="xtl_html_abs js_loader"></div>
				
				<div id="js_html">
					'. get_mobile_xtl_temp($from, $to, 1) .'	
				</div>
			</div>
			
		</div>
	</div>	
	';
	
	return $temp;
}

add_action('myaction_site_mobile_table3_change_select', 'def_myaction_ajax_mobile_table3_change_select');
function def_myaction_ajax_mobile_table3_change_select(){
global $wpdb, $premiumbox;	
	
	$log = array();
	$log['status'] = '';
	$log['response'] = '';
	$log['status_code'] = '0'; 
	$log['status_text']= '';
	$log['html'] = '';	
	
	$premiumbox->up_mode();
	
	$type_table = get_mobile_type_table();
	if($type_table == 3){	
	
		$id = intval(is_param_post('id'));
		$id1 = intval(is_param_post('id1'));
		$id2 = intval(is_param_post('id2'));	

		$log['html'] = get_mobile_xtl_temp($id1, $id2, $id);
	}
	
	echo json_encode($log);
	exit;
}

function get_mobile_xtl_temp($from, $to, $id){
global $wpdb, $premiumbox;
	
	if($id != 2){ $id = 1; }
	
	$v = array(); /* массив валют с данными */
	$valutsn = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."valuts ORDER BY psys_title ASC");
	foreach($valutsn as $valut){
		$v[$valut->id] = $valut;
	}	
	
	$temp = '';
	
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);

	$valid1 = intval($from);
	$valid2 = intval($to);	

	$where = get_naps_where('home');
	
	$v1 = $v2 = $img1 = $img2 = '';
	$tablenot = intval($premiumbox->get_option('exchange','tablenot')); 
	$tableselect = intval($premiumbox->get_option('exchange','tableselect'));
	$naps1 = $naps2 = array();

	$naps = ''; 
	
	if($valid1 and $valid2){ /* если есть id, выбираем направление по фильтрам и по id */
		$nap_items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."naps WHERE $where AND valut_id1='$valid1' AND valut_id2='$valid2' ORDER BY to3_1 ASC");
		foreach($nap_items as $nap){
			$output = apply_filters('get_naps_output', 1, $nap, 'home');
			if($output){
				$naps = $nap;
				break;
			}	
		}	
	}
	
	if(isset($naps->id)){ /* если есть направление обмена */

		$nap_items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."naps WHERE $where ORDER BY to3_1 ASC");
		foreach($nap_items as $nap){
			$output = apply_filters('get_naps_output', 1, $nap, 'home');
			if($output == 1){
				$naps1[$nap->valut_id1] = $nap;
				if($nap->valut_id1 == $valid1 or $tableselect != 1){
					$naps2[$nap->valut_id2] = $nap;
				}
			}
		}	
	
	} else { /* если нет направления обмена */
		if($tablenot == 1){ /* 0 - ошибка */	
			if($id == 1){ /* если выбрана левая сторона */
				
				$nap_items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."naps WHERE $where AND valut_id1='$valid1' ORDER BY to3_1 ASC");
				foreach($nap_items as $nap){
					$output = apply_filters('get_naps_output', 1, $nap, 'home');
					if($output){
						$naps = $nap;
						break;
					}	
				}					
				
				if(isset($naps->id)){
					
					$napobmens = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."naps WHERE $where ORDER BY to3_1 ASC");
					$r=0; 
					foreach($napobmens as $nd){ 
						$output = apply_filters('get_naps_output', 1, $nd, 'home');
						if($output){
							$naps1[$nd->valut_id1] = $nd;
							
							if($nd->valut_id1 == $valid1){ $r++;
								if($r==1){ $valid2 = $nd->valut_id2;}
							}
							if($nd->valut_id1 == $valid1 or $tableselect != 1){
								$naps2[$nd->valut_id2] = $nd;
							}
						}
					}	
					
				} else {

					$nap_items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."naps WHERE $where AND valut_id2='$valid2' ORDER BY to3_1 ASC");
					foreach($nap_items as $nap){
						$output = apply_filters('get_naps_output', 1, $nap, 'home');
						if($output){
							$naps = $nap;
							break;
						}	
					}				
					if(isset($naps->id)){
						
						$napobmens = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."naps WHERE $where ORDER BY to3_1 ASC");
						$r=0;
						foreach($napobmens as $nd){ 
							$output = apply_filters('get_naps_output', 1, $nd, 'home');
							if($output){
								$naps2[$nd->valut_id2] = $nd;
								
								if($nd->valut_id2 == $valid2){ $r++;
									if($r==1){ $valid1 = $nd->valut_id1; }
								}
								if($nd->valut_id2 == $valid2 or $tableselect != 1){
									$naps1[$nd->valut_id1] = $nd;
								}	
							}
						}						

					}	
				
				}
				
			} else { /* если выбрана правая сторона */
			
				$nap_items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."naps WHERE $where AND valut_id2='$valid2' ORDER BY to3_1 ASC");
				foreach($nap_items as $nap){
					$output = apply_filters('get_naps_output', 1, $nap, 'home');
					if($output){
						$naps = $nap;
						break;
					}	
				}			
			
				if(isset($naps->id)){
					
					$napobmens = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."naps WHERE $where ORDER BY to3_1 ASC");
					$r=0;
					foreach($napobmens as $nd){ 
						$output = apply_filters('get_naps_output', 1, $nd, 'home');
						if($output){
							$naps2[$nd->valut_id2] = $nd;
							
							if($nd->valut_id2 == $valid2){ $r++;
								if($r==1){ $valid1 = $nd->valut_id1; }
							}
							if($nd->valut_id2 == $valid2 or $tableselect != 1){
								$naps1[$nd->valut_id1] = $nd;
							}
						}
					}						
					
				} else {
					
					$nap_items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."naps WHERE $where AND valut_id1='$valid1' ORDER BY to3_1 ASC");
					foreach($nap_items as $nap){
						$output = apply_filters('get_naps_output', 1, $nap, 'home');
						if($output){
							$naps = $nap;
							break;
						}	
					}					
					if(isset($naps->id)){
						
						$napobmens = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."naps WHERE $where ORDER BY to3_1 ASC");
						$r=0;
						foreach($napobmens as $nd){ 
							$output = apply_filters('get_naps_output', 1, $nd, 'home');
							if($output){
								$naps1[$nd->valut_id1] = $nd;
								 
								if($nd->valut_id1 == $valid1){ $r++;
									if($r==1){ $valid2 = $nd->valut_id2; }
								}
								if($nd->valut_id1 == $valid1 or $tableselect != 1){
									$naps2[$nd->valut_id2] = $nd;
								}
							}
						}						
						
					}					
					
				}
			}
		}
	}
	
	if(!isset($naps->id)){
		$napobmens = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."naps WHERE $where ORDER BY to3_1 ASC");
		foreach($napobmens as $nd){
			$output = apply_filters('get_naps_output', 1, $nd, 'home');
			if($output){
				$naps1[$nd->valut_id1] = $nd;
				$naps2[$nd->valut_id1] = $nd;
				$naps1[$nd->valut_id2] = $nd;
				$naps2[$nd->valut_id2] = $nd;
			}
		}
	}	
	
		$v1 = $valid1;
		$v2 = $valid2;
		
		if(!isset($v[$v1]) and !isset($v[$v2])){
			return '';
		}		
		
		$vd1 = $v[$v1];
		$vd2 = $v[$v2];
		
		$img1 = get_valut_logo($vd1);
		$img2 = get_valut_logo($vd2); 		
	
		if(isset($naps->id)){

			$post_sum = is_my_money(is_param_get('get_sum'));
			if($post_sum <= 0){
				$post_sum = is_my_money(get_mycookie('summ1'));
			}
			$cdata = get_calc_data($vd1,$vd2,$naps,$user_id, $post_sum);
			
			$vtype1 = $cdata['vtype1'];
			$vtype2 = $cdata['vtype2'];
			$psys1 = $cdata['psys1'];
			$psys2 = $cdata['psys2'];											
							
			$v_title1 = $psys1.' '.$vtype1;				
			$v_title2 = $psys2.' '.$vtype2;
			
			$reserv = is_out_sum(get_naps_reserv($vd2->valut_reserv,$vd2->valut_decimal, $naps), $vd2->valut_decimal, 'reserv');									
			
			$curs1 = $cdata['curs1'];
			$curs2 = $cdata['curs2'];				
			
			$viv_com1 = 'style="display: none;"'; /* не выводим поле доп.комиссии */
			if($cdata['viv_com1'] == 1){
				$viv_com1 = '';
			}
			$viv_com2 = 'style="display: none;"'; /* не выводим поле доп.комиссии */
			if($cdata['viv_com2'] == 1){
				$viv_com2 = '';
			}				
			
			$summ1_error = $summ2_error = $summ1c_error = $summ2c_error = '';
			$summ1_error_txt = $summ2_error_txt = $summ1c_error_txt = $summ2c_error_txt = '';				
			
		}	
	
		$temp .= '
		<div class="xtl_cols">
			<div class="xtl_left_col">
								
				<div class="xtl_selico_wrap">				
					<div class="xtl_ico_wrap">
						<div class="xtl_ico" style="background: url('. $img1 .') no-repeat center center;"></div>
					</div>
											
					<div class="xtl_select_wrap">
						<select name="" id="js_left_sel" class="js_my_sel" autocomplete="off">';
							foreach($naps1 as $key => $np){
								$temp .= '<option value="'. $key .'" '. selected($key,$v1,false) .' data-img="'. get_valut_logo($v[$key]) .'">'. get_valut_title($v[$key]) .'</option>';					
							}
						$temp .= '	
						</select>						
					</div>													
										
				</div>';
				
				if(isset($naps->id)){
					$temp .= '<input type="hidden" name="" class="js_napr_id" value="'. $naps->id .'" />';
					
					$temp .= '
					<div class="xtl_input_wrap js_wrap_error js_wrap_error_br '. $summ1_error .'">';
					
						$temp .= apply_filters('exchange_input', '', 'give', $cdata, $vd1, $vd2, $naps, $user_id, $post_sum);
						
						$temp .= '
						<div class="js_error js_summ1_error">'. $summ1_error_txt .'</div>
					</div>';						
					
					$temp .= '
					<div class="xtl_commis_wrap js_wrap_error js_wrap_error_br '. $summ1c_error .'">';				
						
						$temp .= apply_filters('exchange_input', '', 'give_com', $cdata, $vd1, $vd2, $naps, $user_id, $post_sum);
						
						$temp .= '
						<div class="xtl_commis_text">'. __('With fees','pn') .'</div>
						<div class="js_error js_summ1c_error">'. $summ1c_error_txt .'</div>				
						<div class="clear"></div>
					</div>';
					
				}	
				
			$temp .= '	
			</div>	
				
			<div class="xtl_center_col">
				<a href="#" class="xtl_change" id="js_reload_table"></a>
			</div>
			
			<div class="xtl_right_col">

				<div class="xtl_selico_wrap">
					<div class="xtl_ico_wrap">
						<div class="xtl_ico" style="background: url('. $img2 .') no-repeat center center;"></div>
					</div>

					<div class="xtl_select_wrap">
						<select name="" id="js_right_sel" class="js_my_sel" autocomplete="off">';
							foreach($naps2 as $key => $np){
								$temp .= '<option value="'. $key .'" '. selected($key,$v2,false) .' data-img="'. get_valut_logo($v[$key]) .'">'. get_valut_title($v[$key]) .'</option>';					
							}
						$temp .= '	
						</select>						
					</div>												
				</div>';

				if(isset($naps->id)){
					
					$temp .= '
					<div class="xtl_input_wrap js_wrap_error js_wrap_error_br '. $summ2_error .'">';
					
						$temp .= apply_filters('exchange_input', '', 'get', $cdata, $vd1, $vd2, $naps, $user_id, $post_sum);
						
						$temp .= '
						<div class="js_error js_summ2_error">'. $summ2_error_txt .'</div>	
					</div>';
					
					$temp .= '
					<div class="xtl_commis_wrap js_wrap_error js_wrap_error_br '. $summ2c_error .'">';				
						
						$temp .= apply_filters('exchange_input', '', 'get_com', $cdata, $vd1, $vd2, $naps, $user_id, $post_sum);
						
						$temp .= '
						<div class="xtl_commis_text">'. __('With fees','pn') .'</div>
						<div class="js_error js_summ2c_error">'. $summ2c_error_txt .'</div>				
						<div class="clear"></div>
					</div>';					
					
				}				
				
				$temp .= '	 	
			</div>
		</div>';

		if(isset($naps->id)){			 			
			
			$temp .='	
			<div class="xtl_submit_wrap">	
				<a href="'. get_exchange_link($naps->naps_name) .'" class="xtl_submit js_exchange_link" id="js_submit_button" data-naps="'. $naps->id .'">'. __('Exchange','pn') .'</a>
					<div class="clear"></div>
			</div>';	
			
			$tbl3_rightcol_data = array(
				'rate' => '
				<div class="xtl_line xtl_exchange_rate">
					'. __('Exchange rate','pn') .': <span class="js_curs_html">'. apply_filters('show_table_course',$curs1,$cdata['decimal1']) .' '. $vtype1 .' = '. apply_filters('show_table_course',$curs2,$cdata['decimal2']) .' '. $vtype2 .'</span>
				</div>						
				',
				'zreserv' => '
				<div class="xtl_line xtl_exchange_reserve">
					'. __('Reserve','pn') .': <span>'. $reserv .' '. $vtype2 .'</span>
				</div>						
				',
			);
			$tbl3_rightcol_data = apply_filters('mobile_tbl3_rightcol_data', $tbl3_rightcol_data, $cdata, $vd1, $vd2, $naps, $user_id, $post_sum);			
			
			foreach($tbl3_rightcol_data as $value){
				$temp .= $value; 
			}					
			
		} else {

			$temp .= '<div class="xtl_error"><div class="xtl_error_ins">'. __('Selected direction does not exist','pn') .'</div></div>';
			
		}
	
	return $temp;	
}