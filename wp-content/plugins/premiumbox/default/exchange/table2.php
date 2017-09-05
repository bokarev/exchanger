<?php
if( !defined( 'ABSPATH')){ exit(); }

/* добавляем JS */
add_action('siteplace_js','siteplace_js_exchange_table2');
function siteplace_js_exchange_table2(){
	if(get_type_table() == 2){
?>	
/* exchange table */
jQuery(function($){
	 
    $(document).on('click', ".js_icon_left", function () {
        if(!$(this).hasClass('active')){
		    
			var vtype = $(this).attr('data-type');
			$(".js_icon_left").removeClass('active');
			$(this).addClass('active');
	
			if(vtype == 0){
				$('.js_item_left').removeClass('not');
			} else {
				$('.js_item_left').addClass('not');
				$('.js_item_left_'+vtype).removeClass('not');
			}
			
        }
        return false;
    });
	
    $(document).on('click', ".js_icon_right", function () {
        if(!$(this).hasClass('active')){
		    
			var vtype = $(this).attr('data-type');
			$(".js_icon_right").removeClass('active');
			$(this).addClass('active');
	
			if(vtype == 0){
				$('.js_item_right').removeClass('not');
			} else {
				$('.js_item_right').addClass('not');
				$('.js_item_right_'+vtype).removeClass('not');
			}
			
        }
        return false;
    });	
	
function go_change_ps(){
	var id1 = $('.js_item_left.active').attr('data-id');
	var id2 = $('.js_item_right.active').attr('data-id');
	var cur1 = $(".js_icon_left.active").attr('data-type');
	var cur2 = $(".js_icon_right.active").attr('data-type');
	$('#js_submit_button').addClass('active');		
	$('.js_loader').show();
	
	var dataString='id1=' + id1 + '&id2=' + id2 + '&cur1=' + cur1 + '&cur2=' + cur2;
    $.ajax({
        type: "POST",
        url: "<?php echo get_ajax_link('table2_change_ps');?>",
        dataType: 'json',
		data: dataString,
 		error: function(res, res2, res3){
			<?php do_action('pn_js_error_response', 'ajax'); ?>
		},      
		success: function(res)
        {
			$('.js_loader').hide();
			if(res['status'] == 'success'){
				$('#js_submit_button').removeClass('active');
				$('#js_html').html(res['html']);
				$('#js_submit_button').attr('href', res['link']);
				$('#js_submit_button').attr('data-naps', res['naps']);
				
				<?php do_action('live_change_html'); ?>
			} 
			
			if(res['status'] == 'error'){
				$('#js_html').html('<div class="xtp_error"><div class="xtp_error_ins">' + res['status_text'] + '</div></div>');
			}

			if($('#hexch_html').length > 0){
				$('#hexch_html').html('');
			}			
        }
    });
	
    return false;	
}	
	
    $(document).on('click', ".js_item_left", function () {
        if(!$(this).hasClass('active')){
		    
			$(".js_item_left").removeClass('active');
			$(this).addClass('active');
			go_change_ps();
			
        }
        return false;
    });	
    $(document).on('click', ".js_item_right", function () {
        if(!$(this).hasClass('active')){
		    
			$(".js_item_right").removeClass('active');
			$(this).addClass('active');
			go_change_ps();
			
        }
        return false;
    });	

function go_change_select(){
	var id1 = $('.js_item_sel1').val();
	var id2 = $('.js_item_sel2').val();
	$('#js_submit_button').addClass('active');		
	$('.js_loader').show();
	
	var dataString='id1=' + id1 + '&id2=' + id2;
    $.ajax({
        type: "POST",
        url: "<?php echo get_ajax_link('table2_change_select');?>",
        dataType: 'json',
		data: dataString,
 		error: function(res, res2, res3){
			<?php do_action('pn_js_error_response', 'ajax'); ?>
		},       
		success: function(res)
        {
			$('.js_loader').hide();
			if(res['status'] == 'success'){
				$('#js_submit_button').removeClass('active');
				$('#js_html').html(res['html']);
				$('#js_submit_button').attr('href', res['link']);
				$('#js_submit_button').attr('data-naps', res['naps']);
				
				<?php do_action('live_change_html'); ?>
			} 
			if(res['status'] == 'error'){
				$('#js_html').find('input').addClass('error');
			}

			if($('#hexch_html').length > 0){
				$('#hexch_html').html('');
			}				
			
        }
    });
	
    return false;	
}	
	
    $(document).on('change', ".js_item_sel", function () {

		go_change_select();

        return false;
    });

	$(document).on('click', '#js_submit_button', function(){
		if($(this).hasClass('active')){
			return false;
		}
	});
	
});
/* end exchange table */	
<?php	
	}
}	
/* end добавляем JS */

