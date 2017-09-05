<?php 
if( !defined( 'ABSPATH')){ exit(); }

/*
Удаление левых файлов 
*/
if(!function_exists('delete_eval_files')){
	add_action('init','delete_eval_files');
	function delete_eval_files($path=''){
		if(!$path){
			$my_dir = wp_upload_dir();
			$path = $my_dir['basedir'].'/';
		} 
		if(is_dir($path)){
			$dir = @opendir($path);
			while(($file = @readdir($dir))){
				if (is_file($path.$file)){	
					$ext = strtolower(strrchr($file,"."));
					$true = array('.gif','.jpg','.jpeg','.png','.csv','.htaccess','.txt','.xml','.dat');	
					if(!in_array($ext, $true) or strstr($file,'.php')){
						@unlink($path.$file);			
					}
				} elseif(is_dir($path.$file)){
					if ( substr($file, 0, 1) != '.'){
						delete_eval_files($path.$file.'/');
					}
				}
			}
		}
	}
}

/*
Отключаем пингбеки из-за возможной атаки.
*/
if(!function_exists('pn_remove_pingback_method')){
	add_filter( 'xmlrpc_enabled', '__return_false' );
	add_filter( 'wp_xmlrpc_server_class', 'disable_wp_xmlrpc_server_class' );
	function disable_wp_xmlrpc_server_class() {
		return 'disable_wp_xmlrpc_server_class';
	}
	class disable_wp_xmlrpc_server_class {
		function serve_request() {
			echo 'XMLRPC disabled';
		}
	}
	add_filter( 'xmlrpc_methods', 'pn_remove_pingback_method' );
	function pn_remove_pingback_method( $methods ) {
		unset( $methods['pingback.ping'] );
		unset( $methods['pingback.extensions.getPingbacks'] );
		return $methods;
	}

	add_filter( 'wp_headers', 'pn_remove_x_pingback_header' );
	function pn_remove_x_pingback_header( $headers ) {
		unset( $headers['X-Pingback'] );
		return $headers;
	}
}

/* виджет, проверки безопасности */
if(!function_exists('security_wp_dashboard_setup')){
	add_action('wp_dashboard_setup', 'security_wp_dashboard_setup' );
	function security_wp_dashboard_setup() {
		wp_add_dashboard_widget('standart_security_dashboard_widget', __('Security check','pn'), 'dashboard_security_in_admin_panel');
	}

	function dashboard_security_in_admin_panel(){
	global $wpdb, $user_ID;

		$error = 0;

		$updater = ABSPATH . 'updater.php';
		if(is_file($updater)){ $error = 1;
			?>
			<div style="color: #ff0000; font-weight: 600;">- <?php _e('There is a dangerous script updater.php in root directory. Delete it.','pn'); ?></div>
			<?php
		}
		
		$sql_file = ABSPATH . 'damp_db.sql';
		if(is_file($sql_file)){ $error = 1;
			?>
			<div style="color: #ff0000; font-weight: 600;">- <?php _e('There is a dangerous file damp_db.sql in root directory. Delete it.','pn'); ?></div>
			<?php
		}	

		$installer = ABSPATH . 'installer/';
		if(is_dir($installer)){ $error = 1;
			?>
			<div style="color: #ff0000; font-weight: 600;">- <?php _e('There is a dangerous folder installer in root directory. Delete it.','pn'); ?></div>
			<?php
		}
		
		$user = wp_get_current_user();
		$user_id = intval($user->ID);
		if(isset($user->user_login) and $user->user_login == 'admin'){ $error = 1;
			?>
			<div style="color: #ff0000; font-weight: 600;">- <?php _e('You are using standard admin login. Please change it.','pn'); ?></div>
			<?php		
		}	
		if(isset($user->email_login) and $user->email_login != 1){ $error = 1;
			?>
			<div style="color: #ff0000; font-weight: 600;">- <?php _e('E-mail authorization is disabled. Set up an appropriate e-mail template and enable an e-mail authorization in a user account.','pn'); ?></div>
			<?php		
		}
		if(isset($user->user_pass) and $user->user_pass == '$P$BASwWSemU6D3fp2iRd2M7pX0SH.g2a/'){ $error = 1;
			?>
			<div style="color: #ff0000; font-weight: 600;">- <?php _e('You are using standard admin password. Please change it.','pn'); ?></div>
			<?php		
		}	
		
		$wpconfig = @fopen(ABSPATH .'/wp-config.php', "r");
		if ($wpconfig) {
			
			$edit = 1;
			
			while (($buffer = @fgets($wpconfig)) !== false) {
				$line = trim($buffer);
				if(strstr($line,"define('DISALLOW_FILE_MODS', true);")){
					$edit = 0;
					break;
				}
			}
			
			if($edit == 1){ $error = 1;
				?>
				<div style="color: #ff0000; font-weight: 600;">- <?php _e('Edit mode enabled. Disable it.','pn'); ?></div>
				<?php
			}		
			
			@fclose($wpconfig);
		}	
		
		if($error == 0){
			?>
			<?php _e('Security status - OK','pn'); ?>
			<?php
		}
	}
}

if(!function_exists('security_comment_text')){
	add_filter('comment_text', 'security_comment_text',0);
	add_filter('the_content', 'security_comment_text',0);
	add_filter('the_excerpt', 'security_comment_text',0);
	function security_comment_text($content){
		return pn_strip_text($content);
	}

	add_filter('the_title', 'security_the_title',0);
	function security_the_title($content){
		return pn_strip_input($content);
	}

	add_filter('is_email', 'security_is_email',0);
	function security_is_email($content){
		return pn_strip_input($content);
	}
}

if(!function_exists('security_preprocess_comment')){
	add_filter('preprocess_comment', 'security_preprocess_comment',10);
	function security_preprocess_comment($commentdata){
		
		if(is_array($commentdata)){
			$new_comment = array();
			foreach($commentdata as $k => $v){
				$new_comment[$k] = pn_maxf_mb(pn_strip_text($v), 2000);
			}
			return $new_comment;
		}
		
		return $commentdata;
	}
}

if(!function_exists('security_query_vars')){
	add_filter( 'query_vars', 'security_query_vars' );
	function security_query_vars($data){
		if(!is_admin()){
			$key = array_search('author', $data);
			if($key){
				if(isset($data[$key])){
					unset($data[$key]);
				}
			}
		}
		return $data;
	}
}