<?php
if( !defined( 'ABSPATH')){ exit(); }

/* добавляем JS */ 
add_action('siteplace_js','siteplace_js_userverify');
function siteplace_js_userverify(){
global $user_ID, $premiumbox;	
	
	if($user_ID and $premiumbox->get_option('usve','status') == 1){
		
		$max_mb = pn_max_upload();
		$max_upload_size = $max_mb * 1024 * 1024;
?>	
jQuery(function($){ 

	$(document).on('click', '#go_usve', function(){
		$('#usveformed').submit();
	});

    $('#usveformed').ajaxForm({
	    dataType:  'json',
        beforeSubmit: function(a,f,o) {
			$('#go_usve').prop('disabled',true);			
        },
		error: function(res, res2, res3) {
			<?php do_action('pn_js_error_response', 'ajax'); ?>
		},		
        success: function(res) {
            if(res['status'] == 'success'){
				$('#usveformedres').html('<div class="resulttrue"><div class="resultclose"></div>'+ res['status_text'] + '</div>');
		    } 
			if(res['status'] == 'error'){
				$('#usveformedres').html('<div class="resultfalse"><div class="resultclose"></div>'+ res['status_text'] + '</div>');
		    } 	
			
			if(res['url']){
				window.location.href = res['url']; 
			}			
			
			$('#go_usve').prop('disabled',false);
        }
    });	
	
	$(document).on('change', '.usveupfilesome', function(){
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
	
	var thet = '';
    $('.usveajaxform').ajaxForm({
	    dataType:  'json',
        beforeSubmit: function(a,f,o) {
		    thet = f;		
			$('#usveformedres').html(' ');
			thet.find('input').prop('disabled',true);
        },
		error: function(res, res2, res3) {
			<?php do_action('pn_js_error_response', 'form'); ?>
		},		
        success: function(res) { 
			if(res['status']== 'error'){
				$('#usveformedres').html('<div class="resultfalse"><div class="resultclose"></div>'+ res['status_text'] + '</div>');
				thet.find('.usveupfilesome').attr('value','');
		    }
			if(res['response']){
				thet.find('.usveupfileres').html(res['response']); 
			}			
			if(res['url']){
				window.location.href = res['url']; 
			}
			thet.find('input').prop('disabled', false);
        }
    });
	
});		
<?php	
	}
} 
/* end добавляем JS */

function usve_userverify_shortcode($atts, $content){ 
global $wpdb, $premiumbox;

	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);

	$temp = '';
	$temp .= apply_filters('before_userverify_page','');
	
	if($premiumbox->get_option('usve','status') == 1){ 
		if($user_id){
			
			if(isset($ui->user_verify) and $ui->user_verify == 0){ /* если не верифицирован и нет заявки в ожидании или на верификации */
				
				$cc = $wpdb->query("SELECT id FROM ".$wpdb->prefix."userverify WHERE user_id = '$user_id' AND status IN('1','2')");
				if($cc == 0){
					
					$verify_text = trim(ctv_ml($premiumbox->get_option('usve','text')));
					if($verify_text){
						
						$temp .= '
						<div class="userverify_text">
							<div class="userverify_text_ins">
								<div class="userverify_text_abs"></div>
								<div class="text">
									'. apply_filters('the_content',$verify_text) .'
										<div class="clear"></div>
								</div>
							</div>
						</div>
						';
						
					}
					
					$temp .= '
					<div class="userverify_div">
						<div class="userverify_div_ins">';
					
						if(is_older_browser()){
						
							$temp .= '<div class="resultfalse">'. __('Error! You are using an old version of your browser!','pn') .'</div>';
						
						} else {
					
							$locale = get_locale();
					
							$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."userverify WHERE user_id = '$user_id' AND status = '0'");
								
							$array = array();
							$array['createdate'] = current_time('mysql');
							$array['status'] = 0;
							$array['user_id'] = $user_id;
							$array['user_login'] = is_user($ui->user_login);
							$array['user_email'] = is_email($ui->user_email);
							$array['locale'] = $locale;						
							
							if(isset($data->id)){
								$id = $data->id;
								$wpdb->update($wpdb->prefix.'userverify', $array, array('id'=>$id));					
							} else {
								$wpdb->insert($wpdb->prefix.'userverify', $array);
								$id = $wpdb->insert_id;
							}
						
							$temp .= '
							<form action="'. get_ajax_link('userverify_created') .'" id="usveformed" method="post">
								<input type="hidden" name="id" value="'. $id .'" />
							';
							
								$before_userverify_textform = '
								<div class="oneusvebody">				
								';
								$temp .= apply_filters('before_userverify_textform',$before_userverify_textform);
						
								$txtfields = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."uv_field WHERE fieldvid = '0' AND status = '1' AND locale IN('0','$locale') ORDER BY uv_order ASC");
								foreach($txtfields as $txtfield){
						
									$thetitle = pn_strip_input(ctv_ml($txtfield->title));
						
									$req_txt = '';
									if($txtfield->uv_req == 1){
										$req_txt = '<span class="req">*</span>';
									}
									$txtvalue = apply_filters('uv_auto_filed_value', '', $txtfield->uv_auto, $ui);
						
									$tooltip = pn_strip_input(ctv_ml($txtfield->helps));
						
									$tooltip_div = '';
									$tooltip_span = '';
									$tooltip_class = '';
									if($tooltip){
										$tooltip_span = '<span class="field_tooltip_label"></span>';
										$tooltip_class = 'has_tooltip';
										$tooltip_div = '<div class="field_tooltip_div"><div class="field_tooltip_abs"></div><div class="field_tooltip">'. $tooltip .'</div></div>';
									}
						
									$text_line = '
									<div class="ustbl_line '. $tooltip_class .'">
										<div class="ustbl_th"><label for="uv'. $txtfield->id .'"><span class="usve_label">'. $thetitle .' '. $req_txt .': '. $tooltip_span .'</span></label></div>
										<div class="ustbl_td"><input type="text" id="uv'. $txtfield->id .'" name="uv'. $txtfield->id .'" value="'. $txtvalue .'" /></div>
											<div class="ustbl_clear"></div>
										'. $tooltip_div .'
									</div>	
									';
									$temp .= apply_filters('userverify_textform_line', $text_line, $txtfield, $txtvalue);

								}

								$after_userverify_textform = '
								</div>					
								';
								$temp .= apply_filters('after_userverify_textform',$after_userverify_textform);					
							
							$temp .= '</form>';
		
							$max_mb = pn_max_upload();
							$fileupform = pn_enable_filetype();
						
								$fields = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."uv_field WHERE fieldvid = '1' AND status = '1' AND locale IN('0','$locale') ORDER BY uv_order ASC");
								foreach($fields as $field){
								
									$temp .= '
									<form action="'. get_ajax_link('userverify_upload') .'" class="usveajaxform" enctype="multipart/form-data" method="post">
										<input type="hidden" name="theid" value="'. $field->id .'" />
										<input type="hidden" name="id" value="'. $id .'" />
									';
									
									$thetitle = pn_strip_input(ctv_ml($field->title));
									
									$req_txt = '';
									if($field->uv_req == 1){
										$req_txt = '<span class="req">*</span>';
									}				

									$tooltip = pn_strip_input(ctv_ml($field->helps));
									
									$file_line = '
									<div class="oneusvebody">
										<div class="ustbl_line">
											<div class="ustbl_th">'. $thetitle .' '. $req_txt .'</div>
											<div class="ustbl_td">
												<div class="usvelabeldown">'. $tooltip .'</div>
												<div class="usvelabeldownsyst">('. strtoupper(join(', ',$fileupform)) .', '. __('max.','pn') .' '. $max_mb .''. __('MB','pn') .')</div>
													
												<div class="usveupfile">
													<input type="file" class="usveupfilesome" name="file" value="" />
												</div>
													
												<div class="usveupfileres">'. get_usvedoc_temp($id, $field->id) .'</div>
											</div>
											<div class="ustbl_clear"></div>
										</div>	
									</div>
									';
									
									$temp .= apply_filters('userverify_fileform_line', $file_line, $field);
								
									$temp .= '</form>';
								}		
						
							$submit_userverify_form = '
							<div class="oneusvebody">
								<div class="ustbl_line">
									<div class="ustbl_th"></div>
									<div class="ustbl_td">
										<input type="submit" name="submit" formtarget="_top" id="go_usve" value="'. __('Send a request','pn') .'" />
									</div>
										<div class="ustbl_clear"></div>
								</div>
							</div>				
							';
							$temp .= apply_filters('submit_userverify_form',$submit_userverify_form);								
			
						}
			
					$temp .= '
							<div id="usveformedres"></div>
						</div>
					</div>';
					
				}
			}
		
			$lists = array(
				'before' => '<table>',
				'after' => '</table>',
				'before_head' => '<thead><tr>',
				'after_head' => '</tr></thead>',
				'head_line' => '<th class="th_[key]">[title]</th>',
				'before_body' => '<tbody>',
				'after_body' => '</tbody>',
				'body_line' => '<tr>[html]</tr>',
				'body_item' => '<td class="td_[key] [odd_even]">[content]</td>',
				'lists' => array(
					'date' => __('Date','pn'),
					'status' => __('Status','pn'),
				),
				'noitem' => '<tr><td colspan="[count]"><div class="no_items"><div class="no_items_ins">[title]</div></div></td></tr>',
			);
			$lists = apply_filters('lists_userverify', $lists);
			$lists = (array)$lists;		
		
			$head_list = '';
			$c = 0;
			if(is_array($lists['lists'])){
				foreach($lists['lists'] as $key => $title){
					$c++;
					$list = is_isset($lists, 'head_line');
					$list = str_replace('[key]',$key,$list);
					$list = str_replace('[title]',$title,$list);
					$head_list .= $list;
				}
			}
			
			$table_list = is_isset($lists, 'before');
			$table_list .= is_isset($lists, 'before_head');
			$table_list .= $head_list;
			$table_list .= is_isset($lists, 'after_head');
			$table_list .= is_isset($lists, 'before_body');		
		
			$datas = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."userverify WHERE user_id = '$user_id' AND status != '0' ORDER BY id DESC");
			if(count($datas) > 0){
					
				$date_format = get_option('date_format');
				$time_format = get_option('time_format');
				
				$s=0;
				foreach ($datas as $item) {  $s++;
					if($s%2==0){ $odd_even = 'even'; } else { $odd_even = 'odd'; }
					
					$one_line = '';
					if(is_array($lists['lists'])){
						foreach($lists['lists'] as $key => $title){
							
							$data_item = '';							
							if($key == 'date'){
								$data_item = get_mytime($item->createdate, "{$date_format}, {$time_format}");
							}
							if($key == 'status'){
								if($item->status == 1){
									$status = '<strong>'. __('Awaiting request','pn') .'</strong>';
								} elseif($item->status == 2){
									$status = '<span class="bgreen">'. __('Confirmed request','pn') .'</span>';
								} elseif($item->status == 3){
									$status = '<span class="bred">'. __('Request is declined','pn') .'</span>';
								} else {
									$status = '<strong>'. __('automatic','pn') .'</strong>';
								}
								$data_item = $status;
							}
							$data_item = apply_filters('body_list_userverify', $data_item, $item, $key, $title, $date_format, $time_format);
							
							if($data_item){
								$list = is_isset($lists, 'body_item');
								$list = str_replace('[key]',$key,$list);
								$list = str_replace('[title]',$title,$list);
								$list = str_replace('[content]',$data_item,$list);
								$one_line .= $list;
							}
							
						}
					}
					
					$body_list_line = is_isset($lists, 'body_line');
					$body_list_line = str_replace('[html]',$one_line,$body_list_line);
					$body_list_line = str_replace('[odd_even]',$odd_even,$body_list_line);
					$table_list .= $body_list_line;						
					
				}		
				
			} else {
				$list = is_isset($lists, 'noitem');
				$list = str_replace('[count]', $c,$list);
				$list = str_replace('[title]',__('No data','pn'),$list);
				$table_list .= $list;
			}

			$table_list .= is_isset($lists, 'after_body');
			$table_list .= is_isset($lists, 'after');				
				
			$array = array(
				'[table_list]' => $table_list,
			);					
				
			$temp_form = '
			<div class="userverify_table_div">	
				<div class="userverify_table_div_ins">
					
					<div class="userverify_table_title">
						<div class="userverify_table_title_ins">
							<div class="userverify_table_title_abs"></div>
							'. __('Requests for verification','pn') .':
						</div>
					</div>
						<div class="clear"></div>
						
					<div class="userverify_table">	 
						<div class="userverify_table_ins">
					
						[table_list]
							
						</div>
					</div>
				
				</div>
			</div>			
			';
		
			$temp_form = apply_filters('userverify_form_temp',$temp_form);
			$temp .= get_replace_arrays($array, $temp_form);				
				
		} else {
			$temp .= '<div class="resultfalse">'. __('Error! You must authorize','pn') .'</div>';
		}
	} else {
		$temp .= '<div class="resultfalse">'. __('Error! Page is unavailable','pn') .'</div>';
	}

	$temp .= apply_filters('after_userverify_page','');
	return $temp;
}
add_shortcode('userverify', 'usve_userverify_shortcode');
 
