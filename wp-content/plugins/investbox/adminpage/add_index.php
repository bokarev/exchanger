<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_inex_add_index', 'adminpage_title_inex_add_index');
function adminpage_title_inex_add_index(){
	$id = intval(is_param_get('item_id'));
	if($id){
		_e('Edit deposit','inex');
	} else {
		_e('Add deposit','inex');
	}
}


add_action('pn_adminpage_content_inex_add_index','def_adminpage_content_inex_add_index');
function def_adminpage_content_inex_add_index(){
global $wpdb, $investbox;

	$id = intval(is_param_get('item_id'));
	$data_id = 0;
	$data = '';
	
	if($id){
		$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."inex_deposit WHERE id='$id'");
		if(isset($data->id)){
			$data_id = $data->id;
		}	
	}

	if($data_id){
		$title = __('Edit deposit','inex');
	} else {
		$title = __('Add deposit','inex');
	}
	

	$users = array();
	$usarr = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."users ORDER BY user_login ASC");
	foreach($usarr as $usa){
		$users[$usa->ID] = pn_strip_input($usa->user_login);
	}

	$gids = array();
	$gids[0] = '-- '. __('No item','inex') .' --';
	$systems = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."inex_system ORDER BY title ASC, valut ASC");
	foreach($systems as $sys){
		if($investbox->check_ps($sys->gid)){
			$gids[$sys->gid] = pn_strip_input($sys->title .' '. $sys->valut);
		}
	}	
	
	$back_menu = array();
	$back_menu['back'] = array(
		'link' => admin_url('admin.php?page=inex_index'),
		'title' => __('Back to list','inex')
	);
	if($data_id){
		$back_menu['add'] = array(
			'link' => admin_url('admin.php?page=inex_add_index'),
			'title' => __('Add new','inex')
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
		'submit' => __('Save','inex'),
		'colspan' => 2,
	);	
	$options['indate'] = array(
		'view' => 'datetime',
		'title' => __('Start date','inex'),
		'default' => is_isset($data, 'indate'),
		'name' => 'indate',
	);
	$options['couday'] = array(
		'view' => 'input',
		'title' => __('Number of days','inex'),
		'default' => is_isset($data, 'couday'),
		'name' => 'couday',
	);
	$options['pers'] = array(
		'view' => 'input',
		'title' => __('Percent','inex'),
		'default' => is_isset($data, 'pers'),
		'name' => 'pers',
	);	
	$options['insumm'] = array(
		'view' => 'input',
		'title' => __('Amount','inex'),
		'default' => is_isset($data, 'insumm'),
		'name' => 'insumm',
	);	
	$options['user_id'] = array(
		'view' => 'select',
		'title' => __('User','inex'),
		'options' => $users,
		'default' => is_isset($data, 'user_id'),
		'name' => 'user_id',
	);
	$options['user_schet'] = array(
		'view' => 'inputbig',
		'title' => __('Account','inex'),
		'default' => is_isset($data, 'user_schet'),
		'name' => 'user_schet',
	);
	$options['gid'] = array(
		'view' => 'select',
		'title' => __('PS','inex'),
		'options' => $gids,
		'default' => is_isset($data, 'gid'),
		'name' => 'gid',
	);
	$options['paystatus'] = array(
		'view' => 'select',
		'title' => __('Payment status','inex'),
		'options' => array('0'=>__('Is not paid by user','inex'), '1'=>__('Paid by user','inex')), //, '2'=>__('Listed as paid','inex')
		'default' => is_isset($data, 'paystatus'),
		'name' => 'paystatus',
	);
	$options['zakstatus'] = array(
		'view' => 'select',
		'title' => __('Payout order status','inex'),
		'options' => array('0'=>__('Is not ordered','inex'), '1'=>__('Ordered','inex')),
		'default' => is_isset($data, 'zakstatus'),
		'name' => 'zakstatus',
	);
	$options['vipstatus'] = array(
		'view' => 'select',
		'title' => __('Payout status','inex'),
		'options' => array('0'=>__('Is not paid','inex'), '1'=>__('Paid','inex')),
		'default' => is_isset($data, 'vipstatus'),
		'name' => 'vipstatus',
	);	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','inex'),
		'colspan' => 2,
	);
	pn_admin_one_screen('', $options, $data);	
} 

/* обработка формы */
add_action('premium_action_inex_add_index','def_myaction_post_inex_add_index');
function def_myaction_post_inex_add_index(){
global $wpdb, $investbox;	

	only_post();
	pn_only_caps(array('administrator','pn_investbox'));

	$data_id = intval(is_param_post('data_id'));
	$array = array();

		if(is_param_post('indate')){
			$indate = get_mytime(is_param_post('indate'), 'Y-m-d H:i:s');
		} else {
			$indate = current_time('mysql');
		}
		$array['indate'] = pn_strip_input($indate);
		$array['couday'] = $couday = intval(is_param_post('couday'));
		$array['pers'] = $pers = is_my_money(is_param_post('pers'),2);
		$array['insumm'] = $insumm = is_my_money(is_param_post('insumm'),2);
		$user_id = intval(is_param_post('user_id'));
		if($user_id){
			$ui = get_userdata($user_id);
			if(isset($ui->user_login)){
				$array['user_id'] = $user_id;
				$array['user_login'] = pn_strip_input($ui->user_login);
				$array['user_email'] = pn_strip_input($ui->user_email);
			}
		}
		$array['user_schet'] = pn_strip_input(is_param_post('user_schet'));

		$array['paystatus'] = intval(is_param_post('paystatus'));
		$array['zakstatus'] = intval(is_param_post('zakstatus'));
		$array['vipstatus'] = intval(is_param_post('vipstatus'));

		$array['gid'] = $gid = $investbox->is_system_name(is_param_post('gid'));
		$gdata = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."inex_system WHERE gid='$gid'");
		if(isset($gdata->id)){
			$array['gtitle'] = pn_strip_input($gdata->title);
			$array['gvalut'] = pn_strip_input($gdata->valut);
		}
		$endtime = strtotime($indate) + ($couday * 24 * 60 * 60);
		$enddate = date('Y-m-d H:i:s', $endtime);
		$array['enddate'] = $enddate;

		if($insumm > 0){
			$plussumm = $insumm / 100 * $pers;
			$outsumm = $insumm + $plussumm;
		} else {
			$plussumm = 0;
			$outsumm = 0;	
		}

		$array['plussumm'] = $plussumm;
		$array['outsumm'] = $outsumm;
		$array['locale'] = get_locale();

		if($data_id){	
			$wpdb->update($wpdb->prefix.'inex_deposit', $array, array('id'=>$data_id));	
		} else {
			$array['createdate'] = current_time('mysql');
			$wpdb->insert($wpdb->prefix.'inex_deposit', $array);
			$data_id = $wpdb->insert_id;	
		}							
			

	$url = admin_url('admin.php?page=inex_add_index&item_id='. $data_id .'&reply=true');
	wp_redirect($url);
	exit;
} 	
/* end обработка формы */