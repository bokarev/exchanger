<?php
if( !defined( 'ABSPATH')){ exit(); }

/* 
Подключаем к меню
*/
add_action('admin_menu', 'admin_menu_theme_header');
function admin_menu_theme_header(){
global $premiumbox;
	
	add_submenu_page("pn_themeconfig", __('Header','pntheme'), __('Header','pntheme'), 'administrator', "pn_theme_header", array($premiumbox, 'admin_temp'));
}

add_action('pn_adminpage_title_pn_theme_header', 'def_adminpage_title_pn_theme_header');
function def_adminpage_title_pn_theme_header($page){
	_e('Header','pntheme');
} 

/* настройки */
add_action('pn_adminpage_content_pn_theme_header','def_pn_adminpage_content_pn_theme_header');
function def_pn_adminpage_content_pn_theme_header(){
	
	$change = get_option('h_change');
	
	$options = array();
	$options['top_title'] = array(
		'view' => 'h3',
		'title' => __('Header','pntheme'),
		'submit' => __('Save','pntheme'),
		'colspan' => 2,
	);
	
	$options['fixheader'] = array(
		'view' => 'select',
		'title' => __('To fix','pntheme'),
		'options' => array('0'=>__('nothing','pntheme'), '1'=>__('bar','pntheme'), '2'=>__('menu','pntheme')),
		'default' => is_isset($change,'fixheader'),
		'name' => 'fixheader',
		'work' => 'int',
	);	

	$options['linkhead'] = array(
		'view' => 'select',
		'title' => __('Logo link','pntheme'),
		'options' => array('0'=>__('always','pntheme'), '1'=>__('with the exception of homepage','pntheme')),
		'default' => is_isset($change,'linkhead'),
		'name' => 'linkhead',
		'work' => 'int',
	);		
	
	$options['line1'] = array(
		'view' => 'line',
		'colspan' => 2,
	);	
	
	$options['phone'] = array(
		'view' => 'inputbig',
		'title' => __('Phone', 'pntheme'),
		'default' => is_isset($change,'phone'),
		'name' => 'phone',
		'work' => 'input',
		'ml' => 1,
	);
	
	$options['icq'] = array(
		'view' => 'inputbig',
		'title' => __('ICQ', 'pntheme'),
		'default' => is_isset($change,'icq'),
		'name' => 'icq',
		'work' => 'input',
		'ml' => 1,
	);

	$options['skype'] = array(
		'view' => 'inputbig',
		'title' => __('Skype', 'pntheme'),
		'default' => is_isset($change,'skype'),
		'name' => 'skype',
		'work' => 'input',
		'ml' => 1,
	);

	$options['email'] = array(
		'view' => 'inputbig',
		'title' => __('E-mail', 'pntheme'),
		'default' => is_isset($change,'email'),
		'name' => 'email',
		'work' => 'input',
		'ml' => 1,
	);

	$options['telegram'] = array(
		'view' => 'inputbig',
		'title' => __('Telegram', 'pntheme'),
		'default' => is_isset($change,'telegram'),
		'name' => 'telegram',
		'work' => 'input',
		'ml' => 1,
	);

	$options['viber'] = array(
		'view' => 'inputbig',
		'title' => __('Viber', 'pntheme'),
		'default' => is_isset($change,'viber'),
		'name' => 'viber',
		'work' => 'input',
		'ml' => 1,
	);

	$options['whatsup'] = array(
		'view' => 'inputbig',
		'title' => __('WhatsApp', 'pntheme'),
		'default' => is_isset($change,'whatsup'),
		'name' => 'whatsup',
		'work' => 'input',
		'ml' => 1,
	);

	$options['jabber'] = array(
		'view' => 'inputbig',
		'title' => __('Jabber', 'pntheme'),
		'default' => is_isset($change,'jabber'),
		'name' => 'jabber',
		'work' => 'input',
		'ml' => 1,
	);		

	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pntheme'),
		'colspan' => 2,
	);
	
	pn_admin_one_screen('', $options);	
} 

/* обработка */
add_action('premium_action_pn_theme_header','def_premium_action_pn_theme_header');
function def_premium_action_pn_theme_header(){
global $wpdb;	

	only_post();

	pn_only_caps(array('administrator'));

	$options = array();
	$options['fixheader'] = array(
		'name' => 'fixheader',
		'work' => 'int',
	);	
	$options['linkhead'] = array(
		'name' => 'linkhead',
		'work' => 'int',
	);		
	$options['phone'] = array(
		'name' => 'phone',
		'work' => 'input',
		'ml' => 1,
	);
	$options['icq'] = array(
		'name' => 'icq',
		'work' => 'input',
		'ml' => 1,
	);
	$options['skype'] = array(
		'name' => 'skype',
		'work' => 'input',
		'ml' => 1,
	);
	$options['telegram'] = array(
		'name' => 'telegram',
		'work' => 'input',
		'ml' => 1,
	);
	$options['viber'] = array(
		'name' => 'viber',
		'work' => 'input',
		'ml' => 1,
	);
	$options['whatsup'] = array(
		'name' => 'whatsup',
		'work' => 'input',
		'ml' => 1,
	);
	$options['jabber'] = array(
		'name' => 'jabber',
		'work' => 'input',
		'ml' => 1,
	);	
	$options['email'] = array(
		'name' => 'email',
		'work' => 'input',
		'ml' => 1,
	);	
	$data = pn_strip_options('', $options, 'post');
	
	$change = get_option('h_change');
	if(!is_array($change)){ $change = array(); }
	
	$change['fixheader'] = $data['fixheader']; 	
	$change['linkhead'] = $data['linkhead'];
				
	$change['phone'] = $data['phone'];
	$change['icq'] = $data['icq'];
	$change['skype'] = $data['skype'];
	$change['email'] = $data['email'];
	$change['telegram'] = $data['telegram'];
	$change['viber'] = $data['viber'];
	$change['whatsup'] = $data['whatsup'];
	$change['jabber'] = $data['jabber'];
	
	update_option('h_change',$change);
	
	$back_url = is_param_post('_wp_http_referer');
	$back_url .= '&reply=true';
			
	wp_safe_redirect($back_url);
	exit;	
}