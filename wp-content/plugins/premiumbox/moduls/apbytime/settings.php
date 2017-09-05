<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('admin_menu', 'admin_menu_apbytime');
function admin_menu_apbytime(){
global $premiumbox;	
	if(current_user_can('administrator')){
		add_submenu_page("users.php", __('Access to control panel by time','pn'), __('Access to control panel by time','pn'), 'read', "pn_apbytime", array($premiumbox, 'admin_temp'));	
	}
}

add_action('pn_adminpage_title_pn_apbytime', 'def_adminpage_title_pn_apbytime');
function def_adminpage_title_pn_apbytime(){
	_e('Access to control panel by time','pn');
}

add_action('pn_adminpage_content_pn_apbytime','def_pn_adminpage_content_pn_apbytime');
function def_pn_adminpage_content_pn_apbytime(){
global $wpdb, $premiumbox;

	$prefix = $wpdb->prefix;
	
	global $wp_roles;
	if (!isset($wp_roles)){
		$wp_roles = new WP_Roles();
	}
	
	$selects = array();
	$selects[] = array(
		'link' => admin_url("users.php?page=pn_apbytime"),
		'title' => '--' . __('Make a choice','pn') . '--',
		'background' => '',
		'default' => '',
	);		
	
	$places = array();
	$place = is_param_get('place');
	if(isset($wp_roles)){
		foreach($wp_roles->role_names as $role => $name){
			if($role != 'administrator' and $role != 'users'){
				$places[] = $role;
				$selects[] = array(
					'link' => admin_url("users.php?page=pn_apbytime&place=" . $role),
					'title' => pn_role_title($role),
					'default' => $role,
				);				
			}	
		}
	}	
	
	pn_admin_select_box($place, $selects, __('Setting up','pn'));

	if(in_array($place,$places)){
		
		$data = $premiumbox->get_option('apbytime', $place);

		$options = array();
		$options['top_title'] = array(
			'view' => 'h3',
			'title' => pn_role_title($place),
			'submit' => __('Save','pn'),
			'colspan' => 2,
		);
		$options[] = array(
			'view' => 'hidden_input',
			'name' => 'role',
			'default' => $place,
		);
		$options['status'] = array(
			'view' => 'select',
			'title' => __('Status','pn'),
			'options' => array('0'=>__('Anytime','pn'), '1'=>__('On schedule','pn')),
			'default' => is_isset($data, 'status'),
			'name' => 'status',
			'work' => 'int',
		);		
		$options['datetime'] = array(
			'view' => 'user_func',
			'func_data' => $data,
			'func' => 'pn_apbytime_datetime',
			'work' => 'input_array',
		);		
		$options['bottom_title'] = array(
			'view' => 'h3',
			'title' => '',
			'submit' => __('Save','pn'),
			'colspan' => 2,
		);		
		
		pn_admin_one_screen('', $options);
	}
} 

function pn_apbytime_datetime($data){
	
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
<?php
}

/* обработка */
add_action('premium_action_pn_apbytime','def_premium_action_pn_apbytime');
function def_premium_action_pn_apbytime(){
global $wpdb, $premiumbox;	

	only_post();

	pn_only_caps(array('administrator'));
		
	$role = trim(is_param_post('role'));
	$prefix = $wpdb->prefix;
			
	if($role){
		$array = array();
		$array['status'] = intval(is_param_post('status'));
		$array['h1'] = zeroise(intval(is_param_post('h1')),2);
		$array['h2'] = zeroise(intval(is_param_post('h2')),2);
		$array['m1'] = zeroise(intval(is_param_post('m1')),2);
		$array['m2'] = zeroise(intval(is_param_post('m2')),2);
		$premiumbox->update_option('apbytime', $role, $array);			
	}
	
	$back_url = is_param_post('_wp_http_referer');
	$back_url .= '&reply=true';
			
	wp_safe_redirect($back_url);
	exit;			
}	