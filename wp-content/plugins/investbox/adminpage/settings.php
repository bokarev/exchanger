<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_inex_settings', 'adminpage_title_inex_settings');
function adminpage_title_inex_settings($page){
	_e('Settings','inex');
} 

/* настройки */
add_action('pn_adminpage_content_inex_settings','def_adminpage_content_inex_settings');
function def_adminpage_content_inex_settings(){
global $wpdb, $investbox;

	$options = array();
	$options['top_title'] = array(
		'view' => 'h3',
		'title' => __('Settings','inex'),
		'submit' => __('Save','inex'),
		'colspan' => 2,
	);
	$options['long'] = array(
		'view' => 'select',
		'title' => __('Allow to prolong the deposit on the same terms and conditions','inex'),
		'options' => array('false'=> __('No','inex'), 'true'=> __('Yes','inex')),
		'default' => $investbox->get_option('change', 'long'),
		'name' => 'long',
	);	
	$options['style'] = array(
		'view' => 'select',
		'title' => __('Disable plugin styles','inex'),
		'options' => array('0'=> __('No','inex'), '1'=> __('Yes','inex')),
		'default' => $investbox->get_option('change', 'style'),
		'name' => 'style',
	);	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','inex'),
		'colspan' => 2,
	);
	pn_admin_one_screen('', $options);
	
} 

/* обработка */
add_action('premium_action_inex_settings','def_premium_action_inex_settings');
function def_premium_action_inex_settings(){
global $wpdb, $investbox;	

	only_post();
	pn_only_caps(array('administrator'));
	
	$options = array('long','style');	
	foreach($options as $key){
		$investbox->update_option('change', $key, pn_strip_input(is_param_post($key)));
	}
	
	$back_url = is_param_post('_wp_http_referer');
	$back_url .= '&reply=true';
			
	wp_safe_redirect($back_url);
	exit;
}