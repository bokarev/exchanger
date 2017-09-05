<?php
if( !defined( 'ABSPATH')){ exit(); }

function del_autobids(){
global $wpdb;
	$second = 60*60;
	$second = apply_filters('del_autobids_second', $second);
	$time = current_time('timestamp') - $second;
	if($second != '-1'){
		$ldate = date('Y-m-d H:i:s', $time);
		$items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."bids WHERE createdate < '$ldate' AND status='auto'");
		foreach($items as $item){
			$id = $item->id;	
			$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."bids WHERE id = '$id'");
			if($result == 1){
				$wpdb->query("DELETE FROM ".$wpdb->prefix."bids_meta WHERE item_id = '$id'");
				do_action('change_bidstatus_all', 'autodelete', $item->id, $item, 'admin','system');
				do_action('change_bidstatus_autodelete', $item->id, $item, 'admin','system'); 			
			}
		}
	}
} 

add_filter('mycron_now', 'mycron_now_del_autobids');
function mycron_now_del_autobids($filters){
	$filters['del_autobids'] = __('Removing orders with inappropriate rules','pn');
	return $filters;
}