<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_pn_add_usve', 'pn_admin_title_pn_add_usve');
function pn_admin_title_pn_add_usve(){
	$id = intval(is_param_get('item_id'));
	if($id){
		_e('Edit verification','pn');
	} else {
		_e('Add verification','pn');
	}
}

add_action('pn_adminpage_content_pn_add_usve','def_pn_admin_content_pn_add_usve');
function def_pn_admin_content_pn_add_usve(){
global $wpdb;

	$id = intval(is_param_get('item_id'));
	$data_id = 0;
	$data = '';
	
	if($id){
		$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."userverify WHERE id='$id'");
		if(isset($data->id)){
			$data_id = $data->id;
		}	
	}

	if($data_id){
		$title = __('Edit verification','pn');
	} else {
		$title = __('Add verification','pn');
		$array = array();
		$array['createdate'] = current_time('mysql');
		$array['status'] = 0;	
		$array['locale'] = get_locale();	
		$wpdb->insert($wpdb->prefix.'userverify', $array);
		$data_id = $wpdb->insert_id;
		if($data_id){
			$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."userverify WHERE id='$data_id'");
		}		
	}
	
	$back_menu = array();
	$back_menu['back'] = array(
		'link' => admin_url('admin.php?page=pn_usve'),
		'title' => __('Back to list','pn')
	);
	if($data_id){
		$back_menu['add'] = array(
			'link' => admin_url('admin.php?page=pn_add_usve'),
			'title' => __('Add new','pn')
		);	
		if($data->status != 0){
			$back_menu['approve'] = array(
				'link' => pn_link_post('enable_userverify') .'&id='. $data_id,
				'title' => __('Approve','pn')
			);
		}
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
	
	$users = array();
	$blogusers = get_users();
	foreach($blogusers as $us){
		$users[$us->ID] = is_user($us->user_login);
	}
	$options['user_id'] = array(
		'view' => 'select',
		'title' => __('User','pn'),
		'options' => $users,
		'default' => is_isset($data, 'user_id'),
		'name' => 'user_id',
		'work' => 'int',
	);	
	if($data->status != 0){
		$options['user_id']['view'] = 'select_disabled';
	} 	

	$txtfields = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."uv_field WHERE fieldvid = '0' AND status = '1' ORDER BY uv_order ASC");
	foreach($txtfields as $txtfield){
		$id = $txtfield->id;
		$field = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."uv_field_user WHERE uv_field='$id' AND uv_id='$data_id'");
		
		$options[] = array(
			'view' => 'inputbig',
			'title' => pn_strip_input(ctv_ml($txtfield->title)),
			'default' => is_isset($field, 'uv_data'),
			'name' => 'uv'. $txtfield->id,
		);	
	}	
	$options['status'] = array(
		'view' => 'select_disabled',
		'title' => __('Status','pn'),
		'options' => array('1'=>__('Awaiting request','pn'),'2'=>__('Confirmed request','pn'),'3'=>__('Request is declined','pn')),
		'default' => is_isset($data, 'status'),
		'name' => 'status',
	);		
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('pn_userverify_addform', $options, $data);

	$max_mb = pn_max_upload();
	$max_upload_size = $max_mb * 1024 * 1024;
	$fileupform = pn_enable_filetype();
 	?>
	<div class="premium_single">
	<?php
	$fields = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."uv_field WHERE fieldvid = '1' AND status = '1' ORDER BY uv_order ASC");
	foreach($fields as $field){				
		$temp = '<form action="'. pn_link_post('userverify_upload') .'" class="usveajaxform" enctype="multipart/form-data" method="post">
			<input type="hidden" name="theid" value="'. $field->id .'" />
			<input type="hidden" name="id" value="'. $data_id .'" />
		';
									
		$thetitle = pn_strip_input(ctv_ml($field->title));
									
		$req_txt = '';
		if($field->uv_req == 1){
			$req_txt = '<span class="req">*</span>';
		}								
									
			$temp .= '
			<div class="premium_standart_table">
				<table>
					<tr>
						<th></th>
						<td>
							<div class="usvelabeldown">'. $thetitle .' '. $req_txt .'</div>
							<div class="usvelabeldownsyst">('. strtoupper(join(', ',$fileupform)) .', '. __('max.','pn') .' '. $max_mb .''. __('MB','pn') .')</div>
													
							<div class="usveupfile">
								<input type="file" class="usveupfilesome" name="file" value="" />
							</div>
													
							<div class="usveupfileres">'. get_usvedoc_temp($data_id, $field->id) .'</div>
						</td>
					</tr>
				</table>
			</div>
			';
						
		$temp .= '</form>';
		echo $temp;		
	}	
	?>
		<div id="usveformedres"></div>
	</div>
	<div class="premium_body">
		<h3 style="padding: 0; margin: 0;"><?php _e('Failure reason','pn'); ?></h3>
				
		<form method="post" action="<?php pn_the_link_post('disable_userverify'); ?>&id=<?php echo $data_id; ?>">
			<p><textarea name="textstatus" style="width: 100%; height: 100px;"><?php echo pn_strip_input(is_isset($data, 'comment')); ?></textarea></p>
			<input type="submit" name="submit" class="button" value="<?php _e('Decline verification','pn'); ?>" />
		</form>	
	</div>	  	
