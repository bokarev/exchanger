<?php
/*
Устаревшие функции фреймворка premium 
*/
if( !defined( 'ABSPATH')){ exit(); }

/* 0.8 */
function is_zerois($sum, $num){
global $premiumbox;	
	$premiumbox->_deprecated_function('is_zerois', '0.8', '');
	
	return $sum;
}

function list_old_key_merchant(){
	$array = array();
	$array['1'] = 'perfectmoney';
	$array['2'] = 'liqpay'; 
	$array['3'] = 'webmoney'; 
	$array['4'] = 'yamoney'; 
	$array['5'] = 'privat'; 
	$array['6'] = 'qiwi';
	$array['7'] = 'deltakey'; 
	$array['8'] = 'okpay'; 
	$array['9'] = 'egopay'; 
	$array['10'] = 'paypal'; 
	$array['11'] = 'paymer'; 
	$array['12'] = 'nixmoney'; 
	$array['13'] = 'bitcoin';
	$array['14'] = 'yamoney_card'; 
	$array['15'] = 'zpayment'; 
	$array['16'] = 'payeer'; 
	$array['17'] = 'qiwishop';
	$array['18'] = 'airpay';
	$array['19'] = 'onlymoney'; 
	$array['20'] = 'cash4pay'; 
	$array['21'] = 'ooopay'; 
	$array['22'] = 'paxum'; 
	$array['23'] = 'btce'; 
	$array['24'] = 'webfin';
	$array['25'] = 'helixmoney'; 
	$array['26'] = 'edinar'; 
	$array['27'] = 'capitalist'; 
	$array['28'] = 'livecoin'; 
	$array['29'] = 'blockio';
	$array['30'] = 'advcash'; 
	$array['31'] = 'webmoney2';
	$array['32'] = 'yamoney2'; 
	$array['33'] = 'yamoney2_card'; 
	return $array;
}

function get_guest_visible(){
global $premiumbox;	
	$premiumbox->_deprecated_function('get_guest_visible', '0.8', '');
	
	return 0; 
}

function get_sum_day_valut_in($valut_id){
global $premiumbox;
	$premiumbox->_deprecated_function('get_sum_day_valut_in', '0.8', 'get_sum_valut');
	
	$time = current_time('timestamp');
	$date = date('Y-m-d 00:00:00',$time);
	
	return get_sum_valut($valut_id, 'in', $date);
}

function get_sum_mon_valut_in($valut_id){
global $premiumbox;
	$premiumbox->_deprecated_function('get_sum_mon_valut_in', '0.8', 'get_sum_valut');
	
	$time = current_time('timestamp');
	$date = date('Y-m-01 00:00:00',$time);
	
	return get_sum_valut($valut_id, 'in', $date);	
}

function get_sum_day_valut_out($valut_id){
global $premiumbox;
	$premiumbox->_deprecated_function('get_sum_day_valut_out', '0.8', 'get_sum_valut');
	
	$time = current_time('timestamp');
	$date = date('Y-m-d 00:00:00',$time);
	
	return get_sum_valut($valut_id, 'out', $date);
}

function get_sum_mon_valut_out($valut_id){
global $premiumbox;
	$premiumbox->_deprecated_function('get_sum_mon_valut_out', '0.8', 'get_sum_valut');
	
	$time = current_time('timestamp');
	$date = date('Y-m-01 00:00:00',$time);
	
	return get_sum_valut($valut_id, 'out', $date);	
}

function get_vaccount_inmonth($accountnum){
global $premiumbox;
	$premiumbox->_deprecated_function('get_vaccount_inmonth', '0.8', 'get_vaccount_sum');
	
	$time = current_time('timestamp');
	$date = date('Y-m-01 00:00:00',$time);
	
	return get_vaccount_sum($accountnum, 'in', $date);
}

function get_vaccount_inday($accountnum){
global $premiumbox;
	$premiumbox->_deprecated_function('get_vaccount_inday', '0.8', 'get_vaccount_sum');
	
	$time = current_time('timestamp');
	$date = date('Y-m-d 00:00:00',$time);
	
	return get_vaccount_sum($accountnum, 'in', $date);	
}

/* 0.9 */
function pn_template($page){
global $premiumbox;

	$premiumbox->_deprecated_function('pn_template', '0.9', '$premiumbox->file_include()');
	$premiumbox->file_include($page);
}

