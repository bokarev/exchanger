<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Подтверждение регистрации по e-mail[:ru_RU][en_US:]Registration confirmation by e-mail[:en_US]
description: [ru_RU:]Подтверждение регистрации по e-mail[:ru_RU][en_US:]Registration confirmation by e-mail[:en_US]
version: 1.0
category: [ru_RU:]Безопасность[:ru_RU][en_US:]Security[:en_US]
cat: secur
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* BD */
add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_rconfirm');
function bd_pn_moduls_active_rconfirm(){
global $wpdb;	
	
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."users LIKE 'rconfirm'");
	if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."users ADD `rconfirm` int(1) NOT NULL default '1'");
	}	
}
/* end BD */

add_action( 'pn_user_register', 'pn_user_register_rconfirm');
function pn_user_register_rconfirm($user_id) {
global $wpdb;
	
	$array = array();
	$array['rconfirm'] = 0;
	$wpdb->update($wpdb->prefix ."users", $array, array('ID'=>$user_id));
			
}

add_action( 'profile_update', 'profile_update_rconfirm');
function profile_update_rconfirm($user_id){
global $change_ld_account, $wpdb;

	$change_ld_account = 'true';
	
	if(isset($_POST['pn_profile_rconfirm'])){
		if(current_user_can('administrator')){ 
				
			$array = array();
			$array['rconfirm'] = intval(is_param_post('rconfirm'));
			$wpdb->update($wpdb->prefix ."users", $array, array('ID'=>$user_id));
			
		}
	}	
}

/* стандартные поля при редактировании пользователя */

add_action( 'show_user_profile', 'pn_edit_user_rconfirm');
add_action( 'edit_user_profile', 'pn_edit_user_rconfirm');
function pn_edit_user_rconfirm($user){
	$user_id = $user->ID;
	if(current_user_can('administrator')){ 
		$rconfirm = intval(is_isset($user,'rconfirm'));
		?>
		<input type="hidden" name="pn_profile_rconfirm" value="1" />
		
	    <table class="form-table">
			<tr>
				<th>
					<label for="rconfirm"><?php _e('Registration confirmation by e-mail','pn'); ?>:</label>
				</th>
				<td>
					<select name="rconfirm" id="rconfirm">
						<option value="0"><?php _e('No','pn'); ?></option>
						<option value="1" <?php selected($rconfirm, 1); ?>><?php _e('Yes','pn'); ?></option>
					</select>
				</td>
			</tr>			
        </table>		
		<?php
	}
}

add_filter( 'authenticate', 'rconfirm_login_check', 99, 1);
function rconfirm_login_check($user){
global $wpdb;

	if(is_object($user) and isset($user->data->ID)){
		if(!user_can($user, 'administrator')){
			$rconfirm = intval($user->data->rconfirm);
			if($rconfirm != 1){	
		
				$error = new WP_Error();
				$error->add( 'pn_error',__('You did not confirm your e-mail','pn'));
				wp_clear_auth_cookie();
							
				return $error;							
			}
		}
	}
		
	return $user;
}

add_action('init', 'init_rconfirm', 11);
function init_rconfirm(){
	global $or_site_url;
	$ui = wp_get_current_user();
	$user_id = intval(is_isset($ui, 'ID'));
	if($user_id){
		if(!user_can($ui, 'administrator')){
			$rconfirm = intval($ui->rconfirm);
			if($rconfirm != 1){
				wp_logout();
				wp_redirect($or_site_url);
				exit();
			}	
		}
	}
}

add_filter('mailtemp_tags_autoregisterform','rconfirm_mailtemp_tags', 100);
add_filter('mailtemp_tags_registerform','rconfirm_mailtemp_tags', 100);
function rconfirm_mailtemp_tags($tags){
	
	$tags['confirm_link'] = __('Link for e-mail confirmation','pn');
	
	return $tags;
}

add_filter('mail_autoregisterform_text', 'rconfirm_mail_text', 10, 2);
add_filter('mail_registerform_text', 'rconfirm_mail_text', 10, 2);
function rconfirm_mail_text($html, $user_id){
	
	$hash = wp_generate_password( 30 , false, false);
	$link = get_ajax_link('rconfirm', 'get') . '&user=' . $user_id . '&hash='. $hash;
	update_user_meta( $user_id, 'rconfirm_hash', $hash) or add_user_meta($user_id, 'rconfirm_hash', $hash, true);
	$html = str_replace('[confirm_link]',$link, $html);
	
	return $html;
}

add_action('myaction_site_rconfirm', 'def_myaction_ajax_rconfirm');
function def_myaction_ajax_rconfirm(){
global $wpdb, $user_ID, $or_site_url, $premiumbox;
	if(!$user_ID){
		$user_id = intval(is_param_get('user'));
		$hash = trim(is_param_get('hash'));
		if($user_id and $hash){
			$user_hash = get_user_meta($user_id, 'rconfirm_hash', true);
			if($hash == $user_hash){
				$wpdb->update($wpdb->prefix."users", array('rconfirm'=> 1), array('ID'=>$user_id));
				
				$url = $premiumbox->get_page('login').'?rconfirm=1';
				wp_redirect($url);
				exit;
			}
		}
	}
		pn_display_mess(__('Error! Error of e-mail confirmation','pn'), __('Error! Error of e-mail confirmation','pn'), 'error');
}

add_filter('ajax_register2_success_message','ajax_register2_success_message_rconfirm');
function ajax_register2_success_message_rconfirm($text){
	
	$text = __('You have successfully registered. A confirmation of registration has been sent to your E-mail. Follow the link from the letter and log in to your personal account','pn');
	
	return $text;
}

add_filter('before_login_page','before_login_page_rconfirm', 100);
function before_login_page_rconfirm($html){
	$rconfirm = intval(is_param_get('rconfirm'));
	if($rconfirm == 1){
		$html .= '<div class="resulttrue">'. __('Your e-mail has been successfully confirmed','pn') .'</div>';
	}
	return $html;
}