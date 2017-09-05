<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Внутренний счет[:ru_RU][en_US:]Internal account[:en_US]
description: [ru_RU:]Внутренний счет для Личного кабинета[:ru_RU][en_US:]Internal account shown in Personal account[:en_US]
version: 1.0
category: [ru_RU:]Направления обменов[:ru_RU][en_US:]Exchange directions[:en_US]
cat: naps
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* BD */
add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_domacc');
function bd_pn_moduls_active_domacc(){
global $wpdb, $premiumbox;	
	
	/* 1- расход(человек отдает), 2-приход(человек получает) */
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."bids LIKE 'domacc1'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."bids ADD `domacc1` int(1) NOT NULL default '0'");
    }	 
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."bids LIKE 'domacc2'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."bids ADD `domacc2` int(1) NOT NULL default '0'");
    }	
	
}

add_action('pn_bd_activated', 'bd_pn_moduls_migrate_domacc');
function bd_pn_moduls_migrate_domacc(){
global $wpdb;

	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."bids LIKE 'domacc1'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."bids ADD `domacc1` int(1) NOT NULL default '0'");
    }	 
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."bids LIKE 'domacc2'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."bids ADD `domacc2` int(1) NOT NULL default '0'");
    }
	
}

add_filter('pn_tech_pages', 'list_tech_pages_domacc');
function list_tech_pages_domacc($pages){
 
	$pages[] = array(
	    'post_name' => 'domacc',
	    'post_title' => '[ru_RU:]Внутренний счет[:ru_RU][en_US:]Internal account[:en_US]',
	    'post_content' => '[domacc_page]',
		'post_template'   => 'pn-pluginpage.php',
	);	
	
	return $pages;
}
/* end BD */

add_filter('change_bids_filter_list', 'domacc_change_bids_filter_list'); 
function domacc_change_bids_filter_list($lists){
global $wpdb;
	
	$options = array(
		'0' => '--'. __('All','pn').'--',
		'1' => __('Yes, incoming','pn'),
		'2' => __('Yes, outcoming','pn'),
		'3' => __('No','pn'),
	);
	$lists['other']['domacc'] = array(
		'title' => __('Internal account','pn'),
		'name' => 'domacc',
		'options' => $options,
		'view' => 'select',
		'work' => 'options',
	);	
	
	return $lists;
}

add_filter('where_request_sql_bids', 'where_request_sql_bids_domacc',0,2);
function where_request_sql_bids_domacc($where, $pars_data){
global $wpdb;	
	
	$domacc = intval(is_isset($pars_data,'domacc'));
	if($domacc == 1){
		$where .= " AND {$wpdb->prefix}bids.domacc1 = '1'";
	} elseif($domacc == 2){
		$where .= " AND {$wpdb->prefix}bids.domacc2 = '1'";
	} elseif($domacc == 3){
		$where .= " AND {$wpdb->prefix}bids.domacc1 = '0' AND {$wpdb->prefix}bids.domacc2 = '0'";
	}
	
	return $where;
} 

add_filter('account_list_pages','domacc_account_list_pages');
function domacc_account_list_pages($account_list_pages){	
	
	$new_list = array();
	foreach($account_list_pages as $key => $val){
		if($key == 'userxch'){
			$new_list['domacc'] = array(
				'title' => '',
				'url' => '',
				'type' => 'page',
			);
		}
		$new_list[$key] = $val;
	}
	
	return $new_list;
}
 
function get_user_domacc($user_id, $vtype_id){
global $wpdb;

	$user_id = intval($user_id);
	$vtype_id = intval($vtype_id);
	$sum1 = $wpdb->get_var("SELECT SUM(summ2c) FROM ".$wpdb->prefix."bids WHERE status = 'success' AND user_id='$user_id' AND domacc2='1' AND vtype2i='$vtype_id'");
	$sum2 = $wpdb->get_var("SELECT SUM(summ1c) FROM ".$wpdb->prefix."bids WHERE status IN('realpay','success','verify') AND user_id='$user_id' AND domacc1='1' AND vtype1i='$vtype_id'");
	$sum = is_my_money($sum1 - $sum2);
	$sum = apply_filters('get_user_domacc', $sum, $user_id, $vtype_id);
	
	return $sum;
}

add_filter('onebid_icons','onebid_icons_domacc',10,2);
function onebid_icons_domacc($onebid_icon, $item){
global $premiumbox;
	
	if(isset($item->domacc)){
		if($item->domacc1 != 0 or $item->domacc2 != 0){
			$onebid_icon['domacc'] = array(
				'type' => 'label',
				'title' => __('Internal account','pn'),
				'image' => $premiumbox->plugin_url . 'moduls/domacc/images/domacc.png',
			);	
		}
	}
	
	return $onebid_icon;
}

global $premiumbox;
$premiumbox->file_include($path . '/shortcode');
$premiumbox->file_include($path . '/users');