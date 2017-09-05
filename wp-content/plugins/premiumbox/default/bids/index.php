<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('admin_menu', 'pn_adminpage_bids');
function pn_adminpage_bids(){
global $premiumbox;
	if(current_user_can('administrator') or current_user_can('pn_bids')){	
		add_menu_page(__('Orders','pn'), __('Orders','pn'), 'read', "pn_bids", array($premiumbox, 'admin_temp'), $premiumbox->get_icon_link('icon'), 3);
	}
}

add_filter('pn_caps','bids_pn_caps');
function bids_pn_caps($pn_caps){
	$pn_caps['pn_bids'] = __('To process exchange orders','pn');
	$pn_caps['pn_bids_change'] = __('Changing order status','pn');
	$pn_caps['pn_bids_delete'] = __('Complete removal of orders','pn');
	$pn_caps['pn_bids_payouts'] = __('Making payouts by button','pn');
	return $pn_caps;
}

add_action('wp_before_admin_bar_render', 'wp_before_admin_bar_render_bids');
function wp_before_admin_bar_render_bids(){
global $wp_admin_bar, $wpdb, $premiumbox;
    if(current_user_can('administrator') or current_user_can('pn_bids')){
		$page = is_param_get('page');
		if($page != 'pn_bids'){	
			$z = $wpdb->query("SELECT id FROM ".$wpdb->prefix."bids WHERE status IN('payed','realpay','verify')");
			if($z > 0){
				$wp_admin_bar->add_menu( array(
					'id'     => 'new_paybids',
					'href' => admin_url('admin.php?page=pn_bids&idspage=1&paystatus=2'),
					'title'  => '<div style="height: 32px; width: 22px; background: url('. $premiumbox->plugin_url .'images/money.gif) no-repeat center center"></div>',
					'meta' => array( 'title' => __('Orders on hold are to be processed','pn').' ('. $z .')' )		
				));	
			}
		}
	}
}

add_filter('get_statusbids_for_admin', 'get_statusbids_for_admin_remove', 1000);
function get_statusbids_for_admin_remove($st){
	if(current_user_can('administrator') or current_user_can('pn_bids_delete')){
		$st['realdelete'] = array(
			'name' => 'realdelete',
			'title' => __('complete removal','pn'),
			'color' => '#ffffff',
			'background' => '#ff0000',
		);		
	}
	return $st;
}

global $premiumbox;
$premiumbox->include_patch(__FILE__, 'bids');
$premiumbox->include_patch(__FILE__, 'ajax');