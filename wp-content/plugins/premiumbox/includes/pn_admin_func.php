<?php 
if( !defined( 'ABSPATH')){ exit(); }

function get_login_form_filelds($place='shortcode'){
	$ui = wp_get_current_user();

	$items = array();
	$items['logmail'] = array(
		'name' => 'logmail',
		'title' => __('Login or email', 'pn'),
		'placeholder' => '',
		'req' => 1,
		'value' => '',
		'type' => 'input',
		'not_auto' => 0,
	);
	$items['pass'] = array(
		'name' => 'pass',
		'title' => __('Password', 'pn'),
		'placeholder' => '',
		'req' => 1,
		'value' => '',
		'type' => 'password',
		'not_auto' => 0,
	);		
	$items = apply_filters('get_form_filelds',$items, 'loginform', $ui, $place);
	$items = apply_filters('login_form_filelds',$items, $ui, $place);	
	
	return $items;
}

function get_register_form_filelds($place='shortcode'){
	$ui = wp_get_current_user();

	$items = array();
	$items['login'] = array(
		'name' => 'login',
		'title' => __('Login', 'pn'),
		'placeholder' => '',
		'req' => 1,
		'value' => '',
		'type' => 'input',
		'not_auto' => 0,
	);
	$items['email'] = array(
		'name' => 'email',
		'title' => __('E-mail', 'pn'),
		'placeholder' => '',
		'req' => 1,
		'value' => '',
		'type' => 'input',
		'not_auto' => 0,
	);	
	$items['pass'] = array(
		'name' => 'pass',
		'title' => __('Password', 'pn'),
		'placeholder' => '',
		'req' => 1,
		'value' => '',
		'type' => 'password',
		'not_auto' => 0,
	);
	$items['pass2'] = array(
		'name' => 'pass2',
		'title' => __('Password again', 'pn'),
		'placeholder' => '',
		'req' => 1,
		'value' => '',
		'type' => 'password',
		'not_auto' => 0,
	);	
	$items = apply_filters('get_form_filelds',$items, 'registerform', $ui, $place);
	$items = apply_filters('register_form_filelds',$items, $ui, $place);	
	
	return $items;
}

function get_lostpass1_form_filelds($place='shortcode'){
	$ui = wp_get_current_user();

	$items = array();
	$items['email'] = array(
		'name' => 'email',
		'title' => __('E-mail', 'pn'),
		'placeholder' => '',
		'req' => 1,
		'value' => '',
		'type' => 'input',
		'not_auto' => 0,
	);		
	$items = apply_filters('get_form_filelds',$items, 'lostpass1form', $ui, $place);
	$items = apply_filters('lostpass1_form_filelds',$items, $ui, $place);	
	
	return $items;
}

function get_lostpass2_form_filelds($place='shortcode'){
	$ui = wp_get_current_user();

	$items = array();
	$items['pass'] = array(
		'name' => 'pass',
		'title' => __('New password', 'pn'),
		'placeholder' => '',
		'req' => 1,
		'value' => '',
		'type' => 'password',
		'not_auto' => 1,
	);
	$items['pass2'] = array(
		'name' => 'pass2',
		'title' => __('New password again', 'pn'),
		'placeholder' => '',
		'req' => 1,
		'value' => '',
		'type' => 'password',
		'not_auto' => 1,
	);	
	$items = apply_filters('get_form_filelds',$items, 'lostpass2form', $ui, $place);
	$items = apply_filters('lostpass2_form_filelds',$items, $ui, $place);	
	
	return $items;
}

