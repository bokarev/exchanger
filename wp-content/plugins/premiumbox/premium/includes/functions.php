<?php
if (session_id() === ''){
	session_start();
}

/* защита от прямого обращения */
if( !defined( 'ABSPATH')){ exit(); }

if(!function_exists('get_keys')){
	function get_keys($word, $method=1){
		if($method == 1){
			$hash = md5($word);
		} elseif($method == 2){
			$hash = sha1($word, false);
		} elseif($method == 3){
			$hash = hash('sha256', $word, false);
		} else {
			$hash = $word;
		}
		
		return $hash;
	}
}

if(!function_exists('premium_rewrite_data')){
	function premium_rewrite_data(){
		global $or_site_url;
		
		$site_url = trailingslashit($or_site_url);
		$schema = 'http://';
		if(is_ssl()){
			$schema = 'https://';
		}
		$current_url = $schema . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];	
		$request_url = str_replace( $site_url, '', $current_url );
		$request_url = str_replace('index.php/', '', $request_url);	
		$url_parts = explode( '?', $request_url);
		$base = $url_parts[0];
		$base = rtrim($base,"/");
		$exp = explode( '/', $base);
		$super_base = end( $exp );		
		$data = array(
			'site_url' => $site_url,
			'current_url' => $current_url,
			'base' => $base,
			'super_base' => $super_base,
		);
		return $data;
	}
}

if(!function_exists('get_premium_url')){
	function get_premium_url(){
		return str_replace('includes/','',plugin_dir_url( __FILE__ ));
	}
}	

if(!function_exists('pn_disallow_file_mode')){
	function pn_disallow_file_mode(){
		if(!defined('DISALLOW_FILE_MODS')){
			define('DISALLOW_FILE_MODS', true);
		}	
	}
}

if(!function_exists('pn_strip_input')){
	function pn_strip_input($txt){	
		if(is_array($txt) or is_object($txt)){ return ''; }
		
		$txt = trim(esc_html(strip_tags(stripslashes($txt))));
		
		$pn_strip_input = array(
			'select' => 'sеlect',
			'insert' => 'insеrt',
			'union' => 'uniоn',
			'loadfile' => 'lоadfile',
			'load_file' => 'lоad_file',
			'outfile' => 'оutfile',
			'cookie' => 'cookie',
			'concat' => 'cоncat',
			'update' => 'updаte',
			'eval' => 'еval',
			'base64' => 'bаse64',
			'delete' => 'dеlete',
			'truncate' => 'truncаte',
			'replace' => 'rеplace',
			'infile' => 'infilе',
			'handler' => 'hаndler',
		);
		
		$pn_strip_input = apply_filters('pn_strip_input', $pn_strip_input);
		$pn_strip_input = (array)$pn_strip_input;
		foreach($pn_strip_input as $key => $value){
			$txt = preg_replace("/\b({$key})\b/iu", $value ,$txt);
		}
		return $txt;
	}
}

if(!function_exists('pn_strip_input_array')){
	function pn_strip_input_array($array){
		$new_array = array();
		if(is_array($array)){
			foreach($array as $key => $val){
				if(is_array($val)){
					$new_array[$key] = pn_strip_input_array($val);
				} else {
					$new_array[$key] = pn_strip_input($val);
				}
			}
		}
			return $new_array;
	}
}

