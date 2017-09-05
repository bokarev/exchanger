<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('admin_menu', 'pn_adminpage_reserv');
function pn_adminpage_reserv(){
global $premiumbox;
	if(current_user_can('administrator') or current_user_can('pn_reserv')){
		$hook = add_menu_page(__('Reserve adjustment','pn'), __('Reserve adjustment','pn'), 'read', "pn_reserv", array($premiumbox, 'admin_temp'), $premiumbox->get_icon_link('reserv'));	
		add_action( "load-$hook", 'pn_trev_hook' );
		add_submenu_page("pn_reserv", __('Add reserve transaction','pn'), __('Add reserve transaction','pn'), 'read', "pn_add_reserv", array($premiumbox, 'admin_temp'));
	}
}

add_filter('pn_caps','reserv_pn_caps');
function reserv_pn_caps($pn_caps){
	$pn_caps['pn_reserv'] = __('Use adjustment reserve','pn');
	return $pn_caps;
}

/* фильтры */
add_action('change_bidstatus_all', 'reserv_change_bidstatus', 1000, 3);
function reserv_change_bidstatus($action, $obmen_id, $obmen){
	update_valut_reserv($obmen->valut1i);
	update_valut_reserv($obmen->valut2i);
}

add_action('pn_valuts_edit','reserv_pn_valuts_edit',1,2);
function reserv_pn_valuts_edit($data_id, $array){
	$object = (object)$array;
	update_valut_reserv($data_id, $object);
} 

add_action('pn_valuts_delete','reserv_pn_valuts_delete', 10, 2);
function reserv_pn_valuts_delete($id, $item){
global $wpdb;

	$items = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."trans_reserv WHERE valut_id = '$id'");
	foreach($items as $item){
		$item_id = $item->id;
		do_action('pn_reserv_delete_before', $item_id, $item);
		$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."trans_reserv WHERE id = '$item_id'");
		if($result){
			do_action('pn_reserv_delete', $item_id, $item);
		}
	}
}

add_action('pn_vtypes_edit','reserv_pn_vtypes_edit',1,2);
function reserv_pn_vtypes_edit($data_id, $array){
global $wpdb;
	$vtype_title = is_isset($array,'vtype_title');
	$wpdb->update($wpdb->prefix.'trans_reserv', array('vtype_title'=>$vtype_title), array('vtype_id'=>$data_id));
}

add_action('pn_reserv_delete','reserv_pn_reserv_delete', 10, 2);
function reserv_pn_reserv_delete($id, $item){
global $wpdb;

	update_valut_reserv($item->valut_id);
}
/* end фильтры */

global $premiumbox;
$premiumbox->include_patch(__FILE__, 'add');
$premiumbox->include_patch(__FILE__, 'list');