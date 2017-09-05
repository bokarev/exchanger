<?php
if( !defined( 'ABSPATH')){ exit(); }
 
/* 
Подключаем к меню
*/
add_action('admin_menu', 'pn_adminpage_exchange_config');
function pn_adminpage_exchange_config(){
global $premiumbox;	
	add_submenu_page("pn_config", __('Exchange settings','pn'), __('Exchange settings','pn'), 'administrator', "pn_exchange_config", array($premiumbox, 'admin_temp'));
}

add_action('pn_adminpage_title_pn_exchange_config', 'pn_adminpage_title_pn_exchange_config');
function pn_adminpage_title_pn_exchange_config($page){
	_e('Exchange settings','pn');
} 

/* настройки */
add_action('pn_adminpage_content_pn_exchange_config','def_pn_adminpage_content_pn_exchange_config');
function def_pn_adminpage_content_pn_exchange_config(){
global $wpdb, $premiumbox;

	$options = array();
	$options['top_title'] = array(
		'view' => 'h3',
		'title' => __('Exchange settings','pn'),
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	$tablevids = array('0'=> sprintf(__('Table %1s','pn'),'1'),'1'=> sprintf(__('Table %1s','pn'),'2'),'2'=> sprintf(__('Table %1s','pn'),'3'),'3'=> sprintf(__('Table %1s','pn'),'4'));
	$tablevids = apply_filters('exchange_tablevids_list', $tablevids);
	$options['tablevid'] = array(
		'view' => 'select',
		'title' => __('Exchange pairs table type','pn'),
		'options' => $tablevids,
		'default' => $premiumbox->get_option('exchange','tablevid'),
		'name' => 'tablevid',
	);		
	$options['tablenot'] = array(
		'view' => 'select',
		'title' => __('If non-existent direction is selected','pn'),
		'options' => array('0'=>__('Show error','pn'),'1'=>__('Show nearest','pn')),
		'default' => $premiumbox->get_option('exchange','tablenot'),
		'name' => 'tablenot',
	);
	$options['tableselect'] = array(
		'view' => 'select',
		'title' => __('Display in exchange form','pn'),
		'options' => array('0'=>__('All currencies','pn'),'1'=>__('Only available currencies for exchange','pn')),
		'default' => $premiumbox->get_option('exchange','tableselect'),
		'name' => 'tableselect',
	);	
	$options[] = array(
		'view' => 'line',
		'colspan' => 2,
	);			
	$options['exch_method'] = array(
		'view' => 'select',
		'title' => __('Exchange type','pn'),
		'options' => array('0'=>__('On a new page','pn'),'1'=>__('On a main page','pn')),
		'default' => $premiumbox->get_option('exchange','exch_method'),
		'name' => 'exch_method',
	);	
	$options[] = array(
		'view' => 'line',
		'colspan' => 2,
	);	
	$options['mhead_style'] = array(
		'view' => 'select',
		'title' => __('Style of page header used for redirecting','pn'),
		'options' => array('0'=>__('White style','pn'),'1'=>__('Black style','pn')),
		'default' => $premiumbox->get_option('exchange','mhead_style'),
		'name' => 'mhead_style',
	);
	$options[] = array(
		'view' => 'line',
		'colspan' => 2,
	);
	$options['m_ins'] = array(
		'view' => 'select',
		'title' => __('If there are no payment instructions given to merchant then','pn'),
		'options' => array('0'=>__('Nothing to be shown','pn'),'1'=>__('Show relevant payment instructions of exchange direction','pn')),
		'default' => $premiumbox->get_option('exchange','m_ins'),
		'name' => 'm_ins',
	);
	$options['mp_ins'] = array(
		'view' => 'select',
		'title' => __('If there are no instructions for automatic payments mode then','pn'),
		'options' => array('0'=>__('Nothing to be shown','pn'),'1'=>__('Show relevant payment instructions of exchange direction','pn')),
		'default' => $premiumbox->get_option('exchange','mp_ins'),
		'name' => 'mp_ins',
	);	
	$options[] = array(
		'view' => 'line',
		'colspan' => 2,
	);	
	$options['allow_dev'] = array(
		'view' => 'select',
		'title' => __('Allow to manage order using another browser','pn'),
		'options' => array('0'=>__('No','pn'),'1'=>__('Yes','pn')),
		'default' => $premiumbox->get_option('exchange','allow_dev'),
		'name' => 'allow_dev',
	);
	$options['ipuserhash'] = array(
		'view' => 'select',
		'title' => __('Forbid managing an order from another IP address','pn'),
		'options' => array('0'=>__('No','pn'),'1'=>__('Yes','pn')),
		'default' => $premiumbox->get_option('exchange','ipuserhash'),
		'name' => 'ipuserhash',
	);	
	$options[] = array(
		'view' => 'line',
		'colspan' => 2,
	);
	$exsum = array(
		'0' => __('Amount To send','pn'),
		'1' => __('Amount To send with add. Fees','pn'),
		'2' => __('Amount To send with add. fees and PS fees','pn'),
		'3' => __('Amount Receive','pn'),
		'4' => __('Amount To receive with add. Fees','pn'),
		'5' => __('Amount To receive with add. fees and PS fees','pn'),
	);	
	$options['exch_exsum'] = array(
		'view' => 'select',
		'title' => __('Amount needed to be exchanged is','pn'),
		'options' => $exsum,
		'default' => $premiumbox->get_option('exchange','exch_exsum'),
		'name' => 'exch_exsum',
	);
	$options['mini_navi'] = array(
		'view' => 'select',
		'title' => __('Disable amount counter of orders in Orders section','pn'),
		'options' => array('0'=>__('No','pn'),'1'=>__('Yes','pn')),
		'default' => $premiumbox->get_option('exchange','mini_navi'),
		'name' => 'mini_navi',
	);	
	$options['admin_mail'] = array(
		'view' => 'select',
		'title' => __('Send e-mail notifications to admin if admin changes status of order on his own','pn'),
		'options' => array('0'=>__('No','pn'),'1'=>__('Yes','pn')),
		'default' => $premiumbox->get_option('exchange','admin_mail'),
		'name' => 'admin_mail',
	);				
	$options[] = array(
		'view' => 'line',
		'colspan' => 2,
	);				
	$options[] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	$options[] = array(
		'view' => 'line',
		'colspan' => 2,
	);
	$options['an1_hidden'] = array(
		'view' => 'select',
		'title' => __('Data visibility in order for Giving','pn'),
		'options' => array('0'=>__('do not show data','pn'),'1'=>__('hide data','pn'),'2'=>__('do not hide first 4 symbols','pn'),'3'=>__('do not hide last 4 symbols','pn'),'4'=>__('do not hide first 4 symbols and the last 4 symbols','pn')),
		'default' => $premiumbox->get_option('exchange','an1_hidden'),
		'name' => 'an1_hidden',
	);
	$options['an2_hidden'] = array(
		'view' => 'select',
		'title' => __('Data visibility in order for Receiving','pn'),
		'options' => array('0'=>__('do not show data','pn'),'1'=>__('hide data','pn'),'2'=>__('do not hide first 4 symbols','pn'),'3'=>__('do not hide last 4 symbols','pn'),'4'=>__('do not hide first 4 symbols and the last 4 symbols','pn')),
		'default' => $premiumbox->get_option('exchange','an2_hidden'),
		'name' => 'an2_hidden',
	);	
	$options[] = array(
		'view' => 'line',
		'colspan' => 2,
	);	
	$options['rateconv'] = array(
		'view' => 'select',
		'title' => __('Use "Converse to"','pn'),
		'options' => array('0'=>__('Everywhere','pn'),'1'=>__('Exchange and rates table','pn'), '2'=> __('Exchange table','pn')),
		'default' => $premiumbox->get_option('exchange','rateconv'),
		'name' => 'rateconv',
	);	
	$options[] = array(
		'view' => 'line',
		'colspan' => 2,
	);
	$options['flysum'] = array(
		'view' => 'select',
		'title' => __('Calculate "in an instant"','pn'),
		'options' => array('0'=>__('No','pn'),'1'=>__('Yes','pn')),
		'default' => $premiumbox->get_option('exchange','flysum'),
		'name' => 'flysum',
	);	
	$options[] = array(
		'view' => 'line',
		'colspan' => 2,
	);	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('pn_exchange_config_option', $options);
} 

/* обработка */
add_action('premium_action_pn_exchange_config','def_premium_action_pn_exchange_config');
function def_premium_action_pn_exchange_config(){
global $wpdb, $premiumbox;	

	only_post();
	pn_only_caps(array('administrator'));

	$options = array('rateconv','tablenot','tableselect','tablevid','exch_method', 'flysum', 'admin_mail','an1_hidden','an2_hidden', 'exch_exsum','allow_dev','ipuserhash', 'mini_navi', 'mhead_style','m_ins','mp_ins');
	foreach($options as $key){
		$val = pn_strip_input(is_param_post($key));
		$premiumbox->update_option('exchange', $key, $val);
	}			
			
	do_action('pn_exchange_config_option_post');
	
	$url = admin_url('admin.php?page=pn_exchange_config&reply=true');
	wp_redirect($url);
	exit;
}