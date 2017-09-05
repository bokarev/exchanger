<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Логирование изменения курсов обмена[:ru_RU][en_US:]Logging changes in exchange rates[:en_US]
description: [ru_RU:]Логирование изменения курсов обмена[:ru_RU][en_US:]Logging changes in exchange rates[:en_US]
version: 1.0
category: [ru_RU:]Направления обменов[:ru_RU][en_US:]Exchange directions[:en_US]
cat: naps
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* BD */
add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_courselogs');
function bd_pn_moduls_active_courselogs(){
global $wpdb;	
	
	$table_name= $wpdb->prefix ."course_logs";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		`createdate` datetime NOT NULL,
		`user_id` bigint(20) NOT NULL default '0',
		`user_login` varchar(150) NOT NULL,
		`naps_id` bigint(20) default '0',
		`v1` bigint(20) NOT NULL default '0',
		`v2` bigint(20) NOT NULL default '0',
		`lcurs1` varchar(150) NOT NULL default '0',
		`lcurs2` varchar(150) NOT NULL default '0',
		`curs1` varchar(150) NOT NULL default '0',
		`curs2` varchar(150) NOT NULL default '0',		
		`who` varchar(50) NOT NULL,
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);
	
}
/* end BD */
 
add_action('admin_menu', 'pn_adminpage_courselogs', 12);
function pn_adminpage_courselogs(){
global $premiumbox;	
	
	if(current_user_can('administrator') or current_user_can('pn_naps')){
		$hook = add_submenu_page("pn_naps", __('Logging changes in exchange rates','pn'), __('Logging changes in exchange rates','pn'), 'read', "pn_courselogs", array($premiumbox, 'admin_temp'));
		add_action( "load-$hook", 'pn_trev_hook' );
	}
}

/* логируем */
add_action('naps_change_course','courselogs_naps_change_course',10,5);  
function courselogs_naps_change_course($naps_id, $naps, $curs1, $curs2, $who=''){
global $wpdb;
	
	if(!isset($naps->id)){
		$naps = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."naps WHERE id='$naps_id'");
	}
	if(isset($naps->id)){
		$ui = wp_get_current_user();
		$user_id = intval($ui->ID);		
				
		$lcurs1 = is_my_money($naps->curs1); 
		$lcurs2 = is_my_money($naps->curs2);
 		if($lcurs1 != $curs1 or $lcurs2 != $curs2){
				
			$arr = array();
			$arr['createdate'] = current_time('mysql');
			$arr['naps_id'] = $naps->id;
			$arr['user_id'] = $user_id;
			$arr['user_login'] = is_isset($ui,'user_login');
			$arr['v1'] = $naps->valut_id1;
			$arr['v2'] = $naps->valut_id2;
			$arr['lcurs1'] = $lcurs1;
			$arr['lcurs2'] = $lcurs2;
			$arr['curs1'] = is_my_money($curs1);
			$arr['curs2'] = is_my_money($curs2);			
			$arr['who'] = pn_strip_input($who);
			$wpdb->insert($wpdb->prefix . 'course_logs', $arr);
		
		}
	}
}
/* end логируем заявки */

/* cron */
function del_courselogs(){
global $wpdb;

	$count_day = apply_filters('delete_courselogs_day', 60);
	if($count_day > 0){
		$time = current_time('timestamp') - ($count_day * DAY_IN_SECONDS); 
		$ldate = date('Y-m-d H:i:s', $time);
		
		$wpdb->query("DELETE FROM ".$wpdb->prefix."course_logs WHERE createdate < '$ldate'");
	}
} 

add_filter('mycron_1day', 'mycron_1day_del_courselogs');
function mycron_1day_del_courselogs($filters){
	
	$filters['del_courselogs'] = __('Deleting logs about changes in rates in direction of exchange','pn');
	
	return $filters;
}
/* end cron */

global $premiumbox;
$premiumbox->file_include($path.'/list');