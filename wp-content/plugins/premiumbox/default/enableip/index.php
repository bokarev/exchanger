<?php
if( !defined( 'ABSPATH')){ exit(); }

add_filter( 'authenticate', 'enableip_login_check', 99, 1);
function enableip_login_check($user){
global $wpdb;

	if(is_object($user) and isset($user->data->ID)){
		if(!defined('PN_ADMIN_GOWP') or defined('PN_ADMIN_GOWP') and constant('PN_ADMIN_GOWP') != 'true'){
			$enable_ips = trim($user->data->enable_ips);
			if(!pn_enable_ip($enable_ips)){	
		
				$error = new WP_Error();
				$error->add( 'pn_error',__('Error! Invalid IP address','pn'));
				wp_clear_auth_cookie();
							
				return $error;							
			}
		}
	}
		
	return $user;
}

add_action('init', 'init_enableip');
function init_enableip(){
	
	if(!defined('PN_ADMIN_GOWP') or defined('PN_ADMIN_GOWP') and constant('PN_ADMIN_GOWP') != 'true'){
		global $or_site_url;
		$ui = wp_get_current_user();
		$enable_ips = trim(is_isset($ui, 'enable_ips'));
		if(!pn_enable_ip($enable_ips)){
			wp_logout();
			wp_redirect($or_site_url);
			exit();
		}			
	}
	
}