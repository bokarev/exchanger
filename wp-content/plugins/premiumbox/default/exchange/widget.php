<?php
if( !defined( 'ABSPATH')){ exit(); }

/* добавляем JS */
add_action('siteplace_js','siteplace_js_exchange_widget');
function siteplace_js_exchange_widget(){
global $premiumbox;	
	$exch_method = intval($premiumbox->get_option('exchange','exch_method'));
	if($exch_method == 1){
?>	
jQuery(function($){ 
	if($('#hexch_html').length > 0){
		$(document).on('click', '.js_exchange_link', function(){
			if(!$(this).hasClass('active')){
				
				var naps_id = $(this).attr('data-naps'); 
				
				$('.hexch_ajax_wrap_abs').show();
				
				var tscroll = $('#hexch_html').offset().top - 100;
				$('body,html').animate({scrollTop : tscroll}, 500);
				
				var ds = 'naps_id=' + naps_id;
				$.ajax({
					type: "POST",
					url: "<?php echo get_ajax_link('exchange_widget');?>",
					dataType: 'json',
					data: ds,
					error: function(res, res2, res3){
						<?php do_action('pn_js_error_response', 'ajax'); ?>
					},					
					success: function(res)
					{
						
						if(res['html']){
							$('#hexch_html').html(res['html']);
						}
						if(res['status'] == 'error'){
							$('#hexch_html').html('<div class="resultfalse"><div class="resultclose"></div>'+res['status_text']+'</div>');
						}
						
						<?php do_action('live_change_html'); ?>
						$('.hexch_ajax_wrap_abs').hide();						
					
					}
				});	

			}
			
			return false;
		});
		
	}	
});
<?php	
	}
} 
/* end добавляем JS */

function the_exchange_widget(){
global $premiumbox;		
	$exch_method = intval($premiumbox->get_option('exchange','exch_method'));
	if($exch_method == 1){	
?>
<form method="post" class="ajax_post_bids" action="<?php echo get_ajax_link('bidsform'); ?>">
	<div class="hexch_ajax_wrap">
		<div class="hexch_ajax_wrap_abs"></div>
		<div id="hexch_html"><?php //echo get_exchange_widget(28); ?></div>
	</div>
</form>
<?php
	}
}

add_action('myaction_site_exchange_widget', 'def_myaction_ajax_exchange_widget');
function def_myaction_ajax_exchange_widget(){
global $premiumbox;
	
	$log = array();
	$log['status'] = '';
	$log['response'] = '';
	$log['status_code'] = 0; 
	$log['status_text'] = __('Error','pn');	
	
	$premiumbox->up_mode();
	
	$naps_id = is_param_post('naps_id');

	$exch_method = intval($premiumbox->get_option('exchange','exch_method'));
	if($exch_method == 1){
		$log['status'] = 'success';
		$log['html'] = get_exchange_widget($naps_id);
	} else {
		$log['status'] = 'error';
		$log['status_code'] = 1; 		
	}
	
	echo json_encode($log);
	exit;
} 

