<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_pn_add_userwallets', 'pn_adminpage_title_pn_add_userwallets');
function pn_adminpage_title_pn_add_userwallets(){
	$id = intval(is_param_get('item_id'));
	if($id){
		_e('Edit account','pn');
	} else {
		_e('Add account','pn');
	}
}

add_action('pn_adminpage_content_pn_add_userwallets','def_pn_adminpage_content_pn_add_userwallets');
function def_pn_adminpage_content_pn_add_userwallets(){
global $wpdb;

	$id = intval(is_param_get('item_id'));
	$data_id = 0;
	$data = '';
	
	if($id){
		$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."user_accounts WHERE id='$id'");
		if(isset($data->id)){
			$data_id = $data->id;
		}	
	}

	if($data_id){
		$title = __('Edit account','pn');
	} else {
		$title = __('Add account','pn');
	}
	
	$users = array();
	$users[0] = '-- '. __('No item','pn') .' --';
	$en_users = get_users();
	foreach($en_users as $en_user){
		$users[$en_user->ID] = is_user($en_user->user_login);
	}
	
	$valuts = apply_filters('list_valuts_manage', array(), __('No item','pn'));	
	
	$back_menu = array();
	$back_menu['back'] = array(
		'link' => admin_url('admin.php?page=pn_userwallets'),
		'title' => __('Back to list','pn')
	);
	if($data_id){
		$back_menu['add'] = array(
			'link' => admin_url('admin.php?page=pn_add_userwallets'),
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
	$options['user_id'] = array(
		'view' => 'select',
		'title' => __('User','pn'),
		'options' => $users,
		'default' => is_isset($data, 'user_id'),
		'name' => 'user_id',
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
		'title' => __('Account number','pn'),
		'default' => is_isset($data, 'accountnum'),
		'name' => 'accountnum',
	);	
	$options['verify'] = array(
		'view' => 'select',
		'title' => __('Status','pn'),
		'options' => array('0'=>__('Unverified','pn'), '1'=>__('Verified account','pn')),
		'default' => is_isset($data, 'verify'),
		'name' => 'verify',
	);
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('pn_userwallets_addform', $options, $data);	
}

/* обработка формы */
add_action('premium_action_pn_add_userwallets','def_premium_action_pn_add_userwallets');
function def_premium_action_pn_add_userwallets(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_userwallets'));
	
	$data_id = intval(is_param_post('data_id'));
	$last_data = '';
	if($data_id > 0){
		$last_data = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "user_accounts WHERE id='$data_id'");
		if(!isset($last_data->id)){
			$data_id = 0;
		}
	}
	
	$array = array();
			
	$array['valut_id'] = $valut_id = intval(is_param_post('valut_id'));
	$array['vidzn'] = 0;		
			
	$item = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."valuts WHERE valut_status = '1' AND id='$valut_id'");
	if(!isset($item->id)){		
		pn_display_mess(__('Error! Currency does not exist or disabled','pn'));	
	} else {		
		$account = pn_strip_input(is_param_post('accountnum'));	
		$array['accountnum'] = get_purse($account, $item);
		$array['vidzn'] = intval($item->vidzn);
	}
			
	$array['user_id'] = $user_id = intval(is_param_post('user_id'));
	$array['user_login'] = '';
	$ui = get_userdata($user_id);
	if(isset($ui->user_login)){
		$array['user_login'] = is_user($ui->user_login);
	}
			
	$array['verify'] = intval(is_param_post('verify'));

	$array = apply_filters('pn_userwallets_addform_post',$array, $last_data);
			
	if($data_id){		
		do_action('pn_userwallets_edit_before', $data_id, $array, $last_data);
		$result = $wpdb->update($wpdb->prefix.'user_accounts', $array, array('id'=>$data_id));
		if($result){
			do_action('pn_userwallets_edit', $data_id, $array, $last_data);	
		}	
	} else {
		$wpdb->insert($wpdb->prefix.'user_accounts', $array);
		$data_id = $wpdb->insert_id;	
		do_action('pn_userwallets_add', $data_id, $array);
	}

	$url = admin_url('admin.php?page=pn_add_userwallets&item_id='. $data_id .'&reply=true');
	wp_redirect($url);
	exit;
}	
/* end обработка формы */