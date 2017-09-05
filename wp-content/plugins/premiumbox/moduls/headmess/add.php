<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_pn_add_headmess', 'pn_adminpage_title_pn_add_headmess');
function pn_adminpage_title_pn_add_headmess(){
	$id = intval(is_param_get('item_id'));
	if($id){
		_e('Edit message','pn');
	} else {
		_e('Add message','pn');
	}
}

add_action('pn_adminpage_content_pn_add_headmess','def_pn_adminpage_content_pn_add_headmess');
function def_pn_adminpage_content_pn_add_headmess(){
global $wpdb;

	$id = intval(is_param_get('item_id'));
	$data_id = 0;
	$data = '';
	
	if($id){
		$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."head_mess WHERE id='$id'");
		if(isset($data->id)){
			$data_id = $data->id;
		}	
	}

	if($data_id){
		$title = __('Edit message','pn');
	} else {
		$title = __('Add message','pn');
	}
	
	$back_menu = array();
	$back_menu['back'] = array(
		'link' => admin_url('admin.php?page=pn_headmess'),
		'title' => __('Back to list','pn')
	);
	if($data_id){
		$back_menu['add'] = array(
			'link' => admin_url('admin.php?page=pn_add_headmess'),
			'title' => __('Add new','pn')
		);	
	}
	pn_admin_back_menu($back_menu, $data);
	 
	$options = array();
	$options['hidden_block'] = array(
		'view' => 'hidden_input',
		'name' => 'data_id',
		'default' => $data_id,
	);	
	$options['top_title'] = array(
		'view' => 'h3',
		'title' => $title,
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);	
	$statused = array();
	$statused['-1'] = '--'. __('Any status','pn') .'--';
	$status_operator = apply_filters('status_operator', array());
	if(is_array($status_operator)){
		foreach($status_operator as $key => $val){
			$statused[$key] = $val;
		}
	}
	$options['op_status'] = array(
		'view' => 'select',
		'title' => __('Status of operator','pn'),
		'options' => $statused,
		'default' => is_isset($data, 'op_status'),
		'name' => 'op_status',
		'work' => 'int',
	);	
	$options['datetime'] = array(
		'view' => 'user_func',
		'func_data' => $data,
		'func' => 'pn_headmess_datetime',
		'work' => 'input_array',
	);	
	$options['theclass'] = array(
		'view' => 'inputbig',
		'title' => __('CSS class','pn'),
		'default' => is_isset($data, 'theclass'),
		'name' => 'theclass',
		'work' => 'input',
	);	
	$options['url'] = array(
		'view' => 'inputbig',
		'title' => __('Link','pn'),
		'default' => is_isset($data, 'url'),
		'name' => 'url',
		'work' => 'input',
		'ml' => 1,
	);
	$options['text'] = array(
		'view' => 'textarea',
		'title' => __('Text','pn'),
		'default' => is_isset($data, 'text'),
		'name' => 'text',
		'width' => '',
		'height' => '150px',
		'work' => 'text',
		'ml' => 1,
	);		
	$options['status'] = array(
		'view' => 'select',
		'title' => __('Status','pn'),
		'options' => array('1'=>__('published','pn'),'0'=>__('moderating','pn')),
		'default' => is_isset($data, 'status'),
		'name' => 'status',
		'work' => 'int',
	);		
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('', $options, $data);
}

function pn_headmess_datetime($data){
	
	$days = array(
		'd1' => __('monday','pn'),
		'd2' => __('tuesday','pn'),
		'd3' => __('wednesday','pn'),
		'd4' => __('thursday','pn'),
		'd5' => __('friday','pn'),
		'd6' => '<span class="bred">'. __('saturday','pn') .'</span>',
		'd7' => '<span class="bred">'. __('sunday','pn') .'</span>',
	);	
?>
	<tr>
		<th><?php _e('Period for display (hours)','pn'); ?></th>
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
		<th><?php _e('Period for display (days)','pn'); ?></th>
		<td>
			<div class="premium_wrap_standart">
				<?php foreach($days as $key => $val){ ?>
					<div><label><input type="checkbox" name="<?php echo $key; ?>" <?php checked(is_isset($data,$key), 1);?> value="1" /> <?php echo $val; ?></label></div>
				<?php } ?>
			</div>
		</td>		
	</tr>				
<?php
}

/* обработка формы */
add_action('premium_action_pn_add_headmess','def_premium_action_pn_add_headmess');
function def_premium_action_pn_add_headmess(){
global $wpdb;	

	only_post();

	pn_only_caps(array('administrator','pn_headmess'));

	$data_id = intval(is_param_post('data_id')); 
	$last_data = '';
	if($data_id > 0){
		$last_data = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "head_mess WHERE id='$data_id'");
		if(!isset($last_data->id)){
			$data_id = 0;
		}
	}	
	
	$array = array();
	$array['op_status'] = intval(is_param_post('op_status'));
	$array['h1'] = $h1 = zeroise(intval(is_param_post('h1')),2);
	$array['h2'] = $h2 = zeroise(intval(is_param_post('h2')),2);
	$array['m1'] = $m1 = zeroise(intval(is_param_post('m1')),2);
	$array['m2'] = $m2 = zeroise(intval(is_param_post('m2')),2);
			
	$array['d1'] = intval(is_param_post('d1'));
	$array['d2'] = intval(is_param_post('d2'));
	$array['d3'] = intval(is_param_post('d3'));
	$array['d4'] = intval(is_param_post('d4'));
	$array['d5'] = intval(is_param_post('d5'));
	$array['d6'] = intval(is_param_post('d6'));
	$array['d7'] = intval(is_param_post('d7'));
	$array['url'] = pn_strip_input(is_param_post_ml('url'));
	$array['text'] = pn_strip_input(is_param_post_ml('text'));
	$array['theclass'] = pn_strip_input(is_param_post('theclass'));
	$array['status'] = intval(is_param_post('status'));
	
	$array = apply_filters('pn_headmess_addform_post',$array, $last_data);
	
	if($data_id){
		do_action('pn_headmess_edit_before', $data_id, $array, $last_data);
		$result = $wpdb->update($wpdb->prefix.'head_mess', $array, array('id'=>$data_id));
		if($result){
			do_action('pn_headmess_edit', $data_id, $array, $last_data);
		}
	} else {
		$wpdb->insert($wpdb->prefix.'head_mess', $array);
		$data_id = $wpdb->insert_id;
		do_action('pn_headmess_add', $data_id, $array);
	}

	$url = admin_url('admin.php?page=pn_add_headmess&item_id='. $data_id .'&reply=true');
	wp_redirect($url);
	exit;
} 
/* end обработка формы */