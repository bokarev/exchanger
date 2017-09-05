<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Текст уведомления технического обслуживания[:ru_RU][en_US:]Maintenance notification[:en_US]
description: [ru_RU:]Текст уведомления технического обслуживания[:ru_RU][en_US:]Maintenance notification[:en_US]
version: 1.0
category: [ru_RU:]Настройки[:ru_RU][en_US:]Settings[:en_US]
cat: sett
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* 
Подключаем к меню
*/
add_action('admin_menu', 'admin_menu_wuptext');
function admin_menu_wuptext(){
global $premiumbox;	
	
	add_submenu_page("pn_moduls", __('Maintenance message','pn'), __('Maintenance message','pn'), 'administrator', "pn_wuptext", array($premiumbox, 'admin_temp'));
}

add_action('pn_adminpage_title_pn_wuptext', 'def_adminpage_title_pn_wuptext');
function def_adminpage_title_pn_wuptext($page){
	_e('Maintenance message','pn');
} 

/* настройки */
add_action('pn_adminpage_content_pn_wuptext','def_adminpage_content_pn_wuptext');
function def_adminpage_content_pn_wuptext(){
global $wpdb;

	$options = array();
	$options['top_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	$options['text'] = array(
		'view' => 'textarea',
		'title' => __('Text','pntheme'),
		'default' => get_option('pn_update_plugin_text'),
		'name' => 'text',
		'width' => '',
		'height' => '180px',
		'work' => 'text',
	);			
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);			
	pn_admin_one_screen('', $options);
			
}  

add_action('premium_action_pn_wuptext','def_premium_action_pn_wuptext');
function def_premium_action_pn_wuptext(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator'));
	
	$options = array();
	$options['text'] = array(
		'name' => 'text',
		'work' => 'input',
	);									
	
	$data = pn_strip_options('', $options);
	update_option('pn_update_plugin_text', $data['text']);			

	$back_url = is_param_post('_wp_http_referer');
	$back_url .= '&reply=true';
			
	wp_safe_redirect($back_url);
	exit;
} 