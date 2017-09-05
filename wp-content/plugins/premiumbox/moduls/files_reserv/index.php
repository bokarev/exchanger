<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Парсер резерва из файла[:ru_RU][en_US:]Parser of reserve from file[:en_US]
description: [ru_RU:]Парсер резерва из файла[:ru_RU][en_US:]Parser of reserve from file[:en_US]
version: 1.0
category: [ru_RU:]Валюты[:ru_RU][en_US:]Currency[:en_US]
cat: currency
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

add_filter('reserv_place_list', 'fres_reserv_place_list', 10, 2);
function fres_reserv_place_list($list, $place){
	
	$reservs = get_reserv_fres($place, 1);
	$r=0;
	foreach($reservs as $key => $val){ $r++;
		$list[$key] = sprintf(__('File reserve, line %s','pn'), $r);
	}
	
	return $list;
}

add_filter('update_naps_reserv', 'fres_update_naps_reserv', 10, 3);
function fres_update_naps_reserv($ind, $key, $naps_id){
	$name = 'fres';
	if($ind == 0){
		if(strstr($key, $name.'_')){	
			$reserv_in_file = get_reserv_fres('direction');		
			$rezerv = '-1';
				if(isset($reserv_in_file[$key])){
					$rezerv = $reserv_in_file[$key];
				}				
			if($rezerv != '-1' and function_exists('pm_update_nr')){
				pm_update_nr($naps_id, $rezerv);
			}											
			return 1;			
		}
	}	
		return $ind;	
}

add_action('update_valut_autoreserv', 'fres_update_valut_autoreserv', 10, 3);
function fres_update_valut_autoreserv($ind, $key, $valut_id){
	$name = 'fres';
	if($ind == 0){
		if(strstr($key, $name.'_')){	
			$reserv_in_file = get_reserv_fres('currency');		
			$rezerv = '-1';
				if(isset($reserv_in_file[$key])){
					$rezerv = $reserv_in_file[$key];
				}				
			if($rezerv != '-1' and function_exists('pm_update_vr')){
				pm_update_vr($valut_id, $rezerv);
			}											
						
			return 1;			
		}
	}
			
		return $ind;	
}

function get_reserv_fres($place='currency'){
global $premiumbox;

	$arr = array();
	if($place == 'currency'){
		$url = trim($premiumbox->get_option('fres','url'));
	} else {
		$url = trim($premiumbox->get_option('fres','url2'));
	}
	$name = 'fres';
	if($url){
		$curl = get_curl_parser($url, '', 'moduls', 'fres');
		$string = $curl['output'];
		if(!$curl['err']){
			$lines = explode("\n",$string);
			$r=0;
			foreach($lines as $line){ $r++;
				$pars_line = explode('=',$line);
				if(isset($pars_line[1])){
					$sum = is_my_money($pars_line[1]);
					$arr[$name.'_'.$r] = $sum;
				}					
			}
		}
	}
	return $arr;
}

add_action('myaction_request_fres','fres_request_cron');
function fres_request_cron(){
global $wpdb, $premiumbox;	

	if(check_hash_cron()){

		$reserv_in_file = get_reserv_fres('currency');
		$name = 'fres';
		$valuts = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."valuts WHERE reserv_place LIKE '{$name}_%'");
		foreach($valuts as $valut){
			$key = $valut->reserv_place;
			$valut_id = $valut->id;
			$rezerv = '-1';
			if(isset($reserv_in_file[$key])){
				$rezerv = $reserv_in_file[$key];
			}						
			if($rezerv != '-1' and function_exists('pm_update_vr')){
				pm_update_vr($valut_id, $rezerv);
			}	
		}
		
		$reserv_in_file = get_reserv_fres('direction');
		$name = 'fres';
		$naps = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."naps WHERE reserv_place LIKE '{$name}_%'");
		foreach($naps as $nap){
			$key = $nap->reserv_place;
			$nap_id = $nap->id;
			$rezerv = '-1';
			if(isset($reserv_in_file[$key])){
				$rezerv = $reserv_in_file[$key];
			}						
			if($rezerv != '-1' and function_exists('pm_update_nr')){
				pm_update_nr($valut_id, $rezerv);
			}	
		}		
	
	}
	
	_e('Done','pn');
}

add_action('admin_menu', 'pn_adminpage_fres');
function pn_adminpage_fres(){
global $premiumbox;		
	add_submenu_page("pn_moduls", __('File reserve','pn'), __('File reserve','pn'), 'administrator', "pn_fres", array($premiumbox, 'admin_temp'));
}

add_action('pn_adminpage_title_pn_fres', 'pn_admin_title_pn_fres');
function pn_admin_title_pn_fres($page){
	_e('File reserve','pn');
} 

add_action('pn_adminpage_content_pn_fres','def_pn_admin_content_pn_fres');
function def_pn_admin_content_pn_fres(){
global $wpdb, $premiumbox;

	$site_url = get_site_url_or();
	$text = '
	<a href="'. $site_url .'/request-fres.html'. get_hash_cron('?') .'" target="_blank">CRON-file</a>
	';
	pn_admin_substrate($text);
	
	$options = array();
	$options['top_title'] = array(
		'view' => 'h3',
		'title' => __('File reserve settings','pn'),
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);	
	$options['url'] = array(
		'view' => 'inputbig',
		'title' => __('URL of file with reserves for Currency section', 'pn'),
		'default' => $premiumbox->get_option('fres','url'),
		'name' => 'url',
	);
	$options['url2'] = array(
		'view' => 'inputbig',
		'title' => __('URL of file with reserves for Exchange directions section', 'pn'),
		'default' => $premiumbox->get_option('fres','url2'),
		'name' => 'url2',
	);		
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('', $options);	
}  

add_action('premium_action_pn_fres','def_premium_action_pn_fres');
function def_premium_action_pn_fres(){
global $wpdb, $premiumbox;	

	only_post();
	pn_only_caps(array('administrator'));

	$options = array('url', 'url2');	
	foreach($options as $key){
		$premiumbox->update_option('fres', $key, pn_strip_input(is_param_post($key)));
	}				

	$url = admin_url('admin.php?page=pn_fres&reply=true');
	wp_redirect($url);
	exit;
} 