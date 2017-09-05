<?php
if( !defined( 'ABSPATH')){ exit(); }

add_filter('user_mailtemp','user_mailtemp_lostpass');
function user_mailtemp_lostpass($places_admin){
	
	$places_admin['lostpassform'] = __('Lost password form','pn');
	
	return $places_admin;
}

add_filter('mailtemp_tags_lostpassform','def_mailtemp_tags_lostpassform');
function def_mailtemp_tags_lostpassform($tags){
	
	$tags['link'] = __('Link','pn');
	
	return $tags;
}


function lostpass_page_shortcode($atts, $content) {
global $wpdb;

	$temp = '';
	
    $temp .= apply_filters('before_lostpass_page','');
			
 	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);			
			
	if(!$user_id){

		$maction = pn_strip_input(is_param_get('maction'));
		$mkey = pn_strip_input(is_param_get('mkey'));
		$mid = pn_strip_input(is_param_get('mid'));
	
		if($maction == 'rp' and $mkey and $mid) {
			
			$items = get_lostpass2_form_filelds();
			$html = prepare_form_fileds($items, 'lostpass2_form_line', 'lp');	

			$array = array(
				'[form]' => '
				<form method="post" class="ajax_post_form" action="'. get_ajax_link('lostpass2') .'">
					<input type="hidden" name="action" value="'. $maction .'" />
					<input type="hidden" name="key" value="'. $mkey .'" />
					<input type="hidden" name="id" value="'. $mid .'" />
				',
				'[/form]' => '</form>',
				'[result]' => '<div class="resultgo"></div>',
				'[html]' => $html,
				'[submit]' => '<input type="submit" formtarget="_top" name="submit" class="lp_submit" value="'. __('Save', 'pn') .'" />',
			);	

			$temp_form = '
			<div class="lp_div_wrap">
			[form]
				
				<div class="lp_div_title">
					<div class="lp_div_title_ins">
						'. __('Password recovery','pn') .'
					</div>
				</div>
			
				<div class="lp_div">
					<div class="lp_div_ins">
						
						[html]
						
						<div class="lp_line has_submit">
							[submit]
						</div>
						
						[result]
						
					</div>
				</div>

			[/form]
			</div>
			';
	
			$temp_form = apply_filters('lostpass2_form_temp',$temp_form);
			$temp .= get_replace_arrays($array, $temp_form);			
			
		} else {

			$items = get_lostpass1_form_filelds();
			$html = prepare_form_fileds($items, 'lostpass1_form_line', 'lp');	

			$array = array(
				'[form]' => '<form method="post" class="ajax_post_form" action="'. get_ajax_link('lostpass1') .'">',
				'[/form]' => '</form>',
				'[result]' => '<div class="resultgo"></div>',
				'[html]' => $html,
				'[submit]' => '<input type="submit" formtarget="_top" name="submit" class="lp_submit" value="'. __('Reset password', 'pn') .'" />',
			);	

			$temp_form = '
			<div class="lp_div_wrap">
			[form]
				
				<div class="lp_div_title">
					<div class="lp_div_title_ins">
						'. __('Password recovery','pn') .'
					</div>
				</div>
			
				<div class="lp_div">
					<div class="lp_div_ins">
						
						[html]
						
						<div class="lp_line has_submit">
							[submit]
						</div>
						
						[result]
						
					</div>
				</div>

			[/form]
			</div>
			';
	
			$temp_form = apply_filters('lostpass1_form_temp',$temp_form);
			$temp .= get_replace_arrays($array, $temp_form);			
			
		}		

	} else {
		$temp .= '<div class="resultfalse">'. __('Error! This form is available for unauthorized users only','pn') .'</div>';
	}	
	
    $after = apply_filters('after_lostpass_page','');
    $temp .= $after;	
	
	return $temp;
}
add_shortcode('lostpass_page', 'lostpass_page_shortcode');

