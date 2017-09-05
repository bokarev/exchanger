<?php
if( !defined( 'ABSPATH')){ exit(); } 

function get_usvedoc_temp($id, $field_id){
global $wpdb;
$temp = '';

	$id = intval($id);
	if($id < 1){ $id = 0; }
	$userverify = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."uv_field_user WHERE uv_id='$id' AND uv_field='$field_id'");
	if(isset($userverify->id)){
		$file = pn_strip_input($userverify->uv_data);
		if($file){
			$temp .= '
				<div class="usvefilelock">
					<a href="'. get_usve_doc($userverify->id) .'" target="_blank">'. $file .'</a>
				';
				
				if(is_admin()){
					$temp .= ' | <a href="'. get_usve_doc($userverify->id) .'" target="_blank">'. __('Download','pn') .'</a> | <a href="'. get_usve_doc_view($userverify->id) .'" target="_blank">'. __('View','pn') .'</a>';
				}
				
				$temp .= '
				</div>	
			';
		} 
	}
	
	return $temp;
}

function pn_verify_uv($key){
global $premiumbox;	

	$uf = $premiumbox->get_option('usve','verify_fields');
	return intval(is_isset($uf, $key));
}

/* админ-панель юзеры */
add_filter('manage_users_sortable_columns','userverify_manage_users_sortable_columns');
function userverify_manage_users_sortable_columns($sortable_columns){
	$sortable_columns['user_verify'] = 'user_verify';
	return $sortable_columns;
}

add_filter('manage_users_columns', 'userverify_users_columns',99);
function userverify_users_columns($columns) {	
	$columns['user_verify'] = __('Verification','pn'); 
	return $columns;
}

add_filter('manage_users_custom_column', 'userverify_manage_status_column', 99, 3);
function userverify_manage_status_column($empty='', $column_name, $user_id) {
	if($column_name == 'user_verify'){
		$ui = get_userdata($user_id);
		if(isset($ui->user_verify) and $ui->user_verify == 1){
			return '<span class="bgreen">'. __('verified','pn') .'</span>';
		} else {
			return '<span class="bred">'. __('not verified','pn') .'</span>';
		}
	}	
	return $empty;
}

add_action( 'profile_update', 'userverify_profile_update');
function userverify_profile_update($user_id){
	if(isset($_POST['userverify_profile_update']) and current_user_can('administrator')){	
		global $wpdb;
		$array = array();
		$array['user_verify'] = intval(is_param_post('user_verify'));
		$wpdb->update($wpdb->prefix ."users", $array, array('ID'=>$user_id));
	}	
}

add_action( 'show_user_profile', 'userverify_edit_user');
add_action( 'edit_user_profile', 'userverify_edit_user');
function userverify_edit_user($user){
global $wpdb;
    $user_id = $user->ID;
	if(current_user_can('administrator')){
		if(isset($user->user_verify)){
		?>
		<input type="hidden" name="userverify_profile_update" value="1" />
		
		<h3><?php _e('Verification','pn'); ?></h3> 
	    <table class="form-table">
			<tr>
				<th>
					<label><?php _e('Status','pn'); ?></label>
				</th>
				<td>
					<select name="user_verify" id="user_verify" autocomplete="off">
						<option value='0'><?php _e('not verified','pn'); ?></option>
						<option value='1' <?php selected($user->user_verify, 1); ?>><?php _e('verified','pn'); ?></option>
					</select>				
			   </td>
			</tr>
			<?php
			if($user->user_verify == 1){
				$fields = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."uv_field LEFT OUTER JOIN ". $wpdb->prefix ."uv_field_user ON(".$wpdb->prefix."uv_field.id = ". $wpdb->prefix ."uv_field_user.uv_field) WHERE user_id='$user_id' AND fieldvid='1' ORDER BY uv_order ASC");
				if(count($fields) > 0){
				?>
				<tr>
					<th>
						<label><?php _e('Verification files','pn'); ?></label>
					</th>
					<td>
						<?php
						foreach($fields as $field){
							?>
							<div><strong><?php echo pn_strip_input(ctv_ml($field->title)); ?>:</strong> <a href="<?php echo get_usve_doc_view($field->id); ?>" target="_blank"><?php _e('View','pn'); ?></a></div>
							<?php
						}	
						?>
				   </td>
				</tr>
				<?php
				}
			}
			?>
        </table>
		<?php
		}
	}
} 

add_action( 'delete_user', 'delete_user_userverify');
function delete_user_userverify($user_id){
global $wpdb;

	$usves = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."userverify WHERE user_id = '$user_id'");
	foreach($usves as $data){
		$id = $data->id;
		do_action('pn_usve_delete_before', $id, $data);
		$result = $wpdb->query("DELETE FROM ". $wpdb->prefix ."userverify WHERE id = '$id'");
		if($result){
			do_action('pn_usve_delete', $id, $data);
		}
	}
	
}
/* end админ-панель юзеры */

