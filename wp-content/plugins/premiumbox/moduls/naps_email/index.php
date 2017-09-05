<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]E-mail верификация[:ru_RU][en_US:]E-mail verification[:en_US]
description: [ru_RU:]E-mail верификация для полей Со счета, На счет, Номер телефона[:ru_RU][en_US:]Verification through e-mail for From account, To account, Phone number fields[:en_US]
version: 1.0
category: [ru_RU:]Направления обменов[:ru_RU][en_US:]Exchange directions[:en_US]
cat: naps
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

add_filter('user_mailtemp','napsemail_user_mailtemp');
function napsemail_user_mailtemp($places_admin){
	$places_admin['napsemail'] = __('Verification through e-mail settings','pn');
	return $places_admin;
}

add_filter('mailtemp_tags_napsemail','def_mailtemp_tags_napsemail');
function def_mailtemp_tags_napsemail($tags){
	$tags['code'] = __('Code','pn');
	return $tags;
}

add_action('tab_naps_tab8', 'napsemail_tab_naps_tab8', 30, 2);
function napsemail_tab_naps_tab8($data, $data_id){
?>
	<tr>
		<th><?php _e('Verification through e-mail','pn'); ?></th>
		<td>
			<div class="premium_wrap_standart">
				<select name="email_button" autocomplete="off">
					<?php 
					$sms_button = intval(get_naps_meta($data_id, 'email_button')); 
					?>						
					<option value="0" <?php selected($sms_button,0); ?>><?php _e('No','pn');?></option>
					<option value="1" <?php selected($sms_button,1); ?>><?php _e('Yes','pn');?></option>						
				</select>
			</div>
		</td>
		<td>
			<div class="premium_wrap_standart">
				<select name="email_button_verify" autocomplete="off">
					<?php 
					$sms_button_verify = intval(get_naps_meta($data_id, 'email_button_verify')); 
					?>						
					<option value="0" <?php selected($sms_button_verify,0); ?>><?php _e('Default','pn');?></option>
					<option value="1" <?php selected($sms_button_verify,1); ?>><?php _e('Account Send','pn');?></option>
					<option value="2" <?php selected($sms_button_verify,2); ?>><?php _e('Account Receive','pn');?></option>
					<option value="3" <?php selected($sms_button_verify,3); ?>><?php _e('E-mail','pn');?></option>					
				</select>
			</div>
		</td>		
	</tr>	
<?php	
}

add_action('pn_naps_edit_before','pn_naps_edit_napsemail');
add_action('pn_naps_add','pn_naps_edit_napsemail');
function pn_naps_edit_napsemail($data_id){
	
	$button = intval(is_param_post('email_button'));
	update_naps_meta($data_id, 'email_button', $button);
	
	$button_verify = intval(is_param_post('email_button_verify'));
	update_naps_meta($data_id, 'email_button_verify', $button_verify);
	
} 

add_action('admin_menu', 'pn_adminpage_napsemail');
function pn_adminpage_napsemail(){
global $premiumbox;		
	add_submenu_page("pn_moduls", __('Verification through e-mail settings','pn'), __('Verification through e-mail settings','pn'), 'administrator', "pn_napsemail", array($premiumbox, 'admin_temp'));
}

add_action('pn_adminpage_title_pn_napsemail', 'pn_admin_title_pn_napsemail');
function pn_admin_title_pn_napsemail($page){
	_e('Verification through e-mail','pn');
}