if(!function_exists('pn_strip_text')){
	function pn_strip_text($txt){
		if(is_array($txt) or is_object($txt)){ return ''; }
		$txt = trim(stripslashes($txt));
		$allow_tag = apply_filters('pn_allow_tag','<strong>,<em>,<a>,<del>,<ins>,<code>,<img>,<h1>,<h2>,<h3>,<h4>,<h5>,<b>,<i>,<table>,<tbody>,<thead>,<tr>,<th>,<td>,<span>,<p>,<div>,<ul>,<li>,<ol>,<center>,<br>,<blockquote>');
		$allow_tag = trim($allow_tag);
		if($allow_tag){
			$txt = strip_tags($txt, $allow_tag);
		} else {
			$txt = strip_tags($txt);
		}
		
		$pn_strip_text = array(
			'select' => 'sеlect',
			'insert' => 'insеrt',
			'union' => 'uniоn',
			'loadfile' => 'lоadfile',
			'load_file' => 'lоad_file',
			'outfile' => 'оutfile',
			'cookie' => 'cookie',
			'concat' => 'cоncat',
			'update' => 'updаte',
			'eval' => 'еval',
			'base64' => 'bаse64',
			'delete' => 'dеlete',
			'truncate' => 'truncаte',
			'replace' => 'rеplace',
			'infile' => 'infilе',
			'handler' => 'hаndler',
		);
		
		$pn_strip_text = apply_filters('pn_strip_text', $pn_strip_text);
		$pn_strip_text = (array)$pn_strip_text;
		foreach($pn_strip_text as $key => $value){
			$txt = preg_replace("/\b({$key})\b/iu", $value ,$txt);
		}		
		return $txt;
	}
}

if(!function_exists('pn_strip_text_array')){
	function pn_strip_text_array($array){
		$new_array = array();
		if(is_array($array)){
			foreach($array as $key => $val){
				if(is_array($val)){
					$new_array[$key] = pn_strip_text_array($val);
				} else {
					$new_array[$key] = pn_strip_text($val);
				}
			}
		}
			return $new_array;
	}
}

/* удаляем лишние символы */
if(!function_exists('replace_cyr')){
	function replace_cyr($arg){
		$iso9_table = array(
			'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Ѓ' => 'G`',
			'Ґ' => 'G`', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'YO', 'Є' => 'YE',
			'Ж' => 'ZH', 'З' => 'Z', 'Ѕ' => 'Z', 'И' => 'I', 'Й' => 'Y',
			'Ј' => 'J', 'І' => 'I', 'Ї' => 'YI', 'К' => 'K', 'Ќ' => 'K',
			'Л' => 'L', 'Љ' => 'L', 'М' => 'M', 'Н' => 'N', 'Њ' => 'N',
			'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
			'У' => 'U', 'Ў' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'TS',
			'Ч' => 'CH', 'Џ' => 'DH', 'Ш' => 'SH', 'Щ' => 'SHH', 'Ъ' => '``',
			'Ы' => 'YI', 'Ь' => '`', 'Э' => 'E`', 'Ю' => 'YU', 'Я' => 'YA',
			'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'ѓ' => 'g',
			'ґ' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'є' => 'ye',
			'ж' => 'zh', 'з' => 'z', 'ѕ' => 'z', 'и' => 'i', 'й' => 'y',
			'ј' => 'j', 'і' => 'i', 'ї' => 'yi', 'к' => 'k', 'ќ' => 'k',
			'л' => 'l', 'љ' => 'l', 'м' => 'm', 'н' => 'n', 'њ' => 'n',
			'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
			'у' => 'u', 'ў' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'ts',
			'ч' => 'ch', 'џ' => 'dh', 'ш' => 'sh', 'щ' => 'shh', 'ь' => '',
			'ы' => 'yi', 'ъ' => "'", 'э' => 'e`', 'ю' => 'yu', 'я' => 'ya'
		);
		$arg = strtr($arg, $iso9_table);
		return $arg;
	}
}

if(!function_exists('delsimbol')){
	function delsimbol($arg, $zn=1){
		if($zn == 1){
			$arg = preg_replace("/[^A-Za-z0-9.]/", '', $arg);
		} else {
			$arg = preg_replace("/[^A-Za-z0-9]/", '', $arg);
		}
		$arg = strtolower($arg);
		return $arg;
	}
}

