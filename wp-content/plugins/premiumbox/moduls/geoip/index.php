<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]GEO IP[:ru_RU][en_US:]GEO IP[:en_US]
description: [ru_RU:]Определение страны пользователя[:ru_RU][en_US:]User's country location[:en_US]
version: 1.0
category: [ru_RU:]Направления обменов[:ru_RU][en_US:]Exchange directions[:en_US]
cat: naps
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* BD */
add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_geoip');
function bd_pn_moduls_active_geoip(){
global $wpdb;	

	$table_name = $wpdb->prefix ."geoip_blackip";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT ,
		`theip` varchar(250) NOT NULL,
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);

	$table_name = $wpdb->prefix ."geoip_whiteip";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT ,
		`theip` varchar(250) NOT NULL,
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);	
	
	$table_name = $wpdb->prefix ."geoip_template";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT ,
		`temptitle` longtext NOT NULL,
		`title` longtext NOT NULL,
		`content` longtext NOT NULL,
		`default_temp` int(1) NOT NULL default '0',
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);	
	
	$table_name = $wpdb->prefix ."geoip_iplist";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT ,
		`before_cip` bigint(20) NOT NULL default '0',
		`after_cip` bigint(20) NOT NULL default '0',
		`before_ip` varchar(250) NOT NULL,
		`after_ip` varchar(250) NOT NULL,
		`country_attr` varchar(20) NOT NULL,
		`place_id` bigint(20) NOT NULL default '0',
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);	

	$table_name = $wpdb->prefix ."geoip_country";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT ,
		`attr` varchar(20) NOT NULL,
		`title` longtext NOT NULL,
		`status` int(1) NOT NULL default '0',
		`temp_id` bigint(20) NOT NULL default '0',
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);

/*
not_country - запрещенные страны
only_country - разрешенные страны
*/	
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'not_country'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `not_country` longtext NOT NULL");
    }	
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'only_country'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `only_country` longtext NOT NULL");
    }	
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."bids LIKE 'user_country'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."bids ADD `user_country` varchar(10) NOT NULL");
    }	
}

add_action('pn_bd_activated', 'bd_pn_moduls_migrate_geoip');
function bd_pn_moduls_migrate_geoip(){
global $wpdb;
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'not_country'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `not_country` longtext NOT NULL");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'only_country'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `only_country` longtext NOT NULL");
    }	
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."bids LIKE 'user_country'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."bids ADD `user_country` varchar(10) NOT NULL");
    }		
}
/* end BD */

add_filter('pn_caps','geoip_pn_caps');
function geoip_pn_caps($pn_caps){
	$pn_caps['pn_geoip'] = __('Use GEO IP','pn');
	return $pn_caps;
}

/* 
Подключаем к меню
*/
add_action('admin_menu', 'pn_adminpage_geoip');
function pn_adminpage_geoip(){
global $premiumbox;	
	if(current_user_can('administrator') or current_user_can('pn_geoip')){
		add_menu_page(__('GEO IP','pn'), __('GEO IP','pn'), 'read', 'pn_geoip', array($premiumbox, 'admin_temp'), $premiumbox->get_icon_link('geoip'));  
		add_submenu_page("pn_geoip", __('Settings','pn'), __('Settings','pn'), 'read', "pn_geoip", array($premiumbox, 'admin_temp'));
		$hook = add_submenu_page("pn_geoip", __('Templates','pn'), __('Templates','pn'), 'read', "pn_geoip_temp", array($premiumbox, 'admin_temp'));
		add_action( "load-$hook", 'pn_trev_hook' );
		add_submenu_page("pn_geoip", __('Add template','pn'), __('Add template','pn'), 'read', "pn_geoip_addtemp", array($premiumbox, 'admin_temp'));
		$hook = add_submenu_page("pn_geoip", __('Blacklist','pn'), __('Blacklist','pn'), 'read', "pn_geoip_blacklist", array($premiumbox, 'admin_temp'));
		add_action( "load-$hook", 'pn_trev_hook' );
		add_submenu_page("pn_geoip", __('Block IP','pn'), __('Block IP','pn'), 'read', "pn_geoip_addblacklist", array($premiumbox, 'admin_temp'));	
		$hook = add_submenu_page("pn_geoip", __('White list','pn'), __('White list','pn'), 'read', "pn_geoip_whitelist", array($premiumbox, 'admin_temp'));
		add_action( "load-$hook", 'pn_trev_hook' );
		add_submenu_page("pn_geoip", __('Allow IP','pn'), __('Allow IP','pn'), 'read', "pn_geoip_addwhitelist", array($premiumbox, 'admin_temp'));
		add_submenu_page("pn_geoip", __('Import countries','pn'), __('Import countries','pn'), 'read', "pn_geoip_import", array($premiumbox, 'admin_temp'));	
	}
}

global $premiumbox;
$premiumbox->file_include($path.'/function');
$premiumbox->file_include($path.'/settings');
$premiumbox->file_include($path.'/import');
$premiumbox->file_include($path.'/temps');
$premiumbox->file_include($path.'/addtemp');
$premiumbox->file_include($path.'/blacklist');
$premiumbox->file_include($path.'/add_blacklist');
$premiumbox->file_include($path.'/whitelist');
$premiumbox->file_include($path.'/add_whitelist');
$premiumbox->file_include($path.'/filters');