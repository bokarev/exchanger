<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('admin_menu', 'pn_adminpage_vtypes');
function pn_adminpage_vtypes(){
global $premiumbox;
	
	if(current_user_can('administrator') or current_user_can('pn_vtypes')){
		$hook = add_menu_page(__('Currency codes','pn'), __('Currency codes','pn'), 'read', "pn_vtypes", array($premiumbox, 'admin_temp'), $premiumbox->get_icon_link('vtypes'));	
		add_action( "load-$hook", 'pn_trev_hook' );
		add_submenu_page("pn_vtypes", __('Add currency code','pn'), __('Add currency code','pn'), 'read', "pn_add_vtypes", array($premiumbox, 'admin_temp'));
	}
}

add_filter('pn_caps','vtypes_pn_caps');
function vtypes_pn_caps($pn_caps){
	$pn_caps['pn_vtypes'] = __('Use currency codes','pn');
	return $pn_caps;
}

/* фильтры */
add_action('load_parser_courses','vtype_load_parser_courses');
function vtype_load_parser_courses(){
	update_vtypes_to_parser();
}

add_action('pn_vtypes_edit','parser_pn_vtypes_edit',0,2);
add_action('pn_vtypes_add','parser_pn_vtypes_edit',0,2);
function parser_pn_vtypes_edit($data_id, $array){
	if($data_id){
		update_vtypes_to_parser($data_id);		
	}	
} 

add_action('pn_vtypes_save','parser_pn_vtypes_save');
function parser_pn_vtypes_save(){
	update_vtypes_to_parser();
}

add_filter('list_vtypes_manage', 'def_list_vtypes_manage',0,2);
function def_list_vtypes_manage($vtypes, $default){
global $wpdb;

	$vtypes = array();
	$vtypes[0] = '--'.$default.'--';
	$vtypes_datas = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."vtypes ORDER BY vtype_title ASC");
	foreach($vtypes_datas as $item){
		$vtypes[$item->id] = is_site_value($item->vtype_title);
	}
	return $vtypes;
}
/* end фильтры */

global $premiumbox;
$premiumbox->include_patch(__FILE__, 'add');
$premiumbox->include_patch(__FILE__, 'list');