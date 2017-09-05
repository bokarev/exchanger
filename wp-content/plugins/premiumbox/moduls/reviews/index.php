<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Отзывы[:ru_RU][en_US:]Reviews[:en_US]
description: [ru_RU:]Отзывы[:ru_RU][en_US:]Reviews[:en_US]
version: 1.0
category: [ru_RU:]Настройки[:ru_RU][en_US:]Settings[:en_US]
cat: sett
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* BD */
add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_reviews');
function bd_pn_moduls_active_reviews(){
global $wpdb;
	
/* 
отзывы

user_id - id пользователя
user_name - имя пользователя
user_email - e-mail пользователя
user_site - сайт пользователя
review_date - дата
review_hash - хэш 
review_status - статус (moderation|publish)
review_locale - локализация
*/
	$table_name = $wpdb->prefix ."reviews";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT ,
		`user_id` bigint(20) NOT NULL default '0',
		`user_name` tinytext NOT NULL,
		`user_email` tinytext NOT NULL,
		`user_site` tinytext NOT NULL,
		`review_date` datetime NOT NULL,
		`review_hash` tinytext NOT NULL,
		`review_text` longtext NOT NULL,		
		`review_status` varchar(150) NOT NULL default 'moderation',
		`review_locale` varchar(10) NOT NULL,
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);

	$table_name= $wpdb->prefix ."reviews_meta";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT ,
		`item_id` bigint(20) NOT NULL default '0',
		`meta_key` longtext NOT NULL,
		`meta_value` longtext NOT NULL,
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);   
	
}

add_filter('pn_tech_pages', 'list_tech_pages_reviews');
function list_tech_pages_reviews($pages){
 
	$pages[] = array(
		'post_name'      => 'reviews',
		'post_title'     => '[ru_RU:]Отзывы[:ru_RU][en_US:]Reviews[:en_US]',
		'post_content'   => '[reviews_page]',
		'post_template'   => 'pn-pluginpage.php',
	);			
	
	return $pages;
}
/* end BD */

add_filter('placed_captcha', 'placed_captcha_reviews');
function placed_captcha_reviews($placed){
	
	$placed['reviewsform'] = __('Add reviews form','pn');
	
	return $placed;
}

function is_reviews_hash($hash){
	$hash = pn_strip_input($hash);
	if (preg_match("/^[a-zA-z0-9]{25}$/", $hash, $matches )) {
		$r = $hash;
	} else {
		$r = 0;
	}
	return $r;
}

function update_reviews_meta($id, $key, $value){ 
	return update_pn_meta('reviews_meta', $id, $key, $value);
}

function get_reviews_meta($id, $key){
	return get_pn_meta('reviews_meta', $id, $key);
}

function delete_reviews_meta($id, $key){
	return delete_pn_meta('reviews_meta', $id, $key);
}

add_action('admin_menu', 'pn_adminpage_reviews');
function pn_adminpage_reviews(){
global $premiumbox;
	
	if(current_user_can('administrator') or current_user_can('pn_reviews')){
		$hook = add_menu_page(__('Reviews','pn'), __('Reviews','pn'), 'read', 'pn_reviews', array($premiumbox, 'admin_temp'), $premiumbox->get_icon_link('reviews'));  
		add_action( "load-$hook", 'pn_trev_hook' );
		add_submenu_page("pn_reviews", __('Add','pn'), __('Add','pn'), 'read', "pn_add_reviews", array($premiumbox, 'admin_temp'));	
		add_submenu_page("pn_reviews", __('Settings','pn'), __('Settings','pn'), 'read', "pn_config_reviews", array($premiumbox, 'admin_temp'));
	}
}

add_filter('pn_caps','reviews_pn_caps');
function reviews_pn_caps($pn_caps){
	$pn_caps['pn_reviews'] = __('Work with reviews','pn');
	return $pn_caps;
} 

/* ссылка на отзыв */
function get_review_link($review_id, $data=''){
global $wpdb, $premiumbox;

	$review_id = intval($review_id);

	if(!is_object($data)){
		$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."reviews WHERE id='$review_id'");
	}
	if(!isset($data->review_date)){
		return '#';
	}

	$review_date = pn_strip_input($data->review_date);
	
    $zcount = intval($premiumbox->get_option('reviews','count')); if($zcount < 1){ $zcount=10; } /* кол-во отзывов на странице */
	
	$reviews_temp = rtrim($premiumbox->get_page('reviews'),'/'); /* страница отзывов */
	$reviews_arr = explode('/',$reviews_temp);
	$reviews_ind = end($reviews_arr);
	
	$deduce = intval($premiumbox->get_option('reviews','deduce'));
	
	$where = '';
	if($deduce == 1){	
		$locale = pn_strip_input($data->review_locale);
		$where = " AND review_locale='$locale'";
		$reviews_page = get_site_url_or() . '/' . get_lang_key($locale) . '/' . $reviews_ind . '/';	
	} else {
		$reviews_page = get_site_url_or() . '/' . $reviews_ind . '/';
	}
	
	$cc = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."reviews WHERE review_status='publish' $where AND id != '$review_id' AND review_date >= '$review_date'"); /* кол-во отзывов после текущего */
	if($cc >= $zcount){ 
	    $pp = floor($cc / $zcount) + 1;
		if($pp > 1){
		    return $reviews_page .'page/'. $pp .'/#review-'. $review_id;
		} 
	} 
	
	return $reviews_page .'#review-'. $review_id;
}

