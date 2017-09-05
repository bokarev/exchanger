<?php
if( !defined( 'ABSPATH')){ exit(); }

function delete_autonaps(){
global $wpdb;

	$time = current_time('timestamp') - (1 * DAY_IN_SECONDS); 
	$ldate = date('Y-m-d H:i:s', $time);
	$items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."naps WHERE createdate < '$ldate' AND autostatus='0'");
	foreach($items as $item){
		$item_id = $item->id;
		do_action('pn_naps_delete_before', $item_id, $item);
		$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."naps WHERE id = '$item_id'");
		if($result){
			do_action('pn_naps_delete', $item_id, $item);
		}
	}	
	
} 

add_filter('mycron_1day', 'mycron_1day_delete_autonaps');
function mycron_1day_delete_autonaps($filters){
	$filters['delete_autonaps'] = __('Deleting technical exchange directions','pn');
	return $filters;
}