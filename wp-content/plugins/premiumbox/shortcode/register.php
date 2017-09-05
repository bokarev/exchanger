<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_quicktags_page','adminpage_quicktags_page_register');
function adminpage_quicktags_page_register(){
?>
edButtons[edButtons.length] = 
new edButton('premium_register', '<?php _e('Sign up','pn'); ?>','[register_form]');
<?php	
}

add_filter('user_mailtemp','user_mailtemp_register');
function user_mailtemp_register($places_admin){
	
	$places_admin['registerform'] = __('Registration form','pn');
	
	return $places_admin;
}

add_filter('mailtemp_tags_registerform','def_mailtemp_tags_registerform');
function def_mailtemp_tags_registerform($tags){
	
	$tags['login'] = __('Login','pn');
	$tags['pass'] = __('Password','pn');
	$tags['email'] = __('E-mail','pn');
	
	return $tags;
}

function get_register_formed(){
global $wpdb, $premiumbox;
	
	$temp = '';
	
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);	
	
	if(!$user_id){
		
		$items = get_register_form_filelds();
		$html = prepare_form_fileds($items, 'register_form_line', 'reg');
	
		$array = array(
			'[form]' => '<form method="post" class="ajax_post_form" action="'. get_ajax_link('registerform') .'">',
			'[/form]' => '</form>',
			'[result]' => '<div class="resultgo"></div>',
			'[html]' => $html,
			'[submit]' => '<input type="submit" formtarget="_top" name="submit" class="reg_submit" value="'. __('Sign up', 'pn') .'" />',
			'[toslink]' => $premiumbox->get_page('tos'),
			'[loginlink]' => $premiumbox->get_page('login'),
			'[lostpasslink]' => $premiumbox->get_page('lostpass'),
		);	
	
		$temp_form = '
		<div class="reg_div_wrap">
		[form]
			
			<div class="reg_div_title">
				<div class="reg_div_title_ins">
					'. __('Sign up','pn') .'
				</div>
			</div>
		
			<div class="reg_div">
				<div class="reg_div_ins">
					
					[html]
					
					<div class="reg_line">
						<div class="checkbox"><input type="checkbox" name="rcheck" value="1" /> '. sprintf(__('I read and agree with <a href="%s">the terms and conditions</a>','pn'), '[toslink]' ) .'</div>
					</div>
					
					<div class="reg_line">
						<div class="reg_line_subm_left">
							[submit]
						</div>
						<div class="reg_line_subm_right">
							<a href="[loginlink]">'. __('Authorization','pn') .'</a>
						</div>
						
						<div class="clear"></div>
					</div>

					[result]
 
				</div>
			</div>

		[/form]
		</div>
		';
	
		$temp_form = apply_filters('register_form_temp',$temp_form);
		$temp .= get_replace_arrays($array, $temp_form);		

	} else {
		$temp .= '<div class="resultfalse">'. __('Error! This form is available for unauthorized users only','pn') .'</div>';
	}
	
	return $temp;
}

function register_form_shortcode($atts, $content) {

	$temp = get_register_formed();	
	
	return $temp;
}
add_shortcode('register_form', 'register_form_shortcode');

function register_page_shortcode($atts, $content) {

	$temp = apply_filters('before_register_page','');
	
	$temp .= get_register_formed();
	
    $temp .= apply_filters('after_register_page','');	
	
	return $temp;
}
add_shortcode('register_page', 'register_page_shortcode');

