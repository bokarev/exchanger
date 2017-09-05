<?php
if( !defined( 'ABSPATH')){ exit(); } 

add_action('template_redirect','naps_initialization',0);
function naps_initialization(){
global $wpdb, $naps_id, $naps_data, $premiumbox;

	$naps_id = 0;
	$naps_data = array();

	$pages = get_option($premiumbox->plugin_page_name);
	$pnhash = is_naps_chpu(get_query_var('pnhash'));
	if(isset($pages['exchange']) and is_page($pages['exchange']) and $pnhash){
		$where = get_naps_where('exchange');
		$naps = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."naps WHERE naps_name='$pnhash' AND $where");
		if(isset($naps->id)){
			$output = apply_filters('get_naps_output', 1, $naps, 'exchange');
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
					
				}
			}
		}
	} 
	
	$naps_data = (object)$naps_data;
}
 
add_action('wp_before_admin_bar_render', 'wp_before_admin_bar_render_naps');
function wp_before_admin_bar_render_naps(){
global $wp_admin_bar, $naps_id, $naps_data;
	
    if(current_user_can('administrator') or current_user_can('pn_naps')){
		if(!is_admin()){
			if(is_exchange_page()){
				$wp_admin_bar->add_menu( array(
					'id'     => 'edit_naps',
					'href' => admin_url('admin.php?page=pn_add_naps&item_id='.$naps_id),
					'title'  => __('Edit direction exchange','pn'),	
				));	
				$wp_admin_bar->add_menu( array(
					'id'     => 'edit_valut1',
					'parent' => 'edit_naps',
					'href' => admin_url('admin.php?page=pn_add_valuts&item_id='.$naps_data->valut1),
					'title'  => sprintf(__('Edit "%s"','pn'), $naps_data->item1),	
				));
				$wp_admin_bar->add_menu( array(
					'id'     => 'edit_valut2',
					'parent' => 'edit_naps',
					'href' => admin_url('admin.php?page=pn_add_valuts&item_id='.$naps_data->valut2),
					'title'  => sprintf(__('Edit "%s"','pn'), $naps_data->item2),	
				));				
			}
		}
	}
	
}

/* добавляем JS */
add_action('siteplace_js','siteplace_js_exchange_step1');
function siteplace_js_exchange_step1(){
?>	
jQuery(function($){ 

 	function get_exchange_step1(id){
		
		var id1 = $('#select_give').val();
		var id2 = $('#select_get').val();
		
		$('.exch_ajax_wrap_abs').show();
			
		var dataString='id='+id+'&id1=' + id1 + '&id2=' + id2;
		$.ajax({
			type: "POST",
			url: "<?php echo get_ajax_link('exchange_step1');?>",
			dataType: 'json',
			data: dataString,
			error: function(res, res2, res3){
				<?php do_action('pn_js_error_response', 'ajax'); ?>
			},			
			success: function(res)
			{
					
				$('.exch_ajax_wrap_abs').hide();
				
				if(res['status'] == 'success'){
					$('#exch_html').html(res['html']);	

					if($('#the_title_page').length > 0){
						$('#the_title_page').html(res['titlepage']);
					}	
					
					$('title').html(res['title']);
					
					if($('meta[name=keywords]').length > 0){
						$('meta[name=keywords]').attr('content', res['keywords']);
					}
					if($('meta[name=description]').length > 0){
						$('meta[name=description]').attr('content', res['description']);
					}
					
					var thelink = res['thelink'];
					if(thelink){
						window.history.replaceState(null, null, thelink);
					}				
					
					<?php do_action('live_change_html'); ?>
				} else {
					<?php do_action('pn_js_alert_response'); ?>
				}
					
			}
		});		
		
	}
	$(document).on('change', '#select_give', function(){
		get_exchange_step1(1);
	});
	
	$(document).on('change', '#select_get', function(){
		get_exchange_step1(2);
	});	
	
});	
<?php	
}	 
/* end добавляем JS */