function get_account_form_filelds($place='shortcode'){
global $premiumbox;	
	$ui = wp_get_current_user();
	$user_id = intval(is_isset($ui, 'ID'));

	$items = array();
	if(pn_allow_uv('login')){
		$items['login'] = array(
			'name' => 'login',
			'title' => __('Login', 'pn'),
			'placeholder' => '',
			'req' => 0,
			'value' => is_user(is_isset($ui,'user_login')),
			'type' => 'input',
			'not_auto' => 0,
			'disable' => 1,
		);
	}
	if(pn_allow_uv('last_name')){
		$items['last_name'] = array(
			'name' => 'last_name',
			'title' => __('Last name', 'pn'),
			'placeholder' => '',
			'req' => 0,
			'value' => pn_strip_input(is_isset($ui,'last_name')),
			'type' => 'input',
			'not_auto' => 0,
			'disable' => apply_filters('disabled_account_form_line', 0, 'last_name', $ui),
		);
	}
	if(pn_allow_uv('first_name')){	
		$items['first_name'] = array(
			'name' => 'first_name',
			'title' => __('First name', 'pn'),
			'placeholder' => '',
			'req' => 0,
			'value' => pn_strip_input(is_isset($ui,'first_name')),
			'type' => 'input',
			'not_auto' => 0,
			'disable' => apply_filters('disabled_account_form_line', 0, 'first_name', $ui),
		);
	}
	if(pn_allow_uv('second_name')){	
		$items['second_name'] = array(
			'name' => 'second_name',
			'title' => __('Second name', 'pn'),
			'placeholder' => '',
			'req' => 0,
			'value' => pn_strip_input(is_isset($ui,'second_name')),
			'type' => 'input',
			'not_auto' => 0,
			'disable' => apply_filters('disabled_account_form_line', 0, 'second_name', $ui),
		);	
	}
	if(pn_allow_uv('user_phone')){	
		$items['user_phone'] = array(
			'name' => 'user_phone',
			'title' => __('Phone no.', 'pn'),
			'placeholder' => '',
			'req' => 0,
			'value' => is_phone(is_isset($ui,'user_phone')),
			'type' => 'input',
			'not_auto' => 0,
			'disable' => apply_filters('disabled_account_form_line', 0, 'user_phone', $ui),
		);
	}
	if(pn_allow_uv('user_skype')){
		$items['user_skype'] = array(
			'name' => 'user_skype',
			'title' => __('Skype', 'pn'),
			'placeholder' => '',
			'req' => 0,
			'value' => pn_strip_input(is_isset($ui,'user_skype')),
			'type' => 'input',
			'not_auto' => 0,
			'disable' => apply_filters('disabled_account_form_line', 0, 'user_skype', $ui),
		);
	}
	$items['user_email'] = array(
		'name' => 'user_email',
		'title' => __('E-mail', 'pn'),
		'placeholder' => '',
		'req' => 0,
		'value' => is_email(is_isset($ui,'user_email')),
		'type' => 'input',
		'not_auto' => 0,
		'disable' => apply_filters('disabled_account_form_line', 0, 'user_email', $ui),
	);	
	if(pn_allow_uv('website')){	
		$items['website'] = array(
			'name' => 'website',
			'title' => __('Website', 'pn'),
			'placeholder' => '',
			'req' => 0,
			'value' => esc_url(is_isset($ui,'user_url')),
			'type' => 'input',
			'not_auto' => 0,
			'disable' => apply_filters('disabled_account_form_line', 0, 'website', $ui),
		);
	}
	if(pn_allow_uv('user_passport')){	
		$items['user_passport'] = array(
			'name' => 'user_passport',
			'title' => __('Passport number', 'pn'),
			'placeholder' => '',
			'req' => 0,
			'value' => pn_strip_input(is_isset($ui,'user_passport')),
			'type' => 'input',
			'not_auto' => 0,
			'disable' => apply_filters('disabled_account_form_line', 0, 'user_passport', $ui),
		);
	}	
	$items['pass'] = array(
		'name' => 'pass',
		'title' => __('New password', 'pn'),
		'placeholder' => '',
		'req' => 0,
		'value' => '',
		'type' => 'password',
		'not_auto' => 0,
		'disable' => 0,
	);
	$items['pass2'] = array(
		'name' => 'pass2',
		'title' => __('New password again', 'pn'),
		'placeholder' => '',
		'req' => 0,
		'value' => '',
		'type' => 'password',
		'not_auto' => 0,
		'disable' => 0,
	);	
	$items = apply_filters('get_form_filelds',$items, 'accountform', $ui, $place);
	$items = apply_filters('account_form_filelds',$items, $ui, $place);	
	
	return $items;
}

