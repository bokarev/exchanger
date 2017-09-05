<?php
if( !defined( 'ABSPATH')){ exit(); } 

/*
title: [ru_RU:]!Не активируйте без необходимости! Код купона при авто выплате[:ru_RU][en_US:]!Do not activate without any reason! Coupon code given during automatic payout[:en_US]
description: [ru_RU:]!Не активируйте без необходимости! Отображение кода купона авто выплаты (BTC, Exmo и т.п.) в карточке заявки[:ru_RU][en_US:]!Do not activate it without any reason! Show automatic payout coupon code (BTC, EXMO etc.) in request form.[:en_US]
version: 1.0
category: [ru_RU:]Заявки[:ru_RU][en_US:]Orders[:en_US]
cat: req
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* BD */
add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_fixbtccode');
function bd_pn_moduls_active_fixbtccode(){
global $wpdb;	
	
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."bids LIKE 'btc_code'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."bids ADD `btc_code` varchar(250) NOT NULL");
    }
	
}
/* end BD */

add_action('merchant_create_coupon', 'merchant_create_coupon_fixbtccode', 10, 4);
function merchant_create_coupon_fixbtccode($coupon, $item, $merchant, $place){
global $wpdb;	
	if(isset($item->id) and $coupon){
		$bid_id = $item->id;
		$array = array();
		$array['btc_code'] = $coupon;
		$wpdb->update($wpdb->prefix ."bids", $array, array('id'=> $bid_id));
	}
}

add_filter('onebid_col1','onebid_col1_fixbtccode',99,3);
function onebid_col1_fixbtccode($actions, $item, $data_fs){
	
	if(isset($item->btc_code) and $item->btc_code){
		$actions['fixbtccode'] = array(
			'type' => 'text',
			'title' => __('Coupon code','pn'),
			'label' => '[fixbtccode]',
		);		
	}
	
	return $actions;
}

add_filter('get_bids_replace_text','get_bids_replace_text_fixbtccode',99,3);
function get_bids_replace_text_fixbtccode($text, $item, $data_fs){
	
	if(strstr($text, '[fixbtccode]')){
		$text = str_replace('[fixbtccode]', '<span class="onebid_item item_fixbtccode" data-clipboard-text="' . pn_strip_input($item->btc_code) . '">' . pn_strip_input($item->btc_code) . '</span>',$text);
	}
	
	return $text;
}

add_filter('pn_exchange_config_option', 'fixbtccode_exchange_config_option');
function fixbtccode_exchange_config_option($options){
global $premiumbox;	
	
	$options['fixbtccode'] = array(
		'view' => 'select',
		'title' => __('Display coupon code in exchange history of user','pn'),
		'options' => array('0'=>__('No','pn'),'1'=>__('Yes','pn')),
		'default' => $premiumbox->get_option('exchange','fixbtccode'),
		'name' => 'fixbtccode',
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

add_action('pn_exchange_config_option_post', 'fixbtccode_exchange_config_option_post');
function fixbtccode_exchange_config_option_post(){
global $premiumbox;
	
	$options = array('fixbtccode');
	foreach($options as $key){
		$val = intval(is_param_post($key));
		$premiumbox->update_option('exchange',$key,$val);
	}
	 
}

add_filter('lists_userxch', 'fixbtccode_lists_userxch');
function fixbtccode_lists_userxch($lists){
global $premiumbox;	

	$fixbtccode = intval($premiumbox->get_option('exchange','fixbtccode'));
	if($fixbtccode){
		$lists['lists']['fixbtccode'] = __('Coupon','pn');
	}
	
	return $lists;
}

add_filter('body_list_userxch', 'fixbtccode_body_list_userxch', 10, 6);
function fixbtccode_body_list_userxch($data_item, $item, $key, $title, $date_format, $time_format){		
				
	if($key == 'fixbtccode'){
		$code = '---';
		$btc_code = pn_strip_input($item->btc_code);
		if($btc_code and is_true_userhash($item)){
			$code = $btc_code;
		}
		$data_item = '<span class="fixbtccode" style="word-wrap: break-word; word-break: break-all;">'. $code .'</span>';
	}	
	
	return $data_item;
}