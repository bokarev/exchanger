<?php
if( !defined( 'ABSPATH')){ exit(); } 

/* добавляем JS */
add_action('siteplace_js','siteplace_js_payouts');
function siteplace_js_payouts(){
global $user_ID;	
	
	if($user_ID){	
?>	
/* payouts */
jQuery(function($){ 
    $(document).on('click', '.delpay_link', function(){
		var thet = $(this);
		if(!thet.hasClass('act')){
		
			var id = $(this).attr('name');
			thet.addClass('act');
			
			var dataString='id=' + id;
			$.ajax({
			type: "POST",
			url: "<?php echo get_ajax_link('delete_payoutlink');?>",
			dataType: 'json',
			data: dataString,
			error: function(res, res2, res3){
				<?php do_action('pn_js_error_response', 'ajax'); ?>
			},		
			success: function(res)
			{
				if(res['status'] == 'success'){
					window.location.href = res['url'];
				} 
				if(res['status'] == 'error'){
					<?php do_action('pn_js_alert_response'); ?>
				}
				thet.removeClass('act');
			}
			});
		
		}
	
        return false;
    });
});	
/* end payouts */	
<?php	
	}
} 
/* end добавляем JS */

/* шорткод выплат */
function payouts_page_shortcode($atts, $content) {
global $wpdb, $premiumbox;
	
	$temp = '';
	
	$temp .= apply_filters('before_payouts_page','');
	
	$pages = $premiumbox->get_option('partners','pages');
	if(!is_array($pages)){ $pages = array(); }	
	if(in_array('payouts',$pages)){	
	
		$ui = wp_get_current_user();
		$user_id = intval($ui->ID);	
		
		if($user_id){
			
			$minpay = is_my_money($premiumbox->get_option('partners','minpay'),2);
			$balans = get_partner_money_now($user_id);
			if($balans >= $minpay){
				$dbalans = $balans;
				$dis = '';
			} else {
				$dbalans = 0;
				$dis = 'disabled="disabled"';
			}		
			
			$cur_type = cur_type();
			
			$ptext = pn_strip_text(ctv_ml($premiumbox->get_option('partners','payouttext')));
			if(!$ptext){ $ptext = sprintf(__('Minimum withdrawal amount is <span class="red">%1$s %2$s</span>. All payments to be done right after admin verifies your account. Actually it takes less than 24 hours after submitting withdrawal request.','pn'), '[minpay]', '[currency]'); }
			
			$ptext = str_replace('[minpay]', $minpay, $ptext);
			$ptext = str_replace('[currency]', $cur_type,$ptext);
			
			$paytext = '
			<div class="paytext">
				<div class="paytext_ins">
					'. $ptext .'
				</div>
			</div>
			';
			
			$valuts_html = '
			<select name="valut_id" id="pay_valut_id" autocomplete="off">
				<option value="0">--'. __('No item','pn') .'--</option>';		
				$valuts = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."valuts WHERE valut_status='1' AND pvivod='1' ORDER BY psys_title ASC");	
				foreach($valuts as $item){ 
					$reserv = $item->valut_reserv; 
					$payout_com = $item->payout_com;
					$paysum = is_my_money(convert_sum($dbalans, $cur_type, $item->vtype_title));
					if($reserv >= $paysum){			
						$valuts_html .= '
						<option value="'. $item->id .'">'. sum_after_comis($paysum, $payout_com) .' '. get_valut_title($item) .' ('. __('Fee of payment system for payout of funds to partner','pn') .' - '. $payout_com .'%)</option>
						';			
					}
				}							
			$valuts_html .= '
			</select>';

			$lists = array(
				'before' => '<table>',
				'after' => '</table>',
				'before_head' => '<thead><tr>',
				'after_head' => '</tr></thead>',
				'head_line' => '<th class="th_[key]">[title]</th>',
				'before_body' => '<tbody>',
				'after_body' => '</tbody>',
				'body_line' => '<tr>[html]</tr>',
				'body_item' => '<td class="td_[key] [odd_even]">[content]</td>',
				'lists' => array(
					'pay_date' => __('Date','pn'),
					'pay_account' => __('Wallet','pn'),
					'pay_sum' => __('Amount','pn'),
					//'pay_sum_or' => __('Amount','pn') .'('. $cur_type .')',
					'pay_status' => __('Status','pn'),
					'del_status' => '',
				),
				'noitem' => '<tr><td colspan="[count]"><div class="no_items"><div class="no_items_ins">[title]</div></div></td></tr>',
			);
			$lists = apply_filters('lists_payouts', $lists);
			$lists = (array)$lists;			
			
			$table_list = '';
			
			$head_list = '';
			$c = 0;
			if(is_array($lists['lists'])){
				foreach($lists['lists'] as $key => $title){
					$c++;
					$list = is_isset($lists, 'head_line');
					$list = str_replace('[key]',$key,$list);
					$list = str_replace('[title]',$title,$list);
					$head_list .= $list;
				}
			}
			
			$table_list = is_isset($lists, 'before');
			$table_list .= is_isset($lists, 'before_head');
			$table_list .= $head_list;
			$table_list .= is_isset($lists, 'after_head');
			$table_list .= is_isset($lists, 'before_body');			
			
			$limit = apply_filters('limit_list_payouts', 15);
			$count = $wpdb->query("SELECT ID FROM ".$wpdb->prefix."payoutuser WHERE user_id = '$user_id'");
			$pagenavi = get_pagenavi_calc($limit,get_query_var('paged'),$count);
			
			$datas = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."payoutuser WHERE user_id = '$user_id' ORDER BY pay_date DESC LIMIT ". $pagenavi['offset'] .",".$pagenavi['limit']);		

			$date_format = get_option('date_format');
			$time_format = get_option('time_format');					
				
			$s=0;	
			foreach($datas as $item){  $s++;
				if($s%2==0){ $odd_even = 'even'; } else { $odd_even = 'odd'; }
				
				$one_line = '';
				if(is_array($lists['lists'])){
					foreach($lists['lists'] as $key => $title){
							
						$data_item = '';
						if($key == 'pay_date'){
							$data_item = get_mytime($item->pay_date, "{$date_format}, {$time_format}");
						}
						if($key == 'pay_account'){
							$valut_title = pn_strip_input(ctv_ml($item->psys_title));
							$data_item = '<span class="ptvaluts">'. $valut_title .'</span><br />'. pn_strip_input($item->pay_account);
						}
						if($key == 'pay_sum'){
							$data_item = is_out_sum($item->pay_sum, 12, 'all') .' '. is_site_value($item->vtype_title);
						}
						// if($key == 'pay_sum_or'){
							// $data_item = is_out_sum($item->pay_sum_or, 12, 'all') .' '. $cur_type;
						// }
						$status = $item->status;
						if($key == 'pay_status'){
							$status_title = get_payuot_status($status);
							$pst = $status + 1;
							$data_item = '<div class="paystatus pst'. $pst .'">'. $status_title .'</div>'; 
						}
						if($key == 'del_status'){
							$link = '-';
							if($status == 0){
								$link = '<a href="#" name="'. $item->id .'" class="delpay_link" title="'. __('Cancel payment','pn') .'">'. __('Cancel payment','pn') .'</a>';
							}	
							$data_item = $link; 
						}						
						$data_item = apply_filters('body_list_payouts', $data_item, $item, $key, $title, $date_format, $time_format);
						
						if($data_item){
							$list = is_isset($lists, 'body_item');
							$list = str_replace('[key]',$key,$list);
							$list = str_replace('[title]',$title,$list);
							$list = str_replace('[content]',$data_item,$list);
							$one_line .= $list;
						}	
					}
				}
					
				$body_list_line = is_isset($lists, 'body_line');
				$body_list_line = str_replace('[html]',$one_line,$body_list_line);
				$body_list_line = str_replace('[odd_even]',$odd_even,$body_list_line);
				$table_list .= $body_list_line;				

			}	

			if($count == 0){
				$list = is_isset($lists, 'noitem');
				$list = str_replace('[count]', $c,$list);
				$list = str_replace('[title]',__('No data','pn'),$list);
				$table_list .= $list;
			}
			
			$table_list .= is_isset($lists, 'after_body');
			$table_list .= is_isset($lists, 'after');
			
			$temp_html = '
				[paytext]
				[form]
					<div class="paydiv">
						<div class="paydiv_ins">					
							<div class="pay_left_col">
								'. __('Wallet','pn') .'
							</div>
							<div class="pay_center_col">
								<div class="pay_select">
									[currency]
								</div>
								<div class="pay_input">							
									[account_input]
								</div>
							</div>
							<div class="pay_right_col">
								[submit]
							</div>
								<div class="clear"></div>							
						</div>
					</div>
					[result]
				[/form]
				
				<div class="paytable">
					<div class="paytable_ins">
						<div class="paytable_title">
							<div class="paytable_title_ins">
								'. __('Orders','pn') .'
							</div>
						</div>				
						<div class="payouts_table">
							<div class="payouts_table_ins">
								[table_list] 
							</div>
						</div>	
					</div>
				</div>
				[pagenavi]
			';
			
			$array = array(
				'[form]' => '<form method="post" class="ajax_post_form" action="'. get_ajax_link('payoutform') .'">',
				'[/form]' => '</form>',
				'[currency]' => $valuts_html,
				'[result]' => '<div class="resultgo"></div>',
				'[submit]' => '<input type="submit" formtarget="_top" '. $dis .' value="'. __('Make a request','pn') .'" />',
				'[account_input]' => apply_filters('payouts_input','<input type="text" name="account" id="pay_valut_account" value="" />'),
				'[paytext]' => $paytext,
				'[pagenavi]' => get_pagenavi($pagenavi),
				'[table_list]' => $table_list,
			);
			
			$temp_html = apply_filters('div_list_payouts',$temp_html);
			$temp .= get_replace_arrays($array, $temp_html);			
		
		} else {
			$temp .= '<div class="resultfalse">'. __('Error! Page is available for authorized users only','pn') .'</div>';
		}
	
	} else {
		$temp .= '<div class="resultfalse">'. __('Error! Page is unavailable','pn') .'</div>';
	}		
	
	$after = apply_filters('after_payouts_page','');
	$temp .= $after;

	return $temp;
}
add_shortcode('payouts_page', 'payouts_page_shortcode');
/* end шорткод выплат */

