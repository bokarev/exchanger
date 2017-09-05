<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_content_pn_blackbroker','blackbroker_pn_adminpage_content_pn_cron',9);
add_action('pn_adminpage_content_pn_cron','blackbroker_pn_adminpage_content_pn_cron',9);
add_action('pn_adminpage_content_pn_parser','blackbroker_pn_adminpage_content_pn_cron',9);
function blackbroker_pn_adminpage_content_pn_cron(){
?>
	<div class="premium_default_window">
		<?php _e('Cron URL for updating rates in the Auto Broker module','pn'); ?><br /> 
		<a href="<?php echo get_site_url_or(); ?>/request-blackbroker.html<?php echo get_hash_cron('?'); ?>" target="_blank"><?php echo get_site_url_or(); ?>/request-blackbroker.html<?php echo get_hash_cron('?'); ?></a>
	</div>	
<?php
} 

function request_blackbroker(){
global $wpdb;
	$sites = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."blackbrokers");
	$now_brokers = array();
	$brokers = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."blackbrokers_naps"); 
	foreach($brokers as $item){
		$now_brokers[$item->site_id][] = $item;
	}
	foreach($sites as $s_item){
		$s_id = $s_item->id;
		if(isset($now_brokers[$s_id]) and count($now_brokers[$s_id]) > 0){
			$s_url = $s_item->url;
			if($s_url){
				$curl = get_curl_parser($s_url, '', 'moduls', 'blackbroker');
				if(is_array($curl) and !$curl['err'] and strstr($curl['output'],'<?xml')){
					$string = $curl['output'];
					$res = @simplexml_load_string($string);
					if(is_object($res) and isset($res->item) and is_object($res->item)){
						foreach($now_brokers[$s_id] as $broker_data){
							update_naps_blackbroker($broker_data, $res);	
						}
					}
				}
			}
		}
	}	
}

add_action('myaction_request_blackbroker', 'def_request_blackbroker');
function def_request_blackbroker(){
	if(check_hash_cron()){
		request_blackbroker();
	}
}

