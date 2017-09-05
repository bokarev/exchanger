<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_quicktags_page','adminpage_quicktags_page_login');
function adminpage_quicktags_page_login(){
?>
edButtons[edButtons.length] = 
new edButton('premium_login', '<?php _e('Authourization form','pn'); ?>','[login_form]');
<?php	
}

function get_login_formed(){
global $wpdb, $premiumbox;
	
	$temp = '';
	
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);
	
	if(!$user_id){

		$items = get_login_form_filelds();
		$html = prepare_form_fileds($items, 'login_form_line', 'log');
	
		$array = array(
			'[form]' => '<form method="post" class="ajax_post_form" action="'. get_ajax_link('loginform') .'">',
			'[/form]' => '</form>',
			'[result]' => '<div class="resultgo"></div>',
			'[html]' => $html,
			'[submit]' => '<input type="submit" formtarget="_top" name="submit" class="log_submit" value="'. __('Sign in', 'pn') .'" />',
			'[toslink]' => $premiumbox->get_page('tos'),
			'[registerlink]' => $premiumbox->get_page('register'),
			'[lostpasslink]' => $premiumbox->get_page('lostpass'),
		);	
	
		$temp_form = '
		<div class="log_div_wrap">
		[form]
			
			<div class="log_div_title">
				<div class="log_div_title_ins">
					'. __('Authorization','pn') .'
				</div>
			</div>
		
			<div class="log_div">
				<div class="log_div_ins">
					
					[html]
					
					<div class="log_line">
						<div class="log_line_subm_left">
							[submit]
						</div>
						<div class="log_line_subm_right">
							<p><a href="[registerlink]">'. __('Sign up','pn') .'</a></p>
							<p><a href="[lostpasslink]">'. __('Forgot password?','pn') .'</a></p>
						</div>
						
						<div class="clear"></div>
					</div>

					[result]
 
				</div>
			</div>

		[/form]
		</div>
		';
	
		$temp_form = apply_filters('login_form_temp',$temp_form);
		$temp .= get_replace_arrays($array, $temp_form);		

	} else {
		$temp .= '<div class="resultfalse">'. __('Error! This form is available for unauthorized users only','pn') .'</div>';
	}
	
	return $temp;	
}

function login_form_shortcode($atts, $content) {

	$temp = get_login_formed();	
	
	return $temp;
}
add_shortcode('login_form', 'login_form_shortcode');

function login_page_shortcode($atts, $content) {

	$temp = apply_filters('before_login_page','');
			
	$temp .= get_login_formed();
	
    $temp .= apply_filters('after_login_page','');	
	
	return $temp;
}
add_shortcode('login_page', 'login_page_shortcode');

add_action('myaction_site_loginform', 'def_myaction_ajax_loginform');
function def_myaction_ajax_loginform(){
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
	
	$log = apply_filters('before_ajax_form_field', $log, 'loginform');
	$log = apply_filters('before_ajax_loginform', $log);
	
	if($user_ID){
		$log['status'] = 'error';
		$log['status_code'] = 1;
		$log['status_text'] = __('Error! This form is available for unauthorized users only','pn');
		$log['url'] = apply_filters('login_auth_redirect', $premiumbox->get_page('account'));
		echo json_encode($log);
		exit;		
	}
		
	$logmail = is_param_post('logmail');
	if(strstr($logmail,'@')){
		$logmail = is_email($logmail);
	} else {
		$logmail = is_user($logmail);
	}

	$pass = is_password(is_param_post('pass'));
	
	if($logmail){
		if($pass){

			if(strstr($logmail,'@')){
				$ui = get_user_by('email', $logmail);
			} else {
				$ui = get_user_by('login', $logmail);
			}
			
			if(isset($ui->ID)){
				$user_id = intval($ui->ID);
				if(!user_can($user_id,'read')){
				
					$creds = array();
					$creds['user_login'] = is_user($ui->user_login);
					$creds['user_password'] = $pass;
					$creds['remember'] = true;
					$user = wp_signon($creds, $secure_cookie);	
			
					if ( $user && !is_wp_error($user) ) {
						$log['status'] = 'success';
						$log['url'] = apply_filters('login_auth_redirect', $premiumbox->get_page('account'));
					} elseif( $user and isset($user->errors['pn_error'])){
						$log['status'] = 'error';	
						$log['status_text'] = $user->errors['pn_error'][0];						
					} elseif( $user and isset($user->errors['pn_success'])){
						$log['status'] = 'success_clear';	
						$log['status_text'] = $user->errors['pn_success'][0];					
					} else {
						$log['status'] = 'error';
						$log['status_code'] = 1;
						$log['status_text'] = __('Error! Wrong pair of username/password entered','pn');		
					}

				} else {
					$log['status'] = 'error';
					$log['status_code'] = 1;
					$log['status_text'] = __('Error! Wrong pair of username/password entered','pn');				
				}
			} else {
				$log['status'] = 'error';
				$log['status_code'] = 1;
				$log['status_text'] = __('Error! Wrong pair of username/password entered','pn');				
			}
		} else {
			$log['status'] = 'error';
			$log['status_code'] = 1;
			$log['status_text'] = __('Error! Incorrect password','pn');
		}
	} else {
		$log['status'] = 'error';
		$log['status_code'] = 1;
		$log['status_text'] = __('Error! Incorrect login or e-mail','pn');
	}			
	
	echo json_encode($log);	
	exit;
}