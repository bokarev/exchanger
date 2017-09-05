<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('admin_menu', 'admin_menu_alogs');
function admin_menu_alogs(){
global $premiumbox;	
	
	if(current_user_can('administrator')){
		$hook = add_submenu_page('users.php', __('Authorization log','pn'), __('Authorization log','pn'), 'read', 'pn_alogs', array($premiumbox, 'admin_temp'));  
		add_action( "load-$hook", 'pn_trev_hook' );
	}
}

add_filter('user_mailtemp','user_mailtemp_alogs');
function user_mailtemp_alogs($places_admin){
	$places_admin['alogs'] = __('Notify of user logging into personal account','pn');
	return $places_admin;
}

add_filter('mailtemp_tags_alogs','def_mailtemp_tags_alogs');
function def_mailtemp_tags_alogs($tags){
	
	$tags['date'] = __('Date','pn');
	$tags['ip'] = __('IP','pn');
	$tags['browser'] = __('Browser','pn');
	
	return $tags;
}

/* действия при удалении */
add_action( 'delete_user', 'delete_user_alogs');
function delete_user_alogs($user_id){
global $wpdb;

	$wpdb->query("DELETE FROM ". $wpdb->prefix. "login_check WHERE user_id = '$user_id'");	
}

global $premiumbox;
$premiumbox->include_patch(__FILE__, 'list');
$premiumbox->include_patch(__FILE__, 'cron');
$premiumbox->include_patch(__FILE__, 'logs');