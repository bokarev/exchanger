<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('tab_naps_tab8', 'geoip_tab_naps_tab8', 30, 2);
function geoip_tab_naps_tab8($data, $data_id){
global $wpdb;	
	?>
	<tr>
		<th><?php _e('Prohibited countries','pn'); ?></th>
		<td colspan="2">
			<div class="premium_wrap_standart">
				
				<div class="cf_div">
					<div style="font-weight: 500;"><label><input type="checkbox" class="check_all" name="" value="1" /> <?php _e('Check all/Uncheck all','pn'); ?></label></div>
					<?php
					$string = pn_strip_input(is_isset($data,'not_country'));
					$def = array();
					if(preg_match_all('/\[d](.*?)\[\/d]/s',$string, $match, PREG_PATTERN_ORDER)){
						$def = $match[1];
					}
					?>
						<div><label><input type="checkbox" name="not_country[]" <?php if(in_array('NaN',$def)){ ?>checked="checked"<?php } ?> value="NaN" /> <?php _e('is not determined','pn'); ?></label></div>	
					<?php
					$country = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."geoip_country ORDER BY title ASC");
					foreach($country as $cou_item){
					?>	
						<div><label><input type="checkbox" name="not_country[]" <?php if(in_array($cou_item->attr,$def)){ ?>checked="checked"<?php } ?> value="<?php echo $cou_item->attr; ?>" /> <?php echo pn_strip_input(ctv_ml($cou_item->title)); ?></label></div>	
					<?php
						}	
					?>
				</div>				
			</div>
		</td>
	</tr>
	<tr>
		<th><?php _e('Allowed countries','pn'); ?></th>
		<td colspan="2">
			<div class="premium_wrap_standart">
				
				<div class="cf_div">
					<div style="font-weight: 500;"><label><input type="checkbox" class="check_all" name="" value="1" /> <?php _e('Check all/Uncheck all','pn'); ?></label></div>
					<?php
					$string = pn_strip_input(is_isset($data,'only_country'));
					$def = array();
					if(preg_match_all('/\[d](.*?)\[\/d]/s',$string, $match, PREG_PATTERN_ORDER)){
						$def = $match[1];
					}
					?>
						<div><label><input type="checkbox" name="only_country[]" <?php if(in_array('NaN',$def)){ ?>checked="checked"<?php } ?> value="NaN" /> <?php _e('is not determined','pn'); ?></label></div>
					<?php	
					$country = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."geoip_country ORDER BY title ASC");
					foreach($country as $cou_item){
					?>	
						<div><label><input type="checkbox" name="only_country[]" <?php if(in_array($cou_item->attr,$def)){ ?>checked="checked"<?php } ?> value="<?php echo $cou_item->attr; ?>" /> <?php echo pn_strip_input(ctv_ml($cou_item->title)); ?></label></div>	
					<?php
						}	
					?>
				</div>				
			</div>
		</td>
	</tr>	
	<?php 		
}

add_filter('pn_naps_addform_post', 'napsgeoip_pn_naps_addform_post');
function napsgeoip_pn_naps_addform_post($array){

	$not_country = is_param_post('not_country');
	$item = '';
	if(is_array($not_country)){
		foreach($not_country as $v){
			$v = is_country_attr($v);
			if($v){
				$item .= '[d]'. $v .'[/d]';
			}
		}
	}
	
	$array['not_country'] = $item;
	
	$only_country = is_param_post('only_country');
	$item = '';
	if(is_array($only_country)){
		foreach($only_country as $v){
			$v = is_country_attr($v);
			if($v){
				$item .= '[d]'. $v .'[/d]';
			}
		}
	}
	
	$array['only_country'] = $item;	
	
	return $array;
}

add_action('pn_exchange_filters', 'napsgeoip_pn_exchange_filters');
function napsgeoip_pn_exchange_filters($lists){
	
	$lists[] = array(
		'title' => __('Filter by country of user','pn'),
		'name' => 'napsgeoip',
	);
	
	return $lists;
} 

