<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_pn_geoip_addblacklist', 'pn_admin_title_pn_geoip_addblacklist');
function pn_admin_title_pn_geoip_addblacklist(){
	_e('Block IP','pn');
}

add_action('pn_adminpage_content_pn_geoip_addblacklist','def_pn_admin_content_pn_geoip_addblacklist');
function def_pn_admin_content_pn_geoip_addblacklist(){
global $wpdb;

	$title = __('Block IP','pn');
	
	$back_menu = array();
	$back_menu['back'] = array(
		'link' => admin_url('admin.php?page=pn_geoip_blacklist'),
		'title' => __('Back to list','pn')
	);	
	pn_admin_back_menu($back_menu, '');

	$options = array();
	$options['top_title'] = array(
		'view' => 'h3',
		'title' => $title,
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);	
	$options['lists'] = array(
		'view' => 'textarea',
		'title' => __('IP addresses (at the beginning of a new line)','pn'),
		'default' => '',
		'name' => 'lists',
		'width' => '',
		'height' => '200px',
	);	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('', $options, '');	
}

/* обработка формы */
add_action('premium_action_pn_geoip_addblacklist','def_premium_action_pn_geoip_addblacklist');
function def_premium_action_pn_geoip_addblacklist(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_geoip'));

	$lists = explode("\n",is_param_post('lists'));
	foreach($lists as $list){
		$ip = pn_strip_input($list);
		if($ip){
			$cc = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."geoip_blackip WHERE theip = '$ip'");
			if($cc == 0){
				$array = array();
				$array['theip'] = $ip;
				$wpdb->insert($wpdb->prefix.'geoip_blackip', $array);
			}
		}
	}	

	$url = admin_url('admin.php?page=pn_geoip_blacklist&reply=true');
	wp_redirect($url);
	exit;
}	
/* end обработка формы */