function pn_admin_themp($page){
global $premiumbox;

	$premiumbox->_deprecated_function('pn_admin_themp', '0.9', '$premiumbox->admin_temp()');
	$premiumbox->admin_temp();
}

function get_icon_link($icon){
global $premiumbox;

	$premiumbox->_deprecated_function('get_icon_link', '0.9', '$premiumbox->get_icon_link()');
	$premiumbox->get_icon_link($icon);
}

function pn_auto_include($folder){
global $premiumbox;

	$premiumbox->_deprecated_function('pn_auto_include', '0.9', '$premiumbox->auto_include()');
	$premiumbox->auto_include($folder);
}

function get_pn_page($attr){
global $premiumbox;

	$premiumbox->_deprecated_function('get_pn_page', '0.9', '$premiumbox->get_page()');
	return $premiumbox->get_page($attr);
}

function get_change($option='', $option2=''){
global $premiumbox;	

	$premiumbox->_deprecated_function('get_change', '0.9', '$premiumbox->get_option($option, $option2)');
	return $premiumbox->get_option($option, $option2);
}

function update_change($key1='', $key2='', $value){
global $premiumbox;	
	
	$premiumbox->_deprecated_function('update_change', '0.9', '$premiumbox->update_option($key1, $key2, $value)');
	return $premiumbox->update_option($key1, $key2, $value);
}

function the_warning($text, $species='error'){
global $premiumbox;
	
	$premiumbox->_deprecated_function('the_warning', '0.9', 'pn_display_mess($title, $text, $species)');
	
	pn_display_mess($text, $text, $species);
}

function link_tosaved($action=''){
global $premiumbox;
	
	$premiumbox->_deprecated_function('link_tosaved', '0.9', 'pn_link_post');
	return pn_link_post($action);
}

function the_link_tosaved($action=''){
global $premiumbox;	

	$premiumbox->_deprecated_function('the_link_tosaved', '0.9', 'pn_the_link_post');
	echo pn_link_post($action);
}

function link_toajax($action=''){
global $premiumbox;	

	$premiumbox->_deprecated_function('link_toajax', '0.9', 'pn_link_ajax');
	return pn_link_ajax($action);
}

function the_link_toajax($action=''){
global $premiumbox;
	
	$premiumbox->_deprecated_function('the_link_toajax', '0.9', 'pn_the_link_ajax');
	echo pn_link_ajax($action);
}

function the_pn_inputbig_ml($name='', $default=''){
global $premiumbox;	
	$premiumbox->_deprecated_function('the_pn_inputbig_ml', '0.9', 'pn_inputbig_ml');
	
	pn_inputbig_ml('', $name, $default, $class, 0, array('label'=>0));
}

function the_pn_help($title, $content=''){
global $premiumbox;	
	$premiumbox->_deprecated_function('the_pn_help', '0.9', 'pn_help');

	pn_help($title, $content , '', '');
}

function ph_the_uploader($name, $content=''){
global $premiumbox;	
	$premiumbox->_deprecated_function('ph_the_uploader', '0.9', 'pn_uploader');
}

function the_pn_textarea_ml($name='', $content='', $width='', $height='100px'){
global $premiumbox;
	$premiumbox->_deprecated_function('the_pn_textarea_ml', '0.9', 'pn_textarea_ml');
}

function the_pn_editor_ml($name='', $content='', $width='', $height='100px'){
global $premiumbox;	
	$premiumbox->_deprecated_function('the_pn_editor_ml', '0.9', 'pn_editor_ml');
}

function get_merchant_admin_options($m_id, $data){
global $premiumbox;
	$premiumbox->_deprecated_function('get_merchant_admin_options', '0.9', '');
}

function get_paymerchant_admin_options($m_id, $data){
global $premiumbox;
	$premiumbox->_deprecated_function('get_paymerchant_admin_options', '0.9', '');
}

function get_status_in(){
global $premiumbox;
	$premiumbox->_deprecated_function('get_status_in', '0.9', 'get_reserv_status("in")');
	return get_reserv_status("in");
}

function get_status_out(){
global $premiumbox;
	$premiumbox->_deprecated_function('get_status_out', '0.9', 'get_reserv_status("out")');
	return get_reserv_status("out");
}

/* 1.0 */

