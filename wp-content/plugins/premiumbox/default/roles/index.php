<?php
if( !defined( 'ABSPATH')){ exit(); }

/* названия ролей */
function pn_role_title($text){
	if ( $text == 'topmeneger' ){
		return __('Manager','pn');
	}
	if ( $text == 'meneger' ){
		return __('Operator','pn');
	}
	if ( $text == 'users' ){
		return __('User','pn');
	}	
	return $text;
}

add_filter( 'gettext_with_context', 'standart_role_text', 10, 4 );
function standart_role_text( $translation, $text, $context, $domain ) {
	if ( $text == 'topmeneger' ){
		return __('Manager','pn');
	}
	if ( $text == 'meneger' ){
		return __('Operator','pn');
	}
	if ( $text == 'users' ){
		return __('User','pn');
	}
		return $translation;
}
/* end названия ролей */

function get_pn_capabilities(){
	$pn_caps = array(
		'read' => __('Access to admin panel','pn'),
		'switch_themes' => __('Switch themes','pn'),
		'edit_theme_options'=> __('Edit theme options','pn'),
		'activate_plugins'=> __('Activate plugins','pn'),
		'list_users' => __('User list','pn'), 
		'edit_users' => __('Edit users','pn'), 
		'upload_files' => __('Upload files','pn'),
		'edit_files' => __('Edit files','pn'),
		'unfiltered_upload' => __('Unfiltered upload','pn'),
		'unfiltered_html' => __('Unfiltered HTML','pn'),
		'edit_posts' => __('Edit posts and images','pn'),
		'edit_others_posts' => __('Edit others posts and images','pn'),		
		'edit_published_posts' => __('Edit published posts','pn'),
		'publish_posts' => __('Publish posts','pn'),
		'delete_posts' => __('Delete posts','pn'),
		'delete_others_posts' => __('Delete other posts','pn'),
		'delete_published_posts' => __('Delete published posts','pn'),		
		'edit_pages' => __('Edit pages','pn'),
		'edit_others_pages' => __('Edit other pages','pn'),
		'edit_published_pages' => __('Edit published pages','pn'),
		'publish_pages' => __('Publish pages','pn'),
		'delete_pages' => __('Delete pages','pn'),
		'delete_others_pages' => __('Delete other pages','pn'),
		'delete_published_pages' => __('Delete published pages','pn'),
	);
	$pn_caps = apply_filters('pn_caps',$pn_caps);
	$pn_caps = (array)$pn_caps;
	return $pn_caps;
}
/* 
Подключаем к меню
*/
add_action('admin_menu', 'admin_menu_roles');
function admin_menu_roles(){
global $premiumbox;

	add_submenu_page("pn_config", __('User roles','pn'), __('User roles','pn'), 'administrator', "pn_roles", array($premiumbox, 'admin_temp'));
}

add_action('pn_adminpage_title_pn_roles', 'def_pn_adminpage_title_pn_roles');
function def_pn_adminpage_title_pn_roles(){
	_e('User roles','pn');
}

add_action('pn_adminpage_content_pn_roles','def_pn_adminpage_content_pn_roles');
function def_pn_adminpage_content_pn_roles(){
global $wpdb;

	$prefix = $wpdb->prefix;
	
	global $wp_roles;
	if (!isset($wp_roles)){
		$wp_roles = new WP_Roles();
	}
	
	$selects = array();
	$selects[] = array(
		'link' => admin_url("admin.php?page=pn_roles"),
		'title' => '--' . __('Make a choice','pn') . '--',
		'background' => '',
		'default' => '',
	);		
	
	$places = array();
	$place = is_param_get('place');
	if(isset($wp_roles)){
		foreach($wp_roles->role_names as $role => $name){
			if($role != 'administrator'){
				$places[] = $role;
				$selects[] = array(
					'link' => admin_url("admin.php?page=pn_roles&place=" . $role),
					'title' => pn_role_title($role),
					'background' => '',
					'default' => $role,
				);				
			}	
		}
	}	
	pn_admin_select_box($place, $selects, __('Setting up','pn'));

	if(in_array($place,$places)){
		$pn_caps = get_pn_capabilities();	
		$capabilities = $wp_roles->roles[$place]['capabilities'];

		$options = array();
		$options['top_title'] = array(
			'view' => 'h3',
			'title' => pn_role_title($place),
			'submit' => __('Save','pn'),
			'colspan' => 2,
		);
		$options[] = array(
			'view' => 'hidden_input',
			'name' => 'role',
			'default' => $place,
		);		
		
		if(is_array($pn_caps)){
			foreach($pn_caps as $key => $val){			
				$default = 0;
				if(isset($capabilities[$key])){
					$default = 1;	
				}		
				$options[$key] = array(
					'view' => 'select',
					'title' => $val,
					'options' => array('0'=>__('No','pn'), '1'=>__('Yes','pn')),
					'default' => $default,
					'name' => $key,
					'work' => 'int',
				);									
				if($key == 'delete_published_pages'){
					$options[] = array(
						'view' => 'line',
						'colspan' => 2,
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
} 


/* обработка */
add_action('premium_action_pn_roles','def_premium_action_pn_roles');
function def_premium_action_pn_roles(){
global $wpdb;	

	only_post();

	pn_only_caps(array('administrator'));
		
	$role = is_param_post('role');
	$prefix = $wpdb->prefix;
	
	global $wp_roles;
	if (!isset($wp_roles)){
		$wp_roles = new WP_Roles();
	}
	
	$roles = array();	
	if(isset($wp_roles)){
		foreach($wp_roles->role_names as $role_key => $name){
			if($role_key != 'administrator'){
				$roles[] = $role_key;
			}	
		}
	}			
	
	if(in_array($role,$roles)){ 
		$pn_caps = get_pn_capabilities();
		$capabilities = array('level_0' => '1');

		foreach($pn_caps as $key => $val){
			$value = is_param_post($key);
			if($value == 1){	
				$capabilities[$key] = 1;
			}
		} 
				
		$roles = get_option($prefix. 'user_roles');
		$roles[$role]['capabilities'] = $capabilities;
		$roles = serialize($roles);
		$wpdb->update( $prefix.'options' , array('option_value' => $roles), array('option_name' => $prefix.'user_roles'));
				
		$back_url = is_param_post('_wp_http_referer');
		$back_url .= '&reply=true';
			
		wp_safe_redirect($back_url);
		exit;			
			
	} else {
		pn_display_mess(__('Error! This role do not exist!','pn'));
	}
}	