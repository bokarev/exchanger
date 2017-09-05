<?php

function is_param_get_i($arg){
	if(isset($_GET[$arg])){
		return $_GET[$arg];
	} else {
		return false;
	}
}

function is_param_post_i($arg){
	if(isset($_POST[$arg])){
		return $_POST[$arg];
	} else {
		return false;
	}
}

function get_step(){
	$step = intval(is_param_get_i('step'));
	if($step < 1){ $step = 1; }
	return $step;
}

function get_lang(){
	$lang = is_param_get_i('lang');
	if($lang != 'en'){ $lang = 'ru'; }

	return $lang; 
}

function installer_title(){
	$step = get_step();
	$lang = get_lang();
	
	if($step == 1){
		$txt = 'Installer';
	} elseif($step == 2) {
		if($lang == 'ru'){
			$txt = 'Проверка требований к хостингу';
		} else {
			$txt = 'Check the requirements for hosting';
		}
	} elseif($step == 3) {
		if($lang == 'ru'){
			$txt = 'Проверка функций и библиотек php';
		} else {
			$txt = 'Check functions and libraries php';
		}
	} elseif($step == 4) {
		if($lang == 'ru'){
			$txt = 'Проверка почты';
		} else {
			$txt = 'Сhecking e-mail';
		}	
	} elseif($step == 5) {
		if($lang == 'ru'){
			$txt = 'Проверка файлов и папок';
		} else {
			$txt = 'Checking files and folders';
		}
	} elseif($step == 6) {
		if($lang == 'ru'){
			$txt = 'Подключение к базе данных';
		} else {
			$txt = 'Connection database';
		}
	} elseif($step == 7) {
		if($lang == 'ru'){
			$txt = 'Импорт базы данных';
		} else {
			$txt = 'Import database';
		}
	} elseif($step == 8) {
		if($lang == 'ru'){
			$txt = 'Основные настройки';
		} else {
			$txt = 'Basic Settings';
		}
	} elseif($step == 9) {
		if($lang == 'ru'){
			$txt = 'Языковые настройки';
		} else {
			$txt = 'Language settings';
		}		
	}
	
	echo $txt;
}

function installer_fix_server_vars() {
	global $PHP_SELF;

	$default_server_values = array(
		'SERVER_SOFTWARE' => '',
		'REQUEST_URI' => '',
	);

	$_SERVER = array_merge( $default_server_values, $_SERVER );

	// Fix for IIS when running with PHP ISAPI
	if ( empty( $_SERVER['REQUEST_URI'] ) || ( PHP_SAPI != 'cgi-fcgi' && preg_match( '/^Microsoft-IIS\//', $_SERVER['SERVER_SOFTWARE'] ) ) ) {

		// IIS Mod-Rewrite
		if ( isset( $_SERVER['HTTP_X_ORIGINAL_URL'] ) ) {
			$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_ORIGINAL_URL'];
		}
		// IIS Isapi_Rewrite
		elseif ( isset( $_SERVER['HTTP_X_REWRITE_URL'] ) ) {
			$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_REWRITE_URL'];
		} else {
			// Use ORIG_PATH_INFO if there is no PATH_INFO
			if ( !isset( $_SERVER['PATH_INFO'] ) && isset( $_SERVER['ORIG_PATH_INFO'] ) )
				$_SERVER['PATH_INFO'] = $_SERVER['ORIG_PATH_INFO'];

			// Some IIS + PHP configurations puts the script-name in the path-info (No need to append it twice)
			if ( isset( $_SERVER['PATH_INFO'] ) ) {
				if ( $_SERVER['PATH_INFO'] == $_SERVER['SCRIPT_NAME'] )
					$_SERVER['REQUEST_URI'] = $_SERVER['PATH_INFO'];
				else
					$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'] . $_SERVER['PATH_INFO'];
			}

			// Append the query string if it exists and isn't null
			if ( ! empty( $_SERVER['QUERY_STRING'] ) ) {
				$_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
			}
		}
	}

	// Fix for PHP as CGI hosts that set SCRIPT_FILENAME to something ending in php.cgi for all requests
	if ( isset( $_SERVER['SCRIPT_FILENAME'] ) && ( strpos( $_SERVER['SCRIPT_FILENAME'], 'php.cgi' ) == strlen( $_SERVER['SCRIPT_FILENAME'] ) - 7 ) )
		$_SERVER['SCRIPT_FILENAME'] = $_SERVER['PATH_TRANSLATED'];

	// Fix for Dreamhost and other PHP as CGI hosts
	if ( strpos( $_SERVER['SCRIPT_NAME'], 'php.cgi' ) !== false )
		unset( $_SERVER['PATH_INFO'] );

	// Fix empty PHP_SELF
	$PHP_SELF = $_SERVER['PHP_SELF'];
	if ( empty( $PHP_SELF ) )
		$_SERVER['PHP_SELF'] = $PHP_SELF = preg_replace( '/(\?.*)?$/', '', $_SERVER["REQUEST_URI"] );
}

if(!function_exists('wp_convert_hr_to_bytes_i')){
	function wp_convert_hr_to_bytes_i( $size ) {
		$size  = strtolower( $size );
		$bytes = (int) $size;
		if ( strpos( $size, 'k' ) !== false )
			$bytes = intval( $size ) * 1024;
		elseif ( strpos( $size, 'm' ) !== false )
			$bytes = intval($size) * 1024 * 1024;
		elseif ( strpos( $size, 'g' ) !== false )
			$bytes = intval( $size ) * 1024 * 1024 * 1024;
		return $bytes;
	}
}
if(!function_exists('wp_max_upload_size_i')){
	function wp_max_upload_size_i() {
		$u_bytes = wp_convert_hr_to_bytes_i( ini_get( 'upload_max_filesize' ) );
	    $p_bytes = wp_convert_hr_to_bytes_i( ini_get( 'post_max_size' ) );
		return min( $u_bytes, $p_bytes );		
	}
}
if(!function_exists('wp_generate_password_i')){
	function wp_generate_password_i( $length = 12, $special_chars = true, $extra_special_chars = false ) {
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		if ( $special_chars )
	        $chars .= '!@#$%^&*()';
		if ( $extra_special_chars )
			$chars .= '-_ []{}<>~`+=,.;:/?|';

			$password = '';
			for ( $i = 0; $i < $length; $i++ ) {
				$password .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
			}

		return $password;
	}
}

if(!function_exists('is_user')){
	function is_user($username){
		$username = (string)$username;
		$username = trim($username);
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
		$password = pn_strip_input($password);
		if (strlen($password) > 3 and strlen($password) < 35) {
			return $password;
		} 
		return false;
	}
}