<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Пользовательские статусы заявок[:ru_RU][en_US:]Users' orders status[:en_US]
description: [ru_RU:]Пользовательские статусы заявок[:ru_RU][en_US:]Users' orders status[:en_US]
version: 1.0
category: [ru_RU:]Направления обменов[:ru_RU][en_US:]Exchange directions[:en_US]
cat: naps
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* BD */
add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_bidstatus');
function bd_pn_moduls_active_bidstatus(){
global $wpdb;
	
	$table_name = $wpdb->prefix ."bidstatus";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT ,
		`title` longtext NOT NULL,
		`bg_color` varchar(250) NOT NULL default '0',
		`status_order` bigint(20) NOT NULL default '0',
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);	

	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."bidstatus LIKE 'bg_color'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."bidstatus ADD `bg_color` varchar(250) NOT NULL default '0'");
    }	
	
}

add_action('pn_bd_activated', 'bd_pn_moduls_migrate_bidstatus');
function bd_pn_moduls_migrate_bidstatus(){
global $wpdb;

	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."bidstatus LIKE 'bg_color'");
	if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."bidstatus ADD `bg_color` varchar(250) NOT NULL default '0'");
	}
}
/* end BD */

add_filter('pn_caps','bidstatus_pn_caps');
function bidstatus_pn_caps($pn_caps){
	$pn_caps['pn_bidstatus'] = __('Work with orders status','pn');
	return $pn_caps;
}

add_action('admin_menu', 'pn_adminpage_bidstatus');
function pn_adminpage_bidstatus(){
global $premiumbox;	
	if(current_user_can('administrator') or current_user_can('pn_bidstatus')){
		$hook = add_menu_page( __('Orders status','pn'), __('Orders status','pn'), 'read', "pn_bidstatus", array($premiumbox, 'admin_temp'), $premiumbox->get_icon_link('mystatus'));	
		add_action( "load-$hook", 'pn_trev_hook' );
		add_submenu_page("pn_bidstatus", __('Add','pn'), __('Add','pn'), 'read', "pn_add_bidstatus", array($premiumbox, 'admin_temp'));
		add_submenu_page("pn_bidstatus", __('Sort','pn'), __('Sort','pn'), 'read', "pn_sort_bidstatus", array($premiumbox, 'admin_temp'));
	}
}

add_filter('bid_status_list', 'bid_status_list_bidstatus');
function bid_status_list_bidstatus($bid_status_list){
global $wpdb;	
	
	$bids_my_statused = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."bidstatus ORDER BY status_order ASC");
	foreach($bids_my_statused as $item){
		$bid_status_list['my'.$item->id] = pn_strip_input(ctv_ml($item->title));
	}
	
	return $bid_status_list;
}

add_action('pn_adminpage_style', 'pn_adminpage_style_bidstatus');
function pn_adminpage_style_bidstatus(){
global $wpdb;	
	$bids_my_statused = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."bidstatus ORDER BY status_order ASC");
	$colors_for_bidstatus = apply_filters('colors_for_bidstatus', array());
	$color = array();
	foreach($colors_for_bidstatus as $cd_key => $cd){
		$color[$cd_key] = is_isset($cd,'color');
	}
	foreach($bids_my_statused as $item){
?>
	.st_my<?php echo $item->id; ?>{ background: <?php echo is_isset($color, $item->bg_color); ?>; }
<?php
	}
?>
<?php	
}

global $premiumbox;
$premiumbox->file_include($path.'/add');
$premiumbox->file_include($path.'/list');
$premiumbox->file_include($path.'/sort');