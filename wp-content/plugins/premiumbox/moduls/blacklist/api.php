<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('myaction_request_blackping','blacklist_request_blackping');
function blacklist_request_blackping(){ 
global $wpdb, $premiumbox;

	$premiumbox->up_mode();

	if($premiumbox->get_option('blacklist','api') == 1 and check_hash_cron()){
	
		$curl = get_curl_parser('http://checkfraud.info/index.php?view=type', array(), 'moduls', 'blacklist');
		$string = $curl['output'];
		if(!$curl['err']){
			
			$array = @json_decode($string);
			if(isset($array->t0) and is_array($array->t0)){
				foreach($array->t0 as $data){
					$key = 0;
					$value = pn_maxf_mb(pn_strip_input($data), 250);
					if($value){
						$cc = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."blacklist WHERE meta_key='$key' AND meta_value='$value'");
						if($cc == 0){
							$arr = array();
							$arr['meta_value'] = $value;
							$arr['meta_key'] = $key;
							$wpdb->insert($wpdb->prefix.'blacklist', $arr);
							$data_id = $wpdb->insert_id;
							do_action('pn_blacklist_add', $data_id, $arr);
						}
					}
				}
			}
			if(isset($array->t1) and is_array($array->t1)){
				foreach($array->t1 as $data){
					$key = 1;
					$value = pn_maxf_mb(pn_strip_input($data),250);
					if($value){
						$cc = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."blacklist WHERE meta_key='$key' AND meta_value='$value'");
						if($cc == 0){
							$arr = array();
							$arr['meta_value'] = $value;
							$arr['meta_key'] = $key;
							$wpdb->insert($wpdb->prefix.'blacklist', $arr);
							$data_id = $wpdb->insert_id;
							do_action('pn_blacklist_add', $data_id, $arr);							
						}
					}
				}
			}
			if(isset($array->t2) and is_array($array->t2)){
				foreach($array->t2 as $data){
					$key = 2;
					$value = pn_maxf_mb(pn_strip_input(str_replace('+','',$data)),250);
					if($value){
						$cc = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."blacklist WHERE meta_key='$key' AND meta_value='$value'");
						if($cc == 0){
							$arr = array();
							$arr['meta_value'] = $value;
							$arr['meta_key'] = $key;
							$wpdb->insert($wpdb->prefix.'blacklist', $arr);
							$data_id = $wpdb->insert_id;
							do_action('pn_blacklist_add', $data_id, $arr);							
						}
					}
				}
			}
			if(isset($array->t3) and is_array($array->t3)){
				foreach($array->t3 as $data){
					$key = 3;
					$value = pn_maxf_mb(pn_strip_input($data),250);
					if($value){
						$cc = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."blacklist WHERE meta_key='$key' AND meta_value='$value'");
						if($cc == 0){
							$arr = array();
							$arr['meta_value'] = $value;
							$arr['meta_key'] = $key;
							$wpdb->insert($wpdb->prefix.'blacklist', $arr);
							$data_id = $wpdb->insert_id;
							do_action('pn_blacklist_add', $data_id, $arr);							
						}
					}
				}
			}

			_e('Done','pn');	
		}
	
		exit;
	
	}
	
}