function get_security_form_filelds($place='shortcode'){
	$ui = wp_get_current_user();

	$items = array();
	$items['sec_lostpass'] = array(
		'name' => 'sec_lostpass',
		'title' => __('Recover password', 'pn'),
		'req' => 0,
		'value' => is_isset($ui,'sec_lostpass'),
		'type' => 'select',
		'options' => array(__('No','pn'), __('Yes','pn')),
	);
	$items['sec_login'] = array(
		'name' => 'sec_login',
		'title' => __('Log in notification by e-mail', 'pn'),
		'req' => 0,
		'value' => is_isset($ui,'sec_login'),
		'type' => 'select',
		'options' => array(__('No','pn'), __('Yes','pn')),
	);
	$items['email_login'] = array(
		'name' => 'email_login',
		'title' => __('Two-factor authorization by one-time ref', 'pn'),
		'req' => 0,
		'value' => is_isset($ui,'email_login'),
		'type' => 'select',
		'options' => array(__('No','pn'), __('Yes','pn')),
	);
	$items['enable_ips'] = array(
		'name' => 'enable_ips',
		'title' => __('Allowed IP address (in new line)', 'pn'),
		'placeholder' => '',
		'req' => 0,
		'value' => is_isset($ui,'enable_ips'),
		'type' => 'text',
		'not_auto' => 0,
	);		
	$items = apply_filters('get_form_filelds',$items, 'securityform', $ui, $place);
	$items = apply_filters('security_form_filelds',$items, $ui, $place);	
	
	return $items;
}

add_filter('placed_captcha', 'def_placed_captcha', 0);
function def_placed_captcha(){
	$placed = array(
		'loginform' => __('Authourization form','pn'),
		'registerform' => __('Registration form','pn'),
		'lostpass1form' => __('Lost password form','pn'),
		'exchangeform' => __('Exchange type','pn'),
	);	
	return $placed;
}

add_filter('pn_exchange_cat_filters', 'def_pn_exchange_cat_filters', 0);
function def_pn_exchange_cat_filters(){
	$cats = array(
		'home' => __('Homepage exchange table','pn'),
		'exchange' => __('Exchange type','pn'),
	);
	return $cats;
}

/* заголовок валюты */
function get_vtitle($valut_id){
global $wpdb;

	$valut_id = intval($valut_id);
	$valut_data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."valuts WHERE id='$valut_id'");
	if(isset($valut_data->id)){
		return get_valut_title($valut_data);
	} else {
		return __('No item','pn');
	}
} 

function get_pstitle($psys_id){
global $wpdb;
	$psys_id = intval($psys_id);
	$data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."psys WHERE id='$psys_id'");
	if(isset($data->id)){
		return pn_strip_input(ctv_ml($data->psys_title));
	} else {
		return __('No item','pn');
	}
} 

function get_payuot_status($status){
	$statused = array(
		'0' => __('Waiting order','pn'),
		'1' => __('Completed order','pn'),
		'2' => __('Cancelled order','pn'),
		'3' => __('Cancelled order by user','pn'),
	);	
	return is_isset($statused, $status);
}

add_filter('bid_status_list','def_bid_status_list',0);
function def_bid_status_list($status){
	
	$status = array(
		'new' => __('new order','pn'),
		'cancel' => __('cancelled order by user','pn'),
		'delete' => __('deleted order','pn'),
		'techpay' => __('when user entered payment section','pn'),
		'payed' => __('user marked order as paid','pn'),
		'coldpay' => __('waiting for merchant confirmation','pn'),
		'realpay' => __('paid order','pn'),
		'verify' => __('order is on checking','pn'),
		'error' => __('error order','pn'),
		'payouterror' => __('automatic payout error','pn'),
		'coldsuccess' => __('waiting for automatic payment module confirmation','pn'),
		'success' => __('successful order','pn'),
	);
	
	return $status;
}

add_filter('colors_for_bidstatus', 'def_colors_for_bidstatus', 0);
function def_colors_for_bidstatus($colors){
	
	$colors = array(
		'0' => array(
			'title' => __('Red','pn'),
			'color' => '#ff3c00',
		),
		'1' => array(
			'title' => __('Orange','pn'),
			'color' => '#fc6d41',
		),
		'2' => array(
			'title' => __('Yellow','pn'),
			'color' => '#dbdd0a',
		),
		'3' => array(
			'title' => __('Green','pn'),
			'color' => '#31dd0a',
		),
		'4' => array(
			'title' => __('Blue','pn'),
			'color' => '#0adddb',
		),
		'5' => array(
			'title' => __('Purple','pn'),
			'color' => '#810add',
		),		
	);
	
	return $colors;
}

add_filter('list_naps_temp','def_list_naps_temp',0);
function def_list_naps_temp($list_naps_temp){
	
	$list_naps_temp = array(
		'description_txt' => __('Exchange description','pn'),
		'timeline_txt' => __('Deadline','pn'),
	);
	$bid_status_list = apply_filters('bid_status_list',array());
	foreach($bid_status_list as $key => $title){
		$list_naps_temp['status_'.$key] = sprintf(__('Status of order is "%s"', 'pn'), $title);
	}	
							
	return $list_naps_temp;
}

