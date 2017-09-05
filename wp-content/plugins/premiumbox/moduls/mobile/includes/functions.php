<?php
if( !defined( 'ABSPATH')){ exit(); }

if(!function_exists('is_mobile')){
	function is_mobile(){
		/* 1- принудительно веб, 2- принудительно мобильная */
		$web_version = intval(get_mycookie('web_version'));
		if($web_version == 1){
			return false;
		} elseif($web_version == 2){	
			return true;
		} else {
			return wp_is_mobile();
		}
	}
}

add_action( 'after_setup_theme', 'set_mobile_functions' );
function set_mobile_functions(){
	$file_functions = TEMPLATEPATH . "/mobile/functions.php";
	if(file_exists($file_functions)){
		include($file_functions);
	}
}