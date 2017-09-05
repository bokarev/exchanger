<?php
include_once('../../../../wp-load.php');
header('Content-Type: text/html; charset=utf-8');

if(!defined('PN_PLUGIN_NAME')){ exit; }

//* * * */02 *  wget --spider http://site.ru/cron.html > /dev/null

if(has_filter('myaction_request_curscron')){
	do_action('myaction_request_curscron');
}