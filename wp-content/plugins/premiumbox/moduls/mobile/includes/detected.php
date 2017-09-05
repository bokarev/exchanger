<?php
if( !defined( 'ABSPATH')){ exit(); }

function mobile_vers_link(){
	return get_ajax_link('set_site_vers', 'get').'&set=mobile&return_url='. urlencode($_SERVER['REQUEST_URI']);
}

function web_vers_link(){
	return get_ajax_link('set_site_vers', 'get').'&set=web&return_url='. urlencode($_SERVER['REQUEST_URI']);
}

add_action('myaction_site_set_site_vers', 'mobile_site_set_site_vers');
function mobile_site_set_site_vers(){
	
	$return_url = trim(urldecode(is_param_get('return_url')));
	$set = trim(is_param_get('set'));
	if($set != 'mobile'){ $set = 'web'; }
	$set_indicator = 0;
	if($set == 'web'){ $set_indicator=1; } else { $set_indicator=2; }
	
	add_mycookie('web_version', $set_indicator);
	
	wp_redirect($return_url);
	exit;
}