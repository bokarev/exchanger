<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('admin_menu', 'admin_menu_usersettings');
function admin_menu_usersettings(){
global $premiumbox;	
	
	if(current_user_can('administrator')){
		$hook = add_submenu_page('pn_config', __('User profile settings','pn'), __('User profile settings','pn'), 'read', 'pn_usersettings', array($premiumbox, 'admin_temp'));  
		add_action( "load-$hook", 'pn_trev_hook' );
	}
}

add_action('pn_adminpage_title_pn_usersettings', 'def_adminpage_title_pn_usersettings');
function def_adminpage_title_pn_usersettings($page){
	_e('User profile settings','pn');
} 

/* настройки */
add_action('pn_adminpage_content_pn_usersettings','def_pn_adminpage_content_pn_usersettings');
function def_pn_adminpage_content_pn_usersettings(){
global $wpdb, $premiumbox;

	$options = array();
	$options['top_title'] = array(
		'view' => 'h3',
		'title' => __('Displaying fields on website','pn'),
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	
	$uf = $premiumbox->get_option('user_fields');
	
	$fields = array(
		'login' => __('Login', 'pn'),
		'last_name' => __('Last name', 'pn'),
		'first_name' => __('First name', 'pn'),
		'second_name' => __('Second name', 'pn'),
		'user_phone' => __('Phone no.', 'pn'),
		'user_skype' => __('Skype', 'pn'),
		'website' => __('Website', 'pn'),
		'user_passport' => __('Passport number', 'pn'),
	);
	
	foreach($fields as $field_key => $field_val){
		$options[$field_key] = array(
			'view' => 'select',
			'title' => sprintf(__('Display "%s" field','pn'), $field_val),
			'options' => array('0'=>__('No','pn'), '1'=>__('Yes','pn')),
			'default' => is_isset($uf, $field_key),
			'name' => $field_key,
			'work' => 'int',
		);	
	}
	
	$options['center_title'] = array(
		'view' => 'h3',
		'title' => __('Editing fields on website','pn'),
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);		
	
	$ufc = $premiumbox->get_option('user_fields_change');
	
	$chfields = array(
		'last_name' => __('Last name', 'pn'),
		'first_name' => __('First name', 'pn'),
		'second_name' => __('Second name', 'pn'),
		'user_phone' => __('Phone no.', 'pn'),
		'user_skype' => __('Skype', 'pn'),
		'website' => __('Website', 'pn'),
		'user_passport' => __('Passport number', 'pn'),
		'email' => __('E-mail', 'pn'),
	);	
	
	foreach($chfields as $field_key => $field_val){
		$options['ch_'.$field_key] = array(
			'view' => 'select',
			'title' => sprintf(__('Allow user to change "%s" field contents','pn'), $field_val),
			'options' => array('0'=>__('No','pn'), '1'=>__('Yes','pn')),
			'default' => is_isset($ufc, $field_key),
			'name' => 'ch_'.$field_key,
			'work' => 'int',
		);	
	}	
	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('pn_usersettings_config_option', $options);
	
} 

/* обработка */
add_action('premium_action_pn_usersettings','def_premium_action_pn_usersettings');
function def_premium_action_pn_usersettings(){
global $wpdb, $premiumbox;	

	only_post();
	pn_only_caps(array('administrator'));
	
	$fields = array(
		'login' => __('Login', 'pn'),
		'last_name' => __('Last name', 'pn'),
		'first_name' => __('First name', 'pn'),
		'second_name' => __('Second name', 'pn'),
		'user_phone' => __('Phone no.', 'pn'),
		'user_skype' => __('Skype', 'pn'),
		'website' => __('Website', 'pn'),
		'user_passport' => __('Passport number', 'pn'),
	);	
	$fields1 = array();
	foreach($fields as $k => $v){
		$fields1[$k] = intval(is_param_post($k));
	}
	
	$chfields = array(
		'last_name' => __('Last name', 'pn'),
		'first_name' => __('First name', 'pn'),
		'second_name' => __('Second name', 'pn'),
		'user_phone' => __('Phone no.', 'pn'),
		'user_skype' => __('Skype', 'pn'),
		'website' => __('Website', 'pn'),
		'user_passport' => __('Passport number', 'pn'),
		'email' => __('E-mail', 'pn'),
	);	
	$fields2 = array();
	foreach($chfields as $k => $v){
		$fields2[$k] = intval(is_param_post('ch_'.$k));
	}	
	
	$premiumbox->update_option('user_fields','',$fields1);
	$premiumbox->update_option('user_fields_change','',$fields2);			
	
	$back_url = is_param_post('_wp_http_referer');
	$back_url .= '&reply=true';
			
	wp_safe_redirect($back_url);
	exit;			
}