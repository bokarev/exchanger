<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('admin_menu', 'pn_adminpage_cfc');
function pn_adminpage_cfc(){
global $premiumbox;	
	if(current_user_can('administrator') or current_user_can('pn_cfc')){
		$hook = add_menu_page(__('Custom currency fields','pn'), __('Custom currency fields','pn'), 'read', "pn_cfc", array($premiumbox, 'admin_temp'), $premiumbox->get_icon_link('vfield'));	
		add_action( "load-$hook", 'pn_trev_hook' );
		add_submenu_page("pn_cfc", __('Add custom field','pn'), __('Add custom field','pn'), 'read', "pn_add_cfc", array($premiumbox, 'admin_temp'));
		add_submenu_page("pn_cfc", __('Sort custom fields','pn'), __('Sort custom fields','pn'), 'read', "pn_sort_cfc", array($premiumbox, 'admin_temp'));
	}
}

add_filter('pn_caps','cfc_pn_caps');
function cfc_pn_caps($pn_caps){
	$pn_caps['pn_cfc'] = __('Work with custom currency fields','pn');
	return $pn_caps;
}

/* фильтры */
add_action('pn_valuts_delete','cfc_pn_valuts_delete');
function cfc_pn_valuts_delete($id){
global $wpdb;

	$items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."custom_fields_valut WHERE valut_id = '$id'");
	foreach($items as $item){
		$item_id = $item->id;
		do_action('pn_cfc_delete_before', $item_id, $item);
		$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."custom_fields_valut WHERE id = '$item_id'");
		if($result){
			do_action('pn_cfc_delete', $item_id, $item);
		}
	}
}
/* end фильтры */

global $premiumbox;
$premiumbox->include_patch(__FILE__, 'add');
$premiumbox->include_patch(__FILE__, 'list');
$premiumbox->include_patch(__FILE__, 'sort');