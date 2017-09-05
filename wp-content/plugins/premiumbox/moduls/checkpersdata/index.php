<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Галочка принятия правил в формах[:ru_RU][en_US:]Check mark of accepting the terms and conditions in the forms[:en_US]
description: [ru_RU:]Галочка принятия правил в формах[:ru_RU][en_US:]Check mark of accepting the terms and conditions in the forms[:en_US]
version: 1.0
category: [ru_RU:]Безопасность[:ru_RU][en_US:]Security[:en_US]
cat: secur
*/

add_filter('pn_tech_pages', 'list_tech_pages_checkpersdata');
function list_tech_pages_checkpersdata($pages){
 
	$pages[] = array(
        'post_name'      => 'terms_personal_data',
        'post_title'     => '[ru_RU:]Пользовательское соглашение по обработке персональных данных[:ru_RU][en_US:]User agreement for personal data processing[:en_US]',
		'post_content'   => '',
		'post_template'   => 'pn-pluginpage.php',
	);		
	
	return $pages;
}

add_filter('get_form_filelds','get_form_filelds_checkpersdata', 10, 2);
function get_form_filelds_checkpersdata($items, $name){
	if($name == 'contactform' or $name == 'reviewsform'){
		$items['terms_personal_data'] = array(
			'type' => 'terms_personal_data',
		);
	}
	return $items;
}

add_filter('form_field_line','form_field_line_checkpersdata', 10, 3);
function form_field_line_checkpersdata($line, $filter, $data){
global $premiumbox;
	
	$type = trim(is_isset($data, 'type'));
	if($type == 'terms_personal_data'){
		$line = '<div class="checkbox"><input type="checkbox" name="tpd" value="1" /> '. sprintf(__('I consent to processing of my personal data in accordance with Law No. 152-FZ "On Personal Data" and accept the terms and conditions of the <a href="%s">User Agreement</a>.','pn'), $premiumbox->get_page('terms_personal_data')) .'</div>';	
	}
	
	return $line;
}

add_filter('before_ajax_form_field','before_ajax_form_field_checkpersdata', 99, 2);
function before_ajax_form_field_checkpersdata($logs, $name){
global $premiumbox, $wpdb;	

	if($name == 'contactform' or $name == 'reviewsform'){
		$tpd = intval(is_param_post('tpd'));
		if(!$tpd){ 
			$logs['status']	= 'error';
			$logs['status_code'] = '1'; 
			$logs['status_text'] = __('Error! You have not accepted the terms and conditions of the User Agreement','pn');
			echo json_encode($logs);	
			exit;
		}		
	}
	
	return $logs;
}