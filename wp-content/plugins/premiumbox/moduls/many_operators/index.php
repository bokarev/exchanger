<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Оператор live[:ru_RU][en_US:]Live operator[:en_US]
description: [ru_RU:]Выделение заявки цветом, если с ней работает оператор[:ru_RU][en_US:]Highlighting the request if operator processes it[:en_US]
version: 1.0
category: [ru_RU:]Заявки[:ru_RU][en_US:]Orders[:en_US]
cat: req
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* BD */
add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_operworks');
function bd_pn_moduls_active_operworks(){
global $wpdb;	
	
	$table_name = $wpdb->prefix ."bids_operators";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		`createdate` datetime NOT NULL,
		`user_id` bigint(20) NOT NULL default '0',
		`user_login` varchar(250) NOT NULL,
		`bid_id` bigint(20) NOT NULL default '0',
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);
	
}
/* end BD */

add_action('change_bidstatus_realdelete', 'change_bidstatus_realdelete_operworks', 10, 2);
function change_bidstatus_realdelete_operworks($item_id, $item){
global $wpdb;
	$wpdb->query("DELETE FROM ".$wpdb->prefix."bids_operators WHERE bid_id = '$item_id'");	
}

/* cron */
function del_operworks(){
global $wpdb, $premiumbox;
	$minuts = intval($premiumbox->get_option('operworks','minuts'));
	if($minuts > 0){
		$second = $minuts*60;
		$time = current_time('timestamp') - $second;
		$ldate = date('Y-m-d H:i:s', $time);
		$wpdb->query("DELETE FROM ".$wpdb->prefix."bids_operators WHERE createdate < '$ldate'");
	}
} 

add_filter('mycron_now', 'mycron_now_del_operworks');
function mycron_now_del_operworks($filters){
	$filters['del_operworks'] = __('Deleting operator sessions when working with orders','pn');
	return $filters;
}
/* end cron */

/* button */
add_action('pn_adminpage_content_pn_bids','operworks_pn_admin_content_pn_bids');
function operworks_pn_admin_content_pn_bids(){
?>
<script type="text/javascript">
jQuery(function($){
 	$(document).on('change', '.wmo_input', function(){
		var id = $(this).parents('.one_bids').attr('id').replace('bidid_','');
		var thet = $(this);
		var par = thet.parents('.wmo_wrap');
		var check = 0;
		if($(this).prop('checked')){
			check = 1;
		}
		thet.prop('disabled', true);
		var param = 'id=' + id + '&check='+check;
		$('.filter_change').addClass('active');
		$.ajax({
			type: "POST",
			url: "<?php pn_the_link_post('operworks_change');?>",
			dataType: 'json',
			data: param,
			error: function(res, res2, res3){
				<?php do_action('pn_js_error_response', 'ajax'); ?>
			},			
			success: function(res)
			{		
				$('.filter_change').removeClass('active');
				thet.prop('disabled', false);
				
				if(res['status'] == 'success'){
					par.html(res['html']);
				}
				if(res['status'] == 'error'){
					<?php do_action('pn_js_alert_response'); ?>
				} 		
			}
		});	
		return false;
	});
});
</script>
<?php	
}

add_action('premium_action_operworks_change', 'pn_premium_action_operworks_change');
function pn_premium_action_operworks_change(){
global $wpdb;	
	only_post();
	
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);
	
	$log = array();
	$log['status'] = '';
	$log['response'] = '';
	$log['status_code'] = 0; 
	$log['status_text'] = __('Error','pn');
	
	if(current_user_can('administrator') or current_user_can('pn_bids')){
	
		$bid_id = intval(is_param_post('id'));
		$check = intval(is_param_post('check'));
		
		if($check == 1){
			$work = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."bids_operators WHERE bid_id='$bid_id' AND user_id='$user_id'");
			$arr = array();
			$arr['createdate'] = current_time('mysql');
			$arr['user_id'] = $user_id;
			$arr['user_login'] = is_user($ui->user_login);
			$arr['bid_id'] = $bid_id;
			if(isset($work->id)){
				$wpdb->update($wpdb->prefix ."bids_operators", $arr, array('id'=>$work->id));
			} else {
				$wpdb->insert($wpdb->prefix ."bids_operators", $arr);
			}
		} else {
			$wpdb->query("DELETE FROM ".$wpdb->prefix."bids_operators WHERE bid_id='$bid_id' AND user_id='$user_id'");
		}
		$log['html'] = get_bid_operworks($bid_id);
		$log['status'] = 'success';
	
	} else {
		$log['status'] = 'error';
		$log['status_code'] = 1;
		$log['status_text'] = __('Authorisation Error','pn');
	}
	
	echo json_encode($log);	
	exit;
}
/* end button */

