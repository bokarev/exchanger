<?php
if( !defined( 'ABSPATH')){ exit(); }

add_filter('user_mailtemp','user_mailtemp_letterauth');
function user_mailtemp_letterauth($places_admin){
	
	$places_admin['letterauth'] = __('Two-factor authorization by one-time ref','pn');
	
	return $places_admin;
}

add_filter('mailtemp_tags_letterauth','def_mailtemp_tags_letterauth');
function def_mailtemp_tags_letterauth($tags){
	
	$tags['link'] = __('Link','pn');
	
	return $tags;
}

add_filter( 'authenticate', 'la_login_check', 100, 1 );
function la_login_check($user){
global $wpdb;

	if(is_object($user) and isset($user->data->ID)){
		if(!defined('PN_ADMIN_GOWP') or defined('PN_ADMIN_GOWP') and constant('PN_ADMIN_GOWP') != 'true'){
			$email_login = $user->data->email_login;
			if($email_login == 1){	
			
				$auto_login1 = wp_generate_password( 30 , false, false);
				$auto_login2 = wp_generate_password( 30 , false, false);
				$al1h = pn_crypt_data($auto_login1);
				$al2h = pn_crypt_data($auto_login2);
				$wpdb->update($wpdb->prefix."users", array('auto_login1'=>$al1h,'auto_login2'=>$al2h), array('ID'=>$user->data->ID));
						
				$link = get_ajax_link('laform', 'get') . '&user=' . $user->data->ID . '&h1='. $auto_login1 .'&h2='. $auto_login2;
						
				$user_email = is_email($user->data->user_email);
				if($user_email){
							
					$mailtemp = get_option('mailtemp');
					if(isset($mailtemp['letterauth'])){
						$data = $mailtemp['letterauth'];
						if($data['send'] == 1){
							$ot_mail = is_email($data['mail']);
							$ot_name = pn_strip_input($data['name']);
							
							$sitename = pn_strip_input(get_bloginfo('sitename'));
							
							$subject = pn_strip_input(ctv_ml($data['title']));
							
							$html = pn_strip_text(ctv_ml($data['text']));

							$to_mail = $user_email;
							$subject = str_replace('[sitename]', $sitename ,$subject);
							$subject = str_replace('[link]', $link ,$subject);			
							$subject = apply_filters('mail_loginauth_subject',$subject);
								
							$html = str_replace('[sitename]', $sitename ,$html);
							$html = str_replace('[link]', $link ,$html);
							$html = apply_filters('mail_loginauth_text',$html);
							$html = apply_filters('comment_text',$html);

							pn_mail($to_mail, $subject, $html, $ot_name, $ot_mail);

							$error = new WP_Error();
							$error->add( 'pn_success', __('We sent you a link needed for your authorization. Check your e-mail.','pn') );
							wp_clear_auth_cookie();
							
							return $error;							
						}	
					}												
				}	
			}
		}
	}
		
	return $user;
}

add_action('myaction_site_laform', 'def_myaction_ajax_laform');
function def_myaction_ajax_laform(){
global $wpdb, $user_ID, $or_site_url, $premiumbox;
	if(!$user_ID){
		$user_id = intval(is_param_get('user'));
		$h1 = is_lahash(is_param_get('h1'));
		$h2 = is_lahash(is_param_get('h2'));
		if($user_id and $h1 and $h2){
			$user = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."users WHERE ID='$user_id'");
			if(isset($user->ID)){
				if(is_pn_crypt($user->auto_login1, $h1) and is_pn_crypt($user->auto_login2, $h2)){
					$wpdb->update($wpdb->prefix."users", array('auto_login1'=>'','auto_login2'=>''), array('ID'=>$user->ID));
					$secure_cookie = is_ssl();
					wp_set_auth_cookie($user->ID, true, $secure_cookie);
					wp_set_current_user($user->ID);
					if(user_can($user->ID,'read')){
						wp_redirect(admin_url('index.php'));
					} else {
						$url = apply_filters('login_auth_redirect', $premiumbox->get_page('account'));
						wp_redirect($url);
					}
					exit;
				}
			}
		}
	}
		pn_display_mess(__('Attention! Authorisation Error!','pn'), __('Attention! Authorisation Error!','pn'), 'error');
}