add_action('myaction_site_userverify_created', 'def_myaction_ajax_userverify_created');
function def_myaction_ajax_userverify_created(){
global $wpdb, $premiumbox;	
	
	only_post();
	
	$log = array();	
	$log['response'] = '';
	$log['status'] = '';
	$log['status_code'] = 0;
	$log['status_text'] = '';	
	
	$premiumbox->up_mode();
	
	$log = apply_filters('before_ajax_form_field', $log, 'userverify_createdform');
	$log = apply_filters('before_ajax_userverify_createdform', $log);
	
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);	
	
	if(!$user_id){
		$log['status'] = 'error'; 
		$log['status_code'] = 1;
		$log['status_text']= __('Error! You must authorize','pn');
		echo json_encode($log);
		exit;		
	}
	
	if($premiumbox->get_option('usve','status') != 1){
		$log['status'] = 'error'; 
		$log['status_code'] = 1;
		$log['status_text']= __('Error! You must authorize','pn');
		echo json_encode($log);
		exit;		
	}
		
	$userverify_url = apply_filters('userverify_redirect', $premiumbox->get_page('userverify'));	
		
	$id = intval(is_param_post('id'));
	if($id < 1){ $id = 0; }
	if($id and $user_id){ 
		if(isset($ui->user_verify) and $ui->user_verify == 0){
			$cc = $wpdb->query("SELECT id FROM ".$wpdb->prefix."userverify WHERE user_id = '$user_id' AND status IN('1','2')");
			if($cc == 0){
				$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."userverify WHERE user_id = '$user_id' AND status = '0' AND id='$id'");
				if(isset($data->id)){

					$locale = get_locale();
				
					$fields = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."uv_field WHERE fieldvid = '0' AND status = '1' AND locale IN('0','$locale') ORDER BY uv_order ASC");
					foreach($fields as $field){
						$field_id = $field->id;
						$title_field = pn_strip_input(ctv_ml($field->title));
						$uv_req = intval($field->uv_req);
						
						$value = apply_filters('uv_strip_filed_value', is_param_post( 'uv' . $field->id ), $field->uv_auto);
						
						$us_data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."uv_field_user WHERE uv_id='$id' AND uv_field='$field_id'");
						$arr = array();
						$arr['user_id'] = $user_id;
						$arr['uv_data'] = $value;
						$arr['uv_id'] = $id;
						$arr['uv_field'] = $field_id;
						
						if(isset($us_data->id)){
							$wpdb->update($wpdb->prefix.'uv_field_user', $arr, array('id'=>$us_data->id)); 
						} else {
							$wpdb->insert($wpdb->prefix.'uv_field_user', $arr);
						}
						
						if($uv_req == 1 and !$value){	
							$log['status'] = 'error';
							$log['status_code'] = 1;
							$log['status_text'] = sprintf(__('Error! You have not entered "%s"','pn'), $title_field);		
							echo json_encode($log);
							exit;
						}
					}				
				
					$fields = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."uv_field WHERE fieldvid = '1' AND status = '1' AND locale IN('0','$locale') ORDER BY uv_order ASC");
					foreach($fields as $field){
						$field_id = $field->id;
						$title_field = pn_strip_input(ctv_ml($field->title));
						$uv_req = intval($field->uv_req);
						if($uv_req == 1){
							$us_data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."uv_field_user WHERE uv_id='$id' AND uv_field='$field_id'");
							if(!isset($us_data->uv_data) or !$us_data->uv_data){
								$log['status'] = 'error';
								$log['status_code'] = 1;
								$log['status_text'] = sprintf(__('Error! You have not uploaded %s','pn'), $title_field);
								echo json_encode($log);
								exit;
							}
						}
					}
				
						
						$array = array();
						$array['createdate'] = current_time('mysql');
						$array['user_id'] = $user_id;
						$array['user_login'] = is_user($ui->user_login);
						$array['user_email'] = is_email($ui->user_email);
						$array['theip'] = pn_real_ip();
						$array['status'] = 1;
						$wpdb->update($wpdb->prefix.'userverify', $array, array('id'=>$id));
								
						$mailtemp = get_option('mailtemp');
						if(isset($mailtemp['userverify1'])){						
							$data = $mailtemp['userverify1'];
							if($data['send'] == 1){								
								$ot_mail = is_email($data['mail']);
								$ot_name = pn_strip_input($data['name']);						
								$subject = pn_strip_input(ctv_ml($data['title']));
								$sitename = pn_strip_input(get_bloginfo('sitename'));
								$html = pn_strip_text(ctv_ml($data['text']));						
								if($data['tomail']){						
									$to_mail = $data['tomail'];
									$sarray = array(
										'[sitename]' => $sitename,
									);							
									$subject = get_replace_arrays($sarray, $subject);						
									$subject = apply_filters('mail_userverify1_subject',$subject);
							
									$html = get_replace_arrays($sarray, $html);
									$html = apply_filters('mail_userverify1_text',$html);
									$html = apply_filters('comment_text',$html);

									pn_mail($to_mail, $subject, $html, $ot_name, $ot_mail);	 
								}								
							}																						
						}
													
					$log['url'] = $userverify_url;							
					
				} else {
					$log['status_code'] = 1;
					$log['url'] = $userverify_url;
				}
			} else {
				$log['status_code'] = 1;
				$log['url'] = $userverify_url;
			}	
		} else {
			$log['status_code'] = 1;
			$log['url'] = $userverify_url;
		}	
	} else {
		$log['status_code'] = 1;		
		$log['url'] = $userverify_url;
	}		
				
	echo json_encode($log);
	exit;
}

