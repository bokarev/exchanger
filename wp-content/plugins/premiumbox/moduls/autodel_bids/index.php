<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Авто-удаление неоплаченных заявок[:ru_RU][en_US:]Auto-removal of unpaid requests[:en_US]
description: [ru_RU:]Авто-удаление неоплаченных заявок с возможность установить индивидуальное время удаления[:ru_RU][en_US:]Auto-removal of unpaid requests with the ability to set individual time of removal[:en_US]
version: 1.0
category: [ru_RU:]Направления обменов[:ru_RU][en_US:]Exchange directions[:en_US]
cat: naps
*/
$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* BD */
add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_autodelbids');
function bd_pn_moduls_active_autodelbids(){
global $wpdb;	
	
	$table_name= $wpdb->prefix ."autodel_bids_time";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT ,
		`naps_id` bigint(20) NOT NULL default '0',
		`enable_autodel` int(1) NOT NULL default '0',
		`cou_hour` varchar(20) NOT NULL default '0',
		`cou_minute` varchar(20) NOT NULL default '0',
		`statused` longtext NOT NULL,
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);
	
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."autodel_bids_time LIKE 'statused'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."autodel_bids_time ADD `statused` longtext NOT NULL");
    }	
	
}

add_action('pn_bd_activated', 'bd_pn_moduls_migrate_autodelbids');
function bd_pn_moduls_migrate_autodelbids(){
global $wpdb;

	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."autodel_bids_time LIKE 'statused'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."autodel_bids_time ADD `statused` longtext NOT NULL");
    }	
}
/* end BD */

add_action('admin_menu', 'pn_adminpage_autodelbids');
function pn_adminpage_autodelbids(){
global $premiumbox;		
	add_submenu_page("pn_naps", __('Automatic deletion of unpaid orders','pn'), __('Automatic deletion of unpaid orders','pn'), 'administrator', "pn_autodelbids", array($premiumbox, 'admin_temp'));
}

add_action('pn_adminpage_title_pn_autodelbids', 'def_adminpage_title_pn_autodelbids');
function def_adminpage_title_pn_autodelbids($page){
	_e('Automatic deletion of unpaid orders','pn');
}

add_action('pn_adminpage_content_pn_autodelbids','def_adminpage_content_pn_autodelbids');
function def_adminpage_content_pn_autodelbids(){
global $wpdb, $premiumbox;

	$options = array();
	$options['top_title'] = array(
		'view' => 'h3',
		'title' => __('Automatic deletion of unpaid orders','pn'),
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	$options['enable'] = array(
		'view' => 'select',
		'title' => __('Automatic deletion of unpaid orders','pn'),
		'options' => array('0'=>__('No','pn'),'1'=>__('Yes','pn')),
		'default' => $premiumbox->get_option('autodel','enable'),
		'name' => 'enable',
	);	
	$options['statused'] = array(
		'view' => 'user_func',
		'func_data' => array(),
		'func' => 'pn_statused_autodelbids_pages_option',
	);	
	$options['ad_h'] = array(
		'view' => 'input',
		'title' => __('How many hours', 'pn'),
		'default' => $premiumbox->get_option('autodel','ad_h'),
		'name' => 'ad_h',
	);
	$options['ad_m'] = array(
		'view' => 'input',
		'title' => __('How many minutes', 'pn'),
		'default' => $premiumbox->get_option('autodel','ad_m'),
		'name' => 'ad_m',
	);
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);			
	pn_admin_one_screen('', $options);  
}  

function pn_statused_autodelbids_pages_option($data){
global $premiumbox;

	$status = $premiumbox->get_option('autodel','statused');
	if(!is_array($status)){ $status = array(); }				
	$lists = array(
		'new' => __('new order','pn'),
		'techpay' => __('when user entered payment section','pn'),
		'payed' => __('user marked order as paid','pn'),
	);
	?>					
	<tr>
		<th><label><?php _e('Delete orders with status','pn'); ?></label></th>
		<td>
			<div class="premium_wrap_standart">		
				<?php foreach($lists as $key => $title){ ?>
					<div><label><input type="checkbox" name="statused[]" <?php if(in_array($key, $status)){ echo 'checked="checked"'; }?> value="<?php echo $key; ?>" /> <?php echo $title; ?></label></div>
				<?php } ?>			
			</div>
		</td>		
	</tr>						
	<?php	
}

add_action('premium_action_pn_autodelbids','def_premium_action_pn_autodelbids');
function def_premium_action_pn_autodelbids(){
global $wpdb, $premiumbox;	

	only_post();
	pn_only_caps(array('administrator'));
	
	$options = array();
	$options['enable'] = array(
		'name' => 'enable',
		'work' => 'int',
	);	
	$options['ad_h'] = array(
		'name' => 'ad_h',
		'work' => 'int',
	);
	$options['ad_m'] = array(
		'name' => 'ad_m',
		'work' => 'int',
	);	
	$data = pn_strip_options('', $options);
	foreach($data as $key => $val){
		$premiumbox->update_option('autodel', $key, $val);
	}				

	$premiumbox->update_option('autodel', 'statused', is_param_post('statused'));
	
	$back_url = is_param_post('_wp_http_referer');
	$back_url .= '&reply=true';
			
	wp_safe_redirect($back_url);
	exit;
} 