function get_exchange_widget($id){
global $wpdb, $naps_data, $naps_id, $premiumbox;	
	
	$temp =' ';
		
	$id = intval($id);	
		
	$naps_id = 0;
	$naps_data = array();

	$where = get_naps_where('home');
	$naps = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."naps WHERE $where AND id='$id'");
	if(isset($naps->id)){
		$output = apply_filters('get_naps_output', 1, $naps, 'home');
		if($output){
		
			$valut_id1 = intval($naps->valut_id1);
			$valut_id2 = intval($naps->valut_id2);
				
			$vd1 = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."valuts WHERE valut_status='1' AND id='$valut_id1'");
			$vd2 = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."valuts WHERE valut_status='1' AND id='$valut_id2'");
			if(isset($vd1->id) and isset($vd2->id)){
					
				$naps_id = intval($naps->id);
					
				$naps_data['item1'] = get_valut_title($vd1);
				$naps_data['item2'] = get_valut_title($vd2);
				$naps_data['valut1'] = $vd1->id;
				$naps_data['valut2'] = $vd2->id;
				$naps_data['vd1'] = $vd1;
				$naps_data['vd2'] = $vd2;
				$naps_data['direction'] = $naps;
				if(!is_object($naps_data)){
					$naps_data = (object)$naps_data;
				}		
		
				$ui = wp_get_current_user();
				$user_id = intval($ui->ID);
		
				$show_data = pn_exchanges_output('home');
					
				if($show_data['text']){
					$temp .= '<div class="exch_error"><div class="exch_error_ins">'. $show_data['text'] .'</div></div>';
				}
				if($show_data['mode'] == 1){
					
					$naps_id = intval($naps_id);
					$vd1 = $naps_data->vd1;
					$vd2 = $naps_data->vd2;
					$naps = $naps_data->direction;
					
					$temp .= apply_filters('before_exchange_widget','');
					$temp .= '<input type="hidden" name="naps_id" class="js_napr_id" value="'. $naps_id .'" />';
					
					/* message */
					$text = trim(get_naps_txtmeta($naps_id, 'timeline_txt'));
					$naps_nodescr = intval($premiumbox->get_option('naps_nodescr', 'timeline_txt'));
					if($naps_nodescr == 1 and !$text or $naps_nodescr == 2){
						$text = trim($premiumbox->get_option('naps_temp', 'timeline_txt'));
					}
					$text = apply_filters('naps_instruction', $text, $naps, $vd1, $vd2);
					$text = pn_strip_text(ctv_ml($text));					
					$message = '';
					if($text){	
						$message = '
						<div class="hexch_message_wrap">
							<div class="hexch_message">
								<div class="hexch_message_ins">
									<div class="hexch_message_abs"></div>
									<div class="hexch_message_close"></div>
									<div class="hexch_message_title">
										<div class="hexch_message_title_ins">
											<span>'. __('Attention!','pn') .'</span>
										</div>
									</div>
									<div class="hexch_message_text">
										<div class="hexch_message_text_ins">
											'. apply_filters('comment_text', $text) .'
										</div>
									</div>
								</div>
							</div>
						</div>';
					}
					/* end message */
			
					/* description */
					$text = trim(get_naps_txtmeta($naps_id, 'description_txt'));
					$naps_nodescr = intval($premiumbox->get_option('naps_nodescr', 'description_txt'));
					if($naps_nodescr == 1 and !$text or $naps_nodescr == 2){
						$text = trim($premiumbox->get_option('naps_temp', 'description_txt'));
					}		
					$text = apply_filters('naps_instruction', $text, $naps, $vd1, $vd2);
					$text = pn_strip_text(ctv_ml($text));		
					$description = '';	
					if($text){			
						$title = get_exchange_title();								
						$description = '
						<div class="warning_message" itemscope itemtype="http://schema.org/Article">
							<div class="warning_message_ins">
								<div class="warning_message_abs"></div>
								<div class="warning_message_close"></div>
								<div class="warning_message_title">
									<div class="warning_message_title_ins" itemprop="name">
										<span>'. $title .'</span>
									</div>
								</div>
								<div class="warning_message_text">
									<div class="warning_message_text_ins" itemprop="articleBody">
										'. apply_filters('comment_text',$text) .'
									</div>
								</div>
							</div>
						</div>';			
					}					
					/* end description */			
				
					/* check */	
					$check_data = intval(get_mycookie('check_data'));
					$cl_ch = '';
					$ch_ch = '';
					if($check_data == 1){
						$cl_ch = 'act';
						$ch_ch = 'checked="checked"';				
					}
						
					$check ='
					<div class="hexch_checkdata_div">
						<div class="checkbox '. $cl_ch .'"><input type="checkbox" id="check_data" name="check_data" '. $ch_ch .' value="1" /> '. __('Remember entered data','pn') .'</div>
					</div>
					';
					/* end check */				
				
					/* submit */	
					$submit = '
					<div class="hexch_submit_div">
						<input type="submit" formtarget="_top" class="hexch_submit" value="'. __('Exchange','pn') .'">
							<div class="clear"></div>
					</div>';					
					/* end submit */
		
					$post_sum = is_my_money(get_mycookie('cache_sum1'));			
									
					$cdata = get_calc_data($vd1,$vd2,$naps,$user_id, $post_sum);

					$vtype1 = $cdata['vtype1'];
					$vtype2 = $cdata['vtype2'];
					$psys1 = $cdata['psys1'];
					$psys2 = $cdata['psys2'];						
																											
					$curs1 = $cdata['curs1'];
					$curs2 = $cdata['curs2'];
										
					$user_discount = $cdata['user_discount'];
					$us = '';
					if($user_discount > 0){
						$us = '<p><span class="span_skidka">'. __('Your discount','pn') .': '. $user_discount .'%</span></p>';
					}											
										
					$comis_text1 = $cdata['comis_text1'];
					$comis_text2 = $cdata['comis_text2'];
									
					$summ1_error = $summ2_error = $summ1c_error = $summ2c_error = '';
					$summ1_error_txt = $summ2_error_txt = $summ1c_error_txt = $summ2c_error_txt = '';
																			
					$viv_com1 = 'style="display: none;"'; /* не выводим поле доп.комиссии */
					if($cdata['viv_com1'] == 1){
						$viv_com1 = '';
					}
													
					$viv_com2 = 'style="display: none;"'; /* не выводим поле доп.комиссии */
					if($cdata['viv_com2'] == 1){
						$viv_com2 = '';
					}	

					$summ1 = $cdata['summ1'];
					$summ1c = $cdata['summ1c'];
					$summ2 = $cdata['summ2'];
					$summ2c = $cdata['summ2c'];
											
					$min1 = get_min_sum_to_naps_give($naps, $vd1);
					$max1 = get_max_sum_to_naps_give($naps, $vd1);
					/* if($min1 > $max1 and is_numeric($max1)){ $min1 = $max1; } */								
											
					$vz1 = array();
					if($min1 > 0){
						$vz1[] = __('min','pn').'.: '. $min1 .' '.$vtype1;
					}
					if(is_numeric($max1)){
						$vz1[] = __('max','pn').'.: '. $max1 .' '.$vtype1;
					}
					$zvt1 = '';
					if(count($vz1) > 0){
						$zvt1 = '<p class="span_give_max">'. join(', ',$vz1) .'</p>';
					}
											
					$min2 = get_min_sum_to_naps_get($naps, $vd2); 
					$max2 = get_max_sum_to_naps_get($naps, $vd2);
					/* if($min2 > $max2 and is_numeric($max2)){ $min2 = $max2; } */
																	
					$vz2 = array();	
					if($min2 > 0){
						$vz2[] = __('min','pn').'.: '. $min2 .' '.$vtype2;
					}
					if(is_numeric($max2)){
						$vz2[] = __('max','pn').'.: '. $max2 .' '.$vtype2;
					}
					$zvt2 = '';
					if(count($vz2) > 0){
						$zvt2 = '<p class="span_get_max">'. join(', ',$vz2) .'</p>';
					}											
											
					if($summ1 < $min1){
						$summ1_error = 'error';
						$summ1_error_txt = __('min','pn').'.: '. $min1 .' '.$vtype1;													
					}
					if($summ1 > $max1 and is_numeric($max1)){
						$summ1_error = 'error';
						$summ1_error_txt = __('max','pn').'.: '. $max1 .' '.$vtype1;													
					}
					if($summ1c < 0){
						$summ1c_error = 'error';
					}
					if($summ2 < $min2){
						$summ2_error = 'error';
						$summ2_error_txt = __('min','pn').'.: '. $min2 .' '.$vtype2;													
					}
					if($summ2 > $max2 and is_numeric($max2)){
						$summ2_error = 'error';
						$summ2_error_txt = __('max','pn').'.: '. $max2 .' '.$vtype2;													
					}
					if($summ2c < 0){
						$summ2c_error = 'error';
					}																																
				
					$reserv = is_out_sum(get_naps_reserv($vd2->valut_reserv,$vd2->valut_decimal, $naps),$vd2->valut_decimal, 'reserv');
				
					$meta1 = $meta2 = '';
				
					if($zvt1 or $zvt2 or $us){
						$meta1 = '
						<div class="hexch_txt_line">
							'. $zvt1 .'
						</div>';	
					}

					if($zvt1 or $zvt2 or $us){
						$meta2 = '
						<div class="hexch_txt_line">
							'. $zvt2 .'
							'. $us .'
						</div>';
					}	

					$input_give = '
					<div class="hexch_curs_input js_wrap_error js_wrap_error_br '. $summ1_error .'">';
						$input_give .= apply_filters('exchange_input', '', 'give', $cdata, $vd1, $vd2, $naps, $user_id, $post_sum);
						$input_give .= '
						<div class="js_error js_summ1_error">'. $summ1_error_txt .'</div>					
					</div>				
					';
					
					$input_get = '
					<div class="hexch_curs_input js_wrap_error js_wrap_error_br '. $summ2_error .'">';
						$input_get .= apply_filters('exchange_input', '', 'get', $cdata, $vd1, $vd2, $naps, $user_id, $post_sum);
						$input_get .= '
						<div class="js_error js_summ2_error">'. $summ2_error_txt .'</div>					
					</div>				
					';				
					
					$com_give = '
					<div class="hexch_curs_input js_wrap_error js_wrap_error_br '. $summ1c_error .'">';
						$com_give .= apply_filters('exchange_input', '', 'give_com', $cdata, $vd1, $vd2, $naps, $user_id, $post_sum);
						$com_give .= '
						<div class="js_error js_summ1c_error">'. $summ1c_error_txt .'</div>
					</div>				
					';
				
					$com_give_text = '
					<div class="hexch_comis_line" '. $viv_com1 .'>
						<span class="js_comis_text1">'. $comis_text1 .'</span>
					</div>				
					';
				
					$com_get = '
					<div class="hexch_curs_input js_wrap_error js_wrap_error_br '. $summ2c_error .'">';
						$com_get .= apply_filters('exchange_input', '', 'get_com', $cdata, $vd1, $vd2, $naps, $user_id, $post_sum);
						$com_get .= '
						<div class="js_error js_summ2c_error">'. $summ2c_error_txt .'</div>
					</div>				
					';
				
					$com_get_text = '
					<div class="hexch_comis_line" '. $viv_com2 .'>
						<span class="js_comis_text2">'. $comis_text2 .'</span>
					</div>				
					';				
				
					$array = array(
						'[timeline]' => $message,
						'[description]' => $description,
						'[result]' => '<div class="ajax_post_bids_res"></div>',
						'[check]' => $check,
						'[submit]' => $submit,
						'[filters]' => apply_filters('exchange_step1',''),
						'[naps_field]' => get_napspole_wline($naps, 'widget'),
						'[reserve]' => '<span class="js_reserv_html">'. $reserv .' '. $vtype2 .'</span>',
						'[course]' => '<span class="js_curs_html">'. apply_filters('show_table_course', $curs1, $cdata['decimal1']) .' '. $vtype1 .' = '. apply_filters('show_table_course', $curs2, $cdata['decimal2']) .' '. $vtype2 .'</span>',
						'[psys_give]' => $psys1,
						'[vtype_give]' => $vtype1,
						'[psys_get]' => $psys2,
						'[vtype_get]' => $vtype2,	
						'[meta1]' => $meta1,
						'[meta2]' => $meta2,
						'[input_give]' => $input_give,
						'[input_get]' => $input_get,
						'[com_give]' => $com_give,
						'[com_give_text]' => $com_give_text,
						'[com_get]' => $com_get,
						'[com_get_text]' => $com_get_text,	
						'[account_give]' => get_account_wline($vd1, $naps, 1,'widget'),	
						'[account_get]' => get_account_wline($vd2, $naps, 2, 'widget'),
						'[give_field]' => get_doppole_wline($vd1, $naps, 1,'widget'),
						'[get_field]' => get_doppole_wline($vd2, $naps, 2, 'widget'),
						'[com_class_give]' => $viv_com1,
						'[com_class_get]' => $viv_com2,
					);	
					$array = apply_filters('exchange_html_list_ajax', $array, $naps, $vd1, $vd2);
		
					$html = '
					[timeline]
					
					<div class="hexch_div">
						<div class="hexch_div_ins">
							<div class="hexch_left">
								<div class="hexch_title">
									<div class="hexch_title_ins">
										<span>'. __('Send','pn') .' <span class="hexch_psys">"[psys_give] [vtype_give]"</span></span>
									</div>
								</div>
								
								[meta1]
								
								<div class="hexch_curs_line">
									<div class="hexch_curs_label">
										<div class="hexch_curs_label_ins">
											'. __('Amount','pn') .'<span class="red">*</span>:
										</div>
									</div>											
	
									[input_give]
				
										<div class="clear"></div>
								</div>

								<div class="hexch_curs_line" [com_class_give]>
									<div class="hexch_curs_label">
										<div class="hexch_curs_label_ins">
											'. __('With fees','pn') .'<span class="red">*</span>:
										</div>
									</div>
									[com_give]
		
									<div class="clear"></div>
								</div>
								[com_give_text]	

								[account_give]
								
								[give_field]								
	
							</div>
							<div class="hexch_right">
							
								<div class="hexch_title">
									<div class="hexch_title_ins">
										<span>'. __('Receive','pn') .' <span class="hexch_psys">"[psys_get] [vtype_get]"</span></span>
									</div>
								</div>	
								
								[meta2]

								<div class="hexch_curs_line">
									<div class="hexch_curs_label">
										<div class="hexch_curs_label_ins">
											'. __('Amount','pn') .'<span class="red">*</span>:
										</div>
									</div>
			
									[input_get]	
			
										<div class="clear"></div>
								</div>								
		
								<div class="hexch_curs_line" [com_class_get]>
									<div class="hexch_curs_label">
										<div class="hexch_curs_label_ins">
											'. __('With fees','pn') .'<span class="red">*</span>:
										</div>
									</div>
									[com_get]
			
										<div class="clear"></div>
								</div>
								[com_get_text]

								[account_get]
								
								[get_field]
								
							</div>
								<div class="clear"></div>
								
							[naps_field]
							[filters]
							[submit]
							[check]
							[result]
						</div>	
					</div>
					';		
		
					$html = apply_filters('exchange_html_ajax', $html, $naps, $vd1, $vd2);			
					$temp .= get_replace_arrays($array, $html);
					$temp .= apply_filters('after_exchange_widget','');
				
				} 			
			} else {
				$temp = '<div class="hexch_error"><div class="hexch_error_ins">'. __('Error! The direction do not exist','pn') .'</div></div>';
			}
		} else {
			$temp = '<div class="hexch_error"><div class="hexch_error_ins">'. __('Error! The direction do not exist','pn') .'</div></div>';
		} 		
	} else {
		$temp = '<div class="hexch_error"><div class="hexch_error_ins">'. __('Error! The direction do not exist','pn') .'</div></div>';
	} 
	
	if(!is_object($naps_data)){
		$naps_data = (object)$naps_data;
	}
	
	return $temp;
}