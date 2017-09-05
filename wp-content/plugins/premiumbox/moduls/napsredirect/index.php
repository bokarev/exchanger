<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Редирект на направления обмена[:ru_RU][en_US:]Redirection to exchange directions[:en_US]
description: [ru_RU:]Редирект на направления обмена[:ru_RU][en_US:]Redirection to exchange directions[:en_US]
version: 1.0
category: [ru_RU:]Направления обменов[:ru_RU][en_US:]Exchange directions[:en_US]
cat: naps
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

add_filter('pn_exchange_config_option', 'napsredirect_exchange_config_option');
function napsredirect_exchange_config_option($options){
global $wpdb, $premiumbox;
	
	$options['redirect'] = array(
		'view' => 'select',
		'title' => __('Redirect to exchange page','pn'),
		'options' => array('0'=>__('No','pn'),'1'=>__('Yes','pn')),
		'default' => $premiumbox->get_option('exchange','redirect'),
		'name' => 'redirect',
	);	
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

add_action('pn_exchange_config_option_post', 'napsredirect_exchange_config_option_post');
function napsredirect_exchange_config_option_post(){
global $wpdb, $premiumbox;
	
	$redirect = intval(is_param_post('redirect'));
	$premiumbox->update_option('exchange', 'redirect', $redirect);
}

add_action('template_redirect', 'napsredirect_redirect', 0);
function napsredirect_redirect(){
global $wpdb, $premiumbox;
			
	if(isset($_GET['cur_from']) and isset($_GET['cur_to'])){
		if($premiumbox->get_option('exchange','redirect') == 1){ 
			$cur_from = is_xml_value(is_param_get('cur_from'));
			$cur_to = is_xml_value(is_param_get('cur_to'));
			if($cur_from and $cur_to and $cur_to != $cur_from){
				$vd1 = $wpdb->get_row("SELECT id FROM ". $wpdb->prefix ."valuts WHERE xml_value='$cur_from'");
				$vd2 = $wpdb->get_row("SELECT id FROM ". $wpdb->prefix ."valuts WHERE xml_value='$cur_to'");
				if(isset($vd1->id) and isset($vd2->id)){
					$val1 = $vd1->id;
					$val2 = $vd2->id;
					$naps = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."naps WHERE valut_id1='$val1' AND valut_id2='$val2' AND naps_status='1' AND autostatus='1'");
					if(isset($naps->id)){
						wp_redirect(get_exchange_link($naps->naps_name));
						exit;
					}
				}
			}
		}
	}	
}