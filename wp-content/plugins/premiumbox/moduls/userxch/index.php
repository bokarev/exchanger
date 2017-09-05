<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Обмены пользователя[:ru_RU][en_US:]User exchanges[:en_US]
description: [ru_RU:]Обмены пользователя[:ru_RU][en_US:]User exchanges[:en_US]
version: 1.0
category: [ru_RU:]Пользователи[:ru_RU][en_US:]Users[:en_US]
cat: user
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* BD */
add_filter('pn_tech_pages', 'list_tech_pages_userxch');
function list_tech_pages_userxch($pages){
 
	$pages[] = array(
		'post_name'      => 'userxch',
		'post_title'     => '[ru_RU:]Ваши операции[:ru_RU][en_US:]Your transactions[:en_US]',
		'post_content'   => '[userxch]',
		'post_template'   => 'pn-pluginpage.php',
	);		
	
	return $pages;
}
/* end BD */

add_filter('account_list_pages','account_list_pages_userxch', 0);
function account_list_pages_userxch($account_list_pages){
	
	$account_list_pages['userxch'] = array(
		'type' => 'page',		
	);
	
	return $account_list_pages;
}

global $premiumbox;
$premiumbox->auto_include($path.'/shortcode');