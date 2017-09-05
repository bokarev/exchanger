<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_pn_add_discount', 'pn_adminpage_title_pn_add_discount');
function pn_adminpage_title_pn_add_discount(){
	$id = intval(is_param_get('item_id'));
	if($id){
		_e('Edit discount','pn');
	} else {
		_e('Add discount','pn');
	}
}

add_action('pn_adminpage_content_pn_add_discount','def_pn_adminpage_content_pn_add_discount');
function def_pn_adminpage_content_pn_add_discount(){
global $wpdb;

	$id = intval(is_param_get('item_id'));
	$data_id = 0;
	$data = '';
	
	if($id){
		$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."user_discounts WHERE id='$id'");
		if(isset($data->id)){
			$data_id = $data->id;
		}	
	}

	if($data_id){
		$title = __('Edit discount','pn');
	} else {
		$title = __('Add discount','pn');
	}
	
	$back_menu = array();
	$back_menu['back'] = array(
		'link' => admin_url('admin.php?page=pn_discount'),
		'title' => __('Back to list','pn')
	);
	if($data_id){
		$back_menu['add'] = array(
			'link' => admin_url('admin.php?page=pn_add_discount'),
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
	$options['sumec'] = array(
		'view' => 'input',
		'title' => __('Amount more than','pn'),
		'default' => is_isset($data, 'sumec'),
		'name' => 'sumec',
		'work' => 'input',
	);	
	$options['sumec_help'] = array(
		'view' => 'help',
		'title' => __('More info','pn'),
		'default' => __('The first level of the acumulative discount should always start with the value ">0"'),
	);	
	$options['discount'] = array(
		'view' => 'input',
		'title' => __('Discount (%)','pn'),
		'default' => is_isset($data, 'discount'),
		'name' => 'discount',
		'work' => 'input',
	);	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('pn_discount_addform', $options, $data);	
	
}

/* обработка формы */
add_action('premium_action_pn_add_discount','def_premium_action_pn_add_discount');
function def_premium_action_pn_add_discount(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_discount'));

		$data_id = intval(is_param_post('data_id')); 
		$last_data = '';
		if($data_id > 0){
			$last_data = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "user_discounts WHERE id='$data_id'");
			if(!isset($last_data->id)){
				$data_id = 0;
			}
		}		
		
		$array = array();
		$array['sumec'] = is_my_money(is_param_post('sumec'));
		$array['discount'] = is_my_money(is_param_post('discount'));

		$array = apply_filters('pn_discount_addform_post',$array, $last_data);
			
		if($data_id){
				
			do_action('pn_discount_edit_before', $data_id, $array, $last_data);
			$result = $wpdb->update($wpdb->prefix.'user_discounts', $array, array('id'=>$data_id));
			if($result){
				do_action('pn_discount_edit', $data_id, $array, $last_data);
			}	
		} else {

			$wpdb->insert($wpdb->prefix.'user_discounts', $array);
			$data_id = $wpdb->insert_id;	
			do_action('pn_discount_add', $data_id, $array);

		}

	$url = admin_url('admin.php?page=pn_add_discount&item_id='. $data_id .'&reply=true');
	wp_redirect($url);
	exit;
}	
/* end обработка формы */