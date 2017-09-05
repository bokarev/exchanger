<?php
if( !defined( 'ABSPATH')){ exit(); } 

add_action('template_redirect','bids_initialization');
function bids_initialization(){
global $wpdb, $bids_id, $bids_data;

	$bids_id = 0;
	$bids_data = array();

	$hashed = is_bid_hash(get_query_var('hashed'));
	if($hashed){
		$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."bids WHERE hashed='$hashed'");
		if(isset($data->id)){
			$bids_data = $data;
		}
	} 
	
	$bids_data = (object)$bids_data;
}

add_action('wp_before_admin_bar_render', 'wp_before_admin_bar_render_exchangestep');
function wp_before_admin_bar_render_exchangestep() {
global $wp_admin_bar, $bids_data;
	
    if(current_user_can('administrator') or current_user_can('pn_bids')){
		if(!is_admin()){
			if(isset($bids_data->id)){
				$wp_admin_bar->add_menu( array(
					'id'     => 'show_bids',
					'href' => admin_url('admin.php?page=pn_bids&bidid='.$bids_data->id),
					'title'  => __('Go to order','pn'),	
				));	
				$wp_admin_bar->add_menu( array(
					'id'     => 'edit_naps',
					'parent' => 'show_bids',
					'href' => admin_url('admin.php?page=pn_add_naps&item_id='.$bids_data->naps_id),
					'title'  => __('Edit direction exchange','pn'),	
				));				
				
				$valut1 = pn_strip_input(ctv_ml($bids_data->valut1)).' '.pn_strip_input($bids_data->vtype1);
				$valut2 = pn_strip_input(ctv_ml($bids_data->valut2)).' '.pn_strip_input($bids_data->vtype2);
				
				$wp_admin_bar->add_menu( array(
					'id'     => 'edit_valut1',
					'parent' => 'show_bids',
					'href' => admin_url('admin.php?page=pn_add_valuts&item_id='.$bids_data->valut1i),
					'title'  => sprintf(__('Edit "%s"','pn'), $valut1),	
				));
				$wp_admin_bar->add_menu( array(
					'id'     => 'edit_valut2',
					'parent' => 'show_bids',
					'href' => admin_url('admin.php?page=pn_add_valuts&item_id='.$bids_data->valut2i),
					'title'  => sprintf(__('Edit "%s"','pn'), $valut2),	
				));			
			}
		}
	}
	
}

add_action('siteplace_js','siteplace_js_exchange_checkrule');
function siteplace_js_exchange_checkrule(){
?>
jQuery(function($){ 
	
	$('#check_rule_step').on('change',function(){
		if($(this).prop('checked')){
			$('#check_rule_step_input').prop('disabled',false);
		} else {
			$('#check_rule_step_input').prop('disabled',true);
		}
	});

	$('#check_rule_step_input').on('click',function(){
		$(this).parents('.ajax_post_form').find('.resultgo').html('<div class="resulttrue"><?php echo esc_attr(__('Processing. Please wait','pn')); ?></div>');
	});
	
	$('.iam_pay_bids').on('click',function(){
		if (!confirm("<?php echo esc_attr(__('Are you sure that you paid your order?','pn')); ?>")) {
			return false;
		}
	});		
			
});		
<?php 
} 

add_action('siteplace_js','siteplace_js_exchange_timer');
function siteplace_js_exchange_timer(){
?>
jQuery(function($){
	
	if($('.check_payment_hash').length > 0){
		var nowdata = 0;
		var redir = 0;	
		var second = parseInt($('.check_payment_hash').attr('data-time'));

		function check_payment_now(){	
			nowdata = parseInt(nowdata) + 1;
			if(nowdata < second){
				$('.block_check_payment_abs').html(nowdata);
				var wid = $('.block_check_payment').width();
				if(wid > 1){
					var onepr = wid / second;
					var nwid = onepr * nowdata;
					$('.block_check_payment_ins').animate({'width': nwid},500);
				}				
			} else {
				if(redir == 0){
					var durl = $('.check_payment_hash').attr('data-hash');
					redir = 1;
					if(durl.length > 0){
						$('.exchange_status_abs').show();
						
						var dataString='hashed='+durl+'&auto_check=1';
						$.ajax({
							type: "POST",
							url: "<?php echo get_ajax_link('refresh_status_bids');?>",
							dataType: 'json',
							data: dataString,
							error: function(res, res2, res3){
								<?php do_action('pn_js_error_response', 'ajax'); ?>
							},			
							success: function(res)
							{
								$('.exchange_status_abs').hide();
								if(res['html']){
									$('#exchange_status_html').html(res['html']);
									<?php do_action('live_change_html'); ?>
									redir = 0;
									nowdata = 0;
								} 
							}
						});	
					}					
				}
			}
		}
		setInterval(check_payment_now,1000);	
	}
	
});		
<?php 
} 

