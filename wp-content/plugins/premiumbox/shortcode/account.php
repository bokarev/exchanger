<?php
if( !defined( 'ABSPATH')){ exit(); }

function account_page_shortcode($atts, $content) {
global $wpdb;

	$temp = '';
	
    $temp .= apply_filters('before_account_page','');
			
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);		
			
	if($user_id){
			
		$items = get_account_form_filelds();
		$html = prepare_form_fileds($items, 'account_form_line', 'acf');		
	
		$array = array(
			'[form]' => '<form method="post" class="ajax_post_form" action="'. get_ajax_link('accountform') .'">',
			'[/form]' => '</form>',
			'[result]' => '<div class="resultgo"></div>',
			'[html]' => $html,
			'[submit]' => '<input type="submit" formtarget="_top" name="submit" class="acf_submit" value="'. __('Save', 'pn') .'" />',
		);	
	
		$temp_form = '
		<div class="acf_div_wrap">
		[form]
			
			<div class="acf_div_title">
				<div class="acf_div_title_ins">
					'. __('Personal data','pn') .'
				</div>
			</div>
		
			<div class="acf_div">
				<div class="acf_div_ins">
					
					[html]
					
					<div class="acf_line has_submit">
						[submit]
					</div>
					
					[result]
				</div>
			</div>

		[/form]
		</div>
		';
	
		$temp_form = apply_filters('account_form_temp',$temp_form);
		$temp .= get_replace_arrays($array, $temp_form);		


	} else {
		$temp .= '<div class="resultfalse">'. __('Error! You must authorize','pn') .'</div>';
	}
	
    $after = apply_filters('after_account_page','');
    $temp .= $after;	
	
	return $temp;
}
add_shortcode('account_page', 'account_page_shortcode');