add_filter('list_tabs_naps','list_tabs_naps_autodelbids');
function list_tabs_naps_autodelbids($list_tabs_naps){
	$list_tabs_naps['autodelbids'] = __('Removing unpaid orders','pn');
	
	return $list_tabs_naps;
}

add_action('tab_naps_autodelbids','naps_tab_autodelbids',10,2);
function naps_tab_autodelbids($data, $data_id){	
global $wpdb, $premiumbox;
 	if(isset($data->id)){ 
		$data_id = $data->id;
		$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."autodel_bids_time WHERE naps_id='$data_id'"); 
		$cou_hour = intval(is_isset($item, 'cou_hour'));
		$cou_minute = intval(is_isset($item, 'cou_minute'));
		$lists = array(
			'new' => __('new order','pn'),
			'techpay' => __('when user entered payment section','pn'),
			'payed' => __('user marked order as paid','pn'),
		);		
	?>
		<tr>
			<th><?php _e('Delete orders with status','pn'); ?></th>
			<td colspan="2">
				<div class="premium_wrap_standart">				
					<div class="cf_div">
						<div style="font-weight: 500;"><label><input type="checkbox" class="check_all" name="" value="1" /> <?php _e('Check all/Uncheck all','pn'); ?></label></div>
						<?php
						$string = trim(is_isset($item,'statused'));
						$def = array();
						if(preg_match_all('/\[d](.*?)\[\/d]/s',$string, $match, PREG_PATTERN_ORDER)){
							$def = $match[1];
						}
						foreach($lists as $key => $title){ 
						?>	
							<div><label><input type="checkbox" name="autodelbids_statused[]" <?php if(in_array($key,$def)){ ?>checked="checked"<?php } ?> value="<?php echo $key; ?>" /> <?php echo $title;?></label></div>	
						<?php
						}	
						?>
					</div>				
				</div>
			</td>
		</tr>	
	
		<tr>
			<th><?php _e('Unpaid orders removal time','pn'); ?></th>
			<td>
				<strong><?php _e('How many hours', 'pn'); ?></strong>
				<div class="premium_wrap_standart">
					<input type="text" name="autodelbids_cou_hour" style="width: 50px;" value="<?php echo $cou_hour; ?>" />
				</div>			
			</td>
			<td>
				<strong><?php _e('How many minutes', 'pn'); ?></strong>
				<div class="premium_wrap_standart">
					<input type="text" name="autodelbids_cou_minute" style="width: 50px;" value="<?php echo $cou_minute; ?>" />	
				</div>			
			</td>
		</tr>			
	<?php 
	} 
} 	 

add_action('pn_naps_edit_before', 'pn_naps_edit_autodelbids', 10, 2);
add_action('pn_naps_add', 'pn_naps_edit_autodelbids', 10, 2);
function pn_naps_edit_autodelbids($data_id, $array){
global $wpdb;	

	if($data_id){
		$cou_hour = intval(is_param_post('autodelbids_cou_hour'));
		$cou_minute = intval(is_param_post('autodelbids_cou_minute'));		
		if($cou_hour != 0 or $cou_minute != 0){
			$arr = array();
			$arr['naps_id'] = $data_id;
			$arr['cou_hour'] = $cou_hour;
			$arr['cou_minute'] = $cou_minute;
			$autodelbids_statused = is_param_post('autodelbids_statused');
			$statused = '';
			if(is_array($autodelbids_statused)){
				foreach($autodelbids_statused as $st){
					$st = is_status_name($st);
					if($st){
						$statused .= '[d]'. $st .'[/d]';
					}
				}
			}
			$arr['statused'] = $statused;			
			$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."autodel_bids_time WHERE naps_id='$data_id'"); 
			if(isset($item->id)){
				$wpdb->update($wpdb->prefix."autodel_bids_time", $arr, array('id'=>$item->id));
			} else {
				$wpdb->insert($wpdb->prefix."autodel_bids_time", $arr);
			}
		} else {
			$wpdb->query("DELETE FROM ".$wpdb->prefix."autodel_bids_time WHERE naps_id = '$data_id'");
		}
	}
}

add_action('pn_naps_copy', 'pn_naps_copy_autodelbids', 10, 2);
function pn_naps_copy_autodelbids($last_id, $data_id){
global $wpdb;	
	$data = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."autodel_bids_time WHERE naps_id='$last_id'");
	foreach($data as $item){
		$arr = array();
		$arr['naps_id'] = $data_id;
		$arr['enable_autodel'] = $item->enable_autodel;
		$arr['cou_hour'] = $item->cou_hour;
		$arr['cou_minute'] = $item->cou_minute;
		$arr['statused'] = $item->statused;
		$wpdb->insert($wpdb->prefix.'autodel_bids_time', $arr);
	}	
}

