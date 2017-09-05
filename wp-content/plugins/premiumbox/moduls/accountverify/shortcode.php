<?php
if( !defined( 'ABSPATH')){ exit(); } 

function get_usac_files($usac_id){
global $wpdb;
	
	$html = '<div class="verify_accline_wrap">';
	$items = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."uv_accounts_files WHERE uv_id='$usac_id'");
	foreach($items as $item){
		$html .='<div class="verify_accline accline_'. $item->id .'"><a href="'. get_usac_doc($item->id) .'" target="_blank">'. pn_strip_input($item->uv_data) .'</a> | <a href="#" data-id="'. $item->id .'" class="bred red js_usac_del">'. __('Delete','pn') .'</a></div>';
	}	
	$html .= '</div>';
	
	return $html;
}

global $premiumbox;
if($premiumbox->get_option('usve','acc_status') == 1){
	add_action('siteplace_js','siteplace_js_userverify_acc');
	add_filter('list_userwallets_items','list_userwallets_items_userverify',99,2);
	add_filter('userwallets_one', 'userwallets_one_userverify_acc', 10, 3);
	add_action('myaction_site_goverify_account', 'def_myaction_ajax_goverify_account');
}

function userwallets_one_userverify_acc($html, $key, $data){
global $wpdb;	
	
	if($key == 'verify'){
		$valut_id = $data->valut_id;
		$account_id = $data->account_id;
		$user_id = $data->user_id;
		$has_verify = intval(get_valuts_meta($valut_id, 'has_verify'));
		if($has_verify == 1){
			$verify = intval($data->verify);
			if($verify == 1){
				return '<div class="verify_status success">'. __('Verified','pn') .'</div>';
			} else {
				$verify_request = $wpdb->query("SELECT * FROM ". $wpdb->prefix ."uv_accounts WHERE usac_id='$account_id' AND status='0'");
				if($verify_request > 0){ 
					return '<div class="verify_status wait">'. __('Verification request is in process','pn') .'</div>';
				} else {
					$help = pn_strip_text(ctv_ml(get_valuts_meta($valut_id, 'help_verify')));
					$html = '
					<div class="userwallets_wrap">
						<div class="verify_status not"><a href="#" class="verify_tab_action">'. __('Unverified','pn') .'</a></div>
						<div class="verify_tab_action_div">
							<div class="verify_tab_action_div_ins">
						';
								if($help){
									$html .= '<div class="verify_tab_descr">'. apply_filters('comment_text',$help) .'</div>';	
								}
								
								$verify_files = intval(get_valuts_meta($valut_id, 'verify_files'));
								if($verify_files > 0){
									
									$max_mb = pn_max_upload();
									$max_upload_size = $max_mb * 1024 * 1024;
									$fileupform = pn_enable_filetype();	
									
									$html .='
									<form action="'. get_ajax_link('accountverify_upload') .'" class="verify_acc_form" enctype="multipart/form-data" method="post">
										<input type="hidden" name="account_id" value="'. $account_id .'" />
										
										<div class="verify_acc_syst">('. strtoupper(join(', ',$fileupform)) .', '. __('max.','pn') .' '. $max_mb .''. __('MB','pn') .')</div>
													
										<div class="verify_acc_file">
											<input type="file" class="verify_acc_filesome" name="file" value="" />
										</div>
									';
									
									$html .= '
										<div class="verify_acc_html">';
										
									$html .= get_usac_files($account_id);
									
									$html .= '
										</div>
									</form>	
									';									
									
								}
								
						$html .= '
								<div class="verify_tab_action_link" data-id="'. $account_id .'" data-title="'. __('Verification request is in process','pn') .'">'. __('Send a request','pn') .'</div>
						
								<div class="clear"></div>
							</div>
						</div>
					</div>
					';
				}
			}
		}	
	}
	
	return $html;
}

function list_userwallets_items_userverify($list){
	
	$list['verify'] = 'verify';
	
	return $list;
}

