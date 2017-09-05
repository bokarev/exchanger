<?php
/* защита от прямого обращения */
if( !defined( 'ABSPATH')){ exit(); }

/*
Функции мультиязычности
*/
/*
Для добавления языка сайта, мы используем фильтр языков pn_site_langs
Таким образом, что бы добавить язык, нужно всего лишь:
загрузить иконку языка, добавить фильтр и создать файл перевода.
*/
if(!function_exists('def_pn_site_langs')){
	add_filter('pn_site_langs','def_pn_site_langs');
	function def_pn_site_langs($langs){
		
		$langs['ru_RU'] = 'Русский';
		$langs['en_US'] = 'English';
		
		return $langs;
	}
}

/* 
Выдаем название языка, по ключу
*/
if(!function_exists('get_title_forkey')){
	function get_title_forkey($key){
		$key = pn_string($key);
		$langs = apply_filters('pn_site_langs', array());
		return is_isset($langs,$key);
	}
}

/* устанавливаем глобально настройки языка */
global $pn_lang, $pn_current_lang;

$pn_lang = get_option('pn_lang');
$pn_lang = (array)$pn_lang;

$pn_current_lang = get_locale();

/* проверка на мультиязычность */
if(!function_exists('is_ml')){
	function is_ml(){
	global $pn_lang;

		$multilingual = 0;
		if(isset($pn_lang['multilingual'])){
			$multilingual = intval($pn_lang['multilingual']);
		} 
		
		return $multilingual;
	}
}

/* иконка языка */
if(!function_exists('get_lang_icon')){
	function get_lang_icon($key){
		$key = (string)$key;
		$key = trim($key);
		$url = rtrim(get_premium_url(), 'premium/'); 
		$parts = explode('plugins/', $url);
		$patch = $parts[0].'plugins/';
		$plugin_folder = rtrim($parts[1],'/');
		$plugin_folder = apply_filters('ml_flag_url', $plugin_folder);
		$new_url = $patch . $plugin_folder;
		return $new_url .'/flags/'. $key .'.png';
	}
}

/* язык сайта по умолчанию */
if(!function_exists('get_site_lang')){
	function get_site_lang(){
	global $pn_lang;
		$lang = get_locale();
		if(isset($pn_lang['site_lang']) and $pn_lang['site_lang']){
			$lang = $pn_lang['site_lang'];
		} 		
		return $lang;
	}
}

/* 
язык админ-панели по умолчанию 
так как в админке нельзя изменять динамически языки, то эта переменная в данный момент равна get_locale
*/
if(!function_exists('get_admin_lang')){
	function get_admin_lang(){
		$lang = get_locale();		
		return $lang;
	}
}

/* 
Получение псевдоключа, по ключу языка
Таким образом ru_RU становится просто ru
*/
if(!function_exists('get_lang_key')){
	function get_lang_key($arg){
		$arg = pn_string($arg);
		$keyname = explode('_',$arg);
		return $keyname = $keyname[0];
	}
}

/*
Оригинальный url сайта
*/
if(!function_exists('get_site_url_or')){
	function get_site_url_or(){
	global $or_site_url;
	
		return $or_site_url;
	}
}

/*
URL версии сайта в данный момент
Если выставлен язык не по дефолту, выдаем его адрес
*/
if(!function_exists('get_site_url_ml')){
	function get_site_url_ml(){

		$now_lang = get_locale();
		$def_lang = get_site_lang();
		
		$url = get_site_url_or();
		if($now_lang != $def_lang){
			$key = get_lang_key($now_lang);
			return $url .'/'. $key;
		}
		
		return $url;
	}
}

/*
Все языки нашего сайта
Первым языком всегда будет тот который по умолчанию
*/
if(!function_exists('get_langs_ml')){
	function get_langs_ml(){
	global $pn_lang;
		$lang = get_locale();	
		$ml_array = array();
		$ml_array[] = $lang;
		if(isset($pn_lang['multisite_lang'])){
			$array = $pn_lang['multisite_lang'];
			if(is_array($array)){
				foreach($array as $key){
					if($key != $lang){
						$ml_array[] = pn_strip_input($key);
					}
				}
			}		
		}
		return $ml_array;
	}
}

