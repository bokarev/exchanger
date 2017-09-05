<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Верификация пользователей[:ru_RU][en_US:]Users verification[:en_US]
description: [ru_RU:]Верификация пользователей[:ru_RU][en_US:]Users verification[:en_US]
version: 1.0
category: [ru_RU:]Пользователи[:ru_RU][en_US:]Users[:en_US]
cat: user
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* BD */
add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_userverify');
function bd_pn_moduls_active_userverify(){
global $wpdb, $premiumbox;	
	
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."users LIKE 'user_verify'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."users ADD `user_verify` int(1) NOT NULL default '0'");
    }	
	
/* 
поля верификации

title - название поля
fieldvid - вид поля
locale - версия локализации
helps - подсказка
status - 0-не работает, 1-работает
uv_order - порядок
*/	
	$table_name = $wpdb->prefix ."uv_field";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		`title` longtext NOT NULL,
		`fieldvid` int(1) NOT NULL default '0',
		`locale` varchar(20) NOT NULL,
		`uv_auto` varchar(250) NOT NULL,
		`uv_req` int(1) NOT NULL default '0',
		`helps` longtext NOT NULL,
		`status` int(1) NOT NULL default '0',
		`uv_order` bigint(20) NOT NULL default '0',
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);

	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."uv_field LIKE 'helps'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."uv_field ADD `helps` longtext NOT NULL");
    }	
	
/*
Данные юзера по заявке
uv_data - данные
uv_id - id заявки
uv_field - id поля заявки
*/	
	$table_name = $wpdb->prefix ."uv_field_user";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		`user_id` bigint(20) NOT NULL default '0',
		`uv_data` longtext NOT NULL,
		`uv_id` bigint(20) NOT NULL default '0',
		`uv_field` bigint(20) NOT NULL default '0',
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);
	
/* 
верификаци
 
createdate - дата создания
user_id - id юзера
user_login - логин юзера
user_email - e-mail юзера
theip - ip
comment - причина отказа
status - 0-авто, 1-отправлено, 2-подтвержден, 3-отказано
*/
	$table_name = $wpdb->prefix ."userverify";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		`createdate` datetime NOT NULL,
		`user_id` bigint(20) NOT NULL default '0',
		`user_login` varchar(250) NOT NULL,
		`user_email` varchar(250) NOT NULL,
		`theip` varchar(250) NOT NULL,
		`comment` longtext NOT NULL,
		`locale` varchar(20) NOT NULL,
		`status` int(1) NOT NULL default '0',
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);	
	
    $query = $wpdb->query("SHOW COLUMNS FROM ". $wpdb->prefix ."userverify LIKE 'status'");
    if ($query == 0) {
        $wpdb->query("ALTER TABLE ". $wpdb->prefix ."userverify ADD `status` int(1) NOT NULL default '0'");
    }		
}

add_filter('pn_tech_pages', 'list_tech_pages_userverify');
function list_tech_pages_userverify($pages){
 
	$pages[] = array(
        'post_name'      => 'userverify',
        'post_title'     => '[ru_RU:]Верификация аккаунта[:ru_RU][en_US:]User verification[:en_US]',
		'post_content'   => '[userverify]',
		'post_template'   => 'pn-pluginpage.php',
	);		
	
	return $pages;
}

add_action('pn_bd_activated', 'bd_pn_moduls_migrate_userverify');
function bd_pn_moduls_migrate_userverify(){
global $wpdb;

    $query = $wpdb->query("SHOW COLUMNS FROM ". $wpdb->prefix ."userverify LIKE 'status'");
    if ($query == 0) {
        $wpdb->query("ALTER TABLE ". $wpdb->prefix ."userverify ADD `status` int(1) NOT NULL default '0'");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."uv_field LIKE 'helps'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."uv_field ADD `helps` longtext NOT NULL");
    }	
	
}
/* end BD */

add_filter('pn_caps','userverify_pn_caps');
function userverify_pn_caps($pn_caps){
	$pn_caps['pn_userverify'] = __('Work with user verification','pn');
	return $pn_caps;
}

/* 
Подключаем к меню
*/
add_action('admin_menu', 'pn_adminpage_userverify');
function pn_adminpage_userverify(){
global $premiumbox;
	
	if(current_user_can('administrator') or current_user_can('pn_userverify')){
		$hook = add_menu_page(__('Verification','pn'), __('Verification','pn'), 'read', 'pn_usve', array($premiumbox, 'admin_temp'), $premiumbox->get_icon_link('verify'));  
		add_action( "load-$hook", 'pn_trev_hook' );
		add_submenu_page("pn_usve", __('Add verification','pn'), __('Add verification','pn'), 'read', 'pn_add_usve', array($premiumbox, 'admin_temp'));
		$hook = add_submenu_page("pn_usve", __('Verification fields','pn'), __('Verification fields','pn'), 'read', 'pn_usfield', array($premiumbox, 'admin_temp'));  
		add_action( "load-$hook", 'pn_trev_hook' );	
		add_submenu_page("pn_usve", __('Add verification field','pn'), __('Add verification field','pn'), 'read', 'pn_add_usfield', array($premiumbox, 'admin_temp'));
		add_submenu_page("pn_usve", __('Sort verification fields','pn'), __('Sort verification fields','pn'), 'read', 'pn_sort_usfield', array($premiumbox, 'admin_temp'));		
		add_submenu_page("pn_usve", __('Settings','pn'), __('Settings','pn'), 'read', "pn_usve_change", array($premiumbox, 'admin_temp'));		
	}
	
}

