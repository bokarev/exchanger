<?php
if( !defined( 'ABSPATH')){ exit(); }

add_filter('pn_caps','pn_caps_mailtemp');
function pn_caps_mailtemp($pn_caps){
	
	$pn_caps['pn_mailtemp'] = __('Work with e-mail templates','pn');
	
	return $pn_caps;
}

/* 
Подключаем к меню
*/
add_action('admin_menu', 'admin_menu_mailtemp');
function admin_menu_mailtemp(){
global $premiumbox;
	
	if(current_user_can('administrator') or current_user_can('pn_mailtemp')){
		add_submenu_page("pn_config", __('E-mail templates','pn'), __('E-mail templates','pn'), 'read', "pn_mailtemp", array($premiumbox, 'admin_temp'));
	}
}

add_action('pn_adminpage_title_pn_mailtemp', 'def_pn_adminpage_title_pn_mailtemp');
function def_pn_adminpage_title_pn_mailtemp(){
	_e('E-mail templates','pn');
}

add_action('pn_adminpage_content_pn_mailtemp','def_pn_adminpage_content_pn_mailtemp');
function def_pn_adminpage_content_pn_mailtemp(){
global $wpdb;
		
	$place = pn_strip_input(is_param_get('place'));	
		
	$selects = array();
	$selects[] = array(
		'link' => admin_url("admin.php?page=pn_mailtemp"),
		'title' => '--' . __('Make a choice','pn') . '--',
		'background' => '',
		'default' => '',
	);			
 
	$places_admin = apply_filters('admin_mailtemp',array());
	$places_admin = (array)$places_admin;
	$places_admin_t = array();
			
	if(count($places_admin) > 0){
		$selects[] = array(
			'link' => admin_url("admin.php?page=pn_mailtemp&place=admin_notify"),
			'title' => '---' . __('Admin notification','pn'),
			'background' => '#faf9c4',
			'default' => 'admin_notify',
		);				
	}
			
	foreach($places_admin as $key => $val){
		$places_admin_t[] = $key;
				
		$selects[] = array(
			'link' => admin_url("admin.php?page=pn_mailtemp&place=".$key),
			'title' => $val,
			'background' => '',
			'default' => $key,
		);				
	}		
			
	$places_user = apply_filters('user_mailtemp',array());
	$places_user = (array)$places_user;
	$places_user_t = array();
			
	if(count($places_user) > 0){
		$selects[] = array(
			'link' => admin_url("admin.php?page=pn_mailtemp&place=user_notify"),
			'title' => '---' . __('Users notification','pn'),
			'background' => '#faf9c4',
			'default' => 'user_notify',
		);					
	}			
			
	foreach($places_user as $key => $val){
		$places_user_t[] = $key;
				
		$selects[] = array(
			'link' => admin_url("admin.php?page=pn_mailtemp&place=".$key),
			'title' => $val,
			'background' => '',
			'default' => $key,
		);				
	}
			
	pn_admin_select_box($place, $selects, __('Setting up','pn'));

	if(in_array($place,$places_admin_t) or in_array($place,$places_user_t)){
		$mailtemp = get_option('mailtemp');
		if(!is_array($mailtemp)){ $mailtemp = array(); }
		$data = is_isset($mailtemp,$place);		
		
		$options = array();
		$options['top_title'] = array(
			'view' => 'h3',
			'title' => __('E-mail templates','pn'),
			'submit' => __('Save','pn'),
			'colspan' => 2,
		);
		$options['hidden_block'] = array(
			'view' => 'hidden_input',
			'name' => 'block',
			'default' => $place,
		);				
		$options['send'] = array(
			'view' => 'select',
			'title' => __('To send a letter','pn'),
			'options' => array('0'=>__('No','pn'),'1'=>__('Yes','pn')),
			'default' => is_isset($data, 'send'),
			'name' => 'send',
			'work' => 'int',
		);		
		$options['title'] = array(
			'view' => 'inputbig',
			'title' => __('Subject of an e-mail','pn'),
			'default' => is_isset($data, 'title'),
			'name' => 'title',
			'work' => 'input',
			'ml' => 1,
		);
		$options['mail'] = array(
			'view' => 'inputbig',
			'title' => __('Senders e-mail','pn'),
			'default' => is_isset($data, 'mail'),
			'name' => 'mail',
			'work' => 'input',
		);	
		$options['mail_warning'] = array(
			'view' => 'warning',
			'default' => __('Use only existing e-mail address (for example - info@site.ru)','pn'),
		);		
		$options['name'] = array(
			'view' => 'inputbig',
			'title' => __('Sender name','pn'),
			'default' => is_isset($data, 'name'),
			'name' => 'name',
			'work' => 'input',
		);	

		if(in_array($place,$places_admin_t)){
			$options['tomail'] = array(
				'view' => 'inputbig',
				'title' => __('Recipient e-mail','pn'),
				'default' => is_isset($data, 'tomail'),
				'name' => 'tomail',
				'work' => 'input',
			);					
			$options['tomailhelp'] = array(
				'view' => 'help',
				'title' => __('More info','pn'),
				'default' => __('If recipient obtained more than one e-mail address then use comma to separate addresses you enter','pn'),
			);					
		}
				
		$tags = array(
			'sitename' => __('Website name','pn'),
		);
		$tags = apply_filters('mailtemp_tags_'.$place, $tags);
				
		$options['text'] = array(
			'view' => 'textareatags',
			'title' => __('E-mail text','pn'),
			'default' => is_isset($data, 'text'),
			'tags' => $tags,
			'width' => '',
			'height' => '300px',
			'prefix1' => '[',
			'prefix2' => ']',
			'name' => 'text',
			'work' => 'text',
			'ml' => 1,
		);				
				
		$options['bottom_title'] = array(
			'view' => 'h3',
			'title' => '',
			'submit' => __('Save','pn'),
			'colspan' => 2,
		);			
		pn_admin_one_screen('pn_mailtemp_option', $options, $data); 		
		
	}	
}

