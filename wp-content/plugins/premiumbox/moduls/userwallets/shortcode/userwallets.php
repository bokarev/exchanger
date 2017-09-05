<?php
if( !defined( 'ABSPATH')){ exit(); } 

/* добавляем JS */
add_action('siteplace_js','siteplace_js_userwallets');
function siteplace_js_userwallets(){
global $user_ID;	
	
	if($user_ID){	
?>	
/* userwallets */
jQuery(function($){ 
	
	$(document).on('change', '#userbill_valut_select', function(){
		var id = $(this).val();
		$('.userbill_one_tab').hide();
		$('#userbill_one_tab_'+id).show();
	});

	$(document).on('keyup', '.js_schet', function(){
		var maximum = parseInt($(this).attr('maxlength'));
		var vale = parseInt($(this).val().length);
		var par = $(this).parents('.js_schet_wrap');
		if(maximum == vale){
			if($(this).next('.js_schet').length > 0){
				$(this).next('.js_schet').focus();
			} 
		} 
	});

    $(document).on('click', '.close_usersbill', function(){
		var id = $(this).parents('.usersbill_table_one').attr('id').replace('usersbill_id_','');
		var thet = $(this);
		if(!thet.hasClass('act')){
			thet.addClass('act');
			var dataString='id=' + id;
			$.ajax({
			type: "POST",
			url: "<?php echo get_ajax_link('delete_userwallets');?>",
			dataType: 'json',
			data: dataString,
			error: function(res, res2, res3){
				<?php do_action('pn_js_error_response', 'ajax'); ?>
			},			
			success: function(res)
			{
				if(res['status'] == 'success'){
					$('#usersbill_id_' + id).remove();
				} 
				if(res['status'] == 'error'){
					<?php do_action('pn_js_alert_response'); ?>
				}
				
				thet.removeClass('act');
				
			}
			});
		}
        return false;
    });	
});		
/* end userwallets */
<?php	
	}
} 
/* end добавляем JS */

