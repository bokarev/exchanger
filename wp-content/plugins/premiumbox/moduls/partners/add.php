<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** добавить ************************************************/

add_action('pn_adminpage_title_pn_addpartners', 'pn_adminpage_title_pn_addpartners');
function pn_adminpage_title_pn_addpartners(){
	$id = intval(is_param_get('item_id'));
	if($id){
		_e('Edit partners','pn');
	} else {
		_e('Add partners','pn');
	}
}

add_action('pn_adminpage_content_pn_addpartners','def_pn_adminpage_content_pn_addpartners');
function def_pn_adminpage_content_pn_addpartners(){
global $wpdb;

	$id = intval(is_param_get('item_id'));
	$data_id = 0;
	$data = '';
	
	if($id){
		$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."partners WHERE id='$id'");
		if(isset($data->id)){
			$data_id = $data->id;
		}	
	}

	if($data_id){
		$title = __('Edit partners','pn');
	} else {
		$title = __('Add partners','pn');
	}
	
	$back_menu = array();
	$back_menu['back'] = array(
		'link' => admin_url('admin.php?page=pn_partners'),
		'title' => __('Back to list','pn')
	);
	if($data_id){
		$back_menu['add'] = array(
			'link' => admin_url('admin.php?page=pn_addpartners'),
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
		'title' => __('Title','pn'),
		'default' => is_isset($data, 'title'),
		'name' => 'title',
		'work' => 'input',
		'ml' => 1,
	);	
	$options['link'] = array(
		'view' => 'inputbig',
		'title' => __('Link','pn'),
		'default' => is_isset($data, 'link'),
		'name' => 'link',
		'work' => 'input',
	);	
	$options['img'] = array(
		'view' => 'uploader',
		'title' => __('Logo', 'pn'),
		'default' => is_isset($data, 'img'),
		'name' => 'img',
		'work' => 'input',
	);	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('pn_partners_addform', $options, $data);				
}

add_action('premium_action_pn_addpartners','def_premium_action_pn_addpartners');
function def_premium_action_pn_addpartners(){
global $wpdb;

	only_post();
	pn_only_caps(array('administrator'));
		
	$data_id = intval(is_param_post('data_id')); 
	$last_data = '';
	if($data_id > 0){
		$last_data = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "partners WHERE id='$data_id'");
		if(!isset($last_data->id)){
			$data_id = 0;
		}
	}		
	
	$array = array();
	$array['title'] = pn_strip_input(is_param_post_ml('title'));
	$array['link'] = esc_url(pn_strip_input(is_param_post('link')));
	$array['img'] = pn_strip_input(is_param_post('img'));

	$array = apply_filters('pn_partners_addform_post',$array, $last_data);
			
	if($data_id){
		do_action('pn_partners_edit_before', $data_id, $array, $last_data);
		$result = $wpdb->update($wpdb->prefix.'partners', $array, array('id'=>$data_id));
		if($result){
			do_action('pn_partners_edit', $data_id, $array, $last_data);
		}
	} else {
		$wpdb->insert($wpdb->prefix.'partners', $array);
		$data_id = $wpdb->insert_id;
		do_action('pn_partners_add', $data_id, $array);
	}

	$url = admin_url('admin.php?page=pn_addpartners&item_id='. $data_id .'&reply=true');
	wp_redirect($url);
	exit;
}	