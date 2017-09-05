<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Обновление статуса заявки в онлайн режиме [:ru_RU][en_US:]Online updating of order status[:en_US]
description: [ru_RU:]Обновление статуса заявки в онлайн режиме в панели управления[:ru_RU][en_US:]Online updating of order status[:en_US]
version: 1.0
category: [ru_RU:]Заявки[:ru_RU][en_US:]Orders[:en_US]
cat: req
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

add_filter('globalajax_admin_data_request', 'bids_globalajax_admin_data_request', 10, 3);
function bids_globalajax_admin_data_request($params, $link, $page){
	if($page == 'pn_bids'){
		$params['bids_ids'] = "'+ $('#visible_ids').val() +'";
	}
	return $params;
}

add_filter('globalajax_admin_data', 'bids_globalajax_admin_data', 10, 3);
function bids_globalajax_admin_data($log, $link, $page){
global $wpdb;

	if($page == 'pn_bids'){
		if(current_user_can('administrator') or current_user_can('pn_bids')){
			$bids_ids = is_param_post('bids_ids');
			$bids_ids_parts = explode(',',$bids_ids);
			$ins = array();
			foreach($bids_ids_parts as $v){
				$v = intval($v);
				if($v){
					$ins[] = "'". $v ."'";
				}
			}
			if(is_array($ins) and count($ins) > 0){
				$ins_join = join(',', $ins);
				$bids = array();
				$items = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."bids WHERE id IN($ins_join)");
				foreach($items as $item){
					$bids[$item->id] = array(
						'status' => $item->status,
						'title' => get_bid_status($item->status),
					);
				}
				$log['status_bids'] = $bids;
			}
		}
	}
	return $log;
}

add_action('globalajax_admin_data_jsresult', 'bids_globalajax_admin_data_jsresult', 10, 2);
function bids_globalajax_admin_data_jsresult($link, $page){
	if($page == 'pn_bids'){
?>
if(res['status_bids']){
	for (key in res['status_bids']) {
		if(!$('#bidid_'+ key).hasClass('st_'+res['status_bids'][key].status)){
			$('#bidid_'+ key).find('.stname').removeClass().addClass('stname').addClass('st_'+res['status_bids'][key].status);
			$('#bidid_'+ key).find('.stname').html(res['status_bids'][key].title);
		}
	}	
}
<?php
	}
}