/* обработка формы сайта */
add_action('myaction_site_delete_payoutlink', 'def_myaction_ajax_delete_payoutlink');
function def_myaction_ajax_delete_payoutlink(){
global $wpdb, $premiumbox;	
	
    $log = array();
	$log['response'] = '';
	$log['status'] = '';
	$log['status_text'] = '';
	$log['status_code'] = 0;	
	
	$premiumbox->up_mode();
	
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);	
	
	if(!$user_id){
		$log['status'] = 'error'; 
		$log['status_code'] = 1;
		$log['status_text'] = __('Error! You must authorize','pn');
		echo json_encode($log);
		exit;		
	}
		
	$id = intval(is_param_post('id'));	
	if($id > 0){
		$item = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."payoutuser WHERE user_id = '$user_id' AND status = '0' AND id = '$id'");
		if(isset($item->id)){
			
			do_action('pn_payoutuser_not_before', $id, $item);
			
			$arr = array();
			$arr['status'] = 3;		
			$wpdb->update($wpdb->prefix.'payoutuser', $arr, array('id'=>$item->id));			
			
			do_action('pn_payoutuser_not_after', $id, $item);			
		}
	}
	
	$log['status'] = 'success';
	$log['url'] = apply_filters('payouts_redirect', $premiumbox->get_page('payouts')); 
	echo json_encode($log);
	exit;
}

