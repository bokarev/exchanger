<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Шаблоны e-mail уведомлений[:ru_RU][en_US:]E-mail notifications templates[:en_US]
description: [ru_RU:]E-mail отправителя и имя отправителя используемые для шаблонов писем по умолчанию[:ru_RU][en_US:]Sender E-mail and sender name used for letters template by default[:en_US]
version: 1.0
category: [ru_RU:]E-mail[:ru_RU][en_US:]E-mail[:en_US]
cat: email
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* 
Подключаем к меню
*/
add_action('admin_menu', 'pn_adminpage_mailtemps');
function pn_adminpage_mailtemps(){
global $premiumbox;	
	add_submenu_page("pn_moduls", __('E-mail settings','pn'), __('E-mail settings','pn'), 'administrator', "pn_mailtemps", array($premiumbox, 'admin_temp'));
}

add_action('pn_adminpage_title_pn_mailtemps', 'pn_adminpage_title_pn_mailtemps');
function pn_adminpage_title_pn_mailtemps($page){
	_e('E-mail settings','pn');
} 

/* настройки */
add_action('pn_adminpage_content_pn_mailtemps','pn_adminpage_content_pn_mailtemps');
function pn_adminpage_content_pn_mailtemps(){
global $wpdb;

	$data = get_option('pn_mailtemp_modul');

	$options = array();
	$options['top_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);	
	$options['mail'] = array(
		'view' => 'inputbig',
		'title' => __('Senders e-mail','pn'),
		'default' => is_isset($data, 'mail'),
		'name' => 'mail',
		'work' => 'input',
	);
	$options['mail_warning'] = array(
		'view' => 'warning',
		'default' => __('Use only existing e-mail like info@site.ru','pn'),
	);	
	$options['name'] = array(
		'view' => 'inputbig',
		'title' => __('Sender name','pn'),
		'default' => is_isset($data, 'name'),
		'name' => 'name',
		'work' => 'input',
	);
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);		
	pn_admin_one_screen('', $options, $data); 
}  

add_action('premium_action_pn_mailtemps','def_premium_action_pn_mailtemps');
function def_premium_action_pn_mailtemps(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_mailtemp'));
	
	$options = array();
	$options['mail'] = array(
		'name' => 'mail',
		'work' => 'input',
	);
	$options['name'] = array(
		'name' => 'name',
		'work' => 'input',
	);	
	$data = pn_strip_options('', $options);
	
	$new_data = array();
	$new_data['mail'] = $data['mail'];
	$new_data['name'] = $data['name'];
	update_option('pn_mailtemp_modul',$new_data);

	$back_url = is_param_post('_wp_http_referer');
	$back_url .= '&reply=true';
			
	wp_safe_redirect($back_url);
	exit;
} 

add_filter('pn_mailtemp_option', 'mailtemps_pn_mailtemp_option');
function mailtemps_pn_mailtemp_option($options){
	
	if(isset($options['mail'])){
		unset($options['mail']);
	}
	if(isset($options['name'])){
		unset($options['name']);
	}
	if(isset($options['mail_warning'])){
		unset($options['mail_warning']);
	}	
	
	return $options;
}

add_filter('wp_mail', 'mailtemps_wp_mail');
function mailtemps_wp_mail($data){
	
	$d = get_option('pn_mailtemp_modul');
	$mail = pn_strip_input(is_isset($d, 'mail'));
	$name = pn_strip_input(is_isset($d, 'name'));
	if($mail and $name){
		$data['headers'] = "From: $name <". $mail .">\r\n";
	}
	
	return $data;
}