add_action('myaction_site_refresh_status_bids', 'def_myaction_site_refresh_status_bids');
function def_myaction_site_refresh_status_bids(){
global $wpdb, $bids_id, $bids_data, $premiumbox;
	
	$log = array();
	$log['status'] = '';
	$log['response'] = '';
	$log['status_code'] = '0'; 
	$log['status_text']= '';
	$log['html'] = '';	
	
	$premiumbox->up_mode();
	
	$hashed = is_bid_hash(is_param_post('hashed'));
	if($hashed){
		$bids_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."bids WHERE hashed='$hashed'");
		if(isset($bids_data->id)){
			$bids_id = $bids_data->id;
			$html = apply_filters('exchangestep_'. is_status_name($bids_data->status), '', $bids_data);
			$html .= apply_filters('exchangestep_all', '', is_status_name($bids_data->status), $bids_data);
			$log['html'] = $html;
		}
	} 	
	
	echo json_encode($log);
	exit;
}

/* шорткод статусов */
function exchangestep_page_shortcode($atts, $content) {
global $wpdb, $bids_data;
	
	$temp = '<div class="resultfalse">'. __('Error! Order do not exist','pn') .'</div>';

	if(isset($bids_data->id)){
			
		$temp = apply_filters('before_exchangestep_page','', $bids_data);
		$temp .= '
		<div class="exchange_status_html">
			<div class="exchange_status_abs"></div>
			<div id="exchange_status_html">';	
				$temp .= apply_filters('exchangestep_'. is_status_name($bids_data->status), '', $bids_data);
				$temp .= apply_filters('exchangestep_all', '', is_status_name($bids_data->status), $bids_data);
			$temp .= '
			</div>
		</div>';	
		$temp .= apply_filters('after_exchangestep_page','', $bids_data);
			
	}
	
	return $temp;
}
add_shortcode('exchangestep', 'exchangestep_page_shortcode');

