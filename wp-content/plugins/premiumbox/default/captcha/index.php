<?php
if( !defined( 'ABSPATH')){ exit(); }

$captcha_settings = intval(apply_filters('captcha_settings', 0));
if($captcha_settings == 1){
	
	add_action('admin_menu', 'admin_menu_sitecaptcha');
	function admin_menu_sitecaptcha(){
	global $premiumbox;	
		add_submenu_page("pn_moduls", __('Captcha','pn'), __('Captcha','pn'), 'administrator', "pn_sitecaptcha", array($premiumbox, 'admin_temp'));
	}	
	
	add_action('pn_adminpage_title_pn_sitecaptcha', 'def_adminpage_title_pn_sitecaptcha');
	function def_adminpage_title_pn_sitecaptcha(){
		_e('Captcha','pn');
	}	
	
	add_action('pn_adminpage_content_pn_sitecaptcha','def_pn_adminpage_content_pn_sitecaptcha');
	function def_pn_adminpage_content_pn_sitecaptcha(){
	global $premiumbox;
		
		$options = array();	
		$options['top_title'] = array(
			'view' => 'h3',
			'title' => __('Captcha','pn'),
			'submit' => __('Save','pn'),
			'colspan' => 2,
		);	
		$placed = apply_filters('placed_captcha', array());	
		if(is_array($placed)){
			foreach($placed as $key => $title){
				$options[] = array(
					'view' => 'select',
					'title' => $title,
					'options' => array('0'=>__('No','pn'),'1'=>__('Yes','pn')),
					'default' => $premiumbox->get_option('captcha',$key),
					'name' => $key,
				);			
			}
		}
		$options['bottom_title'] = array(
			'view' => 'h3',
			'title' => '',
			'submit' => __('Save','pn'),
			'colspan' => 2,
		);
		pn_admin_one_screen('', $options, '');	
	}

	/* форма обработки */
	add_action('premium_action_pn_sitecaptcha','def_premium_action_pn_sitecaptcha');
	function def_premium_action_pn_sitecaptcha() {
	global $wpdb, $premiumbox;	

		only_post();
		pn_only_caps(array('administrator'));

		$placed = apply_filters('placed_captcha', array());	
		if(is_array($placed)){
			foreach($placed as $key => $title){	
				$premiumbox->update_option('captcha',$key ,intval(is_param_post($key)));	
			}
		}		

		$url = admin_url('admin.php?page=pn_sitecaptcha&reply=true');
		wp_redirect($url);
		exit;
	}	
	/* end форма обработки */	
	
}