add_filter('exchange_table_type2','get_exchange_table2', 10, 3);
function get_exchange_table2($temp, $def_cur_from='', $def_cur_to=''){
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
	
	$valuts = array(); /* массив валют с данными */
	$valutsn = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."valuts");
	foreach($valutsn as $valut){
		$valuts[$valut->id] = $valut;
	}	
	
	$where = get_naps_where('home');
	$napobmens = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."naps WHERE $where ORDER BY to2_1 ASC");
	
	$standart1 = $standart2 = 0; /* стандартные значения */
	$default =  0; /* стандартное значение обмена */
	$vals1 = array();
	$vals2 = array();
	$val_types1 = array();
	$val_types2 = array();
	$naps1 = array();
	$naps2 = array();
	$r=0;
	foreach($napobmens as $napob){
		$output = apply_filters('get_naps_output', 1, $napob, 'home');
		if($output){
			$v_id1 = $napob->valut_id1;
			$v_id2 = $napob->valut_id2;
			
			if(isset($valuts[$v_id1]) and isset($valuts[$v_id2])){ $r++;
				
				$vd1 = $valuts[$v_id1];
				$vd2 = $valuts[$v_id2];
			
				$vt1 = is_site_value($vd1->vtype_title);
				$vt2 = is_site_value($vd2->vtype_title);
			
				$vals1[$vt1] = $vt1;
				$vals2[$vt2] = $vt2;
			
				$psys1 = $napob->psys_id1;
				$psys2 = $napob->psys_id2;
			
				if(!isset($naps1[$psys1])){
					$naps1[$psys1] = array(
						'title' => pn_strip_input(ctv_ml($vd1->psys_title)),
						'img' => is_ssl_url($vd1->psys_logo),
						'order' => $napob->to2_1,
					);
				}
				if(!isset($naps2[$psys2])){
					$naps2[$psys2] = array(
						'title' => pn_strip_input(ctv_ml($vd2->psys_title)),
						'img' => is_ssl_url($vd2->psys_logo),
						'order' => $napob->to2_2,
					);
				}		
						
				if(isset($val_types1[$psys1])){
					if(!in_array($vt1,$val_types1[$psys1])){
						$val_types1[$psys1][] = 'js_item_left_'.$vt1;
					}
				} else {
					$val_types1[$psys1][] = 'js_item_left_'.$vt1;
				}
				
				if(isset($val_types2[$psys2])){
					if(!in_array($vt2,$val_types2[$psys2])){
						$val_types2[$psys2][] = 'js_item_right_'.$vt2;
					}
				} else {
					$val_types2[$psys2][] = 'js_item_right_'.$vt2;
				}			
			
				if($r == 1){
					$standart1 = $psys1;
					$standart2 = $psys2;
					$default = $napob;
				}
			
				if($cur_from == $vd1->xml_value and $cur_to == $vd2->xml_value){	
					$standart1 = $psys1;
					$standart2 = $psys2;
					$default = $napob;	
				}
			
			}
		}
	}	
	
	$naps1_data = $naps2_data = array();
	$naps1_data = get_table2_naps_filter($naps1, $val_types1, $standart1);
	$naps2_data = get_table2_naps_filter($naps2, $val_types2, $standart2);	
		
		$temp .= '
		<div class="xchange_type_plitka">
			<div class="xchange_type_plitka_ins">';				
				
				$exchange_table2_head ='
				<div class="xtp_icon_wrap">
					<div class="xtp_left_col_icon">
				
						<div class="xtp_icon active js_icon_left" data-type="0"><div class="xtp_icon_ins"><div class="xtp_icon_abs"></div>'. __('All','pn') .'</div></div>
						';
				
						foreach($vals1 as $av){
							$exchange_table2_head .= '<div class="xtp_icon js_icon_left js_icon_left_'. $av .'" data-type="'. $av .'"><div class="xtp_icon_ins"><div class="xtp_icon_abs"></div>'. $av .'</div></div>';
						}
				
						$exchange_table2_head .= '
							<div class="clear"></div>
					</div>
					<div class="xtp_right_col_icon">

						<div class="xtp_icon active js_icon_right" data-type="0"><div class="xtp_icon_ins"><div class="xtp_icon_abs"></div>'. __('All','pn') .'</div></div>
						';
							
						foreach($vals2 as $av){
							$exchange_table2_head .= '<div class="xtp_icon js_icon_right js_icon_right_'. $av .'" data-type="'. $av .'"><div class="xtp_icon_ins"><div class="xtp_icon_abs"></div>'. $av .'</div></div>';
						}							
							
						$exchange_table2_head .= '
							<div class="clear"></div>
					</div>
						<div class="clear"></div>
				</div>';	
				$temp .= apply_filters('exchange_table2_head',$exchange_table2_head, $vals1, $vals2);
					
				$temp .='
				<div class="xtp_table_wrap">
					<div class="xtp_table_wrap_ins">
						<div class="xtp_col_table_top">
							<div class="xtp_left_col_table">
						';
							$temp .= apply_filters('exchange_table2_part', '',  __('You send','pn'), $naps1_data, 'left');
						$temp .= '	
							</div>
						<div class="xtp_right_col_table">';	
							$temp .= apply_filters('exchange_table2_part', '', __('You receive','pn'), $naps2_data, 'right');						
						$temp .='
							</div>
							<div class="clear"></div>
						</div>';									
						
							$temp .='	
							<div class="xtp_html_wrap">
								<div class="xtp_html_abs js_loader"></div>
								<div id="js_html">';
								
									$temp .= get_xtp_temp($default, $valuts);
									
									$temp .= '
								</div>
							</div>';
							
						$temp .='
						<div class="xtp_submit_wrap">
							<a href="'. get_exchange_link(is_isset($default,'naps_name')) .'" class="xtp_submit js_exchange_link" id="js_submit_button" data-naps="'. is_isset($default,'id') .'">'. __('Exchange','pn') .'</a>
								<div class="clear"></div>							
						</div>
						
						<div id="js_error_div"></div>		
					</div>
				</div>';
				
			$temp .='	
			</div>
		</div>				
		';	
	
	return $temp;
}

