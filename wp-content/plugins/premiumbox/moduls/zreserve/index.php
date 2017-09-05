<?php 
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Запрос резерва[:ru_RU][en_US:]Request for reserve[:en_US]
description: [ru_RU:]Запрос резерва[:ru_RU][en_US:]Request for reserve[:en_US]
version: 1.0
category: [ru_RU:]Направления обменов[:ru_RU][en_US:]Exchange directions[:en_US]
cat: naps
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* BD */
add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_zresrve');
function bd_pn_moduls_active_zresrve(){
global $wpdb;
	
/*
Запрос резерва

rdate - дата запроса
user_email - e-mail
naps_id - id направления
amount - сумма запроса
comment - комментарий
*/
	$table_name= $wpdb->prefix ."reserve_requests";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT ,
		`rdate` datetime NOT NULL,
		`user_email` varchar(250) NOT NULL,
		`naps_id` bigint(20) NOT NULL default '0',
		`naps_title` longtext NOT NULL,
		`amount` varchar(250) NOT NULL,
		`comment` longtext NOT NULL,
		`locale` varchar(250) NOT NULL,
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);
	
}
/* end BD */

add_action('admin_menu', 'pn_adminpage_zreserv');
function pn_adminpage_zreserv(){
global $premiumbox;		
	if(current_user_can('administrator') or current_user_can('pn_zreserv')){
		$hook = add_menu_page( __('Reserve requests','pn'), __('Reserve requests','pn'), 'read', "pn_zreserv", array($premiumbox, 'admin_temp'), $premiumbox->get_icon_link('zreserve'));	
		add_action( "load-$hook", 'pn_trev_hook' );
	}
}

add_filter('pn_caps','zreserv_pn_caps');
function zreserv_pn_caps($pn_caps){
	$pn_caps['pn_zreserv'] = __('Work with reserve requests','pn');
	return $pn_caps;
}

add_filter('admin_mailtemp','admin_mailtemp_zreserv');
function admin_mailtemp_zreserv($places_admin){
	$places_admin['zreserv_admin'] = __('Reserve request','pn');
	return $places_admin;
}

add_filter('user_mailtemp','user_mailtemp_zreserv');
function user_mailtemp_zreserv($places_admin){
	$places_admin['zreserv'] = __('Reserve request','pn');
	return $places_admin;
}

add_filter('mailtemp_tags_zreserv','def_mailtemp_tags_zreserv');
function def_mailtemp_tags_zreserv($tags){
	$tags['email'] = __('E-mail','pn');
	$tags['sumres'] = __('Amount reserved','pn');
	$tags['sum'] = __('Requested amount','pn');
	$tags['direction'] = __('Direction of Exchange','pn');
	$tags['comment'] = __('Comment','pn');
	return $tags;
}

add_filter('mailtemp_tags_zreserv_admin','def_mailtemp_tags_zreserv_admin');
function def_mailtemp_tags_zreserv_admin($tags){
	$tags['email'] = __('E-mail','pn');
	$tags['sum'] = __('Requested amount','pn');
	$tags['direction'] = __('Direction of Exchange','pn');
	$tags['comment'] = __('Comment','pn');
	return $tags;
}

add_filter('placed_captcha', 'placed_captcha_zreserv');
function placed_captcha_zreserv($placed){
	
	$placed['reservform'] = __('Reserve request','pn');
	
	return $placed;
}

function get_zreserv_form_filelds($place='shortcode'){
	$ui = wp_get_current_user();

	$items = array();
	$items['sum'] = array(
		'name' => 'sum',
		'title' => '',
		'placeholder' => __('Required amount', 'pn'),
		'req' => 1,
		'value' => '',
		'type' => 'input',
		'not_auto' => 0,
	);
	$items['email'] = array(
		'name' => 'email',
		'title' => '',
		'placeholder' => __('E-mail', 'pn'),
		'req' => 1,
		'value' => is_email(is_isset($ui,'user_email')),
		'type' => 'input',
		'not_auto' => 0,
	);		
	$items['comment'] = array(
		'name' => 'comment',
		'title' => '',
		'placeholder' => __('Comment', 'pn'),
		'req' => 0,
		'value' => '', 
		'type' => 'text',
		'not_auto' => 0,
	);	
	$items = apply_filters('get_form_filelds',$items, 'reservform', $ui, $place);
	$items = apply_filters('reserv_form_filelds',$items, $ui, $place);	
	
	return $items;
}