add_action('pn_adminpage_content_pn_napsemail','pn_admin_content_pn_napsemail');
function pn_admin_content_pn_napsemail(){
global $wpdb, $premiumbox;

	$options = array();
	$options['top_title'] = array(
		'view' => 'h3',
		'title' => __('Verification through e-mail settings','pn'),
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	$options['vid'] = array(
		'view' => 'select',
		'title' => __('Code type','pn'),
		'options' => array('0'=>__('Digits','pn'),'1'=>__('Letters','pn')),
		'default' => $premiumbox->get_option('napsemail','vid'),
		'name' => 'vid',
		'work' => 'int',
	);	
	$options['sendto'] = array(
		'view' => 'select',
		'title' => __('Send e-mail to','pn'),
		'options' => array('0'=>__('All users','pn'),'1'=>__('Newcomer','pn')),
		'default' => $premiumbox->get_option('napsemail','sendto'),
		'name' => 'sendto',
		'work' => 'int',
	);
	$options['time_check'] = array(
		'view' => 'input',
		'title' => __('Timeout (seconds)','pn'),
		'default' => $premiumbox->get_option('napsemail','time_check'),
		'name' => 'time_check',
		'work' => 'int',
	);	
	$options['max_check'] = array(
		'view' => 'input',
		'title' => __('Max amount of resended e-mail','pn'),
		'default' => $premiumbox->get_option('napsemail','max_check'),
		'name' => 'max_check',
		'work' => 'int',
	);	
	$options['field'] = array(
		'view' => 'select',
		'title' => __('Verification option','pn'),
		'options' => array('0'=>__('Account Send','pn'),'1'=>__('Account Receive','pn'),'2'=>__('E-mail','pn')),
		'default' => $premiumbox->get_option('napsemail','field'),
		'name' => 'field',
		'work' => 'int',
	);	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);			
	pn_admin_one_screen('', $options);  
}  

add_action('premium_action_pn_napsemail','def_premium_action_pn_napsemail');
function def_premium_action_pn_napsemail(){
global $wpdb, $premiumbox;	

	only_post();
	pn_only_caps(array('administrator'));
	
	$options = array();
	$options['vid'] = array(
		'name' => 'vid',
		'work' => 'int',
	);		
	$options['field'] = array(
		'name' => 'field',
		'work' => 'int',
	);							
	$options['sendto'] = array(
		'name' => 'sendto',
		'work' => 'int',
	);	
	$options['max_check'] = array(
		'name' => 'max_check',
		'work' => 'int',
	);
	$options['time_check'] = array(
		'name' => 'time_check',
		'work' => 'int',
	);	
	$data = pn_strip_options('', $options);
	foreach($data as $key => $val){
		$premiumbox->update_option('napsemail', $key, $val);
	}				

	$back_url = is_param_post('_wp_http_referer');
	$back_url .= '&reply=true';
			
	wp_safe_redirect($back_url);
	exit;
}  

