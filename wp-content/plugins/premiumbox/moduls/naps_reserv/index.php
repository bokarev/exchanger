<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Настройки резерва для направлений обмена[:ru_RU][en_US:]Reserve settings for exchange directions[:en_US]
description: [ru_RU:]Настройки резерва для направлений обмена[:ru_RU][en_US:]Reserve settings for exchange directions[:en_US]
version: 1.0
category: [ru_RU:]Направления обменов[:ru_RU][en_US:]Exchange directions[:en_US]
cat: naps
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* BD */
add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_napsreserv');
function bd_pn_moduls_active_napsreserv(){
global $wpdb;	
	
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'naps_reserv'");
    if($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `naps_reserv` varchar(250) NOT NULL default '0'");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'reserv_place'");
    if($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `reserv_place` varchar(250) NOT NULL default '0'");
    }	
	
}
/* end BD */

add_filter('list_tabs_naps','list_tabs_naps_napsreserv');
function list_tabs_naps_napsreserv($list_tabs_naps){
	$list_tabs_naps['tab300'] = __('Reserve','pn');
	
	return $list_tabs_naps;
}

add_action('tab_naps_tab300','tab_naps_tab_napsreserv',99,2);
function tab_naps_tab_napsreserv($data, $data_id){
	
	$rplaced = array();
	$rplaced[0] = '--'. __('Default','pn') .'--';
	$rplaced[1] = '--'. __('From field below','pn') .'--';
	$rplaced = apply_filters('reserv_place_list', $rplaced, 'direction');
	$rplaced = (array)$rplaced;	
?>	
	<tr>
		<th><?php _e('Reserve','pn'); ?></th>
		<td colspan="2">
			<div class="premium_wrap_standart">
				<select name="reserv_place" autocomplete="off">
					<?php 
					foreach($rplaced as $key => $title){
					?>						
					<option value="<?php echo $key; ?>" <?php selected($key,$data->reserv_place); ?>><?php echo $title;?></option>			
					<?php } ?>
				</select>
			</div>
		</td>
	</tr>
	<tr>
		<th><?php _e('Field for reserve','pn'); ?></th>
		<td>
			<div class="premium_wrap_standart">
				<input type="text" name="naps_reserv" style="width: 100px;" value="<?php echo is_my_money($data->naps_reserv); ?>" />
			</div>
		</td>
		<td>			
		</td>
	</tr>	
<?php
} 
 
add_filter('pn_naps_addform_post', 'napsreserv_pn_naps_addform_post');
function napsreserv_pn_naps_addform_post($array){
	
	$array['reserv_place'] = is_extension_name(is_param_post('reserv_place'));
	$array['naps_reserv'] = is_my_money(is_param_post('naps_reserv'));
	
	return $array;
}

function update_naps_reserv($naps_id){
global $wpdb;

	$naps_id = intval($naps_id); 
	if($naps_id){ 
		$item = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."naps WHERE id='$naps_id'");
		if(isset($item->id)){
			apply_filters('update_naps_reserv', 0,  is_extension_name($item->reserv_place), $naps_id, $item);
		}
	}
}

add_action('change_bidstatus_all','napsreserv_change_bidstatus',1000,3);
function napsreserv_change_bidstatus($action, $obmen_id, $obmen){
	update_naps_reserv($obmen->naps_id);
}

add_action('pn_naps_edit','napsreserv_pn_naps_edit', 1000, 2); 
add_action('pn_naps_add','napsreserv_pn_naps_edit', 1000, 2);
function napsreserv_pn_naps_edit($data_id, $array){
	update_naps_reserv($data_id);
}

add_filter('get_naps_reserv', 'napsreserv_get_naps_reserv', 1000, 4);
function napsreserv_get_naps_reserv($reserv, $valut_reserv, $decimal, $naps){
	if($naps->reserv_place != '0'){
		return $naps->naps_reserv;
	}
	return $reserv;
}