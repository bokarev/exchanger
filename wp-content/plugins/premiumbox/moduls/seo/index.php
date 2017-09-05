<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]SEO[:ru_RU][en_US:]SEO[:en_US]
description: [ru_RU:]SEO[:ru_RU][en_US:]SEO[:en_US]
version: 1.0
category: [ru_RU:]Настройки[:ru_RU][en_US:]Settings[:en_US]
cat: sett
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

add_filter('pn_caps','seo_pn_caps');
function seo_pn_caps($pn_caps){
	$pn_caps['pn_seo'] = __('Work with SEO','pn');
	return $pn_caps;
}

/* 
Подключаем к меню
*/
add_action('admin_menu', 'pn_adminpage_seo');
function pn_adminpage_seo(){
global $premiumbox;
	if(current_user_can('administrator') or current_user_can('pn_seo')){
		add_menu_page(__('SEO','pn'), __('SEO','pn'), 'read', 'pn_seo', array($premiumbox, 'admin_temp'), $premiumbox->get_icon_link('seo'));  
		add_submenu_page("pn_seo", __('Settings','pn'), __('Settings','pn'), 'read', "pn_seo", array($premiumbox, 'admin_temp'));
		add_submenu_page("pn_seo", __('XML sitemap settings','pn'), __('XML sitemap settings','pn'), 'read', "pn_xmlmap", array($premiumbox, 'admin_temp'));
		add_submenu_page("pn_seo", __('Robots.txt settings','pn'), __('Robots.txt settings','pn'), 'read', "pn_robotstxt", array($premiumbox, 'admin_temp'));
	}
}

add_theme_support('post-thumbnails');

add_filter('robots_txt','pn_robotstxt',99,2);
function pn_robotstxt($output, $public){
global $wpdb, $premiumbox;
    if($public == 1){
		
	    $txt = pn_strip_text($premiumbox->get_option('robotstxt','txt'));
		if($txt){
			$txt = $txt ."\n\r";
		} else {
			$txt = "User-agent: *\nDisallow: /wp-admin/\nDisallow: /wp-includes/\n\r";
		}
 
		$txt.= 'Sitemap: '. get_site_url_or() .'/sitemap.xml'."\n"; 

		return $txt;
	}
	
	return $output;
}

add_filter('pn_exchange_cat_filters','pn_exchange_cat_filters_sitemap');
function pn_exchange_cat_filters_sitemap($cats){
	$cats['smxml'] = __('Sitemap XML','pn');
	return $cats;
}

global $premiumbox;
$premiumbox->file_include($path.'/meta');
$premiumbox->file_include($path.'/seo');
$premiumbox->file_include($path.'/xmlmap');
$premiumbox->file_include($path.'/robotstxt');
$premiumbox->file_include($path.'/api');