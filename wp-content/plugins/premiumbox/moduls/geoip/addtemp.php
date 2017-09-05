<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** добавить отзыв ************************************************/

add_action('pn_adminpage_title_pn_geoip_addtemp', 'pn_admin_title_pn_geoip_addtemp');
function pn_admin_title_pn_geoip_addtemp(){
	$id = intval(is_param_get('item_id'));
	if($id){
		_e('Edit template','pn');
	} else {
		_e('Add template','pn');
	}
}
 
add_action('pn_adminpage_content_pn_geoip_addtemp','def_pn_admin_content_pn_geoip_addtemp');
function def_pn_admin_content_pn_geoip_addtemp(){
global $wpdb;

	$id = intval(is_param_get('item_id'));
	$data_id = 0;
	$data = '';
	
	if($id){
		$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."geoip_template WHERE id='$id'");
		if(isset($data->id)){
			$data_id = $data->id;
		}	
	}

	if($data_id){
		$title = __('Edit template','pn');
	} else {
		$title = __('Add template','pn');
	}
	
	$back_menu = array();
	$back_menu['back'] = array(
		'link' => admin_url('admin.php?page=pn_geoip_temp'),
		'title' => __('Back to list','pn')
	);
	if($data_id){
		$back_menu['add'] = array(
			'link' => admin_url('admin.php?page=pn_geoip_addtemp'),
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
	$options['temptitle'] = array(
		'view' => 'inputbig',
		'title' => __('Template name','pn'),
		'default' => is_isset($data, 'temptitle'),
		'name' => 'temptitle',
	);	
	$options['line1'] = array(
		'view' => 'line',
		'colspan' => 2,
	);
	$options['title'] = array(
		'view' => 'inputbig',
		'title' => __('Title','pn'),
		'default' => is_isset($data, 'title'),
		'name' => 'title',
	);	
	$options['content'] = array(
		'view' => 'textarea',
		'title' => __('Text','pn'),
		'default' => is_isset($data, 'content'),
		'name' => 'content',
		'width' => '',
		'height' => '150px',
	);
	$options['default_temp'] = array(
		'view' => 'select',
		'title' => __('Type','pn'),
		'options' => array('0'=>__('For selection','pn'),'1'=>__('Default template','pn')),
		'default' => is_isset($data, 'default_temp'),
		'name' => 'default_temp',
	);		
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('pn_geoip_template_addform', $options, $data);	
} 

/* обработка формы */
add_action('premium_action_pn_geoip_addtemp','def_premium_action_pn_geoip_addtemp');
function def_premium_action_pn_geoip_addtemp(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_geoip'));
	
	$data_id = intval(is_param_post('data_id'));
	$array = array();
	$array['temptitle'] = pn_strip_input(is_param_post('temptitle'));
	$array['title'] = pn_strip_input(is_param_post('title'));
	$array['content'] = pn_strip_text(is_param_post('content'));
	$array['default_temp'] = intval(is_param_post('default_temp'));
			
	if($array['default_temp'] == 1){
		$wpdb->update($wpdb->prefix.'geoip_template', array('default_temp'=>0), array('default_temp'=>1));
	}			
			
	$array = apply_filters('pn_geoip_template_addform_post',$array);
			
	if($data_id){
		do_action('pn_geoip_template_edit_before', $data_id, $array);
		$result = $wpdb->update($wpdb->prefix.'geoip_template', $array, array('id'=>$data_id));
		if($result){
			do_action('pn_geoip_template_edit', $data_id, $array);
		}
	} else {
		$wpdb->insert($wpdb->prefix.'geoip_template', $array);
		$data_id = $wpdb->insert_id;
		do_action('pn_geoip_template_add', $data_id, $array);	
	}

	$url = admin_url('admin.php?page=pn_geoip_addtemp&item_id='. $data_id .'&reply=true');
	wp_redirect($url);
	exit;
}	
/* end обработка формы */