add_action('pn_naps_delete','zreserv_pn_naps_delete',0,2);
function zreserv_pn_naps_delete($id, $item){
global $wpdb;

	$items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."reserve_requests WHERE naps_id = '$id'");
	foreach($items as $item){	
		$item_id = $item->id;
		do_action('pn_zreserv_delete_before', $item_id, $item);
		$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."reserve_requests WHERE id = '$item_id'");
		if($result){
			do_action('pn_zreserv_delete', $item_id, $item);
		}
	}	
}

/* options */
add_filter('pn_exchange_config_option', 'zreserv_exchange_config_option');
function zreserv_exchange_config_option($options){
global $premiumbox;

	if(isset($options['bottom_title'])){
		unset($options['bottom_title']);
	}
	$options['reserv'] = array(
		'view' => 'select',
		'title' => __('Allow reserve request','pn'),
		'options' => array('0'=>__('No','pn'),'1'=>__('Yes','pn')),
		'default' => $premiumbox->get_option('exchange','reserv'),
		'name' => 'reserv',
	);	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);	
	
	return $options;	
}

add_action('pn_exchange_config_option_post', 'zreserv_exchange_config_option_post');
function zreserv_exchange_config_option_post(){
global $premiumbox;
	
	$reserv = intval(is_param_post('reserv'));
	$premiumbox->update_option('exchange', 'reserv', $reserv);
	
}
/* end options */

function is_enable_zreserve(){
global $premiumbox;		
	$en_reserv = intval($premiumbox->get_option('exchange','reserv'));
	return apply_filters('is_enable_zreserve', $en_reserv);
}

/* filters */
add_filter('tbl1_rightcol_data','tbl1_rightcol_data_zreserv', 10, 6); 
function tbl1_rightcol_data_zreserv($data, $naps_data, $vd1, $vd2, $curs, $cur_to){
	if(is_enable_zreserve()){
						
		$v_title1 = get_valut_title($vd1);		
		$v_title2 = get_valut_title($vd2);				
						
		$data['zreserv'] = '
		<div class="xtt_one_line_rez js_reserv" data-id="'. $naps_data->naps_id .'" data-title="'. $v_title1 .'-'. $v_title2 .'">
			<div class="xtt_one_line_rez_ins">
				<span>'. __('Not enough?','pn') .'</span>
			</div>
		</div>														
		';
		
	}
	return $data;													
}
add_filter('tbl2_rightcol_data','tbl2_rightcol_data_zreserv', 10, 7); 
function tbl2_rightcol_data_zreserv($data, $cdata, $vd1, $vd2, $naps, $user_id, $post_sum){
	if(is_enable_zreserve()){
						
		$reserv = is_out_sum(get_naps_reserv($vd2->valut_reserv, $vd2->valut_decimal, $naps), $vd2->valut_decimal, 'reserv');
				
		$v_title1 = get_valut_title($vd1);		
		$v_title2 = get_valut_title($vd2);				
						
		$data['zreserv'] = '
		<div class="xtp_line xtp_exchange_reserve">
			'. __('Reserve','pn') .': <span class="js_reserv_html">'. $reserv .' '. $cdata['vtype2'] .'</span> <a href="#" class="xtp_link js_reserv" data-id="'. $naps->id .'" data-title="'. $v_title1 .'-'. $v_title2 .'">'. __('Not enough?','pn') .'</a> 
		</div>														
		';
		
	}
	return $data;													
}
add_filter('tbl3_rightcol_data','tbl3_rightcol_data_zreserv', 10, 7); 
function tbl3_rightcol_data_zreserv($data, $cdata, $vd1, $vd2, $naps, $user_id, $post_sum){
	if(is_enable_zreserve()){
						
		$reserv = is_out_sum(get_naps_reserv($vd2->valut_reserv, $vd2->valut_decimal, $naps), $vd2->valut_decimal, 'reserv');
				
		$v_title1 = get_valut_title($vd1);		
		$v_title2 = get_valut_title($vd2);				
						
		$data['zreserv'] = '
		<div class="xtl_line xtl_exchange_reserve">
			'. __('Reserve','pn') .': <span class="js_reserv_html">'. $reserv .' '. $cdata['vtype2'] .'</span> <a href="#" class="xtp_link js_reserv" data-id="'. $naps->id .'" data-title="'. $v_title1 .'-'. $v_title2 .'">'. __('Not enough?','pn') .'</a> 
		</div>														
		';
		
	}
	return $data;													
}
/* end filters */
 
