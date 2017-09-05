<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_pn_export_currency', 'pn_admin_title_pn_export_currency');
function pn_admin_title_pn_export_currency($page){
	_e('Currency Export/Import','pn');
} 

/* настройки */
add_action('pn_adminpage_content_pn_export_currency','def_pn_admin_content_pn_export_currency');
function def_pn_admin_content_pn_export_currency(){
global $wpdb;
?>
<?php if(current_user_can('administrator') or current_user_can('pn_export_currency')){ ?>
<div class="premium_body">	
    <form method="post" target="_blank" action="<?php pn_the_link_post('export_currency'); ?>">
    <table class="premium_standart_table">
        <tr>
		    <th><?php _e('Select data','pn'); ?></th>
			<td>
			<div class="premium_wrap_standart">
				<div style="font-weight: 500;"><label><input type="checkbox" class="check_all" name="" value="1" /> <?php _e('Check all/Uncheck all','pn'); ?></label></div>
				<?php
				$array = array(
					'psys_title' => __('PS title','pn'),
					'vtype_title' => __('Currency code','pn'),
					'xml_value' => __('XML name','pn'),
					'lead_num' => __('Convert to','pn'),
					'valut_decimal' => __('Amount of Decimal places','pn'),
					'inday1' => __('Daily limit for Send','pn'),
					'inday2' => __('Daily limit for Receive','pn'),
					'inmon1' => __('Monthly limit for Send','pn'),
					'inmon2' => __('Monthly limit for Receive','pn'),					
					'txt1' => __('Field title "From Account"','pn'),
					'txt2' => __('Field title "Onto Account"','pn'),
					'show1' => __('Show field "From Account"','pn'),
					'show2' => __('Show filed "Onto Account"','pn'),
					//'helps' => __('Fill-in tips','pn'),
					'minzn' => __('Min. number of symbols','pn'),
					'maxzn' => __('Max. number of symbols','pn'),
					'firstzn' => __('First symbols','pn'),
					'valut_reserv' => __('Reserve','pn'),
					'pvivod' => __('Allow affiliate money withdrawal','pn'),
					'valut_status' => __('Status','pn'),
				);
				foreach($array as $key => $val){
				?>
					<div><label><input type="checkbox" name="data[]" class="check_once" value="<?php echo $key; ?>" /> <?php echo $val; ?></label></div>
				<?php } ?>
			</div>
			</td>
		</tr>		
        <tr>
		    <th></th>
			<td>
			<div class="premium_wrap_standart">
			    <input type="submit" name="" class="button" value="<?php _e('Download','pn'); ?>" />
			</div>
			</td>
		</tr>		
    </table>
	</form>	
</div>
<?php } ?>

<?php if(current_user_can('administrator') or current_user_can('pn_import_currency')){ ?>
<div class="premium_body">	
    <form method="post" target="_blank" action="<?php pn_the_link_post('import_currency'); ?>" enctype="multipart/form-data">
    <table class="premium_standart_table">
        <tr>
		    <th><?php _e('Import','pn'); ?></th>
			<td>
			<div class="premium_wrap_standart">
				<input type="file" name="importfile" value="" />
			</div>
			</td>
		</tr>		
        <tr>
		    <th></th>
			<td>
			<div class="premium_wrap_standart">
			    <input type="submit" name="" class="button" value="<?php _e('Upload','pn'); ?>" />
			</div>
			</td>
		</tr>		
    </table>
	</form>	
</div>
<?php } ?>

<script type="">
jQuery(function($){ 
	$('.check_all').on('change', function(){
		if($(this).prop('checked')){
			$('.check_once').prop('checked',true);
		} else {
			$('.check_once').prop('checked',false);
		}
	});	
});
</script>	
<?php
} 

