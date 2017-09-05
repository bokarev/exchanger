<?php
if( !defined( 'ABSPATH')){ exit(); }

/* добавляем JS */
add_action('siteplace_js','siteplace_js_mobile_exchange_table2');
function siteplace_js_mobile_exchange_table2(){
	if(get_mobile_type_table() == 2){
?>	
/* exchange table */
jQuery(function($){
	
function go_active_left_col(){
	
	if($('.js_item_left.active').length == 0){
		$('.js_item_left').removeClass('active');
		$('.js_item_left:first').addClass('active');
	} 	
	
	var valid = $('.js_item_left.active').attr('data-id');		
	
	$('.xtt_html_abs').show();
	var dataString='id=' + valid;
    $.ajax({
        type: "POST",
        url: "<?php echo get_ajax_link('mobile_table2_change');?>",
        dataType: 'json',
		data: dataString,
 		error: function(res, res2, res3){
			<?php do_action('pn_js_error_response', 'ajax'); ?>
		},       
		success: function(res)
        {
			$('.xtt_html_abs').hide();
			if(res['status'] == 'success'){
				$('#xtt_right_col_html').html(res['html']);
			} 	
        }
    });
	
}

	go_active_left_col();
	$('.xtt_html_abs').show();
 
    $(document).on('click',".js_item_left",function () {
        if(!$(this).hasClass('active')){
		    
			$(".js_item_left").removeClass('active');
			$(this).addClass('active');

			go_active_left_col();
        }
        return false;
    });	
	
});		
/* end exchange table */	
<?php	
	}
}	
/* end добавляем JS */

add_action('myaction_site_mobile_table2_change', 'def_myaction_ajax_mobile_table2_change');
function def_myaction_ajax_mobile_table2_change(){
global $wpdb, $premiumbox;	
	
	$log = array();
	$log['status'] = '';
	$log['response'] = '';
	$log['status_code'] = '0'; 
	$log['status_text']= '';
	
	$premiumbox->up_mode();
	
	if(get_mobile_type_table() == 2){	
	
		$ui = wp_get_current_user();
		$user_id = intval($ui->ID);
		
		$id = intval(is_param_post('id'));
		if($id > 0){
			$valuts = array(); /* массив валют с данными */
			$valutsn = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."valuts ORDER BY psys_title ASC");
			foreach($valutsn as $valut){
				$valuts[$valut->id] = $valut;
			}	
			
			$where = get_naps_where('home');
			$html = '';
			$napobmens = $wpdb->get_results("SELECT *, ".$wpdb->prefix."naps_order.id AS item_id FROM ".$wpdb->prefix."naps LEFT OUTER JOIN ".$wpdb->prefix."naps_order ON(".$wpdb->prefix."naps.id = ".$wpdb->prefix."naps_order.naps_id AND ".$wpdb->prefix."naps.valut_id1 = ".$wpdb->prefix."naps_order.v_id) WHERE $where AND ".$wpdb->prefix."naps.valut_id1 = '$id' ORDER BY ".$wpdb->prefix."naps_order.order1 ASC");
			foreach($napobmens as $naps_data){
				$output = apply_filters('get_naps_output', 1, $naps_data, 'home');
				if($output){
					$valsid1 = $naps_data->valut_id1;
					$valsid2 = $naps_data->valut_id2;
					if(isset($valuts[$valsid1]) and isset($valuts[$valsid2])){					
						$vd1 = is_isset($valuts,$valsid1);
						$vd2 = is_isset($valuts,$valsid2);
															
						$v_title1 = get_valut_title($vd1);		
						$v_title2 = get_valut_title($vd2);
														
						$curs2 = is_out_sum(get_course2($vd1->lead_num, $naps_data->curs1, $naps_data->curs2, $vd2->valut_decimal, 'table1'), $vd2->valut_decimal, 'course');
															
						$html .= '
						<!-- one item -->
						<a href="'. get_exchange_link($naps_data->naps_name) .'" class="js_exchange_link js_item_right js_item_right_'. is_site_value($vd2->vtype_title) .'" data-type="'. is_site_value($vd2->vtype_title) .'" data-naps="'. $naps_data->naps_id .'">
							<div class="xtt_one_line_right">
						';	 
							
							$tbl1_rightcol_data = array(
								'line_abs1' => '<div class="xtt_one_line_abs"></div>',
								'line_abs2' => '<div class="xtt_one_line_abs2"></div>',
								'icon' => '
								<div class="xtt_one_line_ico_right"> 
									<div class="xtt_change_ico" style="background: url('. get_valut_logo($vd2) .') no-repeat center center;"></div>
								</div>															
								',
								'title' =>'
								<div class="xtt_one_line_name_right">
									<div class="xtt_one_line_name">
										'. $v_title2 .'
									</div>
								</div>														
								',
							);	
							$tbl1_rightcol_data = apply_filters('mobile_tbl1_rightcol_data',$tbl1_rightcol_data, $naps_data, $vd1, $vd2, $curs2, '');
														
							foreach($tbl1_rightcol_data as $value){
								$html .= $value; 
							}
																	
						$html .= '
								<div class="clear"></div>
							</div>						
						</a>
						<!-- end one item -->											
						';							
									
					}	
				}
			}							

			$log['status'] = 'success';
			$log['html'] = $html;			
		}
		
	}
	
	echo json_encode($log);
	exit;
}

