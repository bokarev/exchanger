<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_pn_maintrance_add', 'pn_adminpage_title_pn_maintrance_add');
function pn_adminpage_title_pn_maintrance_add(){
	$id = intval(is_param_get('item_id'));
	if($id){
		_e('Edit mode','pn');
	} else {
		_e('Add mode','pn');
	}
}

add_action('pn_adminpage_content_pn_maintrance_add','def_pn_adminpage_content_pn_maintrance_add');
function def_pn_adminpage_content_pn_maintrance_add(){
global $wpdb;

	$id = intval(is_param_get('item_id'));
	$data_id = 0;
	$data = '';
	
	if($id){
		$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."maintrance WHERE id='$id'");
		if(isset($data->id)){
			$data_id = $data->id;
		}	
	}

	if($data_id){
		$title = __('Edit mode','pn');
	} else {
		$title = __('Add mode','pn');
	}
	
	$changes = array(
		'0' => __('Do not hide','pn'),
		'1' => __('Do not hide and enter text','pn'),
		'2' => __('Hide','pn'),
	);
	
	$statuses = array();
	$status_operator = apply_filters('status_operator', array()); 
	if(is_array($status_operator)){
		foreach($status_operator as $key => $v){
			$statuses[$key] = $v;					
		}
	}

	$back_menu = array();
	$back_menu['back'] = array(
		'link' => admin_url('admin.php?page=pn_maintrance_list'),
		'title' => __('Back to list','pn')
	);
	if($data_id){
		$back_menu['add'] = array(
			'link' => admin_url('admin.php?page=pn_maintrance_add'),
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
	$options['the_title'] = array(
		'view' => 'inputbig',
		'title' => __('Website header','pn'),
		'default' => is_isset($data, 'the_title'),
		'name' => 'the_title',
		'ml' => 1,
	);	
	$options['show_text'] = array(
		'view' => 'textarea',
		'title' => __('Message for customers','pn'),
		'default' => is_isset($data, 'show_text'),
		'name' => 'show_text',
		'width' => '',
		'height' => '100px',
		'ml' => 1,
	);
	$options['line1'] = array(
		'view' => 'line',
		'colspan' => 2,
	);		
	$cats = apply_filters('pn_exchange_cat_filters', array());
	$pages_law = unserialize(is_isset($data, 'pages_law'));
	foreach($cats as $cat => $c_title){
		$options[] = array(
			'view' => 'select',
			'title' => $c_title,
			'options' => $changes,
			'default' => is_isset($pages_law, $cat),
			'name' => 'pages_law['. $cat .']',
		);		
	}
	$options['line2'] = array(
		'view' => 'line',
		'colspan' => 2,
	);	
	$options['operator_status'] = array(
		'view' => 'select',
		'title' => __('Activate mode in case when operator status is','pn'),
		'options' => $statuses,
		'default' => is_isset($data, 'operator_status'),
		'name' => 'operator_status',
	);
	$options['line3'] = array(
		'view' => 'line',
		'colspan' => 2,
	);
	$options['for_whom'] = array(
		'view' => 'select',
		'title' => __('Apply mode','pn'),
		'options' => array('0'=>__('For users and administrators','pn'),'1'=>__('For users','pn')),
		'default' => is_isset($data, 'for_whom'),
		'name' => 'for_whom',
	);	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('pn_maintrance_addform', $options, $data);				
}

/* обработка формы */
add_action('premium_action_pn_maintrance_add','def_premium_action_pn_maintrance_add');
function def_premium_action_pn_maintrance_add(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_maintrance'));
	
	$data_id = intval(is_param_post('data_id'));
	$last_data = '';
	if($data_id > 0){
		$last_data = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "maintrance WHERE id='$data_id'");
		if(!isset($last_data->id)){
			$data_id = 0;
		}
	}	
	$array = array();
	$array['the_title'] = pn_strip_input(is_param_post_ml('the_title'));
	$array['show_text'] = pn_strip_input(is_param_post_ml('show_text'));		
	$array['pages_law'] = @serialize(is_param_post('pages_law'));		
	$array['operator_status'] = intval(is_param_post('operator_status'));
	$array['for_whom'] = intval(is_param_post('for_whom'));		
	$array = apply_filters('pn_maintrance_addform_post',$array,$last_data);
			
	if($data_id){		
		do_action('pn_maintrance_edit_before', $data_id, $array, $last_data);
		$result = $wpdb->update($wpdb->prefix.'maintrance', $array, array('id'=>$data_id));
		if($result){
			do_action('pn_maintrance_edit', $data_id, $array, $last_data);
		}	
	} else {
		$wpdb->insert($wpdb->prefix.'maintrance', $array);
		$data_id = $wpdb->insert_id;	
		do_action('pn_maintrance_add', $data_id, $array);
	}

	$url = admin_url('admin.php?page=pn_maintrance_add&item_id='. $data_id .'&reply=true');
	wp_redirect($url);
	exit;
}	
/* end обработка формы */