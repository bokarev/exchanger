<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_reserv_delete','apbd_pn_reserv_delete');
add_action('pn_discount_delete','apbd_pn_reserv_delete');
function apbd_pn_reserv_delete($id){
global $wpdb;

	$wpdb->query("DELETE FROM ".$wpdb->prefix."db_admin_logs WHERE item_id = '$id'");
}

/************************/
add_action('pn_reserv_edit','apbd_pn_reserv', 10 , 3);
function apbd_pn_reserv($id, $array, $ldata=''){	

	$tbl_check = array(
		'trans_title' => __('Comment','pn'),
		'trans_summ' => __('Amount','pn'),
		'valut_id' => __('Currency name','pn'),
	);	
	
	insert_apbd('reserv', $tbl_check, $id, $array, $ldata);
}
add_action('pn_adminpage_content_pn_add_reserv','transreslogs_pn_admin_content_pn_add_reserv');
function transreslogs_pn_admin_content_pn_add_reserv(){
	$tbl_check = array(
		'trans_summ' => __('Amount','pn'),
		'valut_id' => __('Currency name','pn'),
		'trans_title' => __('Comment','pn'),
	);	
	view_apbd('reserv', $tbl_check);
}	
/************************/
add_action('pn_discount_edit','apbd_pn_discount', 10 , 3);
function apbd_pn_discount($id, $array, $ldata=''){	

	$tbl_check = array(
		'sumec' => __('Amount more than','pn'),
		'discount' => __('Discount (%)','pn'),
	);	
	
	insert_apbd('discount', $tbl_check, $id, $array, $ldata);
}
add_action('pn_adminpage_content_pn_add_discount','transreslogs_pn_admin_content_pn_add_discount');
function transreslogs_pn_admin_content_pn_add_discount(){
	$tbl_check = array(
		'sumec' => __('Amount more than','pn'),
		'discount' => __('Discount (%)','pn'),
	);	
	view_apbd('discount', $tbl_check);
}	
/************************/