/* обработка */
add_action('premium_action_export_currency','def_premium_action_export_currency');
function def_premium_action_export_currency(){
global $wpdb;	

	pn_only_caps(array('administrator','pn_export_currency'));			
	
		$my_dir = wp_upload_dir();
		$path = $my_dir['basedir'].'/';		
		
		$file = $path.'currencyexport-'. date('Y-m-d-H-i') .'.csv';           
		$fs=@fopen($file, 'w');
	
		$items = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."valuts ORDER BY id DESC");
	
		$data = is_param_post('data');
		
		$content = '';
		
			$array = array(
				'id' => __('Identifier','pn'), 
				'psys_title' => __('PS title','pn'),
				'vtype_title' => __('Currency code','pn'),
				'xml_value' => __('XML name','pn'),
				'lead_num' => __('Convert to','pn'),
				'valut_decimal' => __('Amount of Decimal places','pn'),
				'inday1' => __('Daily limit for Send','pn'),
				'inday2' => __('Daily limit for Receive','pn'),
				'inmon1' => __('Monthly limit for Send','pn'),
				'inmon2' => __('Monthly limit for Receive','pn'),					
				'txt1' => __('Field title "From Account"','pn'),
				'txt2' => __('Field title "Onto Account"','pn'),
				'show1' => __('Show field "From Account"','pn'),
				'show2' => __('Show filed "Onto Account"','pn'),
				//'helps' => __('Fill-in tips','pn'),
				'minzn' => __('Min. number of symbols','pn'),
				'maxzn' => __('Max. number of symbols','pn'),
				'firstzn' => __('First symbols','pn'),
				'valut_reserv' => __('Reserve','pn'),
				'pvivod' => __('Allow affiliate money withdrawal','pn'),
				'valut_status' => __('Status','pn'),
			);
			
		if(is_array($data)){
			
			$en = array();
			$csv_title = '';
			$csv_key = '';
			foreach($array as $k => $v){
				if(in_array($k, $data) or $k == 'id'){
					$en[] = $k;
					$csv_title .= '"'.get_cptgn($v).'";';
					$csv_key .= '"'.get_cptgn($k).'";';
				} 
			}	
			
			$content .= $csv_title."\n";
			$content .= $csv_key."\n";

			$qw_arr = array('show1','show2','pvivod','valut_status');
			$num_arr = array('inday1','inday2','valut_reserv','inmon1','inmon2');
			
			if(count($en) > 0){

				foreach($items as $item){
					$line = '';
					
					foreach($en as $key){
						$line .= '"';
						if(in_array($key,$qw_arr)){
							$line .= get_cptgn(get_exvar(is_isset($item,$key),array(__('no','pn'),__('yes','pn'))));
						} elseif(in_array($key,$num_arr)){
							$line .= rep_dot(is_isset($item,$key));
						} else {
							$line .= get_cptgn(rez_exp(ctv_ml(is_isset($item,$key))));
						}
						$line .= '";';
					}
					
					$line .= "\n";
					$content .= $line;
				}
			
			}
		}
		
		@fwrite($fs, $content);
		@fclose($fs);	
	
		if(is_file($file)) {
			if (ob_get_level()) {
				ob_end_clean();
			}
			header('Content-Type: text/html; charset=CP1251');
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename=' . basename($file));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			readfile($file);
			unlink($file);
			exit;
		} else {
			pn_display_mess(__('Error! Unable to create file!','pn'));
		}	
}

