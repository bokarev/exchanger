<?php
if( !defined( 'ABSPATH')){ exit(); }

//* * * */02 *  wget --spider http://site.ru/cron.html > /dev/null

/* 
файл cron-заданий
Ставим задание на него. Он распределяет интервалы и в зависимости от них,
запускает нужную функцию, если такая существует.
*/
function go_pn_cron($key=''){
	
	$cron_func = apply_filters('mycron_'.$key, array());
	if(!is_array($cron_func)){ $cron_func = array(); }
	
	foreach($cron_func as $func => $title){
		call_user_func($func);
	}	
	
}

function go_pn_cron_func($action=''){
	if($action){
		$funcs = array();
		$defs = array('now','2min','5min','10min','30min','1hour','3hour','05day','1day');
		foreach($defs as $def){
			$cron_func = apply_filters('mycron_'.$def, array());
			$cron_func = (array)$cron_func;
			foreach($cron_func as $func => $name){
				$funcs[] = $func;
			}
		}
		
		if(in_array($action,$funcs)){ 
			call_user_func($action);
		} else {
			pn_display_mess(__('Error! Invalid command!','pn'));
		}
	}
}

function pn_cron_init_file($action){
	if($action){
		go_pn_cron_func($action);
	} else {
		pn_cron_init();
	}
				
	_e('Done','pn');
	exit;
}


function pn_cron_init(){

	$time = current_time('timestamp');
	$cronsite = get_option('the_cron');
	if(!is_array($cronsite)){ $cronsite = array(); }
	
	$t2min = is_isset($cronsite,'2min');
	$t5min = is_isset($cronsite,'5min');
	$t10min = is_isset($cronsite,'10min');
	$t30min = is_isset($cronsite,'30min');
	$t1hour = is_isset($cronsite,'1hour');
	$t3hour = is_isset($cronsite,'3hour');
	$t05day = is_isset($cronsite,'05day');
	$t1day = is_isset($cronsite,'1day');
	
	$cron = array(
		'now' => 0,
		'2min' => $t2min + (2*60),
		'5min' => $t5min + (5*60),
		'10min' => $t10min + (11*60),
		'30min' => $t30min + (31*60),
		'1hour' => $t1hour + (61*60),
		'3hour' => $t3hour + (3*60*60),
		'05day' => $t05day + (12*60*60) + 5,
		'1day' => $t1day + (24*60*60),
	);
	
	$actions = array();
	
	foreach($cron as $key => $mtime){
		if($mtime < $time){
			$actions[] = $key;
			$cronsite[$key] = $time;
		}
	}
	
	update_option('the_cron',$cronsite);

	foreach($actions as $action){
		go_pn_cron($action);
	}
}	

add_action('init', 'pn_cron_init_all', 3);
function pn_cron_init_all(){
global $premiumbox;
		
	$data = premium_rewrite_data();
	$super_base = $data['super_base'];	

	$matches = '';	
	if(preg_match("/^cron-([a-zA-Z0-9\_]+).html$/", $super_base, $matches ) or $super_base == 'cron.html'){	
		if(check_hash_cron()){	
			header('Content-Type: text/html; charset=utf-8');
			
			$action = trim(is_isset($matches,1));
			if(function_exists('pn_cron_init_file')){
				pn_cron_init_file($action);
			} else {
				_e('Cron function does not exist','pn');
			}	
		}
		exit;
		
	} else {
		
		$cron = intval($premiumbox->get_option('cron'));
		if($cron != 1){
			pn_cron_init();
		}
		
	}
}		