add_filter('bid_instruction_tags','autodelbids_bid_instruction_tags', 1000, 2);
function autodelbids_bid_instruction_tags($instruction, $item){
global $wpdb, $premiumbox;
	if(strstr($instruction,'[bid_delete_time]') and isset($item->status)){
		$status = $item->status;
		$naps_id = $item->naps_id;
		if($status == 'new'){
			$editdate = $item->editdate;
			$del_date = __('undefined','pn');
			$date_format = get_option('date_format');
			$time_format = get_option('time_format');
			
			if($premiumbox->get_option('autodel','enable') == 1){
				$hour = intval($premiumbox->get_option('autodel','ad_h'));
				$minuts = intval($premiumbox->get_option('autodel','ad_m'));
				$statused = $premiumbox->get_option('autodel','statused');
				if(!is_array($statused)){ $statused = array(); }				
				$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."autodel_bids_time WHERE naps_id='$naps_id'");
				if(isset($data->id)){
					$hour = intval($data->cou_hour);
					$minuts = intval($data->cou_minute);
					$string = trim(is_isset($data,'statused'));
					if(preg_match_all('/\[d](.*?)\[\/d]/s',$string, $match, PREG_PATTERN_ORDER)){
						$statused = $match[1];
					} 					
				}	
				$sec = 0;
				if($hour > 0){
					$sec = $hour * 60 * 60;
				}
				if($minuts > 0){
					$sec = $sec + ($minuts * 60);
				}				
				$editdate = strtotime($editdate);
				$del_time = $editdate + $sec;
				if(in_array($item->status, $statused)){
					$del_date = date("{$date_format}, {$time_format}", $del_time);
				}
			}
			
			$instruction = str_replace('[bid_delete_time]', $del_date, $instruction);
		} 
	}	
	
	return $instruction;
}

function del_notpaybids(){
global $wpdb, $premiumbox;

	if($premiumbox->get_option('autodel','enable') == 1){
		$time = current_time('timestamp');
		$date = current_time('mysql');	

		$hour = intval($premiumbox->get_option('autodel','ad_h'));
		$minuts = intval($premiumbox->get_option('autodel','ad_m'));
		$statused = $premiumbox->get_option('autodel','statused');
		if(!is_array($statused)){ $statused = array(); }
		
		$naps_autodel = array();
		$in_status = array();
		foreach($statused as $st){
			$st = is_status_name($st);
			if($st){
				$in_status[] = "'".$st."'";
			}
		}
		
		$autodels = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."autodel_bids_time");
		foreach($autodels as $au){
			$naps_id = $au->naps_id;
			$naps_autodel[$naps_id]['hour'] = intval($au->cou_hour);
			$naps_autodel[$naps_id]['minuts'] = intval($au->cou_minute);
			$string = trim($au->statused);
			if(preg_match_all('/\[d](.*?)\[\/d]/s',$string, $match, PREG_PATTERN_ORDER)){
				$naps_autodel[$naps_id]['statused'] = $match[1];
				if(is_array($match[1])){
					foreach($match[1] as $st){
						$st = is_status_name($st);
						if($st){
							$in_status[] = "'".$st."'";
						}						
					}
				}
			}  
		}
		
		$in_status = array_unique($in_status);
		if(count($in_status) > 0){
			$in_join = join(',',$in_status);
			$items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."bids WHERE status IN($in_join)");
			foreach($items as $item){
				$id = $item->id;
				$editdate = $item->editdate;
				$create_time = strtotime($editdate);
				$naps_id = $item->naps_id;
				if(isset($naps_autodel[$naps_id])){
					$naps = $naps_autodel[$naps_id];
					$hour = intval(is_isset($naps,'hour'));
					$minuts = intval(is_isset($naps,'minuts'));
					if(isset($naps['statused'])){
						$statused = $naps['statused'];
					}
				}

				$sec = 0;
				if($hour > 0){
					$sec = $hour * 60 * 60;
				}
				if($minuts > 0){
					$sec = $sec + ($minuts * 60);
				}			
				$del_time = $time - $sec;
				if($create_time < $del_time){
					if(in_array($item->status, $statused)){
						$result = $wpdb->update($wpdb->prefix.'bids', array('status'=>'delete','editdate'=> $date), array('id'=>$item->id));
						if($result == 1){
							do_action('change_bidstatus_all', 'delete', $item->id, $item, 'admin','system');
							do_action('change_bidstatus_delete', $item->id, $item, 'admin','system');								
						}
					}
				}
			}
		}
	}
} 

add_filter('mycron_now', 'mycron_now_delautobids');
function mycron_now_delautobids($filters){
	$filters['del_notpaybids'] = __('Removing unpaid orders','pn');
	return $filters;
}