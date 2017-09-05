<?php
if( !defined( 'ABSPATH')){ exit(); }

function the_exchange_home_mobile() {
	echo get_exchange_table_mobile();
}
	
function get_exchange_table_mobile($def_cur_from='', $def_cur_to=''){
global $wpdb;	
	
	$temp = '';
	
	$arr = array(
		'from' => $def_cur_from,
		'to' => $def_cur_to,
	);
	$arr = apply_filters('get_exchange_table_vtypes', $arr, 'mobile');
	
	$show_data = pn_exchanges_output('home');
	
	if($show_data['text']){
		$temp .= '<div class="home_resultfalse"><div class="home_resultfalse_close">'. $show_data['text'] .'</div></div>';
	}	
	
	if($show_data['mode'] == 1){
		$type_table = get_mobile_type_table();
		$html = apply_filters('exchange_mobile_table_type', '', $type_table ,$arr['from'] ,$arr['to']);
		$temp .= apply_filters('exchange_mobile_table_type' . $type_table, $html ,$arr['from'] ,$arr['to']);
	} 	
	
	return $temp;
}

if(is_mobile()){
	remove_action('siteplace_js','siteplace_js_exchange_table1');
	remove_action('siteplace_js','siteplace_js_exchange_table2');
	remove_action('siteplace_js','siteplace_js_exchange_table3');
	remove_action('siteplace_js','siteplace_js_exchange_table4');
}

global $premiumbox;
$premiumbox->include_patch(__FILE__, 'table1');
$premiumbox->include_patch(__FILE__, 'table2');
$premiumbox->include_patch(__FILE__, 'table3');