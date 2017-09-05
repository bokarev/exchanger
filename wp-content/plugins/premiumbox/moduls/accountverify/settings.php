<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** настройки ************************************************/
add_action('pn_adminpage_title_pn_usac_change', 'def_adminpage_title_pn_usac_change');
function def_adminpage_title_pn_usac_change(){
	_e('Settings','pn');
} 

add_action('pn_adminpage_content_pn_usac_change','def_adminpage_content_pn_usac_change');
function def_adminpage_content_pn_usac_change(){
global $premiumbox;	
	
	$options = array();
	$options['top_title'] = array(
		'view' => 'h3',
		'title' => __('Settings','pn'),
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);		
	$options['acc_status'] = array(
		'view' => 'select',
		'title' => __('Allow send request','pn'),
		'options' => array('0'=>__('No','pn'),'1'=>__('Yes','pn')),
		'default' => $premiumbox->get_option('usve','acc_status'),
		'name' => 'acc_status',
	);	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('pn_usac_change_adminform', $options);	
	
} 

add_action('premium_action_pn_usac_change','def_premium_action_pn_usac_change');
function def_premium_action_pn_usac_change(){
global $wpdb, $premiumbox;	

	only_post();
	pn_only_caps(array('administrator','pn_accountverify'));

	$options = array('acc_status');
	foreach($options as $key){
		$val = is_my_money(is_param_post($key));
		$premiumbox->update_option('usve',$key,$val);
	}			
			
	do_action('pn_usac_change_adminform_post');
			
	$url = admin_url('admin.php?page=pn_usac_change&reply=true');
	wp_redirect($url);
	exit;
}	 