/* обработка формы сайта */
add_action('myaction_site_lostpass1', 'def_myaction_ajax_lostpass1');
function def_myaction_ajax_lostpass1(){
global $or_site_url, $user_ID, $wpdb, $premiumbox;	
	
	only_post();

	$log = array();	
	$log['response'] = '';
	$log['status'] = '';
	$log['status_code'] = 0;
	$log['status_text'] = '';
	
	$premiumbox->up_mode();
	
	$log = apply_filters('before_ajax_form_field', $log, 'lostpass1form');
	$log = apply_filters('before_ajax_lostpass1', $log);
	
	if($user_ID){
		$log['status'] = 'error'; 
		$log['status_code'] = 1;
		$log['status_text']= __('Error! This form is available for unauthorized users only','pn');
		echo json_encode($log);
		exit;		
	}
	
	$email = is_email(is_param_post('email'));
	if($email){
		$user_id = email_exists($email);
		if ($user_id){
			
			$ui = get_userdata($user_id);
			$sec_lostpass = is_isset($ui,'sec_lostpass');
		    if($sec_lostpass == 1){
				
		        $admin_password = wp_generate_password( 20 , false, false);
				$ad_hash = pn_crypt_data($admin_password);
				
		        $wpdb->query("UPDATE ".$wpdb->prefix."users SET user_activation_key = '$ad_hash' WHERE user_email = '$email'");
	            
					$mailtemp = get_option('mailtemp');
					if(isset($mailtemp['lostpassform'])){
						$data = $mailtemp['lostpassform'];
						if($data['send'] == 1){
							$ot_mail = is_email($data['mail']);
							$ot_name = pn_strip_input($data['name']);
						
							$sitename = pn_strip_input(get_bloginfo('sitename'));
						
							$subject = pn_strip_input(ctv_ml($data['title']));
						
							$html = pn_strip_text(ctv_ml($data['text']));
						
							$link = $premiumbox->get_page('lostpass'). '?maction=rp&mid='. $user_id .'&mkey='. $admin_password;
							$link = apply_filters('lostpass_remind_link', $link, $user_id, $admin_password);
						
							$to_mail = $email;
						
							$sarray = array(
								'[sitename]' => $sitename,
							);							
							$subject = get_replace_arrays($sarray, $subject);						
							$subject = apply_filters('mail_lostpassform_subject',$subject);
							
							$sarray = array(
								'[sitename]' => $sitename,
								'[link]' => $link,
							);							
							$html = get_replace_arrays($sarray, $html);							
							$html = apply_filters('mail_lostpassform_text',$html);
							$html = apply_filters('comment_text',$html);
						
							pn_mail($to_mail, $subject, $html, $ot_name, $ot_mail);	 
							
						}
					}

					$log['status'] = 'success_clear';
					$log['status_text'] = apply_filters('ajax_lostpass1_success_message', __('Confirmation e-mail is sent you','pn'));					
	   
			} else {
				$log['status'] = 'error';
				$log['status_code'] = 1;
				$log['status_text'] = __('Error! Password recovery is disabled','pn');
			}	   
		} else {
			$log['status'] = 'error';
			$log['status_code'] = 1;
			$log['status_text'] = __('Error! This e-mail is not registered','pn');
		}
	}  else {
		$log['status'] = 'error';
		$log['status_code'] = 1;
		$log['status_text'] = __('Error! You have entered an incorrect e-mail','pn');
	}	
		
	echo json_encode($log);	
	exit;
}
/* end обработка формы сайта */

/* обработка формы сайта */
add_action('myaction_site_lostpass2', 'def_myaction_ajax_lostpass2');
function def_myaction_ajax_lostpass2(){
global $or_site_url, $user_ID, $wpdb, $premiumbox;	
	
	only_post();

	$log = array();	
	$log['response'] = '';
	$log['status'] = '';
	$log['status_code'] = 0;
	$log['status_text'] = '';
	
	$premiumbox->up_mode();
	
	$log = apply_filters('before_ajax_form_field', $log, 'lostpass2form');
	$log = apply_filters('before_ajax_lostpass2', $log);
	
	if($user_ID){
		$log['status'] = 'error'; 
		$log['status_code'] = 1;
		$log['status_text']= __('Error! This form is available for unauthorized users only','pn');
		echo json_encode($log);
		exit;		
	}
	
	$action = pn_strip_input(is_param_post('action'));
	$key = pn_strip_input(is_param_post('key'));
	$user_id = intval(is_param_post('id'));
	$pass = is_password(is_param_post('pass'));
	$pass2 = is_password(is_param_post('pass2'));
	
	if(preg_match("/^[a-zA-z0-9]{0,150}$/", $key) and $action == 'rp'){
		if($pass and $pass == $pass2){
			$password = wp_hash_password($pass);
			$ui = get_userdata($user_id);
		    if(isset($ui->sec_lostpass) and $ui->sec_lostpass == 1){				
				
				$user = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."users WHERE ID='$user_id'");
				if(isset($user->ID)){
					if(is_pn_crypt($user->user_activation_key, $key)){
					
						$wpdb->query("UPDATE ".$wpdb->prefix."users SET user_pass = '$password', user_activation_key = '' WHERE ID = '$user_id'");
				
						$log['url'] = $link = apply_filters('lostpass_login_redirect', $premiumbox->get_page('login'));
						$log['status'] = 'success_clear';
						$log['status_text'] = apply_filters('ajax_lostpass2_success_message', __('Password successfully changed','pn'));					

					} else {
						$log['status'] = 'error';
						$log['status_code'] = 1;
						$log['status_text'] = __('Error! System error 2','pn');						
					}
				} else {
					$log['status'] = 'error';
					$log['status_code'] = 1;
					$log['status_text'] = __('Error! Password is incorrect or does not match with the previously entered password','pn');					
				}
				
			} else {
				$log['status'] = 'error';
				$log['status_code'] = 1;
				$log['status_text'] = __('Error! Password recovery is disabled','pn');				
			}
		} else {
			$log['status'] = 'error';
			$log['status_code'] = 1;
			$log['status_text'] = __('Error! Password is incorrect or does not match with the previously entered password','pn');
		}
	}  else {
		$log['status'] = 'error';
		$log['status_code'] = 1;
		$log['status_text'] = __('Error! System error 1','pn');
	}	
		
	echo json_encode($log);	
	exit;
}
/* end обработка формы сайта */