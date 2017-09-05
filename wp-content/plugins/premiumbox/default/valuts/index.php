<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('admin_menu', 'pn_adminpage_valuts');
function pn_adminpage_valuts(){
global $premiumbox;	
	
	if(current_user_can('administrator') or current_user_can('pn_valuts')){
		
		$hook = add_menu_page( __('Currency','pn'), __('Currency','pn'), 'read', "pn_valuts", array($premiumbox, 'admin_temp'), $premiumbox->get_icon_link('valuts'));	
		add_action( "load-$hook", 'pn_trev_hook' );
		add_submenu_page("pn_valuts", __('Add currency','pn'), __('Add currency','pn'), 'read', "pn_add_valuts", array($premiumbox, 'admin_temp'));
		add_submenu_page("pn_valuts", __('Sort currency','pn'), __('Sort currency','pn'), 'read', "pn_sort_valuts", array($premiumbox, 'admin_temp'));
		add_submenu_page("pn_valuts", __('Sort reserve','pn'), __('Sort reserve','pn'), 'read', "pn_sort_valuts_reserve", array($premiumbox, 'admin_temp'));
	
	}
	
}

add_filter('pn_caps','valuts_pn_caps');
function valuts_pn_caps($pn_caps){
	$pn_caps['pn_valuts'] = __('Use currencies','pn');
	return $pn_caps;
}

/* фильтры */
add_action('pn_valuts_delete','def_pn_valuts_delete',0,2);
function def_pn_valuts_delete($data_id, $item){
global $wpdb;
	
	$items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."valuts_meta WHERE item_id = '$data_id'");
	foreach($items as $item){
		$item_id = $item->id;
		do_action('pn_valutsmeta_delete_before', $id, $item);
		$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."valuts_meta WHERE id = '$item_id'");
		if($result){
			do_action('pn_valutsmeta_delete', $id, $item);
		}
	}
}

add_action('pn_vtypes_edit','def_pn_vtypes_edit',0,2);
function def_pn_vtypes_edit($data_id, $array){
global $wpdb;	
	if(isset($array['vtype_title'])){
		$wpdb->update($wpdb->prefix.'valuts', array('vtype_title'=>$array['vtype_title']), array('vtype_id'=>$data_id));
	}
}

add_action('pn_vtypes_delete','def_pn_vtypes_delete');
function def_pn_vtypes_delete($id){
global $wpdb;
	$wpdb->update($wpdb->prefix.'valuts', array('vtype_title'=> '', 'vtype_id'=> 0), array('vtype_id'=>$id));
}

add_action('pn_psys_edit','def_pn_psys_edit',0,2);
function def_pn_psys_edit($data_id, $array){
global $wpdb;	
	if(isset($array['psys_title'], $array['psys_logo'])){ 
		$wpdb->update($wpdb->prefix.'valuts', array('psys_title'=>$array['psys_title'],'psys_logo'=>$array['psys_logo']), array('psys_id'=>$data_id));
	}
}

add_action('pn_psys_delete', 'def_pn_psys_delete');
function def_pn_psys_delete($id){
global $wpdb;
	$wpdb->update($wpdb->prefix.'valuts', array('psys_title'=> '', 'psys_id'=> 0), array('psys_id'=>$id));
}

add_filter('list_valuts_manage', 'def_list_valuts_manage',0,3);
function def_list_valuts_manage($valuts, $default, $show_decimal=0){
global $wpdb;

	$valuts = $valuts_info = array();
	$valuts[0] = '--'.$default.'--';
	$valuts_info[0] = array(
		'title' => '--'.$default.'--',
		'decimal' => 0,
	);
	$valuts_datas = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."valuts ORDER BY site_order ASC");
	foreach($valuts_datas as $valut){
		$title = pn_strip_input(ctv_ml($valut->psys_title)) .' '. is_site_value($valut->vtype_title);
		$valuts[$valut->id] = $title;
		$valuts_info[$valut->id] = array(
			'title' => $title,
			'decimal' => $valut->valut_decimal,
		);
	}
	
	if($show_decimal == 1){
		return $valuts_info;
	} else {
		return $valuts;
	}
}
/* end фильтры */

global $premiumbox;
$premiumbox->include_patch(__FILE__, 'add');
$premiumbox->include_patch(__FILE__, 'list');
$premiumbox->include_patch(__FILE__, 'sort');
$premiumbox->include_patch(__FILE__, 'sortres');