<?php
if( !defined( 'ABSPATH')){ exit(); }

/* 
Подключаем к меню
*/
add_action('admin_menu', 'admin_menu_theme_footer');
function admin_menu_theme_footer(){
global $premiumbox;	
	
	add_submenu_page("pn_themeconfig", __('Footer','pntheme'), __('Footer','pntheme'), 'administrator', "pn_theme_footer", array($premiumbox, 'admin_temp'));
}

add_action('pn_adminpage_title_pn_theme_footer', 'def_adminpage_title_pn_theme_footer');
function def_adminpage_title_pn_theme_footer($page){
	_e('Footer','pntheme');
} 

/* настройки */
add_action('pn_adminpage_content_pn_theme_footer','def_pn_adminpage_content_pn_theme_footer');
function def_pn_adminpage_content_pn_theme_footer(){
	$change = get_option('f_change');
	
	$options = array();
	$options['top_title'] = array(
		'view' => 'h3',
		'title' => __('Footer','pntheme'),
		'submit' => __('Save','pntheme'),
		'colspan' => 2,
	);
	
	$options['ctext'] = array(
		'view' => 'textarea',
		'title' => __('Copywriting','pntheme'),
		'default' => is_isset($change,'ctext'),
		'name' => 'ctext',
		'width' => '',
		'height' => '100px',
		'work' => 'text',
		'ml' => 1,
	);

	$options['timetable'] = array(
		'view' => 'textarea',
		'title' => __('Timetable','pntheme'),
		'default' => is_isset($change,'timetable'),
		'name' => 'timetable',
		'width' => '',
		'height' => '100px',
		'work' => 'text',
		'ml' => 1,
	);	
	
	$options['line1'] = array(
		'view' => 'line',
		'colspan' => 2,
	);	
	
	$options['phone'] = array(
		'view' => 'inputbig',
		'title' => __('Phone', 'pntheme'),
		'default' => is_isset($change,'phone'),
		'name' => 'phone',
		'work' => 'input',
		'ml' => 1,
	);

	$options['line2'] = array(
		'view' => 'line',
		'colspan' => 2,
	);	
	
	$options['vk'] = array(
		'view' => 'inputbig',
		'title' => __('Link to Vk.com','pntheme'),
		'default' => is_isset($change,'vk'),
		'name' => 'vk',
		'work' => 'input',
	);
	
	$options['fb'] = array(
		'view' => 'inputbig',
		'title' => __('Link to Facebook','pntheme'),
		'default' => is_isset($change,'fb'),
		'name' => 'fb',
		'work' => 'input',
	);
	
	$options['gp'] = array(
		'view' => 'inputbig',
		'title' => __('Link to Google+','pntheme'),
		'default' => is_isset($change,'gp'),
		'name' => 'gp',
		'work' => 'input',
	);
	
	$options['tw'] = array(
		'view' => 'inputbig',
		'title' => __('Link to Twitter','pntheme'),
		'default' => is_isset($change,'tw'),
		'name' => 'tw',
		'work' => 'input',
	);	
	
	$help = '
	<p>'. __('If you plan to use links as social buttons, use the following shortcode','pntheme') .'<br />
	<input type="text" name="" value="[soc_link]" onclick="this.select()" /></p>';
	
	$options['newpanel_help'] = array(
		'view' => 'help',
		'title' => __('Info','pntheme'),
		'default' => $help,
	);	
	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pntheme'),
		'colspan' => 2,
	);
	
	pn_admin_one_screen('', $options);	
} 

/* обработка */
add_action('premium_action_pn_theme_footer','def_premium_action_pn_theme_footer');
function def_premium_action_pn_theme_footer(){
global $wpdb;	

	only_post();

	pn_only_caps(array('administrator'));

	$options = array();
	$options['ctext'] = array(
		'name' => 'ctext',
		'work' => 'text',
		'ml' => 1,
	);
	$options['timetable'] = array(
		'name' => 'timetable',
		'work' => 'text',
		'ml' => 1,
	);	
	$options['phone'] = array(
		'name' => 'phone',
		'work' => 'input',
		'ml' => 1,
	);
	$options['vk'] = array(
		'name' => 'vk',
		'work' => 'input',
	);
	$options['fb'] = array(
		'name' => 'fb',
		'work' => 'input',
	);
	$options['gp'] = array(
		'name' => 'gp',
		'work' => 'input',
	);
	$options['tw'] = array(
		'name' => 'tw',
		'work' => 'input',
	);	
	
	$data = pn_strip_options('', $options, 'post');
	
	$change = get_option('f_change');
	if(!is_array($change)){ $change = array(); }
	
	$change['ctext'] = $data['ctext']; 
	$change['timetable'] = $data['timetable']; 
	$change['phone'] = $data['phone']; 
	$change['vk'] = $data['vk'];
	$change['fb'] = $data['fb'];
	$change['gp'] = $data['gp'];
	$change['tw'] = $data['tw'];	
	
	update_option('f_change',$change);
	
	$back_url = is_param_post('_wp_http_referer');
	$back_url .= '&reply=true';
			
	wp_safe_redirect($back_url);
	exit;	
}