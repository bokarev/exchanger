<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Преобразование номера счета[:ru_RU][en_US:]Account number conversion[:en_US]
description: [ru_RU:]Разделение номера счета проблеми[:ru_RU][en_US:]Separation of account number by problems[:en_US]
version: 0.1
category: [ru_RU:]Направления обменов[:ru_RU][en_US:]Exchange directions[:en_US]
cat: naps
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

add_filter('pn_exchange_config_option', 'beautyacc_exchange_config_option');
function beautyacc_exchange_config_option($options){
global $premiumbox;	
	
	$options['bacc_admin'] = array(
		'view' => 'select',
		'title' => __('Separate account number with spaces in control panel','pn'),
		'options' => array('0'=>__('No','pn'),'1'=>__('Yes','pn')),
		'default' => $premiumbox->get_option('exchange','bacc_admin'),
		'name' => 'bacc_admin',
	);
/* 	$options['bacc_site'] = array(
		'view' => 'select',
		'title' => __('Beautiful output of accounts in the site','pn'),
		'options' => array('0'=>__('No','pn'),'1'=>__('Yes','pn')),
		'default' => $premiumbox->get_option('exchange','bacc_site'),
		'name' => 'bacc_site',
	); */	
	$options[] = array(
		'view' => 'line',
		'colspan' => 2,
	);	
	if(isset($options['bottom_title'])){
		unset($options['bottom_title']);
	}	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);	
	return $options;
}

add_action('pn_exchange_config_option_post', 'beautyacc_exchange_config_option_post');
function beautyacc_exchange_config_option_post(){
global $premiumbox;
	
	$options = array('bacc_admin'); //,'bacc_site'
	foreach($options as $key){
		$val = intval(is_param_post($key));
		$premiumbox->update_option('exchange',$key,$val);
	}
	
}

/* add_filter('show_user_account', 'beautyacc_show_user_account', 10, 4); 
function beautyacc_show_user_account($account, $item, $naps, $vd){
global $premiumbox;	
	$c = intval($premiumbox->get_option('exchange','bacc_site'));
	if($c == 1){
		$vid = intval(is_isset($vd, 'vidzn'));
		if($vid == 1){
			$account = str_replace('+','',$account);
			$len = mb_strlen($account);
			$nacc = '';
			$r=$s=0;
			while($r++<$len){ $s++;
				$nacc .= mb_substr($account, ($r-1), 1);
				if($s%4==0){ $nacc .= ' '; }
			}
			return $nacc;		
		} elseif($vid == 2){
			$account = str_replace('+','',$account);
			$len = mb_strlen($account);
			$nacc = '';
			$r=$s=0;
			while($r++<$len){
				if($r==1){
					$nacc .= '+' . mb_substr($account, 0, 1).' ';
				} else { $s++;
					$nacc .= mb_substr($account, ($r-1), 1);
				}
				if($s%4==0){ $nacc .= ' '; }
			}
			return $nacc;
		}
	}
	return $account;
} */	

add_filter('onebid_account_give', 'beautyacc_onebid_account_give', 0, 4);
function beautyacc_onebid_account_give($account, $account_or, $item, $v){	
	$valut1i = intval(is_isset($item, 'valut1i'));
	$vd = is_isset($v, $valut1i);
	return beautyacc_onebid_account($account, $vd);
} 
add_filter('onebid_account_get', 'beautyacc_onebid_account_get', 0, 4);
function beautyacc_onebid_account_get($account, $account_or, $item, $v){	
	$valut2i = intval(is_isset($item, 'valut2i'));
	$vd = is_isset($v, $valut2i);
	return beautyacc_onebid_account($account, $vd);
}	

function beautyacc_onebid_account($account, $vd){
global $premiumbox;	
	$c = intval($premiumbox->get_option('exchange','bacc_admin'));
	if($c == 1){
		$vid = intval(is_isset($vd, 'vidzn'));
		if($vid == 1){
			$account = str_replace('+','',$account);
			$len = mb_strlen($account);
			$nacc = '';
			$r=$s=0;
			while($r++<$len){ $s++;
				$nacc .= mb_substr($account, ($r-1), 1);
				if($s%4==0){ $nacc .= ' '; }
			}
			return $nacc;		
		} elseif($vid == 2){
			$account = str_replace('+','',$account);
			$len = mb_strlen($account);
			$nacc = '';
			$r=$s=0;
			while($r++<$len){
				if($r==1){
					$nacc .= '+' . mb_substr($account, 0, 1).' ';
				} else { $s++;
					$nacc .= mb_substr($account, ($r-1), 1);
				}
				if($s%4==0){ $nacc .= ' '; }
			}
			return $nacc;
		}
	}
	return $account;
}	