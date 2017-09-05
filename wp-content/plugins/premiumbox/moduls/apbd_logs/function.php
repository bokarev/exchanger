<?php
if( !defined( 'ABSPATH')){ exit(); }

function insert_apbd($tbl_name, $tbl_check, $id, $array, $ldata){
global $wpdb;

	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);	
	
	$data = array();
	$last_data = array();
	
	$checks = array();
	if(is_array($tbl_check)){
		foreach($tbl_check as $k => $v){
			$checks[$k] = $k;
		}
	}
	
	if(is_array($array)){
		foreach($array as $k => $v){
			if(in_array($k, $checks)){
				$data[$k] = $v;
			}
		}
	}
	
	$trans_type = 0;
	if(is_object($ldata)){
		$trans_type = 1;
		foreach($ldata as $k => $v){
			if(in_array($k, $checks)){
				$last_data[$k] = $v;
			}
		}
	}
	$data = @serialize($data);
	$last_data = @serialize($last_data);
	
	if($data != $last_data){
		$arr = array();
		$arr['tbl_name'] = $tbl_name;
		$arr['item_id'] = $id;
		$arr['trans_type'] = $trans_type;
		$arr['trans_date'] = current_time('mysql');
		$arr['old_data'] = $last_data;
		$arr['new_data'] = $data;
		$arr['user_id'] = $user_id;
		$arr['user_login'] = is_isset($ui,'user_login');
		$wpdb->insert($wpdb->prefix.'db_admin_logs', $arr);
	}	
}

function view_apbd($tbl_name, $tbl_check){
global $wpdb;

	$data_id = intval(is_param_get('item_id'));
	if($data_id > 0){
		$trans = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."db_admin_logs WHERE item_id = '$data_id' AND tbl_name = '$tbl_name' ORDER BY trans_date DESC");	
		if(count($trans) > 0){
			$checks = array();
			if(is_array($tbl_check)){
				foreach($tbl_check as $k => $v){
					$checks[$k] = $k;
				}	
			}
			?>
			<div class="premium_single">
				<div style="overflow-y: auto; max-height: 300px;">
					<?php 
					foreach($trans as $item){ 
					$old_data = maybe_unserialize($item->old_data);
					$new_data = maybe_unserialize($item->new_data);
					?>
						<div class="premium_single_line">
							<strong><?php echo get_mytime($item->trans_date,'d.m.Y H:i'); ?></strong> | <a href="<?php echo admin_url('user-edit.php?user_id='.$item->user_id); ?>"><?php echo is_user($item->user_login); ?></a> |
							<?php
							$items = array();
							if(is_array($new_data)){
								foreach($new_data as $k => $v){
									if(in_array($k, $checks)){
										$d1 = pn_strip_input(ctv_ml($v));
										$d2 = pn_strip_input(ctv_ml(is_isset($old_data, $k)));
										if($d1 != $d2){
											$items[] = '<strong>'. is_isset($tbl_check, $k) .':</strong> '. $d1 .' <span class="bred">('. $d2 .')</span>';
										}
									}
								} 
							}
							echo join(' | ', $items);
							?>
						</div>		
					<?php } ?>
				</div>
			</div>
			<?php
		}
	}	
}