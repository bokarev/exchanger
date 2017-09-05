<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('admin_menu', 'pn_adminpage_naps');
function pn_adminpage_naps(){
global $premiumbox;		
	if(current_user_can('administrator') or current_user_can('pn_naps')){
		$hook = add_menu_page(__('Direction of Exchange','pn'), __('Direction of Exchange','pn'), 'read', "pn_naps", array($premiumbox, 'admin_temp'), $premiumbox->get_icon_link('naps'));	
		add_action( "load-$hook", 'pn_trev_hook' );
		add_submenu_page("pn_naps", __('Add exchange direction','pn'), __('Add exchange direction','pn'), 'read', "pn_add_naps", array($premiumbox, 'admin_temp'));
		add_submenu_page("pn_naps", __('Exchange direction templates','pn'), __('Exchange direction templates','pn'), 'read', "pn_naps_temp", array($premiumbox, 'admin_temp'));
		add_submenu_page("pn_naps", __('Sort exchange direction for tariffs','pn'), __('Sort exchange direction for tariffs','pn'), 'read', "pn_sort_naps", array($premiumbox, 'admin_temp'));
		if(get_type_table() == 1 or get_type_table() == 4){
			add_submenu_page("pn_naps", sprintf(__('Sort exchange direction for exchange table %s','pn'),'1'), sprintf(__('Sort exchange direction for exchange table %s','pn'),'1'), 'read', "pn_sort_table1", array($premiumbox, 'admin_temp'));
		}
		if(get_type_table() == 2){
			add_submenu_page("pn_naps", sprintf(__('Sort exchange direction for exchange table %s','pn'),'2'), sprintf(__('Sort exchange direction for exchange table %s','pn'),'2'), 'read', "pn_sort_table2", array($premiumbox, 'admin_temp'));
		}
		if(get_type_table() == 3){
			add_submenu_page("pn_naps", sprintf(__('Sort exchange direction for exchange table %s','pn'),'3'), sprintf(__('Sort exchange direction for exchange table %s','pn'),'3'), 'read', "pn_sort_table3", array($premiumbox, 'admin_temp'));
		}
	}
	
}

add_filter('pn_caps','naps_pn_caps');
function naps_pn_caps($pn_caps){
	$pn_caps['pn_naps'] = __('Use exchange direction','pn');
	return $pn_caps;
}

add_action('premium_action_copy_direction_exchange','def_premium_action_copy_direction_exchange');
function def_premium_action_copy_direction_exchange(){
global $wpdb;	

	pn_only_caps(array('administrator','pn_naps'));
		
	$item_id = intval(is_param_get('item_id'));
	if($item_id){
		$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."naps WHERE id='$item_id' AND autostatus='1'");
		if(isset($data->id)){
			$last_id = $data->id;	
			$array = array();
			foreach($data as $key => $item){
				if($key != 'id'){
					$array[$key] = $item;
				}
				if($key == 'tech_name'){
					$array[$key] = $item . '[copy]';
				}	
				if($key == 'naps_name'){
					$array[$key] = unique_naps_name($item, 0);
				}
			}
			$array['naps_status'] = 0;
			$wpdb->insert($wpdb->prefix.'naps', $array);
			$data_id = $wpdb->insert_id;
			if($data_id){
				$naps_meta = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."naps_meta WHERE item_id='$last_id'");
				foreach($naps_meta as $nap){
					$arr = array();
					$arr['item_id'] = $data_id;
					$arr['meta_key'] = $nap->meta_key;
					$arr['meta_value'] = $nap->meta_value;
					$wpdb->insert($wpdb->prefix.'naps_meta', $arr);
				}							
				$cf_naps = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."naps_order WHERE naps_id='$last_id'");
				foreach($cf_naps as $nap){
					$arr = array();
					$arr['naps_id'] = $data_id;
					$arr['v_id'] = $nap->v_id;
					$arr['order1'] = $nap->order1;
					$wpdb->insert($wpdb->prefix.'naps_order', $arr);
				}				
				copy_naps_txtmeta($last_id, $data_id);
				do_action('pn_naps_copy', $last_id, $data_id);
			}
		}
	}
			
	$url = admin_url('admin.php?page=pn_naps') . '&reply=true';
	wp_redirect($url);
	exit;			
} 

add_action('pn_psys_delete','naps_pn_psys_delete');
function naps_pn_psys_delete($id){
global $wpdb;
	$wpdb->update($wpdb->prefix.'naps', array('psys_id1'=> 0, 'naps_status' => 0), array('psys_id1'=>$id));
	$wpdb->update($wpdb->prefix.'naps', array('psys_id2'=> 0, 'naps_status' => 0), array('psys_id2'=>$id));
}

add_action('pn_valuts_delete', 'naps_pn_valuts_delete');
function naps_pn_valuts_delete($id){
global $wpdb;

	$items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."naps WHERE valut_id1 = '$id' OR valut_id2 = '$id'");
	foreach($items as $item){
		$item_id = $item->id;
		do_action('pn_naps_delete_before', $item_id, $item);		
		$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."naps WHERE id = '$item_id'");
		if($result){						
			do_action('pn_naps_delete', $item_id, $item);
		}
	}
}

add_action('pn_naps_delete', 'def_pn_naps_delete');
function def_pn_naps_delete($id){
global $wpdb;

	$wpdb->query("DELETE FROM ".$wpdb->prefix."naps_order WHERE naps_id = '$id'"); 
	
	$items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."naps_meta WHERE item_id = '$id'");
	foreach($items as $item){
		$item_id = $item->id;
		do_action('pn_napsmeta_delete_before', $id, $item);
		$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."naps_meta WHERE id = '$item_id'");
		if($result){
			do_action('pn_napsmeta_delete', $id, $item);
		}
	}	
	
	delete_naps_txtmeta($id);
}	

