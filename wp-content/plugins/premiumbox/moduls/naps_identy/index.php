<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Номер денежного перевода[:ru_RU][en_US:]Money transfer number[:en_US]
description: [ru_RU:]Форма для ввода номера денежного перевода после создания заявки[:ru_RU][en_US:]Form used for entering money transfer number after creating a request[:en_US]
version: 1.0
category: [ru_RU:]Направления обменов[:ru_RU][en_US:]Exchange directions[:en_US]
cat: naps
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* BD */
add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_napsidenty');
function bd_pn_moduls_active_napsidenty(){
global $wpdb;	
	
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."bids LIKE 'napsidenty'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."bids ADD `napsidenty` varchar(250) NOT NULL");
    }
	
}
/* end BD */

add_action('tab_naps_tab8', 'napsidenty_tab_naps_tab8', 30, 2);
function napsidenty_tab_naps_tab8($data, $data_id){
global $premiumbox;
?>
	<tr>
		<th><?php _e('Show field for entering number of money transfer','pn'); ?></th>
		<td>
			<div class="premium_wrap_standart">
				<select name="enable_naps_identy" autocomplete="off">
					<?php 
					$enable_naps_identy = intval(get_naps_meta($data_id, 'enable_naps_identy')); 
					?>						
					<option value="0" <?php selected($enable_naps_identy,0); ?>><?php _e('No','pn');?></option>
					<option value="1" <?php selected($enable_naps_identy,1); ?>><?php _e('Yes','pn');?></option>						
				</select>
			</div>
		</td>	
	</tr>
	<tr>
		<th><?php _e('Field name for entering number of money transfer','pn'); ?></th>
		<td>
			<?php 
			$naps_identy_text = pn_strip_input(get_naps_meta($data_id, 'naps_identy_text'));
			if(!$naps_identy_text){ $naps_identy_text = $premiumbox->get_option('napsidenty','text'); }
			pn_inputbig_ml('','naps_identy_text', $naps_identy_text); 
			?>
		</td>		
	</tr>	
<?php	
}

add_action('pn_naps_edit_before','pn_naps_edit_napsidenty');
add_action('pn_naps_add','pn_naps_edit_napsidenty');
function pn_naps_edit_napsidenty($data_id){
	
	$enable_naps_identy = intval(is_param_post('enable_naps_identy'));
	update_naps_meta($data_id, 'enable_naps_identy', $enable_naps_identy);
	
	$naps_identy_text = pn_strip_input(is_param_post_ml('naps_identy_text'));
	update_naps_meta($data_id, 'naps_identy_text', $naps_identy_text);
	
}

add_action('admin_menu', 'pn_adminpage_napsidenty');
function pn_adminpage_napsidenty(){
global $premiumbox;		
	add_submenu_page("pn_moduls", __('Number of money transfer','pn'), __('Number of money transfer','pn'), 'administrator', "pn_napsidenty", array($premiumbox, 'admin_temp'));
} 

add_action('pn_adminpage_title_pn_napsidenty', 'pn_admin_title_pn_napsidenty');
function pn_admin_title_pn_napsidenty($page){
	_e('Number of money transfer','pn');
}

add_action('pn_adminpage_content_pn_napsidenty','pn_admin_content_pn_napsidenty');
function pn_admin_content_pn_napsidenty(){
global $wpdb, $premiumbox;

	$options = array();
	$options['top_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	$options['text'] = array(
		'view' => 'inputbig',
		'title' => __('Field name for entering number of money transfer','pn'),
		'default' => $premiumbox->get_option('napsidenty','text'),
		'name' => 'text',
		'work' => 'input',
		'ml' => 1,
	);	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);			
	pn_admin_one_screen('', $options);  
}  

add_action('premium_action_pn_napsidenty','def_premium_action_pn_napsidenty');
function def_premium_action_pn_napsidenty(){
global $wpdb, $premiumbox;	

	only_post();
	pn_only_caps(array('administrator'));
	
	$options = array();
	$options['text'] = array(
		'name' => 'text',
		'work' => 'input',
		'ml' => 1,
	);	
	$data = pn_strip_options('', $options);
	foreach($data as $key => $val){
		$premiumbox->update_option('napsidenty', $key, $val);
	}				

	$back_url = is_param_post('_wp_http_referer');
	$back_url .= '&reply=true';
			
	wp_safe_redirect($back_url);
	exit;
} 

add_filter('merchant_pay_button_visible','napsidenty_merchant_pay_button_visible', 0, 4);
function napsidenty_merchant_pay_button_visible($ind, $m_id, $item, $naps){
	if($ind == 1){
		$naps_id = $naps->id;
		$bid_id = $item->id;
		$enable_naps_identy = intval(get_naps_meta($naps_id, 'enable_naps_identy'));
		if($enable_naps_identy == 1){ 
			$napsidenty = pn_strip_input(is_isset($item,'napsidenty'));
			if(!$napsidenty){ 
				return 0;
			}
		}
	}
	return $ind;
}

add_action('before_bidaction_payedbids', 'napsidenty_before_bidaction_payedbids');
function napsidenty_before_bidaction_payedbids($obmen){
	$naps_id = $obmen->naps_id;
	$bid_id = $obmen->id;
	$enable_naps_identy = intval(get_naps_meta($naps_id, 'enable_naps_identy'));
	if($enable_naps_identy == 1){	
		$napsidenty = pn_strip_input(is_isset($obmen,'napsidenty'));
		if(!$napsidenty){ 
			$url = get_bids_url($obmen->hashed);
			wp_redirect($url);
			exit;
		}
	}	
}

