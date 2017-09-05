<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_pn_add_cf', 'pn_admin_title_pn_add_cf');
function pn_admin_title_pn_add_cf(){
	$id = intval(is_param_get('item_id'));
	if($id){
		_e('Edit custom field','pn');
	} else {
		_e('Add custom field','pn');
	}
}

add_action('pn_adminpage_content_pn_add_cf','def_pn_admin_content_pn_add_cf');
function def_pn_admin_content_pn_add_cf(){
global $wpdb;

	$id = intval(is_param_get('item_id'));
	$data_id = 0;
	$data = '';
	
	if($id){
		$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."custom_fields WHERE id='$id'");
		if(isset($data->id)){
			$data_id = $data->id;
		}	
	}

	if($data_id){
		$title = __('Edit custom field','pn');
	} else {
		$title = __('Add custom field','pn');
	}
	
	$back_menu = array();
	$back_menu['back'] = array(
		'link' => admin_url('admin.php?page=pn_cf'),
		'title' => __('Back to list','pn')
	);
	if($data_id){
		$back_menu['add'] = array(
			'link' => admin_url('admin.php?page=pn_add_cf'),
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
	$options['tech_name'] = array(
		'view' => 'inputbig',
		'title' => __('Custom field name (technical)','pn'),
		'default' => is_isset($data, 'tech_name'),
		'name' => 'tech_name',
		'ml' => 1,
	);		
	$options['cf_name'] = array(
		'view' => 'inputbig',
		'title' => __('Custom field name','pn'),
		'default' => is_isset($data, 'cf_name'),
		'name' => 'cf_name',
		'work' => 'input',
		'ml' => 1,
	);	
	$options['uniqueid'] = array(
		'view' => 'inputbig',
		'title' => __('Unique ID','pn'),
		'default' => is_isset($data, 'uniqueid'),
		'name' => 'uniqueid',
		'work' => 'input',
	);	
	$options['vid'] = array(
		'view' => 'select',
		'title' => __('Custom field type','pn'),
		'options' => array('0'=> __('Text input field','pn'), '1'=> __('Options','pn')),
		'default' => is_isset($data, 'vid'),
		'name' => 'vid',
	);	
	$options['cf_hidden'] = array(
		'view' => 'select',
		'title' => __('Data visibility in order placed on a website','pn'),
		'options' => array('0'=>__('do not show data','pn'),'1'=>__('hide data','pn'),'2'=>__('do not hide first 4 symbols','pn'),'3'=>__('do not hide last 4 symbols','pn'),'4'=>__('do not hide first 4 symbols and the last 4 symbols','pn')),
		'default' => is_isset($data, 'cf_hidden'),
		'name' => 'cf_hidden',
	);	
	
	$vid = intval(is_isset($data, 'vid'));
	if($vid == 0){
		$cl1 = '';
		$cl2 = 'mhide';
	} else {
		$cl1 = 'mhide';
		$cl2 = '';			
	}
	
	$cf_auto = apply_filters('cf_auto_filed', array());
		
	$options['cf_auto'] = array(
		'view' => 'select',
		'title' => __('Automatic completion','pn'),
		'options' => $cf_auto,
		'default' => is_isset($data, 'cf_auto'),
		'name' => 'cf_auto',
		'class' => 'thevib thevib0 '.$cl1,
	);
	$options['minzn'] = array(
		'view' => 'input',
		'title' => __('Min. number of symbols','pn'),
		'default' => is_isset($data, 'minzn'),
		'name' => 'minzn',
		'class' => 'thevib thevib0 '.$cl1,
	);	
	$options['maxzn'] = array(
		'view' => 'input',
		'title' => __('Max. number of symbols','pn'),
		'default' => is_isset($data, 'maxzn'),
		'name' => 'maxzn',
		'class' => 'thevib thevib0 '.$cl1,
	);				
	$options['firstzn'] = array(
		'view' => 'input',
		'title' => __('First symbols','pn'),
		'default' => is_isset($data, 'firstzn'),
		'name' => 'firstzn',
		'class' => 'thevib thevib0 '.$cl1,
	);
	$options['firstzn_help'] = array(
		'view' => 'help',
		'title' => __('More info','pn'),
		'default' => __('Checking the first symbols while a customer fills out a field.','pn'),
		'class' => 'thevib thevib0 '.$cl1,
	);
	$options['cf_req'] = array(
		'view' => 'select',
		'title' => __('Required field','pn'),
		'options' => array('1'=>__('Yes','pn'),'0'=>__('No','pn')),
		'default' => is_isset($data, 'cf_req'),
		'name' => 'cf_req',
		'class' => 'thevib thevib0 '.$cl1,
	);
	$options['helps'] = array(
		'view' => 'textarea',
		'title' => __('Fill-in tips','pn'),
		'default' => is_isset($data, 'helps'),
		'name' => 'helps',
		'width' => '',
		'height' => '100px',
		'ml' => 1,
		'class' => 'thevib thevib0 '.$cl1
	);	
	$options['datas'] = array(
		'view' => 'textarea',
		'title' => __('Options (at the beginning of a new line)','pn'),
		'default' => is_isset($data, 'datas'),
		'name' => 'datas',
		'width' => '',
		'height' => '200px',
		'ml' => 1,
		'class' => 'thevib thevib1 '.$cl2
	);	
	$options['status'] = array(
		'view' => 'select',
		'title' => __('Status','pn'),
		'options' => array('1'=>__('active field','pn'),'0'=>__('inactive field','pn')),
		'default' => is_isset($data, 'status'),
		'name' => 'status',
	);	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('pn_cf_addform', $options, $data);	
?>
<script type="text/javascript">
$(function(){ 
	$('#pn_vid').on('change',function(){
		var id = $(this).val();
		$('.thevib').hide();
		$('.thevib' + id).show();
		
		return false;
	});
});
</script>	
<?php
} 

/* обработка формы */
add_action('premium_action_pn_add_cf','def_premium_action_pn_add_cf');
function def_premium_action_pn_add_cf(){
global $wpdb;

	only_post();
	pn_only_caps(array('administrator','pn_cf'));	
	
	$data_id = intval(is_param_post('data_id'));
	$last_data = '';
	if($data_id > 0){
		$last_data = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "custom_fields WHERE id='$data_id'");
		if(!isset($last_data->id)){
			$data_id = 0;
		}
	}	
	
	$array = array();
	$array['cf_name'] = pn_strip_input(is_param_post_ml('cf_name'));
	$tech_name = pn_strip_input(is_param_post_ml('tech_name'));
	if(!$tech_name){
		$tech_name = $array['cf_name'];
	}
	$array['tech_name'] = $tech_name;
	$array['vid'] = $vid = intval(is_param_post('vid'));
	$array['uniqueid'] = pn_strip_input(is_param_post('uniqueid'));
	$array['cf_hidden'] = intval(is_param_post('cf_hidden'));
	if($vid == 1){
		$array['cf_auto'] = 0;
		$array['datas'] = pn_strip_input(is_param_post_ml('datas'));
		$array['cf_req'] = 0;		
		$array['minzn'] = 0;
		$array['maxzn'] = 0;
		$array['helps'] = '';
		$array['firstzn'] = '';				
	} else {
		$array['cf_auto'] = pn_strip_input(is_param_post_ml('cf_auto'));
		$array['datas'] = '';
		$array['cf_req'] = intval(is_param_post('cf_req'));		
		$array['minzn'] = intval(is_param_post('minzn'));
		$array['maxzn'] = intval(is_param_post('maxzn'));
		$array['helps'] = pn_strip_input(is_param_post_ml('helps'));
		$array['firstzn'] = is_firstzn_value(is_param_post('firstzn'));				
	}

	$array['status'] = intval(is_param_post('status'));

	$array = apply_filters('pn_cf_addform_post',$array,$last_data);
			
	if($data_id){	
		do_action('pn_cf_edit_before', $data_id, $array,$last_data);
		$result = $wpdb->update($wpdb->prefix.'custom_fields', $array, array('id'=>$data_id));
		if($result){
			do_action('pn_cf_edit', $data_id, $array,$last_data);
		}	
	} else {	
		$wpdb->insert($wpdb->prefix.'custom_fields', $array);
		$data_id = $wpdb->insert_id;	
		do_action('pn_cf_add', $data_id, $array);
	}

	$url = admin_url('admin.php?page=pn_add_cf&item_id='. $data_id .'&reply=true');
	wp_redirect($url);
	exit;
}
/* end обработка формы */