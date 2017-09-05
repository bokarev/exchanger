<?php
if( !defined( 'ABSPATH')){ exit(); }
 
add_filter('pn_caps','merchants_pn_caps');
function merchants_pn_caps($pn_caps){
	$pn_caps['pn_merchants'] = __('Work with merchants','pn');
	return $pn_caps;
}

/* 
Подключаем к меню
*/
add_action('admin_menu', 'pn_adminpage_merchants');
function pn_adminpage_merchants(){
global $premiumbox;	

	if(current_user_can('administrator') or current_user_can('pn_merchants')){
		$hook = add_submenu_page("pn_merchants", __('Merchants','pn'), __('Merchants','pn'), 'read', "pn_merchants", array($premiumbox, 'admin_temp'));
		add_action( "load-$hook", 'pn_trev_hook' );
		add_submenu_page("pn_merchants", __('Merchant settings','pn'), __('Merchant settings','pn'), 'read', "pn_data_merchants", array($premiumbox, 'admin_temp'));
	
		$hook = add_submenu_page("pn_merchants", __('Automatic payouts','pn'), __('Automatic payouts','pn'), 'read', "pn_paymerchants", array($premiumbox, 'admin_temp'));
		add_action( "load-$hook", 'pn_trev_hook' );
		add_submenu_page("pn_merchants", __('Automatic payout settings','pn'), __('Automatic payout settings','pn'), 'read', "pn_data_paymerchants", array($premiumbox, 'admin_temp'));	
	
		$hook = add_submenu_page("pn_merchants", __('SMS gates','pn'), __('SMS gates','pn'), 'read', "pn_smsgate", array($premiumbox, 'admin_temp'));
		add_action( "load-$hook", 'pn_trev_hook' );
		add_submenu_page("pn_merchants", __('SMS gate settings','pn'), __('SMS gate settings','pn'), 'read', "pn_data_smsgate", array($premiumbox, 'admin_temp'));
	
		$hook = add_submenu_page("pn_merchants", __('Accounts verification checker','pn'), __('Accounts verification checker','pn'), 'read', "pn_wchecks", array($premiumbox, 'admin_temp'));
		add_action( "load-$hook", 'pn_trev_hook' );
		add_submenu_page("pn_merchants", __('Accounts verification checker settings','pn'), __('Accounts verification checker settings','pn'), 'read', "pn_data_wchecks", array($premiumbox, 'admin_temp'));	
	}
}

//is_extension_name
global $premiumbox;
$premiumbox->include_patch(__FILE__, 'temps');
$premiumbox->include_patch(__FILE__, 'merch_func');
$premiumbox->include_patch(__FILE__, 'paymerch_func'); 
$premiumbox->include_patch(__FILE__, 'smsgate_func');
$premiumbox->include_patch(__FILE__, 'wchecks_func'); 
$premiumbox->include_patch(__FILE__, 'merchants'); 
$premiumbox->include_patch(__FILE__, 'paymerchants'); 
$premiumbox->include_patch(__FILE__, 'smsgate');
$premiumbox->include_patch(__FILE__, 'wchecks');
$premiumbox->include_patch(__FILE__, 'instruction'); 
$premiumbox->include_patch(__FILE__, 'datasmsgate');
$premiumbox->include_patch(__FILE__, 'datawchecks');
$premiumbox->include_patch(__FILE__, 'timeout_ap');