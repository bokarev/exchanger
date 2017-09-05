<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action( 'delete_user', 'delete_user_pp');
function delete_user_pp($user_id){
global $wpdb;

	$user_id = intval($user_id);
    $wpdb->update($wpdb->prefix."users" , array('ref_id'=>'0'), array('ref_id'=>$user_id));
	
	$items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."plinks WHERE user_id = '$user_id'");
	foreach($items as $item){
		$item_id = $item->id;
		do_action('pn_plinks_delete_before', $item_id, $item);
		$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."plinks WHERE id = '$item_id'");
		if($result){
			do_action('pn_plinks_delete', $item_id, $item);
		}
	}	
	
	$items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."payoutuser WHERE user_id = '$user_id'");
	foreach($items as $item){
		$item_id = $item->id;
		do_action('pn_payoutuser_delete_before', $item_id, $item);
		$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."payoutuser WHERE id = '$item_id'");
		if($result){
			do_action('pn_payoutuser_delete_after', $item_id, $item);
		}
	}
	
	$wpdb->query("DELETE FROM ". $wpdb->prefix. "archive_data WHERE item_id = '$user_id' AND meta_key IN('plinks','pbids','pbids_sum','pbids_exsum')");
	
}

add_action( 'pn_user_register', 'pn_register_user_pp');
function pn_register_user_pp($user_id) {
global $wpdb;
	
	$user_id = intval($user_id);
	$ref_id = intval(get_mycookie('ref_id')); if($ref_id < 0){ $ref_id = 0; }
	if($ref_id){
		$wpdb->update($wpdb->prefix."users" , array('ref_id'=>$ref_id), array('ID'=>$user_id));
	}	

}

add_action('init','init_pp', 10);
function init_pp(){
global $user_ID, $wpdb, $premiumbox;	
	
	$ref_id = intval(is_param_get(stand_refid()));
	if($ref_id > 0 and !$user_ID){
		$col = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."users WHERE ID='$ref_id'");
		if(isset($col->ID)){
				
			$time = current_time('timestamp');
			$date = current_time('mysql');	
				
			$clife = intval($premiumbox->get_option('partners','clife'));
			if($clife < 1){ $clife = 365; }
			$cookie_time = $time + ($clife*24*60*60);	
				
			add_mycookie('ref_id', $ref_id, $cookie_time); 
			$ip = pn_real_ip(); 
			$browser = pn_maxf(pn_strip_input(is_isset($_SERVER,'HTTP_USER_AGENT')),250);
			$referer = pn_maxf(pn_strip_input(is_isset($_SERVER,'HTTP_REFERER')),500);
			$wpdb->insert( $wpdb->prefix.'plinks' , array( 'user_id' => $ref_id, 'user_login'=> is_user($col->user_login),'pbrowser' => $browser, 'pdate' => $date, 'pip' => $ip, 'prefer'=> $referer));
			
		}
	}	

}

/* настройки сохранения */
add_action( 'profile_update', 'pn_profile_update_pp');
function pn_profile_update_pp($user_id){
global $wpdb;
	$user_id = intval($user_id);
	if(isset($_POST['pn_profile_update_pp'])){
		if(current_user_can('administrator') or current_user_can('pn_pp')){ 
			
			$array = array();
			$array['ref_id'] = intval(is_param_post('ref_id'));
			$array['partner_pers'] = is_my_money(is_param_post('partner_pers'));
			$wpdb->update($wpdb->prefix."users" , $array, array('ID'=>$user_id));

		}
	}	
}

/* поля при редактировании пользователя */
	
add_action( 'show_user_profile', 'pn_edit_user_pp');
add_action( 'edit_user_profile', 'pn_edit_user_pp');
function pn_edit_user_pp($user){
	$user_id = $user->ID;
		
	if(current_user_can('administrator') or current_user_can('pn_pp')){ 
			
		$ref_id = $user->ref_id;
		$users = get_users('exclude='.$user_id);
		$partner_pers = is_my_money($user->partner_pers);
		?>
		<input type="hidden" name="pn_profile_update_pp" value="1" />
		
		<h3><?php _e('Affiliate program','pn'); ?></h3>
	    <table class="form-table">
			<tr>
				<th>
					<label for="ref_id"><?php _e('Referral','pn'); ?></label>
				</th>
				<td>
					<select name="ref_id" id="ref_id" autocomplete="off">
						<option value="0"><?php _e('No','pn'); ?></option>
						<?php foreach($users as $us){ ?>
							<option value="<?php echo $us->ID; ?>" <?php selected($ref_id,$us->ID); ?>><?php echo is_user($us->user_login); ?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<th>
					<label for="partner_pers"><?php _e('Individual affiliate reward (%)','pn'); ?></label>
				</th>
				<td>
					<input type="text" name="partner_pers" id="partner_pers" autocomplete="off" value="<?php echo $partner_pers;?>" />%
				</td>
			</tr>
			<tr>
				<th>
					<?php _e('Transitions','pn'); ?>
				</th>
				<td>
					<?php echo get_partner_plinks($user_id); ?>
				</td>
			</tr>

			<tr>
				<th>
					<?php _e('Affiliate exchange','pn'); ?>
				</th>
				<td> 
					<?php echo get_user_count_refobmen($user_id);?> (<?php echo get_user_sum_refobmen($user_id); ?> <?php echo cur_type(); ?>)
				</td>
			</tr>	

			<tr>
				<th>
					<?php _e('Affiliate interest','pn'); ?>
				</th>
				<td>
					<?php echo get_user_pers_refobmen($user_id);?>%
				</td>
			</tr>

			<tr>
				<th>
					<?php _e('Amount on your balance','pn'); ?>
				</th>
				<td>
					<?php echo get_partner_money($user_id);?> <?php echo cur_type(); ?>
				</td>
			</tr>

			<tr>
				<th>
					<?php _e('All time earned','pn'); ?>
				</th>
				<td>
					<?php echo get_partner_earn_all($user_id); ?> <?php echo cur_type(); ?>
				</td>
			</tr>

			<tr>
				<th>
					<?php _e('Paid in total','pn'); ?>
				</th>
				<td>
					<?php echo get_partner_payout($user_id); ?> <?php echo cur_type(); ?>
				</td>
			</tr>			
        </table>		
		<?php
	}
}

add_filter('manage_users_columns', 'pp_users_columns');
function pp_users_columns($columns) { 
		
	$columns['partnermoney'] = __('Amount on your balance','pn');		
	
	return $columns;
}

add_filter('manage_users_custom_column', 'pp_manage_status_column', 10, 3);	
function pp_manage_status_column($empty='', $column_name, $user_id) {
global $premiumbox;

	if($column_name == 'partnermoney'){
		
        $minpay = is_my_money($premiumbox->get_option('partners','minpay'),2);
	    $balans = get_partner_money_now($user_id);
	    $dbalans = 0;
	    if($balans >= $minpay){
            $dbalans = $balans;
        } 
		
	    return $balans.' <span class="bgreen" title="'. __('Awaits for payout','pn') .'">('.$dbalans.')</span> '. cur_type() .'';
		
	}	
	
	return $empty;
} 