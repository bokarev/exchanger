<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]SMPT[:ru_RU][en_US:]SMTP[:en_US]
description: [ru_RU:]Отправление электронной почты с помощью SMTP[:ru_RU][en_US:]Sending e-mail via SMTP[:en_US]
version: 1.0
category: [ru_RU:]E-mail[:ru_RU][en_US:]E-mail[:en_US]
cat: email
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* 
Подключаем к меню
*/
add_action('admin_menu', 'pn_adminpage_mailsmtp');
function pn_adminpage_mailsmtp(){
global $premiumbox;	
	add_submenu_page("pn_moduls", __('SMTP settings','pn'), __('SMTP settings','pn'), 'administrator', "pn_mailsmtp", array($premiumbox, 'admin_temp'));
}

add_action('pn_adminpage_title_pn_mailsmtp', 'pn_adminpage_title_pn_mailsmtp');
function pn_adminpage_title_pn_mailsmtp($page){
	_e('SMTP settings','pn');
} 

/* настройки */
add_action('pn_adminpage_content_pn_mailsmtp','pn_adminpage_content_pn_mailsmtp');
function pn_adminpage_content_pn_mailsmtp(){
global $wpdb, $premiumbox;

	$options = array();
	$options['top_title'] = array(
		'view' => 'h3',
		'title' => __('SMTP settings','pn'),
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	$options['enable'] = array(
		'view' => 'select',
		'title' => __('Enable SMTP','pn'),
		'options' => array('0'=>__('No','pn'),'1'=>__('Yes','pn')),
		'default' => $premiumbox->get_option('smtp','enable'),
		'name' => 'enable',
		'work' => 'int',
	);		
	$options['host'] = array(
		'view' => 'inputbig',
		'title' => __('SMTP Host','pn'),
		'default' => $premiumbox->get_option('smtp','host'),
		'name' => 'host',
		'work' => 'input',
	);
	$options['port'] = array(
		'view' => 'inputbig',
		'title' => __('SMTP Port','pn'),
		'default' => $premiumbox->get_option('smtp','port'),
		'name' => 'port',
		'work' => 'input',
	);
	$options['username'] = array(
		'view' => 'inputbig',
		'title' => __('SMTP Username','pn'),
		'default' => $premiumbox->get_option('smtp','username'),
		'name' => 'username',
		'work' => 'input',
	);
	$options['password'] = array(
		'view' => 'inputbig',
		'title' => __('SMTP Password','pn'),
		'default' => $premiumbox->get_option('smtp','password'),
		'name' => 'password',
		'work' => 'input',
	);
	$options['from'] = array(
		'view' => 'inputbig',
		'title' => __('SMTP Under name','pn'),
		'default' => $premiumbox->get_option('smtp','from'),
		'name' => 'from',
		'work' => 'input',
	);			
			
	$help = '
	<p>
		<strong>'. __('SMTP Host','pn').'</strong>: smtp.yandex.ru<br />
		<strong>'. __('SMTP Port','pn').'</strong>: 465
	</p>
	';
	$options['yahelp'] = array(
		'view' => 'help',
		'title' => __('Info for yandex','pn'),
		'default' => $help,
	);		
			
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);			
	pn_admin_one_screen('', $options); 
			
	$options = array();
	$options['top_title'] = array(
		'view' => 'h3',
		'title' => __('Send test e-mail','pn'),
		'submit' => __('Send a message','pn'),
		'colspan' => 2,
	);
	$options['to'] = array(
		'view' => 'inputbig',
		'title' => __('Your e-mail','pn'),
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
	pn_admin_one_screen('', $options, '', pn_link_post('pn_mailsmtp_send')); 
}  

/* обработка */
add_action('premium_action_pn_mailsmtp_send','def_premium_action_pn_mailsmtp_send');
function def_premium_action_pn_mailsmtp_send(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_mailtemp'));

		$to = is_email(is_param_post('to'));
		if(!$to){
			pn_display_mess(__('Error! You have not entered an e-mail!','pn'));
		} else {
			wp_mail($to, 'Test SMTP', 'Test SMTP content');			
		}

	$back_url = is_param_post('_wp_http_referer');
	$back_url .= '&reply=true';
			
	wp_safe_redirect($back_url);
	exit;
} 

add_action('premium_action_pn_mailsmtp','def_premium_action_pn_mailsmtp');
function def_premium_action_pn_mailsmtp(){
global $wpdb, $premiumbox;	

	only_post();
	pn_only_caps(array('administrator','pn_mailtemp'));
	
	$options = array();
	$options['enable'] = array(
		'name' => 'enable',
		'work' => 'int',
	);		
	$options['host'] = array(
		'name' => 'host',
		'work' => 'input',
	);
	$options['port'] = array(
		'name' => 'port',
		'work' => 'input',
	);
	$options['username'] = array(
		'name' => 'username',
		'work' => 'input',
	);
	$options['password'] = array(
		'name' => 'password',
		'work' => 'input',
	);
	$options['from'] = array(
		'name' => 'from',
		'work' => 'input',
	);							
	
	$data = pn_strip_options('', $options);
	foreach($data as $key => $val){
		$premiumbox->update_option('smtp', $key, $val);
	}				

	$back_url = is_param_post('_wp_http_referer');
	$back_url .= '&reply=true';
			
	wp_safe_redirect($back_url);
	exit;
} 


add_action('phpmailer_init','pn_send_smtp_email');
function pn_send_smtp_email( $phpmailer ) {
global $premiumbox;	
    if($premiumbox->get_option('smtp','enable') == 1){
	
		$phpmailer->isSMTP();
		$phpmailer->Host = $premiumbox->get_option('smtp','host');
		$phpmailer->SMTPAuth = true;
		$phpmailer->Port = $premiumbox->get_option('smtp','port');
		$phpmailer->Username = $premiumbox->get_option('smtp','username');
		$phpmailer->From = $premiumbox->get_option('smtp','username'); 
		$phpmailer->FromName = $premiumbox->get_option('smtp','from');
		$phpmailer->Password = $premiumbox->get_option('smtp','password');
		$phpmailer->SMTPSecure = "ssl";
	
	}
} 