add_filter('exchange_mobile_table_type2','get_exchange_mobile_table_type2', 10, 3);
function get_exchange_mobile_table_type2($temp, $def_cur_from='', $def_cur_to=''){
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
	
	$valuts = array(); /* массив данных валют */
	$valutsn = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."valuts ORDER BY psys_title ASC");
	foreach($valutsn as $valut){
		$valuts[$valut->id] = $valut;
	}		

	$where = get_naps_where('home');
	$naps = array();
	$napobmens = $wpdb->get_results("SELECT *, ".$wpdb->prefix."naps.id AS naps_id FROM ".$wpdb->prefix."naps WHERE $where ORDER BY to1 ASC");
	foreach($napobmens as $napob){
		$output = apply_filters('get_naps_output', 1, $napob, 'home');
		if($output){
			$naps[$napob->valut_id1] = $napob;
		}
	}	
	
		$temp .= '
		<div class="xchange_type_table">
			<div class="xchange_type_table_ins">';
					
				$temp .= '
				<div class="xtt_table_wrap">';
					
					$exchange_table1_headname = '
					<div class="xtt_table_title_wrap">
						<div class="xtt_left_col_title">
							<div class="xtt_table_title1">
								<span>'. __('You send','pn') .'</span>
							</div>
						</div>
						<div class="xtt_right_col_title">
							<div class="xtt_table_title2">
								<span>'. __('You receive','pn') .'</span>
							</div>
						</div>
							<div class="clear"></div>
					</div>';
					
					$temp .= apply_filters('exchange_table1_headname',$exchange_table1_headname);
					
					$temp .= '
					<div class="xtt_table_body_wrap">
						<div class="xtt_html_abs"></div>';
						
						$temp .= '
						<div class="xtt_left_col_table">';
									
								if(is_array($naps)){		
									foreach($naps as $naps_data){ 
											
										$valsid1 = $naps_data->valut_id1;
										if(isset($valuts[$valsid1])){
											
											$vd1 = $valuts[$valsid1];
											
											$cl = '';
											if($cur_from){
												if($cur_from == $vd1->xml_value){
													$cl = 'active';
												}
											}
											
											$curs1 = is_out_sum(get_course1($naps_data->curs1, $vd1->lead_num, $vd1->valut_decimal, 'table1'), $vd1->valut_decimal, 'course');		
											$vtype1 = is_site_value($vd1->vtype_title);
												
											$temp .= '
											<!-- one item -->
											<div class="js_item_left js_item_left_'. $vtype1 .'  '. $cl .'" data-id="'. $valsid1 .'" data-type="'. $vtype1 .'">
												<div class="xtt_one_line_left">
											';
												
												$tbl1_leftcol_data = array(
													'line_abs1' => '<div class="xtt_one_line_abs"></div>',
													'line_abs2' => '<div class="xtt_one_line_abs2"></div>',
													'icon' => '
													<div class="xtt_one_line_ico_left"> 
														<div class="xtt_change_ico" style="background: url('. get_valut_logo($vd1) .') no-repeat center center;"></div>
													</div>
													',
													'title' => '
													<div class="xtt_one_line_name_left">
														<div class="xtt_one_line_name">
															'. get_valut_title($vd1) .'
														</div>
													</div>
													',
												);
												$tbl1_leftcol_data = apply_filters('mobile_tbl1_leftcol_data',$tbl1_leftcol_data, $naps_data, $vd1, '', $curs1, $cur_from);
												
												foreach($tbl1_leftcol_data as $value){
													$temp .= $value; 
												}												

											$temp .= '
													<div class="clear"></div>
												</div>	
											</div>
											<!-- end one item -->
											';
											
										}	
									}
								}
									
							$temp .= '
						</div>		
						<div class="xtt_right_col_table">
							<div id="xtt_right_col_html">
							</div>
						</div>';
						
					$temp .= '	
							<div class="clear"></div>
					</div>';
					
				$temp .= '	
						<div class="clear"></div>
				</div>';
				
		$temp .='		
			</div>
		</div>';	
	
	return $temp;
}