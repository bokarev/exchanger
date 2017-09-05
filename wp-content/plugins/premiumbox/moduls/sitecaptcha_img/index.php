<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Captcha для сайта (выбор картинки)[:ru_RU][en_US:]Captcha for website (sеlect image)[:en_US]
description: [ru_RU:]Captcha для сайта с выбором верной картинки[:ru_RU][en_US:]Captcha for website with a correct image selection[:en_US]
version: 1.0
category: [ru_RU:]Безопасность[:ru_RU][en_US:]Security[:en_US]
cat: secur
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* BD */
add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_sitecaptcha_image');
function bd_pn_moduls_active_sitecaptcha_image(){
global $wpdb;	
	
	$table_name = $wpdb->prefix ."sitecaptcha_user";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT ,
		`createdate` datetime NOT NULL,
		`sess_hash` varchar(150) NOT NULL,
		`img1` varchar(150) NOT NULL,
		`img2` varchar(150) NOT NULL,
		`img3` varchar(150) NOT NULL,		
		`num1` varchar(150) NOT NULL,
		`num2` varchar(150) NOT NULL,
		`num3` varchar(150) NOT NULL,
		`uslov` longtext NOT NULL,
		`variant` varchar(150) NOT NULL,
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);
	
	$table_name = $wpdb->prefix ."sitecaptcha_images";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT ,
		`uslov` longtext NOT NULL,
		`img1` varchar(250) NOT NULL,
		`img2` varchar(250) NOT NULL,
		`img3` varchar(250) NOT NULL,
		`variant` int(1) NOT NULL default '1',
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);
	
}
/* end BD */

add_filter('captcha_settings', 'captcha_settings_sitecaptcha_sci');
function captcha_settings_sitecaptcha_sci($ind){
	return 1;
}

/* 
Подключаем к меню
*/
add_action('admin_menu', 'pn_adminpage_sitecaptcha_image');
function pn_adminpage_sitecaptcha_image(){
global $premiumbox;	
	
	$hook = add_menu_page(__('Choosing picture captcha','pn'), __('Choosing picture captcha','pn'), 'administrator', 'pn_scimage_variants', array($premiumbox, 'admin_temp'));  
	$hook = add_submenu_page("pn_scimage_variants", __('Captcha options','pn'), __('Captcha options','pn'), 'administrator', 'pn_scimage_variants', array($premiumbox, 'admin_temp'));  
	add_action( "load-$hook", 'pn_trev_hook' );	

	add_submenu_page("pn_scimage_variants", __('Add captcha options','pn'), __('Add captcha options','pn'), 'administrator', 'pn_scimage_add_variants', array($premiumbox, 'admin_temp'));	

}

global $premiumbox;	
$premiumbox->file_include($path.'/list'); 
$premiumbox->file_include($path.'/add');
$premiumbox->file_include($path.'/settings'); 
$premiumbox->file_include($path.'/function');