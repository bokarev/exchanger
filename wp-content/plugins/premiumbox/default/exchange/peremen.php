<?php
if( !defined( 'ABSPATH')){ exit(); }
	
add_action('siteplace_js','siteplace_js_exchange_changes');
function siteplace_js_exchange_changes(){
?>	
jQuery(function($){
	
	function checknumbr(mixed_var) {
		return ( mixed_var == '' ) ? false : !isNaN( mixed_var );
	}		
	$(document).on('click', 'input', function(){
		$(this).parents('.js_wrap_error').removeClass('error');
	});		 
	
	function goed_peremen(sum, dej){
		var id = $('.js_napr_id:first').val();
		
		var check1 = 0;
		if($('input[name=check_purse1]').length > 0){
			if($('input[name=check_purse1]').prop('checked')){
				var check1 = 1;
			}
		}
		var check2 = 0;
		if($('input[name=check_purse2]').length > 0){
			if($('input[name=check_purse2]').prop('checked')){
				var check2 = 1;
			}
		}	
		
		var dataString = 'id='+id+'&sum='+sum+'&dej='+dej+'&check1='+check1+'&check2='+check2;
		
		$('.exch_ajax_wrap_abs, .hexch_ajax_wrap_abs, .js_loader').show();
		
        $.ajax({
            type: "POST",
            url: "<?php echo get_ajax_link('exchange_changes');?>",
            data: dataString,
	        dataType: 'json',
 			error: function(res, res2, res3){
				<?php do_action('pn_js_error_response', 'ajax'); ?>
			},           
			success: function(res){ 
				
				if(dej !== 1){
					$('.js_summ1').val(res['summ1']);
				}
				if(dej !== 2){
					$('.js_summ2').val(res['summ2']);
				}
				if(dej !== 3){
					$('.js_summ1c').val(res['summ1c']);
				}
				if(dej !== 4){
					$('.js_summ2c').val(res['summ2c']);
				}
				
				$('.js_comis_text1').html(res['comis_text1']);
				$('.js_comis_text2').html(res['comis_text2']);
				
				if(res['summ1_error'] == 1){
					$('.js_summ1').parents('.js_wrap_error').addClass('error');
					$('.js_summ1_error').html(res['summ1_error_text']);
				} else {
					$('.js_summ1').parents('.js_wrap_error').removeClass('error');					
				}
				if(res['summ2_error'] == 1){
					$('.js_summ2').parents('.js_wrap_error').addClass('error');
					$('.js_summ2_error').html(res['summ2_error_text']);
				} else {
					$('.js_summ2').parents('.js_wrap_error').removeClass('error');
				}
				if(res['summ1c_error'] == 1){
					$('.js_summ1c').parents('.js_wrap_error').addClass('error');
					$('.js_summ1c_error').html(res['summ1c_error_text']);
				} else {
					$('.js_summ1c').parents('.js_wrap_error').removeClass('error');
				}
				if(res['summ2c_error'] == 1){
					$('.js_summ2c').parents('.js_wrap_error').addClass('error');
					$('.js_summ2c_error').html(res['summ2c_error_text']);
				} else {
					$('.js_summ2c').parents('.js_wrap_error').removeClass('error');
				}
				
				if(res['curs_html']){
					$('.js_curs_html').html(res['curs_html']);
				}
				if(res['reserv_html']){
					$('.js_reserv_html').html(res['reserv_html']);
				}				
				
				$('.exch_ajax_wrap_abs, .hexch_ajax_wrap_abs, .js_loader').hide();
				
            }
		});					    
	}
	
		var gp = 0;
		var go_int = 0;
		var field_id = '';
		var old_field_id = '';
		var now_sum = 0;
		var old_sum = 0;
		var up_form = 0;
		var start_ex_timer = 1;
		
		function clear_ind(){
			gp=0;
		}

		function start_exchange_timer(){
			if(start_ex_timer == 1){
				start_ex_timer = 0;
				
				setInterval(function(){
					if(go_int == 1 && gp == 0){
						go_int = 0;
						if(now_sum !== old_sum || field_id != old_field_id || up_form == 1){ 
							old_sum = now_sum;
							old_field_id = field_id;
							goed_peremen(now_sum, field_id);
						}
					}		
				}, 500);
			}
		}

		function go_calc(obj, f_id, req){
			
			var vale = obj.val().replace(/,/g,'.');
			if (checknumbr(vale)) {
				
				obj.parents('.js_wrap_error').removeClass('error');
				
				if(f_id == 1){
					$('.js_summ1:not(:focus)').val(vale);
				} else if(f_id == 2){
					$('.js_summ2:not(:focus)').val(vale);
				} else if(f_id == 3){
					$('.js_summ1c:not(:focus)').val(vale);
				} else if(f_id == 4){
					$('.js_summ2c:not(:focus)').val(vale);
				}
				
				now_sum = vale;
				up_form = req;
				go_int = 1;
				field_id = f_id;
				gp = 1;
				setTimeout(clear_ind, 1000);
				
			} else {
				obj.parents('.js_wrap_error').addClass('error');
			}	

			start_exchange_timer();
			
		}

		$(document).on('keyup', '.js_summ1', function(){
			var thet = $(this);
			go_calc(thet,1,0);
		});
		$(document).on('change', '.js_summ1', function(){
			var thet = $(this);
			go_calc(thet,1,0);
		});

		$(document).on('keyup', '.js_summ2', function(){
			var thet = $(this);
			go_calc(thet,2,0);
		});
		$(document).on('change', '.js_summ2', function(){
			var thet = $(this);
			go_calc(thet,2,0);
		});

		$(document).on('keyup', '.js_summ1c', function(){
			var thet = $(this);
			go_calc(thet,3,0);
		});
		$(document).on('change', '.js_summ1c', function(){
			var thet = $(this);
			go_calc(thet,3,0);
		});

		$(document).on('keyup', '.js_summ2c', function(){
			var thet = $(this);
			go_calc(thet,4,0);
		});
		$(document).on('change', '.js_summ2c', function(){
			var thet = $(this);
			go_calc(thet,4,0);
		});	

		$(document).on('click','.js_check_purse',function(){
			var thet = $('.js_summ1');
			go_calc(thet,1,1);
		});			
	
});
<?php
} 	