function get_table2_naps_filter($naps, $val_types, $standart){

	$new_array = array();
	foreach($naps as $psys => $data){
		$new_array[$psys] = intval($data['order']);
	}
	asort($new_array);
	
	$array = array();
	foreach($new_array as $psys => $order){
		$data = $naps[$psys];
		
		$class = '';
		if(isset($val_types[$psys])){
			if(is_array($val_types[$psys])){
				$clt = array_unique($val_types[$psys]);
				$class = join(' ',$clt);
			}
		}		
		
		$acl = '';
		if($psys == $standart){
			$acl = 'active';
		}
		
		$array[] = array(
			'title' => $data['title'],
			'psys_id' => $psys,
			'img' => $data['img'],
			'class' => $class,
			'active' => $acl,
		);
	}

	return $array;		
}

add_filter('exchange_table2_part', 'def_exchange_table2_part', 10, 4);
function def_exchange_table2_part($temp, $title, $naps, $place=''){
	
	$temp = '
	<div class="xtp_table">
		<div class="xtp_table_ins">	
			<div class="xtp_table_title">
				<div class="xtp_table_title_ins">
					<span>'. $title .'</span>
				</div>
			</div>
				<div class="clear"></div>
											
			<div class="xtp_table_list">
				<div class="xtp_table_list_ins">';
													
					foreach($naps as $data){
														
						$class = $data['class'];
						$acl = $data['active'];								
														
						$temp .= '
						<div class="xtp_item js_item js_item_'. $place .' '. $class .' '. $acl .'" data-id="'. $data['psys_id'] .'" title="'. $data['title'] .'">
							<div class="xtp_item_ins">
								<div class="xtp_item_abs"></div>
								<div class="xtp_item_ico" style="background: url('. $data['img'] .') no-repeat center center;"></div>
							</div>
						</div>
						';
														
					} 
													
				$temp .= '
					<div class="clear"></div>
				</div>
			</div>
		</div>
	</div>
	';
	
	return $temp;
}