/* чекер */
add_action('wp_before_admin_bar_render', 'wp_before_admin_bar_render_userverify');
function wp_before_admin_bar_render_userverify() {
global $wp_admin_bar, $wpdb, $premiumbox;
	
    if(current_user_can('administrator') or current_user_can('pn_userverify')){
	
		$z = $wpdb->query("SELECT id FROM ".$wpdb->prefix."userverify WHERE status = '1'");
		if($z > 0){
			$wp_admin_bar->add_menu( array(
				'id'     => 'new_userverify',
				'href' => admin_url('admin.php?page=pn_usve&mod=1'),
				'title'  => '<div style="height: 32px; width: 22px; background: url('. $premiumbox->plugin_url .'moduls/userverify/images/verify.png) no-repeat center center"></div>',
				'meta' => array( 'title' => __('Requests for profile verification','pn').' ('. $z .')' )		
			));	
		}
	
	}
	
}
/* end чекер */

/* скидка юзера */
add_filter('user_discount','userverify_user_discount',99,2);
function userverify_user_discount($sk, $user_id){
global $premiumbox;	
	if($user_id){
		$ui = get_userdata($user_id);
		if(isset($ui->user_verify) and $ui->user_verify == 1){
			$verifysk = is_my_money($premiumbox->get_option('usve','verifysk'));
			$sk = $sk + $verifysk;
		}
	}
	return $sk;
}  
/* end скидка юзера */

/* cron */
function delete_last_userverify(){
global $wpdb;	
	
	$my_dir = wp_upload_dir();
	$time = current_time('timestamp') - (24*60*60);
	$date = date('Y-m-d H:i:s', $time); 
	$usves = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."userverify WHERE status='0' AND createdate < '$date'");
	foreach($usves as $item){
		$id = $item->id;
		do_action('pn_usve_delete_before', $id, $item);
		$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."userverify WHERE id = '$id'");
		if($result){
			do_action('pn_usve_delete', $id, $item);
		}
	}			
	
}

function clear_visible_userverify(){
	$my_dir = wp_upload_dir();
	$path = $my_dir['basedir'].'/usveshow/';
	full_del_dir($path);
}

add_filter('mycron_1day', 'mycron_1day_userverify');
function mycron_1day_userverify($filters){
	$filters['delete_last_userverify'] = __('Removal of blank requests waiting for verification','pn');
	return $filters;
}

add_filter('mycron_1hour', 'mycron_1hour_userverify');
function mycron_1hour_userverify($filters){
	$filters['clear_visible_userverify'] = __('Delete verification files','pn');
	return $filters;
}
/* end cron */

/* отключаем поля на сайте */
add_filter('disabled_account_form_line', 'userverify_disabled_account_form_line',99,3);
function userverify_disabled_account_form_line($disabled,$name, $ui){
	
	if(isset($ui->user_verify) and $ui->user_verify == 1){
		if(
			$name == 'first_name' and pn_verify_uv('first_name') or 
			$name == 'second_name' and pn_verify_uv('second_name') or 
			$name == 'last_name' and pn_verify_uv('last_name') or 
			$name == 'user_passport' and pn_verify_uv('user_passport') or
			$name == 'user_phone' and pn_verify_uv('user_phone') or
			$name == 'user_skype' and pn_verify_uv('user_skype') or
			$name == 'user_email' and pn_verify_uv('user_email')
		){
			return 1;
		}
	}
	
	return $disabled;
}
/* end отключаем поля на сайте */

/* настройка к направлению обмена */
add_action('tab_naps_tab8','tab_naps_tab_userverify',100,2);
function tab_naps_tab_userverify($data, $data_id){
?>	
	<tr>
		<th><?php _e('Verified users only','pn'); ?></th>
		<td>
			<div class="premium_wrap_standart">
				<?php 
					$verify = get_naps_meta($data_id, 'verify');
				?>									
				<select name="verify" autocomplete="off"> 
					<option value="0" <?php selected($verify,0); ?>><?php _e('No','pn'); ?></option>
					<option value="1" <?php selected($verify,1); ?>><?php _e('Yes','pn'); ?></option>
					<option value="2" <?php selected($verify,2); ?>><?php _e('If exchange amount is more than','pn'); ?></option>
				</select>
			</div>
		</td>
		<td>
			<div class="premium_wrap_standart">
				<?php 
				$verify_sum = get_naps_meta($data_id, 'verify_sum');
				?>									
				<input type="text" name="verify_sum" value="<?php echo is_my_money($verify_sum); ?>" /> <strong><?php _e('Exchange amount for Send','pn'); ?></strong>
			</div>		
		</td>
	</tr>	
<?php
}
 
