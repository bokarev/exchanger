<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_pn_maintrance', 'pn_adminpage_title_pn_maintrance');
function pn_adminpage_title_pn_maintrance($page){
	_e('Maintenance mode','pn');
} 

/* настройки */
add_action('pn_adminpage_content_pn_maintrance','def_pn_adminpage_content_pn_maintrance');
function def_pn_adminpage_content_pn_maintrance(){
global $wpdb, $premiumbox;

	$options = array();
	$options['top_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);	
	$options['maintrance'] = array(
		'view' => 'select',
		'title' => __('How to switch maintenance mode','pn'),
		'options' => array('0'=>__('Manually','pn'),'1'=>__('Depends on operator status','pn')),
		'default' => $premiumbox->get_option('tech','maintrance'),
		'name' => 'maintrance',
	);	
	$status = intval($premiumbox->get_option('tech','manualy'));
	$items = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."maintrance ORDER BY id DESC");
	$selects = array();
	$selects[0] = __('No','pn');
	foreach($items as $item){
		$selects[$item->id] = pn_strip_input(ctv_ml($item->the_title));
	}	
	$options['manualy'] = array(
		'view' => 'select',
		'title' => __('Maintenance mode','pn'),
		'options' => $selects,
		'default' => $premiumbox->get_option('tech','manualy'),
		'name' => 'manualy',
	);		
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('pn_maintrance_option', $options);
} 

/* обработка */
add_action('premium_action_pn_maintrance','def_premium_action_pn_maintrance');
function def_premium_action_pn_maintrance(){
global $wpdb, $premiumbox;	

	only_post();
	pn_only_caps(array('administrator'));
	
	$options = array('maintrance','manualy');	
	foreach($options as $key){
		$val = intval(is_param_post($key));
		$premiumbox->update_option('tech', $key, $val);
	}		
			
	do_action('pn_maintrance_option_post');
	
	$url = admin_url('admin.php?page=pn_maintrance&reply=true');
	wp_redirect($url);
	exit;
}