<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_pn_add_masschange', 'pn_admin_title_pn_add_masschange');
function pn_admin_title_pn_add_masschange(){
	$id = intval(is_param_get('item_id'));
	if($id){
		_e('Edit rate','pn');
	} else {
		_e('Add rate','pn');
	}
} 

add_action('pn_adminpage_content_pn_add_masschange','def_pn_admin_content_pn_add_masschange');
function def_pn_admin_content_pn_add_masschange(){
global $wpdb;

	$id = intval(is_param_get('item_id'));
	$data_id = 0;
	$data = '';
	
	if($id){
		$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."masschange WHERE id='$id'");
		if(isset($data->id)){
			$data_id = $data->id;
		}	
	}

	if($data_id){
		$title = __('Edit rate','pn');
	} else {
		$title = __('Add rate','pn');
	}	
	
	$back_menu = array();
	$back_menu['back'] = array(
		'link' => admin_url('admin.php?page=pn_masschange'),
		'title' => __('Back to list','pn')
	);
	if($data_id){
		$back_menu['add'] = array(
			'link' => admin_url('admin.php?page=pn_add_masschange'),
			'title' => __('Add new','pn')
		);	
	}	
	pn_admin_back_menu($back_menu, $data);	
	
	$options = array();
	$options['hidden_block'] = array(
		'view' => 'hidden_input',
		'name' => 'data_id',
		'default' => $data_id,
	);	
	$options['top_title'] = array(
		'view' => 'h3',
		'title' => $title,
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);	
	$options['title'] = array(
		'view' => 'inputbig',
		'title' => __('Rate name','pn'),
		'default' => is_isset($data, 'title'),
		'name' => 'title',
	);	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('pn_masschange_addform', $options, $data);	
} 

/* обработка формы */
add_action('premium_action_pn_add_masschange','def_premium_action_pn_add_masschange');
function def_premium_action_pn_add_masschange(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_masschange'));
	
	$data_id = intval(is_param_post('data_id'));
	$last_data = '';
	if($data_id > 0){
		$last_data = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "masschange WHERE id='$data_id'");
		if(!isset($last_data->id)){
			$data_id = 0;
		}
	}	
	
	$array = array();
	$array['title'] = pn_strip_input(is_param_post('title'));
			
	$array = apply_filters('pn_masschange_addform_post',$array, $last_data);
			
	if($data_id){		
		do_action('pn_masschange_edit_before', $data_id, $array, $last_data);
		$result = $wpdb->update($wpdb->prefix.'masschange', $array, array('id'=>$data_id));
		if($result){
			do_action('pn_masschange_edit', $data_id, $array, $last_data);
		}	
	} else {		
		$wpdb->insert($wpdb->prefix.'masschange', $array);
		$data_id = $wpdb->insert_id;	
		do_action('pn_masschange_add', $data_id, $array);		
	}

	$url = admin_url('admin.php?page=pn_add_masschange&item_id='. $data_id .'&reply=true');
	wp_redirect($url);
	exit;
}	
/* end обработка формы */