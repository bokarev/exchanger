<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_pn_add_reserv', 'pn_admin_title_pn_add_reserv');
function pn_admin_title_pn_add_reserv(){
	$id = intval(is_param_get('item_id'));
	if($id){
		_e('Edit reserve transaction','pn');
	} else {
		_e('Add reserve transaction','pn');
	}
}

add_action('pn_adminpage_content_pn_add_reserv','def_pn_admin_content_pn_add_reserv');
function def_pn_admin_content_pn_add_reserv(){
global $wpdb;

	$id = intval(is_param_get('item_id'));
	$data_id = 0;
	$data = '';
	
	if($id){
		$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."trans_reserv WHERE id='$id'");
		if(isset($data->id)){
			$data_id = $data->id;
		}	
	}

	if($data_id){
		$title = __('Edit reserve transaction','pn');
	} else {
		$title = __('Add reserve transaction','pn');
	}
	
	$valuts = apply_filters('list_valuts_manage', array(), __('No item','pn'));	
	
	$back_menu = array();
	$back_menu['back'] = array(
		'link' => admin_url('admin.php?page=pn_reserv'),
		'title' => __('Back to list','pn')
	);
	if($data_id){
		$back_menu['add'] = array(
			'link' => admin_url('admin.php?page=pn_add_reserv'),
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
	$options['trans_title'] = array(
		'view' => 'inputbig',
		'title' => __('Comment','pn'),
		'default' => is_isset($data, 'trans_title'),
		'name' => 'trans_title',
	);
	$options['trans_summ'] = array(
		'view' => 'inputbig',
		'title' => __('Amount','pn'),
		'default' => is_isset($data, 'trans_summ'),
		'name' => 'trans_summ',
	);
	$options['valut_id'] = array(
		'view' => 'select',
		'title' => __('Currency name','pn'),
		'options' => $valuts,
		'default' => is_isset($data, 'valut_id'),
		'name' => 'valut_id',
	);
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('pn_reserv_addform', $options, $data);	
} 

/* обработка формы */
add_action('premium_action_pn_add_reserv','def_premium_action_pn_add_reserv');
function def_premium_action_pn_add_reserv(){
global $wpdb, $user_ID;	

	only_post();
	pn_only_caps(array('administrator','pn_reserv'));

	$data_id = intval(is_param_post('data_id')); 
	$last_data = '';
	if($data_id > 0){
		$last_data = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "trans_reserv WHERE id='$data_id'");
		if(!isset($last_data->id)){
			$data_id = 0;
		}
	}	
	
	$array = array();
			
	$array['trans_title'] = pn_strip_input(is_param_post('trans_title'));
	$array['trans_summ'] = is_my_money(is_param_post('trans_summ'));

	$array['valut_id'] = 0;
	$array['vtype_id'] = 0;
	$array['vtype_title'] = '';
			
	$valut_id = intval(is_param_post('valut_id'));
	if($valut_id){
		$valut_data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."valuts WHERE id='$valut_id'");
		if(isset($valut_data->id)){
			$array['valut_id'] = $valut_data->id;
			$array['vtype_id'] = $valut_data->vtype_id;
			$array['vtype_title'] = is_site_value($valut_data->vtype_title);	
		}	
	} 

	$array = apply_filters('pn_reserv_addform_post',$array, $last_data);
			
	if($data_id){
						
		$array['trans_edit'] = current_time('mysql');
		$array['user_editor'] = intval($user_ID);

		do_action('pn_reserv_edit_before', $data_id, $array, $last_data);
				
		$result = $wpdb->update($wpdb->prefix.'trans_reserv', $array, array('id'=>$data_id));
		if($result){	
			$update = 1;
					
			if(isset($last_data->valut_id)){
				update_valut_reserv($last_data->valut_id);
						
				if($last_data->valut_id == $array['valut_id']){
					$update = 0;
				}
			}		
					
			if($update == 1){
				update_valut_reserv($array['valut_id']);
			}
		
			do_action('pn_reserv_edit', $data_id, $array, $last_data);
		}
				
	} else {
				
		$array['trans_create'] = current_time('mysql');
		$array['user_creator'] = intval($user_ID);
				
		$wpdb->insert($wpdb->prefix.'trans_reserv', $array);
		$data_id = $wpdb->insert_id;	
				
		update_valut_reserv($array['valut_id']);
				
		do_action('pn_reserv_add', $data_id, $array);
				
	}

	$url = admin_url('admin.php?page=pn_add_reserv&item_id='. $data_id .'&reply=true');
	wp_redirect($url);
	exit;
}	
/* end обработка формы */