function get_napsemail($bid_id){
global $wpdb, $premiumbox;

	$item = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."bids WHERE id='$bid_id' AND status IN('new','techpay')");
	if(isset($item->id)){
		$new_user_option = intval($premiumbox->get_option('napsemail','sendto'));
		if($new_user_option == 0 or isset($item->new_user) and $item->new_user == 1 and $new_user_option == 1){
			$naps_id = $item->naps_id;
			$button = intval(get_naps_meta($naps_id, 'email_button'));
			if($button == 1){
				$word = pn_strip_input(get_bids_meta($bid_id, 'email_word'));
				if(!$word){ /* если нет слова */
					$word = get_rand_word(10, $premiumbox->get_option('napsemail','vid'));
					update_bids_meta($bid_id, 'email_word', $word);
				}
				$field_now = intval(get_naps_meta($naps_id, 'email_button_verify'));
				if($field_now == 0){
					$field = intval($premiumbox->get_option('napsemail','field'));
					if($field == 0){
						$user_email = pn_strip_input($item->account1);
					} elseif($field == 1){
						$user_email = pn_strip_input($item->account2);
					} elseif($field == 2){	
						$user_email = pn_strip_input($item->user_email);
					}
				} elseif($field_now == 1){
					$user_email = pn_strip_input($item->account1);
				} elseif($field_now == 2){
					$user_email = pn_strip_input($item->account2);
				} elseif($field_now == 3){
					$user_email = pn_strip_input($item->user_email);
				}
				
				if(is_email($user_email)){ /* если есть e-mail */
					$time = current_time('timestamp');
					update_bids_meta($bid_id, 'email_checker_time', $time);
					$email_checker_count = intval(get_bids_meta($bid_id, 'email_checker_count')) + 1;
					update_bids_meta($bid_id, 'email_checker_count', $email_checker_count);
				
					$mailtemp = get_option('mailtemp');	
					if(isset($mailtemp['napsemail'])){
						$data = $mailtemp['napsemail'];
						if($data['send'] == 1){
							$ot_mail = is_email($data['mail']);
							$ot_name = pn_strip_input($data['name']);
							$subject = pn_strip_input(ctv_ml($data['title']));
							$html = pn_strip_text(ctv_ml($data['text']));
							$sitename = pn_strip_input(get_bloginfo('sitename'));	
							$sarray = array(
								'[sitename]' => $sitename,
								'[code]' => $word,
							);							
							$subject = get_replace_arrays($sarray, $subject);
							$subject = apply_filters('mail_napsemail_subject',$subject);	
							$sarray = array(
								'[sitename]' => $sitename,
								'[code]' => $word,
							);							
							$html = get_replace_arrays($sarray, $html);
							$html = apply_filters('mail_napsemail_text',$html);
							$html = apply_filters('comment_text',$html);	
							pn_mail($user_email, $subject, $html, $ot_name, $ot_mail);	
						}
					}				
					
					return true;
				}
			}
		}
	} 
		return false;
}

add_filter('merchant_pay_button_visible','napsemail_merchant_pay_button_visible', 0, 4);
function napsemail_merchant_pay_button_visible($ind, $m_id, $item, $naps){
global $premiumbox;	
	if($ind == 1){
		$new_user_option = intval($premiumbox->get_option('napsemail','sendto'));
		if($new_user_option == 0 or isset($item->new_user) and $item->new_user == 1 and $new_user_option == 1){
			$naps_id = $naps->id;
			$bid_id = $item->id;
			$button = intval(get_naps_meta($naps_id, 'email_button'));
			if($button == 1){ /* если включена */
				$checker = intval(get_bids_meta($bid_id, 'email_checker'));
				if($checker != 1){ /* если не чек */
					
					$checker_count = intval(get_bids_meta($bid_id, 'email_checker_count')); /* кол-во отправленных смс */
					if($checker_count < 1){
						get_napsemail($bid_id);
					}
				
					return 0;
				}
			}
		}
	}
	return $ind;
}

add_action('before_bidaction_payedbids', 'napsemail_before_bidaction_payedbids');
function napsemail_before_bidaction_payedbids($item){
global $premiumbox;	
	$new_user_option = intval($premiumbox->get_option('napsemail','sendto'));
	if($new_user_option == 0 or isset($item->new_user) and $item->new_user == 1 and $new_user_option == 1){
		$naps_id = $item->naps_id;
		$bid_id = $item->id;
		$button = intval(get_naps_meta($naps_id, 'email_button'));
		if($button == 1){ /* если включена */
			$checker = intval(get_bids_meta($bid_id, 'email_checker'));
			if($checker != 1){ /* если не чек */
			
				$checker_count = intval(get_bids_meta($bid_id, 'email_checker_count')); /* кол-во отправленных смс */
				if($checker_count < 1){
					get_napsemail($bid_id);
				}				
			
				$url = get_bids_url($item->hashed);
				wp_redirect($url);
				exit;					
			}
		}
	}		
}

