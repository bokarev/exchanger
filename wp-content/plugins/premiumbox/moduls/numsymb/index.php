<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Макс. кол-во знаков после запятой в БД[:ru_RU][en_US:]Max number of decimal places allowed in DB[:en_US]
description: [ru_RU:]Макс. кол-во знаков после запятой в БД[:ru_RU][en_US:]Max number of decimal places in calculations allowed in database[:en_US]
version: 1.0
category: [ru_RU:]Настройки[:ru_RU][en_US:]Settings[:en_US]
cat: sett
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

add_filter('pn_config_option', 'numsybm_config_option');
function numsybm_config_option($options){
global $premiumbox;

	if(isset($options['bottom_title'])){
		unset($options['bottom_title']);
	}
	$options['numsybm_count'] = array(
		'view' => 'input',
		'title' => __('Max number of decimal places in calculations allowed in DB','pn'),
		'default' => $premiumbox->get_option('numsybm_count'),
		'name' => 'numsybm_count',
		'work' => 'input',
	);	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);	
	
	return $options;	
}

add_action('pn_config_option_post', 'numsybm_config_option_post');
function numsybm_config_option_post(){
global $premiumbox;
	
	$numsybm_count = intval(is_param_post('numsybm_count'));
	$premiumbox->update_option('numsybm_count', '', $numsybm_count);
	
}

add_filter('is_my_money_cz', 'numsybm_is_my_money_cz', 10, 4);
function numsybm_is_my_money_cz($cz){
global $premiumbox;
	
	$numsybm_count = intval($premiumbox->get_option('numsybm_count'));
	if($numsybm_count > 0){
		if($cz > $numsybm_count){
			$cz = $numsybm_count;	
		}
	}	
	
	return $cz;
}			