function siteplace_js_userverify_acc(){
global $user_ID;	
	if($user_ID){
		
		$max_mb = pn_max_upload();
		$max_upload_size = $max_mb * 1024 * 1024;
		$fileupform = pn_enable_filetype();
?>	
jQuery(function($){	 
	
	$(document).on('click', '.verify_tab_action', function(){
		var par = $(this).parents('.userwallets_wrap');
		par.find('.verify_tab_action_div').toggle();
		return false;
	});
	
    $(document).on('click', '.verify_tab_action_link', function(){
		var thet = $(this);
		var par = thet.parents('.userwallets_wrap');
		var id = thet.attr('data-id');
		var wait_title = thet.attr('data-title');
		
		if(!thet.hasClass('act')){
			thet.addClass('act');
		
			var dataString='id=' + id;
			$.ajax({
			type: "POST",
			url: "<?php echo get_ajax_link('goverify_account');?>",
			dataType: 'json',
			data: dataString,
			error: function(res, res2, res3){
				<?php do_action('pn_js_error_response', 'ajax'); ?>
			},			
			success: function(res)
			{
				if(res['status'] == 'success'){
					par.find('.verify_tab_action_div').hide();
					par.find('.verify_status').removeClass('not').addClass('wait').html(wait_title);
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
	
	$(document).on('change', '.verify_acc_filesome', function(){
		var thet = $(this);
		var text = thet.val();
		var par = thet.parents('form');
		var ccn = thet[0].files.length;
		if(ccn > 0){
            var fileInput = thet[0];
			var bitec = fileInput.files[0].size;		
			if(bitec > <?php echo $max_upload_size; ?>){
				alert('<?php _e('Max.','pn'); ?> <?php echo $max_mb; ?> <?php _e('MB','pn'); ?> !');
				thet.val('');
			} else {
				par.submit();
			}
		}	
	});		
	
    $('.verify_acc_form').ajaxForm({
	    dataType:  'json',
        beforeSubmit: function(a,f,o) {
			f.addClass('uploading');
			$('.uploading input').prop('disabled', true);
        },
		error: function(res, res2, res3) {
			<?php do_action('pn_js_error_response', 'ajax'); ?>
		},		
        success: function(res) {
            if(res['status'] == 'success'){
				$('.uploading').find('.verify_acc_html').html(res['response']);
		    } 
			if(res['status'] == 'error'){
				<?php do_action('pn_js_alert_response'); ?>
		    } 	
			if(res['url']){
				window.location.href = res['url']; 
			}			
			
			$('.uploading input').prop('disabled', false);
			$('.verify_acc_form').removeClass('uploading');
        }
    });	
	
    $(document).on('click', '.js_usac_del', function(){
		var id = $(this).attr('data-id');
		var thet = $(this);
		if(!thet.hasClass('act')){
			thet.addClass('act');
			var dataString='id=' + id;
			$.ajax({
			type: "POST",
			url: "<?php echo get_ajax_link('delete_accverify');?>",
			dataType: 'json',
			data: dataString,
			error: function(res, res2, res3){
				<?php do_action('pn_js_error_response', 'ajax'); ?>
			},			
			success: function(res)
			{
				if(res['status'] == 'success'){
					$('.accline_' + id).remove();
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
<?php	
	}
} 

function def_myaction_ajax_goverify_account(){
global $or_site_url, $wpdb, $premiumbox;	
	
	only_post();
	
	$log = array();
	$log['response'] = '';
	$log['status'] = '';
	$log['status_code'] = 0;
	$log['status_text'] = '';

	$premiumbox->up_mode();
	
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);
	
	if(!$user_id){
		$log['status'] = 'error'; 
		$log['status_code'] = 1;
		$log['status_text']= __('Error! You must authorize','pn');
		echo json_encode($log);
		exit;		
	}

	if($premiumbox->get_option('usve','acc_status') != 1){
		$log['status'] = 'error'; 
		$log['status_code'] = 1;
		$log['status_text']= __('Error! Verification is disabled','pn');
		echo json_encode($log);
		exit;		
	}	
	
	$account_id = intval(is_param_post('id'));
	$item = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."user_accounts WHERE user_id = '$user_id' AND id='$account_id'");
	if(isset($item->id)){
		if($item->verify == 0){
			$verify_request = $wpdb->query("SELECT * FROM ". $wpdb->prefix ."uv_accounts WHERE usac_id='$account_id' AND status='0'");
			if($verify_request < 1){
				$valut_id = $item->valut_id;
				$has_verify = intval(get_valuts_meta($valut_id, 'has_verify'));
				if($has_verify == 1){
				
					$array = array();
					$array['createdate'] = current_time('mysql');
					$array['valut_id'] = $item->valut_id;
					$array['user_id'] = $user_id;
					$array['user_login'] = is_user($ui->user_login);
					$array['user_email'] = is_email($ui->user_email);
					$array['usac_id'] = $account_id;
					$array['accountnum'] = pn_strip_input($item->accountnum);
					$array['theip'] = pn_real_ip();
					$array['locale'] = pn_strip_input(get_locale());
					$array['status'] = 0;
					$wpdb->insert($wpdb->prefix.'uv_accounts', $array);	

					$mailtemp = get_option('mailtemp');
					if(isset($mailtemp['userverify2'])){									
						$data = $mailtemp['userverify2'];
						if($data['send'] == 1){
							
							$ot_mail = is_email($data['mail']);
							$ot_name = pn_strip_input($data['name']);
							$sitename = pn_strip_input(get_bloginfo('sitename'));
							$subject = pn_strip_input(ctv_ml($data['title']));
							
							$html = pn_strip_text(ctv_ml($data['text']));
							
							if($data['tomail']){
								$to_mail = $data['tomail'];
							
								$subject = str_replace('[sitename]', $sitename ,$subject);
								$subject = str_replace('[user_login]', is_user($ui->user_login),$subject);
								$subject = str_replace('[purse]', $array['accountnum'] ,$subject);
								$subject = apply_filters('mail_userverify2_subject',$subject);
								
								$html = str_replace('[sitename]', $sitename,$html);
								$html = str_replace('[user_login]', is_user($ui->user_login),$html);
								$html = str_replace('[purse]', $array['accountnum'] ,$html);
								$html = apply_filters('mail_userverify2_text',$html);
								$html = apply_filters('comment_text',$html);
								
								pn_mail($to_mail, $subject, $html, $ot_name, $ot_mail);	 
							}
						}																								
					}				
			
					$log['status'] = 'success';

				} else {
					$log['status'] = 'error';
					$log['status_code'] = 1;
					$log['status_text'] = __('Error! Currency does not exist or disabled','pn');					
				}
			} else {
				$log['status'] = 'success';
			}
		} else {
			$log['status'] = 'success';			
		}	
	} else {
		$log['status'] = 'error';
		$log['status_code'] = 1;
		$log['status_text'] = __('Error! Currency does not exist or disabled','pn');		
	}
	
	echo json_encode($log);
	exit;
} 

add_action('myaction_site_accountverify_upload', 'def_myaction_ajax_accountverify_upload');
function def_myaction_ajax_accountverify_upload(){
global $or_site_url, $wpdb, $premiumbox;	
	
	only_post();
	
	$log = array();
	$log['response'] = '';
	$log['status'] = '';
	$log['status_code'] = 0;
	$log['status_text'] = '';	
	
	$premiumbox->up_mode();
	
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);	
	
	if(!$user_id){
		$log['status'] = 'error'; 
		$log['status_code'] = 1;
		$log['status_text']= __('Error! You must authorize','pn');
		echo json_encode($log);
		exit;		
	}
	
	if($premiumbox->get_option('usve','acc_status') != 1){
		$log['status'] = 'error'; 
		$log['status_code'] = 1;
		$log['status_text']= __('Error! Verification is disabled','pn');
		echo json_encode($log);
		exit;		
	}	
				
	$account_id = intval(is_param_post('account_id'));
	if($account_id < 1){ $account_id = 0; } 
	
	$item = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."user_accounts WHERE user_id = '$user_id' AND id='$account_id'");
	if(isset($item->id)){
		if($item->verify == 0){
			$verify_request = $wpdb->query("SELECT * FROM ". $wpdb->prefix ."uv_accounts WHERE usac_id='$account_id' AND status='0'");
			if($verify_request < 1){
				$valut_id = $item->valut_id;
				$has_verify = intval(get_valuts_meta($valut_id, 'has_verify'));
				if($has_verify == 1){
					$verify_files = intval(get_valuts_meta($valut_id, 'verify_files'));
					if($verify_files > 0){
						$count_files = $wpdb->query("SELECT * FROM ". $wpdb->prefix ."uv_accounts_files WHERE uv_id='$account_id'");
						if($count_files < $verify_files){
							$countfile = count($_FILES['file']['name']);
							if($countfile > 0){
								$ext = pn_mime_filetype($_FILES['file']);
								$tempFile = $_FILES['file']['tmp_name'];
						
								$max_mb = pn_max_upload();
								$max_upload_size = $max_mb * 1024 * 1024;
								$fileupform = pn_enable_filetype();					
						
								if(in_array($ext, $fileupform)){
									if($_FILES["file"]["size"] > 0 and $_FILES["file"]["size"] < $max_upload_size){
								
										$filename = time().'_'.delsimbol(replace_cyr($_FILES['file']['name']));				
						
										$my_dir = wp_upload_dir();
										$path = $my_dir['basedir'].'/';
										$path2 = $my_dir['basedir'].'/accountverify/';
										$path3 = $my_dir['basedir'].'/accountverify/'. $account_id .'/';
										if(!is_dir($path)){ 
											@mkdir($path , 0777);
										}
										if(!is_dir($path2)){ 
											@mkdir($path2 , 0777);
										}	
										if(!is_dir($path3)){ 
											@mkdir($path3 , 0777);
										}	

										$htacces = $path2.'.htaccess';
										if(!is_file($htacces)){
											$nhtaccess = "Order allow,deny \n Deny from all";
											$file_open = @fopen($htacces, 'w');
											@fwrite($file_open, $nhtaccess);
											@fclose($file_open);		
										}							

										$targetFile =  str_replace('//','/',$path3) . $filename;
										$result = move_uploaded_file($tempFile,$targetFile);
										if($result){
								
											$arr = array();
											$arr['user_id'] = $user_id;
											$arr['uv_data'] = $filename;
											$arr['uv_id'] = $account_id;							
											$wpdb->insert($wpdb->prefix.'uv_accounts_files', $arr);
									
											$log['status'] = 'success';
											$log['response'] = get_usac_files($account_id);
								
										} else {
											$log['status'] = 'error';
											$log['status_code'] = 1;
											$log['status_text'] = __('Error! Error loading file','pn');
										}
									} else {
										$log['status'] = 'error';
										$log['status_code'] = 1;
										$log['status_text'] = __('Max.','pn').' '. $max_mb .' '. __('MB','pn') .'!';			
									}
								} else {
									$log['status'] = 'error';
									$log['status_code'] = 1;
									$log['status_text'] = __('Error! Incorrect file format','pn');					
								}
							} else {
								$log['status'] = 'error';
								$log['status_code'] = 1;
								$log['status_text'] = __('Error! Error loading file','pn');
							}
						} else {
							$log['status'] = 'error';
							$log['status_code'] = 1;
							$log['status_text'] = sprintf(__('Error! Maximum number of files for upload: %s','pn'), $verify_files);							
						}
					} else {
						$log['status'] = 'error';
						$log['status_code'] = 1;
						$log['status_text'] = __('Error! Currency does not exist or disabled','pn');					
					}		
				} else {
					$log['status'] = 'error';
					$log['status_code'] = 1;
					$log['status_text'] = __('Error! Currency does not exist or disabled','pn');					
				}		
			} else {
				$log['status'] = 'success';
				$log['response'] = get_usac_files($account_id);
			}		
		} else {
			$log['status'] = 'success';
			$log['response'] = get_usac_files($account_id);
		}
	} else {
		$log['status'] = 'error';
		$log['status_code'] = 1;
		$log['status_text'] = __('Error! Currency does not exist or disabled','pn');		
	}				
	echo json_encode($log);
	exit;
}

add_action('myaction_site_delete_accverify', 'def_myaction_ajax_delete_accverify');
function def_myaction_ajax_delete_accverify(){
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
	$data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."uv_accounts_files WHERE id='$id'");
	if(isset($data->id)){
		$dostup = 0;
		if($data->user_id == $user_id or current_user_can('administrator') or current_user_can('pn_accountverify')){
			$dostup = 1;
		}		
		if($dostup == 1){
			$wpdb->query("DELETE FROM ".$wpdb->prefix."uv_accounts_files WHERE id='$id'");

			$my_dir = wp_upload_dir();
			$file = $my_dir['basedir'].'/accountverify/'. $data->uv_id .'/'. pn_strip_input($data->uv_data);
			if(is_file($file)){
				@unlink($file);
			}
			
			$log['status'] = 'success';
		} else {
			$log['status'] = 'error';
			$log['status_code'] = 1;
			$log['status_text'] = __('Error! File does not exist','pn');			
		}
	} else {
		$log['status'] = 'error';
		$log['status_code'] = 1;
		$log['status_text'] = __('Error! File does not exist','pn');		
	}
	
	echo json_encode($log);
	exit;
}
/* end сайт */