add_filter('merchant_formstep_after','napsemail_merchant_formstep_after', 0, 4);
function napsemail_merchant_formstep_after($html, $m_id, $item, $naps){
global $premiumbox;	

	$new_user_option = intval($premiumbox->get_option('napsemail','sendto'));
	if($new_user_option == 0 or isset($item->new_user) and $item->new_user == 1 and $new_user_option == 1){	
		$naps_id = $naps->id;
		$bid_id = $item->id;
		$button = intval(get_naps_meta($naps_id, 'email_button'));
		if($button == 1){ 
			$checker = intval(get_bids_meta($bid_id, 'email_checker'));
			if($checker != 1){ 
				$new_html = '
				<div class="block_smsbutton napsemail_block">
					<div class="block_smsbutton_ins">
						<div class="block_smsbutton_label">
							<div class="block_smsbutton_label_ins">
								'. __('Enter code specified in e-mail:','pn') .'
							</div>
						</div>
						<div class="block_smsbutton_action">
							<input type="text" name="" maxlength="10" placeholder="'. __('Enter code','pn') .'" id="napsemail_text" value="" />
							<input type="submit" name="" data-id="'. $bid_id .'" id="napsemail_send" value="'. __('Confirm code','pn') .'" />
							<input type="submit" name="" data-id="'. $bid_id .'" data-timer="1" disabled="disabled" id="napsemail_reload" value="'. __('Resend','pn') .'" />
								<div class="clear"></div>
						</div>
					</div>
				</div>
				';			
				return $new_html;
			}
		}
	}
	
	return $html;
} 

add_action('siteplace_js','siteplace_js_napsemail');
function siteplace_js_napsemail(){
global $premiumbox;	
	$time_check = intval($premiumbox->get_option('napsemail','time_check'));
	if($time_check < 1){ $time_check = 60; }
?>	
jQuery(function($){ 

	if($('.napsemail_block').length > 0){

		var ch_sec = parseInt('<?php echo $time_check; ?>');
		var now = ch_sec;	
	
		function interval_napsemail(){
			if($('#napsemail_reload').attr('data-timer') == 1){
				if(now > 1){
					now = now - 1;
					$('#napsemail_reload').val('<?php _e('Resend','pn'); ?> ('+ now +')');
				} else {
					$('#napsemail_reload').val('<?php _e('Resend','pn'); ?>');
					$('#napsemail_reload').attr('data-timer', 0);
					$('#napsemail_reload').prop('disabled', false);
				}
			} 
		}
		setInterval(interval_napsemail, 1000);
		
		$(document).on('click', '#napsemail_reload', function(){
			if(!$(this).prop('disabled')){
				
				var id = $(this).attr('data-id');
				var thet = $(this);
				thet.prop('disabled', true);
				var dataString='id=' + id;
				$.ajax({
					type: "POST",
					url: "<?php echo get_ajax_link('resend_napsemail_bids');?>",
					dataType: 'json',
					data: dataString,
					error: function(res, res2, res3){
						<?php do_action('pn_js_error_response', 'ajax'); ?>
					},			
					success: function(res)
					{
						now = ch_sec;
						if(res['status'] == 'success'){
							now = ch_sec;
							$('#napsemail_reload').attr('data-timer', 1);
						} 
						if(res['status'] == 'error'){
							thet.prop('disabled', false);
						}
						<?php do_action('pn_js_alert_response'); ?>
					}
				});
			}
		
			return false;
		});

		$(document).on('click', '#napsemail_send', function(){
			if(!$(this).prop('disabled')){
				
				var id = $(this).attr('data-id');
				var txt = $('#napsemail_text').val();
				var thet = $(this);
				thet.prop('disabled', true);

				var dataString='id=' + id + '&txt=' + txt;
				$.ajax({
					type: "POST",
					url: "<?php echo get_ajax_link('repair_napsemail_bids');?>",
					dataType: 'json',
					data: dataString,
					error: function(res, res2, res3){
						<?php do_action('pn_js_error_response', 'ajax'); ?>
					},			
					success: function(res)
					{
						if(res['status'] == 'success'){
							window.location.href = '';
						} 
						if(res['status'] == 'error'){
							<?php do_action('pn_js_alert_response'); ?>
							thet.prop('disabled', false);
						}
					}
				});
		
			}
		
			return false;
		});		
	
	}
	
});		
<?php	
}  

