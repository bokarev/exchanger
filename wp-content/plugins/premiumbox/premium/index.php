<?php
/*
Универсальный фреймворк Premium
*/

/* защита от прямого обращения */
if( !defined( 'ABSPATH')){ exit(); }

if(!class_exists('WP_List_Table')){
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/*
Данные, которые независимо от настроек мультиязычности, всегда будут указывать на оригинал
*/
global $or_template_directory;
if(!$or_template_directory){
	$or_template_directory = get_template_directory_uri();
}

global $or_site_url;
if(!$or_site_url){
	$or_site_url = rtrim(get_option('siteurl'), '/');
}

/* версия фреймворка */
if(!function_exists('get_premium_version')){
	function get_premium_version(){
		return '1.4';
	}		
}

/* подключаем функции */
require_once( dirname(__FILE__) . "/includes/functions.php");

/* создаем указатели страниц */
require_once( dirname(__FILE__) . "/includes/set_admin_pointer.php");

/* письма */
require_once( dirname(__FILE__) . "/includes/mail_filters.php");

/* работаем с заголовками */
require_once( dirname(__FILE__) . "/includes/title_filters.php");

/* подключаем административные функции */
require_once( dirname(__FILE__) . "/includes/admin_func.php");

/* подключаем языковые функции */
require_once( dirname(__FILE__) . "/includes/lang_func.php");

/* фильтры для меню */
require_once( dirname(__FILE__) . "/includes/menu_filters.php");

/* подключаем общий класс */
require_once( dirname(__FILE__) . "/includes/premium_class.php");

/* pagenavi */
require_once( dirname(__FILE__) . "/includes/pagenavi.php");

/* инициализация страниц */
require_once( dirname(__FILE__) . "/includes/init_page.php");

/* локализация фреймворка */
if(!function_exists('premium_langs_loaded')){
	add_action('plugins_loaded', 'premium_langs_loaded');
	function premium_langs_loaded(){
		load_plugin_textdomain( 'premium', false, dirname( plugin_basename( __FILE__ ) ) . '/langs' ); 
	}			
}