function get_comis_text($com_ps, $dop_com, $psys, $vtype, $vid, $gt){
	$comis_text = '';
	
	if($com_ps > 0 or $dop_com > 0){
		$comis_text = __('Including','pn').' ';
	}		

	if($com_ps > 0 and $dop_com > 0){
		$comis_text .= __('add. service fee','pn');
		$comis_text .= ' (<span class="dop_com">'. $dop_com .'</span> <span class="vtype">'. $vtype .'</span>)';
		$comis_text .= __('and','pn');
		$comis_text .= ' ';		
		$comis_text .= __('payment system fees','pn');
		$comis_text .= ' <span class="psys">'. $psys . '</span> (<span class="com_ps">'. $com_ps .'</span> <span class="vtype">'. $vtype .'</span>) ';
	} elseif($com_ps > 0){
		$comis_text .= __('payment system fees','pn');
		$comis_text .= ' <span class="psys">'. $psys . '</span> (<span class="com_ps">'. $com_ps .'</span> <span class="vtype">'. $vtype .'</span>) ';	
	} elseif($dop_com > 0){
		$comis_text .= __('add. service fee','pn');
		$comis_text .= ' (<span class="dop_com">'. $dop_com .'</span> <span class="vtype">'. $vtype .'</span>)';
	}	
	
	if($gt == 1){
		if($com_ps > 0 or $dop_com > 0){
			$comis_text .= ', ';
			if($vid == 1){
				$comis_text .= __('you send','pn');
			} else {
				$comis_text .= __('you receive','pn');
			}
		}
	}
	
	return pn_strip_input($comis_text);
}

function get_exchangestep_title(){
global $wpdb, $bids_id, $bids_data;	
	$title = '';
	if(isset($bids_data->id)){
		if($bids_data->status == 'auto'){
			$valut1 = pn_strip_input(ctv_ml($bids_data->valut1)).' '.pn_strip_input($bids_data->vtype1);
			$valut2 = pn_strip_input(ctv_ml($bids_data->valut2)).' '.pn_strip_input($bids_data->vtype2);
		    $title = sprintf(__('Exchange %1$s to %2$s','pn'),$valut1,$valut2);
			return apply_filters('get_exchange_title', $title, $bids_data->naps_id, $valut1, $valut2);
		} else {
			$title = __('ID Order','pn') . ' '. $bids_data->id;
			return apply_filters('get_exchangestep_title', $title, $bids_data->id);
		}
	}
}

function get_exchange_title(){
global $naps_id, $naps_data;	
	if(isset($naps_data->item1) and isset($naps_data->item2)){
		$item_title1 = pn_strip_input($naps_data->item1);
		$item_title2 = pn_strip_input($naps_data->item2);
								
		$title = sprintf(__('Exchange %1$s to %2$s','pn'),$item_title1,$item_title2);	
		return apply_filters('get_exchange_title', $title, $naps_id, $item_title1, $item_title2);
	}
}

function update_bids_meta($id, $key, $value){ 
	return update_pn_meta('bids_meta', $id, $key, $value);
}

function get_bids_meta($id, $key){
	return get_pn_meta('bids_meta', $id, $key);
}

function delete_bids_meta($id, $key){
	return delete_pn_meta('bids_meta', $id, $key);
}
function update_naps_meta($id, $key, $value){ 
	return update_pn_meta('naps_meta', $id, $key, $value);
}

function get_naps_meta($id, $key){
	return get_pn_meta('naps_meta', $id, $key);
}

function delete_naps_meta($id, $key){
	return delete_pn_meta('naps_meta', $id, $key);
}
function update_valuts_meta($id, $key, $value){ 
	return update_pn_meta('valuts_meta', $id, $key, $value);
}

function get_valuts_meta($id, $key){
	return get_pn_meta('valuts_meta', $id, $key);
}

function delete_valuts_meta($id, $key){
	return delete_pn_meta('valuts_meta', $id, $key);
}
function copy_naps_txtmeta($data_id, $new_id){
	copy_txtmeta('napsmeta', $data_id, $new_id);
}

function delete_naps_txtmeta($data_id){
	delete_txtmeta('napsmeta', $data_id);
}

function get_naps_txtmeta($data_id, $key){
	return get_txtmeta('napsmeta', $data_id, $key);
}

function update_naps_txtmeta($data_id, $key, $value){
	return update_txtmeta('napsmeta', $data_id, $key, $value);
}