/* шорткод валют пользователя */
function userwallets_page_shortcode($atts, $content) {
global $wpdb;
	
	$temp = '';
	
	$temp .= apply_filters('before_userwallets_page','');
	
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);	
	
	if($user_id){

		$valuts = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."valuts WHERE valut_status = '1' AND user_account = '1' ORDER BY psys_title ASC");
	
		$select_userbill = '
		<select name="valut_id" id="userbill_valut_select" autocomplete="off">
			<option value="0">-- '. __('select currency','pn') .' --</option>
		';
			foreach($valuts as $valut){
				$select_userbill .='<option value="'. $valut->id .'">'. get_valut_title($valut) .'</option>';
			}
		$select_userbill .= '
		</select>';
		
		$select_tabs = '<div class="userbill_tabs">';
			foreach($valuts as $valut){
				$input = '';
				$help = '';
				if($valut->vidzn == 0){
					$input = '
					<input type="text" name="schet['. $valut->id .']" placeholder="'. pn_strip_input($valut->firstzn) .'" class="long_schet" value="" />
					';
					$help = '<p>'. __('Enter your account number','pn') .'</p>';
				} elseif($valut->vidzn == 2){
					$input = '
					<input type="text" name="schet['. $valut->id .']" placeholder="'. pn_strip_input($valut->firstzn) .'" class="long_schet" value="" />
					';
					$help = '<p>'. __('Enter your phone number','pn') .'</p>';				
				} else {	
					$input = '
					<input type="text" name="schet['. $valut->id .'][]" class="shot_schet js_schet" maxlength="4" value="" />
					<input type="text" name="schet['. $valut->id .'][]" class="shot_schet js_schet" maxlength="4" value="" />
					<input type="text" name="schet['. $valut->id .'][]" class="shot_schet js_schet" maxlength="4" value="" />
					<input type="text" name="schet['. $valut->id .'][]" class="shot_schet js_schet" maxlength="4" value="" />
					<input type="text" name="schet['. $valut->id .'][]" class="shot_schet js_schet" maxlength="4" value="" />
					';
					$help = '<p>'. __('Enter your card number','pn') .'</p>';					
				}
					if($valut->minzn > 0){
						$help .= '<p>'. __('Min. number of characters','pn') .' - '. intval($valut->minzn) .'</p>';
					}
					if($valut->maxzn > 0){
						$help .= '<p>'. __('Max. number of characters','pn') .' - '. intval($valut->maxzn) .'</p>';
					}
					if($valut->cifrzn == 0){ 
						$help .= '<p>'. __('use numbers and letters','pn') .'</p>';	
					} elseif($valut->cifrzn == 1){
						$help .= '<p>'. __('use numbers','pn') .'</p>';
					} elseif($valut->cifrzn == 2){
						$help .= '<p>'. __('use letters','pn') .'</p>';					
					}				
				
				$select_tabs .= '<div class="userbill_one_tab" id="userbill_one_tab_'. $valut->id .'">';
				
				$array = array(
					'[input]' => $input,
					'[help]' => $help,
					'[submit]' => '<input type="submit" formtarget="_top" name="" value="'. __('Save','pn') .'" />',
				);	
				$userbill_one_line = '
					<div class="userbill_one_line js_schet_wrap">
						[input]
							<div class="clear"></div>
					</div>
					<div class="userbill_help">
						[help]
					</div>
					[submit]
				';
				$userbill_one_line = apply_filters('userwallets_select_line_temp',$userbill_one_line);
				$select_tabs .= get_replace_arrays($array, $userbill_one_line);			
				$select_tabs .= '</div>';
			}		
		$select_tabs .= '</div>';
	
		$limit = apply_filters('limit_list_userwallets', 15);
		$count = $wpdb->query("SELECT ".$wpdb->prefix."user_accounts.id FROM ".$wpdb->prefix."user_accounts LEFT OUTER JOIN ".$wpdb->prefix."valuts ON(".$wpdb->prefix."user_accounts.valut_id = ".$wpdb->prefix."valuts.id) WHERE user_id = '$user_id'");
		$pagenavi = get_pagenavi_calc($limit,get_query_var('paged'),$count);
		$datas = $wpdb->get_results("SELECT *, ". $wpdb->prefix ."user_accounts.id AS account_id FROM ". $wpdb->prefix ."user_accounts LEFT OUTER JOIN ".$wpdb->prefix."valuts ON(".$wpdb->prefix."user_accounts.valut_id = ".$wpdb->prefix."valuts.id) WHERE user_id = '$user_id' ORDER BY ".$wpdb->prefix."valuts.id DESC LIMIT ". $pagenavi['offset'] .",".$pagenavi['limit']);	
		$pagenavi_html = get_pagenavi($pagenavi);
	
		$list_userwallets_items = array(
			'title' => 'title',
			'account' => 'account',
			'close' => 'close',
		);
		$list_userwallets_items = apply_filters('list_userwallets_items', $list_userwallets_items);
	
		$accounts = '';
		if($count > 0){
			foreach($datas as $data){
				$line_one = '
				<div class="usersbill_table_one" id="usersbill_id_'. $data->account_id .'">
				';
					foreach($list_userwallets_items as $v){
						$line_one .= apply_filters('userwallets_one', '', $v, $data);
					}
				$line_one .= '
				</div>';
				$accounts .= $line_one;
			}	
		} else {
			$accounts .= apply_filters('userwallets_noitem', '<div class="usersbill_table_one"><div class="no_items"><div class="no_items_ins">'. __('No data','pn') .'</div></div></div>');
		}	
	
		$array = array(
			'[form]' => '<form method="post" class="ajax_post_form" action="'. get_ajax_link('userwalletsform') .'">',
			'[/form]' => '</form>',
			'[result]' => '<div class="resultgo"></div>',
			'[select_userbill]' => $select_userbill,
			'[select_tabs]' => $select_tabs,
			'[pagenavi]' => $pagenavi_html,
			'[accounts]' => $accounts,
			'[submit]' => '<input type="submit" formtarget="_top" name="" value="'. __('Save','pn') .'" />',
		);	
		$shortcode_temp = '
			[form]
			[result]
			<div class="usersbill_form">
				<div class="usersbill_form_ins">
					<div class="usersbill_form_title">
						<div class="usersbill_form_title_ins">
							'. __('Add account','pn') .'
						</div>
					</div>	
					<div class="userbill_title">
						[select_userbill]
					</div>
					[select_tabs]
					
						<div class="clear"></div>
				</div>	
			</div>
			[/form]
			
			<div class="usersbill_table">
				<div class="usersbill_table_ins">
					<div class="usersbill_table_title">
						<div class="usersbill_table_title_ins">
							'. __('Your accounts','pn') .'
						</div>
					</div>

					[accounts]
				</div>
			</div>
			[pagenavi]
		';
		$shortcode_temp = apply_filters('userwallets_page_temp',$shortcode_temp);
		$temp .= get_replace_arrays($array, $shortcode_temp);

	} else {
		$temp .= '<div class="resultfalse">'. __('Error! Page is available for authorized users only','pn') .'</div>';
	}
	
	$temp .= apply_filters('after_userwallets_page','');

	return $temp;
}
add_shortcode('userwallets', 'userwallets_page_shortcode');
/* end шорткод валют пользователя */

