<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Лимит резерва для валют[:ru_RU][en_US:]Currency reserve limits[:en_US]
description: [ru_RU:]Лимит резерва для валют[:ru_RU][en_US:]Currency reserve limits[:en_US]
version: 1.0
category: [ru_RU:]Направления обменов[:ru_RU][en_US:]Exchange directions[:en_US]
cat: naps
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_currlimit');
function bd_pn_moduls_active_currlimit(){
global $wpdb;	
	
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."valuts LIKE 'inday1'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."valuts ADD `inday1` varchar(50) NOT NULL default '0'");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."valuts LIKE 'inday2'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."valuts ADD `inday2` varchar(50) NOT NULL default '0'");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."valuts LIKE 'inmon1'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."valuts ADD `inmon1` varchar(50) NOT NULL default '0'");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."valuts LIKE 'inmon2'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."valuts ADD `inmon2` varchar(50) NOT NULL default '0'");
    }
	
}

add_filter('pn_valuts_addform', 'currlimit_pn_valuts_addform', 10, 2);
function currlimit_pn_valuts_addform($options, $data){
	
	if(isset($options['bottom_title'])){
		unset($options['bottom_title']);
	}	
	$options[] = array(
		'view' => 'line',
		'colspan' => 2,
	);	
	$options[] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	$options['inday1'] = array(
		'view' => 'input',
		'title' => __('Daily limit for Send','pn'),
		'default' => is_isset($data, 'inday1'),
		'name' => 'inday1',
	);
	$options['inday1_help'] = array(
		'view' => 'help',
		'title' => __('More info','pn'),
		'default' => __('Daily limit for currency purchase of currency. Unable to buy more currency more than previously set.','pn'),
	);	
	$options['inday2'] = array(
		'view' => 'input',
		'title' => __('Daily limit for Receive','pn'),
		'default' => is_isset($data, 'inday2'),
		'name' => 'inday2',
	);
	$options['inday2_help'] = array(
		'view' => 'help',
		'title' => __('More info','pn'),
		'default' => __('Daily limit for currency sale. Unable to sell currency more than previously set.','pn'),
	);	
	$options[] = array(
		'view' => 'line',
		'colspan' => 2,
	);	
	$options['inmon1'] = array(
		'view' => 'input',
		'title' => __('Monthly limit for Send','pn'),
		'default' => is_isset($data, 'inmon1'),
		'name' => 'inmon1',
	);
	$options['inmon1_help'] = array(
		'view' => 'help',
		'title' => __('More info','pn'),
		'default' => __('Monthly limit for currency purchase. Unable to buy currency more than previously set.','pn'),
	);		
	$options['inmon2'] = array(
		'view' => 'input',
		'title' => __('Monthly limit for Receive','pn'),
		'default' => is_isset($data, 'inmon2'),
		'name' => 'inmon2',
	);
	$options['inmon2_help'] = array(
		'view' => 'help',
		'title' => __('More info','pn'),
		'default' => __('Monthly limit for currency sale. Unable to sell currency more than previously set.','pn'),
	);		
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);	
	
	return $options;
}

add_filter('pn_valuts_addform_post', 'currlimit_valuts_addform_post');
function currlimit_valuts_addform_post($array){
	
	$array['inday1'] = is_my_money(is_param_post('inday1'));
	$array['inday2'] = is_my_money(is_param_post('inday2'));
	$array['inmon1'] = is_my_money(is_param_post('inmon1'));
	$array['inmon2'] = is_my_money(is_param_post('inmon2'));	
	
	return $array;
}

add_filter('valuts_manage_ap_columns', 'currlimit_valuts_manage_ap_columns');
function currlimit_valuts_manage_ap_columns($columns){
	$new = array();
	foreach($columns as $k => $v){
		$new[$k] = $v;
		if($k == 'decimal'){
			$new['inday1'] = __('Daily limit for Send','pn');
			$new['inday2'] = __('Daily limit for Receive','pn');
		}
	}
	return $new;
}

