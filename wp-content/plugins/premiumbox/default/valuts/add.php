<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_pn_add_valuts', 'pn_admin_title_pn_add_valuts');
function pn_admin_title_pn_add_valuts(){
	$id = intval(is_param_get('item_id'));
	if($id){
		_e('Edit currency','pn');
	} else {
		_e('Add currency','pn');
	}
}

add_action('pn_adminpage_content_pn_add_valuts','def_pn_admin_content_pn_add_valuts');
function def_pn_admin_content_pn_add_valuts(){
global $wpdb;

	$id = intval(is_param_get('item_id'));
	$data_id = 0;
	$data = '';
	
	if($id){
		$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."valuts WHERE id='$id'");
		if(isset($data->id)){
			$data_id = $data->id;
		}	
	}

	if($data_id){
		$title = __('Edit currency','pn');
	} else {
		$title = __('Add currency','pn');
	}
	
	$psys = apply_filters('list_psys_manage', array(), __('No item','pn'));	

	$vtypes = apply_filters('list_vtypes_manage', array(), __('No item','pn'));
	
	$wchecks = array();
	$wchecks[0] = '--'. __('No item','pn') .'--';
	$list_wchecks = apply_filters('list_wchecks', array());
	$list_wchecks = (array)$list_wchecks;
	foreach($list_wchecks as $val){
		$wchecks[is_isset($val,'id')] = is_isset($val,'title');
	}
	
	$rplaced = array();
	$rplaced[0] = '--'. __('calculate according to orders','pn') .'--';
	$rplaced = apply_filters('reserv_place_list', $rplaced, 'currency');
	$rplaced = (array)$rplaced;
	
	$back_menu = array();
	$back_menu['back'] = array(
		'link' => admin_url('admin.php?page=pn_valuts'),
		'title' => __('Back to list','pn')
	);
	if($data_id){
		$back_menu['add'] = array(
			'link' => admin_url('admin.php?page=pn_add_valuts'),
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
	$options['psys_id'] = array(
		'view' => 'select',
		'title' => __('PS title','pn'),
		'options' => $psys,
		'default' => is_isset($data, 'psys_id'),
		'name' => 'psys_id',
	);	
	$options['vtype_id'] = array(
		'view' => 'select',
		'title' => __('Currency code','pn'),
		'options' => $vtypes,
		'default' => is_isset($data, 'vtype_id'),
		'name' => 'vtype_id',
	);	
	$pn_icon_size = apply_filters('pn_icon_size','50 x 50');
	$options['valut_logo'] = array(
		'view' => 'uploader',
		'title' => __('Logo','pn').' ('. $pn_icon_size .')',
		'default' => is_isset($data, 'valut_logo'),
		'name' => 'valut_logo',
	);	
	$options['line1'] = array(
		'view' => 'line',
		'colspan' => 2,
	);	
	$options['xml_value'] = array(
		'view' => 'input',
		'title' => __('XML name','pn'),
		'default' => is_isset($data, 'xml_value'),
		'name' => 'xml_value',
	);	
	$options['xml_value_help'] = array(
		'view' => 'help',
		'title' => __('More info','pn'),
		'default' => sprintf(__('Allowed symbols: a-z, A-Z, 0-9, min.: %1$s , max.: %2$s symbols','pn'), 3, 30),
	);
	$options['xml_value_warning'] = array(
		'view' => 'warning',
		'title' => __('More info','pn'),
		'default' => sprintf(__('Enter the name (according to the standard): <a href="%s">Estandards.info</a>.','pn'), 'http://estandards.info/formirovanie-eksportnogo-fajla-s-kursami/'),
	);	
	$options['line2'] = array(
		'view' => 'line',
		'colspan' => 2,
	);	
	$options['lead_num'] = array(
		'view' => 'input',
		'title' => __('Convert to','pn'),
		'default' => is_isset($data, 'lead_num'),
		'name' => 'lead_num',
	);
	$options['valut_decimal'] = array(
		'view' => 'input',
		'title' => __('Amount of Decimal places','pn'),
		'default' => is_isset($data, 'valut_decimal'),
		'name' => 'valut_decimal',
	);
	$options['line3'] = array(
		'view' => 'line',
		'colspan' => 2,
	);	
	$options['reserv_place'] = array(
		'view' => 'select',
		'title' => __('Currency reserve','pn'),
		'options' => $rplaced,
		'default' => is_isset($data, 'reserv_place'),
		'name' => 'reserv_place',
	);	
	$options['line4'] = array(
		'view' => 'line',
		'colspan' => 2,
	);
	$options['check_purse'] = array(
		'view' => 'select',
		'title' => __('Checking account for verification in PS','pn'),
		'options' => $wchecks,
		'default' => is_isset($data, 'check_purse'),
		'name' => 'check_purse',
	);
	$options['check_text'] = array(
		'view' => 'inputbig',
		'title' => __('Text indicating the verified wallet','pn'),
		'default' => is_isset($data, 'check_text'),
		'name' => 'check_text',
		'work' => 'input',
		'ml' => 1,
	);
	$options['line5'] = array(
		'view' => 'line',
		'colspan' => 2,
	);	
	$options['center_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);	
	$options['txt1'] = array(
		'view' => 'inputbig',
		'title' => __('Field title "From Account"','pn'),
		'default' => is_isset($data, 'txt1'),
		'name' => 'txt1',
		'ml' => 1,
	);
	$options['show1'] = array(
		'view' => 'select',
		'title' => __('Show field "From Account"','pn'),
		'options' => array('1'=>__('Yes','pn'),'0'=>__('No','pn')),
		'default' => is_isset($data, 'show1'),
		'name' => 'show1',
	);	
	$options['txt2'] = array(
		'view' => 'inputbig',
		'title' => __('Field title "Onto Account"','pn'),
		'default' => is_isset($data, 'txt2'),
		'name' => 'txt2',
		'ml' => 1,
	);
	$options['show2'] = array(
		'view' => 'select',
		'title' => __('Show filed "Onto Account"','pn'),
		'options' => array('1'=>__('Yes','pn'),'0'=>__('No','pn')),
		'default' => is_isset($data, 'show2'),
		'name' => 'show2',
	);	
	$options['line6'] = array(
		'view' => 'line',
		'colspan' => 2,
	);	
	$options['helps'] = array(
		'view' => 'textarea',
		'title' => __('Tip for field "From Account"','pn'),
		'default' => is_isset($data, 'helps'),
		'name' => 'helps',
		'width' => '',
		'height' => '100px',
		'ml' => 1,
	);
	$options['helps2'] = array(
		'view' => 'textarea',
		'title' => __('Tip for field "Onto Account"','pn'),
		'default' => is_isset($data, 'helps2'),
		'name' => 'helps2',
		'width' => '',
		'height' => '100px',
		'ml' => 1,
	);
	$options['line7'] = array(
		'view' => 'line',
		'colspan' => 2,
	);	
	$options['center_title2'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);	
	$options['minzn'] = array(
		'view' => 'input',
		'title' => __('Min. number of symbols','pn'),
		'default' => is_isset($data, 'minzn'),
		'name' => 'minzn',
	);
	$options['maxzn'] = array(
		'view' => 'input',
		'title' => __('Max. number of symbols','pn'),
		'default' => is_isset($data, 'maxzn'),
		'name' => 'maxzn',
	);
	$options['firstzn'] = array(
		'view' => 'input',
		'title' => __('First symbols','pn'),
		'default' => is_isset($data, 'firstzn'),
		'name' => 'firstzn',
	);
	$options['firstzn_help'] = array(
		'view' => 'help',
		'title' => __('More info','pn'),
		'default' => __('Checking the first symbols when client enters own account. For example, the first symbol of WebMoney Z wallet is set as Z.','pn'),
	);	
	$options['cifrzn'] = array(
		'view' => 'select',
		'title' => __('Allowed symbols','pn'),
		'options' => array('0'=>__('Numbers and letters','pn'),'1'=>__('Numbers','pn'),'2'=>__('Letters','pn'),'3'=>__('E-mail','pn'),'4'=>__('Any symbols','pn'),'5'=>__('Phone number','pn')),
		'default' => is_isset($data, 'cifrzn'),
		'name' => 'cifrzn',
	);
	$options['vidzn'] = array(
		'view' => 'select',
		'title' => __('Account type','pn'),
		'options' => array('0'=>__('Account','pn'),'1'=>__('Bank card','pn'),'2'=>__('Phone number','pn')),
		'default' => is_isset($data, 'vidzn'),
		'name' => 'vidzn',
	);	
	$options['line8'] = array(
		'view' => 'line',
		'colspan' => 2,
	);
	$options['valut_status'] = array(
		'view' => 'select',
		'title' => __('Status','pn'),
		'options' => array('1'=>__('Active currency','pn'),'0'=>__('Inactive currency','pn')),
		'default' => is_isset($data, 'valut_status'),
		'name' => 'valut_status',
	);	
	$options['valut_warning'] = array(
		'view' => 'warning',
		'title' => __('More info','pn'),
		'default' => sprintf(__('Caution! After adding currency it is necessary to set <a href="%s">reserve</a>.','pn'), admin_url('admin.php?page=pn_reserv')),
	);	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('pn_valuts_addform', $options, $data);	
} 

/* обработка формы */
add_action('premium_action_pn_add_valuts','def_premium_action_pn_add_valuts');
function def_premium_action_pn_add_valuts(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_valuts'));

	$data_id = intval(is_param_post('data_id')); 
	$last_data = '';
	if($data_id > 0){
		$last_data = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "valuts WHERE id='$data_id'");
		if(!isset($last_data->id)){
			$data_id = 0;
		}
	}	
	
	$array = array();
			
	$array['valut_decimal'] = intval(is_param_post('valut_decimal'));
	if($array['valut_decimal'] < 0){ $array['valut_decimal'] = 2; }
			
	$lead_num = intval(is_param_post('lead_num')); if($lead_num < 1){ $lead_num = 0; }
	$array['lead_num'] = $lead_num;						
			
	$array['reserv_place'] = is_extension_name(is_param_post('reserv_place'));

	$array['valut_status'] = intval(is_param_post('valut_status'));
			
	$array['vtype_id'] = 0;
	$array['vtype_title'] = '';
			
	$vtype_id = intval(is_param_post('vtype_id'));
	if($vtype_id){
		$vtype_data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."vtypes WHERE id='$vtype_id'");
		if(isset($vtype_data->id)){
			$array['vtype_id'] = $vtype_data->id;
			$array['vtype_title'] = is_site_value($vtype_data->vtype_title);
		}
	} 
			
	$array['valut_logo'] = esc_url(is_param_post('valut_logo'));
	$array['psys_id'] = 0;
	$array['psys_title'] = '';
	$array['psys_logo'] = '';
			
	$psys_id = intval(is_param_post('psys_id'));
	if($psys_id){
		$psys_data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."psys WHERE id='$psys_id'");
		if(isset($psys_data->id)){
			$array['psys_id'] = $psys_data->id;
			$array['psys_title'] = pn_strip_input($psys_data->psys_title);
			$array['psys_logo'] = esc_url($psys_data->psys_logo); 
		}
	} 

	$xml_value = is_xml_value(is_param_post('xml_value'));
	if(!$xml_value){
		$xml_value = delsimbol(replace_cyr(ctv_ml($array['psys_title'])), 0);
		$xml_value = unique_xml_value($xml_value, $data_id);
	}
			
	$array['xml_value'] = $xml_value;
			
	$array['helps'] = pn_strip_input(is_param_post_ml('helps'));
	$array['helps2'] = pn_strip_input(is_param_post_ml('helps2'));
	$array['show1'] = intval(is_param_post('show1'));
	$array['show2'] = intval(is_param_post('show2'));
	$array['txt1'] = pn_strip_input(is_param_post_ml('txt1'));
	$array['txt2'] = pn_strip_input(is_param_post_ml('txt2'));
	$array['check_text'] = pn_strip_input(is_param_post_ml('check_text'));
	$array['check_purse'] = is_extension_name(is_param_post('check_purse'));
			
	$array['minzn'] = intval(is_param_post('minzn'));
	$array['maxzn'] = intval(is_param_post('maxzn'));
	$array['firstzn'] = is_firstzn_value(is_param_post('firstzn'));
	$array['cifrzn'] = intval(is_param_post('cifrzn'));
	$array['vidzn'] = intval(is_param_post('vidzn'));

	$array = apply_filters('pn_valuts_addform_post',$array, $last_data);		
			
	if($data_id){	
		do_action('pn_valuts_edit_before', $data_id, $array, $last_data);
		$wpdb->update($wpdb->prefix.'valuts', $array, array('id'=>$data_id));
		do_action('pn_valuts_edit', $data_id, $array, $last_data);	
	} else {
		$wpdb->insert($wpdb->prefix.'valuts', $array);
		$data_id = $wpdb->insert_id;	
		do_action('pn_valuts_add', $data_id, $array);	
	}

	$url = admin_url('admin.php?page=pn_add_valuts&item_id='. $data_id .'&reply=true');
	wp_redirect($url);
	exit;
}	
/* end обработка формы */