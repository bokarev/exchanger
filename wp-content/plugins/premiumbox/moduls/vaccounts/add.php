<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** добавить ************************************************/
add_action('pn_adminpage_title_pn_add_vaccounts', 'pn_admin_title_pn_add_vaccounts');
function pn_admin_title_pn_add_vaccounts(){
	$id = intval(is_param_get('item_id'));
	if($id){
		_e('Edit account','pn');
	} else {
		_e('Add account','pn');
	}
}

add_action('pn_adminpage_content_pn_add_vaccounts','def_pn_admin_content_pn_add_vaccounts');
function def_pn_admin_content_pn_add_vaccounts(){
global $wpdb;

	$id = intval(is_param_get('item_id'));
	$data_id = 0;
	$data = '';
	
	if($id){
		$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."valuts_account WHERE id='$id'");
		if(isset($data->id)){
			$data_id = $data->id;
		}	
	}

	if($data_id){
		$title = __('Edit account','pn');
	} else {
		$title = __('Add account','pn');
	}
	
	
	$valuts = apply_filters('list_valuts_manage', array(), __('No item','pn'));	

	$back_menu = array();
	$back_menu['back'] = array(
		'link' => admin_url('admin.php?page=pn_vaccounts'),
		'title' => __('Back to list','pn')
	);
	if($data_id){
		$back_menu['add'] = array(
			'link' => admin_url('admin.php?page=pn_add_vaccounts'),
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
	$options['text_comment'] = array(
		'view' => 'inputbig',
		'title' => __('Comment','pn'),
		'default' => is_isset($data, 'text_comment'),
		'name' => 'text_comment',
	);	
	$options['valut_id'] = array(
		'view' => 'select',
		'title' => __('Currency name','pn'),
		'options' => $valuts,
		'default' => is_isset($data, 'valut_id'),
		'name' => 'valut_id',
	);
	$options['accountnum'] = array(
		'view' => 'inputbig',
		'title' => __('Account','pn'),
		'default' => is_isset($data, 'accountnum'),
		'name' => 'accountnum',
	);					
	$options['count_visit'] = array(
		'view' => 'input',
		'title' => __('Hits','pn'),
		'default' => is_isset($data, 'count_visit'),
		'name' => 'count_visit',
	);					
	$options['max_visit'] = array(
		'view' => 'input',
		'title' => __('Hits limit','pn'),
		'default' => is_isset($data, 'max_visit'),
		'name' => 'max_visit',
	);
	$options['inday'] = array(
		'view' => 'input',
		'title' => __('Daily limit','pn'),
		'default' => is_isset($data, 'inday'),
		'name' => 'inday',
	);
	$options['inmonth'] = array(
		'view' => 'input',
		'title' => __('Monthly limit','pn'),
		'default' => is_isset($data, 'inmonth'),
		'name' => 'inmonth',
	);	
	$options['status'] = array(
		'view' => 'select',
		'title' => __('Status','pn'),
		'options' => array('0'=>__('inactive account','pn'),'1'=>__('active account','pn')),
		'default' => is_isset($data, 'status'),
		'name' => 'status',
	);
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('pn_vaccounts_addform', $options, $data);	
}

/* обработка формы */
add_action('premium_action_pn_add_vaccounts','def_premium_action_pn_add_vaccounts');
function def_premium_action_pn_add_vaccounts(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_vaccounts'));
	
	$data_id = intval(is_param_post('data_id')); 
			
	$array = array();
	$array['text_comment'] = pn_strip_input(is_param_post('text_comment'));
			
	$array['accountnum'] = $accountnum = pn_strip_input(is_param_post('accountnum'));
	if(!$array['accountnum']){ pn_display_mess(__('You did not enter your account','pn')); }
			
	$array['inday'] = is_my_money(is_param_post('inday'));
	$array['inmonth'] = is_my_money(is_param_post('inmonth'));
			
	$array['valut_id'] = intval(is_param_post('valut_id'));
	$array['count_visit'] = intval(is_param_post('count_visit'));
	$array['max_visit'] = intval(is_param_post('max_visit'));
	$array['status'] = intval(is_param_post('status'));
			
	$array = apply_filters('pn_vaccounts_addform_post',$array);
			
	if($data_id){
		do_action('pn_vaccounts_edit_before', $data_id, $array);
		$result = $wpdb->update($wpdb->prefix.'valuts_account', $array, array('id'=>$data_id));
		if($result){
			do_action('pn_vaccounts_edit', $data_id, $array);
		}
	} else {
		$wpdb->insert($wpdb->prefix.'valuts_account', $array);
		$data_id = $wpdb->insert_id;	
		do_action('pn_vaccounts_add', $data_id, $array);
	}
			
	if($data_id){
		$otv = update_vaccs_txtmeta($data_id, 'accountnum', $accountnum);
		if($otv != 1){
			pn_display_mess(sprintf(__('Error! Directory <b>%s</b> do not exist or cannot be written! Create this directory or get permission 777.','pn'),'/wp-content/uploads/vaccsmeta/'));
		}				
	}

	$url = admin_url('admin.php?page=pn_add_vaccounts&item_id='. $data_id .'&reply=true');
	wp_redirect($url);
	exit;
}	
/* end обработка формы */