/* bids */
function get_bid_operworks($item_id){
global $wpdb, $user_ID, $premiumbox;

	$works = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."bids_operators WHERE bid_id = '$item_id'");
	$iam = 0;
	$yourself = intval($premiumbox->get_option('operworks','yourself'));
	$users = array();
	foreach($works as $work){
		if($work->user_id == $user_ID){
			$iam = 1;
			if($yourself){
				$users[$work->user_id] = is_user($work->user_login);
			}
		} else {
			$users[$work->user_id] = is_user($work->user_login);
		}
	}
	$ch = '';
	$class='';
	if($iam == 1){ $ch = 'checked="checked"'; }
	if(count($users) > 0 or $iam == 1){
		$class='btbg_redded';
	}	
	
	$html = '<div class="bids_text '. $class .'"><label><strong>'. __('Processing order','pn') .'</strong> <input type="checkbox" '. $ch .' class="wmo_input" name="" value="1" /></label></div>';
	
	if(count($users) > 0){
		$html .= '
		<div class="bids_text '. $class .'">
			<div><strong>'. __('Operators processing order are','pn') .':</strong></div>
			';
			foreach($users as $user_id => $user_login){
				$html .= '<div class="wmo_line"><a href="'. admin_url('user-edit.php?user_id='.$user_id) .'">'. $user_login .'</a></div>';
			}
			$html .= '
		</div>';
	}	

	return $html;
}

add_filter('onebid_col1','onebid_col1_operworks',99,3);
function onebid_col1_operworks($actions, $item, $data_fs){	
	
	$item_id = $item->id;
	$html = get_bid_operworks($item_id);
	
	$nactions = array();
	$nactions['operworks'] = array(
		'type' => 'html',
		'html' => '<div class="wmo_wrap">'.$html.'</div>',
	);
	$nactions = array_merge($nactions,$actions);
	
	return $nactions;
}
/* end bids */

/* ajax */
add_filter('globalajax_admin_data_request', 'operworks_globalajax_admin_data_request', 10, 3);
function operworks_globalajax_admin_data_request($params, $link, $page){
	if($page == 'pn_bids'){
		$params['bids_ids'] = "'+ $('#visible_ids').val() +'";
	}
	return $params;
}

add_filter('globalajax_admin_data', 'operworks_globalajax_admin_data', 10, 3);
function operworks_globalajax_admin_data($log, $link, $page){
global $wpdb;

	if($page == 'pn_bids'){
		if(current_user_can('administrator') or current_user_can('pn_bids')){
			$bids_ids = is_param_post('bids_ids');
			$bids_ids_parts = explode(',',$bids_ids);
			$ins = array();
			foreach($bids_ids_parts as $v){
				$v = intval($v);
				if($v){
					$ins[] = "'". $v ."'";
				}
			}
			if(is_array($ins) and count($ins) > 0){
				$ins_join = join(',', $ins);
				$bids = array();
				$items = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."bids WHERE id IN($ins_join)");
				foreach($items as $item){
					$bids[$item->id] = get_bid_operworks($item->id);
				}
				$log['wmo_bids'] = $bids;
			}
		}
	}
	return $log;
}

add_action('globalajax_admin_data_jsresult', 'operworks_globalajax_admin_data_jsresult', 10, 2);
function operworks_globalajax_admin_data_jsresult($link, $page){
	if($page == 'pn_bids'){
?>
if(res['wmo_bids']){
	for (key in res['wmo_bids']) {
		$('#bidid_'+ key).find('.wmo_wrap').html(res['wmo_bids'][key]);
	}	
}
<?php
	}
}
/* end ajax */

/* settings */
add_action('admin_menu', 'pn_adminpage_operworks');
function pn_adminpage_operworks(){
global $premiumbox;		
	add_submenu_page("pn_moduls", __('Live operator','pn'), __('Live operator','pn'), 'administrator', "pn_operworks", array($premiumbox, 'admin_temp'));
}

add_action('pn_adminpage_title_pn_operworks', 'pn_admin_title_pn_operworks');
function pn_admin_title_pn_operworks($page){
	_e('Live operator','pn');
} 

add_action('pn_adminpage_content_pn_operworks','def_pn_admin_content_pn_operworks');
function def_pn_admin_content_pn_operworks(){
global $wpdb, $premiumbox;

	$options = array();
	$options['top_title'] = array(
		'view' => 'h3',
		'title' => __('Operator settings','pn'),
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);	
	$options['minuts'] = array(
		'view' => 'inputbig',
		'title' => __('Time needed to process order (min.)', 'pn'),
		'default' => $premiumbox->get_option('operworks','minuts'),
		'name' => 'minuts',
	);
	$options['yourself'] = array(
		'view' => 'select',
		'title' => __('Display your login in list of operators', 'pn'),
		'default' => $premiumbox->get_option('operworks','yourself'),
		'options' => array('0'=>__('No','pn'), '1'=>__('Yes','pn')),
		'name' => 'yourself',
	);	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('', $options);	
}  

add_action('premium_action_pn_operworks','def_premium_action_pn_operworks');
function def_premium_action_pn_operworks(){
global $wpdb, $premiumbox;	

	only_post();
	pn_only_caps(array('administrator'));

	$options = array('minuts', 'yourself');	
	foreach($options as $key){
		$premiumbox->update_option('operworks', $key, intval(is_param_post($key)));
	}				

	$url = admin_url('admin.php?page=pn_operworks&reply=true');
	wp_redirect($url);
	exit;
} 
/* end settings */