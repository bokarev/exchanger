<?php
if( !defined( 'ABSPATH')){ exit(); }

/* добавляем JS */
add_action('siteplace_js','siteplace_js_exchange_table1');
function siteplace_js_exchange_table1(){
	if(get_type_table() == 1){
?>	
/* exchange table */
jQuery(function($){

function go_visible_icon_start(){

	$('.js_icon_left').hide();
	$('.js_icon_left:first').show();
	
	$('.js_item_left').each(function(){
		var vtype = $(this).attr('data-type');
		$('.js_icon_left_' + vtype).show();
	});

	$('.js_icon_right').hide();
	$('.js_icon_right:first').show();
	
	$('.js_line_tab.active .js_item_right').each(function(){
		var vtype = $(this).attr('data-type');
		$('.js_icon_right_' + vtype).show();
	});	
	
	if($('.js_icon_right.active:visible').length == 0){
		$('.js_item_right').show();
		$('.js_icon_right').removeClass('active');
		$('.js_icon_right:first').addClass('active');
	}
	
}

function go_active_left_col(){
	
	if($('.js_item_left:visible.active').length == 0){
		$('.js_item_left').removeClass('active');
		$('.js_item_left:visible:first').addClass('active');
	} 	
	
	var valid = $('.js_item_left.active').attr('data-id');
	$('.js_line_tab').removeClass('active');
	$('#js_tabnaps_'+valid).addClass('active');
	go_visible_icon_start();
	
}

	go_active_left_col();
 
    $(document).on('click',".js_item_left",function () {
        if(!$(this).hasClass('active')){
			
			$(".js_item_left").removeClass('active');
			$(this).addClass('active');

			go_active_left_col();
        }
        return false;
    });	
	
    $(document).on('click',".js_icon_left",function () {
        if(!$(this).hasClass('active')){
		    
			var vtype = $(this).attr('data-type');
			$(".js_icon_left").removeClass('active');
			$(this).addClass('active');
	
			if(vtype == 0){
				$('.js_item_left').show();
			} else {
				$('.js_item_left').hide();
				$('.js_item_left_'+vtype).show();
			}
			
			go_active_left_col();
			
        }
        return false;
    });
	
    $(document).on('click',".js_icon_right",function () {
        if(!$(this).hasClass('active')){
		    
			var vtype = $(this).attr('data-type');
			$(".js_icon_right").removeClass('active');
			$(this).addClass('active');
	
			if(vtype == 0){
				$('.js_item_right').show();
			} else {
				$('.js_item_right').hide();
				$('.js_item_right_'+vtype).show();
			}
			
        }
        return false;
    });

});		
/* end exchange table */	
<?php	
	}
}	
/* end добавляем JS */