if(!function_exists('pn_strip_symbols')){
	function pn_strip_symbols($txt){	
		if(is_array($txt) or is_object($txt)){ return ''; }
		
		return delsimbol($txt, 0);
	}
}

if(!function_exists('pn_string')){
	function pn_string($text){
		$text = (string)$text;
		$text = trim($text);
		return $text;
	}
}

if(!function_exists('pn_maxf_mb')){
	function pn_maxf_mb($text, $length){
		$text = pn_string($text);
		$length = intval($length);
		if(mb_strlen($text) > $length){
			return mb_substr($text, 0, $length);
		}
			return $text;
	}
}

if(!function_exists('pn_maxf')){
	function pn_maxf($text, $length){
		$text = pn_string($text);
		$length = intval($length);
		if(strlen($text) > $length){
			return substr($text,0,$length);
		}
			return $text;
	}
}

/*
Если используется ssl, воизбежании ошибок браузера,
меняем все ссылки на https://
*/
if(!function_exists('is_ssl_url')){
	function is_ssl_url($url){
		if(is_ssl()){
			$url = str_replace('http://','https://',$url);
		} else {
			$url = str_replace('https://','http://',$url);
		}
		return $url;
	}
}

/* id сессии */
if(!function_exists('get_session_id')){
	function get_session_id(){
		return md5(pn_strip_input(session_id()));
	}
}

/* nonce */
if(!function_exists('pn_create_nonce')){
	function pn_create_nonce($key=''){
		return mb_substr(md5($key . session_id() . $key), 0, 12);
	}
}

if(!function_exists('pn_verify_nonce')){
	function pn_verify_nonce( $word, $key=''){
		if(mb_substr(md5($key . session_id() . $key),0, 12) == $word){
			return 1;
		} else {
			return 0;
		}
	}
}

/*
Получаем куку по ключу
*/
if(!function_exists('get_mycookie')){
	function get_mycookie($key){
		$key = pn_strip_input($key);
		
		if(isset($_COOKIE[$key])){
			return pn_strip_input($_COOKIE[$key]);
		} else {
			return false;
		}
	}
}

/*
Записываем в куку по ключу, если время не указано, ставим на 1 год
*/
if(!function_exists('add_mycookie')){
	function add_mycookie($key, $arg, $time=0){
		if($time == 0){
			$time = time()+365*24*60*60;
		}	
		
		$key = pn_strip_input($key);
		$arg = pn_strip_input($arg);
		
		if(isset($_COOKIE[$key])){
			unset($_COOKIE[$key]);
		}	
		
		setcookie($key, $arg, $time, COOKIEPATH, COOKIE_DOMAIN, false);
	}
}

if(!function_exists('is_text')){
	function is_text($arg){
		$arg = pn_string($arg);
		$arg = preg_replace("/[^A-Za-z0-9АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧЩШЭЮЯЪЬфбвгдеёщзийклмнопрстуфхцчшщэюяъъ\n\r\-.,!?$%:;()@ ]/iu", '', $arg);

		return $arg;
	}
}

if(!function_exists('is_phone')){
	function is_phone($phone){
		$phone = pn_string($phone);
		$phone = preg_replace( '/[^(+)0-9]/', '',$phone);
		return $phone;
	}
}

if(!function_exists('is_admin_newurl')){
	function is_admin_newurl($url){
		$url = pn_string($url);
		$url = delsimbol(replace_cyr($url));
		if (preg_match("/^[a-zA-z0-9]{3,250}$/", $url, $matches)) {
			return strtolower($url);
		} 
			return '';
	}
}

/* дата */
if(!function_exists('is_my_date')){
	function is_my_date($date, $zn='.'){
		$date = pn_string($date);
		$zn = preg_quote($zn);
		if (preg_match("/^[0-9]{1,2}[$zn]{1}[0-9]{1,2}[$zn]{1}[0-9]{4}$/", $date, $matches )) {
			return $date;
		} 
			return '';	
	}
}

