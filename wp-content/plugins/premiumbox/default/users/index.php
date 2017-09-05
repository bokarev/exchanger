<?php
if( !defined( 'ABSPATH')){ exit(); }

add_filter('sanitize_user','pn_sanitize_user');
function pn_sanitize_user($login){
	$login = is_user($login);
	return $login;
}

/* действия при регистрации */	
add_action( 'pn_user_register', 'user_pn_user_register');
function user_pn_user_register($user_id) {
global $wpdb;
	
	$array = array();
	$array['user_registered'] = current_time('mysql');
	$wpdb->update($wpdb->prefix ."users", $array, array('ID'=>$user_id));
			
	$user_browser = pn_maxf(pn_strip_input(is_isset($_SERVER,'HTTP_USER_AGENT')),500);
	add_user_meta( $user_id, 'user_browser', $user_browser, true );
			
	$user_ip = pn_real_ip();
	add_user_meta( $user_id, 'user_ip', $user_ip, true );

	add_user_meta( $user_id, 'user_bann', 0 , true );
	add_user_meta( $user_id, 'admin_comment', '' , true );
			
}

/* удаление цветовой схемы */
add_action('admin_head', 'pn_unset_profile_colorcheme');
function pn_unset_profile_colorcheme() {
	global $_wp_admin_css_colors;
	$_wp_admin_css_colors = 0;
}

/* время визита в админке */
add_action('admin_init','admin_init_operator');
function admin_init_operator(){
global $user_ID;	
	
	$user_id = intval($user_ID);
	$time = current_time('timestamp');
	update_user_meta($user_id, 'admin_time_last', $time) or add_user_meta($user_id, 'admin_time_last', $time, true);
	
}	

/* удаление ненужных полей */
add_filter('user_contactmethods','pn_unset_profile_details',10,1);
function pn_unset_profile_details( $conts ) {
	unset($conts['yim']);
	unset($conts['aim']);
	unset($conts['jabber']);
	
	$conts['second_name'] = __('Second name','pn');
	$conts['user_phone'] = __('Phone no.','pn');
	$conts['user_skype'] = __('Skype','pn');
	$conts['user_passport'] = __('Passport number','pn');
		
	return $conts;
}

/* свой стиль для админки юзеров */

add_action('admin_enqueue_scripts', 'admin_enqueue_scripts_user');
function admin_enqueue_scripts_user(){
global $premiumbox;
		
	$plguin_url = $premiumbox->plugin_url;
	$premium_url = get_premium_url();
	$time = current_time('timestamp');
		
	$screen = get_current_screen();	
	if($screen->id == 'users'){
		wp_enqueue_script("standart_admin_user_script", $plguin_url . "default/users/js/user.js?vers=".$time, false, $premiumbox->plugin_version);
	}
			
}
	
add_action('admin_footer', 'pn_admin_user_footer');
function pn_admin_user_footer(){
	$screen = get_current_screen(); 
	if($screen->id == 'users'){
		?>
			<div class="standart_window js_techwindow" id="window_user_comment">
				<div class="standart_windowins">
					<div class="standart_window_close"></div>
					<div class="standart_window_title"><?php _e('Comment to user','pn'); ?></div>
					
					<div class="standart_windowcontent">
						<form action="<?php pn_the_link_ajax('stand_admin_comment'); ?>" class="user_ajax_form" method="post">

							<p><textarea id="hide_user_comment" name="comment"></textarea></p>
							<p><input type="submit" name="submit" class="button-primary" value="<?php _e('Save','pn'); ?>" /></p>
							<input type="hidden" id="hide_user_id" name="user_id" value="" />
							
						</form>
					</div>
				</div>
			</div>			
		<?php
	}
} 

