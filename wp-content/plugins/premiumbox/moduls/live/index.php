<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]LIVE заявки[:ru_RU][en_US:]LIVE orders[:en_US]
description: [ru_RU:]LIVE заявки[:ru_RU][en_US:]LIVE orders[:en_US]
version: 0.1
category: [ru_RU:]Заявки[:ru_RU][en_US:]Orders[:en_US]
cat: req
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

add_action('admin_menu', 'pn_adminpage_bidslive', 11);
function pn_adminpage_bidslive(){
global $premiumbox;
	if(current_user_can('administrator') or current_user_can('pn_bids')){	
		add_submenu_page("pn_bids", __('LIVE orders','pn'), __('LIVE orders','pn'), 'read', "pn_live_bids", array($premiumbox, 'admin_temp'));
	}
}

global $premiumbox;
$premiumbox->file_include($path.'/live');
$premiumbox->file_include($path.'/actions');