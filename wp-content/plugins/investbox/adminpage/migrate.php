<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_inex_migrate', 'pn_adminpage_title_inex_migrate');
function pn_adminpage_title_inex_migrate($page){
	_e('Migration','pn');
} 

/* настройки */
add_action('pn_adminpage_content_inex_migrate','def_pn_adminpage_content_inex_migrate');
function def_pn_adminpage_content_inex_migrate(){
?>
<div class="premium_body">
	<table class="premium_standart_table">
		<?php
		pn_h3(sprintf(__('Migration (if version is lesser than %s)','pn'),'3.2'), '');
		
		$r=0;
		while($r++<3){
		?>		
		<tr>
			<td>		
				<input name="submit" type="submit" class="button pn_prbar" data-count-url="<?php pn_the_link_ajax('inex_migrate_step_count'); ?>&step=1_<?php echo $r; ?>" data-title="<?php printf(__('Step %s','pn'),$r); ?>" value="<?php printf(__('Step %s','pn'),$r); ?>" />	
				<input name="submit" type="submit" class="button pn_prbar" data-count-url="<?php pn_the_link_ajax('inex_migrate_step_count'); ?>&step=1_<?php echo $r; ?>&tech=1" data-title="<?php printf(__('Step %s','pn'),$r); ?>" value="<?php printf(__('Technical step %s','pn'),$r); ?>" />		
			</td>
		</tr>
		<?php 
		} 
		?>
	</table>
</div>
	
<div class="premium_shadow js_techwindow"></div>
<div class="prbar_wrap js_techwindow">
	<div class="prbar_wrap_ins">
		<div class="prbar_close"></div>
		<div class="prbar_title"></div>
		<div class="prbar_content">
		
			<div class="prbar_num">
				<?php printf(__('Found: %1s %2s %3s requests','pn'), '<input type="text" name="" class="prbar_num_count" value="','0','" />'); ?>
			</div>
			<div class="prbar_control">
				<div class="prbar_input">
					<?php _e('Perform','pn'); ?>: <input type="text" name="" class="prbar_count" value="100" />
				</div>
				<div class="prbar_submit"><?php _e('Run','pn'); ?></div>
					<div class="premium_clear"></div>
			</div>
			
			<div class="prbar_ind"><div class="prbar_ind_abs"></div><div class="prbar_ind_text">0%</div></div>
			<div class="prbar_log_wrap">
				<div class="prbar_log"></div>			
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
jQuery(function($){
	$(document).PrBar({ 
		trigger: '.pn_prbar',
		start_title: '<?php _e('determining the number of requests','pn'); ?>...',
		end_title: '<?php _e('number of requests defined','pn'); ?>',
		line_text: '<?php _e('%now% of %max% steps completed','pn'); ?>',
		line_success: '<?php _e('step %now% is successful','pn'); ?>',
		end_progress: '<?php _e('action is completed','pn'); ?>',
		success: function(res){
			res.prop('disabled', true);
		}
	});
});
</script>
<?php
}

add_action('premium_action_inex_migrate_step_count','def_premium_action_inex_migrate_step_count');
function def_premium_action_inex_migrate_step_count(){
global $wpdb;	

	only_post();

	$log = array();
	$log['status'] = '';
	$log['status_code'] = 0; 
	$log['status_text'] = '';
	$log['count'] = 0;
	$log['link'] = '';
	
	$step = is_param_get('step');
	$tech = intval(is_param_get('tech'));
	if(current_user_can('administrator')){
		$count = 0;
		
		if(!$tech){
			
			if($step == '1_1'){
				$count = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."inex_system");				
			}
			if($step == '1_2'){
				$count = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."inex_tars");				
			}			
			if($step == '1_3'){
				$count = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."inex_deposit");				
			}
			
		}
		
		$log['status'] = 'success';
		$log['count'] = $count;
		$log['link'] = pn_link_ajax('inex_migrate_step_request').'&step='.$step;
		$log['status_text'] = __('Ok!','pn');

	} else {
		$log['status'] = 'error';
		$log['status_code'] = 1; 
		$log['status_text'] = __('Error! insufficient privileges!','pn');
	}
	
	echo json_encode($log);
	exit;	
}