add_filter('valuts_manage_ap_col', 'currlimit_valuts_manage_ap_col', 10, 3);
function currlimit_valuts_manage_ap_col($html, $column_name, $item){
	
	if($column_name == 'inday1'){		
		return '<input type="text" style="width: 80px;" name="inday1['. $item->id .']" value="'. is_my_money($item->inday1) .'" />';
	} elseif($column_name == 'inday2'){		
		return '<input type="text" style="width: 80px;" name="inday2['. $item->id .']" value="'. is_my_money($item->inday2) .'" />';		
	}
	
	return $html;
}

add_action('pn_valuts_save', 'currlimit_pn_valuts_save');
function currlimit_pn_valuts_save(){
global $wpdb;

	if(isset($_POST['inday1']) and is_array($_POST['inday1'])){ 	
		foreach($_POST['inday1'] as $id => $inday1){
			$id = intval($id);
			$inday1 = is_my_money($inday1);
			if($inday1 <= 0){ $inday1 = 0; }			
				$wpdb->query("UPDATE ".$wpdb->prefix."valuts SET inday1 = '$inday1' WHERE id = '$id'");
		}		
	}

	if(isset($_POST['inday2']) and is_array($_POST['inday2'])){		
		foreach($_POST['inday2'] as $id => $inday2){
			$id = intval($id);
			$inday2 = is_my_money($inday2);
			if($inday2 <= 0){ $inday2 = 0; }				
				$wpdb->query("UPDATE ".$wpdb->prefix."valuts SET inday2 = '$inday2' WHERE id = '$id'");
		}	
	}	
}


add_filter('get_max_sum_to_naps_give', 'currlimit_get_max_sum_to_naps_give', 10, 3);
function currlimit_get_max_sum_to_naps_give($max, $naps, $vd){
	
	$time = current_time('timestamp');
	$inday = is_my_money($vd->inday1);
	if($inday > 0){
		$date = date('Y-m-d 00:00:00',$time);
		$sum_day_valut = get_sum_valut($vd->id, 'in', $date);
		$inday = $inday - $sum_day_valut;
		if(is_numeric($max)){
			if($max > $inday){
				$max = $inday;
			}	
		} else {
			$max = $inday;
		}
	}	
	
	$inmon = is_my_money($vd->inmon1);
	if($inmon > 0){
		$date = date('Y-m-01 00:00:00',$time);
		$sum_mon_valut = get_sum_valut($vd->id, 'in', $date);
		$inmon = $inmon - $sum_mon_valut;
		if(is_numeric($max)){
			if($max > $inmon){
				$max = $inmon;
			}	
		} else {
			$max = $inmon;
		}
	}	
	
	return $max;
}

add_filter('get_max_sum_to_naps_get', 'currlimit_get_max_sum_to_naps_get', 10, 3);
function currlimit_get_max_sum_to_naps_get($max, $naps, $vd){
	
	$time = current_time('timestamp');
	
	$inday = is_my_money($vd->inday2);
	if($inday > 0){
		$date = date('Y-m-d 00:00:00',$time);
		$sum_day_valut = get_sum_valut($vd->id, 'out', $date);
		$inday = $inday - $sum_day_valut;
		
		if(is_numeric($max)){
			if($max > $inday){
				$max = $inday;
			}	
		} else {
			$max = $inday;
		}
	}		
	
	$inmon = is_my_money($vd->inmon2);
	if($inmon > 0){
		$date = date('Y-m-01 00:00:00',$time);
		$sum_mon_valut = get_sum_valut($vd->id, 'out', $date);
		$inmon = $inmon - $sum_mon_valut;
		
		if(is_numeric($max)){
			if($max > $inmon){
				$max = $inmon;
			}	
		} else {
			$max = $inmon;
		}
	}	
	
	return $max;
}										