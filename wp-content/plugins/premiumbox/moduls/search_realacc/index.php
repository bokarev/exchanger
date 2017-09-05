<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Фильтр поиска по реальному счету оплаты[:ru_RU][en_US:]Filter by an existing wallet info[:en_US]
description: [ru_RU:]Фильтр поиска по реальному счету оплаты[:ru_RU][en_US:]Filter by an existing wallet info[:en_US]
version: 1.0
category: [ru_RU:]Заявки[:ru_RU][en_US:]Orders[:en_US]
cat: req
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

add_filter('change_bids_filter_list', 'srealacc_change_bids_filter_list'); 
function srealacc_change_bids_filter_list($lists){
global $wpdb;
	
	$lists['other']['pay_ac'] = array(
		'title' => __('Real account','pn'),
		'name' => 'pay_ac',
		'view' => 'input',
		'work' => 'input',
	);		
	
	return $lists;
}

add_filter('where_request_sql_bids', 'srealacc_where_request_sql_bids', 10,2);
function srealacc_where_request_sql_bids($where, $pars_data){
global $wpdb;	
	
	$pr = $wpdb->prefix;
	$pay_ac = pn_sfilter(pn_strip_input(is_isset($pars_data,'pay_ac')));
	if($pay_ac){
		$ids = array();
		$results = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."bids_meta WHERE meta_key='pay_ac' AND meta_value LIKE '%$pay_ac%'");
		foreach($results as $res){
			$ids[] = '"'. $res->item_id .'"';
		}
		if(count($ids) > 0){
			$res_join = join(',',$ids);
			$where .= " AND {$pr}bids.id IN ({$res_join})";
		}
	}
	
	return $where;
}