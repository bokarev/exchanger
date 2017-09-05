<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Логирование действий администратора[:ru_RU][en_US:]Logging the administrator activity[:en_US]
description: [ru_RU:]Логирование действий администратора[:ru_RU][en_US:]Logging the administrator activity[:en_US]
version: 1.0
category: [ru_RU:]Безопасность[:ru_RU][en_US:]Security[:en_US]
cat: secur
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_apbd');
function bd_pn_moduls_active_apbd(){
global $wpdb;
	
	$table_name = $wpdb->prefix ."db_admin_logs";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		`item_id` bigint(20) NOT NULL default '0',
		`tbl_name` varchar(250) NOT NULL default '0',
		`trans_type` int(1) NOT NULL default '0',
		`trans_date` datetime NOT NULL,
		`old_data` longtext NOT NULL,
		`new_data` longtext NOT NULL,
		`user_id` bigint(20) NOT NULL default '0',
		`user_login` longtext NOT NULL,
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);	
}

add_action('pn_bd_activated', 'bd_pn_moduls_migrate_apbd');
function bd_pn_moduls_migrate_apbd(){
global $wpdb;

	$table_name = $wpdb->prefix ."db_admin_logs";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		`item_id` bigint(20) NOT NULL default '0',
		`tbl_name` varchar(250) NOT NULL default '0',
		`trans_type` int(1) NOT NULL default '0',
		`trans_date` datetime NOT NULL,
		`old_data` longtext NOT NULL,
		`new_data` longtext NOT NULL,
		`user_id` bigint(20) NOT NULL default '0',
		`user_login` longtext NOT NULL,
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);
}

global $premiumbox;
$premiumbox->include_patch(__FILE__, 'function');
$premiumbox->include_patch(__FILE__, 'filters');
$premiumbox->include_patch(__FILE__, 'cron');