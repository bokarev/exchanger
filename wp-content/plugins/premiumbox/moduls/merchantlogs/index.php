<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]!Не активируйте без необходимости! Логи мерчантов[:ru_RU][en_US:]!Do not activate without any reason! Merchant logs[:en_US]
description: [ru_RU:]!Не активируйте без необходимости! Логирование обращений мерчантов, которые присылают платежные системы после оплаты.[:ru_RU][en_US:]!Do not activate without any reason! Logging requests of those merchants who send payment systems right after making a payment.[:en_US]
version: 0.2
category: [ru_RU:]Заявки[:ru_RU][en_US:]Orders[:en_US]
cat: req
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* BD */
add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_merchantlogs');
function bd_pn_moduls_active_merchantlogs(){
global $wpdb;	
	
	$table_name= $wpdb->prefix ."merchant_logs";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		`createdate` datetime NOT NULL,
		`mdata` longtext NOT NULL,
		`merchant` varchar(150) NOT NULL,
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);	
	
}
/* end BD */
 
add_action('admin_menu', 'pn_adminpage_merchantlogs', 13);
function pn_adminpage_merchantlogs(){
global $premiumbox;	
	
	if(current_user_can('administrator') or current_user_can('pn_bids')){
		$hook = add_submenu_page("pn_bids", __('Merchant log','pn'), __('Merchant log','pn'), 'read', "pn_merchantlogs", array($premiumbox, 'admin_temp'));
		add_action( "load-$hook", 'pn_trev_hook' );
	}
}

/* логируем */
add_action('merchant_logs','merchantlogs_merchant_logs',10); 
function merchantlogs_merchant_logs($merchant=''){
global $wpdb;
	
	$arr = array();
	$arr['createdate'] = current_time('mysql');
	$arr['mdata'] = pn_strip_input(http_build_query($_REQUEST));
	$arr['merchant'] = is_extension_name($merchant);
	$wpdb->insert($wpdb->prefix.'merchant_logs', $arr);
	
}
/* end логируем */

/* cron */
function del_merchantlogs(){
global $wpdb;

	$count_day = apply_filters('delete_merchantlogs_day', 180);
	if($count_day > 0){
		$time = current_time('timestamp') - ($count_day * DAY_IN_SECONDS); 
		$ldate = date('Y-m-d H:i:s', $time);
		
		$wpdb->query("DELETE FROM ".$wpdb->prefix."merchant_logs WHERE createdate < '$ldate'");
	}
} 

add_filter('mycron_1day', 'mycron_1day_del_merchantlogs');
function mycron_1day_del_merchantlogs($filters){
	
	$filters['del_merchantlogs'] = __('Delete merchant log','pn');
	
	return $filters;
}
/* end cron */

global $premiumbox;
$premiumbox->file_include($path.'/list');