add_action('myaction_site_payoutform', 'def_myaction_ajax_payoutform');
function def_myaction_ajax_payoutform(){
global $wpdb, $premiumbox;	
	
	only_post();
	
    $log = array();	
	$log['response'] = '';
	$log['status'] = '';
	$log['status_text'] = '';
	$log['status_code'] = 0;	
	
	$premiumbox->up_mode();
	
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);	
	
	if(!$user_id){
		$log['status'] = 'error'; 
		$log['status_code'] = 1;
		$log['status_text'] = __('Error! You must authorize','pn');
		echo json_encode($log);
		exit;		
	}
		
	$minpay = is_my_money($premiumbox->get_option('partners','minpay'),2);
	$balans = get_partner_money_now($user_id);
	if($balans >= $minpay and $balans > 0){  
		$valut_id = intval(is_param_post('valut_id')); if($valut_id < 1){ $valut_id = 0; }	
		$item = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."valuts WHERE valut_status='1' AND pvivod='1' AND id='$valut_id'");	
		if(isset($item->id)){
			$reserv = is_my_money($item->valut_reserv); 
			$payout_com = $item->payout_com;
			$pay_sum = is_my_money(convert_sum($balans, cur_type(), $item->vtype_title));
			if($reserv >= $pay_sum){
				
				$account = pn_strip_input(is_param_post('account'));
				$account = get_purse($account, $item);
				if($account){
				
					$arr = array();
					$arr['pay_date'] = current_time('mysql');
					$arr['user_id'] = $user_id;
					$arr['user_login'] = is_user($ui->user_login);
					$arr['pay_sum'] = sum_after_comis($pay_sum, $payout_com);
					$arr['pay_sum_or'] = $balans;
					$arr['psys_title'] = pn_strip_input($item->psys_title);
					$arr['valut_id'] = $item->id;
					$arr['vtype_id'] = $item->vtype_id;
					$arr['vtype_title'] = is_site_value($item->vtype_title);
					$arr['pay_account'] = $account;
					$arr['status'] = 0;			
					$wpdb->insert($wpdb->prefix.'payoutuser', $arr);				
					$insert_id = $wpdb->insert_id;
				
					$arr['id'] = $insert_id;
					$payoutuser_item = (object)$arr;
						
					do_action('pn_payoutuser_wait_before', $insert_id, $payoutuser_item);	
					do_action('pn_payoutuser_wait_after', $insert_id, $payoutuser_item);	

					$mailtemp = get_option('mailtemp');
					if(isset($mailtemp['payout'])){
						$data = $mailtemp['payout'];
						if($data['send'] == 1){
							$ot_mail = is_email($data['mail']);
							$ot_name = pn_strip_input($data['name']);
							$subject = pn_strip_input(ctv_ml($data['title']));
							$sitename = pn_strip_input(get_bloginfo('sitename'));
							$html = pn_strip_text(ctv_ml($data['text']));
							if($data['tomail']){
								$to_mail = $data['tomail'];
								$sarray = array(
									'[sitename]' => $sitename,
									'[user]' => is_user($ui->user_login),
									'[sum]' => $arr['pay_sum'] .' '. get_valut_title($item),
								);							
								$subject = get_replace_arrays($sarray, $subject);								
								$subject = apply_filters('mail_payout_subject',$subject);
																
								$html = get_replace_arrays($sarray, $html);									
								$html = apply_filters('mail_payout_text',$html);
								$html = apply_filters('comment_text',$html);
									
								pn_mail($to_mail, $subject, $html, $ot_name, $ot_mail);	
							}
						}
					} 
						
					$log['status'] = 'success';
					$log['status_text'] = __('Payout is successfully requested','pn');
					$log['url'] = apply_filters('payouts_redirect', $premiumbox->get_page('payouts')); 

				} else {
					$log['status'] = 'error';
					$log['status_code'] = 1;
					$log['status_text'] = __('Error! Invalid wallet account','pn');
				}							
			} else {
				$log['status'] = 'error'; 
				$log['status_code'] = 1;
				$log['status_text'] = __('Error! You are unable to make a selected currency transaction','pn');
			}
		} else {
			$log['status'] = 'error'; 
			$log['status_code'] = 1;
			$log['status_text'] = __('Error! Selected currency can not be ordered payment','pn');			
		}	
	} else {
		$log['status'] = 'error'; 
		$log['status_code'] = 1;
		$log['status_text'] = __('Error! There is not enough money on your balance','pn');		
	}		
	
	echo json_encode($log);
	exit;
}
/* end обработка формы сайта */