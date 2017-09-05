<?php
if( !defined( 'ABSPATH')){ exit(); }

add_filter('get_pagenavi', 'mobile_get_pagenavi');
function mobile_get_pagenavi($array){
	if(is_mobile()){
		if(isset($array['first'])){
			unset($array['first']);
		}
		if(isset($array['last'])){
			unset($array['last']);
		}		
		$array['num'] = 1;
		$array['numleft'] = 0;
		$array['numright'] = 0;
	}			
	return $array;
}

add_action('tab_naps_tab8', 'mobile_tab_naps_tab8', 1, 2);
function mobile_tab_naps_tab8($data, $data_id){
	?>
		<th><?php _e('Website version','pn'); ?></th>
		<td colspan="2">
			<div class="premium_wrap_standart">
				<?php 
				$mobile = intval(is_isset($data, 'mobile')); 
				?>														
				<select name="mobile" autocomplete="off"> 
					<option value="0" <?php selected($mobile,0); ?>><?php _e('for all','pn'); ?></option>
					<option value="1" <?php selected($mobile,1); ?>><?php _e('original version only','pn'); ?></option>
					<option value="2" <?php selected($mobile,2); ?>><?php _e('mobile version only','pn'); ?></option>
				</select>
			</div>
		</td>	
	<?php 		
}


add_filter('pn_naps_addform_post', 'mobile_pn_naps_addform_post');
function mobile_pn_naps_addform_post($array){
	$array['mobile'] = intval(is_param_post('mobile'));
	return $array;
}

add_action('pn_exchange_filters', 'mobile_pn_exchange_filters');
function mobile_pn_exchange_filters($lists){
	$lists[] = array(
		'title' => __('Filter by website version','pn'),
		'name' => 'mobile',
	);
	return $lists;
}

add_filter('get_naps_where', 'mobile_get_naps_where', 10, 2);
function mobile_get_naps_where($where, $place){
global $premiumbox;	
	$ind = $premiumbox->get_option('exf_'. $place .'_mobile');
	if($ind == 1){
		if(is_mobile()){
			$where .= "AND mobile IN ('0','2') ";
		} else {
			$where .= "AND mobile IN ('0','1') ";
		}
	} 
	return $where;
}

add_filter('error_bids', 'error_bids_mobile', 99 ,6);
function error_bids_mobile($error_bids, $account1, $account2, $naps, $vd1, $vd2){
	$mobile = intval(is_isset($naps,'mobile'));	
	if($mobile == 1 and is_mobile() or $mobile == 2 and !is_mobile()){
		$error_bids['error'] = 1;
		$error_bids['error_text'][] = __('Error! Direction of exchange for your device is denied','pn');			
	}	
	return $error_bids;
}

add_filter('array_data_bids_new', 'mobile_array_data_bids_new', 10, 2);
function mobile_array_data_bids_new($array, $obmen){
global $wpdb;
	$device = 0;
	if(is_mobile()){
		$device = 1;
	}
	$array['device'] = $device;	
	return $array;
}

add_filter('change_bids_filter_list', 'mobile_change_bids_filter_list'); 
function mobile_change_bids_filter_list($lists){
global $wpdb;
	
	/*********/ 		
	$options = array(
		'0' => '--'. __('for all','pn').'--',
		'1' => __('Original version','pn'),
		'2' => __('Mobile version','pn'),
	);
			
	$lists['other']['device'] = array(
		'title' => __('Website version','pn'),
		'name' => 'device',
		'options' => $options,
		'view' => 'select',
		'work' => 'options',
	); 
	/*********/
	
	return $lists;
}

add_filter('where_request_sql_bids', 'where_request_sql_bids_mobile',0,2);
function where_request_sql_bids_mobile($where, $pars_data){
global $wpdb;	
	
	$device = intval(is_isset($pars_data,'device'));
	if($device == 1){
		$where .= " AND {$wpdb->prefix}bids.device = '0'";
	} elseif($device == 2){
		$where .= " AND {$wpdb->prefix}bids.device = '1'";
	}
	
	return $where;
}

add_filter('onebid_icons','onebid_icons_mobile',10,2);
function onebid_icons_mobile($onebid_icon, $item){
global $premiumbox;
	
	if(isset($item->device)){
		if($item->device == 0){
			$onebid_icon['device'] = array(
				'type' => 'label',
				'title' => __('Original version','pn'),
				'image' => $premiumbox->plugin_url . 'images/desctop.png',
			);	
		} else {
			$onebid_icon['device'] = array(
				'type' => 'label',
				'title' => __('Mobile','pn'),
				'image' => $premiumbox->plugin_url . 'images/mobile.png',
			);			
		}
	}
	
	return $onebid_icon;
}