/* e-mail */
add_filter('admin_mailtemp','admin_mailtemp_userverify');
function admin_mailtemp_userverify($places_admin){
	
	$places_admin['userverify1'] = __('Requests for identity verification','pn');
	
	return $places_admin;
}

add_filter('user_mailtemp','user_mailtemp_userverify');
function user_mailtemp_userverify($places_admin){
	
	$places_admin['userverify1_u'] = __('Verification is successful','pn');
	$places_admin['userverify2_u'] = __('Verification declined','pn');	
	
	return $places_admin;
}


add_filter('mailtemp_tags_userverify2_u','def_mailtemp_tags_userverify2_u');
function def_mailtemp_tags_userverify2_u($tags){
	
	$tags['text'] = __('Failure reason','pn');
	
	return $tags;
}
/* end e-mail */

/* авто-поля */
add_filter('uv_auto_filed','def_uv_auto_filed',1);
function def_uv_auto_filed($uv_auto){
	
	$uv_auto = array();
	$uv_auto[0] = '---'.__('No','pn').'---';
	$uv_auto['first_name'] = __('First name', 'pn');
	$uv_auto['last_name'] = __('Last name', 'pn');
	$uv_auto['second_name'] = __('Second name', 'pn');
	$uv_auto['user_phone'] = __('Phone no.', 'pn');
	$uv_auto['user_skype'] = __('Skype', 'pn');
	$uv_auto['user_email'] = __('E-mail', 'pn');
	$uv_auto['user_passport'] = __('Passport number','pn');
	
	return $uv_auto;
}

add_filter('uv_auto_filed_value','def_uv_auto_filed_value',1,3);
function def_uv_auto_filed_value($value, $cf_auto ,$ui){
	
	if($cf_auto == 'first_name'){
		$value = pn_strip_input($ui->first_name);
	} elseif($cf_auto == 'last_name'){
		$value = pn_strip_input($ui->last_name);
	} elseif($cf_auto == 'second_name'){
		$value = pn_strip_input($ui->second_name);
	} elseif($cf_auto == 'user_phone'){
		$value = str_replace('+','',is_phone($ui->user_phone));
	} elseif($cf_auto == 'user_skype'){
		$value = pn_strip_input($ui->user_skype);
	} elseif($cf_auto == 'user_email'){
		$value = is_email($ui->user_email);
	} elseif($cf_auto == 'user_passport'){
		$value = pn_strip_input($ui->user_passport);		
	}	
	
	return $value;
}

add_filter('uv_strip_filed_value','def_uv_strip_filed_value',1,2);
function def_uv_strip_filed_value($value, $cf_auto){
	
	if($cf_auto == 'first_name'){
		$value = pn_maxf_mb(get_caps_name($value), 250);
	} elseif($cf_auto == 'last_name'){
		$value = pn_maxf_mb(get_caps_name($value), 250);
	} elseif($cf_auto == 'second_name'){
		$value = pn_maxf_mb(get_caps_name($value), 250);
	} elseif($cf_auto == 'user_phone'){
		$value = pn_maxf_mb(str_replace('+','',is_phone($value)), 250);
	} elseif($cf_auto == 'user_skype'){
		$value = pn_maxf_mb(pn_strip_input($value), 250);
	} elseif($cf_auto == 'user_email'){
		$value = is_email($value);
	} elseif($cf_auto == 'user_passport'){
		$value = pn_maxf_mb(pn_strip_input($value), 250);		
	} else {
		$value = pn_maxf_mb(pn_strip_input($value), 500);
	}
	
	return $value;
}
/* end авто-поля */

add_action('pn_usfield_delete', 'def_usfield_delete', 10, 2);
function def_usfield_delete($id, $item){
global $wpdb;

	$items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."uv_field_user WHERE uv_field = '$id'");
	foreach($items as $item){
		$item_id = $item->id;
		do_action('pn_usfielduser_delete_before', $item_id, $item);
		$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."uv_field_user WHERE id = '$item_id'");
		if($result){
			do_action('pn_usfielduser_delete', $item_id, $item); 
		}
	}		
}

add_action('pn_usve_delete', 'def_usve_delete', 10, 2);
function def_usve_delete($id, $item){
global $wpdb;

	$my_dir = wp_upload_dir();
	$path = $my_dir['basedir'].'/userverify/'. $id .'/';
	full_del_dir($path);
	
	$user_id = $item->user_id;
	$cc = $wpdb->query("SELECT id FROM ".$wpdb->prefix."userverify WHERE user_id = '$user_id' AND status = '2' AND id != '$id'");
	if($cc == 0){
		$arr = array();
		$arr['user_verify'] = 0;
		$wpdb->update($wpdb->prefix.'users', $arr, array('ID'=>$user_id));
	}	
	
	$items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."uv_field_user WHERE uv_id = '$id'");
	foreach($items as $item){
		$item_id = $item->id;
		do_action('pn_usfielduser_delete_before', $item_id, $item);
		$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."uv_field_user WHERE id = '$item_id'");
		if($result){
			do_action('pn_usfielduser_delete', $item_id, $item); 
		}
	}	
}

global $premiumbox;
$premiumbox->file_include($path.'/function');
$premiumbox->file_include($path.'/settings');
$premiumbox->file_include($path.'/list_usfield'); 
$premiumbox->file_include($path.'/add_usfield'); 
$premiumbox->file_include($path.'/sort_usfield');
$premiumbox->file_include($path.'/usvedoc');
$premiumbox->file_include($path.'/show_usvedoc');
$premiumbox->file_include($path.'/usve');
$premiumbox->file_include($path.'/add_usve');

$premiumbox->auto_include($path.'/shortcode');

$premiumbox->file_include($path.'/widget/userverify');