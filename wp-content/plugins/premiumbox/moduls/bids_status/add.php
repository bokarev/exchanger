<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_pn_add_bidstatus', 'pn_admin_title_pn_add_bidstatus');
function pn_admin_title_pn_add_bidstatus(){
	$id = intval(is_param_get('item_id'));
	if($id){
		_e('Edit order status','pn');
	} else {
		_e('Add order status','pn');
	}
}

add_action('pn_adminpage_content_pn_add_bidstatus','def_pn_admin_content_pn_add_bidstatus');
function def_pn_admin_content_pn_add_bidstatus(){
global $wpdb;

	$id = intval(is_param_get('item_id'));
	$data_id = 0;
	$data = '';
	if($id){
		$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."bidstatus WHERE id='$id'");
		if(isset($data->id)){
			$data_id = $data->id;
		}	
	}
	if($data_id){
		$title = __('Edit order status','pn');
	} else {
		$title = __('Add order status','pn');
	}
	
	$back_menu = array();
	$back_menu['back'] = array(
		'link' => admin_url('admin.php?page=pn_bidstatus'),
		'title' => __('Back to list','pn')
	);
	if($data_id){
		$back_menu['add'] = array(
			'link' => admin_url('admin.php?page=pn_add_bidstatus'),
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
		'title' => __('Displayed name','pn'),
		'default' => is_isset($data, 'title'),
		'name' => 'title',
		'work' => 'input',
		'ml' => 1,
	);			

	$colors_for_bidstatus = apply_filters('colors_for_bidstatus', array());
	$set_colors = array();
	foreach($colors_for_bidstatus as $cd_key => $cd){
		$set_colors[$cd_key] = is_isset($cd,'title');
	}
	
	$options['bg_color'] = array(
		'view' => 'select',
		'title' => __('Color','pn'),
		'options' => $set_colors,
		'default' => is_isset($data, 'bg_color'),
		'name' => 'bg_color',
	);				
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('pn_bidstatus_addform', $options, $data);																		
} 

/* обработка формы */
add_action('premium_action_pn_add_bidstatus','def_premium_action_pn_add_bidstatus');
function def_premium_action_pn_add_bidstatus(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_bidstatus'));

	$data_id = intval(is_param_post('data_id'));
	$last_data = '';
	if($data_id > 0){
		$last_data = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "bidstatus WHERE id='$data_id'");
		if(!isset($last_data->id)){
			$data_id = 0;
		}
	}	
	
	$array = array();
	$array['title'] = pn_strip_input(is_param_post_ml('title'));
	$array['bg_color'] = intval(is_param_post('bg_color'));

	$array = apply_filters('pn_bidstatus_addform_post',$array, $last_data);
	
	if($data_id){		
		do_action('pn_bidstatus_edit_before', $data_id, $array, $last_data);
		$result = $wpdb->update($wpdb->prefix.'bidstatus', $array, array('id'=>$data_id));
		if($result){
			do_action('pn_bidstatus_edit', $data_id, $array, $last_data);
		}		
	} else {		
		$wpdb->insert($wpdb->prefix.'bidstatus', $array);
		$data_id = $wpdb->insert_id;
		do_action('pn_bidstatus_add', $data_id, $array);
	}

	$url = admin_url('admin.php?page=pn_add_bidstatus&item_id='. $data_id .'&reply=true');
	wp_redirect($url);
	exit;
}
/* end обработка формы */