add_filter('merchant_formstep_after','napsidenty_merchant_formstep_after', 9999, 4);
function napsidenty_merchant_formstep_after($html, $m_id, $item, $naps){
	$naps_id = $naps->id;
	$bid_id = $item->id;
	$enable_naps_identy = intval(get_naps_meta($naps_id, 'enable_naps_identy'));
	if($enable_naps_identy == 1){ 
		$napsidenty = pn_strip_input(is_isset($item,'napsidenty'));
		if(!$napsidenty){ 
			$naps_identy_text = ctv_ml(pn_strip_input(get_naps_meta($naps_id, 'naps_identy_text')));
			$new_html = '
			<div class="block_smsbutton">
				<div class="block_smsbutton_ins">
					<div class="block_smsbutton_label">
						<div class="block_smsbutton_label_ins">
							'. $naps_identy_text .'
						</div>
					</div>
					<div class="block_smsbutton_action">
						<input type="text" name="" id="napsidenty_text" value="" />
						<input type="submit" name="" data-id="'. $bid_id .'" id="napsidenty_send" value="'. __('Confirm','pn') .'" />
							<div class="clear"></div>
					</div>
				</div>
			</div>
			';			
			return $new_html;
		}
	}
	
	return $html;
} 

add_action('siteplace_js','siteplace_js_napsidenty');
function siteplace_js_napsidenty(){
?>	
jQuery(function($){ 

	$(document).on('click', '#napsidenty_send', function(){
		if(!$(this).prop('disabled')){
				
			var id = $(this).attr('data-id');
			var txt = $('#napsidenty_text').val();
			var thet = $(this);
			thet.prop('disabled', true);

			var dataString='id=' + id + '&txt=' + txt;
			$.ajax({
				type: "POST",
				url: "<?php echo get_ajax_link('save_napsidenty_bids');?>",
				dataType: 'json',
				data: dataString,
				error: function(res, res2, res3){
					<?php do_action('pn_js_error_response', 'ajax'); ?>
				},			
				success: function(res)
				{
					if(res['status'] == 'success'){
						window.location.href = '';
					} 
					if(res['status'] == 'error'){
						thet.prop('disabled', false);
						<?php do_action('pn_js_alert_response'); ?>
					}
				}
			});
		}
		
		return false;
	});		

});		
<?php	
} 

add_action('myaction_site_save_napsidenty_bids', 'def_myaction_ajax_save_napsidenty_bids');
function def_myaction_ajax_save_napsidenty_bids(){
global $or_site_url, $wpdb, $premiumbox;	
	
	only_post();
	
	$log = array();
	$log['response'] = '';
	$log['status'] = '';
	$log['status_text'] = '';
	$log['status_code'] = 0;
	
	$premiumbox->up_mode();
	
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);	
	$bid_id = intval(is_param_post('id'));
	$txt = pn_strip_input(is_param_post('txt'));
	if($bid_id and $txt){
		
		$arr = array();
		$arr['napsidenty'] = $txt;
		$wpdb->update($wpdb->prefix."bids", $arr, array('id'=>$bid_id));
		
		$log['status'] = 'success';
		$log['status_code'] = 0;		
	} else {
		$log['status'] = 'error';
		$log['status_code'] = 1;
		$log['status_text'] = __('You have not entered number','pn');		
	}
	
	echo json_encode($log);
	exit;
}

add_filter('change_bids_filter_list', 'napsidenty_change_bids_filter_list'); 
function napsidenty_change_bids_filter_list($lists){
global $wpdb;
	
	$lists['other']['napsidenty'] = array(
		'title' => __('Number of money transfer','pn'),
		'name' => 'napsidenty',
		'view' => 'input',
		'work' => 'input',
	);	
	
	return $lists;
}

add_filter('where_request_sql_bids', 'where_request_sql_bids_napsidenty',0,2);
function where_request_sql_bids_napsidenty($where, $pars_data){
global $wpdb;	
	
	$napsidenty = pn_strip_input(pn_sfilter(is_isset($pars_data,'napsidenty')));
	if($napsidenty){
		$where .= " AND {$wpdb->prefix}bids.napsidenty LIKE '%$napsidenty%'";
	} 
	
	return $where;
}

add_filter('onebid_icons','onebid_icons_napsidenty',99,3);
function onebid_icons_napsidenty($onebid_icon, $item, $data_fs){
global $wpdb;
	 
	$napsidenty = pn_strip_input(is_isset($item,'napsidenty'));
	if($napsidenty){
		
		$onebid_icon['napsidenty'] = array(
			'type' => 'text',
			'title' => __('Number of money transfer','pn'),
			'label' => '[napsidenty]',
		);		
		
	}
	
	return $onebid_icon; 
}

add_filter('get_bids_replace_text','get_bids_replace_text_napsidenty',99,3);
function get_bids_replace_text_napsidenty($text, $item, $data_fs){
global $wpdb;
	
	if(strstr($text, '[napsidenty]')){
		$napsidenty = '';
		$identy = pn_strip_input($item->napsidenty);
		$bid_id = $item->id;
		$cc = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."bids WHERE status != 'auto' AND napsidenty = '$identy' AND id != '$bid_id'");
		$cl = '';
		if($cc > 0){
			$cl = 'bred_dash';
		}	
		$napsidenty = '<span class="item_napsidenty '. $cl .'">' . pn_strip_input(is_isset($item,'napsidenty')) .'</span>';
		$text = str_replace('[napsidenty]', $napsidenty,$text);
	}	
	
	return $text;
}