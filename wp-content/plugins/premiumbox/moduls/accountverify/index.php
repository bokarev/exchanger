<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Верификация счетов пользователей[:ru_RU][en_US:]Accounts verification[:en_US]
description: [ru_RU:]Верификация счетов пользователей[:ru_RU][en_US:]Accounts verification[:en_US]
version: 1.0
category: [ru_RU:]Пользователи[:ru_RU][en_US:]Users[:en_US]
cat: user
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* BD */
add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_accountverify');
function bd_pn_moduls_active_accountverify(){
global $wpdb;	
	
/* 
верификаци счетов
 
createdate - дата создания
user_id - id юзера
user_login - логин юзера
user_email - e-mail юзера

usac_id - id номера счета
valut_id - id валюты

theip - ip
accountnum - номер счета
status - 0-ожидает верификации, 1-подтвержден, 2-отказано
*/
	$table_name = $wpdb->prefix ."uv_accounts";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		`createdate` datetime NOT NULL,
		`user_id` bigint(20) NOT NULL default '0',
		`user_login` varchar(250) NOT NULL,
		`user_email` varchar(250) NOT NULL,
		`valut_id` bigint(20) NOT NULL default '0',
		`usac_id` bigint(20) NOT NULL default '0',
		`theip` varchar(250) NOT NULL,
		`accountnum` longtext NOT NULL,
		`locale` varchar(20) NOT NULL,
		`status` int(1) NOT NULL default '0',
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);

/*
Прикрепленные файлы

uv_data - данные
uv_id - id заявки
*/	
	$table_name = $wpdb->prefix ."uv_accounts_files";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		`user_id` bigint(20) NOT NULL default '0',
		`uv_data` longtext NOT NULL,
		`uv_id` bigint(20) NOT NULL default '0',
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);	
	
}
/* end BD */

add_filter('pn_caps','accountverify_pn_caps');
function accountverify_pn_caps($pn_caps){
	$pn_caps['pn_accountverify'] = __('Use account verification','pn');
	return $pn_caps;
}

/* 
Подключаем к меню
*/
add_action('admin_menu', 'pn_adminpage_accountverify');
function pn_adminpage_accountverify(){
global $premiumbox;
	
	if(current_user_can('administrator') or current_user_can('pn_accountverify')){
		$hook = add_menu_page(__('Account verification','pn'), __('Account verification','pn'), 'read', 'pn_usac', array($premiumbox, 'admin_temp'), $premiumbox->get_icon_link('verify'));  
		add_action( "load-$hook", 'pn_trev_hook' );		
		add_submenu_page("pn_usac", __('Settings','pn'), __('Settings','pn'), 'read', "pn_usac_change", array($premiumbox, 'admin_temp'));		
	}
	
}

/* e-mail */
add_filter('admin_mailtemp','admin_mailtemp_accountverify');
function admin_mailtemp_accountverify($places_admin){
	
	$places_admin['userverify2'] = __('Account verification requests','pn');
	
	return $places_admin;
}

add_filter('user_mailtemp','user_mailtemp_accountverify');
function user_mailtemp_accountverify($places_admin){
	
	$places_admin['userverify3_u'] = __('Successful account verification','pn');
	$places_admin['userverify4_u'] = __('Account verification declined','pn');	
	
	return $places_admin;
}

add_filter('mailtemp_tags_userverify2','def_mailtemp_tags_userverify3_u');
add_filter('mailtemp_tags_userverify3_u','def_mailtemp_tags_userverify3_u');
add_filter('mailtemp_tags_userverify4_u','def_mailtemp_tags_userverify3_u');
function def_mailtemp_tags_userverify3_u($tags){
	
	$tags['user_login'] = __('User login','pn');
	$tags['purse'] = __('Account number','pn');
	
	return $tags;
}
/* end e-mail */

function delete_userwallets_files($id){
global $wpdb;
	$id = intval($id);
	$my_dir = wp_upload_dir();
	$wpdb->query("DELETE FROM ".$wpdb->prefix."uv_accounts_files WHERE uv_id = '$id'");
	$path = $my_dir['basedir'].'/accountverify/'. $id .'/';
	full_del_dir($path);
}

add_action('pn_userwallets_delete', 'pn_userwallets_delete_accountverify');
function pn_userwallets_delete_accountverify($id){
global $wpdb;
	
	$items = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."uv_accounts WHERE usac_id = '$id'");
	foreach($items as $item){
		$item_id = $item->id;
		do_action('pn_user_accounts_delete_before', $item_id, $item);
		$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."uv_accounts WHERE id = '$item_id'");
		if($result){
			do_action('pn_user_accounts_delete', $item_id, $item);
		}
	}
	
}

add_action('pn_user_accounts_delete', 'pn_user_accounts_delete_accountverify', 10, 2);
function pn_user_accounts_delete_accountverify($item_id, $item){
	$usac_id = $item->usac_id;
	delete_userwallets_files($usac_id);
}

add_action('pn_valuts_delete', 'pn_valuts_delete_accountverify');
function pn_valuts_delete_accountverify($id){
global $wpdb;	

	$items = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."uv_accounts WHERE valut_id = '$id'");
	foreach($items as $item){
		$item_id = $item->id;
		do_action('pn_user_accounts_delete_before', $item_id, $item);
		$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."uv_accounts WHERE id = '$item_id'");
		if($result){
			do_action('pn_user_accounts_delete', $item_id, $item);
		}
	}
}

global $premiumbox;
$premiumbox->file_include($path.'/settings');
$premiumbox->file_include($path.'/usac');
$premiumbox->file_include($path.'/file'); 
$premiumbox->file_include($path.'/shortcode'); 

$premiumbox->file_include($path.'/function'); 