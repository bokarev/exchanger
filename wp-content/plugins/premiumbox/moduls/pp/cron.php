<?php
if( !defined( 'ABSPATH')){ exit(); }

function archive_plinks(){
global $wpdb;

	$count_day = apply_filters('archive_plinks_day', 60);
	if($count_day > 0){
		$time = current_time('timestamp') - ($count_day * DAY_IN_SECONDS); 
		$ldate = date('Y-m-d H:i:s', $time);
		
		$items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."plinks WHERE pdate < '$ldate'");
		foreach($items as $item){
			$item_id = $item->id;	
			$user_id = $item->user_id;
			
			set_archive_data($user_id, 'plinks', '', '', 1);
			
			$wpdb->query("DELETE FROM ".$wpdb->prefix."plinks WHERE id = '$item_id'");
		}		

	}
} 

add_filter('mycron_1day', 'mycron_1day_archive_plinks');
function mycron_1day_archive_plinks($filters){
	
	$filters['archive_plinks'] = __('Archiving partnership transitions','pn');
	
	return $filters;
}