/* auto */
add_filter('exchangestep_auto','get_exchangestep_auto',1,2);
function get_exchangestep_auto($temp, $item){
global $wpdb, $premiumbox;
	
    $temp = '';
	
	if(isset($item->id)){
		
		$item_id = intval($item->id);
		$hashed = is_bid_hash($item->hashed);
		$naps_id = intval($item->naps_id);
		
		$valut1 = intval($item->valut1i);
		$valut2 = intval($item->valut2i);
		
		$vd1 = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."valuts WHERE id='$valut1'");
		$vd2 = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."valuts WHERE id='$valut2'");
	
		$where = get_naps_where('exchange');
		$naps_data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."naps WHERE $where AND id='$naps_id'");
		if(!isset($naps_data->id)){
			$output = apply_filters('get_naps_output', 1, $naps_data, 'exchange');
			if($output != 1){
				return '<div class="exch_error"><div class="exch_error_ins">'. __('Exchange direction is disabled','pn') .'</div></div>';
			}
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
		
		$pay_com1 = $pay_com2 = 0;
		if(isset($naps->id)){
			$pay_com1 = pn_strip_input($naps->pay_com1);
			$pay_com2 = pn_strip_input($naps->pay_com2);
		}
		
		$com_ps1 = pn_strip_input($item->com_ps1);
		if($pay_com1 == 1){
			$com_ps1 = 0;
		}
		 
		$comis_text1 = get_comis_text($com_ps1, $item->dop_com1, ctv_ml(is_isset($vd1,'psys_title')), is_isset($vd1,'vtype_title'), 1, 0);
		
		$com_ps2 = pn_strip_input($item->com_ps2);		
		if($pay_com2 == 1){
			$com_ps2 = 0;
		}		
		
		$comis_text2 = get_comis_text($com_ps2, $item->dop_com2, ctv_ml(is_isset($vd2,'psys_title')), is_isset($vd2,'vtype_title'), 2,0);

		$dmetas = @unserialize($item->dmetas);
		$metas = @unserialize($item->metas);
	
		/* timeline */	
		$text = trim(get_naps_txtmeta($naps_id, 'timeline_txt'));
		$naps_nodescr = intval($premiumbox->get_option('naps_nodescr', 'timeline_txt'));
		if($naps_nodescr == 1 and !$text or $naps_nodescr == 2){
			$text = trim($premiumbox->get_option('naps_temp', 'timeline_txt'));
		}
		$text = ctv_ml($text);
		$text = apply_filters('naps_instruction', $text, $naps, $vd1, $vd2);								
		$timeline = '';		
		if($text){	
			$timeline = '
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
		/* end timeline */		
		
		$com_give_text = $com_get_text = '';
		if($comis_text1){
			$com_give_text ='
			<div class="block_xchdata_comm">
				('. $comis_text1 .')
			</div>	
			';
		}
		if($comis_text2){
			$com_get_text ='
			<div class="block_xchdata_comm">
				('. $comis_text2 .')
			</div>	
			';
		}	

		$account_give = $account_get = '';
		if($item->account1){
			$txt = pn_strip_input(ctv_ml(is_isset($vd1,'txt1')));
			if(!$txt){ $txt = __('From account','pn'); }
			$account = $item->account1;
			$account = apply_filters('show_user_account', $account, $item, $naps, $vd1);						
			$account_give = '<div class="block_xchdata_line"><span>'. $txt .':</span> '. get_secret_value($account, $premiumbox->get_option('exchange','an1_hidden')) .'</div>';
		}	

		if($item->account2){
			$txt = pn_strip_input(ctv_ml(is_isset($vd2,'txt2')));
			if(!$txt){ $txt = __('Into account','pn'); }
			$account = $item->account2;
			$account = apply_filters('show_user_account', $account, $item, $naps, $vd2);									
			$account_get = '<div class="block_xchdata_line"><span>'. $txt .':</span> '. get_secret_value($account, $premiumbox->get_option('exchange','an2_hidden')) .'</div>';
		}		
	
		$give_field = $get_field = '';
	
		if(isset($dmetas[1]) and is_array($dmetas[1])){
			foreach($dmetas[1] as $value){
										
				$title = pn_strip_input(ctv_ml(is_isset($value,'title')));
				$data = pn_strip_input(is_isset($value,'data'));
				$hidden = intval(is_isset($value,'hidden'));
				if(trim($data)){
					$give_field .= '<div class="block_xchdata_line"><span>'. $title .':</span> '. get_secret_value($data, $hidden) .'</div>';
				}
			}
		}

		if(isset($dmetas[2]) and is_array($dmetas[2])){
			foreach($dmetas[2] as $value){
										
				$title = pn_strip_input(ctv_ml(is_isset($value,'title')));
				$data = pn_strip_input(is_isset($value,'data'));
				$hidden = intval(is_isset($value,'hidden'));
				if(trim($data)){
					$get_field .= '<div class="block_xchdata_line"><span>'. $title .':</span> '. get_secret_value($data, $hidden) .'</div>';
				}
			}
		}		

		$personal_data = '';
		if(isset($metas) and is_array($metas) and count($metas) > 0){
				
			$personal_data = '
			<div class="block_persdata">
				<div class="block_persdata_ins">
					<div class="block_persdata_title">
						<div class="block_persdata_title_ins">
							<span>'. apply_filters('exchnage_personaldata_title',__('Personal data','pn')) .'</span>
						</div>
					</div>
					<div class="block_persdata_info">';	
						foreach($metas as $value){				
							$title = pn_strip_input(ctv_ml(is_isset($value,'title')));
							$data = pn_strip_input(is_isset($value,'data'));
							$hidden = intval(is_isset($value,'hidden'));
							if(trim($data)){			
								$personal_data .= '<p><span>'. $title .':</span> '. get_secret_value($data, $hidden) .'</p>';			
							}	
						}							
					$personal_data .= '
					</div>
				</div>
			</div>';
				
		}	
		
		$check_rule = '<div class="checkbox"><input type="checkbox" id="check_rule_step" name="check" value="1" /> '. sprintf(__('I read and agree with <a href="%s">the terms and conditions</a>','pn'), $premiumbox->get_page('tos') ) .'</div>';
	
		$submit = '<input type="submit" name="" formtarget="_top" id="check_rule_step_input" disabled="disabled" value="'. __('Create a order','pn') .'" />';
	
		$array = array(
			'[timeline]' => $timeline,
			'[result]' => '<div class="ajax_post_bids_res"><div class="resultgo"></div></div>',
			'[submit]' => $submit,
			'[check_rule]' => $check_rule,
			'[personal_data]' => $personal_data,
			'[give_field]' => $give_field,
			'[get_field]' => $get_field,
			'[account_give]' => $account_give,
			'[account_get]' => $account_get,
			'[com_give_text]' => $com_give_text,
			'[com_get_text]' => $com_get_text,	
			'[sum_give]' => pn_strip_input($item->summ1c),	
			'[sum_get]' => pn_strip_input($item->summ2c),
			'[give_currency]' => get_valut_title($vd1),
			'[get_currency]' => get_valut_title($vd2),
			'[give_currency_logo]' => get_valut_logo($vd1),
			'[get_currency_logo]' => get_valut_logo($vd2),
		);
		$array = apply_filters('exchangestep_auto_html_list', $array, $item, $naps, $vd1, $vd2);	
	
		$temp .= '
		<form action="'. get_ajax_link('createbids') .'" class="ajax_post_form" method="post">
			<input type="hidden" name="hash" value="'. $hashed .'" />
		';
	
		$html = '
		[timeline]
			
		<div class="block_xchangedata">
			<div class="block_xchangedata_ins">			
				<div class="block_xchdata">
					<div class="block_xchdata_ins">	

						<div class="block_xchdata_title otd">
							<span>'. __('Send','pn') .'</span>
						</div>	

						[com_give_text]
						
						<div class="block_xchdata_info">
							<div class="block_xchdata_info_left">
								<div class="block_xchdata_line"><span>'. __('Amount','pn') .':</span> [sum_give] [give_currency]</div>
								
								[account_give]
								
								[give_field]

							</div>
							<div class="block_xchdata_info_right"> 
								<div class="block_xchdata_ico" style="background: url([give_currency_logo]) no-repeat center center"></div>
								<div class="block_xchdata_text">[give_currency]</div>
									<div class="clear"></div>
							</div>		
								<div class="clear"></div>
						</div>		
					</div>
				</div>
				<div class="block_xchdata">
					<div class="block_xchdata_ins">
						<div class="block_xchdata_title pol">							
							<span>'. __('Receive','pn') .'</span>
						</div>
						
						[com_get_text]
						
						<div class="block_xchdata_info">
							<div class="block_xchdata_info_left">
								<div class="block_xchdata_line"><span>'. __('Amount','pn') .':</span> [sum_get] [get_currency]</div>
								
								[account_get]
								
								[get_field]

							</div>
							<div class="block_xchdata_info_right">
								<div class="block_xchdata_ico" style="background: url([get_currency_logo]) no-repeat center center;"></div>
								<div class="block_xchdata_text">[get_currency]</div>
									<div class="clear"></div>
							</div>		
								<div class="clear"></div>
						</div>
					</div>
				</div>

				[personal_data]
				
				<div class="block_checked_rule">
					[check_rule]
				</div>				
				<div class="block_warning">
					<div class="block_warning_ins">
						<div class="block_warning_title">
							<div class="block_warning_title_ins">
								<span>'. __('Attention!','pn') .'</span>
							</div>
						</div>
						<div class="block_warning_text">
							<div class="block_warning_text_ins">
								<p>'. __('Clicking on button "Create a order" means that you accept all the basic terms and conditions.','pn') .'</p>
							</div>
						</div>
					</div>
				</div>				
				<div class="block_rule_info">
					'. __('Thoroughly check all data before creating a order!','pn') .'
				</div>				
				<div class="block_submitbutton">
					[submit]
				</div>
				
				[result]
		
			</div>
		</div>		
		';

		$html = apply_filters('exchangestep_auto_html', $html, $item, $naps, $vd1, $vd2);			
		$temp .= get_replace_arrays($array, $html);	

		$temp .= '
		</form>
		';		
	
	}
	
	return $temp;
}
/* end auto */

