<?php
/*
Plugin Name: Invest
Plugin URI: http://best-curs.info
Description: Плагин инвестирования
Version: 3.2
Author: Best-Curs.info
Author URI: http://best-curs.info
*/

/* защита от прямого обращения */
if( !defined( 'ABSPATH')){ exit(); }

if (strpos($_SERVER['REQUEST_URI'], "eval(") ||
      strpos($_SERVER['REQUEST_URI'], "CONCAT") ||
      strpos($_SERVER['REQUEST_URI'], "UNION+SELECT") ||
      strpos($_SERVER['REQUEST_URI'], "base64")) {
    header("HTTP/1.1 414 Request-URI Too Long");
	header("Status: 414 Request-URI Too Long");
	header("Connection: Close");
	exit;
}

/* 
Основные константы плагина
*/
if(!defined('INEX_PLUGIN_NAME')){
	define('INEX_PLUGIN_NAME', plugin_basename(__FILE__));
}
if(!defined('INEX_PLUGIN_DIR')){
	define('INEX_PLUGIN_DIR', dirname(__FILE__).'/');
}
if(!defined('INEX_PLUGIN_URL')){
	define('INEX_PLUGIN_URL', plugin_dir_url( __FILE__ ));
}

/* подключаем фреймворк */
require_once( dirname(__FILE__) . "/premium/index.php");
if(!class_exists('Premium')){
	return;
}

require( dirname(__FILE__) . "/includes/plugin_class.php");
if(!class_exists('InvestBox')){
	return;
}

/* 
Если вы проводите тестирование, поставьте 1, в противном случае, оставьте 0
Также желательно в файле wp-config.php поставить WP_DEBUG true.
*/
$debug_mode = 1;

global $investbox;
$investbox = new InvestBox($debug_mode);

/* 
отключаем редактирование файлов из админки,
если оно еще не было отключено в WP-config 
*/
pn_disallow_file_mode();

$investbox->file_include('includes/cron');
$investbox->file_include('includes/actions');
$investbox->file_include('includes/merchant_class');

$investbox->auto_include('merchants','index');
$investbox->auto_include('adminpage');
$investbox->auto_include('shortcode');

add_action('widgets_init', 'investbox_register_widgets');
function investbox_register_widgets(){
global $investbox;
	$investbox->auto_include('widget');	 
}