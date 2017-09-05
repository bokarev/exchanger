<?php
if( !defined( 'ABSPATH')){ exit(); }
 
/* 
Подключаем к меню
*/
add_action('admin_menu', 'pn_adminpage_exchange_filters');
function pn_adminpage_exchange_filters(){
global $premiumbox;	
	add_submenu_page("pn_config", __('Exchange filters','pn'), __('Exchange filters','pn'), 'administrator', "pn_exchange_filters", array($premiumbox, 'admin_temp'));
}

add_action('pn_adminpage_title_pn_exchange_filters', 'pn_adminpage_title_pn_exchange_filters');
function pn_adminpage_title_pn_exchange_filters($page){
	_e('Exchange filters','pn');
} 

/* настройки */
add_action('pn_adminpage_content_pn_exchange_filters','def_pn_adminpage_content_pn_exchange_filters');
function def_pn_adminpage_content_pn_exchange_filters(){
global $wpdb, $premiumbox;

	$options = array();
	$options['warning'] = array(
		'view' => 'warning',
		'title' => __('More info','pn'),
		'default' => sprintf(__('You can set the filter properties for the following objects. For example, if the user will see the exchange direction on the home page if his country is forbidden for this exchange direction. Will the user see the exchange direction on the home page if he is not registered user and etc.','pn'), 'slovo'),
	);
	$changes = array(
		'0' => __('Show but disabled','pn'),
		'1' => __('Hide','pn'),
	);		
	$lists = apply_filters('pn_exchange_filters', array());
	$lists = (array)$lists;	
	$get_lists = array();
	foreach($lists as $list){
		$title = trim(is_isset($list,'title'));
		$name = trim(is_isset($list,'name'));
		if($name){
			$get_lists[] = array(
				'title' => $title,
				'name' => $name,
			);
		}
	}	

	$cats = apply_filters('pn_exchange_cat_filters', array());
	
	foreach($cats as $k => $v){
		$options[] = array(
			'view' => 'h3',
			'title' => $v,
			'submit' => __('Save','pn'),
			'colspan' => 2,
		);	
		if(isset($get_lists) and is_array($get_lists)){
			foreach($get_lists as $vn){
				$options[] = array(
					'view' => 'select',
					'title' => $vn['title'],
					'options' => $changes,
					'default' => $premiumbox->get_option('exf_'. $k .'_'. $vn['name']),
					'name' => 'exf_'. $k .'_'. $vn['name'],
				);				
			}
		}	
	}	
	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('', $options);							
} 

/* обработка */
add_action('premium_action_pn_exchange_filters','def_premium_action_pn_exchange_filters');
function def_premium_action_pn_exchange_filters(){
global $wpdb, $premiumbox;	

	only_post();
	pn_only_caps(array('administrator'));

	$lists = apply_filters('pn_exchange_filters', array());
	$lists = (array)$lists;
					
	$cats = apply_filters('pn_exchange_cat_filters', array());

	foreach($cats as $k => $v){
		foreach($lists as $list){
			$title = trim(is_isset($list,'title'));
			$name = trim(is_isset($list,'name'));
			if($name){
				$val = intval(is_param_post('exf_'. $k .'_'. $name));
				$premiumbox->update_option('exf_'. $k .'_'. $name, '', $val);
			}
		}	
	}

	$url = admin_url('admin.php?page=pn_exchange_filters&reply=true');
	wp_redirect($url);
	exit;
}