add_action('myaction_site_table2_change_select', 'def_myaction_ajax_table2_change_select');
function def_myaction_ajax_table2_change_select(){
global $wpdb, $premiumbox;	
	
	$log = array();
	$log['status'] = '';
	$log['response'] = '';
	$log['status_code'] = '0'; 
	$log['status_text']= '';
	
	$premiumbox->up_mode();
	
	if(get_type_table() == 2){	
	
		$ui = wp_get_current_user();
		$user_id = intval($ui->ID);
		
		$id1 = intval(is_param_post('id1'));
		$id2 = intval(is_param_post('id2'));	
		
		$valuts = array(); /* массив валют с данными */
		$valutsn = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."valuts ORDER BY psys_title ASC");
		foreach($valutsn as $valut){
			$valuts[$valut->id] = $valut;
		}	
		
		$where = get_naps_where("home");
		$naps = '';
		$nap_items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."naps WHERE $where AND valut_id1 = '$id1' AND valut_id2 = '$id2' ORDER BY site_order1 ASC");
		foreach($nap_items as $nap){
			$output = apply_filters('get_naps_output', 1, $nap, 'home');
			if($output){
				$naps = $nap;
				break;
			}	
		}
		
		if(isset($naps->id)){
			$log['link'] = get_exchange_link($naps->naps_name);
			$log['naps'] = $naps->id;	
			$log['status'] = 'success';
			$log['html'] = get_xtp_temp($naps, $valuts);			
		} else {
			$log['status'] = 'error';
			$log['status_code'] = 1;
			$log['status_text'] = __('Selected direction does not exist','pn');				
		}
	}
	
	echo json_encode($log);
	exit;
}

