<?php
if( !defined( 'ABSPATH')){ exit(); }

add_filter('register_url','pn_register_url');
function pn_register_url($url){
global $premiumbox;	
	return $premiumbox->get_page('register');
}

add_filter('lostpassword_url','pn_lostpassword_url');
function pn_lostpassword_url($url){
global $premiumbox;		
	return $premiumbox->get_page('lostpass');
}

global $admin_panel_url, $premiumbox, $user_ID, $wpdb;
$admin_panel_url = is_admin_newurl($premiumbox->get_option('admin_panel_url'));

add_action('login_form_register','def_login_form_notfound');
add_action('login_form_retrievepassword','def_login_form_notfound');
add_action('login_form_resetpass','def_login_form_notfound');
add_action('login_form_rp','def_login_form_notfound');
add_action('login_form_lostpassword','def_login_form_notfound');
if(defined('PN_ADMIN_GOWP') and constant('PN_ADMIN_GOWP') != 'true' and $admin_panel_url){
	remove_action('admin_enqueue_scripts','wp_auth_check_load');
	add_action('login_form_login','def_login_form_notfound');
}
function def_login_form_notfound(){
	pn_display_mess(__('Page does not exist','pn'));
}

if(defined('PN_ADMIN_GOWP') and constant('PN_ADMIN_GOWP') != 'true' and $admin_panel_url){ /* если не включён аварийный вход */

	function pn_filter_wp_login($str){
	global $admin_panel_url, $or_site_url, $user_ID, $wpdb;	
		
		if(preg_match("/reauth/i", $str)){
			wp_redirect($or_site_url);
			exit;
		} 
			
		return str_replace('wp-login.php', $admin_panel_url, $str);
	}
	add_filter('wp_redirect', 'pn_filter_wp_login');
	add_filter('network_site_url', 'pn_filter_wp_login');
	add_filter('site_url', 'pn_filter_wp_login');	

	add_action('init', 'set_login_page');
	function set_login_page(){
		global $admin_panel_url, $premiumbox, $user_ID, $wpdb, $or_site_url;
		
		$data = premium_rewrite_data();
		$super_base = $data['super_base'];
		$base = $data['base'];

		if($super_base == $admin_panel_url){
			
			if($user_ID){
				if(current_user_can('read')){
					$url = admin_url('index.php');
					wp_redirect($url);
					exit;
				} else {
					return;
				}
			}			
			
			header('Content-Type: text/html; charset=utf-8');
			
			$plugin_version = $premiumbox->plugin_version;
			$premium_url = get_premium_url();
			$plugin_url = $premiumbox->plugin_url;
			?>
<!DOCTYPE html>
<html lang="<?php echo get_locale(); ?>">
<head>
	<meta charset="UTF-8">
	<title><?php _e('Authorization','pn'); ?></title>
	<link rel='stylesheet' href='<?php echo $plugin_url; ?>default/newadminpanel/style.css?ver=<?php echo $plugin_version; ?>' type='text/css' media='all' />
	<script type='text/javascript' src='<?php echo $premium_url; ?>/js/jquery.min.js?ver=3.2.1'></script>
	<script type='text/javascript' src='<?php echo $premium_url; ?>/js/jquery.form.js?ver=3.51'></script>
	<script type="text/javascript">
		<?php set_premium_default_js(); ?>
	</script>	
	<?php do_action('newadminpanel_form_head'); ?>
</head>
<body>
<div id="container">
	<div class="wrap">
		<form method="post" class="ajax_post_form" action="<?php pn_the_link_post('pn_admin_login'); ?>">
		<input type="hidden" name="super_base" value="<?php echo $super_base; ?>" />
		
		<div class="resultgo"></div>
		<div class="form">
			<div class="form_title"><?php _e('Authorization','pn'); ?></div>
			
			<div class="form_line">
				<div class="form_label"><?php _e('Login or email', 'pn'); ?></div>
				<input type="text" name="logmail" class="notclear" value="" />
			</div>

			<div class="form_line">
				<div class="form_label"><?php _e('Password', 'pn'); ?></div>
				<input type="password" name="pass" class="notclear" value="" />
			</div>
			
			<?php do_action('newadminpanel_form'); ?>
			
			<div class="form_line centered"><input type="submit" formtarget="_top" name="submit" value="<?php _e('Sign in', 'pn'); ?>" /></div>
			
			<div class="form_links"><a href="<?php echo $premiumbox->get_page('register'); ?>"><?php _e('Sign up','pn'); ?></a> | <a href="<?php echo $premiumbox->get_page('lostpass'); ?>"><?php _e('Forgot password?','pn'); ?></a></div>
		</div>
		</form>
	</div>
	<?php do_action('newadminpanel_form_footer'); ?>
</div>	
</body>
</html>		
			<?php
			exit;
		}	
	}
}

add_action('premium_action_pn_admin_login','def_premium_action_pn_admin_login');
function def_premium_action_pn_admin_login(){
global $wpdb, $premiumbox, $user_ID, $or_site_url, $admin_panel_url;	
	
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
	
	$log = apply_filters('newadminpanel_ajax_form', $log);
	
	if($user_ID){
		$log['status'] = 'error';
		$log['status_code'] = 1;
		$log['status_text'] = __('Error! This form is available for unauthorized users only','pn');
		if(current_user_can('read')){
			$url = admin_url('index.php');
		} else {
			$url = $or_site_url;
		}
		$log['url'] = $url;
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
			$super_base = trim(is_param_post('super_base'));
			if($admin_panel_url == $super_base){
				if(strstr($logmail,'@')){
					$ui = get_user_by('email', $logmail);
				} else {
					$ui = get_user_by('login', $logmail);
				}
				if(isset($ui->ID)){
					$user_id = intval($ui->ID);
					if(user_can($user_id,'read')){
					
						$creds = array();
						$creds['user_login'] = is_user($ui->user_login);
						$creds['user_password'] = $pass;
						$creds['remember'] = true;
						$user = wp_signon($creds, $secure_cookie);	
				
						if ( $user && !is_wp_error($user) ) {
							$log['status'] = 'success';
							$log['url'] = admin_url('index.php');		
						} elseif($user and isset($user->errors['pn_error'])){
							$log['status'] = 'error';	
							$log['status_text'] = $user->errors['pn_error'][0];
						} elseif($user and isset($user->errors['pn_success'])){	
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