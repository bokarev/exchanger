<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action( 'set_logged_in_cookie', 'set_logged_in_cookie_alogs', 99, 4 );
function set_logged_in_cookie_alogs($logged_in_cookie, $expire, $expiration, $user_id){
global $change_ld_account, $wpdb;
	
	if($change_ld_account != 'true' and $user_id > 0){
		
		$array = array();
		$ui = get_userdata($user_id);
		$array['datelogin'] = current_time('mysql');
		$array['user_id'] = $user_id;
		$array['user_login'] = is_user($ui->user_login);
		$array['user_ip'] = pn_real_ip();
		$array['user_browser'] = pn_maxf(pn_strip_input(is_isset($_SERVER,'HTTP_USER_AGENT')),250);
		$wpdb->insert($wpdb->prefix.'login_check', $array);
		
		if(isset($ui->sec_login) and $ui->sec_login == 1){
			
			$mailtemp = get_option('mailtemp');
			if(isset($mailtemp['alogs'])){
				$data = $mailtemp['alogs'];
				if($data['send'] == 1){
					$ot_mail = is_email($data['mail']);
					$ot_name = pn_strip_input($data['name']);
						
					$sitename = pn_strip_input(get_bloginfo('sitename'));	
						
					$subject = pn_strip_input(ctv_ml($data['title']));
						
					$html = pn_strip_text(ctv_ml($data['text']));
						
					$browser = 	get_browser_name($array['user_browser'], __('Unknown','pn'));
						
					$to_mail = is_email($ui->user_email);
					$subject = str_replace('[sitename]', $sitename ,$subject);
					$subject = str_replace('[date]', $array['datelogin'] ,$subject);
					$subject = str_replace('[ip]', $array['user_ip'] ,$subject);
					$subject = str_replace('[browser]',$browser,$subject);				
					$subject = apply_filters('mail_logincheck_subject',$subject);
					
					$html = str_replace('[sitename]', $sitename ,$html);
					$html = str_replace('[date]', $array['datelogin'] ,$html);
					$html = str_replace('[ip]', $array['user_ip'] ,$html);
					$html = str_replace('[browser]',$browser,$html);
					$html = apply_filters('mail_logincheck_text',$html);
					$html = apply_filters('comment_text',$html);
		
					pn_mail($to_mail, $subject, $html, $ot_name, $ot_mail);	
				}	
			}			
		}		
	}
}