/* обработка формы сайта */
add_action('myaction_site_registerform', 'def_myaction_ajax_registerform');
function def_myaction_ajax_registerform(){
global $or_site_url, $user_ID, $wpdb, $premiumbox;	
	
	only_post();
	nocache_headers();
	
	global $myerrors;
	$myerrors = new WP_Error();	
	$secure_cookie = is_ssl();
	
	$log = array();		
	$log['response'] = '';
	$log['status'] = '';
	$log['status_code'] = 0;
	$log['status_text'] = '';
	
	$premiumbox->up_mode();
	
	$log = apply_filters('before_ajax_form_field', $log, 'registerform');
	$log = apply_filters('before_ajax_registerform', $log);
	
	if($user_ID){
		$log['status'] = 'error'; 
		$log['status_code'] = 1;
		$log['status_text']= __('Error! This form is available for unauthorized users only','pn');
		echo json_encode($log);
		exit;		
	}
		
	$user_login = is_user(is_param_post('login'));
	$email = is_email(is_param_post('email'));
	$pass = is_password(is_param_post('pass'));
	$pass2 = is_password(is_param_post('pass2'));
	$rcheck = intval(is_param_post('rcheck'));
	if($rcheck){
		if($user_login){
			if($email){
				if($pass and $pass == $pass2){
					if (!username_exists($user_login)) {
						if (!email_exists($email) ){
							$user_id = wp_insert_user( array ('user_login' => $user_login, 'user_email' => $email, 'user_pass' => $pass) ) ;
							if($user_id){
								
								do_action( 'pn_user_register', $user_id);
								
								$mailtemp = get_option('mailtemp');
								if(isset($mailtemp['registerform'])){
									$data = $mailtemp['registerform'];
									if($data['send'] == 1){
										$ot_mail = is_email($data['mail']);
										$ot_name = pn_strip_input($data['name']);
						
										$sitename = pn_strip_input(get_bloginfo('sitename'));
						
										$subject = pn_strip_input(ctv_ml($data['title']));
						
										$html = pn_strip_text(ctv_ml($data['text']));
						
										$to_mail = $email;
						
										$sarray = array(
											'[sitename]' => $sitename,
											'[login]' => $user_login,
											'[pass]' => $pass,
											'[email]' => $email,
										);							
										$subject = get_replace_arrays($sarray, $subject);											
										$subject = apply_filters('mail_registerform_subject',$subject, $user_id);
														
										$html = get_replace_arrays($sarray, $html);											
										$html = apply_filters('mail_registerform_text',$html, $user_id);
										$html = apply_filters('comment_text',$html);
													
										pn_mail($to_mail, $subject, $html, $ot_name, $ot_mail);	 
									}
								}								
								
								$creds = array();
								$creds['user_login'] = $user_login;
								$creds['user_password'] = $pass;
								$creds['remember'] = true;
								$user = wp_signon($creds, $secure_cookie);	
	
								if ( $user && !is_wp_error($user) ) {
									$log['status'] = 'success';
									$log['url'] = apply_filters('login_auth_redirect', $premiumbox->get_page('account'));
									$log['status_text'] = apply_filters('ajax_register_success_message', __('You have successfully registered','pn'));
								} else {
									$log['status'] = 'success';
									$log['status_text'] = apply_filters('ajax_register2_success_message', __('You have successfully registered. You can now log into your account','pn'));
								}								
								
							} else {
								$log['status'] = 'error';
								$log['status_code'] = 1;
								$log['status_text'] = __('Error! Contact with website admin','pn');							
							}
						} else {
							$log['status'] = 'error';
							$log['status_code'] = 1;
							$log['status_text'] = __('Error! This e-mail is already in use','pn');							
						}
					} else {
						$log['status'] = 'error';
						$log['status_code'] = 1;
						$log['status_text'] = __('Error! This login is already in use','pn');						
					}
				} else {
					$log['status'] = 'error';
					$log['status_code'] = 1;
					$log['status_text'] = __('Error! Password is incorrect or does not match with the previously entered password','pn');					
				}
			} else {
				$log['status'] = 'error';
				$log['status_code'] = 1;
				$log['status_text'] = __('Error! You have entered an incorrect e-mail','pn');			
			}		
		} else {
			$log['status'] = 'error';
			$log['status_code'] = 1;
			$log['status_text'] = __('Error! You have entered an incorrect username. The username must consist of digits or latin letters and contain from 3 up to 30 characters.','pn');			
		}
	} else {
		$log['status'] = 'error';
		$log['status_code'] = 1;
		$log['status_text'] = __('Error! You did not agree with our terms and conditions','pn');
	}
	
	echo json_encode($log);
	exit;
}
/* end обработка формы сайта */