<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_pn_add_blackbroker', 'pn_admin_title_pn_add_blackbroker');
function pn_admin_title_pn_add_blackbroker(){
	$id = intval(is_param_get('item_id'));
	if($id){
		_e('Edit website','pn');
	} else {
		_e('Add website','pn');
	}
} 

add_action('pn_adminpage_content_pn_add_blackbroker','def_pn_admin_content_pn_add_blackbroker');
function def_pn_admin_content_pn_add_blackbroker(){
global $wpdb;

	$id = intval(is_param_get('item_id'));
	$data_id = 0;
	$data = '';
	
	if($id){
		$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."blackbrokers WHERE id='$id'");
		if(isset($data->id)){
			$data_id = $data->id;
		}	
	}

	if($data_id){
		$title = __('Edit website','pn');
	} else {
		$title = __('Add website','pn');
	}	
	
	$back_menu = array();
	$back_menu['back'] = array(
		'link' => admin_url('admin.php?page=pn_blackbroker'),
		'title' => __('Back to list','pn')
	);
	if($data_id){
		$back_menu['add'] = array(
			'link' => admin_url('admin.php?page=pn_add_blackbroker'),
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
		'title' => __('Website name','pn'),
		'default' => is_isset($data, 'title'),
		'name' => 'title',
	);	
	$options['url'] = array(
		'view' => 'inputbig',
		'title' => __('XML URL','pn'),
		'default' => is_isset($data, 'url'),
		'name' => 'url',
	);		
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('pn_blackbrokers_addform', $options, $data);	
} 

/* обработка формы */
add_action('premium_action_pn_add_blackbroker','def_premium_action_pn_add_blackbroker');
function def_premium_action_pn_add_blackbroker(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_naps'));
	
	$data_id = intval(is_param_post('data_id'));
	$last_data = '';
	if($data_id > 0){
		$last_data = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "blackbrokers WHERE id='$data_id'");
		if(!isset($last_data->id)){
			$data_id = 0;
		}
	}	
	
	$array = array();
	$array['title'] = pn_strip_input(is_param_post('title'));
	$array['url'] = pn_strip_input(is_param_post('url'));
	
	$array = apply_filters('pn_blackbrokers_addform_post',$array, $last_data);
			
	if($data_id){		
		do_action('pn_blackbrokers_edit_before', $data_id, $array, $last_data);
		$result = $wpdb->update($wpdb->prefix.'blackbrokers', $array, array('id'=>$data_id));
		if($result){
			do_action('pn_blackbrokers_edit', $data_id, $array, $last_data);
		}	
	} else {		
		$wpdb->insert($wpdb->prefix.'blackbrokers', $array);
		$data_id = $wpdb->insert_id;	
		do_action('pn_blackbrokers_add', $data_id, $array);		
	}

	$url = admin_url('admin.php?page=pn_add_blackbroker&item_id='. $data_id .'&reply=true');
	wp_redirect($url);
	exit;
}	
/* end обработка формы */