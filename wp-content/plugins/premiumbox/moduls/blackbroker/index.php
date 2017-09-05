<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Авто брокер курсов обмена[:ru_RU][en_US:]Auto Broker of exchange rates[:en_US]
description: [ru_RU:]Авто брокер курсов обмена[:ru_RU][en_US:]Auto Broker of exchange rates[:en_US]
version: 0.1
category: [ru_RU:]Направления обменов[:ru_RU][en_US:]Exchange directions[:en_US]
cat: naps
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* BD */
add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_blackbroker');
function bd_pn_moduls_active_blackbroker(){
global $wpdb;	
	
	$table_name = $wpdb->prefix ."blackbrokers_naps";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT ,
		`naps_id` bigint(20) NOT NULL default '0',
		`site_id` bigint(20) NOT NULL default '0',
		`step_column` int(20) NOT NULL default '0',
		`step` varchar(150) NOT NULL default '0',
		`min_sum` varchar(150) NOT NULL default '0',
		`max_sum` varchar(150) NOT NULL default '0',
		`cours1` varchar(150) NOT NULL default '0',
		`cours2` varchar(150) NOT NULL default '0',
		`item_from` varchar(150) NOT NULL,
		`item_to` varchar(150) NOT NULL,
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);

	$table_name = $wpdb->prefix ."blackbrokers";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT ,
		`title` longtext NOT NULL,
		`url` longtext NOT NULL,
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);	
}
/* end BD */

add_action('admin_menu', 'pn_adminpage_blackbroker');
function pn_adminpage_blackbroker(){
global $premiumbox;		
	if(current_user_can('administrator') or current_user_can('pn_naps')){
		$hook = add_menu_page( __('Auto Broker','pn'), __('Auto Broker','pn'), 'read', "pn_blackbroker", array($premiumbox, 'admin_temp'), $premiumbox->get_icon_link('parser'));	
		add_action( "load-$hook", 'pn_trev_hook' );
		add_submenu_page("pn_blackbroker", __('Add','pn'), __('Add','pn'), 'read', "pn_add_blackbroker", array($premiumbox, 'admin_temp'));
	}
}

function update_naps_blackbroker($broker_data, $res){
global $wpdb;

	$from = is_xml_value($broker_data->item_from);
	$to = is_xml_value($broker_data->item_to);
	
	$now_data = '';
	foreach($res->item as $item){
		if($item->from == $from and $item->to == $to){
			$now_data = $item;
		}
	}
	
	if(is_object($now_data)){
		$naps_id = intval($broker_data->naps_id);
		$site_id = intval($broker_data->site_id);
		$step_column = intval($broker_data->step_column);
		$step = is_my_money($broker_data->step);
		$min_sum = is_my_money($broker_data->min_sum);
		$max_sum = is_my_money($broker_data->max_sum);
		$def_cours1 = is_my_money($broker_data->cours1);
		$def_cours2 = is_my_money($broker_data->cours2);
		if($naps_id > 0){
			if($step_column == 0){
				$sum = is_my_money($now_data->in);
			} else {
				$sum = is_my_money($now_data->out);
			}
			$sum = $sum + $step;
			
			$arr = array();
			if($step_column == 0){
				$arr['curs1'] = $sum;
			} else {
				$arr['curs2'] = $sum;
			}		
			
			if($sum > $max_sum and $max_sum > 0 or $sum < $min_sum){
				$arr['curs1'] = $def_cours1;
				$arr['curs2'] = $def_cours2;
			}		
			
			$c1 = is_isset($arr, 'curs1');
			$c2 = is_isset($arr, 'curs2');
			if(count($arr) > 0){
				do_action('naps_change_course', $naps_id, '', $c1, $c2, 'blackbroker');
				$wpdb->update($wpdb->prefix."naps", $arr, array('id'=>$naps_id)); 
			}
		}
	}
}

global $premiumbox;
$premiumbox->file_include($path.'/filters'); 
$premiumbox->file_include($path.'/list');
$premiumbox->file_include($path.'/add');
$premiumbox->file_include($path.'/cron');