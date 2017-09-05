<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Авто обновление резервов валют (по Cron)[:ru_RU][en_US:]Currency reserve auto-update (Cron)[:en_US]
description: [ru_RU:]Авто обновление резервов валют (по Cron)[:ru_RU][en_US:]Currency reserve auto-update (Cron)[:en_US]
version: 0.1
category: [ru_RU:]Валюты[:ru_RU][en_US:]Currency[:en_US]
cat: currency
*/

add_filter('valuts_manage_ap_columns', 'cres_valuts_manage_ap_columns');
function cres_valuts_manage_ap_columns($columns){
	$columns['cres'] = __('Cron Link','pn');
	return $columns;
}

add_filter('valuts_manage_ap_col', 'cres_valuts_manage_ap_col', 10, 3);
function cres_valuts_manage_ap_col($html, $column_name, $item){
	if($column_name == 'cres'){
		return '<a href="'. get_site_url_or(). '/request-cres.html?id='. $item->id . get_hash_cron('&') .'" class="button" target="_blank">'. __('Link','pn') .'</a>'; 
	}
	return $html;
}

add_action('myaction_request_cres','cres_request_cron');
function cres_request_cron(){
global $wpdb;	
	$data_id = intval(is_param_get('id'));
	if($data_id and check_hash_cron()){	
		update_valut_reserv($data_id);	
	}	
	_e('Done','pn');
}