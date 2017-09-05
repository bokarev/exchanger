<?php
if( !defined( 'ABSPATH')){ exit(); }

/* live */ 
add_action('premium_action_bids_live_change', 'pn_premium_action_bids_live_change');
function pn_premium_action_bids_live_change(){
only_post();
	
	$log = array();
	$log['status'] = '';
	$log['response'] = '';
	$log['status_code'] = 0; 
	$log['status_text'] = __('Error','pn');
	
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);
	if(current_user_can('administrator') or current_user_can('pn_bids')){
	
		$ulc = array();
		$ulc['autoupdate'] = intval(is_param_post('autoupdate'));
		$ulc['timeupdate'] = intval(is_param_post('timeupdate'));
		$ulc['rington'] = intval(is_param_post('rington'));
		$ulc['hidetransit'] = intval(is_param_post('hidetransit'));
		
		$you_status = array();
		$status = (array)is_param_post('status');
		if(count($status) > 0){
			foreach($status as $val){
				$you_status[] = is_status_name($val);
			}
		} 
		
		$ulc['status'] = join(',',$you_status);
		update_user_meta( $user_id, 'user_live_change', $ulc) or add_user_meta($user_id, 'user_live_change', $ulc, true);
	
	} else {
		$log['status'] = 'error';
		$log['status_code'] = 1;
		$log['status_text'] = __('Authorisation Error','pn');
	}
	
	echo json_encode($log);
	exit;
}

add_action('premium_action_bids_live_html', 'pn_premium_action_bids_live_html');
function pn_premium_action_bids_live_html(){
global $wpdb;

	only_post();
	
	$log = array();
	$log['status'] = '';
	$log['response'] = '';
	$log['status_code'] = 0; 
	$log['status_text'] = __('Error','pn');
	
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);
	if(current_user_can('administrator') or current_user_can('pn_bids')){
	
		$enable = array();
		$bid_status_list = apply_filters('bid_status_list',array());
		if(is_array($bid_status_list)){	
			foreach($bid_status_list as $key => $status){
				$enable[] = trim($key);
			}
		}
		$bid_status = explode(',',is_param_post('bid_status'));
		$en_join = array();
		foreach($bid_status as $bs){
			$bs = trim($bs);
			if(in_array($bs,$enable)){
				$en_join[] = "'". $bs ."'";
			}
		}
		
		$hide_id = array();
		$old_join = array();
		$old_id = explode(',',is_param_post('old_id'));
		foreach($old_id as $id){
			$id = intval($id);
			if($id){
				$old_join[] = "'". $id ."'";
				$hide_id[] = $id;
			}
		}

		$last_id = intval(is_param_post('last_id'));
		$bids = array();
		if(count($en_join) > 0){
			
			$join = join(',',$en_join);
			$items = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."bids WHERE status IN($join) AND id > $last_id ORDER BY id ASC");
			foreach($items as $item){
				
				$bid_id = $item->id;
				$last_id = $bid_id;
				$status = is_status_name($item->status);
				$link = admin_url('admin.php?page=pn_bids&bidid='. $item->id);
				$status_name = get_bid_status($item->status);
				
				$bids[] = array(
					'id' => $bid_id,
					'status' => $status,
					'status_name' => $status_name,
					'link' => $link,
					'sum_give' => is_my_money($item->summ1_dc),
					'sum_get' => is_my_money($item->summ2c),
					'cur_give' => pn_strip_input(ctv_ml($item->valut1)) .' '. is_site_value($item->vtype1),
					'cur_get' => pn_strip_input(ctv_ml($item->valut2)) .' '. is_site_value($item->vtype2),
				);
				
			}
			
			if(count($old_join) > 0){
				$hide_id = array();
				$id_join = join(',',$old_join);
				$del_items = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."bids WHERE status NOT IN($join) AND id IN($id_join) ORDER BY id ASC");
				foreach($del_items as $item){
					$hide_id[] = $item->id;
				}	
			}
		
		} 
		
		$log['hide_id'] = $hide_id;
		$log['last_id'] = $last_id;	
		$log['bids'] = $bids;
		$log['status'] = 'success';
	
	} else {
		$log['status'] = 'error';
		$log['status_code'] = 1; 
		$log['status_text'] = __('Authorisation Error','pn');		
	}
	
    echo json_encode($log);	
	exit;
}
/* end live */