add_action('premium_action_stand_admin_comment', 'pn_premium_action_stand_admin_comment');
function pn_premium_action_stand_admin_comment(){
	$log = array();
	$log['response'] = '';
	$log['status'] = '';
	$log['status_code'] = 0;
	$log['status_text'] = '';
	
	$id = intval(is_param_post('user_id'));
	$text = pn_maxf_mb(pn_strip_input(is_param_post('comment')),1000);
	if($id){
		if(current_user_can('administrator')){
			update_user_meta( $id, 'admin_comment', $text) or add_user_meta($id, 'admin_comment', $text, true);		
			$log['status'] = 'success';
			$log['response'] = $text;
		} else {
			$log['status'] = 'error'; 
			$log['status_code'] = 1;
			$log['status_text'] = __('Error! insufficient privileges!','pn');		
		}
	} else {
		$log['status'] = 'error'; 
		$log['status_code'] = 1;
		$log['status_text'] = __('Error! ID is not specified','pn');
	} 
	echo json_encode($log);		
	exit;	
} 	

/* стандартные настройки сохранения */

add_action( 'profile_update', 'pn_profile_update');
function pn_profile_update($user_id){
	
	global $change_ld_account;
	$change_ld_account = 'true';
	
	if(isset($_POST['pn_profile_update'])){
		if(current_user_can('administrator')){ 
			
			global $wpdb;
			$prefix = $wpdb->prefix;
			$level = get_user_meta($user_id, $prefix.'user_level', true);       

			$user_bann = intval(is_param_post('user_bann'));
			update_user_meta( $user_id, 'user_bann', $user_bann) or add_user_meta($user_id, 'user_bann', $user_bann, true);
		
			$admin_comment = pn_maxf_mb(pn_strip_input(is_param_post('admin_comment')),1000);
			update_user_meta( $user_id, 'admin_comment', $admin_comment) or add_user_meta($user_id, 'admin_comment', $admin_comment, true);
					
			$array = array();
			$array['user_discount'] = is_my_money(is_param_post('user_discount'));
			$array['sec_lostpass'] = intval(is_param_post('sec_lostpass'));
			$array['sec_login'] = intval(is_param_post('sec_login'));
			$array['email_login'] = intval(is_param_post('email_login'));
			$array['enable_ips'] = pn_maxf(pn_strip_input(is_param_post('enable_ips')),1500);
			$wpdb->update($wpdb->prefix ."users", $array, array('ID'=>$user_id));
		}
	}	
}

/* стандартные поля при редактировании пользователя */