add_filter('exchange_table_type1','get_exchange_table1', 10, 3);
function get_exchange_table1($temp, $def_cur_from='', $def_cur_to=''){
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
	
	$all_vtypes = array(); /* собираем все возможные типы валют */
	$valuts = array(); /* массив данных валют */
	$valutsn = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."valuts ORDER BY psys_title ASC");
	foreach($valutsn as $valut){
		$valuts[$valut->id] = $valut;
		$all_vtypes[] = is_site_value($valut->vtype_title);
	}	
	$all_vtypes = array_unique($all_vtypes);	

	$where = get_naps_where('home');
	$naps = array();
	$napobmens = $wpdb->get_results("SELECT *, ".$wpdb->prefix."naps.id AS naps_id FROM ".$wpdb->prefix."naps WHERE $where ORDER BY to1 ASC");
	foreach($napobmens as $napob){
		$output = apply_filters('get_naps_output', 1, $napob, 'home');
		if($output == 1){
			$naps[$napob->valut_id1] = $napob;
		}
	}

	$napobmens2 = $wpdb->get_results("SELECT *, ".$wpdb->prefix."naps_order.id AS item_id FROM ".$wpdb->prefix."naps LEFT OUTER JOIN ".$wpdb->prefix."naps_order ON(".$wpdb->prefix."naps.id = ".$wpdb->prefix."naps_order.naps_id AND ".$wpdb->prefix."naps.valut_id1 = ".$wpdb->prefix."naps_order.v_id) WHERE $where ORDER BY ".$wpdb->prefix."naps_order.order1 ASC");
	$naps2 = array();
	foreach($napobmens2 as $napob){
		$output = apply_filters('get_naps_output', 1, $napob, 'home');
		if($output == 1){
			$naps2[$napob->valut_id1][] = $napob;
		}
	}	
	
	$temp .= '
	<div class="xchange_type_table">
		<div class="xchange_type_table_ins">';
					
			$exchange_table1_head = '	
			<div class="xtt_icon_wrap">
				<div class="xtt_left_col_icon">
				
					<div class="xtt_icon active js_icon_left" data-type="0"><div class="xtt_icon_ins"><div class="xtt_icon_abs"></div>'. __('All','pn') .'</div></div>
					';
				
						foreach($all_vtypes as $av){
							$exchange_table1_head .= '<div class="xtt_icon js_icon_left js_icon_left_'. $av .'" style="display: none;" data-type="'. $av .'"><div class="xtt_icon_ins"><div class="xtt_icon_abs"></div>'. $av .'</div></div>';
						}
				
				$exchange_table1_head .= '
					<div class="clear"></div>
				</div>
				<div class="xtt_right_col_icon">

					<div class="xtt_icon active js_icon_right" data-type="0"><div class="xtt_icon_ins"><div class="xtt_icon_abs"></div>'. __('All','pn') .'</div></div>
					';
							
						foreach($all_vtypes as $av){
							$exchange_table1_head .= '<div class="xtt_icon js_icon_right js_icon_right_'. $av .'" style="display: none;" data-type="'. $av .'"><div class="xtt_icon_ins"><div class="xtt_icon_abs"></div>'. $av .'</div></div>';
						}							
							
				$exchange_table1_head .= '
					<div class="clear"></div>
				</div>
					<div class="clear"></div>
			</div>';	
				
			$temp .= apply_filters('exchange_table1_head',$exchange_table1_head, $all_vtypes);

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
						<div class="xtt_table_title3">
							<span>'. __('Reserve','pn') .'</span>
						</div>
							<div class="clear"></div>
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
												</div>',
												'title' => '
												<div class="xtt_one_line_name_left">
													<div class="xtt_one_line_name">
														'. get_valut_title($vd1) .'
													</div>
												</div>
												',
												'course' => '
												<div class="xtt_one_line_curs_left">
													<div class="xtt_one_line_curs">
														'. $curs1 .'
													</div>	
												</div>												
												',
											);
											$tbl1_leftcol_data = apply_filters('tbl1_leftcol_data',$tbl1_leftcol_data, $naps_data, $vd1, '', $curs1, $cur_from);
											
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
						';
								
							if(is_array($naps)){	
								foreach($naps as $naps_data){
									
									$valsid1 = $naps_data->valut_id1;
									if(isset($valuts[$valsid1])){
										
										$vd1 = is_isset($valuts,$valsid1);
										
										$temp .= '
										<!-- tab currency -->
										<div class="xtt_line_tab js_line_tab" id="js_tabnaps_'. $valsid1 .'">';										
											
										if(isset($naps2[$valsid1])){
											$naps_otd = $naps2[$valsid1];
											foreach($naps_otd as $naps_data2){
													
												$valsid2 = $naps_data2->valut_id2;
												if(isset($valuts[$valsid2])){
													
													$vd2 = is_isset($valuts,$valsid2);
														
													$v_title1 = get_valut_title($vd1);		
													$v_title2 = get_valut_title($vd2);
													 
													$curs2 = is_out_sum(get_course2($vd1->lead_num, $naps_data2->curs1, $naps_data2->curs2, $vd2->valut_decimal, 'table1'), $vd2->valut_decimal,'course');
														
													$temp .= '
													<!-- one item -->
													<a href="'. get_exchange_link($naps_data2->naps_name) .'" class="js_exchange_link js_item_right js_item_right_'. is_site_value($vd2->vtype_title) .'" data-type="'. is_site_value($vd2->vtype_title) .'" data-naps="'. $naps_data2->naps_id .'">
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
															'course' => '
															<div class="xtt_one_line_curs_right">
																<div class="xtt_one_line_curs">
																	'. $curs2 .'
																</div>	
															</div>															
															',
															'reserve' => '
															<div class="xtt_one_line_reserv_right">
																<div class="xtt_one_line_reserv">
																	'. is_out_sum(get_naps_reserv($vd2->valut_reserv , $vd2->valut_decimal, $naps_data2), $vd2->valut_decimal, 'reserv').'
																</div>	
															</div>															
															',
														);	
														$tbl1_rightcol_data = apply_filters('tbl1_rightcol_data',$tbl1_rightcol_data, $naps_data2, $vd1, $vd2, $curs2, $cur_to);
														
														foreach($tbl1_rightcol_data as $value){
															$temp .= $value; 
														}		
																
													$temp .= '		
															<div class="clear"></div>
														</div>	
													</a>
													<!-- end one item -->											
													';
													
												}	
											}
										}
											
										$temp .= '
										</div>
										<!-- end tab currency -->										
										';
								
									}		
								}
							}							

						$temp .= '
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