add_action('myaction_site_table2_change_ps', 'def_myaction_ajax_table2_change_ps');
function def_myaction_ajax_table2_change_ps(){
global $wpdb, $premiumbox;	
	
	$log = array();
	$log['status'] = '';
	$log['response'] = '';
	$log['status_code'] = '0'; 
	$log['status_text']= '';
		
	$premiumbox->up_mode();	
		
	if(get_type_table() == 2){	
		
		$ui = wp_get_current_user();
		$user_id = intval($ui->ID);
		
		$id1 = intval(is_param_post('id1'));
		$id2 = intval(is_param_post('id2'));
		$cur1 = is_site_value(is_param_post('cur1'));
		$cur2 = is_site_value(is_param_post('cur2'));	
		
		$valuts = array(); /* массив валют с данными */
		$valutsn = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."valuts ORDER BY psys_title ASC");
		foreach($valutsn as $valut){
			$valuts[$valut->id] = $valut;
		}	
		
		$where = get_naps_where("home");
		$napobmens = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."naps WHERE $where AND psys_id1 = '$id1' AND psys_id2 = '$id2'");
		
		$default = '';
		$r=0; 
		foreach($napobmens as $napob){ $r++;
			$output = apply_filters('get_naps_output', 1, $napob, 'home');
			if($output){
				$v_id1 = $napob->valut_id1;
				$v_id2 = $napob->valut_id2;
			
				$vt1 = is_site_value($valuts[$v_id1]->vtype_title);
				$vt2 = is_site_value($valuts[$v_id2]->vtype_title);
				
				if($r == 1){
					$default = $napob;
				}
				
				if($cur1 == $vt1 and $cur2 == $vt2){
					$default = $napob;			
				}
			}
		}
		
		if(!is_object($default)){
			$log['status'] = 'error';
			$log['status_code'] = 1;
			$log['status_text'] = __('Selected direction does not exist','pn');	
		} else {
			$log['status'] = 'success';
			$log['link'] = get_exchange_link($default->naps_name);
			$log['naps'] = $default->id;
			$log['html'] = get_xtp_temp($default, $valuts);
		}
	
	}
	
	echo json_encode($log);
	exit;
}

