<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Экспорт/импорт[:ru_RU][en_US:]Export/Import[:en_US]
description: [ru_RU:]Экспорт/импорт заявок, валют, направлений обмена[:ru_RU][en_US:]Export/Import of requests, currency, exchange directions[:en_US]
version: 1.0
category: [ru_RU:]Направления обменов[:ru_RU][en_US:]Exchange directions[:en_US]
cat: naps
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

add_action('admin_menu', 'admin_menu_export');
function admin_menu_export(){
global $premiumbox;	
	
	if(current_user_can('administrator') or current_user_can('pn_export_exchange')){
		add_submenu_page("pn_moduls", __('Exchanges export','pn'), __('Exchanges export','pn'), 'read', "pn_export_exchange", array($premiumbox, 'admin_temp'));
	}
	if(current_user_can('administrator') or current_user_can('pn_export_exchange_direcions') or current_user_can('pn_import_exchange_direcions')){
		add_submenu_page("pn_moduls", __('Exchange directions Export/Import','pn'), __('Exchange directions Export/Import','pn'), 'read', "pn_export_direction", array($premiumbox, 'admin_temp'));
	}
	if(current_user_can('administrator') or current_user_can('pn_export_currency') or current_user_can('pn_import_currency')){
		add_submenu_page("pn_moduls", __('Currency Export/Import','pn'), __('Currency Export/Import','pn'), 'read', "pn_export_currency", array($premiumbox, 'admin_temp'));
	}
	
}

add_filter('pn_caps','export_pn_caps');
function export_pn_caps($pn_caps){
	
	$pn_caps['pn_export_exchange'] = __('Exchanges export','pn');
	$pn_caps['pn_export_exchange_direcions'] = __('Exchanges directions Export','pn');
	$pn_caps['pn_import_exchange_direcions'] = __('Exchanges directions Import','pn');
	$pn_caps['pn_export_currency'] = __('Currency Export','pn');
	$pn_caps['pn_import_currency'] = __('Currency Import','pn');
	
	return $pn_caps;
}

global $premiumbox;	
$premiumbox->file_include($path.'/exchange');
$premiumbox->file_include($path.'/direction');
$premiumbox->file_include($path.'/currency');