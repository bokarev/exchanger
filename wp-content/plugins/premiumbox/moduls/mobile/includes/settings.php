<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('admin_menu', 'admin_menu_mobile');
function admin_menu_mobile(){
global $premiumbox;
	
	if(current_user_can('administrator')){
		add_submenu_page('pn_config', __('Mobile version settings','pn'), __('Mobile version settings','pn'), 'administrator', 'pn_mobile_settings', array($premiumbox, 'admin_temp'));
	}
}

add_action('pn_adminpage_title_pn_mobile_settings', 'def_adminpage_title_pn_mobile_settings');
function def_adminpage_title_pn_mobile_settings(){
	_e('Mobile version settings','pn');
}

add_action('pn_adminpage_content_pn_mobile_settings','def_adminpage_content_pn_mobile_settings');
function def_adminpage_content_pn_mobile_settings(){
global $wpdb, $premiumbox;

	$options = array();
	$options['top_title'] = array(
		'view' => 'h3',
		'title' => __('Exchange settings','pn'),
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	$tablevids = array('0'=> sprintf(__('Table %1s','pn'),'1'),'1'=> sprintf(__('Table %1s','pn'),'2'),'2'=> sprintf(__('Table %1s','pn'),'3'));
	$tablevids = apply_filters('mobile_exchange_tablevids_list', $tablevids);
	$options['tablevid'] = array(
		'view' => 'select',
		'title' => __('Exchange pairs table type','pn'),
		'options' => $tablevids,
		'default' => $premiumbox->get_option('mobile','tablevid'),
		'name' => 'tablevid',
	);		
	
	$options[] = array(
		'view' => 'line',
		'colspan' => 2,
	);
	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('pn_mobile_exchange_config_option', $options);
} 

add_action('premium_action_pn_mobile_settings','def_premium_action_pn_mobile_settings');
function def_premium_action_pn_mobile_settings(){
global $wpdb, $premiumbox;	

	only_post();
	pn_only_caps(array('administrator'));

	$options = array('tablevid');
	foreach($options as $key){
		$val = pn_strip_input(is_param_post($key));
		$premiumbox->update_option('mobile',$key,$val);
	}			
			
	do_action('pn_mobile_exchange_config_option_post');
	
	$url = admin_url('admin.php?page=pn_mobile_settings&reply=true');
	wp_redirect($url);
	exit;
}	