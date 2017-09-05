<?php
if( !defined( 'ABSPATH')){ exit(); }

function del_apbd(){
global $wpdb;
	$second = 3*30*24*60*60;
	$second = apply_filters('del_apbd_second', $second);
	$time = current_time('timestamp') - $second;
	if($second != '-1'){
		$ldate = date('Y-m-d H:i:s', $time);
		$wpdb->query("DELETE FROM ".$wpdb->prefix."db_admin_logs WHERE trans_date < '$ldate'");
	}
} 

add_filter('mycron_now', 'mycron_now_apbd');
function mycron_now_apbd($filters){
	$filters['del_apbd'] = __('Deleting logs of administrator actions','pn');
	return $filters;
}