add_action('myaction_site_repair_napsemail_bids', 'def_myaction_ajax_repair_napsemail_bids');
function def_myaction_ajax_repair_napsemail_bids(){
global $or_site_url, $wpdb, $premiumbox;	
	
	only_post();
	
	$log = array();
	$log['response'] = '';
	$log['status'] = '';
	$log['status_text'] = '';
	$log['status_code'] = 0;
	
	$premiumbox->up_mode();
	
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);	
	$bid_id = intval(is_param_post('id'));
	$txt = strtoupper(is_param_post('txt'));
	if($bid_id and $txt){
		$word = pn_strip_input(get_bids_meta($bid_id, 'email_word'));
		if($word == $txt){
			
			update_bids_meta($bid_id, 'email_checker', 1);
			
			$log['status'] = 'success';
			$log['status_code'] = 0;		
		} else {
			$log['status'] = 'error';
			$log['status_code'] = 1;
			$log['status_text'] = __('You have entered the wrong code','pn');
		}
	} else {
		$log['status'] = 'error';
		$log['status_code'] = 1;
		$log['status_text'] = __('You have not entered the code','pn');		
	}
	
	echo json_encode($log);
	exit;
}

add_action('myaction_site_resend_napsemail_bids', 'def_myaction_ajax_resend_napsemail_bids');
function def_myaction_ajax_resend_napsemail_bids(){
global $or_site_url, $wpdb, $premiumbox;	
	
	only_post();
	
	$log = array();
	$log['response'] = '';
	$log['status'] = '';
	$log['status_text'] = '';
	$log['status_code'] = 0;
	
	$premiumbox->up_mode();
	
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);	
	
	$bid_id = intval(is_param_post('id'));
	if($bid_id){		
		$checker = intval(get_bids_meta($bid_id, 'email_checker'));
		if($checker != 1){ /* если не чек */
			$checker_count = intval(get_bids_meta($bid_id, 'email_checker_count')); /* кол-во отправленных смс */
			$checker_time = get_bids_meta($bid_id, 'email_checker_time'); /* время последней отправки */
			$time = current_time('timestamp');
			$razn = $time - $checker_time; /* кол-во секунд после последней отправки */
			$max_check = intval($premiumbox->get_option('napsemail','max_check'));
			if($max_check < 1){ $max_check = 1; }
			$check_second = intval($premiumbox->get_option('napsemail','time_check'));
			if($check_second < 1){ $check_second = 60; }
			$next = $check_second - $razn;
			if($checker_count < $max_check){
				if($razn > ($check_second-1)){
					if(get_napsemail($bid_id)){
						$log['status'] = 'success';
						$log['status_code'] = 0;
						$log['status_text'] = __('Resent e-mail','pn');							
					} else {
						$log['status'] = 'error';
						$log['status_code'] = 1;
						$log['status_text'] = __('E-mail sending error','pn');							
					}
				} else {
					$log['status'] = 'error';
					$log['status_code'] = 1;
					$log['status_text'] = sprintf(__('Sending is possible not earlier than %1s seconds','pn'), $next);					
				}
			} else {
				$log['status'] = 'error';
				$log['status_code'] = 1;
				$log['status_text'] = sprintf(__('You have been sent the maximum number of e-mail (%1s of %2s)','pn'), $checker_count, $max_check);				
			}
		} else {
			$log['status'] = 'error';
			$log['status_code'] = 1;
			$log['status_text'] = __('Code has already been confirmed. Refresh the page','pn');
		}
	} else {
		$log['status'] = 'error';
		$log['status_code'] = 1;
		$log['status_text'] = __('E-mail sending error','pn');		
	}
	
	echo json_encode($log);
	exit;
}