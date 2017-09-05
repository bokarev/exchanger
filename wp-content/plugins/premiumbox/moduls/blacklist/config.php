<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** настройки ************************************************/

add_action('pn_adminpage_title_pn_config_blacklist', 'pn_admin_title_pn_config_blacklist');
function pn_admin_title_pn_config_blacklist(){
	_e('Settings','pn');
}

add_action('pn_adminpage_content_pn_config_blacklist','def_pn_admin_content_pn_config_blacklist');
function def_pn_admin_content_pn_config_blacklist(){
global $premiumbox;
?>
	<div class="premium_default_window">
		<?php _e('Cron URL for updating the black list of details from the checkfraud.info service','pn'); ?><br /> 
		<a href="<?php echo get_site_url_or(); ?>/request-blackping.html<?php echo get_hash_cron('?'); ?>" target="_blank"><?php echo get_site_url_or(); ?>/request-blackping.html<?php echo get_hash_cron('?'); ?></a>
	</div>	
<?php	
	$options = array();	
	$options['top_title'] = array(
		'view' => 'h3',
		'title' => __('Settings','pn'),
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);		
	$options['api'] = array(
		'view' => 'select',
		'title' => __('Enable checkfraud.info API','pn'),
		'options' => array('0'=>__('No','pn'),'1'=>__('Yes','pn')),
		'default' => $premiumbox->get_option('blacklist','api'),
		'name' => 'api',
	);
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('pn_blacklist_configform', $options, '');	
}

add_action('premium_action_pn_config_blacklist','def_premium_action_pn_config_blacklist');
function def_premium_action_pn_config_blacklist(){
global $wpdb, $premiumbox;

	only_post();
	pn_only_caps(array('administrator','pn_blacklist'));	
	
	$options = array('api');		
	foreach($options as $key){
		$premiumbox->update_option('blacklist', $key, intval(is_param_post($key)));
	}

	do_action('pn_blacklist_configform_post');
			
	$url = admin_url('admin.php?page=pn_config_blacklist&reply=true');
	wp_redirect($url);
	exit;
}	