/* вывод отзывов */
function list_reviews($count=5){
global $wpdb, $premiumbox;
	$count = intval($count); if($count < 1){ $count = 5; }
	$deduce = intval($premiumbox->get_option('reviews','deduce'));
	$where = '';
	if($deduce == 1){	
		$locale = get_locale();
		$where = " AND review_locale='$locale'";	
	}	
	return $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."reviews WHERE review_status = 'publish' $where ORDER BY review_date DESC limit $count");	
}

add_filter('admin_mailtemp','admin_mailtemp_reviews');
function admin_mailtemp_reviews($places_admin){
	$places_admin['newreview'] = __('New review','pn');
	return $places_admin;
}

add_filter('user_mailtemp','user_mailtemp_reviews');
function user_mailtemp_reviews($places_admin){
	$places_admin['newreview_auto'] = __('Auto-responder (wew review)','pn');
	$places_admin['confirmreview'] = __('Review confirmation','pn');
	return $places_admin;
}

add_filter('mailtemp_tags_newreview','def_mailtemp_tags_newreview');
function def_mailtemp_tags_newreview($tags){
	$tags['user'] = __('User','pn');
	$tags['management'] = __('Manage a review','pn');
	$tags['status'] = __('Review status','pn');
	return $tags;
}

add_filter('mailtemp_tags_newreview_auto','def_mailtemp_tags_newreview_auto');
function def_mailtemp_tags_newreview_auto($tags){
	$tags['user'] = __('User','pn');
	$tags['status'] = __('Review status','pn');
	return $tags;
}

add_filter('mailtemp_tags_confirmreview','def_mailtemp_tags_confirmreview');
function def_mailtemp_tags_confirmreview($tags){
	$tags['link'] = __('Confirmation Link','pn');
	return $tags;
}

add_action('pn_reviews_delete', 'def_reviews_delete', 10, 2);
function def_reviews_delete($data_id, $item){
global $wpdb;
	
	$items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."reviews_meta WHERE item_id = '$data_id'");
	foreach($items as $item){
		$item_id = $item->id;
		do_action('pn_reviewsmeta_delete_before', $id, $item);
		$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."reviews_meta WHERE id = '$item_id'");
		if($result){
			do_action('pn_reviewsmeta_delete', $id, $item);
		}
	}	
}

add_action('wp_before_admin_bar_render', 'wp_before_admin_bar_render_reviews');
function wp_before_admin_bar_render_reviews() {
global $wp_admin_bar, $wpdb, $premiumbox;
    if(current_user_can('administrator') or current_user_can('pn_reviews')){
		$z = $wpdb->query("SELECT id FROM ".$wpdb->prefix."reviews WHERE review_status='moderation'");
		if($z > 0){
			$wp_admin_bar->add_menu( array(
				'id'     => 'new_review',
				'href' => admin_url('admin.php?page=pn_reviews&mod=2'),
				'title'  => '<div style="height: 32px; width: 22px; background: url('. $premiumbox->plugin_url .'images/reviews.png) no-repeat center center"></div>',
					'meta' => array( 
						'title' => sprintf(__('Unapproved reviews (%s)','pn'), $z) 
					)		
			));	
		}
	}
}

