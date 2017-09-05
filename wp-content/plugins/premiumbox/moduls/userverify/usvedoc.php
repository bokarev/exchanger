<?php
if( !defined( 'ABSPATH')){ exit(); } 

/* ссылка на файл */
function get_usve_doc($uv_field_user_id){
	return get_ajax_link('usvedoc').'&id='. $uv_field_user_id;
}
/* end ссылка на файл */

add_action('myaction_site_usvedoc', 'def_myaction_ajax_usvedoc');
function def_myaction_ajax_usvedoc(){
global $wpdb, $premiumbox; 

	$premiumbox->up_mode();

	$id = intval(is_param_get('id'));
	if(!$id){
		pn_display_mess(__('Error!','pn'));
	}

	$data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."uv_field_user WHERE id='$id'");
	$dostup = 0;

	$file_id = intval(is_isset($data,'id'));

	if($file_id < 1){
		pn_display_mess(__('Error!','pn'));
	}

	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);	
	
	if($data->user_id == $user_id or current_user_can('administrator') or current_user_can('pn_userverify')){
		$dostup = 1;
	}

	if($dostup != 1){
		pn_display_mess(__('Error! Access denied','pn'));
	}

	$wp_dir = wp_upload_dir();

	$file = $wp_dir['basedir'].'/userverify/'. $data->uv_id .'/'. $data->uv_data;

	if(is_file($file)){
		if (ob_get_level()) {
			ob_end_clean();
		}
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=' . basename($file));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));
		@readfile($file);
		exit;
		
	} else {
		pn_display_mess(__('Error! File does not exist','pn'));
	}
}