add_action('myaction_site_userverify_upload', 'def_myaction_ajax_userverify_upload');
function def_myaction_ajax_userverify_upload(){
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
	
	if($premiumbox->get_option('usve','status') != 1){
		$log['status'] = 'error'; 
		$log['status_code'] = 1;
		$log['status_text']= __('Error! You must authorize','pn');
		echo json_encode($log);
		exit;		
	}	
				
	$id = intval(is_param_post('id'));
	if($id < 1){ $id = 0; } /* id заявки */
	
	$theid = intval(is_param_post('theid'));
	if($theid < 1){ $theid = 0; }	/* id поля */
	
	if($id){
		$locale = get_locale();
		
		$field_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."uv_field WHERE fieldvid = '1' AND status = '1' AND id='$theid' AND locale IN('0','$locale')");
		if(!isset($field_data->id)){
			$log['status'] = '';
			$log['status_code'] = 1; 
			$log['status_text'] = __('Error! Error loading file','pn');			
			echo json_encode($log);
			exit;	
		}		
		
		$userverify_url = apply_filters('userverify_redirect', $premiumbox->get_page('userverify'));
		
		if(isset($ui->user_verify) and $ui->user_verify == 0){

			$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."userverify WHERE user_id = '$user_id' AND status = '0' AND id='$id'");
			if(isset($data->id)){

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
							$path2 = $my_dir['basedir'].'/userverify/';
							$path3 = $my_dir['basedir'].'/userverify/'. $data->id .'/';
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
							
								$olddata = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."uv_field_user WHERE uv_id='$id' AND uv_field='$theid'");
								
								$arr = array();
								$arr['user_id'] = $user_id;
								$arr['uv_data'] = $filename;
								$arr['uv_id'] = $id;
								$arr['uv_field'] = $theid;							
								
								if(isset($olddata->id)){									
									if($olddata->uv_data){
										$file = $my_dir['basedir'].'/userverify/'. $data->id .'/'. $olddata->uv_data;
										if(is_file($file)) {
											@unlink($file);
										}										
									}
									
									$wpdb->update($wpdb->prefix.'uv_field_user', $arr, array('id'=>$olddata->id));									
								} else {									
									$wpdb->insert($wpdb->prefix.'uv_field_user', $arr);
								}
								
								$log['response'] = get_usvedoc_temp($id, $theid);
							
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
				$log['status_code'] = 1;				
				$log['url'] = $userverify_url;
			}
		} else {
			$log['status_code'] = 1;			
			$log['url'] = $userverify_url;
		}	
	} else {
		$log['status_code'] = 1;
		$log['url'] = $userverify_url;
	}				
	echo json_encode($log);
	exit;
}