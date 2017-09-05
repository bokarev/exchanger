<?php
include_once('../../../../wp-load.php');
header("Content-type: text/txt; charset=utf-8");

if(!defined('PN_PLUGIN_NAME')){ exit; }

if(has_filter('myaction_request_exporttxt')){
	do_action('myaction_request_exporttxt');
}