/*
Функция логина пользователя
*/
if(!function_exists('is_user')){
	function is_user($username){
		$username = pn_string($username);
		$username = apply_filters('is_user', $username);
		if (preg_match("/^[a-zA-z0-9]{3,30}$/", $username, $matches )) {
			return strtolower($username);
		} 
			return false;
	}	
}

/* функция пароля пользователя */
if(!function_exists('is_password')){
	function is_password($password){
		$password = pn_string($password);
		if (strlen($password) > 3 and strlen($password) < 50) {
			return $password;
		} 
			return false;
	}
}

/* 
Деньги со знаками
half_up - Округляет val в большую сторону от нуля до precision десятичных знаков, если следующий знак находится посередине.
half_down - Округляет val в меньшую сторону к нулю до precision десятичных знаков, если следующий знак находится посередине.
*/
if(!function_exists('is_my_money')){
	function is_my_money($sum, $cz=12, $mode='half_up'){
		$sum = pn_string($sum);
		$sum = str_replace(',','.',$sum);
		$cz = apply_filters('is_my_money_cz', $cz);
		$cz = intval($cz); if($cz < 0){ $cz = 0; }	
		if ($sum) {
			$s_arr = explode('.', $sum);
			$s_ceil = trim(is_isset($s_arr, 0));
			$s_double = trim(is_isset($s_arr, 1));
			$cz_now = mb_strlen($s_double);
			
			if($cz > $cz_now){
				$cz = $cz_now;
			}
			
			$new_sum = sprintf("%0.{$cz}F",$sum);
			if(strstr($new_sum,'.')){
				$new_sum = rtrim($new_sum,'0');
				$new_sum = rtrim($new_sum,'.');
			}
			
			return apply_filters('is_my_money', $new_sum, $sum, $cz, $mode);
		} else {
			return 0;
		}
	}
}

/* проверка браузера */
if(!function_exists('is_older_browser')){
	function is_older_browser(){
		$older_browser = false;
		
		if(isset($_SERVER['HTTP_USER_AGENT'])){
			if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0') ){
				$older_browser = true;
			} elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 7.0') ){
				$older_browser = true;
			} elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 8.0') ){
				$older_browser = true;
			} elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 9.0') ){
				$older_browser = true;
			}
		}
					
		$older_browser = apply_filters('is_older_browser',$older_browser);
		return $older_browser;
	}
}

/* браузер */
if(!function_exists('get_browser_name')){
	function get_browser_name($user_agent, $unknown='Unknown'){
		
		$user_agent = (string)$user_agent;
		if (strpos($user_agent, "Firefox") !== false){
			$browser = 'Firefox';
		} elseif (strpos($user_agent, "OPR") !== false){
			$browser = 'Opera';
		} elseif (strpos($user_agent, "Chrome") !== false){
			$browser = 'Chrome';
		} elseif (strpos($user_agent, "MSIE") !== false){
			$browser = 'Internet Explorer';
		} elseif (strpos($user_agent, "Safari") !== false){
			$browser = 'Safari';
		} else { 
			$browser = $unknown; 
		}
		
		$browser = apply_filters('get_browser_name',$browser, $user_agent);
		return $browser;
	}
}

/* стандартный CURL-парсер */
if(!function_exists('get_curl_parser')){
	function get_curl_parser($url, $options=array(), $place='', $pointer=''){
		$options = (array)$options;
		$arg = array(
			'output' => '',
			'err' => 1,
		);
		if($ch = curl_init()){
			$curl_options = array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_SSL_VERIFYHOST => false,
				CURLOPT_REFERER => '',
				CURLOPT_TIMEOUT => 40,
				CURLOPT_USERAGENT => "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)",
			);
			foreach($options as $k => $v){
				$curl_options[$k] = $v;
			}
			$curl_options = apply_filters('get_curl_parser', $curl_options, $place, $pointer);
			curl_setopt_array($ch, $curl_options);
			
			$arg['output'] = curl_exec($ch);
			$arg['err'] = curl_errno($ch);
			curl_close($ch);
		} else {
			$arg['err'] = '901';
		}
		
		return $arg;
	}
}

