<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Счета пользователей[:ru_RU][en_US:]User accounts[:en_US]
description: [ru_RU:]Счета пользователей[:ru_RU][en_US:]User accounts[:en_US]
version: 1.0
category: [ru_RU:]Пользователи[:ru_RU][en_US:]Users[:en_US]
cat: user
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* BD */
add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_userwallets');
function bd_pn_moduls_active_userwallets(){
global $wpdb;

/* 
счета пользователей 

user_id - id юзера
valut_id - id валюты
accountnum - номер счета
verify - 0-не верифицирован, 1-верифицирован
*/
	$table_name= $wpdb->prefix ."user_accounts";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		`user_id` bigint(20) NOT NULL default '0',	
		`user_login` varchar(250) NOT NULL,
		`valut_id` bigint(20) NOT NULL default '0',
		`accountnum` longtext NOT NULL,
		`verify` int(1) NOT NULL default '0',
		`vidzn` int(5) NOT NULL default '0',
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);

	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."user_accounts LIKE 'vidzn'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."user_accounts ADD `vidzn` int(5) NOT NULL default '0'");
    }	
	
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."valuts LIKE 'user_account'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."valuts ADD `user_account` int(2) NOT NULL default '1'");
    }		 
	
}

add_action('pn_bd_activated', 'bd_pn_moduls_migrate_userwallets');
function bd_pn_moduls_migrate_userwallets(){
global $wpdb;

	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."valuts LIKE 'user_account'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."valuts ADD `user_account` int(2) NOT NULL default '1'");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."user_accounts LIKE 'vidzn'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."user_accounts ADD `vidzn` int(5) NOT NULL default '0'");
    }	
	
}

add_filter('pn_tech_pages', 'list_tech_pages_userwallets');
function list_tech_pages_userwallets($pages){
  
	$pages[] = array(
		'post_name'      => 'userwallets',
		'post_title'     => '[ru_RU:]Ваши счета[:ru_RU][en_US:]Your accounts[:en_US]',
		'post_content'   => '[userwallets]',
		'post_template'   => 'pn-pluginpage.php',
	);		
	
	return $pages;
}
/* end BD */

add_action('admin_menu', 'pn_adminpage_userwallets');
function pn_adminpage_userwallets(){
global $premiumbox;
	if(current_user_can('administrator') or current_user_can('pn_userwallets')){
		$hook = add_menu_page(__('User accounts','pn'), __('User accounts','pn'), 'read', "pn_userwallets", array($premiumbox, 'admin_temp'), $premiumbox->get_icon_link('vtypes'));	
		add_action( "load-$hook", 'pn_trev_hook' );
		add_submenu_page("pn_userwallets", __('Add user account','pn'), __('Add user account','pn'), 'read', "pn_add_userwallets", array($premiumbox, 'admin_temp'));
	}
}

add_filter('pn_caps','userwallets_pn_caps');
function userwallets_pn_caps($pn_caps){
	$pn_caps['pn_userwallets'] = __('Work with user accounts','pn');
	return $pn_caps;
}

add_action('pn_valuts_edit','pn_valuts_edit_userwallets',0,2);
function pn_valuts_edit_userwallets($data_id, $array){
global $wpdb;	
	if(isset($array['vidzn'])){
		$wpdb->update($wpdb->prefix . 'user_accounts', array('vidzn'=>$array['vidzn']), array('valut_id'=>$data_id));
	}
}

add_action('pn_valuts_delete', 'pn_valuts_delete_userwallets');
function pn_valuts_delete_userwallets($id){
global $wpdb;	

	$items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."user_accounts WHERE valut_id = '$id'");
	foreach($items as $item){
		$item_id = $item->id;
		do_action('pn_userwallets_delete_before', $item_id, $item);
		$result = $wpdb->query("DELETE FROM ". $wpdb->prefix ."user_accounts WHERE id = '$item_id'");
		if($result){
			do_action('pn_userwallets_delete', $item_id, $item);
		}
	}
}

add_action('delete_user', 'delete_user_userwallets');
function delete_user_userwallets($user_id){
global $wpdb;
	
	$items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."user_accounts WHERE user_id = '$user_id'");
	foreach($items as $item){
		$item_id = $item->id;
		do_action('pn_userwallets_delete_before', $item_id, $item);
		$result = $wpdb->query("DELETE FROM ". $wpdb->prefix ."user_accounts WHERE id = '$item_id'");
		if($result){
			do_action('pn_userwallets_delete', $item_id, $item);
		}
	}	
}

add_filter('pn_valuts_addform','pn_valuts_addform_userwallets', 10, 2);
function pn_valuts_addform_userwallets($options, $data){
		
	$options['line_userwallets'] = array(
		'view' => 'line',
		'colspan' => 2,
	);	
	$options['user_account'] = array(
		'view' => 'select',
		'title' => __('Allow users to add new wallet in Account section','pn'),
		'options' => array('1'=>__('Yes','pn'),'0'=>__('No','pn')),
		'default' => is_isset($data, 'user_account'),
		'name' => 'user_account',
	);
	
	if(isset($options['bottom_title'])){
		unset($options['bottom_title']);
	}	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);	
	
	return $options;
}

