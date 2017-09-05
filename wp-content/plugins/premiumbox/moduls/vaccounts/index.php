<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Счета валют[:ru_RU][en_US:]Currency accounts[:en_US]
description: [ru_RU:]Счета валют[:ru_RU][en_US:]Currency accounts[:en_US]
version: 1.0
category: [ru_RU:]Направления обменов[:ru_RU][en_US:]Exchange directions[:en_US]
cat: naps
*/

/* BD */
$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_vaccounts');
function bd_pn_moduls_active_vaccounts(){
global $wpdb;	
	
/*
Счета валют

valut_id - id валюты, он же шорткод
accountnum - номер счета
count_visit - кол-во просмотров
max_visit - максимальное кол-во просмотров
status - 0-не выводится, 1-выводится
text_comment - просто комментарий
inday - дневной лимит на прием
inmonth - месячный лимит на прием
*/	
	$table_name= $wpdb->prefix ."valuts_account";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT ,
		`valut_id` bigint(20) NOT NULL default '0',
		`accountnum` longtext NOT NULL,
		`count_visit` int(5) NOT NULL default '0',
		`max_visit` int(5) NOT NULL default '0',
		`text_comment` longtext NOT NULL,
		`inday` varchar(50) NOT NULL default '0',
		`inmonth` varchar(50) NOT NULL default '0',
		`status` int(1) NOT NULL default '0',
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);
	
}
/* end BD */

add_action('admin_menu', 'pn_adminpage_vaccounts');
function pn_adminpage_vaccounts(){
global $premiumbox;		
	if(current_user_can('administrator') or current_user_can('pn_vaccounts')){
		
		$hook = add_menu_page(__('Currency accounts','pn'), __('Currency accounts','pn'), 'read', 'pn_vaccounts', array($premiumbox, 'admin_temp'), $premiumbox->get_icon_link('accounts'));  
		add_action( "load-$hook", 'pn_trev_hook' );
		add_submenu_page("pn_vaccounts", __('Add','pn'), __('Add','pn'), 'read', "pn_add_vaccounts", array($premiumbox, 'admin_temp'));	
		add_submenu_page("pn_vaccounts", __('Add list','pn'), __('Add list','pn'), 'read', "pn_add_vaccounts_many", array($premiumbox, 'admin_temp'));
	
	}
	
}

add_filter('pn_caps','vaccounts_pn_caps');
function vaccounts_pn_caps($pn_caps){
	$pn_caps['pn_vaccounts'] = __('Work with currency accounts','pn');
	return $pn_caps;
}

add_action('pn_valuts_delete','vaccounts_pn_valuts_delete');
function vaccounts_pn_valuts_delete($id){
global $wpdb;

	$items = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."valuts_account WHERE valut_id = '$id'");
	foreach($items as $item){
		$item_id = $item->id;
		do_action('pn_vaccounts_delete_before', $item_id, $item);
		$result = $wpdb->query("DELETE FROM ". $wpdb->prefix ."valuts_account WHERE id = '$item_id'");
		if($result){
			delete_vaccs_txtmeta($item_id);
			do_action('pn_vaccounts_delete', $item_id, $item);
		}
	}
}

function delete_vaccs_txtmeta($data_id){
	delete_txtmeta('vaccsmeta', $data_id);
}

function get_vaccs_txtmeta($data_id, $key){
	return get_txtmeta('vaccsmeta', $data_id, $key);
}

function update_vaccs_txtmeta($data_id, $key, $value){
	return update_txtmeta('vaccsmeta', $data_id, $key, $value);
}

global $premiumbox;	
$premiumbox->file_include($path.'/add');
$premiumbox->file_include($path.'/add_many');
$premiumbox->file_include($path.'/list');

$premiumbox->auto_include($path.'/shortcode');