/* изменения live */
add_action('myaction_site_exchange_changes', 'def_myaction_ajax_exchange_changes');
function def_myaction_ajax_exchange_changes(){
global $wpdb, $premiumbox;	
	
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);
	
	$log = array();
	$log['status'] = '';
	$log['response'] = '';
	$log['status_code'] = 0; 
	$log['status_text'] = __('Error','pn');		

	$premiumbox->up_mode();
	
	$comis_text1 = '';
	$comis_text2 = '';
	$summ1_error = $summ2_error = $summ1c_error = $summ2c_error = 0;
	$summ1_error_text = $summ2_error_text = $summ1c_error_text = $summ2c_error_text = '';
	
	$summ1 = 0;
	$summ1c = 0;
	$summ2 = 0;
	$summ2c = 0;
	$curs_html = '';
	$reserv_html = '';
	
	$naps_id = intval(is_param_post('id'));
	$sum = is_my_money(is_param_post('sum'));
	$dej = intval(is_param_post('dej'));
	$check1 = intval(is_param_post('check1'));
	$check2 = intval(is_param_post('check2'));
	$show_data = pn_exchanges_output('exchange');
	if($show_data['mode'] == 1){
		 
		if($dej > 0 or $dej < 5){ 
			$where = get_naps_where('exchange');
			$naps = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."naps WHERE $where AND id='$naps_id'");
			if(isset($naps->id)){
				$output = apply_filters('get_naps_output', 1, $naps, 'exchange');
				if($output){
					$valut1 = $naps->valut_id1;
					$valut2 = $naps->valut_id2;
					$vd1 = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."valuts WHERE id='$valut1'");
					$vd2 = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."valuts WHERE id='$valut2'");
					if(isset($vd1->id) and isset($vd2->id)){
						if($sum > 0){
							
							$cdata = get_calc_data($vd1, $vd2, $naps, $user_id, $sum, $check1, $check2, $dej);
							
							$curs1 = $cdata['curs1'];
							$curs2 = $cdata['curs2'];
							$decimal1 = $cdata['decimal1'];
							$decimal2 = $cdata['decimal2'];
							$vtype1 = $cdata['vtype1'];
							$vtype2 = $cdata['vtype2'];
							$summ1 = $cdata['summ1'];
							$summ1c = $cdata['summ1c'];
							$summ2 = $cdata['summ2'];
							$summ2c = $cdata['summ2c'];
							$comis_text1 = $cdata['comis_text1'];
							$comis_text2 = $cdata['comis_text2'];
											
							if($premiumbox->get_option('exchange','flysum') == 1){
								
								$min1 = get_min_sum_to_naps_give($naps, $vd1);
								$max1 = get_max_sum_to_naps_give($naps, $vd1);
								/* if($min1 > $max1 and is_numeric($max1)){ $min1 = $max1; } */

								$min2 = get_min_sum_to_naps_get($naps, $vd2); 
								$max2 = get_max_sum_to_naps_get($naps, $vd2);
								/* if($min2 > $max2 and is_numeric($max2)){ $min2 = $max2; } */							
									
								if($summ1 < $min1){
									$summ1_error = 1;
									$summ1_error_text = __('min','pn').'.: '. $min1 .' '.$vtype1;													
								}
									
								if($summ1 > $max1 and is_numeric($max1)){
									$summ1_error = 1;
									$summ1_error_text = __('max','pn').'.: '. $max1 .' '.$vtype1;													
								}
									
								if($summ2 < $min2){
									$summ2_error = 1;
									$summ2_error_text = __('min','pn').'.: '. $min2 .' '.$vtype2;													
								}
									
								if($summ2 > $max2 and is_numeric($max2)){
									$summ2_error = 1;
									$summ2_error_text = __('max','pn').'.: '. $max2 .' '.$vtype2;													
								}								
								
							}

							$reserv = is_out_sum(get_naps_reserv($vd2->valut_reserv, $vd2->valut_decimal, $naps), $vd2->valut_decimal, 'reserv');
							$reserv_html = $reserv .' '. $cdata['vtype2'];
						
							$curs_html = apply_filters('show_table_course',$curs1,$decimal1).' '. $vtype1 .' = '. apply_filters('show_table_course',$curs2,$decimal2) .' '. $vtype2;									
						}
						
							if($summ1 <= 0){
								$summ1_error = 1;
							}							
							if($summ2 <= 0){
								$summ2_error = 1;
							}						
							if($summ1c <= 0){
								$summ1c_error = 1;
							}							
							if($summ2c <= 0){
								$summ2c_error = 1;
							}					
						
					}
				}
			}
		}
	}
	
	$log['summ1'] = $summ1;
	$log['summ2'] = $summ2;
	$log['summ1c'] = $summ1c;
	$log['summ2c'] = $summ2c;
	$log['curs_html'] = $curs_html;
	$log['reserv_html'] = $reserv_html;
	$log['comis_text1'] = $comis_text1;
	$log['comis_text2'] = $comis_text2;
	$log['summ1_error'] = $summ1_error;
	$log['summ1_error_text'] = $summ1_error_text;
	$log['summ2_error'] = $summ2_error;
	$log['summ2_error_text'] = $summ2_error_text;
	$log['summ1c_error'] = $summ1c_error;
	$log['summ1c_error_text'] = $summ1c_error_text;
	$log['summ2c_error'] = $summ2c_error;
	$log['summ2c_error_text'] = $summ2c_error_text;
	
	echo json_encode($log);
	exit;
}