add_filter('get_naps_where', 'napsgeoip_get_naps_where',1, 2);
function napsgeoip_get_naps_where($where, $place){
global $user_now_country, $premiumbox;
	
	$ind = $premiumbox->get_option('exf_'. $place .'_napsgeoip');
	$user_country = pn_strip_input($user_now_country);
	if($ind == 1){
		$where .= "AND not_country NOT LIKE '%[d]{$user_country}[/d]%' ";
	}
	
	return $where;
}

add_filter('error_bids', 'error_bids_napsgeoip', 99 ,6);
function error_bids_napsgeoip($error_bids, $account1, $account2, $naps, $vd1, $vd2){
global $user_now_country;

	$user_country = is_country_attr($user_now_country);
	$string = pn_strip_input(is_isset($naps,'not_country'));
	$not_country = array();
	if(preg_match_all('/\[d](.*?)\[\/d]/s',$string, $match, PREG_PATTERN_ORDER)){
		$not_country = $match[1];
	}	
	if(in_array($user_country,$not_country)){
		$error_bids['error'] = 1;
		$error_bids['error_text'][] = __('Error! For your country exchange is denied','pn');			
	}	
	
	$string = pn_strip_input(is_isset($naps,'only_country'));
	$yes_country = array();
	if(preg_match_all('/\[d](.*?)\[\/d]/s',$string, $match, PREG_PATTERN_ORDER)){
		$yes_country = $match[1];
	}	
	if(count($yes_country) > 0 and !in_array($user_country,$yes_country)){
		$error_bids['error'] = 1;
		$error_bids['error_text'][] = __('Error! For your country exchange is denied','pn');			
	}	
		
	return $error_bids;
}

add_filter('array_data_create_bids', 'napsgeoip_data_create_bids');
function napsgeoip_data_create_bids($array){
global $user_now_country;	
	
	$array['user_country'] = is_country_attr($user_now_country);
	
	return $array;
}

add_filter('change_bids_filter_list', 'napsgeoip_change_bids_filter_list'); 
function napsgeoip_change_bids_filter_list($lists){
global $wpdb;
	
	$options = array(
		'0' => '--'. __('All','pn').'--',
		'NaN' => __('is not determined','pn'),
	);
	$countries = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."geoip_country ORDER BY title ASC");
	foreach($countries as $item){
		$options[is_country_attr($item->attr)] = pn_strip_input(ctv_ml($item->title));
	}
		
	$lists['other']['country'] = array(
		'title' => __('User country','pn'),
		'name' => 'country',
		'options' => $options,
		'view' => 'select',
		'work' => 'options',
	);	
	
	return $lists;
}

add_filter('where_request_sql_bids', 'napsgeoip_where_request_sql_bids', 10,2);
function napsgeoip_where_request_sql_bids($where, $pars_data){
global $wpdb;

	$pr = $wpdb->prefix;
	$country = is_country_attr(is_isset($pars_data,'country'));
	if($country){
		$where .= " AND {$pr}bids.user_country = '$country'";
	}	
	
	return $where;
}

add_filter('onebid_icons','onebid_icons_napsgeoip',99,3);
function onebid_icons_napsgeoip($onebid_icon, $item, $data_fs){
global $wpdb;
	 
	if(isset($item->user_country)){
		$onebid_icon['napsgeoip'] = array(
			'type' => 'text',
			'title' => __('User country','pn') .': [country]',
			'label' => '[country_attr]',
		);	
	}
	
	return $onebid_icon; 
}

add_filter('get_bids_replace_text','get_bids_replace_text_napsgeoip',99,3);
function get_bids_replace_text_napsgeoip($text, $item, $data_fs){
global $wpdb;
	
	if(strstr($text, '[country]')){
		$title = __('is not determined','pn');
		$user_cou = $item->user_country;
		if($user_cou and $user_cou != 'NaN'){
			$title = get_country_title($user_cou);
		}		
		$country = '<span class="item_country">' . $title . '</span>';
		$text = str_replace('[country]', $country ,$text);
	}

	if(strstr($text, '[country_attr]')){
		$user_cou = $item->user_country;	
		if($user_cou == 'NaN' or !$user_cou){
			$user_cou = __('N/A','pn');
		}
		$country_attr = '<span class="item_country_attr">' . $user_cou . '</span>';
		$text = str_replace('[country_attr]', $country_attr ,$text);
	}	
	
	return $text;
} 