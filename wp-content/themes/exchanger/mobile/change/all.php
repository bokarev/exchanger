<?php
if( !defined( 'ABSPATH')){ exit(); }

/* 
Подключаем к меню
*/
add_action('admin_menu', 'pn_adminpage_theme_mobile');
function pn_adminpage_theme_mobile(){
global $premiumbox;
	
	add_submenu_page("pn_themeconfig", __('Mobile version','pntheme'), __('Mobile version','pntheme'), 'administrator', "pn_mobile_theme", array($premiumbox, 'admin_temp'));
}

add_action('pn_adminpage_title_pn_mobile_theme', 'pn_adminpage_title_pn_mobile_theme');
function pn_adminpage_title_pn_mobile_theme($page){
	_e('Mobile version','pntheme');
} 

/* настройки */
add_action('pn_adminpage_content_pn_mobile_theme','def_pn_adminpage_content_pn_mobile_theme');
function def_pn_adminpage_content_pn_mobile_theme(){
global $premiumbox;
	
	$change = get_option('mobile_change');
	
	$options = array();
	$options['top_title'] = array(
		'view' => 'h3',
		'title' => __('Mobile version','pntheme'),
		'submit' => __('Save','pntheme'),
		'colspan' => 2,
	);
	
	$options['mobilelogo'] = array(
		'view' => 'uploader',
		'title' => __('Logo', 'pntheme'),
		'default' => $premiumbox->get_option('mobilelogo'),
		'name' => 'mobilelogo',
		'work' => 'input',
		'ml' => 1,
	);	
	
	$options['mobiletextlogo'] = array(
		'view' => 'inputbig',
		'title' => __('Text logo', 'pn'),
		'default' => $premiumbox->get_option('mobiletextlogo'),
		'name' => 'mobiletextlogo',
		'work' => 'input',
		'ml' => 1,
	);	
	
	$options[] = array(
		'view' => 'line',
		'colspan' => 2,
	);	
	
	$options['linkhead'] = array(
		'view' => 'select',
		'title' => __('Logo link','pntheme'),
		'options' => array('0'=>__('always','pntheme'), '1'=>__('with the exception of homepage','pntheme')),
		'default' => is_isset($change,'linkhead'),
		'name' => 'linkhead',
		'work' => 'int',
	);	
	
	$options[] = array(
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

	$options[] = array(
		'view' => 'line',
		'colspan' => 2,
	);

	$options['ctext'] = array(
		'view' => 'textarea',
		'title' => __('Copywriting','pntheme'),
		'default' => is_isset($change,'ctext'),
		'name' => 'ctext',
		'width' => '',
		'height' => '100px',
		'work' => 'text',
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
add_action('premium_action_pn_mobile_theme','def_premium_action_pn_mobile_theme');
function def_premium_action_pn_mobile_theme(){
global $wpdb, $premiumbox;	

	only_post();

	pn_only_caps(array('administrator'));

	$options = array();
	$options['mobilelogo'] = array(
		'name' => 'mobilelogo',
		'work' => 'input',
		'ml' => 1,
	);
	$options['mobiletextlogo'] = array(
		'name' => 'mobiletextlogo',
		'work' => 'input',
		'ml' => 1,
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
	$options['ctext'] = array(
		'name' => 'ctext',
		'work' => 'text',
		'ml' => 1,
	);	
	$data = pn_strip_options('', $options, 'post');
	
	$premiumbox->update_option('mobilelogo', '', $data['mobilelogo']);
	$premiumbox->update_option('mobiletextlogo', '', $data['mobiletextlogo']);
	
	$change = get_option('mobile_change');
	if(!is_array($change)){ $change = array(); }
		
	$change['linkhead'] = $data['linkhead'];		
	$change['phone'] = $data['phone'];
	$change['icq'] = $data['icq'];
	$change['skype'] = $data['skype'];
	$change['email'] = $data['email'];
	$change['telegram'] = $data['telegram'];
	$change['viber'] = $data['viber'];
	$change['whatsup'] = $data['whatsup'];
	$change['jabber'] = $data['jabber'];
	$change['ctext'] = $data['ctext'];
	
	update_option('mobile_change',$change);
	
	$back_url = is_param_post('_wp_http_referer');
	$back_url .= '&reply=true';
			
	wp_safe_redirect($back_url);
	exit;	
}

function get_mobile_logotype(){
global $premiumbox;
	return is_ssl_url(pn_strip_input(ctv_ml($premiumbox->get_option('mobilelogo'))));
}

function get_mobile_textlogo(){
global $premiumbox;	
	return pn_strip_input(ctv_ml($premiumbox->get_option('mobiletextlogo')));
} 