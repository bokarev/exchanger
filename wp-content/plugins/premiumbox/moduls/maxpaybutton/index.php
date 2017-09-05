<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Макс кол-во раз, которое можно нажать кнопку "Перевести"[:ru_RU][en_US:]Max number of times "Transfer" button can be clicked[:en_US]
description: [ru_RU:]Макс кол-во раз, которое можно нажать кнопку "Перевести" в панели управления[:ru_RU][en_US:]Max number of times "Transfer" button can be clicked in the control panel[:en_US]
version: 0.1
category: [ru_RU:]Заявки[:ru_RU][en_US:]Orders[:en_US]
cat: req
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

add_filter('pn_exchange_config_option', 'maxpaybutton_exchange_config_option');
function maxpaybutton_exchange_config_option($options){
global $premiumbox;	
	
	$options['maxpaybutton'] = array(
		'view' => 'input',
		'title' => __('Maximum number of times "Transfer" button can be clicked in control panel','pn'),
		'default' => $premiumbox->get_option('exchange','maxpaybutton'),
		'name' => 'maxpaybutton',
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

add_action('pn_exchange_config_option_post', 'maxpaybutton_exchange_config_option_post');
function maxpaybutton_exchange_config_option_post(){
global $premiumbox;
	
	$options = array('maxpaybutton');
	foreach($options as $key){
		$val = intval(is_param_post($key));
		$premiumbox->update_option('exchange',$key,$val);
	}
	 
}
  
add_filter('autopayment_filter', 'autopayment_filter_maxpaybutton', 0, 6); 
function autopayment_filter_maxpaybutton($au_filter, $m_id, $item, $place, $naps_data, $paymerch_data){
global $wpdb, $premiumbox;

	if(isset($item->id) and count($au_filter['error']) == 0 and $place == 'admin'){ 
		$item_id = $item->id;
		$maxpaybutton = intval($premiumbox->get_option('exchange','maxpaybutton')); if($maxpaybutton < 1){ $maxpaybutton = 1; }
		$count = intval(get_bids_meta($item_id, 'paybutton')) + 1;
		if($count > $maxpaybutton){
			$au_filter['error'][] = sprintf(__('You have clicked the "Transfer" button more than %s times', 'pn'), $maxpaybutton);
		} 
		update_bids_meta($item_id, 'paybutton', $count);
	}
	
	return $au_filter;
}