/* new */
add_filter('exchangestep_new','get_exchangestep_new',1,2);
add_filter('exchangestep_techpay','get_exchangestep_new',1,2); 
function get_exchangestep_new($temp, $item){
global $wpdb, $premiumbox;
	
	$temp = '';
	
	if(isset($item->id)){
	
		$naps_id = intval($item->naps_id);
	
		$where = get_naps_where('exchange');
		$naps = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."naps WHERE $where AND id='$naps_id'");
		if(!isset($naps->id)){
			$output = apply_filters('get_naps_output', 1, $naps_data, 'exchange');
			if($output != 1){			
				return '<div class="exch_error"><div class="exch_error_ins">'. __('Exchange direction is disabled','pn') .'</div></div>';
			}	
		}		
		$m_id = apply_filters('get_merchant_id','', is_isset($naps,'m_in'), $item);		
	
		$valut1 = intval($item->valut1i);
		$valut2 = intval($item->valut2i);
		
		$vd1 = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."valuts WHERE id='$valut1'");
		$vd2 = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."valuts WHERE id='$valut2'");	
	
		$status = is_status_name($item->status);
		$js_autocheck = intval($premiumbox->get_option('naps_timer', 'status_'.$status));
		$status_text = pn_strip_text(ctv_ml($premiumbox->get_option('naps_status', 'status_'.$status)));
		$status_title = pn_strip_input(ctv_ml($premiumbox->get_option('naps_title', 'status_'.$status)));
		$instruction = trim(get_naps_txtmeta($naps_id, 'status_'.$status));
		$naps_nodescr = intval($premiumbox->get_option('naps_nodescr', 'status_'.$status));
		if($naps_nodescr == 1 and !$instruction or $naps_nodescr == 2){
			$instruction = trim($premiumbox->get_option('naps_temp', 'status_'.$status));
		}
		$instruction = ctv_ml($instruction);

		$def_status = array(
			'new' => array(
				'title' => __('How to make a payment','pn'),
				'text' => __('accepted, waiting to be paid by client','pn'),
			),
			'techpay' => array(
				'title' => __('How to make a payment','pn'),
				'text' => __('accepted, waiting to be paid by client','pn'),
			),
		);		
		
		if(!$status_text and isset($def_status[$status]['text'])){
			$status_text = $def_status[$status]['text'];
		}
		
		if(!$status_title and isset($def_status[$status]['title'])){
			$status_title = $def_status[$status]['title'];
		}	
	
		$instruction = apply_filters('instruction_merchant', $instruction, $m_id, $item, $naps, $vd1, $vd2);
		$instruction = apply_filters('bid_instruction_tags', $instruction, $item, $naps, $vd1, $vd2);
		
		$summ_to_pay = apply_filters('summ_to_pay', is_my_money($item->summ1_dc), $m_id , $item, $naps);
	
		$timeline = trim(get_naps_txtmeta($naps_id, 'timeline_txt'));
		$timeline_nodescr = intval($premiumbox->get_option('naps_nodescr', 'timeline_txt'));
		if($timeline_nodescr == 1 and !$timeline or $timeline_nodescr == 2){
			$timeline = trim($premiumbox->get_option('naps_temp', 'timeline_txt'));
		}	
		$timeline = ctv_ml($timeline);
		$timeline = apply_filters('naps_instruction', $timeline, $naps, $vd1, $vd2);
		if($timeline){
			$timeline = '
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
							'. apply_filters('comment_text',$timeline) .'
						</div>
					</div>
				</div>
			</div>';	
		}	
		
		$instruct = '';
		if($instruction){
			$instruct = '
			<div class="block_statusnew_instruction">
				'. apply_filters('comment_text',$instruction) .'
			</div>			
			';
		}	
		
		$date_format = get_option('date_format');
		$time_format = get_option('time_format');		
		$status_date = get_mytime($item->createdate, "{$date_format}, {$time_format}");
	
		$action_or_error = '';
	
		if(is_true_userhash($item)){
			
			$pay_button_visible = apply_filters('merchant_pay_button_visible', 1, $m_id, $item, $naps, $vd1, $vd2);
			$action_or_error = '
			<div class="block_paybutton">
				<div class="block_paybutton_ins">';
								
					$action_or_error .= '<a href="'. get_ajax_link('canceledbids') .'&hash='. is_bid_hash($item->hashed) .'" class="cancel_paybutton">'. __('Cancel a order','pn') .'</a>';
							
					if($pay_button_visible == 1){
						if($m_id){		
							$merchant_pay_button = '<a href="'. get_ajax_link('payedmerchant') .'&hash='. is_bid_hash($item->hashed) .'" target="_blank" class="success_paybutton">'. __('Make a payment','pn') .'</a>';
							$action_or_error .= apply_filters('merchant_pay_button_'.$m_id, $merchant_pay_button, $summ_to_pay, $item, $naps);		
						} else {
							$action_or_error .= '<a href="'. get_ajax_link('payedbids') .'&hash='. is_bid_hash($item->hashed) .'" class="success_paybutton iam_pay_bids">'. __('Paid','pn') .'</a>';
						}
					}
							
					$action_or_error .= '
						<div class="clear"></div>
				</div>
			</div>
			';
				
		} else {
					
			$action_or_error = '
			<div class="block_change_browse">
				<div class="block_change_browse_ins">	
					<p>'. __('Error! You cannot control this order in another browser','pn') .'</p>	
				</div>
			</div>					
			';
					
		}	
		
		$autocheck = apply_filters('merchant_formstep_autocheck', 0, $m_id);
		$autocheck_html = '';
		if($autocheck and !$js_autocheck){
			if(isset($_GET['auto_check']) or isset($_POST['auto_check'])){	

				$autocheck_html .= '
				<div class="block_check_payment check_payment_hash" data-time="30" data-hash="'. is_bid_hash($item->hashed) .'">
					<div class="block_check_payment_abs"></div>
					<div class="block_check_payment_ins"></div>
				</div>
					
				<div class="block_warning_merch">
					<div class="block_warning_merch_ins">
						<p>'. sprintf(__('We check the payment every %s seconds.','pn'), 30) .'</p>
					</div>
				</div>				
					
				<div class="block_paybutton_merch">
					<div class="block_paybutton_merch_ins">	
						<a href="'. get_bids_url($item->hashed) .'" class="merch_paybutton">'. __('Cancel','pn') .'</a>		
					</div>
				</div>					
				';						
						
			} else {
						
				$autocheck_html .= '
				<div class="block_warning_merch">
					<div class="block_warning_merch_ins">
						
						<p>'. __('Attention! Click "Check payment", if you have aready paid the order.','pn') .'</p>
						<p>'. sprintf(__('Then the check will be automatically performed every %s seconds.','pn'), 30) .'</p>
						
					</div>
				</div>
					
				<div class="block_paybutton_merch">
					<div class="block_paybutton_merch_ins">
								
						<a href="'. get_bids_url($item->hashed) .'?auto_check=true" class="merch_paybutton">'. __('Verify a payment','pn') .'</a>
								
					</div>
				</div>					
				';										
						
			}
		}		
	
		$array = array(
			'[timeline]' => $timeline,
			'[status]' => $status,
			'[status_title]' => $status_title,
			'[summ_to_pay]' => $summ_to_pay,
			'[instruction]' => $instruct,
			'[status_date]' => $status_date,
			'[status_text]' => $status_text,
			'[ps_give]' => pn_strip_input(ctv_ml($item->valut1)),
			'[ps_get]' => pn_strip_input(ctv_ml($item->valut2)),
			'[action_or_error]' => $action_or_error,
			'[merchant_action]' => apply_filters('merchant_formstep_after', '', $m_id, $item, $naps),
			'[autocheck]' => $autocheck_html,	
			'[vtype_give]' => pn_strip_input($item->vtype1),
			'[vtype_get]' => pn_strip_input($item->vtype2),
		);
		$array = apply_filters('exchangestep_'. $status .'_html_list', $array, $item, $naps, $vd1, $vd2);
		$array = apply_filters('exchangestep_all_html_list', $array, $item, $naps, $vd1, $vd2);
	
		$html = '
			[timeline]
			<div class="block_statusbids block_status_statusnew">
				<div class="block_statusbids_ins block_status_statusnew_ins">
					<div class="block_statusbid_title">
						<div class="block_statusbid_title_ins">
							<span>[status_title]</span>
						</div>
					</div>
					<div class="block_payinfo">
						<div class="block_payinfo_ins">	
							[instruction]	

							<div class="block_statusnew_sum">
								<p><strong>'. __('Amount of payment','pn') .':</strong> [summ_to_pay] <span class="ps">[ps_give] [vtype_give]</span></p>
							</div>
							<div class="block_statusnew_warning">
								<span class="red">'. __('Please be careful!','pn') .'</span> '. __('All fields must be filled in accordance with the instructions. Otherwise, the payment may be cancelled.','pn') .' 
							</div>			
						</div>
					</div>			
					<div class="block_status">
						<div class="block_status_ins">
							<div class="block_status_time"><span>'. __('Creation time','pn') .':</span> [status_date]</div>
							<div class="block_status_text"><span class="block_status_text_info">'. __('Status of order','pn') .':</span> <span class="block_status_bids bidstatus_new">[status_text]</span></div>
						</div>
					</div>
				
					[action_or_error]
					[merchant_action]
					[autocheck]
				</div>
			</div>		
		';
		
		if($js_autocheck){
			$temp .= '<div class="check_payment_hash" data-time="30" data-hash="'. is_bid_hash($item->hashed) .'"></div>';
		}		
		
		$html = apply_filters('exchangestep_'. $status .'_html', $html, $item, $naps, $vd1, $vd2);
		$html = apply_filters('exchangestep_all_html', $html, $item, $naps, $vd1, $vd2);
		$temp .= get_replace_arrays($array, $html);		
		
	}
	
	return $temp;
}
/* end new */							

