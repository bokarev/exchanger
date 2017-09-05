<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_pn_config_reviews', 'pn_adminpage_title_pn_config_reviews');
function pn_adminpage_title_pn_config_reviews(){
	_e('Settings','pn');
}

add_action('pn_adminpage_content_pn_config_reviews','def_pn_adminpage_content_pn_config_reviews');
function def_pn_adminpage_content_pn_config_reviews(){
global $premiumbox;	
	
	$options = array();
	$options['top_title'] = array(
		'view' => 'h3',
		'title' => __('Settings','pn'),
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	$count = intval($premiumbox->get_option('reviews','count'));
	if(!$count){ $count = 10; }
	$options['count'] = array(
		'view' => 'input',
		'title' => __('Amount of reviews on a page','pn'),
		'default' => $count,
		'name' => 'count',
		'work' => 'input',
	);				
	$options['deduce'] = array(
		'view' => 'select',
		'title' => __('Display reviews','pn'),
		'options' => array('0'=>__('All'),'1'=>__('by language','pn')),
		'default' => $premiumbox->get_option('reviews','deduce'),
		'name' => 'deduce',
		'work' => 'int',
	);
	$options['method'] = array(
		'view' => 'select',
		'title' => __('Method used for adding process','pn'),
		'options' => array('not'=>__('Forbidden to add','pn'),'verify'=>__('E-mail confirmation','pn'),'moderation'=>__('Moderation by admin','pn'),'notmoderation'=>__('Without moderation','pn')),
		'default' => $premiumbox->get_option('reviews','method'),
		'name' => 'method',
		'work' => 'int',
	);
	$options['website'] = array(
		'view' => 'select',
		'title' => __('Enable field "Website"','pn'),
		'options' => array('0'=>__('No','pn'),'1'=>__('Yes','pn')),
		'default' => $premiumbox->get_option('reviews','website'),
		'name' => 'website',
		'work' => 'int',
	);	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	
	pn_admin_one_screen('pn_reviews_configform', $options);	
	
}

add_action('premium_action_pn_config_reviews','def_premium_action_pn_config_reviews');
function def_premium_action_pn_config_reviews(){
global $wpdb, $premiumbox;	

	only_post();
	pn_only_caps(array('administrator','pn_reviews'));
		
	$options = array('count','deduce','method','website');	
	foreach($options as $key){
		$val = pn_strip_input(is_param_post($key));
		$premiumbox->update_option('reviews', $key, $val);
	}
				
	do_action('pn_reviews_configform_post');
			
	$url = admin_url('admin.php?page=pn_config_reviews&reply=true');
	wp_redirect($url);
	exit;
}	