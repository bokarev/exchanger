<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_pn_data_smsgate', 'pn_admin_title_pn_data_smsgate');
function pn_admin_title_pn_data_smsgate(){
	_e('SMS gate settings','pn');
}

add_action('pn_adminpage_content_pn_data_smsgate','def_pn_admin_content_pn_data_smsgate');
function def_pn_admin_content_pn_data_smsgate(){
	
	$options = array();
	$options['top_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);	
	
	$data = get_option('smsgatedata');
	
	$options['merch'] = array(
		'view' => 'select',
		'title' => __('Send SMS when paying through merchant','pn'),
		'options' => array('0'=>__('No','pn'), '1'=>__('Yes','pn')),
		'default' => is_isset($data, 'merch'),
		'name' => 'merch',
	);
	$options['manual'] = array(
		'view' => 'select',
		'title' => __('Send SMS when notification of payment is manual','pn'),
		'options' => array('0'=>__('No','pn'), '1'=>__('Yes','pn')),
		'default' => is_isset($data, 'manual'),
		'name' => 'manual',
	);					
	$tags = array(
		'id' => __('ID Order','pn'),
	);
	$options['text1'] = array(
		'view' => 'textareatags',
		'title' => __('SMS message','pn'),
		'default' => is_isset($data, 'text1'),
		'tags' => $tags,
		'width' => '',
		'height' => '40px',
		'prefix1' => '[',
		'prefix2' => ']',
		'name' => 'text1',
		'ml' => 1,
	);	
	$options['line1'] = array(
		'view' => 'line',
		'colspan' => 2,
	);
	$options['autopay'] = array(
		'view' => 'select',
		'title' => __('Send SMS when automatic payment is done','pn'),
		'options' => array('0'=>__('No','pn'), '1'=>__('Yes','pn')),
		'default' => is_isset($data, 'autopay'),
		'name' => 'autopay',
	);	
	$tags = array(
		'id' => __('ID Order','pn'),
	);
	$options['text2'] = array(
		'view' => 'textareatags',
		'title' => __('SMS message','pn'),
		'default' => is_isset($data, 'text2'),
		'tags' => $tags,
		'width' => '',
		'height' => '40px',
		'prefix1' => '[',
		'prefix2' => ']',
		'name' => 'text2',
		'ml' => 1,
	);	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('pn_data_smsgate', $options, $data);

	$options = array();
	$options['top_title'] = array(
		'view' => 'h3',
		'title' => __('Test SMS sending','pn'),
		'submit' => __('Send a message','pn'),
		'colspan' => 2,
	);
	$options['to'] = array(
		'view' => 'inputbig',
		'title' => __('Phone number','pn'),
		'default' => '',
		'name' => 'to',
		'work' => 'email',
		'not_auto' => 1,
	);		
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Send a message','pn'),
		'colspan' => 2,
	);			
	pn_admin_one_screen('', $options, '', pn_link_post('pn_smsgate_send'));	
} 

/* обработка */
add_action('premium_action_pn_smsgate_send','def_premium_action_pn_smsgate_send');
function def_premium_action_pn_smsgate_send(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_merchants'));

		$to = is_phone(is_param_post('to'));
		if(!$to){
			pn_display_mess(__('Error! You have not entered a phone number','pn'));
		} else {
			do_action('pn_send_sms', 'Test SMS');			
		}

	$back_url = is_param_post('_wp_http_referer');
	$back_url .= '&reply=true';
			
	wp_safe_redirect($back_url);
	exit;
} 

add_action('premium_action_pn_data_smsgate','def_premium_action_pn_data_smsgate');
function def_premium_action_pn_data_smsgate(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_merchants'));

	$data = array();
				
	$data['merch'] = intval(is_param_post('merch'));
	$data['manual'] = intval(is_param_post('manual'));
	$data['autopay'] = intval(is_param_post('autopay'));
	$data['text1'] = pn_strip_input(is_param_post_ml('text1'));
	$data['text2'] = pn_strip_input(is_param_post_ml('text2'));
				
	update_option('smsgatedata', $data);
				
	$url = admin_url('admin.php?page=pn_data_smsgate&reply=true');
	wp_redirect($url);
	exit;
}