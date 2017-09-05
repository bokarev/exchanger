<?php
if( !defined( 'ABSPATH')){ exit(); }

/* 
Подключаем к меню
*/
add_action('admin_menu', 'admin_menu_ga_settings');
function admin_menu_ga_settings(){
global $premiumbox;
	
	if(current_user_can('administrator')){
		add_submenu_page("pn_config", __('AJAX settings','pn'), __('AJAX settings','pn'), 'read', "pn_ga_settings", array($premiumbox, 'admin_temp'));
	}
}

add_action('pn_adminpage_title_pn_ga_settings', 'def_adminpage_title_pn_ga_settings');
function def_adminpage_title_pn_ga_settings($page){
	_e('AJAX settings','pn');
} 

/* настройки */
add_action('pn_adminpage_content_pn_ga_settings','def_pn_adminpage_content_pn_ga_settings');
function def_pn_adminpage_content_pn_ga_settings(){
global $wpdb, $premiumbox;

	$options = array();
	$options['top_title'] = array(
		'view' => 'h3',
		'title' => __('AJAX settings','pn'),
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	$options['ga_admin'] = array(
		'view' => 'select',
		'title' => __('AJAX checker for admin panel','pn'),
		'options' => array('0'=>__('No','pn'), '1'=>__('Yes','pn')),
		'default' => $premiumbox->get_option('ga','ga_admin'),
		'name' => 'ga_admin',
		'work' => 'int',
	);
	$options['ga_site'] = array(
		'view' => 'select',
		'title' => __('AJAX checker for website','pn'),
		'options' => array('0'=>__('No','pn'), '1'=>__('Yes','pn')),
		'default' => $premiumbox->get_option('ga','ga_site'),
		'name' => 'ga_site',
		'work' => 'int',
	);	
	$options['globalajax_help'] = array(
		'view' => 'help',
		'title' => __('More info','pn'),
		'default' => __('This option is able to create an additional load on server','pn'),
	);		
	$options['line1'] = array(
		'view' => 'line',
		'colspan' => 2,
	);	
	$options['admin_time'] = array(
		'view' => 'inputbig',
		'title' => __('Frequency of requests from admin panel', 'pn').' ('.__('seconds','pn').')',
		'default' => $premiumbox->get_option('ga','admin_time'),
		'name' => 'admin_time',
		'work' => 'input',
	);
	$options['site_time'] = array(
		'view' => 'inputbig',
		'title' => __('Frequency of requests from website', 'pn').' ('.__('seconds','pn').')',
		'default' => $premiumbox->get_option('ga','site_time'),
		'name' => 'site_time',
		'work' => 'input',
	);	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);	
	pn_admin_one_screen('pn_ga_settings_option', $options);

} 

/* обработка */
add_action('premium_action_pn_ga_settings','def_premium_action_pn_ga_settings');
function def_premium_action_pn_ga_settings(){
global $wpdb, $premiumbox;	

	only_post();

	pn_only_caps(array('administrator'));
		
	$options = array();
	$options['ga_admin'] = array(
		'name' => 'ga_admin',
		'work' => 'int',
	);
	$options['ga_site'] = array(
		'name' => 'ga_site',
		'work' => 'int',
	);	
	$options['admin_time'] = array(
		'name' => 'admin_time',
		'work' => 'int',
	);
	$options['site_time'] = array(
		'name' => 'site_time',
		'work' => 'int',
	);			
	$data = pn_strip_options('pn_ga_settings_option', $options, 'post');	
		
	$opts = array('ga_admin','ga_site');		
	foreach($opts as $key){
		$param = intval($data[$key]);
		$premiumbox->update_option('ga', $key, $param);
	}		
		
	$opts = array('admin_time','site_time');		
	foreach($opts as $key){
		$param = intval($data[$key]);
		if($param < 1){ $param = 1; }
		$premiumbox->update_option('ga', $key, $param);
	}		
	
	do_action('pn_ga_settings_option_post', $data);			
	
	$back_url = is_param_post('_wp_http_referer');
	$back_url .= '&reply=true';
			
	wp_safe_redirect($back_url);
	exit;	
}