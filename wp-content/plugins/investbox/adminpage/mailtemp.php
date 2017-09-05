<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_inex_mailtemp', 'pn_adminpage_title_inex_mailtemp');
function pn_adminpage_title_inex_mailtemp(){
	_e('E-mail templates','inex');
}

add_action('pn_adminpage_content_inex_mailtemp','def_pn_adminpage_content_inex_mailtemp');
function def_pn_adminpage_content_inex_mailtemp(){
global $wpdb;
		
	$place = pn_strip_input(is_param_get('place'));	
		
	$selects = array();
	$selects[] = array(
		'link' => admin_url("admin.php?page=inex_mailtemp"),
		'title' => '--' . __('Make your choice','inex') . '--',
		'background' => '',
		'default' => '',
	);			
 
	$places_admin = apply_filters('inex_admin_mailtemp',array());
	$places_admin = (array)$places_admin;
	$places_admin_t = array();
			
	if(count($places_admin) > 0){
		$selects[] = array(
			'link' => admin_url("admin.php?page=inex_mailtemp&place=admin_notify"),
			'title' => '---' . __('Admin notification','inex'),
			'background' => '#faf9c4',
			'default' => 'admin_notify',
		);				
	}
			
	foreach($places_admin as $key => $val){
		$places_admin_t[] = $key;
				
		$selects[] = array(
			'link' => admin_url("admin.php?page=inex_mailtemp&place=".$key),
			'title' => $val,
			'background' => '',
			'default' => $key,
		);				
	}		
			
	$places_user = apply_filters('inex_user_mailtemp',array());
	$places_user = (array)$places_user;
	$places_user_t = array();
			
	if(count($places_user) > 0){
		$selects[] = array(
			'link' => admin_url("admin.php?page=inex_mailtemp&place=user_notify"),
			'title' => '---' . __('User notifications','inex'),
			'background' => '#faf9c4',
			'default' => 'user_notify',
		);					
	}			
			
	foreach($places_user as $key => $val){
		$places_user_t[] = $key;
				
		$selects[] = array(
			'link' => admin_url("admin.php?page=inex_mailtemp&place=".$key),
			'title' => $val,
			'background' => '',
			'default' => $key,
		);				
	}
			
	pn_admin_select_box($place, $selects, __('Setting up','inex'));

	if(in_array($place,$places_admin_t) or in_array($place,$places_user_t)){
		$mailtemp = get_option('inex_mailtemp');
		if(!is_array($mailtemp)){ $mailtemp = array(); }
		$data = is_isset($mailtemp,$place);		
		
		$options = array();
		$options['top_title'] = array(
			'view' => 'h3',
			'title' => __('E-mail templates','inex'),
			'submit' => __('Save','inex'),
			'colspan' => 2,
		);
		$options['hidden_block'] = array(
			'view' => 'hidden_input',
			'name' => 'block',
			'default' => $place,
		);				
		$options['send'] = array(
			'view' => 'select',
			'title' => __('Send letter','inex'),
			'options' => array('0'=>__('No','inex'),'1'=>__('Yes','inex')),
			'default' => is_isset($data, 'send'),
			'name' => 'send',
			'work' => 'int',
		);		
		$options['title'] = array(
			'view' => 'inputbig',
			'title' => __('Subject','inex'),
			'default' => is_isset($data, 'title'),
			'name' => 'title',
			'work' => 'input',
			'ml' => 1,
		);
		$options['mail'] = array(
			'view' => 'inputbig',
			'title' => __('Sender e-mail','inex'),
			'default' => is_isset($data, 'mail'),
			'name' => 'mail',
			'work' => 'input',
		);	
		$options['mail_warning'] = array(
			'view' => 'warning',
			'default' => __('Use only existing e-mail like info@site.ru','inex'),
		);		
		$options['name'] = array(
			'view' => 'inputbig',
			'title' => __('Sender name','inex'),
			'default' => is_isset($data, 'name'),
			'name' => 'name',
			'work' => 'input',
		);	

		if(in_array($place,$places_admin_t)){
			$options['tomail'] = array(
				'view' => 'inputbig',
				'title' => __('Recipient e-mail','inex'),
				'default' => is_isset($data, 'tomail'),
				'name' => 'tomail',
				'work' => 'input',
			);					
			$options['tomailhelp'] = array(
				'view' => 'help',
				'title' => __('More info','inex'),
				'default' => __('If recipient obtained more than one e-mail address then use comma to separate addresses you enter','inex'),
			);					
		}
				
		$tags = array(
			'sitename' => __('Website name','inex'),
		);
		$tags = apply_filters('inex_mailtemp_tags_'.$place, $tags);
				
		$options['text'] = array(
			'view' => 'textareatags',
			'title' => __('E-mail text','inex'),
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
			'submit' => __('Save','inex'),
			'colspan' => 2,
		);			
		pn_admin_one_screen('', $options, $data); 		
		
	}	
}

/* обработка */
add_action('premium_action_inex_mailtemp','def_premium_action_inex_mailtemp');
function def_premium_action_inex_mailtemp(){
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
	
		$data = pn_strip_options('', $options);
				
		if($block){
			$mailtemp = get_option('inex_mailtemp');
			if(!is_array($mailtemp)){ $mailtemp = array(); }

			$mailtemp[$block]['send'] = $data['send'];
			$mailtemp[$block]['title'] = $data['title'];
			if(isset($data['mail'])){
				$mailtemp[$block]['mail'] = $data['mail'];
			}
			$mailtemp[$block]['tomail'] = $data['tomail'];
			if(isset($data['name'])){
				$mailtemp[$block]['name'] = $data['name'];
			}
			$mailtemp[$block]['text'] = $data['text'];

			update_option('inex_mailtemp', $mailtemp);
		}			

		$back_url = is_param_post('_wp_http_referer');
		$back_url .= '&reply=true';
			
		wp_safe_redirect($back_url);
		exit; 
}	

add_filter('inex_admin_mailtemp','def_inex_admin_mailtemp');
function def_inex_admin_mailtemp($places_admin){
	
	$places_admin['mail1'] = __('New deposit','inex');
	$places_admin['mail2'] = __('Payout already requested','inex');
	$places_admin['mail3'] = __('Prolongation of the deposit','inex');
	
	return $places_admin;
}

add_filter('inex_user_mailtemp','def_inex_user_mailtemp');
function def_inex_user_mailtemp($places_admin){
	
	$places_admin['mail1u'] = __('Deposit period is about to reach the limit','inex');
	
	return $places_admin;
}

add_filter('inex_mailtemp_tags_mail1u','def_mailtemp_tags_mail1u');
function def_mailtemp_tags_mail1u($tags){
	
	$tags['id'] = __('Deposit ID','inex');
	$tags['outsumm'] = __('Total amount','inex');
	
	return $tags;
}

add_filter('inex_mailtemp_tags_mail1','def_mailtemp_tags_mail1');
add_filter('inex_mailtemp_tags_mail2','def_mailtemp_tags_mail1');
function def_mailtemp_tags_mail1($tags){
	
	$tags['id'] = __('Deposit ID','inex');
	$tags['outsumm'] = __('Amount','inex');
	$tags['system'] = __('Payment system','inex');
	
	return $tags;
}

add_filter('inex_mailtemp_tags_mail3','def_mailtemp_tags_mail3');
function def_mailtemp_tags_mail3($tags){
	
	$tags['id'] = __('Deposit ID','inex');
	$tags['newid'] = __('New deposit ID','inex');
	
	return $tags;
}