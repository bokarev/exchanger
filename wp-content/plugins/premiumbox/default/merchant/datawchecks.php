<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_pn_data_wchecks', 'pn_admin_title_pn_data_wchecks');
function pn_admin_title_pn_data_wchecks(){
	_e('Accounts verification checker settings','pn');
}

add_action('pn_adminpage_content_pn_data_wchecks','def_pn_admin_content_pn_data_wchecks');
function def_pn_admin_content_pn_data_wchecks(){
global $wpdb;
		
	$m_id = is_extension_name(is_param_get('m_id'));

	$list_wchecks = apply_filters('list_wchecks',array());
	$list_wchecks_t = array();
	foreach($list_wchecks as $data){
		$list_wchecks_t[] = is_isset($data,'id');
	}
	
	$selects = array();
	$selects[] = array(
		'link' => admin_url("admin.php?page=pn_data_wchecks"),
		'title' => '--' . __('Make a choice','pn') . '--',
		'background' => '',
		'default' => '',
	);		
	if(is_array($list_wchecks)){  
		foreach($list_wchecks as $data){
			$id = is_isset($data,'id');
			$title = is_isset($data,'title');	
			$selects[] = array(
				'link' => admin_url("admin.php?page=pn_data_wchecks&m_id=".$id),
				'title' => $title,
				'background' => '',
				'default' => $id,
			);			
		}
	}	
	pn_admin_select_box($m_id, $selects, __('Setting up','pn'));

	if(in_array($m_id,$list_wchecks_t)){
		do_action('wchecks_admin', $m_id);		
	} 
} 