/*
Перевод из utf-8 в ANSI
*/
if(!function_exists('get_cptgn')){
	function get_cptgn($text){
		$text =  pn_string($text);
		$txt = iconv('UTF-8','CP1251',$text);
		return $txt;
	}
}

/*
Перевод из ANSI в utf-8
*/
if(!function_exists('get_tgncp')){
	function get_tgncp($text){
		$text =  pn_string($text);
		$txt = iconv('CP1251','UTF-8',$text);
		return $txt;
	}
}

/* выводим слово, первая буква которого большая */
if(!function_exists('get_caps_name')){
	function get_caps_name($name){
		$name = pn_strip_input($name);
		if($name){
			$newname = mb_strtoupper(mb_substr($name,0,1)).mb_strtolower(mb_substr($name,1,mb_strlen($name)));
			return $newname;
		}
			return $name;
	}
}

/*
Заменяем данные из массива по контенту
*/
if(!function_exists('get_replace_arrays')){
	function get_replace_arrays($array, $content){
		if(is_array($array)){
			foreach($array as $key => $value){
				$content = str_replace($key, $value, $content);
			}
		}
		return $content;
	}
}

/* конвертеры времени */
if(!function_exists('get_mydate')){
	function get_mydate($date, $format='d.m.Y'){
		$date = pn_strip_input($date);
		if($date and $date != '0000-00-00'){
			$time = strtotime($date);
			return date($format, $time);
		}
	}
}	

if(!function_exists('get_mytime')){
	function get_mytime($date, $format='d.m.Y H:i'){
		$date = pn_strip_input($date);
		if($date and $date != '0000-00-00 00:00:00'){
			$time = strtotime($date);
			return date($format, $time);
		}
	}
}	
/* end конвертеры времени */

/* запросы и существование */
if(!function_exists('is_isset')){ 
	function is_isset($where, $look){
		if(is_array($where)){
			if(isset($where[$look])){
				return $where[$look];
			} 
		} elseif(is_object($where)) {
			if(isset($where->$look)){
				return $where->$look;
			} 		
		}
			return '';
	}
}

if(!function_exists('is_param_get')){
	function is_param_get($arg){
		if(isset($_GET[$arg])){
			return $_GET[$arg];
		} else {
			return false;
		}
	}
}

if(!function_exists('is_param_post')){
	function is_param_post($arg){
		if(isset($_POST[$arg])){
			return $_POST[$arg];
		} else {
			return false;
		}
	}
}	

if(!function_exists('is_param_req')){
	function is_param_req($arg){
		if(isset($_REQUEST[$arg])){
			return $_REQUEST[$arg];
		} else {
			return false;
		}
	}
}

if(!function_exists('get_admin_action')){
	function get_admin_action(){
		$action = false;

		if ( isset( $_REQUEST['action'] ) && -1 != $_REQUEST['action'] ){
			$action = $_REQUEST['action'];
		}
		if ( isset( $_REQUEST['action2'] ) && -1 != $_REQUEST['action2'] ){
			$action = $_REQUEST['action2'];
		}	
		return $action;
	}
}
	
if(!function_exists('only_post')){	
	function only_post(){
		if ( 'POST' != $_SERVER['REQUEST_METHOD'] ) {
			header('Allow: POST');
			header('HTTP/1.1 405 Method Not Allowed');
			header('Content-Type: text/plain');
			exit;
		}		
	}
}
/* end запросы и существование */