add_filter('after_update_valut_reserv','zreserv_update_valut_reserv', 10, 3);
function zreserv_update_valut_reserv($valut_reserv, $valut_id, $item){ 
global $wpdb;
	$valut_id = intval($valut_id);
	$mailtemp = get_option('mailtemp');
	if(isset($mailtemp['zreserv'])){
		$data = $mailtemp['zreserv'];
		if($data['send'] == 1){
			if(isset($item->id)){
				$naps = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."naps WHERE autostatus='1' AND naps_status='1' AND valut_id2 = '$valut_id'");
				foreach($naps as $nap){
					$naps_id = $nap->id;
					$reserv = get_naps_reserv($valut_reserv, $item->valut_decimal, $nap);
					
					$ot_mail = is_email($data['mail']);
					$ot_name = pn_strip_input($data['name']);
				
					$zapros = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."reserve_requests WHERE naps_id='$naps_id' AND amount <= $reserv");
					foreach($zapros as $za){
					
						$zaid = $za->id;
						$wpdb->query("DELETE FROM ".$wpdb->prefix."reserve_requests WHERE id = '$zaid'");
					
						$locale = pn_strip_input($za->locale);
						$direction = get_vtitle($nap->valut_id1) .' &rarr; '. get_vtitle($nap->valut_id2);
					
						$subject = pn_strip_input(ctv_ml($data['title'],$locale));
						$sitename = pn_strip_input(get_bloginfo('sitename'));
						$html = pn_strip_text(ctv_ml($data['text'],$locale));
								
						$user_email = is_email($za->user_email);			
								
						if($user_email){	
							$to_mail = $user_email;
							$sarray = array(
								'[sitename]' => $sitename,
								'[email]' => $user_email,
								'[sumres]' => $reserv,
								'[sum]' => $za->amount,
								'[comment]' => pn_strip_input($za->comment),
								'[direction]' => $direction,
							);							
							$subject = get_replace_arrays($sarray, $subject);								
							$subject = apply_filters('mail_zreserv_subject',$subject, $za);
				
							$html = get_replace_arrays($sarray, $html);
							$html = apply_filters('mail_zreserv_text',$html, $za);
							$html = apply_filters('comment_text',$html);
														
							pn_mail($user_email, $subject, $html, $ot_name, $ot_mail);	 
						}					
					}
				}
			}
		}
	}
	return $valut_reserv;
}

add_action('wp_before_admin_bar_render', 'wp_before_admin_bar_render_zreserv');
function wp_before_admin_bar_render_zreserv(){
global $wp_admin_bar, $wpdb, $premiumbox;
	
    if(current_user_can('administrator') or current_user_can('pn_zreserv')){
		$z1 = $wpdb->query("SELECT id FROM ".$wpdb->prefix."reserve_requests");
		if($z1 > 0){
			$wp_admin_bar->add_menu( array(
			'id'     => 'new_zreserve',
			'href' => admin_url('admin.php?page=pn_zreserv'),
			'title'  => '<div style="height: 32px; width: 22px; background: url('. $premiumbox->plugin_url .'moduls/zreserve/images/zreserv.png) no-repeat center center"></div>',
				'meta' => array( 
					'title' => sprintf(__('Reserve requests (%s)','pn'), $z1) 
				)		
			));	
		}	
	}
	
}

global $premiumbox;
$premiumbox->include_patch(__FILE__, 'list');
$premiumbox->include_patch(__FILE__, 'window');