<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Ограничения для IP пользователей[:ru_RU][en_US:]Users IP restrictions[:en_US]
description: [ru_RU:]Ограничения для IP пользователей[:ru_RU][en_US:]Users IP restrictions[:en_US]
version: 1.0
category: [ru_RU:]Направления обменов[:ru_RU][en_US:]Exchange directions[:en_US]
cat: naps
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* BD */
add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_napsip');
function bd_pn_moduls_active_napsip(){
global $wpdb;	
	
/*
naps_lang - языки
maxexip - макс кол-во обменов с одного ip в сутки
*/	
	
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'not_ip'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `not_ip` longtext NOT NULL");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'maxexip'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `maxexip` bigint(20) NOT NULL default '0'");
    }	
	
}
/* end BD */

add_action('tab_naps_tab8', 'napsip_tab_naps_tab8', 30, 2);
function napsip_tab_naps_tab8($data, $data_id){

	$string = pn_strip_input(is_isset($data, 'not_ip'));
	$def = array();
	if(preg_match_all('/\[d](.*?)\[\/d]/s',$string, $match, PREG_PATTERN_ORDER)){
		$def = $match[1];
	}
	?>
	<tr>
		<th><?php _e('Prohibited IP and masks (at the beginning of a new line)','pn'); ?></th>
		<td colspan="2">
			<div class="premium_wrap_standart">
				<textarea name="not_ip" style="width: 100%; height: 100px;"><?php echo join("\n",$def); ?></textarea>
			</div>
		</td>
	</tr>	
	<tr>
		<th><?php _e('Max. amount of exchange orders from same IP','pn'); ?></th>
		<td colspan="2">
			<div class="premium_wrap_standart">
				<input type="text" name="maxexip" style="width: 200px;" value="<?php echo intval(is_isset($data, 'maxexip')); ?>" />
			</div>
		</td>
	</tr>	
	<?php 		
}


add_filter('pn_naps_addform_post', 'napsip_pn_naps_addform_post');
function napsip_pn_naps_addform_post($array){

	$not_ip = explode("\n", is_param_post('not_ip'));
	$item = '';
	foreach($not_ip as $v){
		$v = pn_strip_input($v);
		if($v){
			$item .= '[d]'. $v .'[/d]';
		}
	}
	$array['not_ip'] = $item;
	$array['maxexip'] = intval(is_param_post('maxexip'));
	
	return $array;
}

add_action('admin_menu', 'admin_init_napsip');
function admin_init_napsip(){
global $premiumbox;	
	add_submenu_page("pn_moduls", __('Users IP restrictions','pn'), __('Users IP restrictions','pn'), 'administrator', "pn_napsip", array($premiumbox, 'admin_temp'));
}

add_action('pn_adminpage_title_pn_napsip', 'def_adminpage_title_pn_napsip');
function def_adminpage_title_pn_napsip($page){
	_e('Users IP restrictions','pn');
} 

/* настройки */
add_action('pn_adminpage_content_pn_napsip','def_pn_adminpage_content_pn_napsip');
function def_pn_adminpage_content_pn_napsip(){
global $wpdb;

	$bid_status_list = apply_filters('bid_status_list',array());
	
	$napsip = get_option('napsip');
	if(!is_array($napsip)){ $napsip = array(); }	
?>
<div class="premium_body">
		
	<form method="post" action="<?php pn_the_link_post(); ?>">
		<table class="premium_standart_table">
			<?php
				pn_h3(__('Status settings','pn'), __('Save','pn'));	
				?>
				<tr>
					<th><?php _e('Which orders are considered executed','pn'); ?></th>
					<td>
						<div class="premium_wrap_standart">
							<?php 
							if(is_array($bid_status_list)){
								foreach($bid_status_list as $key => $val){ ?>
									<div><label><input type="checkbox" name="napsip[]" <?php if(in_array($key,$napsip)){ ?>checked="checked"<?php } ?> value="<?php echo $key; ?>" /> <?php echo $val; ?></label></div>
							<?php 
								} 
							}
							?>							
						</div>
					</td>		
				</tr>							
				<?php	
				pn_h3('', __('Save','pn'));								
			?>
		</table>
	</form>
		
</div>	
<?php
} 

/* обработка */
add_action('premium_action_pn_napsip','def_premium_action_pn_napsip');
function def_premium_action_pn_napsip(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator'));
	
	$new_napsip = array();
	$napsip = is_param_post('napsip');
	if(is_array($napsip)){
		foreach($napsip as $v){
			$v = is_status_name($v);
			if($v){
				$new_napsip[] = $v;
			}
		}
	}
	update_option('napsip',$new_napsip);	

	$url = admin_url('admin.php?page=pn_napsip&reply=true');
	wp_redirect($url);
	exit;
} 

add_filter('error_bids', 'error_bids_napsip', 99 ,6);
function error_bids_napsip($error_bids, $account1, $account2, $naps, $vd1, $vd2){
global $wpdb;

	$user_ip = pn_real_ip();

	if(!enable_to_ip($user_ip, $naps->not_ip)){
		$error_bids['error'] = 1;
		$error_bids['error_text'][] = __('Error! For your exchange denied','pn');			
	} else {
		$maxexip = intval(is_isset($naps, 'maxexip'));
		if($maxexip > 0){
			
			$napsip = get_option('napsip');
			if(!is_array($napsip)){ $napsip = array(); }
			$status = array();
			foreach($napsip as $st){
				$status[] = "'". $st ."'";
			}
			$where = '';
			if(count($status) > 0){
				$st_join = join(',',$status);
				$where = " AND status IN($st_join)";
			} 
			
			$time = current_time('timestamp');
			$date = date('Y-m-d 00:00:00',$time);
			$naps_id = $naps->id;
			$now_cou = $wpdb->query("SELECT id FROM ".$wpdb->prefix."bids WHERE user_ip='$user_ip' AND createdate >= '$date' AND status != 'auto' $where AND naps_id='$naps_id'");
			if($now_cou >= $maxexip){
				$error_bids['error'] = 1;
				$error_bids['error_text'][] = __('Error! For your exchange denied','pn');			
			}
		}
	}
	
	return $error_bids;
}