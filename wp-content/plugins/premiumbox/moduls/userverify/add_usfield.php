<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_pn_add_usfield', 'def_adminpage_title_pn_add_usfield');
function def_adminpage_title_pn_add_usfield(){
	$id = intval(is_param_get('item_id'));
	if($id){
		_e('Edit verification field','pn');
	} else {
		_e('Add verification field','pn');
	}
}

add_action('pn_adminpage_content_pn_add_usfield','def_adminpage_content_pn_add_usfield');
function def_adminpage_content_pn_add_usfield(){
global $wpdb;

	$id = intval(is_param_get('item_id'));
	$data_id = 0;
	$data = '';
	
	if($id){
		$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."uv_field WHERE id='$id'");
		if(isset($data->id)){
			$data_id = $data->id;
		}	
	}

	if($data_id){
		$title = __('Edit verification field','pn');
	} else {
		$title = __('Add verification field','pn');
	}
	
	$langs = get_langs_ml();
	$the_lang = array();
	$the_lang[0] = __('All','pn');
	foreach($langs as $lan){
		$the_lang[$lan] = get_title_forkey($lan);
	}	
	
	$back_menu = array();
	$back_menu['back'] = array(
		'link' => admin_url('admin.php?page=pn_usfield'),
		'title' => __('Back to list','pn')
	);
	if($data_id){
		$back_menu['add'] = array(
			'link' => admin_url('admin.php?page=pn_add_usfield'),
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
		'title' => __('Verification field title','pn'),
		'default' => is_isset($data, 'title'),
		'name' => 'title',
		'work' => 'input',
		'ml' => 1,
	);			
	$options['fieldvid'] = array(
		'view' => 'select',
		'title' => __('Verification field type','pn'),
		'options' => array('0'=> __('Text input field','pn'), '1'=> __('File','pn')),
		'default' => is_isset($data, 'fieldvid'),
		'name' => 'fieldvid',
	);	
	
	$uv_auto = apply_filters('uv_auto_filed', array());	
	
	$options['uv_auto'] = array(
		'view' => 'select',
		'title' => __('Automatic completion','pn'),
		'options' => $uv_auto,
		'default' => is_isset($data, 'uv_auto'),
		'name' => 'uv_auto',
	);		
	$options['uv_req'] = array(
		'view' => 'select',
		'title' => __('Required field','pn'),
		'options' => array('1'=>__('Yes','pn'),'0'=>__('No','pn')),
		'default' => is_isset($data, 'uv_req'),
		'name' => 'uv_req',
	);
	$options['helps'] = array(
		'view' => 'textarea',
		'title' => __('Tip for field','pn'),
		'default' => is_isset($data, 'helps'),
		'name' => 'helps',
		'width' => '',
		'height' => '100px',
		'ml' => 1,
	);	
	$options['locale'] = array(
		'view' => 'select',
		'title' => __('Language','pn'),
		'options' => $the_lang,
		'default' => is_isset($data, 'locale'),
		'name' => 'locale',
	);	
	$options['status'] = array(
		'view' => 'select',
		'title' => __('Status','pn'),
		'options' => array('1'=>__('active field','pn'),'0'=>__('inactive field','pn')),
		'default' => is_isset($data, 'status'),
		'name' => 'status',
	);		
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('pn_usfield_addform', $options, $data);	
} 

/* обработка формы */
add_action('premium_action_pn_add_usfield','def_premium_action_pn_add_usfield');
function def_premium_action_pn_add_usfield(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_userverify'));

	$data_id = intval(is_param_post('data_id'));
	$last_data = '';
	if($data_id > 0){
		$last_data = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "uv_field WHERE id='$data_id'");
		if(!isset($last_data->id)){
			$data_id = 0;
		}
	}	
	
	$array = array();
	$array['title'] = pn_strip_input(is_param_post_ml('title'));
	$array['fieldvid'] = $vid = intval(is_param_post('fieldvid'));
	$array['uv_auto'] = pn_strip_input(is_param_post('uv_auto'));
	$array['uv_req'] = intval(is_param_post('uv_req'));
	$array['helps'] = pn_strip_input(is_param_post_ml('helps'));
	$array['locale'] = pn_strip_input(is_param_post('locale'));
	$array['status'] = intval(is_param_post('status'));

	$array = apply_filters('pn_usfield_addform_post',$array,$last_data);
			
	if($data_id){	
		do_action('pn_usfield_edit_before', $data_id, $array,$last_data);
		$result = $wpdb->update($wpdb->prefix.'uv_field', $array, array('id'=>$data_id));
		if($result){
			do_action('pn_usfield_edit', $data_id, $array,$last_data);
		}	
	} else {	
		$wpdb->insert($wpdb->prefix.'uv_field', $array);
		$data_id = $wpdb->insert_id;	
		do_action('pn_usfield_add', $data_id, $array);		
	}

	$url = admin_url('admin.php?page=pn_add_usfield&item_id='. $data_id .'&reply=true');
	wp_redirect($url);
	exit;
}
/* end обработка формы */