/* обработка */
add_action('premium_action_pn_mailtemp','def_premium_action_pn_mailtemp');
function def_premium_action_pn_mailtemp(){
global $wpdb;

	only_post();
	pn_only_caps(array('administrator','pn_mailtemp'));
	
		$block = pn_strip_input(is_param_post('block'));
			
		$options = array();
		$options['send'] = array(
			'name' => 'send',
			'work' => 'int',
		);	
		$options['title'] = array(
			'name' => 'title',
			'work' => 'input',
			'ml' => 1,
		);	
		$options['mail'] = array(
			'name' => 'mail',
			'work' => 'input',
		);
		$options['name'] = array(
			'name' => 'name',
			'work' => 'input',
		);				
		$options['tomail'] = array(
			'name' => 'tomail',
			'work' => 'input',
		);
		$options['text'] = array(
			'name' => 'text',
			'work' => 'text',
			'ml' => 1,
		);				
	
		$data = pn_strip_options('pn_mailtemp_option', $options);
				
		if($block){
			$mailtemp = get_option('mailtemp');
			if(!is_array($mailtemp)){ $mailtemp = array(); }

			$mailtemp[$block]['send'] = is_isset($data,'send');
			$mailtemp[$block]['title'] = is_isset($data,'title');
			$mailtemp[$block]['mail'] = is_isset($data,'mail');
			$mailtemp[$block]['tomail'] = is_isset($data,'tomail');
			$mailtemp[$block]['name'] = is_isset($data,'name');
			$mailtemp[$block]['text'] = is_isset($data,'text');

			update_option('mailtemp', $mailtemp);
		}			

		do_action('pn_mailtemp_option_post', $data);

		$back_url = is_param_post('_wp_http_referer');
		$back_url .= '&reply=true';
			
		wp_safe_redirect($back_url);
		exit; 
}	