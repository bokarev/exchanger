<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Доступ к панели управления по времени[:ru_RU][en_US:]Access to control panel by time[:en_US]
description: [ru_RU:]Доступ к панели управления по времени[:ru_RU][en_US:]Access to control panel by time[:en_US]
version: 1.0
category: [ru_RU:]Безопасность[:ru_RU][en_US:]Security[:en_US]
cat: secur
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

add_action('init', 'init_apbytime', 4);
function init_apbytime(){
global $premiumbox;
	if (is_admin()) {	
		$ui = wp_get_current_user();
		$user_id = intval(is_isset($ui, 'ID'));
		if($user_id){
			$role = '';
			if(user_can($user_id, 'meneger')){
				$role = 'meneger';
			} elseif(user_can($user_id, 'topmeneger')){
				$role = 'topmeneger';
			}
			
			$data = $premiumbox->get_option('apbytime', $role);
			if(!get_apbytime_status($data)){
				pn_display_mess(__('Access to the control panel is temporarily disabled','Access to the control panel is temporarily disabled'));
			}	
		}	
	}
}

global $premiumbox;
$premiumbox->file_include($path.'/settings');