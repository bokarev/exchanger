<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('admin_menu', 'pn_adminpage_psys');
function pn_adminpage_psys(){
global $premiumbox;
	if(current_user_can('administrator') or current_user_can('pn_psys')){
		$hook = add_menu_page(__('Payment systems','pn'), __('Payment systems','pn'), 'read', "pn_psys", array($premiumbox, 'admin_temp'), $premiumbox->get_icon_link('psys'));	
		add_action( "load-$hook", 'pn_trev_hook' );
		add_submenu_page("pn_psys", __('Add payment system','pn'), __('Add payment system','pn'), 'read', "pn_add_psys", array($premiumbox, 'admin_temp'));
	}
}

add_filter('pn_caps','psys_pn_caps');
function psys_pn_caps($pn_caps){
	$pn_caps['pn_psys'] = __('Use payment systems','pn');
	return $pn_caps;
}

/* фильтры */
add_filter('list_psys_manage', 'def_list_psys_manage',0,2);
function def_list_psys_manage($psys, $default){
global $wpdb;
	
	$psys = $psys_v = array();
	$psys_datas = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."psys");
	foreach($psys_datas as $item){
		$psys_v[$item->id] = pn_strip_input(ctv_ml($item->psys_title));
	}
	asort($psys_v);
	
	$psys[0] = '--'. $default .'--';
	foreach($psys_v as $k => $v){
		$psys[$k] = $v;
	}
	
	return $psys;
}
/* end фильтры */

global $premiumbox;
$premiumbox->include_patch(__FILE__, 'add');
$premiumbox->include_patch(__FILE__, 'list');