/* excerpt для новостей */
if(!function_exists('get_pn_excerpt')){	
	function get_pn_excerpt($item, $count=15){
		if(function_exists('ctv_ml')){
			$excerpt = pn_strip_text(ctv_ml($item->post_excerpt));
			if($excerpt){
				return $excerpt;
			} else {
				return wp_trim_words(pn_strip_text(ctv_ml($item->post_content)),$count);
			}
		} else {
			$excerpt = pn_strip_text($item->post_excerpt);
			if($excerpt){
				return $excerpt;
			} else {
				return wp_trim_words(pn_strip_text($item->post_content),$count);
			}			
		}		
	}
}
/* end excerpt для новостей */

/* универсальная дата копирайта */
if(!function_exists('get_copy_date')){	
	function get_copy_date($year){
		$time = current_time('timestamp');
		$y = date('Y', $time);
		if($year != $y){
			return $year.'-'.$y;
		} else {
			return $y;
		}
	}
}
/* end универсальная дата копирайта */

/* склонение */
if(!function_exists('get_sklon')){
	function get_sklon($num, $text1, $text2, $text3){

		$num = abs($num);
		$nums = $num % 100;
			 
		if (($nums > 4) && ($nums < 21)) {
			return str_replace('%',$num,$text3);
		}
			
		$nums = $num % 10;
		if (($nums == 0) || ($nums > 4)) {
			return str_replace('%',$num,$text3);
		}	
			
		if ($nums == 1) {
			return str_replace('%',$num,$text1);
		}
			 
		return str_replace('%',$num,$text2);	
	}
}
/* end склонение */

/* удаление папки */
if(!function_exists('full_del_dir')){
	function full_del_dir($directory){
		if(is_dir($directory)){
			$dir = @opendir($directory);
			while(($file = @readdir($dir))){
				if ( is_file($directory."/".$file)){
					@unlink($directory."/".$file);
				} else if ( is_dir ($directory."/".$file) && ($file != ".") && ($file != "..")){
					full_del_dir($directory."/".$file);  
				}
			}
			@closedir ($dir);
			@rmdir ($directory);
		}
	}
}
/* end удаление папки */

if(!function_exists('get_month_title')){
	function get_month_title($arg, $months=array()){
		$arg = intval($arg);

		if(!is_array($months) or count($months) < 7){
			$months = array('',
				'Jan.',
				'Feb.',
				'Mar.',
				'Apr.',
				'May',
				'June',
				'July',
				'Aug.',
				'Sep.',
				'Oct.',
				'Nov.',
				'Dec.'
			);
		}
		
		return is_isset($months,$arg);
	}
}

/* вывод ошибки */
if(!function_exists('pn_display_mess')){
	function pn_display_mess($title, $text='', $species='error'){
		$title = trim($title);
		$text = trim($text);
		if(!$text){ $text = $title; }
		if($species == 'error'){
			$html = '<body><head><title>'. $title .'</title></head><body><p style="text-align: center; color: #ff0000; padding: 20px 0;">'. $text .'</p></body></html>';
		} else {
			$html = '<body><head><title>'. $title .'</title></head><body><p style="text-align: center; color: green; padding: 20px 0;">'. $text .'</p></body></html>';
		}
		$html = apply_filters('premium_display_mess', $html, $title, $text, $species);
		echo $html;
		exit;
	}
}
/* end вывод ошибки */

if(!function_exists('pn_set_wp_admin')){
	function pn_set_wp_admin(){
		if(!defined('WP_ADMIN')){
			define('WP_ADMIN', true);
		} 		
	}
}

/* действия для админа */
if(!function_exists('pn_link_post')){
	function pn_link_post($action='', $key='pnbx'){
		global $or_site_url;
		if(!$action){
			$action = pn_strip_input(is_param_get('page'));
		}
			
		$link = $or_site_url .'/premium_post.html?meth=get&yid='. pn_create_nonce($key);
		if($action){
			$link .= '&myaction='.$action;	
		}
			
		return $link;
	}
}
	
if(!function_exists('pn_the_link_post')){	
	function pn_the_link_post($action='', $key='pnbx'){
		echo pn_link_post($action, $key);
	}
}