add_filter('pn_valuts_addform_post', 'pn_valuts_addform_post_userwallets');
function pn_valuts_addform_post_userwallets($array){
	$array['user_account'] = intval(is_param_post('user_account'));
	return $array;
}

add_filter('account_list_pages','account_list_pages_userwallets', 0);
function account_list_pages_userwallets($account_list_pages){
	
	$account_list_pages['userwallets'] = array(
		'type' => 'page',			
	);
	
	return $account_list_pages;
}

add_filter('userwallets_one', 'def_userwallets_one', 10, 3);
function def_userwallets_one($html, $key, $data){
	
	if($key == 'title'){
		$html .= '
		<div class="usersbill_one_title">
			'. get_valut_title($data) .'
		</div>';
	} elseif($key == 'account'){
		$html .= '
		<div class="usersbill_one_account">
			'. pn_strip_input($data->accountnum) .'
		</div>';		
	} elseif($key == 'close'){
		$html .= '
		<div class="close_usersbill"></div>
		';			
	}
	
	return $html;
}

/* partner payouts */
add_action('siteplace_js','siteplace_js_payouts_userwallets');
function siteplace_js_payouts_userwallets(){
global $user_ID;	
	if($user_ID){	
?>	
jQuery(function($){ 
	$(document).on('click', '.pay_purse_link', function(){
		$('.pay_purse_ul').show();
		var id = $('#pay_valut_id').val();
		$('.pay_purse_line').hide();
		var cc = $('.ppl_'+id).length;
		if(cc > 0){
			$('.ppl_'+id).show();
		} else {
			$('.ppl_0').show();
		}
		return false;
	});
	
	$(document).on('click', '.pay_purse_line', function(){
		var account = $(this).attr('data-purse');
		$('#pay_valut_account').val(account);
		$('.pay_purse_ul').hide();
		return false;
	});	
	
    $(document).click(function(event) {
        if ($(event.target).closest(".pay_purse_link").length) return;
        $('.pay_purse_ul').hide();
        event.stopPropagation();
    });    
});		
<?php	
	}
} 

add_filter('payouts_input', 'payouts_input_userwallets');
function payouts_input_userwallets($input){
global $wpdb;
	
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);	
	
	$new_input ='
	<div class="pay_purse_link">
		<div class="pay_purse_link_ins">
			<div class="pay_purse_ul">
				<div class="pay_purse_line ppl_0" data-purse="">'. __('No wallet','pn') .'</div>';
				
				$purses = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."user_accounts WHERE user_id = '$user_id'");
				foreach($purses as $purse){
					$new_input .= '<div class="pay_purse_line ppl_'. $purse->valut_id .'" data-purse="'. pn_strip_input($purse->accountnum) .'">'. pn_strip_input($purse->accountnum) .'</div>';
				}
				
				$new_input .= '
			</div>
		</div>
	</div>';
	
	$input = str_replace('name="','class="pay_input_purse" name="',$input);				
	return $input . $new_input;
} 
/* end partner payouts */

/* exchange form */
add_action('siteplace_js','siteplace_js_exchange_purse_userwallets');
function siteplace_js_exchange_purse_userwallets(){
?>
jQuery(function($){ 
	
    $(document).on('click', function(event) {
        if ($(event.target).closest(".js_purse_link").length) return;
        $('.js_purse_ul').hide();		
		
        event.stopPropagation();
    });	
	
	$(document).on('click', '.js_purse_link', function(){
		$(this).parents('.js_window_wrap').find('.js_purse_ul').show();
		
		return false;
	});
	
	$(document).on('click', '.js_purse_line', function(){
		var account = $(this).attr('data-purse');
		$(this).parents('.js_window_wrap').find('input').val(account).trigger( "change" );
		$('.js_purse_ul').hide();
		
		return false;
	});	
	
});	
<?php	
} 

add_filter('form_bids_account_input', 'form_bids_account_input_userwallets', 10, 6);
function form_bids_account_input_userwallets($input, $id, $vdid, $purse, $placeholder, $h_class){
global $wpdb;
	
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);
	
	$purse_div = '';
	if($user_id){
		$purses = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."user_accounts WHERE user_id = '$user_id' AND valut_id='$vdid'");
		$cp = count($purses);
		if($cp > 0){
			$purse_div = '
			<div class="js_purse_link">
				<div class="js_purse_link_ins">
					<div class="js_purse_ul">
						<div class="js_purse_line" data-purse="">'. __('No wallet','pn') .'</div>';												
							foreach($purses as $ps){
								$purse_div .= '<div class="js_purse_line" data-purse="'. pn_strip_input($ps->accountnum) .'">'. pn_strip_input($ps->accountnum) .'</div>';
							}	
						$purse_div .= '
						</div>
				</div>
			</div>';
			$input = str_replace('class="','class="js_purse_input ',$input);
		}												
	}	
	
	return $purse_div . $input;
}
/* end exchange form */

global $premiumbox;
$premiumbox->file_include($path.'/add');
$premiumbox->file_include($path.'/list');

$premiumbox->auto_include($path.'/shortcode');