/*
Проверка на префикс языка
*/
if(!function_exists('is_lang_prefix')){
	function is_lang_prefix($arg){
		$arg = pn_string($arg);
		if (preg_match("/^[A-Za-z]{2}$/", $arg, $matches )) {
			return strtolower($arg);
		}
		return false;
	}
}

if(!function_exists('is_lang_attr')){
	function is_lang_attr($arg){
		$arg = pn_string($arg);
		if (preg_match("/^[A-Za-z]{2}[_]{1}[A-Za-z]{2}$/", $arg, $matches )) {
			return strtolower($arg);
		} 
		return false;
	}
}

/*
Ссылка на себя, но с языковым префиксом
*/
if(!function_exists('lang_self_link')){
	function lang_self_link($lang=''){
		if(!$lang){
			$lang = get_locale();
		}
		$def_lang = get_site_lang();
		if($def_lang == $lang){
			$link = esc_url(get_site_url_or() . $_SERVER['REQUEST_URI']);
		} else {
			$key = get_lang_key($lang);
			$link = esc_url(get_site_url_or() .'/'. $key .$_SERVER['REQUEST_URI']);
		}
		return $link;
	}
}

/* 
Конвертируем контент 
*/
if(!function_exists('convert_to_ml')){
	function convert_to_ml($string){
		$site_lang = get_site_lang();
		
		if(is_string($string)){
			$string = trim($string);
			if($string){
				if ( false === strpos( $string, '['. $site_lang .':]' ) ) {
					$string = '['. $site_lang .':]'. $string .'[:'. $site_lang .']';
				}
			}
		}
		
			return $string;
	}
}

/*
Конвертируем контент для полей
Все данные хранятся в виде строки, мы разбиваем их на массив
*/
if(!function_exists('get_value_ml')){
	function get_value_ml($string){ 
		$array = array();
		if(is_string($string)){
			$now_lang = get_locale();
			if ( false === strpos( $string, '[' ) ) {
				$array[$now_lang] = $string;
				return $array;
			}		
			if(preg_match_all('/\[(.*?):\](.*?)\[:(.*?)\]/s',$string, $match, PREG_PATTERN_ORDER)){
				foreach($match[1] as $key => $lang){
					$array[$lang] = $match[2][$key];
				}
			} else {
				$array[$now_lang] = $string;
			}
		}	
		return $array;
	}
}

if(!function_exists('replace_value_ml')){
	function replace_value_ml($string, $newtext='', $lang=''){
		if(!$lang){ $lang = get_locale(); }
		if(is_string($string)){
			if ( false === strpos( $string, '[' ) ) {
				return $string;	
			}
			if(preg_match_all('/\[(.*?):\](.*?)\[:(.*?)\]/s',$string, $match, PREG_PATTERN_ORDER)){
				$key = array_search($lang, $match[1]);
				if(is_numeric($key)){
					$string = preg_replace('/\['. $lang .':\](.*?)\[:'. $lang .'\]/s', '['. $lang .':]'.$newtext.'[:'. $lang .']', $string);
				} else {
					$string .= '['. $lang .':]'. $newtext .'[:'. $lang .']';
				}		
			}	
		}
		return $string;
	}
}

