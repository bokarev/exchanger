<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_pn_naps_temp', 'pn_admin_title_pn_naps_temp');
function pn_admin_title_pn_naps_temp($page){
	_e('Exchange direction templates','pn');
} 

/* настройки */
add_action('pn_adminpage_content_pn_naps_temp','def_pn_admin_content_pn_naps_temp');
function def_pn_admin_content_pn_naps_temp(){
global $premiumbox;
	
	$place = is_status_name(is_param_get('place'));
	$places = apply_filters('list_naps_temp',array());
	$places = (array)$places;
	$places_t = array();
	foreach($places as $key => $v){
		$places_t[] = $key;
	}	
	
	$selects = array();
	$selects[] = array(
		'link' => admin_url("admin.php?page=pn_naps_temp"),
		'title' => '--' . __('Make a choice','pn') . '--',
		'background' => '',
		'default' => '',
	);		
	if(is_array($places)){ 
		foreach($places as $key => $val){ 
			$selects[] = array(
				'link' => admin_url("admin.php?page=pn_naps_temp&place=".$key),
				'title' => $val,
				'background' => '',
				'default' => $key,
			);		
		}
	}		
	pn_admin_select_box($place, $selects, __('Setting up','pn'));	

	if(in_array($place,$places_t)){
		$options = array();
		$options['hidden_block'] = array(
			'view' => 'hidden_input',
			'name' => 'place',
			'default' => $place,
		);	
		$not = array('description_txt','timeline_txt');
		if(!in_array($place, $not)){
			$options[] = array(
				'view' => 'inputbig',
				'title' => __('Website header','pn'),
				'default' => $premiumbox->get_option('naps_title',$place),
				'name' => 'title',
				'ml' => 1,
			);
			$options[] = array(
				'view' => 'inputbig',
				'title' => __('Brief status description','pn'),
				'default' => $premiumbox->get_option('naps_status',$place),
				'name' => 'status',
				'ml' => 1,
			);		
			$options[] = array(
				'view' => 'select',
				'title' => __('Webpage automatic update','pn'),
				'options' => array('0'=>__('No','pn'), '1'=>__('Yes','pn')),
				'default' => $premiumbox->get_option('naps_timer',$place),
				'name' => 'timer',
			);							
		}
		$options[] = array(
			'view' => 'select',
			'title' => __('How to show description from form below','pn'),
			'options' => array('0'=>__('Show relevant description of exchange direction only','pn'), '1'=>__('If there is no description given to exchange direction then show from form below','pn'), '2' =>__('Always show description from form below','pn') ),
			'default' => $premiumbox->get_option('naps_nodescr',$place),
			'name' => 'naps_nodescr',
		);		
		$options['temp'] = array(
			'view' => 'editor',
			'title' => __('Text', 'pn'),
			'default' => $premiumbox->get_option('naps_temp',$place),
			'name' => 'temp',
			'rows' => 10,
			'media' => false,
			'ml' => 1,
		);			
		$options['bottom_title'] = array(
			'view' => 'h3',
			'title' => '',
			'submit' => __('Save','pn'),
			'colspan' => 2,
		);
		pn_admin_one_screen('pn_naps_temp_option', $options);
	} 
}  

/* обработка */
add_action('premium_action_pn_naps_temp','def_premium_action_pn_naps_temp');
function def_premium_action_pn_naps_temp(){
global $wpdb, $premiumbox;	

	only_post();
	pn_only_caps(array('administrator', 'pn_naps'));
	
	$place = is_status_name(is_param_post('place'));
	if($place){
		$premiumbox->update_option('naps_title', $place, pn_strip_input(is_param_post_ml('title')));
		$premiumbox->update_option('naps_status', $place, pn_strip_input(is_param_post_ml('status')));
		$premiumbox->update_option('naps_timer', $place, intval(is_param_post('timer')));
		$premiumbox->update_option('naps_temp', $place, pn_strip_text(is_param_post_ml('temp')));
		$premiumbox->update_option('naps_nodescr', $place, intval(is_param_post('naps_nodescr')));
	}
	
	$url = admin_url('admin.php?page=pn_naps_temp&place='. $place .'&reply=true');
	wp_redirect($url);
	exit;
}