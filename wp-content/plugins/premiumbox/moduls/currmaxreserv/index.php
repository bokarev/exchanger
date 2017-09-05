<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Отображаемое значение резерва валюты[:ru_RU][en_US:]Displayed value of the currency reserve[:en_US]
description: [ru_RU:]Отображаемое значение резерва валюты[:ru_RU][en_US:]Displayed value of the currency reserve[:en_US]
version: 1.0
category: [ru_RU:]Валюты[:ru_RU][en_US:]Currency[:en_US]
cat: currency
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_currmaxreserv');
function bd_pn_moduls_active_currmaxreserv(){
global $wpdb;	
	
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."valuts LIKE 'max_reserv'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."valuts ADD `max_reserv` varchar(50) NOT NULL default '0'");
    }
	
}

add_filter('pn_valuts_addform', 'currmaxreserv_pn_valuts_addform', 10, 2);
function currmaxreserv_pn_valuts_addform($options, $data){
	
	if(isset($options['bottom_title'])){
		unset($options['bottom_title']);
	}	
	$options[] = array(
		'view' => 'line',
		'colspan' => 2,
	);	
	$options[] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	$options['max_reserv'] = array(
		'view' => 'input',
		'title' => __('Displayed value of the currency reserve','pn'),
		'default' => is_isset($data, 'max_reserv'),
		'name' => 'max_reserv',
	);
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);	
	
	return $options;
}

add_filter('pn_valuts_addform_post', 'currmaxreserv_valuts_addform_post');
function currmaxreserv_valuts_addform_post($array){
	
	$array['max_reserv'] = is_my_money(is_param_post('max_reserv'));	
	
	return $array;
}

add_filter('get_valut_reserv', 'get_valut_reserv_currmaxreserv', 10, 3);
function get_valut_reserv_currmaxreserv($reserv, $data, $decimal){
	
	$max = is_my_money($data->max_reserv);
	if($max > 0){
		if($reserv > $max){
			$reserv = $max;
		}
	}			
	
	return is_my_money($reserv, $decimal);
}										