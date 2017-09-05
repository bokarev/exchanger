<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_pn_operator_add_schedule', 'pn_adminpage_title_pn_operator_add_schedule');
function pn_adminpage_title_pn_operator_add_schedule(){
	$id = intval(is_param_get('item_id'));
	if($id){
		_e('Edit schedule','pn');
	} else {
		_e('Add schedule','pn');
	}
}

add_action('pn_adminpage_content_pn_operator_add_schedule','def_pn_adminpage_content_pn_operator_add_schedule');
function def_pn_adminpage_content_pn_operator_add_schedule(){
global $wpdb;

	$id = intval(is_param_get('item_id'));
	$data_id = 0;
	$data = '';
	
	if($id){
		$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."operator_schedules WHERE id='$id'");
		if(isset($data->id)){
			$data_id = $data->id;
		}	
	}

	if($data_id){
		$title = __('Edit schedule','pn');
	} else {
		$title = __('Add schedule','pn');
	}
	
	$statused = array();
	$status_operator = apply_filters('status_operator', array());
	if(is_array($status_operator)){
		foreach($status_operator as $key => $val){
			$statused[$key] = $val;
		}
	}
	
	$days = array(
		'd1' => __('monday','pn'),
		'd2' => __('tuesday','pn'),
		'd3' => __('wednesday','pn'),
		'd4' => __('thursday','pn'),
		'd5' => __('friday','pn'),
		'd6' => '<span class="bred">'. __('saturday','pn') .'</span>',
		'd7' => '<span class="bred">'. __('sunday','pn') .'</span>',
	);
	
	$back_menu = array();
	$back_menu['back'] = array(
		'link' => admin_url('admin.php?page=pn_operator_schedule'),
		'title' => __('Back to list','pn')
	);
	if($data_id){
		$back_menu['add'] = array(
			'link' => admin_url('admin.php?page=pn_operator_add_schedule'),
			'title' => __('Add new','pn')
		);	
	}	
	pn_admin_back_menu($back_menu, $data);	
?>	

<div class="premium_body">	
	<form method="post" action="<?php pn_the_link_post(); ?>">
		<input type="hidden" name="data_id" value="<?php echo $data_id; ?>" />
		
		<table class="premium_standart_table">
			<?php
				pn_h3($title, __('Save','pn'), 2);
					
				$template = array(
					'before' => '<tr class="[class]">',
					'after' => '</tr>',
					'before_title' => '<th>',
					'after_title' => '</th>',
					'before_content' => '<td>',
					'after_content' => '</td>',
					'label' => 1,
				);					
				pn_select(__('Status','pn'), 'status', $statused, is_isset($data, 'status'), '', $template);
				?>
				<tr>
					<th><?php _e('Work time','pn'); ?></th>
					<td>
						<div class="premium_wrap_standart">
							<select name="h1" style="width: 50px;" autocomplete="off">	
								<?php
								$r=-1;
								while($r++<23){
								?>
								<option value="<?php echo $r; ?>" <?php selected(intval(is_isset($data, 'h1')),$r);?>><?php echo zeroise($r,2); ?></option>
								<?php } ?>
							</select>
							:
							<select name="m1" style="width: 50px;" autocomplete="off">	
								<?php
								$r=-1;
								while($r++<59){
								?>
								<option value="<?php echo $r; ?>" <?php selected(intval(is_isset($data, 'm1')),$r);?>><?php echo zeroise($r,2); ?></option>
								<?php } ?>
							</select>							
							-
							
							<select name="h2" style="width: 50px;" autocomplete="off">	
								<?php
								$r=-1;
								while($r++<23){
								?>
								<option value="<?php echo $r; ?>" <?php selected(intval(is_isset($data, 'h2')),$r);?>><?php echo zeroise($r,2); ?></option>
								<?php } ?>
							</select>	
							:
							<select name="m2" style="width: 50px;" autocomplete="off">	
								<?php
								$r=-1;
								while($r++<59){
								?>
								<option value="<?php echo $r; ?>" <?php selected(intval(is_isset($data, 'm2')),$r);?>><?php echo zeroise($r,2); ?></option>
								<?php } ?>
							</select>							
								<div class="premium_clear"></div>
						</div>
					</td>		
				</tr>				
				<tr>
					<th><?php _e('Work days','pn'); ?></th>
					<td>
						<div class="premium_wrap_standart">
							<?php foreach($days as $key => $val){ ?>
							<div><label><input type="checkbox" name="<?php echo $key; ?>" <?php checked(is_isset($data,$key), 1);?> value="1" /> <?php echo $val; ?></label></div>
							<?php } ?>
						</div>
					</td>		
				</tr>					
				<?php				
				
				do_action('pn_schedule_addform', $data);
				
				pn_h3('', __('Save','pn'), 2);	
			?>
		</table>
	</form>		
</div>		
<?php
}

/* обработка формы */
add_action('premium_action_pn_operator_add_schedule','def_premium_action_pn_operator_add_schedule');
function def_premium_action_pn_operator_add_schedule(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator'));
	
	$data_id = intval(is_param_post('data_id')); 
	$last_data = '';
	if($data_id > 0){
		$last_data = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "operator_schedules WHERE id='$data_id'");
		if(!isset($last_data->id)){
			$data_id = 0;
		}
	}	
	
	$array = array();
	$array['status'] = intval(is_param_post('status'));
	$array['h1'] = $h1 = zeroise(intval(is_param_post('h1')),2);
	$array['h2'] = $h2 = zeroise(intval(is_param_post('h2')),2);
	$array['m1'] = $m1 = zeroise(intval(is_param_post('m1')),2);
	$array['m2'] = $m2 = zeroise(intval(is_param_post('m2')),2);
			
	$time1 = strtotime('01-01-2020 '. $h1 .':'. $m1 .':00');
	$time2 = strtotime('01-01-2020 '. $h2 .':'. $m2 .':00');
			
	if($time1 > $time2){
		$array['h1'] = $h2; 
		$array['h2'] = $h1;
		$array['m1'] = $m2;
		$array['m2'] = $m1;
	}
			
	$array['d1'] = intval(is_param_post('d1'));
	$array['d2'] = intval(is_param_post('d2'));
	$array['d3'] = intval(is_param_post('d3'));
	$array['d4'] = intval(is_param_post('d4'));
	$array['d5'] = intval(is_param_post('d5'));
	$array['d6'] = intval(is_param_post('d6'));
	$array['d7'] = intval(is_param_post('d7'));
			
	$array = apply_filters('pn_schedule_addform_post',$array, $last_data);
			
	if($data_id){	
			
		do_action('pn_schedule_edit_before', $data_id, $array, $last_data);
		$result = $wpdb->update($wpdb->prefix.'operator_schedules', $array, array('id'=>$data_id));
		if($result){
			do_action('pn_schedule_edit', $data_id, $array, $last_data);
		}		
	} else {

		$wpdb->insert($wpdb->prefix.'operator_schedules', $array);
		$data_id = $wpdb->insert_id;	
		do_action('pn_schedule_add', $data_id, $array);

	}

	$url = admin_url('admin.php?page=pn_operator_add_schedule&item_id='. $data_id .'&reply=true');
	wp_redirect($url);
	exit;
}	
/* end обработка формы */