<?php
include_once('../../../../wp-load.php');
header("Content-type: text/xml; charset=utf-8");
global $wpdb;

if(!defined('PN_PLUGIN_NAME')){ exit; }

if(has_filter('myaction_request_exportxml')){
	do_action('myaction_request_exportxml');
}