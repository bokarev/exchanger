<?php
if( !defined( 'ABSPATH')){ exit(); }
 
/* 
Подключаем к меню
*/
add_action('admin_menu', 'pn_adminpage_reserv_config');
function pn_adminpage_reserv_config(){
global $premiumbox;	
	add_submenu_page("pn_config", __('Reserve settings','pn'), __('Reserve settings','pn'), 'administrator', "pn_reserv_config", array($premiumbox, 'admin_temp'));
}

add_action('pn_adminpage_title_pn_reserv_config', 'pn_adminpage_title_pn_reserv_config');
function pn_adminpage_title_pn_reserv_config($page){
	_e('Reserve settings','pn');
} 

/* настройки */
add_action('pn_adminpage_content_pn_reserv_config','def_pn_adminpage_content_pn_reserv_config');
function def_pn_adminpage_content_pn_reserv_config(){
global $wpdb;

	$bid_status_list = apply_filters('bid_status_list',array());
	
	$reserv_out = get_option('reserv_out');
	if(!is_array($reserv_out)){ $reserv_out = array(); }
	
	$reserv_in = get_option('reserv_in');
	if(!is_array($reserv_in)){ $reserv_in = array(); }
	
	$reserv_auto = get_option('reserv_auto');
	if(!is_array($reserv_auto)){ $reserv_auto = array(); }	
?>
<div class="premium_body">
		
	<form method="post" action="<?php pn_the_link_post(); ?>">
		<table class="premium_standart_table">
			<?php
				pn_h3(__('Reserve settings','pn'), __('Save','pn'));	
				?>
				<tr>
					<th><?php _e('Change reserve Receive when auto reserve function for currency To receive is enabled or when Order possesses own status','pn'); ?></th>
					<td>
						<div class="premium_wrap_standart">
							<?php 
							if(is_array($bid_status_list)){
								foreach($bid_status_list as $key => $val){ ?>
									<div><label><input type="checkbox" name="reserv_auto[]" <?php if(in_array($key,$reserv_auto)){ ?>checked="checked"<?php } ?> value="<?php echo $key; ?>" /> <?php echo $val; ?></label></div>
							<?php 
								} 
							}
							?>							
						</div>
					</td>		
				</tr>				
				<tr>
					<th><?php _e('Change reserve Send when Order has status','pn'); ?></th>
					<td>
						<div class="premium_wrap_standart">
							<?php 
							if(is_array($bid_status_list)){
								foreach($bid_status_list as $key => $val){ ?>
									<div><label><input type="checkbox" name="reserv_in[]" <?php if(in_array($key,$reserv_in)){ ?>checked="checked"<?php } ?> value="<?php echo $key; ?>" /> <?php echo $val; ?></label></div>
							<?php 
								} 
							}
							?>							
						</div>
					</td>		
				</tr>				
				<tr>
					<th><?php _e('Change reserve Receive when Order has status','pn'); ?></th>
					<td>
						<div class="premium_wrap_standart">
							<?php 
							if(is_array($bid_status_list)){
								foreach($bid_status_list as $key => $val){ ?>
								<div><label><input type="checkbox" name="reserv_out[]" <?php if(in_array($key,$reserv_out)){ ?>checked="checked"<?php } ?> value="<?php echo $key; ?>" /> <?php echo $val; ?></label></div>
							<?php 
								} 
							}
							?>
						</div>					
					</td>		
				</tr>				
				<?php	
				do_action('pn_reserv_config_option');
				
				pn_h3('', __('Save','pn'));								
			?>
		</table>
	</form>
		
</div>	
<?php
} 

/* обработка */
add_action('premium_action_pn_reserv_config','def_premium_action_pn_reserv_config');
function def_premium_action_pn_reserv_config(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator'));
	
	$new_reserv_auto = array();
	$reserv_auto = is_param_post('reserv_auto');
	if(is_array($reserv_auto)){
		foreach($reserv_auto as $v){
			$v = is_status_name($v);
			if($v){
				$new_reserv_auto[] = $v;
			}
		}
	}
	update_option('reserv_auto',$new_reserv_auto);	
	
	$new_reserv_out = array();
	$reserv_out = is_param_post('reserv_out');
	if(is_array($reserv_out)){
		foreach($reserv_out as $v){
			$v = is_status_name($v);
			if($v){
				$new_reserv_out[] = $v;
			}
		}
	}
	update_option('reserv_out',$new_reserv_out);

	$new_reserv_in = array();
	$reserv_in = is_param_post('reserv_in');
	if(is_array($reserv_in)){
		foreach($reserv_in as $v){
			$v = is_status_name($v);
			if($v){
				$new_reserv_in[] = $v;
			}
		}
	}
	update_option('reserv_in',$new_reserv_in);

	do_action('pn_reserv_config_option_post');

	$url = admin_url('admin.php?page=pn_reserv_config&reply=true');
	wp_redirect($url);
	exit;
} 