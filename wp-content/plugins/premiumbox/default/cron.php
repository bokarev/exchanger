<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_caps', 'pn_caps_cron');
function pn_caps_cron($pn_caps){
	
	$pn_caps['pn_test_cron'] = __('Test cron tasks','pn');
	
	return $pn_caps;	
}

/* 
Подключаем к меню
*/
add_action('admin_menu', 'admin_menu_cron');
function admin_menu_cron(){
global $premiumbox;	
	if(current_user_can('administrator') or current_user_can('pn_test_cron')){
		add_submenu_page("pn_config", __('Cron','pn'), __('Cron','pn'), 'read', "pn_cron", array($premiumbox, 'admin_temp'));
	}
}

add_action('pn_adminpage_title_pn_cron', 'def_pn_adminpage_title_pn_cron');
function def_pn_adminpage_title_pn_cron($page){
	_e('Cron','pn');
} 

/* настройки */
add_action('pn_adminpage_content_pn_cron','def_pn_adminpage_content_pn_cron');
function def_pn_adminpage_content_pn_cron(){
global $premiumbox;

	$site_url = get_site_url_or();
	$text = __('If for some reason your tasks do not work then you can use direct link','pn').'<br />
	<a href="'. $site_url .'/cron.html'. get_hash_cron('?') .'" target="_blank">'. $site_url .'/cron.html'. get_hash_cron('?') .'</a>
	';
	pn_admin_substrate($text);
	
	$options = array();
	$options['top_title'] = array(
		'view' => 'h3',
		'title' => __('Cron settings','pn'),
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	
	$options['cron'] = array(
		'view' => 'select',
		'title' => __('Cron','pn'),
		'options' => array('0'=>__('Always','pn'), '1'=>__('In a special file','pn')),
		'default' => $premiumbox->get_option('cron'),
		'name' => 'cron',
		'work' => 'int',
	);	
	
	$options['cron_help'] = array(
		'view' => 'help',
		'title' => __('More info','pn'),
		'default' => __('If you are using server version of cron, then select "In a special file."','pn'),
	);	
	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('pn_cron_option', $options, '', pn_link_post('pn_cron_settings'));	
?>

<div class="premium_body">
	<table class="premium_standart_table">
		<?php
			pn_h3(__('Cron tasks','pn'), '');	
		?>
	</table>
		
	<?php 
	$cronsite = get_option('the_cron');
	
	$cron = array(
		'now' => __('When handling','pn'),
		'2min' => __('Interval 2 minutes','pn'),
		'5min' => __('Interval 5 minutes','pn'),
		'10min' => __('Interval 10 minutes','pn'),
		'30min' => __('Interval 30 minutes','pn'),
		'1hour' => __('Interval 1 hour','pn'),
		'3hour' => __('Interval 3 hours','pn'),
		'05day' => __('Interval half of a day','pn'),
		'1day' => __('Interval 1 day','pn'),
	);		
	
	foreach($cron as $key => $title){ 
		$time = __('none','pn');
		if(isset($cronsite[$key])){
			$time = date('d.m.Y H:i:s', $cronsite[$key]);	
		}
		
		$cron_func = apply_filters('mycron_'.$key, array());
		if(!is_array($cron_func)){ $cron_func = array(); }
		
		if(count($cron_func) > 0){
	?>
	<div class="crontab">
		<form method="post" action="<?php pn_the_link_post(); ?>">
			<input type="hidden" name="action" value="<?php echo $key; ?>" />
			<?php wp_referer_field(); ?>
			<div class="crontitle"><?php echo $title; ?></div>
			<div class="cronmeta"><span class="crontime"><?php echo $time; ?></span> <input type="submit" name="submit" class="button" value="<?php _e('Run','pn'); ?>" /></div>
		</form>
		
		<div class="cronjobdiv">	
			
			<?php foreach($cron_func as $func => $title){ ?>
				<form method="post" action="<?php pn_the_link_post('pn_cron_func'); ?>">
					<?php wp_referer_field(); ?>
					<input type="hidden" name="action" value="<?php echo $func; ?>" />
							
					<div class="cronjob"><?php echo $title; ?></div>
					<div class="cronjob_action">
						<input type="submit" name="submit" class="button" value="<?php _e('Run','pn'); ?>" />
									
						<a href="<?php echo $site_url;?>/cron-<?php echo $func;?>.html<?php echo get_hash_cron('?'); ?>" class="button" target="_blank"><?php _e('Cron file','pn'); ?></a>
					</div>
				</form>
			<?php } ?>			
			
		</div>	
	</div>
		<?php } 
	} ?>
</div>		
<?php
}

/* обработка */
add_action('premium_action_pn_cron','def_premium_action_pn_cron');
function def_premium_action_pn_cron(){
global $wpdb;	
	
	only_post();
	pn_only_caps(array('administrator','pn_test_cron'));
	
		$action = strip_tags(is_param_post('action'));
		$def = array('now','2min','5min','10min','30min','1hour','3hour','05day','1day');
		if(in_array($action,$def)){ 
	
			$time = current_time('timestamp');
			$cronsite = get_option('the_cron');
			$cronsite[$action] = $time;
			update_option('the_cron',$cronsite);
	
			go_pn_cron($action);

		} else {
			pn_display_mess(__('Error! Invalid command!','pn'));
		}
	
	$back_url = is_param_post('_wp_http_referer');
	$back_url .= '&reply=true';
			
	wp_safe_redirect($back_url);
	exit;	
}

add_action('premium_action_pn_cron_settings','def_premium_action_pn_cron_settings');
function def_premium_action_pn_cron_settings(){
global $wpdb, $premiumbox;	

	only_post();
	pn_only_caps(array('administrator','pn_test_cron'));
	
	$cron = intval(is_param_post('cron'));
	$premiumbox->update_option('cron','', $cron);		
	
	$back_url = is_param_post('_wp_http_referer');
	$back_url .= '&reply=true';
			
	wp_safe_redirect($back_url);
	exit;
}

add_action('premium_action_pn_cron_func','def_premium_action_pn_cron_func');
function def_premium_action_pn_cron_func(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_test_cron'));
	
		$action = strip_tags(is_param_post('action'));
		go_pn_cron_func($action);	
	
	$back_url = is_param_post('_wp_http_referer');
	$back_url .= '&reply=true';
			
	wp_safe_redirect($back_url);
	exit;
}