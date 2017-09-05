<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Настройки вывода направлений обмена[:ru_RU][en_US:]Exchange direction output settings[:en_US]
description: [ru_RU:]Настройки вывода направлений обмена в таблице обмена[:ru_RU][en_US:]Exchange direction output settings in exchange table[:en_US]
version: 1.0
category: [ru_RU:]Направления обменов[:ru_RU][en_US:]Exchange directions[:en_US]
cat: naps
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

add_filter('pn_exchange_config_option', 'currtable_exchange_config_option');
function currtable_exchange_config_option($options){
global $wpdb, $premiumbox;
	
	$arr = array();
	$arr[0] = '---'. __('No item','pn') .'---';
	$items = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."naps WHERE autostatus = '1' AND naps_status='1' ORDER BY site_order1 ASC");
	foreach($items as $item){
		$arr[$item->id] = pn_strip_input($item->tech_name);
	}
	$options['currtable'] = array(
		'view' => 'select',
		'title' => __('Exchange direction in exchange table is by default','pn'),
		'options' => $arr,
		'default' => $premiumbox->get_option('exchange','currtable'),
		'name' => 'currtable',
	);	
	$options[] = array(
		'view' => 'line',
		'colspan' => 2,
	);
	if(isset($options['bottom_title'])){
		unset($options['bottom_title']);
	}	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);	
	
	return $options;
}

add_action('pn_exchange_config_option_post', 'currtable_exchange_config_option_post');
function currtable_exchange_config_option_post(){
global $wpdb, $premiumbox;
	
	$curr_id = intval(is_param_post('currtable'));
	$premiumbox->update_option('exchange','currtable',$curr_id);
	
	$v1 = $v2 = 0;
	
	if($curr_id){
		$item = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."naps WHERE autostatus = '1' AND naps_status='1' AND id='$curr_id'");
		if(isset($item->id)){
			$v_data = get_valuts_data(array($item->valut_id1, $item->valut_id2));
			
			$v1 = is_xml_value($v_data[$item->valut_id1]->xml_value);
			$v2 = is_xml_value($v_data[$item->valut_id2]->xml_value);
		}
	}
	
	$premiumbox->update_option('currtable','v1',$v1);
	$premiumbox->update_option('currtable','v2',$v2);
	
}

add_filter('pn_mobile_exchange_config_option', 'currtable_mobile_exchange_config_option');
function currtable_mobile_exchange_config_option($options){
global $wpdb, $premiumbox;
	
	$arr = array();
	$arr[0] = '---'. __('No item','pn') .'---';
	$items = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."naps WHERE autostatus = '1' AND naps_status='1' ORDER BY site_order1 ASC");
	foreach($items as $item){
		$arr[$item->id] = pn_strip_input($item->tech_name);
	}
	$options['currtable'] = array(
		'view' => 'select',
		'title' => __('Exchange direction in exchange table is by default','pn'),
		'options' => $arr,
		'default' => $premiumbox->get_option('mobile','currtable'),
		'name' => 'currtable',
	);	
	$options[] = array(
		'view' => 'line',
		'colspan' => 2,
	);
	if(isset($options['bottom_title'])){
		unset($options['bottom_title']);
	}	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);	
	
	return $options;
}

add_action('pn_mobile_exchange_config_option_post', 'currtable_mobile_exchange_config_option_post');
function currtable_mobile_exchange_config_option_post(){
global $wpdb, $premiumbox;
	
	$curr_id = intval(is_param_post('currtable'));
	$premiumbox->update_option('mobile','currtable',$curr_id);
	
	$v1 = $v2 = 0;
	
	if($curr_id){
		$item = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."naps WHERE autostatus = '1' AND naps_status='1' AND id='$curr_id'");
		if(isset($item->id)){
			$v_data = get_valuts_data(array($item->valut_id1, $item->valut_id2));
			
			$v1 = is_xml_value($v_data[$item->valut_id1]->xml_value);
			$v2 = is_xml_value($v_data[$item->valut_id2]->xml_value);
		}
	}
	
	$premiumbox->update_option('currtable','mob_v1',$v1);
	$premiumbox->update_option('currtable','mob_v2',$v2);
	
}

add_filter('get_exchange_table_vtypes', 'currtable_get_exchange_table_vtypes', 0, 2);
function currtable_get_exchange_table_vtypes($arr, $vers){
global $premiumbox;
	
	if($vers == 'web'){
		$v1 = is_xml_value($premiumbox->get_option('currtable','v1'));
		$v2 = is_xml_value($premiumbox->get_option('currtable','v2'));
	} else {
		$v1 = is_xml_value($premiumbox->get_option('currtable','mob_v1'));
		$v2 = is_xml_value($premiumbox->get_option('currtable','mob_v2'));		
	}
	
	if(!$arr['from'] and !$arr['to']){
		$arr = array(
			'from' => $v1,
			'to' => $v2,
		);
	}
	
	return $arr;
}