add_action('myaction_site_exchange_step1', 'def_myaction_ajax_exchange_step1');
function def_myaction_ajax_exchange_step1(){
global $wpdb, $premiumbox, $naps_id, $naps_data;	
	
	$log = array();
	$log['status'] = '';
	$log['response'] = '';
	$log['status_code'] = 0; 
	$log['status_text'] = __('Error','pn');		
	
	$premiumbox->up_mode();
	
	$id = intval(is_param_post('id'));
	$id1 = intval(is_param_post('id1')); if($id1 < 0){ $id1 = 0; }
	$id2 = intval(is_param_post('id2')); if($id2 < 0){ $id2 = 0; }	
	
	$where = get_naps_where('exchange');
	
	$naps = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."naps WHERE $where AND valut_id1='$id1' AND valut_id2='$id2'");
	if(isset($naps->id)){
		$output = apply_filters('get_naps_output', 1, $naps, 'exchange');
		if(!$output){
			$naps = '';
		}
	}
	
	if(!isset($naps->id)){
		$tablenot = intval($premiumbox->get_option('exchange','tablenot'));
		if($tablenot == 1){
			if($id == 1){
				$nap_items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."naps WHERE $where AND valut_id1 = '$id1' ORDER BY site_order1 ASC");
				foreach($nap_items as $nap){
					$output = apply_filters('get_naps_output', 1, $nap, 'exchange');
					if($output){
						$naps = $nap;
						break;
					}	
				}				
			} else {
				$nap_items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."naps WHERE $where AND valut_id2='$id2' ORDER BY site_order1 ASC");
				foreach($nap_items as $nap){
					$output = apply_filters('get_naps_output', 1, $nap, 'exchange');
					if($output){
						$naps = $nap;
						break;
					}	
				}				
			}
		}
	}	
			
	if(isset($naps->id)){
		
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
						
			$log['status'] = 'success';
			$log['thelink'] = get_exchange_link($naps->naps_name);
			$log['html'] = get_exchange_html($id);

			$name = get_option('blogname');
			$item_title1 = get_valut_title($vd1);
			$item_title2 = get_valut_title($vd2);
												
			$titlepage = get_exchange_title();	
									
			$log['title'] = $name . '- '. $titlepage;
			$log['titlepage'] = $titlepage;
			$log['keywords'] = '';
			$log['description'] = '';
			$log = apply_filters('exchange_step1_log', $log);
			
		} else {	
			$log['status'] = 'error';
			$log['status_code'] = 1;
			$log['status_text'] = __('Error! The direction do not exist','pn');
		}								
	} else {	
		$log['status'] = 'error';
		$log['status_code'] = 1;
		$log['status_text'] = __('Error! The direction do not exist','pn');
	}
	
	if(!is_object($naps_data)){
		$naps_data = (object)$naps_data;
	}	
	
	echo json_encode($log);
	exit;
}

