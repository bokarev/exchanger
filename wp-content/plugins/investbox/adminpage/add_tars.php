<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_inex_add_tars', 'adminpage_title_inex_add_tars');
function adminpage_title_inex_add_tars(){
	$id = intval(is_param_get('item_id'));
	if($id){
		_e('Edit rate','inex');
	} else {
		_e('Add rate','inex');
	}
}


add_action('pn_adminpage_content_inex_add_tars','def_adminpage_content_inex_add_tars');
function def_adminpage_content_inex_add_tars(){
global $wpdb, $investbox;

	$id = intval(is_param_get('item_id'));
	$data_id = 0;
	$data = '';
	
	if($id){
		$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."inex_tars WHERE id='$id'");
		if(isset($data->id)){
			$data_id = $data->id;
		}	
	}

	if($data_id){
		$title = __('Edit rate','inex');
	} else {
		$title = __('Add rate','inex');
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
		'link' => admin_url('admin.php?page=inex_tars'),
		'title' => __('Back to list','inex')
	);
	if($data_id){
		$back_menu['add'] = array(
			'link' => admin_url('admin.php?page=inex_add_tars'),
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
	$options['title'] = array(
		'view' => 'inputbig',
		'title' => __('Rate name','inex'),
		'default' => is_isset($data, 'title'),
		'name' => 'title',
		'ml' => 1,
	);
	$options['minsum'] = array(
		'view' => 'input',
		'title' => __('Minimum amount','inex'),
		'default' => is_isset($data, 'minsum'),
		'name' => 'minsum',
	);
	$options['maxsum'] = array(
		'view' => 'input',
		'title' => __('Maximum amount','inex'),
		'default' => is_isset($data, 'maxsum'),
		'name' => 'maxsum',
	);
	// $options['maxsumtar'] = array(
		// 'view' => 'input',
		// 'title' => __('Maximum amount in tarif','inex'),
		// 'default' => is_isset($data, 'maxsumtar'),
		// 'name' => 'maxsumtar',
	// );	
	$options['gid'] = array(
		'view' => 'select',
		'title' => __('PS','inex'),
		'options' => $gids,
		'default' => is_isset($data, 'gid'),
		'name' => 'gid',
	);
	$options['mpers'] = array(
		'view' => 'input',
		'title' => __('Percent amount over period','inex').' (%)',
		'default' => is_isset($data, 'mpers'),
		'name' => 'mpers',
	);
	$options['cdays'] = array(
		'view' => 'input',
		'title' => __('Number of days','inex'),
		'default' => is_isset($data, 'cdays'),
		'name' => 'cdays',
	);
	$options['status'] = array(
		'view' => 'select',
		'title' => __('Status','pn'),
		'options' => array('1'=>__('Active','inex'),'0'=>__('Inactive','inex')),
		'default' => is_isset($data, 'status'),
		'name' => 'status',
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
add_action('premium_action_inex_add_tars','def_myaction_post_inex_add_tars');
function def_myaction_post_inex_add_tars(){
global $wpdb, $investbox;	

	only_post();
	pn_only_caps(array('administrator','pn_investbox'));

	$data_id = intval(is_param_post('data_id'));
	$array = array();
			
	$array['title'] = pn_strip_input(is_param_post_ml('title'));
	$array['minsum'] = intval(is_param_post('minsum')); if($array['minsum'] < 0){ $array['minsum'] = 0; }
	$array['maxsum'] = is_my_money(is_param_post('maxsum'),2); if($array['maxsum'] < 0 or $array['maxsum'] <= $array['minsum']){ $array['maxsum'] = 0; }
	//$array['maxsumtar'] = is_my_money(is_param_post('maxsumtar'),2);
	$array['mpers'] = is_my_money(is_param_post('mpers'),2);
	$array['cdays'] = intval(is_param_post('cdays'));
	$array['status'] = intval(is_param_post('status'));
	$array['gid'] = $gid = $investbox->is_system_name(is_param_post('gid'));
	$gdata = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."inex_system WHERE gid='$gid'");
	if(isset($gdata->title)){
		$array['gtitle'] = pn_strip_input($gdata->title);
		$array['gvalut'] = pn_strip_input($gdata->valut);
	}
			
	if($data_id){
		$wpdb->update($wpdb->prefix.'inex_tars', $array, array('id'=>$data_id));
	} else {
		$wpdb->insert($wpdb->prefix.'inex_tars', $array);
		$data_id = $wpdb->insert_id;
	}			

	$url = admin_url('admin.php?page=inex_add_tars&item_id='. $data_id .'&reply=true');
	wp_redirect($url);
	exit;
} 	
/* end обработка формы */