function mailto_add_reviews($review, $status){
	$review_id = intval($review->id); 
	$user_id = intval($review->user_id);
	$user_name = pn_strip_input($review->user_name);
	$user_email = is_email($review->user_email);
	
	if($status=='moderation'){
		$textstatus = __('moderating','pn');
		$management = '[ <a href="'. admin_url('admin.php?page=pn_add_reviews&item_id='.$review_id) .'">'. __('Edit','pn') .'</a> ]';
	} else {
		$textstatus = __('published','pn');		
		$management = '[ <a href="'. admin_url('admin.php?page=pn_add_reviews&item_id='.$review_id) .'">'. __('Edit','pn') .'</a> ] [ <a href="'. get_review_link($review_id, $review) .'">'. __('View','pn') .'</a> ]';
	}		
	
	$mailtemp = get_option('mailtemp');
	if(isset($mailtemp['newreview'])){
		$data = $mailtemp['newreview'];
		if($data['send'] == 1){
			$ot_mail = is_email($data['mail']);
			$ot_name = pn_strip_input($data['name']);
			$sitename = pn_strip_input(get_bloginfo('sitename'));			
			$subject = pn_strip_input(ctv_ml($data['title']));
						
			$html = pn_strip_text(ctv_ml($data['text']));
						
			if($data['tomail']){
						
				$to_mail = $data['tomail'];			
					
				if($user_id){
					$user = '<a href="'. admin_url('user-edit.php?user_id='.$user_id) .'">'.$user_name.'</a>';
				} else {
					$user = $user_name;
				}					
					
				$sarray = array(
					'[sitename]' => $sitename,
					'[user]' => $user,
					'[status]' => $textstatus,
				);							
				$subject = get_replace_arrays($sarray, $subject);				
				$subject = apply_filters('mail_newreview_subject',$subject, $review);
					
				$sarray = array(
					'[sitename]' => $sitename,
					'[user]' => $user,
					'[status]' => $textstatus,
					'[management]' => $management,
				);							
				$html = get_replace_arrays($sarray, $html);				
				$html = apply_filters('mail_newreview_text',$html, $review);
				$html = apply_filters('comment_text',$html);
							
				pn_mail($to_mail, $subject, $html, $ot_name, $ot_mail);	 

			}
		}	
	}
	if(isset($mailtemp['newreview_auto'])){
		$data = $mailtemp['newreview_auto'];
		if($data['send'] == 1){
			$ot_mail = is_email($data['mail']);
			$ot_name = pn_strip_input($data['name']);
			$sitename = pn_strip_input(get_bloginfo('sitename'));			
			$subject = pn_strip_input(ctv_ml($data['title']));
						
			$html = pn_strip_text(ctv_ml($data['text']));
						
			if($user_email){
						
				$user = $user_name;					
					
				$sarray = array(
					'[sitename]' => $sitename,
					'[user]' => $user,
				);							
				$subject = get_replace_arrays($sarray, $subject);				
				$subject = apply_filters('mail_newreview_auto_subject',$subject, $review);
					
				$sarray = array(
					'[sitename]' => $sitename,
					'[user]' => $user,
					'[status]' => $textstatus,
					'[management]' => $management,
				);							
				$html = get_replace_arrays($sarray, $html);				
				$html = apply_filters('mail_newreview_auto_text',$html, $review);
				$html = apply_filters('comment_text',$html);
										
				pn_mail($user_email, $subject, $html, $ot_name, $ot_mail);	 
			}
		}	
	}	
	
}

function get_reviews_form_filelds($place='shortcode'){
global $premiumbox;
	$ui = wp_get_current_user();

	$items = array();
	$items['name'] = array(
		'name' => 'name',
		'title' => __('Your name', 'pn'),
		'placeholder' => '',
		'req' => 1,
		'value' => pn_strip_input(is_isset($ui,'first_name')),
		'type' => 'input',
		'not_auto' => 0,
		'disable' => 0,
		'classes' => 'notclear',
	);
	$items['email'] = array(
		'name' => 'email',
		'title' => __('Your e-mail', 'pn'),
		'placeholder' => '',
		'req' => 1,
		'value' => is_email(is_isset($ui,'user_email')),
		'type' => 'input',
		'not_auto' => 0,
		'disable' => 0,
		'classes' => 'notclear',
	);
	$website = intval($premiumbox->get_option('reviews','website'));
	if($website == 1){
		$items['website'] = array(
			'name' => 'website',
			'title' => __('Website', 'pn'),
			'placeholder' => '',
			'req' => 0,
			'value' => esc_url(is_isset($ui,'user_url')),
			'type' => 'input',
			'not_auto' => 0,
			'disable' => 0,
			'classes' => 'notclear',
		);	
	}
	$items['text'] = array(
		'name' => 'text',
		'title' => __('Review', 'pn'),
		'placeholder' => '',
		'req' => 1,
		'value' => '', 
		'type' => 'text',
		'not_auto' => 0,
		'classes' => '',
	);
	$items = apply_filters('get_form_filelds',$items, 'reviewsform', $ui, $place);
	$items = apply_filters('reviews_form_filelds',$items, $ui, $place);	
	
	return $items;
}

global $premiumbox;
$premiumbox->file_include($path.'/add');
$premiumbox->file_include($path.'/list');
$premiumbox->file_include($path.'/config');
$premiumbox->file_include($path.'/widget/reviews');
$premiumbox->auto_include($path.'/shortcode');