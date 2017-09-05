<?php
include_once('../../../../wp-load.php');
header('Content-Type: text/html; charset=utf-8');

if(!defined('PN_PLUGIN_NAME')){ exit; }

if(has_filter('myaction_request_affiliate')){
	do_action('myaction_request_affiliate');
}