/* обработка формы сайта */
add_action('myaction_site_accountform', 'def_myaction_ajax_accountform');
function def_myaction_ajax_accountform(){
global $or_site_url, $wpdb, $premiumbox;	
	
	only_post();
	
	$log = array();
	$log['response'] = '';
	$log['status'] = '';
	$log['status_code'] = 0;
	$log['status_text'] = '';
	
	$premiumbox->up_mode();
	
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);	
	
	if(!$user_id){
		$log['status'] = 'error'; 
		$log['status_code'] = 1;
		$log['status_text']= __('Error! You must authorize','pn');
		echo json_encode($log);
		exit;		
	}
	
	$log = apply_filters('before_ajax_form_field', $log, 'accountform');
	$log = apply_filters('before_ajax_accountform', $log, $ui);
		
	$first_name = pn_maxf_mb(pn_strip_input(get_caps_name(is_param_post('first_name'))), 250); 
	$second_name = pn_maxf_mb(pn_strip_input(get_caps_name(is_param_post('second_name'))),250);
	$last_name = pn_maxf_mb(pn_strip_input(get_caps_name(is_param_post('last_name'))),250);

	$user_phone = is_phone(is_param_post('user_phone'));
	$user_skype = pn_maxf_mb(pn_strip_input(is_param_post('user_skype')), 100);
	$user_passport = pn_maxf_mb(pn_strip_input(is_param_post('user_passport')),500);
			
	$email = is_email(is_param_post('user_email'));
	$website = pn_maxf(esc_url(pn_strip_input(is_param_post('website'))),300);		
			
	$old_email = is_email($ui->user_email);		
	$old_website = esc_url($ui->user_url);	
	
	if(pn_allow_uv('website')){
		$disabled = apply_filters('disabled_account_form_line', 0, 'website', $ui);
		if($disabled != 1){
			if($website and $website != $old_website){
				$wpdb->update($wpdb->prefix.'users', array( 'user_url' => $website ), array('ID' => $user_id));
			}
		}
	}

	if(pn_allow_uv('first_name')){
		$disabled = apply_filters('disabled_account_form_line', 0, 'first_name', $ui);
		if($disabled != 1){		
			update_user_meta( $user_id, 'first_name', $first_name) or add_user_meta($user_id, 'first_name', $first_name, true);
		}
	}
	
	if(pn_allow_uv('second_name')){
		$disabled = apply_filters('disabled_account_form_line', 0, 'second_name', $ui);
		if($disabled != 1){		
			update_user_meta( $user_id, 'second_name', $second_name) or add_user_meta($user_id, 'second_name', $second_name, true);
		}
	}
	
	if(pn_allow_uv('last_name')){
		$disabled = apply_filters('disabled_account_form_line', 0, 'last_name', $ui);
		if($disabled != 1){		
			update_user_meta( $user_id, 'last_name', $last_name) or add_user_meta($user_id, 'last_name', $last_name, true);
		}
	}
	
	if(pn_allow_uv('user_passport')){
		$disabled = apply_filters('disabled_account_form_line', 0, 'user_passport', $ui);
		if($disabled != 1){			
			update_user_meta( $user_id, 'user_passport', $user_passport) or add_user_meta($user_id, 'user_passport', $user_passport, true);
		}
	}
	
	if(pn_allow_uv('user_phone')){
		$disabled = apply_filters('disabled_account_form_line', 0, 'user_phone', $ui);
		if($disabled != 1){			
			update_user_meta( $user_id, 'user_phone', $user_phone) or add_user_meta($user_id, 'user_phone', $user_phone, true);
		}
	}
	
	if(pn_allow_uv('user_skype')){
		$disabled = apply_filters('disabled_account_form_line', 0, 'user_skype', $ui);
		if($disabled != 1){			
			update_user_meta( $user_id, 'user_skype', $user_skype) or add_user_meta($user_id, 'user_skype', $user_skype, true);
		}
	}
	
	$errwat = array();
	$error = 0;	
	
	$disabled = apply_filters('disabled_account_form_line', 0, 'user_email', $ui);
	if($disabled != 1){
		if($email){	
			if($email != $old_email){
				if (is_email($email)){
					if (!email_exists($email)) {	
						$wpdb->update($wpdb->prefix.'users', array('user_email' => $email), array('ID'=>$user_id));
					} else {
						$error = $error + 1;
						$errwat[] = __('You have entered an incorrect e-mail','pn');
					}
				} else {
					$error = $error + 1;
					$errwat[] = __('You have entered an incorrect e-mail','pn');
				}
			}
		}
	}
	
	$pass = is_password(is_param_post('pass'));
	$pass2 = is_password(is_param_post('pass2'));	
	
	global $change_ld_account;
	$change_ld_account = 'true';
	
	if($pass){
		if($pass == $pass2){
			wp_set_password($pass, $user_id);
			/* wp_clear_auth_cookie(); */
			$secure_cookie = is_ssl();
			wp_set_auth_cookie($user_id, true, $secure_cookie);
			wp_set_current_user($user_id);
		} else {
			$error = $error+1;
			$errwat[] = __('Passwords do not match','pn');
		}
	} 

	do_action('user_account_post', $user_id, $ui);
	
    if($error > 0){
        $errwat = join('<br />',$errwat);
        $log['status'] = 'error';
		$log['status_code'] = 1;
        $log['status_text'] = $errwat;		
    } else {
        $log['status'] = 'success';
        $log['status_text'] = apply_filters('ajax_account_success_message', __('Data successfully saved','pn'));		
    }		
	
	echo json_encode($log);
	exit;
}
/* end обработка формы сайта */

/* стандартный фильтр */
add_filter('disabled_account_form_line', 'disabled_account_form_line_standart', 9, 3);
function disabled_account_form_line_standart($ind, $name, $ui){
	
	if($ind == 0){
		$value = '';
		if($name == 'last_name'){
			$value = pn_strip_input(is_isset($ui,'last_name'));
		} elseif($name == 'first_name'){
			$value = pn_strip_input(is_isset($ui,'first_name'));
		} elseif($name == 'second_name'){
			$value = pn_strip_input(is_isset($ui,'second_name'));
		} elseif($name == 'user_phone'){
			$value = is_phone(is_isset($ui,'user_phone'));
		} elseif($name == 'user_skype'){
			$value = pn_strip_input(is_isset($ui,'user_skype'));
		} elseif($name == 'user_email'){
			$value = is_email(is_isset($ui,'user_email'));
		} elseif($name == 'website'){
			$value = esc_url(is_isset($ui,'user_url'));
		} elseif($name == 'user_passport'){
			$value = pn_strip_input(is_isset($ui,'user_passport'));
		}
		if(pn_change_uv($name) == 0 and $value){
			return 1;
		}
	}
		return $ind;
}
/* end стандартный фильтр */