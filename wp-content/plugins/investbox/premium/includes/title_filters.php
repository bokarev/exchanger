<?php
/* защита от прямого обращения */
if( !defined( 'ABSPATH')){ exit(); }

/*
Приводим названия всех файлов, к латинице
*/
if(!function_exists('pn_sanitize_title')){
	add_filter('sanitize_title', 'pn_sanitize_title', 9);
	add_filter('sanitize_file_name', 'pn_sanitize_title');
	function pn_sanitize_title($title) {
		
		$title = replace_cyr($title);
		$title = preg_replace("/[^A-Za-z0-9\-\.]/", '-', $title);

		return $title;
	}
}

/*
Начальные установки заголовка
*/
if(!function_exists('premium_wp_title')){
	add_filter('wp_title' , 'premium_wp_title', 1);
	function premium_wp_title($title) {
		
		$temp = apply_filters('premium_wp_title', '[title] - [description]');
		if($temp){
			$site_name = pn_strip_input(get_bloginfo('sitename'));
			if(is_front_page()){
				$site_description = pn_strip_input(get_bloginfo('description'));
			} else {
				$site_description = str_replace('&raquo;','',$title);
			}
			$new_title = str_replace('[title]',$site_name,$temp);
			$new_title = str_replace('[description]',$site_description,$new_title);
			return $new_title;
		}
				
		return $title;
	}
}