add_action('pn_valuts_notactive', 'naps_pn_valuts_notactive');
function naps_pn_valuts_notactive($id){
global $wpdb;
	$wpdb->query("UPDATE ".$wpdb->prefix."naps SET naps_status = '0' WHERE valut_id1 = '$id' OR valut_id2 = '$id'");
}

add_action('pn_valuts_edit','naps_pn_valuts_edit', 1, 2);
function naps_pn_valuts_edit($data_id, $array){
global $wpdb;
	if($data_id > 0){
		if($array['valut_status'] == 0){
			$wpdb->query("UPDATE ".$wpdb->prefix."naps SET naps_status = '0' WHERE valut_id1 = '$data_id' OR valut_id2 = '$data_id'");
		}
		$wpdb->update($wpdb->prefix.'naps', array('psys_id1'=> $array['psys_id']), array('valut_id1'=>$data_id));
		$wpdb->update($wpdb->prefix.'naps', array('psys_id2'=> $array['psys_id']), array('valut_id2'=>$data_id));

		$naps = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."naps");
		foreach($naps as $nap){
			$nap_id = $nap->id;
			$cc = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."naps_order WHERE naps_id='$nap_id' AND v_id='$data_id'");
			if($cc == 0){
				$arr = array(
					'naps_id' => $nap_id,
					'v_id' => $data_id,
				);
				$wpdb->insert($wpdb->prefix.'naps_order', $arr);
			}
		}		
	}
}

add_action('pn_valuts_add','naps_pn_valuts_add', 1, 2);
function naps_pn_valuts_add($data_id, $array){
global $wpdb;
	if($data_id > 0){
		$naps = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."naps");
		foreach($naps as $nap){
			$nap_id = $nap->id;
			$arr = array(
				'naps_id' => $nap_id,
				'v_id' => $data_id,
			);
			$wpdb->insert($wpdb->prefix.'naps_order', $arr);
		}
	}
}
 
add_action('pn_adminpage_quicktags_pn_add_naps','adminpage_quicktags_page_naps');
add_action('pn_adminpage_quicktags_pn_naps_temp','adminpage_quicktags_page_naps');
function adminpage_quicktags_page_naps(){
?>
edButtons[edButtons.length] = 
new edButton('premium_bidid', '<?php _e('ID Order','pn'); ?>','[bid_id]');

edButtons[edButtons.length] = 
new edButton('premium_paysum', '<?php _e('Payment amount','pn'); ?>','[sum_dc]');

edButtons[edButtons.length] = 
new edButton('premium_psys_give', '<?php _e('Payment system Giving','pn'); ?>','[psys_give]');

edButtons[edButtons.length] = 
new edButton('premium_psys_get', '<?php _e('Payment system Receiving','pn'); ?>','[psys_get]');

edButtons[edButtons.length] = 
new edButton('premium_autodel_time', '<?php _e('Time of the order withdrawal','pn'); ?>','[bid_delete_time]');

edButtons[edButtons.length] = 
new edButton('premium_trans_in', '<?php _e('Merchant transaction ID','pn'); ?>','[bid_trans_in]');

edButtons[edButtons.length] = 
new edButton('premium_trans_out', '<?php _e('Auto payout transaction ID','pn'); ?>','[bid_trans_out]');
<?php	
} 
 
add_filter('bid_instruction_tags','quicktags_bid_instruction_tags', 1000, 2);
function quicktags_bid_instruction_tags($instruction, $item){
	$instruction = str_replace('[bid_id]', $item->id ,$instruction);
	$instruction = str_replace('[sum_dc]', $item->summ1_dc ,$instruction);
	$instruction = str_replace('[bid_trans_in]', $item->trans_in ,$instruction);
	$instruction = str_replace('[bid_trans_out]', $item->trans_out ,$instruction);
	if(strstr($instruction,'[psys_give]') and isset($item->psys1i)){
		$instruction = str_replace('[psys_give]', get_pstitle($item->psys1i) ,$instruction);
	}
	if(strstr($instruction,'[psys_get]') and isset($item->psys2i)){
		$instruction = str_replace('[psys_get]', get_pstitle($item->psys2i) ,$instruction);
	}
	if(strstr($instruction,'[bid_delete_time]') and isset($item->status)){
		$status = $item->status;
		if($status == 'auto'){
			$createdate = $item->createdate;
			$editdate = $item->createdate;
			$del_date = '';
			$date_format = get_option('date_format');
			$time_format = get_option('time_format');			
			$createtime = strtotime($createdate);
			$second = 24*60*60; $second = apply_filters('del_autobids_second', $second);
			$del_time = $createtime + $second;
			$del_date = date("{$date_format}, {$time_format}", $del_time);
			$instruction = str_replace('[bid_delete_time]', $del_date, $instruction);
		} 
	}	
	
	return $instruction;
}

global $premiumbox;
$premiumbox->include_patch(__FILE__, 'list');
$premiumbox->include_patch(__FILE__, 'add');
$premiumbox->include_patch(__FILE__, 'temps');
$premiumbox->include_patch(__FILE__, 'cron');
$premiumbox->include_patch(__FILE__, 'sort');
$premiumbox->include_patch(__FILE__, 'sort1');
$premiumbox->include_patch(__FILE__, 'sort2');
$premiumbox->include_patch(__FILE__, 'sort3');