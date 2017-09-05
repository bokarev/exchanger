<?php
if( !defined( 'ABSPATH')){ exit(); }

/* удаляем язык из стандартных настроек */
add_filter( 'whitelist_options', 'lang_whitelist_options' );
function lang_whitelist_options($whitelist_options){
	if(isset($whitelist_options['general'])){	
		$key = array_search('WPLANG',$whitelist_options['general']);
		if(isset($whitelist_options['general'][$key])){
			unset($whitelist_options['general'][$key]);
		}
		$key = array_search('blogname',$whitelist_options['general']);
		if(isset($whitelist_options['general'][$key])){
			unset($whitelist_options['general'][$key]);
		}
		$key = array_search('blogdescription',$whitelist_options['general']);
		if(isset($whitelist_options['general'][$key])){
			unset($whitelist_options['general'][$key]);
		}			
	}
	return $whitelist_options;
}

add_action('admin_footer', 'standart_admin_lang_footer');
function standart_admin_lang_footer(){
	$screen = get_current_screen();
	if($screen->id == 'options-general'){
		?>
		<script type="text/javascript">
		jQuery(function($){
			$('#WPLANG').parents('tr').hide();
			$('#blogname').parents('tr').hide();
			$('#blogdescription').parents('tr').hide();
		});
		</script>
		<?php
	}
}
/* end удаляем язык из стандартных настроек */

/* заголовок */
add_action('pn_adminpage_title_pn_config', 'def_adminpage_title_pn_config');
function def_adminpage_title_pn_config($page){
	_e('General settings','pn');
} 

/* настройки */
add_action('pn_adminpage_content_pn_config','def_adminpage_content_pn_config');
function def_adminpage_content_pn_config(){
global $wpdb, $premiumbox;

	$options = array();
	$options['top_title'] = array(
		'view' => 'h3',
		'title' => __('General settings','pn'),
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	
	$options['up_mode'] = array(
		'view' => 'select',
		'title' => __('Updating mode','pn'),
		'options' => array('0'=>__('No','pn'), '1'=>__('Yes','pn')),
		'default' => $premiumbox->get_option('up_mode'),
		'name' => 'up_mode',
		'work' => 'int',
	);	
	
	$options['line1'] = array(
		'view' => 'line',
		'colspan' => 2,
	);	
	
	$row = $wpdb->get_row( "SELECT option_value FROM ". $wpdb->prefix ."options WHERE option_name = 'blogname'");
	$options['blogname'] = array(
		'view' => 'inputbig',
		'title' => __('Website Title','pn'),
		'default' => $row->option_value,
		'name' => 'blogname',
		'work' => 'input',
		'ml' => 1,
	);

	$row = $wpdb->get_row( "SELECT option_value FROM ". $wpdb->prefix ."options WHERE option_name = 'blogdescription'");
	$options['blogdescription'] = array(
		'view' => 'inputbig',
		'title' => __('Description'),
		'default' => $row->option_value,
		'name' => 'blogdescription',
		'work' => 'input',
		'ml' => 1,
	);	
	
	$options['line2'] = array(
		'view' => 'line',
		'colspan' => 2,
	);	
	
	$options['newpanel'] = array(
		'view' => 'inputbig',
		'title' => __('Admin panel URL', 'pn'),
		'default' => $premiumbox->get_option('admin_panel_url'),
		'name' => 'admin_panel_url',
		'work' => 'input',
	);	
	
	$options['newpanel_help'] = array(
		'view' => 'help',
		'title' => __('More info','pn'),
		'default' => __('Enter new URL to enter the admin panel. Use only lowercase letters and numbers. Be sure to remember the entered address!','pn'),
	);
	
	$options[] = array(
		'view' => 'line',
		'colspan' => 2,
	);			
	
	$options['adminpass'] = array(
		'view' => 'select',
		'title' => __('Remember successful entry of the security code','pn'),
		'options' => array('0'=>__('No','pn'), '1'=>__('Yes','pn')),
		'default' => $premiumbox->get_option('adminpass'),
		'name' => 'adminpass',
		'work' => 'int',
	);

	$options[] = array(
		'view' => 'line',
		'colspan' => 2,
	);

	$options['nocopydata'] = array(
		'view' => 'select',
		'title' => __('Ability to copy information on clients in one click','pn'),
		'options' => array('0'=>__('Yes','pn'), '1'=>__('No','pn')),
		'default' => $premiumbox->get_option('nocopydata'),
		'name' => 'nocopydata',
		'work' => 'int',
	);	
	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('pn_config_option', $options);
	
} 

/* обработка */
add_action('premium_action_pn_config','def_premium_action_pn_config');
function def_premium_action_pn_config(){
global $wpdb, $premiumbox;	

	only_post();
	pn_only_caps(array('administrator'));
	
	$options = array();
	$options['up_mode'] = array(
		'name' => 'up_mode',
		'work' => 'int',
	);		
	$options['blogname'] = array(
		'name' => 'blogname',
		'work' => 'input',
		'ml' => 1,
	);
	$options['blogdescription'] = array(
		'name' => 'blogdescription',
		'work' => 'input',
		'ml' => 1,
	);	
	$options['newpanel'] = array(
		'name' => 'admin_panel_url',
		'work' => 'input',
	);	
	$options['adminpass'] = array(
		'name' => 'adminpass',
		'work' => 'int',
	);
	$options['nocopydata'] = array(
		'name' => 'nocopydata',
		'work' => 'int',
	);		
	$data = pn_strip_options('pn_config_option', $options, 'post');
	
	update_option('blogname', $data['blogname']);
	update_option('blogdescription', $data['blogdescription']);
	$opts =  array('up_mode','adminpass', 'nocopydata'); 
	foreach($opts as $opt){
		$premiumbox->update_option($opt,'',$data[$opt]);
	}
	
	$admin_panel_url = is_admin_newurl($data['admin_panel_url']);
	$premiumbox->update_option('admin_panel_url','',$admin_panel_url);
	
	do_action('pn_config_option_post', $data);			
	
	$back_url = is_param_post('_wp_http_referer');
	$back_url .= '&reply=true';
			
	wp_safe_redirect($back_url);
	exit;			
}