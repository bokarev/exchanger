<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_pn_add_psys', 'pn_admin_title_pn_add_psys');
function pn_admin_title_pn_add_psys(){
	$id = intval(is_param_get('item_id'));
	if($id){
		_e('Edit payment system','pn');
	} else {
		_e('Add payment system','pn');
	}
}

add_action('pn_adminpage_content_pn_add_psys','def_pn_admin_content_pn_add_psys');
function def_pn_admin_content_pn_add_psys(){
global $wpdb;

	$id = intval(is_param_get('item_id'));
	$data_id = 0;
	$data = '';
	
	if($id){
		$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."psys WHERE id='$id'");
		if(isset($data->id)){
			$data_id = $data->id;
		}	
	}

	if($data_id){
		$title = __('Edit payment system','pn');
	} else {
		$title = __('Add payment system','pn');
	}
	
	$back_menu = array();
	$back_menu['back'] = array(
		'link' => admin_url('admin.php?page=pn_psys'),
		'title' => __('Back to list','pn')
	);
	if($data_id){
		$back_menu['add'] = array(
			'link' => admin_url('admin.php?page=pn_add_psys'),
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
	$options['psys_title'] = array(
		'view' => 'inputbig',
		'title' => __('PS title','pn'),
		'default' => is_isset($data, 'psys_title'),
		'name' => 'psys_title',
		'work' => 'input',
		'ml' => 1,
	);		
	$pn_icon_size = apply_filters('pn_icon_size','50 x 50');
	$options['psys_logo'] = array(
		'view' => 'uploader',
		'title' => __('Logo','pn').' ('. $pn_icon_size .')',
		'default' => is_isset($data, 'psys_logo'),
		'name' => 'psys_logo',
		'work' => 'input',
	);	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('pn_psys_addform', $options, $data);	
}

add_action('premium_action_pn_add_psys','def_premium_action_pn_add_psys');
function def_premium_action_pn_add_psys(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_psys'));
	
	$data_id = intval(is_param_post('data_id')); 
	$last_data = '';
	if($data_id > 0){
		$last_data = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "psys WHERE id='$data_id'");
		if(!isset($last_data->id)){
			$data_id = 0;
		}
	}	
	
	$array = array();
	$array['psys_title'] = $psys_title = pn_strip_input(is_param_post_ml('psys_title'));
			
	if(!$psys_title){ 
		pn_display_mess(__('Error! You did not enter the name','pn')); 
	}

	$array['psys_logo'] = esc_url(is_param_post('psys_logo'));
			
	$array = apply_filters('pn_psys_addform_post',$array, $last_data);
			
	$cc = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."psys WHERE psys_title='$psys_title' AND id != '$data_id'");
	if($cc > 0){
		pn_display_mess(__('Error! This currency code already exists','pn'));
	}
			
	if($data_id){		
		do_action('pn_psys_edit_before', $data_id, $array, $last_data);
		$result = $wpdb->update($wpdb->prefix.'psys', $array, array('id'=>$data_id));
		if($result){
			do_action('pn_psys_edit', $data_id, $array, $last_data);
		}	
	} else {
		$wpdb->insert($wpdb->prefix.'psys', $array);
		$data_id = $wpdb->insert_id;	
		do_action('pn_psys_add', $data_id, $array);
	}

	$url = admin_url('admin.php?page=pn_add_psys&item_id='. $data_id .'&reply=true');
	wp_redirect($url);
	exit;
}	