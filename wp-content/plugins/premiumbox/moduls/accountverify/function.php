<?php 
if( !defined( 'ABSPATH')){ exit(); } 

add_action( 'delete_user', 'delete_user_accountverify');
function delete_user_accountverify($user_id){
global $wpdb;

	$items = $wpdb->get_results("SELECT FROM ". $wpdb->prefix ."uv_accounts WHERE user_id = '$user_id'");
	foreach($items as $item){
		$item_id = $item->id;
		do_action('pn_user_accounts_delete_before', $item_id, $item);
		$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."uv_accounts WHERE id = '$item_id'");
		if($result){
			do_action('pn_user_accounts_delete', $item_id, $item);
		}
	}	
}

/* чекер */
add_action('wp_before_admin_bar_render', 'wp_before_admin_bar_render_userverify_acc');
function wp_before_admin_bar_render_userverify_acc() {
global $wp_admin_bar, $wpdb, $premiumbox;
	
    if(current_user_can('administrator') or current_user_can('pn_accountverify')){
	
		$z = $wpdb->query("SELECT id FROM ".$wpdb->prefix."uv_accounts WHERE status = '0'");
		if($z > 0){
			$wp_admin_bar->add_menu( array(
				'id'     => 'new_userverify_ac',
				'href' => admin_url('admin.php?page=pn_usac&mod=1'),
				'title'  => '<div style="height: 32px; width: 22px; background: url('. $premiumbox->plugin_url .'moduls/accountverify/images/verify.png) no-repeat center center"></div>',
				'meta' => array( 'title' => __('Account verification requests','pn').' ('. $z .')' )		
			));	
		}
	
	}
	
}
/* end чекер */

/* настройка к валютам */
add_action('pn_valuts_addform','pn_valuts_addform_userverify_acc', 10, 2);
function pn_valuts_addform_userverify_acc($options, $data){
	
	$has_verify = get_valuts_meta(is_isset($data, 'id'), 'has_verify');
	$help_verify = get_valuts_meta(is_isset($data, 'id'), 'help_verify');
	$verify_files = get_valuts_meta(is_isset($data, 'id'), 'verify_files');
	
	if(isset($options['bottom_title'])){
		unset($options['bottom_title']);
	}
	
	$options[] = array(
		'view' => 'line',
		'colspan' => 2,
	);	
	$options['has_verify'] = array(
		'view' => 'select',
		'title' => __('Ability for account verification','pn'),
		'options' => array('0'=>__('No','pn'), '1'=>__('Yes','pn')),
		'default' => $has_verify,
		'name' => 'has_verify',
	);
	$options['verify_files'] = array(
		'view' => 'input',
		'title' => __('Number of images for upload','pn'),
		'default' => $verify_files,
		'name' => 'verify_files',
	);	
	$options['help_verify'] = array(
		'view' => 'textarea',
		'title' => __('Instruction for account verification','pn'),
		'default' => $help_verify,
		'name' => 'help_verify',
		'width' => '',
		'height' => '100px',
		'ml' => 1,
	);
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);	
	
	return $options;
} 

add_action('pn_valuts_edit_before','pn_valuts_edit_userverify_acc');
add_action('pn_valuts_add','pn_valuts_edit_userverify_acc');
function pn_valuts_edit_userverify_acc($data_id){
	if($data_id){
		
		$has_verify = intval(is_param_post('has_verify'));
		update_valuts_meta($data_id, 'has_verify', $has_verify);

		$verify_files = intval(is_param_post('verify_files'));
		update_valuts_meta($data_id, 'verify_files', $verify_files);		
		
		$help_verify = pn_strip_input(is_param_post_ml('help_verify'));
		update_valuts_meta($data_id, 'help_verify', $help_verify);
		
	}
} 
/* end настройка к валютам */

