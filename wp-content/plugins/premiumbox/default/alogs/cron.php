<?php
if( !defined( 'ABSPATH')){ exit(); }

function del_autologs(){
global $wpdb;
	$count_day = apply_filters('delete_autologs_day', 120);
	if($count_day > 0){
		$time = current_time('timestamp') - ($count_day * DAY_IN_SECONDS); 
		$ldate = date('Y-m-d H:i:s', $time);
		$items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."login_check WHERE datelogin < '$ldate'");
		foreach($items as $item){
			$item_id = $item->id;
			do_action('pn_alogs_delete_before', $item_id, $item);
			$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."login_check WHERE id = '$item_id'");
			if($result){
				do_action('pn_alogs_delete', $id, $item);
			}
		}
	}
} 

add_filter('mycron_1day', 'mycron_1day_del_autologs');
function mycron_1day_del_autologs($filters){
	
	$filters['del_autologs'] = __('Deleting authorization logs','pn');
	
	return $filters;
}