function get_exchange_html($place){
global $wpdb, $naps_id, $naps_data, $premiumbox;	
	
	$temp = '';	
	
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);
	
	$place = intval($place);
	
	$show_data = pn_exchanges_output('exchange');
	
	if($show_data['text']){
		$temp .= '<div class="exch_error"><div class="exch_error_ins">'. $show_data['text'] .'</div></div>';
	}	
	
	if($show_data['mode'] == 1 and $naps_id > 0){
		$naps_id = intval($naps_id);
		$vd1 = $naps_data->vd1;
		$vd2 = $naps_data->vd2;
		$naps = $naps_data->direction;
		
		$temp .= apply_filters('before_exchange_table','');
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
			<div class="notice_message">
				<div class="notice_message_ins">
					<div class="notice_message_abs"></div>
					<div class="notice_message_close"></div>
					<div class="notice_message_title">
						<div class="notice_message_title_ins">
							<span>'. __('Attention!','pn') .'</span>
						</div>
					</div>
					<div class="notice_message_text">
						<div class="notice_message_text_ins">
							'. apply_filters('comment_text',$text) .'
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
		<div class="xchange_checkdata_div">
			<div class="checkbox '. $cl_ch .'"><input type="checkbox" id="check_data" name="check_data" '. $ch_ch .' value="1" /> '. __('Remember entered data','pn') .'</div>
		</div>
		';
		/* end check */	
			
		/* submit */	
		$submit = '
		<div class="xchange_submit_div">
			<input type="submit" formtarget="_top" class="xchange_submit" name="" value="'. __('Exchange','pn') .'" /> 
				<div class="clear"></div>
		</div>';
		/* end submit */	
	
		$post_sum = is_my_money(is_param_get('get_sum'));
		if($post_sum <= 0){
			$post_sum = is_my_money(get_mycookie('cache_sum1'));
		}
		
		$cdata = get_calc_data($vd1, $vd2, $naps, $user_id, $post_sum);
		
		$vtype1 = $cdata['vtype1'];
		$vtype2 = $cdata['vtype2'];
		$psys1 = $cdata['psys1'];
		$psys2 = $cdata['psys2'];						
																											
		$curs1 = $cdata['curs1'];
		$curs2 = $cdata['curs2'];		
		
		$user_discount = $cdata['user_discount'];
		$us = '';
		if($user_discount > 0){
			$us = '<span class="span_skidka">'. __('Your discount','pn') .': '. $user_discount .'%</span>';
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
		/* if($min1 > $max1 and is_numeric($max1)){ $min1 = $max1; }	 */	
											
		$vz1 = array();
		if($min1 > 0){
			$vz1[] = __('min','pn').'.: '. $min1 .' '.$vtype1;
		}
		if(is_numeric($max1)){
			$vz1[] = __('max','pn').'.: '. $max1 .' '.$vtype1;
		}
		$zvt1 = '';
		if(count($vz1) > 0){
			$zvt1 = '<span class="span_give_max">'. join(', ',$vz1) .'</span>';
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
			$zvt2 = '<span class="span_get_max">'. join(', ',$vz2) .'</span>';
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
				
		$meta1 = $meta2 = $meta1d = $meta2d = '';	
		if($zvt1){
			$meta1d = '<div class="xchange_info_line"></div>';
		}	

		if($zvt1){
			$meta1 = '<div class="xchange_info_line">'. $zvt1 .'</div>';
		}

		if($zvt2 or $us){
			$meta2d = '<div class="xchange_info_line">'. $us .'</div>';
		}		
		
		if($zvt2 or $us){
			$meta2 = '<div class="xchange_info_line">'. $zvt2 .'</div>';
		}

		/* selects */
		$naps1 = $naps2 = array();
			
		$valid1 = $vd1->id;
		$valid2 = $vd2->id;
			
		$select_give = $select_get = '';
			
		$tableselect = intval($premiumbox->get_option('exchange','tableselect'));	
			
		$v = get_valuts_data();	
			
		$where = get_naps_where('exchange');			
		$napobmens = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."naps WHERE $where ORDER BY site_order1 ASC");
		foreach($napobmens as $nd){
			$output = apply_filters('get_naps_output', 1, $nd, 'exchange');
			if($output){
				if($tableselect == 1){
					if($place == 1){ /* если выбрана левая сторона */
						$naps1[$nd->valut_id1] = $nd;
						if($nd->valut_id1 == $valid1){
							$naps2[$nd->valut_id2] = $nd;
						}
					} else { /* если выбрана правая сторона */
						$naps2[$nd->valut_id2] = $nd;
						if($nd->valut_id2 == $valid2){
							$naps1[$nd->valut_id1] = $nd;
						}						
					}
				} else {
					$naps1[$nd->valut_id1] = $nd;
					$naps2[$nd->valut_id2] = $nd;					
				}
			}
		}		
		
		$select_give = '
		<select name="" class="imager" autocomplete="off" id="select_give">'; 
			foreach($naps1 as $key => $np){
				$select_give .= '<option value="'. $key .'" '. selected($key,$valid1,false) .' data-img="'. get_valut_logo($v[$key]) .'">'. get_valut_title($v[$key]) .'</option>';
			}
			$select_give .= '
		</select>';	

		$select_get = '
		<select name="" class="imager" autocomplete="off" id="select_get">';
			foreach($naps2 as $key => $np){					
				$select_get .= '<option value="'. $key .'" '. selected($key,$valid2,false) .' data-img="'. get_valut_logo($v[$key]) .'">'. get_valut_title($v[$key]) .'</option>';					
			}
		$select_get .= '
		</select>';	
		/* end selects */
		
		$com_give_text = '
		<div class="xchange_sumandcom" '. $viv_com1 .'>
			<span class="js_comis_text1">'. $comis_text1 .'</span>
		</div>';		
		
		$com_get_text = '
		<div class="xchange_sumandcom" '. $viv_com2 .'>
			<span class="js_comis_text2">'. $comis_text2 .'</span>
		</div>';		
		
		$input_give ='
		<div class="xchange_sum_input js_wrap_error js_wrap_error_br '. $summ1_error .'">';
			$input_give .= apply_filters('exchange_input', '', 'give', $cdata, $vd1, $vd2, $naps, $user_id, $post_sum);
			$input_give .= '
			<div class="js_error js_summ1_error">'. $summ1_error_txt .'</div>
		</div>
		';	

		$input_get ='
		<div class="xchange_sum_input js_wrap_error js_wrap_error_br '. $summ2_error .'">';
			$input_get .= apply_filters('exchange_input', '', 'get', $cdata, $vd1, $vd2, $naps, $user_id, $post_sum);
			$input_get .= '
			<div class="js_error js_summ2_error">'. $summ2_error_txt .'</div>
		</div>
		';	

		$com_give ='
		<div class="xchange_sum_input js_wrap_error js_wrap_error_br '. $summ1c_error .'">';
			$com_give .= apply_filters('exchange_input', '', 'give_com', $cdata, $vd1, $vd2, $naps, $user_id, $post_sum);
			$com_give .= '
			<div class="js_error js_summ1c_error">'. $summ1c_error_txt .'</div>
		</div>';

		$com_get ='
		<div class="xchange_sum_input js_wrap_error js_wrap_error_br '. $summ2c_error .'">';
			$com_get .= apply_filters('exchange_input', '', 'get_com', $cdata, $vd1, $vd2, $naps, $user_id, $post_sum);
			$com_get .= '
			<div class="js_error js_summ2c_error">'. $summ2c_error_txt .'</div>
		</div>';		
		
		$array = array(
			'[timeline]' => $message,
			'[description]' => $description,
			'[result]' => '<div class="ajax_post_bids_res"></div>',
			'[check]' => $check,
			'[submit]' => $submit,
			'[filters]' => apply_filters('exchange_step1',''),
			'[naps_field]' => get_napspole_wline($naps,2),
			'[reserve]' => '<span class="js_reserv_html">'. $reserv .' '. $vtype2 .'</span>',
			'[course]' => '<span class="js_curs_html">'. apply_filters('show_table_course', $curs1, $cdata['decimal1']) .' '. $vtype1 .' = '. apply_filters('show_table_course', $curs2, $cdata['decimal2']) .' '. $vtype2 .'</span>',
			'[psys_give]' => $psys1,
			'[vtype_give]' => $vtype1,
			'[psys_get]' => $psys2,
			'[vtype_get]' => $vtype2,	
			'[meta1]' => $meta1,
			'[meta1d]' => $meta1d,
			'[meta2]' => $meta2,
			'[meta2d]' => $meta2d,
			'[select_give]' => $select_give,
			'[select_get]' => $select_get,
			'[input_give]' => $input_give,
			'[input_get]' => $input_get,
			'[com_give]' => $com_give,
			'[com_give_text]' => $com_give_text,
			'[com_get]' => $com_get,
			'[com_get_text]' => $com_get_text,	
			'[account_give]' => get_account_wline($vd1, $naps, 1,'shortcode'),	
			'[account_get]' => get_account_wline($vd2, $naps, 2,'shortcode'),
			'[give_field]' => get_doppole_wline($vd1, $naps, 1, 'shortcode'),
			'[get_field]' => get_doppole_wline($vd2, $naps, 2, 'shortcode'),
			'[com_class_give]' => $viv_com1,
			'[com_class_get]' => $viv_com2,
		);
		$array = apply_filters('exchange_html_list', $array, $naps, $vd1, $vd2);

		$html = '
			[timeline]
			<div class="xchange_div">
				<div class="xchange_div_ins">
					<div class="xchange_data_title otd">
						<div class="xchange_data_title_ins">
							<span>'. __('Send','pn') .'</span>
						</div>	
					</div>
					<div class="xchange_data_div">
						<div class="xchange_data_ins">
							<div class="xchange_data_left">
								[meta1d]
							</div>	
							<div class="xchange_data_right">
								[meta1]
							</div>
								<div class="clear"></div>
							<div class="xchange_data_left">
								<div class="xchange_select">
									[select_give]						
								</div>
							</div>	
							<div class="xchange_data_right">
								<div class="xchange_sum_line">
									<div class="xchange_sum_label">
										'. __('Amount','pn') .'<span class="red">*</span>:
									</div>
									[input_give]
										<div class="clear"></div>
								</div>
							</div>
								<div class="clear"></div>										
							<div class="xchange_data_left">
								[com_give_text]
							</div>	
							<div class="xchange_data_right">
								<div class="xchange_sum_line" [com_class_give]>
									<div class="xchange_sum_label">
										'. __('Amount','pn') .'<span class="red">*</span>:
									</div>
									[com_give]
										<div class="clear"></div>
								</div>
							</div>
								<div class="clear"></div>										
							<div class="xchange_data_left">
								[account_give]
								[give_field]
							</div>	
							<div class="clear"></div>
						</div>
					</div>
					<div class="xchange_data_title pol">
						<div class="xchange_data_title_ins">
							<span>'. __('Receive','pn') .'</span>
						</div>	
					</div>
					<div class="xchange_data_div">
						<div class="xchange_data_ins">
							<div class="xchange_data_left">
								[meta2d]
							</div>
							<div class="xchange_data_right">
								[meta2]
							</div>
								<div class="clear"></div>
							<div class="xchange_data_left">
								<div class="xchange_select">
									[select_get]						
								</div>									
							</div>
							<div class="xchange_data_right">
								<div class="xchange_sum_line">
									<div class="xchange_sum_label">
										'. __('Amount','pn') .'<span class="red">*</span>:
									</div>
									[input_get]
										<div class="clear"></div>
									</div>									
								</div>
									<div class="clear"></div>
									<div class="xchange_data_left">
										[com_get_text]
									</div>
									<div class="xchange_data_right">
										<div class="xchange_sum_line" [com_class_get]>
											<div class="xchange_sum_label">
												'. __('Amount','pn') .'<span class="red">*</span>:
											</div>
											[com_get]
												<div class="clear"></div>
										</div>									
									</div>
										<div class="clear"></div>
							<div class="xchange_data_left">	
								[account_get]
								[get_field]
							 </div>
							<div class="clear"></div>	
						</div>
					</div>					
					
					[naps_field]
					
					[filters]
					
					[submit]
					[check]

					[result]
				</div>
			</div>					
			[description]
		';

		$html = apply_filters('exchange_html', $html, $naps, $vd1, $vd2);			
		$temp .= get_replace_arrays($array, $html);	
	
		$temp .= apply_filters('after_exchange_table','');
	}
	
	return $temp;
}

/* шорткод */
function exchange_page_shortcode($atts, $content) {
global $wpdb, $naps_id, $naps_data, $premiumbox;
	
	$temp = '';
	$temp .= apply_filters('before_exchange_page','');
	
	$temp .= '
	<form method="post" class="ajax_post_bids" action="'. get_ajax_link('bidsform') .'">
		<div class="exch_ajax_wrap">
			<div class="exch_ajax_wrap_abs"></div>
			<div id="exch_html">'. get_exchange_html(1) .'</div>
		</div>
	</form>
	';
	
	$temp .= apply_filters('after_exchange_page','');
	
	return $temp;
}
add_shortcode('exchange', 'exchange_page_shortcode');
/* end шорткод */