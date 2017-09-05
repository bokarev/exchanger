<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_naps_delete', 'pn_naps_delete_blackbroker');
function pn_naps_delete_blackbroker($item_id){
global $wpdb;	
	$wpdb->query("DELETE FROM ".$wpdb->prefix."blackbrokers_naps WHERE naps_id = '$item_id'");
}

add_action('pn_naps_copy', 'pn_naps_copy_blackbroker', 1, 2);
function pn_naps_copy_blackbroker($last_id, $new_id){
global $wpdb;

	$broker = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."blackbrokers_naps WHERE naps_id='$last_id'"); 
	if(isset($broker->id)){
		$arr = array();
		$arr['naps_id'] = $new_id;
		$arr['site_id'] = intval($broker->site_id);
		$arr['step_column'] = intval($broker->step_column);
		$arr['step'] = is_my_money($broker->step);
		$arr['cours1'] = is_my_money($broker->cours1);
		$arr['cours2'] = is_my_money($broker->cours2);
		$arr['min_sum'] = is_my_money($broker->min_sum);
		$arr['max_sum'] = is_my_money($broker->max_sum);
		$arr['item_from'] = is_xml_value($broker->item_from);
		$arr['item_to'] = is_xml_value($broker->item_to);
		$wpdb->insert($wpdb->prefix.'blackbrokers', $arr);
	}
}

add_action('tab_naps_tab2', 'tab_naps_tab2_blackbroker');
function tab_naps_tab2_blackbroker($data){	
global $wpdb;
	if(isset($data->id)){ 
		$data_id = $data->id;
		
		$step_column = 0;
		$site_id = 0;
		
		$sites = array();
		$sites[0] = '--'. __('No item','pn') .'--';
		$blackbrokers = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."blackbrokers");
		foreach($blackbrokers as $blackbroker){
			$sites[$blackbroker->id] = pn_strip_input($blackbroker->title);
		}
		
		$broker = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."blackbrokers_naps WHERE naps_id='$data_id'"); 
		if(isset($broker->id)){
			$step_column = $broker->step_column;
			$site_id = $broker->site_id;			
		}
	?>
		<tr>
			<th><?php _e('Auto Broker','pn'); ?></th>
			<td>
				<div class="premium_wrap_standart">
					<div style="margin: 0 0 10px 0;">
						<select name="bbr_site_id" autocomplete="off">
							<?php foreach($sites as $sites_id => $site_title){ ?>
								<option value="<?php echo $sites_id; ?>" <?php selected($site_id,$sites_id); ?>><?php echo $site_title; ?></option>
							<?php } ?>
						</select>					
					</div>
					<div>
						<select name="bbr_step_column" autocomplete="off">
							<option value="0" <?php selected($step_column,0); ?>><?php _e('Correct rate Send','pn'); ?></option>
							<option value="1" <?php selected($step_column,1); ?>><?php _e('Correct rate Receive','pn'); ?></option>
						</select>					
					</div>					
				</div>			
			</td>
			<td>
				<div class="premium_wrap_standart">
					<div><input type="text" name="bbr_step" style="width: 100px;" value="<?php echo is_my_money(is_isset($broker, 'step')); ?>" /> <?php _e('Step','pn'); ?></div>
					<div><input type="text" name="bbr_min_sum" style="width: 100px;" value="<?php echo is_my_money(is_isset($broker, 'min_sum')); ?>" /> <?php _e('Min rate','pn'); ?></div>
					<div><input type="text" name="bbr_max_sum" style="width: 100px;" value="<?php echo is_my_money(is_isset($broker, 'max_sum')); ?>" /> <?php _e('Max rate','pn'); ?></div>
				</div>							
			</td>			
		</tr>
		<tr>
			<th><?php _e('Standard rate','pn'); ?> (<?php _e('Auto Broker','pn'); ?>)</th>
			<td>
				<div class="premium_wrap_standart">
					<input type="text" name="bbr_cours1" style="width: 200px;" value="<?php echo is_my_money(is_isset($broker, 'cours1')); ?>" />
				</div>			
			</td>
			<td>
				<div class="premium_wrap_standart">
					<input type="text" name="bbr_cours2" style="width: 200px;" value="<?php echo is_my_money(is_isset($broker, 'cours2')); ?>" />	
				</div>			
			</td>
		</tr>
		<tr>
			<th><?php _e('XML currency notation','pn'); ?> (<?php _e('Auto Broker','pn'); ?>)</th>
			<td>
				<div class="premium_wrap_standart">
					<input type="text" name="bbr_item1" style="width: 200px;" value="<?php echo is_xml_value(is_isset($broker, 'item_from')); ?>" />
				</div>			
			</td>
			<td>
				<div class="premium_wrap_standart">
					<input type="text" name="bbr_item2" style="width: 200px;" value="<?php echo is_xml_value(is_isset($broker, 'item_to')); ?>" />	
				</div>			
			</td>
		</tr>		
	<?php }
}

add_action('pn_naps_edit', 'pn_naps_edit_blackbroker', 10, 2);
add_action('pn_naps_add', 'pn_naps_edit_blackbroker', 10, 2);
function pn_naps_edit_blackbroker($data_id, $array){
global $wpdb;	

	if($data_id){
		$site_id = intval(is_param_post('bbr_site_id'));
		if($site_id > 0){
			$arr = array();
			$arr['naps_id'] = $data_id;
			$arr['site_id'] = intval(is_param_post('bbr_site_id'));
			$arr['step_column'] = intval(is_param_post('bbr_step_column'));
			$arr['step'] = is_my_money(is_param_post('bbr_step'));
			$arr['min_sum'] = is_my_money(is_param_post('bbr_min_sum'));
			$arr['max_sum'] = is_my_money(is_param_post('bbr_max_sum'));
			$arr['cours1'] = is_my_money(is_param_post('bbr_cours1'));
			$arr['cours2'] = is_my_money(is_param_post('bbr_cours2'));
			$arr['item_from'] = is_xml_value(is_param_post('bbr_item1'));
			$arr['item_to'] = is_xml_value(is_param_post('bbr_item2'));			
			
			$broker = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."blackbrokers_naps WHERE naps_id='$data_id'"); 
			if(isset($broker->id)){
				$wpdb->update($wpdb->prefix."blackbrokers_naps", $arr, array('id'=>$broker->id));
			} else {
				$wpdb->insert($wpdb->prefix."blackbrokers_naps", $arr);
			}
		} else {
			$wpdb->query("DELETE FROM ".$wpdb->prefix."blackbrokers_naps WHERE naps_id = '$data_id'");
		}
		request_blackbroker();
	}
}