if(!function_exists('pn_link_ajax')){
	function pn_link_ajax($action='', $key='pnbx'){
		global $or_site_url;
		if(!$action){
			$action = pn_strip_input(is_param_get('page'));
		}
			
		$link = $or_site_url .'/premium_post.html?meth=post&yid='. pn_create_nonce($key);
		if($action){
			$link .= '&myaction='.$action;	
		}
			
		return $link;
	}
}

if(!function_exists('pn_the_link_ajax')){	
	function pn_the_link_ajax($action='', $key='pnbx'){
		echo pn_link_ajax($action, $key);
	}
}
/* end действия для админа */

/* действия для пользователя */
if(!function_exists('get_ajax_link')){
	function get_ajax_link($action, $method='post', $key='pnbx_site'){ 
	global $or_site_url;
		
		$link = $or_site_url .'/ajax-'. pn_strip_input($action) .'.html?meth='. $method .'&yid='. pn_create_nonce($key);
		
		if(function_exists('is_ml') and is_ml()){
			$link .= '&lang='. get_lang_key(get_locale());
		} 
		
		return $link;
	}
}

if(!function_exists('get_merchant_link')){
	function get_merchant_link($action){
		global $or_site_url;
		return $or_site_url .'/merchant-'. pn_strip_input($action) .'.html';
	}
}
/* end действия для пользователя */

if(!function_exists('is_place_url')){
	function is_place_url($url, $class='current'){
		$http = 'http://'; if(is_ssl()){ $http = 'https://'; }
		$url_site = $http . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		if($url == $url_site){
			return $class;
		}
	}
}		

/* узнаём страницу нашего плагина */
if(!function_exists('get_userpage_pn')){
	function get_userpage_pn($page_id, $class='act'){
		if(is_page($page_id)){
			return $class;
		} else {
			return false;
		}
	}
}	

if(!function_exists('update_pn_meta')){
	function update_pn_meta($table, $id, $key, $value){ 
	global $wpdb;
		
		$id = intval($id);
		if(is_array($value)){
			$value = serialize($value);
		}
		$cc = $wpdb->query("SELECT id FROM ". $wpdb->prefix . $table ." WHERE item_id='$id' AND meta_key='$key'");
		if($cc == 0){
			$result = $wpdb->insert($wpdb->prefix . $table, array('meta_value'=>$value, 'item_id'=>$id, 'meta_key'=>$key));	
		} else {
			$result = $wpdb->update($wpdb->prefix . $table, array('meta_value'=>$value), array('item_id'=>$id, 'meta_key'=>$key));
		}
		return $result;
	}
}

if(!function_exists('get_pn_meta')){
	function get_pn_meta($table, $id, $key){
	global $wpdb;
		$id = intval($id);
		$data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix . $table ." WHERE item_id='$id' AND meta_key='$key'");
		if(isset($data->meta_value)){
			return maybe_unserialize($data->meta_value);
		} else {
			return false;
		}
	}
}

if(!function_exists('delete_pn_meta')){
	function delete_pn_meta($table, $id, $key){
	global $wpdb;   	
		$id = intval($id);			
		return $wpdb->query("DELETE FROM ".$wpdb->prefix . $table ." WHERE item_id='$id' AND meta_key='$key'");
	}
}

if(!function_exists('delete_txtmeta')){
	function delete_txtmeta($folder, $data_id){
		if($folder){
			$my_dir = wp_upload_dir();
			$dir = $my_dir['basedir'].'/'. $folder .'/';
			if(!is_dir($dir)){
				@mkdir($dir, 0777);
			}
			
			$file = $dir . $data_id .'.txt';
			if(file_exists($file)){
				@unlink($file);
			} 	
		}
	}
}

