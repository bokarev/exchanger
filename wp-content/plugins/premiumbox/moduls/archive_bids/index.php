<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Архивация старых заявок[:ru_RU][en_US:]Archiving of old requests[:en_US]
description: [ru_RU:]Архивация старых заявок со сроком создания более двух месяцев[:ru_RU][en_US:]Archiving of old requests with the creation date longer than two months[:en_US]
version: 1.0
category: [ru_RU:]Заявки[:ru_RU][en_US:]Orders[:en_US]
cat: req
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* BD */
add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_archive_bids');
function bd_pn_moduls_active_archive_bids(){
global $wpdb;	
	
	$table_name= $wpdb->prefix ."archive_bids";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT ,
		`archive_date` datetime NOT NULL,
		`bid_id` bigint(20) NOT NULL default '0',
		`user_id` bigint(20) NOT NULL default '0',
		`ref_id` bigint(20) NOT NULL default '0',
		`account1` varchar(250) NOT NULL,
		`account2` varchar(250) NOT NULL,
		`first_name` varchar(150) NOT NULL,
		`last_name` varchar(150) NOT NULL,
		`second_name` varchar(150) NOT NULL,
		`user_phone` varchar(150) NOT NULL,
		`user_skype` varchar(150) NOT NULL,
		`user_email` varchar(150) NOT NULL,
		`user_passport` varchar(250) NOT NULL,
		`archive_content` longtext NOT NULL,
		`archive_meta` longtext NOT NULL,
		`status` varchar(35) NOT NULL,
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);
	
}
/* end BD */

add_action( 'delete_user', 'delete_user_archive_bids');
function delete_user_archive_bids($user_id){
global $wpdb;

    $wpdb->query("DELETE FROM ". $wpdb->prefix ."archive_data WHERE item_id='$user_id' AND meta_key IN('user_exsum','user_bids_success','domacc1_vtype','domacc2_vtype')");
	
}

add_filter('user_sum_exchanges', 'user_sum_exchanges_archive_bids', 1, 3);
function user_sum_exchanges_archive_bids($d_sum, $sum, $user_id){ 
global $wpdb;
	
	$count = $wpdb->get_var("SELECT SUM(meta_value) FROM ".$wpdb->prefix."archive_data WHERE meta_key='user_exsum' AND item_id='$user_id'");
	$d_sum = $d_sum + $count;
	$d_sum = is_my_money($d_sum);
	
	return $d_sum;
}

add_filter('user_count_exchanges', 'user_count_exchanges_archive_bids', 1, 2);
function user_count_exchanges_archive_bids($sum, $user_id){
global $wpdb;
	
	$count = $wpdb->get_var("SELECT SUM(meta_value) FROM ".$wpdb->prefix."archive_data WHERE meta_key='user_bids_success' AND item_id='$user_id'");
	$sum = $sum + $count;
	$sum = is_my_money($sum);
	
	return $sum;
}

add_filter('partner_money', 'partner_money_archive_bids', 1, 3);
add_filter('partner_money_now', 'partner_money_archive_bids', 1, 3);
add_filter('get_partner_earn_all', 'partner_money_archive_bids', 1, 3);
function partner_money_archive_bids($sum, $sum2, $ref_id){
global $wpdb;
	
	$count = $wpdb->get_var("SELECT SUM(meta_value) FROM ".$wpdb->prefix."archive_data WHERE meta_key='pbids_sum' AND item_id='$ref_id'");
	$sum = $sum + $count;
	$sum = is_my_money($sum);
	
	return $sum;
}
 
add_filter('user_sum_refobmen', 'user_sum_refobmen_archive_bids', 1, 3);
function user_sum_refobmen_archive_bids($sum, $sum2, $ref_id){
global $wpdb;
	
	$count = $wpdb->get_var("SELECT SUM(meta_value) FROM ".$wpdb->prefix."archive_data WHERE meta_key='pbids_exsum' AND item_id='$ref_id'");
	$sum = $sum + $count;
	$sum = is_my_money($sum);
	
	return $sum;
}

add_filter('user_count_refobmen', 'user_count_refobmen_archive_bids', 1, 2);
function user_count_refobmen_archive_bids($sum, $ref_id){
global $wpdb;
	
	$count = $wpdb->get_var("SELECT SUM(meta_value) FROM ".$wpdb->prefix."archive_data WHERE meta_key='pbids' AND item_id='$ref_id'");
	$sum = $sum + $count;
	$sum = is_my_money($sum);
	
	return $sum;
}

/* тип валюты */
add_action('pn_vtypes_delete','archive_bids_pn_vtypes_delete');
function archive_bids_pn_vtypes_delete($id){
global $wpdb;

	$wpdb->query("DELETE FROM ".$wpdb->prefix."archive_data WHERE item_id = '$id' AND meta_key IN('vtype_give','vtype_get')");
}