/* настройка к направлению обмена */
add_action('tab_naps_tab8','tab_naps_tab_userverify_acc',100,2);
function tab_naps_tab_userverify_acc($data, $data_id){
?>	
	<tr>
		<th><?php _e('Verified accounts only','pn'); ?></th>
		<td colspan="2">
			<div class="premium_wrap_standart">
				<?php 
				$verify_account = get_naps_meta($data_id, 'verify_account');
				?>									
				<select name="verify_account" autocomplete="off"> 
					<option value="0" <?php selected($verify_account,0); ?>><?php _e('No','pn'); ?></option>
					<option value="1" <?php selected($verify_account,1); ?>><?php _e('account Send','pn'); ?></option>
					<option value="2" <?php selected($verify_account,2); ?>><?php _e('account Receive','pn'); ?></option>
					<option value="3" <?php selected($verify_account,3); ?>><?php _e('accounts Send and Receive','pn'); ?></option>
				</select>
			</div>
		</td>
	</tr>	
<?php
}

add_action('pn_naps_edit_before','pn_naps_edit_userverify_acc');
add_action('pn_naps_add','pn_naps_edit_userverify_acc');
function pn_naps_edit_userverify_acc($data_id){
	
	$verify_account = intval(is_param_post('verify_account'));
	update_naps_meta($data_id, 'verify_account', $verify_account);	
	
} 
/* end направление обмена */

/* счет проверка верификации */
add_filter('account1_bids','account1_bids_userverify',99, 4);
function account1_bids_userverify($account_bids, $account, $naps, $vd){
global $wpdb;
	
	if(isset($naps->id)){
		$valut_id = $vd->id;
		$data_id = $naps->id;
		$verify_account = is_isset($naps,'verify_account');
		if($verify_account == 1 or $verify_account == 3){
			$ui = wp_get_current_user();
			$user_id = intval($ui->ID);
			if($user_id){
				$cc = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."user_accounts WHERE user_id = '$user_id' AND verify='1' AND accountnum='$account' AND valut_id='$valut_id'");
				if($cc == 0){
					$account_bids = array(
						'error' => 1,
						'error_now' => 1,
						'error_text' => __('account is not verified','pn')
					);					
				}
			} else {
				$account_bids = array(
					'error' => 1,
					'error_now' => 1,
					'error_text' => __('account is not verified','pn')
				);				
			}
		}
	}
	
	return $account_bids;
} 

add_filter('account2_bids','account2_bids_userverify',99, 4);
function account2_bids_userverify($account_bids, $account, $naps, $vd){
global $wpdb;
	
	if(isset($naps->id)){
		$valut_id = $vd->id;
		$data_id = $naps->id;
		$verify_account = is_isset($naps,'verify_account');
		if($verify_account == 2 or $verify_account == 3){
			$ui = wp_get_current_user();
			$user_id = intval($ui->ID);
			if($user_id){
				$cc = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."user_accounts WHERE user_id = '$user_id' AND verify='1' AND accountnum='$account' AND valut_id='$valut_id'");
				if($cc == 0){
					$account_bids = array(
						'error' => 1,
						'error_text' => __('account is not verified','pn')
					);					
				}
			} else {
				$account_bids = array(
					'error' => 1,
					'error_text' => __('account is not verified','pn')
				);				
			}
		}
	}
	
	return $account_bids;
} 
/* end счет проверка верификации */
 
add_filter('onebid_account_give','onebid_account_verify',99,3);
add_filter('onebid_account_get','onebid_account_verify',99,3);
function onebid_account_verify($txtacc, $account, $item){
global $wpdb;	
	
	$account = pn_strip_input($account);
	if($account){
		$user_id = $item->user_id;
		if($user_id){
			$cc = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."user_accounts WHERE user_id = '$user_id' AND verify='1' AND accountnum='$account'");
			if($cc > 0){
				$txtacc .= '<br /> <span class="bgreen">'. __('Verified account','pn') .'</span>';
			}
		}
	}
	
	return $txtacc;
} 