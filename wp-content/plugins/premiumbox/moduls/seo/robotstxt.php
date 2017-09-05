<?php
if( !defined( 'ABSPATH')){ exit(); }

/* 
Подключаем к меню
*/
add_action('pn_adminpage_title_pn_robotstxt', 'pn_adminpage_title_pn_robotstxt');
function pn_adminpage_title_pn_robotstxt($page){
	_e('Robots.txt settings','pn');
} 

/* настройки */
add_action('pn_adminpage_content_pn_robotstxt','def_pn_adminpage_content_pn_robotstxt');
function def_pn_adminpage_content_pn_robotstxt(){
global $premiumbox;
	
	$options = array();
	$options['top_title'] = array(
		'view' => 'h3',
		'title' => __('Robots.txt settings','pn'),
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);		
	$options['txt'] = array( 
		'view' => 'textarea',
		'title' => __('Text','pn'),
		'default' => $premiumbox->get_option('robotstxt','txt'),
		'name' => 'txt',
		'width' => '',
		'height' => '300px',
	);	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('robotstxt_changeform', $options);	
} 


/* обработка */
add_action('premium_action_pn_robotstxt','def_premium_action_pn_robotstxt');
function def_premium_action_pn_robotstxt(){
global $wpdb, $premiumbox;	

	only_post();
	pn_only_caps(array('administrator', 'pn_seo'));
	
	$options = array('txt');	
					
	foreach($options as $key){
		$val = pn_strip_input(is_param_post($key));
		$premiumbox->update_option('robotstxt',$key,$val);
	}				

	do_action('robotstxt_changeform_post');
	
	$url = admin_url('admin.php?page=pn_robotstxt&reply=true');
	wp_redirect($url);
	exit;
} 