add_filter('exchangestep_all','get_exchangestep_all',1,3);
function get_exchangestep_all($temp, $status, $item){
global $wpdb, $premiumbox;
	
	$temp = '';
	
	$not_status = array('auto','new','techpay');
	
	if(isset($item->id) and !in_array($status, $not_status)){
		
		$naps_id = intval($item->naps_id);
		$naps = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."naps WHERE naps_status='1' AND autostatus='1' AND id='$naps_id'");
		$m_id = apply_filters('get_paymerchant_id',0, is_isset($item,'m_out'), $item);
		
		$valut1 = intval($item->valut1i);
		$valut2 = intval($item->valut2i);
		
		$vd1 = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."valuts WHERE id='$valut1'");
		$vd2 = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."valuts WHERE id='$valut2'");	
	
		$status = is_status_name($item->status);
		$js_autocheck = intval($premiumbox->get_option('naps_timer', 'status_'.$status));
		$status_text = pn_strip_text(ctv_ml($premiumbox->get_option('naps_status', 'status_'.$status)));
		$status_title = pn_strip_input(ctv_ml($premiumbox->get_option('naps_title', 'status_'.$status)));		
		$pay_instruction = 0;
		
		if($status == 'realpay'){
			$pay_instruction = 1;						
		} elseif($status == 'verify'){
			$pay_instruction = 1;							
		} 		
		
		$def_status = array(
			'coldpay' => array(
				'title' => __('Waiting for merchant confirmation','pn'),
				'text' => __('Waiting for merchant confirmation','pn'),
			),		
			'payed' => array(
				'title' => __('Order is paid','pn'),
				'text' => __('Received confirmation of payment from the client','pn'),
			),
			'verify' => array(
				'title' => __('Order is paid','pn'),
				'text' => __('Order is paid','pn'),
			),
			'realpay' => array(
				'title' => __('Order is paid','pn'),
				'text' => __('Order is paid','pn'),
			),			
			'delete' => array(
				'title' => __('The order is deleted','pn'),
				'text' => __('The order is deleted','pn'),
			),
			'cancel' => array(
				'title' => __('Refusal of payment','pn'),
				'text' => __('User refused to make a payment','pn'),
			),				
			'error' => array(
				'title' => __('Error','pn'),
				'text' => __('Error','pn'),
			),
			'coldsuccess' => array(
				'title' => __('Waiting for automatic payments module confirmation','pn'),
				'text' => __('Waiting for automatic payments module confirmation','pn'),
			),
			'success' => array(
				'title' => __('The order is completed','pn'),
				'text' => __('The order is completed','pn'),
			),	
		);		
		
		if(!$status_text and isset($def_status[$status]['text'])){
			$status_text = $def_status[$status]['text'];
		}
		
		if(!$status_title and isset($def_status[$status]['title'])){
			$status_title = $def_status[$status]['title'];
		}		
		
		$instruction = trim(get_naps_txtmeta($naps_id, 'status_'.$status));
		$naps_nodescr = intval($premiumbox->get_option('naps_nodescr', 'status_'.$status));
		if($naps_nodescr == 1 and !$instruction or $naps_nodescr == 2){
			$instruction = trim($premiumbox->get_option('naps_temp', 'status_'.$status));
		}			
		if($pay_instruction == 1){
			$instruction = apply_filters('instruction_paymerchant',$instruction,$m_id, $item, $naps, $vd1, $vd2);
		}
		$instruction = ctv_ml($instruction);
		$instruction = apply_filters('bid_instruction_tags', $instruction, $item, $naps, $vd1, $vd2);	
		
		$instruct = '';
		if($instruction){
			$instruct = '
			<div class="block_instruction_text">
				'. apply_filters('comment_text',$instruction) .'
			</div>			
			';
		}

		$date_format = get_option('date_format');
		$time_format = get_option('time_format');		
		$status_date = get_mytime($item->createdate, "{$date_format}, {$time_format}");		
		
		$account_give = $account_get = '';
		if($item->account1){
			$txt = pn_strip_input(ctv_ml(is_isset($vd1,'txt1')));
			if(!$txt){ $txt = __('From account','pn'); }
			$account = $item->account1;
			$account = apply_filters('show_user_account', $account, $item, $naps, $vd1);						
			$account_give = ', <span>'. $txt .'</span>: '. get_secret_value($item->account1, $premiumbox->get_option('exchange','an1_hidden'));
		}	

		if($item->account2){
			$txt = pn_strip_input(ctv_ml(is_isset($vd2,'txt2')));
			if(!$txt){ $txt = __('Into account','pn'); }
			$account = $item->account2;
			$account = apply_filters('show_user_account', $account, $item, $naps, $vd2);									
			$account_get = ', <span>'. $txt .'</span>: '. get_secret_value($item->account2, $premiumbox->get_option('exchange','an2_hidden'));
		}		
		
		$array = array(
			'[status]' => $status,
			'[status_title]' => $status_title,
			'[instruction]' => $instruct,
			'[status_date]' => $status_date,
			'[status_text]' => $status_text,
			'[ps_give]' => pn_strip_input(ctv_ml($item->valut1)),
			'[ps_get]' => pn_strip_input(ctv_ml($item->valut2)),
			'[sum_give]' => pn_strip_input($item->summ1c),
			'[sum_get]' => pn_strip_input($item->summ2c),
			'[vtype_give]' => is_site_value($item->vtype1),
			'[vtype_get]' => is_site_value($item->vtype2),			
			'[account_give]' => $account_give,
			'[account_get]' => $account_get,	
		);
		$array = apply_filters('exchangestep_'. $status .'_html_list', $array, $item, $naps, $vd1, $vd2);
		$array = apply_filters('exchangestep_all_html_list', $array, $item, $naps, $vd1, $vd2);
		
		$html = '
		<div class="block_statusbids block_status_[status]">
			<div class="block_statusbids_ins">
		
				<div class="block_statusbid_title">
					<div class="block_statusbid_title_ins">
						<span>[status_title]</span>
					</div>
				</div>
			
				[instruction]		

				<div class="block_payinfo">
					<div class="block_payinfo_ins">
						<div class="block_payinfo_line">
							<span>'. __('Send','pn') .':</span> [sum_give] [ps_give] [vtype_give] [account_give]
						</div>
						<div class="block_payinfo_line">
							<span>'. __('Receive','pn') .':</span> [sum_get] [ps_get] [vtype_get] [account_get]
						</div>						
					</div>
				</div>					
				
				<div class="block_status">
					<div class="block_status_ins">
						<div class="block_status_time"><span>'. __('Creation time','pn') .':</span> [status_date]</div>
						<div class="block_status_text"><span class="block_status_text_info">'. __('Status of order','pn') .':</span> <span class="block_status_bids bstatus_[status]">[status_text]</span></div>
					</div>
				</div>		
			</div>
		</div>		
		';
		
		$html = apply_filters('exchangestep_'. $status .'_html', $html, $item, $naps, $vd1, $vd2);
		$html = apply_filters('exchangestep_all_html', $html, $item, $naps, $vd1, $vd2);
		if($js_autocheck){
			$temp .= '<div class="check_payment_hash" data-time="30" data-hash="'. is_bid_hash($item->hashed) .'"></div>';
		}
		$temp .= get_replace_arrays($array, $html);				
		
	}
	
	return $temp;
}