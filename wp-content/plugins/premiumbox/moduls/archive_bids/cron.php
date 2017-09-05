<?php
if( !defined( 'ABSPATH')){ exit(); }

function pn_archives_bids(){
global $wpdb, $premiumbox;

	if($premiumbox->get_option('up_mode') != 1){
		$second = 2*30*24*60*60;
		$date = current_time('mysql');
		$time = current_time('timestamp') - $second;
		$ldate = date('Y-m-d H:i:s', $time);
		$my_dir = wp_upload_dir();
		$dir = $my_dir['basedir'].'/bids/';	
		$items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."bids WHERE createdate < '$ldate' LIMIT 5");
		foreach($items as $item){
			$id = $item->id;	
			$status = $item->status;
			$user_id = $item->user_id;
			$vtype1i = $item->vtype1i;
			$vtype2i = $item->vtype2i;
			$vtype1 = $item->vtype1;
			$vtype2 = $item->vtype2;
			$valut1i = $item->valut1i;
			$valut2i = $item->valut2i;
			$pcalc = intval(is_isset($item, 'pcalc'));
			$domacc = intval(is_isset($item, 'domacc'));
			$domacc1 = intval(is_isset($item, 'domacc1'));
			$domacc2 = intval(is_isset($item, 'domacc2'));
			$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."bids WHERE id = '$id'");
			if($result == 1){
				
				if($status == 'success'){
					if($user_id > 0){
						set_archive_data($user_id, 'user_exsum', '', '', $item->exsum);	
						set_archive_data($user_id, 'user_bids_success', '', '', 1);
					}
					if($pcalc == 1){
						set_archive_data($item->ref_id, 'pbids', '', '', 1);
						set_archive_data($item->ref_id, 'pbids_sum', '', '', $item->summp);
						set_archive_data($item->ref_id, 'pbids_exsum', '', '', $item->exsum);
					}
				}
				
				set_archive_data($vtype1i, 'vtype_give', $status, '', $item->summ1cr);
				set_archive_data($vtype2i, 'vtype_get', $status, '', $item->summ2cr);
				set_archive_data($valut1i, 'valut_give', $status, '', $item->summ1cr);
				set_archive_data($valut2i, 'valut_get', $status, '', $item->summ2cr);
				set_archive_data($item->naps_id, 'naps_give', $status, '', $item->summ1cr);
				set_archive_data($item->naps_id, 'naps_get', $status, '', $item->summ2cr);
				
				if($user_id > 0){
					if($domacc == 1){
						set_archive_data($user_id, 'domacc1_vtype', $status, $vtype1i, $item->summ1c);
					}
					if($domacc == 2){
						set_archive_data($user_id, 'domacc2_vtype', $status, $vtype2i, $item->summ2c);
					}
					if($domacc1 == 1){
						set_archive_data($user_id, 'domacc1_vtype', $status, $vtype1i, $item->summ1c);
					}
					if($domacc2 == 1){
						set_archive_data($user_id, 'domacc2_vtype', $status, $vtype2i, $item->summ2c);
					}				
				}
				
				$archive_content = array();
				foreach($item as $k => $v){
					$archive_content[$k] = $v;
				}
				
				$arr = array();
				$arr['archive_date'] = $date;
				$arr['bid_id'] = $id;
				$arr['user_id'] = $user_id;
				$arr['ref_id'] = $item->ref_id;
				$arr['account1'] = $item->account1;
				$arr['account2'] = $item->account2;
				$arr['first_name'] = $item->first_name;
				$arr['last_name'] = $item->last_name;
				$arr['second_name'] = $item->second_name;
				$arr['user_phone'] = $item->user_phone;
				$arr['user_skype'] = $item->user_skype;
				$arr['user_email'] = $item->user_email;
				$arr['user_passport'] = $item->user_passport;
				$arr['archive_content'] = serialize($archive_content);
				$arr['status'] = $item->status;
				$wpdb->insert($wpdb->prefix."archive_bids", $arr);
				
				do_action('archive_bids', $item->id, $item);
				
				$wpdb->query("DELETE FROM ".$wpdb->prefix."bids_meta WHERE item_id = '$id'");
				
				do_action('change_bidstatus_all', 'archived', $item->id, $item, 'admin', 'system');
				do_action('change_bidstatus_archived', $item->id, $item, 'admin', 'system');
				
				$file = $dir . $id .'.txt';
				if(is_file($file)){
					@unlink($file);
				}			
				
			}
		}
	}
} 

add_filter('mycron_10min', 'mycron_10min_archives_bids');
function mycron_10min_archives_bids($filters){
	$filters['pn_archives_bids'] = __('Archiving orders older than 2 months','pn');
	return $filters;
}