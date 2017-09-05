<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_pn_add_vtypes', 'pn_adminpage_title_pn_add_vtypes');
function pn_adminpage_title_pn_add_vtypes(){
	$id = intval(is_param_get('item_id'));
	if($id){
		_e('Edit currency code','pn');
	} else {
		_e('Add currency code','pn');
	}
}

add_action('pn_adminpage_content_pn_add_vtypes','def_pn_admin_content_pn_add_vtypes');
function def_pn_admin_content_pn_add_vtypes(){
global $wpdb;

	$id = intval(is_param_get('item_id'));
	$data_id = 0;
	$data = '';
	
	if($id){
		$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."vtypes WHERE id='$id'");
		if(isset($data->id)){
			$data_id = $data->id;
		}	
	}

	if($data_id){
		$title = __('Edit currency code','pn');
	} else {
		$title = __('Add currency code','pn');
	}
	
	$parsers = array();
	$parsers[0] = '-- '. __('No item','pn') .' --';
	$en_parsers = array();
	if(function_exists('get_list_parsers')){
		$en_parsers = get_list_parsers();
	}
	if(is_array($en_parsers)){
		foreach($en_parsers as $key => $val){
			$parsers[$key] = $val['title'];
		}
	}
	
	$back_menu = array();
	$back_menu['back'] = array(
		'link' => admin_url('admin.php?page=pn_vtypes'),
		'title' => __('Back to list','pn')
	);
	if($data_id){
		$back_menu['add'] = array(
			'link' => admin_url('admin.php?page=pn_add_vtypes'),
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
	$options['vtype_title'] = array(
		'view' => 'input',
		'title' => __('Currency code','pn'),
		'default' => is_isset($data, 'vtype_title'),
		'name' => 'vtype_title',
	);		
	$options['vncurs'] = array(
		'view' => 'input',
		'title' => __('Internal rate','pn'). '(1 '. cur_type() .')',
		'default' => is_isset($data, 'vncurs'),
		'name' => 'vncurs',
	);	
	$options['line1'] = array(
		'view' => 'line',
		'colspan' => 2,
	);	
	$options['parser'] = array(
		'view' => 'select',
		'title' => __('Automatic change of rate','pn'),
		'options' => $parsers,
		'default' => is_isset($data, 'parser'),
		'name' => 'parser',
		'work' => 'input',
	);	
	$options['add_to_rate'] = array(
		'view' => 'user_func',
		'func_data' => array(
			'nums' => pn_strip_input(is_isset($data, 'nums')),
			'elem' => is_isset($data, 'elem'),
		),
		'func' => 'pn_vtypes_addtorate_option',
	);	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('pn_vtypes_addform', $options, $data);				
}

function pn_vtypes_addtorate_option($data){
?>
	<tr>
		<th><?php _e('Add to rate','pn'); ?></th>
		<td>
			<div class="premium_wrap_standart">
				<input type="text" name="nums" style="width: 100px; float: left; margin: 2px 5px 0 0;" value="<?php echo pn_strip_input(is_isset($data, 'nums'));?>" />
				<select name="elem" style="float: left;" autocomplete="off">	
					<option value="0" <?php selected(is_isset($data, 'elem'),0);?>>S</option>
					<option value="1" <?php selected(is_isset($data, 'elem'),1);?>>%</option>
				</select>
					<div class="premium_clear"></div>
			</div>
		</td>		
	</tr>
<?php		
}

/* обработка формы */
add_action('premium_action_pn_add_vtypes','def_premium_action_pn_add_vtypes');
function def_premium_action_pn_add_vtypes(){
global $wpdb;

	only_post();
	pn_only_caps(array('administrator','pn_vtypes'));		
	
		$data_id = intval(is_param_post('data_id')); 
		$last_data = '';
		if($data_id > 0){
			$last_data = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "vtypes WHERE id='$data_id'");
			if(!isset($last_data->id)){
				$data_id = 0;
			}
		}		
		
		$array = array();
		$array['vtype_title'] = $vtype_title = is_site_value(is_param_post('vtype_title'));
			
		if(!$vtype_title){ pn_display_mess(__('Error! You did not enter the name','pn')); }

		$array['vncurs'] = is_my_money(is_param_post('vncurs'));
		if($array['vncurs'] <= 0){ $array['vncurs'] = 1; }
			
		$array['parser'] = intval(is_param_post('parser'));
		$array['elem'] = intval(is_param_post('elem'));
		$array['nums'] = pn_parser_num(is_param_post('nums'));
		
		$array = apply_filters('pn_vtypes_addform_post',$array, $last_data);
			
		$cc = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."vtypes WHERE vtype_title='$vtype_title' AND id != '$data_id'");
		if($cc > 0){
			pn_display_mess(__('Error! This currency code already exists','pn'));
		}
			
		if($data_id){	
			do_action('pn_vtypes_edit_before', $data_id, $array, $last_data);
			$result = $wpdb->update($wpdb->prefix.'vtypes', $array, array('id'=>$data_id));
			if($result){
				do_action('pn_vtypes_edit', $data_id, $array, $last_data);
			}
		} else {
			$wpdb->insert($wpdb->prefix.'vtypes', $array);
			$data_id = $wpdb->insert_id;	
			do_action('pn_vtypes_add', $data_id, $array);
		}

	$url = admin_url('admin.php?page=pn_add_vtypes&item_id='. $data_id .'&reply=true');
	wp_redirect($url);
	exit;
}	
/* end обработка формы */