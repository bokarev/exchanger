<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** добавить ************************************************/

add_action('pn_adminpage_title_pn_add_blacklist_many', 'pn_admin_title_pn_add_blacklist_many');
function pn_admin_title_pn_add_blacklist_many(){
	_e('Add list','pn');
}

add_action('pn_adminpage_content_pn_add_blacklist_many','def_pn_admin_content_pn_add_blacklist_many');
function def_pn_admin_content_pn_add_blacklist_many(){
global $wpdb;
	$title = __('Add list','pn');
	
	$back_menu = array();
	$back_menu['back'] = array(
		'link' => admin_url('admin.php?page=pn_blacklist'),
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
	$options['items'] = array(
		'view' => 'textarea',
		'title' => __('Value (on a new line)','pn'),
		'default' => '',
		'name' => 'items',
		'width' => '',
		'height' => '200px',
	);	
	$options['meta_key'] = array(
		'view' => 'select',
		'title' => __('Type','pn'),
		'options' => array('0'=>__('account','pn'),'1'=>__('e-mail','pn'),'2'=>__('phone number','pn'),'3'=>__('skype','pn'),'4'=>__('ip','pn')),
		'default' => 0,
		'name' => 'meta_key',
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
add_action('premium_action_pn_add_blacklist_many','def_premium_action_pn_add_blacklist_many');
function def_premium_action_pn_add_blacklist_many(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_blacklist'));

		$items = explode("\n",is_param_post('items'));
		if(is_array($items)){	
			$meta_key = intval(is_param_post('meta_key'));
			foreach($items as $item){
				$meta_value = pn_strip_input(str_replace('+','',$item));
				if($meta_value){
					$array = array();
					$array['meta_value'] = $meta_value;
					$array['meta_key'] = $meta_key;
					$wpdb->insert($wpdb->prefix.'blacklist', $array);
					$data_id = $wpdb->insert_id;
					do_action('pn_blacklist_add', $data_id, $array);
				}
			}
		}

	$url = admin_url('admin.php?page=pn_blacklist&reply=true');
	wp_redirect($url);
	exit;
}	
/* end обработка формы */