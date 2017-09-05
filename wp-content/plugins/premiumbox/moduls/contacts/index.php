<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Контакты[:ru_RU][en_US:]Contacts[:en_US]
description: [ru_RU:]Форма контактов[:ru_RU][en_US:]Contacts[:en_US]
version: 1.0
category: [ru_RU:]Настройки[:ru_RU][en_US:]Settings[:en_US]
cat: sett
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* BD */
add_filter('pn_tech_pages', 'list_tech_pages_contacts');
function list_tech_pages_contacts($pages){
 
	$pages[] = array(
		'post_name'      => 'feedback',
		'post_title'     => '[ru_RU:]Контакты[:ru_RU][en_US:]Contacts[:en_US]',
		'post_content'   => '[contact_form]',
		'post_template'   => 'pn-pluginpage.php',
	);	
	
	return $pages;
}
/* end BD */

add_filter('placed_captcha', 'placed_captcha_contact');
function placed_captcha_contact($placed){
	$placed['contactform'] = __('Contact form','pn');
	return $placed;
}

function get_contact_form_filelds($place='shortcode'){
	$ui = wp_get_current_user();

	$items = array();
	$items['name'] = array(
		'name' => 'name',
		'title' => __('Your name', 'pn'),
		'placeholder' => '',
		'req' => 1,
		'value' => pn_strip_input(is_isset($ui,'first_name')),
		'type' => 'input',
		'not_auto' => 0,
		'classes' => 'notclear',
	);
	$items['email'] = array(
		'name' => 'email',
		'title' => __('Your e-mail', 'pn'),
		'placeholder' => '',
		'req' => 1,
		'value' => is_email(is_isset($ui,'user_email')),
		'type' => 'input',
		'not_auto' => 0,
		'classes' => 'notclear',
	);	
	$items['idz'] = array(
		'name' => 'idz',
		'title' => __('Exchange ID', 'pn'),
		'placeholder' => '',
		'req' => 0,
		'value' => '', 
		'type' => 'input',
		'not_auto' => 0,
	);	
	$items['text'] = array(
		'name' => 'text',
		'title' => __('Message', 'pn'),
		'placeholder' => '',
		'req' => 0,
		'value' => '', 
		'type' => 'text',
		'not_auto' => 0,
	);	
	$items = apply_filters('get_form_filelds', $items, 'contactform', $ui, $place);
	$items = apply_filters('contact_form_filelds',$items, $ui, $place);	
	
	return $items;
}

global $premiumbox;
$premiumbox->auto_include($path.'/shortcode');