add_action( 'show_user_profile', 'pn_edit_user');
add_action( 'edit_user_profile', 'pn_edit_user');
function pn_edit_user($user){
	$user_id = $user->ID;
	if(current_user_can('administrator')){ 
		$user_bann = get_user_meta($user_id, 'user_bann', true);
		$user_browser = pn_strip_input(get_user_meta($user_id, 'user_browser', true));
		$user_ip = pn_strip_input(get_user_meta($user_id, 'user_ip', true));
		$admin_comment = pn_strip_input(get_user_meta($user_id, 'admin_comment', true));
		$user_discount = is_my_money(is_isset($user,'user_discount'));
		?>
		<input type="hidden" name="pn_profile_update" value="1" />
		
		<h3><?php _e('User information','pn'); ?></h3>
	    <table class="form-table">
			<tr>
				<th>
					<label><?php _e('Orders','pn'); ?></label>
				</th>
				<td>
					<a href="<?php echo admin_url('admin.php?page=pn_bids&iduser='. $user_id); ?>" class="button" target="_blank"><?php _e('User orders','pn'); ?></a>
			   </td>
			</tr>		
			<tr>
				<th>
					<label for="user_ip"><?php _e('IP','pn'); ?></label>
				</th>
				<td>
					<input type="text" name="user_ip" id="user_ip" disabled class="regular-text" autocomplete="off" value="<?php echo $user_ip;?>" />
			   </td>
			</tr>
			<tr>
				<th>
					<label for="user_browser"><?php _e('Browser','pn'); ?></label>
				</th>
				<td>
					<input type="text" name="user_browser" id="user_browser" disabled class="regular-text" autocomplete="off" value="<?php echo $user_browser;?>" />
			   </td>
			</tr>
			
			<tr>
				<th>
					<label for="user_discount"><?php _e('Personal discount','pn'); ?></label>
				</th>
				<td>
					<input type="text" name="user_discount" id="user_discount" autocomplete="off" value="<?php echo $user_discount;?>" />%
			   </td>
			</tr>
			
			<tr>
				<th>
					<?php _e('Discount (%)','pn'); ?>
				</th>
				<td>
					<?php echo get_user_discount($user_id);?>%
			   </td>
			</tr>			

			<tr>
				<th>
					<label><?php _e('User exchange list','pn'); ?></label>
				</th>
				<td>
					<?php echo get_user_count_exchanges($user_id); ?> ( <?php echo get_user_sum_exchanges($user_id); ?> <?php echo cur_type(); ?>)
			   </td>
			</tr>			
			
			<tr>
				<th>
					<label for="admin_comment"><?php _e('Comment','pn'); ?></label>
				</th>
				<td>
					<textarea name="admin_comment" id="admin_comment" rows="5" cols="30"><?php echo $admin_comment; ?></textarea>
			   </td>
			</tr>
			<tr>
				<th>
					<label for="user_bann"><?php _e('Ban','pn'); ?></label>
				</th>
				<td>
					<select name="user_bann" id="user_bann" autocomplete="off">
						<option value='0'><?php _e('not banned','pn'); ?></option>
						<option value='1' <?php selected($user_bann, 1); ?>><?php _e('banned','pn'); ?></option>
					</select>
			   </td>
			</tr>			
        </table>

		<h3><?php _e('Security settings','pn'); ?></h3>
	    <table class="form-table">
			<tr>
				<th>
					<label for="sec_lostpass"><?php _e('Password recovery','pn'); ?>:</label>
				</th>
				<td>
					<select name="sec_lostpass" id="sec_lostpass">
						<option value="0"><?php _e('No','pn'); ?></option>
						<option value="1" <?php selected($user->sec_lostpass, 1); ?>><?php _e('Yes','pn'); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th>
					<label for="sec_login"><?php _e('Log in notification by e-mail','pn'); ?>:</label>
				</th>
				<td>
					<select name="sec_login" id="sec_login">
						<option value="0"><?php _e('No','pn'); ?></option>
						<option value="1" <?php selected($user->sec_login, 1); ?>><?php _e('Yes','pn'); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th>
					<label for="email_login"><?php _e('Two-factor authorization by one-time ref','pn'); ?>:</label>
				</th>
				<td>
					<select name="email_login" id="email_login">
						<option value="0"><?php _e('No','pn'); ?></option>
						<option value="1" <?php selected($user->email_login, 1); ?>><?php _e('Yes','pn'); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th>
					<label><?php _e('Allowed IP address (in new line)','pn'); ?>:</label>
				</th>
				<td>
					<textarea name="enable_ips" style="width: 100%; height: 100px;"><?php echo pn_strip_input(is_isset($user,'enable_ips')); ?></textarea>
				</td>
			</tr>			
        </table>		
		<?php
	}
}

/* стандартные поля пользователя */
add_filter('manage_users_sortable_columns','pn_manage_users_sortable_columns');
function pn_manage_users_sortable_columns($sortable_columns){
	
	$sortable_columns['rdate'] = 'user_registered';
	$sortable_columns['rid'] = 'ID';
	$sortable_columns['admin_time'] = 'admin_time_last';
	
	return $sortable_columns;
}	
	
add_filter('manage_users_columns', 'pn_users_columns',1);
function pn_users_columns($columns) {

	$columns = array();
	$columns['cb']  = '<input type="checkbox" />';
	$columns['rid'] = 'ID';
	$columns['username'] = __( 'Username' );
	$columns['rdate'] = __('Registration date','pn');
	$columns['email']    = __( 'E-mail' );
	$columns['role']     = __( 'Role' );
	$columns['admin_time']    = __( 'Admin Panel','pn' );
		
	$columns['user_phone'] = __('Phone no.','pn');
	$columns['user_skype'] = __('Skype','pn');
	
	$columns['countobmen'] = __('User exchange list','pn');
	$columns['userskidka'] = __('Discount (%)','pn');
	
	$columns['user_browser'] = __('Browser','pn');
	$columns['user_ip'] = __('IP','pn');
	$columns['user_bann'] = __('Ban','pn');
	$columns['admin_comment'] = __('Comment','pn');
		
	return $columns;
}
 
