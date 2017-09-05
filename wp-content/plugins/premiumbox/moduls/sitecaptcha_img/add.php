<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_pn_scimage_add_variants', 'pn_adminpage_title_pn_scimage_add_variants');
function pn_adminpage_title_pn_scimage_add_variants(){
	$id = intval(is_param_get('item_id'));
	if($id){
		_e('Edit option','pn');
	} else {
		_e('Add option','pn');
	}
}

add_action('pn_adminpage_content_pn_scimage_add_variants','def_pn_adminpage_content_pn_scimage_add_variants');
function def_pn_adminpage_content_pn_scimage_add_variants(){
global $wpdb;

	$id = intval(is_param_get('item_id'));
	$data_id = 0;
	$data = '';
	
	if($id){
		$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."sitecaptcha_images WHERE id='$id'");
		if(isset($data->id)){
			$data_id = $data->id;
		}	
	}

	if($data_id){
		$title = __('Edit option','pn');
	} else {
		$title = __('Add option','pn');
	}
	
	$back_menu = array();
	$back_menu['back'] = array(
		'link' => admin_url('admin.php?page=pn_scimage_variants'),
		'title' => __('Back to list','pn')
	);
	if($data_id){
		$back_menu['add'] = array(
			'link' => admin_url('admin.php?page=pn_scimage_add_variants'),
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
	$options['uslov'] = array(
		'view' => 'inputbig',
		'title' => __('Title','pn'),
		'default' => is_isset($data, 'uslov'),
		'name' => 'uslov',
		'ml' => 1,
	);		
	$options['img1'] = array(
		'view' => 'uploader',
		'title' => sprintf('%1s %2s', __('Image','pn'), '1'),
		'default' => is_isset($data, 'img1'),
		'name' => 'img1',
	);
	$options['img2'] = array(
		'view' => 'uploader',
		'title' => sprintf('%1s %2s', __('Image','pn'), '2'),
		'default' => is_isset($data, 'img2'),
		'name' => 'img2',
	);
	$options['img3'] = array(
		'view' => 'uploader',
		'title' => sprintf('%1s %2s', __('Image','pn'), '3'),
		'default' => is_isset($data, 'img3'),
		'name' => 'img3',
	);
	$options['variant'] = array(
		'view' => 'select',
		'title' => __('Right choice','pn'),
		'options' => array('1'=>'1','2'=>'2','3'=>'3'),
		'default' => is_isset($data, 'variant'),
		'name' => 'variant',
	);		
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('', $options, $data);				
}

add_action('premium_action_pn_scimage_add_variants','def_premium_action_pn_scimage_add_variants');
function def_premium_action_pn_scimage_add_variants(){
global $wpdb;

	only_post();
	pn_only_caps(array('administrator'));
		
	$data_id = intval(is_param_post('data_id'));
	$last_data = '';
	if($data_id > 0){
		$last_data = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "sitecaptcha_images WHERE id='$data_id'");
		if(!isset($last_data->id)){
			$data_id = 0;
		}
	}		
	
	$array = array();
	$array['uslov'] = pn_strip_input(is_param_post_ml('uslov'));
	$array['img1'] = pn_strip_input(is_param_post('img1'));
	$array['img2'] = pn_strip_input(is_param_post('img2'));
	$array['img3'] = pn_strip_input(is_param_post('img3'));
	$array['variant'] = intval(is_param_post('variant'));

	$array = apply_filters('pn_sci_addform_post',$array, $last_data);
	
	if($data_id){
		do_action('pn_sci_edit_before', $data_id, $array, $last_data);
		$result = $wpdb->update($wpdb->prefix.'sitecaptcha_images', $array, array('id'=>$data_id));
		if($result){
			do_action('pn_discount_edit', $data_id, $array, $last_data);
		}
	} else {
		$wpdb->insert($wpdb->prefix.'sitecaptcha_images', $array);
		$data_id = $wpdb->insert_id;
		do_action('pn_discount_add', $data_id, $array);
	}

	$url = admin_url('admin.php?page=pn_scimage_add_variants&item_id='. $data_id .'&reply=true');
	wp_redirect($url);
	exit;
}	