/* обработка формы */
add_action('myaction_site_userwalletsform', 'def_myaction_ajax_userwalletsform');
function def_myaction_ajax_userwalletsform(){
global $wpdb, $premiumbox;	
	
	only_post();
	
	$log = array();
	$log['status'] = '';
	$log['status_text'] = '';
	$log['status_code'] = 0;
	
	$premiumbox->up_mode();
	
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);	
	
	if(!$user_id){
		$log['status'] = 'error'; 
		$log['status_code'] = 1;
		$log['status_text'] = __('Error! You must authorize','pn');
		echo json_encode($log);
		exit;		
	}	
	
	$log = apply_filters('before_ajax_form_field', $log, 'userwalletsform');
	$log = apply_filters('before_ajax_userwalletsform', $log);
	
	$valut_id = intval(is_param_post('valut_id'));
	$item = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."valuts WHERE valut_status = '1' AND user_account = '1' AND id='$valut_id'");
	if(isset($item->id)){
		$account = '';
		$schet = is_param_post('schet');

		if($item->vidzn == 1){
			if(isset($schet[$valut_id])){
				foreach($schet[$valut_id] as $val){
					$zn = trim($val);
					if(mb_strlen($zn) >= 2){
						$account .= $zn;
					}
				}
			}
		} else {
			if(isset($schet[$valut_id])){
				$account = trim($schet[$valut_id]);
			}
		}
		$account = pn_strip_input($account);	
		$account = get_purse($account, $item);
		if($account){	
			$cc = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."user_accounts WHERE user_id='$user_id' AND valut_id='$valut_id' AND accountnum='$account'");
			if($cc == 0){		
				$array = array();
				$array['user_id'] = $user_id;
				$array['user_login'] = is_user($ui->user_login);
				$array['valut_id'] = $valut_id;
				$array['vidzn'] = intval($item->vidzn);
				$array['accountnum'] = $account;
				$wpdb->insert($wpdb->prefix.'user_accounts', $array);
				$data_id = $wpdb->insert_id;	
				do_action('pn_userwallets_add', $data_id, $array);
				
				$log['status'] = 'success';
				$log['status_text'] = __('Account is successfully added','pn');
				$log['url'] = apply_filters('userwallets_redirect', $premiumbox->get_page('userwallets'));	
			} else {	
				$log['status'] = 'error';
				$log['status_code'] = 1;
				$log['status_text'] = __('Error! This account already exists','pn');
			}
		} else {
			$log['status'] = 'error';
			$log['status_code'] = 1;
			$log['status_text'] = __('Error! Invalid wallet account','pn');				
		} 
	} else {
		$log['status'] = 'error';
		$log['status_code'] = 1;
		$log['status_text'] = __('Error! Currency does not exist or disabled','pn');		
	}
	
	echo json_encode($log);
	exit;
}

add_action('myaction_site_delete_userwallets', 'def_myaction_ajax_delete_userwallets');
function def_myaction_ajax_delete_userwallets(){
global $or_site_url, $wpdb, $premiumbox;	
	
	only_post();
	
	$log = array();
	$log['status'] = '';
	$log['status_text'] = '';
	$log['status_code'] = 0;
	
	$premiumbox->up_mode();
	
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);	
	
	if(!$user_id){
		$log['status'] = 'error'; 
		$log['status_code'] = 1;
		$log['status_text'] = __('Error! You must authorize','pn');
		echo json_encode($log);
		exit;		
	}	
	
	$id = intval(is_param_post('id'));
	$item = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."user_accounts WHERE user_id = '$user_id' AND id='$id'");
	if(isset($item->id)){
		do_action('pn_userwallets_delete_before', $id, $item);
		$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."user_accounts WHERE id='$id'");
		if($result){
			do_action('pn_userwallets_delete', $id, $item);
		}
		$log['status'] = 'success';
	} else {
		$log['status'] = 'error';
		$log['status_code'] = 1;
		$log['status_text'] = __('Error! Currency does not exist or disabled','pn');		
	}
	
	echo json_encode($log);
	exit;
}
/* end обработка формы */