/* обработка */
add_action('premium_action_import_currency','def_premium_action_import_currency');
function def_premium_action_import_currency(){
global $wpdb;	

	pn_only_caps(array('administrator','pn_import_currency'));
	
	$premit_ext = array(".csv");
	if(isset($_FILES['importfile']['name'])){
		$ext = strtolower(strrchr($_FILES['importfile']['name'],"."));
		if(in_array($ext,$premit_ext)){
				
			$max_upload_size = wp_max_upload_size();
			if ( ! $max_upload_size ) {
				$max_upload_size = 0;
			}	
			$max_mb = ($max_upload_size / 1024 / 1024);
				
	        if($_FILES["importfile"]["size"] > 0 and $_FILES["importfile"]["size"] < $max_upload_size){
				$tempFile = $_FILES['importfile']['tmp_name'];
				$filename = delsimbol(time() . $_FILES['importfile']['name']);

				$my_dir = wp_upload_dir();
				$path = $my_dir['basedir'].'/';
				$path2 = $my_dir['basedir'].'/import/';
				if(!is_dir($path)){ 
					@mkdir($path , 0777);
				}
				if(!is_dir($path2)){ 
					@mkdir($path2 , 0777);
				}
					
				$targetFile =  str_replace('//','/',$path2) . $filename;
					
				if(move_uploaded_file($tempFile,$targetFile)){
					
					$error = 0;
						
					$array = array(
						'id' => __('Identifier','pn'),
						'psys_title' => __('PS title','pn'),
						'vtype_title' => __('Currency code','pn'),
						'xml_value' => __('XML name','pn'),
						'lead_num' => __('Convert to','pn'),
						'valut_decimal' => __('Amount of Decimal places','pn'),
						'inday1' => __('Daily limit for Send','pn'),
						'inday2' => __('Daily limit for Receive','pn'),
						'inmon1' => __('Monthly limit for Send','pn'),
						'inmon2' => __('Monthly limit for Receive','pn'),							
						'txt1' => __('Field title "From Account"','pn'),
						'txt2' => __('Field title "Onto Account"','pn'),
						'show1' => __('Show field "From Account"','pn'),
						'show2' => __('Show filed "Onto Account"','pn'),
						//'helps' => __('Fill-in tips','pn'),
						'minzn' => __('Min. number of symbols','pn'),
						'maxzn' => __('Max. number of symbols','pn'),
						'firstzn' => __('First symbols','pn'),
						'valut_reserv' => __('Reserve','pn'),
						'pvivod' => __('Allow affiliate money withdrawal','pn'),
						'valut_status' => __('Status','pn'),
					);
					
					$allow_key = array();
					foreach($array as $k => $v){
						$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."valuts LIKE '{$k}'");
						if ($query == 1){
							$allow_key[] = $k;
						}
					}
						
					$result = file_get_contents($targetFile);
					$lines = explode("\n",$result);
					if(count($lines) > 2){
							
						$file_map = array();
						$csv_keys = explode(';',is_isset($lines,1));
						foreach($csv_keys as $csv_k => $csv_v){
							$file_map[$csv_k] = rez_exp($csv_v);
						}
							
						$r = -1;
						
						$int_arr = array('id','valut_decimal','minzn','maxzn');
						$num_arr = array('inday1','inday2','lead_num','inmon1','inmon2');
						$input_arr = array('psys_title','vtype_title','txt1','txt2');
						$qw_arr = array('show1','show2','pvivod','valut_status');						
						
						foreach($lines as $line){ $r++;
							if($r > 1){	
								$line = get_tgncp(trim($line));
								if($line){
									$bd_array = array();
										
									$items = explode(';',$line);
									foreach($items as $item_key => $item){
										$item = rez_exp($item);
											
										$db_key = $file_map[$item_key];
										if(in_array($db_key, $allow_key)){
												
											if(in_array($db_key, $int_arr)){
												$bd_array[$db_key] = intval($item);
											} elseif(in_array($db_key, $input_arr)){
												$bd_array[$db_key] = pn_maxf_mb(pn_strip_input($item),500);
											} elseif(in_array($db_key, $num_arr)){	
												$bd_array[$db_key] = is_my_money($item);
											} elseif(in_array($db_key, $qw_arr)){
												$bd_array[$db_key] = intval(get_exvar(mb_strtolower($item), array(__('no','pn')=>'0',__('yes','pn')=>'1')));
											} elseif('firstzn' == $db_key){
												$item = is_firstzn_value($item);
												if($item){
													$bd_array[$db_key] = $item;
												}
											} elseif('xml_value' == $db_key){
												$item = is_xml_value($item);
												if($item){
													$bd_array[$db_key] = $item;
												}
											}
												
										}
									}	
											
									if(count($bd_array) > 0){
										$data_id = intval(is_isset($bd_array,'id'));
										if(isset($bd_array['id'])){
											unset($bd_array['id']);
										}											
											
										$locale = get_locale();
											
										if(isset($bd_array['psys_title']) and $bd_array['psys_title']){
												
											$now = $bd_array['psys_title'];
											if(is_ml()){
												$now_ml = '['. $locale .':]'. $now .'[:'. $locale .']';
											} else {
												$now_ml = $now;
											}
												
											$psys_data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."psys WHERE psys_title LIKE '%". $now_ml ."%' OR psys_title='$now'");
											if(isset($psys_data->id)){
												$bd_array['psys_id'] = $psys_data->id;
											} else {	
												$up_arr = array(
													'psys_title' => $bd_array['psys_title'],
												);
												$wpdb->insert($wpdb->prefix.'psys', $up_arr);
												$bd_array['psys_id'] = $wpdb->insert_id;
											}
										}
										
										if(isset($bd_array['vtype_title']) and $bd_array['vtype_title']){
											$now = $bd_array['vtype_title'];
											$vtype_data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."vtypes WHERE vtype_title = '$now'");
											if(isset($vtype_data->id)){
												$bd_array['vtype_id'] = $vtype_data->id;
											} else {	
												$up_arr = array(
													'vtype_title' => $bd_array['vtype_title'],
													'vncurs' => '1',
												);
												$wpdb->insert($wpdb->prefix.'vtypes', $up_arr);
												$bd_array['vtype_id'] = $wpdb->insert_id;
											}
										}

										$install = 1;
										if($data_id){
											$vd = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."valuts WHERE id='$data_id'");
											if(isset($vd->id)){
												$install = 0;
													
												if(isset($bd_array['psys_title'])){
													$bd_array['psys_title'] = replace_value_ml($vd->psys_title,$bd_array['psys_title'],$locale);
												}
												if(isset($bd_array['vtype_title'])){
													$bd_array['vtype_title'] = replace_value_ml($vd->vtype_title,$bd_array['vtype_title'],$locale);
												}
												if(isset($bd_array['txt1'])){
													$bd_array['txt1'] = replace_value_ml($vd->txt1,$bd_array['txt1'],$locale);
												}
												if(isset($bd_array['txt2'])){
													$bd_array['txt2'] = replace_value_ml($vd->txt2,$bd_array['txt2'],$locale);
												}
												/* 												
												if(isset($bd_array['helps'])){
													$bd_array['helps'] = replace_value_ml($vd->helps,$bd_array['helps'],$locale);
												} 
												*/													
													
												$wpdb->update($wpdb->prefix.'valuts', $bd_array, array('id'=>$data_id));
											}
										}																							
											
										if($install == 1){
											if(isset($bd_array['psys_title']) and isset($bd_array['vtype_title']) and isset($bd_array['xml_value'])){
												$wpdb->insert($wpdb->prefix.'valuts', $bd_array);
											}
										}
									}
								}	
							}
						}								
					} 
						
					if($error == 0){
						if(is_file($targetFile)){
							@unlink($targetFile);
						}
							
						$url = admin_url('admin.php?page=pn_export_currency&reply=true');
						wp_redirect($url);
						exit;	
					}
						
                } else {
					pn_display_mess(__('Error! Error loading file','usve'));
				}
            } else {
				pn_display_mess(__('Error! Incorrect file size!','pn'));
			}		
        } else {
			pn_display_mess(__('Error! Incorrect file format!','pn'));
		}
	} else {
		pn_display_mess(__('Error! File is not selected!','pn'));
	}		
}