add_action('premium_action_inex_migrate_step_request','def_premium_action_inex_migrate_step_request');
function def_premium_action_inex_migrate_step_request(){
global $wpdb, $premiumbox;	

	only_post();

	$log = array();
	$log['status'] = '';
	$log['status_code'] = 0; 
	$log['status_text'] = '';
	$log['count'] = 0;
	$log['link'] = '';
	
	$step = is_param_get('step');
	$idspage = intval(is_param_post('idspage'));
	$limit = intval(is_param_post('limit')); if($limit < 1){ $limit = 1; }
	$offset = ($idspage - 1) * $limit;
	if(current_user_can('administrator')){
		
		if($step == '1_1'){	 /*****************/

			$datas = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."inex_system LIMIT {$offset},{$limit}");
			foreach($datas as $data){
				$data_id = $data->id;
				$gid = intval($data->gid);		
				$arr = array();
				$old = array(
					'1' => 'perfectmoney_usd',
					'2' => 'perfectmoney_eur',
					'3' => 'okpay_usd',
					'4' => 'okpay_rub',
					'5' => 'payeer_usd',
					'6' => 'payeer_rub',
					'100' => 'webmoney_usd',
					'101' => 'webmoney_rub',	
				);
				$newgid = trim(is_isset($old, $gid));
				if($newgid){
					$arr['gid'] = $newgid;
				}
				if(count($arr) > 0){
					$wpdb->update($wpdb->prefix.'inex_system', $arr, array('id'=>$data_id));
				}
			} 			
			
		}

		if($step == '1_2'){	 /*****************/

			$datas = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."inex_tars LIMIT {$offset},{$limit}");
			foreach($datas as $data){
				$data_id = $data->id;
				$gid = intval($data->gid);		
				$arr = array();
				$old = array(
					'1' => 'perfectmoney_usd',
					'2' => 'perfectmoney_eur',
					'3' => 'okpay_usd',
					'4' => 'okpay_rub',
					'5' => 'payeer_usd',
					'6' => 'payeer_rub',
					'100' => 'webmoney_usd',
					'101' => 'webmoney_rub',	
				);
				$newgid = trim(is_isset($old, $gid));
				if($newgid){
					$arr['gid'] = $newgid;
				}
				if(count($arr) > 0){
					$wpdb->update($wpdb->prefix.'inex_tars', $arr, array('id'=>$data_id));
				}
			} 			
			
		}

		if($step == '1_3'){	 /*****************/

			$datas = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."inex_deposit LIMIT {$offset},{$limit}");
			foreach($datas as $data){
				$data_id = $data->id;
				$gid = intval($data->gid);		
				$arr = array();
				$old = array(
					'1' => 'perfectmoney_usd',
					'2' => 'perfectmoney_eur',
					'3' => 'okpay_usd',
					'4' => 'okpay_rub',
					'5' => 'payeer_usd',
					'6' => 'payeer_rub',
					'100' => 'webmoney_usd',
					'101' => 'webmoney_rub',	
				);
				$newgid = trim(is_isset($old, $gid));
				if($newgid){
					$arr['gid'] = $newgid;
				}
				if(count($arr) > 0){
					$wpdb->update($wpdb->prefix.'inex_deposit', $arr, array('id'=>$data_id));
				}
			} 			
			
		}		
		
		$log['status'] = 'success';	
		$log['status_text'] = __('Ok!','pn');		
		
	} else {
		$log['status'] = 'error';
		$log['status_code'] = 1; 
		$log['status_text'] = __('Error! insufficient privileges!','pn');
	}
	
	echo json_encode($log);
	exit;	
}