if(!function_exists('get_txtmeta')){
	function get_txtmeta($folder, $data_id, $key){
		if($folder){
			$my_dir = wp_upload_dir();
			$dir = $my_dir['basedir'].'/'. $folder .'/';
			if(!is_dir($dir)){
				@mkdir($dir, 0777);
			}
			
			$file = $dir . $data_id .'.txt';
			$array = '';
			if(file_exists($file)){
				$data = @file_get_contents($file);
				$array = @unserialize($data);
			} 
				
			return trim(stripslashes(is_isset($array, $key)));
		}
	}
}

if(!function_exists('copy_txtmeta')){
	function copy_txtmeta($folder, $data_id, $new_id){
		if($folder){
			$my_dir = wp_upload_dir();
			$dir = $my_dir['basedir'].'/'. $folder .'/';
			if(!is_dir($dir)){
				@mkdir($dir, 0777);
			}
			
			$file = $dir . $data_id .'.txt';
			$newfile = $dir . $new_id .'.txt';
			if(file_exists($file)){
				@copy($file, $newfile);
			} 	
		}
	}
}

if(!function_exists('update_txtmeta')){
	function update_txtmeta($folder, $data_id, $key, $value){
		
		if($folder){
			
			$my_dir = wp_upload_dir();
			$dir = $my_dir['basedir'].'/'. $folder .'/';
			if(!is_dir($dir)){
				@mkdir($dir, 0777);
			}
			$htacces = $dir.'.htaccess';
			if(!is_file($htacces)){
				$nhtaccess = "Order allow,deny \n Deny from all";
				$file_open = @fopen($htacces, 'w');
				@fwrite($file_open, $nhtaccess);
				@fclose($file_open);		
			}
			
			$file = $dir . $data_id .'.txt';
			$array = '';
			if(file_exists($file)){
				$data = @file_get_contents($file);
				$array = @unserialize($data);
			} 
			if(!is_array($array)){
				$array = array();
			}
			
			$array[$key] = addslashes($value);
			
			$file_data = @serialize($array);
			
			$file_open = @fopen($file, 'w');
			@fwrite($file_open, $file_data);
			@fclose($file_open);	
			
			if(is_file($file)){
				return 1;
			} 
		} 
		
		return 0;
	}
}

if(!function_exists('pn_real_ip')){
	function pn_real_ip(){
		if (!empty($_SERVER['HTTP_CLIENT_IP'])){
			$ips = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$ips = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ips = $_SERVER['REMOTE_ADDR'];
		}
		
		$ips_arr = explode(',',$ips);
		$ip = trim($ips_arr[0]);
		$ip = preg_replace( '/[^0-9a-fA-F.]/', '',$ip);
		$ip = pn_maxf($ip, 140);
		
		return apply_filters('pn_real_ip', $ip, $ips_arr);	
	}
}

if(!function_exists('get_rand_word')){
	function get_rand_word($count=4, $vid=1){
	global $premiumbox;	

		$count = intval($count);
		if($count < 1){ $count = 4; }
		
		$vid = intval($vid);
		if($vid == 1){
			$arr = 'q,w,e,r,t,y,u,i,o,p,a,s,d,f,g,h,j,k,l,z,x,c,v,b,n,m';
		} else {
			$arr = '1,2,3,4,5,6,7,8,9,0';
		}
		$array = explode(',',$arr);
		
		$r=0;
		$word = '';
		while($r++<$count){
			shuffle($array);
			$word .= mb_strtoupper($array[0]);
		}
		
		return $word;
	}
}

if(!function_exists('rez_exp')){
	function rez_exp($text){
		$text = trim($text);
		$text = str_replace(array(';','"'),'',$text);
		
		return $text;
	}
}

if(!function_exists('rep_dot')){
	function rep_dot($text){
		$text = str_replace('.',',',$text);
		
		return $text;
	}
}

if(!function_exists('get_exvar')){
	function get_exvar($zn, $arr){
		return is_isset($arr,$zn);
	}
}