add_filter('get_reserv_vtype', 'get_reserv_vtype_archive_bids', 1, 2);
function get_reserv_vtype_archive_bids($sum, $vtype_id){
global $wpdb;
	
	$count = $wpdb->get_var("SELECT SUM(meta_value) FROM ".$wpdb->prefix."archive_data WHERE meta_key='vtype_give' AND meta_key2='success' AND item_id='$vtype_id'");
	$count2 = $wpdb->get_var("SELECT SUM(meta_value) FROM ".$wpdb->prefix."archive_data WHERE meta_key='vtype_get' AND meta_key2='success' AND item_id='$vtype_id'");
	$sum = $sum + $count - $count2;
	$sum = is_my_money($sum);
	
	return $sum;
}
/* end тип валюты */

/* валюта */
add_filter('update_valut_reserv', 'update_valut_reserv_archive_bids', 1, 4);
function update_valut_reserv_archive_bids($sum, $valut_id, $f_st1, $f_st2){
global $wpdb;
	
	$sum1 = $wpdb->get_var("SELECT SUM(meta_value) FROM ".$wpdb->prefix."archive_data WHERE meta_key='valut_give' AND meta_key2 IN($f_st1) AND item_id='$valut_id'");
	$sum2 = $wpdb->get_var("SELECT SUM(meta_value) FROM ".$wpdb->prefix."archive_data WHERE meta_key='valut_get' AND meta_key2 IN($f_st2) AND item_id='$valut_id'");
	$sum = $sum + $sum1 - $sum2;
	$sum = is_my_money($sum);
	
	return $sum;
}

add_filter('get_valut_in', 'get_valut_in_archive_bids', 1, 4);
function get_valut_in_archive_bids($sum, $valut_id, $status){
global $wpdb;
	
	$count = $wpdb->get_var("SELECT SUM(meta_value) FROM ".$wpdb->prefix."archive_data WHERE meta_key='valut_give' AND meta_key2='$status' AND item_id='$valut_id'");
	$sum = $sum + $count;
	$sum = is_my_money($sum);
	
	return $sum;
}

add_filter('get_valut_out', 'get_valut_out_archive_bids', 1, 4);
function get_valut_out_archive_bids($sum, $valut_id, $status){
global $wpdb;
	
	$count = $wpdb->get_var("SELECT SUM(meta_value) FROM ".$wpdb->prefix."archive_data WHERE meta_key='valut_get' AND meta_key2='$status' AND item_id='$valut_id'");
	$sum = $sum + $count;
	$sum = is_my_money($sum);
	
	return $sum;
}
/* end валюта */

/* направления */
add_action('pn_naps_delete','archive_bids_pn_naps_delete');
function archive_bids_pn_naps_delete($id){
global $wpdb;

	$wpdb->query("DELETE FROM ".$wpdb->prefix."archive_data WHERE meta_key IN('naps_give','naps_get') AND item_id = '$id'");
}

add_filter('get_summ_naps_all', 'get_summ_naps_all_archive_bids', 1, 5);
function get_summ_naps_all_archive_bids($sum, $naps_id, $method, $filter_status, $date){
global $wpdb;
	
	$date = trim($date);
	if(!$date){
		if($method == 'in'){
			$sum1 = $wpdb->get_var("SELECT SUM(meta_value) FROM ".$wpdb->prefix."archive_data WHERE item_id='$naps_id' AND meta_key='naps_give' AND meta_key2 IN($filter_status)");
		} else {
			$sum1 = $wpdb->get_var("SELECT SUM(meta_value) FROM ".$wpdb->prefix."archive_data WHERE item_id='$naps_id' AND meta_key='naps_get' AND meta_key2 IN($filter_status)");
		}		
		$sum = is_my_money($sum + $sum1);
	}
	
	return $sum;
}
/* end направления */

/* dom acc */
add_filter('get_user_domacc', 'get_user_domacc_archive_bids', 1, 3);
function get_user_domacc_archive_bids($sum, $user_id, $vtype_id){
global $wpdb;

	$sum1 = $wpdb->get_var("SELECT SUM(meta_value) FROM ".$wpdb->prefix."archive_data WHERE item_id='$user_id' AND meta_key='domacc2_vtype' AND meta_key2 = 'success' AND meta_key3='$vtype_id'");
	$sum2 = $wpdb->get_var("SELECT SUM(meta_value) FROM ".$wpdb->prefix."archive_data WHERE item_id='$user_id' AND meta_key='domacc1_vtype' AND meta_key2 IN('realpay','success','verify') AND meta_key3='$vtype_id'");
	$sum3 = is_my_money($sum + $sum1 - $sum2);
	
	return $sum3;
}
/* end dom acc */

global $premiumbox;
$premiumbox->file_include($path.'/cron');
$premiumbox->file_include($path.'/list');