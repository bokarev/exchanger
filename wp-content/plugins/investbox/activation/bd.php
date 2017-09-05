<?php
if( !defined( 'ABSPATH')){ exit(); }		
	
global $wpdb;
$prefix = $wpdb->prefix;

/* настройки плагина */	
	$table_name= $wpdb->prefix ."inex_change";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		`meta_key` varchar(250) NOT NULL,
		`meta_key2` varchar(250) NOT NULL,
		`meta_value` longtext NOT NULL,
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);	

/* 
включенные системы
 
title - название
valut - валюта расчета
gid - глобальный идентификатор в системе
*/
	$table_name= $wpdb->prefix ."inex_system";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		`title` tinytext NOT NULL,
		`valut` varchar(250) NOT NULL,
		`gid` varchar(250) NOT NULL,
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);
	
/* 
тарифы
 
title - название
minsum - минимальная сумма
maxsum - максимальная сумма
gid - глобальный идентификатор в системе
gtitle - название системы
gvalut - название валюты
mpers - процент в месяц
cdays - количество месяцев
status - 0- отключен, 1- включен
*/
	$table_name= $wpdb->prefix ."inex_tars";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		`title` tinytext NOT NULL,
		`minsum` varchar(250) NOT NULL default '0',
		`maxsum` varchar(250) NOT NULL default '0',
		`maxsumtar` varchar(250) NOT NULL default '0',
		`gid` varchar(250) NOT NULL,
		`gtitle` tinytext NOT NULL,
		`gvalut` varchar(250) NOT NULL,
		`mpers` varchar(250) NOT NULL,
		`cdays` bigint(20) NOT NULL default '0',
		`status` int(1) NOT NULL default '1',
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);

/* 
депозиты
 
createdate - дата создания депозита
indate - дата оплаты депозита и старта
enddate - дата окончания депозита
outdate - дата выплаты депозита
couday - кол-во дней
pers - процент
insumm - сумма депозита
outsumm - сумма с процентами
plussumm - сумма вознаграждения(доход)
user_id - id юзера
user_login - login юзера
user_email - email юзера
user_schet - номер счета
gid - глобальный идентификатор в системе
gtitle - название системы
gvalut - название валюты
paystatus - статус оплаты (0- не оплачен, 1- оплачен)
zakstatus - статус заказа денег (0- не заказан, 1 - заказан)
vipstatus - статус выплаты (0- не выплачен, 1 - выплачен)
*/
	$table_name= $wpdb->prefix ."inex_deposit";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		`createdate` datetime NOT NULL,
		`indate` datetime NOT NULL,
		`enddate` datetime NOT NULL,
		`outdate` datetime NOT NULL,
		`couday` int(5) NOT NULL default '0',
		`pers` varchar(250) NOT NULL,
		`insumm` varchar(250) NOT NULL default '0',
		`outsumm` varchar(250) NOT NULL default '0',
		`plussumm` varchar(250) NOT NULL default '0',
		`user_id` bigint(20) NOT NULL default '0',
		`user_login` varchar(250) NOT NULL,
		`user_email` varchar(250) NOT NULL,
		`user_schet` varchar(250) NOT NULL,
		`gid` varchar(250) NOT NULL,
		`gtitle` tinytext NOT NULL,
		`gvalut` varchar(250) NOT NULL,
		`paystatus` int(3) NOT NULL default '0',
		`vipstatus` int(3) NOT NULL default '0',
		`zakstatus` int(3) NOT NULL default '0',
		`locale` varchar(20) NOT NULL,
		`mail1` int(1) NOT NULL default '0',
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);	
