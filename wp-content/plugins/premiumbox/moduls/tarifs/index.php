<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Тарифы[:ru_RU][en_US:]Rates[:en_US]
description: [ru_RU:]Тарифы[:ru_RU][en_US:]Rates[:en_US]
version: 1.0
category: [ru_RU:]Направления обменов[:ru_RU][en_US:]Exchange directions[:en_US]
cat: naps
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* BD */
add_filter('pn_tech_pages', 'list_tech_pages_tarifs');
function list_tech_pages_tarifs($pages){
 
	$pages[] = array(
		'post_name'      => 'tarifs',
		'post_title'     => '[ru_RU:]Тарифы[:ru_RU][en_US:]Tariffs[:en_US]',
		'post_content'   => '[tarifs]',
		'post_template'   => 'pn-pluginpage.php',
	);			
	
	return $pages;
}
/* end BD */

add_filter('pn_exchange_cat_filters','pn_exchange_cat_filters_tarifs');
function pn_exchange_cat_filters_tarifs($cats){
	
	$cats['tar'] = __('Tariffs','pn');
	
	return $cats;
}

global $premiumbox;
$premiumbox->auto_include($path.'/shortcode');