/*
Конвертируем мультиязычную строку в обычную для нужного языка
Делаем это рекурсионно, выдаем тот же вид, который получаем.
Тут и string и array и object
*/
if(!function_exists('ctv_ml')){
	function ctv_ml($string, $now_lang=''){
		if(!trim($now_lang)){
			$now_lang = get_locale();
		}
		if(is_string($string)){
			if ( false === strpos( $string, '[' ) ) {
				return $string;	
			}
			if(preg_match_all('/\[(.*?):\](.*?)\[:(.*?)\]/s',$string, $match, PREG_PATTERN_ORDER)){
				$key = array_search($now_lang, $match[1]);
				if(is_numeric($key)){
					$newtext = trim($match[2][$key]);
				} else {
					$newtext = trim($match[2][0]);
					$newtext = apply_filters('ctv_ml_default', $newtext, $match);
				}
				return $newtext;			
			}	
		} elseif(is_object($string)){
			$new_object = array();
			foreach($string as $key => $val){
				$new_object[$key] = ctv_ml($val, $now_lang);
			}
			return (object)$new_object;
		} elseif(is_array($string)){ 
			$new_object = array();
			foreach($string as $key => $val){
				$new_object[$key] = ctv_ml($val, $now_lang);
			}
			return $new_object;	
		}
		
			return $string;
	}
}

/*
Мультиязычный сборщик данных
*/
if(!function_exists('is_param_post_ml')){
	function is_param_post_ml($name){
		if(isset($_POST[$name])){
			return $_POST[$name];
		} else {
			$arg = '';
			$langs = get_langs_ml();
			foreach($langs as $key){
				$val = is_param_post($name.'_'.$key);
				if($val){
					$arg .= '['. $key .':]'. $val .'[:'. $key .']';
				}	
			}
			return $arg;
		}
	} 
}

if(!function_exists('is_param_get_ml')){
	function is_param_get_ml($name){
		if(isset($_GET[$name])){
			return $_GET[$name];
		} else {
			$arg = '';
			$langs = get_langs_ml();
			foreach($langs as $key){
				$val = is_param_get($name.'_'.$key);
				if($val){
					$arg .= '['. $key .':]'. $val .'[:'. $key .']';
				}	
			}
			return $arg;
		}
	} 
}

if(!function_exists('lang_locale')){
	/* 
	Устанавливаем версию языка по умолчанию
	Изначально, текущий язык равен локализации, ну а дальше смотрим по фильтрам
	*/
	if(is_admin()){
		/*
		Если мы в админке и в настройках указан язык, значит этот язык и есть основным
		*/
		if(isset($pn_lang['admin_lang']) and $pn_lang['admin_lang']){
			$pn_current_lang = $pn_lang['admin_lang'];
		} 
	} else {
		/*
		Если мы на сайте и в настройках указан язык, значит этот язык и есть основным
		*/	
		if(isset($pn_lang['site_lang']) and $pn_lang['site_lang']){
			$pn_current_lang = $pn_lang['site_lang'];
		} 
		/*
		Но если сайт мультиязычный, смотрим на особые условия
		*/
		if(is_ml()){ 		
			$http = 'http://'; if(is_ssl()){ $http = 'https://'; }
			$url_site = $http . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];				
			$lprefix = '';
			$delprefix = '';
			$getlang = is_lang_prefix(is_param_get('lang'));
			if($getlang){
				$lprefix = $getlang;
				$rew_uri = 0;
			} else {
				$rew_uri = 1;
				$uri = str_replace(get_site_url_or(), '', $url_site);
				$exuri = explode('/',$uri);
				if(isset($exuri[1]) and is_lang_prefix($exuri[1])){
					$lprefix = $exuri[1];
					$delprefix = '/'.$exuri[1];
				}
				if(isset($exuri[2])){
					$delprefix .= '/';
				}			
			}
			if($lprefix){
				$langs = get_langs_ml();	
				foreach($langs as $lang){
					$key_lang = get_lang_key($lang);
					if($key_lang == $lprefix){	
						$pn_current_lang = $lang;				
						if($rew_uri){
							$replace_uri = str_replace($delprefix,'',$_SERVER['REQUEST_URI']);
							$_SERVER['REQUEST_URI'] = '/'.$replace_uri;
						}
						break;
					}
				}
			} 
		} 
	} 
	/* end языковая версия */

	/*
	Меняем локализацию get_locale, если установлен наш язык
	*/
	add_filter('locale','lang_locale',1);
	function lang_locale($locale){
	global $pn_current_lang;	
		
		if($pn_current_lang){
			return $pn_current_lang;
		}

		return pn_strip_input($locale);
	} 
}