add_filter('manage_users_custom_column', 'pn_manage_users_custom_column', 10, 3);
function pn_manage_users_custom_column($empty='', $column_name, $user_id){
		
	if($column_name == 'rdate'){
		$ui = get_userdata($user_id);
		return get_mytime($ui->user_registered,'d.m.Y, H:i');
	} 
		
	if($column_name == 'user_browser'){
		$user_browser = get_browser_name(get_user_meta($user_id, 'user_browser', true), __('Unknown','pn'));
		return $user_browser;
	}
	
	if($column_name == 'countobmen'){

	   return get_user_count_exchanges($user_id).'<br />(<strong>'. get_user_sum_exchanges($user_id) .'</strong>&nbsp;'. cur_type() .')';
		
	}
	if($column_name == 'userskidka'){

	   return get_user_discount($user_id).'%';
		
	}		
	
	if($column_name == 'user_phone'){
		$user_phone = is_phone(get_user_meta($user_id, 'user_phone', true));
		return $user_phone;
	}

	if($column_name == 'user_skype'){
		$user_skype = pn_strip_input(get_user_meta($user_id, 'user_skype', true));
		return $user_skype;
	}	
	
	if($column_name == 'admin_time'){
		$admin_time_last = pn_strip_input(get_user_meta($user_id, 'admin_time_last', true));
		if($admin_time_last){
			return date("d.m.Y, H:i:s",$admin_time_last);
		}
	}	

	if($column_name == 'user_ip'){
		$user_ip = pn_strip_input(get_user_meta($user_id, 'user_ip', true));
		return $user_ip;
	}

	if($column_name == 'user_bann'){
		$user_bann = get_user_meta($user_id, 'user_bann', true);
		if($user_bann == 1){		
			return '<span class="bred">'. __('banned','pn') .'</span>';
		} else {
			return __('not banned','pn');
		}
	}

	if($column_name == 'admin_comment'){
		$admin_comment = pn_strip_input(get_user_meta($user_id, 'admin_comment', true));
		$cl='';
		if($admin_comment){ 
			$cl='has_comment'; 
		}	
			
		return '
		<div class="user_comment '. $cl .'" id="ucomment-'. $user_id .'"">
			<div class="user_comment_text">'. $admin_comment .'</div>
		</div>';
	}		
	
	if($column_name == 'rid'){
		return $user_id;	
	}		
		
	return $empty;	
}

/* стандартный виджет, с пользователями в админке */
add_action('wp_dashboard_setup', 'standart_user_wp_dashboard_setup' );
function standart_user_wp_dashboard_setup() {
	wp_add_dashboard_widget('standart_user_dashboard_widget', __('Users in Admin Panel','pn'), 'dashboard_users_in_admin_panel');
}

function dashboard_users_in_admin_panel(){
global $wpdb;

	$time = current_time('timestamp') - 60;
	$users = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."users LEFT OUTER JOIN ". $wpdb->prefix ."usermeta ON (". $wpdb->prefix ."users.ID = ". $wpdb->prefix ."usermeta.user_id) WHERE meta_key = 'admin_time_last' AND meta_value >= '$time'");

	foreach($users as $us){
		echo '<strong>'. is_user($us->user_login) . '</strong> ('. date("d.m.Y, H:i:s", pn_strip_input($us->meta_value)) .')';
		echo '<hr />';
	}
}

/*
Удаление админ-бара для тех, кому нельзя
Разлогивание заблокированных пользователей
*/
add_action('init', 'pn_hide_admin_bar');
function pn_hide_admin_bar(){
	if (!current_user_can('read')) {	
		add_filter('show_admin_bar', '__return_false');	
	}
		
	if(!current_user_can('administrator')){
		global $or_site_url;
		$ui = wp_get_current_user();
		$user_bann = intval(is_isset($ui, 'user_bann'));
		if($user_bann == 1){
			wp_logout();
			wp_redirect($or_site_url);
			exit();
		}			
	}
}

global $premiumbox;
$premiumbox->include_patch(__FILE__, 'settings');