<script type="text/javascript">
jQuery(function($){ 

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
				$('#usveformedres').html('<div class="premium_reply theerror">'+ res['status_text'] + '</div>');
				thet.find('.usveupfilesome').attr('value','');
		    }
			if(res['response']){
				thet.find('.usveupfileres').html(res['response']); 
			}			
			thet.find('input').prop('disabled', false);
        }
    });
	
});	
</script>
	<?php
}  

add_action('premium_action_userverify_upload','def_premium_action_userverify_upload');
function def_premium_action_userverify_upload(){
global $or_site_url, $wpdb, $premiumbox;	
	
	only_post();
	
	$log = array();
	$log['response'] = '';
	$log['status'] = '';
	$log['status_code'] = 0;
	$log['status_text'] = '';	
	
	if(!current_user_can('administrator') and !current_user_can('pn_userverify')){
		$log['status'] = 'error'; 
		$log['status_code'] = 1;
		$log['status_text']= __('Error! insufficient privileges!','pn');
		echo json_encode($log);
		exit;		
	}
			
	$id = intval(is_param_post('id'));
	if($id < 1){ $id = 0; } /* id заявки */
	
	$theid = intval(is_param_post('theid'));
	if($theid < 1){ $theid = 0; }	/* id поля */
	
	if($id){
		$locale = get_locale();
		$field_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."uv_field WHERE fieldvid = '1' AND status = '1' AND id='$theid'");
		if(!isset($field_data->id)){
			$log['status'] = '';
			$log['status_code'] = 1; 
			$log['status_text'] = __('Error! Error loading file','pn');			
			echo json_encode($log);
			exit;	
		}		
		
		$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."userverify WHERE id='$id'");
		if(isset($data->id)){
			$data_id = $data->id;
			$user_id = $data->user_id;
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
							
							$wpdb->query("UPDATE ".$wpdb->prefix."uv_field_user SET user_id = '$user_id' WHERE uv_id = '$data_id'");
								
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
			$log['status'] = 'error';
			$log['status_code'] = 1;
			$log['status_text'] = __('Error! Error loading file','pn');
		}
	} 
	
	echo json_encode($log);
	exit;	
}
	
add_action('premium_action_pn_add_usve','def_premium_action_pn_add_usve');
function def_premium_action_pn_add_usve(){	
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_userverify'));
	
	$data_id = intval(is_param_post('data_id'));
	$last_data = '';
	if($data_id > 0){
		$last_data = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "userverify WHERE id='$data_id'");
		if(!isset($last_data->id)){
			$data_id = 0;
		}
	}		
	$user_id = intval(is_isset($last_data,'user_id'));
	
	$array = array();
	
	if(isset($last_data->status)){
		if($last_data->status == 0){
			$user_id = intval(is_param_post('user_id'));
			$array['createdate'] = current_time('mysql');
			$array['user_id'] = $user_id;
			$ui = get_userdata($user_id);
			if(isset($ui->ID)){
				$array['user_login'] = is_user($ui->user_login);
				$array['user_email'] = is_email($ui->user_email);
			} else {
				pn_display_mess(__('Error! You did not choose the user','pn'));
			}			
			$array['status'] = 1;
			
			$cc = $wpdb->query("SELECT id FROM ".$wpdb->prefix."userverify WHERE user_id = '$user_id' AND status IN('1','2') AND id != '$data_id'");		
			if($cc > 0){
				pn_display_mess(__('Error! This user already has a verification order','pn'));
			}			
		}	
	}	
	
	$array['theip'] = pn_real_ip();	
	
	if($data_id){		
		$wpdb->update($wpdb->prefix.'userverify', $array, array('id'=>$data_id));	
	} else {
		$wpdb->insert($wpdb->prefix.'userverify', $array);
		$data_id = $wpdb->insert_id;	
	}
	
	if($data_id){
		$fields = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."uv_field WHERE fieldvid = '0' AND status = '1' ORDER BY uv_order ASC");
		foreach($fields as $field){
			$field_id = $field->id;
			$title_field = pn_strip_input(ctv_ml($field->title));
			$uv_req = intval($field->uv_req);
						
			$value = apply_filters('uv_strip_filed_value', is_param_post( 'uv' . $field->id ), $field->uv_auto);
						
			$us_data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."uv_field_user WHERE uv_id='$data_id' AND uv_field='$field_id'");
			$arr = array();
			$arr['user_id'] = $user_id;
			$arr['uv_data'] = $value;
			$arr['uv_id'] = $data_id;
			$arr['uv_field'] = $field_id;
						
			if(isset($us_data->id)){
				$wpdb->update($wpdb->prefix.'uv_field_user', $arr, array('id'=>$us_data->id)); 
			} else {
				$wpdb->insert($wpdb->prefix.'uv_field_user', $arr);
			}
			
			$wpdb->query("UPDATE ".$wpdb->prefix."uv_field_user SET user_id = '$user_id' WHERE uv_id = '$data_id'");
						
			if($uv_req == 1 and !$value){				
				pn_display_mess(sprintf(__('Error! You have not entered "%s"','pn'), $title_field));		
			}
		}			
	}

	$url = admin_url('admin.php?page=pn_add_usve&item_id='. $data_id .'&reply=true');
	wp_redirect($url);
	exit;
}	