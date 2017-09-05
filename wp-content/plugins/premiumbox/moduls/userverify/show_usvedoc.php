<?php
if( !defined( 'ABSPATH')){ exit(); } 

/* ссылка на файл */
function get_usve_doc_view($uv_field_user_id){
	return pn_link_post('usvedoc_view').'&id='. $uv_field_user_id;
}
/* end ссылка на файл */

add_action('premium_action_usvedoc_view', 'def_premium_action_usvedoc_view');
function def_premium_action_usvedoc_view(){
global $wpdb, $premiumbox; 

	pn_only_caps(array('administrator','pn_userverify'));	

	$id = intval(is_param_get('id'));
	if(!$id){
		pn_display_mess(__('Error!','pn'));
	}

	$data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."uv_field_user WHERE id='$id'");
	$file_id = intval(is_isset($data,'id'));

	if($file_id < 1){
		pn_display_mess(__('Error!','pn'));
	}

	$wp_dir = wp_upload_dir();
	$file = $wp_dir['basedir'].'/userverify/'. $data->uv_id .'/'. $data->uv_data;
	$newfile = $wp_dir['basedir'].'/usveshow/'. $data->uv_data;
	$linkfile = $wp_dir['baseurl'].'/usveshow/'. $data->uv_data;
	$path = $wp_dir['basedir'].'/usveshow/';
	if(!is_dir($path)){ 
		@mkdir($path , 0777);
	}	
	
	if(is_file($file) and is_dir($path)){
		if (@copy($file, $newfile)) {
			
			$url = $linkfile;
			wp_redirect($url);
			exit;		
			
		} else {
			pn_display_mess(__('Error! File does not copy','pn'));
		}
	} else {
		pn_display_mess(__('Error! File does not exist','pn'));
	}
}