add_action('pn_naps_edit_before','pn_naps_edit_userverify');
add_action('pn_naps_add','pn_naps_edit_userverify');
function pn_naps_edit_userverify($data_id){
	
	$verify = intval(is_param_post('verify'));
	update_naps_meta($data_id, 'verify', $verify);
	
	$verify_sum = is_my_money(is_param_post('verify_sum'));
	update_naps_meta($data_id, 'verify_sum', $verify_sum);	
	
} 
/* end направление обмена */

/* xml файл */
add_filter('file_xml_lines', 'file_xml_lines_userverify', 100, 4);
function file_xml_lines_userverify($lines, $ob, $vd1, $vd2){
	
	$verify = intval(get_naps_meta($ob->id, 'verify'));
	if($verify){
		if(isset($lines['param'])){
			$lines['param'] = $lines['param'].', verifying';
		} else {
			$lines['param'] = 'verifying';
		}
	}
	
	return $lines;
} 
/* end xml файл */

/* проверка всех данных на соответствие аккаунту */
add_filter('cf_auto_form_value','cf_auto_form_value_userverify',99,5);
function cf_auto_form_value_userverify($cauv,$value,$item,$naps, $cdata){
global $wpdb;
	
	$cf_auto = $item->cf_auto;
	$sum = $cdata['summ1_dc'];

	$verify = intval(is_isset($naps,'verify'));
	$verify_sum = is_my_money(is_isset($naps,'verify_sum'));
	if($verify == 1 or $verify == 2 and $sum >= $verify_sum){
		$ui = wp_get_current_user();
		$user_id = intval($ui->ID);
		if($user_id){
			if(isset($ui->user_verify)){	
				if($ui->user_verify == 1){
					
					$err = 0;
					
					if($cf_auto == 'first_name' and $ui->first_name != $value and pn_verify_uv('first_name')){
						$err = 1;
					} elseif($cf_auto == 'last_name' and $ui->last_name != $value and pn_verify_uv('last_name')){
						$err = 1;
					} elseif($cf_auto == 'second_name' and $ui->second_name != $value and pn_verify_uv('second_name')){
						$err = 1;
					} elseif($cf_auto == 'user_passport' and $ui->user_passport != $value and pn_verify_uv('user_passport')){
						$err = 1;
					} elseif($cf_auto == 'user_phone' and $ui->user_phone != $value and pn_verify_uv('user_phone')){
						$err = 1;
					} elseif($cf_auto == 'user_skype' and $ui->user_skype != $value and pn_verify_uv('user_skype')){
						$err = 1;
					} elseif($cf_auto == 'user_email' and $ui->user_email != $value and pn_verify_uv('user_email')){
						$err = 1;																		
					} 	

					if($err ==1){
						$cauv = array(
							'error' => 1,
							'error_text' => __('not verified','pn')
						);						
					}
				
				}
			}
		}
	}
	return $cauv;
} 
/* end проверка всех данных на соответствие аккаунту */
				
/* проверка при обмене */
add_filter('error_bids', 'error_bids_verify', 99 ,9);
function error_bids_verify($error_bids, $account1, $account2, $naps, $vd1, $vd2, $auto_data, $unmetas, $cdata){

	$sum = $cdata['summ1_dc'];
	$verify = intval(is_isset($naps,'verify'));
	$verify_sum = is_my_money(is_isset($naps,'verify_sum'));
	if($verify == 1 or $verify == 2 and $sum >= $verify_sum){
		$ui = wp_get_current_user();
		$user_id = intval($ui->ID);
		if($user_id){
			if(isset($ui->user_verify)){
				if($ui->user_verify != 1){	
					$error_bids['error'] = 1;
					$error_bids['error_text'][] = __('Error! Exchange is available for verified users only','pn');
				}
			}
		} else { 
			$error_bids['error'] = 1;
			$error_bids['error_text'][] = __('Error! Exchange is available for verified users only','pn');
		}
	}
	
	return $error_bids;
}

add_filter('onebid_icons','onebid_icons_verify',10,2);
function onebid_icons_verify($onebid_icon, $item){
global $premiumbox;
	
	if(isset($item->user_id) and $item->user_id > 0){
		$ui = get_userdata($item->user_id);
		if(isset($ui->user_verify) and $ui->user_verify == 1){
			$onebid_icon['userverify'] = array(
				'type' => 'label',
				'title' => __('Verified user','pn'),
				'image' => $premiumbox->plugin_url . 'images/userverify.png',
			);	
		}
	}
	
	return $onebid_icon;
}