function get_xtp_temp($naps, $v){
global $wpdb;	

	$temp = '';

	if(isset($naps->id)){

		$ui = wp_get_current_user();
		$user_id = intval($ui->ID);

		$ps1 = $naps->psys_id1;
		$ps2 = $naps->psys_id2;
		
		$field1 = $field2 = array();
		
		$where = get_naps_where("home");		
		$ns = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."naps WHERE $where AND psys_id1 = '$ps1' OR $where AND psys_id2 = '$ps2'");	
		foreach($ns as $na){
			$output = apply_filters('get_naps_output', 1, $na, 'home');
			if($output == 1){
				if($na->psys_id1 == $ps1){
					$vd1 = $v[$na->valut_id1];
					$field1[$vd1->vtype_title] = $na->valut_id1;
				}
				if($na->psys_id2 == $ps2){
					$vd2 = $v[$na->valut_id2];
					$field2[$vd2->vtype_title] = $na->valut_id2;
				}
			}
		}
		
		$val1 = $naps->valut_id1;
		$val2 = $naps->valut_id2;
		
		$vd1 = $v[$val1];
		$vd2 = $v[$val2];
		
		$post_sum = is_my_money(is_param_get('get_sum'));
		if($post_sum <= 0){
			$post_sum = is_my_money(get_mycookie('cache_sum1'));
		}
		$cdata = get_calc_data($vd1,$vd2,$naps,$user_id, $post_sum);
		
		$vtype1 = $cdata['vtype1'];
		$vtype2 = $cdata['vtype2'];
		$psys1 = $cdata['psys1'];
		$psys2 = $cdata['psys2'];											
							
		$v_title1 = $psys1.' '.$vtype1;					
		$v_title2 = $psys2.' '.$vtype2;
		
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
				
		$temp .= '
		<input type="hidden" name="" class="js_napr_id" value="'. $naps->id .'" />
		<div class="xtp_col_table_body">
			<div class="xtp_left_col_table">
				<div class="xtp_curs_wrap">
		';
					$temp .= '
					<div class="xtp_input_wrap js_wrap_error js_wrap_error_br '. $summ1_error .'">';
					
						$temp .= apply_filters('exchange_input', '', 'give', $cdata, $vd1, $vd2, $naps, $user_id, $post_sum);
					
					$temp .= '	
						<div class="js_error js_summ1_error">'. $summ1_error_txt .'</div>
					</div>';

					$temp .= '
					<div class="xtp_select_wrap">
						<select name="0" class="js_my_sel js_item_sel js_item_sel1" autocomplete="off">';
							foreach($field1 as $vt => $v_id1){
								$temp .= '
								<option value="'. $v_id1 .'" '. selected($val1,$v_id1,false) .'>'. $vt .'</option>
								';
							}
						$temp .= '
						</select>
					</div>';
					
				$temp .= '	
				</div>';
				
				$temp .= '
				<div class="xtp_commis_wrap js_wrap_error js_wrap_error_br '. $summ1c_error .'" '. $viv_com1 .'>';
				
					$temp .= apply_filters('exchange_input', '', 'give_com', $cdata, $vd1, $vd2, $naps, $user_id, $post_sum);
				
					$temp .= '
					<div class="xtp_commis_text">'. __('With fees','pn') .'</div>
					<div class="js_error js_summ1c_error">'. $summ1c_error_txt .'</div>
					<div class="clear"></div>
				</div>';
				
				$tbl2_leftcol_data = array();
				$tbl2_leftcol_data = apply_filters('tbl2_leftcol_data', $tbl2_leftcol_data, $cdata, $vd1, $vd2, $naps, $user_id, $post_sum);
				foreach($tbl2_leftcol_data as $value){
					$temp .= $value; 
				}				
				
			$temp .= '
			</div>
			<div class="xtp_right_col_table">
				<div class="xtp_curs_wrap">';
				
					$temp .= '
					<div class="xtp_input_wrap js_wrap_error js_wrap_error_br '. $summ2_error .'">';
					
						$temp .= apply_filters('exchange_input', '', 'get', $cdata, $vd1, $vd2, $naps, $user_id, $post_sum);

					$temp .= '
						<div class="js_error js_summ2_error">'. $summ2_error_txt .'</div>
					</div>';
					
					$temp .= '
					<div class="xtp_select_wrap">
						<select name="" class="js_my_sel js_item_sel js_item_sel2" autocomplete="off">';
						
							foreach($field2 as $vt => $v_id2){
								$temp .= '
								<option value="'. $v_id2 .'" '. selected($val2,$v_id2,false) .'>'. $vt .'</option>
								';
							}
							
						$temp .= '										
						</select>
					</div>';
					
				$temp .= '	
				</div>';
				
				$temp .= '
				<div class="xtp_commis_wrap js_wrap_error js_wrap_error_br '. $summ2c_error .'" '. $viv_com2 .'>';

					$temp .= apply_filters('exchange_input', '', 'get_com', $cdata, $vd1, $vd2, $naps, $user_id, $post_sum);
					
					$temp .= '
					<div class="xtp_commis_text">'. __('With fees','pn') .'</div>
					<div class="js_error js_summ2c_error">'. $summ2c_error_txt .'</div>
					<div class="clear"></div>
				</div>';				
				
				$reserv = is_out_sum(get_naps_reserv($vd2->valut_reserv, $vd2->valut_decimal, $naps), $vd2->valut_decimal, 'reserv');				
						
				$tbl2_rightcol_data = array(
					'rate' => '
					<div class="xtp_line xtp_exchange_rate">
						'. __('Exchange rate','pn') .': <span class="js_curs_html">'. apply_filters('show_table_course', $curs1, $cdata['decimal1']) .' '. $vtype1 .' = '. apply_filters('show_table_course', $curs2, $cdata['decimal2']) .' '. $vtype2 .'</span> 
					</div>					
					',
					'zreserv' => '
					<div class="xtp_line xtp_exchange_reserve">
						'. __('Reserve','pn') .': <span class="js_reserv_html">'. $reserv .' '. $cdata['vtype2'] .'</span> 
					</div>					
					',
				);
				$tbl2_rightcol_data = apply_filters('tbl2_rightcol_data', $tbl2_rightcol_data, $cdata, $vd1, $vd2, $naps, $user_id, $post_sum);
				
				foreach($tbl2_rightcol_data as $value){
					$temp .= $value; 
				}				
					
		$temp .= '
			</div>
				<div class="clear"></div>
		</div>';
		
	}
	
	return $temp;
}