function update_pn_term_meta($id, $key, $value){
global $premiumbox;	
	$premiumbox->_deprecated_function('update_pn_term_meta', '1.0', 'update_term_meta');
	
	return update_term_meta($id, $key, $value);
}

function get_pn_term_meta($id, $key){
global $premiumbox;	
	$premiumbox->_deprecated_function('get_pn_term_meta', '1.0', 'get_term_meta');	
	
	return get_term_meta($id, $key);
}

function delete_pn_term_meta($id, $key){
global $premiumbox;	
	$premiumbox->_deprecated_function('delete_pn_term_meta', '1.0', 'delete_term_meta');	
	
	return delete_term_meta($id, $key);
}

function is_merchant_id($name){
global $premiumbox;	
	$premiumbox->_deprecated_function('is_merchant_id', '1.0', 'is_extension_name');
	return is_extension_name($name);
}

/* 1.2 */

function pn_allow_second_name(){
global $premiumbox;

	$premiumbox->_deprecated_function('pn_allow_second_name', '1.2', "pn_allow_uv('second_name')");
	return pn_allow_uv('second_name');	
}

function is_modul_name($name){
global $premiumbox;	
	$premiumbox->_deprecated_function('is_modul_name', '1.2', 'is_extension_name');
	return is_extension_name($name);
}

function get_merchant_file($file){
global $premiumbox;

	$premiumbox->_deprecated_function('get_merchant_file', '1.2', 'get_extension_file');
	return get_extension_file($file);	
}

function get_merchant_name($path){
global $premiumbox;

	$premiumbox->_deprecated_function('get_merchant_name', '1.2', 'get_extension_name');
	return get_extension_name($path);		
}

function get_merchant_num($name){
global $premiumbox;

	$premiumbox->_deprecated_function('get_merchant_num', '1.2', 'get_extension_num');
	return get_extension_num($file);	
}

function set_merchant_data($path, $map){
global $premiumbox;

	$premiumbox->_deprecated_function('set_merchant_data', '1.2', 'set_extension_data');
	return set_extension_data($path, $map);	
}	

function get_reserv_status_auto(){
global $premiumbox;

	$premiumbox->_deprecated_function('get_reserv_status_auto', '1.2', 'get_reserv_status("auto")');
	return get_reserv_status('auto');	
}

function get_reserv_status_in(){
global $premiumbox;

	$premiumbox->_deprecated_function('get_reserv_status_in', '1.2', 'get_reserv_status("in")');
	return get_reserv_status('in');
}

function get_reserv_status_out(){
global $premiumbox;

	$premiumbox->_deprecated_function('get_reserv_status_out', '1.2', 'get_reserv_status("out")');
	return get_reserv_status('out');
}

function is_enable_reserv(){
global $premiumbox;

	$premiumbox->_deprecated_function('is_enable_reserv', '1.2', "is_enable_zreserve");	
}

function get_goodly_num($sum, $decimal=2, $place='all'){
global $premiumbox;

	$premiumbox->_deprecated_function('get_goodly_num', '1.2', "is_out_sum");	
	return is_out_sum($sum, $decimal, $place);
}

function the_merchant_bid_payed($id, $sum=0, $purse='', $naschet='', $payment_id='', $system='user'){					
global $premiumbox;
	$premiumbox->_deprecated_function('the_merchant_bid_payed', '1.2', "the_merchant_bid_status");
}

function the_merchant_bid_coldpay($id, $sum=0, $purse='', $naschet='', $payment_id='', $system=''){			
global $premiumbox;
	$premiumbox->_deprecated_function('the_merchant_bid_coldpay', '1.2', "the_merchant_bid_status");
}

function the_merchant_bid_techpay($id, $sum=0, $purse='', $naschet='', $payment_id='', $system=''){			
global $premiumbox;
	$premiumbox->_deprecated_function('the_merchant_bid_techpay', '1.2', "the_merchant_bid_status");
}

function the_paymerchant_bid_success($id, $sum=0, $purse='', $naschet='', $payment_id='', $system=''){			
global $premiumbox;
	$premiumbox->_deprecated_function('the_paymerchant_bid_success', '1.2', "the_merchant_bid_status");
}

function the_paymerchant_bid_coldsuccess($id, $sum=0, $purse='', $naschet='', $payment_id='', $system=''){			
global $premiumbox;
	$premiumbox->_deprecated_function('the_paymerchant_bid_coldsuccess', '1.2', "the_merchant_bid_status");
}