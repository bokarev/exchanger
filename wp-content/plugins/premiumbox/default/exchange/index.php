<?php
if( !defined( 'ABSPATH')){ exit(); }
	
function the_exchange_home() {
	echo get_exchange_table();
}
	
function get_exchange_table($def_cur_from='', $def_cur_to=''){
global $wpdb;	
	
	$temp = '';
	
	$arr = array(
		'from' => $def_cur_from,
		'to' => $def_cur_to,
	);
	$arr = apply_filters('get_exchange_table_vtypes', $arr, 'web');
	
	$show_data = pn_exchanges_output('home');
	
	if($show_data['text']){
		$temp .= '<div class="home_resultfalse"><div class="home_resultfalse_close">'. $show_data['text'] .'</div></div>';
	}	
	
	if($show_data['mode'] == 1){
		$type_table = get_type_table();
		$html = apply_filters('exchange_table_type', '', $type_table ,$arr['from'] ,$arr['to']);
		$temp .= apply_filters('exchange_table_type' . $type_table, $html ,$arr['from'] ,$arr['to']);
	} 	
	
	return $temp;
}	

add_filter('exchange_input', 'def_exchange_input', 10, 8);
function def_exchange_input($html, $place, $cdata, $vd1, $vd2, $naps, $user_id, $post_sum){
	
	$dis1 = $dis1c = $dis2 = $dis2c = '';
	if($cdata['dis1'] == 1){ $dis1 = 'disabled="disabled"'; }
	if($cdata['dis1c'] == 1){ $dis1c = 'disabled="disabled"'; }
	if($cdata['dis2'] == 1){ $dis2 = 'disabled="disabled"'; }
	if($cdata['dis2c'] == 1){ $dis2c = 'disabled="disabled"'; }	
	
	$summ1 = $cdata['summ1'];
	$summ1c = $cdata['summ1c'];
	$summ2 = $cdata['summ2'];
	$summ2c = $cdata['summ2c'];		
	
	if($place == 'give'){
		$html = '<input type="text" name="sum1" '. $dis1 .' cash-id="sum1" class="js_summ1 cache_data" value="'. $summ1 .'" />';
	} elseif($place == 'give_com'){
		$html = '<input type="text" name="" '. $dis1c .' class="js_summ1c" value="'. $summ1c .'" />';
	} elseif($place == 'get'){
		$html = '<input type="text" name="" '. $dis2 .' class="js_summ2" value="'. $summ2 .'" />';
	} elseif($place == 'get_com'){	
		$html = '<input type="text" name="" '. $dis2c .' class="js_summ2c" value="'. $summ2c .'" /> ';
	}
	
	return $html;
}

global $premiumbox; 
$premiumbox->include_patch(__FILE__, 'function'); 
$premiumbox->include_patch(__FILE__, 'peremen'); 
$premiumbox->include_patch(__FILE__, 'action');
$premiumbox->include_patch(__FILE__, 'cron');
$premiumbox->include_patch(__FILE__, 'table1'); 
$premiumbox->include_patch(__FILE__, 'table2'); 
$premiumbox->include_patch(__FILE__, 'table3'); 
$premiumbox->include_patch(__FILE__, 'table4'); 
$premiumbox->include_patch(__FILE__, 'widget'); 
$premiumbox->include_patch(__FILE__, 'mails');